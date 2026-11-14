<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. loan_products: tambahkan savings_percentage
        if (!Schema::hasColumn('loan_products', 'savings_percentage')) {
            Schema::table('loan_products', function (Blueprint $table) {
                $table->decimal('savings_percentage', 5, 2)->default(0.00)->after('admin_fee_fixed');
            });
        }

        // 2. loan_applications: tambahkan savings_percentage, savings_amount, net_disbursement_amount
        if (!Schema::hasColumn('loan_applications', 'savings_percentage')) {
            Schema::table('loan_applications', function (Blueprint $table) {
                $table->decimal('savings_percentage', 5, 2)->default(0.00)->after('admin_fee');
                $table->unsignedBigInteger('savings_amount')->default(0)->after('savings_percentage');
                $table->unsignedBigInteger('net_disbursement_amount')->nullable()->after('savings_amount');
            });
        }

        // 3. loans: tambahkan savings_percentage, total_savings_deduction, net_disbursement_amount
        if (!Schema::hasColumn('loans', 'savings_percentage')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->decimal('savings_percentage', 5, 2)->default(0.00)->after('total_admin_fee');
                $table->unsignedBigInteger('total_savings_deduction')->default(0)->after('savings_percentage');
                $table->unsignedBigInteger('net_disbursement_amount')->nullable()->after('total_savings_deduction');
            });
        }

        // 4. Update data produk Pinjaman Mingguan Mikro (PRD-MGN) sesuai tabel resmi pada gambar:
        // Plafon mulai Rp 200.000, Bunga Flat 2.00%/minggu, Administrasi 5%, Simpanan 5%, Tenor 10 minggu, Besar angsuran Rp 24.000, Diterima Rp 180.000
        DB::table('loan_products')->where('code', 'PRD-MGN')->update([
            'name' => 'Pinjaman Mingguan Mikro (Flat)',
            'description' => 'Pinjaman produktif mikro berjangka mingguan untuk pedagang pasar dan UMKM anggota dengan bunga flat 2,00% per minggu (10 minggu), simpanan 5%, dan administrasi 5%.',
            'calculation_method' => 'flat',
            'tenor_unit' => 'week',
            'min_amount' => 200000,
            'max_amount' => 5000000,
            'min_tenor' => 4,
            'max_tenor' => 24,
            'interest_rate_per_period' => 2.00,
            'interest_rate_monthly' => 8.66,
            'interest_rate_annual' => 104.00,
            'admin_fee_percentage' => 5.00,
            'admin_fee_fixed' => 0,
            'savings_percentage' => 5.00,
            'is_active' => true,
        ]);
    }

    public function down(): void
    {
        if (Schema::hasColumn('loans', 'savings_percentage')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->dropColumn(['savings_percentage', 'total_savings_deduction', 'net_disbursement_amount']);
            });
        }

        if (Schema::hasColumn('loan_applications', 'savings_percentage')) {
            Schema::table('loan_applications', function (Blueprint $table) {
                $table->dropColumn(['savings_percentage', 'savings_amount', 'net_disbursement_amount']);
            });
        }

        if (Schema::hasColumn('loan_products', 'savings_percentage')) {
            Schema::table('loan_products', function (Blueprint $table) {
                $table->dropColumn('savings_percentage');
            });
        }
    }
};

