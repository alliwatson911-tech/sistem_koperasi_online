<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. loan_products: tambahkan tenor_unit, min_tenor, max_tenor, interest_rate_per_period
        if (!Schema::hasColumn('loan_products', 'tenor_unit')) {
            Schema::table('loan_products', function (Blueprint $table) {
                $table->string('tenor_unit', 10)->default('month')->after('calculation_method'); // month | week
                $table->unsignedInteger('min_tenor')->nullable()->after('max_amount');
                $table->unsignedInteger('max_tenor')->nullable()->after('min_tenor');
                $table->decimal('interest_rate_per_period', 5, 2)->nullable()->after('interest_rate_monthly');
            });

            DB::table('loan_products')->update([
                'min_tenor' => DB::raw('min_tenor_months'),
                'max_tenor' => DB::raw('max_tenor_months'),
                'interest_rate_per_period' => DB::raw('interest_rate_monthly'),
            ]);
        }

        // 2. loan_applications: tambahkan tenor_unit, tenor_count, interest_rate_per_period
        if (!Schema::hasColumn('loan_applications', 'tenor_unit')) {
            Schema::table('loan_applications', function (Blueprint $table) {
                $table->string('tenor_unit', 10)->default('month')->after('calculation_method'); // month | week
                $table->unsignedInteger('tenor_count')->nullable()->after('tenor_months');
                $table->decimal('interest_rate_per_period', 5, 2)->nullable()->after('interest_rate_monthly');
            });

            DB::table('loan_applications')->update([
                'tenor_count' => DB::raw('tenor_months'),
                'interest_rate_per_period' => DB::raw('interest_rate_monthly'),
            ]);
        }

        // 3. loans: tambahkan tenor_unit, tenor_count, interest_rate_per_period
        if (!Schema::hasColumn('loans', 'tenor_unit')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->string('tenor_unit', 10)->default('month')->after('calculation_method'); // month | week
                $table->unsignedInteger('tenor_count')->nullable()->after('tenor_months');
                $table->decimal('interest_rate_per_period', 5, 2)->nullable()->after('interest_rate_monthly');
            });

            DB::table('loans')->update([
                'tenor_count' => DB::raw('tenor_months'),
                'interest_rate_per_period' => DB::raw('interest_rate_monthly'),
            ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('loans', 'tenor_unit')) {
            Schema::table('loans', function (Blueprint $table) {
                $table->dropColumn(['tenor_unit', 'tenor_count', 'interest_rate_per_period']);
            });
        }

        if (Schema::hasColumn('loan_applications', 'tenor_unit')) {
            Schema::table('loan_applications', function (Blueprint $table) {
                $table->dropColumn(['tenor_unit', 'tenor_count', 'interest_rate_per_period']);
            });
        }

        if (Schema::hasColumn('loan_products', 'tenor_unit')) {
            Schema::table('loan_products', function (Blueprint $table) {
                $table->dropColumn(['tenor_unit', 'min_tenor', 'max_tenor', 'interest_rate_per_period']);
            });
        }
    }
};

