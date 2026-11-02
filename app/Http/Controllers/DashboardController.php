<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isMember()) {
            return $this->memberDashboard($user);
        }

        return $this->staffDashboard($user);
    }

    protected function memberDashboard(User $user)
    {
        $member = $user->member;
        if (!$member) {
            return view('dashboard.member', [
                'user' => $user,
                'member' => null,
                'upcomingSchedules' => collect(),
            ]);
        }

        $savingsAccounts = $member->savingsAccounts()->get();
        $totalSavings = $member->total_savings;

        $activeLoans = $member->loans()
            ->whereIn('status', [Loan::STATUS_ACTIVE, Loan::STATUS_OVERDUE, Loan::STATUS_AWAITING_DISBURSEMENT])
            ->with(['loanProduct', 'schedules'])
            ->get();

        $loanApplications = $member->loanApplications()
            ->with('loanProduct')
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $recentTransactions = \App\Models\SavingsTransaction::whereIn('savings_account_id', $savingsAccounts->pluck('id'))
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        $upcomingSchedules = \App\Models\LoanSchedule::whereIn('loan_id', $activeLoans->pluck('id'))
            ->whereIn('status', [\App\Models\LoanSchedule::STATUS_UNPAID, \App\Models\LoanSchedule::STATUS_PARTIAL, \App\Models\LoanSchedule::STATUS_OVERDUE])
            ->orderBy('due_date', 'asc')
            ->take(3)
            ->get();

        return view('dashboard.member', compact(
            'user',
            'member',
            'savingsAccounts',
            'totalSavings',
            'activeLoans',
            'loanApplications',
            'recentTransactions',
            'upcomingSchedules'
        ));
    }

    protected function staffDashboard(User $user)
    {
        $stats = [
            'total_members' => Member::count(),
            'active_members' => Member::where('status', Member::STATUS_ACTIVE)->count(),
            'pending_members' => Member::where('status', Member::STATUS_PENDING)->count(),
            'total_savings' => (int) SavingsAccount::sum('balance'),
            'total_loans_active' => (int) Loan::where('status', Loan::STATUS_ACTIVE)->sum('outstanding_principal'),
            'total_loans_overdue' => (int) Loan::where('status', Loan::STATUS_OVERDUE)->sum('outstanding_principal'),
            'pending_loan_applications' => LoanApplication::whereIn('status', [LoanApplication::STATUS_SUBMITTED, LoanApplication::STATUS_IN_REVIEW])->count(),
            'ready_to_disburse_loans' => Loan::where('status', Loan::STATUS_AWAITING_DISBURSEMENT)->count(),
        ];

        // Antrean verifikasi anggota untuk Teller/Admin
        $pendingVerifications = Member::where('status', Member::STATUS_PENDING)
            ->with(['user', 'documents'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // Antrean pengajuan pinjaman
        $pendingApplications = LoanApplication::whereIn('status', [LoanApplication::STATUS_SUBMITTED, LoanApplication::STATUS_IN_REVIEW])
            ->with(['member.user', 'loanProduct', 'approvals.user'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        // Pinjaman siap dicairkan
        $awaitingDisbursementLoans = Loan::where('status', Loan::STATUS_AWAITING_DISBURSEMENT)
            ->with(['member.user', 'loanProduct'])
            ->orderBy('id', 'desc')
            ->take(5)
            ->get();

        return view('dashboard.staff', compact(
            'user',
            'stats',
            'pendingVerifications',
            'pendingApplications',
            'awaitingDisbursementLoans'
        ));
    }
}
