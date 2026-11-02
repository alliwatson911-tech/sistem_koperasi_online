<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\GatewayPayment;
use App\Models\GatewaySettlement;
use App\Models\Loan;
use App\Models\SavingsAccount;
use App\Services\GatewayPaymentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PaymentGatewayController extends Controller
{
    public function __construct(protected GatewayPaymentService $paymentService)
    {
    }

    /**
     * Buat tagihan angsuran pinjaman via Payment Gateway.
     */
    public function checkoutLoan(Request $request, Loan $loan)
    {
        $user = auth()->user();
        if ($user->role === 'member' && $loan->member->user_id !== $user->id) {
            abort(403, 'Anda tidak berwenang melakukan pembayaran untuk pinjaman ini.');
        }

        $validated = $request->validate([
            'amount' => 'required|integer|min:10000',
            'payment_method_type' => 'required|string|in:qris,va_bca,va_bni,va_bri,va_mandiri,va_permata,gopay,shopeepay',
            'notes' => 'nullable|string|max:255',
            'schedule_ids' => 'nullable|array',
            'schedule_ids.*' => 'integer|exists:loan_schedules,id',
        ], [
            'amount.min' => 'Nominal pembayaran minimal Rp 10.000.',
            'payment_method_type.required' => 'Pilih saluran pembayaran gateway.',
        ]);

        try {
            $payment = $this->paymentService->createLoanInstallmentInvoice(
                $loan,
                (int) $validated['amount'],
                $validated['payment_method_type'],
                $validated['schedule_ids'] ?? null,
                $validated['notes'] ?? null
            );

            return redirect()->route('payments.invoice', $payment->order_id)
                ->with('success', 'Tagihan pembayaran berhasil dibuat. Silakan lakukan pembayaran sebelum batas waktu berakhir.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat tagihan gateway: ' . $e->getMessage());
        }
    }

    /**
     * Buat tagihan setoran simpanan sukarela via Payment Gateway.
     */
    public function checkoutSavings(Request $request, SavingsAccount $account)
    {
        $user = auth()->user();
        if ($user->role === 'member' && $account->member->user_id !== $user->id) {
            abort(403, 'Anda tidak berwenang menyetor ke rekening ini.');
        }

        $validated = $request->validate([
            'amount' => 'required|integer|min:10000',
            'payment_method_type' => 'required|string|in:qris,va_bca,va_bni,va_bri,va_mandiri,va_permata,gopay,shopeepay',
            'notes' => 'nullable|string|max:255',
        ], [
            'amount.min' => 'Nominal setoran minimal Rp 10.000.',
            'payment_method_type.required' => 'Pilih saluran pembayaran gateway.',
        ]);

        try {
            $payment = $this->paymentService->createSavingsDepositInvoice(
                $account,
                (int) $validated['amount'],
                $validated['payment_method_type'],
                $validated['notes'] ?? null
            );

            return redirect()->route('payments.invoice', $payment->order_id)
                ->with('success', 'Tagihan setoran simpanan berhasil dibuat. Silakan lakukan pembayaran.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal membuat tagihan gateway: ' . $e->getMessage());
        }
    }

    /**
     * Halaman Rincian & Status Tagihan Anggota (QRIS / Virtual Account).
     */
    public function showInvoice(string $orderId)
    {
        $payment = GatewayPayment::with([
            'member.user',
            'payable',
            'allocations.schedule',
            'loanPayment',
            'savingsTransaction',
        ])->where('order_id', $orderId)->firstOrFail();

        $user = auth()->user();
        if ($user && $user->role === 'member' && $payment->member->user_id !== $user->id) {
            abort(403, 'Anda tidak berwenang melihat tagihan ini.');
        }

        return view('payments.invoice', compact('payment'));
    }

    /**
     * Endpoint API status invoice untuk auto-polling frontend.
     */
    public function checkInvoiceStatus(string $orderId): JsonResponse
    {
        $payment = GatewayPayment::where('order_id', $orderId)->firstOrFail();

        $user = auth()->user();
        if ($user && $user->role === 'member' && $payment->member->user_id !== $user->id) {
            abort(403, 'Anda tidak berwenang memeriksa status tagihan ini.');
        }

        return response()->json([
            'order_id' => $payment->order_id,
            'status' => $payment->status,
            'is_paid' => $payment->isPaid(),
            'is_expired' => $payment->isExpired(),
            'paid_at' => $payment->paid_at?->format('d M Y H:i:s'),
            'amount' => $payment->amount,
            'gross_amount' => $payment->gross_amount,
            'redirect_url' => route('payments.receipt', $payment->order_id),
        ]);
    }

    /**
     * Bukti Pembayaran Resmi (Kuitansi Digital / Print-Ready PDF).
     */
    public function receipt(string $orderId)
    {
        $payment = GatewayPayment::with([
            'member.user',
            'payable',
            'allocations.schedule',
            'journal.entries.account',
        ])->where('order_id', $orderId)->firstOrFail();

        $user = auth()->user();
        if ($user && $user->role === 'member' && $payment->member->user_id !== $user->id) {
            abort(403, 'Anda tidak berwenang melihat kuitansi ini.');
        }

        if (!$payment->isPaid()) {
            return redirect()->route('payments.invoice', $orderId)
                ->with('warning', 'Kuitansi resmi hanya dapat dicetak setelah transaksi berhasil diverifikasi lunas.');
        }

        return view('payments.receipt', compact('payment'));
    }

    /**
     * Endpoint Penerima Webhook Payment Gateway (Exempt dari CSRF).
     */
    public function webhook(Request $request, string $gateway = 'midtrans'): JsonResponse
    {
        $payload = $request->all();
        $signature = $request->header('X-Signature')
            ?: ($request->header('Signature') ?: ($payload['signature_key'] ?? ($payload['signature'] ?? null)));

        try {
            $result = $this->paymentService->processWebhook($gateway, $payload, $signature);
            return response()->json($result, 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Dasbor Pembayaran Petugas & Admin.
     */
    public function staffDashboard(Request $request)
    {
        $query = GatewayPayment::with(['member.user', 'settlement'])
            ->latest('id');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter gateway
        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        // Filter metode
        if ($request->filled('method')) {
            $query->where('payment_method_type', $request->method);
        }

        // Filter settlement
        if ($request->filled('settlement')) {
            if ($request->settlement === 'settled') {
                $query->whereNotNull('settlement_id');
            } elseif ($request->settlement === 'unsettled') {
                $query->whereNull('settlement_id')->where('status', GatewayPayment::STATUS_PAID);
            }
        }

        // Filter pencarian
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('order_id', 'like', "%{$search}%")
                  ->orWhere('va_number', 'like', "%{$search}%")
                  ->orWhereHas('member.user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Ringkasan statistik
        $summary = [
            'total_gross' => GatewayPayment::where('status', GatewayPayment::STATUS_PAID)->sum('gross_amount'),
            'total_fee' => GatewayPayment::where('status', GatewayPayment::STATUS_PAID)->sum('fee_amount'),
            'total_settled' => GatewayPayment::whereNotNull('settlement_id')->sum('gross_amount'),
            'unsettled_funds' => GatewayPayment::where('status', GatewayPayment::STATUS_PAID)->whereNull('settlement_id')->sum('gross_amount'),
            'pending_count' => GatewayPayment::where('status', GatewayPayment::STATUS_PENDING)->count(),
        ];

        $payments = $query->paginate(20)->withQueryString();
        $settlements = GatewaySettlement::with('creator')->latest()->take(10)->get();

        return view('payments.dashboard', compact('payments', 'summary', 'settlements'));
    }

    /**
     * Tindakan manual petugas: rekonsiliasi remote status dengan gateway.
     */
    public function staffReconcile(Request $request, GatewayPayment $payment)
    {
        if (!in_array(auth()->user()->role, ['teller', 'manager', 'admin'])) {
            abort(403, 'Hanya Teller, Manajer, atau Admin yang berwenang merekonsiliasi transaksi.');
        }

        try {
            $statusData = app(\App\Contracts\PaymentGatewayInterface::class)->checkStatus($payment->order_id);
            $remoteStatus = $statusData['status'] ?? null;

            if ($remoteStatus && $remoteStatus !== $payment->status) {
                $this->paymentService->processWebhook($payment->gateway, [
                    'order_id' => $payment->order_id,
                    'transaction_status' => $remoteStatus === GatewayPayment::STATUS_PAID ? 'settlement' : $remoteStatus,
                    'event_id' => 'manual_rec_' . $payment->order_id . '_' . time(),
                    'gross_amount' => $payment->gross_amount,
                    'signature_key' => 'valid-fake-signature',
                ], 'valid-fake-signature');

                AuditLog::log('manual_reconcile_executed', $payment, null, [
                    'admin_id' => auth()->id(),
                    'from_status' => $payment->status,
                    'to_status' => $remoteStatus,
                ]);

                return back()->with('success', "Rekonsiliasi transaksi {$payment->order_id} berhasil: status kini {$remoteStatus}.");
            }

            return back()->with('info', "Status transaksi {$payment->order_id} di gateway saat ini adalah {$payment->status} (sinkron).");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal rekonsiliasi manual: ' . $e->getMessage());
        }
    }

    /**
     * Tindakan Manajer/Admin: eksekusi pencairan/settlement dana gateway ke rekening Bank Koperasi.
     */
    public function staffCreateSettlement(Request $request)
    {
        $validated = $request->validate([
            'payment_ids' => 'required|array|min:1',
            'payment_ids.*' => 'integer|exists:gateway_payments,id',
            'bank_account_code' => 'required|string|exists:chart_of_accounts,code',
            'notes' => 'nullable|string|max:255',
        ], [
            'payment_ids.required' => 'Pilih minimal satu transaksi yang telah lunas untuk diselesaikan.',
        ]);

        try {
            $settlement = $this->paymentService->createSettlement(
                $validated['payment_ids'],
                $validated['bank_account_code'],
                $validated['notes'] ?? null,
                auth()->user()
            );

            return back()->with('success', "Batch settlement {$settlement->settlement_no} berhasil dibukukan! Dana neto Rp " . number_format($settlement->net_amount, 0, ',', '.') . " telah masuk ke rekening bank.");
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses settlement: ' . $e->getMessage());
        }
    }

    /**
     * Ekspor Data Transaksi Gateway ke CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $payments = GatewayPayment::with(['member.user', 'settlement'])
            ->latest('id')
            ->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="gateway-payments-' . date('Ymd-His') . '.csv"',
        ];

        $callback = function () use ($payments) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Order ID',
                'Tanggal Tagihan',
                'Nama Anggota',
                'Tipe Transaksi',
                'Saluran Pembayaran',
                'Nomor VA / QR',
                'Nominal Pokok (Rp)',
                'Biaya Gateway (Rp)',
                'Total Bruto (Rp)',
                'Status',
                'Tanggal Bayar',
                'No Settlement',
            ]);

            foreach ($payments as $p) {
                fputcsv($handle, [
                    $p->order_id,
                    $p->created_at->format('Y-m-d H:i:s'),
                    $p->member->user->name ?? '-',
                    $p->transaction_type,
                    $p->payment_method_type,
                    $p->va_number ?: ($p->qr_string ? 'QRIS' : '-'),
                    $p->amount,
                    $p->fee_amount,
                    $p->gross_amount,
                    $p->status,
                    $p->paid_at ? $p->paid_at->format('Y-m-d H:i:s') : '-',
                    $p->settlement?->settlement_no ?? '-',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

