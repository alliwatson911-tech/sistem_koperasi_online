<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loan_product_id')->constrained()->cascadeOnDelete();
            $table->string('loan_no', 35)->unique(); // LOAN-20261027-0001
            $table->unsignedBigInteger('principal_amount');
            $table->unsignedBigInteger('total_interest');
            $table->unsignedBigInteger('total_admin_fee');
            $table->unsignedBigInteger('total_loan_amount');
            $table->unsignedBigInteger('outstanding_principal');
            $table->unsignedBigInteger('outstanding_interest');
            $table->unsignedBigInteger('outstanding_penalty')->default(0);
            $table->string('tenor_unit', 10)->default('month'); // month | week
            $table->unsignedInteger('tenor_months'); // Tenor bulan (backward compatibility)
            $table->unsignedInteger('tenor_count')->nullable(); // Jumlah periode tenor
            $table->decimal('interest_rate_monthly', 5, 2);
            $table->decimal('interest_rate_per_period', 5, 2)->nullable();
            $table->enum('calculation_method', ['flat', 'effective', 'annuity']);
            $table->enum('status', ['approved_awaiting_disbursement', 'active', 'overdue', 'paid_off', 'written_off'])->default('approved_awaiting_disbursement')->index();
            $table->timestamp('agreed_at')->nullable(); // Waktu akad ditandatangani anggota
            $table->timestamp('disbursed_at')->nullable();
            $table->foreignId('disbursed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('maturity_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
