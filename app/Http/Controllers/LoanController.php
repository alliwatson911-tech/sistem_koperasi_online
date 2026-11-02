<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanPayment;
use App\Models\LoanProduct;
use App\Models\LoanSchedule;
use App\Models\Member;
use App\Models\User;
use App\Services\LoanCalculationService;
use App\Services\LoanService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoanController extends Controller
{
    public function __construct(
        protected LoanService $loanService,
        protected LoanCalculationService $calculator
    ) {
    }

    public function products()
    {
        $products = LoanProduct::where('is_active', true)->get();
        return view('loans.products', compact('products'));
    }

    public function simulation(Request $request)
    {
        $products = LoanProduct::where('is_active', true)->get();

        $selectedProduct = null;
        $simulationResult = null;

        $productId = $request->get('product_id', $products->first()?->id);

        if ($productId && $products->isNotEmpty()) {
            $selectedProduct = $products->firstWhere('id', (int) $productId) ?: $products->first();
            $defaultAmount = $selectedProduct->isWeekly() ? 3000000 : 10000000;
            $defaultTenor = $selectedProduct->isWeekly() ? 10 : 12;

            $amount = (int) $request->get('amount', $defaultAmount);
            $tenor = (int) $request->get('tenor', $defaultTenor);

            $method = $request->get('calculation_method', $selectedProduct->calculation_method);

            $adminFee = (int) round(($amount * ($selectedProduct->admin_fee_percentage / 100)) + $selectedProduct->admin_fee_fixed);
            $simulationResult = $this->calculator->simulate(
                $amount,
                $tenor,
                (float) $selectedProduct->interest_rate_per_period,
                $method,
                $adminFee,
                $selectedProduct->tenor_unit ?? 'month'
            );
        }

        return view('loans.simulation', compact('products', 'selectedProduct', 'simulationResult'));
    }

    public function applyForm(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member || $member->status !== Member::STATUS_ACTIVE) {
            return redirect()->route('dashboard')->with('error', 'Hanya anggota berstatus AKTIF yang dapat mengajukan pinjaman.');
        }

        $products = LoanProduct::where('is_active', true)->get();
        $selectedProductId = $request->product_id ?: $products->first()?->id;
        $selectedProduct = $products->firstWhere('id', (int) $selectedProductId) ?: $products->first();

        return view('loans.apply', compact('member', 'products', 'selectedProductId', 'selectedProduct'));
    }

    public function apply(Request $request)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member || $member->status !== Member::STATUS_ACTIVE) {
            return redirect()->route('dashboard')->with('error', 'Hanya anggota aktif yang dapat mengajukan pinjaman.');
        }

        $product = LoanProduct::findOrFail($request->loan_product_id);
        $tenor = (int) ($request->tenor_count ?? $request->tenor_months ?? $request->tenor);

        $request->validate([
            'loan_product_id' => 'required|exists:loan_products,id',
            'amount' => "required|numeric|min:{$product->min_amount}|max:{$product->max_amount}",
            'purpose' => 'required|string|max:500',
            'agree_terms' => 'required|accepted',
        ], [
            'agree_terms.accepted' => 'Anda wajib menyetujui prinsip transparansi dan syarat pengajuan pinjaman.',
        ]);

        $minTenor = $product->min_tenor;
        $maxTenor = $product->max_tenor;
        $unitLabel = $product->tenor_unit_label;
        if ($tenor < $minTenor || $tenor > $maxTenor) {
            return back()->with('error', "Tenor pinjaman harus antara {$minTenor} hingga {$maxTenor} {$unitLabel}.")->withInput();
        }

        try {
            $application = $this->loanService->apply(
                $member,
                $product,
                (int) $request->amount,
                $tenor,
                $request->purpose
            );

            return redirect()->route('loans.my')
                ->with('success', "Pengajuan pinjaman {$application->application_no} berhasil dikirim! Menunggu pemeriksaan dokumen oleh teller.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function myLoans()
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member) {
            return redirect()->route('dashboard')->with('error', 'Profil anggota tidak ditemukan.');
        }

        $loans = $member->loans()->with('loanProduct')->orderBy('id', 'desc')->get();
        $applications = $member->loanApplications()->with('loanProduct')->orderBy('id', 'desc')->get();

        return view('loans.my_loans', compact('member', 'loans', 'applications'));
    }

    public function show(Loan $loan)
    {
        $user = Auth::user();
        if ($user->isMember() && $loan->member->user_id !== $user->id) {
            abort(403);
        }

        $loan->load(['member.user', 'loanProduct', 'schedules.loan', 'payments', 'loanApplication.receivedBy', 'disburser']);
        $timeline = $this->loanService->getTimeline($loan);

        return view('loans.show', compact('loan', 'timeline'));
    }

    public function signAkad(Loan $loan)
    {
        $user = Auth::user();
        if ($loan->member->user_id !== $user->id && !$user->isAdmin()) {
            abort(403, 'Hanya peminjam yang bersangkutan (atau Admin resmi) yang dapat menandatangani akad perjanjian digital.');
        }

        try {
            $this->loanService->signAgreement($loan);
            return back()->with('success', 'Akad perjanjian pinjaman berhasil ditandatangani! Dana segera dicairkan oleh petugas.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function payForm(Loan $loan)
    {
        $user = Auth::user();
        if ($user->isMember() && $loan->member->user_id !== $user->id) {
            abort(403);
        }

        $loan->load(['schedules', 'member.user']);
        $nextSchedule = $loan->getNextUnpaidSchedule();

        return view('loans.pay', compact('loan', 'nextSchedule'));
    }

    public function pay(Request $request, Loan $loan)
    {
        $user = Auth::user();
        if ($user->isMember() && $loan->member->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'payment_method' => 'required|in:cash,transfer',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3072',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $proofPath = null;
            if ($request->hasFile('proof_file')) {
                $proofPath = $request->file('proof_file')->store('loan_proofs', 'public');
            }

            $payment = $this->loanService->payInstallment(
                $loan,
                (int) $request->amount,
                $request->payment_method,
                $proofPath,
                $request->notes
            );

            return redirect()->route('loans.show', $loan->id)
                ->with('success', "Pembayaran sebesar Rp " . number_format($request->amount) . " berhasil diverifikasi dan dijurnal! No Kuitansi: {$payment->payment_no}");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function earlyRepay(Request $request, Loan $loan)
    {
        $user = Auth::user();
        if ($user->isMember() && $loan->member->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'payment_method' => 'required|in:cash,transfer',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $payment = $this->loanService->earlyRepayment(
                $loan,
                $request->payment_method,
                $request->notes
            );

            return redirect()->route('loans.show', $loan->id)
                ->with('success', "Pelunasan dipercepat pinjaman #{$loan->loan_no} berhasil diproses! Sisa pokok telah dilunasi dan kewajiban bunga masa depan telah dibebaskan. Kuitansi: {$payment->payment_no}");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // === STAFF WORKFLOWS ===

    public function indexLoans(Request $request)
    {
        $query = Loan::with(['member.user', 'loanProduct']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_no', 'like', "%{$search}%")
                  ->orWhereHas('member.user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $loans = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $totalOutstanding = (int) Loan::whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])->sum('outstanding_principal');

        return view('loans.index', compact('loans', 'totalOutstanding'));
    }

    public function applications(Request $request)
    {
        $query = LoanApplication::with(['member.user', 'loanProduct', 'approvals.user', 'assignedTo', 'receivedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('teller_id')) {
            $query->where(function ($q) use ($request) {
                $q->where('assigned_to', $request->teller_id)
                  ->orWhere('received_by', $request->teller_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('application_no', 'like', "%{$search}%")
                  ->orWhereHas('member.user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('member', fn($mq) => $mq->where('member_no', 'like', "%{$search}%"));
            });
        }

        $applications = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        $stats = [
            'all' => LoanApplication::count(),
            'submitted' => LoanApplication::where('status', LoanApplication::STATUS_SUBMITTED)->count(),
            'received' => LoanApplication::where('status', LoanApplication::STATUS_RECEIVED)->count(),
            'needs_revision' => LoanApplication::where('status', LoanApplication::STATUS_NEEDS_REVISION)->count(),
            'in_review' => LoanApplication::where('status', LoanApplication::STATUS_IN_REVIEW)->count(),
            'approved' => LoanApplication::where('status', LoanApplication::STATUS_APPROVED)->count(),
            'rejected' => LoanApplication::where('status', LoanApplication::STATUS_REJECTED)->count(),
        ];

        $tellers = User::where('role', User::ROLE_TELLER)->where('is_active', true)->get();

        return view('loans.applications', compact('applications', 'stats', 'tellers'));
    }

    public function showApplication(LoanApplication $application)
    {
        $application->load([
            'member.user',
            'member.savingsAccounts',
            'member.documents',
            'loanProduct',
            'approvals.user',
            'loan',
            'assignedTo',
            'assignedBy',
            'receivedBy',
        ]);

        $timeline = $this->loanService->getTimeline($application);
        $defaultChecklist = LoanApplication::defaultChecklistItems();
        $tellers = User::where('role', User::ROLE_TELLER)->where('is_active', true)->get();

        return view('loans.show_application', compact('application', 'timeline', 'defaultChecklist', 'tellers'));
    }

    public function claimApplication(Request $request, LoanApplication $application)
    {
        $user = Auth::user();
        if (!$user->isTeller() && !$user->isAdmin()) {
            abort(403, 'Hanya Teller atau Admin yang dapat mengambil antrean pengajuan.');
        }

        try {
            $this->loanService->claimByTeller($application, $user);
            return back()->with('success', "Pengajuan pinjaman {$application->application_no} berhasil Anda ambil dari antrean.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function receiveApplication(Request $request, LoanApplication $application)
    {
        $user = Auth::user();
        if (!$user->isTeller() && !$user->isAdmin()) {
            abort(403, 'Hanya Teller atau Admin yang berhak memproses penerimaan berkas.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'checklist' => 'required|array',
        ], [
            'checklist.required' => 'Checklist kelengkapan dokumen wajib diisi.',
        ]);

        try {
            $this->loanService->receiveByTeller($application, $user, $request->checklist, $request->notes);
            return back()->with('success', "Pengajuan pinjaman {$application->application_no} BERHASIL DITERIMA! Berkas telah diverifikasi lengkap dan diteruskan ke tim Analis Kredit.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function requestRevisionApplication(Request $request, LoanApplication $application)
    {
        $user = Auth::user();
        if (!$user->isTeller() && !$user->isAdmin()) {
            abort(403, 'Hanya Teller atau Admin yang berhak meminta perbaikan berkas.');
        }

        $request->validate([
            'revision_notes' => 'required|string|max:1000',
            'checklist' => 'nullable|array',
        ], [
            'revision_notes.required' => 'Catatan perbaikan / alasan pengembalian berkas wajib diisi.',
        ]);

        try {
            $this->loanService->requestRevisionByTeller($application, $user, $request->revision_notes, $request->checklist);
            return back()->with('warning', "Pengajuan pinjaman {$application->application_no} telah dikembalikan ke anggota untuk dilengkapi.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reassignTeller(Request $request, LoanApplication $application)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isManager()) {
            abort(403, 'Hanya Admin atau Manajer yang berwenang mengalihkan penugasan teller.');
        }

        $request->validate([
            'new_teller_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:500',
        ], [
            'new_teller_id.required' => 'Silakan pilih teller tujuan.',
            'reason.required' => 'Alasan pengalihan penugasan teller wajib dicantumkan.',
        ]);

        try {
            $newTeller = User::findOrFail($request->new_teller_id);
            $this->loanService->reassignTeller($application, $newTeller, $user, $request->reason);
            return back()->with('success', "Penugasan pengajuan {$application->application_no} berhasil dialihkan ke {$newTeller->name}.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function resubmitApplication(Request $request, LoanApplication $application)
    {
        $user = Auth::user();
        $member = $user->member;

        if (!$member || $application->member_id !== $member->id) {
            abort(403, 'Anda tidak memiliki hak untuk memperbarui pengajuan ini.');
        }

        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'purpose' => 'nullable|string|max:500',
        ]);

        try {
            $this->loanService->resubmitByMember($application, $member, $request->purpose, $request->notes);
            return redirect()->route('loans.my')->with('success', "Pengajuan pinjaman {$application->application_no} telah berhasil diperbarui dan dikirimkan kembali ke Teller!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reviewApplication(Request $request, LoanApplication $application)
    {
        $request->validate([
            'action' => 'required|in:recommend,reject',
            'notes' => 'required|string|max:1000',
            'recommendations' => 'nullable|string|max:1000',
        ]);

        try {
            $this->loanService->reviewByAnalyst($application, $request->action, $request->notes, $request->recommendations);
            return redirect()->route('loans.applications')->with('success', 'Hasil analisis kredit berhasil disimpan.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function approveApplication(Request $request, LoanApplication $application)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $this->loanService->approveByManager($application, $request->action, $request->notes);
            $msg = ($request->action === 'approve')
                ? 'Pengajuan pinjaman BERHASIL DISETUJUI! Pinjaman siap untuk penandatanganan akad dan pencairan.'
                : 'Pengajuan pinjaman ditolak.';
            return redirect()->route('loans.applications')->with('success', $msg);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function disburse(Request $request, Loan $loan)
    {
        if (!in_array(Auth::user()->role, ['teller', 'admin'])) {
            abort(403, 'Hanya Teller atau Admin yang dapat mencairkan dana pinjaman.');
        }

        try {
            $this->loanService->disburse($loan, $request->payment_method ?? 'cash');
            return back()->with('success', "Dana pinjaman {$loan->loan_no} berhasil dicairkan! Jadwal angsuran dan jurnal keuangan telah otomatis dibuat.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
