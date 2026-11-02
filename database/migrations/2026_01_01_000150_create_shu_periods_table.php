<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shu_periods', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('year')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedBigInteger('total_revenue')->default(0);
            $table->unsignedBigInteger('total_expense')->default(0);
            $table->bigInteger('net_shu')->default(0); // Bisa negatif jika rugi (Signed)
            $table->enum('status', ['open', 'closed'])->default('open')->index();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('shu_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shu_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('member_savings_total')->default(0);
            $table->unsignedBigInteger('member_loan_interest_total')->default(0);
            $table->unsignedBigInteger('savings_portion_amount')->default(0); // Bagian Jasa Modal/Simpanan
            $table->unsignedBigInteger('transaction_portion_amount')->default(0); // Bagian Jasa Usaha/Pinjaman
            $table->unsignedBigInteger('total_shu_amount')->default(0);
            $table->enum('status', ['pending', 'distributed'])->default('pending')->index();
            $table->timestamp('distributed_at')->nullable();
            $table->timestamps();

            $table->unique(['shu_period_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shu_distributions');
        Schema::dropIfExists('shu_periods');
    }
};
