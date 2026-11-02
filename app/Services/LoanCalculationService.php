<?php

namespace App\Services;

use Carbon\Carbon;
use DateTimeInterface;

class LoanCalculationService
{
    /**
     * Hitung simulasi pinjaman dan hasilkan array jadwal angsuran
     * yang mendukung satuan bulanan (month) dan mingguan (week),
     * aritmetika tanggal kalender sebenarnya, penyerapan selisih pada angsuran terakhir,
     * serta indikator transparansi biaya setara per tahun (APR).
     *
     * @param int $principal Pokok pinjaman dalam Rupiah
     * @param int $tenor Jumlah periode tenor (minggu atau bulan)
     * @param float $ratePerPeriod Suku bunga per periode (contoh: 0.40% per minggu, 1.50% per bulan)
     * @param string $method Metode perhitungan: 'flat' | 'effective' | 'annuity'
     * @param int $adminFee Biaya administrasi dalam Rupiah
     * @param string $tenorUnit Satuan tenor: 'month' | 'week'
     * @param DateTimeInterface|null $startDate Tanggal awal kalkulasi jadwal
     * @return array
     */
    public function simulate(
        int $principal,
        int $tenor,
        float $ratePerPeriod,
        string $method = 'effective',
        int $adminFee = 50000,
        string $tenorUnit = 'month',
        ?DateTimeInterface $startDate = null
    ): array {
        $rateDecimal = $ratePerPeriod / 100.0;
        $schedules = [];
        $totalInterest = 0;
        $remainingPrincipal = $principal;

        $periodsPerYear = ($tenorUnit === 'week') ? 52 : 12;
        $periodLabel = ($tenorUnit === 'week') ? 'Minggu' : 'Bulan';
        $baseDate = $startDate ? Carbon::parse($startDate) : Carbon::today();

        if ($method === 'flat') {
            $periodPrincipal = (int) floor($principal / $tenor);
            $periodInterest = (int) round($principal * $rateDecimal);

            for ($i = 1; $i <= $tenor; $i++) {
                // Angsuran terakhir menyerap seluruh sisa pokok yang belum diamortisasi
                $principalPortion = ($i === $tenor) ? $remainingPrincipal : $periodPrincipal;
                $interestPortion = $periodInterest;
                $totalInstallment = $principalPortion + $interestPortion;
                $remainingPrincipal -= $principalPortion;
                $totalInterest += $interestPortion;

                $dueDate = ($tenorUnit === 'week')
                    ? $baseDate->copy()->addWeeks($i)->toDateString()
                    : $baseDate->copy()->addMonthsNoOverflow($i)->toDateString();

                $schedules[] = [
                    'installment_no' => $i,
                    'period_label' => "{$periodLabel} {$i}",
                    'due_date' => $dueDate,
                    'principal_amount' => $principalPortion,
                    'interest_amount' => $interestPortion,
                    'total_amount' => $totalInstallment,
                    'remaining_principal' => max(0, $remainingPrincipal),
                ];
            }
        } elseif ($method === 'annuity') {
            // Anuitas baku: A = P * [i / (1 - (1+i)^-n)]
            if ($rateDecimal > 0) {
                $annuityPayment = $principal * ($rateDecimal / (1 - pow(1 + $rateDecimal, -$tenor)));
            } else {
                $annuityPayment = $principal / $tenor;
            }

            for ($i = 1; $i <= $tenor; $i++) {
                $interestPortion = (int) round($remainingPrincipal * $rateDecimal);

                // Angsuran terakhir menyerap sisa pokok persis hingga nol
                if ($i === $tenor) {
                    $principalPortion = $remainingPrincipal;
                } else {
                    $principalPortion = (int) round($annuityPayment - $interestPortion);
                    if ($principalPortion > $remainingPrincipal) {
                        $principalPortion = $remainingPrincipal;
                    }
                }

                $totalInstallment = $principalPortion + $interestPortion;
                $remainingPrincipal -= $principalPortion;
                $totalInterest += $interestPortion;

                $dueDate = ($tenorUnit === 'week')
                    ? $baseDate->copy()->addWeeks($i)->toDateString()
                    : $baseDate->copy()->addMonthsNoOverflow($i)->toDateString();

                $schedules[] = [
                    'installment_no' => $i,
                    'period_label' => "{$periodLabel} {$i}",
                    'due_date' => $dueDate,
                    'principal_amount' => $principalPortion,
                    'interest_amount' => $interestPortion,
                    'total_amount' => $totalInstallment,
                    'remaining_principal' => max(0, $remainingPrincipal),
                ];
            }
        } else {
            // Default: Efektif (Saldo Menurun)
            $periodPrincipal = (int) floor($principal / $tenor);

            for ($i = 1; $i <= $tenor; $i++) {
                $interestPortion = (int) round($remainingPrincipal * $rateDecimal);
                $principalPortion = ($i === $tenor) ? $remainingPrincipal : $periodPrincipal;
                $totalInstallment = $principalPortion + $interestPortion;
                $remainingPrincipal -= $principalPortion;
                $totalInterest += $interestPortion;

                $dueDate = ($tenorUnit === 'week')
                    ? $baseDate->copy()->addWeeks($i)->toDateString()
                    : $baseDate->copy()->addMonthsNoOverflow($i)->toDateString();

                $schedules[] = [
                    'installment_no' => $i,
                    'period_label' => "{$periodLabel} {$i}",
                    'due_date' => $dueDate,
                    'principal_amount' => $principalPortion,
                    'interest_amount' => $interestPortion,
                    'total_amount' => $totalInstallment,
                    'remaining_principal' => max(0, $remainingPrincipal),
                ];
            }
        }

        $totalCost = $totalInterest + $adminFee;
        $totalPayable = $principal + $totalCost;

        // Perhitungan Suku Bunga Setara Per Tahun (Transparansi Finansial):
        // 1. Suku bunga nominal tahunan (r * periode per tahun)
        $annualNominalRate = round($ratePerPeriod * $periodsPerYear, 2);

        // 2. Tingkat bunga efektif per tahun (Effective Annual Rate / AER)
        $annualEffectiveRate = round((pow(1 + $rateDecimal, $periodsPerYear) - 1) * 100, 2);

        // 3. Biaya setara tahunan termasuk administrasi (Total Cost APR)
        // APR = (Total Biaya / Plafon) * (Jumlah Periode per Tahun / Tenor) * 100%
        $annualEquivalentCostRate = ($principal > 0 && $tenor > 0)
            ? round(($totalCost / $principal) * ($periodsPerYear / $tenor) * 100, 2)
            : $annualNominalRate;

        return [
            'principal' => $principal,
            'tenor' => $tenor,
            'tenor_unit' => $tenorUnit,
            'tenor_unit_label' => $periodLabel,
            'method' => $method,
            'rate_per_period' => $ratePerPeriod,
            'monthly_rate' => $ratePerPeriod, // backward compatibility
            'periods_per_year' => $periodsPerYear,
            'total_interest' => $totalInterest,
            'admin_fee' => $adminFee,
            'total_cost' => $totalCost,
            'total_payable' => $totalPayable,
            'first_month_installment' => $schedules[0]['total_amount'] ?? 0,
            'first_installment' => $schedules[0]['total_amount'] ?? 0,
            'annual_nominal_rate' => $annualNominalRate,
            'annual_effective_rate' => $annualEffectiveRate,
            'annual_equivalent_cost_rate' => $annualEquivalentCostRate,
            'schedules' => $schedules,
        ];
    }
}

