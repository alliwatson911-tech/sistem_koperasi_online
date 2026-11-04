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
            if (!Schema::hasColumn('loan_applications', 'business_name')) {
                $table->string('business_name', 150)->nullable()->after('purpose');
            }
            if (!Schema::hasColumn('loan_applications', 'business_type')) {
                $table->string('business_type', 100)->nullable()->after('business_name');
            }
            if (!Schema::hasColumn('loan_applications', 'business_address')) {
                $table->text('business_address')->nullable()->after('business_type');
            }
            if (!Schema::hasColumn('loan_applications', 'business_duration_years')) {
                $table->unsignedInteger('business_duration_years')->nullable()->after('business_address');
            }
            if (!Schema::hasColumn('loan_applications', 'business_monthly_revenue')) {
                $table->unsignedBigInteger('business_monthly_revenue')->nullable()->after('business_duration_years');
            }
            if (!Schema::hasColumn('loan_applications', 'business_photo_path')) {
                $table->string('business_photo_path', 255)->nullable()->after('business_monthly_revenue');
            }
            if (!Schema::hasColumn('loan_applications', 'survey_photo_path')) {
                $table->string('survey_photo_path', 255)->nullable()->after('business_photo_path');
            }
            if (!Schema::hasColumn('loan_applications', 'survey_notes')) {
                $table->text('survey_notes')->nullable()->after('survey_photo_path');
            }
        });

        // 2. Tambah kolom Foto Usaha & Status Kolektibilitas pada tabel loans
        Schema::table('loans', function (Blueprint $table) {
            if (!Schema::hasColumn('loans', 'business_name')) {
                $table->string('business_name', 150)->nullable()->after('notes');
            }
            if (!Schema::hasColumn('loans', 'business_type')) {
                $table->string('business_type', 100)->nullable()->after('business_name');
            }
            if (!Schema::hasColumn('loans', 'business_address')) {
                $table->text('business_address')->nullable()->after('business_type');
            }
            if (!Schema::hasColumn('loans', 'business_duration_years')) {
                $table->unsignedInteger('business_duration_years')->nullable()->after('business_address');
            }
            if (!Schema::hasColumn('loans', 'business_monthly_revenue')) {
                $table->unsignedBigInteger('business_monthly_revenue')->nullable()->after('business_duration_years');
            }
            if (!Schema::hasColumn('loans', 'business_photo_path')) {
                $table->string('business_photo_path', 255)->nullable()->after('business_monthly_revenue');
            }
            if (!Schema::hasColumn('loans', 'survey_photo_path')) {
                $table->string('survey_photo_path', 255)->nullable()->after('business_photo_path');
            }

            // Manajemen Kolektibilitas & Penagihan
            if (!Schema::hasColumn('loans', 'collectibility_status')) {
                $table->string('collectibility_status', 30)->default('lancar')->after('status');
            }
            if (!Schema::hasColumn('loans', 'collection_stage')) {
                $table->string('collection_stage', 30)->default('normal')->after('collectibility_status');
            }
            if (!Schema::hasColumn('loans', 'last_collected_at')) {
                $table->dateTime('last_collected_at')->nullable()->after('collection_stage');
            }
            if (!Schema::hasColumn('loans', 'promised_payment_date')) {
                $table->date('promised_payment_date')->nullable()->after('last_collected_at');
            }
            if (!Schema::hasColumn('loans', 'promised_payment_amount')) {
                $table->unsignedBigInteger('promised_payment_amount')->nullable()->after('promised_payment_date');
            }
        });

        // 3. Tabel Riwayat Penagihan & Interaksi Pinjaman Menunggak (Collection Logs)
        if (!Schema::hasTable('loan_collection_logs')) {
            Schema::create('loan_collection_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('action_type', 50);
                $table->dateTime('action_date');
                $table->string('result_status', 50);
                $table->date('promised_date')->nullable();
                $table->unsignedBigInteger('promised_amount')->nullable();
                $table->text('notes');
                $table->string('proof_photo_path', 255)->nullable();
                $table->timestamps();
            });
        }

        // 4. Tabel Permohonan Keringanan / Restrukturisasi / Pembebasan Denda
        if (!Schema::hasTable('loan_restructuring_requests')) {
            Schema::create('loan_restructuring_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
                $table->foreignId('requested_by_user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('request_type', 50)->default('penalty_waiver');
                $table->unsignedBigInteger('requested_waiver_amount')->default(0);
                $table->unsignedBigInteger('approved_waiver_amount')->default(0);
                $table->text('reason');
                $table->text('manager_notes')->nullable();
                $table->string('status', 30)->default('pending');
                $table->dateTime('decided_at')->nullable();
                $table->timestamps();
            });
        }
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

