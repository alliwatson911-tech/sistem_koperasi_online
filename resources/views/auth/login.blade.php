@extends('layouts.app')

@section('title', 'Masuk ke Sistem')

@section('content')
<div class="max-w-md mx-auto my-6">
    <div class="ksp-card bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto mb-3 rounded-2xl navbar-brand-badge flex items-center justify-center text-white font-extrabold text-2xl shadow-md">
                K
            </div>
            <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Masuk Akun Koperasi</h1>
            <p class="text-sm text-slate-500 mt-1">Sistem Informasi KSP Sahabat Sejahtera</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
            @csrf

            <div class="form-group">
                <label for="emailInput" class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider">Alamat Email *</label>
                <div class="relative">
                    <input type="email" name="email" id="emailInput" class="form-control" value="{{ old('email') }}" required autofocus placeholder="nama@ksp.test">
                </div>
                @error('email')
                    <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <div class="flex items-center justify-between mb-1">
                    <label for="passwordInput" class="form-label text-xs font-bold text-slate-700 uppercase tracking-wider mb-0">Kata Sandi *</label>
                </div>
                <input type="password" name="password" id="passwordInput" class="form-control" required placeholder="••••••••">
                @error('password')
                    <div class="text-red-600 text-xs mt-1 font-medium">{{ $message }}</div>
                @enderror
            </div>

            <div class="flex items-center justify-between text-xs text-slate-600 pt-1">
                <label class="flex items-center gap-2 cursor-pointer font-medium">
                    <input type="checkbox" name="remember" class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300">
                    <span>Ingat saya di perangkat ini</span>
                </label>
            </div>

            <button type="submit" class="w-full btn btn-primary py-2.5 rounded-xl font-bold text-sm shadow transition-all hover:shadow-md">
                Masuk ke Akun
            </button>
        </form>

        <div class="text-center mt-6 pt-5 border-t border-slate-100 text-xs text-slate-500">
            Belum terdaftar sebagai anggota? 
            <a href="{{ route('register') }}" class="text-emerald-700 hover:text-emerald-900 font-bold hover:underline">
                Daftar Anggota Online
            </a>
        </div>
    </div>

    {{-- Kartu Akun Demo untuk Pengujian Cepat --}}
    <div class="mt-4 p-5 rounded-2xl bg-slate-100/80 border border-dashed border-slate-300">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1.5">
                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                Pilih Akun Demo untuk Uji Coba:
            </span>
            <span class="text-[11px] font-mono bg-white px-2 py-0.5 rounded border border-slate-200 text-slate-500">password123</span>
        </div>

        <div class="grid grid-cols-1 gap-2">
            <button type="button" class="flex items-center justify-between p-2.5 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-left transition shadow-xs group" onclick="fillLogin('admin@ksp.test', 'password123')">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-red-500"></span>
                    <div>
                        <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-700">Admin / Pengurus</div>
                        <div class="text-[11px] text-slate-500 font-mono">admin@ksp.test</div>
                    </div>
                </div>
                <span class="role-badge role-admin text-[10px]">ADMIN</span>
            </button>

            <button type="button" class="flex items-center justify-between p-2.5 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-left transition shadow-xs group" onclick="fillLogin('manajer@ksp.test', 'password123')">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    <div>
                        <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-700">Manajer Operasional</div>
                        <div class="text-[11px] text-slate-500 font-mono">manajer@ksp.test</div>
                    </div>
                </div>
                <span class="role-badge role-manager text-[10px]">MANAGER</span>
            </button>

            <button type="button" class="flex items-center justify-between p-2.5 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-left transition shadow-xs group" onclick="fillLogin('analis@ksp.test', 'password123')">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <div>
                        <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-700">Analis Pembiayaan</div>
                        <div class="text-[11px] text-slate-500 font-mono">analis@ksp.test</div>
                    </div>
                </div>
                <span class="role-badge role-analyst text-[10px]">ANALYST</span>
            </button>

            <button type="button" class="flex items-center justify-between p-2.5 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-left transition shadow-xs group" onclick="fillLogin('teller@ksp.test', 'password123')">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <div>
                        <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-700">Teller / Petugas Kas</div>
                        <div class="text-[11px] text-slate-500 font-mono">teller@ksp.test</div>
                    </div>
                </div>
                <span class="role-badge role-teller text-[10px]">TELLER</span>
            </button>

            <button type="button" class="flex items-center justify-between p-2.5 bg-white hover:bg-slate-50 rounded-xl border border-slate-200 text-left transition shadow-xs group" onclick="fillLogin('budi@anggota.test', 'password123')">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                    <div>
                        <div class="text-xs font-bold text-slate-800 group-hover:text-emerald-700">Budi Santoso (Anggota)</div>
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
    document.getElementById('emailInput').value = email;
    document.getElementById('passwordInput').value = password;
}
</script>
@endsection
