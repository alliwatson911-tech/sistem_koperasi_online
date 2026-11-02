<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('savings_account_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_no', 35)->unique(); // TXS-20261027-0001
            $table->enum('type', ['deposit', 'withdrawal', 'interest', 'fee'])->index();
            $table->unsignedBigInteger('amount'); // Rupiah
            $table->unsignedBigInteger('balance_after'); // Rupiah
            $table->enum('payment_method', ['cash', 'transfer'])->default('cash');
            $table->string('proof_file')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->index();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_transactions');
    }
};
