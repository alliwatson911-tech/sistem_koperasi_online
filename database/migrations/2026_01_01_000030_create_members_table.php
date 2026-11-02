<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('member_no', 30)->unique()->nullable(); // KSP-202610-0001
            $table->string('nik', 20)->unique();
            $table->date('birth_date');
            $table->string('birth_place', 100);
            $table->enum('gender', ['L', 'P']);
            $table->text('address');
            $table->string('occupation', 100);
            $table->unsignedBigInteger('monthly_income')->default(0);
            $table->string('bank_name', 50)->nullable();
            $table->string('bank_account', 50)->nullable();
            $table->enum('status', ['pending', 'verified', 'active', 'suspended', 'resigned'])->default('pending')->index();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('activated_at')->nullable();
            $table->foreignId('activated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
