<?php

namespace Tests\Feature;

use App\Models\GatewayPayment;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\User;
use App\Services\SavingsService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityAndAuthorizationAuditTest extends TestCase
{
    use RefreshDatabase;

    protected User $memberUserA;
    protected Member $memberA;
    protected User $memberUserB;
    protected Member $memberB;
    protected User $tellerUser;
    protected User $analystUser;
    protected User $managerUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->memberUserA = User::where('role', User::ROLE_MEMBER)->first();
        $this->memberA = $this->memberUserA->member;

        // Buat Member B
        $this->memberUserB = User::factory()->create([
            'role' => User::ROLE_MEMBER,
            'name' => 'Anggota Korban IDOR',
            'email' => 'memberb@koperasi.id',
            'phone' => '081299998888',
        ]);
        $this->memberB = Member::create([
            'user_id' => $this->memberUserB->id,
            'member_no' => 'ANG-AUDIT-B',
            'nik' => '7371010101990002',
            'birth_date' => '1992-05-10',
            'birth_place' => 'Makassar',
            'gender' => 'L',
            'address' => 'Jl. Somba Opu No. 12',
            'occupation' => 'Wiraswasta',
            'monthly_income' => 7500000,
            'status' => Member::STATUS_ACTIVE,
        ]);
        app(SavingsService::class)->createDefaultAccounts($this->memberB);

        $this->tellerUser = User::where('role', User::ROLE_TELLER)->first();
        $this->analystUser = User::where('role', User::ROLE_ANALYST)->first();
        $this->managerUser = User::where('role', User::ROLE_MANAGER)->first();
        $this->adminUser = User::where('role', User::ROLE_ADMIN)->first();
    }

    /**
     * Uji 1: Header Keamanan HTTP wajib ada di seluruh response.
     */
    public function test_security_headers_are_present_on_http_responses()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
    }

    /**
     * Uji 2: Proteksi IDOR - Member B tidak boleh melihat tagihan/invoice Member A.
     */
    public function test_idor_member_cannot_view_or_poll_another_members_invoice()
    {
        $savingsA = $this->memberA->savingsAccounts->first();

        $payment = GatewayPayment::create([
            'order_id' => 'KSP-GW-AUDIT-001',
            'member_id' => $this->memberA->id,
            'transaction_type' => GatewayPayment::TYPE_SAVINGS_DEPOSIT,
            'payable_type' => SavingsAccount::class,
            'payable_id' => $savingsA->id,
            'amount' => 50000,
            'fee_amount' => 1000,
            'gross_amount' => 51000,
            'gateway' => 'fake',
            'status' => GatewayPayment::STATUS_PENDING,
            'payment_method_type' => 'qris',
            'payment_url' => 'http://localhost/pay/AUDIT001',
            'expires_at' => now()->addDay(),
        ]);

        // Member B mencoba membuka invoice Member A -> Harus 403
        $response = $this->actingAs($this->memberUserB)->get(route('payments.invoice', $payment->order_id));
        $response->assertStatus(403);

        // Member B mencoba polling status invoice Member A -> Harus 403
        $responseStatus = $this->actingAs($this->memberUserB)->get(route('payments.invoice.status', $payment->order_id));
        $responseStatus->assertStatus(403);

        // Member B mencoba mencetak kuitansi Member A -> Harus 403
        $responseReceipt = $this->actingAs($this->memberUserB)->get(route('payments.receipt', $payment->order_id));
        $responseReceipt->assertStatus(403);

        // Member A sendiri BISA mengakses tagihannya
        $responseOwner = $this->actingAs($this->memberUserA)->get(route('payments.invoice', $payment->order_id));
        $responseOwner->assertStatus(200);

        // Staff (teller/admin) BISA mengakses untuk keperluan operasional
        $responseStaff = $this->actingAs($this->tellerUser)->get(route('payments.invoice', $payment->order_id));
        $responseStaff->assertStatus(200);
    }

    /**
     * Uji 3: Proteksi IDOR - Member B tidak boleh melihat rincian pinjaman atau simpanan Member A.
     */
    public function test_idor_member_cannot_view_another_members_loan_or_savings()
    {
        $product = LoanProduct::first();
        $loanA = Loan::create([
            'member_id' => $this->memberA->id,
            'loan_product_id' => $product->id,
            'loan_no' => 'LOAN-AUDIT-IDOR-A',
            'principal_amount' => 10000000,
            'total_interest' => 1200000,
            'total_admin_fee' => 50000,
            'total_loan_amount' => 11250000,
            'outstanding_principal' => 10000000,
            'outstanding_interest' => 1200000,
            'outstanding_penalty' => 0,
            'tenor_months' => 12,
            'tenor_unit' => 'month',
            'interest_rate_monthly' => 1.0,
            'calculation_method' => 'flat',
            'status' => Loan::STATUS_ACTIVE,
            'disbursed_at' => now(),
            'maturity_date' => now()->addMonths(12),
        ]);

        $savingsA = $this->memberA->savingsAccounts->first();

        // Member B mencoba mengakses rincian pinjaman Member A -> Harus 403
        $respLoan = $this->actingAs($this->memberUserB)->get(route('loans.show', $loanA->id));
        $respLoan->assertStatus(403);

        // Member B mencoba mengakses rekening simpanan Member A -> Harus 403
        $respSavings = $this->actingAs($this->memberUserB)->get(route('savings.show', $savingsA->id));
        $respSavings->assertStatus(403);
    }

    /**
     * Uji 4: Maker-Checker & Segregation of Duties - Penandatanganan Akad Pinjaman.
     */
    public function test_maker_checker_member_cannot_sign_akad_for_another_member()
    {
        $product = LoanProduct::first();
        $loanA = Loan::create([
            'member_id' => $this->memberA->id,
            'loan_product_id' => $product->id,
            'loan_no' => 'LOAN-AUDIT-AKAD-A',
            'principal_amount' => 5000000,
            'total_interest' => 600000,
            'total_admin_fee' => 25000,
            'total_loan_amount' => 5625000,
            'outstanding_principal' => 5000000,
            'outstanding_interest' => 600000,
            'outstanding_penalty' => 0,
            'tenor_months' => 6,
            'tenor_unit' => 'month',
            'interest_rate_monthly' => 1.0,
            'calculation_method' => 'flat',
            'status' => Loan::STATUS_AWAITING_DISBURSEMENT,
            'maturity_date' => now()->addMonths(6),
        ]);

        // Member B mencoba menandatangani akad pinjaman Member A -> Harus 403
        $response = $this->actingAs($this->memberUserB)->post(route('loans.sign_akad', $loanA->id), [
            'agree_terms' => '1',
            'signature' => 'data:image/png;base64,dummy',
        ]);
        $response->assertStatus(403);

        // Status pinjaman tidak boleh berubah
        $this->assertEquals(Loan::STATUS_AWAITING_DISBURSEMENT, $loanA->fresh()->status);
    }

    /**
     * Uji 5: Proteksi IDOR & Pemisahan Tugas pada Simpanan - Member B tidak boleh setor/tarik rekening Member A.
     */
    public function test_savings_deposit_and_withdrawal_protected_against_idor_and_unauthorized_staff()
    {
        $savingsA = $this->memberA->savingsAccounts->where('type', 'sukarela')->first();

        // Member B mencoba setor ke rekening Member A -> Harus 403 (IDOR)
        $respDepositIdor = $this->actingAs($this->memberUserB)->post(route('savings.deposit.submit', $savingsA->id), [
            'amount' => 100000,
            'payment_method' => 'cash',
            'notes' => 'Coba setor rekening orang lain',
        ]);
        $respDepositIdor->assertStatus(403);

        // Member B mencoba tarik dari rekening Member A -> Harus 403 (IDOR)
        $respWithdrawIdor = $this->actingAs($this->memberUserB)->post(route('savings.withdraw.submit', $savingsA->id), [
            'amount' => 50000,
            'notes' => 'Coba bobol tabungan orang lain',
        ]);
        $respWithdrawIdor->assertStatus(403);

        // Analis kredit (bukan teller/admin) mencoba mengeksekusi setoran kas -> Harus 403
        $respAnalyst = $this->actingAs($this->analystUser)->post(route('savings.deposit.submit', $savingsA->id), [
            'amount' => 100000,
            'payment_method' => 'cash',
        ]);
        $respAnalyst->assertStatus(403);
    }

    /**
     * Uji 6: Rekonsiliasi & Settlement dibatasi hanya untuk staf berwenang.
     */
    public function test_reconciliation_and_settlement_restricted_to_authorized_roles()
    {
        $savingsA = $this->memberA->savingsAccounts->first();

        $payment = GatewayPayment::create([
            'order_id' => 'KSP-GW-AUDIT-002',
            'member_id' => $this->memberA->id,
            'transaction_type' => GatewayPayment::TYPE_SAVINGS_DEPOSIT,
            'payable_type' => SavingsAccount::class,
            'payable_id' => $savingsA->id,
            'amount' => 100000,
            'fee_amount' => 1500,
            'gross_amount' => 101500,
            'gateway' => 'fake',
            'status' => GatewayPayment::STATUS_PENDING,
            'payment_method_type' => 'qris',
            'payment_url' => 'http://localhost/pay/AUDIT002',
            'expires_at' => now()->addDay(),
        ]);

        // Anggota biasa tidak boleh merekonsiliasi -> Harus 403
        $respMember = $this->actingAs($this->memberUserA)->post(route('payments.reconcile', $payment->id));
        $respMember->assertStatus(403);

        // Analis tidak boleh settlement -> Harus 403
        $respAnalyst = $this->actingAs($this->analystUser)->post(route('payments.settlement'), [
            'payment_ids' => [$payment->id],
            'settlement_no' => 'SETTLE-AUDIT-001',
            'bank_account' => 'BCA 1234567890',
        ]);
        $respAnalyst->assertStatus(403);
    }

    /**
     * Uji 7: Konfigurasi Zona Waktu Sistem Terkonfigurasi ke Asia/Makassar (WITA).
     */
    public function test_application_timezone_is_strictly_asia_makassar()
    {
        $this->assertEquals('Asia/Makassar', config('app.timezone'));
        $this->assertEquals('Asia/Makassar', Carbon::now()->getTimezone()->getName());
    }
}
