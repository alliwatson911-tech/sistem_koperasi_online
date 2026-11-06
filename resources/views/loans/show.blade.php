@extends('layouts.app')

@section('title', 'Detail Pinjaman - ' . $loan->loan_no)

@section('content')
<div class="space-y-6">
    <!-- Tombol Kembali & Header Status -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            @php
                $backUrl = auth()->user()->isMember() ? route('loans.my') : route('loans.manage');
                if (url()->previous() && url()->previous() !== url()->current() && !str_contains(url()->previous(), '/pinjaman/' . $loan->id)) {
                    $backUrl = url()->previous();
                }
            @endphp
            <a href="{{ $backUrl }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 border border-slate-200 text-xs font-bold transition-all shadow-2xs mb-3" title="Kembali ke halaman sebelumnya">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali ke Halaman Sebelumnya</span>
            </a>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Kontrak Pinjaman: {{ $loan->loan_no }}</h1>
                @php
                    $statusStyles = match($loan->status) {
                        'active' => 'bg-emerald-100 text-emerald-800 border-emerald-300 ring-2 ring-emerald-100',
                        'overdue' => 'bg-rose-100 text-rose-800 border-rose-300 ring-2 ring-rose-100',
                        'paid_off' => 'bg-slate-100 text-slate-800 border-slate-300',
                        'approved_awaiting_disbursement' => 'bg-blue-100 text-blue-800 border-blue-300 ring-2 ring-blue-100',
                        default => 'bg-slate-100 text-slate-800 border-slate-300',
                    };

                    $statusLabel = match($loan->status) {
                        'active' => 'Pinjaman Aktif',
                        'overdue' => 'Menunggak (Overdue)',
                        'paid_off' => 'LUNAS (Paid Off)',
                        'approved_awaiting_disbursement' => 'Menunggu Akad & Pencairan',
                        default => strtoupper($loan->status),
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $statusStyles }}">
                    {{ $statusLabel }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Peminjam: <strong class="text-slate-700">{{ $loan->member->user->name }}</strong> (No. Anggota: {{ $loan->member->member_no }}) &bull; Produk: {{ $loan->loanProduct->name }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            @if(in_array($loan->status, ['active', 'overdue']))
                <button type="button" onclick="document.getElementById('modal-early-repay').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3 py-2.5 rounded-xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-bold border border-indigo-200 shadow-xs transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Pelunasan Dipercepat
                </button>
                <a href="{{ route('loans.pay', $loan->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Bayar Angsuran
                </a>
            @endif

            @if($loan->status === 'approved_awaiting_disbursement' && $loan->agreed_at && in_array(auth()->user()->role, ['teller', 'admin']))
                <form action="{{ route('loans.disburse', $loan->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="payment_method" value="transfer">
                    <button type="submit" onclick="return confirm('Cairkan dana pinjaman sebesar Rp {{ number_format($loan->principal_amount - $loan->total_admin_fee) }} ke anggota sekarang?')" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Cairkan Dana Pinjaman
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Banner Akad Digital --}}
    @if($loan->status === 'approved_awaiting_disbursement' && !$loan->agreed_at && auth()->user()->isMember())
    <div class="rounded-2xl p-5 bg-gradient-to-r from-blue-50 to-emerald-50 border border-blue-200 shadow-sm">
        <div class="flex items-start gap-3.5">
            <div class="p-2.5 rounded-xl bg-blue-600 text-white shadow-sm mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
            </div>
            <div class="space-y-2 flex-1">
                <h3 class="text-sm font-bold text-slate-900">Persetujuan Akad Perjanjian Pinjaman Digital</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Pengajuan pinjaman Anda telah <strong>DISETUJUI</strong> oleh Manajer Koperasi. Silakan telaah ketentuan akad pinjaman di bawah ini lalu tanda tangani secara digital agar Teller dapat langsung mencairkan dana ke rekening Anda.
                </p>
                <form action="{{ route('loans.sign_akad', $loan->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold shadow transition" onclick="return confirm('Apakah Anda menyetujui seluruh ketentuan akad pinjaman ini?')">
                        Saya Setuju & Tanda Tangani Akad Pinjaman Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- Banner Peringatan Keterlambatan / Jatuh Tempo (Overdue / Due Soon Alert) --}}
    @php
        $maxOverdueDays = $loan->getMaxDaysOverdue();
        $isOverdue = ($loan->status === 'overdue' || $maxOverdueDays > 0);
    @endphp
    @if($isOverdue)
    <div class="rounded-2xl p-5 bg-gradient-to-r from-red-50 to-rose-50 border border-rose-300 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-start gap-3">
                <div class="p-2.5 rounded-xl bg-red-600 text-white shadow-sm shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <h2 class="text-sm font-bold text-red-950">Peringatan: Pinjaman Ini Sedang Menunggak (Overdue)</h2>
                        <span class="badge {{ $loan->collectibility_badge_class }} text-[10px]">
                            {{ $loan->collectibility_label }}
                        </span>
                    </div>
                    <p class="text-xs text-red-800 leading-relaxed">
                        Terdapat keterlambatan pembayaran hingga <strong>{{ $maxOverdueDays }} hari</strong> dengan akumulasi denda berjalan sebesar <strong>Rp {{ number_format($loan->outstanding_penalty, 0, ',', '.') }}</strong>. Tahapan penagihan: <strong>{{ strtoupper($loan->collection_stage ?? 'sp1') }}</strong>.
                    </p>
                    @if($loan->promised_payment_date)
                    <div class="text-[11px] font-semibold text-amber-900 bg-amber-100/80 px-2.5 py-1 rounded-md inline-block mt-1">
                        Komitmen Janji Bayar Tercatat: {{ \Carbon\Carbon::parse($loan->promised_payment_date)->format('d/m/Y') }} @if($loan->promised_payment_amount) &bull; Est: Rp {{ number_format($loan->promised_payment_amount, 0, ',', '.') }} @endif
                    </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if(auth()->user()->isMember())
                    <button type="button" onclick="document.getElementById('modal-request-restructuring').classList.remove('hidden')" class="btn btn-secondary btn-sm text-xs border border-rose-300 text-rose-900 bg-white hover:bg-rose-50">
                        Ajukan Keringanan Denda
                    </button>
                    <a href="{{ route('loans.pay', $loan->id) }}" class="btn btn-primary btn-sm text-xs bg-red-600 hover:bg-red-700">
                        Bayar Angsuran Sekarang
                    </a>
                @else
                    <button type="button" onclick="document.getElementById('modal-record-collection').classList.remove('hidden')" class="btn btn-primary btn-sm text-xs bg-red-700 hover:bg-red-800">
                        Catat Aksi Penagihan (SP/Kunjungan)
                    </button>
                    <button type="button" onclick="document.getElementById('modal-request-restructuring').classList.remove('hidden')" class="btn btn-secondary btn-sm text-xs border border-rose-300">
                        Rekomendasi Keringanan
                    </button>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- KARTU PROFIL & FOTO TEMPAT USAHA (MODAL USAHA / UMKM) --}}
    @if($loan->isBusinessLoan() || $loan->business_photo_path)
    <div class="bg-white rounded-2xl shadow-sm border border-emerald-200 p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 text-white flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Data & Foto Tempat Usaha Anggota (Modal Usaha)</h2>
                    <p class="text-xs text-slate-500">Dokumentasi fisik tempat usaha anggota untuk transparansi dan pemantauan perputaran usaha</p>
                </div>
            </div>
            <span class="badge badge-success text-[10px]">Pembiayaan Produktif</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="space-y-2 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <div>
                    <span class="text-slate-400 block">Nama Usaha / Toko:</span>
                    <strong class="text-slate-800 text-sm">{{ $loan->business_name ?? 'Usaha Anggota' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block">Jenis / Bidang Usaha:</span>
                    <span class="text-slate-700 font-medium">{{ $loan->business_type ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Lama Berjalan & Omset Rata-rata:</span>
                    <span class="text-slate-700 font-semibold">{{ $loan->business_duration_years ?? 1 }} Tahun &bull; Rp {{ number_format($loan->business_monthly_revenue ?? 0, 0, ',', '.') }}/bln</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Alamat Tempat Usaha:</span>
                    <p class="text-slate-700 leading-relaxed">{{ $loan->business_address ?? '-' }}</p>
                </div>
            </div>

            <div class="space-y-2 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-slate-400 block font-semibold">Foto Tempat Usaha:</span>
                @if($loan->business_photo_path)
                    <a href="{{ asset('storage/' . $loan->business_photo_path) }}" target="_blank" class="block group relative rounded-xl overflow-hidden border border-slate-300 shadow-xs max-w-xs">
                        <img src="{{ asset('storage/' . $loan->business_photo_path) }}" alt="Foto Tempat Usaha" class="h-32 w-full object-cover group-hover:scale-105 transition duration-200">
                        <span class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition">
                            Lihat Foto Beresolusi Penuh
                        </span>
                    </a>
                @else
                    <div class="h-28 rounded-xl bg-white border border-dashed border-slate-300 flex items-center justify-center text-slate-400 text-xs italic">
                        Foto usaha tidak tersedia
                    </div>
                @endif

                @if($loan->survey_photo_path)
                <div class="pt-2 border-t border-slate-200">
                    <span class="text-slate-400 block font-semibold">Foto Verifikasi Lapangan Analis:</span>
                    <a href="{{ asset('storage/' . $loan->survey_photo_path) }}" target="_blank" class="block group relative rounded-xl overflow-hidden border border-emerald-300 shadow-xs max-w-xs mt-1">
                        <img src="{{ asset('storage/' . $loan->survey_photo_path) }}" alt="Foto Survei Lapangan" class="h-28 w-full object-cover group-hover:scale-105 transition">
                        <span class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition">
                            Lihat Foto Survei Lapangan
                        </span>
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    {{-- KARTU VERIFIKASI IDENTITAS FOTO WAJAH DENGAN KTP (SELFIE e-KTP) --}}
    @if($loan->ktp_selfie_path)
    <div class="bg-white rounded-2xl shadow-sm border border-blue-200 p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Verifikasi Foto Wajah & KTP (Selfie e-KTP)</h2>
                    <p class="text-xs text-slate-500">Dokumentasi otentikasi KYC identitas peminjam</p>
                </div>
            </div>
            <span class="badge badge-info text-[10px]">Identitas Terverifikasi</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-xs">
            <div class="space-y-2 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <div>
                    <span class="text-slate-400 block">Nama Anggota:</span>
                    <strong class="text-slate-800 text-sm">{{ $loan->member->user->name ?? '-' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block">Nomor KTP (NIK):</span>
                    <span class="text-slate-700 font-mono font-semibold">{{ $loan->member->nik ?? $loan->member->user->nik ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block">Nomor Akad Pinjaman:</span>
                    <span class="text-slate-700 font-mono font-bold">{{ $loan->loan_no }}</span>
                </div>
            </div>

            <div class="space-y-2 p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-slate-400 block font-semibold">Foto Selfie Memegang KTP:</span>
                <a href="{{ asset('storage/' . $loan->ktp_selfie_path) }}" target="_blank" class="block group relative rounded-xl overflow-hidden border border-blue-300 shadow-xs max-w-xs">
                    <img src="{{ asset('storage/' . $loan->ktp_selfie_path) }}" alt="Foto Selfie Memegang KTP" class="h-36 w-full object-cover group-hover:scale-105 transition duration-200">
                    <span class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition">
                        Lihat Foto Beresolusi Penuh
                    </span>
                </a>
            </div>
        </div>
    </div>
    @endif

    <!-- Ringkasan Statistik Finansial Pinjaman -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-medium text-slate-400 block mb-1">Plafon Pokok Awal</span>
            <div class="text-2xl font-black text-slate-800">Rp {{ number_format($loan->principal_amount, 0, ',', '.') }}</div>
            <div class="text-xs font-semibold text-slate-500 mt-1">Tenor: {{ $loan->tenor_display }} &bull; {{ ucfirst($loan->calculation_method) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-medium text-slate-400 block mb-1">Sisa Pokok Beredar</span>
            <div class="text-2xl font-black text-emerald-700">Rp {{ number_format($loan->outstanding_principal, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-1">Telah dibayar: Rp {{ number_format($loan->principal_amount - $loan->outstanding_principal, 0, ',', '.') }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-medium text-slate-400 block mb-1">Sisa Bunga Pinjaman</span>
            <div class="text-2xl font-black text-slate-800">Rp {{ number_format($loan->outstanding_interest, 0, ',', '.') }}</div>
            <div class="text-xs text-slate-500 mt-1">Bunga: {{ $loan->interest_rate_per_period }}% per {{ strtolower($loan->tenor_unit_label) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-sm">
            <span class="text-xs font-medium text-slate-400 block mb-1">Denda Keterlambatan</span>
            <div class="text-2xl font-black {{ $loan->outstanding_penalty > 0 ? 'text-rose-600' : 'text-slate-800' }}">
                Rp {{ number_format($loan->outstanding_penalty, 0, ',', '.') }}
            </div>
            <div class="text-xs {{ $loan->outstanding_penalty > 0 ? 'text-rose-600 font-bold' : 'text-slate-400' }} mt-1">
                {{ $loan->outstanding_penalty > 0 ? 'Menunggak - Segera lunasi' : 'Tidak ada denda menunggak' }}
            </div>
        </div>
    </div>

    <!-- Dua Kolom: Keterangan Teller & Lini Masa Timeline -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- KETERANGAN TELLER PENERIMA PENGAJUAN (TASK 3) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-slate-800">Keterangan Penerimaan Teller</h2>
                        <p class="text-xs text-slate-400">Petugas kasir/teller yang menerima dan memverifikasi berkas</p>
                    </div>
                </div>
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-300">
                    Lengkap & Absah
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block mb-1">Nama & Kode Teller Penerima:</span>
                    @if($loan->received_by)
                        <div class="font-bold text-slate-800 text-sm">{{ $loan->received_by->name }}</div>
                        <span class="inline-block mt-0.5 px-2 py-0.5 rounded bg-teal-100 text-teal-800 font-mono font-bold text-[11px]">
                            {{ $loan->received_by->employee_code_formatted }}
                        </span>
                    @else
                        <div class="text-slate-500 italic">Terverifikasi oleh Tim Teller Koperasi</div>
                    @endif
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                    <span class="text-slate-400 block mb-1">Waktu Penerimaan Berkas:</span>
                    <div class="font-bold text-slate-800 text-sm">
                        {{ $loan->received_at ? $loan->received_at->format('d/m/Y H:i') . ' WITA' : ($loan->created_at->format('d/m/Y H:i') . ' WITA') }}
                    </div>
                    <span class="text-teal-700 font-semibold text-[11px]">Memenuhi Standar SOP Koperasi</span>
                </div>
            </div>

            <!-- Ringkasan Checklist Dokumen -->
            <div>
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Checklist Dokumen Terverifikasi:</h3>
                <div class="space-y-1.5 text-xs">
                    @php
                        $checklist = $loan->reception_checklist ?? [];
                    @endphp
                    @if(!empty($checklist))
                        @foreach($checklist as $k => $checked)
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-teal-50/50 border border-teal-100 text-teal-900 font-medium">
                            <span class="w-4 h-4 rounded-full bg-teal-600 text-white flex items-center justify-center text-[10px]">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            </span>
                            <span>{{ ucwords(str_replace('_', ' ', $k)) }} &bull; <strong>Absah</strong></span>
                        </div>
                        @endforeach
                    @else
                        <div class="flex items-center gap-2 p-2 rounded-lg bg-teal-50/50 border border-teal-100 text-teal-900 font-medium">
                            <span class="w-4 h-4 rounded-full bg-teal-600 text-white flex items-center justify-center text-[10px]">✓</span>
                            <span>KTP, Formulir Pengajuan, Bukti Penghasilan, dan Status Keanggotaan Terverifikasi Lengkap</span>
                        </div>
                    @endif
                </div>
            </div>

            @if($loan->reception_note)
            <div class="p-3.5 rounded-xl bg-teal-50 border border-teal-200 text-xs">
                <span class="font-bold text-teal-900 block mb-0.5">Catatan Penerimaan:</span>
                <p class="text-teal-800 italic">"{{ $loan->reception_note }}"</p>
            </div>
            @endif

            <!-- Detail Akad & Pencairan -->
            <div class="pt-3 border-t border-slate-100 grid grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-slate-400 block">Tanda Tangan Akad Digital:</span>
                    <strong class="text-slate-800">{{ $loan->agreed_at ? $loan->agreed_at->format('d/m/Y H:i') . ' WITA' : 'Belum' }}</strong>
                </div>
                <div>
                    <span class="text-slate-400 block">Dicairkan Oleh:</span>
                    <strong class="text-slate-800">
                        {{ $loan->disburser ? "{$loan->disburser->name} ({$loan->disburser->employee_code_formatted})" : 'Petugas Kasir' }}
                    </strong>
                </div>
            </div>
        </div>

        <!-- LINI MASA PENANGANAN PINJAMAN (TASK 4 - TIMELINE LENGKAP) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
            <div class="flex items-center gap-2.5 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-slate-800 text-white flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Lini Masa Penanganan (Timeline)</h2>
                    <p class="text-xs text-slate-400">Jejak audit perjalanan pengajuan hingga pencairan pinjaman</p>
                </div>
            </div>

            <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                @forelse($timeline as $event)
                @php
                    $dotColor = match($event['status'] ?? 'completed') {
                        'completed' => 'bg-emerald-600 ring-4 ring-emerald-100',
                        'warning' => 'bg-amber-500 ring-4 ring-amber-100',
                        'danger' => 'bg-rose-500 ring-4 ring-rose-100',
                        default => 'bg-slate-300 ring-4 ring-slate-100',
                    };
                @endphp
                <div class="relative group">
                    <span class="absolute -left-[1.8rem] top-1 w-3.5 h-3.5 rounded-full {{ $dotColor }} transition"></span>

                    <div class="space-y-1 text-xs">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-slate-800">{{ $event['title'] }}</span>
                            <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                {{ $event['badge'] }}
                            </span>
                        </div>

                        <p class="text-slate-500 text-[11px]">{{ $event['subtitle'] }}</p>

                        <div class="text-slate-600 font-medium">
                            Oleh: <strong class="text-slate-800">{{ $event['actor_name'] }}</strong>
                            @if(!empty($event['actor_role']))
                                <span class="text-[10px] font-mono text-slate-400">({{ $event['actor_role'] }})</span>
                            @endif
                            @if(!empty($event['timestamp']))
                                <span class="text-slate-400">&bull; {{ \Carbon\Carbon::parse($event['timestamp'])->format('d/m/Y H:i') }} WITA</span>
                            @endif
                        </div>

                        @if(!empty($event['notes']))
                        <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 italic text-slate-700 mt-1">
                            "{{ $event['notes'] }}"
                        </div>
                        @endif

                        @if(!empty($event['details']) && is_array($event['details']))
                        <div class="flex flex-wrap gap-1.5 mt-1">
                            @foreach($event['details'] as $k => $v)
                            @if(is_scalar($v))
                            <span class="inline-block px-2 py-0.5 rounded bg-slate-100 text-[10px] text-slate-600 border border-slate-200">
                                <strong>{{ $k }}:</strong> {{ $v }}
                            </span>
                            @endif
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 italic">Belum ada jejak audit penanganan.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Jadwal Angsuran & Status Pembayaran -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-800">Jadwal Angsuran & Status Pembayaran</h2>
                <p class="text-xs text-slate-400">Kewajiban angsuran per {{ strtolower($loan->tenor_unit_label) }}, porsi pokok, bunga, dan denda</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-3">Periode</th>
                        <th class="py-3 px-3">Jatuh Tempo</th>
                        <th class="py-3 px-3">Pokok</th>
                        <th class="py-3 px-3">Bunga</th>
                        <th class="py-3 px-3">Total Tagihan</th>
                        <th class="py-3 px-3">Dibayar</th>
                        <th class="py-3 px-3">Denda</th>
                        <th class="py-3 px-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($loan->schedules as $s)
                    <tr class="{{ $s->status === 'overdue' ? 'bg-rose-50/50' : 'hover:bg-slate-50/75' }} transition">
                        <td class="py-3 px-3 font-bold text-slate-800">{{ ($loan->isWeekly() ? 'Minggu' : 'Bulan') . ' ' . $s->installment_no }}</td>
                        <td class="py-3 px-3 font-mono">{{ $s->due_date->format('d/m/Y') }}</td>
                        <td class="py-3 px-3 font-mono">Rp {{ number_format($s->principal_amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 font-mono">Rp {{ number_format($s->interest_amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 font-mono font-bold text-slate-800">Rp {{ number_format($s->total_amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 font-mono">
                            Rp {{ number_format($s->paid_principal + $s->paid_interest, 0, ',', '.') }}
                            @if($s->paid_at)
                                <span class="block text-[10px] text-slate-400">{{ $s->paid_at->format('d/m/Y') }}</span>
                            @endif
                        </td>
                        <td class="py-3 px-3 font-mono">
                            @if($s->penalty_amount > 0)
                                <span class="text-rose-600 font-bold">Rp {{ number_format($s->penalty_amount, 0, ',', '.') }}</span>
                                <span class="block text-[10px] text-rose-500">({{ $s->days_overdue }} hari terlambat)</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-center">
                            @php
                                $schedBadge = match($s->status) {
                                    'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                    'partial' => 'bg-amber-100 text-amber-800 border-amber-300',
                                    'overdue' => 'bg-rose-100 text-rose-800 border-rose-300',
                                    default => 'bg-slate-100 text-slate-700 border-slate-300',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $schedBadge }}">
                                {{ strtoupper($s->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-8 text-center text-slate-400">
                            Jadwal angsuran akan terbentuk otomatis setelah dana dicairkan oleh Teller.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Riwayat Pembayaran Angsuran -->
    @if($loan->payments->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
        <h2 class="text-base font-bold text-slate-800 pb-2 border-b border-slate-100">Riwayat Pembayaran Angsuran</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-3">No. Kuitansi</th>
                        <th class="py-3 px-3">Tanggal Bayar</th>
                        <th class="py-3 px-3">Porsi Pokok</th>
                        <th class="py-3 px-3">Porsi Bunga</th>
                        <th class="py-3 px-3">Porsi Denda</th>
                        <th class="py-3 px-3">Total Pembayaran</th>
                        <th class="py-3 px-3">Metode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($loan->payments as $p)
                    <tr class="hover:bg-slate-50/75 transition">
                        <td class="py-3 px-3 font-mono font-bold text-slate-800">{{ $p->payment_no }}</td>
                        <td class="py-3 px-3">{{ $p->payment_date->format('d/m/Y') }}</td>
                        <td class="py-3 px-3 font-mono">Rp {{ number_format($p->principal_portion, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 font-mono">Rp {{ number_format($p->interest_portion, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 font-mono">Rp {{ number_format($p->penalty_portion, 0, ',', '.') }}</td>
                        <td class="py-3 px-3 font-mono font-black text-emerald-700">Rp {{ number_format($p->amount, 0, ',', '.') }}</td>
                        <td class="py-3 px-3"><span class="capitalize">{{ $p->payment_method }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- RIWAYAT PENAGIHAN & PEMBINAAN ANGGOTA (COLLECTION LOGS) --}}
    @if(!auth()->user()->isMember() || $loan->collectionLogs->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-rose-600 text-white flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Riwayat Penagihan & Pendampingan Debitur</h2>
                    <p class="text-xs text-slate-500">Jejak komunikasi, surat peringatan (SP), komitmen janji bayar, dan kunjungan lapangan</p>
                </div>
            </div>

            @if(!auth()->user()->isMember())
            <button type="button" onclick="document.getElementById('modal-record-collection').classList.remove('hidden')" class="btn btn-primary btn-sm text-xs self-start sm:self-auto">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Catat Aktivitas Penagihan
            </button>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-3">Waktu & Petugas</th>
                        <th class="py-3 px-3">Tindakan</th>
                        <th class="py-3 px-3">Hasil / Respon</th>
                        <th class="py-3 px-3">Komitmen Janji Bayar</th>
                        <th class="py-3 px-3">Catatan / Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($loan->collectionLogs as $log)
                    <tr class="hover:bg-slate-50/75 transition">
                        <td class="py-3 px-3">
                            <div class="font-bold text-slate-800">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                            <div class="text-[11px] text-slate-500">{{ $log->collector->name ?? 'Petugas' }} ({{ ucfirst($log->collector->role ?? 'staf') }})</div>
                        </td>
                        <td class="py-3 px-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-800 border border-slate-300">
                                {{ $log->action_type_label }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            @php
                                $resBadge = match($log->result_status) {
                                    'promised', 'paid' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                    'connected' => 'bg-blue-100 text-blue-800 border-blue-300',
                                    'refusal', 'uncontactable' => 'bg-rose-100 text-rose-800 border-rose-300',
                                    default => 'bg-slate-100 text-slate-700 border-slate-300',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border {{ $resBadge }}">
                                {{ $log->result_status_label }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            @if($log->promised_date)
                                <div class="font-bold text-amber-900">{{ \Carbon\Carbon::parse($log->promised_date)->format('d/m/Y') }}</div>
                                @if($log->promised_amount)
                                    <div class="font-mono text-[11px] text-slate-600">Rp {{ number_format($log->promised_amount, 0, ',', '.') }}</div>
                                @endif
                            @else
                                <span class="text-slate-400 italic">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            <p class="text-slate-700 leading-relaxed">{{ $log->notes ?: '-' }}</p>
                            @if($log->proof_photo_path)
                                <a href="{{ asset('storage/' . $log->proof_photo_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-emerald-700 hover:underline font-semibold mt-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Lihat Foto Bukti
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-6 text-center text-slate-400 italic">
                            Belum ada catatan aktivitas penagihan untuk rekening pinjaman ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- RESTRUKTURISASI & PERMOHONAN KERINGANAN (RESTRUCTURING REQUESTS) --}}
    @if(!auth()->user()->isMember() || $loan->restructuringRequests->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-2">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg bg-amber-600 text-white flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800">Permohonan Keringanan & Restrukturisasi Pinjaman</h2>
                    <p class="text-xs text-slate-500">Pengajuan pembebasan denda, perpanjangan tenor, atau restrukturisasi pola angsuran</p>
                </div>
            </div>

            <button type="button" onclick="document.getElementById('modal-request-restructuring').classList.remove('hidden')" class="btn btn-secondary btn-sm text-xs self-start sm:self-auto border border-amber-300 text-amber-900 bg-amber-50/50 hover:bg-amber-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Ajukan Permohonan Keringanan
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3 px-3">Tanggal & Pemohon</th>
                        <th class="py-3 px-3">Jenis Permohonan</th>
                        <th class="py-3 px-3">Keringanan Diminta</th>
                        <th class="py-3 px-3">Alasan Debitur</th>
                        <th class="py-3 px-3">Status Putusan</th>
                        <th class="py-3 px-3">Catatan Putusan</th>
                        @if(in_array(auth()->user()->role, ['manager', 'admin']))
                        <th class="py-3 px-3 text-right">Aksi Manajer</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($loan->restructuringRequests as $req)
                    <tr class="hover:bg-slate-50/75 transition">
                        <td class="py-3 px-3">
                            <div class="font-bold text-slate-800">{{ $req->created_at->format('d/m/Y H:i') }}</div>
                            <div class="text-[11px] text-slate-500">{{ $req->requester->name ?? 'Pemohon' }}</div>
                        </td>
                        <td class="py-3 px-3">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-50 text-amber-900 border border-amber-200">
                                {{ $req->type_label }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            @if($req->requested_waiver_amount > 0)
                                <div class="font-bold font-mono text-slate-900">Rp {{ number_format($req->requested_waiver_amount, 0, ',', '.') }}</div>
                                @if($req->status === 'approved')
                                    <div class="text-[11px] text-emerald-700 font-bold font-mono">Disetujui: Rp {{ number_format($req->approved_waiver_amount, 0, ',', '.') }}</div>
                                @endif
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            <p class="text-slate-700 max-w-xs">{{ $req->reason }}</p>
                        </td>
                        <td class="py-3 px-3">
                            <span class="badge {{ $req->status_badge_class }}">
                                {{ $req->status_label }}
                            </span>
                        </td>
                        <td class="py-3 px-3">
                            @if($req->decider)
                                <div class="text-[11px] font-semibold text-slate-700">Oleh: {{ $req->decider->name }}</div>
                                <div class="text-[11px] text-slate-500">{{ $req->decided_at?->format('d/m/Y H:i') }}</div>
                                @if($req->manager_notes)
                                    <p class="text-[11px] text-slate-600 italic mt-0.5">"{{ $req->manager_notes }}"</p>
                                @endif
                            @else
                                <span class="text-slate-400 italic">Menunggu review</span>
                            @endif
                        </td>
                        @if(in_array(auth()->user()->role, ['manager', 'admin']))
                        <td class="py-3 px-3 text-right">
                            @if($req->status === 'pending')
                                <button type="button" onclick="openDecideModalSingle('{{ $req->id }}', '{{ $loan->loan_no }}', '{{ $req->requested_waiver_amount }}', '{{ addslashes($req->reason) }}')" class="btn btn-primary btn-sm text-xs bg-amber-600 hover:bg-amber-700">
                                    Putuskan
                                </button>
                            @else
                                <span class="text-slate-400 text-xs">Selesai</span>
                            @endif
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ in_array(auth()->user()->role, ['manager', 'admin']) ? 7 : 6 }}" class="py-6 text-center text-slate-400 italic">
                            Belum ada permohonan keringanan / restrukturisasi untuk pinjaman ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Modal Pelunasan Dipercepat --}}
    @if(in_array($loan->status, ['active', 'overdue']))
    <div id="modal-early-repay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="earlyRepayModalTitle">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-xl bg-indigo-50 text-indigo-700 font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <h3 id="earlyRepayModalTitle" class="text-base font-extrabold text-slate-900">Pelunasan Dipercepat</h3>
                        <p class="text-2xs text-slate-400">Pembebasan beban bunga masa depan</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modal-early-repay').classList.add('hidden')" aria-label="Tutup modal" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <div class="space-y-2 p-3.5 rounded-2xl bg-indigo-50/60 border border-indigo-100 text-xs">
                <div class="flex justify-between text-slate-600">
                    <span>Sisa Pokok Pinjaman:</span>
                    <strong class="font-mono text-slate-900">Rp {{ number_format($loan->outstanding_principal, 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Denda Keterlambatan:</span>
                    <strong class="font-mono {{ $loan->outstanding_penalty > 0 ? 'text-rose-600' : 'text-slate-900' }}">Rp {{ number_format($loan->outstanding_penalty, 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between text-emerald-700 font-medium">
                    <span>Pembebasan Bunga Masa Depan:</span>
                    <span>- Rp {{ number_format($loan->outstanding_interest, 0, ',', '.') }}</span>
                </div>
                <div class="pt-2 border-t border-indigo-200/60 flex justify-between font-extrabold text-sm text-slate-900">
                    <span>Total Bayar Pelunasan:</span>
                    <span class="text-indigo-700 font-mono">Rp {{ number_format($loan->outstanding_principal + $loan->outstanding_penalty, 0, ',', '.') }}</span>
                </div>
            </div>

            <form action="{{ route('loans.early_repay', $loan->id) }}" method="POST" class="space-y-3">
                @csrf
                <div class="form-group">
                    <label for="early_repay_method" class="form-label text-xs">Metode Pembayaran <span class="text-rose-500">*</span></label>
                    <select name="payment_method" id="early_repay_method" class="form-control text-xs" required>
                        <option value="cash">Kas Tunai Teller</option>
                        <option value="transfer">Transfer Bank Koperasi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="early_repay_notes" class="form-label text-xs">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" id="early_repay_notes" class="form-control text-xs" placeholder="Contoh: Pelunasan dipercepat dari hasil panen/usaha">
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-early-repay').classList.add('hidden')" class="btn btn-secondary w-1/3 py-2 text-xs">Batal</button>
                    <button type="submit" class="btn btn-primary w-2/3 py-2 text-xs font-bold" onclick="return confirm('Konfirmasi pelunasan dipercepat sebesar Rp {{ number_format($loan->outstanding_principal + $loan->outstanding_penalty, 0, ',', '.') }}?')">Konfirmasi Pelunasan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- MODAL CATAT PENAGIHAN (UNTUK STAF: TELLER, ANALIS, MANAJER, ADMIN) --}}
    @if(!auth()->user()->isMember())
    <div id="modal-record-collection" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="collectionModalTitle">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl border border-slate-200 space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-xl bg-rose-50 text-rose-700 font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div>
                        <h3 id="collectionModalTitle" class="text-base font-extrabold text-slate-900">Catat Aktivitas Penagihan</h3>
                        <p class="text-2xs text-slate-500">Pinjaman #{{ $loan->loan_no }} &bull; {{ $loan->member->user->name }}</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modal-record-collection').classList.add('hidden')" aria-label="Tutup modal" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <form action="{{ route('loans.collection.record', $loan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3.5">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="form-group">
                        <label for="action_type" class="form-label text-xs font-bold text-slate-700">Jenis Tindakan *</label>
                        <select name="action_type" id="action_type" class="form-control text-xs" required>
                            <option value="phone">Panggilan Telepon</option>
                            <option value="whatsapp">Pesan WhatsApp / SMS</option>
                            <option value="field_visit">Kunjungan Langsung (Visit Usaha/Rumah)</option>
                            <option value="letter_sp1">Penerbitan Surat Peringatan 1 (SP-1)</option>
                            <option value="letter_sp2">Penerbitan Surat Peringatan 2 (SP-2)</option>
                            <option value="letter_sp3">Penerbitan Surat Peringatan 3 (SP-3)</option>
                            <option value="mediation">Somasi Hukum / Mediasi</option>
                            <option value="promise_to_pay">Follow Up Komitmen Janji Bayar</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="result_status" class="form-label text-xs font-bold text-slate-700">Hasil Komunikasi *</label>
                        <select name="result_status" id="result_status" class="form-control text-xs" required>
                            <option value="promise_to_pay">Debitur Sepakat Komitmen Janji Bayar</option>
                            <option value="cooperative">Tersambung & Kooperatif</option>
                            <option value="partial_paid">Bayar Sebagian Angsuran</option>
                            <option value="unreachable">Tidak Menjawab / Alamat Tidak Ditemukan</option>
                            <option value="uncooperative">Menolak Membayar / Tidak Kooperatif</option>
                            <option value="dispute">Sengketa / Keberatan Perhitungan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 rounded-xl bg-amber-50/60 border border-amber-200">
                    <div class="form-group">
                        <label for="promised_date" class="form-label text-xs font-bold text-amber-950">Tanggal Janji Bayar</label>
                        <input type="date" name="promised_date" id="promised_date" class="form-control text-xs bg-white" min="{{ date('Y-m-d') }}">
                    </div>

                    <div class="form-group">
                        <label for="promised_amount" class="form-label text-xs font-bold text-amber-950">Nominal Janji Bayar (Rp)</label>
                        <input type="number" name="promised_amount" id="promised_amount" class="form-control text-xs bg-white" placeholder="Contoh: 500000" min="0" step="10000">
                    </div>
                </div>

                <div class="form-group">
                    <label for="notes" class="form-label text-xs font-bold text-slate-700">Catatan Hasil Kunjungan / Pembicaraan *</label>
                    <textarea name="notes" id="notes" rows="3" class="form-control text-xs" placeholder="Tuliskan poin hasil komunikasi, kendala usaha anggota, atau komitmen pelunasan..." required></textarea>
                </div>

                <div class="form-group">
                    <label for="proof_photo" class="form-label text-xs font-bold text-slate-700">Unggah Foto Bukti Kunjungan / Dokumen SP (Opsional)</label>
                    <input type="file" name="proof_photo" id="proof_photo" accept="image/*" class="form-control text-xs">
                    <span class="text-[11px] text-slate-500">Maksimal 5MB (JPG/PNG).</span>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modal-record-collection').classList.add('hidden')" class="btn btn-secondary btn-sm text-xs">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm text-xs font-bold">Simpan Catatan Penagihan</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    {{-- MODAL AJUKAN KERINGANAN / RESTRUKTURISASI --}}
    <div id="modal-request-restructuring" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="restructuringModalTitle">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-700 font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l4-2 4 2 4-2 4 2z"></path></svg>
                    </div>
                    <div>
                        <h3 id="restructuringModalTitle" class="text-base font-extrabold text-slate-900">Ajukan Keringanan Pinjaman</h3>
                        <p class="text-2xs text-slate-500">Pinjaman #{{ $loan->loan_no }}</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modal-request-restructuring').classList.add('hidden')" aria-label="Tutup modal" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <form action="{{ route('loans.restructuring.request', $loan->id) }}" method="POST" class="space-y-3.5">
                @csrf
                <div class="form-group">
                    <label for="request_type" class="form-label text-xs font-bold text-slate-700">Bentuk Keringanan *</label>
                    <select name="request_type" id="request_type" class="form-control text-xs" required>
                        <option value="penalty_waiver">Pembebasan / Diskon Sebagian Denda</option>
                        <option value="payment_reschedule">Penjadwalan Ulang Jatuh Tempo (Rescheduling)</option>
                        <option value="tenor_extension">Perpanjangan Tenor Pinjaman</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="requested_waiver_amount" class="form-label text-xs font-bold text-slate-700">Nominal Pengurangan Denda yang Dimohonkan (Rp)</label>
                    <input type="number" name="requested_waiver_amount" id="requested_waiver_amount" class="form-control text-xs font-mono font-bold" value="{{ $loan->outstanding_penalty }}" min="0" max="{{ max(1, $loan->outstanding_penalty) }}" step="1000">
                    <span class="text-[11px] text-slate-500">Denda berjalan saat ini: Rp {{ number_format($loan->outstanding_penalty, 0, ',', '.') }}.</span>
                </div>

                <div class="form-group">
                    <label for="restructure_reason" class="form-label text-xs font-bold text-slate-700">Alasan Permohonan Debitur *</label>
                    <textarea name="reason" id="restructure_reason" rows="3" class="form-control text-xs" placeholder="Jelaskan alasan kendala ekonomi, musibah, atau rencana perputaran kas usaha yang dihadapi..." required></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('modal-request-restructuring').classList.add('hidden')" class="btn btn-secondary btn-sm text-xs">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm text-xs font-bold">Kirim Permohonan ke Manajer</button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL KEPUTUSAN RESTRUKTURISASI (HANYA MANAJER / ADMIN) --}}
    @if(in_array(auth()->user()->role, ['manager', 'admin']))
    <div id="modal-decide-restructuring-single" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="decideModalTitle">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-xl bg-amber-50 text-amber-700 font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h3 id="decideModalTitle" class="text-base font-extrabold text-slate-900">Putusan Keringanan Pinjaman</h3>
                        <p class="text-2xs text-slate-500" id="decideSingleSubtitle">Pinjaman #{{ $loan->loan_no }}</p>
                    </div>
                </div>
                <button type="button" onclick="closeDecideModalSingle()" aria-label="Tutup modal" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>

            <form id="decideRestructuringFormSingle" method="POST" class="space-y-3.5">
                @csrf
                <div>
                    <span class="text-xs font-semibold text-slate-500">Alasan Permohonan Debitur:</span>
                    <p id="decideSingleReason" class="text-xs text-slate-800 bg-slate-50 p-2.5 rounded-lg border border-slate-200 mt-1 italic"></p>
                </div>

                <div class="form-group">
                    <label for="decide_single_decision" class="form-label text-xs font-bold text-slate-700">Keputusan Manajer *</label>
                    <select name="decision" id="decide_single_decision" class="form-control text-xs" required>
                        <option value="approved">SETUJUI Keringanan</option>
                        <option value="rejected">TOLAK Permohonan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="decide_single_approved_amount" class="form-label text-xs font-bold text-slate-700">Nominal Pembebasan Denda yang Disetujui (Rp)</label>
                    <input type="number" name="approved_waiver_amount" id="decide_single_approved_amount" min="0" step="1000" class="form-control text-xs font-bold text-emerald-800">
                    <span class="text-[11px] text-slate-500">Denda berjalan pinjaman ini akan dipotong otomatis sebesar nominal yang disetujui.</span>
                </div>

                <div class="form-group">
                    <label for="decide_single_notes" class="form-label text-xs font-bold text-slate-700">Catatan Putusan Manajer</label>
                    <textarea name="manager_notes" id="decide_single_notes" rows="2" class="form-control text-xs" placeholder="Contoh: Disetujui pembebasan denda 100% karena kondisi darurat kesehatan anggota"></textarea>
                </div>

                <div class="flex items-center justify-end gap-2 pt-2 border-t border-slate-100">
                    <button type="button" onclick="closeDecideModalSingle()" class="btn btn-secondary btn-sm text-xs">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm text-xs font-bold">Simpan Putusan Manajer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openDecideModalSingle(restructureId, loanNo, requestedAmount, reason) {
            const modal = document.getElementById('modal-decide-restructuring-single');
            const form = document.getElementById('decideRestructuringFormSingle');
            form.action = '/kelola-tunggakan/' + restructureId + '/putusan';
            document.getElementById('decideSingleSubtitle').innerText = 'Pinjaman #' + loanNo;
            document.getElementById('decideSingleReason').innerText = reason || 'Tidak ada keterangan rinci';
            document.getElementById('decide_single_approved_amount').value = requestedAmount;
            modal.classList.remove('hidden');
        }

        function closeDecideModalSingle() {
            document.getElementById('modal-decide-restructuring-single').classList.add('hidden');
        }
    </script>
    @endif
</div>
@endsection
