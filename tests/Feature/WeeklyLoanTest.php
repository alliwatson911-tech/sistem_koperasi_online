<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\LoanSchedule;
use App\Models\Member;
use App\Models\User;
use App\Services\LoanCalculationService;
use App\Services\LoanService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyLoanTest extends TestCase
{
    use RefreshDatabase;

    protected LoanCalculationService $calculator;
    protected LoanService $loanService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = app(LoanCalculationService::class);
        $this->loanService = app(LoanService::class);
    }

    /**
     * CONTOH BAKU 1:
     * Mingguan: pinjaman Rp5.000.000, 10 minggu, 0,4% per minggu anuitas.
     * Angsuran sekitar Rp511.066 per minggu (toleransi Rp10 karena pembulatan);
     * minggu pertama bunga Rp20.000 dan pokok sekitar Rp491.066;
     * total bayar sekitar Rp5.110.660.
     */
    public function test_weekly_loan_standard_annuity_calculation()
    {
        $principal = 5000000;
        $tenor = 10;
        $rateWeekly = 0.4; // 0.4% per minggu
        $adminFee = 0;

        $result = $this->calculator->simulate(
            $principal,
            $tenor,
            $rateWeekly,
            'annuity',
            $adminFee,
            'week'
        );

        // Angsuran per minggu sekitar Rp 511.066 (toleransi Rp 10)
        $firstInstallment = $result['schedules'][0]['total_amount'];
        $this->assertEqualsWithDelta(511066, $firstInstallment, 10, 'Angsuran minggu pertama harus sekitar Rp 511.066 (toleransi Rp 10)');

        // Minggu pertama bunga Rp 20.000 dan pokok sekitar Rp 491.066
        $this->assertEquals(20000, $result['schedules'][0]['interest_amount'], 'Bunga minggu pertama harus Rp 20.000');
        $this->assertEqualsWithDelta(491066, $result['schedules'][0]['principal_amount'], 10, 'Pokok minggu pertama harus sekitar Rp 491.066');

        // Total bayar sekitar Rp 5.110.660
        $this->assertEqualsWithDelta(5110660, $result['total_payable'], 100, 'Total bayar harus sekitar Rp 5.110.660');

        // Penyerapan selisih: Total pokok terjadwal harus TEPAT sama dengan pokok pinjaman
        $sumPrincipal = array_sum(array_column($result['schedules'], 'principal_amount'));
        $this->assertEquals($principal, $sumPrincipal, 'Total pokok di seluruh angsuran harus tepat sama dengan Rp 5.000.000');

        // Angsuran terakhir harus menyerap selisih pembulatan
        $this->assertCount(10, $result['schedules']);
        $lastSchedule = end($result['schedules']);
        $this->assertEquals(0, $lastSchedule['remaining_principal'], 'Sisa pokok pada angsuran ke-10 harus Rp 0');
    }

    /**
     * CONTOH BAKU 2:
     * Bulanan: pinjaman Rp10.000.000, 12 bulan, 1,5% per bulan anuitas.
     * Angsuran sekitar Rp916.800; bulan pertama bunga Rp150.000 dan pokok sekitar Rp766.800 (toleransi Rp12).
     */
    public function test_monthly_loan_standard_annuity_calculation()
    {
        $principal = 10000000;
        $tenor = 12;
        $rateMonthly = 1.5; // 1.5% per bulan
        $adminFee = 0;

        $result = $this->calculator->simulate(
            $principal,
            $tenor,
            $rateMonthly,
            'annuity',
            $adminFee,
            'month'
        );

        // Angsuran sekitar Rp 916.800 (toleransi Rp 12)
        $firstInstallment = $result['schedules'][0]['total_amount'];
        $this->assertEqualsWithDelta(916800, $firstInstallment, 12, 'Angsuran bulan pertama harus sekitar Rp 916.800 (toleransi Rp 12)');

        // Bulan pertama bunga Rp 150.000 dan pokok sekitar Rp 766.800
        $this->assertEquals(150000, $result['schedules'][0]['interest_amount'], 'Bunga bulan pertama harus Rp 150.000');
        $this->assertEqualsWithDelta(766800, $result['schedules'][0]['principal_amount'], 12, 'Pokok bulan pertama harus sekitar Rp 766.800');

        // Total pokok terjadwal harus TEPAT sama dengan pokok pinjaman
        $sumPrincipal = array_sum(array_column($result['schedules'], 'principal_amount'));
        $this->assertEquals($principal, $sumPrincipal, 'Total pokok di seluruh angsuran harus tepat sama dengan Rp 10.000.000');
    }

    /**
     * CONTOH BAKU 3:
     * Alokasi pembayaran: tunggakan satu angsuran terdiri dari denda Rp5.000, bunga Rp20.000, pokok Rp491.066;
     * anggota membayar Rp300.000;
     * hasilnya denda Rp5.000 lunas, bunga Rp20.000 lunas, pokok Rp275.000 terbayar dan angsuran berstatus partial.
     */
    public function test_payment_waterfall_allocation_standard()
    {
        $this->seed();

        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $product = LoanProduct::first();

        // Buat pinjaman dummy aktif
        $loan = Loan::create([
            'member_id' => $member->id,
            'loan_product_id' => $product->id,
            'loan_no' => 'LOAN-TEST-WATERFALL',
            'principal_amount' => 5000000,
            'total_interest' => 110660,
            'total_admin_fee' => 0,
            'total_loan_amount' => 5110660,
            'outstanding_principal' => 5000000,
            'outstanding_interest' => 110660,
            'outstanding_penalty' => 5000,
            'tenor_months' => 10,
            'interest_rate_monthly' => 0.4,
            'calculation_method' => 'annuity',
            'status' => Loan::STATUS_ACTIVE,
            'disbursed_at' => now(),
            'maturity_date' => now()->addWeeks(10),
        ]);

        // Buat jadwal angsuran pertama sesuai contoh baku:
        // Pokok Rp 491.066, Bunga Rp 20.000, Denda Rp 5.000
        $schedule = LoanSchedule::create([
            'loan_id' => $loan->id,
            'installment_no' => 1,
            'due_date' => now()->subDays(5),
            'principal_amount' => 491066,
            'interest_amount' => 20000,
            'total_amount' => 511066,
            'paid_principal' => 0,
            'paid_interest' => 0,
            'paid_penalty' => 0,
            'remaining_principal' => 4508934,
            'status' => LoanSchedule::STATUS_OVERDUE,
            'days_overdue' => 5,
            'penalty_amount' => 5000,
        ]);

        // Anggota membayar Rp 300.000
        $payment = $this->loanService->payInstallment($loan, 300000, 'cash', null, 'Pembayaran contoh baku');

        // Refresh jadwal dari DB
        $schedule->refresh();

        // Verifikasi hasil alokasi:
        // Denda Rp 5.000 lunas
        $this->assertEquals(5000, $schedule->paid_penalty, 'Denda Rp 5.000 harus lunas terbayar');

        // Bunga Rp 20.000 lunas
        $this->assertEquals(20000, $schedule->paid_interest, 'Bunga Rp 20.000 harus lunas terbayar');

        // Pokok Rp 275.000 terbayar (300.000 - 5.000 - 20.000 = 275.000)
        $this->assertEquals(275000, $schedule->paid_principal, 'Pokok yang terbayar harus Rp 275.000');

        // Angsuran berstatus partial
        $this->assertEquals(LoanSchedule::STATUS_PARTIAL, $schedule->status, 'Status jadwal harus partial');
        $this->assertEquals(275000, $payment->principal_portion);
        $this->assertEquals(20000, $payment->interest_portion);
        $this->assertEquals(5000, $payment->penalty_portion);
    }

    /**
     * Uji Aritmetika Tanggal Kalender Sebenarnya:
     * - Mingguan: Jatuh tempo setiap 7 hari tepat dari tanggal pencairan.
     * - Bulanan: Menggunakan penambahan bulan kalender yang benar (bukan 30 hari datar).
     */
    public function test_calendar_arithmetic_weekly_and_monthly()
    {
        $startDate = Carbon::create(2026, 1, 15); // 15 Januari 2026

        // 1. Uji Jadwal Mingguan
        $weeklySim = $this->calculator->simulate(
            5000000,
            4,
            0.4,
            'annuity',
            0,
            'week',
            $startDate
        );

        $this->assertEquals('2026-01-22', $weeklySim['schedules'][0]['due_date']); // +7 hari
        $this->assertEquals('2026-01-29', $weeklySim['schedules'][1]['due_date']); // +14 hari
        $this->assertEquals('2026-02-05', $weeklySim['schedules'][2]['due_date']); // +21 hari
        $this->assertEquals('2026-02-12', $weeklySim['schedules'][3]['due_date']); // +28 hari

        // 2. Uji Jadwal Bulanan dari tanggal 31 Januari (harus aman dari overflow bulan)
        $startJan31 = Carbon::create(2026, 1, 31);
        $monthlySim = $this->calculator->simulate(
            10000000,
            3,
            1.5,
            'effective',
            0,
            'month',
            $startJan31
        );

        // Februari 2026 memiliki 28 hari (bukan kabisat)
        $this->assertEquals('2026-02-28', $monthlySim['schedules'][0]['due_date']);
        $this->assertEquals('2026-03-31', $monthlySim['schedules'][1]['due_date']);
        $this->assertEquals('2026-04-30', $monthlySim['schedules'][2]['due_date']);
    }

    /**
     * Uji Transparansi Biaya Setara Per Tahun (APR) pada Simulasi.
     */
    public function test_annual_equivalent_rate_transparency_in_simulation()
    {
        $principal = 5000000;
        $tenorWeeks = 10;
        $rateWeekly = 0.4;
        $adminFee = 50000;

        $sim = $this->calculator->simulate(
            $principal,
            $tenorWeeks,
            $rateWeekly,
            'annuity',
            $adminFee,
            'week'
        );

        // Memastikan field transparansi biaya ada
        $this->assertArrayHasKey('total_interest', $sim);
        $this->assertArrayHasKey('admin_fee', $sim);
        $this->assertArrayHasKey('total_cost', $sim);
        $this->assertArrayHasKey('total_payable', $sim);
        $this->assertArrayHasKey('annual_nominal_rate', $sim);
        $this->assertArrayHasKey('annual_equivalent_cost_rate', $sim);

        // Bunga nominal mingguan per tahun: 0.4% * 52 = 20.8%
        $this->assertEquals(20.8, $sim['annual_nominal_rate']);

        // Total biaya = bunga + admin fee
        $this->assertEquals($sim['total_interest'] + $adminFee, $sim['total_cost']);
        // Total bayar = principal + total biaya
        $this->assertEquals($principal + $sim['total_cost'], $sim['total_payable']);

        // Pastikan APR biaya setara per tahun dihitung secara matematis jujur
        $this->assertGreaterThan(0, $sim['annual_equivalent_cost_rate']);
    }

    /**
     * Uji Batas Maksimum Denda 10% per Angsuran & Non-Compounding.
     */
    public function test_penalty_cap_max_10_percent_non_compounding()
    {
        $this->seed();

        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $product = LoanProduct::first();

        $loan = Loan::create([
            'member_id' => $member->id,
            'loan_product_id' => $product->id,
            'loan_no' => 'LOAN-TEST-PENALTY-CAP',
            'principal_amount' => 1000000,
            'total_interest' => 100000,
            'total_admin_fee' => 0,
            'total_loan_amount' => 1100000,
            'outstanding_principal' => 1000000,
            'outstanding_interest' => 100000,
            'outstanding_penalty' => 0,
            'tenor_months' => 1,
            'interest_rate_monthly' => 1.5,
            'calculation_method' => 'flat',
            'status' => Loan::STATUS_ACTIVE,
            'disbursed_at' => now()->subDays(200),
            'maturity_date' => now()->subDays(170),
        ]);

        $schedule = LoanSchedule::create([
            'loan_id' => $loan->id,
            'installment_no' => 1,
            'due_date' => now()->subDays(170), // Terlambat 170 hari (jauh melebihi batas 100 hari untuk tarif 0.1%/hari)
            'principal_amount' => 1000000,
            'interest_amount' => 15000,
            'total_amount' => 1015000,
            'paid_principal' => 0,
            'paid_interest' => 0,
            'paid_penalty' => 0,
            'remaining_principal' => 0,
            'status' => LoanSchedule::STATUS_UNPAID,
            'days_overdue' => 0,
            'penalty_amount' => 0,
        ]);

        // Jalankan update overdue & penalties
        $this->loanService->updateOverdueAndPenalties();

        $schedule->refresh();

        // Angsuran pokok adalah Rp 1.000.000.
        // Dengan batas maksimal 10%, denda tidak boleh melebihi Rp 100.000 (meski telat 170 hari)
        $maxPenaltyCap = (int) round($schedule->principal_amount * 0.10);
        $this->assertEquals($maxPenaltyCap, $schedule->penalty_amount, 'Denda keterlambatan harus dibatasi maksimal 10% per angsuran');
    }

    /**
     * Uji Pelunasan Dipercepat (Early Repayment).
     * Bunga masa depan dibebaskan, seluruh sisa pokok dilunasi, dan status pinjaman menjadi paid_off.
     */
    public function test_early_repayment_for_weekly_loan()
    {
        $this->seed();

        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $product = LoanProduct::first();

        $loan = Loan::create([
            'member_id' => $member->id,
            'loan_product_id' => $product->id,
            'loan_no' => 'LOAN-TEST-EARLY-REPAY',
            'principal_amount' => 5000000,
            'total_interest' => 110660,
            'total_admin_fee' => 0,
            'total_loan_amount' => 5110660,
            'outstanding_principal' => 5000000,
            'outstanding_interest' => 110660,
            'outstanding_penalty' => 0,
            'tenor_unit' => 'week',
            'tenor_count' => 10,
            'tenor_months' => 3,
            'interest_rate_monthly' => 0.4,
            'interest_rate_per_period' => 0.4,
            'calculation_method' => 'annuity',
            'status' => Loan::STATUS_ACTIVE,
            'disbursed_at' => now(),
            'maturity_date' => now()->addWeeks(10),
        ]);

        for ($i = 1; $i <= 10; $i++) {
            LoanSchedule::create([
                'loan_id' => $loan->id,
                'installment_no' => $i,
                'due_date' => now()->addWeeks($i)->toDateString(),
                'principal_amount' => 500000,
                'interest_amount' => 11066,
                'total_amount' => 511066,
                'paid_principal' => 0,
                'paid_interest' => 0,
                'paid_penalty' => 0,
                'remaining_principal' => 5000000 - ($i * 500000),
                'status' => LoanSchedule::STATUS_UNPAID,
            ]);
        }

        // Lakukan pelunasan dipercepat
        $payment = $this->loanService->earlyRepayment($loan, 'transfer', 'Pelunasan dipercepat minggu ke-2');

        $loan->refresh();

        $this->assertEquals(Loan::STATUS_PAID_OFF, $loan->status);
        $this->assertEquals(0, $loan->outstanding_principal);
        $this->assertEquals(0, $loan->outstanding_interest);
        $this->assertEquals(5000000, $payment->principal_portion);
        $this->assertEquals(5000000, $payment->amount);

        // Seluruh jadwal harus berstatus paid
        $unpaidCount = $loan->schedules()->where('status', '!=', LoanSchedule::STATUS_PAID)->count();
        $this->assertEquals(0, $unpaidCount);
    }

    /**
     * Uji Siklus Utuh Pinjaman Mingguan (Lifecycle):
     * Pengajuan -> Penerimaan Teller -> Review Analis -> Persetujuan Manajer -> Akad -> Pencairan -> Pembayaran.
     */
    public function test_full_weekly_loan_lifecycle()
    {
        $this->seed();

        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $teller = User::where('role', User::ROLE_TELLER)->first();
        $analyst = User::where('role', User::ROLE_ANALYST)->first();
        $manager = User::where('role', User::ROLE_MANAGER)->first();

        // Buat produk mingguan
        $weeklyProduct = LoanProduct::create([
            'name' => 'Pinjaman Mikro Mingguan',
            'code' => 'PRD-MKK-TEST',
            'description' => 'Pinjaman mingguan untuk pedagang pasar',
            'interest_rate_annual' => 20.80,
            'interest_rate_monthly' => 1.73,
            'interest_rate_per_period' => 0.40,
            'calculation_method' => 'annuity',
            'tenor_unit' => 'week',
            'min_amount' => 500000,
            'max_amount' => 5000000,
            'min_tenor_months' => 1,
            'max_tenor_months' => 6,
            'min_tenor' => 4,
            'max_tenor' => 24,
            'admin_fee_percentage' => 1.00,
            'admin_fee_fixed' => 25000,
            'penalty_rate_daily' => 0.0010,
            'is_active' => true,
        ]);

        // 1. Anggota mengajukan pinjaman 10 minggu
        $app = $this->loanService->apply(
            $member,
            $weeklyProduct,
            5000000,
            10,
            'Tambahan modal usaha warung'
        );

        $this->assertEquals('week', $app->tenor_unit);
        $this->assertEquals(10, $app->tenor_count);
        $this->assertEquals('submitted', $app->status);

        // 2. Teller mengklaim dan menerima berkas
        $this->loanService->claimByTeller($app, $teller);
        $checklist = LoanApplication::defaultChecklistItems();
        foreach ($checklist as $key => &$item) {
            $item['verified'] = true;
        }
        $this->loanService->receiveByTeller($app, $teller, $checklist, 'Berkas fisik warung terverifikasi lengkap');
        $app->refresh();
        $this->assertEquals('received', $app->status);

        // 3. Analis mereview
        $this->actingAs($analyst);
        $this->loanService->reviewByAnalyst($app, 'recommend', 'Rekomendasi plafon penuh Rp 5.000.000 tenor 10 minggu', 'Rp 5.000.000');
        $app->refresh();
        $this->assertEquals('in_review', $app->status);

        // 4. Manajer menyetujui
        $this->actingAs($manager);
        $this->loanService->approveByManager($app, 'approve', 'Disetujui untuk modal dagang mingguan');
        $app->refresh();
        $this->assertEquals('approved', $app->status);

        $loan = Loan::where('loan_application_id', $app->id)->first();
        $this->assertNotNull($loan);
        $this->assertEquals('week', $loan->tenor_unit);
        $this->assertEquals(10, $loan->tenor_count);

        // 5. Anggota tanda tangan akad
        $this->loanService->signAgreement($loan);
        $loan->refresh();
        $this->assertNotNull($loan->agreed_at);

        // 6. Pencairan dana oleh teller
        $this->actingAs($teller);
        $this->loanService->disburse($loan, 'cash');
        $loan->refresh();

        $this->assertEquals(Loan::STATUS_ACTIVE, $loan->status);
        $this->assertCount(10, $loan->schedules);

        // Periksa jadwal pertama: tepat 7 hari ke depan
        $firstSchedule = $loan->schedules->first();
        $this->assertEquals(now()->addWeeks(1)->toDateString(), $firstSchedule->due_date->toDateString());
        $this->assertEquals(511066, $firstSchedule->total_amount);

        // 7. Pembayaran angsuran pertama
        $payment = $this->loanService->payInstallment($loan, 511066, 'cash', null, 'Angsuran minggu ke-1');
        $firstSchedule->refresh();
        $this->assertEquals(LoanSchedule::STATUS_PAID, $firstSchedule->status);
    }

    /**
     * Uji Ketentuan Resmi Tabel Pinjaman Mingguan Sesuai Dokumen Fisik:
     * - Besar pinjaman: Rp 200.000
     * - Simpanan 5%: Rp 10.000
     * - Administrasi 5%: Rp 10.000
     * - Besar angsuran: Rp 24.000 (10x angsuran)
     * - Jumlah yang diterima (bersih): Rp 180.000
     * - Banyak angsuran: 10
     */
    public function test_weekly_loan_matches_official_table_provisions_from_image()
    {
        $this->seed();

        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $teller = User::where('role', User::ROLE_TELLER)->first();
        $analyst = User::where('role', User::ROLE_ANALYST)->first();
        $manager = User::where('role', User::ROLE_MANAGER)->first();

        // Ambil produk resmi PRD-MGN atau buat produk mingguan mikro flat
        $product = LoanProduct::where('code', 'PRD-MGN')->first();
        if (!$product) {
            $product = LoanProduct::create([
                'name' => 'Pinjaman Mingguan Mikro',
                'code' => 'PRD-MGN',
                'description' => 'Pinjaman mingguan untuk pedagang pasar & mikro',
                'interest_rate_annual' => 104.00,
                'interest_rate_monthly' => 8.00,
                'interest_rate_per_period' => 2.00,
                'calculation_method' => 'flat',
                'tenor_unit' => 'week',
                'min_amount' => 200000,
                'max_amount' => 5000000,
                'min_tenor_months' => 1,
                'max_tenor_months' => 6,
                'min_tenor' => 4,
                'max_tenor' => 24,
                'admin_fee_percentage' => 5.00,
                'admin_fee_fixed' => 0,
                'savings_percentage' => 5.00,
                'penalty_rate_daily' => 0.0010,
                'is_active' => true,
            ]);
        } else {
            $product->update([
                'calculation_method' => 'flat',
                'interest_rate_per_period' => 2.00,
                'admin_fee_percentage' => 5.00,
                'admin_fee_fixed' => 0,
                'savings_percentage' => 5.00,
                'min_amount' => 200000,
            ]);
        }

        // 1. Verifikasi kalkulator simulasi
        $principal = 200000;
        $tenor = 10;
        $adminFee = (int) round($principal * ($product->admin_fee_percentage / 100)); // 10.000
        $savingsDeduction = (int) round($principal * ($product->savings_percentage / 100)); // 10.000

        $simulation = $this->calculator->simulate(
            $principal,
            $tenor,
            (float) $product->interest_rate_per_period,
            'flat',
            $adminFee,
            'week',
            null,
            $savingsDeduction
        );

        $this->assertEquals(200000, $simulation['principal'], 'Besar pinjaman harus Rp 200.000');
        $this->assertEquals(10000, $simulation['admin_fee'], 'Administrasi 5% harus Rp 10.000');
        $this->assertEquals(10000, $simulation['savings_deduction'], 'Simpanan 5% harus Rp 10.000');
        $this->assertEquals(180000, $simulation['net_disbursement_amount'], 'Jumlah yang diterima harus Rp 180.000');
        $this->assertEquals(24000, $simulation['first_installment'], 'Besar angsuran harus Rp 24.000 per minggu');
        $this->assertEquals(40000, $simulation['total_interest'], 'Total bunga pinjaman 10 minggu harus Rp 40.000');
        $this->assertEquals(250000, $simulation['total_payable'], 'Total pengeluaran (pokok + bunga + admin) harus Rp 250.000');
        $sumInstallments = array_sum(array_column($simulation['schedules'], 'total_amount'));
        $this->assertEquals(240000, $sumInstallments, 'Total angsuran pengembalian 10 kali harus Rp 240.000');
        $this->assertCount(10, $simulation['schedules'], 'Banyak angsuran harus 10');

        foreach ($simulation['schedules'] as $s) {
            $this->assertEquals(20000, $s['principal_amount'], 'Pokok per minggu Rp 20.000');
            $this->assertEquals(4000, $s['interest_amount'], 'Bunga per minggu Rp 4.000');
            $this->assertEquals(24000, $s['total_amount'], 'Angsuran per minggu Rp 24.000');
        }

        // 2. Lifecycle pengajuan pinjaman
        $app = $this->loanService->apply(
            $member,
            $product,
            200000,
            10,
            'Modal usaha dagang mikro sesuai ketentuan tabel'
        );

        $this->assertEquals(200000, $app->amount);
        $this->assertEquals(10, $app->tenor_count);
        $this->assertEquals(5.00, $app->savings_percentage);
        $this->assertEquals(10000, $app->savings_amount);
        $this->assertEquals(180000, $app->net_disbursement_amount);
        $this->assertEquals(24000, $app->monthly_installment_estimate);

        // 3. Teller terima & verifikasi berkas
        $this->loanService->claimByTeller($app, $teller);
        $checklist = LoanApplication::defaultChecklistItems();
        foreach ($checklist as $key => &$item) {
            $item['verified'] = true;
        }
        $this->loanService->receiveByTeller($app, $teller, $checklist, 'Berkas pengajuan Rp 200.000 lengkap');

        // 4. Analis review
        $this->actingAs($analyst);
        $this->loanService->reviewByAnalyst($app, 'recommend', 'Rekomendasi pinjaman mikro Rp 200.000 tenor 10 minggu', 'Rp 200.000');

        // 5. Manajer setujui
        $this->actingAs($manager);
        $this->loanService->approveByManager($app, 'approve', 'Disetujui sesuai tabel resmi');

        $loan = Loan::where('loan_application_id', $app->id)->first();
        $this->assertNotNull($loan);
        $this->assertEquals(200000, $loan->principal_amount);
        $this->assertEquals(10000, $loan->total_admin_fee);
        $this->assertEquals(10000, $loan->total_savings_deduction);
        $this->assertEquals(180000, $loan->net_disbursement_amount);

        // 6. Anggota tanda tangani akad
        $this->loanService->signAgreement($loan);

        // 7. Saldo simpanan wajib anggota sebelum pencairan
        $wajibAccount = $member->savingsAccounts()->firstOrCreate(
            ['type' => 'wajib'],
            [
                'account_number' => 'SW-' . $member->member_no,
                'balance' => 0,
                'status' => 'active',
                'interest_rate' => 0,
                'opened_at' => now(),
            ]
        );
        $initialBalance = $wajibAccount->balance;

        // 8. Teller cairkan pinjaman
        $this->actingAs($teller);
        $this->loanService->disburse($loan, 'cash');
        $loan->refresh();
        $wajibAccount->refresh();

        $this->assertEquals(Loan::STATUS_ACTIVE, $loan->status);
        $this->assertEquals($initialBalance + 10000, $wajibAccount->balance, 'Simpanan 5% (Rp 10.000) masuk ke rekening simpanan wajib anggota');

        // 9. Cek jadwal angsuran
        $this->assertCount(10, $loan->schedules);
        foreach ($loan->schedules as $sched) {
            $this->assertEquals(24000, $sched->total_amount, 'Tiap angsuran tepat Rp 24.000');
            $this->assertEquals(20000, $sched->principal_amount, 'Pokok tepat Rp 20.000');
            $this->assertEquals(4000, $sched->interest_amount, 'Bunga tepat Rp 4.000');
        }

        // 10. Bayar angsuran pertama Rp 24.000
        $firstSched = $loan->schedules->first();
        $this->loanService->payInstallment($loan, 24000, 'cash', null, 'Angsuran 1 pinjaman mingguan');
        $firstSched->refresh();
        $this->assertEquals(LoanSchedule::STATUS_PAID, $firstSched->status);
        $this->assertEquals(20000, $firstSched->paid_principal);
        $this->assertEquals(4000, $firstSched->paid_interest);
        $this->assertEquals(24000, $firstSched->paid_principal + $firstSched->paid_interest);
        $this->assertNotNull($firstSched->paid_at);
    }
}
