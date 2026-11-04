<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanCollectionLog;
use App\Models\LoanProduct;
use App\Models\LoanRestructuringRequest;
use App\Models\LoanSchedule;
use App\Models\Member;
use App\Models\Setting;
use App\Models\User;
use App\Services\LoanService;
use App\Services\SavingsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OverdueManagementAndBusinessPhotoTest extends TestCase
{
    use RefreshDatabase;

    protected LoanService $loanService;
    protected User $memberUser;
    protected Member $member;
    protected User $tellerUser;
    protected User $analystUser;
    protected User $managerUser;
    protected User $adminUser;
    protected LoanProduct $businessProduct;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->loanService = app(LoanService::class);

        // Siapkan Akun CoA
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

        Setting::set('penalty_rate_daily', 0.1);

        // Pengguna dengan peran-peran spesifik
        $this->tellerUser = User::factory()->create(['role' => 'teller', 'name' => 'Teller Siti']);
        $this->analystUser = User::factory()->create(['role' => 'analyst', 'name' => 'Analis Budi']);
        $this->managerUser = User::factory()->create(['role' => 'manager', 'name' => 'Manajer Agus']);
        $this->adminUser = User::factory()->create(['role' => 'admin', 'name' => 'Admin Utama']);

        $this->memberUser = User::factory()->create(['role' => 'member', 'name' => 'Pak Joko Warung']);
        $this->member = Member::create([
            'user_id' => $this->memberUser->id,
            'member_no' => 'ANG-MODAL-001',
            'nik' => '3201010101010099',
            'birth_date' => '1985-04-12',
            'birth_place' => 'Surabaya',
            'gender' => 'L',
            'address' => 'Jl. Pasar Tradisional No. 12',
            'occupation' => 'Pedagang Kelontong',
            'monthly_income' => 15000000,
            'status' => Member::STATUS_ACTIVE,
        ]);

        app(SavingsService::class)->createDefaultAccounts($this->member);

        // Produk Pembiayaan Modal Usaha (Mudharabah / Usaha Produktif)
        $this->businessProduct = LoanProduct::create([
            'code' => 'PRD-MDR',
            'name' => 'Pembiayaan Modal Usaha Mudharabah',
            'description' => 'Khusus modal perputaran usaha UMKM anggota',
            'calculation_method' => 'flat',
            'tenor_unit' => 'month',
            'interest_rate_annual' => 12.0,
            'interest_rate_monthly' => 1.0,
            'interest_rate_per_period' => 1.0,
            'min_amount' => 1000000,
            'max_amount' => 50000000,
            'min_tenor' => 3,
            'max_tenor' => 36,
            'min_tenor_months' => 3,
            'max_tenor_months' => 36,
            'admin_fee_percentage' => 1.0,
            'admin_fee_fixed' => 0,
            'penalty_rate_daily' => 0.1,
            'is_active' => true,
        ]);
    }

    /**
     * Helper untuk membuat pinjaman aktif cepat untuk pengujian.
     */
    protected function createActiveLoan(int $amount = 3000000, int $tenor = 3): Loan
    {
        $app = LoanApplication::create([
            'application_no' => 'APP-TEST-' . uniqid(),
            'member_id' => $this->member->id,
            'loan_product_id' => $this->businessProduct->id,
            'amount' => $amount,
            'tenor_months' => $tenor,
            'tenor_units' => $tenor,
            'tenor_count' => $tenor,
            'tenor_unit' => 'month',
            'interest_rate_monthly' => 1.0,
            'interest_rate_per_period' => 1.0,
            'calculation_method' => 'flat',
            'purpose' => 'Modal Usaha Toko Kelontong',
            'business_name' => 'Toko Kelontong Berkah',
            'business_type' => 'Retail / Sembako',
            'status' => LoanApplication::STATUS_RECEIVED,
            'assigned_to' => $this->tellerUser->id,
            'received_by' => $this->tellerUser->id,
            'received_at' => now(),
        ]);

        // Rekomendasi analis
        $this->actingAs($this->analystUser);
        $this->loanService->reviewByAnalyst($app, 'recommend', 'Rekomendasi disetujui, usaha berjalan lancar.');

        // Persetujuan manajer
        $this->actingAs($this->managerUser);
        $this->loanService->approveByManager($app, 'approve', 'Disetujui manajer.');

        $loan = Loan::where('loan_application_id', $app->id)->firstOrFail();

        // Tanda tangan akad anggota
        $this->actingAs($this->memberUser);
        $this->loanService->signAgreement($loan);

        // Pencairan teller
        $this->actingAs($this->tellerUser);
        $this->loanService->disburse($loan, 'transfer');

        return $loan->fresh();
    }

    /**
     * Test pengajuan pinjaman modal usaha dengan upload foto tempat usaha & data profil usaha.
     */
    public function test_apply_loan_with_business_photo_upload_and_details()
    {
        $file = UploadedFile::fake()->image('warung_kelontong.jpg', 800, 600);

        $response = $this->actingAs($this->memberUser)->post(route('loans.apply'), [
            'loan_product_id' => $this->businessProduct->id,
            'amount' => 5000000,
            'tenor_months' => 6,
            'tenor_unit' => 'month',
            'purpose' => 'Tambahan persediaan sembako dan modal usaha toko',
            'agree_terms' => '1',
            'business_name' => 'Toko Kelontong Berkah Barokah',
            'business_type' => 'Retail / Sembako',
            'business_address' => 'Pasar Wonokromo Kios B-14',
            'business_duration_years' => 4,
            'business_monthly_revenue' => 25000000,
            'business_photo' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('loan_applications', [
            'member_id' => $this->member->id,
            'business_name' => 'Toko Kelontong Berkah Barokah',
            'business_type' => 'Retail / Sembako',
            'business_duration_years' => 4,
            'business_monthly_revenue' => 25000000,
        ]);

        $application = LoanApplication::where('member_id', $this->member->id)->latest()->first();
        $this->assertNotNull($application->business_photo_path);
        Storage::disk('public')->assertExists($application->business_photo_path);
    }

    /**
     * Test pengajuan pinjaman modal usaha dengan pengambilan foto langsung kamera (Base64 WebCam capture).
     */
    public function test_apply_loan_with_business_photo_base64_camera_capture()
    {
        $base64Image = 'data:image/png;base64,' . base64_encode('fake_camera_binary_content');

        $response = $this->actingAs($this->memberUser)->post(route('loans.apply'), [
            'loan_product_id' => $this->businessProduct->id,
            'amount' => 3000000,
            'tenor_months' => 3,
            'tenor_unit' => 'month',
            'purpose' => 'Pengembangan modal usaha sayur mayur',
            'agree_terms' => '1',
            'business_name' => 'Kios Sayur Segar',
            'business_type' => 'Pertanian & Sayur',
            'business_address' => 'Jl. Pasar No. 5',
            'business_duration_years' => 2,
            'business_monthly_revenue' => 12000000,
            'business_photo_camera' => $base64Image,
        ]);

        $response->assertRedirect();
        $application = LoanApplication::where('business_name', 'Kios Sayur Segar')->first();
        $this->assertNotNull($application);
        $this->assertNotNull($application->business_photo_path);
        Storage::disk('public')->assertExists($application->business_photo_path);
    }

    /**
     * Test alur verifikasi berkas oleh Analis Kredit termasuk foto verifikasi survei lapangan.
     */
    public function test_credit_analyst_review_includes_field_survey_photo_and_notes()
    {
        $app = LoanApplication::create([
            'application_no' => 'APP-TEST-SURVEY',
            'member_id' => $this->member->id,
            'loan_product_id' => $this->businessProduct->id,
            'amount' => 10000000,
            'tenor_months' => 6,
            'tenor_units' => 6,
            'tenor_count' => 6,
            'tenor_unit' => 'month',
            'interest_rate_monthly' => 1.0,
            'interest_rate_per_period' => 1.0,
            'calculation_method' => 'flat',
            'purpose' => 'Modal usaha konveksi',
            'business_name' => 'Konveksi Berkah',
            'business_type' => 'Tekstil & Pakaian',
            'status' => LoanApplication::STATUS_RECEIVED,
            'assigned_to' => $this->tellerUser->id,
            'received_by' => $this->tellerUser->id,
            'received_at' => now(),
        ]);

        $surveyPhoto = UploadedFile::fake()->image('survei_lapangan.jpg', 600, 400);

        $response = $this->actingAs($this->analystUser)->post(route('loans.applications.review', $app->id), [
            'action' => 'recommend',
            'notes' => 'Survei tempat usaha valid, mesin jahit aktif 4 unit, omset konsisten.',
            'survey_notes' => 'Tempat usaha milik sendiri, stok bahan kain senilai Rp 15jt tersedia.',
            'survey_photo' => $surveyPhoto,
        ]);

        $response->assertRedirect();
        $app->refresh();
        $this->assertEquals(LoanApplication::STATUS_IN_REVIEW, $app->status);
        $this->assertNotNull($app->survey_photo_path);
        $this->assertStringContainsString('Tempat usaha milik sendiri', $app->survey_notes);
        Storage::disk('public')->assertExists($app->survey_photo_path);
    }

    /**
     * Test bahwa data usaha dan foto tersalin secara utuh saat pinjaman disetujui Manajer.
     */
    public function test_business_data_and_photos_are_copied_to_loan_on_approval()
    {
        $app = LoanApplication::create([
            'application_no' => 'APP-TEST-COPY',
            'member_id' => $this->member->id,
            'loan_product_id' => $this->businessProduct->id,
            'amount' => 6000000,
            'tenor_months' => 6,
            'tenor_units' => 6,
            'tenor_count' => 6,
            'tenor_unit' => 'month',
            'interest_rate_monthly' => 1.0,
            'interest_rate_per_period' => 1.0,
            'calculation_method' => 'flat',
            'purpose' => 'Modal Usaha Warung Kopi',
            'business_name' => 'Warkop Nusantara',
            'business_type' => 'F&B',
            'business_address' => 'Jl. Diponegoro No. 8',
            'business_duration_years' => 3,
            'business_monthly_revenue' => 18000000,
            'business_photo_path' => 'business_photos/test_warkop.jpg',
            'survey_photo_path' => 'survey_photos/test_survei.jpg',
            'survey_notes' => 'Survei lokasi ramai pengunjung',
            'status' => LoanApplication::STATUS_IN_REVIEW,
        ]);

        // Manajer Menyetujui
        $this->actingAs($this->managerUser);
        $this->loanService->approveByManager($app, 'approve', 'Disetujui untuk pengembangan cabang');

        $loan = Loan::where('loan_application_id', $app->id)->firstOrFail();
        $this->assertEquals('Warkop Nusantara', $loan->business_name);
        $this->assertEquals('F&B', $loan->business_type);
        $this->assertEquals('business_photos/test_warkop.jpg', $loan->business_photo_path);
        $this->assertEquals('survey_photos/test_survei.jpg', $loan->survey_photo_path);
        $this->assertEquals('lancar', $loan->collectibility_status);
        $this->assertEquals('normal', $loan->collection_stage);
        $this->assertTrue($loan->isBusinessLoan());
    }

    /**
     * Test klasifikasi kolektibilitas OJK (Kol 1 - Kol 5) berdasarkan hari keterlambatan.
     */
    public function test_collectibility_status_and_penalty_calculation()
    {
        $loan = $this->createActiveLoan(3000000, 3);
        $schedules = $loan->schedules()->orderBy('installment_no')->get();
        $firstSched = $schedules[0];

        // Skenario 1: Terlambat 15 hari -> DPK (Kol 2)
        $firstSched->update([
            'due_date' => Carbon::now()->subDays(15)->format('Y-m-d'),
            'status' => LoanSchedule::STATUS_UNPAID,
        ]);

        $this->loanService->updateOverdueAndPenalties();
        $loan->refresh();
        $this->assertEquals('dpk', $loan->collectibility_status);
        $this->assertEquals('Kol-2: Dalam Perhatian Khusus (DPK)', $loan->collectibility_label);
        $this->assertEquals('reminder', $loan->collection_stage);

        // Skenario 2: Terlambat 45 hari -> Kurang Lancar (Kol 3)
        $firstSched->update([
            'due_date' => Carbon::now()->subDays(45)->format('Y-m-d'),
        ]);
        $this->loanService->updateOverdueAndPenalties();
        $loan->refresh();
        $this->assertEquals('kurang_lancar', $loan->collectibility_status);
        $this->assertEquals('Kol-3: Kurang Lancar (1-60 Hari)', $loan->collectibility_label);
        $this->assertEquals('sp1', $loan->collection_stage);

        // Skenario 3: Terlambat 75 hari -> Diragukan (Kol 4)
        $firstSched->update([
            'due_date' => Carbon::now()->subDays(75)->format('Y-m-d'),
        ]);
        $this->loanService->updateOverdueAndPenalties();
        $loan->refresh();
        $this->assertEquals('diragukan', $loan->collectibility_status);
        $this->assertEquals('Kol-4: Diragukan (61-90 Hari)', $loan->collectibility_label);
        $this->assertEquals('sp2', $loan->collection_stage);

        // Skenario 4: Terlambat 100 hari -> Macet / NPL (Kol 5)
        $firstSched->update([
            'due_date' => Carbon::now()->subDays(100)->format('Y-m-d'),
        ]);
        $this->loanService->updateOverdueAndPenalties();
        $loan->refresh();
        $this->assertEquals('macet', $loan->collectibility_status);
        $this->assertEquals('Kol-5: Macet / NPL (>90 Hari)', $loan->collectibility_label);
        $this->assertEquals('sp3', $loan->collection_stage);
    }

    /**
     * Test pencatatan aktivitas penagihan dan pembinaan (LoanCollectionLog) oleh petugas.
     */
    public function test_record_collection_action_and_promise_to_pay()
    {
        $loan = $this->createActiveLoan(2000000, 2);
        $proofFile = UploadedFile::fake()->image('bukti_kunjungan.jpg', 600, 400);

        $response = $this->actingAs($this->analystUser)->post(route('loans.collection.record', $loan->id), [
            'action_type' => 'field_visit',
            'result_status' => 'promise_to_pay',
            'promised_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'promised_amount' => 500000,
            'notes' => 'Kunjungan ke toko kelontong. Debitur berjanji melunasi angsuran pertama dalam 5 hari.',
            'proof_photo' => $proofFile,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('loan_collection_logs', [
            'loan_id' => $loan->id,
            'user_id' => $this->analystUser->id,
            'action_type' => 'field_visit',
            'result_status' => 'promise_to_pay',
            'promised_amount' => 500000,
        ]);

        $loan->refresh();
        $this->assertEquals(Carbon::now()->addDays(5)->format('Y-m-d'), Carbon::parse($loan->promised_payment_date)->format('Y-m-d'));
        $this->assertEquals(500000, $loan->promised_payment_amount);
    }

    /**
     * Test permohonan keringanan denda (Restructuring) dan persetujuan oleh Manajer (Maker-Checker).
     */
    public function test_restructuring_waiver_lifecycle_member_request_and_manager_approval()
    {
        $loan = $this->createActiveLoan(4000000, 4);

        // Berikan denda pada jadwal
        $sched = $loan->schedules()->first();
        $sched->update([
            'due_date' => Carbon::now()->subDays(20)->format('Y-m-d'),
            'penalty_amount' => 150000,
            'status' => LoanSchedule::STATUS_OVERDUE,
        ]);
        $loan->update([
            'status' => Loan::STATUS_OVERDUE,
            'outstanding_penalty' => 150000,
        ]);

        // 1. Anggota mengajukan permohonan pembebasan denda Rp 100.000
        $response = $this->actingAs($this->memberUser)->post(route('loans.restructuring.request', $loan->id), [
            'request_type' => 'penalty_waiver',
            'requested_waiver_amount' => 100000,
            'reason' => 'Toko terkena banjir seminggu lalu sehingga operasional terhenti sementara.',
        ]);

        $response->assertRedirect();
        $restructure = LoanRestructuringRequest::where('loan_id', $loan->id)->first();
        $this->assertNotNull($restructure);
        $this->assertEquals('pending', $restructure->status);
        $this->assertEquals(100000, $restructure->requested_waiver_amount);

        // 2. Teller / Analis mencoba memutus -> Wajib 403 (Segregation of Duties)
        $forbiddenResponse = $this->actingAs($this->tellerUser)->post(route('loans.restructuring.decide', $restructure->id), [
            'decision' => 'approved',
            'approved_waiver_amount' => 100000,
        ]);
        $forbiddenResponse->assertForbidden();

        // 3. Manajer menyetujui pembebasan denda sebesar Rp 100.000
        $approveResponse = $this->actingAs($this->managerUser)->post(route('loans.restructuring.decide', $restructure->id), [
            'decision' => 'approved',
            'approved_waiver_amount' => 100000,
            'manager_notes' => 'Disetujui pembebasan denda Rp 100.000 karena faktor bencana banjir.',
        ]);

        $approveResponse->assertRedirect();
        $restructure->refresh();
        $loan->refresh();

        $this->assertEquals('approved', $restructure->status);
        $this->assertEquals(100000, $restructure->approved_waiver_amount);
        $this->assertEquals($this->managerUser->id, $restructure->approved_by_user_id);

        // Denda pinjaman harus berkurang dari 150.000 menjadi 50.000
        $this->assertEquals(50000, $loan->outstanding_penalty);
    }

    /**
     * Test aksesibilitas dan hak akses (RBAC) pada halaman kelola tunggakan.
     */
    public function test_overdue_management_rbac_and_views()
    {
        // Anggota umum tidak boleh mengakses dashboard kelola tunggakan
        $response = $this->actingAs($this->memberUser)->get(route('loans.overdue.manage'));
        $response->assertForbidden();

        // Staf (Analis, Teller, Manajer, Admin) dapat mengakses
        $analystResponse = $this->actingAs($this->analystUser)->get(route('loans.overdue.manage'));
        $analystResponse->assertOk();
        $analystResponse->assertSee('Kelola Pinjaman Menunggak &amp; Jatuh Tempo', false);

        // Halaman detail pinjaman dapat dilihat oleh peminjam
        $loan = $this->createActiveLoan(2000000, 2);
        $loan->update(['business_name' => 'Kios Kelontong Bintang']);

        $memberLoanView = $this->actingAs($this->memberUser)->get(route('loans.show', $loan->id));
        $memberLoanView->assertOk();
        $memberLoanView->assertSee('Data & Foto Tempat Usaha Anggota', false);
        $memberLoanView->assertSee('Kios Kelontong Bintang');
    }
}
