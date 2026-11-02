<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\SavingsTransaction;
use App\Services\SavingsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SavingsController extends Controller
{
    public function __construct(protected SavingsService $savings)
    {
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->isMember()) {
            $member = $user->member;
            if (!$member) {
                return redirect()->route('dashboard')->with('error', 'Profil anggota belum lengkap.');
            }
            $accounts = $member->savingsAccounts()->with('transactions')->get();
            $recentTransactions = SavingsTransaction::whereIn('savings_account_id', $accounts->pluck('id'))
                ->with('savingsAccount')
                ->orderBy('id', 'desc')
                ->paginate(15);

            return view('savings.member_index', compact('member', 'accounts', 'recentTransactions'));
        }

        // Tampilan untuk Petugas/Admin
        $query = SavingsAccount::with('member.user');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('account_no', 'like', "%{$search}%")
                  ->orWhereHas('member.user', fn($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $accounts = $query->orderBy('balance', 'desc')->paginate(15)->withQueryString();
        $totalSavings = (int) SavingsAccount::sum('balance');

        return view('savings.index', compact('accounts', 'totalSavings'));
    }

    public function show(SavingsAccount $account)
    {
        $user = Auth::user();
        if ($user->isMember() && $account->member->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak melihat rekening simpanan ini.');
        }

        $account->load(['member.user']);
        $transactions = $account->transactions()->orderBy('id', 'desc')->paginate(15);

        return view('savings.show', compact('account', 'transactions'));
    }

    public function depositForm(SavingsAccount $account)
    {
        $user = Auth::user();
        if ($user->isMember() && $account->member->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengakses rekening ini.');
        }
        if (!$user->isMember() && !in_array($user->role, ['teller', 'admin'])) {
            abort(403, 'Hanya Kasir Teller atau Admin yang berwenang melayani setoran simpanan.');
        }

        $account->loadMissing('member.user');
        return view('savings.deposit', compact('account'));
    }

    public function deposit(Request $request, SavingsAccount $account)
    {
        $user = Auth::user();
        if ($user->isMember() && $account->member->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengakses rekening ini.');
        }
        if (!$user->isMember() && !in_array($user->role, ['teller', 'admin'])) {
            abort(403, 'Hanya Kasir Teller atau Admin yang berwenang melayani setoran simpanan.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'payment_method' => 'required|in:cash,transfer',
            'proof_file' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:3072',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $proofPath = null;
            if ($request->hasFile('proof_file')) {
                $proofPath = $request->file('proof_file')->store('proofs', 'public');
            }

            $tx = $this->savings->deposit(
                $account,
                (int) $request->amount,
                $request->payment_method,
                $proofPath,
                $request->description
            );

            return redirect()->route('savings.show', $account->id)
                ->with('success', "Setoran sebesar Rp " . number_format($request->amount) . " berhasil dicatat! Saldo akhir: Rp " . number_format($tx->balance_after));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function withdrawForm(SavingsAccount $account)
    {
        $user = Auth::user();
        if ($user->isMember() && $account->member->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengakses rekening ini.');
        }
        if (!$user->isMember() && !in_array($user->role, ['teller', 'admin'])) {
            abort(403, 'Hanya Kasir Teller atau Admin yang berwenang melayani penarikan simpanan.');
        }

        if ($account->type !== 'sukarela') {
            return back()->with('error', 'Hanya Simpanan Sukarela yang dapat ditarik.');
        }

        $account->loadMissing('member.user');
        return view('savings.withdraw', compact('account'));
    }

    public function withdraw(Request $request, SavingsAccount $account)
    {
        $user = Auth::user();
        if ($user->isMember() && $account->member->user_id !== $user->id) {
            abort(403, 'Anda tidak berhak mengakses rekening ini.');
        }
        if (!$user->isMember() && !in_array($user->role, ['teller', 'admin'])) {
            abort(403, 'Hanya Kasir Teller atau Admin yang berwenang melayani penarikan simpanan.');
        }

        if ($account->type !== 'sukarela') {
            return back()->with('error', 'Hanya Simpanan Sukarela yang dapat ditarik.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:10000',
            'description' => 'nullable|string|max:255',
        ]);

        try {
            $tx = $this->savings->withdraw($account, (int) $request->amount, $request->description);

            return redirect()->route('savings.show', $account->id)
                ->with('success', "Penarikan sebesar Rp " . number_format($request->amount) . " berhasil diproses! Saldo akhir: Rp " . number_format($tx->balance_after));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }
}
