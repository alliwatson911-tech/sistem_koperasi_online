<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->string('journal_no', 35)->unique(); // JRN-20261027-0001
            $table->date('date');
            $table->string('reference_type', 50)->nullable(); // savings_transaction, loan_disbursement, loan_payment, shu_distribution, manual
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description');
            $table->unsignedBigInteger('total_amount'); // Total debit (harus sama dengan total credit)
            $table->enum('status', ['posted', 'reversed'])->default('posted')->index();
            $table->foreignId('reversed_by_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->enum('type', ['debit', 'credit']);
            $table->unsignedBigInteger('amount');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('journals');
    }
};
