<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('employee_code', 30)->nullable()->unique()->after('role');
        });

        Schema::table('loan_applications', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_by')->nullable()->after('assigned_to')->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable()->after('assigned_by');
            $table->text('assignment_notes')->nullable()->after('assigned_at');

            $table->foreignId('received_by')->nullable()->after('assignment_notes')->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable()->after('received_by');
            $table->json('reception_checklist')->nullable()->after('received_at');
            $table->text('reception_note')->nullable()->after('reception_checklist');
            $table->string('reception_status', 30)->nullable()->after('reception_note'); // complete | needs_revision

            $table->timestamp('revision_requested_at')->nullable()->after('reception_status');
            $table->text('revision_notes')->nullable()->after('revision_requested_at');
        });

        if (Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            Illuminate\Support\Facades\DB::statement("ALTER TABLE loan_applications MODIFY COLUMN status ENUM('draft', 'submitted', 'received', 'needs_revision', 'in_review', 'approved', 'rejected', 'cancelled') DEFAULT 'submitted'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_applications', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropForeign(['assigned_by']);
            $table->dropForeign(['received_by']);
            $table->dropColumn([
                'assigned_to',
                'assigned_by',
                'assigned_at',
                'assignment_notes',
                'received_by',
                'received_at',
                'reception_checklist',
                'reception_note',
                'reception_status',
                'revision_requested_at',
                'revision_notes',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employee_code');
        });
    }
};
