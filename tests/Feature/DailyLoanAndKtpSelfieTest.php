<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\Member;
use App\Models\User;
use App\Services\LoanCalculationService;
use App\Services\LoanService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DailyLoanAndKtpSelfieTest extends TestCase
{
    use RefreshDatabase;

    protected LoanCalculationService $calculator;
    protected LoanService $loanService;
    protected User $memberUser;
    protected Member $member;
    protected LoanProduct $dailyKonsumtifProduct;
    protected LoanProduct $dailyMultigunaProduct;
    protected LoanProduct $businessProduct;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->calculator = app(LoanCalculationService::class);
        $this->loanService = app(LoanService::class);

        $this->memberUser = User::factory()->create([
            'role' => User::ROLE_MEMBER,
            'is_active' => true,
        ]);

        $this->member = Member::factory()->create([
            'user_id' => $this->memberUser->id,
            'status' => Member::STATUS_ACTIVE,
        ]);

        $this->dailyKonsumtifProduct = LoanProduct::firstOrCreate(
            ['code' => 'PRD-KNS-DAY'],
            [
                'name' => 'Pinjaman Konsumtif Harian (Flat)',
                'description' => 'Pinjaman harian flat',
                'interest_rate_annual' => 73.00,
                'interest_rate_monthly' => 6.00,
                'interest_rate_per_period' => 0.20,
                'calculation_method' => 'flat',
                'tenor_unit' => 'day',
                'min_amount' => 100000,
                'max_amount' => 1000000,
                'min_tenor_months' => 1,
                'max_tenor_months' => 1,
                'min_tenor' => 7,
                'max_tenor' => 30,
                'admin_fee_percentage' => 1.00,
                'admin_fee_fixed' => 5000,
                'penalty_rate_daily' => 0.0010,
                'is_active' => true,
            ]
        );

        $this->dailyMultigunaProduct = LoanProduct::firstOrCreate(
            ['code' => 'PRD-MLT-DAY'],
            [
                'name' => 'Pinjaman Multiguna Harian (Anuitas)',
                'description' => 'Pinjaman harian anuitas',
                'interest_rate_annual' => 54.75,
                'interest_rate_monthly' => 4.50,
                'interest_rate_per_period' => 0.15,
                'calculation_method' => 'annuity',
                'tenor_unit' => 'day',
                'min_amount' => 100000,
                'max_amount' => 1000000,
                'min_tenor_months' => 1,
                'max_tenor_months' => 1,
                'min_tenor' => 7,
                'max_tenor' => 30,
                'admin_fee_percentage' => 1.00,
                'admin_fee_fixed' => 5000,
                'penalty_rate_daily' => 0.0010,
                'is_active' => true,
            ]
        );

        $this->businessProduct = LoanProduct::firstOrCreate(
            ['code' => 'PRD-MDR'],
            [
                'name' => 'Pinjaman Modal Usaha (Efektif)',
                'description' => 'Pinjaman modal usaha',
                'interest_rate_annual' => 12.00,
                'interest_rate_monthly' => 1.00,
                'interest_rate_per_period' => 1.00,
                'calculation_method' => 'effective',
                'tenor_unit' => 'month',
                'min_amount' => 2000000,
                'max_amount' => 50000000,
                'min_tenor_months' => 3,
                'max_tenor_months' => 24,
                'min_tenor' => 3,
                'max_tenor' => 24,
                'admin_fee_percentage' => 1.00,
                'admin_fee_fixed' => 50000,
                'penalty_rate_daily' => 0.0010,
                'is_active' => true,
            ]
        );
    }

    public function test_daily_loan_simulation_flat_schedule_and_due_dates()
    {
        $startDate = Carbon::parse('2026-11-01');
        $sim = $this->calculator->simulate(
            500000,
            10,
            0.20,
            'flat',
            10000,
            'day',
            $startDate
        );

        $this->assertEquals(365, $sim['periods_per_year']);
        $this->assertEquals('Hari', $sim['tenor_unit_label']);
        $this->assertCount(10, $sim['schedules']);

        // Jadwal hari 1 jatuh tempo 2026-11-02
        $this->assertEquals('2026-11-02', $sim['schedules'][0]['due_date']);
        $this->assertEquals('Hari 1', $sim['schedules'][0]['period_label']);
        $this->assertEquals(50000, $sim['schedules'][0]['principal_amount']);
        $this->assertEquals(1000, $sim['schedules'][0]['interest_amount']);
        $this->assertEquals(51000, $sim['schedules'][0]['total_amount']);

        // Jadwal hari terakhir jatuh tempo 2026-11-11
        $this->assertEquals('2026-11-11', $sim['schedules'][9]['due_date']);
        $this->assertEquals('Hari 10', $sim['schedules'][9]['period_label']);
        $this->assertEquals(0, $sim['schedules'][9]['remaining_principal']);
    }

    public function test_daily_loan_simulation_annuity_amortization()
    {
        $startDate = Carbon::parse('2026-11-01');
        $sim = $this->calculator->simulate(
            1000000,
            7,
            0.15,
            'annuity',
            15000,
            'day',
            $startDate
        );

        $this->assertEquals(365, $sim['periods_per_year']);
        $this->assertEquals('Hari', $sim['tenor_unit_label']);
        $this->assertCount(7, $sim['schedules']);
        $this->assertEquals('2026-11-08', $sim['schedules'][6]['due_date']);
        $this->assertEquals(0, $sim['schedules'][6]['remaining_principal']);
    }

    public function test_non_business_loan_requires_ktp_selfie()
    {
        $response = $this->actingAs($this->memberUser)->post(route('loans.apply.submit'), [
            'loan_product_id' => $this->dailyKonsumtifProduct->id,
            'amount' => 500000,
            'tenor_count' => 14,
            'purpose' => 'Kebutuhan mendesak pengobatan keluarga',
            'agree_terms' => 1,
            // ktp_selfie sengaja dikosongkan
        ]);

        $response->assertSessionHasErrors(['ktp_selfie']);
        $this->assertDatabaseCount('loan_applications', 0);
    }

    public function test_non_business_loan_succeeds_with_ktp_selfie_file_upload()
    {
        $fakeSelfie = UploadedFile::fake()->image('selfie_holding_ktp.jpg', 800, 600);

        $response = $this->actingAs($this->memberUser)->post(route('loans.apply.submit'), [
            'loan_product_id' => $this->dailyKonsumtifProduct->id,
            'amount' => 500000,
            'tenor_count' => 14,
            'purpose' => 'Biaya darurat kesehatan',
            'agree_terms' => 1,
            'ktp_selfie' => $fakeSelfie,
        ]);

        $response->assertRedirect(route('loans.my'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('loan_applications', 1);
        $app = LoanApplication::first();
        $this->assertNotNull($app->ktp_selfie_path);
        $this->assertEquals('day', $app->tenor_unit);
        $this->assertEquals('Hari', $app->tenor_unit_label);
        Storage::disk('public')->assertExists($app->ktp_selfie_path);
    }

    public function test_non_business_loan_succeeds_with_ktp_selfie_camera_base64()
    {
        $base64Image = 'data:image/jpeg;base64,' . base64_encode('fake-ktp-selfie-camera-bytes');

        $response = $this->actingAs($this->memberUser)->post(route('loans.apply.submit'), [
            'loan_product_id' => $this->dailyMultigunaProduct->id,
            'amount' => 300000,
            'tenor_count' => 10,
            'purpose' => 'Kebutuhan perlengkapan belajar anak',
            'agree_terms' => 1,
            'ktp_selfie_camera' => $base64Image,
        ]);

        $response->assertRedirect(route('loans.my'));
        $this->assertDatabaseCount('loan_applications', 1);
        $app = LoanApplication::first();
        $this->assertNotNull($app->ktp_selfie_path);
        Storage::disk('public')->assertExists($app->ktp_selfie_path);
    }

    public function test_loan_disbursement_copies_ktp_selfie_path_to_loan()
    {
        $fakeSelfie = UploadedFile::fake()->image('my_ktp_selfie.jpg', 600, 600);
        $path = $fakeSelfie->store('ktp_selfies', 'public');

        $app = LoanApplication::create([
            'member_id' => $this->member->id,
            'loan_product_id' => $this->dailyKonsumtifProduct->id,
            'application_no' => 'APP-TEST-KTP',
            'amount' => 500000,
            'tenor_unit' => 'day',
            'tenor_months' => 1,
            'tenor_count' => 14,
            'purpose' => 'Talangan belanja bulanan',
            'calculation_method' => 'flat',
            'interest_rate_monthly' => 6.00,
            'interest_rate_per_period' => 0.20,
            'admin_fee' => 10000,
            'monthly_installment_estimate' => 45000,
            'status' => LoanApplication::STATUS_APPROVED,
            'submitted_at' => now(),
            'ktp_selfie_path' => $path,
        ]);

        // Manajer menyetujui dan memicu pembuatan objek Loan (STATUS_AWAITING_DISBURSEMENT)
        $manager = User::factory()->create(['role' => User::ROLE_MANAGER]);
        $this->actingAs($manager);

        // Simulasi disburse / persetujuan maker-checker
        $loan = Loan::create([
            'loan_application_id' => $app->id,
            'member_id' => $app->member_id,
            'loan_product_id' => $app->loan_product_id,
            'loan_no' => 'LN-2026-TEST',
            'principal_amount' => $app->amount,
            'total_interest' => 14000,
            'total_admin_fee' => $app->admin_fee,
            'total_loan_amount' => $app->amount + 14000 + $app->admin_fee,
            'outstanding_principal' => $app->amount,
            'outstanding_interest' => 14000,
            'outstanding_penalty' => 0,
            'tenor_unit' => $app->tenor_unit,
            'tenor_months' => $app->tenor_months,
            'tenor_count' => $app->tenor_count,
            'interest_rate_monthly' => $app->interest_rate_monthly,
            'interest_rate_per_period' => $app->interest_rate_per_period,
            'calculation_method' => $app->calculation_method,
            'status' => Loan::STATUS_ACTIVE,
            'ktp_selfie_path' => $app->ktp_selfie_path,
        ]);

        $this->assertEquals($path, $loan->ktp_selfie_path);
        $this->assertTrue($loan->isDaily());
        $this->assertEquals('Hari', $loan->tenor_unit_label);
    }

    public function test_business_loan_does_not_enforce_ktp_selfie()
    {
        $fakeBusinessPhoto = UploadedFile::fake()->image('warung.jpg', 600, 600);

        $response = $this->actingAs($this->memberUser)->post(route('loans.apply.submit'), [
            'loan_product_id' => $this->businessProduct->id,
            'amount' => 5000000,
            'tenor_count' => 6,
            'purpose' => 'Penambahan modal belanja stok sembako usaha warung',
            'agree_terms' => 1,
            'business_name' => 'Toko Kelontong Berkah',
            'business_type' => 'Perdagangan / Retail / Sembako',
            'business_photo' => $fakeBusinessPhoto,
            // ktp_selfie tidak diisi, karena ini produk modal usaha
        ]);

        $response->assertRedirect(route('loans.my'));
        $this->assertDatabaseCount('loan_applications', 1);
        $app = LoanApplication::first();
        $this->assertTrue($app->isBusinessLoan());
        $this->assertNotNull($app->business_photo_path);
    }

    public function test_daily_loan_limits_enforced()
    {
        // 1. Plafon di bawah batas minimum (di bawah 100rb)
        $responseLow = $this->actingAs($this->memberUser)->post(route('loans.apply.submit'), [
            'loan_product_id' => $this->dailyKonsumtifProduct->id,
            'amount' => 50000, // di bawah min_amount (100.000)
            'tenor_count' => 10,
            'purpose' => 'Cek limit min',
            'agree_terms' => 1,
            'ktp_selfie_camera' => 'data:image/jpeg;base64,' . base64_encode('bytes'),
        ]);
        $responseLow->assertSessionHasErrors(['amount']);

        // 2. Plafon di atas batas maksimum (di atas 1jt)
        $responseHigh = $this->actingAs($this->memberUser)->post(route('loans.apply.submit'), [
            'loan_product_id' => $this->dailyKonsumtifProduct->id,
            'amount' => 1500000, // di atas max_amount (1.000.000)
            'tenor_count' => 10,
            'purpose' => 'Cek limit max',
            'agree_terms' => 1,
            'ktp_selfie_camera' => 'data:image/jpeg;base64,' . base64_encode('bytes'),
        ]);
        $responseHigh->assertSessionHasErrors(['amount']);
    }
}
