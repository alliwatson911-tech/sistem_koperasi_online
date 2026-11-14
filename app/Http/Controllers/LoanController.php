<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanCollectionLog;
use App\Models\LoanPayment;
use App\Models\LoanProduct;
use App\Models\LoanRestructuringRequest;
use App\Models\LoanSchedule;
use App\Models\Member;
use App\Models\User;
use App\Services\LoanCalculationService;
use App\Services\LoanService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            $defaultAmount = $selectedProduct->isDaily() ? 500000 : ($selectedProduct->isWeekly() ? 200000 : 10000000);
            $defaultTenor = $selectedProduct->isDaily() ? 14 : ($selectedProduct->isWeekly() ? 10 : 12);

            $amount = (int) $request->get('amount', $defaultAmount);
            $tenor = (int) $request->get('tenor', $defaultTenor);

            $method = $request->get('calculation_method', $selectedProduct->calculation_method);

            $adminFee = (int) round(($amount * ($selectedProduct->admin_fee_percentage / 100)) + $selectedProduct->admin_fee_fixed);
            $savingsDeduction = (int) round($amount * (($selectedProduct->savings_percentage ?? 0) / 100));
            $simulationResult = $this->calculator->simulate(
                $amount,
                $tenor,
                (float) $selectedProduct->interest_rate_per_period,
                $method,
                $adminFee,
                $selectedProduct->tenor_unit ?? 'month',
                null,
                $savingsDeduction
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

        $isBusinessProduct = ($product->code === 'PRD-MDR') 
            || (str_contains(strtolower((string) $request->purpose), 'modal') && !in_array($product->code, ['PRD-KNS', 'PRD-KPT', 'PRD-KNS-DAY', 'PRD-MLT-DAY'])) 
            || (str_contains(strtolower((string) $request->purpose), 'usaha') && !in_array($product->code, ['PRD-KNS', 'PRD-KPT', 'PRD-KNS-DAY', 'PRD-MLT-DAY']));

        $request->validate([
            'loan_product_id' => 'required|exists:loan_products,id',
            'amount' => "required|numeric|min:{$product->min_amount}|max:{$product->max_amount}",
            'purpose' => 'required|string|max:500',
            'agree_terms' => 'required|accepted',
            'business_name' => 'nullable|string|max:150',
            'business_type' => 'nullable|string|max:100',
            'business_address' => 'nullable|string|max:500',
            'business_duration_years' => 'nullable|numeric|min:0',
            'business_monthly_revenue' => 'nullable|numeric|min:0',
            'business_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'business_photo_camera' => 'nullable|string',
            'ktp_selfie' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'ktp_selfie_camera' => 'nullable|string',
        ], [
            'agree_terms.accepted' => 'Anda wajib menyetujui prinsip transparansi dan syarat pengajuan pinjaman.',
            'ktp_selfie.max' => 'Ukuran foto wajah dan e-KTP maksimal 5MB.',
        ]);

        // Verifikasi wajib foto wajah memegang e-KTP untuk produk selain pinjaman modal usaha
        if (!$isBusinessProduct) {
            if (!$request->hasFile('ktp_selfie') && !$request->filled('ktp_selfie_camera')) {
                return back()->withErrors(['ktp_selfie' => 'Wajib mengambil atau mengunggah foto wajah sambil memegang e-KTP untuk verifikasi identitas pengajuan pinjaman ini.'])->withInput();
            }
        }

        $minTenor = $product->min_tenor;
        $maxTenor = $product->max_tenor;
        $unitLabel = $product->tenor_unit_label;
        if ($tenor < $minTenor || $tenor > $maxTenor) {
            return back()->with('error', "Tenor pinjaman harus antara {$minTenor} hingga {$maxTenor} {$unitLabel}.")->withInput();
        }

        // Proses penyimpanan Foto Usaha (baik dari file upload maupun kamera capture)
        $businessPhotoPath = null;
        if ($request->hasFile('business_photo')) {
            $businessPhotoPath = $request->file('business_photo')->store('business_photos', 'public');
        } elseif ($request->filled('business_photo_camera')) {
            $dataUrl = $request->business_photo_camera;
            if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $typeMatches)) {
                $rawBase64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
                $ext = strtolower($typeMatches[1]);
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $binaryData = base64_decode($rawBase64);
                    if ($binaryData !== false) {
                        $filename = 'business_' . uniqid() . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
                        Storage::disk('public')->put('business_photos/' . $filename, $binaryData);
                        $businessPhotoPath = 'business_photos/' . $filename;
                    }
                }
            }
        }

        // Proses penyimpanan Foto Wajah Sambil Memegang KTP (Selfie KYC)
        $ktpSelfiePath = null;
        if ($request->hasFile('ktp_selfie')) {
            $ktpSelfiePath = $request->file('ktp_selfie')->store('ktp_selfies', 'public');
        } elseif ($request->filled('ktp_selfie_camera')) {
            $dataUrl = $request->ktp_selfie_camera;
            if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $typeMatches)) {
                $rawBase64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
                $ext = strtolower($typeMatches[1]);
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $binaryData = base64_decode($rawBase64);
                    if ($binaryData !== false) {
                        $filename = 'ktp_selfie_' . uniqid() . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
                        Storage::disk('public')->put('ktp_selfies/' . $filename, $binaryData);
                        $ktpSelfiePath = 'ktp_selfies/' . $filename;
                    }
                }
            }
        }

        $businessData = [
            'business_name' => $request->business_name,
            'business_type' => $request->business_type,
            'business_address' => $request->business_address,
            'business_duration_years' => $request->business_duration_years,
            'business_monthly_revenue' => $request->business_monthly_revenue,
            'business_photo_path' => $businessPhotoPath,
            'ktp_selfie_path' => $ktpSelfiePath,
        ];

        try {
            $application = $this->loanService->apply(
                $member,
                $product,
                (int) $request->amount,
                $tenor,
                $request->purpose,
                $businessData
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

        $relations = [
            'member.user',
            'loanProduct',
            'schedules.loan',
            'payments',
            'loanApplication.receivedBy',
            'disburser',
        ];

        if (\Illuminate\Support\Facades\Schema::hasTable('loan_collection_logs')) {
            $relations[] = 'collectionLogs.collector';
        }
        if (\Illuminate\Support\Facades\Schema::hasTable('loan_restructuring_requests')) {
            $relations[] = 'restructuringRequests.requester';
            $relations[] = 'restructuringRequests.decider';
        }

        $loan->load($relations);
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
            'survey_notes' => 'nullable|string|max:1000',
            'survey_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            if ($request->hasFile('survey_photo')) {
                $surveyPath = $request->file('survey_photo')->store('survey_photos', 'public');
                $application->survey_photo_path = $surveyPath;
            }
            if ($request->filled('survey_notes')) {
                $application->survey_notes = $request->survey_notes;
            }
            $application->save();

            $this->loanService->reviewByAnalyst($application, $request->action, $request->notes, $request->recommendations);
            return redirect()->route('loans.applications')->with('success', 'Hasil analisis kredit dan survei usaha berhasil disimpan.');
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

    /**
     * Dashboard & Pengelolaan Pinjaman Menunggak & Jatuh Tempo (Lintas Stakeholder).
     */
    public function manageOverdue(Request $request)
    {
        // Pemeriksaan ketahanan schema jika migrasi belum dijalankan di server
        $hasSchema = \Illuminate\Support\Facades\Schema::hasTable('loan_collection_logs')
            && \Illuminate\Support\Facades\Schema::hasTable('loan_restructuring_requests')
            && \Illuminate\Support\Facades\Schema::hasColumn('loans', 'collectibility_status');

        if (!$hasSchema) {
            $loans = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $stats = [
                'total_overdue' => 0,
                'total_due_soon' => 0,
                'total_penalty' => 0,
                'total_outstanding' => 0,
                'dpk_count' => 0,
                'kurang_lancar_count' => 0,
                'diragukan_count' => 0,
                'macet_count' => 0,
                'pending_restructuring' => 0,
            ];
            $pendingRestructurings = collect();
            $migrationNeeded = true;

            return view('loans.overdue_manage', compact('loans', 'stats', 'pendingRestructurings', 'migrationNeeded'));
        }

        $today = Carbon::today()->toDateString();
        $dueSoonDate = Carbon::today()->addDays(7)->toDateString();

        $query = Loan::with(['member.user', 'loanProduct', 'schedules', 'collectionLogs.user', 'restructuringRequests'])
            ->where(function ($q) use ($today, $dueSoonDate) {
                $q->where('status', Loan::STATUS_OVERDUE)
                  ->orWhere(function ($sq) use ($today, $dueSoonDate) {
                      $sq->where('status', Loan::STATUS_ACTIVE)
                         ->whereHas('schedules', function ($schedQ) use ($today, $dueSoonDate) {
                             $schedQ->whereIn('status', [LoanSchedule::STATUS_UNPAID, LoanSchedule::STATUS_PARTIAL])
                                    ->whereBetween('due_date', [$today, $dueSoonDate]);
                         });
                  });
            });

        // Filter status kolektibilitas
        if ($request->filled('collectibility')) {
            if ($request->collectibility === 'due_soon') {
                $query->where('status', Loan::STATUS_ACTIVE);
            } else {
                $query->where('collectibility_status', $request->collectibility);
            }
        }

        // Filter tahapan penagihan
        if ($request->filled('stage')) {
            $query->where('collection_stage', $request->stage);
        }

        // Filter pencarian anggota / nomor pinjaman
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('loan_no', 'like', "%{$search}%")
                  ->orWhereHas('member.user', fn($uq) => $uq->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('member', fn($mq) => $mq->where('member_no', 'like', "%{$search}%"));
            });
        }

        $loans = $query->orderBy('outstanding_penalty', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Statistik Ringkasan Portofolio Tunggakan
        $allOverdue = Loan::where('status', Loan::STATUS_OVERDUE)->get();
        $dueSoonCount = Loan::where('status', Loan::STATUS_ACTIVE)
            ->whereHas('schedules', function ($q) use ($today, $dueSoonDate) {
                $q->whereIn('status', [LoanSchedule::STATUS_UNPAID, LoanSchedule::STATUS_PARTIAL])
                  ->whereBetween('due_date', [$today, $dueSoonDate]);
            })->count();

        $stats = [
            'total_overdue' => $allOverdue->count(),
            'total_due_soon' => $dueSoonCount,
            'total_penalty' => (int) $allOverdue->sum('outstanding_penalty'),
            'total_outstanding' => (int) $allOverdue->sum('outstanding_principal'),
            'dpk_count' => $allOverdue->where('collectibility_status', 'dpk')->count(),
            'kurang_lancar_count' => $allOverdue->where('collectibility_status', 'kurang_lancar')->count(),
            'diragukan_count' => $allOverdue->where('collectibility_status', 'diragukan')->count(),
            'macet_count' => $allOverdue->where('collectibility_status', 'macet')->count(),
            'pending_restructuring' => LoanRestructuringRequest::where('status', LoanRestructuringRequest::STATUS_PENDING)->count(),
        ];

        $pendingRestructurings = LoanRestructuringRequest::with(['loan.member.user', 'requestedBy'])
            ->where('status', LoanRestructuringRequest::STATUS_PENDING)
            ->orderBy('id', 'desc')
            ->get();

        return view('loans.overdue_manage', compact('loans', 'stats', 'pendingRestructurings'));
    }

    /**
     * Mencatat tindakan penagihan (telepon, kunjungan, SP-1/2/3, janji bayar) oleh Analis/Petugas.
     */
    public function recordCollection(Request $request, Loan $loan)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['teller', 'analyst', 'manager', 'admin'])) {
            abort(403, 'Anda tidak berwenang mencatat riwayat penagihan.');
        }

        $request->validate([
            'action_type' => 'required|in:phone,whatsapp,field_visit,letter_sp1,letter_sp2,letter_sp3,mediation,promise_to_pay',
            'result_status' => 'required|in:promise_to_pay,partial_paid,unreachable,uncooperative,dispute,cooperative',
            'promised_date' => 'nullable|date|after_or_equal:today',
            'promised_amount' => 'nullable|numeric|min:0',
            'notes' => 'required|string|max:1000',
            'proof_photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'proof_photo_camera' => 'nullable|string',
        ]);

        $proofPath = null;
        if ($request->hasFile('proof_photo')) {
            $proofPath = $request->file('proof_photo')->store('collection_proofs', 'public');
        } elseif ($request->filled('proof_photo_camera')) {
            $dataUrl = $request->proof_photo_camera;
            if (preg_match('/^data:image\/(\w+);base64,/', $dataUrl, $typeMatches)) {
                $rawBase64 = substr($dataUrl, strpos($dataUrl, ',') + 1);
                $ext = strtolower($typeMatches[1]);
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                    $binaryData = base64_decode($rawBase64);
                    if ($binaryData !== false) {
                        $filename = 'proof_' . uniqid() . '.' . ($ext === 'jpeg' ? 'jpg' : $ext);
                        Storage::disk('public')->put('collection_proofs/' . $filename, $binaryData);
                        $proofPath = 'collection_proofs/' . $filename;
                    }
                }
            }
        }

        try {
            $this->loanService->recordCollectionAction($loan, $user, [
                'action_type' => $request->action_type,
                'result_status' => $request->result_status,
                'promised_date' => $request->promised_date,
                'promised_amount' => $request->promised_amount,
                'notes' => $request->notes,
                'proof_photo_path' => $proofPath,
            ]);

            return back()->with('success', "Aktivitas penagihan untuk pinjaman #{$loan->loan_no} berhasil dicatat!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Mengajukan permohonan keringanan / restrukturisasi denda (Anggota atau Analis).
     */
    public function requestRestructuring(Request $request, Loan $loan)
    {
        $user = Auth::user();
        if ($user->isMember() && $loan->member->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengajukan permohonan untuk pinjaman ini.');
        }

        $request->validate([
            'request_type' => 'required|in:penalty_waiver,tenor_extension,payment_reschedule',
            'requested_waiver_amount' => 'nullable|numeric|min:0|max:' . max(1, $loan->outstanding_penalty),
            'reason' => 'required|string|max:1000',
        ]);

        try {
            $waiverAmount = (int) ($request->requested_waiver_amount ?? 0);
            $this->loanService->requestRestructuring($loan, $user, $request->request_type, $waiverAmount, $request->reason);

            return back()->with('success', 'Permohonan keringanan pinjaman berhasil dikirim! Menunggu keputusan Manajer.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Memutuskan permohonan keringanan denda (Hanya Manajer / Admin).
     */
    public function decideRestructuring(Request $request, LoanRestructuringRequest $restructure)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['manager', 'admin'])) {
            abort(403, 'Hanya Manajer atau Administrator yang berwenang memutuskan permohonan keringanan.');
        }

        $request->validate([
            'decision' => 'required|in:approved,rejected',
            'approved_waiver_amount' => 'nullable|numeric|min:0|max:' . max(1, $restructure->loan->outstanding_penalty),
            'manager_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $approvedWaiver = (int) ($request->approved_waiver_amount ?? $restructure->requested_waiver_amount);
            $this->loanService->decideRestructuring($restructure, $user, $request->decision, $approvedWaiver, $request->manager_notes);

            $actionText = ($request->decision === 'approved') ? 'DISETUJUI' : 'DITOLAK';
            return back()->with('success', "Permohonan keringanan berhasil diputuskan: {$actionText}.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Eksekusi pembaruan status keterlambatan & kalkulasi denda harian manual.
     */
    public function syncOverdue()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['teller', 'analyst', 'manager', 'admin'])) {
            abort(403, 'Akses terbatas untuk staf koperasi.');
        }

        try {
            $count = $this->loanService->updateOverdueAndPenalties();
            return back()->with('success', "Sinkronisasi denda dan status tunggakan selesai! Sebanyak {$count} jadwal angsuran diperbarui.");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
