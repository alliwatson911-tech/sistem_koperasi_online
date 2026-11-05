@php
    $u = auth()->user();
    $role = $u->role;

    // Helper closure untuk kelas link menu dengan warna senada & elegan (Harmonized Emerald Theme)
    $itemClass = function ($isActive) {
        return $isActive
            ? 'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-extrabold bg-emerald-600 text-white shadow-md shadow-emerald-950/40 border border-emerald-400/30 transition-all'
            : 'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold text-emerald-100/80 hover:text-white hover:bg-emerald-800/35 transition-all group';
    };

    $iconClass = function ($isActive) {
        return $isActive ? 'w-4 h-4 text-white shrink-0' : 'w-4 h-4 text-emerald-300/70 group-hover:text-white shrink-0 transition-colors';
    };
@endphp

@if($role === 'admin')
    {{-- ========================================================================= --}}
    {{-- SIDEBAR ROLE: ADMINISTRATOR --}}
    {{-- ========================================================================= --}}
    <div class="space-y-4">
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Menu Utama</span>
            <a href="{{ route('dashboard') }}" class="{{ $itemClass(request()->routeIs('dashboard')) }}">
                <svg class="{{ $iconClass(request()->routeIs('dashboard')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dasbor Operasional</span>
            </a>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Layanan Anggota</span>
            <div class="space-y-1">
                <a href="{{ route('members.index') }}" class="{{ $itemClass(request()->routeIs('members.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('members.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Kelola Anggota</span>
                </a>
                <a href="{{ route('savings.index') }}" class="{{ $itemClass(request()->routeIs('savings.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('savings.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Simpanan & Kas</span>
                </a>
            </div>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Pinjaman & Pembiayaan</span>
            <div class="space-y-1">
                <a href="{{ route('loans.applications') }}" class="{{ $itemClass(request()->routeIs('loans.applications*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.applications*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Pengajuan Pinjaman</span>
                </a>
                <a href="{{ route('loans.manage') }}" class="{{ $itemClass(request()->routeIs('loans.manage*') || request()->routeIs('loans.index')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.manage*') || request()->routeIs('loans.index')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Daftar Pinjaman</span>
                </a>
                <a href="{{ Route::has('loans.overdue.manage') ? route('loans.overdue.manage') : url('/kelola-tunggakan') }}" class="{{ $itemClass(request()->routeIs('loans.overdue*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.overdue*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Kelola Tunggakan</span>
                </a>
                <a href="{{ route('payments.dashboard') }}" class="{{ $itemClass(request()->routeIs('payments.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('payments.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Gateway & Bayar</span>
                </a>
            </div>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Akuntansi & Finansial</span>
            <div class="space-y-1">
                <a href="{{ route('accounting.journals') }}" class="{{ $itemClass(request()->routeIs('accounting.journals')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('accounting.journals')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Jurnal Umum</span>
                </a>
                <a href="{{ route('accounting.ledger') }}" class="{{ $itemClass(request()->routeIs('accounting.ledger')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('accounting.ledger')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    <span>Buku Besar</span>
                </a>
                <a href="{{ route('accounting.trial_balance') }}" class="{{ $itemClass(request()->routeIs('accounting.trial_balance')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('accounting.trial_balance')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    <span>Neraca Saldo</span>
                </a>
                <a href="{{ route('accounting.shu') }}" class="{{ $itemClass(request()->routeIs('accounting.shu*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('accounting.shu*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Tutup Buku & SHU</span>
                </a>
                <a href="{{ route('reports.savings') }}" class="{{ $itemClass(request()->routeIs('reports.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('reports.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Finansial</span>
                </a>
            </div>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Pengaturan Sistem</span>
            <div class="space-y-1">
                {{-- FITUR ADMIN: KELOLA BILLBOARD PENGUMUMAN --}}
                <a href="{{ route('admin.billboards.index') }}" class="{{ $itemClass(request()->routeIs('admin.billboards.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('admin.billboards.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                    <div class="flex items-center justify-between flex-1">
                        <span>Kelola Billboard</span>
                        <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold uppercase bg-emerald-500/25 text-emerald-200 border border-emerald-400/40 ml-1">Admin</span>
                    </div>
                </a>
                <a href="{{ route('loans.products') }}" class="{{ $itemClass(request()->routeIs('loans.products*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.products*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span>Katalog Produk</span>
                </a>
                <a href="{{ route('loans.simulation') }}" class="{{ $itemClass(request()->routeIs('loans.simulation')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.simulation')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Simulasi Pinjaman</span>
                </a>
            </div>
        </div>
    </div>

