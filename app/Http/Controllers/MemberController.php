<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Member;
use App\Models\MemberDocument;
use App\Services\SavingsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function __construct(protected SavingsService $savings)
    {
    }

    public function index(Request $request)
    {
        $query = Member::with(['user', 'savingsAccounts']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('member_no', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('phone', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $members = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        return view('members.index', compact('members'));
    }

    public function show(Member $member)
    {
        $member->load(['user', 'documents.verifier', 'savingsAccounts.transactions', 'loans.loanProduct']);
        return view('members.show', compact('member'));
    }

    public function verify(Request $request, Member $member)
    {
        if (!in_array(Auth::user()->role, ['teller', 'admin'])) {
            abort(403, 'Hanya Teller atau Admin yang dapat memverifikasi berkas anggota.');
        }

        $request->validate([
            'action' => 'required|in:approve,reject',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($request->action === 'approve') {
            $nextNumber = Member::whereNotNull('member_no')->count() + 1;
            $memberNo = 'KSP-' . date('Y') . '-' . str_pad((string)$nextNumber, 4, '0', STR_PAD_LEFT);

            $member->update([
                'member_no' => $memberNo,
                'status' => Member::STATUS_VERIFIED,
                'verified_at' => now(),
                'verified_by' => Auth::id(),
                'notes' => $request->notes,
            ]);

            // Update status seluruh dokumen pending menjadi approved
            $member->documents()->where('status', 'pending')->update([
                'status' => 'approved',
                'verified_at' => now(),
                'verified_by' => Auth::id(),
            ]);

            AuditLog::log('member_verified', $member, null, ['status' => Member::STATUS_VERIFIED, 'member_no' => $memberNo]);

            return back()->with('success', "Anggota {$member->user->name} berhasil diverifikasi! Nomor Anggota: {$memberNo}. Silakan arahkan untuk setoran Simpanan Pokok.");
        } else {
            $member->update([
                'status' => Member::STATUS_RESIGNED,
                'notes' => $request->notes,
            ]);

            $member->documents()->where('status', 'pending')->update([
                'status' => 'rejected',
                'notes' => $request->notes,
                'verified_at' => now(),
                'verified_by' => Auth::id(),
            ]);

            AuditLog::log('member_rejected', $member, null, ['status' => Member::STATUS_RESIGNED, 'notes' => $request->notes]);

            return back()->with('error', "Pendaftaran anggota {$member->user->name} ditolak. Catatan: {$request->notes}");
        }
    }

    public function profile()
    {
        $user = Auth::user();
        $member = $user->member;
        if (!$member) {
            abort(404, 'Data anggota tidak ditemukan.');
        }
        $member->load(['documents', 'savingsAccounts', 'loans']);

        return view('members.profile', compact('user', 'member'));
    }
}
