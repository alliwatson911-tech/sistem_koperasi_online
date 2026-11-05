<?php

use App\Models\LoanProduct;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah ktp_selfie_path pada tabel loan_applications
        Schema::table('loan_applications', function (Blueprint $table) {
            if (!Schema::hasColumn('loan_applications', 'ktp_selfie_path')) {
                $table->string('ktp_selfie_path', 255)->nullable()->after('business_photo_path');
            }
        });

        // 2. Tambah ktp_selfie_path pada tabel loans
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'ktp_selfie_path')) {
                $table->string('ktp_selfie_path', 255)->nullable()->after('business_photo_path');
            }
        });

        // 3. Tambahkan produk pinjaman harian: Konsumtif & Multiguna (Plafon 100rb - 1jt, Tenor harian)
        LoanProduct::updateOrCreate(
            ['code' => 'PRD-KNS-DAY'],
            [
                'name' => 'Pinjaman Konsumtif Harian (Flat)',
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
            ]
        );

        LoanProduct::updateOrCreate(
            ['code' => 'PRD-MLT-DAY'],
            [
                'name' => 'Pinjaman Multiguna Harian (Anuitas)',
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
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        LoanProduct::whereIn('code', ['PRD-KNS-DAY', 'PRD-MLT-DAY'])->delete();

        Schema::table('loan_applications', function (Blueprint $table) {
            if (Schema::hasColumn('loan_applications', 'ktp_selfie_path')) {
                $table->dropColumn('ktp_selfie_path');
            }
        });

        Schema::table('loans', function (Blueprint $table) {
            if (Schema::hasColumn('loans', 'ktp_selfie_path')) {
                $table->dropColumn('ktp_selfie_path');
            }
        });
    }
};
