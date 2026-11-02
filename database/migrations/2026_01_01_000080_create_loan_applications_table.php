<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loan_product_id')->constrained()->cascadeOnDelete();
            $table->string('application_no', 35)->unique(); // APP-20261027-0001
            $table->unsignedBigInteger('amount'); // Nominal pengajuan
            $table->string('tenor_unit', 10)->default('month'); // month | week
            $table->unsignedInteger('tenor_months'); // Tenor bulan (backward compatibility)
            $table->unsignedInteger('tenor_count')->nullable(); // Jumlah periode tenor
            $table->string('purpose'); // Tujuan pinjaman
            $table->enum('calculation_method', ['flat', 'effective', 'annuity']);
            $table->decimal('interest_rate_monthly', 5, 2);
            $table->decimal('interest_rate_per_period', 5, 2)->nullable();
            $table->unsignedBigInteger('admin_fee')->default(0);
            $table->unsignedBigInteger('monthly_installment_estimate')->default(0);
            $table->enum('status', ['draft', 'submitted', 'received', 'needs_revision', 'in_review', 'approved', 'rejected', 'cancelled'])->default('submitted')->index();
            $table->timestamp('submitted_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_applications');
    }
};
