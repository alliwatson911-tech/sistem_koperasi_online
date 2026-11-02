<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\User;
use App\Services\JournalService;
use App\Services\SavingsService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingJournalTest extends TestCase
{
    use RefreshDatabase;

    protected JournalService $journalService;
    protected SavingsService $savingsService;
    protected User $memberUser;
    protected Member $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->journalService = app(JournalService::class);
        $this->savingsService = app(SavingsService::class);

        // Siapkan CoA standar
        ChartOfAccount::create(['code' => '1101', 'name' => 'Kas Koperasi', 'type' => 'asset', 'category' => 'asset', 'normal_balance' => 'debit']);
        ChartOfAccount::create(['code' => '1102', 'name' => 'Bank Mandiri', 'type' => 'asset', 'category' => 'asset', 'normal_balance' => 'debit']);
        ChartOfAccount::create(['code' => '2101', 'name' => 'Simpanan Pokok Anggota', 'type' => 'liability', 'category' => 'liability', 'normal_balance' => 'credit']);
        ChartOfAccount::create(['code' => '2102', 'name' => 'Simpanan Wajib Anggota', 'type' => 'liability', 'category' => 'liability', 'normal_balance' => 'credit']);
        ChartOfAccount::create(['code' => '2103', 'name' => 'Simpanan Sukarela Anggota', 'type' => 'liability', 'category' => 'liability', 'normal_balance' => 'credit']);
        ChartOfAccount::create(['code' => '3101', 'name' => 'Modal Koperasi', 'type' => 'equity', 'category' => 'equity', 'normal_balance' => 'credit']);

        $this->memberUser = User::factory()->create(['role' => 'member']);
        $this->member = Member::create([
            'user_id' => $this->memberUser->id,
            'member_no' => 'ANG-JRNL-01',
            'nik' => '3201010101018888',
            'birth_date' => '1992-02-02',
            'birth_place' => 'Surabaya',
            'gender' => 'L',
            'address' => 'Jl. Akuntansi No. 1',
            'occupation' => 'Akuntan',
            'monthly_income' => 8000000,
            'status' => Member::STATUS_ACTIVE,
        ]);

        $this->savingsService->createDefaultAccounts($this->member);
    }

    public function test_journal_must_be_balanced_or_throws_exception()
    {
        // Jurnal seimbang (Rp 500.000 debit, Rp 500.000 kredit)
        $balancedJournal = $this->journalService->record(
            'Penerimaan Modal Tambahan',
            [
                ['account_code' => '1101', 'type' => 'debit', 'amount' => 500000],
                ['account_code' => '3101', 'type' => 'credit', 'amount' => 500000],
            ],
            'manual',
            null
        );

        $this->assertInstanceOf(Journal::class, $balancedJournal);
        $this->assertTrue($balancedJournal->isBalanced());
        $this->assertEquals(500000, $balancedJournal->total_debit);
        $this->assertEquals(500000, $balancedJournal->total_credit);

        // Jurnal tidak seimbang harus melempar Exception
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('tidak seimbang');

        $this->journalService->record(
            'Jurnal Tidak Seimbang (Invalid)',
            [
                ['account_code' => '1101', 'type' => 'debit', 'amount' => 500000],
                ['account_code' => '3101', 'type' => 'credit', 'amount' => 400000], // Selisih 100rb
            ],
            'manual',
            null
        );
    }

    public function test_savings_deposit_and_withdrawal_generate_balanced_journals()
    {
        $sukarelaAccount = $this->member->savingsAccounts()->where('type', 'sukarela')->first();

        // 1. Setoran Simpanan Sukarela Rp 250.000
        $trxDeposit = $this->savingsService->deposit(
            $sukarelaAccount,
            250000,
            'cash',
            'Setoran tabungan sukarela'
        );

        $this->assertEquals(250000, $sukarelaAccount->fresh()->balance);

        $journalDeposit = Journal::where('reference_type', 'savings_transaction')
            ->where('reference_id', $trxDeposit->id)
            ->first();

        $this->assertNotNull($journalDeposit);
        $this->assertTrue($journalDeposit->isBalanced());
        $this->assertEquals(250000, $journalDeposit->total_debit);
        $this->assertEquals(250000, $journalDeposit->total_credit);

        // 2. Penarikan Simpanan Sukarela Rp 100.000
        $trxWithdraw = $this->savingsService->withdraw(
            $sukarelaAccount,
            100000,
            'cash',
            'Tarik tabungan belanja'
        );

        $this->assertEquals(150000, $sukarelaAccount->fresh()->balance);

        $journalWithdraw = Journal::where('reference_type', 'savings_transaction')
            ->where('reference_id', $trxWithdraw->id)
            ->first();

        $this->assertNotNull($journalWithdraw);
        $this->assertTrue($journalWithdraw->isBalanced());
        $this->assertEquals(100000, $journalWithdraw->total_debit);
        $this->assertEquals(100000, $journalWithdraw->total_credit);
    }

    public function test_journal_reversal_creates_opposite_entries_without_deleting()
    {
        $original = $this->journalService->record(
            'Penerimaan Kas Awal',
            [
                ['account_code' => '1101', 'type' => 'debit', 'amount' => 1000000],
                ['account_code' => '3101', 'type' => 'credit', 'amount' => 1000000],
            ],
            'manual',
            null
        );

        $reversal = $this->journalService->reverse($original, 'Koreksi salah input kas awal');

        $this->assertInstanceOf(Journal::class, $reversal);
        $this->assertTrue($reversal->isBalanced());
        $this->assertNotEquals($original->id, $reversal->id);

        // Jurnal asli tetap ada di database (prinsip immutability pencatatan keuangan)
        $this->assertDatabaseHas('journals', ['id' => $original->id]);
        $this->assertDatabaseHas('journals', ['id' => $reversal->id]);

        // Jurnal pembalik membalik debit dan kredit
        $debitInReversal = $reversal->entries()->where('type', 'debit')->first();
        $creditInReversal = $reversal->entries()->where('type', 'credit')->first();

        $this->assertEquals('3101', $debitInReversal->account->code);
        $this->assertEquals('1101', $creditInReversal->account->code);
    }
}
