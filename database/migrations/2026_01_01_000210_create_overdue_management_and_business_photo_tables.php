<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom Foto Usaha & Data Usaha pada tabel loan_applications
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->string('business_name', 150)->nullable()->after('purpose');
            $table->string('business_type', 100)->nullable()->after('business_name');
            $table->text('business_address')->nullable()->after('business_type');
            $table->unsignedInteger('business_duration_years')->nullable()->after('business_address');
            $table->unsignedBigInteger('business_monthly_revenue')->nullable()->after('business_duration_years');
            $table->string('business_photo_path', 255)->nullable()->after('business_monthly_revenue');
            $table->string('survey_photo_path', 255)->nullable()->after('business_photo_path');
            $table->text('survey_notes')->nullable()->after('survey_photo_path');
        });

        // 2. Tambah kolom Foto Usaha & Status Kolektibilitas pada tabel loans
        Schema::table('loans', function (Blueprint $table) {
            $table->string('business_name', 150)->nullable()->after('notes');
            $table->string('business_type', 100)->nullable()->after('business_name');
            $table->text('business_address')->nullable()->after('business_type');
            $table->unsignedInteger('business_duration_years')->nullable()->after('business_address');
            $table->unsignedBigInteger('business_monthly_revenue')->nullable()->after('business_duration_years');
            $table->string('business_photo_path', 255)->nullable()->after('business_monthly_revenue');
            $table->string('survey_photo_path', 255)->nullable()->after('business_photo_path');

            // Manajemen Kolektibilitas & Penagihan
            $table->string('collectibility_status', 30)->default('lancar')->after('status'); // lancar, dpk, kurang_lancar, diragukan, macet
            $table->string('collection_stage', 30)->default('normal')->after('collectibility_status'); // normal, reminder, sp1, sp2, sp3, restructured
            $table->dateTime('last_collected_at')->nullable()->after('collection_stage');
            $table->date('promised_payment_date')->nullable()->after('last_collected_at');
            $table->unsignedBigInteger('promised_payment_amount')->nullable()->after('promised_payment_date');
        });

        // 3. Tabel Riwayat Penagihan & Interaksi Pinjaman Menunggak (Collection Logs)
        Schema::create('loan_collection_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Petugas penagih / Analis
            $table->string('action_type', 50); // phone, whatsapp, field_visit, letter_sp1, letter_sp2, letter_sp3, mediation, promise_to_pay
            $table->dateTime('action_date');
            $table->string('result_status', 50); // promise_to_pay, partial_paid, unreachable, uncooperative, dispute, cooperative
            $table->date('promised_date')->nullable();
            $table->unsignedBigInteger('promised_amount')->nullable();
            $table->text('notes');
            $table->string('proof_photo_path', 255)->nullable(); // Foto kunjungan lapangan / bukti tanda terima surat
            $table->timestamps();
        });

        // 4. Tabel Permohonan Keringanan / Restrukturisasi / Pembebasan Denda
        Schema::create('loan_restructuring_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            $table->foreignId('requested_by_user_id')->constrained('users')->onDelete('cascade'); // Anggota atau Analis
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null'); // Manajer
            $table->string('request_type', 50)->default('penalty_waiver'); // penalty_waiver, tenor_extension, payment_reschedule
            $table->unsignedBigInteger('requested_waiver_amount')->default(0);
            $table->unsignedBigInteger('approved_waiver_amount')->default(0);
            $table->text('reason');
            $table->text('manager_notes')->nullable();
            $table->string('status', 30)->default('pending'); // pending, approved, rejected
            $table->dateTime('decided_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_restructuring_requests');
        Schema::dropIfExists('loan_collection_logs');

        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'business_type',
                'business_address',
                'business_duration_years',
                'business_monthly_revenue',
                'business_photo_path',
                'survey_photo_path',
                'collectibility_status',
                'collection_stage',
                'last_collected_at',
                'promised_payment_date',
                'promised_payment_amount',
            ]);
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropColumn([
                'business_name',
                'business_type',
                'business_address',
                'business_duration_years',
                'business_monthly_revenue',
                'business_photo_path',
                'survey_photo_path',
                'survey_notes',
            ]);
        });
    }
};

