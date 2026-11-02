<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\Member;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\User;
use App\Services\LoanService;
use App\Services\SavingsService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TellerReceptionTest extends TestCase
{
    use RefreshDatabase;

    protected LoanService $loanService;
    protected User $memberUser;
    protected Member $member;
    protected User $teller1;
    protected User $teller2;
    protected User $analyst;
    protected User $manager;
    protected User $admin;
    protected LoanProduct $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loanService = app(LoanService::class);

        // Chart of Accounts
        ChartOfAccount::create(['code' => '1101', 'name' => 'Kas Koperasi', 'type' => 'asset', 'category' => 'asset', 'normal_balance' => 'debit']);
        ChartOfAccount::create(['code' => '1102', 'name' => 'Bank Mandiri', 'type' => 'asset', 'category' => 'asset', 'normal_balance' => 'debit']);
        ChartOfAccount::create(['code' => '1201', 'name' => 'Piutang Pinjaman Anggota', 'type' => 'asset', 'category' => 'asset', 'normal_balance' => 'debit']);
        ChartOfAccount::create(['code' => '2101', 'name' => 'Simpanan Pokok Anggota', 'type' => 'liability', 'category' => 'liability', 'normal_balance' => 'credit']);
        ChartOfAccount::create(['code' => '2102', 'name' => 'Simpanan Wajib Anggota', 'type' => 'liability', 'category' => 'liability', 'normal_balance' => 'credit']);
        ChartOfAccount::create(['code' => '2103', 'name' => 'Simpanan Sukarela Anggota', 'type' => 'liability', 'category' => 'liability', 'normal_balance' => 'credit']);
        ChartOfAccount::create(['code' => '3101', 'name' => 'Modal Koperasi', 'type' => 'equity', 'category' => 'equity', 'normal_balance' => 'credit']);
        ChartOfAccount::create(['code' => '4101', 'name' => 'Pendapatan Bunga Pinjaman', 'type' => 'revenue', 'category' => 'revenue', 'normal_balance' => 'credit']);
        ChartOfAccount::create(['code' => '4102', 'name' => 'Pendapatan Biaya Administrasi', 'type' => 'revenue', 'category' => 'revenue', 'normal_balance' => 'credit']);
        ChartOfAccount::create(['code' => '4103', 'name' => 'Pendapatan Denda Keterlambatan', 'type' => 'revenue', 'category' => 'revenue', 'normal_balance' => 'credit']);

        // Settings
        Setting::set('simpanan_pokok_amount', 100000);
        Setting::set('simpanan_wajib_amount', 25000);
        Setting::set('teller_assignment_mode', 'queue_or_assign');
        Setting::set('application_reception_deadline_hours', 24);
        Setting::set('teller_display_mode', 'name_and_code');

        // Users
        $this->teller1 = User::factory()->create([
            'name' => 'Dewi Teller',
            'role' => User::ROLE_TELLER,
            'employee_code' => 'TLR-001',
            'is_active' => true,
        ]);

        $this->teller2 = User::factory()->create([
            'name' => 'Bambang Teller',
            'role' => User::ROLE_TELLER,
            'employee_code' => 'TLR-002',
            'is_active' => true,
        ]);

        $this->analyst = User::factory()->create([
            'name' => 'Rian Analis',
            'role' => User::ROLE_ANALYST,
            'employee_code' => 'ANL-001',
            'is_active' => true,
        ]);

        $this->manager = User::factory()->create([
            'name' => 'Endang Manajer',
            'role' => User::ROLE_MANAGER,
            'employee_code' => 'MGR-001',
            'is_active' => true,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Suryanto Admin',
            'role' => User::ROLE_ADMIN,
            'employee_code' => 'ADM-001',
            'is_active' => true,
        ]);

        // Member
        $this->memberUser = User::factory()->create(['name' => 'Budi Santoso', 'role' => User::ROLE_MEMBER]);
        $this->member = Member::create([
            'user_id' => $this->memberUser->id,
            'member_no' => 'ANG-202610-001',
            'nik' => '3201010101010001',
            'birth_date' => '1990-01-01',
            'birth_place' => 'Jakarta',
            'gender' => 'L',
            'address' => 'Jl. Koperasi No. 10',
            'occupation' => 'Pedagang',
            'monthly_income' => 8000000,
            'status' => Member::STATUS_ACTIVE,
        ]);

        app(SavingsService::class)->createDefaultAccounts($this->member);

        // Product
        $this->product = LoanProduct::create([
            'code' => 'PRD-MDR',
            'name' => 'Pinjaman Modal Usaha',
            'description' => 'Produk pembiayaan modal usaha',
            'calculation_method' => 'effective',
            'interest_rate_annual' => 12.0,
            'interest_rate_monthly' => 1.0,
            'min_amount' => 1000000,
            'max_amount' => 50000000,
            'min_tenor_months' => 3,
            'max_tenor_months' => 24,
            'admin_fee_percentage' => 1.0,
            'admin_fee_fixed' => 50000,
            'penalty_rate_daily' => 0.001,
            'is_active' => true,
        ]);
    }

    public function test_application_starts_in_submitted_status_and_notifies_tellers()
    {
        $this->actingAs($this->memberUser);

        $application = $this->loanService->apply(
            $this->member,
            $this->product,
            10000000,
            6,
            'Modal belanja stok sembako'
        );

        $this->assertEquals(LoanApplication::STATUS_SUBMITTED, $application->status);
        $this->assertNull($application->received_by);
        $this->assertNull($application->received_at);

        // Tellers receive notification
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->teller1->id,
            'type' => 'info',
        ]);
    }

    public function test_teller_can_claim_application_from_queue()
    {
        $application = $this->loanService->apply($this->member, $this->product, 5000000, 6, 'Modal');

        $this->actingAs($this->teller1);
        $this->loanService->claimByTeller($application, $this->teller1);
        $application->refresh();

        $this->assertEquals($this->teller1->id, $application->assigned_to);
        $this->assertNotNull($application->assigned_at);
        $this->assertDatabaseHas('audit_logs', [
            'entity_type' => LoanApplication::class,
            'entity_id' => $application->id,
            'action' => 'teller_claimed',
        ]);
    }

    public function test_other_teller_cannot_claim_already_assigned_application()
    {
        $application = $this->loanService->apply($this->member, $this->product, 5000000, 6, 'Modal');

        // Teller 1 klaim
        $this->loanService->claimByTeller($application, $this->teller1);
        $application->refresh();

        // Teller 2 mencoba klaim -> Exception
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('telah ditugaskan kepada Dewi Teller');

        $this->actingAs($this->teller2);
        $this->loanService->claimByTeller($application, $this->teller2);
    }

    public function test_admin_can_reassign_teller_with_reason()
    {
        $application = $this->loanService->apply($this->member, $this->product, 5000000, 6, 'Modal');
        $this->loanService->claimByTeller($application, $this->teller1);

        // Admin alihkan penugasan ke Teller 2
        $this->actingAs($this->admin);
        $this->loanService->reassignTeller($application, $this->teller2, $this->admin, 'Teller 1 sedang dinas luar kantor');
        $application->refresh();

        $this->assertEquals($this->teller2->id, $application->assigned_to);
        $this->assertEquals($this->admin->id, $application->assigned_by);
        $this->assertEquals('Teller 1 sedang dinas luar kantor', $application->assignment_notes);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'teller_reassigned',
            'entity_id' => $application->id,
        ]);

        // Teller 2 menerima notifikasi pengalihan
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->teller2->id,
            'type' => 'info',
        ]);
    }

    public function test_teller_receives_application_with_complete_checklist_and_note()
    {
        $application = $this->loanService->apply($this->member, $this->product, 8000000, 6, 'Ekspansi warung');

        $this->actingAs($this->teller1);
        $checklist = [
            'ktp' => true,
            'application_form' => true,
            'income_proof' => true,
            'member_status' => true,
            'plafon_suitability' => true,
        ];
        $note = 'Semua dokumen asli pemohon telah diverifikasi dan valid.';

        $this->loanService->receiveByTeller($application, $this->teller1, $checklist, $note);
        $application->refresh();

        $this->assertEquals(LoanApplication::STATUS_RECEIVED, $application->status);
        $this->assertEquals($this->teller1->id, $application->received_by);
        $this->assertNotNull($application->received_at);
        $this->assertEquals('complete', $application->reception_status);
        $this->assertEquals($note, $application->reception_note);
        $this->assertEquals($checklist, $application->reception_checklist);
        $this->assertEquals('Dewi Teller (TLR-001)', $application->teller_display);

        // Anggota menerima notifikasi
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->memberUser->id,
            'type' => 'success',
        ]);

        // Analis menerima notifikasi berkas siap dianalisis
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->analyst->id,
            'type' => 'info',
        ]);
    }

    public function test_teller_cannot_receive_if_required_checklist_missing()
    {
        $application = $this->loanService->apply($this->member, $this->product, 8000000, 6, 'Ekspansi warung');

        $this->actingAs($this->teller1);
        $incompleteChecklist = [
            'ktp' => false, // missing required
            'application_form' => true,
            'income_proof' => true,
            'member_status' => true,
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('wajib diverifikasi dan dicentang');

        $this->loanService->receiveByTeller($application, $this->teller1, $incompleteChecklist, 'Belum lengkap');
    }

    public function test_unassigned_teller_cannot_receive_application_assigned_to_someone_else()
    {
        $application = $this->loanService->apply($this->member, $this->product, 8000000, 6, 'Ekspansi warung');
        $this->loanService->claimByTeller($application, $this->teller1);

        // Teller 2 mencoba menerima berkas milik Teller 1
        $this->actingAs($this->teller2);
        $checklist = [
            'ktp' => true,
            'application_form' => true,
            'income_proof' => true,
            'member_status' => true,
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('telah ditugaskan kepada Dewi Teller');

        $this->loanService->receiveByTeller($application, $this->teller2, $checklist, 'Mencoba ambil alih');
    }

    public function test_teller_can_request_revision_and_member_can_resubmit()
    {
        $application = $this->loanService->apply($this->member, $this->product, 8000000, 6, 'Ekspansi warung');

        // Teller mengembalikan berkas untuk perbaikan
        $this->actingAs($this->teller1);
        $this->loanService->requestRevisionByTeller($application, $this->teller1, 'Foto KTP buram dan slip gaji 3 bulan belum ada');
        $application->refresh();

        $this->assertEquals(LoanApplication::STATUS_NEEDS_REVISION, $application->status);
        $this->assertEquals('needs_revision', $application->reception_status);
        $this->assertEquals('Foto KTP buram dan slip gaji 3 bulan belum ada', $application->revision_notes);
        $this->assertNotNull($application->revision_requested_at);

        // Anggota menerima notifikasi perbaikan
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->memberUser->id,
            'type' => 'warning',
        ]);

        // Anggota memperbaiki dan submit ulang
        $this->actingAs($this->memberUser);
        $this->loanService->resubmitByMember($application, $this->member, 'Ekspansi warung nasi', 'KTP baru dan slip gaji telah diunggah');
        $application->refresh();

        $this->assertEquals(LoanApplication::STATUS_SUBMITTED, $application->status);
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'resubmitted_by_member',
            'entity_id' => $application->id,
        ]);
    }

    public function test_maker_checker_teller_cannot_approve_own_received_application_as_manager()
    {
        $application = $this->loanService->apply($this->member, $this->product, 5000000, 6, 'Modal');

        // Teller menerima pengajuan
        $this->actingAs($this->teller1);
        $this->loanService->receiveByTeller($application, $this->teller1, [
            'ktp' => true,
            'application_form' => true,
            'income_proof' => true,
            'member_status' => true,
        ], 'Lengkap');

        // Analis merekomendasikan
        $this->actingAs($this->analyst);
        $this->loanService->reviewByAnalyst($application, 'recommend', 'Rekomendasi setuju');

        // User Teller 1 diberi peran manager sementara atau mencoba mengeksekusi approveByManager
        $this->teller1->role = User::ROLE_MANAGER;
        $this->teller1->save();
        $this->actingAs($this->teller1);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Teller yang menerima pengajuan tidak boleh menyetujui pinjaman yang ia terima sendiri');

        $this->loanService->approveByManager($application, 'approve', 'Saya setujui sendiri');
    }

    public function test_maker_checker_analyst_cannot_approve_as_manager()
    {
        $application = $this->loanService->apply($this->member, $this->product, 5000000, 6, 'Modal');

        $this->actingAs($this->teller1);
        $this->loanService->receiveByTeller($application, $this->teller1, [
            'ktp' => true,
            'application_form' => true,
            'income_proof' => true,
            'member_status' => true,
        ], 'Lengkap');

        $this->actingAs($this->analyst);
        $this->loanService->reviewByAnalyst($application, 'recommend', 'Rekomendasi');

        // Analis mencoba menyetujui sebagai manajer
        $this->analyst->role = User::ROLE_MANAGER;
        $this->analyst->save();
        $this->actingAs($this->analyst);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Analis Kredit yang merekomendasikan pinjaman tidak boleh merangkap sebagai Manajer');

        $this->loanService->approveByManager($application, 'approve', 'Saya setujui');
    }

    public function test_timeline_generation_has_all_milestones()
    {
        // Siklus utuh
        $this->actingAs($this->memberUser);
        $application = $this->loanService->apply($this->member, $this->product, 10000000, 6, 'Pengembangan toko');

        $this->actingAs($this->teller1);
        $this->loanService->claimByTeller($application, $this->teller1);
        $this->loanService->receiveByTeller($application, $this->teller1, [
            'ktp' => true,
            'application_form' => true,
            'income_proof' => true,
            'member_status' => true,
        ], 'Berkas lengkap');

        $this->actingAs($this->analyst);
        $this->loanService->reviewByAnalyst($application, 'recommend', 'Kelayakan 5C bagus', 'Plafon Rp 10.000.000');

        $this->actingAs($this->manager);
        $this->loanService->approveByManager($application, 'approve', 'Disetujui penuh');

        $loan = $application->loan;
        $this->actingAs($this->memberUser);
        $this->loanService->signAgreement($loan);

        $this->actingAs($this->teller2);
        $this->loanService->disburse($loan, 'cash');
        $loan->refresh();

        // Bangun Timeline
        $timeline = $this->loanService->getTimeline($loan);

        $stages = array_column($timeline, 'stage');
        $this->assertContains('applied', $stages);
        $this->assertContains('assigned', $stages);
        $this->assertContains('received', $stages);
        $this->assertContains('analyst_reviewed', $stages);
        $this->assertContains('manager_decided', $stages);
        $this->assertContains('agreement_signed', $stages);
        $this->assertContains('disbursed', $stages);

        // Pastikan teller display dan kode tercantum
        $receivedMilestone = collect($timeline)->firstWhere('stage', 'received');
        $this->assertNotNull($receivedMilestone);
        $this->assertEquals('Dewi Teller', $receivedMilestone['actor_name']);
        $this->assertEquals('TLR-001', $receivedMilestone['actor_role']);
    }

    public function test_reception_deadline_reminder_command()
    {
        // Buat pengajuan yang diajukan 30 jam yang lalu (melebihi batas 24 jam)
        $application = $this->loanService->apply($this->member, $this->product, 15000000, 12, 'Modal darurat');
        $application->update([
            'status' => LoanApplication::STATUS_SUBMITTED,
            'submitted_at' => now()->subHours(30),
        ]);

        $this->artisan('koperasi:check-reception-deadline')
            ->assertExitCode(0);

        // Manajer dan Admin harus menerima notifikasi peringatan deadline
        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->manager->id,
            'title' => 'Peringatan: Batas Waktu Penerimaan Teller Terlewati',
            'type' => 'warning',
        ]);

        $this->assertDatabaseHas('notifications', [
            'user_id' => $this->admin->id,
            'title' => 'Peringatan: Batas Waktu Penerimaan Teller Terlewati',
            'type' => 'warning',
        ]);
    }
}

