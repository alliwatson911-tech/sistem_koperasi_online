@extends('layouts.app')

@section('title', 'Katalog Produk Simpan Pinjam - KSP Sahabat Sejahtera')

@section('content')
<div class="space-y-10 py-2 sm:py-4">

    {{-- Breadcrumb & Hero Header --}}
    <div class="bg-gradient-to-br from-[#0F4C81] via-[#1B263B] to-[#12304d] rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden border border-white/10">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-[#2A9D8F]/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -top-12 w-64 h-64 rounded-full bg-[#D4AF37]/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-white/10 backdrop-blur-md border border-white/20 text-[#D4AF37]">
                <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span>Fasilitas Keuangan Anggota Terpercaya</span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight text-white">
                Katalog Produk Simpanan &amp; Pinjaman
            </h1>

            <p class="text-sm sm:text-base text-slate-200 leading-relaxed font-normal">
                Solusi permodalan usaha mikro dan pengelolaan simpanan berkeadilan. Dikelola berasaskan kekeluargaan, transparansi penuh tanpa biaya tersembunyi, dan memberikan dividen SHU tahunan bagi seluruh anggota.
            </p>

            <div class="pt-2 flex flex-wrap items-center gap-3">
                <a href="{{ route('loans.simulation') }}" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-[#D4AF37] hover:bg-[#C59F2D] text-[#1B263B] shadow-md transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span>Kalkulator Simulasi Pinjaman</span>
                </a>
                @guest
                    <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-white/10 hover:bg-white/20 text-white border border-white/20 backdrop-blur-md transition-all">
                        Daftar Anggota Baru &rarr;
                    </a>
                @endguest
            </div>
        </div>
    </div>

    {{-- Filter Navigasi Kategori Produk --}}
    <div class="flex items-center justify-between flex-wrap gap-4 border-b border-slate-200 pb-4">
        <div>
            <h2 class="text-lg sm:text-xl font-bold text-slate-900">Pilihan Produk Koperasi</h2>
            <p class="text-xs sm:text-sm text-slate-500">Pilih skema pendanaan atau tabungan yang paling sesuai dengan kebutuhan finansial Anda</p>
        </div>

        <div class="inline-flex p-1 rounded-xl bg-slate-100 border border-slate-200 text-xs font-bold" id="productTabGroup">
            <button type="button" onclick="switchProductTab('all')" id="tabBtn-all" class="px-3.5 py-1.5 rounded-lg transition-all bg-[#0F4C81] text-white shadow-xs">
                Semua Produk
            </button>
            <button type="button" onclick="switchProductTab('loan')" id="tabBtn-loan" class="px-3.5 py-1.5 rounded-lg transition-all text-slate-600 hover:text-slate-900">
                Pinjaman Anggota ({{ $loanProducts->count() }})
            </button>
            <button type="button" onclick="switchProductTab('savings')" id="tabBtn-savings" class="px-3.5 py-1.5 rounded-lg transition-all text-slate-600 hover:text-slate-900">
                Simpanan &amp; Tabungan ({{ count($savingsProducts) }})
            </button>
        </div>
    </div>

    {{-- BAGIAN 1: PRODUK PINJAMAN ANGGOTA --}}
    <div id="section-loans" class="space-y-6">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-[#EAF6F5] text-[#0F4C81] flex items-center justify-center font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900">Produk Pinjaman &amp; Pembiayaan Mikro</h3>
                <p class="text-xs text-slate-500">Pemberdayaan modal kerja dan kebutuhan produktif dengan bunga adil dan proses cepat 1x24 jam</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($loanProducts as $p)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-md transition-all flex flex-col justify-between overflow-hidden group hover:border-[#2A9D8F]/50">
                <div class="p-5 sm:p-6 space-y-4">
                    {{-- Header Card --}}
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold font-mono bg-blue-50 text-[#0F4C81] border border-blue-200">
                            {{ $p->code }}
                        </span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-100 text-slate-600">
                            {{ ucfirst($p->calculation_method) }}
                        </span>
                    </div>

                    <div>
                        <h4 class="text-base sm:text-lg font-bold text-slate-900 group-hover:text-[#0F4C81] transition-colors">
                            {{ $p->name }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">
                            {{ $p->description }}
                        </p>
                    </div>

                    {{-- Spesifikasi Kotak --}}
                    <div class="bg-[#FDFBF7] rounded-xl p-3.5 border border-[#E8E2D8] space-y-2 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Plafon Pinjaman:</span>
                            <strong class="text-slate-800 font-bold">
                                Rp {{ $p->min_amount >= 1000000 ? number_format($p->min_amount/1000000, 1) . ' Jt' : number_format($p->min_amount, 0, ',', '.') }} s.d. Rp {{ number_format($p->max_amount/1000000, 1) }} Jt
                            </strong>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Tenor Pinjaman:</span>
                            <strong class="text-slate-800 font-bold">
                                {{ $p->min_tenor }} s.d. {{ $p->max_tenor }} {{ $p->tenor_unit_label }}
                            </strong>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Suku Bunga / Jasa:</span>
                            <strong class="text-emerald-700 font-bold">
                                {{ $p->interest_rate_per_period }}% / {{ strtolower($p->tenor_unit_label) }}
                            </strong>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Biaya Administrasi:</span>
                            <strong class="text-slate-800 font-bold">
                                {{ $p->admin_fee_fixed > 0 ? "{$p->admin_fee_percentage}% + Rp " . number_format($p->admin_fee_fixed, 0, ',', '.') : "{$p->admin_fee_percentage}%" }}
                            </strong>
                        </div>

                        @if(($p->savings_percentage ?? 0) > 0)
                        <div class="flex items-center justify-between pt-1 border-t border-dashed border-[#E8E2D8]">
                            <span class="text-slate-500">Simpanan Wajib (5%):</span>
                            <strong class="text-[#2A9D8F] font-bold">
                                {{ $p->savings_percentage }}% (Masuk Tabungan)
                            </strong>
                        </div>
                        @endif
                    </div>

                    {{-- Highlight Sesuai Ketentuan --}}
                    @if($p->isWeekly())
                    <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-900 space-y-1">
                        <div class="font-bold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-emerald-700 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                            <span>Standar Tabel Resmi Koperasi:</span>
                        </div>
                        <p class="text-slate-600">
                            Pinjaman <strong>Rp 200.000</strong> tenor 10 minggu &bull; Angsuran <strong>Rp 24.000 / minggu</strong> &bull; Diterima bersih <strong>Rp 180.000</strong> (potongan simpanan Rp 10.000 + admin Rp 10.000).
                        </p>
                    </div>
                    @endif
                </div>

                {{-- Action Footer Card --}}
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center gap-2">
                    <a href="{{ route('loans.simulation', ['product_id' => $p->id, 'amount' => $p->min_amount, 'tenor' => ($p->isWeekly() ? 10 : $p->min_tenor)]) }}" 
                       class="flex-1 text-center py-2 px-3 rounded-xl border border-slate-300 hover:border-[#0F4C81] text-xs font-bold text-slate-700 hover:text-[#0F4C81] bg-white transition-all shadow-2xs">
                        Simulasi Angsuran
                    </a>

                    @auth
                        @if(auth()->user()->isMember())
                            <a href="{{ route('loans.apply', ['product_id' => $p->id]) }}" 
                               class="flex-1 text-center py-2 px-3 rounded-xl bg-[#0F4C81] hover:bg-[#1B263B] text-xs font-bold text-white transition-all shadow-xs">
                                Ajukan Sekarang
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-slate-200 hover:bg-slate-300 text-xs font-bold text-slate-700 transition-all">
                                Dasbor Staf
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="flex-1 text-center py-2 px-3 rounded-xl bg-[#0F4C81] hover:bg-[#1B263B] text-xs font-bold text-white transition-all shadow-xs">
                            Ajukan Pinjaman
                        </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- BAGIAN 2: PRODUK SIMPANAN KOPERASI --}}
    <div id="section-savings" class="space-y-6 pt-4">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-800 flex items-center justify-center font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <h3 class="text-base sm:text-lg font-bold text-slate-900">Produk Simpanan &amp; Tabungan Anggota</h3>
                <p class="text-xs text-slate-500">Membangun aset masa depan yang berkah, aman dari inflasi, dan memperoleh bagi hasil SHU tahunan</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($savingsProducts as $sp)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs hover:shadow-md transition-all flex flex-col justify-between overflow-hidden">
                <div class="p-5 space-y-4">
                    <div class="flex items-center justify-between gap-2">
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold font-mono bg-slate-100 text-slate-700">
                            {{ $sp['code'] }}
                        </span>
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold border {{ $sp['badge_color'] }}">
                            {{ $sp['badge'] }}
                        </span>
                    </div>

                    <div>
                        <h4 class="text-base font-bold text-slate-900">
                            {{ $sp['name'] }}
                        </h4>
                        <div class="text-xs font-semibold text-emerald-800 mt-1">
                            {{ $sp['nominal_formatted'] }}
                        </div>
                        <p class="text-[11px] text-slate-500 mt-2 leading-relaxed">
                            {{ $sp['description'] }}
                        </p>
                    </div>

                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Fitur &amp; Keuntungan:</span>
                        <ul class="space-y-1.5 text-xs text-slate-600">
                            @foreach($sp['features'] as $f)
                            <li class="flex items-start gap-1.5">
                                <span class="text-[#2A9D8F] font-bold mt-0.5">&check;</span>
                                <span class="leading-tight">{{ $f }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100">
                    @auth
                        @if(auth()->user()->isMember())
                            <a href="{{ route('savings.index') }}" class="block w-full text-center py-2 px-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-xs font-bold text-white transition-all shadow-xs">
                                Buka / Setor Simpanan
                            </a>
                        @else
                            <a href="{{ route('dashboard') }}" class="block w-full text-center py-2 px-3 rounded-xl bg-slate-200 text-xs font-bold text-slate-700">
                                Buka Rekening Anggota
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}" class="block w-full text-center py-2 px-3 rounded-xl bg-[#D4AF37] hover:bg-[#C59F2D] text-xs font-bold text-[#1B263B] transition-all shadow-xs">
                            Mulai Menabung &rarr;
                        </a>
                    @endauth
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- KARTU KOMITMEN KEAMANAN & TRANSPARANSI --}}
    <div class="bg-[#FDFBF7] rounded-3xl p-6 sm:p-8 border border-[#E8E2D8] grid grid-cols-1 md:grid-cols-3 gap-6 shadow-xs">
        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-[#EAF6F5] text-[#0F4C81] flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Legalitas &amp; Perlindungan Sah</h4>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">Berbadan hukum resmi Kemenkop UKM RI No. AHU-0012345.AH.01.26.TAHUN 2026 dan tunduk pada UU Perkoperasian.</p>
            </div>
        </div>

        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">100% Bebas Biaya Gaib</h4>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">Semua biaya administrasi, suku bunga, dan potongan simpanan tertera transparan di simulasi dan akad perjanjian digital.</p>
            </div>
        </div>

        <div class="flex items-start gap-3.5">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-800 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-slate-900">Hak Bagi Hasil SHU</h4>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">Keuntungan operasional koperasi dibagikan kembali kepada anggota dalam bentuk Sisa Hasil Usaha pada RAT setiap tahun.</p>
            </div>
        </div>
    </div>

</div>

{{-- Script Switch Tab Kategori --}}
<script>
    function switchProductTab(cat) {
        const secLoans = document.getElementById('section-loans');
        const secSavings = document.getElementById('section-savings');
        const btnAll = document.getElementById('tabBtn-all');
        const btnLoan = document.getElementById('tabBtn-loan');
        const btnSavings = document.getElementById('tabBtn-savings');

        // Reset tombol
        [btnAll, btnLoan, btnSavings].forEach(btn => {
            btn.className = 'px-3.5 py-1.5 rounded-lg transition-all text-slate-600 hover:text-slate-900';
        });

        if (cat === 'all') {
            secLoans.classList.remove('hidden');
            secSavings.classList.remove('hidden');
            btnAll.className = 'px-3.5 py-1.5 rounded-lg transition-all bg-[#0F4C81] text-white shadow-xs';
        } else if (cat === 'loan') {
            secLoans.classList.remove('hidden');
            secSavings.classList.add('hidden');
            btnLoan.className = 'px-3.5 py-1.5 rounded-lg transition-all bg-[#0F4C81] text-white shadow-xs';
        } else if (cat === 'savings') {
            secLoans.classList.add('hidden');
            secSavings.classList.remove('hidden');
            btnSavings.className = 'px-3.5 py-1.5 rounded-lg transition-all bg-[#0F4C81] text-white shadow-xs';
        }
    }
</script>
@endsection

