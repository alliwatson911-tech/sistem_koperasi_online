<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ShuPeriod;
use App\Services\ShuService;
use Exception;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function __construct(protected ShuService $shuService)
    {
    }

    public function journals(Request $request)
    {
        $query = Journal::with(['entries.account', 'creator']);

        if ($request->filled('date_from')) {
            $query->where('date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('date', '<=', $request->date_to);
        }
        if ($request->filled('ref_type')) {
            $query->where('reference_type', $request->ref_type);
        }

        $journals = $query->orderBy('date', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('accounting.journals', compact('journals'));
    }

    public function ledger(Request $request)
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
        $selectedAccount = null;
        $entries = collect();
        $totalDebit = 0;
        $totalCredit = 0;
        $balance = 0;

        if ($request->filled('account_id')) {
            $selectedAccount = ChartOfAccount::findOrFail($request->account_id);
            $query = JournalEntry::where('chart_of_account_id', $selectedAccount->id)
                ->whereHas('journal', fn($q) => $q->where('status', 'posted'));

            if ($request->filled('date_from')) {
                $query->whereHas('journal', fn($q) => $q->where('date', '>=', $request->date_from));
            }
            if ($request->filled('date_to')) {
                $query->whereHas('journal', fn($q) => $q->where('date', '<=', $request->date_to));
            }

            $entries = $query->with('journal')->orderBy('id', 'asc')->get();

            $totalDebit = (int) $entries->where('type', 'debit')->sum('amount');
            $totalCredit = (int) $entries->where('type', 'credit')->sum('amount');

            if ($selectedAccount->normal_balance === 'debit') {
                $balance = $totalDebit - $totalCredit;
            } else {
                $balance = $totalCredit - $totalDebit;
            }
        }

        return view('accounting.ledger', compact('accounts', 'selectedAccount', 'entries', 'totalDebit', 'totalCredit', 'balance'));
    }

    public function trialBalance(Request $request)
    {
        $accounts = ChartOfAccount::orderBy('code')->get();
        $trialBalances = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $acc) {
            $debitSum = (int) JournalEntry::where('chart_of_account_id', $acc->id)
                ->where('type', 'debit')
                ->whereHas('journal', fn($q) => $q->where('status', 'posted'))
                ->sum('amount');

            $creditSum = (int) JournalEntry::where('chart_of_account_id', $acc->id)
                ->where('type', 'credit')
                ->whereHas('journal', fn($q) => $q->where('status', 'posted'))
                ->sum('amount');

            $netDebit = 0;
            $netCredit = 0;

            if ($acc->normal_balance === 'debit') {
                $net = $debitSum - $creditSum;
                if ($net >= 0) {
                    $netDebit = $net;
                } else {
                    $netCredit = abs($net);
                }
            } else {
                $net = $creditSum - $debitSum;
                if ($net >= 0) {
                    $netCredit = $net;
                } else {
                    $netDebit = abs($net);
                }
            }

            $totalDebit += $netDebit;
            $totalCredit += $netCredit;

            $trialBalances[] = [
                'account' => $acc,
                'debit' => $netDebit,
                'credit' => $netCredit,
            ];
        }

        $isBalanced = ($totalDebit === $totalCredit);

        return view('accounting.trial_balance', compact('trialBalances', 'totalDebit', 'totalCredit', 'isBalanced'));
    }

    public function shu(Request $request)
    {
        $year = (int) ($request->year ?: date('Y'));
        $preview = $this->shuService->calculatePreview($year);
        $periods = ShuPeriod::with('closer')->orderBy('year', 'desc')->get();

        return view('accounting.shu', compact('year', 'preview', 'periods'));
    }

    public function closeShu(Request $request)
    {
        $request->validate(['year' => 'required|integer|min:2020|max:2050']);

        try {
            $period = $this->shuService->closeBookAndDistribute((int) $request->year);
            return back()->with('success', "Tutup buku dan pembagian SHU tahun {$period->year} berhasil diproses dan dijurnal!");
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
