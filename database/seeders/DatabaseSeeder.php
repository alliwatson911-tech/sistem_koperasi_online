<?php

namespace Database\Seeders;

use App\Models\ChartOfAccount;
use App\Models\LoanProduct;
use App\Models\Member;
use App\Models\MemberDocument;
use App\Models\Setting;
use App\Models\User;
use App\Services\JournalService;
use App\Services\LoanService;
use App\Services\SavingsService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(JournalService $journals, SavingsService $savings, LoanService $loans): void
    {
        // 1. Bagan Akun (Chart of Accounts)
        $accounts = [
            ['code' => '1101', 'name' => 'Kas Tunai Teller', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Kas fisik di teller koperasi'],
            ['code' => '1102', 'name' => 'Bank Koperasi', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Rekening bank operasional koperasi'],
            ['code' => '1201', 'name' => 'Piutang Pinjaman Anggota', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Saldo pokok pinjaman yang dipinjam anggota'],
            ['code' => '1202', 'name' => 'Piutang Bunga Pinjaman', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Bunga pinjaman yang telah jatuh tempo'],
            ['code' => '1203', 'name' => 'Piutang Denda Pinjaman', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Denda keterlambatan yang belum dibayar'],
            ['code' => '2101', 'name' => 'Simpanan Pokok Anggota', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'Simpanan pokok anggota koperasi'],
            ['code' => '2102', 'name' => 'Simpanan Wajib Anggota', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'Simpanan bulanan anggota koperasi'],
            ['code' => '2103', 'name' => 'Simpanan Sukarela Anggota', 'type' => 'liability', 'normal_balance' => 'credit', 'description' => 'Simpanan sukarela/tabungan harian anggota'],
            ['code' => '3101', 'name' => 'Modal Awal Koperasi', 'type' => 'equity', 'normal_balance' => 'credit', 'description' => 'Modal pendirian awal koperasi'],
            ['code' => '3201', 'name' => 'SHU Tahun Berjalan', 'type' => 'equity', 'normal_balance' => 'credit', 'description' => 'Akumulasi laba/SHU tahun berjalan'],
            ['code' => '3202', 'name' => 'Cadangan Koperasi', 'type' => 'equity', 'normal_balance' => 'credit', 'description' => 'Dana cadangan koperasi dari alokasi SHU'],
            ['code' => '4101', 'name' => 'Pendapatan Bunga Pinjaman', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Pendapatan jasa bunga dari pinjaman anggota'],
            ['code' => '4102', 'name' => 'Pendapatan Administrasi Pinjaman', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Biaya administrasi dan provisi pinjaman'],
            ['code' => '4103', 'name' => 'Pendapatan Denda Keterlambatan', 'type' => 'revenue', 'normal_balance' => 'credit', 'description' => 'Pendapatan dari denda keterlambatan angsuran'],
            ['code' => '1301', 'name' => 'Dana dalam Penyelesaian Gateway', 'type' => 'asset', 'normal_balance' => 'debit', 'description' => 'Kliring penerimaan pembayaran anggota lewat payment gateway (Midtrans/QRIS/VA)'],
            ['code' => '5101', 'name' => 'Beban Bunga Simpanan Sukarela', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Bunga bagi hasil untuk simpanan sukarela'],
            ['code' => '5102', 'name' => 'Beban Operasional & Administrasi', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Biaya operasional kantor dan sistem'],
            ['code' => '5103', 'name' => 'Biaya Transaksi Payment Gateway', 'type' => 'expense', 'normal_balance' => 'debit', 'description' => 'Beban MDR/biaya administrasi gateway yang dipotong saat pencairan (settlement)'],
        ];

        foreach ($accounts as $acc) {
            ChartOfAccount::updateOrCreate(['code' => $acc['code']], $acc);
        }

        // 2. Pengaturan Dasar Koperasi
        $settings = [
            ['key' => 'koperasi_name', 'value' => 'KSP Sahabat Sejahtera', 'description' => 'Nama Resmi Koperasi', 'type' => 'string'],
            ['key' => 'koperasi_legal_no', 'value' => 'AHU-0012345.AH.01.26.TAHUN.2020', 'description' => 'Nomor Badan Hukum', 'type' => 'string'],
            ['key' => 'simpanan_pokok_nominal', 'value' => '500000', 'description' => 'Besaran Simpanan Pokok (Rp)', 'type' => 'integer'],
            ['key' => 'simpanan_wajib_nominal', 'value' => '50000', 'description' => 'Besaran Simpanan Wajib per Bulan (Rp)', 'type' => 'integer'],
            ['key' => 'shu_cadangan_percent', 'value' => '40', 'description' => 'Alokasi SHU untuk Dana Cadangan (%)', 'type' => 'decimal'],
            ['key' => 'shu_jasa_simpanan_percent', 'value' => '25', 'description' => 'Alokasi SHU untuk Jasa Modal/Simpanan (%)', 'type' => 'decimal'],
            ['key' => 'shu_jasa_pinjaman_percent', 'value' => '20', 'description' => 'Alokasi SHU untuk Jasa Usaha/Pinjaman (%)', 'type' => 'decimal'],
            ['key' => 'teller_assignment_mode', 'value' => 'queue_or_assign', 'description' => 'Mode Penugasan Teller (queue_or_assign / auto_workload)', 'type' => 'string'],
            ['key' => 'application_reception_deadline_hours', 'value' => '24', 'description' => 'Batas Waktu Toleransi Penerimaan Teller (Jam)', 'type' => 'integer'],
            ['key' => 'teller_display_mode', 'value' => 'name_and_code', 'description' => 'Format Tampilan Identitas Teller', 'type' => 'string'],
            ['key' => 'payment_gateway_driver', 'value' => 'fake', 'description' => 'Driver Payment Gateway Aktif (fake/midtrans)', 'type' => 'string'],
            ['key' => 'payment_gateway_mode', 'value' => 'sandbox', 'description' => 'Mode Gateway (sandbox/production)', 'type' => 'string'],
            ['key' => 'payment_gateway_fee_bearer', 'value' => 'cooperative', 'description' => 'Penanggung Biaya Gateway (cooperative/member)', 'type' => 'string'],
            ['key' => 'payment_gateway_expiry_hours', 'value' => '24', 'description' => 'Masa Berlaku Tagihan Gateway (Jam)', 'type' => 'integer'],
        ];

        foreach ($settings as $s) {
            Setting::set($s['key'], $s['value'], $s['description'], $s['type']);
        }

        // 3. Produk Pinjaman
        $products = [
            [
                'name' => 'Pinjaman Modal Usaha (Efektif)',
                'code' => 'PRD-MDR',
                'description' => 'Pinjaman produktif untuk modal dagang dan UMKM dengan bunga saldo menurun yang adil.',
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
            ],
            [
                'name' => 'Pinjaman Konsumtif (Flat)',
                'code' => 'PRD-KNS',
                'description' => 'Pinjaman kebutuhan darurat keluarga, pendidikan, atau renovasi dengan cicilan tetap setiap bulan.',
                'interest_rate_annual' => 15.00,
                'interest_rate_monthly' => 1.25,
                'interest_rate_per_period' => 1.25,
                'calculation_method' => 'flat',
                'tenor_unit' => 'month',
                'min_amount' => 1000000,
                'max_amount' => 20000000,
                'min_tenor_months' => 3,
                'max_tenor_months' => 12,
                'min_tenor' => 3,
                'max_tenor' => 12,
                'admin_fee_percentage' => 1.00,
                'admin_fee_fixed' => 25000,
                'penalty_rate_daily' => 0.0010,
                'is_active' => true,
            ],
            [
                'name' => 'Pinjaman Multiguna (Anuitas)',
                'code' => 'PRD-KPT',
                'description' => 'Pinjaman jangka menengah dengan skema anuitas terstruktur tanpa biaya tersembunyi.',
                'interest_rate_annual' => 13.20,
                'interest_rate_monthly' => 1.10,
                'interest_rate_per_period' => 1.10,
                'calculation_method' => 'annuity',
                'tenor_unit' => 'month',
                'min_amount' => 5000000,
                'max_amount' => 100000000,
                'min_tenor_months' => 6,
                'max_tenor_months' => 36,
                'min_tenor' => 6,
                'max_tenor' => 36,
                'admin_fee_percentage' => 1.50,
                'admin_fee_fixed' => 100000,
                'penalty_rate_daily' => 0.0010,
                'is_active' => true,
            ],
            [
                'name' => 'Pinjaman Mingguan Mikro (Anuitas)',
                'code' => 'PRD-MGN',
                'description' => 'Pinjaman produktif mikro berjangka mingguan untuk pedagang pasar dan UMKM anggota dengan bunga 0,40% per minggu.',
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
            ],
            [
                'name' => 'Pinjaman Konsumtif Harian (Flat)',
                'code' => 'PRD-KNS-DAY',
                'description' => 'Pinjaman dana talangan darurat harian dengan cicilan pokok dan bunga flat terjangkau.',
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
            ],
            [
                'name' => 'Pinjaman Multiguna Harian (Anuitas)',
                'code' => 'PRD-MLT-DAY',
                'description' => 'Pinjaman serbaguna harian dengan skema bunga anuitas adil untuk kebutuhan mendesak.',
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
            ],
        ];

        foreach ($products as $p) {
            LoanProduct::updateOrCreate(['code' => $p['code']], $p);
        }

        // 4. Pengguna Tiap Peran
        $defaultPassword = Hash::make('password123');

        $admin = User::updateOrCreate(
            ['email' => 'admin@ksp.test'],
            ['name' => 'H. Suryanto (Admin)', 'phone' => '081234567890', 'role' => User::ROLE_ADMIN, 'employee_code' => 'ADM-001', 'password' => $defaultPassword, 'is_active' => true]
        );

        $manager = User::updateOrCreate(
            ['email' => 'manajer@ksp.test'],
            ['name' => 'Dra. Endang Lestari (Manajer)', 'phone' => '081234567891', 'role' => User::ROLE_MANAGER, 'employee_code' => 'MGR-001', 'password' => $defaultPassword, 'is_active' => true]
        );

        $analyst = User::updateOrCreate(
            ['email' => 'analis@ksp.test'],
            ['name' => 'Rian Pratama, S.E. (Analis Kredit)', 'phone' => '081234567892', 'role' => User::ROLE_ANALYST, 'employee_code' => 'ANL-001', 'password' => $defaultPassword, 'is_active' => true]
        );

        $teller = User::updateOrCreate(
            ['email' => 'teller@ksp.test'],
            ['name' => 'Dewi Anggraini (Teller)', 'phone' => '081234567893', 'role' => User::ROLE_TELLER, 'employee_code' => 'TLR-001', 'password' => $defaultPassword, 'is_active' => true]
        );

        // 5. Jurnal Setoran Modal Awal Koperasi
        if (!\App\Models\Journal::where('reference_type', 'initial_equity')->exists()) {
            $journals->record(
                'Penyetoran Modal Pendirian Koperasi Awal ke Rekening Bank',
                [
                    ['account_code' => '1102', 'type' => 'debit', 'amount' => 200000000],
                    ['account_code' => '3101', 'type' => 'credit', 'amount' => 200000000],
                ],
                'initial_equity',
                1,
                date('Y') . '-01-02'
            );
        }

        // 6. Anggota 1: Budi Santoso (Aktif, Sudah Simpanan Pokok & Wajib, Memiliki Pinjaman Berjalan)
        $userBudi = User::updateOrCreate(
            ['email' => 'budi@anggota.test'],
            ['name' => 'Budi Santoso', 'phone' => '081398765432', 'role' => User::ROLE_MEMBER, 'password' => $defaultPassword, 'is_active' => true]
        );

        $memberBudi = Member::updateOrCreate(
            ['user_id' => $userBudi->id],
            [
                'member_no' => 'KSP-' . date('Y') . '-0001',
                'nik' => '3171012304850001',
                'birth_date' => '1985-04-23',
                'birth_place' => 'Jakarta',
                'gender' => 'L',
                'address' => 'Jl. Kebon Jeruk No. 15, Jakarta Barat',
                'occupation' => 'Wiraswasta Kuliner',
                'monthly_income' => 7500000,
                'bank_name' => 'BCA',
                'bank_account' => '1234567890',
                'status' => Member::STATUS_ACTIVE,
                'verified_at' => now()->subMonths(6),
                'verified_by' => $teller->id,
                'activated_at' => now()->subMonths(6),
                'activated_by' => $teller->id,
            ]
        );

        $savings->createDefaultAccounts($memberBudi);

        // Setoran Simpanan Pokok Budi
        $pokokAcc = $memberBudi->getSimpananPokokAccount();
        if ($pokokAcc && $pokokAcc->balance == 0) {
            $savings->deposit($pokokAcc, 500000, 'cash', null, 'Setoran Simpanan Pokok Anggota Baru');
        }

        // Setoran Simpanan Wajib Budi (3 bulan = 150.000)
        $wajibAcc = $memberBudi->getSimpananWajibAccount();
        if ($wajibAcc && $wajibAcc->balance == 0) {
            $savings->deposit($wajibAcc, 150000, 'cash', null, 'Setoran Simpanan Wajib 3 Bulan');
        }

        // Setoran Simpanan Sukarela Budi
        $sukarelaAcc = $memberBudi->getSimpananSukarelaAccount();
        if ($sukarelaAcc && $sukarelaAcc->balance == 0) {
            $savings->deposit($sukarelaAcc, 2000000, 'transfer', null, 'Tabungan Sukarela Awal');
        }

        // Dokumen KTP Budi
        MemberDocument::updateOrCreate(
            ['member_id' => $memberBudi->id, 'document_type' => 'ktp'],
            ['file_path' => 'documents/ktp_budi.jpg', 'original_name' => 'ktp_budi.jpg', 'status' => 'approved', 'verified_at' => now()->subMonths(6), 'verified_by' => $teller->id]
        );

        // Buat pinjaman aktif untuk Budi (Plafon Rp 10.000.000, 6 bulan, Efektif)
        $prodUsaha = LoanProduct::where('code', 'PRD-MDR')->first();
        if ($prodUsaha && $memberBudi->loans()->count() === 0) {
            $app = $loans->apply($memberBudi, $prodUsaha, 10000000, 6, 'Tambahan etalase dan bahan baku kedai');
            $loans->receiveByTeller($app, $teller, [
                'ktp' => true,
                'application_form' => true,
                'income_proof' => true,
                'member_status' => true,
                'plafon_suitability' => true,
            ], 'Dokumen identitas, izin usaha mikro, dan kelayakan permohonan telah diverifikasi lengkap.');
            $loans->reviewByAnalyst($app, 'recommend', 'Usaha kuliner berjalan lancar 3 tahun, omset stabil.');
            $loans->approveByManager($app, 'approve', 'Disetujui sesuai rekomendasi analis.');

            $loan = $app->loan;
            $loans->signAgreement($loan);
            $loans->disburse($loan, 'transfer');

            // Simulasikan pembayaran angsuran ke-1 oleh Budi
            $nextSchedule = $loan->getNextUnpaidSchedule();
            if ($nextSchedule) {
                $loans->payInstallment($loan, $nextSchedule->total_amount, 'transfer', null, 'Pembayaran Angsuran Bulan ke-1');
            }
        }

        // 7. Anggota 2: Siti Rahma (Aktif, Banyak Simpanan Sukarela)
        $userSiti = User::updateOrCreate(
            ['email' => 'siti@anggota.test'],
            ['name' => 'Siti Rahma', 'phone' => '081387654321', 'role' => User::ROLE_MEMBER, 'password' => $defaultPassword, 'is_active' => true]
        );

        $memberSiti = Member::updateOrCreate(
            ['user_id' => $userSiti->id],
            [
                'member_no' => 'KSP-' . date('Y') . '-0002',
                'nik' => '3171016508900002',
                'birth_date' => '1990-08-15',
                'birth_place' => 'Bandung',
                'gender' => 'P',
                'address' => 'Jl. Tebet Timur Dalam No. 8, Jakarta Selatan',
                'occupation' => 'Karyawan Swasta',
                'monthly_income' => 9000000,
                'bank_name' => 'Mandiri',
                'bank_account' => '9876543210',
                'status' => Member::STATUS_ACTIVE,
                'verified_at' => now()->subMonths(4),
                'verified_by' => $teller->id,
                'activated_at' => now()->subMonths(4),
                'activated_by' => $teller->id,
            ]
        );

        $savings->createDefaultAccounts($memberSiti);
        $pokokSiti = $memberSiti->getSimpananPokokAccount();
        if ($pokokSiti && $pokokSiti->balance == 0) {
            $savings->deposit($pokokSiti, 500000, 'transfer', null, 'Setoran Simpanan Pokok');
        }
        $wajibSiti = $memberSiti->getSimpananWajibAccount();
        if ($wajibSiti && $wajibSiti->balance == 0) {
            $savings->deposit($wajibSiti, 200000, 'transfer', null, 'Setoran Simpanan Wajib 4 Bulan');
        }
        $sukarelaSiti = $memberSiti->getSimpananSukarelaAccount();
        if ($sukarelaSiti && $sukarelaSiti->balance == 0) {
            $savings->deposit($sukarelaSiti, 5000000, 'transfer', null, 'Tabungan Sukarela Modal');
        }

        // Pengajuan Pinjaman baru dari Siti (masuk antrean verifikasi teller)
        $prodKonsumtif = LoanProduct::where('code', 'PRD-KNS')->first();
        if ($prodKonsumtif && $memberSiti->loanApplications()->count() === 0) {
            $loans->apply($memberSiti, $prodKonsumtif, 5000000, 6, 'Biaya kursus pelatihan kejuruan keluarga');
        }

        // 8. Anggota 3: Agus Setiawan (Baru mendaftar online, status PENDING, butuh verifikasi teller)
        $userAgus = User::updateOrCreate(
            ['email' => 'agus@anggota.test'],
            ['name' => 'Agus Setiawan', 'phone' => '081211223344', 'role' => User::ROLE_MEMBER, 'password' => $defaultPassword, 'is_active' => true]
        );

        $memberAgus = Member::updateOrCreate(
            ['user_id' => $userAgus->id],
            [
                'member_no' => null, // Belum terverifikasi
                'nik' => '3273011211920003',
                'birth_date' => '1992-11-12',
                'birth_place' => 'Bogor',
                'gender' => 'L',
                'address' => 'Jl. Pajajaran No. 42, Bogor',
                'occupation' => 'Freelance IT',
                'monthly_income' => 6000000,
                'bank_name' => 'BRI',
                'bank_account' => '0123456789',
                'status' => Member::STATUS_PENDING,
            ]
        );

        $savings->createDefaultAccounts($memberAgus);
        MemberDocument::updateOrCreate(
            ['member_id' => $memberAgus->id, 'document_type' => 'ktp'],
            ['file_path' => 'documents/ktp_agus.jpg', 'original_name' => 'ktp_agus.jpg', 'status' => 'pending']
        );
        MemberDocument::updateOrCreate(
            ['member_id' => $memberAgus->id, 'document_type' => 'selfie'],
            ['file_path' => 'documents/selfie_agus.jpg', 'original_name' => 'selfie_agus.jpg', 'status' => 'pending']
        );
    }
}
