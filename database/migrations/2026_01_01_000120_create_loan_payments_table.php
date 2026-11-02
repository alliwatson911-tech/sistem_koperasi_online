<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->string('payment_no', 35)->unique(); // PAY-20261027-0001
            $table->date('payment_date');
            $table->unsignedBigInteger('amount');
            $table->unsignedBigInteger('principal_portion')->default(0);
            $table->unsignedBigInteger('interest_portion')->default(0);
            $table->unsignedBigInteger('penalty_portion')->default(0);
            $table->enum('payment_method', ['cash', 'transfer'])->default('cash');
            $table->string('proof_file')->nullable();
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('verified')->index();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_payments');
    }
};
