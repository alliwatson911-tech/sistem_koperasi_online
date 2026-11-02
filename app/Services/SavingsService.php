<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\SavingsTransaction;
use App\Models\Setting;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SavingsService
{
    public function __construct(protected JournalService $journals)
    {
    }

    /**
     * Buat 3 rekening simpanan standar saat pendaftaran anggota baru.
     */
    public function createDefaultAccounts(Member $member): void
    {
        $types = ['pokok', 'wajib', 'sukarela'];
        foreach ($types as $type) {
            $prefix = match ($type) {
                'pokok' => 'POK',
                'wajib' => 'WAJ',
                'sukarela' => 'SUK',
            };

            SavingsAccount::firstOrCreate(
                ['member_id' => $member->id, 'type' => $type],
                [
                    'account_no' => 'SAV-' . $prefix . '-' . str_pad((string)$member->id, 5, '0', STR_PAD_LEFT),
                    'balance' => 0,
                    'status' => 'active',
                    'opened_at' => now(),
                ]
            );
        }
    }

    /**
     * Setoran Simpanan (Pokok, Wajib, atau Sukarela).
     */
    public function deposit(SavingsAccount $account, int $amount, string $paymentMethod = 'cash', ?string $proofFile = null, ?string $description = null): SavingsTransaction
    {
        if ($amount <= 0) {
            throw new Exception("Nominal setoran harus lebih dari Rp 0.");
        }

        return DB::transaction(function () use ($account, $amount, $paymentMethod, $proofFile, $description) {
            $account = SavingsAccount::lockForUpdate()->find($account->id);

            $oldBalance = $account->balance;
            $newBalance = $oldBalance + $amount;
            $account->balance = $newBalance;
            $account->save();

            $transactionNo = 'TXS-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            $tx = SavingsTransaction::create([
                'savings_account_id' => $account->id,
                'transaction_no' => $transactionNo,
                'type' => 'deposit',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'payment_method' => $paymentMethod,
                'proof_file' => $proofFile,
                'description' => $description ?: "Setoran Simpanan " . ucfirst($account->type),
                'created_by' => auth()->id() ?? $account->member->user_id,
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            // Jurnal double-entry otomatis
            $kasCode = $paymentMethod === 'transfer' ? '1102' : '1101';
            $simpananCode = match ($account->type) {
                'pokok' => '2101',
                'wajib' => '2102',
                'sukarela' => '2103',
            };

            $this->journals->record(
                "Setoran {$tx->description} [No: {$tx->transaction_no}]",
                [
                    ['account_code' => $kasCode, 'type' => 'debit', 'amount' => $amount],
                    ['account_code' => $simpananCode, 'type' => 'credit', 'amount' => $amount],
                ],
                'savings_transaction',
                $tx->id
            );

            // Jika setoran simpanan pokok dan anggota berstatus pending/verified, aktivasi anggota!
            $requiredPokok = Setting::get('simpanan_pokok_nominal', 500000);
            if ($account->type === 'pokok' && $account->balance >= $requiredPokok) {
                $member = $account->member;
                if ($member->status === Member::STATUS_VERIFIED || $member->status === Member::STATUS_PENDING) {
                    $member->update([
                        'status' => Member::STATUS_ACTIVE,
                        'activated_at' => now(),
                        'activated_by' => auth()->id(),
                    ]);

                    AuditLog::log('activated', $member, ['status' => Member::STATUS_PENDING], ['status' => Member::STATUS_ACTIVE]);
                }
            }

            return $tx;
        });
    }

    /**
     * Penarikan Simpanan (Hanya diizinkan untuk Simpanan Sukarela bagi anggota aktif).
     */
    public function withdraw(SavingsAccount $account, int $amount, ?string $description = null): SavingsTransaction
    {
        if ($amount <= 0) {
            throw new Exception("Nominal penarikan harus lebih dari Rp 0.");
        }

        if ($account->type !== 'sukarela') {
            throw new Exception("Simpanan " . ucfirst($account->type) . " tidak dapat ditarik selama masih berstatus anggota aktif koperasi.");
        }

        return DB::transaction(function () use ($account, $amount, $description) {
            $account = SavingsAccount::lockForUpdate()->find($account->id);

            if ($account->balance < $amount) {
                throw new Exception("Saldo simpanan sukarela tidak mencukupi. Saldo saat ini: Rp " . number_format($account->balance));
            }

            $oldBalance = $account->balance;
            $newBalance = $oldBalance - $amount;
            $account->balance = $newBalance;
            $account->save();

            $transactionNo = 'TXS-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            $tx = SavingsTransaction::create([
                'savings_account_id' => $account->id,
                'transaction_no' => $transactionNo,
                'type' => 'withdrawal',
                'amount' => $amount,
                'balance_after' => $newBalance,
                'payment_method' => 'cash',
                'description' => $description ?: "Penarikan Simpanan Sukarela",
                'created_by' => auth()->id() ?? $account->member->user_id,
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => auth()->id(),
            ]);

            // Jurnal: Debit Simpanan Sukarela (2103), Credit Kas (1101)
            $this->journals->record(
                "Penarikan Simpanan Sukarela [No: {$tx->transaction_no}]",
                [
                    ['account_code' => '2103', 'type' => 'debit', 'amount' => $amount],
                    ['account_code' => '1101', 'type' => 'credit', 'amount' => $amount],
                ],
                'savings_transaction',
                $tx->id
            );

            return $tx;
        });
    }
}
