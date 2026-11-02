<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanSchedule;
use App\Models\Member;
use App\Models\SavingsAccount;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function savings(Request $request)
    {
        $query = Member::with(['user', 'savingsAccounts'])
            ->where('status', Member::STATUS_ACTIVE);

        $members = $query->paginate(20);

        $summary = [
            'total_pokok' => (int) SavingsAccount::where('type', 'pokok')->sum('balance'),
            'total_wajib' => (int) SavingsAccount::where('type', 'wajib')->sum('balance'),
            'total_sukarela' => (int) SavingsAccount::where('type', 'sukarela')->sum('balance'),
            'grand_total' => (int) SavingsAccount::sum('balance'),
        ];

        return view('reports.savings', compact('members', 'summary'));
    }

    public function loans(Request $request)
    {
        $query = Loan::with(['member.user', 'loanProduct']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query->orderBy('id', 'desc')->paginate(20)->withQueryString();

        $summary = [
            'total_disbursed' => (int) Loan::sum('principal_amount'),
            'total_outstanding' => (int) Loan::whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE])->sum('outstanding_principal'),
            'total_active' => Loan::where('status', Loan::STATUS_ACTIVE)->count(),
            'total_overdue' => Loan::where('status', Loan::STATUS_OVERDUE)->count(),
            'total_paid_off' => Loan::where('status', Loan::STATUS_PAID_OFF)->count(),
        ];

        return view('reports.loans', compact('loans', 'summary'));
    }

    public function overdue(Request $request)
    {
        $today = now()->toDateString();
        $overdueLoans = Loan::where('status', Loan::STATUS_OVERDUE)
            ->with(['member.user', 'loanProduct', 'schedules' => function ($q) use ($today) {
                $q->whereIn('status', [LoanSchedule::STATUS_OVERDUE, LoanSchedule::STATUS_UNPAID])
                  ->where('due_date', '<', $today);
            }])
            ->get();

        $totalOverduePrincipal = (int) $overdueLoans->sum('outstanding_principal');
        $totalPenalty = (int) $overdueLoans->sum('outstanding_penalty');

        return view('reports.overdue', compact('overdueLoans', 'totalOverduePrincipal', 'totalPenalty'));
    }

    public function cashFlow(Request $request)
    {
        $month = $request->month ?: date('m');
        $year = $request->year ?: date('Y');

        // Akun Kas & Bank
        $cashAccounts = [1, 2]; // id akun 1101 dan 1102

        $inflows = JournalEntry::whereIn('chart_of_account_id', $cashAccounts)
            ->where('type', 'debit')
            ->whereHas('journal', fn($q) => $q->whereMonth('date', $month)->whereYear('date', $year)->where('status', 'posted'))
            ->with(['journal', 'account'])
            ->get();

        $outflows = JournalEntry::whereIn('chart_of_account_id', $cashAccounts)
            ->where('type', 'credit')
            ->whereHas('journal', fn($q) => $q->whereMonth('date', $month)->whereYear('date', $year)->where('status', 'posted'))
            ->with(['journal', 'account'])
            ->get();

        $totalInflow = (int) $inflows->sum('amount');
        $totalOutflow = (int) $outflows->sum('amount');
        $netCashFlow = $totalInflow - $totalOutflow;

        return view('reports.cash_flow', compact('month', 'year', 'inflows', 'outflows', 'totalInflow', 'totalOutflow', 'netCashFlow'));
    }
}
