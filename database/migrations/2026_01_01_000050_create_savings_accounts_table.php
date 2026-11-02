<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('savings_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('account_no', 30)->unique(); // SAV-POK-0001
            $table->enum('type', ['pokok', 'wajib', 'sukarela'])->index();
            $table->unsignedBigInteger('balance')->default(0); // dalam Rupiah
            $table->enum('status', ['active', 'closed'])->default('active');
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('savings_accounts');
    }
};
