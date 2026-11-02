<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Member;
use App\Models\Setting;
use App\Models\ShuDistribution;
use App\Models\ShuPeriod;
use Exception;
use Illuminate\Support\Facades\DB;

class ShuService
{
    public function __construct(protected JournalService $journals)
    {
    }

    /**
     * Hitung preview SHU untuk suatu tahun buku.
     */
    public function calculatePreview(int $year): array
    {
        $startDate = "{$year}-01-01";
        $endDate = "{$year}-12-31";

        // Total pendapatan (Akun tipe revenue)
        $revenueAccounts = ChartOfAccount::where('type', 'revenue')->pluck('id');
        $totalRevenue = (int) JournalEntry::whereIn('chart_of_account_id', $revenueAccounts)
            ->whereHas('journal', fn($q) => $q->whereBetween('date', [$startDate, $endDate])->where('status', 'posted'))
            ->where('type', 'credit')
            ->sum('amount');

        // Total beban (Akun tipe expense)
        $expenseAccounts = ChartOfAccount::where('type', 'expense')->pluck('id');
        $totalExpense = (int) JournalEntry::whereIn('chart_of_account_id', $expenseAccounts)
            ->whereHas('journal', fn($q) => $q->whereBetween('date', [$startDate, $endDate])->where('status', 'posted'))
            ->where('type', 'debit')
            ->sum('amount');

        $netShu = $totalRevenue - $totalExpense;

        $cadanganPercent = (float) Setting::get('shu_cadangan_percent', 40);
        $jasaSimpananPercent = (float) Setting::get('shu_jasa_simpanan_percent', 25);
        $jasaPinjamanPercent = (float) Setting::get('shu_jasa_pinjaman_percent', 20);

        $danaCadangan = (int) round($netShu * ($cadanganPercent / 100));
        $danaJasaSimpanan = (int) round($netShu * ($jasaSimpananPercent / 100));
        $danaJasaPinjaman = (int) round($netShu * ($jasaPinjamanPercent / 100));

        // Total Simpanan seluruh anggota aktif
        $activeMembers = Member::where('status', Member::STATUS_ACTIVE)->with(['user', 'savingsAccounts'])->get();
        $totalGlobalSavings = (int) $activeMembers->sum(fn($m) => $m->savingsAccounts->whereIn('type', ['pokok', 'wajib'])->sum('balance'));

        // Total Bunga pinjaman yang dibayar seluruh anggota tahun ini
        $totalGlobalLoanInterest = (int) DB::table('loan_payments')
            ->whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'verified')
            ->sum('interest_portion');

        $memberDistributions = [];
        foreach ($activeMembers as $member) {
            $memberSavings = (int) $member->savingsAccounts->whereIn('type', ['pokok', 'wajib'])->sum('balance');
            $memberInterestPaid = (int) DB::table('loan_payments')
                ->join('loans', 'loans.id', '=', 'loan_payments.loan_id')
                ->where('loans.member_id', $member->id)
                ->whereBetween('loan_payments.payment_date', [$startDate, $endDate])
                ->where('loan_payments.status', 'verified')
                ->sum('loan_payments.interest_portion');

            $simpananShare = ($totalGlobalSavings > 0)
                ? (int) round(($memberSavings / $totalGlobalSavings) * $danaJasaSimpanan)
                : 0;

            $pinjamanShare = ($totalGlobalLoanInterest > 0)
                ? (int) round(($memberInterestPaid / $totalGlobalLoanInterest) * $danaJasaPinjaman)
                : 0;

            $totalMemberShu = $simpananShare + $pinjamanShare;

            $memberDistributions[] = [
                'member' => $member,
                'member_savings' => $memberSavings,
                'member_interest_paid' => $memberInterestPaid,
                'simpanan_share' => $simpananShare,
                'pinjaman_share' => $pinjamanShare,
                'total_shu' => $totalMemberShu,
            ];
        }

        return [
            'year' => $year,
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_shu' => $netShu,
            'dana_cadangan' => $danaCadangan,
            'dana_jasa_simpanan' => $danaJasaSimpanan,
            'dana_jasa_pinjaman' => $danaJasaPinjaman,
            'total_global_savings' => $totalGlobalSavings,
            'total_global_loan_interest' => $totalGlobalLoanInterest,
            'member_distributions' => $memberDistributions,
        ];
    }

    /**
     * Tutup Buku & Bagikan SHU Tahun Buku.
     */
    public function closeBookAndDistribute(int $year): ShuPeriod
    {
        return DB::transaction(function () use ($year) {
            $existing = ShuPeriod::where('year', $year)->where('status', ShuPeriod::STATUS_CLOSED)->first();
            if ($existing) {
                throw new Exception("Tahun buku {$year} sudah pernah ditutup.");
            }

            $preview = $this->calculatePreview($year);

            $period = ShuPeriod::updateOrCreate(
                ['year' => $year],
                [
                    'start_date' => "{$year}-01-01",
                    'end_date' => "{$year}-12-31",
                    'total_revenue' => $preview['total_revenue'],
                    'total_expense' => $preview['total_expense'],
                    'net_shu' => $preview['net_shu'],
                    'status' => ShuPeriod::STATUS_CLOSED,
                    'closed_at' => now(),
                    'closed_by' => auth()->id(),
                ]
            );

            $totalDistributedToMembers = 0;

            foreach ($preview['member_distributions'] as $dist) {
                if ($dist['total_shu'] > 0) {
                    ShuDistribution::updateOrCreate(
                        ['shu_period_id' => $period->id, 'member_id' => $dist['member']->id],
                        [
                            'member_savings_total' => $dist['member_savings'],
                            'member_loan_interest_total' => $dist['member_interest_paid'],
                            'savings_portion_amount' => $dist['simpanan_share'],
                            'transaction_portion_amount' => $dist['pinjaman_share'],
                            'total_shu_amount' => $dist['total_shu'],
                            'status' => 'distributed',
                            'distributed_at' => now(),
                        ]
                    );

                    // Tambahkan SHU langsung ke Simpanan Sukarela anggota
                    $sukarela = $dist['member']->getSimpananSukarelaAccount();
                    if ($sukarela) {
                        $sukarela->balance += $dist['total_shu'];
                        $sukarela->save();
                    }

                    $totalDistributedToMembers += $dist['total_shu'];
                }
            }

            // Jurnal Penutupan SHU:
            // Debit: SHU Tahun Berjalan (3201) [Total Net SHU]
            // Credit: Simpanan Sukarela Anggota (2103) [Total Bagian Anggota]
            // Credit: Cadangan Koperasi (3202) [Sisa / Dana Cadangan]
            if ($preview['net_shu'] > 0) {
                $cadanganAmount = $preview['net_shu'] - $totalDistributedToMembers;
                $this->journals->record(
                    "Tutup Buku & Pembagian SHU Tahun Buku {$year}",
                    [
                        ['account_code' => '3201', 'type' => 'debit', 'amount' => $preview['net_shu']],
                        ['account_code' => '2103', 'type' => 'credit', 'amount' => $totalDistributedToMembers],
                        ['account_code' => '3202', 'type' => 'credit', 'amount' => $cadanganAmount],
                    ],
                    'shu_distribution',
                    $period->id
                );
            }

            AuditLog::log('shu_closed', $period);

            return $period;
        });
    }
}
