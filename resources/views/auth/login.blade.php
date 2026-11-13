@extends('layouts.app')

@section('title', 'Masuk ke Sistem')

@section('content')
<style>
    /* Principles of UX Motion: Exaggerated Feedback Animation */
    @keyframes kspExaggeratedShake {
        0%, 100% {
            transform: translateX(0) rotate(0deg);
        }
        12% {
            transform: translateX(-14px) rotate(-1.8deg);
        }
        28% {
            transform: translateX(12px) rotate(1.5deg);
        }
        44% {
            transform: translateX(-9px) rotate(-1deg);
        }
        60% {
            transform: translateX(7px) rotate(0.6deg);
        }
        76% {
            transform: translateX(-4px) rotate(-0.3deg);
        }
        88% {
            transform: translateX(2px);
        }
    }

    .input-exaggerated-shake {
        animation: kspExaggeratedShake 0.65s cubic-bezier(0.36, 0.07, 0.19, 0.97) both;
        border-color: #E63946 !important;
        box-shadow: 0 0 0 3.5px rgba(230, 57, 70, 0.22) !important;
    }

    @keyframes fadeInSlideUp {
        from {
            opacity: 0;
            transform: translateY(-4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-feedback-in {
        animation: fadeInSlideUp 0.25s ease-out forwards;
    }
</style>

<div class="max-w-md mx-auto my-6">
    <div class="ksp-card bg-white p-8 rounded-2xl shadow-sm border border-[#E8E2D8]">
        <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl navbar-brand-badge flex items-center justify-center text-white font-extrabold text-2xl shadow-md">
                K
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Masuk Akun Koperasi</h1>
            <p class="text-sm text-slate-500 mt-1">Sistem Informasi KSP Sahabat Sejahtera</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4" id="loginForm" novalidate>
            @csrf

            <div class="form-group">
                <label for="emailInput" class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email / Akun *</label>
                <div class="relative">
                    <input type="email" 
                           name="email" 
                           id="emailInput" 
                           class="form-control @error('email') input-exaggerated-shake border-[#E63946] text-rose-900 @enderror" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           placeholder="nama@ksp.test">
                </div>
                @error('email')
                    <div id="emailFeedback" class="text-[#E63946] text-xs mt-1.5 font-bold flex items-center gap-1.5 animate-feedback-in" role="alert">
                        <svg class="w-3.5 h-3.5 shrink-0 text-[#E63946]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <div class="flex items-center justify-between mb-1">
                    <label for="passwordInput" class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider mb-0">Kata Sandi *</label>
                </div>
                <input type="password" 
                       name="password" 
                       id="passwordInput" 
                       class="form-control @error('password') input-exaggerated-shake border-[#E63946] text-rose-900 @enderror" 
                       required 
                       placeholder="••••••••">
                @error('password')
                    <div id="passwordFeedback" class="text-[#E63946] text-xs mt-1.5 font-bold flex items-center gap-1.5 animate-feedback-in" role="alert">
                        <svg class="w-3.5 h-3.5 shrink-0 text-[#E63946]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ $message }}</span>
                    </div>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs text-slate-600 pt-1">
                <label class="flex items-center gap-2 cursor-pointer font-medium">
                    <input type="checkbox" name="remember" class="rounded text-[#2A9D8F] focus:ring-[#2A9D8F] border-slate-300">
                    <span>Ingat saya di perangkat ini</span>
                </label>
            </div>

            <button type="submit" class="w-full btn btn-primary py-2.5 rounded-xl font-bold text-sm shadow transition-all hover:shadow-md">
                Masuk ke Akun
            </button>
        </form>

        <div class="text-center mt-6 pt-5 border-t border-slate-100 text-xs text-slate-500">
            Belum terdaftar sebagai anggota? 
            <a href="{{ route('register') }}" class="text-[#0F4C81] hover:text-[#1B263B] font-bold hover:underline">
                Daftar Anggota Online
            </a>
        </div>
    </div>

    {{-- Kartu Akun Demo untuk Pengujian Cepat --}}
    <div class="mt-4 p-5 rounded-2xl bg-[#F8F5F0] border border-dashed border-[#E8E2D8]">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-[#0F4C81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                Pilih Akun Demo untuk Uji Coba:
            </span>
            <span class="text-[11px] font-mono bg-white px-2 py-0.5 rounded border border-[#E8E2D8] text-slate-500">password123</span>
        </div>

        <div class="grid grid-cols-1 gap-2">
            <button type="button" class="flex items-center justify-between p-2.5 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-left transition shadow-xs group" onclick="fillLogin('admin@ksp.test', 'password123')">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <div>
                        <div class="text-xs font-bold text-slate-800 group-hover:text-[#0F4C81]">Admin / Pengurus</div>
                        <div class="text-[11px] text-slate-500 font-mono">admin@ksp.test</div>
                    </div>
                </div>
                <span class="role-badge role-admin text-[10px]">ADMIN</span>
            </button>

            <button type="button" class="flex items-center justify-between p-2.5 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-left transition shadow-xs group" onclick="fillLogin('manajer@ksp.test', 'password123')">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <div>
                        <div class="text-xs font-bold text-slate-800 group-hover:text-[#0F4C81]">Manajer Operasional</div>
                        <div class="text-[11px] text-slate-500 font-mono">manajer@ksp.test</div>
                    </div>
                </div>
                <span class="role-badge role-manager text-[10px]">MANAGER</span>
            </button>

            <button type="button" class="flex items-center justify-between p-2.5 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-left transition shadow-xs group" onclick="fillLogin('analis@ksp.test', 'password123')">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <div>
                        <div class="text-xs font-bold text-slate-800 group-hover:text-[#0F4C81]">Analis Pembiayaan</div>
                        <div class="text-[11px] text-slate-500 font-mono">analis@ksp.test</div>
                    </div>
                </div>
                <span class="role-badge role-analyst text-[10px]">ANALYST</span>
            </button>

            <button type="button" class="flex items-center justify-between p-2.5 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-left transition shadow-xs group" onclick="fillLogin('teller@ksp.test', 'password123')">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <div>
                        <div class="text-xs font-bold text-slate-800 group-hover:text-[#0F4C81]">Teller / Petugas Kas</div>
                        <div class="text-[11px] text-slate-500 font-mono">teller@ksp.test</div>
                    </div>
                </div>
                <span class="role-badge role-teller text-[10px]">TELLER</span>
            </button>

            <button type="button" class="flex items-center justify-between p-2.5 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-left transition shadow-xs group" onclick="fillLogin('budi@anggota.test', 'password123')">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <div>
                        <div class="text-xs font-bold text-slate-800 group-hover:text-[#0F4C81]">Budi Santoso (Anggota)</div>
                        <div class="text-[11px] text-slate-500 font-mono">budi@anggota.test</div>
                    </div>
                </div>
                <span class="role-badge role-member text-[10px]">MEMBER</span>
            </button>
        </div>
    </div>
</div>

<script>
function fillLogin(email, password) {
    const emailInput = document.getElementById('emailInput');
    const passwordInput = document.getElementById('passwordInput');
    if (emailInput) {
        emailInput.value = email;
        emailInput.classList.remove('input-exaggerated-shake', 'border-[#E63946]', 'text-rose-900');
    }
    if (passwordInput) {
        passwordInput.value = password;
        passwordInput.classList.remove('input-exaggerated-shake', 'border-[#E63946]', 'text-rose-900');
    }
    const emailFb = document.getElementById('emailFeedback');
    if (emailFb) emailFb.remove();
    const passFb = document.getElementById('passwordFeedback');
    if (passFb) passFb.remove();
}

document.addEventListener('DOMContentLoaded', function() {
    // Focus otomatis pada field input yang mengalami error
    const shakingInput = document.querySelector('.input-exaggerated-shake');
    if (shakingInput) {
        shakingInput.focus();
    }

    // Hapus kelas animasi getar saat pengguna mulai mengetik kembali
    ['emailInput', 'passwordInput'].forEach(function(inputId) {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function() {
                this.classList.remove('input-exaggerated-shake', 'border-[#E63946]', 'text-rose-900');
            });
        }
    });
});
</script>
@endsection
