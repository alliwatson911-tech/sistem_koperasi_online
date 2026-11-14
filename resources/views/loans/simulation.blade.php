@extends('layouts.app')

@section('title', 'Kalkulator Simulasi Pinjaman')

@section('content')
<!-- Navigasi Kembali ke Halaman Sebelumnya (Kiri Atas) -->
<div class="mb-4">
    <a href="{{ auth()->check() ? route('dashboard') : route('home') }}" 
       class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white hover:bg-[#F8F5F0] text-[#0F4C81] hover:text-[#1B263B] border border-[#E8E2D8] text-xs font-bold transition-all shadow-2xs"
       title="Kembali ke halaman sebelumnya">
        <svg class="w-4 h-4 text-[#0F4C81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali ke {{ auth()->check() ? 'Dasbor' : 'Beranda Utama' }}</span>
    </a>
</div>

{{-- Header Halaman yang Ramah & Bersahabat --}}
<div class="mb-6 pb-4 border-b border-slate-200/80">
    <div class="flex items-center gap-2 mb-1.5">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#EAF6F5] text-[#0F4C81] border border-[#2A9D8F]/30">
            <svg class="w-3.5 h-3.5 text-[#2A9D8F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            Kalkulator Finansial Transparan
        </span>
        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-3xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
            ⭐ Sangat Mudah Digunakan
        </span>
    </div>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Simulasi Perhitungan Angsuran Pinjaman</h1>
    <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-3xl">
        Kalkulator sederhana untuk menghitung angsuran pinjaman Anda. Cukup ikuti <strong>3 langkah mudah</strong> di bawah ini, hasil angsuran langsung muncul seketika secara jujur, adil, dan tanpa biaya tersembunyi.
    </p>
</div>

{{-- Banner Petunjuk 3 Langkah Sederhana untuk Target Minim Literasi Digital --}}
<div class="mb-6 p-3.5 sm:p-4 rounded-2xl bg-[#F8F5F0] border border-[#E8E2D8] flex flex-wrap items-center justify-between gap-3 text-xs text-slate-700 shadow-2xs">
    <div class="flex items-center gap-2 font-bold text-[#0F4C81]">
        <span class="w-7 h-7 rounded-lg bg-[#0F4C81] text-white flex items-center justify-center text-xs font-black shadow-xs">💡</span>
        <span>Cara Menghitung Pinjaman:</span>
    </div>
    <div class="flex items-center gap-2 sm:gap-4 flex-wrap text-2xs sm:text-xs">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-[#E8E2D8] font-bold text-slate-800">
            <span class="w-4 h-4 rounded-full bg-[#0F4C81] text-white text-3xs flex items-center justify-center font-bold">1</span>
            Pilih Jenis Pinjaman
        </span>
        <span class="text-slate-400">&rarr;</span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-[#E8E2D8] font-bold text-slate-800">
            <span class="w-4 h-4 rounded-full bg-[#0F4C81] text-white text-3xs flex items-center justify-center font-bold">2</span>
            Pilih Berapa Uang yang Dipinjam
        </span>
        <span class="text-slate-400">&rarr;</span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-white border border-[#E8E2D8] font-bold text-slate-800">
            <span class="w-4 h-4 rounded-full bg-[#0F4C81] text-white text-3xs flex items-center justify-center font-bold">3</span>
            Pilih Mau Berapa Kali Mengangsur
        </span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

    {{-- =========================================================================
         KOLOM KIRI: FORM SEDERHANA 3 LANGKAH MUDAH (LOW COGNITIVE LOAD)
       ========================================================================= --}}
    <div class="lg:col-span-5 space-y-6">
        <div class="bg-white rounded-3xl border-2 border-[#E8E2D8] shadow-sm p-5 sm:p-6 space-y-6">

            <form id="loanSimForm" action="{{ route('loans.simulation') }}" method="GET" class="space-y-6">

                {{-- LANGKAH 1: PILIH JENIS PINJAMAN --}}
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-[#0F4C81] flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-[#0F4C81] text-white text-3xs flex items-center justify-center font-black">1</span>
                            Pilih Jenis Pinjaman
                        </label>
                        <span class="text-3xs text-slate-400 font-semibold">Sentuh untuk memilih</span>
                    </div>

                    {{-- Kartu Pilihan Jenis Pinjaman (Tiles Ramah Jari) --}}
                    <div class="grid grid-cols-1 gap-2.5" id="product_tiles_container">
                        @foreach($products as $p)
                            @php
                                $isSelected = ($selectedProduct && $selectedProduct->id == $p->id);
                                $badgeText = $p->isWeekly() ? 'Populer Pedagang' : ($p->isDaily() ? 'Dana Cepat Harian' : 'Usaha & Bulanan');
                                $iconSvg = $p->isWeekly() 
                                    ? '<svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>'
                                    : ($p->isDaily() 
                                        ? '<svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>'
                                        : '<svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>');
                            @endphp
                            <div onclick="selectProduct({{ $p->id }})" 
                                 id="product_card_{{ $p->id }}"
                                 class="product-choice-card cursor-pointer p-3.5 rounded-2xl border-2 transition-all flex items-center justify-between gap-3 {{ $isSelected ? 'border-[#0F4C81] bg-[#EAF6F5] shadow-xs' : 'border-slate-200 hover:border-slate-300 bg-white' }}">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-[#E8E2D8] flex items-center justify-center shrink-0 shadow-2xs">
                                        {!! $iconSvg !!}
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-bold text-slate-900">{{ $p->name }}</span>
                                            <span class="text-3xs px-2 py-0.5 rounded-full font-bold {{ $p->isWeekly() ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-700' }}">
                                                {{ $badgeText }}
                                            </span>
                                        </div>
                                        <div class="text-3xs text-slate-500 mt-0.5">
                                            Bunga: {{ $p->interest_rate_per_period }}% / {{ strtolower($p->tenor_unit_label) }} &bull; Cicilan Tiap {{ $p->tenor_unit_label }}
                                        </div>
                                    </div>
                                </div>
                                <div class="check-indicator w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 {{ $isSelected ? 'border-[#0F4C81] bg-[#0F4C81] text-white' : 'border-slate-300 bg-white' }}">
                                    <svg class="w-3 h-3 {{ $isSelected ? 'block' : 'hidden' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Hidden Native Select for Form Compatibility --}}
                    <select name="product_id" id="sim_product_id" class="sr-only" required>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}" {{ ($selectedProduct && $selectedProduct->id == $p->id) ? 'selected' : '' }}>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- LANGKAH 2: TENTUKAN JUMLAH UANG YANG INGIN DIPINJAM --}}
                <div class="space-y-3 pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <label for="sim_amount" class="text-xs font-extrabold uppercase tracking-wider text-[#0F4C81] flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-[#0F4C81] text-white text-3xs flex items-center justify-center font-black">2</span>
                            Berapa Uang yang Dipinjam?
                        </label>
                        <span class="text-3xs text-slate-500 font-semibold" id="amount_range_label"></span>
                    </div>

                    {{-- Kotak Tampilan Besar dengan Tombol Stepper [-] dan [+] yang Nyaman untuk Jempol --}}
                    <div class="bg-slate-50 border-2 border-slate-200 rounded-2xl p-3 flex items-center justify-between gap-2 shadow-2xs">
                        <button type="button" onclick="adjustAmount(-1)" 
                                class="w-12 h-12 rounded-xl bg-white hover:bg-slate-100 active:scale-95 border-2 border-slate-300 text-slate-700 flex items-center justify-center font-black text-2xl transition-all shadow-xs"
                                title="Kurangi Nominal Pinjaman">
                            &minus;
                        </button>
                        
                        <div class="text-center flex-1 min-w-0">
                            <div class="text-2xl sm:text-3xl font-black text-[#0F4C81] tracking-tight font-mono" id="formatted_amount_display">
                                Rp {{ number_format(request('amount', ($selectedProduct && $selectedProduct->isDaily() ? 500000 : ($selectedProduct && $selectedProduct->isWeekly() ? 200000 : 10000000))), 0, ',', '.') }}
                            </div>
                            <span class="text-3xs text-slate-500 font-medium block">Nominal Pokok Pengajuan</span>
                        </div>

                        <button type="button" onclick="adjustAmount(1)" 
                                class="w-12 h-12 rounded-xl bg-[#0F4C81] hover:bg-[#1B263B] active:scale-95 text-white flex items-center justify-center font-black text-2xl transition-all shadow-xs"
                                title="Tambah Nominal Pinjaman">
                            &#43;
                        </button>
                    </div>

                    {{-- Input Asli (Hidden/Synchronized) --}}
                    <input type="number" id="sim_amount" name="amount" class="sr-only"
                           value="{{ request('amount', ($selectedProduct && $selectedProduct->isDaily() ? 500000 : ($selectedProduct && $selectedProduct->isWeekly() ? 200000 : 10000000))) }}"
                           required min="{{ $selectedProduct->min_amount ?? 100000 }}" max="{{ $selectedProduct->max_amount ?? 100000000 }}">

                    {{-- Tombol Pilihan Nominal Cepat Siap-Sentuh (Pills) --}}
                    <div class="space-y-1.5">
                        <span class="text-3xs text-slate-500 font-semibold block">Pilihan Cepat (Tinggal Sentuh):</span>
                        <div class="grid grid-cols-3 sm:grid-cols-5 gap-1.5" id="quick_amount_buttons">
                            {{-- Diisi secara dinamis oleh JavaScript sesuai produk --}}
                        </div>
                    </div>
                </div>

                {{-- LANGKAH 3: PILIH MAU BERAPA KALI MENGANGSUR --}}
                <div class="space-y-3 pt-3 border-t border-slate-100">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-extrabold uppercase tracking-wider text-[#0F4C81] flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-[#0F4C81] text-white text-3xs flex items-center justify-center font-black">3</span>
                            Mau Berapa Lama / Berapa Kali Bayar?
                        </label>
                        <span class="text-3xs text-slate-500 font-semibold" id="tenor_unit_hint"></span>
                    </div>

                    {{-- Tombol Pilihan Tenor Cepat Siap-Sentuh --}}
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2" id="quick_tenor_buttons">
                        {{-- Diisi secara dinamis oleh JavaScript sesuai produk --}}
                    </div>

                    {{-- Input Asli Tenor (Hidden/Synchronized) --}}
                    @php
                        $defaultTenor = $selectedProduct && $selectedProduct->isDaily() ? 14 : ($selectedProduct && $selectedProduct->isWeekly() ? 10 : 12);
                    @endphp
                    <input type="number" id="sim_tenor" name="tenor" class="sr-only"
                           value="{{ request('tenor', $defaultTenor) }}"
                           required min="{{ $selectedProduct->min_tenor ?? 1 }}" max="{{ $selectedProduct->max_tenor ?? 60 }}">
                </div>

                {{-- PENGATURAN LANJUTAN (OPSIONAL / ACCORDION AGAR TIDAK MEMBINGUNGKAN AWAM) --}}
                <div class="pt-2 border-t border-slate-100">
                    <details class="group rounded-2xl border border-slate-200 bg-slate-50/50 p-3 text-xs">
                        <summary class="font-bold text-slate-600 flex items-center justify-between cursor-pointer list-none select-none">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400 group-open:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                <span>Pengaturan Bunga Lanjutan (Opsional)</span>
                            </span>
                            <span class="text-3xs text-slate-400 group-open:hidden">Klik untuk buka</span>
                        </summary>
                        <div class="mt-3 pt-3 border-t border-slate-200 space-y-2">
                            <label for="sim_method" class="form-label text-2xs text-slate-600 font-bold">Metode Perhitungan Bunga</label>
                            <select name="calculation_method" id="sim_method" class="form-control text-xs" onchange="onMethodChange()">
                                <option value="annuity" {{ request('calculation_method', $selectedProduct->calculation_method ?? 'annuity') === 'annuity' ? 'selected' : '' }}>Anuitas (Angsuran Tetap Tiap Periode)</option>
                                <option value="effective" {{ request('calculation_method', $selectedProduct->calculation_method ?? '') === 'effective' ? 'selected' : '' }}>Efektif (Saldo Menurun - Adil)</option>
                                <option value="flat" {{ request('calculation_method', $selectedProduct->calculation_method ?? '') === 'flat' ? 'selected' : '' }}>Flat (Pokok &amp; Bunga Tetap)</option>
                            </select>
                            <p class="text-3xs text-slate-400">Default mengikuti ketentuan resmi produk koperasi.</p>
                        </div>
                    </details>
                </div>

                {{-- Tombol Fallback Hitung (Berguna jika tanpa JavaScript) --}}
                <noscript>
                    <button type="submit" class="btn btn-primary w-full py-3 rounded-xl font-bold text-xs shadow-xs">
                        Hitung Simulasi Angsuran
                    </button>
                </noscript>

            </form>

        </div>
    </div>

    {{-- =========================================================================
         KOLOM KANAN: HASIL PERHITUNGAN SUPER JELAS & RAMAH LITERASI
       ========================================================================= --}}
    <div class="lg:col-span-7 space-y-6">

        @if($simulationResult)
        {{-- KARTU HASIL UTAMA --}}
        <div class="bg-white rounded-3xl border-2 border-[#E8E2D8] shadow-sm p-6 sm:p-7 space-y-6">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div>
                    <span class="text-2xs font-extrabold uppercase tracking-wider text-[#0F4C81] block">Hasil Perhitungan Simulasi</span>
                    <h2 class="text-lg font-black text-slate-900 mt-0.5">Rincian Angsuran &amp; Dana Diterima</h2>
                </div>
                <span class="badge badge-success text-2xs font-bold px-3 py-1">
                    ✓ Bunga Adil &amp; Transparan
                </span>
            </div>

            {{-- 1. KOTAK JUMBO: BESAR ANGSURAN PER PERIODE --}}
            <div class="p-6 rounded-3xl bg-[#EAF6F5] border-2 border-[#2A9D8F] text-center space-y-2 shadow-2xs">
                <span class="text-xs font-bold uppercase tracking-wider text-[#0F4C81] block">
                    Besar Angsuran Tiap <span id="res_tenor_unit_name">{{ $simulationResult['tenor_unit_label'] }}</span>:
                </span>
                <div class="text-3xl sm:text-5xl font-black text-[#0F4C81] tracking-tight" id="res_installment_val">
                    Rp {{ number_format($simulationResult['first_installment'] ?? $simulationResult['first_month_installment'], 0, ',', '.') }}
                </div>
                <div class="text-xs font-bold text-slate-600 flex items-center justify-center gap-1.5 flex-wrap">
                    <span>Dibayar sebanyak</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-white text-[#0F4C81] border border-[#2A9D8F]/40 font-black" id="res_tenor_count_badge">
                        {{ count($simulationResult['schedules']) }} Kali Angsuran
                    </span>
                    <span>secara rutin sampai lunas.</span>
                </div>
            </div>

            {{-- 2. KOTAK PENCAIRAN BERSIH (90%) SESUAI TABEL RESMI KOPERASI --}}
            <div class="bg-[#F8F5F0] border-2 border-[#2A9D8F]/40 rounded-2xl p-4 sm:p-5 shadow-2xs space-y-3.5">
                <div class="flex items-center justify-between pb-2 border-b border-[#E8E2D8]">
                    <span class="text-xs font-extrabold uppercase tracking-wider text-[#0F4C81] flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#2A9D8F] animate-pulse"></span>
                        Ketentuan Resmi Pencairan Sesuai Tabel Koperasi
                    </span>
                    <span class="badge badge-success text-2xs font-bold">Resmi &amp; Transparan</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 text-center">
                    <div class="p-3 bg-white rounded-xl border border-[#E8E2D8] shadow-2xs">
                        <span class="text-3xs font-bold text-slate-500 uppercase block">Besar Pinjaman</span>
                        <span class="text-sm sm:text-base font-extrabold text-slate-900 mt-0.5 block" id="res_principal_val">
                            Rp {{ number_format($simulationResult['principal'], 0, ',', '.') }}
                        </span>
                        <span class="text-3xs text-slate-400 block mt-0.5">Plafon Pokok</span>
                    </div>

                    <div class="p-3 bg-white rounded-xl border border-[#E8E2D8] shadow-2xs">
                        <span class="text-3xs font-bold text-slate-500 uppercase block">Simpanan 5%</span>
                        <span class="text-sm sm:text-base font-extrabold text-[#2A9D8F] mt-0.5 block" id="res_savings_val">
                            Rp {{ number_format($simulationResult['savings_deduction'], 0, ',', '.') }}
                        </span>
                        <span class="text-3xs text-[#2A9D8F] font-semibold block mt-0.5">Tabungan Anda</span>
                    </div>

                    <div class="p-3 bg-white rounded-xl border border-[#E8E2D8] shadow-2xs">
                        <span class="text-3xs font-bold text-slate-500 uppercase block">Administrasi 5%</span>
                        <span class="text-sm sm:text-base font-extrabold text-[#0F4C81] mt-0.5 block" id="res_admin_val">
                            Rp {{ number_format($simulationResult['admin_fee'], 0, ',', '.') }}
                        </span>
                        <span class="text-3xs text-slate-400 block mt-0.5">Layanan Resmi</span>
                    </div>

                    <div class="p-3 bg-[#EAF6F5] rounded-xl border-2 border-[#2A9D8F] shadow-2xs">
                        <span class="text-3xs font-black text-[#0F4C81] uppercase block">Jumlah yang Diterima</span>
                        <span class="text-sm sm:text-base font-black text-[#0F4C81] mt-0.5 block" id="res_disbursed_val">
                            Rp {{ number_format($simulationResult['net_disbursement_amount'], 0, ',', '.') }}
                        </span>
                        <span class="text-3xs text-emerald-700 font-bold block mt-0.5">Uang Bersih (90%)</span>
                    </div>
                </div>

                <div class="p-2.5 rounded-xl bg-white border border-[#E8E2D8] text-3xs sm:text-2xs text-slate-600 flex items-start gap-2">
                    <span class="text-emerald-600 font-bold shrink-0">ℹ️ Penjelasan:</span>
                    <span>
                        Potongan <strong>Simpanan 5% (Rp <span id="res_savings_expl">{{ number_format($simulationResult['savings_deduction'], 0, ',', '.') }}</span>)</strong> tetap merupakan <strong>uang tabungan milik Anda</strong> di koperasi dan tercatat di buku simpanan Anda.
                    </span>
                </div>
            </div>

            {{-- 3. RINGKASAN TOTAL PENGEMBALIAN & JASA PINJAMAN --}}
            <div class="grid grid-cols-2 gap-3 text-center">
                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                    <span class="text-3xs uppercase font-bold text-slate-500 block">Total Jasa / Bunga Koperasi</span>
                    <span class="text-base sm:text-lg font-black text-slate-900 mt-0.5 block" id="res_total_interest_val">
                        Rp {{ number_format($simulationResult['total_interest'], 0, ',', '.') }}
                    </span>
                    <span class="text-3xs text-slate-400 block mt-0.5">Selama masa pinjaman</span>
                </div>

                <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                    <span class="text-3xs uppercase font-bold text-slate-500 block">Total Uang Kembali (Lunas)</span>
                    <span class="text-base sm:text-lg font-black text-[#0F4C81] mt-0.5 block" id="res_total_payable_val">
                        Rp {{ number_format($simulationResult['total_payable'], 0, ',', '.') }}
                    </span>
                    <span class="text-3xs text-slate-400 block mt-0.5">Semua angsuran dijumlahkan</span>
                </div>
            </div>

            {{-- 4. TOMBOL AKSI UTAMA & BANTUAN WHATSAPP (SANGAT COCOK UNTUK MINIM LITERASI) --}}
            <div class="pt-2 space-y-3">
                <div class="flex flex-col sm:flex-row gap-3">
                    {{-- Tombol Ajukan Pinjaman --}}
                    @auth
                        @if(auth()->user()->isMember())
                            <a id="btn_apply_loan" 
                               href="{{ route('loans.apply', ['product_id' => $selectedProduct->id ?? '', 'amount' => $simulationResult['principal'], 'tenor' => count($simulationResult['schedules'])]) }}" 
                               class="flex-1 py-3.5 px-6 rounded-2xl bg-[#0F4C81] hover:bg-[#1B263B] text-white font-extrabold text-sm text-center shadow-md transition-all flex items-center justify-center gap-2">
                                <span>Ajukan Pinjaman Ini Sekarang</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @else
                            <a id="btn_apply_loan" 
                               href="{{ route('loans.apply', ['product_id' => $selectedProduct->id ?? '', 'amount' => $simulationResult['principal'], 'tenor' => count($simulationResult['schedules'])]) }}" 
                               class="flex-1 py-3.5 px-6 rounded-2xl bg-[#0F4C81] hover:bg-[#1B263B] text-white font-extrabold text-sm text-center shadow-md transition-all flex items-center justify-center gap-2">
                                <span>Ajukan Pinjaman Ini Sekarang</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endif
                    @else
                        <a id="btn_apply_loan" 
                           href="{{ route('register') }}" 
                           class="flex-1 py-3.5 px-6 rounded-2xl bg-[#0F4C81] hover:bg-[#1B263B] text-white font-extrabold text-sm text-center shadow-md transition-all flex items-center justify-center gap-2">
                            <span>Daftar Anggota &amp; Ajukan Pinjaman</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @endauth

                    {{-- Tombol Bantuan WhatsApp CS Otomatis Terisi --}}
                    @php
                        $waText = "Halo KSP Sahabat Sejahtera, saya ingin dibantu simulasi pinjaman: Produk " . ($selectedProduct->name ?? 'Pinjaman') . ", Plafon Rp " . number_format($simulationResult['principal'], 0, ',', '.') . ", Tenor " . count($simulationResult['schedules']) . " " . $simulationResult['tenor_unit_label'] . ". Mohon informasinya. Terima kasih.";
                        $waUrl = "https://wa.me/6282197233174?text=" . urlencode($waText);
                    @endphp
                    <a id="btn_wa_help" href="{{ $waUrl }}" target="_blank" rel="noopener" 
                       class="py-3.5 px-5 rounded-2xl bg-emerald-600 hover:bg-emerald-700 active:scale-98 text-white font-extrabold text-xs sm:text-sm text-center shadow-md transition-all flex items-center justify-center gap-2 shrink-0">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                        <span>Butuh Bantuan? Tanya WhatsApp CS</span>
                    </a>
                </div>
            </div>

        </div>

        {{-- =========================================================================
             BAGIAN TRANSPARANSI & JADWAL LENGKAP (COLLAPSIBLE / ACCORDION)
             (Tidak membingungkan orang awam, tapi data akuntansi lengkap tetap ada)
           ========================================================================= --}}
        <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
            <details class="group">
                <summary class="px-5 py-4 bg-slate-50 hover:bg-slate-100 transition-colors cursor-pointer list-none select-none flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-[#0F4C81] text-white flex items-center justify-center shrink-0 shadow-2xs font-bold text-xs">
                            📅
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span>Lihat Jadwal Lengkap Tanggal Jatuh Tempo &amp; Transparansi Bunga</span>
                            </h3>
                            <p class="text-3xs text-slate-500">Klik untuk melihat rincian tanggal kalender, porsi pokok, bunga, dan APR tahunan.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-bold text-[#0F4C81]">
                        <span class="group-open:hidden">Buka Rincian &darr;</span>
                        <span class="hidden group-open:inline">Tutup &uarr;</span>
                    </div>
                </summary>

                <div class="p-5 space-y-6 border-t border-slate-200">

                    {{-- Kartu Transparansi Biaya Setara Per Tahun (APR & AER) --}}
                    <div class="bg-[#1B263B] text-white rounded-2xl p-4 sm:p-5 shadow-sm border border-[#24344E]">
                        <div class="flex items-start justify-between gap-4 flex-col sm:flex-row">
                            <div>
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-3xs font-bold uppercase tracking-wider bg-[#2A9D8F]/20 text-[#2A9D8F] border border-[#2A9D8F]/30 mb-2">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    Transparansi Finansial Perlindungan Anggota
                                </div>
                                <h4 class="text-sm sm:text-base font-extrabold text-white tracking-tight">Perbandingan Biaya Setara Per Tahun (APR)</h4>
                                <p class="text-xs text-slate-300 mt-0.5">Memudahkan anggota membandingkan biaya riil produk mingguan vs bulanan secara adil dan terbuka.</p>
                            </div>
                            <div class="text-right sm:self-center bg-white/10 px-4 py-2 rounded-xl backdrop-blur-xs border border-white/10 shrink-0">
                                <span class="text-3xs uppercase tracking-wider text-[#D4AF37] font-bold block">Biaya Setara Tahunan (APR)</span>
                                <span class="text-xl sm:text-2xl font-black text-white tracking-tight" id="res_apr_val">
                                    {{ number_format($simulationResult['annual_equivalent_cost_rate'], 2, ',', '.') }}% <span class="text-xs font-normal text-slate-300">/ thn</span>
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-white/10 text-xs">
                            <div>
                                <span class="text-3xs text-slate-400 uppercase font-semibold block">Bunga Nominal Tahunan</span>
                                <span class="text-xs sm:text-sm font-bold text-white" id="res_annual_nominal_val">{{ number_format($simulationResult['annual_nominal_rate'], 2, ',', '.') }}% / tahun</span>
                                <span class="text-3xs text-slate-400 block" id="res_annual_nominal_sub">({{ $simulationResult['rate_per_period'] }}% &times; {{ $simulationResult['periods_per_year'] }} periode)</span>
                            </div>
                            <div>
                                <span class="text-3xs text-slate-400 uppercase font-semibold block">Bunga Efektif Tahunan (AER)</span>
                                <span class="text-xs sm:text-sm font-bold text-white" id="res_aer_val">{{ number_format($simulationResult['annual_effective_rate'], 2, ',', '.') }}% / tahun</span>
                                <span class="text-3xs text-slate-400 block">Tingkat bunga berjenjang</span>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <span class="text-3xs text-slate-400 uppercase font-semibold block">Total Beban Finansial</span>
                                <span class="text-xs sm:text-sm font-bold text-[#2A9D8F]" id="res_total_cost_val">Rp {{ number_format($simulationResult['total_cost'], 0, ',', '.') }}</span>
                                <span class="text-3xs text-slate-400 block">Bunga + Administrasi</span>
                            </div>
                        </div>
                    </div>

                    {{-- Tabel Jadwal Simulasi Kalender --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-800">
                            <span>Jadwal Angsuran Tiap <span id="tbl_tenor_unit">{{ $simulationResult['tenor_unit_label'] }}</span></span>
                            <span class="text-3xs text-slate-500 font-normal">Tanggal jatuh tempo kalender aktual</span>
                        </div>

                        <div class="table-responsive rounded-2xl border border-slate-200 overflow-x-auto">
                            <table class="w-full text-left text-xs" id="simulation_schedule_table">
                                <thead class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase text-3xs">
                                    <tr>
                                        <th class="py-2.5 px-3">Periode</th>
                                        <th class="py-2.5 px-3">Jatuh Tempo</th>
                                        <th class="py-2.5 px-3">Angsuran Pokok</th>
                                        <th class="py-2.5 px-3">Bunga / Jasa</th>
                                        <th class="py-2.5 px-3">Total Angsuran</th>
                                        <th class="py-2.5 px-3 text-right">Sisa Saldo Pokok</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100" id="schedule_tbody">
                                    @foreach($simulationResult['schedules'] as $s)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="py-2 px-3 font-bold text-slate-800">{{ $s['period_label'] ?? ($simulationResult['tenor_unit_label'] . ' ' . $s['installment_no']) }}</td>
                                        <td class="py-2 px-3 font-mono text-slate-600 text-2xs">{{ \Carbon\Carbon::parse($s['due_date'])->format('d/m/Y') }}</td>
                                        <td class="py-2 px-3 font-mono text-slate-700">Rp {{ number_format($s['principal_amount'], 0, ',', '.') }}</td>
                                        <td class="py-2 px-3 font-mono text-slate-700">Rp {{ number_format($s['interest_amount'], 0, ',', '.') }}</td>
                                        <td class="py-2 px-3 font-mono font-bold text-emerald-700">Rp {{ number_format($s['total_amount'], 0, ',', '.') }}</td>
                                        <td class="py-2 px-3 font-mono text-slate-600 text-right">Rp {{ number_format($s['remaining_principal'], 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-slate-100 font-bold text-slate-900 border-t-2 border-slate-200">
                                        <td class="py-2.5 px-3" colspan="2">TOTAL</td>
                                        <td class="py-2.5 px-3 font-mono" id="tbl_foot_principal">Rp {{ number_format($simulationResult['principal'], 0, ',', '.') }}</td>
                                        <td class="py-2.5 px-3 font-mono" id="tbl_foot_interest">Rp {{ number_format($simulationResult['total_interest'], 0, ',', '.') }}</td>
                                        <td class="py-2.5 px-3 font-mono text-emerald-800" id="tbl_foot_total">Rp {{ number_format($simulationResult['principal'] + $simulationResult['total_interest'], 0, ',', '.') }}</td>
                                        <td class="py-2.5 px-3 text-right text-slate-400">-</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                </div>
            </details>
        </div>
        @else
        <div class="bg-white rounded-3xl border border-slate-200 p-8 text-center text-slate-400 text-xs">
            Pilih produk pinjaman dan tentukan nominal di sebelah kiri untuk melihat simulasi.
        </div>
        @endif

    </div>

</div>

{{-- SCRIPT INTERAKTIF CLIENT-SIDE UNTUK SIMULASI INSTAN TANPA RELOAD --}}
<script>
    // Data Produk Pinjaman dari Backend
    const productsData = @json($products);
    let selectedProductId = {{ $selectedProduct ? $selectedProduct->id : ($products->first()?->id ?? 0) }};
    const applyBaseUrl = "{{ route('loans.apply') }}";
    const waBaseNumber = "6282197233174";

    // Format Rupiah
    function formatRupiah(number) {
        return 'Rp ' + Number(number).toLocaleString('id-ID');
    }

    // Dapatkan Objek Produk yang Aktif
    function getSelectedProduct() {
        return productsData.find(p => p.id === selectedProductId) || productsData[0];
    }

    // Ubah Pilihan Produk
    function selectProduct(productId) {
        selectedProductId = productId;
        const product = getSelectedProduct();

        // Update select value
        const select = document.getElementById('sim_product_id');
        if (select) select.value = productId;

        // Update visual card styles
        document.querySelectorAll('.product-choice-card').forEach(card => {
            card.classList.remove('border-[#0F4C81]', 'bg-[#EAF6F5]', 'shadow-xs');
            card.classList.add('border-slate-200', 'bg-white');
            const check = card.querySelector('.check-indicator');
            if (check) {
                check.classList.remove('border-[#0F4C81]', 'bg-[#0F4C81]', 'text-white');
                check.classList.add('border-slate-300', 'bg-white');
                const svg = check.querySelector('svg');
                if (svg) svg.classList.add('hidden');
            }
        });

        const activeCard = document.getElementById('product_card_' + productId);
        if (activeCard) {
            activeCard.classList.remove('border-slate-200', 'bg-white');
            activeCard.classList.add('border-[#0F4C81]', 'bg-[#EAF6F5]', 'shadow-xs');
            const check = activeCard.querySelector('.check-indicator');
            if (check) {
                check.classList.remove('border-slate-300', 'bg-white');
                check.classList.add('border-[#0F4C81]', 'bg-[#0F4C81]', 'text-white');
                const svg = check.querySelector('svg');
                if (svg) svg.classList.remove('hidden');
            }
        }

        // Tentukan nominal default sesuai jenis produk
        let defaultAmount = 10000000;
        if (product.tenor_unit === 'week') defaultAmount = 200000;
        else if (product.tenor_unit === 'day') defaultAmount = 500000;

        // Pastikan berada di dalam min - max
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

        // Tentukan metode perhitungan default produk
        const methodSelect = document.getElementById('sim_method');
        if (methodSelect && product.calculation_method) {
            methodSelect.value = product.calculation_method;
        }

        renderPillsAndRecalculate();
    }

    // Render Tombol Cepat Nominal dan Tenor
    function renderPillsAndRecalculate() {
        const product = getSelectedProduct();
        const currentAmount = parseInt(document.getElementById('sim_amount').value) || product.min_amount;
        const currentTenor = parseInt(document.getElementById('sim_tenor').value) || product.min_tenor;

        // 1. Render Tombol Nominal Cepat
        const amountContainer = document.getElementById('quick_amount_buttons');
        let amountPresets = [];
        if (product.tenor_unit === 'week') {
            amountPresets = [200000, 500000, 1000000, 2000000, 5000000];
        } else if (product.tenor_unit === 'day') {
            amountPresets = [100000, 250000, 500000, 750000, 1000000];
        } else {
            amountPresets = [2000000, 5000000, 10000000, 25000000, 50000000];
        }

        // Filter sesuai batas min max produk
        amountPresets = amountPresets.filter(a => a >= product.min_amount && a <= product.max_amount);
        if (!amountPresets.includes(product.min_amount)) amountPresets.unshift(product.min_amount);

        let amountHtml = '';
        amountPresets.forEach(amt => {
            const isAct = (amt === currentAmount);
            const label = amt >= 1000000 ? (amt / 1000000) + ' Jt' : (amt / 1000) + ' Rb';
            const isStandard = (product.tenor_unit === 'week' && amt === 200000);
            amountHtml += `
                <button type="button" onclick="setNominal(${amt})" 
                        class="px-2 py-2 rounded-xl text-xs font-bold transition-all text-center border-2 ${isAct ? 'bg-[#0F4C81] text-white border-[#0F4C81] shadow-2xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 border-slate-200'}">
                    ${label} ${isStandard ? '<span class="block text-3xs opacity-80">(Standar)</span>' : ''}
                </button>
            `;
        });
        if (amountContainer) amountContainer.innerHTML = amountHtml;

        // 2. Render Tombol Tenor Cepat
        const tenorContainer = document.getElementById('quick_tenor_buttons');
        let tenorPresets = [];
        if (product.tenor_unit === 'week') {
            tenorPresets = [
                { t: 10, label: '10 Minggu', sub: 'Standar Pasar (10x)' },
                { t: 4, label: '4 Minggu', sub: '1 Bulan (4x)' },
                { t: 8, label: '8 Minggu', sub: '2 Bulan (8x)' },
                { t: 16, label: '16 Minggu', sub: '4 Bulan (16x)' },
                { t: 24, label: '24 Minggu', sub: '6 Bulan (24x)' }
            ];
        } else if (product.tenor_unit === 'day') {
            tenorPresets = [
                { t: 7, label: '7 Hari', sub: '1 Minggu' },
                { t: 10, label: '10 Hari', sub: '10 Kali Bayar' },
                { t: 14, label: '14 Hari', sub: '2 Minggu' },
                { t: 21, label: '21 Hari', sub: '3 Minggu' },
                { t: 30, label: '30 Hari', sub: '1 Bulan' }
            ];
        } else {
            tenorPresets = [
                { t: 6, label: '6 Bulan', sub: 'Setengah Tahun' },
                { t: 12, label: '12 Bulan', sub: '1 Tahun (Standar)' },
                { t: 24, label: '24 Bulan', sub: '2 Tahun' },
                { t: 36, label: '36 Bulan', sub: '3 Tahun' }
            ];
        }

        // Filter sesuai rentang tenor produk
        tenorPresets = tenorPresets.filter(item => item.t >= product.min_tenor && item.t <= product.max_tenor);

        let tenorHtml = '';
        tenorPresets.forEach(item => {
            const isAct = (item.t === currentTenor);
            tenorHtml += `
                <button type="button" onclick="setTenor(${item.t})" 
                        class="p-2.5 rounded-xl text-left border-2 transition-all ${isAct ? 'bg-[#0F4C81] text-white border-[#0F4C81] shadow-2xs' : 'bg-slate-50 hover:bg-slate-100 text-slate-800 border-slate-200'}">
                    <span class="block text-xs font-black">${item.label}</span>
                    <span class="block text-3xs ${isAct ? 'text-amber-200' : 'text-slate-500'} font-medium mt-0.5">${item.sub}</span>
                </button>
            `;
        });
        if (tenorContainer) tenorContainer.innerHTML = tenorHtml;

        // Label keterangan batas
        const rangeLabel = document.getElementById('amount_range_label');
        if (rangeLabel) {
            rangeLabel.textContent = `Min ${formatRupiah(product.min_amount)} - Max ${formatRupiah(product.max_amount)}`;
        }

        const tenorHint = document.getElementById('tenor_unit_hint');
        if (tenorHint) {
            tenorHint.textContent = `Satuan: ${product.tenor_unit_label}`;
        }

        runLiveSimulation();
    }

    // Set Nominal Langsung dari Tombol
    function setNominal(amount) {
        const input = document.getElementById('sim_amount');
        if (input) {
            input.value = amount;
            renderPillsAndRecalculate();
        }
    }

    // Stepper Nominal [-] atau [+]
    function adjustAmount(direction) {
        const product = getSelectedProduct();
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
        renderPillsAndRecalculate();
    }

    // Set Tenor Langsung dari Tombol
    function setTenor(tenor) {
        const input = document.getElementById('sim_tenor');
        if (input) {
            input.value = tenor;
            renderPillsAndRecalculate();
        }
    }

    function onMethodChange() {
        runLiveSimulation();
    }

    // Kalkulasi Live di Browser & Update Tampilan Seketika
    function runLiveSimulation() {
        const product = getSelectedProduct();
        const principal = parseInt(document.getElementById('sim_amount').value) || product.min_amount;
        const tenor = parseInt(document.getElementById('sim_tenor').value) || product.min_tenor;
        const methodSelect = document.getElementById('sim_method');
        const method = methodSelect ? methodSelect.value : (product.calculation_method || 'flat');

        // Update tampilan display nominal di stepper
        const dispAmount = document.getElementById('formatted_amount_display');
        if (dispAmount) dispAmount.textContent = formatRupiah(principal);

        // 1. Simpanan 5% & Administrasi 5%
        const savingsDeduction = Math.round(principal * ((product.savings_percentage || 5.0) / 100));
        const adminFee = Math.round(principal * ((product.admin_fee_percentage || 5.0) / 100) + (product.admin_fee_fixed || 0));
        const netDisbursement = Math.max(0, principal - savingsDeduction - adminFee);

        // 2. Kalkulasi Angsuran Sesuai Metode
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
                    period_label: `${product.tenor_unit_label} ${i}`,
                    due_date: dueDate.toLocaleDateString('id-ID'),
                    principal_amount: principalPortion,
                    interest_amount: interestPortion,
                    total_amount: totalInstallment,
                    remaining_principal: Math.max(0, remainingPrincipal)
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
                    period_label: `${product.tenor_unit_label} ${i}`,
                    due_date: dueDate.toLocaleDateString('id-ID'),
                    principal_amount: principalPortion,
                    interest_amount: interestPortion,
                    total_amount: totalInstallment,
                    remaining_principal: Math.max(0, remainingPrincipal)
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
                    period_label: `${product.tenor_unit_label} ${i}`,
                    due_date: dueDate.toLocaleDateString('id-ID'),
                    principal_amount: principalPortion,
                    interest_amount: interestPortion,
                    total_amount: totalInstallment,
                    remaining_principal: Math.max(0, remainingPrincipal)
                });
            }
            firstInstallment = schedules[0] ? schedules[0].total_amount : 0;
        }

        const totalCost = totalInterest + adminFee;
        const totalPayable = principal + totalCost;

        // APR & AER
        const periodsPerYear = product.tenor_unit === 'day' ? 365 : (product.tenor_unit === 'week' ? 52 : 12);
        const annualNominalRate = (ratePerPeriod * periodsPerYear).toFixed(2);
        const annualEffectiveRate = ((Math.pow(1 + rateDecimal, periodsPerYear) - 1) * 100).toFixed(2);
        const apr = (principal > 0 && tenor > 0)
            ? ((totalCost / principal) * (periodsPerYear / tenor) * 100).toFixed(2)
            : annualNominalRate;

        // 3. Update DOM Hasil Kalkulasi (Real-Time Live UI)
        const resInst = document.getElementById('res_installment_val');
        if (resInst) resInst.textContent = formatRupiah(firstInstallment);

        const resTenorUnit = document.getElementById('res_tenor_unit_name');
        if (resTenorUnit) resTenorUnit.textContent = product.tenor_unit_label;

        const resTenorBadge = document.getElementById('res_tenor_count_badge');
        if (resTenorBadge) resTenorBadge.textContent = `${tenor} Kali Angsuran`;

        const resPrincipal = document.getElementById('res_principal_val');
        if (resPrincipal) resPrincipal.textContent = formatRupiah(principal);

        const resSavings = document.getElementById('res_savings_val');
        if (resSavings) resSavings.textContent = formatRupiah(savingsDeduction);

        const resSavingsExpl = document.getElementById('res_savings_expl');
        if (resSavingsExpl) resSavingsExpl.textContent = Number(savingsDeduction).toLocaleString('id-ID');

        const resAdmin = document.getElementById('res_admin_val');
        if (resAdmin) resAdmin.textContent = formatRupiah(adminFee);

        const resDisbursed = document.getElementById('res_disbursed_val');
        if (resDisbursed) resDisbursed.textContent = formatRupiah(netDisbursement);

        const resTotInterest = document.getElementById('res_total_interest_val');
        if (resTotInterest) resTotInterest.textContent = formatRupiah(totalInterest);

        const resTotPayable = document.getElementById('res_total_payable_val');
        if (resTotPayable) resTotPayable.textContent = formatRupiah(totalPayable);

        // APR & Transparansi
        const resApr = document.getElementById('res_apr_val');
        if (resApr) resApr.innerHTML = `${apr}% <span class="text-xs font-normal text-slate-300">/ thn</span>`;

        const resAnnNom = document.getElementById('res_annual_nominal_val');
        if (resAnnNom) resAnnNom.textContent = `${annualNominalRate}% / tahun`;

        const resAnnNomSub = document.getElementById('res_annual_nominal_sub');
        if (resAnnNomSub) resAnnNomSub.innerHTML = `(${ratePerPeriod}% &times; ${periodsPerYear} periode)`;

        const resAer = document.getElementById('res_aer_val');
        if (resAer) resAer.textContent = `${annualEffectiveRate}% / tahun`;

        const resTotCost = document.getElementById('res_total_cost_val');
        if (resTotCost) resTotCost.textContent = formatRupiah(totalCost);

        const tblTenorUnit = document.getElementById('tbl_tenor_unit');
        if (tblTenorUnit) tblTenorUnit.textContent = product.tenor_unit_label;

        // 4. Update Tabel Jadwal Angsuran
        const tbody = document.getElementById('schedule_tbody');
        if (tbody) {
            let tbodyHtml = '';
            schedules.forEach(s => {
                tbodyHtml += `
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-2 px-3 font-bold text-slate-800">${s.period_label}</td>
                        <td class="py-2 px-3 font-mono text-slate-600 text-2xs">${s.due_date}</td>
                        <td class="py-2 px-3 font-mono text-slate-700">${formatRupiah(s.principal_amount)}</td>
                        <td class="py-2 px-3 font-mono text-slate-700">${formatRupiah(s.interest_amount)}</td>
                        <td class="py-2 px-3 font-mono font-bold text-emerald-700">${formatRupiah(s.total_amount)}</td>
                        <td class="py-2 px-3 font-mono text-slate-600 text-right">${formatRupiah(s.remaining_principal)}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = tbodyHtml;
        }

        const footP = document.getElementById('tbl_foot_principal');
        if (footP) footP.textContent = formatRupiah(principal);

        const footI = document.getElementById('tbl_foot_interest');
        if (footI) footI.textContent = formatRupiah(totalInterest);

        const footT = document.getElementById('tbl_foot_total');
        if (footT) footT.textContent = formatRupiah(principal + totalInterest);

        // 5. Update Link Tombol Pengajuan & WhatsApp CS
        const btnApply = document.getElementById('btn_apply_loan');
        if (btnApply && btnApply.getAttribute('href').includes('loans/apply')) {
            btnApply.href = `${applyBaseUrl}?product_id=${product.id}&amount=${principal}&tenor=${tenor}`;
        }

        const btnWa = document.getElementById('btn_wa_help');
        if (btnWa) {
            const waMsg = `Halo KSP Sahabat Sejahtera, saya ingin dibantu pengajuan pinjaman: Produk ${product.name}, Plafon ${formatRupiah(principal)}, Tenor ${tenor} ${product.tenor_unit_label} (Angsuran ${formatRupiah(firstInstallment)}/${product.tenor_unit_label}). Mohon petunjuk prosesnya. Terima kasih.`;
            btnWa.href = `https://wa.me/${waBaseNumber}?text=${encodeURIComponent(waMsg)}`;
        }
    }

    // Inisialisasi saat Halaman Selesai Dimuat
    document.addEventListener('DOMContentLoaded', function() {
        renderPillsAndRecalculate();
    });
</script>
@endsection
