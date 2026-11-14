<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Terapkan ketentuan resmi tabel (Simpanan 5%, Administrasi 5%, Fixed Fee 0) ke SEMUA produk pinjaman
        DB::table('loan_products')->update([
            'savings_percentage' => 5.00,
            'admin_fee_percentage' => 5.00,
            'admin_fee_fixed' => 0,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert ke konfigurasi awal
        DB::table('loan_products')->where('code', '!=', 'PRD-MGN')->update([
            'savings_percentage' => 0.00,
            'admin_fee_percentage' => 1.00,
            'admin_fee_fixed' => 25000,
        ]);
    }
};

