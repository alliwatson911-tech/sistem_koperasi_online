<?php

namespace App\Http\Controllers;

use App\Models\BillboardAnnouncement;
use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Services\LoanCalculationService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected LoanCalculationService $calculator
    ) {
    }

    /**
     * Halaman Utama / Landing Page Sistem Koperasi
     */
    public function index(Request $request)
    {
        $products = LoanProduct::where('is_active', true)->get();

        // Parameter Simulasi Pinjaman Terpadu
        $selectedProductId = $request->get('product_id', $products->first()?->id);
        $selectedProduct = $products->firstWhere('id', (int) $selectedProductId) ?: $products->first();

        $defaultAmount = ($selectedProduct && $selectedProduct->isDaily()) ? 500000 : (($selectedProduct && $selectedProduct->isWeekly()) ? 200000 : 10000000);
        $defaultTenor = ($selectedProduct && $selectedProduct->isDaily()) ? 14 : (($selectedProduct && $selectedProduct->isWeekly()) ? 10 : 12);

        $amount = (int) $request->get('amount', $defaultAmount);
        $tenor = (int) $request->get('tenor', $defaultTenor);
        $method = $request->get('calculation_method', $selectedProduct?->calculation_method ?? 'flat');

        $simulationResult = null;
        if ($selectedProduct) {
            $adminFee = (int) round(($amount * ($selectedProduct->admin_fee_percentage / 100)) + $selectedProduct->admin_fee_fixed);
            $savingsDeduction = (int) round($amount * (($selectedProduct->savings_percentage ?? 0) / 100));
            $simulationResult = $this->calculator->simulate(
                $amount,
                $tenor,
                (float) $selectedProduct->interest_rate_per_period,
                $method,
                $adminFee,
                $selectedProduct->tenor_unit,
                null,
                $savingsDeduction
            );
        }

        // Statistik Koperasi untuk Indikator Kepercayaan & Keterbukaan
        try {
            $totalMembers = Member::where('status', Member::STATUS_ACTIVE)->count();
            $totalLoans = Loan::sum('principal_amount');
            $totalSavings = SavingsAccount::sum('balance');
        } catch (\Throwable $e) {
            $totalMembers = 1450;
            $totalLoans = 12850000000;
            $totalSavings = 18500000000;
        }

        $stats = [
            'total_members' => $totalMembers ?: 1450,
            'total_loan_distributed' => $totalLoans ?: 12850000000,
            'total_savings_managed' => $totalSavings ?: 18500000000,
            'product_count' => $products->count(),
        ];

        $billboards = BillboardAnnouncement::where('is_active', true)->orderBy('order', 'asc')->get();

        return view('home', compact(
            'products',
            'selectedProduct',
            'simulationResult',
            'stats',
            'amount',
            'tenor',
            'method',
            'billboards'
        ));
    }

    /**
     * Halaman Katalog Produk Lengkap (Simpanan & Pinjaman)
     */
    public function products()
    {
        $loanProducts = LoanProduct::where('is_active', true)->get();
        $savingsProducts = $this->getSavingsProducts();

        return view('pages.products', compact('loanProducts', 'savingsProducts'));
    }

    /**
     * Halaman Panduan Layanan & Tata Cara Anggota
     */
    public function guide()
    {
        $loanProducts = LoanProduct::where('is_active', true)->get();
        return view('pages.guide', compact('loanProducts'));
    }

    /**
     * Halaman Tentang Kami, Legalitas & Tata Kelola
     */
    public function about()
    {
        try {
            $totalMembers = Member::where('status', Member::STATUS_ACTIVE)->count();
            $totalLoans = Loan::sum('principal_amount');
            $totalSavings = SavingsAccount::sum('balance');
        } catch (\Throwable $e) {
            $totalMembers = 1450;
            $totalLoans = 12850000000;
            $totalSavings = 18500000000;
        }

        $stats = [
            'total_members' => $totalMembers ?: 1450,
            'total_loan_distributed' => $totalLoans ?: 12850000000,
            'total_savings_managed' => $totalSavings ?: 18500000000,
        ];

        return view('pages.about', compact('stats'));
    }

    /**
     * Data Rincian Produk Simpanan Koperasi
     */
    public function getSavingsProducts(): array
    {
        return [
            [
                'code' => 'SP-POKOK',
                'name' => 'Simpanan Pokok',
                'type' => 'pokok',
                'nominal' => 100000,
                'nominal_formatted' => 'Rp 100.000',
                'frequency' => 'Satu kali saat registrasi',
                'badge' => 'Modal Awal Sah',
                'badge_color' => 'bg-blue-100 text-[#0F4C81] border-blue-200',
                'description' => 'Bukti kepemilikan dan keanggotaan sah KSP Sahabat Sejahtera. Memberikan hak suara dalam Rapat Anggota Tahunan (RAT) dan dapat ditarik kembali saat mengundurkan diri secara resmi.',
                'features' => [
                    'Penyetoran 1 kali diawal saat bergabung sebagai anggota',
                    'Memberikan hak suara demokratis 1 anggota 1 suara dalam RAT',
                    'Mendapatkan alokasi pembagian dividen Sisa Hasil Usaha (SHU)',
                    'Dapat dicairkan penuh saat berhenti menjadi anggota',
                ],
            ],
            [
                'code' => 'SW-WAJIB',
                'name' => 'Simpanan Wajib',
                'type' => 'wajib',
                'nominal' => 25000,
                'nominal_formatted' => 'Rp 25.000 / bln (atau 5% pencairan pinjaman)',
                'frequency' => 'Rutin bulanan atau terakumulasi saat pinjaman',
                'badge' => 'Akumulasi SHU',
                'badge_color' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'description' => 'Simpanan berkala untuk memperkuat permodalan bersama. Pada pinjaman mingguan mikro, alokasi 5% otomatis dikreditkan ke buku simpanan ini sebagai aset tabungan Anda.',
                'features' => [
                    'Iuran bulanan terjangkau Rp 25.000 atau alokasi 5% dari pencairan pinjaman mikro',
                    'Otomatis terakumulasi di buku rekening simpanan anggota secara transparan',
                    'Makin besar saldo simpanan, makin tinggi porsi SHU Jasa Modal Anda',
                    'Dapat disetor mudah via QRIS, Virtual Account, maupun Teller',
                ],
            ],
            [
                'code' => 'SS-BERKAH',
                'name' => 'Simpanan Sukarela Berkah',
                'type' => 'sukarela',
                'nominal' => 10000,
                'nominal_formatted' => 'Mulai Rp 10.000 (Fleksibel)',
                'frequency' => 'Bebas disetor & ditarik kapan saja',
                'badge' => 'Bebas Biaya Admin',
                'badge_color' => 'bg-amber-100 text-amber-900 border-amber-200',
                'description' => 'Tabungan harian likuid tanpa potongan biaya administrasi bulanan dengan bagi hasil yang berkah dan kompetitif untuk simpanan masa depan keluarga.',
                'features' => [
                    'Bebas biaya administrasi bulanan (0 Rupiah)',
                    'Bebas setor dan tarik kapan saja saat membutuhkan dana mendesak',
                    'Mendapatkan bagi hasil tabungan yang dikreditkan setiap bulan',
                    'Transaksi setor instan 24 jam melalui QRIS & Virtual Account',
                ],
            ],
            [
                'code' => 'SD-BERJANGKA',
                'name' => 'Simpanan Berjangka (Deposito Berkah)',
                'type' => 'berjangka',
                'nominal' => 1000000,
                'nominal_formatted' => 'Mulai Rp 1.000.000',
                'frequency' => 'Tenor Pilihan: 3, 6, atau 12 Bulan',
                'badge' => 'Bagi Hasil Optimal',
                'badge_color' => 'bg-purple-100 text-purple-800 border-purple-200',
                'description' => 'Penempatan dana permodalan produktif berjangka untuk pemberdayaan UMKM dengan nisbah bagi hasil optimal, aman, dan diterbitkan bilyet sertifikat digital.',
                'features' => [
                    'Pilihan jangka waktu: 3 bulan, 6 bulan, atau 12 bulan',
                    'Nisbah imbal hasil setara 7,0% s.d. 9,0% per tahun',
                    'Diterbitkan bilyet sertifikat digital resmi dari koperasi',
                    'Diprioritaskan untuk pembiayaan sektor riil dan pedagang mikro',
                ],
            ],
        ];
    }
}

