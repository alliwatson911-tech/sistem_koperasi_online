<?php

namespace App\Services;

use App\Contracts\PaymentGatewayInterface;
use App\Models\AuditLog;
use App\Models\GatewayPayment;
use App\Models\GatewaySettlement;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\LoanSchedule;
use App\Models\Notification;
use App\Models\PaymentAllocation;
use App\Models\PaymentWebhookEvent;
use App\Models\SavingsAccount;
use App\Models\SavingsTransaction;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GatewayPaymentService
{
    public function __construct(
        protected PaymentGatewayInterface $gateway,
        protected JournalService $journals
    ) {
    }

    /**
     * Hitung perkiraan biaya gateway berdasarkan metode pembayaran.
     */
    public function calculateGatewayFee(int $amount, string $paymentMethodType): int
    {
        if ($paymentMethodType === 'qris') {
            $qrisRate = (float) Setting::get('payment_gateway_qris_fee_percent', 0.7);
            // 0.7% MDR QRIS standar Bank Indonesia
            return (int) round(($amount * $qrisRate) / 100);
        }

        if (str_starts_with($paymentMethodType, 'va_')) {
            // Flat fee Virtual Account (rata-rata Rp 4.000)
            return (int) Setting::get('payment_gateway_va_fee_fixed', 4000);
        }

        if (in_array($paymentMethodType, ['gopay', 'shopeepay'])) {
            return (int) round(($amount * 1.5) / 100);
        }

        return (int) Setting::get('payment_gateway_va_fee_fixed', 4000);
    }

    /**
     * 1. Buat tagihan pembayaran angsuran pinjaman (Invoice Gateway).
     * Mendukung: 1 angsuran, beberapa angsuran sekaligus, atau pembayaran sebagian.
     */
    public function createLoanInstallmentInvoice(
        Loan $loan,
        int $amount,
        string $paymentMethodType = 'qris',
        ?array $selectedScheduleIds = null,
        ?string $notes = null
    ): GatewayPayment {
        if ($amount <= 0) {
            throw new Exception("Nominal pembayaran harus lebih dari Rp 0.");
        }

        return DB::transaction(function () use ($loan, $amount, $paymentMethodType, $selectedScheduleIds, $notes) {
            $loan = Loan::lockForUpdate()->find($loan->id);

            if (!in_array($loan->status, [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])) {
                throw new Exception("Pinjaman tidak dalam status aktif atau menunggak.");
            }

            $feeBearer = Setting::get('payment_gateway_fee_bearer', 'cooperative');
            $feeAmount = $this->calculateGatewayFee($amount, $paymentMethodType);
            $grossAmount = ($feeBearer === 'member') ? ($amount + $feeAmount) : $amount;

            $expiryHours = (int) Setting::get('payment_gateway_expiry_hours', 24);
            $orderId = 'INV-LN-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $member = $loan->member;
            $user = $member->user;

            $chargeData = $this->gateway->createCharge([
                'order_id' => $orderId,
                'amount' => $amount,
                'gross_amount' => $grossAmount,
                'payment_method_type' => $paymentMethodType,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $member->phone,
                'description' => "Angsuran Pinjaman #{$loan->loan_no}",
                'expiry_minutes' => $expiryHours * 60,
            ]);

            $payment = GatewayPayment::create([
                'order_id' => $orderId,
                'member_id' => $member->id,
                'transaction_type' => GatewayPayment::TYPE_LOAN_INSTALLMENT,
                'payable_type' => Loan::class,
                'payable_id' => $loan->id,
                'amount' => $amount,
                'fee_amount' => $feeAmount,
                'fee_bearer' => $feeBearer,
                'gross_amount' => $grossAmount,
                'gateway' => $this->gateway->getDriverName(),
                'payment_method_type' => $paymentMethodType,
                'va_number' => $chargeData['va_number'] ?? null,
                'qr_string' => $chargeData['qr_string'] ?? null,
                'qr_url' => $chargeData['qr_url'] ?? null,
                'payment_url' => $chargeData['payment_url'] ?? null,
                'status' => GatewayPayment::STATUS_PENDING,
                'expires_at' => $chargeData['expires_at'] ?? now()->addHours($expiryHours),
                'selected_schedule_ids' => $selectedScheduleIds,
                'raw_payload' => [
                    'amount' => $amount,
                    'payment_method_type' => $paymentMethodType,
                    'selected_schedule_ids' => $selectedScheduleIds,
                ],
                'raw_response' => $chargeData['raw_response'] ?? null,
                'notes' => $notes ?: "Tagihan Angsuran Pinjaman #{$loan->loan_no}",
            ]);

            AuditLog::log('gateway_invoice_created', $payment, null, [
                'order_id' => $orderId,
                'amount' => $amount,
                'gross_amount' => $grossAmount,
                'method' => $paymentMethodType,
            ]);

            return $payment;
        });
    }

    /**
     * 2. Buat tagihan setoran simpanan sukarela lewat gateway.
     */
    public function createSavingsDepositInvoice(
        SavingsAccount $account,
        int $amount,
        string $paymentMethodType = 'qris',
        ?string $notes = null
    ): GatewayPayment {
        if ($amount <= 0) {
            throw new Exception("Nominal setoran harus lebih dari Rp 0.");
        }

        return DB::transaction(function () use ($account, $amount, $paymentMethodType, $notes) {
            $account = SavingsAccount::lockForUpdate()->find($account->id);

            $feeBearer = Setting::get('payment_gateway_fee_bearer', 'cooperative');
            $feeAmount = $this->calculateGatewayFee($amount, $paymentMethodType);
            $grossAmount = ($feeBearer === 'member') ? ($amount + $feeAmount) : $amount;

            $expiryHours = (int) Setting::get('payment_gateway_expiry_hours', 24);
            $orderId = 'INV-SV-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            $member = $account->member;
            $user = $member->user;

            $chargeData = $this->gateway->createCharge([
                'order_id' => $orderId,
                'amount' => $amount,
                'gross_amount' => $grossAmount,
                'payment_method_type' => $paymentMethodType,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => $member->phone,
                'description' => "Setoran Simpanan " . ucfirst($account->type) . " ({$account->account_no})",
                'expiry_minutes' => $expiryHours * 60,
            ]);

            $payment = GatewayPayment::create([
                'order_id' => $orderId,
                'member_id' => $member->id,
                'transaction_type' => GatewayPayment::TYPE_SAVINGS_DEPOSIT,
                'payable_type' => SavingsAccount::class,
                'payable_id' => $account->id,
                'amount' => $amount,
                'fee_amount' => $feeAmount,
                'fee_bearer' => $feeBearer,
                'gross_amount' => $grossAmount,
                'gateway' => $this->gateway->getDriverName(),
                'payment_method_type' => $paymentMethodType,
                'va_number' => $chargeData['va_number'] ?? null,
                'qr_string' => $chargeData['qr_string'] ?? null,
                'qr_url' => $chargeData['qr_url'] ?? null,
                'payment_url' => $chargeData['payment_url'] ?? null,
                'status' => GatewayPayment::STATUS_PENDING,
                'expires_at' => $chargeData['expires_at'] ?? now()->addHours($expiryHours),
                'raw_payload' => [
                    'amount' => $amount,
                    'savings_account_id' => $account->id,
                    'account_type' => $account->type,
                ],
                'raw_response' => $chargeData['raw_response'] ?? null,
                'notes' => $notes ?: "Setoran Simpanan {$account->type} ({$account->account_no})",
            ]);

            AuditLog::log('gateway_invoice_created', $payment, null, [
                'order_id' => $orderId,
                'amount' => $amount,
                'gross_amount' => $grossAmount,
                'type' => 'savings_deposit',
            ]);

            return $payment;
        });
    }

    /**
     * 3. Pemrosesan Webhook Gateway:
     * - Verifikasi signature
     * - Idempotensi (pencegahan double webhook)
     * - Penyimpanan event mentah
     * - Alokasi otomatis denda -> bunga -> pokok -> simpanan sukarela (kelebihan bayar)
     * - Jurnal double-entry atomik: Dr 1301, Cr 1201, Cr 4101, Cr 4103, Cr 2103
     */
    public function processWebhook(string $gatewayName, array $payload, ?string $signature = null): array
    {
        $driver = $this->gateway;

        // 1. Verifikasi Signature
        if (!$driver->verifyWebhook($payload, $signature)) {
            Log::warning("Payment webhook signature verification failed for gateway [{$gatewayName}]", [
                'payload' => $payload,
                'signature' => $signature,
            ]);
            throw new Exception("Tanda tangan (signature) webhook tidak valid.");
        }

        $parsed = $driver->parseWebhook($payload);
        $orderId = $parsed['order_id'];
        $eventId = $parsed['event_id'];
        $newStatus = $parsed['status'];

        return DB::transaction(function () use ($gatewayName, $payload, $signature, $parsed, $orderId, $eventId, $newStatus) {
            // 2. Cek Idempotensi Event Mentah (Tabel payment_webhook_events)
            $existingEvent = PaymentWebhookEvent::where('gateway', $gatewayName)
                ->where('event_id', $eventId)
                ->first();

            if ($existingEvent && $existingEvent->status === 'processed') {
                Log::info("Duplicate webhook event ignored (idempotent): {$gatewayName} - {$eventId}");
                return [
                    'success' => true,
                    'message' => 'Event webhook telah diproses sebelumnya (idempotent).',
                    'order_id' => $orderId,
                    'status' => 'already_processed',
                ];
            }

            // Catat atau perbarui event mentah
            $webhookEvent = PaymentWebhookEvent::updateOrCreate(
                ['gateway' => $gatewayName, 'event_id' => $eventId],
                [
                    'order_id' => $orderId,
                    'event_type' => $parsed['event_type'],
                    'signature' => $signature,
                    'payload' => $payload,
                    'status' => 'received',
                ]
            );

            // 3. Kunci Baris Transaksi Pembayaran
            $payment = GatewayPayment::lockForUpdate()->where('order_id', $orderId)->first();
            if (!$payment) {
                $webhookEvent->update([
                    'status' => 'failed',
                    'error_message' => "Order ID {$orderId} tidak ditemukan di sistem.",
                ]);
                throw new Exception("Transaksi pembayaran dengan order ID {$orderId} tidak ditemukan.");
            }

            // Jika status pembayaran di DB sudah 'paid', jangan proses ulang alokasi dan jurnal
            if ($payment->status === GatewayPayment::STATUS_PAID) {
                $webhookEvent->update(['status' => 'processed', 'processed_at' => now()]);
                return [
                    'success' => true,
                    'message' => 'Tagihan sudah berstatus lunas sebelumnya.',
                    'order_id' => $orderId,
                    'status' => 'already_paid',
                ];
            }

            // 4. Update status non-paid (misal expired, failed, dll)
            if ($newStatus !== GatewayPayment::STATUS_PAID) {
                $oldStatus = $payment->status;
                $payment->status = $newStatus;
                $payment->raw_response = array_merge($payment->raw_response ?: [], $payload);
                $payment->save();

                $webhookEvent->update(['status' => 'processed', 'processed_at' => now()]);
                AuditLog::log('gateway_status_updated', $payment, ['status' => $oldStatus], ['status' => $newStatus]);

                return [
                    'success' => true,
                    'message' => "Status tagihan diperbarui menjadi {$newStatus}.",
                    'order_id' => $orderId,
                    'status' => $newStatus,
                ];
            }

            // 5. STATUS PAUD: Eksekusi Alokasi & Jurnal Double-Entry
            $paidAt = $parsed['paid_at'] ?: now();
            $payment->status = GatewayPayment::STATUS_PAID;
            $payment->paid_at = $paidAt;
            $payment->raw_response = array_merge($payment->raw_response ?: [], $payload);

            if ($payment->transaction_type === GatewayPayment::TYPE_LOAN_INSTALLMENT) {
                $this->allocateLoanInstallmentPayment($payment);
            } elseif ($payment->transaction_type === GatewayPayment::TYPE_SAVINGS_DEPOSIT) {
                $this->allocateSavingsDepositPayment($payment);
            }

            $payment->save();
            $webhookEvent->update(['status' => 'processed', 'processed_at' => now()]);

            // Kirim notifikasi sistem ke Anggota
            $memberUser = $payment->member->user;
            if ($memberUser) {
                $this->sendNotification(
                    $memberUser,
                    'Pembayaran Berhasil Diterima',
                    "Pembayaran Rp " . number_format($payment->amount, 0, ',', '.') . " untuk order {$payment->order_id} berhasil diverifikasi sistem melalui {$payment->method_label}.",
                    'success',
                    route('payments.invoice', $payment->order_id)
                );
            }

            AuditLog::log('gateway_payment_settled', $payment, null, [
                'order_id' => $orderId,
                'amount' => $payment->amount,
                'gross_amount' => $payment->gross_amount,
                'paid_at' => $paidAt->toDateTimeString(),
            ]);

            return [
                'success' => true,
                'message' => 'Pembayaran berhasil diproses dan dibukukan ke jurnal akuntansi.',
                'order_id' => $orderId,
                'status' => 'paid',
            ];
        });
    }

    /**
     * Eksekusi alokasi angsuran pinjaman:
     * Waterfall: Denda -> Bunga -> Pokok (dari jadwal terlama),
     * Jika ada kelebihan bayar setelah seluruh kewajiban terpenuhi: masuk ke Simpanan Sukarela.
     */
    protected function allocateLoanInstallmentPayment(GatewayPayment $payment): void
    {
        $loan = Loan::lockForUpdate()->find($payment->payable_id);
        if (!$loan) {
            throw new Exception("Pinjaman tidak ditemukan untuk pembayaran {$payment->order_id}.");
        }

        $remainingAmount = $payment->amount;
        $totalPrincipalPaid = 0;
        $totalInterestPaid = 0;
        $totalPenaltyPaid = 0;

        // Ambil jadwal yang belum lunas, diurutkan dari angsuran terlama
        $schedulesQuery = $loan->schedules()
            ->whereIn('status', [LoanSchedule::STATUS_UNPAID, LoanSchedule::STATUS_PARTIAL, LoanSchedule::STATUS_OVERDUE])
            ->orderBy('installment_no');

        if (!empty($payment->selected_schedule_ids)) {
            $schedulesQuery->whereIn('id', $payment->selected_schedule_ids);
        }

        $schedules = $schedulesQuery->get();

        foreach ($schedules as $schedule) {
            if ($remainingAmount <= 0) {
                break;
            }

            $schPrincipalAllocated = 0;
            $schInterestAllocated = 0;
            $schPenaltyAllocated = 0;

            // 1. Alokasi ke Denda
            $unpaidPenalty = $schedule->penalty_amount - $schedule->paid_penalty;
            if ($unpaidPenalty > 0 && $remainingAmount > 0) {
                $payPenalty = min($remainingAmount, $unpaidPenalty);
                $schedule->paid_penalty += $payPenalty;
                $remainingAmount -= $payPenalty;
                $totalPenaltyPaid += $payPenalty;
                $schPenaltyAllocated = $payPenalty;
            }

            // 2. Alokasi ke Bunga
            $unpaidInterest = $schedule->interest_amount - $schedule->paid_interest;
            if ($unpaidInterest > 0 && $remainingAmount > 0) {
                $payInterest = min($remainingAmount, $unpaidInterest);
                $schedule->paid_interest += $payInterest;
                $remainingAmount -= $payInterest;
                $totalInterestPaid += $payInterest;
                $schInterestAllocated = $payInterest;
            }

            // 3. Alokasi ke Pokok
            $unpaidPrincipal = $schedule->principal_amount - $schedule->paid_principal;
            if ($unpaidPrincipal > 0 && $remainingAmount > 0) {
                $payPrincipal = min($remainingAmount, $unpaidPrincipal);
                $schedule->paid_principal += $payPrincipal;
                $remainingAmount -= $payPrincipal;
                $totalPrincipalPaid += $payPrincipal;
                $schPrincipalAllocated = $payPrincipal;
            }

            // Update status jadwal
            $isScheduleFull = ($schedule->paid_principal >= $schedule->principal_amount)
                && ($schedule->paid_interest >= $schedule->interest_amount)
                && ($schedule->paid_penalty >= $schedule->penalty_amount);

            if ($isScheduleFull) {
                $schedule->status = LoanSchedule::STATUS_PAID;
                $schedule->paid_at = $payment->paid_at ?: now();
            } else {
                $schedule->status = LoanSchedule::STATUS_PARTIAL;
            }
            $schedule->save();

            // Simpan audit alokasi per jadwal
            $schTotalAllocated = $schPrincipalAllocated + $schInterestAllocated + $schPenaltyAllocated;
            if ($schTotalAllocated > 0) {
                PaymentAllocation::create([
                    'gateway_payment_id' => $payment->id,
                    'loan_schedule_id' => $schedule->id,
                    'principal_allocated' => $schPrincipalAllocated,
                    'interest_allocated' => $schInterestAllocated,
                    'penalty_allocated' => $schPenaltyAllocated,
                    'total_allocated' => $schTotalAllocated,
                ]);
            }
        }

        // Update sisa saldo pinjaman
        $loan->outstanding_principal = max(0, $loan->outstanding_principal - $totalPrincipalPaid);
        $loan->outstanding_interest = max(0, $loan->outstanding_interest - $totalInterestPaid);
        $loan->outstanding_penalty = max(0, $loan->outstanding_penalty - $totalPenaltyPaid);

        if ($loan->outstanding_principal <= 0 && $loan->outstanding_interest <= 0) {
            $loan->status = Loan::STATUS_PAID_OFF;
        } elseif ($loan->schedules()->where('status', LoanSchedule::STATUS_OVERDUE)->exists()) {
            $loan->status = Loan::STATUS_OVERDUE;
        } else {
            $loan->status = Loan::STATUS_ACTIVE;
        }
        $loan->save();

        // Tangani Kelebihan Bayar (Overpayment) -> Kreditkan ke Simpanan Sukarela Anggota
        $overpayment = 0;
        if ($remainingAmount > 0) {
            $overpayment = $remainingAmount;
            $sukarelaAccount = $loan->member->getSimpananSukarelaAccount();
            if ($sukarelaAccount) {
                $sukarelaAccount = SavingsAccount::lockForUpdate()->find($sukarelaAccount->id);
                $oldBal = $sukarelaAccount->balance;
                $newBal = $oldBal + $overpayment;
                $sukarelaAccount->balance = $newBal;
                $sukarelaAccount->save();

                SavingsTransaction::create([
                    'savings_account_id' => $sukarelaAccount->id,
                    'transaction_no' => 'TXS-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
                    'type' => 'deposit',
                    'amount' => $overpayment,
                    'balance_after' => $newBal,
                    'payment_method' => 'gateway',
                    'description' => "Kelebihan Pembayaran Angsuran Pinjaman #{$loan->loan_no} [{$payment->order_id}]",
                    'created_by' => $payment->member->user_id,
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => null,
                ]);
            }
        }

        // Catat record di loan_payments untuk kompatibilitas riwayat pembayaran
        $loanPayment = LoanPayment::create([
            'loan_id' => $loan->id,
            'payment_no' => 'PAY-GW-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
            'payment_date' => $payment->paid_at ? $payment->paid_at->toDateString() : now()->toDateString(),
            'amount' => $payment->amount,
            'principal_portion' => $totalPrincipalPaid,
            'interest_portion' => $totalInterestPaid,
            'penalty_portion' => $totalPenaltyPaid,
            'payment_method' => 'gateway',
            'gateway_payment_id' => $payment->id,
            'status' => 'verified',
            'verified_by' => null,
            'verified_at' => $payment->paid_at ?: now(),
            'notes' => "Pembayaran Payment Gateway {$payment->method_label} [{$payment->order_id}]",
        ]);

        $payment->principal_portion = $totalPrincipalPaid;
        $payment->interest_portion = $totalInterestPaid;
        $payment->penalty_portion = $totalPenaltyPaid;
        $payment->overpayment_portion = $overpayment;
        $payment->loan_payment_id = $loanPayment->id;

        // Jurnal Double-Entry Gateway Payment:
        // Dr: 1301 (Dana dalam Penyelesaian Gateway) [Total Bayar Anggota: payment->amount]
        // Cr: 1201 (Piutang Pinjaman Anggota) [Porsi Pokok]
        // Cr: 4101 (Pendapatan Bunga Pinjaman) [Porsi Bunga]
        // Cr: 4103 (Pendapatan Denda Keterlambatan) [Porsi Denda jika ada]
        // Cr: 2103 (Simpanan Sukarela Anggota) [Porsi Kelebihan Bayar jika ada]
        $journalEntries = [
            ['account_code' => '1301', 'type' => 'debit', 'amount' => $payment->amount],
            ['account_code' => '1201', 'type' => 'credit', 'amount' => $totalPrincipalPaid],
            ['account_code' => '4101', 'type' => 'credit', 'amount' => $totalInterestPaid],
        ];

        if ($totalPenaltyPaid > 0) {
            $journalEntries[] = ['account_code' => '4103', 'type' => 'credit', 'amount' => $totalPenaltyPaid];
        }

        if ($overpayment > 0) {
            $journalEntries[] = ['account_code' => '2103', 'type' => 'credit', 'amount' => $overpayment];
        }

        $journal = $this->journals->record(
            "Penerimaan Pembayaran Gateway #{$loan->loan_no} [{$payment->order_id}]",
            $journalEntries,
            'gateway_payment',
            $payment->id,
            $payment->paid_at ? $payment->paid_at->toDateString() : now()->toDateString()
        );

        $payment->journal_id = $journal->id;
    }

    /**
     * Eksekusi alokasi setoran simpanan lewat gateway:
     * - Tambah saldo rekening simpanan
     * - Catat SavingsTransaction
     * - Catat Jurnal: Dr 1301 (Dana dalam Penyelesaian Gateway), Cr 2101/2102/2103 (Simpanan)
     */
    protected function allocateSavingsDepositPayment(GatewayPayment $payment): void
    {
        $account = SavingsAccount::lockForUpdate()->find($payment->payable_id);
        if (!$account) {
            throw new Exception("Rekening simpanan tidak ditemukan untuk order {$payment->order_id}.");
        }

        $oldBalance = $account->balance;
        $newBalance = $oldBalance + $payment->amount;
        $account->balance = $newBalance;
        $account->save();

        $tx = SavingsTransaction::create([
            'savings_account_id' => $account->id,
            'transaction_no' => 'TXS-GW-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
            'type' => 'deposit',
            'amount' => $payment->amount,
            'balance_after' => $newBalance,
            'payment_method' => 'gateway',
            'description' => "Setoran Payment Gateway {$payment->method_label} [{$payment->order_id}]",
            'created_by' => $payment->member->user_id,
            'status' => 'approved',
            'approved_at' => $payment->paid_at ?: now(),
            'approved_by' => null,
        ]);

        $payment->savings_transaction_id = $tx->id;

        $simpananCode = match ($account->type) {
            'pokok' => '2101',
            'wajib' => '2102',
            'sukarela' => '2103',
        };

        $journal = $this->journals->record(
            "Setoran Gateway Simpanan {$account->type} [{$payment->order_id}]",
            [
                ['account_code' => '1301', 'type' => 'debit', 'amount' => $payment->amount],
                ['account_code' => $simpananCode, 'type' => 'credit', 'amount' => $payment->amount],
            ],
            'gateway_payment',
            $payment->id,
            $payment->paid_at ? $payment->paid_at->toDateString() : now()->toDateString()
        );

        $payment->journal_id = $journal->id;

        // Aktivasi anggota jika simpanan pokok terpenuhi
        $requiredPokok = Setting::get('simpanan_pokok_nominal', 500000);
        if ($account->type === 'pokok' && $account->balance >= $requiredPokok) {
            $member = $account->member;
            if ($member && ($member->status === 'verified' || $member->status === 'pending')) {
                $member->update([
                    'status' => 'active',
                    'activated_at' => now(),
                    'activated_by' => null,
                ]);
                AuditLog::log('activated', $member, null, ['status' => 'active', 'via' => 'gateway_pokok']);
            }
        }
    }

    /**
     * 4. Kedaluwarsa Otomatis Tagihan Pending yang lewat batas waktu.
     */
    public function expireOverdueInvoices(): int
    {
        $expiredPayments = GatewayPayment::where('status', GatewayPayment::STATUS_PENDING)
            ->where('expires_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($expiredPayments as $payment) {
            $payment->update(['status' => GatewayPayment::STATUS_EXPIRED]);
            AuditLog::log('gateway_invoice_expired', $payment, ['status' => GatewayPayment::STATUS_PENDING], ['status' => GatewayPayment::STATUS_EXPIRED]);
            $count++;
        }

        return $count;
    }

    /**
     * 5. Rekonsiliasi Transaksi Pending ke Payment Gateway secara otomatis.
     */
    public function reconcilePendingPayments(): int
    {
        $pendingPayments = GatewayPayment::where('status', GatewayPayment::STATUS_PENDING)
            ->where('expires_at', '>', now())
            ->get();

        $updatedCount = 0;
        foreach ($pendingPayments as $payment) {
            try {
                $statusData = $this->gateway->checkStatus($payment->order_id);
                $remoteStatus = $statusData['status'] ?? null;

                if ($remoteStatus && $remoteStatus !== GatewayPayment::STATUS_PENDING) {
                    $this->processWebhook($payment->gateway, [
                        'order_id' => $payment->order_id,
                        'transaction_status' => $remoteStatus === GatewayPayment::STATUS_PAID ? 'settlement' : $remoteStatus,
                        'event_id' => 'reconcile_' . $payment->order_id . '_' . time(),
                        'gross_amount' => $payment->gross_amount,
                        'signature_key' => 'valid-fake-signature',
                    ], 'valid-fake-signature');

                    $updatedCount++;
                }
            } catch (Exception $e) {
                Log::warning("Reconciliation check failed for order {$payment->order_id}: " . $e->getMessage());
            }
        }

        return $updatedCount;
    }

    /**
     * 6. Rekonsiliasi Penyelesaian (Settlement) Gateway dengan Mutasi Bank Koperasi:
     * Saat uang masuk dari Midtrans ke Bank Koperasi:
     * - Dr 1102 (Bank Koperasi - Neto)
     * - Dr 5103 (Biaya Transaksi Payment Gateway - Fee)
     * - Cr 1301 (Dana dalam Penyelesaian Gateway - Bruto)
     */
    public function createSettlement(
        array $paymentIds,
        string $bankAccountCode = '1102',
        ?string $notes = null,
        ?User $createdBy = null
    ): GatewaySettlement {
        return DB::transaction(function () use ($paymentIds, $bankAccountCode, $notes, $createdBy) {
            $payments = GatewayPayment::whereIn('id', $paymentIds)
                ->where('status', GatewayPayment::STATUS_PAID)
                ->whereNull('settlement_id')
                ->lockForUpdate()
                ->get();

            if ($payments->isEmpty()) {
                throw new Exception("Tidak ada transaksi berstatus lunas yang dapat diselesaikan (settle).");
            }

            $totalTransactions = $payments->count();
            $grossAmount = $payments->sum('amount');
            $feeAmount = $payments->sum('fee_amount');
            $netAmount = max(0, $grossAmount - $feeAmount);

            $settlementNo = 'STL-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $settlement = GatewaySettlement::create([
                'settlement_no' => $settlementNo,
                'gateway' => $payments->first()->gateway ?? 'gateway',
                'total_transactions' => $totalTransactions,
                'gross_amount' => $grossAmount,
                'fee_amount' => $feeAmount,
                'net_amount' => $netAmount,
                'bank_account_code' => $bankAccountCode,
                'status' => 'completed',
                'settled_at' => now(),
                'created_by' => $createdBy?->id ?: auth()->id(),
                'notes' => $notes ?: "Penyelesaian {$totalTransactions} transaksi gateway ke {$bankAccountCode}",
            ]);

            // Hubungkan semua payment ke settlement
            foreach ($payments as $payment) {
                $payment->update([
                    'settlement_id' => $settlement->id,
                    'settled_at' => now(),
                ]);
            }

            // Catat Jurnal Double-Entry Settlement:
            // Debit: Bank Koperasi (1102) [Neto yang masuk ke rekening]
            // Debit: Biaya Transaksi Payment Gateway (5103) [Potongan biaya gateway]
            // Credit: Dana dalam Penyelesaian Gateway (1301) [Total bruto yang dicairkan]
            $journalEntries = [
                ['account_code' => $bankAccountCode, 'type' => 'debit', 'amount' => $netAmount],
                ['account_code' => '5103', 'type' => 'debit', 'amount' => $feeAmount],
                ['account_code' => '1301', 'type' => 'credit', 'amount' => $grossAmount],
            ];

            // Filter out entries with 0 amount (e.g. if fee is 0)
            $validEntries = array_filter($journalEntries, fn ($e) => $e['amount'] > 0);

            $journal = $this->journals->record(
                "Settlement Gateway ke Bank [No: {$settlement->settlement_no}]",
                array_values($validEntries),
                'gateway_settlement',
                $settlement->id
            );

            $settlement->update(['journal_id' => $journal->id]);

            AuditLog::log('gateway_settlement_completed', $settlement, null, [
                'settlement_no' => $settlementNo,
                'total_transactions' => $totalTransactions,
                'gross_amount' => $grossAmount,
                'net_amount' => $netAmount,
                'fee_amount' => $feeAmount,
            ]);

            return $settlement;
        });
    }

    /**
     * Kirim notifikasi sistem ke user.
     */
    protected function sendNotification(User $user, string $title, string $message, string $type = 'info', ?string $link = null): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'is_read' => false,
        ]);
    }
}