@elseif($role === 'manager')
    {{-- ========================================================================= --}}
    {{-- SIDEBAR ROLE: MANAJER --}}
    {{-- ========================================================================= --}}
    <div class="space-y-4">
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Menu Utama</span>
            <a href="{{ route('dashboard') }}" class="{{ $itemClass(request()->routeIs('dashboard')) }}">
                <svg class="{{ $iconClass(request()->routeIs('dashboard')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dasbor Manajer</span>
            </a>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Pengawasan Layanan</span>
            <div class="space-y-1">
                <a href="{{ route('members.index') }}" class="{{ $itemClass(request()->routeIs('members.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('members.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Data & Verifikasi Anggota</span>
                </a>
                <a href="{{ route('savings.index') }}" class="{{ $itemClass(request()->routeIs('savings.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('savings.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Rekap Simpanan</span>
                </a>
            </div>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Pembiayaan & Kredit</span>
            <div class="space-y-1">
                <a href="{{ route('loans.applications') }}" class="{{ $itemClass(request()->routeIs('loans.applications*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.applications*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Persetujuan Pinjaman</span>
                </a>
                <a href="{{ route('loans.manage') }}" class="{{ $itemClass(request()->routeIs('loans.manage*') || request()->routeIs('loans.index')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.manage*') || request()->routeIs('loans.index')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Portofolio Pinjaman</span>
                </a>
                <a href="{{ Route::has('loans.overdue.manage') ? route('loans.overdue.manage') : url('/kelola-tunggakan') }}" class="{{ $itemClass(request()->routeIs('loans.overdue*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.overdue*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Pengawasan Tunggakan</span>
                </a>
                <a href="{{ route('payments.dashboard') }}" class="{{ $itemClass(request()->routeIs('payments.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('payments.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Gateway & Settlement</span>
                </a>
            </div>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Akuntansi & Audit</span>
            <div class="space-y-1">
                <a href="{{ route('accounting.journals') }}" class="{{ $itemClass(request()->routeIs('accounting.journals')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('accounting.journals')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Jurnal Umum</span>
                </a>
                <a href="{{ route('accounting.ledger') }}" class="{{ $itemClass(request()->routeIs('accounting.ledger')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('accounting.ledger')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"/></svg>
                    <span>Buku Besar</span>
                </a>
                <a href="{{ route('accounting.trial_balance') }}" class="{{ $itemClass(request()->routeIs('accounting.trial_balance')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('accounting.trial_balance')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                    <span>Neraca Saldo</span>
                </a>
                <a href="{{ route('accounting.shu') }}" class="{{ $itemClass(request()->routeIs('accounting.shu*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('accounting.shu*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span>Perhitungan SHU</span>
                </a>
                <a href="{{ route('reports.savings') }}" class="{{ $itemClass(request()->routeIs('reports.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('reports.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Finansial</span>
                </a>
            </div>
        </div>
    </div>

@elseif($role === 'analyst')
    {{-- ========================================================================= --}}
    {{-- SIDEBAR ROLE: ANALIS / KOMITE KREDIT --}}
    {{-- ========================================================================= --}}
    <div class="space-y-4">
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Menu Utama</span>
            <a href="{{ route('dashboard') }}" class="{{ $itemClass(request()->routeIs('dashboard')) }}">
                <svg class="{{ $iconClass(request()->routeIs('dashboard')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dasbor Analis</span>
            </a>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Analisis & Verifikasi</span>
            <div class="space-y-1">
                <a href="{{ route('members.index') }}" class="{{ $itemClass(request()->routeIs('members.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('members.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Verifikasi Berkas Anggota</span>
                </a>
                <a href="{{ route('loans.applications') }}" class="{{ $itemClass(request()->routeIs('loans.applications*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.applications*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Evaluasi Pengajuan Kredit</span>
                </a>
                <a href="{{ route('loans.manage') }}" class="{{ $itemClass(request()->routeIs('loans.manage*') || request()->routeIs('loans.index')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.manage*') || request()->routeIs('loans.index')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Portofolio Kredit</span>
                </a>
            </div>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Laporan Kualitas Kredit</span>
            <div class="space-y-1">
                <a href="{{ route('reports.overdue') }}" class="{{ $itemClass(request()->routeIs('reports.overdue')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('reports.overdue')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Laporan Kolektibilitas / NPL</span>
                </a>
                <a href="{{ route('reports.loans') }}" class="{{ $itemClass(request()->routeIs('reports.loans')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('reports.loans')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Pembiayaan</span>
                </a>
            </div>
        </div>
    </div>

