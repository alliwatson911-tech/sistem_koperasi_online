<?php

use App\Models\ChartOfAccount;
use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan Akun Bagan Akun (COA) untuk Payment Gateway jika belum ada
        $accounts = [
            [
                'code' => '1301',
                'name' => 'Dana dalam Penyelesaian Gateway',
                'type' => 'asset',
                'normal_balance' => 'debit',
                'description' => 'Kliring penerimaan pembayaran anggota lewat payment gateway (Midtrans/QRIS/VA)',
            ],
            [
                'code' => '5103',
                'name' => 'Biaya Transaksi Payment Gateway',
                'type' => 'expense',
                'normal_balance' => 'debit',
                'description' => 'Beban MDR/biaya administrasi gateway yang dipotong saat pencairan (settlement)',
            ],
        ];

        foreach ($accounts as $acc) {
            ChartOfAccount::updateOrCreate(['code' => $acc['code']], $acc);
        }

        // 2. Modifikasi loan_payments & savings_transactions untuk mendukung relasi gateway_payment
        Schema::table('loan_payments', function (Blueprint $table) {
            $table->string('payment_method', 30)->default('cash')->change();
            $table->unsignedBigInteger('gateway_payment_id')->nullable()->after('verified_by');
        });

        Schema::table('savings_transactions', function (Blueprint $table) {
            $table->string('payment_method', 30)->default('cash')->change();
        });

        // 3. Tabel Rekonsiliasi Settlement Gateway ke Bank Koperasi
        Schema::create('gateway_settlements', function (Blueprint $table) {
            $table->id();
            $table->string('settlement_no', 40)->unique(); // STL-20261029-0001
            $table->string('gateway', 30)->default('fake');
            $table->unsignedInteger('total_transactions')->default(0);
            $table->unsignedBigInteger('gross_amount')->default(0); // Total bruto dana dari anggota (Cr 1301)
            $table->unsignedBigInteger('fee_amount')->default(0);   // Total potongan fee gateway (Dr 5103)
            $table->unsignedBigInteger('net_amount')->default(0);   // Total neto masuk rekening bank (Dr 1102)
            $table->string('bank_account_code', 20)->default('1102');
            $table->foreignId('journal_id')->nullable()->constrained('journals')->nullOnDelete();
            $table->enum('status', ['pending', 'completed'])->default('completed');
            $table->timestamp('settled_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 4. Tabel Transaksi / Tagihan Payment Gateway
        Schema::create('gateway_payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 64)->unique(); // INV-LOAN-20261029-XXXXX / INV-SAV-20261029-XXXXX
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('transaction_type', 30); // 'loan_installment', 'savings_deposit'
            $table->nullableMorphs('payable');      // payable_type: Loan/SavingsAccount, payable_id
            $table->unsignedBigInteger('amount');   // Pokok kewajiban / setoran murni
            $table->unsignedBigInteger('fee_amount')->default(0); // Biaya gateway
            $table->string('fee_bearer', 20)->default('cooperative'); // 'cooperative' atau 'member'
            $table->unsignedBigInteger('gross_amount'); // Total ditagihkan ke anggota
            
            // Porsi alokasi keuangan
            $table->unsignedBigInteger('principal_portion')->default(0);
            $table->unsignedBigInteger('interest_portion')->default(0);
            $table->unsignedBigInteger('penalty_portion')->default(0);
            $table->unsignedBigInteger('overpayment_portion')->default(0);

            // Detail gateway
            $table->string('gateway', 30)->default('fake'); // fake, midtrans, xendit
            $table->string('payment_method_type', 40)->default('qris'); // qris, va_bca, va_bni, va_bri, va_mandiri, gopay, shopeepay
            $table->string('va_number', 64)->nullable();
            $table->text('qr_string')->nullable();
            $table->string('qr_url', 500)->nullable();
            $table->string('payment_url', 500)->nullable();
            
            // Status siklus pembayaran
            $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'cancelled', 'refunded'])->default('pending')->index();
            $table->timestamp('expires_at');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('settled_at')->nullable();

            // Relasi eksternal
            $table->foreignId('settlement_id')->nullable()->constrained('gateway_settlements')->nullOnDelete();
            $table->foreignId('loan_payment_id')->nullable()->constrained('loan_payments')->nullOnDelete();
            $table->foreignId('savings_transaction_id')->nullable()->constrained('savings_transactions')->nullOnDelete();
            $table->foreignId('journal_id')->nullable()->constrained('journals')->nullOnDelete();

            $table->json('selected_schedule_ids')->nullable(); // Id jadwal angsuran spesifik jika dipilih
            $table->json('raw_payload')->nullable();
            $table->json('raw_response')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 5. Tabel Detail Alokasi Pembayaran per Jadwal Angsuran
        Schema::create('payment_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gateway_payment_id')->constrained('gateway_payments')->cascadeOnDelete();
            $table->foreignId('loan_schedule_id')->constrained('loan_schedules')->cascadeOnDelete();
            $table->unsignedBigInteger('principal_allocated')->default(0);
            $table->unsignedBigInteger('interest_allocated')->default(0);
            $table->unsignedBigInteger('penalty_allocated')->default(0);
            $table->unsignedBigInteger('total_allocated')->default(0);
            $table->timestamps();
        });

        // 6. Tabel Webhook Events untuk Idempotensi & Audit Mentah
        Schema::create('payment_webhook_events', function (Blueprint $table) {
            $table->id();
            $table->string('gateway', 30);
            $table->string('event_id', 120); // Unique event key from gateway or hash
            $table->string('order_id', 64)->index();
            $table->string('event_type', 50);
            $table->string('signature', 255)->nullable();
            $table->json('payload');
            $table->enum('status', ['received', 'processed', 'failed', 'ignored'])->default('received');
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->unique(['gateway', 'event_id']);
        });

        // 7. Pengaturan Tambahan Payment Gateway
        $gatewaySettings = [
            ['key' => 'payment_gateway_driver', 'value' => 'fake', 'description' => 'Driver Payment Gateway Aktif (fake/midtrans)', 'type' => 'string'],
            ['key' => 'payment_gateway_mode', 'value' => 'sandbox', 'description' => 'Mode Gateway (sandbox/production)', 'type' => 'string'],
            ['key' => 'payment_gateway_fee_bearer', 'value' => 'cooperative', 'description' => 'Penanggung Biaya Gateway (cooperative/member)', 'type' => 'string'],
            ['key' => 'payment_gateway_expiry_hours', 'value' => '24', 'description' => 'Masa Berlaku Tagihan Gateway (Jam)', 'type' => 'integer'],
            ['key' => 'payment_gateway_qris_fee_percent', 'value' => '0.7', 'description' => 'Perkiraan MDR QRIS (%)', 'type' => 'decimal'],
            ['key' => 'payment_gateway_va_fee_fixed', 'value' => '4000', 'description' => 'Biaya Flat Virtual Account (Rp)', 'type' => 'integer'],
        ];

        foreach ($gatewaySettings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_webhook_events');
        Schema::dropIfExists('payment_allocations');
        Schema::dropIfExists('gateway_payments');
        Schema::dropIfExists('gateway_settlements');

        Schema::table('loan_payments', function (Blueprint $table) {
            $table->dropColumn('gateway_payment_id');
        });
    }
};
