<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('installment_no');
            $table->date('due_date');
            $table->unsignedBigInteger('principal_amount');
            $table->unsignedBigInteger('interest_amount');
            $table->unsignedBigInteger('total_amount');
            $table->unsignedBigInteger('paid_principal')->default(0);
            $table->unsignedBigInteger('paid_interest')->default(0);
            $table->unsignedBigInteger('paid_penalty')->default(0);
            $table->unsignedBigInteger('remaining_principal');
            $table->enum('status', ['unpaid', 'partial', 'paid', 'overdue'])->default('unpaid')->index();
            $table->timestamp('paid_at')->nullable();
            $table->unsignedInteger('days_overdue')->default(0);
            $table->unsignedBigInteger('penalty_amount')->default(0);
            $table->timestamps();

            $table->unique(['loan_id', 'installment_no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_schedules');
    }
};
