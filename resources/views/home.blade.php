@extends('layouts.app')

@section('title', 'Koperasi Simpan Pinjam Online - KSP Sahabat Sejahtera')

@section('container-class', 'w-full py-0')

@section('styles')
<style>
    html {
        scroll-behavior: smooth;
        scroll-padding-top: 4rem;
    }
    .hero-gradient {
        background: radial-gradient(120% 120% at 50% 10%, #FFFFFF 0%, #F8F5F0 45%, #EAF6F5 80%, #DBEFEF 100%);
    }
    .billboard-slide {
        display: none;
    }
    .billboard-slide.active {
        display: block;
        animation: fadeIn 0.4s ease-in-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="space-y-0 text-slate-800">

    {{-- =========================================================================
         1. FITUR NAV HEADER PERTAMA: DASHBOARD / BERANDA UTAMA
         (Sambutan & Ringkasan Organisasi, Billboard Pengumuman, Standar Layanan, FAQ)
       ========================================================================= --}}
    <section id="dashboard" class="hero-gradient border-b border-[#E8E2D8] pt-3.5 pb-12 sm:pt-4 sm:pb-16 lg:pt-5 lg:pb-24 overflow-hidden w-full max-w-full" aria-labelledby="heading-dashboard">
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-12 min-w-0">
            
            {{-- Sambutan & Ringkasan Organisasi Koperasi --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                <div class="lg:col-span-7 space-y-5 sm:space-y-6 min-w-0">
                    <div class="inline-flex max-w-full items-center gap-2 px-3 sm:px-3.5 py-1.5 rounded-xl sm:rounded-full text-[10px] sm:text-xs font-bold bg-[#EAF6F5] text-[#0F4C81] border border-[#2A9D8F]/40 shadow-2xs leading-snug">
                        <span class="w-2 h-2 rounded-full bg-[#2A9D8F] animate-pulse shrink-0"></span>
                        <span class="break-words">BADAN HUKUM RESMI KEMENKOP UKM RI &bull; AHU-0012345.AH.01.26.TAHUN 2026</span>
                    </div>

                    <h1 id="heading-dashboard" class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                        Koperasi Simpan Pinjam Digital: <span class="text-[#0F4C81]">Amanah, Transparan, & Melayani Anggota.</span>
                    </h1>

                    <p class="text-xs sm:text-base text-slate-600 leading-relaxed max-w-2xl">
                        Selamat datang di <strong>KSP Sahabat Sejahtera</strong>. Lembaga keuangan koperasi modern berasaskan kekeluargaan dan gotong-royong. Kami menyediakan permodalan mikro UMKM tanpa agunan rumit, simpanan bagi hasil berkah bulanan, serta transaksi digital 24 jam dengan standar akuntansi publik yang akuntabel.
                    </p>

                    <div class="flex flex-col sm:flex-row flex-wrap items-stretch sm:items-center gap-2.5 sm:gap-3 pt-2 w-full">
                        <a href="{{ route('register') }}" class="px-5 sm:px-6 py-3 rounded-xl font-extrabold text-xs sm:text-sm shadow-sm hover:shadow transition-all flex items-center justify-center gap-2 bg-[#D4AF37] hover:bg-[#C59F2D] text-[#1B263B] border border-[#C59F2D]">
                            <span>Daftar Anggota Baru</span>
                            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="#simulasi" class="btn btn-secondary px-5 sm:px-6 py-3 rounded-xl font-bold text-xs sm:text-sm hover:bg-[#F8F5F0] text-[#0F4C81] border border-[#E8E2D8] transition-all justify-center">
                            Simulasi Pinjaman
                        </a>
                        <a href="https://wa.me/6282197233174?text=Halo%20Admin%20KSP%20Sahabat%20Sejahtera,%20saya%20ingin%20konsultasi%20layanan%20koperasi" target="_blank" rel="noopener" class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-xs sm:text-sm font-bold text-[#0F4C81] hover:text-[#1B263B] hover:bg-[#EAF6F5] transition-all">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#2A9D8F] shrink-0"></span>
                            Chat CS 24 Jam WhatsApp
                        </a>
                    </div>

                    {{-- Fitur Unggulan Singkat --}}
                    <div class="pt-4 border-t border-[#E8E2D8] grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3 text-xs text-slate-600 font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#2A9D8F] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Bagi Hasil SHU Tahunan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#2A9D8F] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Pencairan Cepat 1x24 Jam</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#2A9D8F] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Tanpa Bunga Berbunga</span>
                        </div>
                    </div>
                </div>

                {{-- Ringkasan Organisasi & Statistik Card --}}
                <div class="lg:col-span-5 min-w-0">
                    <div class="bg-white rounded-3xl p-4 sm:p-7 border border-[#E8E2D8] shadow-xl space-y-5 sm:space-y-6 relative overflow-hidden">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl navbar-brand-badge flex items-center justify-center text-white font-black text-xl sm:text-2xl shadow-sm shrink-0">
                                    K
                                </div>
                                <div class="min-w-0">
                                    <h2 class="text-sm sm:text-base font-bold text-slate-900 truncate">KSP Sahabat Sejahtera</h2>
                                    <p class="text-2xs sm:text-xs text-slate-500 truncate">Ringkasan Organisasi & Tata Kelola</p>
                                </div>
                            </div>
                            <span class="badge badge-success text-2xs shrink-0">Terakreditasi</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2.5 sm:gap-3.5">
                            <div class="p-3 sm:p-3.5 rounded-2xl bg-[#F8F5F0] border border-[#E8E2D8]">
                                <span class="text-2xs font-bold uppercase tracking-wider text-slate-500 block">Anggota Aktif</span>
                                <span class="text-lg sm:text-2xl font-black text-[#0F4C81] mt-1 block">{{ number_format($stats['total_members']) }}+</span>
                                <span class="text-3xs text-slate-400">Terdaftar di seluruh wilayah</span>
                            </div>

                            <div class="p-3 sm:p-3.5 rounded-2xl bg-[#F8F5F0] border border-[#E8E2D8]">
                                <span class="text-2xs font-bold uppercase tracking-wider text-slate-500 block">Aset Dikelola</span>
                                <span class="text-lg sm:text-2xl font-black text-[#1B263B] mt-1 block">Rp {{ number_format($stats['total_savings_managed']/1000000000, 1) }} M</span>
                                <span class="text-3xs text-slate-400">Simpanan Pokok, Wajib, Sukarela</span>
                            </div>

                            <div class="p-3 sm:p-3.5 rounded-2xl bg-[#F8F5F0] border border-[#E8E2D8]">
                                <span class="text-2xs font-bold uppercase tracking-wider text-slate-500 block">Modal UMKM Tersalur</span>
                                <span class="text-lg sm:text-2xl font-black text-[#2A9D8F] mt-1 block">Rp {{ number_format($stats['total_loan_distributed']/1000000000, 1) }} M</span>
                                <span class="text-3xs text-slate-400">Pemberdayaan sektor produktif</span>
                            </div>

                            <div class="p-3 sm:p-3.5 rounded-2xl bg-[#F8F5F0] border border-[#E8E2D8]">
                                <span class="text-2xs font-bold uppercase tracking-wider text-slate-500 block">Kolektibilitas</span>
                                <span class="text-lg sm:text-2xl font-black text-[#0F4C81] mt-1 block">98.6%</span>
                                <span class="text-3xs text-slate-400">Rasio lancar (NPL sehat)</span>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-[#EAF6F5] border border-[#2A9D8F]/30 text-xs text-[#1B263B] leading-relaxed">
                            <strong>Prinsip Koperasi:</strong> Dari anggota, oleh anggota, dan untuk kesejahteraan anggota. Setiap transaksi tercatat dalam pembukuan berpasangan <em>(Double-Entry Bookkeeping)</em> yang dapat diaudit secara berkala.
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAMPILAN BILLBOARD UNTUK PENGUMUMAN RESMI --}}
            <div class="bg-white rounded-3xl p-4 sm:p-6 lg:p-8 border border-[#E8E2D8] shadow-md overflow-hidden min-w-0 w-full">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3 min-w-0 w-full">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-10 h-10 rounded-xl bg-[#FFF9E6] text-[#D4AF37] flex items-center justify-center font-bold shrink-0">
                            <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-2xs font-bold text-[#B45309] uppercase tracking-wider block">Papan Informasi Resmi</span>
                                @if(auth()->check() && auth()->user()->isAdmin())
                                    <a href="{{ route('admin.billboards.index') }}" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-[#FFF9E6] hover:bg-[#FDF0C8] text-[#1B263B] text-3xs font-extrabold uppercase tracking-wider transition-colors border border-[#F5E0A0] shadow-2xs" title="Kelola Papan Informasi">
                                        <svg class="w-3 h-3 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Ubah Billboard
                                    </a>
                                @endif
                            </div>
                            <h2 class="text-base sm:text-lg font-extrabold text-slate-900 break-words">Billboard Pengumuman Koperasi</h2>
                        </div>
                    </div>

                    {{-- Navigasi Tab Billboard: Fleksibel Responsif di Ponsel (Tanpa Potongan Teks & Tanpa Slide Samping) --}}
                    @php
                        $displayBillboards = (isset($billboards) && $billboards->isNotEmpty()) ? $billboards : null;
                    @endphp
                    <div class="w-full sm:w-auto min-w-0">
                        <div class="flex flex-wrap sm:inline-flex items-stretch sm:items-center gap-1.5 bg-[#F8F5F0] border border-[#E8E2D8] p-1.5 sm:p-1 rounded-2xl sm:rounded-xl text-xs w-full sm:w-auto" role="tablist" aria-label="Tab Pilihan Billboard Pengumuman">
                            @if($displayBillboards)
                                @foreach($displayBillboards as $idx => $bb)
                                    <button type="button" 
                                            onclick="switchBillboard({{ $idx }})" 
                                            id="bb-tab-{{ $idx }}" 
                                            class="billboard-tab-btn flex-1 sm:flex-none text-center justify-center min-w-[110px] sm:min-w-0 px-2.5 sm:px-3 py-2 sm:py-1.5 rounded-xl sm:rounded-lg text-2xs sm:text-xs leading-snug whitespace-normal sm:whitespace-nowrap transition-all focus:outline-none focus:ring-2 focus:ring-[#2A9D8F] flex items-center {{ $idx === 0 ? 'font-bold bg-[#0F4C81] text-white shadow-2xs' : 'font-semibold text-slate-600 hover:text-[#0F4C81]' }}" 
                                            role="tab" 
                                            aria-selected="{{ $idx === 0 ? 'true' : 'false' }}">
                                        <span>{{ $bb->tab_title }}</span>
                                    </button>
                                @endforeach
                            @else
                                <button type="button" 
                                        onclick="switchBillboard(0)" 
                                        id="bb-tab-0" 
                                        class="billboard-tab-btn flex-1 sm:flex-none text-center justify-center min-w-[110px] sm:min-w-0 px-2.5 sm:px-3 py-2 sm:py-1.5 rounded-xl sm:rounded-lg text-2xs sm:text-xs leading-snug whitespace-normal sm:whitespace-nowrap transition-all focus:outline-none focus:ring-2 focus:ring-[#2A9D8F] flex items-center font-bold bg-[#0F4C81] text-white shadow-2xs" 
                                        role="tab" 
                                        aria-selected="true">
                                    <span>RAT &amp; SHU</span>
                                </button>
                                <button type="button" 
                                        onclick="switchBillboard(1)" 
                                        id="bb-tab-1" 
                                        class="billboard-tab-btn flex-1 sm:flex-none text-center justify-center min-w-[110px] sm:min-w-0 px-2.5 sm:px-3 py-2 sm:py-1.5 rounded-xl sm:rounded-lg text-2xs sm:text-xs leading-snug whitespace-normal sm:whitespace-nowrap transition-all focus:outline-none focus:ring-2 focus:ring-[#2A9D8F] flex items-center font-semibold text-slate-600 hover:text-[#0F4C81]" 
                                        role="tab" 
                                        aria-selected="false">
                                    <span>Modal UMKM</span>
                                </button>
                                <button type="button" 
                                        onclick="switchBillboard(2)" 
                                        id="bb-tab-2" 
                                        class="billboard-tab-btn flex-1 sm:flex-none text-center justify-center min-w-[110px] sm:min-w-0 px-2.5 sm:px-3 py-2 sm:py-1.5 rounded-xl sm:rounded-lg text-2xs sm:text-xs leading-snug whitespace-normal sm:whitespace-nowrap transition-all focus:outline-none focus:ring-2 focus:ring-[#2A9D8F] flex items-center font-semibold text-slate-600 hover:text-[#0F4C81]" 
                                        role="tab" 
                                        aria-selected="false">
                                    <span>Pembayaran QRIS &amp; VA</span>
                                </button>
                                <button type="button" 
                                        onclick="switchBillboard(3)" 
                                        id="bb-tab-3" 
                                        class="billboard-tab-btn flex-1 sm:flex-none text-center justify-center min-w-[110px] sm:min-w-0 px-2.5 sm:px-3 py-2 sm:py-1.5 rounded-xl sm:rounded-lg text-2xs sm:text-xs leading-snug whitespace-normal sm:whitespace-nowrap transition-all focus:outline-none focus:ring-2 focus:ring-[#2A9D8F] flex items-center font-semibold text-slate-600 hover:text-[#0F4C81]" 
                                        role="tab" 
                                        aria-selected="false">
                                    <span>Hasil Audit</span>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Konten Slider / Billboard Tab --}}
                <div class="pt-6">
                    @if($displayBillboards)
                        @foreach($displayBillboards as $idx => $bb)
                            <div id="bb-slide-{{ $idx }}" class="billboard-slide {{ $idx === 0 ? 'active' : '' }} space-y-3">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="badge {{ $bb->badge_class }} text-2xs font-bold">{{ $bb->badge_text }}</span>
                                    @if($bb->meta_text)
                                        <span class="text-xs text-slate-400">{{ $bb->meta_text }}</span>
                                    @endif
                                </div>
                                <h3 class="text-lg sm:text-xl font-black text-slate-900 break-words leading-snug">{{ $bb->title }}</h3>
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-4xl break-words">
                                    {!! nl2br(e($bb->content)) !!}
                                </p>
                                @if($bb->status_text || ($bb->link_url && $bb->link_text))
                                    <div class="pt-2 flex flex-wrap items-center gap-2 sm:gap-4 text-xs font-bold text-[#0F4C81]">
                                        @if($bb->status_text)
                                            <span>{{ $bb->status_text }}</span>
                                        @endif
                                        @if($bb->status_text && $bb->link_url && $bb->link_text)
                                            <span>&bull;</span>
                                        @endif
                                        @if($bb->link_url && $bb->link_text)
                                            @php
                                                $cleanLinkText = rtrim($bb->link_text, " \t\n\r\0\x0B→->");
                                            @endphp
                                            <a href="{{ $bb->link_url }}" class="hover:underline flex items-center gap-1">{{ $cleanLinkText }} &rarr;</a>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    @else
                        {{-- Fallback Slide 0 --}}
                        <div id="bb-slide-0" class="billboard-slide active space-y-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge badge-warning text-2xs font-bold">Pengumuman Utama</span>
                                <span class="text-xs text-slate-400">Dipublikasikan: 01 November 2026</span>
                            </div>
                            <h3 class="text-lg sm:text-xl font-black text-slate-900 break-words leading-snug">Rapat Anggota Tahunan (RAT) Tahun Buku Berjalan & Alokasi Pembagian SHU</h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-4xl break-words">
                                Pengurus mengumumkan bahwa Rapat Anggota Tahunan (RAT) telah menetapkan alokasi Sisa Hasil Usaha (SHU) sebesar <strong>40% untuk Jasa Modal dan Jasa Anggota</strong>. Anggota yang telah melunasi Simpanan Pokok dan Wajib dapat langsung memeriksa simulasi hak perolehan SHU pada menu akuntansi di dashboard anggota.
                            </p>
                            <div class="pt-2 flex flex-wrap items-center gap-2 sm:gap-4 text-xs font-bold text-[#0F4C81]">
                                <span>Status: Pelaksanaan Berjalan</span>
                                <span>&bull;</span>
                                <a href="#panduan" class="hover:underline flex items-center gap-1">Pelajari Alur SHU &rarr;</a>
                            </div>
                        </div>

                        {{-- Fallback Slide 1 --}}
                        <div id="bb-slide-1" class="billboard-slide space-y-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge badge-success text-2xs font-bold">Program Permodalan</span>
                                <span class="text-xs text-slate-400">Berlaku untuk Anggota Aktif</span>
                            </div>
                            <h3 class="text-lg sm:text-xl font-black text-slate-900 break-words leading-snug">Skema Pinjaman Mikro Mingguan Khusus Pelaku Usaha UMKM</h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-4xl break-words">
                                KSP Sahabat Sejahtera meluncurkan fasilitas pembiayaan mikro mingguan (tenor 4 s.d. 24 minggu) dengan margin anuitas bersahabat mulai 0.40% per minggu. Persyaratan mudah: cukup upload foto tempat usaha saat mengajukan permohonan melalui sistem koperasi online ini.
                            </p>
                            <div class="pt-2 flex flex-wrap items-center gap-2 sm:gap-4 text-xs font-bold text-[#0F4C81]">
                                <a href="#produk" class="hover:underline">Lihat Rincian Plafon Produk Pinjaman &rarr;</a>
                            </div>
                        </div>

                        {{-- Fallback Slide 2 --}}
                        <div id="bb-slide-2" class="billboard-slide space-y-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge badge-info text-2xs font-bold">Teknologi Pembayaran</span>
                                <span class="text-xs text-slate-400">Layanan Otomatis 24 Jam</span>
                            </div>
                            <h3 class="text-lg sm:text-xl font-black text-slate-900 break-words leading-snug">Setoran Simpanan Sukarela & Angsuran Melalui QRIS dan Virtual Account Bank</h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-4xl break-words">
                                Anggota kini dapat melakukan setoran simpanan sukarela dan pembayaran angsuran menggunakan QRIS dinamis serta Virtual Account resmi Bank Mandiri, BRI, BCA, dan BNI. Transaksi terverifikasi otomatis melalui webhook server-to-server tanpa perlu konfirmasi manual bukti transfer.
                            </p>
                            <div class="pt-2 flex flex-wrap items-center gap-2 sm:gap-4 text-xs font-bold text-[#0F4C81]">
                                <a href="#panduan" class="hover:underline">Lihat Panduan Pembayaran Digital &rarr;</a>
                            </div>
                        </div>

                        {{-- Fallback Slide 3 --}}
                        <div id="bb-slide-3" class="billboard-slide space-y-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="badge badge-gray text-2xs font-bold">Kepatuhan Hukum</span>
                                <span class="text-xs text-slate-400">Audit Berkala Kemenkop UKM</span>
                            </div>
                            <h3 class="text-lg sm:text-xl font-black text-slate-900 break-words leading-snug">Hasil Audit Pengawasan Koperasi: Predikat Sehat & Akuntabel</h3>
                            <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-4xl break-words">
                                Berdasarkan hasil pengawasan dan audit independen tata kelola koperasi, KSP Sahabat Sejahtera dinyatakan berstatus <strong>KOPERASI SEHAT</strong> dengan rasio likuiditas aman, kepatuhan batas maksimal denda non-compounding, serta pemisahan tugas kewenangan <em>(Maker-Checker Segregation of Duties)</em>.
                            </p>
                            <div class="pt-2 flex flex-wrap items-center gap-2 sm:gap-4 text-xs font-bold text-[#0F4C81]">
                                <a href="#tentang-kami" class="hover:underline">Informasi Legalitas & Kontak Pengurus &rarr;</a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- STANDAR LAYANAN KOPERASI (4 PILAR KEUNGGULAN) --}}
            <div class="space-y-6">
                <div class="text-center max-w-2xl mx-auto space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-[#0F4C81]">Komitmen Integritas</span>
                    <h2 class="text-2xl font-black text-slate-900">Standar Layanan KSP Sahabat Sejahtera</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Prinsip operasional terpercaya yang menjadi landasan setiap pelayanan kami kepada anggota.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3.5 sm:gap-4">
                    <div class="card p-4 sm:p-5 border-t-4 border-t-[#0F4C81] hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-[#EAF2F8] text-[#0F4C81] flex items-center justify-center font-bold mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1.5">Transparansi Finansial Mutlak</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Semua perhitungan bunga, biaya administrasi, dan simulasi angsuran dihitung terbuka. Tidak ada biaya tersembunyi atau potongan siluman.
                        </p>
                    </div>

                    <div class="card p-4 sm:p-5 border-t-4 border-t-[#2A9D8F] hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-[#EAF6F5] text-[#2A9D8F] flex items-center justify-center font-bold mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1.5">Pencairan Cepat 1x24 Jam</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Proses verifikasi berkas dan analisis kredit terdigitalisasi. Begitu akad digital ditandatangani, dana langsung dicairkan oleh teller.
                        </p>
                    </div>

                    <div class="card p-4 sm:p-5 border-t-4 border-t-[#D4AF37] hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-[#FFF9E6] text-[#D4AF37] flex items-center justify-center font-bold mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1.5">Keadilan & Denda Terbatas</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Denda keterlambatan dibatasi maksimal 10% per angsuran tanpa bunga-berbunga. Tersedia permohonan restrukturisasi jika ada musibah usaha.
                        </p>
                    </div>

                    <div class="card p-4 sm:p-5 border-t-4 border-t-[#1B263B] hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-[#F8F5F0] text-[#1B263B] flex items-center justify-center font-bold mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1.5">Audit Trail & Pembukuan SAK</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Setiap mutasi kas tercatat otomatis dalam jurnal akuntansi berpasangan, neraca saldo real-time, dan siap dipertanggungjawabkan pada RAT.
                        </p>
                    </div>
                </div>
            </div>

            {{-- FAQ & INFORMASI KOPERASI --}}
            <div class="bg-white rounded-3xl p-4 sm:p-6 lg:p-8 border border-[#E8E2D8] space-y-5 sm:space-y-6 overflow-hidden">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-[#EAF6F5] text-[#0F4C81] flex items-center justify-center font-bold">
                        ?
                    </div>
                    <div>
                        <span class="text-2xs font-bold text-[#0F4C81] uppercase tracking-wider block">Pusat Informasi Anggota</span>
                        <h2 class="text-lg font-black text-slate-900">Pertanyaan yang Sering Diajukan (FAQ)</h2>
                    </div>
                </div>

                <div class="space-y-3" id="faqAccordion">
                    {{-- FAQ Item 1 --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all">
                        <button type="button" onclick="toggleFaq('faq-1')" class="w-full text-left p-4 bg-slate-50/70 hover:bg-[#F8F5F0] flex items-center justify-between gap-4 font-bold text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]" aria-expanded="false" id="btn-faq-1">
                            <span>1. Apa saja syarat utama untuk mendaftar menjadi anggota KSP Sahabat Sejahtera?</span>
                            <span class="text-slate-400 font-normal text-lg" id="icon-faq-1">+</span>
                        </button>
                        <div id="faq-1" class="hidden p-4 text-xs sm:text-sm text-slate-600 bg-white leading-relaxed border-t border-slate-100">
                            Syarat pendaftaran sangat mudah: Anda adalah Warga Negara Indonesia (WNI) berusia minimal 18 tahun, memiliki e-KTP dan Kartu Keluarga yang masih berlaku, memiliki kontak WhatsApp aktif, serta bersedia menyetujui Anggaran Dasar & Anggaran Rumah Tangga (AD/ART) Koperasi dan menyetorkan Simpanan Pokok awal.
                        </div>
                    </div>

                    {{-- FAQ Item 2 --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all">
                        <button type="button" onclick="toggleFaq('faq-2')" class="w-full text-left p-4 bg-slate-50/70 hover:bg-[#F8F5F0] flex items-center justify-between gap-4 font-bold text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]" aria-expanded="false" id="btn-faq-2">
                            <span>2. Berapa nominal Simpanan Pokok dan Simpanan Wajib?</span>
                            <span class="text-slate-400 font-normal text-lg" id="icon-faq-2">+</span>
                        </button>
                        <div id="faq-2" class="hidden p-4 text-xs sm:text-sm text-slate-600 bg-white leading-relaxed border-t border-slate-100">
                            Simpanan Pokok adalah sebesar <strong>Rp 100.000</strong> (dibayarkan satu kali di awal pendaftaran sebagai bukti keanggotaan/saham). Simpanan Wajib adalah sebesar <strong>Rp 50.000 per bulan</strong> yang menjadi tabungan rutin anggota dan menjadi dasar perhitungan pembagian Sisa Hasil Usaha (SHU) setiap tahunnya.
                        </div>
                    </div>

                    {{-- FAQ Item 3 --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all">
                        <button type="button" onclick="toggleFaq('faq-3')" class="w-full text-left p-4 bg-slate-50/70 hover:bg-[#F8F5F0] flex items-center justify-between gap-4 font-bold text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]" aria-expanded="false" id="btn-faq-3">
                            <span>3. Bagaimana cara mengajukan pinjaman permodalan usaha secara online?</span>
                            <span class="text-slate-400 font-normal text-lg" id="icon-faq-3">+</span>
                        </button>
                        <div id="faq-3" class="hidden p-4 text-xs sm:text-sm text-slate-600 bg-white leading-relaxed border-t border-slate-100">
                            Setelah akun anggota Anda diverifikasi oleh teller, Anda dapat langsung memilih menu <em>Ajukan Pinjaman</em> di dasbor, memilih produk pinjaman yang sesuai, memasukkan nominal dan tenor, serta melampirkan foto tempat usaha / surat keterangan penghasilan. Analis kredit akan melakukan survei cepat dan menerbitkan akad digital untuk ditandatangani.
                        </div>
                    </div>

                    {{-- FAQ Item 4 --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all">
                        <button type="button" onclick="toggleFaq('faq-4')" class="w-full text-left p-4 bg-slate-50/70 hover:bg-[#F8F5F0] flex items-center justify-between gap-4 font-bold text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-[#2A9D8F]" aria-expanded="false" id="btn-faq-4">
                            <span>4. Bagaimana cara pembayaran angsuran dan apakah ada denda jika terlambat?</span>
                            <span class="text-slate-400 font-normal text-lg" id="icon-faq-4">+</span>
                        </button>
                        <div id="faq-4" class="hidden p-4 text-xs sm:text-sm text-slate-600 bg-white leading-relaxed border-t border-slate-100">
                            Pembayaran dapat dilakukan fleksibel melalui QRIS, Virtual Account bank, transfer rekening, maupun setor langsung ke kasir teller koperasi. Koperasi menerapkan denda harian wajar yang <strong>dibatasi maksimal 10% dari angsuran berjalan (non-compounding)</strong>. Jika mengalami musibah, anggota berhak mengajukan surat permohonan keringanan/penghapusan denda kepada manajer.
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- =========================================================================
         2. FITUR PRODUK SEPERTI PADA SCREENSHOT
         (Katalog Produk Pinjaman Mingguan/Bulanan & Simpanan Anggota)
       ========================================================================= --}}
    <section id="produk" class="py-12 sm:py-16 bg-slate-50 border-b border-[#E8E2D8] w-full max-w-full overflow-hidden" aria-labelledby="heading-produk">
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-10 min-w-0">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-[#0F4C81]">Katalog Lengkap</span>
                <h2 id="heading-produk" class="text-2xl sm:text-3xl font-black text-slate-900">Produk Pembiayaan & Simpanan</h2>
                <p class="text-xs sm:text-sm text-slate-500">Pilihan fasilitas permodalan adil dan simpanan terencana yang dirancang khusus untuk anggota KSP Sahabat Sejahtera.</p>
            </div>

            {{-- Kartu Produk Pinjaman (Dinamis dari Database) --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#0F4C81]"></span>
                        Produk Pinjaman Anggota
                    </h3>
                    <a href="{{ route('loans.products') }}" class="text-xs font-bold text-[#0F4C81] hover:underline">
                        Lihat Semua Rincian &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $p)
                    <div class="card p-6 border-t-4 border-t-[#0F4C81] flex flex-col justify-between hover:shadow-lg transition-all duration-200 bg-white">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="badge badge-info text-2xs">{{ $p->code }}</span>
                                <span class="badge badge-gray text-2xs">Metode: {{ ucfirst($p->calculation_method) }}</span>
                            </div>

                            <div>
                                <h4 class="text-lg font-black text-slate-900">{{ $p->name }}</h4>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $p->description }}</p>
                            </div>

                            <div class="bg-[#F8F5F0] rounded-2xl p-4 text-xs space-y-2.5 border border-[#E8E2D8]">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Suku Bunga:</span>
                                    <strong class="text-[#0F4C81] font-black">{{ $p->interest_rate_per_period }}% / {{ strtolower($p->tenor_unit_label) }}</strong>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Plafon Pinjaman:</span>
                                    <strong class="text-slate-800">Rp {{ $p->min_amount >= 1000000 ? number_format($p->min_amount / 1000000, $p->min_amount % 1000000 === 0 ? 0 : 1) . ' Jt' : number_format($p->min_amount / 1000, 0) . ' Rb' }} - Rp {{ number_format($p->max_amount / 1000000, $p->max_amount % 1000000 === 0 ? 0 : 1) }} Jt</strong>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Pilihan Tenor:</span>
                                    <strong class="text-slate-800">{{ $p->min_tenor }} s.d. {{ $p->max_tenor }} {{ $p->tenor_unit_label }}</strong>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Biaya Administrasi:</span>
                                    <strong class="text-slate-800">{{ $p->admin_fee_fixed > 0 ? "{$p->admin_fee_percentage}% + Rp " . number_format($p->admin_fee_fixed, 0, ',', '.') : "{$p->admin_fee_percentage}%" }}</strong>
                                </div>
                                @if($p->savings_percentage > 0)
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Simpanan Wajib:</span>
                                    <strong class="text-[#2A9D8F]">{{ $p->savings_percentage }}% (Masuk Saldo Simpanan)</strong>
                                </div>
                                @endif
                                <div class="flex items-center justify-between pt-1 border-t border-slate-200/60">
                                    <span class="text-slate-500">Batas Denda Maksimal:</span>
                                    <strong class="text-amber-800">Maks. 10% (Non-Compounding)</strong>
                                </div>
                            </div>
                        </div>

                        <div class="pt-5 mt-4 border-t border-slate-100 flex gap-2">
                            <button type="button" onclick="selectSimulationProduct({{ $p->id }}, {{ $p->min_amount }}, {{ $p->isDaily() ? 14 : ($p->isWeekly() ? 10 : 12) }}, '{{ $p->calculation_method }}')" class="btn btn-secondary btn-sm flex-1 text-center font-bold text-xs py-2">
                                Simulasikan
                            </button>
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm flex-1 text-center font-bold text-xs py-2">
                                Ajukan
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Produk Simpanan Koperasi --}}
            <div class="pt-6">
                <div class="mb-4">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#0F4C81]"></span>
                        Produk Simpanan Anggota
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="card p-6 border-l-4 border-l-[#0F4C81] bg-white space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#0F4C81] uppercase tracking-wider">Simpanan Wajib Awal</span>
                            <span class="badge badge-info text-2xs">Saham Koperasi</span>
                        </div>
                        <h4 class="text-lg font-black text-slate-900">Simpanan Pokok</h4>
                        <div class="text-2xl font-black text-slate-900">Rp 100.000 <span class="text-xs text-slate-400 font-normal">/ sekali setor</span></div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Dibayarkan satu kali saat mendaftar sebagai anggota resmi. Berfungsi sebagai bukti kepemilikan saham koperasi dan dapat diambil kembali saat keluar dari keanggotaan.
                        </p>
                    </div>

                    <div class="card p-6 border-l-4 border-l-[#2A9D8F] bg-white space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#2A9D8F] uppercase tracking-wider">Rutin Bulanan</span>
                            <span class="badge badge-success text-2xs">Dasar SHU</span>
                        </div>
                        <h4 class="text-lg font-black text-slate-900">Simpanan Wajib</h4>
                        <div class="text-2xl font-black text-slate-900">Rp 50.000 <span class="text-xs text-slate-400 font-normal">/ bulan</span></div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Tabungan rutin bulanan anggota yang menjadi dasar perhitungan pembagian dividen Sisa Hasil Usaha (SHU) setiap penutupan buku tahunan koperasi.
                        </p>
                    </div>

                    <div class="card p-6 border-l-4 border-l-[#D4AF37] bg-white space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-[#B45309] uppercase tracking-wider">Fleksibel & Likuid</span>
                            <span class="badge badge-warning text-2xs">Bagi Hasil</span>
                        </div>
                        <h4 class="text-lg font-black text-slate-900">Simpanan Sukarela</h4>
                        <div class="text-2xl font-black text-slate-900">Mulai Rp 10.000 <span class="text-xs text-slate-400 font-normal">/ bebas</span></div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Simpanan bebas yang dapat disetor maupun ditarik sewaktu-waktu oleh anggota. Mendapatkan porsi bagi hasil kompetitif setiap akhir bulan.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- =========================================================================
         3. FITUR PANDUAN MENJADI ANGGOTA & PANDUAN SIMPAN PINJAM
         (Alur Registrasi e-KYC, Alur Transaksi, Syarat Umum Koperasi Online)
       ========================================================================= --}}
    <section id="panduan" class="py-12 sm:py-16 bg-white border-b border-[#E8E2D8] w-full max-w-full overflow-hidden" aria-labelledby="heading-panduan">
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-12 min-w-0">
            
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-[#0F4C81]">Petunjuk Praktis</span>
                <h2 id="heading-panduan" class="text-2xl sm:text-3xl font-black text-slate-900">Panduan Menjadi Anggota & Simpan Pinjam</h2>
                <p class="text-xs sm:text-sm text-slate-500">Langkah mudah bergabung dan alur operasional koperasi online yang transparan dan aman.</p>
                <div class="pt-1">
                    <a href="{{ route('guide') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0F4C81] hover:text-[#1B263B] hover:underline">
                        <span>Buka Halaman Panduan &amp; FAQ Lengkap</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- 4 Langkah Menjadi Anggota --}}
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">A. Panduan 4 Langkah Menjadi Anggota Koperasi</h3>
                    <span class="badge badge-success text-2xs">Proses Cepat Online</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="card p-5 bg-[#F8F5F0] border border-[#E8E2D8] space-y-3 relative">
                        <div class="w-9 h-9 rounded-xl bg-[#0F4C81] text-white font-black flex items-center justify-center text-sm shadow-xs">
                            1
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">Registrasi Akun</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Isi formulir pendaftaran online dengan nama lengkap, NIK, alamat email, dan nomor WhatsApp aktif.
                        </p>
                    </div>

                    <div class="card p-5 bg-[#F8F5F0] border border-[#E8E2D8] space-y-3 relative">
                        <div class="w-9 h-9 rounded-xl bg-[#0F4C81] text-white font-black flex items-center justify-center text-sm shadow-xs">
                            2
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">Unggah Dokumen e-KYC</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Unggah foto e-KTP dan Kartu Keluarga yang jelas untuk verifikasi data kependudukan sesuai aturan perkoperasian.
                        </p>
                    </div>

                    <div class="card p-5 bg-[#F8F5F0] border border-[#E8E2D8] space-y-3 relative">
                        <div class="w-9 h-9 rounded-xl bg-[#0F4C81] text-white font-black flex items-center justify-center text-sm shadow-xs">
                            3
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">Verifikasi Petugas</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Teller dan petugas keanggotaan memvalidasi data Anda dalam waktu 1x24 jam kerja demi keamanan bersama.
                        </p>
                    </div>

                    <div class="card p-5 bg-[#F8F5F0] border border-[#E8E2D8] space-y-3 relative">
                        <div class="w-9 h-9 rounded-xl bg-[#0F4C81] text-white font-black flex items-center justify-center text-sm shadow-xs">
                            4
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">Setor Simpanan & Aktif</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Lakukan setoran Simpanan Pokok via QRIS atau Virtual Account. Akun Anda langsung aktif dan dapat mengajukan pinjaman.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Alur Transaksi Simpan Pinjam Online --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Alur Simpanan --}}
                <div class="card p-6 bg-[#F8F5F0] border border-[#E8E2D8] space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-[#E8E2D8]">
                        <div class="w-8 h-8 rounded-lg bg-[#EAF6F5] text-[#0F4C81] flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">B. Alur Simpanan Anggota</h3>
                            <p class="text-2xs text-slate-500">Mekanisme Setor & Tarik Simpanan</p>
                        </div>
                    </div>

                    <ul class="space-y-3 text-xs text-slate-600">
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#EAF6F5] text-[#2A9D8F] flex items-center justify-center font-bold text-2xs mt-0.5 shrink-0">&check;</span>
                            <div><strong>Setoran Mandiri:</strong> Anggota memilih rekening simpanan sukarela di dasbor, menginput nominal, lalu membayar via QRIS/VA atau transfer bank.</div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#EAF6F5] text-[#2A9D8F] flex items-center justify-center font-bold text-2xs mt-0.5 shrink-0">&check;</span>
                            <div><strong>Konfirmasi Otomatis:</strong> Sistem payment gateway mendeteksi pembayaran server-to-server dan saldo simpanan bertambah seketika.</div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#EAF6F5] text-[#2A9D8F] flex items-center justify-center font-bold text-2xs mt-0.5 shrink-0">&check;</span>
                            <div><strong>Penarikan Simpanan Sukarela:</strong> Dapat diajukan melalui portal online dan diverifikasi oleh teller kasir untuk dicairkan ke rekening bank Anda.</div>
                        </li>
                    </ul>
                </div>

                {{-- Alur Pinjaman --}}
                <div class="card p-6 bg-[#F8F5F0] border border-[#E8E2D8] space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-[#E8E2D8]">
                        <div class="w-8 h-8 rounded-lg bg-[#EAF2F8] text-[#0F4C81] flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">C. Alur Pengajuan Pinjaman Modal</h3>
                            <p class="text-2xs text-slate-500">Mekanisme Pengajuan Hingga Pencairan</p>
                        </div>
                    </div>

                    <ul class="space-y-3 text-xs text-slate-600">
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#EAF2F8] text-[#0F4C81] flex items-center justify-center font-bold text-2xs mt-0.5 shrink-0">&check;</span>
                            <div><strong>Pengajuan & Foto Usaha:</strong> Anggota mengisi jumlah permohonan pinjaman dan melampirkan foto tempat usaha secara live atau unggah berkas.</div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#EAF2F8] text-[#0F4C81] flex items-center justify-center font-bold text-2xs mt-0.5 shrink-0">&check;</span>
                            <div><strong>Analisis Kredit & Otorisasi:</strong> Analis kredit meneliti kelayakan kapasitas bayar, disetujui manajer koperasi secara berjenjang <em>(Maker-Checker)</em>.</div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-[#EAF2F8] text-[#0F4C81] flex items-center justify-center font-bold text-2xs mt-0.5 shrink-0">&check;</span>
                            <div><strong>Akad Digital & Pencairan:</strong> Anggota menandatangani akad digital dengan 1 klik, dan dana pinjaman langsung dicairkan tanpa potongan liar.</div>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Syarat-Syarat Umum Koperasi Online --}}
            <div class="bg-[#1B263B] text-white rounded-3xl p-6 sm:p-8 space-y-4 border border-[#24344E]">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md bg-[#0F4C81] text-white text-2xs font-bold uppercase tracking-wider">Legalitas & Kepatuhan</span>
                    <h3 class="text-lg font-black">Syarat & Ketentuan Umum Anggota KSP Sahabat Sejahtera</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs text-slate-300 leading-relaxed pt-2">
                    <div>
                        <strong class="text-white block mb-1 text-sm">1. Syarat Keanggotaan:</strong>
                        WNI cakap hukum (minimal 18 tahun atau telah menikah), memiliki e-KTP dan Kartu Keluarga sah, memiliki pekerjaan atau usaha aktif, dan berdomisili di wilayah jangkauan operasional.
                    </div>
                    <div>
                        <strong class="text-white block mb-1 text-sm">2. Hak & Kewajiban Anggota:</strong>
                        Berhak menghadiri RAT, berhak memperoleh SHU tahunan, wajib mematuhi ketentuan AD/ART, dan berkewajiban membayar simpanan wajib secara tertib.
                    </div>
                    <div>
                        <strong class="text-white block mb-1 text-sm">3. Ketentuan Pinjaman:</strong>
                        Pinjaman hanya diberikan kepada anggota terdaftar, bebas dari status pinjaman macet, bersedia disurvei, serta menyetujui jadwal angsuran yang telah disimulasikan.
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- =========================================================================
         4. FITUR SIMULASI PINJAMAN (PRAKTEK KALKULATOR INTERAKTIF)
         (Sesuai Alur & Tampilan Yang Sudah Ada)
       ========================================================================= --}}
    <section id="simulasi" class="py-12 sm:py-16 bg-slate-50 border-b border-[#E8E2D8] w-full max-w-full overflow-hidden" aria-labelledby="heading-simulasi">
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-10 min-w-0">
            
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#EAF6F5] text-[#0F4C81] border border-[#2A9D8F]/30">
                    <span class="w-2 h-2 rounded-full bg-[#2A9D8F] animate-pulse"></span>
                    Kalkulator Finansial Transparan
                </div>
                <h2 id="heading-simulasi" class="text-2xl sm:text-3xl font-black text-slate-900">Simulasi Perhitungan Pinjaman Online</h2>
                <p class="text-xs sm:text-sm text-slate-600">
                    Kalkulator mudah untuk seluruh anggota, pedagang pasar, dan pelaku UMKM. Cukup <strong>3 langkah mudah</strong>, angsuran langsung terhitung seketika tanpa perlu pusing.
                </p>
                <div class="pt-1">
                    <a href="{{ route('loans.simulation') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0F4C81] hover:text-[#1B263B] hover:underline">
                        <span>Buka Kalkulator Simulasi Penuh &amp; Jadwal Angsuran</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            {{-- Banner Petunjuk 3 Langkah Sederhana --}}
            <div class="p-3.5 sm:p-4 rounded-2xl bg-white border border-[#E8E2D8] flex flex-wrap items-center justify-between gap-3 text-xs text-slate-700 shadow-2xs">
                <div class="flex items-center gap-2 font-bold text-[#0F4C81]">
                    <span class="w-7 h-7 rounded-lg bg-[#0F4C81] text-white flex items-center justify-center text-xs font-black shadow-xs">💡</span>
                    <span>3 Langkah Mudah Hitung Pinjaman:</span>
                </div>
                <div class="flex items-center gap-2 sm:gap-4 flex-wrap text-2xs sm:text-xs">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#F8F5F0] border border-[#E8E2D8] font-bold text-slate-800">
                        <span class="w-4 h-4 rounded-full bg-[#0F4C81] text-white text-3xs flex items-center justify-center font-bold">1</span>
                        Pilih Jenis Pinjaman
                    </span>
                    <span class="text-slate-400">&rarr;</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#F8F5F0] border border-[#E8E2D8] font-bold text-slate-800">
                        <span class="w-4 h-4 rounded-full bg-[#0F4C81] text-white text-3xs flex items-center justify-center font-bold">2</span>
                        Pilih Uang yang Dipinjam
                    </span>
                    <span class="text-slate-400">&rarr;</span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-[#F8F5F0] border border-[#E8E2D8] font-bold text-slate-800">
                        <span class="w-4 h-4 rounded-full bg-[#0F4C81] text-white text-3xs flex items-center justify-center font-bold">3</span>
                        Pilih Lama Mengangsur
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- Form Input Parameter Simulasi (Kiri) --}}
                <div class="lg:col-span-5 bg-white p-5 sm:p-6 rounded-3xl border-2 border-[#E8E2D8] shadow-sm space-y-5">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-[#EAF6F5] text-[#0F4C81] flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Parameter Pinjaman</h3>
                            <p class="text-2xs text-slate-400">Atur plafon &amp; tenor yang Anda butuhkan</p>
                        </div>
                    </div>

                    <form id="landingSimForm" action="{{ route('home') }}#simulasi" method="GET" class="space-y-5">
                        
                        {{-- LANGKAH 1: PILIH JENIS PINJAMAN --}}
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-extrabold uppercase tracking-wider text-[#0F4C81] flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-[#0F4C81] text-white text-3xs flex items-center justify-center font-black">1</span>
                                    Pilih Produk Pinjaman
                                </label>
                                <span class="text-3xs text-slate-400 font-semibold">Sentuh pilihan</span>
                            </div>

                            {{-- Kartu Pilihan Produk --}}
                            <div class="grid grid-cols-1 gap-2" id="home_product_cards">
                                @foreach($products as $p)
                                    @php
                                        $isCurSelected = ($selectedProduct && $selectedProduct->id == $p->id);
                                        $badgeTxt = $p->isWeekly() ? 'Populer Pedagang' : ($p->isDaily() ? 'Dana Cepat Harian' : 'Bulanan');
                                    @endphp
                                    <div onclick="selectHomeProduct({{ $p->id }})"
                                         id="home_pcard_{{ $p->id }}"
                                         class="home-product-card cursor-pointer p-3 rounded-2xl border-2 transition-all flex items-center justify-between gap-2.5 {{ $isCurSelected ? 'border-[#0F4C81] bg-[#EAF6F5] shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <div class="w-8 h-8 rounded-lg bg-white border border-[#E8E2D8] flex items-center justify-center shrink-0 font-bold text-xs text-[#0F4C81]">
                                                {{ $p->isWeekly() ? '🏪' : ($p->isDaily() ? '⚡' : '🏢') }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5 flex-wrap">
                                                    <span class="text-xs font-bold text-slate-900 truncate">{{ $p->name }}</span>
                                                    <span class="text-3xs px-1.5 py-0.5 rounded font-bold {{ $p->isWeekly() ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">{{ $badgeTxt }}</span>
                                                </div>
                                                <span class="text-3xs text-slate-500 block">Bunga: {{ $p->interest_rate_per_period }}%/{{ strtolower($p->tenor_unit_label) }}</span>
                                            </div>
                                        </div>
                                        <div class="home-check w-4 h-4 rounded-full border-2 flex items-center justify-center shrink-0 {{ $isCurSelected ? 'border-[#0F4C81] bg-[#0F4C81] text-white' : 'border-slate-300 bg-white' }}">
                                            <svg class="w-2.5 h-2.5 {{ $isCurSelected ? 'block' : 'hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Select Dropdown Native (Hidden/Fallback) --}}
                            <select name="product_id" id="sim_product_id" class="sr-only" required>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ ($selectedProduct && $selectedProduct->id == $p->id) ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->interest_rate_per_period }}%/{{ strtolower($p->tenor_unit_label) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- LANGKAH 2: TENTUKAN JUMLAH UANG YANG DIPINJAM --}}
                        <div class="space-y-2.5 pt-3 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <label for="sim_amount" class="text-xs font-extrabold uppercase tracking-wider text-[#0F4C81] flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-[#0F4C81] text-white text-3xs flex items-center justify-center font-black">2</span>
                                    Berapa Uang yang Dipinjam?
                                </label>
                                <span class="text-3xs text-slate-400 font-semibold" id="home_amount_range"></span>
                            </div>

                            {{-- Stepper Besar --}}
                            <div class="bg-slate-50 border-2 border-slate-200 rounded-2xl p-2.5 flex items-center justify-between gap-2 shadow-2xs">
                                <button type="button" onclick="adjustHomeAmount(-1)" 
                                        class="w-10 h-10 rounded-xl bg-white hover:bg-slate-100 active:scale-95 border-2 border-slate-300 text-slate-700 flex items-center justify-center font-black text-xl transition-all shadow-xs"
                                        title="Kurangi Nominal">
                                    &minus;
                                </button>
                                
                                <div class="text-center flex-1 min-w-0">
                                    <div class="text-xl sm:text-2xl font-black text-[#0F4C81] tracking-tight font-mono" id="home_formatted_amount">
                                        Rp {{ number_format($amount, 0, ',', '.') }}
                                    </div>
                                    <span class="text-3xs text-slate-500 font-medium block">Nominal Pinjaman</span>
                                </div>

                                <button type="button" onclick="adjustHomeAmount(1)" 
                                        class="w-10 h-10 rounded-xl bg-[#0F4C81] hover:bg-[#1B263B] active:scale-95 text-white flex items-center justify-center font-black text-xl transition-all shadow-xs"
                                        title="Tambah Nominal">
                                    &#43;
                                </button>
                            </div>

                            {{-- Input Tersembunyi Sinkron --}}
                            <input type="number" id="sim_amount" name="amount" class="sr-only"
                                   value="{{ $amount }}"
                                   required min="{{ $selectedProduct->min_amount ?? 100000 }}" max="{{ $selectedProduct->max_amount ?? 100000000 }}">
                            
                            {{-- Tombol Cepat Nominal --}}
                            <div class="space-y-1">
                                <span class="text-3xs text-slate-500 font-semibold block">Pilihan Cepat:</span>
                                <div class="grid grid-cols-3 sm:grid-cols-5 gap-1.5" id="home_quick_amounts">
                                    {{-- Diisi secara dinamis oleh JS --}}
                                </div>
                            </div>
                        </div>

                        {{-- LANGKAH 3: PILIH LAMA WAKTU MENGANGSUR --}}
                        <div class="space-y-2.5 pt-3 border-t border-slate-100">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-extrabold uppercase tracking-wider text-[#0F4C81] flex items-center gap-2">
                                    <span class="w-5 h-5 rounded-full bg-[#0F4C81] text-white text-3xs flex items-center justify-center font-black">3</span>
                                    Mau Berapa Kali Mengangsur?
                                </label>
                                <span class="text-3xs text-slate-500 font-semibold" id="home_tenor_hint"></span>
                            </div>

                            {{-- Tombol Tenor Cepat --}}
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="home_quick_tenors">
                                {{-- Diisi secara dinamis oleh JS --}}
                            </div>

                            {{-- Input Tersembunyi Sinkron --}}
                            <input type="number" id="sim_tenor" name="tenor" class="sr-only"
                                   value="{{ $tenor }}"
                                   required min="{{ $selectedProduct->min_tenor ?? 1 }}" max="{{ $selectedProduct->max_tenor ?? 60 }}">
                        </div>

                        {{-- PENGATURAN LANJUTAN (ACCORDION) --}}
                        <div class="pt-2 border-t border-slate-100">
                            <details class="group rounded-xl border border-slate-200 bg-slate-50/50 p-2.5 text-xs">
                                <summary class="font-bold text-slate-600 flex items-center justify-between cursor-pointer list-none select-none text-2xs">
                                    <span class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-slate-400 group-open:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        <span>Metode Bunga (Opsional)</span>
                                    </span>
                                    <span class="text-3xs text-slate-400 group-open:hidden">Buka &darr;</span>
                                </summary>
                                <div class="mt-2 pt-2 border-t border-slate-200 space-y-1.5">
                                    <select name="calculation_method" id="sim_method" class="form-control text-xs" onchange="runHomeLiveSimulation()">
                                        <option value="annuity" {{ $method === 'annuity' ? 'selected' : '' }}>Anuitas (Angsuran Tetap Tiap Periode)</option>
                                        <option value="effective" {{ $method === 'effective' ? 'selected' : '' }}>Efektif (Saldo Menurun)</option>
                                        <option value="flat" {{ $method === 'flat' ? 'selected' : '' }}>Flat (Pokok &amp; Bunga Tetap)</option>
                                    </select>
                                </div>
                            </details>
                        </div>

                        <noscript>
                            <button type="submit" class="btn btn-primary w-full py-2.5 rounded-xl font-bold text-xs shadow-xs">
                                Hitung Ulang Simulasi
                            </button>
                        </noscript>
                    </form>
                </div>

                {{-- Hasil Rincian Simulasi (Kanan) --}}
                <div class="lg:col-span-7 space-y-6">
                    @if($simulationResult)
                    <div class="bg-white p-5 sm:p-7 rounded-3xl border-2 border-[#E8E2D8] shadow-sm space-y-6">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <span class="text-2xs font-extrabold uppercase tracking-wider text-[#0F4C81] block">Hasil Estimasi Resmi</span>
                                <h3 class="text-base font-bold text-slate-900">Rincian Angsuran &amp; Pengembalian</h3>
                            </div>
                            <span class="badge badge-success text-2xs font-bold">✓ Bunga Transparan</span>
                        </div>

                        {{-- 1. Estimasi Angsuran Jumbo --}}
                        <div class="p-5 sm:p-6 rounded-3xl bg-[#EAF6F5] border-2 border-[#2A9D8F] text-center space-y-1.5 shadow-2xs">
                            <span class="text-xs font-bold text-[#0F4C81] uppercase tracking-wider block">
                                Estimasi Angsuran Per <span id="home_res_unit_label">{{ $selectedProduct ? $selectedProduct->tenor_unit_label : 'Periode' }}</span>
                            </span>
                            <div class="text-3xl sm:text-4xl font-black text-[#0F4C81]" id="home_res_installment">
                                Rp {{ number_format($simulationResult['first_installment'] ?? $simulationResult['first_month_installment'], 0, ',', '.') }}
                            </div>
                            <span class="text-2xs sm:text-xs text-slate-600 font-semibold block" id="home_res_tenor_sub">
                                Selama {{ $tenor }} {{ $selectedProduct ? $selectedProduct->tenor_unit_label : 'Periode' }} secara rutin sampai lunas.
                            </span>
                        </div>

                        {{-- 2. Rincian Pencairan Bersih Sesuai Tabel Resmi Produk (90%) --}}
                        <div class="p-4 bg-[#F8F5F0] rounded-2xl border-2 border-[#2A9D8F]/30 space-y-2.5">
                            <div class="flex items-center justify-between text-2xs font-bold text-[#0F4C81]">
                                <span class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-[#2A9D8F] animate-pulse"></span>
                                    Ketentuan Resmi Pencairan Sesuai Tabel Produk
                                </span>
                                <span class="text-3xs bg-[#EAF6F5] text-[#2A9D8F] px-2.5 py-0.5 rounded-full font-black border border-[#2A9D8F]/30" id="home_res_badge_amount">
                                    Besar Pinjaman: Rp {{ number_format($amount, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-center text-xs">
                                <div class="p-2.5 bg-white rounded-xl border border-[#E8E2D8] shadow-2xs">
                                    <span class="text-slate-400 block text-3xs uppercase font-bold">Simpanan 5%</span>
                                    <strong class="text-[#2A9D8F] text-xs sm:text-sm" id="home_res_savings">Rp {{ number_format($simulationResult['savings_deduction'], 0, ',', '.') }}</strong>
                                    <span class="text-3xs text-[#2A9D8F] font-semibold block mt-0.5">Tabungan Anda</span>
                                </div>
                                <div class="p-2.5 bg-white rounded-xl border border-[#E8E2D8] shadow-2xs">
                                    <span class="text-slate-400 block text-3xs uppercase font-bold">Administrasi 5%</span>
                                    <strong class="text-[#0F4C81] text-xs sm:text-sm" id="home_res_admin">Rp {{ number_format($simulationResult['admin_fee'], 0, ',', '.') }}</strong>
                                    <span class="text-3xs text-slate-400 block mt-0.5">Layanan Resmi</span>
                                </div>
                                <div class="p-2.5 bg-[#EAF6F5] rounded-xl border-2 border-[#2A9D8F] col-span-2 shadow-2xs">
                                    <span class="text-[#0F4C81] block text-3xs uppercase font-black">Jumlah yang Diterima (Bersih)</span>
                                    <strong class="text-[#0F4C81] text-sm sm:text-base font-black" id="home_res_disbursed">Rp {{ number_format($simulationResult['net_disbursement_amount'], 0, ',', '.') }}</strong>
                                    <span class="text-3xs text-emerald-700 font-bold block mt-0.5">Uang Bersih Diterima (90%)</span>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Grid Ringkasan Finansial --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs text-center">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-slate-400 block text-3xs uppercase font-bold">Pokok Pinjaman</span>
                                <strong class="text-slate-900 text-xs sm:text-sm font-black" id="home_res_principal">Rp {{ number_format($amount, 0, ',', '.') }}</strong>
                            </div>
                            <div class="p-3 bg-[#EAF6F5] rounded-xl border border-[#2A9D8F]/20">
                                <span class="text-slate-500 block text-3xs uppercase font-bold">Total Bunga/Margin</span>
                                <strong class="text-[#2A9D8F] text-xs sm:text-sm font-black" id="home_res_interest">Rp {{ number_format($simulationResult['total_interest'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-slate-400 block text-3xs uppercase font-bold">Biaya Admin</span>
                                <strong class="text-slate-900 text-xs sm:text-sm font-black" id="home_res_admin_fee">Rp {{ number_format($simulationResult['admin_fee'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-slate-400 block text-3xs uppercase font-bold">Total Pengembalian</span>
                                <strong class="text-slate-900 text-xs sm:text-sm font-black" id="home_res_payable">Rp {{ number_format($simulationResult['total_payable'], 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        {{-- 4. Tabel Jadwal 5 Angsuran Pertama --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                                <span>Preview 5 Jadwal Angsuran Pertama</span>
                                <a id="home_link_full_sim" href="{{ route('loans.simulation', ['product_id' => $selectedProduct->id, 'amount' => $amount, 'tenor' => $tenor, 'calculation_method' => $method]) }}" class="text-[#0F4C81] hover:underline font-bold">Lihat Rincian Jadwal Penuh &rarr;</a>
                            </div>

                            <div class="table-responsive bg-white rounded-xl border border-[#E8E2D8] overflow-x-auto">
                                <table class="text-xs w-full">
                                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase text-3xs">
                                        <tr>
                                            <th class="py-2 px-3">Periode</th>
                                            <th class="py-2 px-3">Jatuh Tempo</th>
                                            <th class="py-2 px-3">Pokok</th>
                                            <th class="py-2 px-3">Bunga</th>
                                            <th class="py-2 px-3">Total Angsuran</th>
                                        </tr>
                                    </thead>
                                    <tbody id="home_schedule_tbody">
                                        @foreach(array_slice($simulationResult['schedules'], 0, 5) as $s)
                                        <tr class="hover:bg-slate-50 border-b border-slate-100 last:border-0">
                                            <td class="py-2 px-3 font-bold text-slate-800">Ke-{{ $s['installment_no'] }}</td>
                                            <td class="py-2 px-3 font-mono text-slate-600 text-2xs">{{ \Carbon\Carbon::parse($s['due_date'])->format('d/m/Y') }}</td>
                                            <td class="py-2 px-3 font-mono text-slate-700">Rp {{ number_format($s['principal_amount'], 0, ',', '.') }}</td>
                                            <td class="py-2 px-3 font-mono text-slate-700">Rp {{ number_format($s['interest_amount'], 0, ',', '.') }}</td>
                                            <td class="py-2 px-3 font-mono font-bold text-[#0F4C81]">Rp {{ number_format($s['total_amount'], 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 5. Tombol Aksi Utama & Bantuan WhatsApp CS --}}
                        <div class="pt-2 flex flex-col sm:flex-row gap-3">
                            <a id="home_btn_apply" href="{{ auth()->check() ? route('loans.apply', ['product_id' => $selectedProduct->id, 'amount' => $amount, 'tenor' => $tenor]) : route('login') }}" class="btn btn-gold flex-1 py-3.5 rounded-2xl font-extrabold text-xs text-center shadow-md transition-all flex items-center justify-center gap-2">
                                <span>Ajukan Pinjaman Ini Sekarang</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>

                            @php
                                $homeWaText = "Halo KSP Sahabat Sejahtera, saya ingin dibantu simulasi pinjaman: Produk " . ($selectedProduct->name ?? 'Pinjaman') . ", Plafon Rp " . number_format($amount, 0, ',', '.') . ", Tenor " . $tenor . " " . ($selectedProduct->tenor_unit_label ?? 'Bulan') . ". Mohon informasinya. Terima kasih.";
                                $homeWaUrl = "https://wa.me/6282197233174?text=" . urlencode($homeWaText);
                            @endphp
                            <a id="home_btn_wa" href="{{ $homeWaUrl }}" target="_blank" rel="noopener" class="py-3.5 px-5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs text-center shadow-md transition-all flex items-center justify-center gap-2 shrink-0">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span>Tanya WhatsApp CS</span>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

            </div>

        </div>
    </section>

    {{-- =========================================================================
         5. FITUR TENTANG KAMI
         (Kontak Layanan Admin, Lokasi Kantor Pusat, Tampilan GMaps, Email & Medsos)
       ========================================================================= --}}
    <section id="tentang-kami" class="py-12 sm:py-16 bg-white border-b border-[#E8E2D8] w-full max-w-full overflow-hidden" aria-labelledby="heading-tentang">
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 space-y-8 sm:space-y-12 min-w-0">
            
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-[#0F4C81]">Hubungi & Kunjungi Kami</span>
                <h2 id="heading-tentang" class="text-2xl sm:text-3xl font-black text-slate-900">Tentang Kami & Layanan Anggota</h2>
                <p class="text-xs sm:text-sm text-slate-500">Pusat informasi kontak, alamat kantor operasional, jam layanan, dan peta lokasi kantor pusat KSP Sahabat Sejahtera.</p>
                <div class="pt-1">
                    <a href="{{ route('about') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#0F4C81] hover:text-[#1B263B] hover:underline">
                        <span>Lihat Profil Lengkap, Legalitas &amp; 7 Prinsip Koperasi</span>
                        <span>&rarr;</span>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- Rincian Kontak & Kantor (Kiri) --}}
                <div class="lg:col-span-6 space-y-6">
                    <div class="card p-6 bg-[#F8F5F0] border border-[#E8E2D8] space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-[#E8E2D8]">
                            <div class="w-10 h-10 rounded-xl bg-[#0F4C81] text-white flex items-center justify-center font-bold shadow-xs">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Kantor Pusat KSP Sahabat Sejahtera</h3>
                                <p class="text-xs text-slate-500">Badan Hukum No. AHU-0012345.AH.01.26.TAHUN 2026</p>
                            </div>
                        </div>

                        <div class="space-y-4 text-xs">
                            {{-- Alamat Kantor --}}
                            <div class="flex items-start gap-3">
                                <span class="p-2 rounded-lg bg-[#EAF6F5] text-[#0F4C81] shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </span>
                                <div>
                                    <strong class="text-slate-800 block mb-0.5">Lokasi Kantor Pusat:</strong>
                                    <span class="text-slate-600 leading-relaxed">
                                        Gedung Graha Sahabat Sejahtera Lantai 1-2, Jl. Perintis Kemerdekaan KM 9 No. 45, Tamalanrea, Kota Makassar, Sulawesi Selatan 90245
                                    </span>
                                </div>
                            </div>

                            {{-- Kontak Admin & CS --}}
                            <div class="flex items-start gap-3">
                                <span class="p-2 rounded-lg bg-[#EAF6F5] text-[#0F4C81] shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </span>
                                <div>
                                    <strong class="text-slate-800 block mb-0.5">Kontak Layanan Admin & CS:</strong>
                                    <div class="space-y-1 text-slate-600">
                                        <div>WhatsApp CS 24 Jam: <a href="https://wa.me/6282197233174" target="_blank" rel="noopener" class="text-[#0F4C81] font-bold hover:underline">+62 821-9723-3174</a></div>
                                        <div>Telepon Operasional: <span class="font-bold text-slate-800">(0411) 892345</span></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Jam Layanan Kas --}}
                            <div class="flex items-start gap-3">
                                <span class="p-2 rounded-lg bg-[#EAF6F5] text-[#0F4C81] shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div>
                                    <strong class="text-slate-800 block mb-0.5">Jam Operasional Layanan:</strong>
                                    <div class="text-slate-600 space-y-0.5">
                                        <div>Layanan Kas Kantor: Senin - Jumat (08.00 - 16.00 WITA), Sabtu (08.30 - 13.00 WITA)</div>
                                        <div>Layanan Sistem Online: <strong class="text-[#0F4C81]">24 Jam Nonstop Setiap Hari</strong></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Email Resmi --}}
                            <div class="flex items-start gap-3">
                                <span class="p-2 rounded-lg bg-[#EAF6F5] text-[#0F4C81] shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </span>
                                <div>
                                    <strong class="text-slate-800 block mb-0.5">Alamat Email Resmi:</strong>
                                    <div class="space-y-0.5 text-slate-600">
                                        <div>Layanan Anggota: <a href="mailto:layanan@koperasi.systemlji.com" class="text-[#0F4C81] font-bold hover:underline">layanan@koperasi.systemlji.com</a></div>
                                        <div>Pengurus Koperasi: <a href="mailto:pengurus@koperasi.systemlji.com" class="text-[#0F4C81] font-bold hover:underline">pengurus@koperasi.systemlji.com</a></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sosial Media --}}
                            <div class="flex items-start gap-3 pt-2">
                                <span class="p-2 rounded-lg bg-[#EAF6F5] text-[#0F4C81] shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </span>
                                <div>
                                    <strong class="text-slate-800 block mb-1">Sosial Media Resmi:</strong>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="https://instagram.com" target="_blank" rel="noopener" class="px-2.5 py-1 rounded-md bg-white border border-[#E8E2D8] text-slate-700 hover:text-[#0F4C81] hover:border-[#0F4C81] font-semibold text-2xs">Instagram</a>
                                        <a href="https://facebook.com" target="_blank" rel="noopener" class="px-2.5 py-1 rounded-md bg-white border border-[#E8E2D8] text-slate-700 hover:text-[#0F4C81] hover:border-[#0F4C81] font-semibold text-2xs">Facebook</a>
                                        <a href="https://youtube.com" target="_blank" rel="noopener" class="px-2.5 py-1 rounded-md bg-white border border-[#E8E2D8] text-slate-700 hover:text-[#0F4C81] hover:border-[#0F4C81] font-semibold text-2xs">YouTube</a>
                                        <a href="https://linkedin.com" target="_blank" rel="noopener" class="px-2.5 py-1 rounded-md bg-white border border-[#E8E2D8] text-slate-700 hover:text-[#0F4C81] hover:border-[#0F4C81] font-semibold text-2xs">LinkedIn</a>
                                        <a href="https://wa.me/6282197233174" target="_blank" rel="noopener" class="px-2.5 py-1 rounded-md bg-[#EAF6F5] border border-[#2A9D8F]/40 text-[#0F4C81] font-bold text-2xs">Saluran WhatsApp</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tampilan Google Maps Kantor Pusat (Kanan) --}}
                <div class="lg:col-span-6 space-y-4">
                    <div class="card p-4 sm:p-5 bg-white border border-[#E8E2D8] shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                Peta Lokasi Kantor Pusat (Google Maps)
                            </h3>
                            <a href="https://maps.google.com/?q=Makassar" target="_blank" rel="noopener" class="text-2xs font-bold text-[#0F4C81] hover:underline">
                                Buka di Google Maps &rarr;
                            </a>
                        </div>

                        {{-- Embed Google Maps Responsif --}}
                        <div class="w-full h-80 sm:h-96 rounded-2xl overflow-hidden border border-[#E8E2D8] shadow-inner relative bg-slate-100">
                            <iframe 
                                title="Peta Lokasi Kantor Pusat KSP Sahabat Sejahtera Makassar"
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d127163.66538475225!2d119.38722774999999!3d-5.147665099999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbf152d19b78809%3A0x3030bfbcaf770b0!2sMakassar%2C%20Kota%20Makassar%2C%20Sulawesi%20Selatan!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                            <div class="absolute bottom-3 left-3 bg-white/95 backdrop-blur-xs p-2.5 rounded-xl border border-[#E8E2D8] shadow text-3xs font-semibold text-slate-700">
                                Graha Sahabat Sejahtera &bull; Tamalanrea, Kota Makassar
                            </div>
                        </div>

                        <p class="text-2xs text-slate-400">
                            *Disediakan fasilitas parkir luas, ruang pelayanan ber-AC, dan loket kas teller ramah disabilitas.
                        </p>
                    </div>
                </div>

            </div>

        </div>
    </section>

    {{-- =========================================================================
         6. FITUR MASUKKAN AKUN & 7. DAFTAR ANGGOTA BARU (CALL TO ACTION)
       ========================================================================= --}}
    <section id="masuk-daftar" class="py-12 sm:py-16 bg-[#F8F5F0] border-t border-[#E8E2D8] w-full max-w-full overflow-hidden" aria-labelledby="heading-cta">
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 min-w-0">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Masukkan Akun (Login) --}}
                <div class="bg-white rounded-3xl p-5 sm:p-8 border border-[#E8E2D8] shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-[#F8F5F0] text-[#0F4C81] flex items-center justify-center font-bold">
                            <svg class="w-6 h-6 text-[#0F4C81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Sudah Menjadi Anggota?</h3>
                        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                            Akses portal anggota koperasi untuk memeriksa mutasi simpanan, riwayat pembayaran cicilan, status pengajuan permodalan, dan notifikasi SHU.
                        </p>
                    </div>

                    <a href="{{ route('login') }}" class="btn btn-secondary w-full py-3.5 rounded-xl font-bold text-sm text-center shadow-xs hover:bg-[#F8F5F0] transition-all flex items-center justify-center gap-2">
                        <span>Masuk ke Akun Koperasi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                {{-- Daftar Anggota Baru (Register) --}}
                <div class="bg-[#1B263B] text-white rounded-3xl p-5 sm:p-8 border border-[#24344E] shadow-xl flex flex-col justify-between space-y-6 relative overflow-hidden">
                    <div class="space-y-3 relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 text-[#D4AF37] flex items-center justify-center font-bold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-white">Belum Terdaftar? Daftar Sekarang!</h3>
                        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                            Bergabunglah bersama 1.450+ pelaku usaha dan keluarga sejahtera lainnya. Dapatkan fasilitas simpanan dengan bagi hasil berkah dan akses permodalan mudah.
                        </p>
                    </div>

                    <a href="{{ route('register') }}" class="btn-gold w-full py-3.5 rounded-xl font-bold text-sm text-center shadow-md flex items-center justify-center gap-2 relative z-10 text-[#1B263B]">
                        <span>Daftar Anggota Baru Sekarang</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

            </div>
        </div>
    </section>

</div>
@endsection

@section('scripts')
<script>
    // Tab Billboard Pengumuman Dinamis
    function switchBillboard(index) {
        const slides = document.querySelectorAll('.billboard-slide');
        const tabs = document.querySelectorAll('.billboard-tab-btn');

        slides.forEach((slide, i) => {
            if (i === index) {
                slide.classList.add('active');
            } else {
                slide.classList.remove('active');
            }
        });

        tabs.forEach((tab, i) => {
            if (i === index) {
                tab.classList.add('bg-[#0F4C81]', 'text-white', 'shadow-2xs', 'font-bold');
                tab.classList.remove('text-slate-600', 'font-semibold');
                tab.setAttribute('aria-selected', 'true');
            } else {
                tab.classList.remove('bg-[#0F4C81]', 'text-white', 'shadow-2xs', 'font-bold');
                tab.classList.add('text-slate-600', 'font-semibold');
                tab.setAttribute('aria-selected', 'false');
            }
        });
    }

    // FAQ Accordion Toggle
    function toggleFaq(id) {
        const item = document.getElementById(id);
        const icon = document.getElementById('icon-' + id);
        const btn = document.getElementById('btn-' + id);
        if (item && icon && btn) {
            const isHidden = item.classList.contains('hidden');
            if (isHidden) {
                item.classList.remove('hidden');
                icon.textContent = '−';
                btn.setAttribute('aria-expanded', 'true');
            } else {
                item.classList.add('hidden');
                icon.textContent = '+';
                btn.setAttribute('aria-expanded', 'false');
            }
        }
    }

    // =========================================================================
    // SIMULASI PINJAMAN INTERAKTIF RAMAH TARGET MINIM LITERASI DIGITAL (BERANDA)
    // =========================================================================
    const homeProductsData = @json($products);
    let homeSelectedProductId = {{ $selectedProduct ? $selectedProduct->id : ($products->first()?->id ?? 0) }};
    const homeApplyBaseUrl = "{{ route('loans.apply') }}";
    const homeLoginUrl = "{{ route('login') }}";
    const homeIsAuth = {{ auth()->check() ? 'true' : 'false' }};
    const homeWaBaseNumber = "6282197233174";

    function homeFormatRupiah(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    function getHomeSelectedProduct() {
        return homeProductsData.find(p => p.id === homeSelectedProductId) || homeProductsData[0];
    }

    function selectHomeProduct(productId) {
        homeSelectedProductId = productId;
        const product = getHomeSelectedProduct();

        const select = document.getElementById('sim_product_id');
        if (select) select.value = productId;

        // Visual cards styling
        document.querySelectorAll('.home-product-card').forEach(card => {
            card.classList.remove('border-[#0F4C81]', 'bg-[#EAF6F5]', 'shadow-xs');
            card.classList.add('border-slate-200', 'bg-white');
            const check = card.querySelector('.home-check');
            if (check) {
                check.classList.remove('border-[#0F4C81]', 'bg-[#0F4C81]', 'text-white');
                check.classList.add('border-slate-300', 'bg-white');
                const svg = check.querySelector('svg');
                if (svg) svg.classList.add('hidden');
            }
        });

        const activeCard = document.getElementById('home_pcard_' + productId);
        if (activeCard) {
            activeCard.classList.remove('border-slate-200', 'bg-white');
            activeCard.classList.add('border-[#0F4C81]', 'bg-[#EAF6F5]', 'shadow-xs');
            const check = activeCard.querySelector('.home-check');
            if (check) {
                check.classList.remove('border-slate-300', 'bg-white');
                check.classList.add('border-[#0F4C81]', 'bg-[#0F4C81]', 'text-white');
                const svg = check.querySelector('svg');
                if (svg) svg.classList.remove('hidden');
            }
        }

        // Tentukan default nominal sesuai produk
        let defaultAmount = 10000000;
        if (product.tenor_unit === 'week') defaultAmount = 200000;
        else if (product.tenor_unit === 'day') defaultAmount = 500000;

        if (defaultAmount < product.min_amount) defaultAmount = product.min_amount;
        if (defaultAmount > product.max_amount) defaultAmount = product.max_amount;

        const amountInput = document.getElementById('sim_amount');
        if (amountInput) {
            amountInput.value = defaultAmount;
            amountInput.min = product.min_amount;
            amountInput.max = product.max_amount;
        }

        // Tentukan default tenor
        let defaultTenor = 12;
        if (product.tenor_unit === 'week') defaultTenor = 10;
        else if (product.tenor_unit === 'day') defaultTenor = 14;

        if (defaultTenor < product.min_tenor) defaultTenor = product.min_tenor;
        if (defaultTenor > product.max_tenor) defaultTenor = product.max_tenor;

        const tenorInput = document.getElementById('sim_tenor');
        if (tenorInput) {
            tenorInput.value = defaultTenor;
            tenorInput.min = product.min_tenor;
            tenorInput.max = product.max_tenor;
        }

        const methodSelect = document.getElementById('sim_method');
        if (methodSelect && product.calculation_method) {
            methodSelect.value = product.calculation_method;
        }

        renderHomePillsAndRecalculate();
    }

    function renderHomePillsAndRecalculate() {
        const product = getHomeSelectedProduct();
        const currentAmount = parseInt(document.getElementById('sim_amount').value) || product.min_amount;
        const currentTenor = parseInt(document.getElementById('sim_tenor').value) || product.min_tenor;

        // 1. Render Tombol Nominal Cepat
        const amountContainer = document.getElementById('home_quick_amounts');
        let amountPresets = [];
        if (product.tenor_unit === 'week') {
            amountPresets = [200000, 500000, 1000000, 2000000, 5000000];
        } else if (product.tenor_unit === 'day') {
            amountPresets = [100000, 250000, 500000, 750000, 1000000];
        } else {
            amountPresets = [2000000, 5000000, 10000000, 25000000, 50000000];
        }

        amountPresets = amountPresets.filter(a => a >= product.min_amount && a <= product.max_amount);
        if (!amountPresets.includes(product.min_amount)) amountPresets.unshift(product.min_amount);

        let amountHtml = '';
        amountPresets.forEach(amt => {
            const isAct = (amt === currentAmount);
            const label = amt >= 1000000 ? (amt / 1000000) + ' Jt' : (amt / 1000) + ' Rb';
            const isStandard = (product.tenor_unit === 'week' && amt === 200000);
            amountHtml += `
                <button type="button" onclick="setAmount(${amt})" 
                        class="px-2 py-1.5 rounded-xl text-3xs sm:text-2xs font-bold transition-all text-center border-2 ${isAct ? 'bg-[#0F4C81] text-white border-[#0F4C81] shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200'}">
                    ${label} ${isStandard ? '<span class="block text-3xs opacity-80">(Standar)</span>' : ''}
                </button>
            `;
        });
        if (amountContainer) amountContainer.innerHTML = amountHtml;

        // 2. Render Tombol Tenor Cepat
        const tenorContainer = document.getElementById('home_quick_tenors');
        let tenorPresets = [];
        if (product.tenor_unit === 'week') {
            tenorPresets = [
                { t: 10, label: '10 Minggu', sub: 'Standar (10x)' },
                { t: 4, label: '4 Minggu', sub: '1 Bulan' },
                { t: 8, label: '8 Minggu', sub: '2 Bulan' },
                { t: 16, label: '16 Minggu', sub: '4 Bulan' },
                { t: 24, label: '24 Minggu', sub: '6 Bulan' }
            ];
        } else if (product.tenor_unit === 'day') {
            tenorPresets = [
                { t: 7, label: '7 Hari', sub: '1 Minggu' },
                { t: 10, label: '10 Hari', sub: '10x Bayar' },
                { t: 14, label: '14 Hari', sub: '2 Minggu' },
                { t: 21, label: '21 Hari', sub: '3 Minggu' },
                { t: 30, label: '30 Hari', sub: '1 Bulan' }
            ];
        } else {
            tenorPresets = [
                { t: 6, label: '6 Bulan', sub: 'Setengah Tahun' },
                { t: 12, label: '12 Bulan', sub: '1 Tahun' },
                { t: 24, label: '24 Bulan', sub: '2 Tahun' },
                { t: 36, label: '36 Bulan', sub: '3 Tahun' }
            ];
        }

        tenorPresets = tenorPresets.filter(item => item.t >= product.min_tenor && item.t <= product.max_tenor);

        let tenorHtml = '';
        tenorPresets.forEach(item => {
            const isAct = (item.t === currentTenor);
            tenorHtml += `
                <button type="button" onclick="setTenor(${item.t})" 
                        class="p-2 rounded-xl text-left border-2 transition-all ${isAct ? 'bg-[#0F4C81] text-white border-[#0F4C81] shadow-2xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-800 border-slate-200'}">
                    <span class="block text-xs font-black">${item.label}</span>
                    <span class="block text-3xs ${isAct ? 'text-amber-200' : 'text-slate-500'} font-medium">${item.sub}</span>
                </button>
            `;
        });
        if (tenorContainer) tenorContainer.innerHTML = tenorHtml;

        const rangeLabel = document.getElementById('home_amount_range');
        if (rangeLabel) {
            rangeLabel.textContent = `Min ${homeFormatRupiah(product.min_amount)} - Max ${homeFormatRupiah(product.max_amount)}`;
        }

        const tenorHint = document.getElementById('home_tenor_hint');
        if (tenorHint) {
            tenorHint.textContent = `Satuan: ${product.tenor_unit_label}`;
        }

        runHomeLiveSimulation();
    }

    function setAmount(val) {
        const input = document.getElementById('sim_amount');
        if (input) {
            input.value = val;
            renderHomePillsAndRecalculate();
        }
    }

    function adjustHomeAmount(direction) {
        const product = getHomeSelectedProduct();
        const input = document.getElementById('sim_amount');
        if (!input) return;

        let cur = parseInt(input.value) || product.min_amount;
        let step = 100000;
        if (product.tenor_unit === 'day') step = 50000;
        else if (product.tenor_unit === 'month') step = 500000;

        let next = cur + (direction * step);
        if (next < product.min_amount) next = product.min_amount;
        if (next > product.max_amount) next = product.max_amount;

        input.value = next;
        renderHomePillsAndRecalculate();
    }

    function setTenor(val) {
        const input = document.getElementById('sim_tenor');
        if (input) {
            input.value = val;
            renderHomePillsAndRecalculate();
        }
    }

    function runHomeLiveSimulation() {
        const product = getHomeSelectedProduct();
        const principal = parseInt(document.getElementById('sim_amount').value) || product.min_amount;
        const tenor = parseInt(document.getElementById('sim_tenor').value) || product.min_tenor;
        const methodSelect = document.getElementById('sim_method');
        const method = methodSelect ? methodSelect.value : (product.calculation_method || 'flat');

        const dispAmount = document.getElementById('home_formatted_amount');
        if (dispAmount) dispAmount.textContent = homeFormatRupiah(principal);

        // 1. Simpanan 5% & Administrasi 5%
        const savingsDeduction = Math.round(principal * ((product.savings_percentage || 5.0) / 100));
        const adminFee = Math.round(principal * ((product.admin_fee_percentage || 5.0) / 100) + (product.admin_fee_fixed || 0));
        const netDisbursement = Math.max(0, principal - savingsDeduction - adminFee);

        // 2. Kalkulasi Angsuran
        const ratePerPeriod = parseFloat(product.interest_rate_per_period) || 0;
        const rateDecimal = ratePerPeriod / 100.0;
        let totalInterest = 0;
        let firstInstallment = 0;
        let schedules = [];

        let remainingPrincipal = principal;
        const today = new Date();

        if (method === 'flat') {
            const periodPrincipal = Math.floor(principal / tenor);
            const periodInterest = Math.round(principal * rateDecimal);

            for (let i = 1; i <= tenor; i++) {
                const principalPortion = (i === tenor) ? remainingPrincipal : periodPrincipal;
                const interestPortion = periodInterest;
                const totalInstallment = principalPortion + interestPortion;
                remainingPrincipal -= principalPortion;
                totalInterest += interestPortion;

                const dueDate = new Date(today);
                if (product.tenor_unit === 'day') dueDate.setDate(today.getDate() + i);
                else if (product.tenor_unit === 'week') dueDate.setDate(today.getDate() + (i * 7));
                else dueDate.setMonth(today.getMonth() + i);

                schedules.push({
                    installment_no: i,
                    due_date: dueDate.toLocaleDateString('id-ID'),
                    principal_amount: principalPortion,
                    interest_amount: interestPortion,
                    total_amount: totalInstallment,
                });
            }
            firstInstallment = schedules[0] ? schedules[0].total_amount : 0;
        } else if (method === 'annuity') {
            let annuityPayment = 0;
            if (rateDecimal > 0) {
                annuityPayment = principal * (rateDecimal / (1 - Math.pow(1 + rateDecimal, -tenor)));
            } else {
                annuityPayment = principal / tenor;
            }

            for (let i = 1; i <= tenor; i++) {
                const interestPortion = Math.round(remainingPrincipal * rateDecimal);
                let principalPortion = (i === tenor) ? remainingPrincipal : Math.round(annuityPayment - interestPortion);
                if (principalPortion > remainingPrincipal) principalPortion = remainingPrincipal;

                const totalInstallment = (i === tenor) ? (principalPortion + interestPortion) : Math.round(annuityPayment);
                remainingPrincipal -= principalPortion;
                totalInterest += interestPortion;

                const dueDate = new Date(today);
                if (product.tenor_unit === 'day') dueDate.setDate(today.getDate() + i);
                else if (product.tenor_unit === 'week') dueDate.setDate(today.getDate() + (i * 7));
                else dueDate.setMonth(today.getMonth() + i);

                schedules.push({
                    installment_no: i,
                    due_date: dueDate.toLocaleDateString('id-ID'),
                    principal_amount: principalPortion,
                    interest_amount: interestPortion,
                    total_amount: totalInstallment,
                });
            }
            firstInstallment = schedules[0] ? schedules[0].total_amount : 0;
        } else {
            // Effective
            const periodPrincipal = Math.floor(principal / tenor);
            for (let i = 1; i <= tenor; i++) {
                const interestPortion = Math.round(remainingPrincipal * rateDecimal);
                const principalPortion = (i === tenor) ? remainingPrincipal : periodPrincipal;
                const totalInstallment = principalPortion + interestPortion;
                remainingPrincipal -= principalPortion;
                totalInterest += interestPortion;

                const dueDate = new Date(today);
                if (product.tenor_unit === 'day') dueDate.setDate(today.getDate() + i);
                else if (product.tenor_unit === 'week') dueDate.setDate(today.getDate() + (i * 7));
                else dueDate.setMonth(today.getMonth() + i);

                schedules.push({
                    installment_no: i,
                    due_date: dueDate.toLocaleDateString('id-ID'),
                    principal_amount: principalPortion,
                    interest_amount: interestPortion,
                    total_amount: totalInstallment,
                });
            }
            firstInstallment = schedules[0] ? schedules[0].total_amount : 0;
        }

        const totalCost = totalInterest + adminFee;
        const totalPayable = principal + totalCost;

        // 3. Update DOM Hasil Beranda
        const resInst = document.getElementById('home_res_installment');
        if (resInst) resInst.textContent = homeFormatRupiah(firstInstallment);

        const resUnit = document.getElementById('home_res_unit_label');
        if (resUnit) resUnit.textContent = product.tenor_unit_label;

        const resTenorSub = document.getElementById('home_res_tenor_sub');
        if (resTenorSub) resTenorSub.textContent = `Selama ${tenor} ${product.tenor_unit_label} secara rutin sampai lunas.`;

        const resBadgeAmt = document.getElementById('home_res_badge_amount');
        if (resBadgeAmt) resBadgeAmt.textContent = `Besar Pinjaman: ${homeFormatRupiah(principal)}`;

        const resSavings = document.getElementById('home_res_savings');
        if (resSavings) resSavings.textContent = homeFormatRupiah(savingsDeduction);

        const resAdmin = document.getElementById('home_res_admin');
        if (resAdmin) resAdmin.textContent = homeFormatRupiah(adminFee);

        const resDisbursed = document.getElementById('home_res_disbursed');
        if (resDisbursed) resDisbursed.textContent = homeFormatRupiah(netDisbursement);

        const resPrincipal = document.getElementById('home_res_principal');
        if (resPrincipal) resPrincipal.textContent = homeFormatRupiah(principal);

        const resInterest = document.getElementById('home_res_interest');
        if (resInterest) resInterest.textContent = homeFormatRupiah(totalInterest);

        const resAdminFee = document.getElementById('home_res_admin_fee');
        if (resAdminFee) resAdminFee.textContent = homeFormatRupiah(adminFee);

        const resPayable = document.getElementById('home_res_payable');
        if (resPayable) resPayable.textContent = homeFormatRupiah(totalPayable);

        // Preview 5 Angsuran
        const tbody = document.getElementById('home_schedule_tbody');
        if (tbody) {
            let tbodyHtml = '';
            schedules.slice(0, 5).forEach(s => {
                tbodyHtml += `
                    <tr class="hover:bg-slate-50 border-b border-slate-100 last:border-0">
                        <td class="py-2 px-3 font-bold text-slate-800">Ke-${s.installment_no}</td>
                        <td class="py-2 px-3 font-mono text-slate-600 text-2xs">${s.due_date}</td>
                        <td class="py-2 px-3 font-mono text-slate-700">${homeFormatRupiah(s.principal_amount)}</td>
                        <td class="py-2 px-3 font-mono text-slate-700">${homeFormatRupiah(s.interest_amount)}</td>
                        <td class="py-2 px-3 font-mono font-bold text-[#0F4C81]">${homeFormatRupiah(s.total_amount)}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = tbodyHtml;
        }

        // Link ke Simulasi Penuh
        const fullLink = document.getElementById('home_link_full_sim');
        if (fullLink) {
            fullLink.href = `{{ route('loans.simulation') }}?product_id=${product.id}&amount=${principal}&tenor=${tenor}&calculation_method=${method}`;
        }

        // Tombol Ajukan & WhatsApp CS
        const btnApply = document.getElementById('home_btn_apply');
        if (btnApply && homeIsAuth) {
            btnApply.href = `${homeApplyBaseUrl}?product_id=${product.id}&amount=${principal}&tenor=${tenor}`;
        }

        const btnWa = document.getElementById('home_btn_wa');
        if (btnWa) {
            const waMsg = `Halo KSP Sahabat Sejahtera, saya ingin dibantu simulasi pinjaman: Produk ${product.name}, Plafon ${homeFormatRupiah(principal)}, Tenor ${tenor} ${product.tenor_unit_label} (Angsuran ${homeFormatRupiah(firstInstallment)}/${product.tenor_unit_label}). Mohon informasinya. Terima kasih.`;
            btnWa.href = `https://wa.me/${homeWaBaseNumber}?text=${encodeURIComponent(waMsg)}`;
        }
    }

    // Tombol "Simulasikan" dari Katalog Produk Atas
    function selectSimulationProduct(productId, amount, tenor, method) {
        selectHomeProduct(productId);
        if (amount) setAmount(amount);
        if (tenor) setTenor(tenor);
        if (method) {
            const methodSelect = document.getElementById('sim_method');
            if (methodSelect) methodSelect.value = method;
        }

        // Scroll halus ke kalkulator simulasi
        const simSection = document.getElementById('simulasi');
        if (simSection) {
            simSection.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Inisialisasi saat Halaman Beranda Dimuat
    document.addEventListener('DOMContentLoaded', function() {
        renderHomePillsAndRecalculate();
    });
</script>
@endsection
