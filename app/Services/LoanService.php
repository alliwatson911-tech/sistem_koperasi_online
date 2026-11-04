<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanApproval;
use App\Models\LoanCollectionLog;
use App\Models\LoanPayment;
use App\Models\LoanProduct;
use App\Models\LoanRestructuringRequest;
use App\Models\LoanSchedule;
use App\Models\Member;
use App\Models\Notification;
use App\Models\Penalty;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LoanService
{
    public function __construct(
        protected LoanCalculationService $calculator,
        protected JournalService $journals
    ) {
    }

    /**
     * Pengajuan pinjaman online oleh anggota.
     */
    public function apply(Member $member, LoanProduct $product, int $amount, int $tenor, string $purpose, array $businessData = []): LoanApplication
    {
        if ($member->status !== Member::STATUS_ACTIVE) {
            throw new Exception("Hanya anggota aktif yang berhak mengajukan pinjaman koperasi.");
        }

        if ($amount < $product->min_amount || $amount > $product->max_amount) {
            throw new Exception("Nominal pengajuan harus antara Rp " . number_format($product->min_amount) . " dan Rp " . number_format($product->max_amount));
        }

        $minTenor = $product->min_tenor;
        $maxTenor = $product->max_tenor;
        $unitLabel = $product->tenor_unit_label;
        if ($tenor < $minTenor || $tenor > $maxTenor) {
            throw new Exception("Tenor pinjaman harus antara {$minTenor} hingga {$maxTenor} {$unitLabel}.");
        }

        $ratePerPeriod = (float) $product->interest_rate_per_period;
        $adminFee = (int) round(($amount * ($product->admin_fee_percentage / 100)) + $product->admin_fee_fixed);
        $sim = $this->calculator->simulate(
            $amount,
            $tenor,
            $ratePerPeriod,
            $product->calculation_method,
            $adminFee,
            $product->tenor_unit ?? 'month'
        );

        $appNo = 'APP-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        // Penugasan teller otomatis bila diaktifkan di pengaturan
        $assignedTellerId = null;
        $mode = Setting::get('teller_assignment_mode', 'queue_or_assign');
        if ($mode === 'auto_workload') {
            $candidate = User::where('role', User::ROLE_TELLER)
                ->where('is_active', true)
                ->withCount(['loanApplicationsAssigned' => function ($q) {
                    $q->whereIn('status', [LoanApplication::STATUS_SUBMITTED, LoanApplication::STATUS_NEEDS_REVISION]);
                }])
                ->orderBy('loan_applications_assigned_count', 'asc')
                ->first();
            if ($candidate) {
                $assignedTellerId = $candidate->id;
            }
        }

        $application = LoanApplication::create([
            'member_id' => $member->id,
            'loan_product_id' => $product->id,
            'application_no' => $appNo,
            'amount' => $amount,
            'tenor_unit' => $product->tenor_unit ?? 'month',
            'tenor_months' => ($product->isWeekly() ? (int) ceil($tenor / 4.33) : $tenor),
            'tenor_count' => $tenor,
            'purpose' => $purpose,
            'calculation_method' => $product->calculation_method,
            'interest_rate_monthly' => $product->interest_rate_monthly,
            'interest_rate_per_period' => $ratePerPeriod,
            'admin_fee' => $adminFee,
            'monthly_installment_estimate' => $sim['first_installment'] ?? $sim['first_month_installment'],
            'status' => LoanApplication::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'assigned_to' => $assignedTellerId,
            'assigned_at' => $assignedTellerId ? now() : null,
            'business_name' => $businessData['business_name'] ?? null,
            'business_type' => $businessData['business_type'] ?? null,
            'business_address' => $businessData['business_address'] ?? null,
            'business_duration_years' => !empty($businessData['business_duration_years']) ? (int) $businessData['business_duration_years'] : null,
            'business_monthly_revenue' => !empty($businessData['business_monthly_revenue']) ? (int) $businessData['business_monthly_revenue'] : null,
            'business_photo_path' => $businessData['business_photo_path'] ?? null,
        ]);

        AuditLog::log('applied', $application, null, $application->toArray());

        // Notifikasi ke Teller
        if ($assignedTellerId) {
            $teller = User::find($assignedTellerId);
            if ($teller) {
                $this->sendNotification(
                    $teller,
                    "Pengajuan Pinjaman Baru Ditugaskan: #{$application->application_no}",
                    "Pengajuan pinjaman baru dari {$member->user->name} sebesar Rp " . number_format($amount, 0, ',', '.') . " telah ditugaskan kepada Anda.",
                    'info',
                    route('loans.applications.show', $application->id)
                );
            }
        } else {
            // Beritahukan ke antrean seluruh teller aktif
            $tellers = User::where('role', User::ROLE_TELLER)->where('is_active', true)->get();
            foreach ($tellers as $t) {
                $this->sendNotification(
                    $t,
                    "Antrean Pengajuan Baru: #{$application->application_no}",
                    "Pengajuan baru sebesar Rp " . number_format($amount, 0, ',', '.') . " dari {$member->user->name} masuk antrean verifikasi teller.",
                    'info',
                    route('loans.applications.show', $application->id)
                );
            }
        }

        return $application;
    }

    /**
     * Teller mengambil pengajuan dari antrean (Claim Queue).
     */
    public function claimByTeller(LoanApplication $application, User $teller): LoanApplication
    {
        return DB::transaction(function () use ($application, $teller) {
            $app = LoanApplication::lockForUpdate()->find($application->id);

            if (!in_array($app->status, [LoanApplication::STATUS_SUBMITTED, LoanApplication::STATUS_NEEDS_REVISION])) {
                throw new Exception("Pengajuan ini tidak dalam status antrean verifikasi teller.");
            }

            if (!$teller->isTeller() && !$teller->isAdmin()) {
                throw new Exception("Hanya Teller atau Admin yang berhak mengambil antrean pengajuan.");
            }

            if ($app->assigned_to && $app->assigned_to !== $teller->id && !$teller->isAdmin()) {
                $assignedName = $app->assignedTo ? $app->assignedTo->name : 'teller lain';
                throw new Exception("Pengajuan ini telah ditugaskan kepada {$assignedName}.");
            }

            $app->update([
                'assigned_to' => $teller->id,
                'assigned_at' => now(),
            ]);

            AuditLog::log('teller_claimed', $app, null, [
                'assigned_to' => $teller->id,
                'teller_name' => $teller->name,
                'employee_code' => $teller->employee_code_formatted,
            ]);

            return $app;
        });
    }

    /**
     * Admin/Manajer mengalihkan penugasan teller dengan catatan alasan.
     */
    public function reassignTeller(LoanApplication $application, User $newTeller, User $assignedBy, string $reason): LoanApplication
    {
        if (empty(trim($reason))) {
            throw new Exception("Alasan pengalihan penugasan teller wajib dicantumkan.");
        }

        return DB::transaction(function () use ($application, $newTeller, $assignedBy, $reason) {
            $app = LoanApplication::lockForUpdate()->find($application->id);

            if (!$assignedBy->isAdmin() && !$assignedBy->isManager()) {
                throw new Exception("Hanya Admin atau Manajer yang berwenang mengalihkan penugasan teller.");
            }

            if (!$newTeller->isTeller() && !$newTeller->isAdmin()) {
                throw new Exception("Petugas tujuan pengalihan harus memiliki peran Teller.");
            }

            $oldTellerId = $app->assigned_to;
            $oldTellerName = $app->assignedTo ? $app->assignedTo->name : 'Belum Ditugaskan';

            $app->update([
                'assigned_to' => $newTeller->id,
                'assigned_by' => $assignedBy->id,
                'assigned_at' => now(),
                'assignment_notes' => $reason,
            ]);

            AuditLog::log('teller_reassigned', $app, ['assigned_to' => $oldTellerId], [
                'assigned_to' => $newTeller->id,
                'new_teller' => $newTeller->name,
                'old_teller' => $oldTellerName,
                'assigned_by' => $assignedBy->name,
                'reason' => $reason,
            ]);

            // Kirim notifikasi ke teller baru
            $this->sendNotification(
                $newTeller,
                "Pengalihan Tugas Pengajuan: #{$app->application_no}",
                "Pengajuan pinjaman #{$app->application_no} telah dialihkan kepada Anda oleh {$assignedBy->name}. Alasan: {$reason}",
                'info',
                route('loans.applications.show', $app->id)
            );

            return $app;
        });
    }

    /**
     * Verifikasi dan penerimaan pengajuan oleh Teller.
     */
    public function receiveByTeller(LoanApplication $application, User $teller, array $checklist, ?string $notes = null): LoanApplication
    {
        return DB::transaction(function () use ($application, $teller, $checklist, $notes) {
            $app = LoanApplication::lockForUpdate()->find($application->id);

            if (!in_array($app->status, [LoanApplication::STATUS_SUBMITTED, LoanApplication::STATUS_NEEDS_REVISION])) {
                throw new Exception("Pengajuan tidak dalam status antrean penerimaan teller.");
            }

            if (!$teller->isTeller() && !$teller->isAdmin()) {
                throw new Exception("Hanya Teller atau Admin yang berhak memproses penerimaan berkas pengajuan.");
            }

            // Validasi penugasan: Hanya teller yang ditugaskan (atau jika belum ada penugasan) yang boleh menerima
            if ($app->assigned_to && $app->assigned_to !== $teller->id && !$teller->isAdmin()) {
                $assignedName = $app->assignedTo ? $app->assignedTo->name : 'teller lain';
                throw new Exception("Pengajuan ini telah ditugaskan kepada {$assignedName}. Anda tidak berhak memprosesnya kecuali dialihkan oleh Admin.");
            }

            // Validasi kelengkapan dokumen wajib dari checklist
            $defaultItems = LoanApplication::defaultChecklistItems();
            foreach ($defaultItems as $key => $meta) {
                if (!empty($meta['required']) && empty($checklist[$key])) {
                    throw new Exception("Dokumen '{$meta['label']}' wajib diverifikasi dan dicentang sebelum diterima.");
                }
            }

            $oldValues = $app->only(['status', 'received_by', 'received_at']);

            $app->update([
                'status' => LoanApplication::STATUS_RECEIVED,
                'received_by' => $teller->id,
                'received_at' => now(),
                'reception_checklist' => $checklist,
                'reception_note' => $notes,
                'reception_status' => 'complete',
                'assigned_to' => $app->assigned_to ?? $teller->id,
                'assigned_at' => $app->assigned_at ?? now(),
            ]);
            $application->refresh();

            AuditLog::log('teller_received', $app, $oldValues, [
                'status' => LoanApplication::STATUS_RECEIVED,
                'received_by' => $teller->id,
                'teller_name' => $teller->name,
                'teller_code' => $teller->employee_code_formatted,
                'checklist' => $checklist,
                'note' => $notes,
            ]);

            // Notifikasi ke anggota pemohon
            if ($app->member && $app->member->user) {
                $this->sendNotification(
                    $app->member->user,
                    "Pengajuan Pinjaman Telah Diterima Teller",
                    "Pengajuan pinjaman Anda (#{$app->application_no}) telah diverifikasi lengkap dan diterima oleh Teller {$teller->name} ({$teller->employee_code_formatted}). Berkas diteruskan ke tim Analis Kredit.",
                    'success',
                    route('loans.my')
                );
            }

            // Notifikasi ke Analis Kredit
            $analysts = User::where('role', User::ROLE_ANALYST)->where('is_active', true)->get();
            foreach ($analysts as $analyst) {
                $this->sendNotification(
                    $analyst,
                    "Pengajuan Baru Siap Dianalisis: #{$app->application_no}",
                    "Pengajuan pinjaman dari {$app->member->user->name} sebesar Rp " . number_format($app->amount, 0, ',', '.') . " telah diverifikasi Teller dan siap dianalisis kelayakannya.",
                    'info',
                    route('loans.applications.show', $app->id)
                );
            }

            return $app;
        });
    }

    /**
     * Teller mengembalikan pengajuan ke anggota karena dokumen tidak lengkap (needs_revision).
     */
    public function requestRevisionByTeller(LoanApplication $application, User $teller, string $revisionNotes, ?array $checklist = null): LoanApplication
    {
        if (empty(trim($revisionNotes))) {
            throw new Exception("Catatan perbaikan / alasan pengembalian berkas wajib dicantumkan.");
        }

        return DB::transaction(function () use ($application, $teller, $revisionNotes, $checklist) {
            $app = LoanApplication::lockForUpdate()->find($application->id);

            if (!in_array($app->status, [LoanApplication::STATUS_SUBMITTED, LoanApplication::STATUS_RECEIVED])) {
                throw new Exception("Pengajuan tidak dalam status yang dapat dikembalikan untuk revisi.");
            }

            if (!$teller->isTeller() && !$teller->isAdmin()) {
                throw new Exception("Hanya Teller atau Admin yang berhak meminta perbaikan berkas.");
            }

            if ($app->assigned_to && $app->assigned_to !== $teller->id && !$teller->isAdmin()) {
                $assignedName = $app->assignedTo ? $app->assignedTo->name : 'teller lain';
                throw new Exception("Pengajuan ini telah ditugaskan kepada {$assignedName}.");
            }

            $oldValues = $app->only(['status', 'revision_notes']);

            $app->update([
                'status' => LoanApplication::STATUS_NEEDS_REVISION,
                'reception_status' => 'needs_revision',
                'revision_requested_at' => now(),
                'revision_notes' => $revisionNotes,
                'received_by' => $teller->id,
                'received_at' => now(),
                'reception_note' => $revisionNotes,
                'reception_checklist' => $checklist,
            ]);

            AuditLog::log('revision_requested', $app, $oldValues, [
                'status' => LoanApplication::STATUS_NEEDS_REVISION,
                'requested_by' => $teller->id,
                'teller_name' => $teller->name,
                'notes' => $revisionNotes,
            ]);

            // Notifikasi ke anggota pemohon
            if ($app->member && $app->member->user) {
                $this->sendNotification(
                    $app->member->user,
                    "Pengajuan Pinjaman Membutuhkan Perbaikan Dokumen",
                    "Pengajuan pinjaman Anda (#{$app->application_no}) dikembalikan oleh Teller {$teller->name} ({$teller->employee_code_formatted}) untuk dilengkapi. Catatan Teller: \"{$revisionNotes}\"",
                    'warning',
                    route('loans.my')
                );
            }

            return $app;
        });
    }

    /**
     * Anggota melengkapi dan mengirimkan ulang berkas pengajuan (Resubmit).
     */
    public function resubmitByMember(LoanApplication $application, Member $member, ?string $purpose = null, ?string $notes = null): LoanApplication
    {
        return DB::transaction(function () use ($application, $member, $purpose, $notes) {
            $app = LoanApplication::lockForUpdate()->find($application->id);

            if ($app->member_id !== $member->id) {
                throw new Exception("Anda tidak memiliki hak untuk memperbarui pengajuan pinjaman ini.");
            }

            if ($app->status !== LoanApplication::STATUS_NEEDS_REVISION) {
                throw new Exception("Pengajuan pinjaman tidak dalam status membutuhkan perbaikan (needs_revision).");
            }

            $data = [
                'status' => LoanApplication::STATUS_SUBMITTED,
                'submitted_at' => now(),
            ];

            if (!empty($purpose)) {
                $data['purpose'] = $purpose;
            }
            if (!empty($notes)) {
                $data['notes'] = $notes;
            }

            $app->update($data);

            AuditLog::log('resubmitted_by_member', $app, null, [
                'member_name' => $member->user->name,
                'notes' => $notes,
            ]);

            // Notifikasi ke teller yang ditugaskan
            if ($app->assignedTo) {
                $this->sendNotification(
                    $app->assignedTo,
                    "Pengajuan Telah Dilengkapi Anggota: #{$app->application_no}",
                    "Anggota {$member->user->name} telah memperbarui dan mengirimkan ulang pengajuan pinjaman #{$app->application_no}. Silakan lakukan verifikasi ulang berkas.",
                    'info',
                    route('loans.applications.show', $app->id)
                );
            }

            return $app;
        });
    }

    /**
     * Analisis berkas dan kelayakan oleh Analis Kredit.
     */
    public function reviewByAnalyst(LoanApplication $application, string $action, ?string $notes = null, ?string $recommendations = null): LoanApproval
    {
        return DB::transaction(function () use ($application, $action, $notes, $recommendations) {
            $user = auth()->user();
            $app = LoanApplication::lockForUpdate()->find($application->id);
            $application->refresh();

            if ($user && !$user->isAnalyst() && !$user->isAdmin()) {
                throw new Exception("Hanya Analis Kredit atau Admin yang berhak melakukan analisis kelayakan pinjaman.");
            }

            // Validasi Maker-Checker: Teller yang menerima pengajuan tidak boleh merangkap sebagai Analis Kredit
            if ($user && $app->received_by && $user->id === $app->received_by) {
                throw new Exception("Prinsip Pemisahan Tugas (Maker-Checker): Petugas yang menerima berkas sebagai Teller tidak boleh merangkap sebagai Analis Kredit untuk pinjaman yang sama.");
            }

            // Validasi Status: Pengajuan harus sudah diterima oleh Teller terlebih dahulu
            if (!in_array($app->status, [LoanApplication::STATUS_RECEIVED, LoanApplication::STATUS_IN_REVIEW])) {
                throw new Exception("Pengajuan pinjaman harus diterima dan diverifikasi kelengkapannya oleh Teller terlebih dahulu sebelum dianalisis.");
            }

            $approval = LoanApproval::create([
                'loan_application_id' => $app->id,
                'user_id' => auth()->id() ?? 1,
                'role' => User::ROLE_ANALYST,
                'action' => $action, // recommend | reject
                'notes' => $notes,
                'recommendations' => $recommendations,
            ]);

            $newStatus = ($action === 'recommend') ? LoanApplication::STATUS_IN_REVIEW : LoanApplication::STATUS_REJECTED;
            $app->update(['status' => $newStatus]);
            $application->refresh();

            AuditLog::log('analyst_reviewed', $app, null, ['action' => $action, 'notes' => $notes, 'recommendations' => $recommendations]);

            // Notifikasi ke Manajer jika direkomendasikan
            if ($action === 'recommend') {
                $managers = User::where('role', User::ROLE_MANAGER)->where('is_active', true)->get();
                $analystName = $user ? $user->name : 'Analis Kredit';
                foreach ($managers as $manager) {
                    $this->sendNotification(
                        $manager,
                        "Rekomendasi Pinjaman Masuk: #{$app->application_no}",
                        "Analis {$analystName} merekomendasikan persetujuan pinjaman #{$app->application_no} ({$app->member->user->name}). Membutuhkan keputusan Manajer.",
                        'info',
                        route('loans.applications.show', $app->id)
                    );
                }
            }

            return $approval;
        });
    }

    /**
     * Persetujuan akhir berjenjang oleh Manajer Koperasi.
     */
    public function approveByManager(LoanApplication $application, string $action, ?string $notes = null): LoanApproval
    {
        return DB::transaction(function () use ($application, $action, $notes) {
            $app = LoanApplication::lockForUpdate()->find($application->id);
            $application->refresh();
            $user = auth()->user();
            if ($user && !$user->isManager() && !$user->isAdmin()) {
                throw new Exception("Hanya Manajer atau Admin yang memiliki otorisasi persetujuan pinjaman.");
            }

            // Validasi Maker-Checker: Teller yang menerima berkas tidak boleh menyetujui pinjaman
            if ($user && $app->received_by && $user->id === $app->received_by) {
                throw new Exception("Prinsip Pemisahan Tugas (Maker-Checker): Teller yang menerima pengajuan tidak boleh menyetujui pinjaman yang ia terima sendiri.");
            }

            // Validasi Maker-Checker: Analis Kredit yang merekomendasikan tidak boleh menyetujui sebagai Manajer
            $analystId = $app->approvals()->where('role', User::ROLE_ANALYST)->value('user_id');
            if ($user && $analystId && $user->id === $analystId) {
                throw new Exception("Prinsip Pemisahan Tugas (Maker-Checker): Analis Kredit yang merekomendasikan pinjaman tidak boleh merangkap sebagai Manajer yang menyetujui.");
            }

            $approval = LoanApproval::create([
                'loan_application_id' => $app->id,
                'user_id' => auth()->id() ?? 1,
                'role' => User::ROLE_MANAGER,
                'action' => $action, // approve | reject
                'notes' => $notes,
            ]);

            if ($action === 'approve') {
                $app->update(['status' => LoanApplication::STATUS_APPROVED]);
                $application->refresh();

                $sim = $this->calculator->simulate(
                    $app->amount,
                    $app->tenor_count,
                    (float) $app->interest_rate_per_period,
                    $app->calculation_method,
                    $app->admin_fee,
                    $app->tenor_unit ?? 'month'
                );

                $loanNo = 'LOAN-' . date('Ymd') . '-' . strtoupper(Str::random(5));
                Loan::create([
                    'loan_application_id' => $app->id,
                    'member_id' => $app->member_id,
                    'loan_product_id' => $app->loan_product_id,
                    'loan_no' => $loanNo,
                    'principal_amount' => $app->amount,
                    'total_interest' => $sim['total_interest'],
                    'total_admin_fee' => $app->admin_fee,
                    'total_loan_amount' => $app->amount + $sim['total_interest'],
                    'outstanding_principal' => $app->amount,
                    'outstanding_interest' => $sim['total_interest'],
                    'outstanding_penalty' => 0,
                    'tenor_unit' => $app->tenor_unit ?? 'month',
                    'tenor_months' => $app->tenor_months,
                    'tenor_count' => $app->tenor_count,
                    'interest_rate_monthly' => $app->interest_rate_monthly,
                    'interest_rate_per_period' => $app->interest_rate_per_period,
                    'calculation_method' => $app->calculation_method,
                    'status' => Loan::STATUS_AWAITING_DISBURSEMENT,
                    'business_name' => $app->business_name,
                    'business_type' => $app->business_type,
                    'business_address' => $app->business_address,
                    'business_duration_years' => $app->business_duration_years,
                    'business_monthly_revenue' => $app->business_monthly_revenue,
                    'business_photo_path' => $app->business_photo_path,
                    'survey_photo_path' => $app->survey_photo_path,
                    'collectibility_status' => 'lancar',
                    'collection_stage' => 'normal',
                ]);

                // Notifikasi ke anggota untuk menandatangani akad digital
                if ($app->member && $app->member->user) {
                    $this->sendNotification(
                        $app->member->user,
                        "Pengajuan Pinjaman Disetujui!",
                        "Pengajuan pinjaman Anda (#{$app->application_no}) telah DISETUJUI oleh Manajer. Silakan masuk ke akun untuk membaca dan menandatangani akad perjanjian pinjaman digital.",
                        'success',
                        route('loans.my')
                    );
                }
            } else {
                $app->update(['status' => LoanApplication::STATUS_REJECTED]);
                $application->refresh();

                if ($app->member && $app->member->user) {
                    $this->sendNotification(
                        $app->member->user,
                        "Pemberitahuan Status Pengajuan Pinjaman",
                        "Pengajuan pinjaman Anda (#{$app->application_no}) belum dapat disetujui oleh Manajer Koperasi. Catatan: {$notes}",
                        'danger',
                        route('loans.my')
                    );
                }
            }

            AuditLog::log('manager_decided', $app, null, ['action' => $action, 'notes' => $notes]);

            return $approval;
        });
    }

    /**
     * Anggota menandatangani akad perjanjian pinjaman.
     */
    public function signAgreement(Loan $loan): Loan
    {
        if ($loan->agreed_at) {
            throw new Exception("Akad pinjaman ini sudah ditandatangani sebelumnya.");
        }

        $loan->update(['agreed_at' => now()]);
        AuditLog::log('agreement_signed', $loan);

        return $loan;
    }

    /**
     * Pencairan dana pinjaman (Disbursement) oleh Teller/Petugas.
     */
    public function disburse(Loan $loan, string $paymentMethod = 'cash'): Loan
    {
        return DB::transaction(function () use ($loan, $paymentMethod) {
            $loan = Loan::lockForUpdate()->find($loan->id);

            if ($loan->status !== Loan::STATUS_AWAITING_DISBURSEMENT) {
                throw new Exception("Pinjaman tidak dalam status siap dicairkan.");
            }

            if (!$loan->agreed_at) {
                throw new Exception("Akad pinjaman belum ditandatangani oleh anggota.");
            }

            // Validasi Maker-Checker: Manajer yang menyetujui tidak boleh mencairkan dana
            $user = auth()->user();
            if ($user) {
                $managerApproverId = $loan->loanApplication?->approvals()->where('role', User::ROLE_MANAGER)->where('action', 'approve')->value('user_id');
                if ($managerApproverId && $user->id === $managerApproverId) {
                    throw new Exception("Prinsip Pemisahan Tugas (Maker-Checker): Manajer yang menyetujui pinjaman tidak boleh mencairkan dana untuk pinjaman yang sama.");
                }
            }

            $startDate = now();
            $sim = $this->calculator->simulate(
                $loan->principal_amount,
                $loan->tenor_count,
                (float) $loan->interest_rate_per_period,
                $loan->calculation_method,
                $loan->total_admin_fee,
                $loan->tenor_unit ?? 'month',
                $startDate
            );

            // Bentuk jadwal angsuran (Loan Schedules) dengan aritmetika tanggal kalender sesungguhnya
            foreach ($sim['schedules'] as $s) {
                LoanSchedule::create([
                    'loan_id' => $loan->id,
                    'installment_no' => $s['installment_no'],
                    'due_date' => $s['due_date'],
                    'principal_amount' => $s['principal_amount'],
                    'interest_amount' => $s['interest_amount'],
                    'total_amount' => $s['total_amount'],
                    'paid_principal' => 0,
                    'paid_interest' => 0,
                    'paid_penalty' => 0,
                    'remaining_principal' => $s['remaining_principal'],
                    'status' => LoanSchedule::STATUS_UNPAID,
                ]);
            }

            $lastSchedule = end($sim['schedules']);
            $maturityDate = $lastSchedule['due_date'] ?? $startDate->copy()->addMonths($loan->tenor_count)->toDateString();

            $loan->update([
                'status' => Loan::STATUS_ACTIVE,
                'disbursed_at' => now(),
                'disbursed_by' => auth()->id(),
                'maturity_date' => $maturityDate,
            ]);

            // Jurnal double-entry pencairan pinjaman:
            // Debit: Piutang Pinjaman Anggota (1201) [Plafon Pokok]
            // Credit: Kas/Bank (1101/1102) [Plafon dikurangi biaya admin]
            // Credit: Pendapatan Administrasi (4102) [Biaya admin]
            $kasCode = $paymentMethod === 'transfer' ? '1102' : '1101';
            $disbursedCash = $loan->principal_amount - $loan->total_admin_fee;

            $this->journals->record(
                "Pencairan Pinjaman Anggota #{$loan->loan_no} ({$loan->member->user->name})",
                [
                    ['account_code' => '1201', 'type' => 'debit', 'amount' => $loan->principal_amount],
                    ['account_code' => $kasCode, 'type' => 'credit', 'amount' => $disbursedCash],
                    ['account_code' => '4102', 'type' => 'credit', 'amount' => $loan->total_admin_fee],
                ],
                'loan_disbursement',
                $loan->id
            );

            AuditLog::log('disbursed', $loan);

            return $loan;
        });
    }

    /**
     * Pembayaran angsuran pinjaman (Partial payment / Full payment).
     */
    public function payInstallment(Loan $loan, int $amount, string $paymentMethod = 'cash', ?string $proofFile = null, ?string $notes = null): LoanPayment
    {
        if ($amount <= 0) {
            throw new Exception("Nominal pembayaran harus lebih dari Rp 0.");
        }

        return DB::transaction(function () use ($loan, $amount, $paymentMethod, $proofFile, $notes) {
            $loan = Loan::lockForUpdate()->find($loan->id);

            if (!in_array($loan->status, [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])) {
                throw new Exception("Pinjaman tidak dalam status aktif atau menunggak.");
            }

            $remainingAmount = $amount;
            $totalPrincipalPaid = 0;
            $totalInterestPaid = 0;
            $totalPenaltyPaid = 0;

            $schedules = $loan->schedules()->whereIn('status', [LoanSchedule::STATUS_UNPAID, LoanSchedule::STATUS_PARTIAL, LoanSchedule::STATUS_OVERDUE])->orderBy('installment_no')->get();

            foreach ($schedules as $schedule) {
                if ($remainingAmount <= 0) {
                    break;
                }

                // 1. Alokasi ke Denda
                $unpaidPenalty = $schedule->penalty_amount - $schedule->paid_penalty;
                if ($unpaidPenalty > 0 && $remainingAmount > 0) {
                    $payPenalty = min($remainingAmount, $unpaidPenalty);
                    $schedule->paid_penalty += $payPenalty;
                    $remainingAmount -= $payPenalty;
                    $totalPenaltyPaid += $payPenalty;
                }

                // 2. Alokasi ke Bunga
                $unpaidInterest = $schedule->interest_amount - $schedule->paid_interest;
                if ($unpaidInterest > 0 && $remainingAmount > 0) {
                    $payInterest = min($remainingAmount, $unpaidInterest);
                    $schedule->paid_interest += $payInterest;
                    $remainingAmount -= $payInterest;
                    $totalInterestPaid += $payInterest;
                }

                // 3. Alokasi ke Pokok
                $unpaidPrincipal = $schedule->principal_amount - $schedule->paid_principal;
                if ($unpaidPrincipal > 0 && $remainingAmount > 0) {
                    $payPrincipal = min($remainingAmount, $unpaidPrincipal);
                    $schedule->paid_principal += $payPrincipal;
                    $remainingAmount -= $payPrincipal;
                    $totalPrincipalPaid += $payPrincipal;
                }

                // Update status jadwal
                $isScheduleFull = ($schedule->paid_principal >= $schedule->principal_amount)
                    && ($schedule->paid_interest >= $schedule->interest_amount)
                    && ($schedule->paid_penalty >= $schedule->penalty_amount);

                if ($isScheduleFull) {
                    $schedule->status = LoanSchedule::STATUS_PAID;
                    $schedule->paid_at = now();
                } else {
                    $schedule->status = LoanSchedule::STATUS_PARTIAL;
                }
                $schedule->save();
            }

            // Update saldo pinjaman
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

            // Catat transaksi pembayaran
            $paymentNo = 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            $payment = LoanPayment::create([
                'loan_id' => $loan->id,
                'payment_no' => $paymentNo,
                'payment_date' => now()->toDateString(),
                'amount' => $amount,
                'principal_portion' => $totalPrincipalPaid,
                'interest_portion' => $totalInterestPaid,
                'penalty_portion' => $totalPenaltyPaid,
                'payment_method' => $paymentMethod,
                'proof_file' => $proofFile,
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'notes' => $notes,
            ]);

            // Jurnal double-entry pembayaran angsuran:
            // Debit: Kas/Bank (1101/1102) [Total Amount]
            // Credit: Piutang Pinjaman Anggota (1201) [Porsi Pokok]
            // Credit: Pendapatan Bunga Pinjaman (4101) [Porsi Bunga]
            // Credit: Pendapatan Denda Keterlambatan (4103) [Porsi Denda jika ada]
            $kasCode = $paymentMethod === 'transfer' ? '1102' : '1101';
            $journalEntries = [
                ['account_code' => $kasCode, 'type' => 'debit', 'amount' => $amount],
                ['account_code' => '1201', 'type' => 'credit', 'amount' => $totalPrincipalPaid],
                ['account_code' => '4101', 'type' => 'credit', 'amount' => $totalInterestPaid],
            ];

            if ($totalPenaltyPaid > 0) {
                $journalEntries[] = ['account_code' => '4103', 'type' => 'credit', 'amount' => $totalPenaltyPaid];
            }

            // Bila ada kelebihan bayar yang belum terserap jadwal angsuran, kreditkan ke Simpanan Sukarela anggota
            if ($remainingAmount > 0) {
                $sukarelaAcc = $loan->member->getSimpananSukarelaAccount();
                if ($sukarelaAcc) {
                    $sukarelaAcc->balance += $remainingAmount;
                    $sukarelaAcc->save();
                    $journalEntries[] = ['account_code' => '2103', 'type' => 'credit', 'amount' => $remainingAmount];
                }
            }

            $this->journals->record(
                "Pembayaran Angsuran Pinjaman #{$loan->loan_no} [No: {$payment->payment_no}]",
                $journalEntries,
                'loan_payment',
                $payment->id
            );

            AuditLog::log('loan_payment_recorded', $payment);

            return $payment;
        });
    }

    /**
     * Pelunasan dipercepat pinjaman (Early Repayment).
     * Anggota melunasi seluruh sisa pokok berjalan dan denda jika ada.
     * Bunga masa depan yang belum jatuh tempo dihapuskan (diskon/rebate bunga transparan).
     */
    public function earlyRepayment(Loan $loan, string $paymentMethod = 'cash', ?string $notes = null): LoanPayment
    {
        return DB::transaction(function () use ($loan, $paymentMethod, $notes) {
            $loan = Loan::lockForUpdate()->find($loan->id);

            if (!in_array($loan->status, [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])) {
                throw new Exception("Pinjaman tidak dalam status aktif atau menunggak untuk pelunasan dipercepat.");
            }

            // Hitung sisa pokok dan denda
            $payoffPrincipal = $loan->outstanding_principal;
            $payoffPenalty = $loan->outstanding_penalty;

            // Bunga berjalan yang telah jatuh tempo (jika ada jadwal overdue)
            $overdueInterest = (int) $loan->schedules()
                ->where('status', LoanSchedule::STATUS_OVERDUE)
                ->sum(DB::raw('interest_amount - paid_interest'));

            $totalPayoff = $payoffPrincipal + $payoffPenalty + $overdueInterest;

            // Tandai seluruh jadwal belum lunas menjadi lunas
            $schedules = $loan->schedules()->whereIn('status', [
                LoanSchedule::STATUS_UNPAID,
                LoanSchedule::STATUS_PARTIAL,
                LoanSchedule::STATUS_OVERDUE
            ])->get();

            foreach ($schedules as $s) {
                $s->paid_principal = $s->principal_amount;
                $s->paid_penalty = $s->penalty_amount;
                if ($s->status === LoanSchedule::STATUS_OVERDUE) {
                    $s->paid_interest = $s->interest_amount;
                }
                $s->status = LoanSchedule::STATUS_PAID;
                $s->paid_at = now();
                $s->save();
            }

            // Update pinjaman menjadi lunas
            $loan->outstanding_principal = 0;
            $loan->outstanding_interest = 0;
            $loan->outstanding_penalty = 0;
            $loan->status = Loan::STATUS_PAID_OFF;
            $loan->save();

            // Catat transaksi pembayaran
            $paymentNo = 'PAY-ER-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            $payment = LoanPayment::create([
                'loan_id' => $loan->id,
                'payment_no' => $paymentNo,
                'payment_date' => now()->toDateString(),
                'amount' => $totalPayoff,
                'principal_portion' => $payoffPrincipal,
                'interest_portion' => $overdueInterest,
                'penalty_portion' => $payoffPenalty,
                'payment_method' => $paymentMethod,
                'status' => 'verified',
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'notes' => $notes ?? 'Pelunasan Dipercepat (Early Repayment) dengan pembebasan bunga masa depan.',
            ]);

            // Jurnal akuntansi pelunasan dipercepat
            $kasCode = ($paymentMethod === 'transfer') ? '1102' : '1101';
            $journalEntries = [
                ['account_code' => $kasCode, 'type' => 'debit', 'amount' => $totalPayoff],
                ['account_code' => '1201', 'type' => 'credit', 'amount' => $payoffPrincipal],
            ];

            if ($overdueInterest > 0) {
                $journalEntries[] = ['account_code' => '4101', 'type' => 'credit', 'amount' => $overdueInterest];
            }
            if ($payoffPenalty > 0) {
                $journalEntries[] = ['account_code' => '4103', 'type' => 'credit', 'amount' => $payoffPenalty];
            }

            $this->journals->record(
                "Pelunasan Dipercepat Pinjaman #{$loan->loan_no} [No: {$payment->payment_no}]",
                $journalEntries,
                'loan_early_repayment',
                $payment->id
            );

            AuditLog::log('early_repayment_completed', $payment, null, [
                'total_amount' => $totalPayoff,
                'principal_settled' => $payoffPrincipal,
                'waived_future_interest' => $loan->total_interest - $overdueInterest,
            ]);

            return $payment;
        });
    }

    /**
     * Hitung denda harian dan update status menunggak untuk seluruh pinjaman aktif.
     */
    public function updateOverdueAndPenalties(): int
    {
        $today = Carbon::today();
        $updatedCount = 0;

        $unpaidSchedules = LoanSchedule::whereIn('status', [LoanSchedule::STATUS_UNPAID, LoanSchedule::STATUS_PARTIAL, LoanSchedule::STATUS_OVERDUE])
            ->where('due_date', '<', $today)
            ->with(['loan.loanProduct'])
            ->get();

        foreach ($unpaidSchedules as $schedule) {
            $daysOverdue = max(1, (int) abs($today->diffInDays($schedule->due_date)));
            $product = $schedule->loan->loanProduct;
            $dailyRate = (float) $product->penalty_rate_daily;
            $rateMultiplier = ($dailyRate > 0.05) ? ($dailyRate / 100.0) : $dailyRate;

            // Denda = tarif denda per hari * pokok angsuran * hari terlambat
            // Tidak berbunga-berbunga, maksimal 10% per angsuran sesuai AD/ART & regulasi perlindungan anggota
            $unpaidPrincipal = $schedule->principal_amount - $schedule->paid_principal;
            $maxPenalty = (int) round($unpaidPrincipal * 0.10);
            $rawPenalty = (int) round($unpaidPrincipal * $rateMultiplier * $daysOverdue);
            $calculatedPenalty = min($maxPenalty, $rawPenalty);

            $schedule->status = LoanSchedule::STATUS_OVERDUE;
            $schedule->days_overdue = $daysOverdue;
            $schedule->penalty_amount = $calculatedPenalty;
            $schedule->save();

            // Catat ke tabel penalties jika denda > 0
            if ($calculatedPenalty > 0) {
                Penalty::updateOrCreate(
                    [
                        'loan_id' => $schedule->loan_id,
                        'loan_schedule_id' => $schedule->id,
                        'calculated_date' => $today->toDateString(),
                    ],
                    [
                        'days_overdue' => $daysOverdue,
                        'daily_rate' => $dailyRate,
                        'amount' => $calculatedPenalty,
                        'is_settled' => false,
                    ]
                );
            }

            // Tandai pinjaman berstatus overdue & perbarui outstanding_penalty dan kolektibilitas
            $loan = $schedule->loan;
            $loan->outstanding_penalty = (int) $loan->schedules()->sum('penalty_amount');
            $loan->status = Loan::STATUS_OVERDUE;

            $maxDays = (int) $loan->schedules()->where('status', LoanSchedule::STATUS_OVERDUE)->max('days_overdue');
            if ($maxDays <= 0) {
                $loan->collectibility_status = 'lancar';
            } elseif ($maxDays <= 30) {
                $loan->collectibility_status = 'dpk';
                if ($loan->collection_stage === 'normal') {
                    $loan->collection_stage = 'reminder';
                }
            } elseif ($maxDays <= 60) {
                $loan->collectibility_status = 'kurang_lancar';
                if (in_array($loan->collection_stage, ['normal', 'reminder'])) {
                    $loan->collection_stage = 'sp1';
                }
            } elseif ($maxDays <= 90) {
                $loan->collectibility_status = 'diragukan';
                if (in_array($loan->collection_stage, ['normal', 'reminder', 'sp1'])) {
                    $loan->collection_stage = 'sp2';
                }
            } else {
                $loan->collectibility_status = 'macet';
                if (in_array($loan->collection_stage, ['normal', 'reminder', 'sp1', 'sp2'])) {
                    $loan->collection_stage = 'sp3';
                }
            }

            $loan->save();

            $updatedCount++;
        }

        return $updatedCount;
    }

    /**
     * Membangun lini masa penanganan (timeline) pinjaman dari jejak audit dan riwayat sistem.
     */
    public function getTimeline(Loan|LoanApplication $subject): array
    {
        $application = ($subject instanceof Loan) ? $subject->loanApplication : $subject;
        $loan = ($subject instanceof Loan) ? $subject : $subject->loan;

        $items = [];

        // Query log audit
        $appLogs = $application
            ? AuditLog::where('entity_type', LoanApplication::class)->where('entity_id', $application->id)->with('user')->orderBy('created_at')->get()
            : collect();

        $loanLogs = $loan
            ? AuditLog::where('entity_type', Loan::class)->where('entity_id', $loan->id)->with('user')->orderBy('created_at')->get()
            : collect();

        // 1. Diajukan oleh Anggota
        $appliedLog = $appLogs->firstWhere('action', 'applied');
        $items[] = [
            'stage' => 'applied',
            'title' => 'Pengajuan Pinjaman Diajukan',
            'subtitle' => 'Anggota mengisi formulir dan mengajukan pinjaman secara online',
            'actor_name' => $application?->member?->user?->name ?? 'Anggota',
            'actor_role' => 'Anggota Koperasi',
            'timestamp' => $appliedLog?->created_at ?? $application?->submitted_at ?? $application?->created_at,
            'status' => 'completed',
            'badge' => 'Diajukan',
            'badge_color' => 'blue',
            'icon' => 'document',
            'details' => array_filter([
                'Plafon Diajukan' => 'Rp ' . number_format($application?->amount ?? 0, 0, ',', '.'),
                'Tenor' => ($application?->tenor_months ?? 0) . ' Bulan',
                'Tujuan Penggunaan' => $application?->purpose,
            ]),
            'notes' => null,
        ];

        // Jejak Penugasan / Pengalihan Teller jika ada
        foreach ($appLogs->whereIn('action', ['teller_claimed', 'teller_reassigned']) as $log) {
            $isReassign = $log->action === 'teller_reassigned';
            $items[] = [
                'stage' => 'assigned',
                'title' => $isReassign ? 'Pengalihan Penugasan Teller' : 'Pengambilan Antrean oleh Teller',
                'subtitle' => $isReassign ? 'Admin/Manajer mengalihkan penugasan teller' : 'Teller mengambil berkas dari antrean untuk diverifikasi',
                'actor_name' => $log->user?->name ?? 'Petugas Koperasi',
                'actor_role' => $log->user ? $log->user->employee_code_formatted : 'Petugas',
                'timestamp' => $log->created_at,
                'status' => 'completed',
                'badge' => $isReassign ? 'Dialihkan' : 'Antrean Diambil',
                'badge_color' => 'amber',
                'icon' => 'user-switch',
                'details' => array_filter([
                    'Petugas Ditugaskan' => $log->new_values['teller_name'] ?? ($log->new_values['new_teller'] ?? null),
                    'Dialihkan Oleh' => $log->new_values['assigned_by'] ?? null,
                ]),
                'notes' => $log->new_values['reason'] ?? null,
            ];
        }

        // Jejak Permintaan Revisi jika pernah terjadi
        foreach ($appLogs->where('action', 'revision_requested') as $revLog) {
            $items[] = [
                'stage' => 'needs_revision',
                'title' => 'Permintaan Perbaikan Berkas Dokumen',
                'subtitle' => 'Teller mengembalikan pengajuan untuk dilengkapi oleh anggota',
                'actor_name' => $revLog->user?->name ?? ($application?->receivedBy?->name ?? 'Teller'),
                'actor_role' => $revLog->user ? $revLog->user->employee_code_formatted : 'Teller',
                'timestamp' => $revLog->created_at,
                'status' => 'warning',
                'badge' => 'Perlu Revisi',
                'badge_color' => 'yellow',
                'icon' => 'exclamation',
                'details' => [],
                'notes' => $revLog->new_values['notes'] ?? $application?->revision_notes,
            ];
        }

        // Jejak Pengiriman Ulang oleh Anggota jika ada
        foreach ($appLogs->where('action', 'resubmitted_by_member') as $resubLog) {
            $items[] = [
                'stage' => 'resubmitted',
                'title' => 'Berkas Diperbaiki & Dikirim Ulang',
                'subtitle' => 'Anggota melengkapi kekurangan dokumen dan mengirimkan kembali berkas',
                'actor_name' => $application?->member?->user?->name ?? 'Anggota',
                'actor_role' => 'Anggota',
                'timestamp' => $resubLog->created_at,
                'status' => 'completed',
                'badge' => 'Dikirim Ulang',
                'badge_color' => 'blue',
                'icon' => 'refresh',
                'details' => [],
                'notes' => $resubLog->new_values['notes'] ?? null,
            ];
        }

        // 2. Diterima oleh Teller
        $receivedLog = $appLogs->firstWhere('action', 'teller_received');
        if ($receivedLog || $application?->received_at) {
            $tellerUser = $receivedLog?->user ?? $application?->receivedBy;
            $checklistData = $application?->reception_checklist ?? ($receivedLog?->new_values['checklist'] ?? []);
            $checklistSummary = is_array($checklistData) ? count(array_filter($checklistData)) . ' dokumen diverifikasi lengkap' : 'Lengkap';

            $items[] = [
                'stage' => 'received',
                'title' => 'Pengajuan Diterima & Diverifikasi Teller',
                'subtitle' => 'Teller memeriksa keabsahan fisik/digital seluruh checklist dokumen',
                'actor_name' => $tellerUser?->name ?? 'Teller Koperasi',
                'actor_role' => $tellerUser ? $tellerUser->employee_code_formatted : 'Teller',
                'timestamp' => $receivedLog?->created_at ?? $application?->received_at,
                'status' => 'completed',
                'badge' => 'Diterima Teller',
                'badge_color' => 'emerald',
                'icon' => 'check-badge',
                'details' => [
                    'Status Kelengkapan' => 'Lengkap & Memenuhi Syarat',
                    'Verifikasi Checklist' => $checklistSummary,
                ],
                'notes' => $application?->reception_note ?? ($receivedLog?->new_values['note'] ?? null),
            ];
        } elseif ($application?->status === LoanApplication::STATUS_SUBMITTED) {
            $items[] = [
                'stage' => 'waiting_reception',
                'title' => 'Menunggu Pemeriksaan Teller',
                'subtitle' => 'Pengajuan berada dalam antrean teller untuk verifikasi dokumen',
                'actor_name' => $application?->assignedTo ? $application->assignedTo->displayNameWithCode : 'Antrean Terbuka Teller',
                'actor_role' => 'Teller Kasir',
                'timestamp' => null,
                'status' => 'pending',
                'badge' => 'Dalam Antrean',
                'badge_color' => 'gray',
                'icon' => 'clock',
                'details' => [],
                'notes' => null,
            ];
        }

        // 3. Analisis Kredit (Analyst)
        $analystLog = $appLogs->firstWhere('action', 'analyst_reviewed');
        $analystApproval = $application?->approvals()->where('role', User::ROLE_ANALYST)->first();
        if ($analystLog || $analystApproval) {
            $isRecommend = ($analystApproval?->action === 'recommend') || (($analystLog?->new_values['action'] ?? '') === 'recommend');
            $analystUser = $analystLog?->user ?? $analystApproval?->user;
            $items[] = [
                'stage' => 'analyst_reviewed',
                'title' => $isRecommend ? 'Rekomendasi Analis Kredit' : 'Penolakan oleh Analis Kredit',
                'subtitle' => 'Analisis kelayakan 5C, histori simpanan, dan mitigasi risiko',
                'actor_name' => $analystUser?->name ?? 'Analis Kredit',
                'actor_role' => $analystUser ? $analystUser->employee_code_formatted : 'Analis Kredit',
                'timestamp' => $analystLog?->created_at ?? $analystApproval?->created_at,
                'status' => $isRecommend ? 'completed' : 'danger',
                'badge' => $isRecommend ? 'Direkomendasikan' : 'Ditolak Analis',
                'badge_color' => $isRecommend ? 'teal' : 'red',
                'icon' => $isRecommend ? 'clipboard-check' : 'x-circle',
                'details' => array_filter([
                    'Rekomendasi Plafon' => $analystApproval?->recommendations ?? ($analystLog?->new_values['recommendations'] ?? null),
                ]),
                'notes' => $analystApproval?->notes ?? ($analystLog?->new_values['notes'] ?? null),
            ];
        }

        // 4. Keputusan Manajer Koperasi
        $managerLog = $appLogs->firstWhere('action', 'manager_decided');
        $managerApproval = $application?->approvals()->where('role', User::ROLE_MANAGER)->first();
        if ($managerLog || $managerApproval) {
            $isApprove = ($managerApproval?->action === 'approve') || (($managerLog?->new_values['action'] ?? '') === 'approve');
            $managerUser = $managerLog?->user ?? $managerApproval?->user;
            $items[] = [
                'stage' => 'manager_decided',
                'title' => $isApprove ? 'Persetujuan Akhir Manajer' : 'Penolakan oleh Manajer',
                'subtitle' => $isApprove ? 'Pengajuan pinjaman disetujui, fasilitas kredit diterbitkan' : 'Pengajuan ditolak oleh pimpinan koperasi',
                'actor_name' => $managerUser?->name ?? 'Manajer Koperasi',
                'actor_role' => $managerUser ? $managerUser->employee_code_formatted : 'Manajer',
                'timestamp' => $managerLog?->created_at ?? $managerApproval?->created_at,
                'status' => $isApprove ? 'completed' : 'danger',
                'badge' => $isApprove ? 'Disetujui Manajer' : 'Ditolak Manajer',
                'badge_color' => $isApprove ? 'emerald' : 'red',
                'icon' => $isApprove ? 'shield-check' : 'x-circle',
                'details' => [],
                'notes' => $managerApproval?->notes ?? ($managerLog?->new_values['notes'] ?? null),
            ];
        }

        // 5. Penandatanganan Akad Pinjaman Digital
        $akadLog = $loanLogs->firstWhere('action', 'agreement_signed');
        if ($akadLog || $loan?->agreed_at) {
            $items[] = [
                'stage' => 'agreement_signed',
                'title' => 'Penandatanganan Akad Digital',
                'subtitle' => 'Anggota menyetujui ketentuan akad pinjaman dan transparansi biaya',
                'actor_name' => $loan?->member?->user?->name ?? 'Anggota',
                'actor_role' => 'Peminjam',
                'timestamp' => $akadLog?->created_at ?? $loan?->agreed_at,
                'status' => 'completed',
                'badge' => 'Akad Ditandatangani',
                'badge_color' => 'indigo',
                'icon' => 'pencil-square',
                'details' => array_filter([
                    'No. Kontrak Pinjaman' => $loan?->loan_no,
                ]),
                'notes' => null,
            ];
        }

        // 6. Pencairan Dana oleh Teller
        $disbursedLog = $loanLogs->firstWhere('action', 'disbursed');
        if ($disbursedLog || $loan?->disbursed_at) {
            $disbursedUser = $disbursedLog?->user ?? $loan?->disburser;
            $items[] = [
                'stage' => 'disbursed',
                'title' => 'Pencairan Dana Pinjaman',
                'subtitle' => 'Kas bersih diserahkan ke anggota, jadwal angsuran dan jurnal otomatis terbentuk',
                'actor_name' => $disbursedUser?->name ?? 'Teller Pencair Dana',
                'actor_role' => $disbursedUser ? $disbursedUser->employee_code_formatted : 'Teller Kasir',
                'timestamp' => $disbursedLog?->created_at ?? $loan?->disbursed_at,
                'status' => 'completed',
                'badge' => 'Dana Dicairkan',
                'badge_color' => 'purple',
                'icon' => 'banknotes',
                'details' => array_filter([
                    'Plafon Pokok' => 'Rp ' . number_format($loan?->principal_amount ?? 0, 0, ',', '.'),
                    'Biaya Administrasi' => 'Rp ' . number_format($loan?->total_admin_fee ?? 0, 0, ',', '.'),
                    'Dana Bersih Diterima' => 'Rp ' . number_format(($loan?->principal_amount ?? 0) - ($loan?->total_admin_fee ?? 0), 0, ',', '.'),
                    'Jatuh Tempo Akhir' => $loan?->maturity_date ? Carbon::parse($loan->maturity_date)->format('d/m/Y') : null,
                ]),
                'notes' => 'Pencairan dana telah dicatat dalam jurnal buku besar akuntansi berpasangan (double-entry).',
            ];
        }

        // Urutkan item berdasarkan timestamp kronologis (yang belum terjadi ditaruh di akhir)
        usort($items, function ($a, $b) {
            if ($a['timestamp'] === null && $b['timestamp'] === null) return 0;
            if ($a['timestamp'] === null) return 1;
            if ($b['timestamp'] === null) return -1;
            return Carbon::parse($a['timestamp'])->timestamp <=> Carbon::parse($b['timestamp'])->timestamp;
        });

        return $items;
    }

    /**
     * Mencatat tindakan penagihan pinjaman menunggak / jatuh tempo.
     */
    public function recordCollectionAction(Loan $loan, User $user, array $data): LoanCollectionLog
    {
        return DB::transaction(function () use ($loan, $user, $data) {
            $log = LoanCollectionLog::create([
                'loan_id' => $loan->id,
                'user_id' => $user->id,
                'action_type' => $data['action_type'],
                'action_date' => now(),
                'result_status' => $data['result_status'],
                'promised_date' => $data['promised_date'] ?? null,
                'promised_amount' => !empty($data['promised_amount']) ? (int) $data['promised_amount'] : null,
                'notes' => $data['notes'] ?? '-',
                'proof_photo_path' => $data['proof_photo_path'] ?? null,
            ]);

            $loan->last_collected_at = now();

            if (!empty($data['promised_date'])) {
                $loan->promised_payment_date = $data['promised_date'];
                $loan->promised_payment_amount = !empty($data['promised_amount']) ? (int) $data['promised_amount'] : null;
            }

            if ($data['action_type'] === LoanCollectionLog::ACTION_LETTER_SP1) {
                $loan->collection_stage = 'sp1';
            } elseif ($data['action_type'] === LoanCollectionLog::ACTION_LETTER_SP2) {
                $loan->collection_stage = 'sp2';
            } elseif ($data['action_type'] === LoanCollectionLog::ACTION_LETTER_SP3) {
                $loan->collection_stage = 'sp3';
            }

            $loan->save();

            AuditLog::log('collection_recorded', $loan, null, [
                'collector' => $user->name,
                'action_type' => $data['action_type'],
                'result_status' => $data['result_status'],
                'promised_date' => $data['promised_date'] ?? null,
            ]);

            // Kirim notifikasi jika surat peringatan atau janji bayar
            if ($loan->member && $loan->member->user) {
                $stageText = match ($data['action_type']) {
                    LoanCollectionLog::ACTION_LETTER_SP1 => 'Penerbitan Surat Peringatan 1 (SP-1)',
                    LoanCollectionLog::ACTION_LETTER_SP2 => 'Penerbitan Surat Peringatan 2 (SP-2)',
                    LoanCollectionLog::ACTION_LETTER_SP3 => 'Penerbitan Surat Peringatan 3 / Somasi (SP-3)',
                    LoanCollectionLog::ACTION_FIELD_VISIT => 'Pemberitahuan Kunjungan Penagihan Lapangan',
                    default => 'Catatan Penagihan Pinjaman',
                };

                $this->sendNotification(
                    $loan->member->user,
                    "Informasi Penagihan Pinjaman: {$stageText}",
                    "Petugas koperasi telah mencatat tindak lanjut penagihan untuk pinjaman #{$loan->loan_no}. Silakan segera lakukan penyelesaian pembayaran angsuran Anda.",
                    'warning',
                    route('loans.show', $loan->id)
                );
            }

            return $log;
        });
    }

    /**
     * Mengajukan permohonan keringanan / restrukturisasi denda pinjaman.
     */
    public function requestRestructuring(Loan $loan, User $user, string $type, int $requestedWaiver, string $reason): LoanRestructuringRequest
    {
        return DB::transaction(function () use ($loan, $user, $type, $requestedWaiver, $reason) {
            $req = LoanRestructuringRequest::create([
                'loan_id' => $loan->id,
                'requested_by_user_id' => $user->id,
                'request_type' => $type,
                'requested_waiver_amount' => $requestedWaiver,
                'reason' => $reason,
                'status' => LoanRestructuringRequest::STATUS_PENDING,
            ]);

            AuditLog::log('restructuring_requested', $loan, null, [
                'requested_by' => $user->name,
                'type' => $type,
                'requested_waiver' => $requestedWaiver,
                'reason' => $reason,
            ]);

            // Notifikasi ke Manajer
            $managers = User::where('role', User::ROLE_MANAGER)->where('is_active', true)->get();
            foreach ($managers as $mgr) {
                $this->sendNotification(
                    $mgr,
                    "Permohonan Keringanan / Restrukturisasi Pinjaman #{$loan->loan_no}",
                    "Terdapat permohonan {$req->type_label} dari {$user->name} untuk pinjaman #{$loan->loan_no}. Menunggu persetujuan Manajer.",
                    'info',
                    route('loans.overdue.manage')
                );
            }

            return $req;
        });
    }

    /**
     * Memutuskan permohonan keringanan denda (Hanya Manajer / Admin).
     */
    public function decideRestructuring(LoanRestructuringRequest $request, User $manager, string $decision, int $approvedWaiver = 0, ?string $notes = null): LoanRestructuringRequest
    {
        return DB::transaction(function () use ($request, $manager, $decision, $approvedWaiver, $notes) {
            $isApproved = ($decision === 'approved');
            $request->status = $isApproved ? LoanRestructuringRequest::STATUS_APPROVED : LoanRestructuringRequest::STATUS_REJECTED;
            $request->approved_by_user_id = $manager->id;
            $request->approved_waiver_amount = $isApproved ? $approvedWaiver : 0;
            $request->manager_notes = $notes;
            $request->decided_at = now();
            $request->save();

            $loan = $request->loan;

            if ($isApproved && $request->request_type === LoanRestructuringRequest::TYPE_PENALTY_WAIVER && $approvedWaiver > 0) {
                // Potong denda berjalan dari schedules
                $remainingWaiver = $approvedWaiver;
                foreach ($loan->schedules()->where('penalty_amount', '>', 0)->get() as $sch) {
                    if ($remainingWaiver <= 0) break;
                    $deduct = min($sch->penalty_amount, $remainingWaiver);
                    $sch->penalty_amount -= $deduct;
                    $sch->save();
                    $remainingWaiver -= $deduct;
                }
                $loan->outstanding_penalty = max(0, $loan->outstanding_penalty - $approvedWaiver);
                $loan->collection_stage = 'restructured';
                $loan->save();
            }

            AuditLog::log('restructuring_decided', $loan, null, [
                'manager' => $manager->name,
                'decision' => $decision,
                'approved_waiver' => $approvedWaiver,
                'notes' => $notes,
            ]);

            if ($loan->member && $loan->member->user) {
                $statusMsg = $isApproved ? "DISETUJUI (Keringanan: Rp " . number_format($approvedWaiver, 0, ',', '.') . ")" : "DITOLAK";
                $this->sendNotification(
                    $loan->member->user,
                    "Hasil Permohonan Keringanan Pinjaman: {$statusMsg}",
                    "Permohonan keringanan untuk pinjaman #{$loan->loan_no} telah diputuskan oleh Manajer: {$statusMsg}. Catatan: {$notes}",
                    $isApproved ? 'success' : 'warning',
                    route('loans.show', $loan->id)
                );
            }

            return $request;
        });
    }

    /**
     * Helper pengiriman notifikasi internal sistem.
     */
    protected function sendNotification(User $user, string $title, string $message, string $type = 'info', ?string $link = null): Notification
    {
        return Notification::create([
            'user_id' => $user->id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
        ]);
    }
}
