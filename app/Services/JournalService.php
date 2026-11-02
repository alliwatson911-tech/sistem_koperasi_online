<?php

namespace App\Services;

use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Models\JournalEntry;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class JournalService
{
    /**
     * Catat jurnal double-entry otomatis.
     * $entries = [
     *    ['account_code' => '1101', 'type' => 'debit', 'amount' => 500000, 'description' => '...'],
     *    ['account_code' => '2101', 'type' => 'credit', 'amount' => 500000, 'description' => '...'],
     * ]
     */
    public function record(string $description, array $entries, ?string $refType = null, ?int $refId = null, ?string $date = null): Journal
    {
        return DB::transaction(function () use ($description, $entries, $refType, $refId, $date) {
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($entries as $e) {
                if ($e['type'] === 'debit') {
                    $totalDebit += (int) $e['amount'];
                } elseif ($e['type'] === 'credit') {
                    $totalCredit += (int) $e['amount'];
                }
            }

            if ($totalDebit !== $totalCredit) {
                throw new Exception("Jurnal tidak seimbang! Total Debit (Rp " . number_format($totalDebit) . ") tidak sama dengan Total Credit (Rp " . number_format($totalCredit) . ").");
            }

            if ($totalDebit <= 0) {
                throw new Exception("Nominal transaksi jurnal harus lebih besar dari 0.");
            }

            $journalNo = 'JRN-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $journal = Journal::create([
                'journal_no' => $journalNo,
                'date' => $date ?: now()->toDateString(),
                'reference_type' => $refType,
                'reference_id' => $refId,
                'description' => $description,
                'total_amount' => $totalDebit,
                'status' => Journal::STATUS_POSTED,
                'created_by' => auth()->id(),
            ]);

            foreach ($entries as $e) {
                $code = match ($e['account_code']) {
                    '1-1300' => '1301',
                    '5-1100' => '5103',
                    default => $e['account_code'],
                };
                $account = ChartOfAccount::where('code', $code)->firstOrFail();
                JournalEntry::create([
                    'journal_id' => $journal->id,
                    'chart_of_account_id' => $account->id,
                    'type' => $e['type'],
                    'amount' => (int) $e['amount'],
                    'description' => $e['description'] ?? $description,
                ]);
            }

            return $journal;
        });
    }

    /**
     * Balik jurnal (Reversing entry) jika ada koreksi tanpa menghapus jejak audit.
     */
    public function reverse(Journal $journal, string $reason): Journal
    {
        return DB::transaction(function () use ($journal, $reason) {
            $journal->loadMissing('entries.account');

            if ($journal->status === Journal::STATUS_REVERSED) {
                throw new Exception("Jurnal ini sudah pernah dibalik sebelumnya.");
            }

            $reverseEntries = [];
            foreach ($journal->entries as $entry) {
                $reverseEntries[] = [
                    'account_code' => $entry->account->code,
                    'type' => $entry->type === 'debit' ? 'credit' : 'debit',
                    'amount' => $entry->amount,
                    'description' => "Pembalik: " . $entry->description,
                ];
            }

            $reverseJournal = $this->record(
                "Pembalik Jurnal #{$journal->journal_no}: {$reason}",
                $reverseEntries,
                'journal_reversal',
                $journal->id
            );

            $journal->update([
                'status' => Journal::STATUS_REVERSED,
                'reversed_by_id' => auth()->id(),
            ]);

            return $reverseJournal;
        });
    }
}
