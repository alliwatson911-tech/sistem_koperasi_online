<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\LoanSchedule;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\Setting;
use App\Models\User;
use App\Services\LoanService;
use App\Services\SavingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanRepaymentTest extends TestCase
{
    use RefreshDatabase;

    protected LoanService $loanService;
    protected User $memberUser;
    protected Member $member;
    protected User $tellerUser;
    protected User $analystUser;
    protected User $managerUser;
    protected LoanProduct $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loanService = app(LoanService::class);

        // Siapkan Akun CoA penting
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

        // Setelan awal
        Setting::set('simpanan_pokok_amount', 100000);
        Setting::set('simpanan_wajib_amount', 25000);
        Setting::set('penalty_rate_daily', 0.1);

        // User peran
        $this->tellerUser = User::factory()->create(['role' => 'teller']);
        $this->analystUser = User::factory()->create(['role' => 'analyst']);
        $this->managerUser = User::factory()->create(['role' => 'manager']);

        // Member
        $this->memberUser = User::factory()->create(['role' => 'member']);
        $this->member = Member::create([
            'user_id' => $this->memberUser->id,
            'member_no' => 'ANG-TEST-001',
            'nik' => '3201010101010001',
            'birth_date' => '1990-01-01',
            'birth_place' => 'Jakarta',
            'gender' => 'L',
            'address' => 'Jl. Uji Coba No. 1',
            'occupation' => 'Wiraswasta',
            'monthly_income' => 7500000,
            'status' => Member::STATUS_ACTIVE,
        ]);

        app(SavingsService::class)->createDefaultAccounts($this->member);

        // Produk Pinjaman
        $this->product = LoanProduct::create([
            'code' => 'PRD-TEST-FLAT',
            'name' => 'Pinjaman Uji Coba Flat',
            'description' => 'Produk untuk testing angsuran',
            'calculation_method' => 'flat',
            'interest_rate_annual' => 12.0, // 12%
            'interest_rate_monthly' => 1.0, // 1%
            'min_amount' => 500000,
            'max_amount' => 20000000,
            'min_tenor_months' => 1,
            'max_tenor_months' => 12,
            'admin_fee_percentage' => 1.0, // 1%
            'admin_fee_fixed' => 0,
            'penalty_rate_daily' => 0.1,
            'is_active' => true,
        ]);
    }

    public function test_loan_full_lifecycle_and_schedules_generation()
    {
        $this->actingAs($this->memberUser);

        // 1. Pengajuan pinjaman
        $application = $this->loanService->apply(
            $this->member,
            $this->product,
            12000000, // 12jt
            12, // 12 bulan
            'Modal usaha sembako'
        );

        $this->assertEquals(LoanApplication::STATUS_SUBMITTED, $application->status);
        $this->assertEquals(12000000, $application->amount);

        // 1.5. Teller menerima dan memverifikasi kelengkapan berkas
        $this->actingAs($this->tellerUser);
        $this->loanService->receiveByTeller($application, $this->tellerUser, [
            'ktp' => true,
            'application_form' => true,
            'income_proof' => true,
            'member_status' => true,
        ], 'Dokumen lengkap dan absah.');
        $application->refresh();
        $this->assertEquals(LoanApplication::STATUS_RECEIVED, $application->status);
        $this->assertEquals($this->tellerUser->id, $application->received_by);

        // 2. Analis meninjau
        $this->actingAs($this->analystUser);
        $this->loanService->reviewByAnalyst($application, 'recommend', 'Debitur memiliki usaha aktif dan prospektif.');
        $application->refresh();
        $this->assertEquals(LoanApplication::STATUS_IN_REVIEW, $application->status);

        // 3. Manajer menyetujui -> otomatis membuat pinjaman berstatus approved_awaiting_disbursement
        $this->actingAs($this->managerUser);
        $this->loanService->approveByManager($application, 'approve', 'Disetujui penuh.');
        $application->refresh();
        $this->assertEquals(LoanApplication::STATUS_APPROVED, $application->status);

        $loan = $application->loan;
        $this->assertNotNull($loan);
        $this->assertEquals(Loan::STATUS_AWAITING_DISBURSEMENT, $loan->status);

        // 4. Akad ditandatangani anggota
        $this->actingAs($this->memberUser);
        $this->loanService->signAgreement($loan);
        $loan->refresh();
        $this->assertNotNull($loan->agreed_at);

        // 5. Pencairan oleh Teller
        $this->actingAs($this->tellerUser);
        $this->loanService->disburse($loan, 'cash');
        $loan->refresh();

        $this->assertEquals(Loan::STATUS_ACTIVE, $loan->status);
        $this->assertEquals(12000000, $loan->outstanding_principal);

        // Verifikasi jadwal angsuran dibuat otomatis (12 jadwal)
        $this->assertCount(12, $loan->schedules);
        $firstSchedule = $loan->schedules->first();
        $this->assertEquals(LoanSchedule::STATUS_UNPAID, $firstSchedule->status);
        $this->assertEquals(1000000, $firstSchedule->principal_amount);
        $this->assertEquals(120000, $firstSchedule->interest_amount);
        $this->assertEquals(1120000, $firstSchedule->total_amount);
    }

    public function test_loan_partial_and_full_payment_settlement()
    {
        $this->actingAs($this->memberUser);
        $application = $this->loanService->apply($this->member, $this->product, 2000000, 2, 'Biaya pendidikan');

        $this->actingAs($this->tellerUser);
        $this->loanService->receiveByTeller($application, $this->tellerUser, [
            'ktp' => true,
            'application_form' => true,
            'income_proof' => true,
            'member_status' => true,
        ], 'Lengkap');

        $this->actingAs($this->analystUser);
        $this->loanService->reviewByAnalyst($application, 'recommend', 'Rekomendasi setuju');

        $this->actingAs($this->managerUser);
        $this->loanService->approveByManager($application, 'approve', 'Disetujui');

        $loan = $application->loan;
        $this->actingAs($this->memberUser);
        $this->loanService->signAgreement($loan);

        $this->actingAs($this->tellerUser);
        $this->loanService->disburse($loan, 'cash');
        $loan->refresh();

        // Pinjaman 2jt tenor 2 bln (pokok 1jt + bunga 20rb per bulan = 1.020.000)
        // 1. Pembayaran Angsuran 1
        $payment1 = $this->loanService->payInstallment($loan, 1020000, 'cash', null, 'Angsuran 1');
        $loan->refresh();

        $this->assertEquals(1000000, $loan->outstanding_principal);
        $this->assertEquals(20000, $loan->outstanding_interest);
        $this->assertEquals(Loan::STATUS_ACTIVE, $loan->status);

        $schedule1 = $loan->schedules()->where('installment_no', 1)->first();
        $this->assertEquals(LoanSchedule::STATUS_PAID, $schedule1->status);

        // 2. Pembayaran Angsuran 2 (Pelunasan)
        $payment2 = $this->loanService->payInstallment($loan, 1020000, 'cash', null, 'Angsuran 2 Pelunasan');
        $loan->refresh();

        $this->assertEquals(0, $loan->outstanding_principal);
        $this->assertEquals(0, $loan->outstanding_interest);
        $this->assertEquals(Loan::STATUS_PAID_OFF, $loan->status); // Status menjadi LUNAS

        $schedule2 = $loan->schedules()->where('installment_no', 2)->first();
        $this->assertEquals(LoanSchedule::STATUS_PAID, $schedule2->status);
    }
}
