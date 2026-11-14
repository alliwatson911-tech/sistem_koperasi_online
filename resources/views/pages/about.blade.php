@extends('layouts.app')

@section('title', 'Tentang Kami & Tata Kelola - KSP Sahabat Sejahtera')

@section('content')
<div class="space-y-12 py-2 sm:py-4">

    {{-- Hero Banner Tentang Kami --}}
    <div class="bg-gradient-to-br from-[#0F4C81] via-[#1B263B] to-[#12304d] rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden border border-white/10">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-[#2A9D8F]/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -top-12 w-64 h-64 rounded-full bg-[#D4AF37]/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-white/10 backdrop-blur-md border border-white/20 text-[#D4AF37]">
                <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                <span>Badan Hukum Kemenkop UKM RI &bull; AHU-0012345.AH.01.26.TAHUN 2026</span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight text-white">
                Tentang KSP Sahabat Sejahtera
            </h1>

            <p class="text-sm sm:text-base text-slate-200 leading-relaxed font-normal">
                Lembaga keuangan koperasi modern berasaskan kekeluargaan dan gotong-royong. Kami hadir memberdayakan permodalan pelaku usaha mikro dan menumbuhkan kemandirian ekonomi anggota dengan tata kelola digital yang amanah dan transparan.
            </p>

            <div class="pt-2 flex flex-wrap items-center gap-3">
                <a href="#legalitas" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-[#D4AF37] hover:bg-[#C59F2D] text-[#1B263B] shadow-md transition-all">
                    Legalitas &amp; Pengesahan &darr;
                </a>
                <a href="#kontak" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-white/10 hover:bg-white/20 text-white border border-white/20 backdrop-blur-md transition-all">
                    Lokasi Kantor &amp; Kontak &darr;
                </a>
            </div>
        </div>
    </div>

    {{-- STATISTIK KOPERASI --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-1">
            <span class="text-xs text-slate-400 font-medium block">Anggota Aktif Terdaftar</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-[#0F4C81]">{{ number_format($stats['total_members'] ?? 1450, 0, ',', '.') }}+</div>
            <span class="text-[11px] text-slate-500 block">Tersebar di berbagai sektor UMKM</span>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-1">
            <span class="text-xs text-slate-400 font-medium block">Penyaluran Modal Mikro</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-emerald-700">Rp {{ number_format(($stats['total_loan_distributed'] ?? 12850000000) / 1000000000, 1) }} M</div>
            <span class="text-[11px] text-slate-500 block">Pembiayaan usaha produktif</span>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-1">
            <span class="text-xs text-slate-400 font-medium block">Aset Simpanan Terkelola</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-[#1B263B]">Rp {{ number_format(($stats['total_savings_managed'] ?? 18500000000) / 1000000000, 1) }} M</div>
            <span class="text-[11px] text-slate-500 block">Simpanan Pokok, Wajib &amp; Sukarela</span>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs space-y-1">
            <span class="text-xs text-slate-400 font-medium block">Kolektibilitas Pembayaran</span>
            <div class="text-2xl sm:text-3xl font-extrabold text-teal-700">98.6%</div>
            <span class="text-[11px] text-emerald-700 font-semibold block">Rasio NPL Sangat Sehat</span>
        </div>
    </div>

    {{-- VISI & MISI KOPERASI --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0F4C81] flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900">Visi Koperasi</h2>
            <p class="text-sm text-slate-600 leading-relaxed">
                Menjadi Koperasi Simpan Pinjam digital terdepan di Indonesia yang berdaya saing tinggi, tepercaya, dan mandiri dengan mengedepankan asas kekeluargaan, keadilan finansial, serta pemanfaatan teknologi modern demi kesejahteraan seluruh anggota.
            </p>
        </div>

        <div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900">Misi Utama</h2>
            <ul class="space-y-2.5 text-xs sm:text-sm text-slate-600 leading-relaxed">
                <li class="flex items-start gap-2">
                    <span class="text-[#2A9D8F] font-bold mt-0.5">&check;</span>
                    <span>Menyediakan akses permodalan usaha mikro yang cepat, adil, tanpa agunan rumit dan bebas biaya tersembunyi.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-[#2A9D8F] font-bold mt-0.5">&check;</span>
                    <span>Menumbuhkan budaya menabung masyarakat melalui simpanan yang menguntungkan dan aman.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-[#2A9D8F] font-bold mt-0.5">&check;</span>
                    <span>Menerapkan standar akuntansi publik berpasangan (*double-entry*) yang dapat diaudit secara berkala.</span>
                </li>
                <li class="flex items-start gap-2">
                    <span class="text-[#2A9D8F] font-bold mt-0.5">&check;</span>
                    <span>Mendistribusikan Sisa Hasil Usaha (SHU) secara adil, proporsional, dan tepat waktu kepada setiap anggota.</span>
                </li>
            </ul>
        </div>
    </div>

    {{-- LEGALITAS & BADAN HUKUM RESMI --}}
    <section id="legalitas" class="bg-[#FDFBF7] rounded-3xl p-6 sm:p-8 border border-[#E8E2D8] space-y-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#0F4C81] text-white flex items-center justify-center font-bold shadow-xs">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Legalitas &amp; Pengesahan Resmi</h2>
                <p class="text-xs text-slate-500">KSP Sahabat Sejahtera beroperasi sah di bawah perlindungan hukum Republik Indonesia</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-1">
                <span class="text-slate-400 block font-medium">SK Pengesahan Badan Hukum:</span>
                <strong class="text-slate-900 text-sm font-bold block">AHU-0012345.AH.01.26.TAHUN 2026</strong>
                <span class="text-[11px] text-emerald-700 font-semibold">Kemenkop UKM RI</span>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-1">
                <span class="text-slate-400 block font-medium">Nomor Induk Koperasi (NIK):</span>
                <strong class="text-slate-900 text-sm font-bold block font-mono">737105001234</strong>
                <span class="text-[11px] text-teal-700 font-semibold">Online Data System (ODS)</span>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-1">
                <span class="text-slate-400 block font-medium">Nomor Pokok Wajib Pajak (NPWP):</span>
                <strong class="text-slate-900 text-sm font-bold block font-mono">01.234.567.8-801.000</strong>
                <span class="text-[11px] text-slate-500 font-semibold">Wajib Pajak Badan Koperasi</span>
            </div>

            <div class="bg-white p-4 rounded-2xl border border-slate-200 space-y-1">
                <span class="text-slate-400 block font-medium">Status Akreditasi Koperasi:</span>
                <strong class="text-emerald-800 text-sm font-bold block">KUALITAS PRIMA (A)</strong>
                <span class="text-[11px] text-emerald-700 font-semibold">Audit Kepatuhan &amp; Likuiditas</span>
            </div>
        </div>
    </section>

    {{-- 7 PRINSIP JATI DIRI KOPERASI --}}
    <div class="space-y-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900">7 Prinsip Koperasi Indonesia (UU No. 25/1992)</h2>
            <p class="text-xs text-slate-500">Nilai-nilai luhur yang menjadi landasan operasional dan pelayanan kami setiap hari</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs">
            <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-1.5">
                <span class="font-bold text-[#0F4C81] text-sm block">1. Sukarela &amp; Terbuka</span>
                <p class="text-slate-600 leading-relaxed">Keanggotaan terbuka bagi seluruh WNI tanpa diskriminasi suku, ras, agama, maupun status sosial.</p>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-1.5">
                <span class="font-bold text-[#0F4C81] text-sm block">2. Pengelolaan Demokratis</span>
                <p class="text-slate-600 leading-relaxed">Prinsip satu anggota satu suara (*one member one vote*) dalam setiap Rapat Anggota Tahunan (RAT).</p>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-1.5">
                <span class="font-bold text-[#0F4C81] text-sm block">3. Pembagian SHU Adil</span>
                <p class="text-slate-600 leading-relaxed">SHU dibagikan sebanding dengan besarnya jasa usaha dan kontribusi simpanan masing-masing anggota.</p>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-1.5">
                <span class="font-bold text-[#0F4C81] text-sm block">4. Balas Jasa Modal Terbatas</span>
                <p class="text-slate-600 leading-relaxed">Imbal hasil modal diberikan secara wajar dan tidak mengeksploitasi anggota peminjam.</p>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-1.5">
                <span class="font-bold text-[#0F4C81] text-sm block">5. Kemandirian &amp; Otonomi</span>
                <p class="text-slate-600 leading-relaxed">Koperasi berdiri tegak secara otonom atas kekuatan swadaya anggota tanpa ketergantungan pihak luar.</p>
            </div>

            <div class="p-4 rounded-2xl bg-white border border-slate-200 space-y-1.5">
                <span class="font-bold text-[#0F4C81] text-sm block">6. Pendidikan Perkoperasian</span>
                <p class="text-slate-600 leading-relaxed">Mendidik dan membina literasi keuangan anggota dan pelaku usaha mikro agar semakin berkembang.</p>
            </div>
        </div>
    </div>

    {{-- INFORMASI KANTOR OPERASIONAL & KONTAK RESMI --}}
    <section id="kontak" class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-8">
        <div class="flex items-center justify-between flex-wrap gap-4 pb-4 border-b border-slate-100">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Kantor Pelayanan &amp; Layanan Bantuan</h2>
                <p class="text-xs sm:text-sm text-slate-500">Hubungi kami atau kunjungi loket operasional resmi untuk konsultasi langsung</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                &bull; Pelayanan Aktif
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs sm:text-sm">
            <div class="space-y-2">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[11px] block">Alamat Kantor Pusat:</span>
                <p class="font-medium text-slate-800 leading-relaxed">
                    Jl. Perintis Kemerdekaan No. 1, Daya, Kec. Biringkanaya, Kota Makassar, Sulawesi Selatan, 90241
                </p>
                <span class="text-slate-500 text-xs block">(Dekat Kawasan Industri Makassar / Simpang Lima Mandai)</span>
            </div>

            <div class="space-y-2">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[11px] block">Jam Kerja Operasional:</span>
                <p class="font-medium text-slate-800 leading-relaxed">
                    <strong>Senin – Jumat:</strong> 08.00 – 16.00 WITA<br>
                    <strong>Sabtu:</strong> 08.00 – 12.00 WITA<br>
                    <strong>Layanan Digital &amp; CS:</strong> 24 Jam Nonstop
                </p>
            </div>

            <div class="space-y-2">
                <span class="text-slate-400 font-bold uppercase tracking-wider text-[11px] block">Kontak Resmi Koperasi:</span>
                <p class="font-medium text-slate-800 leading-relaxed">
                    <strong>Telepon / WhatsApp:</strong> +62 821-9723-3174<br>
                    <strong>Layanan Anggota:</strong> +62 822-3558-5445<br>
                    <strong>Email:</strong> info@sahabatsejahtera.koperasi.id
                </p>
            </div>
        </div>

        <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="https://wa.me/6282197233174" target="_blank" rel="noopener noreferrer" class="px-5 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    <span>Hubungi CS via WhatsApp</span>
                </a>
                <a href="mailto:info@sahabatsejahtera.koperasi.id" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all">
                    Kirim Email Resmi
                </a>
            </div>

            @guest
            <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl bg-[#D4AF37] hover:bg-[#C59F2D] text-[#1B263B] font-bold text-xs shadow-xs transition-all">
                Daftar Jadi Anggota Sekarang &rarr;
            </a>
            @endguest
        </div>
    </section>

</div>
@endsection

