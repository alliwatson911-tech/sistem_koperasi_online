@extends('layouts.app')

@section('title', 'Koperasi Simpan Pinjam Online - KSP Sahabat Sejahtera')

@section('container-class', 'w-full py-0')

@section('styles')
<style>
    html {
        scroll-behavior: smooth;
    }
    .hero-gradient {
        background: radial-gradient(120% 120% at 50% 10%, #ffffff 0%, #f0fdf4 40%, #ecfdf5 70%, #d1fae5 100%);
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
    <section id="dashboard" class="hero-gradient border-b border-emerald-100/80 pt-10 pb-16 lg:pt-16 lg:pb-24" aria-labelledby="heading-dashboard">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            {{-- Sambutan & Ringkasan Organisasi Koperasi --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <div class="lg:col-span-7 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-emerald-100/90 text-emerald-900 border border-emerald-300 shadow-2xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                        BADAN HUKUM RESMI KEMENKOP UKM RI &bull; AHU-0012345.AH.01.26.TAHUN 2026
                    </div>

                    <h1 id="heading-dashboard" class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                        Koperasi Simpan Pinjam Digital: <span class="text-emerald-800">Amanah, Transparan, & Melayani Anggota.</span>
                    </h1>

                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-2xl">
                        Selamat datang di <strong>KSP Sahabat Sejahtera</strong>. Lembaga keuangan koperasi modern berasaskan kekeluargaan dan gotong-royong. Kami menyediakan permodalan mikro UMKM tanpa agunan rumit, simpanan bagi hasil berkah bulanan, serta transaksi digital 24 jam dengan standar akuntansi publik yang akuntabel.
                    </p>

                    <div class="flex flex-wrap items-center gap-3 pt-2">
                        <a href="{{ route('register') }}" class="btn btn-primary px-6 py-3 rounded-xl font-bold text-sm shadow-sm hover:shadow transition-all flex items-center gap-2">
                            <span>Daftar Anggota Baru</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="#simulasi" class="btn btn-secondary px-6 py-3 rounded-xl font-bold text-sm hover:bg-slate-100 transition-all">
                            Simulasi Pinjaman
                        </a>
                        <a href="https://wa.me/6282197233174?text=Halo%20Admin%20KSP%20Sahabat%20Sejahtera,%20saya%20ingin%20konsultasi%20layanan%20koperasi" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-4 py-3 rounded-xl text-xs sm:text-sm font-bold text-emerald-900 hover:text-emerald-950 hover:bg-emerald-100/60 transition-all">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            Chat CS 24 Jam WhatsApp
                        </a>
                    </div>

                    {{-- Fitur Unggulan Singkat --}}
                    <div class="pt-4 border-t border-emerald-200/60 grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs text-slate-600 font-medium">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Bagi Hasil SHU Tahunan</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Pencairan Cepat 1x24 Jam</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Tanpa Bunga Berbunga</span>
                        </div>
                    </div>
                </div>

                {{-- Ringkasan Organisasi & Statistik Card --}}
                <div class="lg:col-span-5">
                    <div class="bg-white rounded-3xl p-6 sm:p-7 border border-emerald-100 shadow-xl space-y-6 relative overflow-hidden">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl navbar-brand-badge flex items-center justify-center text-white font-black text-2xl shadow-sm">
                                    K
                                </div>
                                <div>
                                    <h2 class="text-base font-bold text-slate-900">KSP Sahabat Sejahtera</h2>
                                    <p class="text-xs text-slate-500">Ringkasan Organisasi & Tata Kelola</p>
                                </div>
                            </div>
                            <span class="badge badge-success text-2xs">Terakreditasi</span>
                        </div>

                        <div class="grid grid-cols-2 gap-3.5">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <span class="text-2xs font-bold uppercase tracking-wider text-slate-500 block">Anggota Aktif</span>
                                <span class="text-xl sm:text-2xl font-black text-emerald-900 mt-1 block">{{ number_format($stats['total_members']) }}+</span>
                                <span class="text-3xs text-slate-400">Terdaftar di seluruh wilayah</span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <span class="text-2xs font-bold uppercase tracking-wider text-slate-500 block">Aset Dikelola</span>
                                <span class="text-xl sm:text-2xl font-black text-slate-900 mt-1 block">Rp {{ number_format($stats['total_savings_managed']/1000000000, 1) }} M</span>
                                <span class="text-3xs text-slate-400">Simpanan Pokok, Wajib, Sukarela</span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <span class="text-2xs font-bold uppercase tracking-wider text-slate-500 block">Modal UMKM Tersalur</span>
                                <span class="text-xl sm:text-2xl font-black text-emerald-900 mt-1 block">Rp {{ number_format($stats['total_loan_distributed']/1000000000, 1) }} M</span>
                                <span class="text-3xs text-slate-400">Pemberdayaan sektor produktif</span>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                                <span class="text-2xs font-bold uppercase tracking-wider text-slate-500 block">Kolektibilitas</span>
                                <span class="text-xl sm:text-2xl font-black text-emerald-700 mt-1 block">98.6%</span>
                                <span class="text-3xs text-slate-400">Rasio lancar (NPL sehat)</span>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-900 leading-relaxed">
                            <strong>Prinsip Koperasi:</strong> Dari anggota, oleh anggota, dan untuk kesejahteraan anggota. Setiap transaksi tercatat dalam pembukuan berpasangan <em>(Double-Entry Bookkeeping)</em> yang dapat diaudit secara berkala.
                        </div>
                    </div>
                </div>
            </div>

            {{-- TAMPILAN BILLBOARD UNTUK PENGUMUMAN RESMI --}}
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-md">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-slate-100 gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-900 flex items-center justify-center font-bold">
                            <svg class="w-5 h-5 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/></svg>
                        </div>
                        <div>
                            <span class="text-2xs font-bold text-amber-800 uppercase tracking-wider block">Papan Informasi Resmi</span>
                            <h2 class="text-lg font-extrabold text-slate-900">Billboard Pengumuman Koperasi</h2>
                        </div>
                    </div>

                    {{-- Navigasi Tab Billboard --}}
                    <div class="flex items-center gap-1.5 bg-slate-100 p-1 rounded-xl self-start sm:self-auto text-xs" role="tablist">
                        <button type="button" onclick="switchBillboard(0)" id="bb-tab-0" class="px-3 py-1.5 rounded-lg font-bold bg-white text-emerald-900 shadow-2xs transition-all" role="tab" aria-selected="true">
                            RAT & SHU
                        </button>
                        <button type="button" onclick="switchBillboard(1)" id="bb-tab-1" class="px-3 py-1.5 rounded-lg font-semibold text-slate-600 hover:text-slate-900 transition-all" role="tab" aria-selected="false">
                            Modal UMKM
                        </button>
                        <button type="button" onclick="switchBillboard(2)" id="bb-tab-2" class="px-3 py-1.5 rounded-lg font-semibold text-slate-600 hover:text-slate-900 transition-all" role="tab" aria-selected="false">
                            Pembayaran QRIS & VA
                        </button>
                        <button type="button" onclick="switchBillboard(3)" id="bb-tab-3" class="px-3 py-1.5 rounded-lg font-semibold text-slate-600 hover:text-slate-900 transition-all" role="tab" aria-selected="false">
                            Hasil Audit
                        </button>
                    </div>
                </div>

                {{-- Konten Slider / Billboard Tab --}}
                <div class="pt-6">
                    {{-- Slide 0 --}}
                    <div id="bb-slide-0" class="billboard-slide active space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-warning text-2xs font-bold">Pengumuman Utama</span>
                            <span class="text-xs text-slate-400">Dipublikasikan: 01 November 2026</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Rapat Anggota Tahunan (RAT) Tahun Buku Berjalan & Alokasi Pembagian SHU</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-4xl">
                            Pengurus mengumumkan bahwa Rapat Anggota Tahunan (RAT) telah menetapkan alokasi Sisa Hasil Usaha (SHU) sebesar <strong>40% untuk Jasa Modal dan Jasa Anggota</strong>. Anggota yang telah melunasi Simpanan Pokok dan Wajib dapat langsung memeriksa simulasi hak perolehan SHU pada menu akuntansi di dashboard anggota.
                        </p>
                        <div class="pt-2 flex items-center gap-4 text-xs font-bold text-emerald-800">
                            <span>Status: Pelaksanaan Berjalan</span>
                            <span>&bull;</span>
                            <a href="#panduan" class="hover:underline flex items-center gap-1">Pelajari Alur SHU &rarr;</a>
                        </div>
                    </div>

                    {{-- Slide 1 --}}
                    <div id="bb-slide-1" class="billboard-slide space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-success text-2xs font-bold">Program Permodalan</span>
                            <span class="text-xs text-slate-400">Berlaku untuk Anggota Aktif</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Skema Pinjaman Mikro Mingguan Khusus Pelaku Usaha UMKM</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-4xl">
                            KSP Sahabat Sejahtera meluncurkan fasilitas pembiayaan mikro mingguan (tenor 4 s.d. 24 minggu) dengan margin anuitas bersahabat mulai 0.40% per minggu. Persyaratan mudah: cukup upload foto tempat usaha saat mengajukan permohonan melalui sistem koperasi online ini.
                        </p>
                        <div class="pt-2 flex items-center gap-4 text-xs font-bold text-emerald-800">
                            <a href="#produk" class="hover:underline">Lihat Rincian Plafon Produk Pinjaman &rarr;</a>
                        </div>
                    </div>

                    {{-- Slide 2 --}}
                    <div id="bb-slide-2" class="billboard-slide space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-info text-2xs font-bold">Teknologi Pembayaran</span>
                            <span class="text-xs text-slate-400">Layanan Otomatis 24 Jam</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Setoran Simpanan Sukarela & Angsuran Melalui QRIS dan Virtual Account Bank</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-4xl">
                            Anggota kini dapat melakukan setoran simpanan sukarela dan pembayaran angsuran menggunakan QRIS dinamis serta Virtual Account resmi Bank Mandiri, BRI, BCA, dan BNI. Transaksi terverifikasi otomatis melalui webhook server-to-server tanpa perlu konfirmasi manual bukti transfer.
                        </p>
                        <div class="pt-2 flex items-center gap-4 text-xs font-bold text-emerald-800">
                            <a href="#panduan" class="hover:underline">Lihat Panduan Pembayaran Digital &rarr;</a>
                        </div>
                    </div>

                    {{-- Slide 3 --}}
                    <div id="bb-slide-3" class="billboard-slide space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="badge badge-gray text-2xs font-bold">Kepatuhan Hukum</span>
                            <span class="text-xs text-slate-400">Audit Berkala Kemenkop UKM</span>
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Hasil Audit Pengawasan Koperasi: Predikat Sehat & Akuntabel</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-4xl">
                            Berdasarkan hasil pengawasan dan audit independen tata kelola koperasi, KSP Sahabat Sejahtera dinyatakan berstatus <strong>KOPERASI SEHAT</strong> dengan rasio likuiditas aman, kepatuhan batas maksimal denda non-compounding, serta pemisahan tugas kewenangan <em>(Maker-Checker Segregation of Duties)</em>.
                        </p>
                        <div class="pt-2 flex items-center gap-4 text-xs font-bold text-emerald-800">
                            <a href="#tentang-kami" class="hover:underline">Informasi Legalitas & Kontak Pengurus &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- STANDAR LAYANAN KOPERASI (4 PILAR KEUNGGULAN) --}}
            <div class="space-y-6">
                <div class="text-center max-w-2xl mx-auto space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Komitmen Integritas</span>
                    <h2 class="text-2xl font-black text-slate-900">Standar Layanan KSP Sahabat Sejahtera</h2>
                    <p class="text-xs sm:text-sm text-slate-500">Prinsip operasional terpercaya yang menjadi landasan setiap pelayanan kami kepada anggota.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="card p-5 border-t-4 border-t-emerald-600 hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center font-bold mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1.5">Transparansi Finansial Mutlak</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Semua perhitungan bunga, biaya administrasi, dan simulasi angsuran dihitung terbuka. Tidak ada biaya tersembunyi atau potongan siluman.
                        </p>
                    </div>

                    <div class="card p-5 border-t-4 border-t-blue-600 hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-800 flex items-center justify-center font-bold mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1.5">Pencairan Cepat 1x24 Jam</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Proses verifikasi berkas dan analisis kredit terdigitalisasi. Begitu akad digital ditandatangani, dana langsung dicairkan oleh teller.
                        </p>
                    </div>

                    <div class="card p-5 border-t-4 border-t-amber-600 hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center font-bold mb-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-900 mb-1.5">Keadilan & Denda Terbatas</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Denda keterlambatan dibatasi maksimal 10% per angsuran tanpa bunga-berbunga. Tersedia permohonan restrukturisasi jika ada musibah usaha.
                        </p>
                    </div>

                    <div class="card p-5 border-t-4 border-t-emerald-800 hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-900 flex items-center justify-center font-bold mb-3">
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
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 space-y-6">
                <div class="flex items-center gap-3 pb-3 border-b border-slate-100">
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-800 flex items-center justify-center font-bold">
                        ?
                    </div>
                    <div>
                        <span class="text-2xs font-bold text-emerald-800 uppercase tracking-wider block">Pusat Informasi Anggota</span>
                        <h2 class="text-lg font-black text-slate-900">Pertanyaan yang Sering Diajukan (FAQ)</h2>
                    </div>
                </div>

                <div class="space-y-3" id="faqAccordion">
                    {{-- FAQ Item 1 --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all">
                        <button type="button" onclick="toggleFaq('faq-1')" class="w-full text-left p-4 bg-slate-50/70 hover:bg-slate-100/70 flex items-center justify-between gap-4 font-bold text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500" aria-expanded="false" id="btn-faq-1">
                            <span>1. Apa saja syarat utama untuk mendaftar menjadi anggota KSP Sahabat Sejahtera?</span>
                            <span class="text-slate-400 font-normal text-lg" id="icon-faq-1">+</span>
                        </button>
                        <div id="faq-1" class="hidden p-4 text-xs sm:text-sm text-slate-600 bg-white leading-relaxed border-t border-slate-100">
                            Syarat pendaftaran sangat mudah: Anda adalah Warga Negara Indonesia (WNI) berusia minimal 18 tahun, memiliki e-KTP dan Kartu Keluarga yang masih berlaku, memiliki kontak WhatsApp aktif, serta bersedia menyetujui Anggaran Dasar & Anggaran Rumah Tangga (AD/ART) Koperasi dan menyetorkan Simpanan Pokok awal.
                        </div>
                    </div>

                    {{-- FAQ Item 2 --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all">
                        <button type="button" onclick="toggleFaq('faq-2')" class="w-full text-left p-4 bg-slate-50/70 hover:bg-slate-100/70 flex items-center justify-between gap-4 font-bold text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500" aria-expanded="false" id="btn-faq-2">
                            <span>2. Berapa nominal Simpanan Pokok dan Simpanan Wajib?</span>
                            <span class="text-slate-400 font-normal text-lg" id="icon-faq-2">+</span>
                        </button>
                        <div id="faq-2" class="hidden p-4 text-xs sm:text-sm text-slate-600 bg-white leading-relaxed border-t border-slate-100">
                            Simpanan Pokok adalah sebesar <strong>Rp 100.000</strong> (dibayarkan satu kali di awal pendaftaran sebagai bukti keanggotaan/saham). Simpanan Wajib adalah sebesar <strong>Rp 50.000 per bulan</strong> yang menjadi tabungan rutin anggota dan menjadi dasar perhitungan pembagian Sisa Hasil Usaha (SHU) setiap tahunnya.
                        </div>
                    </div>

                    {{-- FAQ Item 3 --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all">
                        <button type="button" onclick="toggleFaq('faq-3')" class="w-full text-left p-4 bg-slate-50/70 hover:bg-slate-100/70 flex items-center justify-between gap-4 font-bold text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500" aria-expanded="false" id="btn-faq-3">
                            <span>3. Bagaimana cara mengajukan pinjaman permodalan usaha secara online?</span>
                            <span class="text-slate-400 font-normal text-lg" id="icon-faq-3">+</span>
                        </button>
                        <div id="faq-3" class="hidden p-4 text-xs sm:text-sm text-slate-600 bg-white leading-relaxed border-t border-slate-100">
                            Setelah akun anggota Anda diverifikasi oleh teller, Anda dapat langsung memilih menu <em>Ajukan Pinjaman</em> di dasbor, memilih produk pinjaman yang sesuai, memasukkan nominal dan tenor, serta melampirkan foto tempat usaha / surat keterangan penghasilan. Analis kredit akan melakukan survei cepat dan menerbitkan akad digital untuk ditandatangani.
                        </div>
                    </div>

                    {{-- FAQ Item 4 --}}
                    <div class="border border-slate-200 rounded-2xl overflow-hidden transition-all">
                        <button type="button" onclick="toggleFaq('faq-4')" class="w-full text-left p-4 bg-slate-50/70 hover:bg-slate-100/70 flex items-center justify-between gap-4 font-bold text-sm text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500" aria-expanded="false" id="btn-faq-4">
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
    <section id="produk" class="py-16 bg-slate-50 border-b border-slate-200" aria-labelledby="heading-produk">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Katalog Lengkap</span>
                <h2 id="heading-produk" class="text-2xl sm:text-3xl font-black text-slate-900">Produk Pembiayaan & Simpanan</h2>
                <p class="text-xs sm:text-sm text-slate-500">Pilihan fasilitas permodalan adil dan simpanan terencana yang dirancang khusus untuk anggota KSP Sahabat Sejahtera.</p>
            </div>

            {{-- Kartu Produk Pinjaman (Dinamis dari Database) --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                        Produk Pinjaman Anggota
                    </h3>
                    <a href="{{ route('loans.products') }}" class="text-xs font-bold text-emerald-800 hover:underline">
                        Lihat Semua Rincian &rarr;
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($products as $p)
                    <div class="card p-6 border-t-4 border-t-emerald-700 flex flex-col justify-between hover:shadow-lg transition-all duration-200 bg-white">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="badge badge-info text-2xs">{{ $p->code }}</span>
                                <span class="badge badge-gray text-2xs">Metode: {{ ucfirst($p->calculation_method) }}</span>
                            </div>

                            <div>
                                <h4 class="text-lg font-black text-slate-900">{{ $p->name }}</h4>
                                <p class="text-xs text-slate-500 mt-1 line-clamp-2 leading-relaxed">{{ $p->description }}</p>
                            </div>

                            <div class="bg-slate-50 rounded-2xl p-4 text-xs space-y-2.5 border border-slate-100">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-500">Suku Bunga:</span>
                                    <strong class="text-emerald-900 font-black">{{ $p->interest_rate_per_period }}% / {{ strtolower($p->tenor_unit_label) }}</strong>
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
                                    <strong class="text-slate-800">{{ $p->admin_fee_percentage }}% + Rp {{ number_format($p->admin_fee_fixed, 0, ',', '.') }}</strong>
                                </div>
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
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                        Produk Simpanan Anggota
                    </h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="card p-6 border-l-4 border-l-emerald-600 bg-white space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Simpanan Wajib Awal</span>
                            <span class="badge badge-success text-2xs">Saham Koperasi</span>
                        </div>
                        <h4 class="text-lg font-black text-slate-900">Simpanan Pokok</h4>
                        <div class="text-2xl font-black text-slate-900">Rp 100.000 <span class="text-xs text-slate-400 font-normal">/ sekali setor</span></div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Dibayarkan satu kali saat mendaftar sebagai anggota resmi. Berfungsi sebagai bukti kepemilikan saham koperasi dan dapat diambil kembali saat keluar dari keanggotaan.
                        </p>
                    </div>

                    <div class="card p-6 border-l-4 border-l-blue-600 bg-white space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-blue-800 uppercase tracking-wider">Rutin Bulanan</span>
                            <span class="badge badge-info text-2xs">Dasar SHU</span>
                        </div>
                        <h4 class="text-lg font-black text-slate-900">Simpanan Wajib</h4>
                        <div class="text-2xl font-black text-slate-900">Rp 50.000 <span class="text-xs text-slate-400 font-normal">/ bulan</span></div>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Tabungan rutin bulanan anggota yang menjadi dasar perhitungan pembagian dividen Sisa Hasil Usaha (SHU) setiap penutupan buku tahunan koperasi.
                        </p>
                    </div>

                    <div class="card p-6 border-l-4 border-l-amber-600 bg-white space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-amber-800 uppercase tracking-wider">Fleksibel & Likuid</span>
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
    <section id="panduan" class="py-16 bg-white border-b border-slate-200" aria-labelledby="heading-panduan">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Petunjuk Praktis</span>
                <h2 id="heading-panduan" class="text-2xl sm:text-3xl font-black text-slate-900">Panduan Menjadi Anggota & Simpan Pinjam</h2>
                <p class="text-xs sm:text-sm text-slate-500">Langkah mudah bergabung dan alur operasional koperasi online yang transparan dan aman.</p>
            </div>

            {{-- 4 Langkah Menjadi Anggota --}}
            <div class="space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">A. Panduan 4 Langkah Menjadi Anggota Koperasi</h3>
                    <span class="badge badge-success text-2xs">Proses Cepat Online</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="card p-5 bg-slate-50/70 border border-slate-200 space-y-3 relative">
                        <div class="w-9 h-9 rounded-xl bg-emerald-800 text-white font-black flex items-center justify-center text-sm shadow-xs">
                            1
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">Registrasi Akun</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Isi formulir pendaftaran online dengan nama lengkap, NIK, alamat email, dan nomor WhatsApp aktif.
                        </p>
                    </div>

                    <div class="card p-5 bg-slate-50/70 border border-slate-200 space-y-3 relative">
                        <div class="w-9 h-9 rounded-xl bg-emerald-800 text-white font-black flex items-center justify-center text-sm shadow-xs">
                            2
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">Unggah Dokumen e-KYC</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Unggah foto e-KTP dan Kartu Keluarga yang jelas untuk verifikasi data kependudukan sesuai aturan perkoperasian.
                        </p>
                    </div>

                    <div class="card p-5 bg-slate-50/70 border border-slate-200 space-y-3 relative">
                        <div class="w-9 h-9 rounded-xl bg-emerald-800 text-white font-black flex items-center justify-center text-sm shadow-xs">
                            3
                        </div>
                        <h4 class="text-sm font-bold text-slate-900">Verifikasi Petugas</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Teller dan petugas keanggotaan memvalidasi data Anda dalam waktu 1x24 jam kerja demi keamanan bersama.
                        </p>
                    </div>

                    <div class="card p-5 bg-slate-50/70 border border-slate-200 space-y-3 relative">
                        <div class="w-9 h-9 rounded-xl bg-emerald-800 text-white font-black flex items-center justify-center text-sm shadow-xs">
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
                <div class="card p-6 bg-slate-50 border border-slate-200 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-200">
                        <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">B. Alur Simpanan Anggota</h3>
                            <p class="text-2xs text-slate-500">Mekanisme Setor & Tarik Simpanan</p>
                        </div>
                    </div>

                    <ul class="space-y-3 text-xs text-slate-600">
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-200 text-emerald-900 flex items-center justify-center font-bold text-2xs mt-0.5 shrink-0">&check;</span>
                            <div><strong>Setoran Mandiri:</strong> Anggota memilih rekening simpanan sukarela di dasbor, menginput nominal, lalu membayar via QRIS/VA atau transfer bank.</div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-200 text-emerald-900 flex items-center justify-center font-bold text-2xs mt-0.5 shrink-0">&check;</span>
                            <div><strong>Konfirmasi Otomatis:</strong> Sistem payment gateway mendeteksi pembayaran server-to-server dan saldo simpanan bertambah seketika.</div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-200 text-emerald-900 flex items-center justify-center font-bold text-2xs mt-0.5 shrink-0">&check;</span>
                            <div><strong>Penarikan Simpanan Sukarela:</strong> Dapat diajukan melalui portal online dan diverifikasi oleh teller kasir untuk dicairkan ke rekening bank Anda.</div>
                        </li>
                    </ul>
                </div>

                {{-- Alur Pinjaman --}}
                <div class="card p-6 bg-slate-50 border border-slate-200 space-y-4">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-200">
                        <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-800 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">C. Alur Pengajuan Pinjaman Modal</h3>
                            <p class="text-2xs text-slate-500">Mekanisme Pengajuan Hingga Pencairan</p>
                        </div>
                    </div>

                    <ul class="space-y-3 text-xs text-slate-600">
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-200 text-blue-900 flex items-center justify-center font-bold text-2xs mt-0.5 shrink-0">&check;</span>
                            <div><strong>Pengajuan & Foto Usaha:</strong> Anggota mengisi jumlah permohonan pinjaman dan melampirkan foto tempat usaha secara live atau unggah berkas.</div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-200 text-blue-900 flex items-center justify-center font-bold text-2xs mt-0.5 shrink-0">&check;</span>
                            <div><strong>Analisis Kredit & Otorisasi:</strong> Analis kredit meneliti kelayakan kapasitas bayar, disetujui manajer koperasi secara berjenjang <em>(Maker-Checker)</em>.</div>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-blue-200 text-blue-900 flex items-center justify-center font-bold text-2xs mt-0.5 shrink-0">&check;</span>
                            <div><strong>Akad Digital & Pencairan:</strong> Anggota menandatangani akad digital dengan 1 klik, dan dana pinjaman langsung dicairkan tanpa potongan liar.</div>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Syarat-Syarat Umum Koperasi Online --}}
            <div class="bg-emerald-900 text-white rounded-3xl p-6 sm:p-8 space-y-4">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md bg-emerald-800 text-emerald-200 text-2xs font-bold uppercase tracking-wider">Legalitas & Kepatuhan</span>
                    <h3 class="text-lg font-black">Syarat & Ketentuan Umum Anggota KSP Sahabat Sejahtera</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs text-emerald-100/90 leading-relaxed pt-2">
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
    <section id="simulasi" class="py-16 bg-slate-50 border-b border-slate-200" aria-labelledby="heading-simulasi">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
            
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Kalkulator Finansial</span>
                <h2 id="heading-simulasi" class="text-2xl sm:text-3xl font-black text-slate-900">Simulasi Perhitungan Pinjaman Online</h2>
                <p class="text-xs sm:text-sm text-slate-500">Praktekkan simulasi angsuran secara transparan. Sesuaikan plafon, jangka waktu tenor, dan metode perhitungan bunga.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- Form Input Parameter Simulasi (Kiri) --}}
                <div class="lg:col-span-5 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm space-y-5">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Parameter Pinjaman</h3>
                            <p class="text-2xs text-slate-400">Atur plafon & tenor yang Anda butuhkan</p>
                        </div>
                    </div>

                    <form id="landingSimForm" action="{{ route('home') }}#simulasi" method="GET" class="space-y-4">
                        <div class="form-group">
                            <label for="sim_product_id" class="form-label text-xs font-bold text-slate-700">Pilih Produk Pinjaman *</label>
                            <select name="product_id" id="sim_product_id" class="form-control text-xs" onchange="this.form.submit()" required>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" data-is-daily="{{ $p->isDaily() ? '1' : '0' }}" data-is-weekly="{{ $p->isWeekly() ? '1' : '0' }}" data-rate="{{ $p->interest_rate_per_period }}" data-min-amount="{{ $p->min_amount }}" data-max-amount="{{ $p->max_amount }}" data-min-tenor="{{ $p->min_tenor }}" data-max-tenor="{{ $p->max_tenor }}" {{ ($selectedProduct && $selectedProduct->id == $p->id) ? 'selected' : '' }}>
                                        {{ $p->name }} ({{ $p->interest_rate_per_period }}%/{{ strtolower($p->tenor_unit_label) }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="sim_amount" class="form-label text-xs font-bold text-slate-700">Nominal Plafon (Rp) *</label>
                            <input type="number" id="sim_amount" name="amount" class="form-control text-xs font-bold text-slate-900"
                                   value="{{ $amount }}"
                                   required min="{{ $selectedProduct->min_amount ?? 100000 }}" max="{{ $selectedProduct->max_amount ?? 100000000 }}" step="{{ $selectedProduct && $selectedProduct->isDaily() ? 50000 : 250000 }}">
                            
                            <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                @if($selectedProduct && $selectedProduct->isDaily())
                                    <button type="button" onclick="setAmount(100000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">100 Rb</button>
                                    <button type="button" onclick="setAmount(250000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">250 Rb</button>
                                    <button type="button" onclick="setAmount(500000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">500 Rb</button>
                                    <button type="button" onclick="setAmount(750000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">750 Rb</button>
                                    <button type="button" onclick="setAmount(1000000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">1 Jt</button>
                                @elseif($selectedProduct && $selectedProduct->isWeekly())
                                    <button type="button" onclick="setAmount(1000000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">1 Jt</button>
                                    <button type="button" onclick="setAmount(2500000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">2.5 Jt</button>
                                    <button type="button" onclick="setAmount(5000000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">5 Jt</button>
                                    <button type="button" onclick="setAmount(10000000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">10 Jt</button>
                                @else
                                    <button type="button" onclick="setAmount(5000000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">5 Jt</button>
                                    <button type="button" onclick="setAmount(10000000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">10 Jt</button>
                                    <button type="button" onclick="setAmount(25000000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">25 Jt</button>
                                    <button type="button" onclick="setAmount(50000000)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">50 Jt</button>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            @php
                                $unitLabel = $selectedProduct ? $selectedProduct->tenor_unit_label : 'Bulan';
                            @endphp
                            <label for="sim_tenor" class="form-label text-xs font-bold text-slate-700">Jangka Waktu Tenor ({{ $unitLabel }}) *</label>
                            <input type="number" id="sim_tenor" name="tenor" class="form-control text-xs font-bold text-slate-900"
                                   value="{{ $tenor }}"
                                   required min="{{ $selectedProduct->min_tenor ?? 1 }}" max="{{ $selectedProduct->max_tenor ?? 60 }}">
                            
                            <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                @if($selectedProduct && $selectedProduct->isDaily())
                                    <button type="button" onclick="setTenor(7)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">7 Hari</button>
                                    <button type="button" onclick="setTenor(10)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">10 Hari</button>
                                    <button type="button" onclick="setTenor(14)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">14 Hari</button>
                                    <button type="button" onclick="setTenor(21)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">21 Hari</button>
                                    <button type="button" onclick="setTenor(30)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">30 Hari</button>
                                @elseif($selectedProduct && $selectedProduct->isWeekly())
                                    <button type="button" onclick="setTenor(4)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">4 Mgg</button>
                                    <button type="button" onclick="setTenor(8)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">8 Mgg</button>
                                    <button type="button" onclick="setTenor(10)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">10 Mgg</button>
                                    <button type="button" onclick="setTenor(16)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">16 Mgg</button>
                                    <button type="button" onclick="setTenor(24)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">24 Mgg</button>
                                @else
                                    <button type="button" onclick="setTenor(6)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">6 Bln</button>
                                    <button type="button" onclick="setTenor(12)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">12 Bln</button>
                                    <button type="button" onclick="setTenor(24)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">24 Bln</button>
                                    <button type="button" onclick="setTenor(36)" class="text-3xs px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-semibold">36 Bln</button>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="sim_method" class="form-label text-xs font-bold text-slate-700">Metode Perhitungan Bunga</label>
                            <select name="calculation_method" id="sim_method" class="form-control text-xs">
                                <option value="annuity" {{ $method === 'annuity' ? 'selected' : '' }}>Anuitas (Angsuran Tetap Tiap Periode)</option>
                                <option value="effective" {{ $method === 'effective' ? 'selected' : '' }}>Efektif (Saldo Menurun)</option>
                                <option value="flat" {{ $method === 'flat' ? 'selected' : '' }}>Flat (Pokok & Bunga Tetap)</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-full py-3 rounded-xl font-bold text-xs shadow-xs">
                            Hitung Ulang Simulasi
                        </button>
                    </form>
                </div>

                {{-- Hasil Rincian Simulasi (Kanan) --}}
                <div class="lg:col-span-7 space-y-6">
                    @if($simulationResult)
                    <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200 shadow-sm space-y-6">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                            <div>
                                <span class="text-2xs font-bold text-emerald-800 uppercase tracking-wider block">Hasil Estimasi Resmi</span>
                                <h3 class="text-base font-bold text-slate-900">Rincian Angsuran & Pengembalian</h3>
                            </div>
                            <span class="badge badge-success text-2xs">Bunga Transparan</span>
                        </div>

                        {{-- Estimasi Angsuran Besar --}}
                        <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 text-center space-y-1">
                            <span class="text-xs font-semibold text-emerald-900">Estimasi Angsuran Per {{ $selectedProduct ? $selectedProduct->tenor_unit_label : 'Periode' }}</span>
                            <div class="text-3xl sm:text-4xl font-black text-emerald-800">
                                Rp {{ number_format($simulationResult['first_installment'] ?? $simulationResult['first_month_installment'], 0, ',', '.') }}
                            </div>
                            <span class="text-2xs text-slate-500 block">Selama {{ $tenor }} {{ $selectedProduct ? $selectedProduct->tenor_unit_label : 'Periode' }} (Metode: {{ ucfirst($method) }})</span>
                        </div>

                        {{-- Grid Ringkasan Finansial --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-slate-400 block text-3xs uppercase font-bold">Pokok Pinjaman</span>
                                <strong class="text-slate-900 text-sm">Rp {{ number_format($amount, 0, ',', '.') }}</strong>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-slate-400 block text-3xs uppercase font-bold">Total Bunga/Margin</span>
                                <strong class="text-emerald-900 text-sm">Rp {{ number_format($simulationResult['total_interest'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-slate-400 block text-3xs uppercase font-bold">Biaya Admin</span>
                                <strong class="text-slate-900 text-sm">Rp {{ number_format($simulationResult['admin_fee'], 0, ',', '.') }}</strong>
                            </div>
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                                <span class="text-slate-400 block text-3xs uppercase font-bold">Total Pengembalian</span>
                                <strong class="text-slate-900 text-sm">Rp {{ number_format($simulationResult['total_payable'], 0, ',', '.') }}</strong>
                            </div>
                        </div>

                        {{-- Tabel Jadwal 5 Angsuran Pertama --}}
                        <div class="space-y-2">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-700">
                                <span>Preview 5 Jadwal Angsuran Pertama</span>
                                <a href="{{ route('loans.simulation', ['product_id' => $selectedProduct->id, 'amount' => $amount, 'tenor' => $tenor, 'calculation_method' => $method]) }}" class="text-emerald-800 hover:underline">Lihat Rincian Jadwal Penuh &rarr;</a>
                            </div>

                            <div class="table-responsive bg-white rounded-xl border border-slate-200">
                                <table class="text-xs">
                                    <thead>
                                        <tr>
                                            <th>Periode</th>
                                            <th>Jatuh Tempo</th>
                                            <th>Pokok</th>
                                            <th>Bunga</th>
                                            <th>Total Angsuran</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(array_slice($simulationResult['schedules'], 0, 5) as $s)
                                        <tr>
                                            <td><strong>Ke-{{ $s['installment_no'] }}</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($s['due_date'])->format('d/m/Y') }}</td>
                                            <td>Rp {{ number_format($s['principal_amount'], 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($s['interest_amount'], 0, ',', '.') }}</td>
                                            <td><strong class="text-emerald-800">Rp {{ number_format($s['total_amount'], 0, ',', '.') }}</strong></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="pt-2 flex flex-col sm:flex-row gap-3">
                            <a href="{{ route('login') }}" class="btn btn-primary w-full py-3 rounded-xl font-bold text-xs text-center shadow-xs">
                                Ajukan Pinjaman Ini Sekarang
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
    <section id="tentang-kami" class="py-16 bg-white border-b border-slate-200" aria-labelledby="heading-tentang">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
            
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-800">Hubungi & Kunjungi Kami</span>
                <h2 id="heading-tentang" class="text-2xl sm:text-3xl font-black text-slate-900">Tentang Kami & Layanan Anggota</h2>
                <p class="text-xs sm:text-sm text-slate-500">Pusat informasi kontak, alamat kantor operasional, jam layanan, dan peta lokasi kantor pusat KSP Sahabat Sejahtera.</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                {{-- Rincian Kontak & Kantor (Kiri) --}}
                <div class="lg:col-span-6 space-y-6">
                    <div class="card p-6 bg-slate-50 border border-slate-200 space-y-5">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
                            <div class="w-10 h-10 rounded-xl bg-emerald-800 text-white flex items-center justify-center font-bold">
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
                                <span class="p-2 rounded-lg bg-emerald-100 text-emerald-900 shrink-0 mt-0.5">
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
                                <span class="p-2 rounded-lg bg-emerald-100 text-emerald-900 shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </span>
                                <div>
                                    <strong class="text-slate-800 block mb-0.5">Kontak Layanan Admin & CS:</strong>
                                    <div class="space-y-1 text-slate-600">
                                        <div>WhatsApp CS 24 Jam: <a href="https://wa.me/6282197233174" target="_blank" rel="noopener" class="text-emerald-800 font-bold hover:underline">+62 821-9723-3174</a></div>
                                        <div>Telepon Operasional: <span class="font-bold text-slate-800">(0411) 892345</span></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Jam Layanan Kas --}}
                            <div class="flex items-start gap-3">
                                <span class="p-2 rounded-lg bg-emerald-100 text-emerald-900 shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </span>
                                <div>
                                    <strong class="text-slate-800 block mb-0.5">Jam Operasional Layanan:</strong>
                                    <div class="text-slate-600 space-y-0.5">
                                        <div>Layanan Kas Kantor: Senin - Jumat (08.00 - 16.00 WITA), Sabtu (08.30 - 13.00 WITA)</div>
                                        <div>Layanan Sistem Online: <strong class="text-emerald-800">24 Jam Nonstop Setiap Hari</strong></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Email Resmi --}}
                            <div class="flex items-start gap-3">
                                <span class="p-2 rounded-lg bg-emerald-100 text-emerald-900 shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </span>
                                <div>
                                    <strong class="text-slate-800 block mb-0.5">Alamat Email Resmi:</strong>
                                    <div class="space-y-0.5 text-slate-600">
                                        <div>Layanan Anggota: <a href="mailto:layanan@koperasi.systemlji.com" class="text-emerald-800 font-bold hover:underline">layanan@koperasi.systemlji.com</a></div>
                                        <div>Pengurus Koperasi: <a href="mailto:pengurus@koperasi.systemlji.com" class="text-emerald-800 font-bold hover:underline">pengurus@koperasi.systemlji.com</a></div>
                                    </div>
                                </div>
                            </div>

                            {{-- Sosial Media --}}
                            <div class="flex items-start gap-3 pt-2">
                                <span class="p-2 rounded-lg bg-emerald-100 text-emerald-900 shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </span>
                                <div>
                                    <strong class="text-slate-800 block mb-1">Sosial Media Resmi:</strong>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="https://instagram.com" target="_blank" rel="noopener" class="px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 hover:text-emerald-800 hover:border-emerald-300 font-semibold text-2xs">Instagram</a>
                                        <a href="https://facebook.com" target="_blank" rel="noopener" class="px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 hover:text-emerald-800 hover:border-emerald-300 font-semibold text-2xs">Facebook</a>
                                        <a href="https://youtube.com" target="_blank" rel="noopener" class="px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 hover:text-emerald-800 hover:border-emerald-300 font-semibold text-2xs">YouTube</a>
                                        <a href="https://linkedin.com" target="_blank" rel="noopener" class="px-2.5 py-1 rounded-md bg-white border border-slate-200 text-slate-700 hover:text-emerald-800 hover:border-emerald-300 font-semibold text-2xs">LinkedIn</a>
                                        <a href="https://wa.me/6282197233174" target="_blank" rel="noopener" class="px-2.5 py-1 rounded-md bg-emerald-50 border border-emerald-300 text-emerald-900 font-bold text-2xs">Saluran WhatsApp</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tampilan Google Maps Kantor Pusat (Kanan) --}}
                <div class="lg:col-span-6 space-y-4">
                    <div class="card p-4 sm:p-5 bg-white border border-slate-200 shadow-sm space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                                Peta Lokasi Kantor Pusat (Google Maps)
                            </h3>
                            <a href="https://maps.google.com/?q=Makassar" target="_blank" rel="noopener" class="text-2xs font-bold text-emerald-800 hover:underline">
                                Buka di Google Maps &rarr;
                            </a>
                        </div>

                        {{-- Embed Google Maps Responsif --}}
                        <div class="w-full h-80 sm:h-96 rounded-2xl overflow-hidden border border-slate-200 shadow-inner relative bg-slate-100">
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
                            <div class="absolute bottom-3 left-3 bg-white/95 backdrop-blur-xs p-2.5 rounded-xl border border-slate-200 shadow text-3xs font-semibold text-slate-700">
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
    <section id="masuk-daftar" class="py-16 bg-slate-100/80 border-t border-slate-200" aria-labelledby="heading-cta">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Masukkan Akun (Login) --}}
                <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm flex flex-col justify-between space-y-6">
                    <div class="space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-800 flex items-center justify-center font-bold">
                            <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-slate-900">Sudah Menjadi Anggota?</h3>
                        <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                            Akses portal anggota koperasi untuk memeriksa mutasi simpanan, riwayat pembayaran cicilan, status pengajuan permodalan, dan notifikasi SHU.
                        </p>
                    </div>

                    <a href="{{ route('login') }}" class="btn btn-secondary w-full py-3.5 rounded-xl font-bold text-sm text-center shadow-xs hover:bg-slate-100 transition-all flex items-center justify-center gap-2">
                        <span>Masuk ke Akun Koperasi</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                </div>

                {{-- Daftar Anggota Baru (Register) --}}
                <div class="bg-gradient-to-br from-emerald-800 to-emerald-950 text-white rounded-3xl p-8 shadow-xl flex flex-col justify-between space-y-6 relative overflow-hidden">
                    <div class="space-y-3 relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-white/10 text-white flex items-center justify-center font-bold">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                        </div>
                        <h3 class="text-xl font-black text-white">Belum Terdaftar? Daftar Sekarang!</h3>
                        <p class="text-xs sm:text-sm text-emerald-100/80 leading-relaxed">
                            Bergabunglah bersama 1.450+ pelaku usaha dan keluarga sejahtera lainnya. Dapatkan fasilitas simpanan dengan bagi hasil berkah dan akses permodalan mudah.
                        </p>
                    </div>

                    <a href="{{ route('register') }}" class="w-full py-3.5 rounded-xl font-bold text-sm text-center bg-white text-emerald-900 hover:bg-emerald-50 transition-all shadow-md flex items-center justify-center gap-2 relative z-10">
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
    // Tab Billboard Pengumuman
    function switchBillboard(index) {
        for (let i = 0; i < 4; i++) {
            const slide = document.getElementById('bb-slide-' + i);
            const tab = document.getElementById('bb-tab-' + i);
            if (slide && tab) {
                if (i === index) {
                    slide.classList.add('active');
                    tab.classList.add('bg-white', 'text-emerald-900', 'shadow-2xs');
                    tab.classList.remove('text-slate-600');
                    tab.setAttribute('aria-selected', 'true');
                } else {
                    slide.classList.remove('active');
                    tab.classList.remove('bg-white', 'text-emerald-900', 'shadow-2xs');
                    tab.classList.add('text-slate-600');
                    tab.setAttribute('aria-selected', 'false');
                }
            }
        }
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

    // Set Parameter Simulasi dari Tombol Shortcut
    function setAmount(val) {
        const input = document.getElementById('sim_amount');
        if (input) {
            input.value = val;
        }
    }

    function setTenor(val) {
        const input = document.getElementById('sim_tenor');
        if (input) {
            input.value = val;
        }
    }

    // Tombol "Simulasikan" dari Katalog Produk
    function selectSimulationProduct(productId, amount, tenor, method) {
        const prodSelect = document.getElementById('sim_product_id');
        const amountInput = document.getElementById('sim_amount');
        const tenorInput = document.getElementById('sim_tenor');
        const methodSelect = document.getElementById('sim_method');
        const form = document.getElementById('landingSimForm');

        if (prodSelect) prodSelect.value = productId;
        if (amountInput) amountInput.value = amount;
        if (tenorInput) tenorInput.value = tenor;
        if (methodSelect && method) methodSelect.value = method;

        // Scroll halus ke kalkulator simulasi
        const simSection = document.getElementById('simulasi');
        if (simSection) {
            simSection.scrollIntoView({ behavior: 'smooth' });
        }

        if (form) {
            form.submit();
        }
    }
</script>
@endsection
