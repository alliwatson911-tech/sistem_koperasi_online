@extends('layouts.app')

@section('title', (auth()->user()->isAdmin() ? 'Admin' : 'Manajer') . ' Dashboard V2 - KSP Sahabat Sejahtera')

@section('content')
<!-- Custom Styles: Palet Biru & Hijau (Kepercayaan & Pertumbuhan) - Biru Navy #1B263B/#0F4C81, Hijau Toska #2A9D8F, Sage #A3B18A, Off-white #F8F5F0/Putih #FFFFFF, Gold #D4AF37 -->
<style>
    /* ========================================================================= */
    /* PALET BIRU & HIJAU ELEGAN (KEPERCAYAAN & PERTUMBUHAN FINANSIAL KOPERASI)   */
    /* ========================================================================= */
    :root {
        --ksp-navy-dark: #1B263B;        /* Warna Utama: Biru Navy Tua (Aman, Stabil, Profesional) */
        --ksp-blue-deep: #0F4C81;        /* Warna Utama 2: Biru Tua Finansial */
        --ksp-teal-growth: #2A9D8F;      /* Warna Sekunder: Hijau Toska / Teal (Pertumbuhan Ekonomi) */
        --ksp-sage-refresh: #A3B18A;     /* Warna Pendukung: Hijau Sage (Penyegaran & Kesejahteraan) */
        --ksp-bg-offwhite: #F8F5F0;      /* Warna Latar: Off-white Hangat Mewah */
        --ksp-card-white: #FFFFFF;       /* Warna Kartu: Putih Bersih */
        --ksp-gold-accent: #D4AF37;      /* Warna Aksen: Kuning Emas / Gold (Tombol CTA & Prioritas) */
        --ksp-gold-hover: #C59F2D;
        --ksp-text-navy: #1B263B;
        --ksp-text-muted: #5C6B73;
        --ksp-border-light: #E8E2D8;
    }

    /* BENTUK: Pembungkus Elegan Kanvas Off-white (#F8F5F0) */
    .admin-v2-wrapper {
        background: var(--ksp-bg-offwhite);
        border-radius: 28px;
        padding: 1.5rem;
        border: 1px solid var(--ksp-border-light);
        box-shadow: 0 4px 28px -4px rgba(27, 38, 59, 0.05);
    }
    @media (min-width: 640px) {
        .admin-v2-wrapper {
            padding: 2rem;
        }
    }

    /* HEADER: Bilah Eksekutif Biru Navy Tua (#1B263B & #0F4C81) */
    .admin-v2-header-glass {
        background: var(--ksp-navy-dark);
        color: #ffffff;
        border-radius: 24px;
        padding: 1.5rem 1.75rem;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 14px 32px -4px rgba(27, 38, 59, 0.35);
    }

    /* KONTROL: Search Bar Pill Halus di Header */
    .admin-v2-search-input {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.22);
        color: #ffffff;
        border-radius: 9999px;
        padding: 0.6rem 1.25rem 0.6rem 2.85rem;
        transition: all 0.22s ease;
    }
    .admin-v2-search-input::placeholder {
        color: rgba(255, 255, 255, 0.65);
    }
    .admin-v2-search-input:focus {
        background: rgba(255, 255, 255, 0.22);
        border-color: var(--ksp-teal-growth);
        box-shadow: 0 0 0 3px rgba(42, 157, 143, 0.3);
        outline: none;
    }

    /* BENTUK & WARNA: 4 Kartu Analitik Putih Bersih (#FFFFFF) di atas Latar Off-white */
    .admin-v2-card {
        background: var(--ksp-card-white);
        border-radius: 24px;
        border: 1px solid var(--ksp-border-light);
        box-shadow: 0 4px 20px -2px rgba(27, 38, 59, 0.05);
        transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .admin-v2-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 36px -4px rgba(27, 38, 59, 0.12);
        border-color: var(--ksp-teal-growth);
    }

    /* KONTRAS: Angka Tebal Tegas Biru Navy (#1B263B) vs Sub-Label (#5C6B73) */
    .admin-v2-stat-value {
        color: var(--ksp-text-navy);
        font-weight: 900;
        letter-spacing: -0.035em;
        line-height: 1.1;
    }
    .admin-v2-stat-label {
        color: var(--ksp-text-muted);
        font-weight: 700;
        font-size: 0.725rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    /* Circular Icon Badge di Kiri Atas Kartu */
    .admin-v2-icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 9999px;
        background: var(--ksp-bg-offwhite);
        border: 1px solid var(--ksp-border-light);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--ksp-blue-deep);
        transition: all 0.2s ease;
    }
    .admin-v2-card:hover .admin-v2-icon-circle {
        background: var(--ksp-navy-dark);
        color: #ffffff;
    }

    /* Trend Pill Badge di Kanan Atas Kartu */
    .admin-v2-badge-trend {
        padding: 0.28rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.6875rem;
        font-weight: 800;
        letter-spacing: 0.02em;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .admin-v2-badge-gold {
        background: #FFF9E6;
        color: #9A7B1C;
        border: 1px solid #F5E0A0;
    }
    .admin-v2-badge-teal {
        background: #EAF6F5;
        color: var(--ksp-teal-growth);
        border: 1px solid #B8E4DF;
    }
    .admin-v2-badge-sage {
        background: #F3F6EF;
        color: #556B2F;
        border: 1px solid #D5E0C6;
    }
    .admin-v2-badge-pink {
        background: #fff1f2;
        color: #e11d48;
        border: 1px solid #fecdd3;
    }

    /* WARNA: Batang Kapsul Chart - Biru Navy (#0F4C81) vs Hijau Toska (#2A9D8F) */
    .admin-v2-bar-blue {
        border-radius: 9999px 9999px 4px 4px;
        background: var(--ksp-blue-deep);
        transition: all 0.25s ease;
    }
    .admin-v2-bar-blue:hover {
        background: #1B263B;
        filter: drop-shadow(0 4px 10px rgba(15, 76, 129, 0.45));
    }
    .admin-v2-bar-teal {
        border-radius: 9999px 9999px 4px 4px;
        background: var(--ksp-teal-growth);
        transition: all 0.25s ease;
    }
    .admin-v2-bar-teal:hover {
        background: #238276;
        filter: drop-shadow(0 4px 10px rgba(42, 157, 143, 0.45));
    }

    /* KONTROL: Tombol Pill Header - Aksen Kuning Emas / Gold (#D4AF37) untuk CTA Penting */
    .admin-v2-btn-pill {
        border-radius: 9999px;
        padding: 0.5rem 1.25rem;
        font-size: 0.75rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        transition: all 0.2s ease;
    }
    .admin-v2-btn-gold {
        background: var(--ksp-gold-accent);
        color: var(--ksp-navy-dark);
        border: 1px solid var(--ksp-gold-hover);
        box-shadow: 0 4px 14px rgba(212, 175, 55, 0.35);
    }
    .admin-v2-btn-gold:hover {
        background: var(--ksp-gold-hover);
        color: #0b1522;
        transform: translateY(-1px);
    }
    .admin-v2-btn-teal {
        background: var(--ksp-teal-growth);
        color: #ffffff;
        border: 1px solid #238276;
        box-shadow: 0 4px 14px rgba(42, 157, 143, 0.3);
    }
    .admin-v2-btn-teal:hover {
        background: #238276;
        color: #ffffff;
        transform: translateY(-1px);
    }
    .admin-v2-btn-light {
        background: rgba(255, 255, 255, 0.14);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.24);
    }
    .admin-v2-btn-light:hover {
        background: rgba(255, 255, 255, 0.26);
        color: #ffffff;
    }

    @keyframes barRise {
        from { transform: scaleY(0); }
        to { transform: scaleY(1); }
    }
    .bar-rise-anim {
        transform-origin: bottom;
        animation: barRise 0.75s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>

<!-- ========================================================================= -->
<!-- PEMBUNGKUS UTAMA ADMIN DASHBOARD V2 (CANVAS OFF-WHITE HANGAT #F8F5F0)     -->
<!-- ========================================================================= -->
<div class="admin-v2-wrapper space-y-6">

    <!-- ========================================================================= -->
    <!-- 1. HEADER SECTION & GLOBAL SEARCH (BIRU NAVY TUA #1B263B & AKSEN GOLD)    -->
    <!-- ========================================================================= -->
    <div class="admin-v2-header-glass">
        <!-- Baris Atas: Global Search Bar & Info Sistem -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 mb-4 border-b border-white/15">
            <!-- Kolom Search Cerdas ala paulcodes -->
            <div class="relative w-full md:max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-white/70">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
                <input type="text" id="adminGlobalSearch" 
                       placeholder="Cari anggota, nomor pengajuan, atau nomor kontrak..." 
                       class="admin-v2-search-input w-full text-xs placeholder-white/60">
                <span class="absolute right-3.5 top-2.5 hidden sm:inline-block px-1.5 py-0.5 text-[9px] font-mono text-white/50 bg-white/10 rounded">ESC</span>
            </div>

            <!-- Status Sistem Badge & Identitas V2 -->
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-white text-[11px] font-extrabold border border-white/20">
                    <span class="w-2 h-2 rounded-full bg-[#D4AF37] animate-pulse"></span>
                    {{ auth()->user()->isAdmin() ? 'Admin Dashboard UI V2' : 'Manager Dashboard UI V2' }}
                </span>
                <span class="text-xs text-white/70 font-semibold hidden lg:inline">&bull; Clean White Theme &bull; Panel Eksekutif</span>
            </div>
        </div>

        <!-- Baris Bawah: Judul Halaman & Tombol Tindakan Kontrol (Warna Aksen Emas & Toska) -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-2">
                    <span>Panel Operasional Koperasi</span>
                    <span class="text-xs font-bold text-amber-950 bg-[#D4AF37] px-2.5 py-0.5 rounded-full border border-amber-300">V2</span>
                </h1>
                <p class="text-xs sm:text-sm text-white/80 mt-1">
                    KSP Sahabat Sejahtera &bull; Ringkasan performa dan antrean layanan harian
                </p>
            </div>

            <!-- Tombol Aksi Kontrol (Aksen Kuning Emas #D4AF37 & Hijau Toska #2A9D8F) -->
            <div class="flex items-center gap-2.5 flex-wrap">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.billboards.index') }}" class="admin-v2-btn-pill admin-v2-btn-gold" title="Kelola Billboard Pengumuman Resmi">
                        <svg class="w-4 h-4 text-[#1B263B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        <span>Kelola Billboard</span>
                    </a>
                @endif

                @if(auth()->user()->isManager())
                    <a href="{{ route('accounting.shu') }}" class="admin-v2-btn-pill admin-v2-btn-gold" title="Tutup Buku & Pembagian SHU">
                        <svg class="w-4 h-4 text-[#1B263B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>Tutup Buku &amp; SHU</span>
                    </a>
                @endif

                <a href="{{ route('loans.applications') }}" class="admin-v2-btn-pill admin-v2-btn-teal">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Antrean Pinjaman</span>
                </a>

                <a href="{{ route('accounting.journals') }}" class="admin-v2-btn-pill admin-v2-btn-light">
                    <svg class="w-4 h-4 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span>Jurnal Umum</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 2. 4 ELEGANT ANALYTICS CARDS (PUTIH BERSIH #FFFFFF DI ATAS OFF-WHITE)    -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <!-- CARD 1: TOTAL DANA SIMPANAN ANGGOTA (LIKUIDITAS KAS) -->
        <a href="{{ route('savings.index') }}" 
           class="admin-v2-card p-6 flex flex-col justify-between group no-underline text-slate-800 cursor-pointer"
           title="Buka Kelola Rekening Simpanan">
            <div>
                <!-- Top Row: Icon Circle Biru Navy di Kiri & Trend Pill Badge Emas di Kanan -->
                <div class="flex items-center justify-between gap-2 mb-4">
                    <div class="admin-v2-icon-circle">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <!-- Badge Emas / Gold Melambangkan Daya Tarik Finansial -->
                    <span class="admin-v2-badge-trend admin-v2-badge-gold">
                        <svg class="w-3 h-3 text-[#9A7B1C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        <span>+12.4%</span>
                    </span>
                </div>

                <!-- Angka Besar Biru Navy (#1B263B) Kontras Tinggi -->
                <div class="admin-v2-stat-value text-3xl sm:text-[32px] mb-1">
                    Rp {{ number_format($stats['total_savings'] / 1000000, 1) }} <span class="text-lg font-bold text-slate-400">jt</span>
                </div>

                <!-- Label Kategori -->
                <div class="admin-v2-stat-label mb-2">
                    Total Dana Simpanan Anggota
                </div>

                <div class="font-mono text-xs font-bold text-[#0F4C81] bg-[#EDF2F7] inline-block px-2.5 py-0.5 rounded-lg border border-[#CBD5E1]">
                    Rp {{ number_format($stats['total_savings'], 0, ',', '.') }}
                </div>
            </div>

            <!-- Footer Card -->
            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-semibold">Likuiditas Kas Koperasi</span>
                <span class="font-bold text-[#0F4C81] group-hover:text-[#2A9D8F] group-hover:translate-x-1 transition-all flex items-center gap-1">
                    <span>Kelola</span>
                    <span>&rarr;</span>
                </span>
            </div>
        </a>

        <!-- CARD 2: TOTAL ANGGOTA AKTIF -->
        <a href="{{ route('members.index') }}" 
           class="admin-v2-card p-6 flex flex-col justify-between group no-underline text-slate-800 cursor-pointer"
           title="Buka Kelola Anggota">
            <div>
                <div class="flex items-center justify-between gap-2 mb-4">
                    <div class="admin-v2-icon-circle">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    @if($stats['pending_members'] > 0)
                        <span class="admin-v2-badge-trend admin-v2-badge-gold">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#D4AF37] animate-ping"></span>
                            <span>{{ $stats['pending_members'] }} pending</span>
                        </span>
                    @else
                        <span class="admin-v2-badge-trend admin-v2-badge-sage">
                            <span>100% Terverifikasi</span>
                        </span>
                    @endif
                </div>

                <div class="admin-v2-stat-value text-3xl sm:text-[32px] mb-1">
                    {{ number_format($stats['active_members']) }} <span class="text-base font-bold text-slate-400">/ {{ $stats['total_members'] }} total</span>
                </div>

                <div class="admin-v2-stat-label mb-2">
                    TOTAL ANGGOTA AKTIF
                </div>

                <div class="text-xs text-slate-500 truncate">
                    Pertumbuhan anggota aktif koperasi
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-semibold">Buku Anggota</span>
                <span class="font-bold text-[#0F4C81] group-hover:text-[#2A9D8F] group-hover:translate-x-1 transition-all flex items-center gap-1">
                    <span>Lihat</span>
                    <span>&rarr;</span>
                </span>
            </div>
        </a>

        <!-- CARD 3: SISA POKOK PINJAMAN (OUTSTANDING PEMBIAYAAN) -->
        <a href="{{ route('loans.manage') }}" 
           class="admin-v2-card p-6 flex flex-col justify-between group no-underline text-slate-800 cursor-pointer"
           title="Buka Portofolio Pinjaman">
            <div>
                <div class="flex items-center justify-between gap-2 mb-4">
                    <div class="admin-v2-icon-circle">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <!-- Badge Hijau Toska / Teal (#2A9D8F) -->
                    <span class="admin-v2-badge-trend admin-v2-badge-teal">
                        <span>Lancar {{ $repaymentRate }}%</span>
                    </span>
                </div>

                <div class="admin-v2-stat-value text-3xl sm:text-[32px] mb-1">
                    Rp {{ number_format($stats['total_loans_active'] / 1000000, 1) }} <span class="text-lg font-bold text-slate-400">jt</span>
                </div>

                <div class="admin-v2-stat-label mb-2">
                    SISA POKOK PINJAMAN
                </div>

                <div class="font-mono text-xs font-semibold text-[#1B6E64] bg-[#EAF6F5] inline-block px-2.5 py-0.5 rounded-lg border border-[#B8E4DF]">
                    Rp {{ number_format($stats['total_loans_active'], 0, ',', '.') }}
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-semibold truncate">Portofolio Pembiayaan</span>
                <span class="font-bold text-[#0F4C81] group-hover:text-[#2A9D8F] group-hover:translate-x-1 transition-all flex items-center gap-1">
                    <span>Portofolio</span>
                    <span>&rarr;</span>
                </span>
            </div>
        </a>

        <!-- CARD 4: PINJAMAN MENUNGGAK (NPL) -->
        <a href="{{ route('reports.overdue') }}" 
           class="admin-v2-card p-6 flex flex-col justify-between group no-underline text-slate-800 cursor-pointer"
           title="Buka Laporan Kolektibilitas / NPL">
            <div>
                <div class="flex items-center justify-between gap-2 mb-4">
                    <div class="admin-v2-icon-circle">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <span class="admin-v2-badge-trend {{ $stats['total_loans_overdue'] > 0 ? 'admin-v2-badge-pink' : 'admin-v2-badge-teal' }}">
                        <span>{{ $stats['total_loans_overdue'] > 0 ? 'Follow-up' : 'NPL 0.0%' }}</span>
                    </span>
                </div>

                <div class="admin-v2-stat-value text-3xl sm:text-[32px] text-rose-600 mb-1">
                    Rp {{ number_format($stats['total_loans_overdue'] / 1000000, 1) }} <span class="text-lg font-bold text-rose-400">jt</span>
                </div>

                <div class="admin-v2-stat-label mb-2">
                    PINJAMAN MENUNGGAK (NPL)
                </div>

                <div class="text-xs text-slate-500 truncate">
                    {{ $stats['total_loans_overdue'] > 0 ? 'Perlu tindakan penagihan kredit' : 'Kolektibilitas kredit lancar (0 tunggakan)' }}
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400 font-semibold">Laporan Kolektibilitas</span>
                <span class="font-bold text-rose-600 group-hover:translate-x-1 transition-transform flex items-center gap-1">
                    <span>Audit NPL</span>
                    <span>&rarr;</span>
                </span>
            </div>
        </a>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. ANALYTICS & CHARTS (BIRU NAVY #0F4C81 & HIJAU TOSKA #2A9D8F)           -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 items-stretch">
        <!-- LEFT: BAR CHART BULANAN (SIMPANAN = BIRU TUA #0F4C81, PINJAMAN = HIJAU TOSKA #2A9D8F) -->
        <div class="lg:col-span-8 admin-v2-card p-6 sm:p-7 flex flex-col justify-between">
            <div>
                <!-- Header Chart -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-lg font-black text-[#1B263B] tracking-tight">Tren Arus Finansial Bulanan</h2>
                            <span class="text-[11px] font-extrabold text-[#2A9D8F] bg-[#EAF6F5] px-2.5 py-0.5 rounded-full border border-[#B8E4DF]">
                                6 Bulan Terakhir
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Pertumbuhan akumulasi simpanan masuk vs pembiayaan kredit yang disalurkan</p>
                    </div>

                    <!-- Legend Indikator: Biru Finansial (#0F4C81) & Hijau Toska (#2A9D8F) -->
                    <div class="flex items-center gap-4 text-xs font-bold">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-[#0F4C81]"></span>
                            <span class="text-[#1B263B]">Simpanan (Kepercayaan)</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-3 rounded-full bg-[#2A9D8F]"></span>
                            <span class="text-[#1B263B]">Pinjaman (Pertumbuhan)</span>
                        </div>
                    </div>
                </div>

                <!-- Visual Bar Chart Container -->
                <div class="pt-7 pb-2">
                    <div class="relative h-60 sm:h-64 w-full flex items-end justify-between gap-3 sm:gap-6 px-2 sm:px-6 border-b border-slate-200/80">
                        <!-- Horizontal Guidelines -->
                        <div class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-50">
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
                                <!-- Tooltip Floating Hover Navy (#1B263B) -->
                                <div class="absolute -top-14 opacity-0 group-hover:opacity-100 transition-all duration-200 bg-[#1B263B] text-white text-[11px] rounded-xl py-1.5 px-3 pointer-events-none whitespace-nowrap shadow-xl z-30">
                                    <div class="font-bold text-center border-b border-white/20 pb-1 mb-1">{{ $bar['label'] }}</div>
                                    <div class="text-[#A3B18A] font-mono">Simpanan: Rp {{ $bar['savings'] }} jt</div>
                                    <div class="text-[#2A9D8F] font-mono">Pinjaman: Rp {{ $bar['loans'] }} jt</div>
                                </div>

                                <!-- Dual Capsule Bars: Biru (#0F4C81) & Toska (#2A9D8F) -->
                                <div class="w-full flex items-end justify-center gap-1.5 sm:gap-2.5 h-full pb-1">
                                    <div class="w-3.5 sm:w-6 admin-v2-bar-blue bar-rise-anim cursor-pointer shadow-xs"
                                         style="height: {{ $savingsHeight }}%;"></div>
                                    <div class="w-3.5 sm:w-6 admin-v2-bar-teal bar-rise-anim cursor-pointer shadow-xs"
                                         style="height: {{ $loansHeight }}%;"></div>
                                </div>

                                <!-- Label Bulan -->
                                <span class="text-xs font-bold text-slate-500 group-hover:text-[#1B263B] transition-colors mt-2.5">
                                    {{ $bar['month'] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Catatan Kinerja Arus Kas -->
            <div class="pt-4 mt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                <div class="flex items-center gap-2 text-[#2A9D8F] font-bold">
                    <svg class="w-4 h-4 text-[#2A9D8F] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    <span>Likuiditas kas bertumbuh sehat dengan penyaluran kredit produktif terkontrol.</span>
                </div>
                <span class="text-slate-400 font-mono text-[11px] hidden sm:inline">Basis Real-Time 2026</span>
            </div>
        </div>

        <!-- RIGHT: CIRCULAR DONUT GAUGE (HIJAU TOSKA #2A9D8F & BIRU NAVY #1B263B) -->
        <div class="lg:col-span-4 admin-v2-card p-6 sm:p-7 flex flex-col justify-between">
            <div>
                <div class="pb-3 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-black text-[#1B263B] tracking-tight">Rasio Kesehatan Kas</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Keseimbangan likuiditas &amp; portofolio</p>
                    </div>
                    <span class="admin-v2-badge-trend admin-v2-badge-teal font-extrabold text-[10px]">
                        Prima
                    </span>
                </div>

                <!-- Donut Circular Gauge Chart -->
                <div class="my-6 flex flex-col items-center justify-center relative">
                    <div class="relative w-48 h-48 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 100 100">
                            <!-- Background Track Circle Off-white/Gray -->
                            <circle cx="50" cy="50" r="39" stroke="#EBF3F1" stroke-width="9.5" fill="transparent" />
                            <!-- Active Hijau Toska (#2A9D8F) Gauge Arc -->
                            @php
                                $circumference = 2 * 3.14159 * 39;
                                $dashOffset = $circumference - ($liquidityPercentage / 100) * $circumference;
                            @endphp
                            <circle cx="50" cy="50" r="39" 
                                    stroke="#2A9D8F" 
                                    stroke-width="9.5" 
                                    stroke-linecap="round" 
                                    stroke-dasharray="{{ $circumference }}" 
                                    stroke-dashoffset="{{ $dashOffset }}" 
                                    fill="transparent" 
                                    class="transition-all duration-1000 ease-out" />
                        </svg>

                        <!-- Center Percentage: Biru Navy Tua (#1B263B) -->
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <span class="text-4xl font-black text-[#1B263B] tracking-tight">{{ $liquidityPercentage }}%</span>
                            <span class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 mt-0.5">Rasio Likuiditas</span>
                            <span class="text-[10px] font-bold text-[#1B6E64] bg-[#EAF6F5] px-2.5 py-0.5 rounded-full mt-1 border border-[#B8E4DF]">
                                Sangat Sehat
                            </span>
                        </div>
                    </div>
                </div>

                <!-- 2 Side-by-Side Stat Boxes (Latar Off-white #F8F5F0) -->
                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div class="bg-[#F8F5F0] rounded-2xl p-3.5 border border-[#E6E0D5] text-center">
                        <div class="text-[10px] font-extrabold uppercase text-[#5C6B73] tracking-wider">Kas Likuid</div>
                        <div class="text-base sm:text-lg font-black text-[#1B263B] mt-0.5">
                            Rp {{ number_format($stats['total_savings'] / 1000000, 1) }} jt
                        </div>
                        <div class="text-[10px] text-[#2A9D8F] font-bold mt-0.5">Simpanan (Hijau)</div>
                    </div>

                    <div class="bg-[#F8F5F0] rounded-2xl p-3.5 border border-[#E6E0D5] text-center">
                        <div class="text-[10px] font-extrabold uppercase text-[#5C6B73] tracking-wider">Beredar</div>
                        <div class="text-base sm:text-lg font-black text-[#1B263B] mt-0.5">
                            Rp {{ number_format($stats['total_loans_active'] / 1000000, 1) }} jt
                        </div>
                        <div class="text-[10px] text-[#0F4C81] font-bold mt-0.5">Sisa Pokok (Biru)</div>
                    </div>
                </div>
            </div>

            <div class="pt-4 mt-4 border-t border-slate-100 text-center">
                <a href="{{ route('accounting.ledger') }}" class="text-xs font-bold text-[#0F4C81] hover:text-[#D4AF37] inline-flex items-center gap-1 group transition-colors">
                    <span>Audit Buku Besar Kas Utama</span>
                    <span class="group-hover:translate-x-1 transition-transform">&rarr;</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. RECENT ACTIVITY & OPERATIONS TABLE (TABBED, PUTIH BERSIH ELEGAN)       -->
    <!-- ========================================================================= -->
    <div class="admin-v2-card overflow-hidden">
        <!-- Table Header & Tab Switcher -->
        <div class="p-6 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white">
            <div>
                <h2 class="text-xl font-black text-[#1B263B] tracking-tight">Antrean Operasional &amp; Evaluasi Layanan</h2>
                <p class="text-xs text-slate-500 mt-0.5">Manajemen antrean pengajuan kredit, verifikasi berkas anggota, dan pencairan dana</p>
            </div>

            <!-- Tab Buttons Berbentuk Capsule Pills -->
            <div class="inline-flex p-1 bg-[#F8F5F0] rounded-full border border-[#E6E0D5] text-xs font-bold self-start md:self-auto">
                <button type="button" onclick="switchAdminTab('tab-loans')" id="btn-tab-loans" 
                        class="px-4 py-2 rounded-full bg-white text-[#1B263B] shadow-xs transition-all flex items-center gap-2">
                    <span>Pengajuan Kredit</span>
                    <span class="px-2 py-0.5 rounded-full bg-[#EAF6F5] text-[#2A9D8F] text-[10px] font-extrabold">{{ $stats['pending_loan_applications'] }}</span>
                </button>
                <button type="button" onclick="switchAdminTab('tab-members')" id="btn-tab-members" 
                        class="px-4 py-2 rounded-full text-slate-500 hover:text-[#1B263B] transition-all flex items-center gap-2">
                    <span>Verifikasi Anggota</span>
                    <span class="px-2 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-extrabold">{{ $stats['pending_members'] }}</span>
                </button>
                <button type="button" onclick="switchAdminTab('tab-disburse')" id="btn-tab-disburse" 
                        class="px-4 py-2 rounded-full text-slate-500 hover:text-[#1B263B] transition-all flex items-center gap-2">
                    <span>Siap Cair</span>
                    <span class="px-2 py-0.5 rounded-full bg-[#EDF2F7] text-[#0F4C81] text-[10px] font-extrabold">{{ $stats['ready_to_disburse_loans'] }}</span>
                </button>
            </div>
        </div>

        <!-- TAB 1: PENGAJUAN PINJAMAN MASUK -->
        <div id="tab-loans" class="admin-tab-pane">
            @if($pendingApplications->isEmpty())
                <div class="p-14 text-center text-slate-400 text-xs flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="font-extrabold text-[#1B263B] text-sm">Tidak Ada Pengajuan Kredit Menunggu</span>
                    <span class="text-slate-400 mt-1">Seluruh pengajuan pinjaman anggota telah diproses oleh tim analis &amp; manajer.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#F8F5F0] border-b border-slate-100 text-[#5C6B73] font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="py-4 px-6">No. Pengajuan</th>
                                <th class="py-4 px-4">Nama Pemohon &amp; Produk</th>
                                <th class="py-4 px-4">Plafon Pinjaman</th>
                                <th class="py-4 px-4">Tahapan Evaluasi</th>
                                <th class="py-4 px-6 text-right">Tindakan Eksekutif</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pendingApplications as $app)
                            <tr class="hover:bg-[#FAF9F5] transition-colors searchable-row">
                                <td class="py-4 px-6 font-mono font-bold text-[#1B263B]">
                                    {{ $app->application_no }}
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-[#1B263B] text-sm">{{ $app->member->user->name }}</div>
                                    <div class="text-slate-400 text-xs font-medium">{{ $app->loanProduct->name }}</div>
                                </td>
                                <td class="py-4 px-4 font-black text-[#1B263B] text-sm">
                                    Rp {{ number_format($app->amount, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4">
                                    <span class="admin-v2-badge-trend {{ $app->status === 'submitted' ? 'admin-v2-badge-gold' : 'admin-v2-badge-teal' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $app->status === 'submitted' ? 'bg-[#D4AF37]' : 'bg-[#2A9D8F]' }}"></span>
                                        {{ $app->status === 'submitted' ? 'PENGAJUAN BARU' : 'ANALISIS KREDIT' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('loans.applications.show', $app->id) }}" class="admin-v2-btn-pill bg-[#1B263B] hover:bg-[#0F4C81] text-white">
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
                <div class="p-14 text-center text-slate-400 text-xs flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span class="font-extrabold text-[#1B263B] text-sm">Semua Anggota Telah Terverifikasi</span>
                    <span class="text-slate-400 mt-1">Tidak ada calon anggota yang menunggu persetujuan KYC saat ini.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#F8F5F0] border-b border-slate-100 text-[#5C6B73] font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="py-4 px-6">Calon Anggota</th>
                                <th class="py-4 px-4">NIK</th>
                                <th class="py-4 px-4">Pekerjaan</th>
                                <th class="py-4 px-4">Dokumen Pendukung</th>
                                <th class="py-4 px-6 text-right">Aksi Verifikasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($pendingVerifications as $pm)
                            <tr class="hover:bg-[#FAF9F5] transition-colors searchable-row">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-[#1B263B] text-sm">{{ $pm->user->name }}</div>
                                    <div class="text-slate-400 font-mono text-xs">{{ $pm->user->phone }}</div>
                                </td>
                                <td class="py-4 px-4 font-mono text-slate-700 font-medium">{{ $pm->nik }}</td>
                                <td class="py-4 px-4 text-slate-600">{{ $pm->occupation }}</td>
                                <td class="py-4 px-4">
                                    <span class="admin-v2-badge-trend admin-v2-badge-teal">
                                        {{ $pm->documents->count() }} Berkas KYC
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('members.show', $pm->id) }}" class="admin-v2-btn-pill bg-[#EDF2F7] hover:bg-[#CBD5E1] text-[#1B263B]">
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
                <div class="p-14 text-center text-slate-400 text-xs flex flex-col items-center justify-center">
                    <svg class="w-12 h-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="font-extrabold text-[#1B263B] text-sm">Tidak Ada Pinjaman Menunggu Pencairan</span>
                    <span class="text-slate-400 mt-1">Seluruh akad kredit yang disetujui telah disalurkan oleh kasir teller.</span>
                </div>
            @else
                <div class="table-responsive">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#EAF6F5] border-b border-[#B8E4DF] text-[#1B6E64] font-bold uppercase text-[11px] tracking-wider">
                            <tr>
                                <th class="py-4 px-6">No. Pinjaman</th>
                                <th class="py-4 px-4">Peminjam</th>
                                <th class="py-4 px-4">Plafon Pokok</th>
                                <th class="py-4 px-4">Netto Dicairkan</th>
                                <th class="py-4 px-6 text-right">Aksi Kasir Teller</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-teal-50">
                            @foreach($awaitingDisbursementLoans as $abl)
                            <tr class="hover:bg-[#FAF9F5] transition-colors searchable-row">
                                <td class="py-4 px-6 font-mono font-bold text-[#1B263B]">
                                    {{ $abl->loan_no }}
                                </td>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-[#1B263B] text-sm">{{ $abl->member->user->name }}</div>
                                    <div class="text-slate-400 font-mono text-xs">{{ $abl->member->member_no }}</div>
                                </td>
                                <td class="py-4 px-4 font-semibold text-[#1B263B]">
                                    Rp {{ number_format($abl->principal_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 font-black text-[#2A9D8F] text-sm">
                                    Rp {{ number_format($abl->principal_amount - $abl->total_admin_fee, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @if(in_array(auth()->user()->role, ['teller', 'admin']))
                                        <form action="{{ route('loans.disburse', $abl->id) }}" method="POST" class="inline" onsubmit="return confirm('Cairkan dana pinjaman sebesar Rp {{ number_format($abl->principal_amount - $abl->total_admin_fee, 0, ',', '.') }}?');">
                                            @csrf
                                            <input type="hidden" name="payment_method" value="transfer">
                                            <button type="submit" class="admin-v2-btn-pill admin-v2-btn-teal">
                                                Cairkan Dana
                                            </button>
                                        </form>
                                    @else
                                        <span class="admin-v2-badge-trend admin-v2-badge-teal">
                                            Menunggu Kasir Teller
                                        </span>
                                    @endif
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
                    btn.className = 'px-4 py-2 rounded-full bg-white text-[#1B263B] shadow-xs transition-all flex items-center gap-2 font-bold';
                } else {
                    btn.className = 'px-4 py-2 rounded-full text-slate-500 hover:text-[#1B263B] transition-all flex items-center gap-2 font-bold';
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
