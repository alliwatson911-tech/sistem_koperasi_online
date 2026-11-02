<?php

namespace Tests\Feature;

use App\Services\LoanCalculationService;
use Tests\TestCase;

class LoanCalculationTest extends TestCase
{
    protected LoanCalculationService $calculator;

    protected function setUp(): void
    {
        parent::setUp();
        $this->calculator = new LoanCalculationService();
    }

    public function test_flat_interest_calculation_is_accurate()
    {
        $amount = 12000000; // Rp 12.000.000
        $tenor = 12; // 12 bulan
        $rateMonthly = 1.5; // 1.5% per bulan
        $adminFee = 150000;

        $result = $this->calculator->simulate($amount, $tenor, $rateMonthly, 'flat', $adminFee);

        $this->assertEquals($amount, $result['principal']);
        $this->assertEquals(2160000, $result['total_interest']); // 12jt * 1.5% * 12 = 2.16jt
        $this->assertEquals(14310000, $result['total_payable']); // 12jt + 2.16jt + 150rb adm
        $this->assertEquals(1180000, $result['first_month_installment']); // 1jt + 180rb

        // Verifikasi jadwal angsuran
        $schedules = $result['schedules'];
        $this->assertCount(12, $schedules);
        $sumPrincipal = array_sum(array_column($schedules, 'principal_amount'));
        $this->assertEquals($amount, $sumPrincipal);
    }

    public function test_effective_interest_calculation_declines_properly()
    {
        $amount = 10000000; // Rp 10.000.000
        $tenor = 5; // 5 bulan
        $rateMonthly = 2.0; // 2% per bulan saldo menurun
        $adminFee = 100000;

        $result = $this->calculator->simulate($amount, $tenor, $rateMonthly, 'effective', $adminFee);

        $schedules = $result['schedules'];
        $this->assertCount(5, $schedules);

        // Bulan 1: bunga dari saldo 10jt = 200rb
        $this->assertEquals(200000, $schedules[0]['interest_amount']);

        // Bulan 2: bunga dari saldo 8jt = 160rb
        $this->assertEquals(160000, $schedules[1]['interest_amount']);

        // Bulan 5: bunga dari saldo 2jt = 40rb
        $this->assertEquals(40000, $schedules[4]['interest_amount']);

        // Bunga harus menurun
        $this->assertGreaterThan($schedules[1]['interest_amount'], $schedules[0]['interest_amount']);
        $this->assertGreaterThan($schedules[4]['interest_amount'], $schedules[3]['interest_amount']);

        // Total pokok di jadwal harus tepat sama dengan pinjaman
        $sumPrincipal = array_sum(array_column($schedules, 'principal_amount'));
        $this->assertEquals($amount, $sumPrincipal);
    }

    public function test_annuity_interest_calculation_has_fixed_installment()
    {
        $amount = 6000000; // Rp 6.000.000
        $tenor = 6; // 6 bulan
        $rateMonthly = 1.0; // 1% per bulan
        $adminFee = 50000;

        $result = $this->calculator->simulate($amount, $tenor, $rateMonthly, 'annuity', $adminFee);

        $schedules = $result['schedules'];
        $this->assertCount(6, $schedules);

        // Angsuran total relatif konstan tiap bulan (kecuali penyesuaian akhir)
        $firstInstallment = $schedules[0]['total_amount'];
        $secondInstallment = $schedules[1]['total_amount'];
        $this->assertEquals($firstInstallment, $secondInstallment);

        // Porsi pokok harus meningkat tiap bulan seiring menurunnya porsi bunga
        $this->assertGreaterThan($schedules[0]['principal_amount'], $schedules[1]['principal_amount']);
        $this->assertLessThan($schedules[0]['interest_amount'], $schedules[1]['interest_amount']);

        // Total pokok harus lunas tepat
        $sumPrincipal = array_sum(array_column($schedules, 'principal_amount'));
        $this->assertEquals($amount, $sumPrincipal);
    }

    public function test_simulate_helper_returns_complete_structure()
    {
        $sim = $this->calculator->simulate(5000000, 6, 1.5, 'flat', 50000);

        $this->assertArrayHasKey('principal', $sim);
        $this->assertArrayHasKey('tenor', $sim);
        $this->assertArrayHasKey('admin_fee', $sim);
        $this->assertArrayHasKey('total_interest', $sim);
        $this->assertArrayHasKey('total_payable', $sim);
        $this->assertArrayHasKey('schedules', $sim);
        $this->assertCount(6, $sim['schedules']);
    }
}
