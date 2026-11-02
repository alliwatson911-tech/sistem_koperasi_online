<?php

namespace Tests\Feature;

use App\Contracts\PaymentGatewayInterface;
use App\Models\ChartOfAccount;
use App\Models\GatewayPayment;
use App\Models\GatewaySettlement;
use App\Models\Journal;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\LoanProduct;
use App\Models\LoanSchedule;
use App\Models\Member;
use App\Models\PaymentWebhookEvent;
use App\Models\SavingsAccount;
use App\Models\User;
use App\Services\GatewayPaymentService;
use App\Services\PaymentGateway\FakePaymentGateway;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PaymentGatewayTest extends TestCase
{
    use RefreshDatabase;

    protected GatewayPaymentService $paymentService;
    protected FakePaymentGateway $fakeGateway;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->paymentService = app(GatewayPaymentService::class);
        $this->fakeGateway = app(FakePaymentGateway::class);
    }

    /**
     * CONTOH BAKU FASE 3:
     * Tunggakan satu angsuran terdiri dari denda Rp5.000, bunga Rp20.000, pokok Rp491.066;
     * Anggota membayar Rp300.000 lewat gateway;
     * Hasilnya: denda Rp5.000 lunas, bunga Rp20.000 lunas, pokok Rp275.000 terbayar, dan angsuran berstatus partial.
     */
    public function test_payment_allocation_waterfall_matches_standard_benchmark()
    {
        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $product = LoanProduct::first();

        // 1. Buat pinjaman dummy aktif
        $loan = Loan::create([
            'member_id' => $member->id,
            'loan_product_id' => $product->id,
            'loan_no' => 'LOAN-TEST-GW-BENCHMARK',
            'principal_amount' => 5000000,
            'total_interest' => 110660,
            'total_admin_fee' => 0,
            'total_loan_amount' => 5110660,
            'outstanding_principal' => 5000000,
            'outstanding_interest' => 110660,
            'outstanding_penalty' => 5000,
            'tenor_months' => 10,
            'tenor_unit' => 'week',
            'interest_rate_monthly' => 0.4,
            'calculation_method' => 'annuity',
            'status' => Loan::STATUS_ACTIVE,
            'disbursed_at' => now(),
            'maturity_date' => now()->addWeeks(10),
        ]);

        // Buat jadwal angsuran pertama sesuai contoh baku:
        // Pokok Rp 491.066, Bunga Rp 20.000, Denda Rp 5.000
        $schedule = LoanSchedule::create([
            'loan_id' => $loan->id,
            'installment_no' => 1,
            'due_date' => now()->subDays(5),
            'principal_amount' => 491066,
            'interest_amount' => 20000,
            'total_amount' => 511066,
            'paid_principal' => 0,
            'paid_interest' => 0,
            'paid_penalty' => 0,
            'remaining_principal' => 4508934,
            'status' => LoanSchedule::STATUS_OVERDUE,
            'days_overdue' => 5,
            'penalty_amount' => 5000,
        ]);

        // 2. Buat tagihan gateway invoice sebesar Rp 300.000
        $invoice = $this->paymentService->createLoanInstallmentInvoice(
            $loan,
            300000,
            'qris',
            [$schedule->id],
            'Pembayaran contoh baku gateway'
        );

        $this->assertEquals(GatewayPayment::STATUS_PENDING, $invoice->status);
        $this->assertEquals(300000, $invoice->amount);
        $this->assertNotEmpty($invoice->qr_string);

        // 3. Simulasikan webhook sukses dari Payment Gateway
        $signature = 'valid-fake-signature';
        $webhookPayload = [
            'order_id' => $invoice->order_id,
            'transaction_status' => 'settlement',
            'event_id' => 'ev_' . $invoice->order_id,
            'gross_amount' => $invoice->gross_amount,
            'signature_key' => $signature,
        ];

        $response = $this->paymentService->processWebhook('fake', $webhookPayload, $signature);
        $this->assertTrue($response['success']);

        // 4. Verifikasi jadwal setelah alokasi waterfall:
        $schedule->refresh();
        $invoice->refresh();

        // Denda Rp 5.000 lunas
        $this->assertEquals(5000, $schedule->paid_penalty, 'Denda Rp 5.000 harus lunas');
        // Bunga Rp 20.000 lunas
        $this->assertEquals(20000, $schedule->paid_interest, 'Bunga Rp 20.000 harus lunas');
        // Pokok Rp 275.000 terbayar (300.000 - 5.000 - 20.000 = 275.000)
        $this->assertEquals(275000, $schedule->paid_principal, 'Pokok yang terbayar harus Rp 275.000');
        // Sisa pokok angsuran Rp 216.066 (491.066 - 275.000)
        $this->assertEquals(216066, $schedule->principal_amount - $schedule->paid_principal);
        // Status jadwal partial
        $this->assertEquals(LoanSchedule::STATUS_PARTIAL, $schedule->status);

        // Verifikasi invoice & loan payment
        $this->assertEquals(GatewayPayment::STATUS_PAID, $invoice->status);
        $this->assertEquals(275000, $invoice->principal_portion);
        $this->assertEquals(20000, $invoice->interest_portion);
        $this->assertEquals(5000, $invoice->penalty_portion);
        $this->assertNotNull($invoice->loan_payment_id);

        // Verifikasi Jurnal Double-Entry:
        // Dr: 1301 (Dana dalam Penyelesaian) Rp 300.000
        // Cr: 1201 (Piutang Pokok) Rp 275.000
        // Cr: 4101 (Pendapatan Bunga) Rp 20.000
        // Cr: 4103 (Pendapatan Denda) Rp 5.000
        $journal = $invoice->journal;
        $this->assertNotNull($journal);
        $this->assertEquals(300000, $journal->total_amount);

        $entries = $journal->entries()->with('account')->get();
        $debit1301 = $entries->first(fn ($e) => $e->account->code === '1301' && $e->type === 'debit');
        $credit1201 = $entries->first(fn ($e) => $e->account->code === '1201' && $e->type === 'credit');
        $credit4101 = $entries->first(fn ($e) => $e->account->code === '4101' && $e->type === 'credit');
        $credit4103 = $entries->first(fn ($e) => $e->account->code === '4103' && $e->type === 'credit');

        $this->assertNotNull($debit1301, 'Jurnal wajib mencatat Dr 1301');
        $this->assertEquals(300000, $debit1301->amount);
        $this->assertNotNull($credit1201, 'Jurnal wajib mencatat Cr 1201');
        $this->assertEquals(275000, $credit1201->amount);
        $this->assertNotNull($credit4101, 'Jurnal wajib mencatat Cr 4101');
        $this->assertEquals(20000, $credit4101->amount);
        $this->assertNotNull($credit4103, 'Jurnal wajib mencatat Cr 4103');
        $this->assertEquals(5000, $credit4103->amount);
    }

    /**
     * Uji pembayaran multi-angsuran: pembayaran meluncur dari jadwal 1 ke jadwal 2 secara berurutan.
     */
    public function test_payment_allocation_cascades_across_multiple_schedules()
    {
        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $product = LoanProduct::first();

        $loan = Loan::create([
            'member_id' => $member->id,
            'loan_product_id' => $product->id,
            'loan_no' => 'LOAN-TEST-CASCADE',
            'principal_amount' => 5000000,
            'total_interest' => 100000,
            'total_admin_fee' => 0,
            'total_loan_amount' => 5100000,
            'outstanding_principal' => 5000000,
            'outstanding_interest' => 100000,
            'outstanding_penalty' => 0,
            'tenor_months' => 2,
            'interest_rate_monthly' => 1.0,
            'calculation_method' => 'flat',
            'status' => Loan::STATUS_ACTIVE,
            'disbursed_at' => now(),
            'maturity_date' => now()->addMonths(2),
        ]);

        // Schedule 1: Pokok 200.000, Bunga 50.000 (Total 250.000)
        $sch1 = LoanSchedule::create([
            'loan_id' => $loan->id,
            'installment_no' => 1,
            'due_date' => now()->subDays(10),
            'principal_amount' => 200000,
            'interest_amount' => 50000,
            'total_amount' => 250000,
            'paid_principal' => 0,
            'paid_interest' => 0,
            'paid_penalty' => 0,
            'remaining_principal' => 4800000,
            'status' => LoanSchedule::STATUS_OVERDUE,
            'days_overdue' => 10,
            'penalty_amount' => 0,
        ]);

        // Schedule 2: Pokok 200.000, Bunga 50.000 (Total 250.000)
        $sch2 = LoanSchedule::create([
            'loan_id' => $loan->id,
            'installment_no' => 2,
            'due_date' => now()->addDays(20),
            'principal_amount' => 200000,
            'interest_amount' => 50000,
            'total_amount' => 250000,
            'paid_principal' => 0,
            'paid_interest' => 0,
            'paid_penalty' => 0,
            'remaining_principal' => 4600000,
            'status' => LoanSchedule::STATUS_UNPAID,
            'days_overdue' => 0,
            'penalty_amount' => 0,
        ]);

        // Anggota bayar Rp 350.000 (menutup penuh sch1 Rp 250.000, sisa Rp 100.000 masuk ke sch2: Bunga 50.000, Pokok 50.000)
        $invoice = $this->paymentService->createLoanInstallmentInvoice($loan, 350000, 'va_bca');
        $this->assertStringStartsWith('88001', $invoice->va_number);

        $this->paymentService->processWebhook('fake', [
            'order_id' => $invoice->order_id,
            'transaction_status' => 'settlement',
            'event_id' => 'ev_' . $invoice->order_id,
            'gross_amount' => $invoice->gross_amount,
            'signature_key' => 'valid-fake-signature',
        ], 'valid-fake-signature');

        $sch1->refresh();
        $sch2->refresh();

        $this->assertEquals(LoanSchedule::STATUS_PAID, $sch1->status, 'Jadwal 1 harus lunas');
        $this->assertEquals(200000, $sch1->paid_principal);
        $this->assertEquals(50000, $sch1->paid_interest);

        $this->assertEquals(LoanSchedule::STATUS_PARTIAL, $sch2->status, 'Jadwal 2 harus partial');
        $this->assertEquals(50000, $sch2->paid_interest, 'Bunga jadwal 2 harus lunas');
        $this->assertEquals(50000, $sch2->paid_principal, 'Pokok jadwal 2 terbayar 50.000');
    }

    /**
     * Uji Kelebihan Bayar (Overpayment):
     * Bila anggota membayar melebihi total seluruh kewajiban pinjaman,
     * sisa kelebihan otomatis dikreditkan ke Simpanan Sukarela anggota (Cr 2103).
     */
    public function test_overpayment_is_credited_to_voluntary_savings()
    {
        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $product = LoanProduct::first();
        $sukarelaAcc = $member->getSimpananSukarelaAccount();
        $initialSukarelaBal = $sukarelaAcc->balance;

        $loan = Loan::create([
            'member_id' => $member->id,
            'loan_product_id' => $product->id,
            'loan_no' => 'LOAN-TEST-OVERPAY',
            'principal_amount' => 400000,
            'total_interest' => 20000,
            'total_admin_fee' => 0,
            'total_loan_amount' => 420000,
            'outstanding_principal' => 400000,
            'outstanding_interest' => 20000,
            'outstanding_penalty' => 0,
            'tenor_months' => 1,
            'interest_rate_monthly' => 1.0,
            'calculation_method' => 'flat',
            'status' => Loan::STATUS_ACTIVE,
            'disbursed_at' => now(),
            'maturity_date' => now()->addMonth(),
        ]);

        LoanSchedule::create([
            'loan_id' => $loan->id,
            'installment_no' => 1,
            'due_date' => now()->addDays(5),
            'principal_amount' => 400000,
            'interest_amount' => 20000,
            'total_amount' => 420000,
            'paid_principal' => 0,
            'paid_interest' => 0,
            'paid_penalty' => 0,
            'remaining_principal' => 0,
            'status' => LoanSchedule::STATUS_UNPAID,
            'penalty_amount' => 0,
        ]);

        // Total kewajiban adalah Rp 420.000, tetapi anggota membayar Rp 500.000 (Kelebihan Rp 80.000)
        $invoice = $this->paymentService->createLoanInstallmentInvoice($loan, 500000, 'qris');

        $this->paymentService->processWebhook('fake', [
            'order_id' => $invoice->order_id,
            'transaction_status' => 'settlement',
            'event_id' => 'ev_' . $invoice->order_id,
            'gross_amount' => $invoice->gross_amount,
            'signature_key' => 'valid-fake-signature',
        ], 'valid-fake-signature');

        $loan->refresh();
        $sukarelaAcc->refresh();
        $invoice->refresh();

        // Pinjaman lunas penuh
        $this->assertEquals(Loan::STATUS_PAID_OFF, $loan->status);
        $this->assertEquals(0, $loan->outstanding_principal);
        $this->assertEquals(0, $loan->outstanding_interest);

        // Kelebihan bayar Rp 80.000 masuk ke saldo Simpanan Sukarela
        $this->assertEquals(80000, $invoice->overpayment_portion);
        $this->assertEquals($initialSukarelaBal + 80000, $sukarelaAcc->balance);

        // Jurnal seimbang dan mencatat Cr 2103 sebesar Rp 80.000
        $journal = $invoice->journal;
        $this->assertEquals(500000, $journal->total_amount);
        $cr2103 = $journal->entries()->whereHas('account', fn ($q) => $q->where('code', '2103'))->first();
        $this->assertNotNull($cr2103);
        $this->assertEquals(80000, $cr2103->amount);
    }

    /**
     * Uji Setoran Simpanan Sukarela via Gateway:
     * Menambah saldo simpanan anggota dan mencatat Dr 1301, Cr 2103.
     */
    public function test_savings_deposit_via_gateway()
    {
        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $sukarelaAcc = $member->getSimpananSukarelaAccount();
        $initialBalance = $sukarelaAcc->balance;

        $invoice = $this->paymentService->createSavingsDepositInvoice($sukarelaAcc, 250000, 'va_bni');
        $this->assertEquals(GatewayPayment::TYPE_SAVINGS_DEPOSIT, $invoice->transaction_type);
        $this->assertStringStartsWith('88002', $invoice->va_number);

        $this->paymentService->processWebhook('fake', [
            'order_id' => $invoice->order_id,
            'transaction_status' => 'settlement',
            'event_id' => 'ev_sav_' . $invoice->order_id,
            'gross_amount' => $invoice->gross_amount,
            'signature_key' => 'valid-fake-signature',
        ], 'valid-fake-signature');

        $sukarelaAcc->refresh();
        $invoice->refresh();

        $this->assertEquals(GatewayPayment::STATUS_PAID, $invoice->status);
        $this->assertEquals($initialBalance + 250000, $sukarelaAcc->balance);

        // Verifikasi jurnal: Dr 1301, Cr 2103
        $journal = $invoice->journal;
        $this->assertNotNull($journal);
        $debit1301 = $journal->entries()->where('type', 'debit')->first();
        $credit2103 = $journal->entries()->where('type', 'credit')->first();

        $this->assertEquals('1301', $debit1301->account->code);
        $this->assertEquals(250000, $debit1301->amount);
        $this->assertEquals('2103', $credit2103->account->code);
        $this->assertEquals(250000, $credit2103->amount);
    }

    /**
     * Uji Keamanan Webhook:
     * - Penolakan signature tidak valid
     * - Penerimaan signature yang sah
     */
    public function test_webhook_signature_verification_and_rejection()
    {
        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $sukarelaAcc = $member->getSimpananSukarelaAccount();
        $invoice = $this->paymentService->createSavingsDepositInvoice($sukarelaAcc, 100000, 'qris');

        // Signature palsu harus memicu exception
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Tanda tangan (signature) webhook tidak valid.');

        $this->paymentService->processWebhook('fake', [
            'order_id' => $invoice->order_id,
            'transaction_status' => 'settlement',
            'event_id' => 'ev_invalid_sig',
            'gross_amount' => $invoice->gross_amount,
            'signature_key' => 'invalid-signature-for-test',
        ], 'invalid-signature-for-test');
    }

    /**
     * Uji Idempotensi Webhook:
     * Pengiriman ulang webhook yang sama persis tidak boleh menduplikasi saldo atau jurnal.
     */
    public function test_webhook_idempotency_prevents_duplicate_processing()
    {
        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $sukarelaAcc = $member->getSimpananSukarelaAccount();
        $invoice = $this->paymentService->createSavingsDepositInvoice($sukarelaAcc, 150000, 'qris');

        $payload = [
            'order_id' => $invoice->order_id,
            'transaction_status' => 'settlement',
            'event_id' => 'ev_idempotent_test_' . $invoice->order_id,
            'gross_amount' => $invoice->gross_amount,
            'signature_key' => 'valid-fake-signature',
        ];

        // Webhook pertama kali
        $res1 = $this->paymentService->processWebhook('fake', $payload, 'valid-fake-signature');
        $this->assertEquals('paid', $res1['status']);

        $sukarelaAcc->refresh();
        $balanceAfterFirst = $sukarelaAcc->balance;
        $journalCountFirst = Journal::count();

        // Webhook kedua kali (duplikat persis)
        $res2 = $this->paymentService->processWebhook('fake', $payload, 'valid-fake-signature');
        $this->assertTrue($res2['success']);
        $this->assertContains($res2['status'], ['already_processed', 'already_paid']);

        // Saldo dan jurnal tidak boleh bertambah dua kali
        $sukarelaAcc->refresh();
        $this->assertEquals($balanceAfterFirst, $sukarelaAcc->balance);
        $this->assertEquals($journalCountFirst, Journal::count());
    }

    /**
     * Uji Scheduler Kedaluwarsa Tagihan:
     * Perintah artisan koperasi:expire-gateway-invoices mengubah status pending yang lewat batas waktu menjadi expired.
     */
    public function test_expired_invoice_scheduler()
    {
        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $sukarelaAcc = $member->getSimpananSukarelaAccount();

        // Buat invoice yang masa berlakunya kemarin
        $invoice = $this->paymentService->createSavingsDepositInvoice($sukarelaAcc, 50000, 'qris');
        $invoice->expires_at = now()->subHours(2);
        $invoice->save();

        $this->assertEquals(GatewayPayment::STATUS_PENDING, $invoice->status);

        // Jalankan perintah artisan
        Artisan::call('koperasi:expire-gateway-invoices');

        $invoice->refresh();
        $this->assertEquals(GatewayPayment::STATUS_EXPIRED, $invoice->status);
    }

    /**
     * Uji Rekonsiliasi Settlement ke Bank Koperasi:
     * Saat dana dicairkan dari gateway ke rekening Bank Koperasi:
     * Dr: 1102 (Bank) [Neto]
     * Dr: 5103 (Biaya Gateway) [Fee]
     * Cr: 1301 (Dana dalam Penyelesaian) [Bruto]
     */
    public function test_settlement_reconciliation_records_proper_double_entry_journal()
    {
        $admin = User::where('role', 'admin')->first();
        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $sukarelaAcc = $member->getSimpananSukarelaAccount();

        // Buat 2 pembayaran lunas via gateway
        $pay1 = $this->paymentService->createSavingsDepositInvoice($sukarelaAcc, 500000, 'va_bca');
        $this->paymentService->processWebhook('fake', [
            'order_id' => $pay1->order_id,
            'transaction_status' => 'settlement',
            'event_id' => 'ev_' . $pay1->order_id,
            'gross_amount' => $pay1->gross_amount,
            'signature_key' => 'valid-fake-signature',
        ], 'valid-fake-signature');

        $pay2 = $this->paymentService->createSavingsDepositInvoice($sukarelaAcc, 500000, 'va_bni');
        $this->paymentService->processWebhook('fake', [
            'order_id' => $pay2->order_id,
            'transaction_status' => 'settlement',
            'event_id' => 'ev_' . $pay2->order_id,
            'gross_amount' => $pay2->gross_amount,
            'signature_key' => 'valid-fake-signature',
        ], 'valid-fake-signature');

        $pay1->refresh();
        $pay2->refresh();

        $totalGross = $pay1->amount + $pay2->amount; // Rp 1.000.000
        $totalFee = $pay1->fee_amount + $pay2->fee_amount; // misal 4.000 + 4.000 = 8.000
        $expectedNet = $totalGross - $totalFee; // Rp 992.000

        // Eksekusi settlement
        $settlement = $this->paymentService->createSettlement(
            [$pay1->id, $pay2->id],
            '1102',
            'Settlement uji coba ke Bank',
            $admin
        );

        $this->assertNotNull($settlement);
        $this->assertEquals($totalGross, $settlement->gross_amount);
        $this->assertEquals($totalFee, $settlement->fee_amount);
        $this->assertEquals($expectedNet, $settlement->net_amount);

        // Verifikasi Jurnal Settlement:
        $journal = $settlement->journal;
        $this->assertNotNull($journal);
        $this->assertEquals($totalGross, $journal->total_amount);

        $entries = $journal->entries()->with('account')->get();
        $drBank = $entries->first(fn ($e) => $e->account->code === '1102' && $e->type === 'debit');
        $drFee = $entries->first(fn ($e) => $e->account->code === '5103' && $e->type === 'debit');
        $crKliring = $entries->first(fn ($e) => $e->account->code === '1301' && $e->type === 'credit');

        $this->assertNotNull($drBank, 'Jurnal settlement wajib Dr 1102');
        $this->assertEquals($expectedNet, $drBank->amount);
        $this->assertNotNull($drFee, 'Jurnal settlement wajib Dr 5103');
        $this->assertEquals($totalFee, $drFee->amount);
        $this->assertNotNull($crKliring, 'Jurnal settlement wajib Cr 1301');
        $this->assertEquals($totalGross, $crKliring->amount);

        // Verifikasi relasi
        $pay1->refresh();
        $pay2->refresh();
        $this->assertEquals($settlement->id, $pay1->settlement_id);
        $this->assertEquals($settlement->id, $pay2->settlement_id);
    }

    /**
     * Uji Perintah Rekonsiliasi Transaksi Pending (koperasi:reconcile-gateway):
     * Memeriksa status transaksi pending ke remote API gateway dan mensinkronisasikan secara otomatis.
     */
    public function test_pending_reconciliation_command()
    {
        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $sukarelaAcc = $member->getSimpananSukarelaAccount();

        $invoice = $this->paymentService->createSavingsDepositInvoice($sukarelaAcc, 200000, 'qris');
        $this->assertEquals(GatewayPayment::STATUS_PENDING, $invoice->status);

        // Simulasikan status di gateway telah berubah menjadi settlement
        FakePaymentGateway::setMockStatus($invoice->order_id, GatewayPayment::STATUS_PAID);

        // Jalankan artisan command rekonsiliasi
        Artisan::call('koperasi:reconcile-gateway');

        $invoice->refresh();
        $this->assertEquals(GatewayPayment::STATUS_PAID, $invoice->status);

        FakePaymentGateway::clearMocks();
    }

    /**
     * Uji Endpoint Member: Checkout, Halaman Invoice, dan Status Polling.
     */
    public function test_member_checkout_and_invoice_routes()
    {
        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $user = $member->user;
        $sukarelaAcc = $member->getSimpananSukarelaAccount();

        // 1. Checkout Setoran Simpanan via Gateway
        $response = $this->actingAs($user)->post(route('savings.deposit.gateway', $sukarelaAcc->id), [
            'amount' => 150000,
            'payment_method_type' => 'va_bca',
            'notes' => 'Uji setoran via web',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('gateway_payments', [
            'member_id' => $member->id,
            'amount' => 150000,
            'payment_method_type' => 'va_bca',
        ]);

        $payment = GatewayPayment::where('member_id', $member->id)->latest('id')->first();

        // 2. Akses Halaman Invoice
        $invoiceRes = $this->actingAs($user)->get(route('payments.invoice', $payment->order_id));
        $invoiceRes->assertOk();
        $invoiceRes->assertSee($payment->order_id);
        $invoiceRes->assertSee($payment->va_number);

        // 3. API Status Polling
        $statusRes = $this->actingAs($user)->getJson(route('payments.invoice.status', $payment->order_id));
        $statusRes->assertOk();
        $statusRes->assertJson([
            'order_id' => $payment->order_id,
            'status' => 'pending',
            'is_paid' => false,
        ]);
    }

    /**
     * Uji Dasbor Petugas & Ekspor CSV:
     * Petugas/Admin dapat memfilter, mengekspor, dan melakukan sinkronisasi manual.
     */
    public function test_staff_dashboard_and_export_and_reconciliation()
    {
        $admin = User::where('role', 'admin')->first();
        $member = Member::where('status', Member::STATUS_ACTIVE)->first();
        $sukarelaAcc = $member->getSimpananSukarelaAccount();

        $invoice = $this->paymentService->createSavingsDepositInvoice($sukarelaAcc, 300000, 'qris');

        // 1. Akses Dasbor Pembayaran oleh Admin
        $res = $this->actingAs($admin)->get(route('payments.dashboard'));
        $res->assertOk();
        $res->assertSee($invoice->order_id);
        $res->assertSee('Manajemen Payment Gateway');

        // 2. Ekspor CSV
        $exportRes = $this->actingAs($admin)->get(route('payments.export'));
        $exportRes->assertOk();
        $this->assertStringContainsString('text/csv', $exportRes->headers->get('Content-Type'));

        // 3. Manual Reconcile oleh Admin
        FakePaymentGateway::setMockStatus($invoice->order_id, GatewayPayment::STATUS_PAID);

        $recRes = $this->actingAs($admin)->post(route('payments.reconcile', $invoice->id));
        $recRes->assertRedirect();

        $invoice->refresh();
        $this->assertEquals(GatewayPayment::STATUS_PAID, $invoice->status);

        FakePaymentGateway::clearMocks();
    }
}
