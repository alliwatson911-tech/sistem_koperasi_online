@extends('layouts.app')

@section('title', 'Detail Pengajuan Pinjaman ' . $application->application_no)

@section('content')
<div class="space-y-6">
    <!-- Top Bar Navigation & Status -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('loans.applications') }}" class="text-emerald-700 hover:text-emerald-900 font-semibold text-xs flex items-center gap-1.5 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Daftar Pengajuan
                </a>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <h1 class="text-2xl font-black text-slate-800 tracking-tight">Pengajuan Pinjaman: {{ $application->application_no }}</h1>
                @php
                    $badgeStyles = match($application->status) {
                        'submitted' => 'bg-amber-100 text-amber-800 border-amber-300 ring-2 ring-amber-100',
                        'received' => 'bg-teal-100 text-teal-800 border-teal-300 ring-2 ring-teal-100',
                        'needs_revision' => 'bg-orange-100 text-orange-800 border-orange-300 ring-2 ring-orange-100',
                        'in_review' => 'bg-blue-100 text-blue-800 border-blue-300 ring-2 ring-blue-100',
                        'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300 ring-2 ring-emerald-100',
                        'rejected' => 'bg-rose-100 text-rose-800 border-rose-300 ring-2 ring-rose-100',
                        default => 'bg-slate-100 text-slate-800 border-slate-300',
                    };

                    $label = match($application->status) {
                        'submitted' => 'Antrean Teller',
                        'received' => 'Diterima Teller (Lengkap)',
                        'needs_revision' => 'Perlu Revisi Dokumen',
                        'in_review' => 'Ditinjau Analis Kredit',
                        'approved' => 'Disetujui Manajer',
                        'rejected' => 'Ditolak',
                        default => strtoupper($application->status),
                    };
                @endphp
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold border {{ $badgeStyles }}">
                    Status: {{ $label }}
                </span>
            </div>
            <p class="text-xs text-slate-400 mt-1">
                Diajukan pada {{ $application->submitted_at ? $application->submitted_at->format('d/m/Y H:i') : $application->created_at->format('d/m/Y H:i') }} WITA
            </p>
        </div>

        @if($application->status === 'approved' && $application->loan)
        <div>
            <a href="{{ route('loans.show', $application->loan->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Lihat Rekening Pinjaman Aktif ({{ $application->loan->loan_no }})
            </a>
        </div>
        @endif
    </div>

    <!-- Layout Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Kolom Kiri: Rincian Pengajuan, Profil, KYC, dan Keterangan Teller -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card 1: Keterangan Teller Penerima Pengajuan (FASE 1 UTAMA) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-teal-50/50 to-white">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-teal-600 text-white flex items-center justify-center shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800">Keterangan Penerimaan Teller</h2>
                            <p class="text-xs text-slate-500">Verifikasi fisik & keabsahan berkas permohonan pembiayaan</p>
                        </div>
                    </div>

                    @if($application->received_at)
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-300">
                            Terverifikasi Lengkap
                        </span>
                    @elseif($application->status === 'needs_revision')
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-800 border border-orange-300">
                            Perlu Perbaikan Berkas
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                            Menunggu Penerimaan
                        </span>
                    @endif
                </div>

                <div class="p-6 space-y-5">
                    <!-- Identitas Teller & Waktu -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <div>
                            <span class="text-xs font-medium text-slate-400 block mb-1">Teller Penerima Berkas:</span>
                            @if($application->receivedBy)
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-teal-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                        TL
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800 text-sm">{{ $application->receivedBy->name }}</div>
                                        <div class="text-xs font-mono font-semibold text-teal-700">Kode Petugas: {{ $application->receivedBy->employee_code_formatted }}</div>
                                    </div>
                                </div>
                            @elseif($application->assignedTo)
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-amber-500 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                                        TG
                                    </div>
                                    <div>
                                        <div class="text-xs text-slate-500">Sedang Ditugaskan Kepada:</div>
                                        <div class="font-bold text-slate-800 text-sm">{{ $application->assignedTo->name }} ({{ $application->assignedTo->employee_code_formatted }})</div>
                                    </div>
                                </div>
                            @else
                                <div class="text-sm font-semibold text-slate-500 italic flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                                    Belum diterima oleh Teller (Berada dalam antrean terbuka)
                                </div>
                            @endif
                        </div>

                        <div>
                            <span class="text-xs font-medium text-slate-400 block mb-1">Waktu Penerimaan / Status:</span>
                            @if($application->received_at)
                                <div class="font-bold text-slate-800 text-sm">{{ $application->received_at->format('d F Y, H:i') }} WITA</div>
                                <div class="text-xs text-teal-700 font-semibold">Telah lolos uji checklist kelengkapan</div>
                            @elseif($application->revision_requested_at)
                                <div class="font-bold text-orange-800 text-sm">Dikembalikan: {{ $application->revision_requested_at->format('d F Y, H:i') }} WITA</div>
                                <div class="text-xs text-orange-600 font-medium">Menunggu anggota melengkapi berkas</div>
                            @else
                                <div class="text-sm text-slate-500 font-medium">Dalam batas toleransi 24 jam kerja</div>
                            @endif
                        </div>

                        @if($application->assignedBy)
                        <div class="sm:col-span-2 pt-2 border-t border-slate-200 text-xs text-slate-600">
                            <span class="font-bold text-slate-700">Catatan Pengalihan Penugasan:</span>
                            Penugasan dialihkan oleh <strong>{{ $application->assignedBy->name }}</strong> pada {{ $application->assigned_at ? $application->assigned_at->format('d/m/Y H:i') : '-' }} WITA.
                            @if($application->assignment_notes)
                            <p class="italic text-slate-500 mt-0.5">"{{ $application->assignment_notes }}"</p>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Checklist Kelengkapan Dokumen -->
                    <div>
                        <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2.5 flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            Checklist Kelengkapan Dokumen Fisik & Digital:
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            @php
                                $checklist = $application->reception_checklist ?? [];
                            @endphp
                            @foreach($defaultChecklist as $key => $item)
                                @php
                                    $isChecked = !empty($checklist[$key]);
                                @endphp
                                <div class="p-3 rounded-xl border flex items-start gap-3 transition {{ $isChecked ? 'bg-teal-50/50 border-teal-200 text-teal-950' : 'bg-slate-50/70 border-slate-200 text-slate-500' }}">
                                    <div class="mt-0.5">
                                        @if($isChecked)
                                            <span class="w-5 h-5 rounded-full bg-teal-600 text-white flex items-center justify-center text-xs shadow-sm">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            </span>
                                        @else
                                            <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-xs">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-xs">
                                        <div class="font-bold flex items-center gap-1.5 {{ $isChecked ? 'text-teal-900' : 'text-slate-600' }}">
                                            {{ $item['label'] }}
                                            @if(!empty($item['required']))
                                                <span class="text-[10px] font-semibold text-rose-600">*wajib</span>
                                            @endif
                                        </div>
                                        <div class="text-slate-500 mt-0.5 text-[11px] leading-relaxed">{{ $item['description'] }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Catatan Penerimaan / Catatan Revisi Teller -->
                    @if($application->reception_note)
                    <div class="p-4 rounded-xl bg-teal-50 border border-teal-200 text-xs">
                        <span class="font-bold text-teal-900 block mb-1">Catatan Penerimaan dari Teller:</span>
                        <p class="text-teal-800 italic leading-relaxed">"{{ $application->reception_note }}"</p>
                    </div>
                    @endif

                    @if($application->revision_notes)
                    <div class="p-4 rounded-xl bg-orange-50 border border-orange-200 text-xs">
                        <span class="font-bold text-orange-900 block mb-1">Catatan Perbaikan Dokumen (Revisi):</span>
                        <p class="text-orange-800 font-medium leading-relaxed">"{{ $application->revision_notes }}"</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Card 2: Rincian Fasilitas Pembiayaan -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100 flex items-center justify-between">
                    <span>Rincian Fasilitas Pembiayaan</span>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono">
                        {{ $application->loanProduct->code }}
                    </span>
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-xs text-slate-400 block">Produk Pembiayaan</span>
                        <span class="font-bold text-slate-800 text-base">{{ $application->loanProduct->name }}</span>
                        <span class="text-xs text-slate-500 block">Metode: {{ ucfirst($application->loanProduct->calculation_method) }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Nominal Plafon Pengajuan</span>
                        <span class="font-black text-emerald-700 text-2xl">Rp {{ number_format($application->amount, 0, ',', '.') }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Jangka Waktu (Tenor)</span>
                        <span class="font-bold text-slate-800">{{ $application->tenor_count }} {{ $application->tenor_unit_label }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Suku Bunga / Jasa Pinjaman</span>
                        <span class="font-semibold text-slate-800">{{ $application->interest_rate_per_period }}% per {{ strtolower($application->tenor_unit_label) }} ({{ number_format($application->interest_rate_per_period * ($application->isWeekly() ? 52 : 12), 2) }}% p.a.)</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Biaya Administrasi (Dipotong di Awal)</span>
                        <span class="font-bold text-slate-800">
                            Rp {{ number_format(($application->amount * $application->loanProduct->admin_fee_percentage / 100) + $application->loanProduct->admin_fee_fixed, 0, ',', '.') }}
                            <span class="text-xs font-normal text-slate-400">({{ $application->loanProduct->admin_fee_percentage }}% + Rp {{ number_format($application->loanProduct->admin_fee_fixed) }})</span>
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Estimasi Angsuran Per {{ $application->tenor_unit_label }}</span>
                        <span class="font-bold text-slate-800">
                            Rp {{ number_format($application->monthly_installment_estimate, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                        <span class="text-xs text-slate-400 block mb-0.5">Tujuan Penggunaan Dana</span>
                        <p class="font-medium text-slate-700 leading-relaxed">{{ $application->purpose }}</p>
                    </div>
                </div>
            </div>

            <!-- Card 3: Profil & Likuiditas Simpanan Anggota -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Profil & Rekening Simpanan Anggota</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <span class="text-xs text-slate-400 block">Nama Lengkap Pemohon</span>
                        <span class="font-bold text-slate-800 text-base">{{ $application->member->user->name }}</span>
                        <span class="text-xs font-mono text-slate-500">{{ $application->member->member_no }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Nomor Induk Kependudukan (NIK)</span>
                        <span class="font-mono font-bold text-slate-800">{{ $application->member->nik }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Telepon / WhatsApp</span>
                        <span class="text-slate-800 font-medium">{{ $application->member->user->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400 block">Pekerjaan / Penghasilan Bulanan</span>
                        <span class="text-slate-800 font-medium">{{ $application->member->occupation ?? '-' }} (Rp {{ number_format($application->member->monthly_income ?? 0, 0, ',', '.') }}/bln)</span>
                    </div>
                    <div class="sm:col-span-2">
                        <span class="text-xs text-slate-400 block">Alamat Domisili</span>
                        <span class="text-slate-700 text-xs">{{ $application->member->address ?? '-' }}</span>
                    </div>
                </div>

                <h3 class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Likuiditas Simpanan di Koperasi:</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    @foreach($application->member->savingsAccounts as $sav)
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                        <span class="text-[11px] text-slate-400 uppercase font-bold block">Simpanan {{ ucfirst($sav->type) }}</span>
                        <span class="font-black text-emerald-800 text-sm">Rp {{ number_format($sav->balance, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Card 4: Dokumen Verifikasi (KYC) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <h2 class="text-base font-bold text-slate-800 mb-4 pb-2 border-b border-slate-100">Dokumen Verifikasi (KYC)</h2>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    @forelse($application->member->documents as $doc)
                    <div class="border border-slate-200 rounded-xl p-3 text-center bg-slate-50 hover:bg-slate-100/60 transition">
                        <span class="text-xs font-bold text-slate-700 uppercase block mb-2">{{ str_replace('_', ' ', $doc->document_type ?? $doc->type) }}</span>
                        @if(in_array(pathinfo($doc->file_path, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png']))
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank">
                                <img src="{{ asset('storage/' . $doc->file_path) }}" class="h-24 w-full object-cover rounded-lg mb-2 border border-slate-300 hover:opacity-90">
                            </a>
                        @else
                            <div class="h-24 flex items-center justify-center bg-slate-200 rounded-lg mb-2 text-slate-500 text-xs font-semibold">
                                PDF Berkas
                            </div>
                        @endif
                        <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-xs text-emerald-700 hover:underline font-semibold">Buka Dokumen</a>
                    </div>
                    @empty
                    <p class="text-xs text-slate-400 col-span-3 text-center py-4">Tidak ada berkas KYC tersimpan.</p>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Aksi Petugas & Lini Masa Timeline -->
        <div class="space-y-6">

            <!-- FORM AKSI TELLER (Klaim, Terima, atau Minta Revisi) -->
            @if(in_array(auth()->user()->role, ['teller', 'admin']) && in_array($application->status, ['submitted', 'needs_revision']))
            <div class="bg-white rounded-2xl shadow-sm border border-teal-200 p-6 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-teal-500"></span>
                        <h2 class="text-base font-bold text-slate-800">Aksi Verifikasi Teller</h2>
                    </div>
                    <span class="text-xs font-mono text-teal-700 font-bold bg-teal-50 px-2 py-0.5 rounded border border-teal-200">
                        {{ auth()->user()->employee_code_formatted }}
                    </span>
                </div>

                {{-- Tombol Klaim jika belum diambil oleh teller login --}}
                @if(!$application->assigned_to || $application->assigned_to !== auth()->id())
                <form action="{{ route('loans.applications.claim', $application->id) }}" method="POST" class="mb-2">
                    @csrf
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shadow transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                        Ambil / Klaim Antrean Ini ke Akun Saya
                    </button>
                </form>
                @endif

                {{-- Form Penerimaan Pengajuan --}}
                <div class="border border-teal-200 rounded-xl p-4 bg-teal-50/30">
                    <h3 class="text-xs font-bold text-teal-900 mb-2.5 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Terima Pengajuan (Dokumen Lengkap)
                    </h3>
                    <form action="{{ route('loans.applications.receive', $application->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="space-y-2 text-xs">
                            <span class="text-[11px] font-bold text-slate-600 block">Centang Verifikasi Dokumen:</span>
                            @foreach($defaultChecklist as $key => $item)
                            <label class="flex items-start gap-2 text-slate-700 cursor-pointer">
                                <input type="checkbox" name="checklist[{{ $key }}]" value="1" checked class="mt-0.5 rounded text-teal-600 focus:ring-teal-500 border-slate-300">
                                <span>
                                    <strong>{{ $item['label'] }}</strong>
                                    @if(!empty($item['required'])) <span class="text-rose-500">*</span> @endif
                                </span>
                            </label>
                            @endforeach
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan Penerimaan Teller</label>
                            <textarea name="notes" rows="2" class="w-full text-xs border border-slate-300 rounded-lg p-2 focus:ring-teal-500 focus:border-teal-500" placeholder="Semua berkas asli telah dicocokkan, anggota aktif..."></textarea>
                        </div>

                        <button type="submit" onclick="return confirm('Apakah Anda yakin dokumen pemohon sudah lengkap dan sah untuk diteruskan ke Analis Kredit?')" class="w-full py-2.5 px-4 bg-teal-600 hover:bg-teal-700 text-white rounded-xl text-xs font-bold shadow transition flex items-center justify-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Terima & Teruskan ke Analis Kredit
                        </button>
                    </form>
                </div>

                {{-- Form Pengembalian Revisi --}}
                <div class="border border-orange-200 rounded-xl p-4 bg-orange-50/30">
                    <h3 class="text-xs font-bold text-orange-900 mb-2 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Kembalikan untuk Perbaikan (Revisi)
                    </h3>
                    <form action="{{ route('loans.applications.request_revision', $application->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-slate-700 mb-1">Kekurangan Berkas / Alasan Pengembalian <span class="text-rose-500">*</span></label>
                            <textarea name="revision_notes" rows="2" required class="w-full text-xs border border-slate-300 rounded-lg p-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Foto KTP buram, slip gaji bulan terakhir belum dilampirkan..."></textarea>
                        </div>
                        <button type="submit" onclick="return confirm('Kembalikan pengajuan ke anggota untuk diperbaiki?')" class="w-full py-2 px-3 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-xs font-bold shadow transition">
                            Kembalikan ke Anggota
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- FORM PENGALIHAN TELLER (Khusus Admin / Manajer) -->
            @if(in_array(auth()->user()->role, ['admin', 'manager']) && !in_array($application->status, ['approved', 'rejected', 'cancelled']))
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 space-y-3">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                    <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Pengalihan Penugasan Teller</h3>
                </div>
                <form action="{{ route('loans.applications.reassign_teller', $application->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Pilih Teller Baru <span class="text-rose-500">*</span></label>
                        <select name="new_teller_id" required class="w-full text-xs border border-slate-300 rounded-lg p-2 bg-white text-slate-800">
                            <option value="">-- Pilih Petugas Teller --</option>
                            @foreach($tellers as $t)
                                <option value="{{ $t->id }}" {{ $application->assigned_to == $t->id ? 'selected' : '' }}>
                                    {{ $t->name }} ({{ $t->employee_code_formatted }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Alasan Pengalihan <span class="text-rose-500">*</span></label>
                        <input type="text" name="reason" required class="w-full text-xs border border-slate-300 rounded-lg p-2" placeholder="Teller sebelumnya cuti / beban kerja antrean tinggi...">
                    </div>
                    <button type="submit" class="w-full py-2 px-3 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold shadow transition">
                        Simpan Pengalihan Teller
                    </button>
                </form>
            </div>
            @endif

            <!-- FORM AKSI ANALIS KREDIT -->
            @if(in_array(auth()->user()->role, ['analyst', 'admin']) && in_array($application->status, ['received', 'in_review']))
            <div class="bg-white rounded-2xl shadow-sm border border-blue-200 p-6 space-y-3">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <h2 class="text-base font-bold text-slate-800">Analisis Kredit (5C)</h2>
                </div>
                <form action="{{ route('loans.applications.review', $application->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Keputusan Analis <span class="text-rose-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-xs text-slate-700 font-semibold cursor-pointer">
                                <input type="radio" name="action" value="recommend" checked class="text-blue-600 focus:ring-blue-500">
                                Rekomendasikan Setuju
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-slate-700 font-semibold cursor-pointer">
                                <input type="radio" name="action" value="reject" class="text-rose-600 focus:ring-rose-500">
                                Tolak Pengajuan
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan Analisis (Kapasitas & Karakter) <span class="text-rose-500">*</span></label>
                        <textarea name="notes" rows="3" required class="w-full text-xs border border-slate-300 rounded-lg p-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Kapasitas bayar, omset kedai, riwayat kedisiplinan anggota..."></textarea>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Rekomendasi Plafon & Catatan Khusus</label>
                        <input type="text" name="recommendations" class="w-full text-xs border border-slate-300 rounded-lg p-2" placeholder="Contoh: Disetujui penuh Rp 10.000.000, tenor 6 bulan">
                    </div>

                    <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition shadow">
                        Kirim Hasil Analisis
                    </button>
                </form>
            </div>
            @endif

            <!-- FORM AKSI MANAJER -->
            @if(in_array(auth()->user()->role, ['manager', 'admin']) && in_array($application->status, ['in_review']))
            <div class="bg-white rounded-2xl shadow-sm border border-emerald-300 p-6 space-y-3">
                <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                    <span class="w-3 h-3 rounded-full bg-emerald-600"></span>
                    <h2 class="text-base font-bold text-slate-800">Persetujuan Akhir Manajer</h2>
                </div>
                <form action="{{ route('loans.applications.approve', $application->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Keputusan Akhir Manajer <span class="text-rose-500">*</span></label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-xs text-slate-700 font-bold cursor-pointer">
                                <input type="radio" name="action" value="approve" checked class="text-emerald-600 focus:ring-emerald-500">
                                SETUJUI PINJAMAN
                            </label>
                            <label class="flex items-center gap-1.5 text-xs text-slate-700 font-bold cursor-pointer">
                                <input type="radio" name="action" value="reject" class="text-rose-600 focus:ring-rose-500">
                                TOLAK PINJAMAN
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Catatan / Alasan Keputusan</label>
                        <textarea name="notes" rows="3" class="w-full text-xs border border-slate-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500" placeholder="Alasan persetujuan atau dasar penolakan pengajuan..."></textarea>
                    </div>

                    <button type="submit" onclick="return confirm('Konfirmasi keputusan persetujuan/penolakan pinjaman ini?')" class="w-full py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white rounded-xl text-xs font-bold transition shadow">
                        Simpan Keputusan Manajer
                    </button>
                </form>
            </div>
            @endif

            <!-- LINI MASA PENANGANAN PINJAMAN (TIMELINE KOMPREHENSIF DARI AUDIT LOGS) -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center gap-2 mb-4 pb-2 border-b border-slate-100">
                    <span class="w-2.5 h-2.5 rounded-full bg-slate-800"></span>
                    <h2 class="text-base font-bold text-slate-800">Lini Masa Penanganan (Timeline)</h2>
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
                        <!-- Dot Indicator -->
                        <span class="absolute -left-[1.8rem] top-1 w-3.5 h-3.5 rounded-full {{ $dotColor }} transition"></span>

                        <div class="space-y-1">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-800">{{ $event['title'] }}</span>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 border border-slate-200">
                                    {{ $event['badge'] }}
                                </span>
                            </div>

                            <p class="text-[11px] text-slate-500">{{ $event['subtitle'] }}</p>

                            <div class="text-[11px] text-slate-600 font-medium">
                                Oleh: <strong class="text-slate-800">{{ $event['actor_name'] }}</strong>
                                @if(!empty($event['actor_role']))
                                    <span class="text-[10px] font-mono text-slate-400">({{ $event['actor_role'] }})</span>
                                @endif
                                @if(!empty($event['timestamp']))
                                    <span class="text-slate-400">&bull; {{ \Carbon\Carbon::parse($event['timestamp'])->format('d/m/Y H:i') }} WITA</span>
                                @endif
                            </div>

                            @if(!empty($event['notes']))
                            <div class="p-2.5 rounded-lg bg-slate-50 border border-slate-200 text-xs italic text-slate-700 mt-1.5">
                                "{{ $event['notes'] }}"
                            </div>
                            @endif

                            @if(!empty($event['details']) && is_array($event['details']))
                            <div class="flex flex-wrap gap-2 mt-1.5">
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
    </div>
</div>
@endsection
