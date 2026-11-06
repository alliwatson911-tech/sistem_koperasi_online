@extends('layouts.app')

@section('title', 'Admin Dashboard V2 - KSP Sahabat Sejahtera')

@section('content')
<!-- Custom Styles & Animations Khusus Admin Dashboard UI V2 (Clean White Theme, Framer Motion-inspired Transitions) -->
<style>
    @keyframes barRise {
        from { transform: scaleY(0); }
        to { transform: scaleY(1); }
    }
    .bar-rise-anim {
        transform-origin: bottom;
        animation: barRise 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
    .chart-grid-line {
        background-image: linear-gradient(to right, #f1f5f9 1px, transparent 1px);
        background-size: 100% 1px;
    }
    .metric-card-hover {
        transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .metric-card-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px -6px rgba(15, 23, 42, 0.08), 0 4px 10px -2px rgba(15, 23, 42, 0.03);
    }
</style>

<div class="space-y-6">
    <!-- ========================================================================= -->
    <!-- 1. HEADER SECTION & GLOBAL SEARCH (ADMIN DASHBOARD UI V2)                 -->
    <!-- ========================================================================= -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-emerald-50 text-emerald-800 text-[11px] font-extrabold border border-emerald-200/90 shadow-2xs">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 animate-pulse"></span>
                    Admin Dashboard UI V2
                </span>
                <span class="text-xs text-slate-400 font-semibold">&bull; Clean White Theme &bull; Panel Eksekutif</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <span>Panel Operasional Koperasi</span>
                <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-md">V2.0</span>
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                KSP Sahabat Sejahtera &bull; Ringkasan performa dan antrean layanan harian
            </p>
        </div>

        <!-- Quick Executive Actions & Kelola Billboard -->
        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ route('admin.billboards.index') }}" class="btn btn-secondary btn-sm rounded-xl px-3.5 py-2 text-xs font-bold shadow-2xs flex items-center gap-1.5 hover:bg-slate-100 transition-all border border-slate-200" title="Kelola Billboard Pengumuman Resmi">
                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                <span>Kelola Billboard</span>
            </a>

            <a href="{{ route('loans.applications') }}" class="btn btn-primary btn-sm rounded-xl px-3.5 py-2 text-xs font-bold shadow-xs flex items-center gap-1.5 bg-[#047857] hover:bg-[#065f46] text-white transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Antrean Pinjaman</span>
            </a>

            <a href="{{ route('accounting.journals') }}" class="btn btn-secondary btn-sm rounded-xl px-3 py-2 text-xs font-bold shadow-2xs flex items-center gap-1.5 hover:bg-slate-100 transition-all border border-slate-200">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                <span>Jurnal Umum</span>
            </a>
        </div>
    </div>

    <!-- Executive Search Bar (Seperti pada video paulcodes) -->
    <div class="bg-white rounded-2xl p-3 border border-slate-200/90 shadow-2xs flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center shrink-0 border border-slate-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
        <input type="text" id="adminGlobalSearch" 
               placeholder="Cari data anggota, pengajuan pinjaman, rekening simpanan, atau nomor kontrak..." 
               class="w-full text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-transparent border-none outline-none focus:ring-0">
        <div class="hidden sm:flex items-center gap-1.5 shrink-0 pr-2">
            <kbd class="px-2 py-0.5 text-[10px] font-mono font-semibold text-slate-400 bg-slate-100 border border-slate-200 rounded">ESC</kbd>
            <span class="text-3xs text-slate-400">untuk reset</span>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. 4 ELEGANT ANALYTICS CARDS (CLEAN WHITE THEME, MINIMAL & PREMIUM)       -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- CARD 1: TOTAL DANA SIMPANAN ANGGOTA (LIKUIDITAS KAS) -->
        <a href="{{ route('savings.index') }}" 
           class="bg-white rounded-2xl p-5 border border-slate-200/90 border-l-4 border-l-emerald-600 shadow-xs metric-card-hover flex flex-col justify-between group no-underline text-slate-800 cursor-pointer"
           title="Buka Kelola Rekening Simpanan">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 group-hover:bg-emerald-600 group-hover:text-white flex items-center justify-center shrink-0 border border-emerald-100 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/80">
                        <svg class="w-2.5 h-2.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        <span>+12.4%</span>
                    </span>
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 group-hover:text-emerald-800 transition-colors">
                    Total Dana Simpanan Anggota
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight my-1 group-hover:text-emerald-950 transition-colors">
                    Rp {{ number_format($stats['total_savings'] / 1000000, 1) }} <span class="text-sm sm:text-base font-bold text-slate-400">jt</span>
                </div>
                <div class="font-mono text-[11px] font-semibold text-emerald-800 bg-emerald-50/70 inline-block px-2 py-0.5 rounded-md border border-emerald-100">
                    Rp {{ number_format($stats['total_savings'], 0, ',', '.') }}
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Likuiditas Kas Koperasi</span>
                <span class="font-bold text-emerald-800 group-hover:translate-x-0.5 transition-transform flex items-center gap-1">
                    <span>Kelola</span>
                    <span class="text-3xs">&rarr;</span>
                </span>
            </div>
        </a>

        <!-- CARD 2: TOTAL ANGGOTA AKTIF -->
        <a href="{{ route('members.index') }}" 
           class="bg-white rounded-2xl p-5 border border-slate-200/90 border-l-4 border-l-sky-600 shadow-xs metric-card-hover flex flex-col justify-between group no-underline text-slate-800 cursor-pointer"
           title="Buka Kelola Anggota">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-700 group-hover:bg-sky-600 group-hover:text-white flex items-center justify-center shrink-0 border border-sky-100 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    @if($stats['pending_members'] > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200/80">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-ping"></span>
                            <span>{{ $stats['pending_members'] }} pending</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-800 border border-sky-200/80">
                            <span>Verifikasi 100%</span>
                        </span>
                    @endif
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 group-hover:text-sky-800 transition-colors">
                    TOTAL ANGGOTA AKTIF
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight my-1 group-hover:text-sky-950 transition-colors">
                    {{ number_format($stats['active_members']) }} <span class="text-sm sm:text-base font-semibold text-slate-400">/ {{ $stats['total_members'] }} total</span>
                </div>
                <div class="text-[11px] text-slate-500 truncate">
                    Pertumbuhan anggota aktif &amp; terdaftar
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Buku Registrasi</span>
                <span class="font-bold text-sky-800 group-hover:translate-x-0.5 transition-transform flex items-center gap-1">
                    <span>Lihat Anggota</span>
                    <span class="text-3xs">&rarr;</span>
                </span>
            </div>
        </a>

        <!-- CARD 3: SISA POKOK PINJAMAN (OUTSTANDING PEMBIAYAAN) -->
        <a href="{{ route('loans.manage') }}" 
           class="bg-white rounded-2xl p-5 border border-slate-200/90 border-l-4 border-l-teal-600 shadow-xs metric-card-hover flex flex-col justify-between group no-underline text-slate-800 cursor-pointer"
           title="Buka Portofolio Pinjaman">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-700 group-hover:bg-teal-600 group-hover:text-white flex items-center justify-center shrink-0 border border-teal-100 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-teal-50 text-teal-800 border border-teal-200/80">
                        <span>Lancar {{ $repaymentRate }}%</span>
                    </span>
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 group-hover:text-teal-800 transition-colors">
                    SISA POKOK PINJAMAN
                </div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight my-1 group-hover:text-teal-950 transition-colors">
                    Rp {{ number_format($stats['total_loans_active'] / 1000000, 1) }} <span class="text-sm sm:text-base font-bold text-slate-400">jt</span>
                </div>
                <div class="font-mono text-[11px] font-semibold text-slate-600 bg-slate-50 inline-block px-2 py-0.5 rounded-md border border-slate-200">
                    Rp {{ number_format($stats['total_loans_active'], 0, ',', '.') }}
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium truncate">Portofolio Pembiayaan</span>
                <span class="font-bold text-teal-800 group-hover:translate-x-0.5 transition-transform flex items-center gap-1 shrink-0">
                    <span>Portofolio</span>
                    <span class="text-3xs">&rarr;</span>
                </span>
            </div>
        </a>

        <!-- CARD 4: PINJAMAN MENUNGGAK (NPL) -->
        <a href="{{ route('reports.overdue') }}" 
           class="bg-white rounded-2xl p-5 border border-slate-200/90 border-l-4 border-l-rose-600 shadow-xs metric-card-hover flex flex-col justify-between group no-underline text-slate-800 cursor-pointer"
           title="Buka Laporan Kolektibilitas / NPL">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-700 group-hover:bg-rose-600 group-hover:text-white flex items-center justify-center shrink-0 border border-rose-100 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold {{ $stats['total_loans_overdue'] > 0 ? 'bg-rose-100 text-rose-800 border border-rose-300' : 'bg-emerald-50 text-emerald-800 border border-emerald-200' }}">
                        <span>{{ $stats['total_loans_overdue'] > 0 ? 'Perlu Follow-up' : 'NPL 0.0%' }}</span>
                    </span>
                </div>
                <div class="text-[11px] font-bold uppercase tracking-wider text-slate-500 group-hover:text-rose-800 transition-colors">
                    PINJAMAN MENUNGGAK (NPL)
                </div>
                <div class="text-2xl sm:text-3xl font-black text-rose-600 tracking-tight my-1">
                    Rp {{ number_format($stats['total_loans_overdue'] / 1000000, 1) }} <span class="text-sm sm:text-base font-bold text-rose-400">jt</span>
                </div>
                <div class="text-[11px] text-slate-500 truncate">
                    {{ $stats['total_loans_overdue'] > 0 ? 'Perlu tindakan penagihan kredit' : 'Kolektibilitas kredit lancar (0 tunggakan)' }}
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-medium">Laporan Kolektibilitas</span>
                <span class="font-bold text-rose-700 group-hover:translate-x-0.5 transition-transform flex items-center gap-1">
                    <span>Audit NPL</span>
                    <span class="text-3xs">&rarr;</span>
                </span>
            </div>
        </a>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. ANALYTICS & CHARTS (BAR CHART BULANAN & CIRCULAR PROGRESS GAUGE)       -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
        <!-- LEFT: BAR CHART BULANAN (SEPERTI PADA VIDEO: COMMISSION BY MONTH) -->
        <div class="lg:col-span-8 bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <!-- Header Chart -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Tren Arus Finansial Bulanan</h2>
                            <span class="text-[11px] font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                                6 Bulan Terakhir
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">Pertumbuhan akumulasi simpanan masuk vs pembiayaan kredit yang disalurkan</p>
                    </div>

                    <!-- Legend Indikator -->
                    <div class="flex items-center gap-4 text-xs font-semibold">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-md bg-[#047857]"></span>
                            <span class="text-slate-600">Simpanan (Kas)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-md bg-[#38bdf8]"></span>
                            <span class="text-slate-600">Pinjaman Disalurkan</span>
                        </div>
                    </div>
                </div>

                <!-- Visual Bar Chart Container -->
                <div class="pt-6 pb-2">
                    <!-- Sumbu Y & Grid Background -->
                    <div class="relative h-56 sm:h-64 w-full flex items-end justify-between gap-2 sm:gap-6 px-2 sm:px-4 border-b border-slate-200">
                        <!-- Grid Lines Horizontal -->
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-40">
                            <div class="border-b border-dashed border-slate-200 w-full"></div>
                            <div class="border-b border-dashed border-slate-200 w-full"></div>
                            <div class="border-b border-dashed border-slate-200 w-full"></div>
                            <div class="border-b border-dashed border-slate-200 w-full"></div>
                        </div>

                        <!-- Bar Columns Per Bulan -->
                        @php
                            $maxVal = max(10, $stats['total_savings'] / 1000000 * 1.25);
                        @endphp

                        @foreach($monthlyChart as $bar)
                            @php
                                $savingsHeight = min(95, max(15, ($bar['savings'] / $maxVal) * 100));
                                $loansHeight = min(95, max(12, ($bar['loans'] / $maxVal) * 100));
                            @endphp
                            <div class="relative flex-1 flex flex-col items-center h-full justify-end group z-10">
                                <!-- Tooltip Hover -->
                                <div class="absolute -top-12 opacity-0 group-hover:opacity-100 transition-opacity bg-slate-900 text-white text-[10px] rounded-lg py-1 px-2 pointer-events-none whitespace-nowrap shadow-lg z-20">
                                    <div class="font-bold">{{ $bar['label'] }}</div>
                                    <div class="text-emerald-300">Simpanan: Rp {{ $bar['savings'] }} jt</div>
                                    <div class="text-sky-300">Pinjaman: Rp {{ $bar['loans'] }} jt</div>
                                </div>

                                <!-- Dual Bars Side-by-Side -->
                                <div class="w-full flex items-end justify-center gap-1 sm:gap-2 h-full pb-1">
                                    <!-- Bar Simpanan (Emerald) -->
                                    <div class="w-3 sm:w-5 bg-[#047857] hover:bg-[#065f46] rounded-t-lg transition-all duration-300 bar-rise-anim cursor-pointer shadow-xs"
                                         style="height: {{ $savingsHeight }}%;"></div>
                                    <!-- Bar Pinjaman (Sky Blue) -->
                                    <div class="w-3 sm:w-5 bg-[#38bdf8] hover:bg-[#0284c7] rounded-t-lg transition-all duration-300 bar-rise-anim cursor-pointer shadow-xs"
                                         style="height: {{ $loansHeight }}%;"></div>
                                </div>

                                <!-- Label Sumbu X -->
                                <span class="text-[11px] sm:text-xs font-bold text-slate-500 group-hover:text-slate-900 transition-colors mt-2">
                                    {{ $bar['month'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Catatan Kinerja Arus Kas -->
            <div class="pt-4 mt-2 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-1.5 text-emerald-800 font-semibold">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span>Likuiditas kas bertumbuh secara organik seimbang dengan plafon kredit produktif.</span>
                </div>
                <span class="text-slate-400 font-mono text-[11px] hidden sm:inline">Basis Real-Time</span>
            </div>
        </div>

        <!-- RIGHT: CIRCULAR PROGRESS GAUGE (SEPERTI PADA VIDEO: DONUT PROGRESS CHART 52%) -->
        <div class="lg:col-span-4 bg-white rounded-2xl p-6 border border-slate-200/90 shadow-xs flex flex-col justify-between">
            <div>
                <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900 tracking-tight">Rasio Kesehatan Kas</h2>
                        <p class="text-xs text-slate-500">Keseimbangan kas likuid vs portofolio pinjaman</p>
                    </div>
                    <span class="badge badge-success text-[10px] px-2 py-0.5 font-bold">Prima</span>
                </div>

                <!-- Donut / Circular Gauge Chart -->
                <div class="my-6 flex flex-col items-center justify-center relative">
                    <div class="relative w-44 h-44 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                            <!-- Background Track Circle -->
                            <circle cx="50" cy="50" r="40" stroke="#f1f5f9" stroke-width="9" fill="transparent" />
                            <!-- Active Progress Circle (Emerald / Cyan) -->
                            @php
                                $circumference = 2 * 3.14159 * 40;
                                $dashOffset = $circumference - ($liquidityPercentage / 100) * $circumference;
                            @endphp
                            <circle cx="50" cy="50" r="40" 
                                    stroke="#047857" 
                                    stroke-width="9" 
                                    stroke-linecap="round" 
                                    stroke-dasharray="{{ $circumference }}" 
                                    stroke-dashoffset="{{ $dashOffset }}" 
                                    fill="transparent" 
                                    class="transition-all duration-1000 ease-out" />
                        </svg>

                        <!-- Center Metrics -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <span class="text-3xl font-black text-slate-900 tracking-tight">{{ $liquidityPercentage }}%</span>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 mt-0.5">Rasio Likuiditas</span>
                            <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full mt-1 border border-emerald-100">
                                Sangat Sehat
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 2 Side-by-Side Stat Boxes (Persis seperti $15.2K & $13.0K di video paulcodes) -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-100 text-center">
                        <div class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Kas Likuid</div>
                        <div class="text-base font-black text-slate-900 mt-0.5">
                            Rp {{ number_format($stats['total_savings'] / 1000000, 1) }} jt
                        </div>
                        <div class="text-[10px] text-emerald-700 font-semibold mt-0.5">Dana Simpanan</div>
                    </div>

                    <div class="bg-slate-50/80 rounded-xl p-3 border border-slate-100 text-center">
                        <div class="text-[10px] font-extrabold uppercase text-slate-400 tracking-wider">Beredar</div>
                        <div class="text-base font-black text-slate-900 mt-0.5">
                            Rp {{ number_format($stats['total_loans_active'] / 1000000, 1) }} jt
                        </div>
                        <div class="text-[10px] text-teal-700 font-semibold mt-0.5">Sisa Pokok</div>
                    </div>
                </div>
            </div>

            <div class="pt-4 mt-3 border-t border-slate-100 text-center">
                <a href="{{ route('accounting.ledger') }}" class="text-xs font-bold text-emerald-800 hover:text-emerald-950 inline-flex items-center gap-1 group">
                    <span>Audit Buku Besar Kas Utama</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. RECENT ACTIVITY & OPERATIONS TABLE (TABBED, BERSIH, MINIMAL & PREMIUM) -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs overflow-hidden">
        <!-- Table Header & Tab Switcher (Seperti "Agent payouts" di video paulcodes) -->
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">Antrean Operasional &amp; Evaluasi Layanan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Manajemen antrean pengajuan kredit, verifikasi berkas anggota, dan pencairan dana</p>
            </div>

            <!-- Tab Buttons -->
            <div class="inline-flex p-1 bg-slate-100/80 rounded-xl border border-slate-200/60 text-xs font-bold self-start md:self-auto">
                <button type="button" onclick="switchAdminTab('tab-loans')" id="btn-tab-loans" 
                        class="px-3.5 py-1.5 rounded-lg bg-white text-slate-900 shadow-2xs transition-all flex items-center gap-1.5">
                    <span>Pengajuan Kredit</span>
                    <span class="px-1.5 py-0.2 rounded-full bg-emerald-100 text-emerald-800 text-3xs">{{ $stats['pending_loan_applications'] }}</span>
                </button>
                <button type="button" onclick="switchAdminTab('tab-members')" id="btn-tab-members" 
                        class="px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition-all flex items-center gap-1.5">
                    <span>Verifikasi Anggota</span>
                    <span class="px-1.5 py-0.2 rounded-full bg-slate-200 text-slate-700 text-3xs">{{ $stats['pending_members'] }}</span>
                </button>
                <button type="button" onclick="switchAdminTab('tab-disburse')" id="btn-tab-disburse" 
                        class="px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition-all flex items-center gap-1.5">
                    <span>Siap Cair</span>
                    <span class="px-1.5 py-0.2 rounded-full bg-teal-100 text-teal-800 text-3xs">{{ $stats['ready_to_disburse_loans'] }}</span>
                </button>
            </div>
        </div>

        <!-- TAB 1: PENGAJUAN PINJAMAN MASUK -->
        <div id="tab-loans" class="admin-tab-pane">
            @if($pendingApplications->isEmpty())
                <div class="p-12 text-center text-slate-400 text-xs flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="font-bold text-slate-600 text-sm">Tidak Ada Pengajuan Kredit Menunggu</span>
                    <span class="text-slate-400 mt-1">Seluruh pengajuan pinjaman anggota telah diproses oleh tim analis &amp; manajer.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="py-3.5 px-5">No. Pengajuan</th>
                                <th class="py-3.5 px-4">Nama Pemohon &amp; Produk</th>
                                <th class="py-3.5 px-4">Plafon Pinjaman</th>
                                <th class="py-3.5 px-4">Tahapan Evaluasi</th>
                                <th class="py-3.5 px-5 text-right">Tindakan Eksekutif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pendingApplications as $app)
                            <tr class="hover:bg-slate-50/70 transition-colors searchable-row">
                                <td class="py-3.5 px-5 font-mono font-bold text-slate-800">
                                    {{ $app->application_no }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $app->member->user->name }}</div>
                                    <div class="text-slate-400 text-[11px] font-medium">{{ $app->loanProduct->name }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-black text-slate-900 text-sm">
                                    Rp {{ number_format($app->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold {{ $app->status === 'submitted' ? 'bg-amber-50 text-amber-800 border border-amber-200' : 'bg-sky-50 text-sky-800 border border-sky-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $app->status === 'submitted' ? 'bg-amber-500' : 'bg-sky-500' }}"></span>
                                        {{ $app->status === 'submitted' ? 'PENGAJUAN BARU' : 'ANALISIS KREDIT' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <a href="{{ route('loans.applications.show', $app->id) }}" class="btn btn-primary btn-sm rounded-lg px-3 py-1.5 text-xs font-bold shadow-2xs">
                                        Periksa Berkas &rarr;
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- TAB 2: CALON ANGGOTA MENUNGGU VERIFIKASI -->
        <div id="tab-members" class="admin-tab-pane hidden">
            @if($pendingVerifications->isEmpty())
                <div class="p-12 text-center text-slate-400 text-xs flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span class="font-bold text-slate-600 text-sm">Semua Anggota Telah Terverifikasi</span>
                    <span class="text-slate-400 mt-1">Tidak ada calon anggota yang menunggu persetujuan KYC saat ini.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="py-3.5 px-5">Calon Anggota</th>
                                <th class="py-3.5 px-4">NIK</th>
                                <th class="py-3.5 px-4">Pekerjaan</th>
                                <th class="py-3.5 px-4">Dokumen Pendukung</th>
                                <th class="py-3.5 px-5 text-right">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pendingVerifications as $pm)
                            <tr class="hover:bg-slate-50/70 transition-colors searchable-row">
                                <td class="py-3.5 px-5">
                                    <div class="font-bold text-slate-900 text-sm">{{ $pm->user->name }}</div>
                                    <div class="text-slate-400 font-mono text-[11px]">{{ $pm->user->phone }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-slate-700 font-medium">{{ $pm->nik }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ $pm->occupation }}</td>
                                <td class="py-3.5 px-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md bg-blue-50 text-blue-800 text-[10px] font-bold border border-blue-200">
                                        {{ $pm->documents->count() }} Berkas KYC
                                    </span>
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <a href="{{ route('members.show', $pm->id) }}" class="btn btn-secondary btn-sm rounded-lg px-3 py-1.5 text-xs font-bold shadow-2xs hover:bg-slate-200">
                                        Verifikasi Anggota &rarr;
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- TAB 3: PINJAMAN SIAP DICAIRKAN -->
        <div id="tab-disburse" class="admin-tab-pane hidden">
            @if($awaitingDisbursementLoans->isEmpty())
                <div class="p-12 text-center text-slate-400 text-xs flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="font-bold text-slate-600 text-sm">Tidak Ada Pinjaman Menunggu Pencairan</span>
                    <span class="text-slate-400 mt-1">Seluruh akad kredit yang telah disetujui sudah disalurkan oleh kasir teller.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-teal-50/70 border-b border-teal-100 text-teal-800 font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="py-3.5 px-5">No. Pinjaman</th>
                                <th class="py-3.5 px-4">Peminjam</th>
                                <th class="py-3.5 px-4">Plafon Pokok</th>
                                <th class="py-3.5 px-4">Netto Dicairkan</th>
                                <th class="py-3.5 px-5 text-right">Aksi Teller / Kasir</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-teal-50">
                            @foreach($awaitingDisbursementLoans as $abl)
                            <tr class="hover:bg-teal-50/40 transition-colors searchable-row">
                                <td class="py-3.5 px-5 font-mono font-bold text-slate-800">{{ $abl->loan_no }}</td>
                                <td class="py-3.5 px-4">
                                    <div class="font-bold text-slate-900 text-sm">{{ $abl->member->user->name }}</div>
                                    <div class="text-slate-400 font-mono text-[11px]">{{ $abl->member->member_no }}</div>
                                </td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800">
                                    Rp {{ number_format($abl->principal_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-4 font-black text-teal-800 text-sm">
                                    Rp {{ number_format($abl->principal_amount - $abl->total_admin_fee, 0, ',', '.') }}
                                </td>
                                <td class="py-3.5 px-5 text-right">
                                    <form action="{{ route('loans.disburse', $abl->id) }}" method="POST" class="inline" onsubmit="return confirm('Cairkan dana pinjaman sebesar Rp {{ number_format($abl->principal_amount - $abl->total_admin_fee, 0, ',', '.') }}?');">
                                        @csrf
                                        <input type="hidden" name="payment_method" value="transfer">
                                        <button type="submit" class="btn btn-success btn-sm rounded-lg px-3 py-1.5 text-xs font-bold shadow-2xs">
                                            Cairkan Dana
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Script Interaktif Tab & Filter Cepat Admin Dashboard UI V2 -->
<script>
    function switchAdminTab(tabId) {
        document.querySelectorAll('.admin-tab-pane').forEach(el => el.classList.add('hidden'));
        const activePane = document.getElementById(tabId);
        if (activePane) activePane.classList.remove('hidden');

        // Reset tab button states
        ['tab-loans', 'tab-members', 'tab-disburse'].forEach(id => {
            const btn = document.getElementById('btn-' + id);
            if (btn) {
                if (id === tabId) {
                    btn.className = 'px-3.5 py-1.5 rounded-lg bg-white text-slate-900 shadow-2xs transition-all flex items-center gap-1.5 font-bold';
                } else {
                    btn.className = 'px-3.5 py-1.5 rounded-lg text-slate-500 hover:text-slate-900 transition-all flex items-center gap-1.5 font-bold';
                }
            }
        });
    }

    // Live search input filtering across tables
    document.getElementById('adminGlobalSearch')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase().trim();
        const rows = document.querySelectorAll('.searchable-row');
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(query) ? '' : 'none';
        });
    });

    // ESC to clear search
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const search = document.getElementById('adminGlobalSearch');
            if (search) {
                search.value = '';
                search.dispatchEvent(new Event('input'));
            }
        }
    });
</script>
@endsection
