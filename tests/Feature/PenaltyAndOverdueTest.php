<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\LoanSchedule;
use App\Models\Member;
use App\Models\Penalty;
use App\Models\Setting;
use App\Models\User;
use App\Services\LoanService;
use App\Services\SavingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenaltyAndOverdueTest extends TestCase
{
    use RefreshDatabase;

    protected LoanService $loanService;
    protected User $memberUser;
    protected Member $member;
    protected User $tellerUser;
    protected LoanProduct $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loanService = app(LoanService::class);

        // Siapkan CoA
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

        Setting::set('penalty_rate_daily', 0.1); // 0.1% per hari

        $this->tellerUser = User::factory()->create(['role' => 'teller']);

        $this->memberUser = User::factory()->create(['role' => 'member']);
        $this->member = Member::create([
            'user_id' => $this->memberUser->id,
            'member_no' => 'ANG-OVERDUE-01',
            'nik' => '3201010101019999',
            'birth_date' => '1988-05-10',
            'birth_place' => 'Bandung',
            'gender' => 'P',
            'address' => 'Jl. Koperasi Makmur',
            'occupation' => 'Karyawan',
            'monthly_income' => 6000000,
            'status' => Member::STATUS_ACTIVE,
        ]);

        app(SavingsService::class)->createDefaultAccounts($this->member);

        $this->product = LoanProduct::create([
            'code' => 'PRD-TEST-PENALTY',
            'name' => 'Pinjaman Flat Denda',
            'description' => 'Testing denda',
            'calculation_method' => 'flat',
            'interest_rate_annual' => 12.0,
            'interest_rate_monthly' => 1.0,
            'min_amount' => 500000,
            'max_amount' => 20000000,
            'min_tenor_months' => 1,
            'max_tenor_months' => 12,
            'admin_fee_percentage' => 1.0,
            'admin_fee_fixed' => 0,
            'penalty_rate_daily' => 0.1, // 0.1% per hari
            'is_active' => true,
        ]);
    }

    public function test_overdue_schedule_triggers_penalty_calculation()
    {
        // Buat pinjaman aktif langsung
        $loan = Loan::create([
            'loan_application_id' => null,
            'member_id' => $this->member->id,
            'loan_product_id' => $this->product->id,
            'loan_no' => 'LOAN-TEST-DND-01',
            'principal_amount' => 1000000,
            'total_interest' => 20000,
            'total_admin_fee' => 10000,
            'total_loan_amount' => 1030000,
            'outstanding_principal' => 1000000,
            'outstanding_interest' => 20000,
            'outstanding_penalty' => 0,
            'tenor_months' => 2,
            'interest_rate_monthly' => 1.0,
            'calculation_method' => 'flat',
            'status' => Loan::STATUS_ACTIVE,
            'agreed_at' => now()->subMonths(2),
            'disbursed_at' => now()->subMonths(2),
            'disbursed_by' => $this->tellerUser->id,
            'maturity_date' => now()->toDateString(),
        ]);

        // Buat 1 jadwal angsuran yang jatuh tempo 5 hari yang lalu
        $schedule = LoanSchedule::create([
            'loan_id' => $loan->id,
            'installment_no' => 1,
            'due_date' => now()->subDays(5)->toDateString(),
            'principal_amount' => 500000,
            'interest_amount' => 10000,
            'total_amount' => 510000,
            'paid_principal' => 0,
            'paid_interest' => 0,
            'paid_penalty' => 0,
            'remaining_principal' => 500000,
            'status' => LoanSchedule::STATUS_UNPAID,
        ]);

        // Jalankan service update keterlambatan & denda
        $updatedCount = $this->loanService->updateOverdueAndPenalties();

        $this->assertGreaterThanOrEqual(1, $updatedCount);

        $schedule->refresh();
        $loan->refresh();

        // 1. Jadwal dan pinjaman berubah menjadi OVERDUE
        $this->assertEquals(LoanSchedule::STATUS_OVERDUE, $schedule->status);
        $this->assertEquals(Loan::STATUS_OVERDUE, $loan->status);

        // 2. Denda tercatat
        $this->assertGreaterThan(0, $loan->outstanding_penalty);
        $this->assertDatabaseHas('penalties', [
            'loan_id' => $loan->id,
            'loan_schedule_id' => $schedule->id,
            'is_settled' => false,
        ]);

        // 3. Pembayaran menunggak melunasi denda terlebih dahulu (waterfall)
        $penaltyBefore = $loan->outstanding_penalty;
        $paymentAmount = $penaltyBefore + 10000 + 100000; // denda + bunga + sebagian pokok

        $this->actingAs($this->tellerUser);
        $this->loanService->payInstallment($loan, $paymentAmount, 'cash', null, 'Bayar angsuran menunggak beserta denda');

        $loan->refresh();
        $this->assertEquals(0, $loan->outstanding_penalty); // Denda lunas
    }
}
