<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Member;
use App\Models\MemberDocument;
use App\Models\User;
use App\Services\SavingsService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(protected SavingsService $savings)
    {
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->with('error', 'Akun Anda dinonaktifkan oleh pengurus koperasi.');
            }

            AuditLog::log('login', $user);

            return redirect()->intended(route('dashboard'))->with('success', "Selamat datang kembali, {$user->name}!");
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
            'nik' => 'required|numeric|digits:16|unique:members,nik',
            'birth_date' => 'required|date',
            'birth_place' => 'required|string|max:100',
            'gender' => 'required|in:L,P',
            'address' => 'required|string|max:500',
            'occupation' => 'required|string|max:100',
            'monthly_income' => 'required|numeric|min:0',
            'bank_name' => 'nullable|string|max:50',
            'bank_account' => 'nullable|string|max:50',
            'ktp_document' => 'required|file|mimes:jpg,jpeg,png,pdf|max:3072',
            'selfie_document' => 'nullable|file|mimes:jpg,jpeg,png|max:3072',
        ]);

        return DB::transaction(function () use ($request, $validated) {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => Hash::make($validated['password']),
                'role' => User::ROLE_MEMBER,
                'is_active' => true,
            ]);

            $member = Member::create([
                'user_id' => $user->id,
                'nik' => $validated['nik'],
                'birth_date' => $validated['birth_date'],
                'birth_place' => $validated['birth_place'],
                'gender' => $validated['gender'],
                'address' => $validated['address'],
                'occupation' => $validated['occupation'],
                'monthly_income' => $validated['monthly_income'],
                'bank_name' => $validated['bank_name'] ?? null,
                'bank_account' => $validated['bank_account'] ?? null,
                'status' => Member::STATUS_PENDING,
            ]);

            // Buat rekening simpanan default
            $this->savings->createDefaultAccounts($member);

            // Simpan dokumen KTP
            if ($request->hasFile('ktp_document')) {
                $path = $request->file('ktp_document')->store('member_documents', 'public');
                MemberDocument::create([
                    'member_id' => $member->id,
                    'document_type' => 'ktp',
                    'file_path' => $path,
                    'original_name' => $request->file('ktp_document')->getClientOriginalName(),
                    'status' => 'pending',
                ]);
            }

            // Simpan dokumen selfie jika ada
            if ($request->hasFile('selfie_document')) {
                $path = $request->file('selfie_document')->store('member_documents', 'public');
                MemberDocument::create([
                    'member_id' => $member->id,
                    'document_type' => 'selfie',
                    'file_path' => $path,
                    'original_name' => $request->file('selfie_document')->getClientOriginalName(),
                    'status' => 'pending',
                ]);
            }

            Auth::login($user);
            AuditLog::log('member_registered', $member);

            return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Akun Anda sedang dalam proses verifikasi oleh petugas.');
        });
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('info', 'Anda telah keluar dari sistem.');
    }
}
