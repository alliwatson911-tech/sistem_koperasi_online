<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_products', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('code', 20)->unique(); // PRD-REG, PRD-MDR
            $table->text('description')->nullable();
            $table->decimal('interest_rate_annual', 5, 2); // contoh: 12.00%
            $table->decimal('interest_rate_monthly', 5, 2); // contoh: 1.00%
            $table->decimal('interest_rate_per_period', 5, 2)->nullable(); // suku bunga per periode (bulan / minggu)
            $table->enum('calculation_method', ['flat', 'effective', 'annuity'])->default('effective');
            $table->string('tenor_unit', 10)->default('month'); // month | week
            $table->unsignedBigInteger('min_amount');
            $table->unsignedBigInteger('max_amount');
            $table->unsignedInteger('min_tenor_months');
            $table->unsignedInteger('max_tenor_months');
            $table->unsignedInteger('min_tenor')->nullable();
            $table->unsignedInteger('max_tenor')->nullable();
            $table->decimal('admin_fee_percentage', 5, 2)->default(1.00); // 1.00%
            $table->unsignedBigInteger('admin_fee_fixed')->default(50000); // Rp 50.000
            $table->decimal('penalty_rate_daily', 5, 4)->default(0.0010); // 0.1% per hari keterlambatan
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_products');
    }
};
