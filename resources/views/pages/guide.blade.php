@extends('layouts.app')

@section('title', 'Panduan Layanan & Tata Cara Anggota - KSP Sahabat Sejahtera')

@section('content')
<div class="space-y-12 py-2 sm:py-4">

    {{-- Hero Banner Panduan --}}
    <div class="bg-gradient-to-br from-[#0F4C81] via-[#1B263B] to-[#12304d] rounded-3xl p-6 sm:p-10 text-white shadow-xl relative overflow-hidden border border-white/10">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 rounded-full bg-[#2A9D8F]/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -top-12 w-64 h-64 rounded-full bg-[#D4AF37]/20 blur-3xl pointer-events-none"></div>

        <div class="relative z-10 max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-bold bg-white/10 backdrop-blur-md border border-white/20 text-[#D4AF37]">
                <svg class="w-4 h-4 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <span>Pusat Edukasi &amp; Bantuan Anggota</span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight leading-tight text-white">
                Panduan Lengkap Layanan Koperasi
            </h1>

            <p class="text-sm sm:text-base text-slate-200 leading-relaxed font-normal">
                Pelajari langkah-langkah mudah mulai dari pendaftaran e-KYC anggota, tata cara menabung, simulasi dan pengajuan pinjaman, akad digital, hingga pembayaran angsuran berkala secara online.
            </p>

            <div class="pt-2 flex flex-wrap items-center gap-3">
                <a href="#alur-pinjaman" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-[#D4AF37] hover:bg-[#C59F2D] text-[#1B263B] shadow-md transition-all">
                    Alur Pengajuan Pinjaman &darr;
                </a>
                <a href="#faq" class="px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm bg-white/10 hover:bg-white/20 text-white border border-white/20 backdrop-blur-md transition-all">
                    Tanya Jawab (FAQ) &darr;
                </a>
            </div>
        </div>
    </div>

    {{-- QUICK NAV JUMP LINKS --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
        <a href="#daftar-anggota" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-[#0F4C81] hover:shadow-xs transition-all group">
            <div class="w-10 h-10 mx-auto rounded-xl bg-blue-50 text-[#0F4C81] flex items-center justify-center font-bold mb-2 group-hover:scale-105 transition-transform">
                1
            </div>
            <span class="text-xs font-bold text-slate-800 block">Daftar Anggota &amp; e-KYC</span>
        </a>

        <a href="#panduan-simpanan" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-[#2A9D8F] hover:shadow-xs transition-all group">
            <div class="w-10 h-10 mx-auto rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold mb-2 group-hover:scale-105 transition-transform">
                2
            </div>
            <span class="text-xs font-bold text-slate-800 block">Menabung &amp; Setor Simpanan</span>
        </a>

        <a href="#alur-pinjaman" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-amber-500 hover:shadow-xs transition-all group">
            <div class="w-10 h-10 mx-auto rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center font-bold mb-2 group-hover:scale-105 transition-transform">
                3
            </div>
            <span class="text-xs font-bold text-slate-800 block">Pengajuan &amp; Pencairan</span>
        </a>

        <a href="#pembayaran-angsuran" class="p-4 rounded-2xl bg-white border border-slate-200 hover:border-purple-500 hover:shadow-xs transition-all group">
            <div class="w-10 h-10 mx-auto rounded-xl bg-purple-50 text-purple-800 flex items-center justify-center font-bold mb-2 group-hover:scale-105 transition-transform">
                4
            </div>
            <span class="text-xs font-bold text-slate-800 block">Bayar Angsuran &amp; Pelunasan</span>
        </a>
    </div>

    {{-- BAGIAN 1: CARA DAFTAR ANGGOTA & e-KYC --}}
    <section id="daftar-anggota" class="space-y-6 pt-4">
        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
            <div class="w-9 h-9 rounded-xl bg-[#0F4C81] text-white flex items-center justify-center font-bold shadow-xs">
                1
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Tata Cara Pendaftaran Anggota &amp; Verifikasi e-KYC</h2>
                <p class="text-xs text-slate-500">Proses pendaftaran 100% digital tanpa perlu datang antre di kantor cabang</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3 relative">
                <span class="text-3xl font-black text-slate-200 absolute top-4 right-4">01</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-[#0F4C81] flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Buat Akun Anggota</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Kunjungi menu <strong>Daftar Anggota Baru</strong>. Masukkan Nama Lengkap sesuai KTP, Nomor WhatsApp aktif, alamat email, dan buat kata sandi yang aman.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3 relative">
                <span class="text-3xl font-black text-slate-200 absolute top-4 right-4">02</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Unggah e-KTP &amp; Swafoto</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Ambil foto e-KTP asli yang jelas dan lakukan swafoto (*selfie*) memegang e-KTP. Pastikan NIK 16 digit terbaca terang tanpa pantulan cahaya.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3 relative">
                <span class="text-3xl font-black text-slate-200 absolute top-4 right-4">03</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-800 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-slate-900">Aktivasi &amp; Simpanan Pokok</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Setelah berkas diverifikasi petugas teller, lakukan penyetoran Simpanan Pokok (Rp 100.000 satu kali). Nomor Anggota resmi diterbitkan dan akun siap digunakan.
                </p>
            </div>
        </div>
    </section>

    {{-- BAGIAN 2: PANDUAN SIMPANAN & MENABUNG --}}
    <section id="panduan-simpanan" class="space-y-6 pt-4">
        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
            <div class="w-9 h-9 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-bold shadow-xs">
                2
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Panduan Menabung &amp; Pengelolaan Simpanan</h2>
                <p class="text-xs text-slate-500">Membangun kebiasaan menabung rutin dan mendapatkan bagi hasil SHU setiap tahun</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-blue-100 text-[#0F4C81]">Cara Menyetor Simpanan</span>
                </div>
                <ul class="space-y-3 text-xs text-slate-600 leading-relaxed">
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-blue-50 text-[#0F4C81] font-bold flex items-center justify-center shrink-0">1</span>
                        <span>Masuk ke menu <strong>Simpanan</strong> di dasbor akun anggota Anda.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-blue-50 text-[#0F4C81] font-bold flex items-center justify-center shrink-0">2</span>
                        <span>Pilih jenis rekening simpanan yang ingin disetor (Simpanan Wajib atau Simpanan Sukarela).</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-blue-50 text-[#0F4C81] font-bold flex items-center justify-center shrink-0">3</span>
                        <span>Pilih metode pembayaran instan: <strong>QRIS</strong> (semua e-wallet), <strong>Virtual Account Bank</strong>, atau setor tunai di Teller.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-blue-50 text-[#0F4C81] font-bold flex items-center justify-center shrink-0">4</span>
                        <span>Saldo simpanan langsung bertambah secara otomatis *real-time* dan tercatat di buku tabungan digital.</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-4">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-100 text-emerald-800">Cara Penarikan Simpanan</span>
                </div>
                <ul class="space-y-3 text-xs text-slate-600 leading-relaxed">
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center shrink-0">1</span>
                        <span>Buka rekening <strong>Simpanan Sukarela</strong> yang memiliki saldo tersedia.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center shrink-0">2</span>
                        <span>Klik tombol <strong>Tarik Simpanan</strong> dan masukkan nominal yang ingin ditarik.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center shrink-0">3</span>
                        <span>Pilih rekening bank tujuan pencairan milik anggota atau ambil tunai di loket teller koperasi.</span>
                    </li>
                    <li class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-emerald-50 text-emerald-700 font-bold flex items-center justify-center shrink-0">4</span>
                        <span>Simpanan Pokok &amp; Wajib bersifat simpanan permanen modal koperasi yang dapat dicairkan saat anggota berhenti secara tertib.</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- BAGIAN 3: ALUR PENGAJUAN & PENCAIRAN PINJAMAN --}}
    <section id="alur-pinjaman" class="space-y-6 pt-4">
        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
            <div class="w-9 h-9 rounded-xl bg-amber-600 text-white flex items-center justify-center font-bold shadow-xs">
                3
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Alur Pengajuan, Persetujuan &amp; Pencairan Pinjaman</h2>
                <p class="text-xs text-slate-500">6 langkah transparan berstandar tata kelola koperasi profesional</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Step 1 --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-8 h-8 rounded-lg bg-slate-100 text-[#0F4C81] font-extrabold flex items-center justify-center text-sm">1</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tahap Mandiri</span>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Simulasi &amp; Pilih Produk</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Gunakan kalkulator simulasi pinjaman untuk mengukur kemampuan bayar. Pilih produk (contoh: <strong>Pinjaman Mingguan Mikro</strong> mulai Rp 200.000 tenor 10 minggu).
                </p>
            </div>

            {{-- Step 2 --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-8 h-8 rounded-lg bg-slate-100 text-[#0F4C81] font-extrabold flex items-center justify-center text-sm">2</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Pengisian Form</span>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Kirim Formulir Pengajuan</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Isi tujuan pembiayaan, lengkapi data usaha jika modal usaha, dan ambil foto wajah memegang e-KTP (*selfie KYC*) langsung lewat kamera ponsel atau unggah file.
                </p>
            </div>

            {{-- Step 3 --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-8 h-8 rounded-lg bg-slate-100 text-[#0F4C81] font-extrabold flex items-center justify-center text-sm">3</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700">Pemeriksaan Berkas</span>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Verifikasi Fisik oleh Teller</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Petugas Teller memeriksa keabsahan identitas KTP, checklist keaktifan anggota, dan kelengkapan dokumen pengajuan dengan ramah dan santun.
                </p>
            </div>

            {{-- Step 4 --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-8 h-8 rounded-lg bg-slate-100 text-[#0F4C81] font-extrabold flex items-center justify-center text-sm">4</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-700">Analisis Kemampuan</span>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Review Analis Kredit</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Analis kredit koperasi menilai kelayakan dan rasio angsuran terhadap penghasilan anggota agar tidak membebani keuangan keluarga anggota.
                </p>
            </div>

            {{-- Step 5 --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-8 h-8 rounded-lg bg-slate-100 text-[#0F4C81] font-extrabold flex items-center justify-center text-sm">5</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Keputusan Resmi</span>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Persetujuan Manajer Koperasi</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Manajer memberikan putusan akhir (Setuju/Penyesuaian Plafon). Anggota menerima notifikasi dan melakukan <strong>Tanda Tangan Akad Digital</strong> secara mandiri.
                </p>
            </div>

            {{-- Step 6 --}}
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3">
                <div class="flex items-center justify-between">
                    <span class="w-8 h-8 rounded-lg bg-slate-100 text-emerald-700 font-extrabold flex items-center justify-center text-sm">6</span>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-700">Pencairan Dana</span>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Pencairan Dana Bersih</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Teller mencairkan dana bersih riil (contoh: plafon Rp 200.000 cair <strong>Rp 180.000</strong>; dipotong admin 5% Rp 10.000 dan simpanan wajib 5% Rp 10.000 yang otomatis masuk buku tabungan).
                </p>
            </div>
        </div>
    </section>

    {{-- BAGIAN 4: PEMBAYARAN ANGSURAN & PELUNASAN --}}
    <section id="pembayaran-angsuran" class="space-y-6 pt-4">
        <div class="flex items-center gap-3 pb-3 border-b border-slate-200">
            <div class="w-9 h-9 rounded-xl bg-purple-700 text-white flex items-center justify-center font-bold shadow-xs">
                4
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-900">Tata Cara Pembayaran Angsuran &amp; Pelunasan</h2>
                <p class="text-xs text-slate-500">Pilihan pembayaran mudah 24 jam dengan verifikasi otomatis</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900">QRIS Dinamis 24 Jam</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Pindai kode QRIS invoice dari aplikasi m-banking (BCA, Mandiri, BRI, BNI) atau e-wallet (GoPay, OVO, DANA, ShopeePay). Pembayaran terkonfirmasi otomatis dalam detik.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Virtual Account Bank</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Dapatkan nomor Virtual Account khusus pinjaman Anda. Transfer dapat dilakukan melalui ATM, Mobile Banking, maupun Internet Banking tanpa perlu konfirmasi manual.
                </p>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-xs space-y-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Pelunasan Dipercepat</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Anggota dapat melunasi sisa pinjaman lebih awal sewaktu-waktu. Bebas penalti pelunasan dipercepat dan sisa bunga periode yang belum berjalan otomatis dihapuskan.
                </p>
            </div>
        </div>
    </section>

    {{-- BAGIAN 5: TANYA JAWAB (FAQ) --}}
    <section id="faq" class="space-y-6 pt-4">
        <div class="text-center max-w-2xl mx-auto space-y-2">
            <h2 class="text-xl sm:text-2xl font-bold text-slate-900">Pertanyaan yang Sering Diajukan (FAQ)</h2>
            <p class="text-xs sm:text-sm text-slate-500">Jawaban cepat seputar keanggotaan, legalitas, keamanan data, dan tata kelola koperasi</p>
        </div>

        <div class="max-w-4xl mx-auto space-y-3">
            <details class="bg-white rounded-2xl border border-slate-200 p-5 group open:border-[#0F4C81]">
                <summary class="font-bold text-slate-800 text-sm cursor-pointer list-none flex items-center justify-between">
                    <span>Apakah KSP Sahabat Sejahtera terdaftar resmi dan berbadan hukum?</span>
                    <span class="text-slate-400 group-open:rotate-180 transition-transform">&darr;</span>
                </summary>
                <p class="text-xs text-slate-600 mt-3 leading-relaxed border-t border-slate-100 pt-3">
                    Ya. KSP Sahabat Sejahtera adalah koperasi simpan pinjam resmi berbadan hukum Kementerian Koperasi dan UKM Republik Indonesia dengan No. Pengesahan <strong>AHU-0012345.AH.01.26.TAHUN 2026</strong> dan Nomor Induk Koperasi (NIK) terdaftar di ODS Kemenkop UKM RI.
                </p>
            </details>

            <details class="bg-white rounded-2xl border border-slate-200 p-5 group open:border-[#0F4C81]">
                <summary class="font-bold text-slate-800 text-sm cursor-pointer list-none flex items-center justify-between">
                    <span>Apakah data kontak telepon atau privasi saya aman dari sadapan?</span>
                    <span class="text-slate-400 group-open:rotate-180 transition-transform">&darr;</span>
                </summary>
                <p class="text-xs text-slate-600 mt-3 leading-relaxed border-t border-slate-100 pt-3">
                    <strong>100% Aman.</strong> Koperasi kami <strong>TIDAK PERNAH</strong> meminta akses kontak ponsel, galeri, log panggilan, atau data privat anggota seperti yang sering dilakukan pinjol ilegal. Kami menjunjung tinggi etika kekeluargaan koperasi dan UU Perlindungan Data Pribadi (UU PDP).
                </p>
            </details>

            <details class="bg-white rounded-2xl border border-slate-200 p-5 group open:border-[#0F4C81]">
                <summary class="font-bold text-slate-800 text-sm cursor-pointer list-none flex items-center justify-between">
                    <span>Berapa besar potongan saat pinjaman dicairkan?</span>
                    <span class="text-slate-400 group-open:rotate-180 transition-transform">&darr;</span>
                </summary>
                <p class="text-xs text-slate-600 mt-3 leading-relaxed border-t border-slate-100 pt-3">
                    Pada produk Pinjaman Mingguan Mikro, dipotong Administrasi 5% (Rp 10.000 untuk pinjaman Rp 200.000) dan Simpanan Wajib 5% (Rp 10.000). Dana bersih yang diterima anggota adalah Rp 180.000. <strong>Perlu diingat:</strong> Potongan simpanan 5% bukan uang hilang, melainkan aset tabungan resmi anggota yang tercatat di rekening simpanan wajib Anda.
                </p>
            </details>

            <details class="bg-white rounded-2xl border border-slate-200 p-5 group open:border-[#0F4C81]">
                <summary class="font-bold text-slate-800 text-sm cursor-pointer list-none flex items-center justify-between">
                    <span>Bagaimana jika saya mengalami musibah atau keterlambatan usaha?</span>
                    <span class="text-slate-400 group-open:rotate-180 transition-transform">&darr;</span>
                </summary>
                <p class="text-xs text-slate-600 mt-3 leading-relaxed border-t border-slate-100 pt-3">
                    Koperasi mengedepankan musyawarah kekeluargaan. Anggota dapat mengajukan <strong>Permohonan Keringanan / Restrukturisasi</strong> secara resmi melalui sistem untuk perpanjangan tenor atau penghapusan denda keterlambatan dengan verifikasi tim analis dan manajer.
                </p>
            </details>
        </div>
    </section>

    {{-- CALL TO ACTION (CTA) --}}
    <div class="bg-gradient-to-r from-emerald-800 to-[#0F4C81] rounded-3xl p-8 sm:p-10 text-white flex flex-col md:flex-row items-center justify-between gap-6 shadow-lg">
        <div class="space-y-2 text-center md:text-left">
            <h3 class="text-xl sm:text-2xl font-extrabold text-white">Butuh Bantuan atau Konsultasi Keuangan?</h3>
            <p class="text-xs sm:text-sm text-emerald-100 max-w-xl">
                Tim layanan anggota kami siap melayani dan mendampingi kebutuhan permodalan Anda dengan ramah dan profesional.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 shrink-0">
            <a href="https://wa.me/6282197233174" target="_blank" rel="noopener noreferrer" class="px-5 py-3 rounded-xl bg-white text-emerald-800 hover:bg-slate-100 text-xs sm:text-sm font-bold shadow-md transition-all flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                <span>WhatsApp CS 24 Jam</span>
            </a>
            @guest
            <a href="{{ route('register') }}" class="px-5 py-3 rounded-xl bg-[#D4AF37] hover:bg-[#C59F2D] text-[#1B263B] text-xs sm:text-sm font-bold shadow-md transition-all">
                Daftar Anggota Baru &rarr;
            </a>
            @endguest
        </div>
    </div>

</div>
@endsection