@elseif($role === 'teller')
    {{-- ========================================================================= --}}
    {{-- SIDEBAR ROLE: TELLER & KASIR --}}
    {{-- ========================================================================= --}}
    <div class="space-y-4">
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Menu Utama</span>
            <a href="{{ route('dashboard') }}" class="{{ $itemClass(request()->routeIs('dashboard')) }}">
                <svg class="{{ $iconClass(request()->routeIs('dashboard')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dasbor Loket Teller</span>
            </a>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Layanan Kasir & Loket</span>
            <div class="space-y-1">
                <a href="{{ route('members.index') }}" class="{{ $itemClass(request()->routeIs('members.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('members.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span>Loket Anggota</span>
                </a>
                <a href="{{ route('savings.index') }}" class="{{ $itemClass(request()->routeIs('savings.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('savings.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Setoran & Penarikan Simpanan</span>
                </a>
                <a href="{{ route('loans.manage') }}" class="{{ $itemClass(request()->routeIs('loans.manage*') || request()->routeIs('loans.index')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.manage*') || request()->routeIs('loans.index')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Bayar Angsuran Pinjaman</span>
                </a>
                <a href="{{ route('payments.dashboard') }}" class="{{ $itemClass(request()->routeIs('payments.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('payments.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    <span>Kasir Gateway Digital</span>
                </a>
            </div>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Laporan Kasir</span>
            <div class="space-y-1">
                <a href="{{ route('reports.cash_flow') }}" class="{{ $itemClass(request()->routeIs('reports.cash_flow')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('reports.cash_flow')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Kas Harian</span>
                </a>
            </div>
        </div>
    </div>

@elseif($role === 'member')
    {{-- ========================================================================= --}}
    {{-- SIDEBAR ROLE: ANGGOTA KOPERASI --}}
    {{-- ========================================================================= --}}
    <div class="space-y-4">
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Portal Anggota</span>
            <a href="{{ route('dashboard') }}" class="{{ $itemClass(request()->routeIs('dashboard')) }}">
                <svg class="{{ $iconClass(request()->routeIs('dashboard')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dasbor Saya</span>
            </a>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Finansial Anggota</span>
            <div class="space-y-1">
                <a href="{{ route('savings.index') }}" class="{{ $itemClass(request()->routeIs('savings.*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('savings.*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span>Rekening Simpanan</span>
                </a>
                <a href="{{ route('loans.my') }}" class="{{ $itemClass(request()->routeIs('loans.my*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.my*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Pinjaman Saya</span>
                </a>
                <a href="{{ route('loans.apply') }}" class="{{ $itemClass(request()->routeIs('loans.apply*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.apply*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Ajukan Pinjaman Baru</span>
                </a>
                <a href="{{ route('loans.simulation') }}" class="{{ $itemClass(request()->routeIs('loans.simulation')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.simulation')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <span>Simulasi Pinjaman</span>
                </a>
            </div>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Akun & Dokumen</span>
            <div class="space-y-1">
                <a href="{{ route('members.profile') }}" class="{{ $itemClass(request()->routeIs('members.profile')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('members.profile')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Profil & Berkas Anggota</span>
                </a>
            </div>
        </div>
    </div>

@else
    {{-- ========================================================================= --}}
    {{-- SIDEBAR ROLE DEFAULT / KOLEKTOR LAPANGAN --}}
    {{-- ========================================================================= --}}
    <div class="space-y-4">
        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Menu Utama</span>
            <a href="{{ route('dashboard') }}" class="{{ $itemClass(request()->routeIs('dashboard')) }}">
                <svg class="{{ $iconClass(request()->routeIs('dashboard')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span>Dasbor Petugas</span>
            </a>
        </div>

        <div>
            <span class="px-3 text-[10px] font-extrabold uppercase tracking-wider text-emerald-400/80 block mb-1.5">Penagihan & Kolektibilitas</span>
            <div class="space-y-1">
                <a href="{{ Route::has('loans.overdue.manage') ? route('loans.overdue.manage') : url('/kelola-tunggakan') }}" class="{{ $itemClass(request()->routeIs('loans.overdue*')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.overdue*')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Kelola Tunggakan & Janji Bayar</span>
                </a>
                <a href="{{ route('loans.manage') }}" class="{{ $itemClass(request()->routeIs('loans.manage*') || request()->routeIs('loans.index')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('loans.manage*') || request()->routeIs('loans.index')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    <span>Daftar Pinjaman</span>
                </a>
                <a href="{{ route('reports.overdue') }}" class="{{ $itemClass(request()->routeIs('reports.overdue')) }}">
                    <svg class="{{ $iconClass(request()->routeIs('reports.overdue')) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Laporan Kolektibilitas</span>
                </a>
            </div>
        </div>
    </div>
@endif
