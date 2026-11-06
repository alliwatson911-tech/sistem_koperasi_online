@extends('layouts.app')

@section('title', 'Kelola Pinjaman Menunggak & Jatuh Tempo')

@section('content')
<!-- Navigasi Kembali ke Halaman Sebelumnya (Kiri Atas) -->
<div class="mb-4">
    <a href="{{ route('dashboard') }}" 
       class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 border border-slate-200 text-xs font-bold transition-all shadow-2xs"
       title="Kembali ke halaman sebelumnya">
        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali ke Halaman Sebelumnya</span>
    </a>
</div>

<div class="space-y-6">
    {{-- Header & Aksi Cepat --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2">
                <span class="inline-flex p-2 rounded-xl bg-amber-50 text-amber-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
                <div>
                    <h1 class="text-xl font-black text-slate-900 tracking-tight">Pengelolaan Pinjaman Menunggak & Jatuh Tempo</h1>
                    <p class="text-xs text-slate-500">Monitoring Kolektibilitas, Tindak Lanjut Penagihan (SP-1 s/d SP-3), dan Restrukturisasi Denda.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <form action="{{ route('loans.overdue.sync') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm flex items-center gap-1.5" title="Hitung denda harian dan update status tunggakan terkini">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    Sinkronkan Denda Hari Ini
                </button>
            </form>
            <a href="{{ route('reports.overdue') }}" class="btn btn-secondary btn-sm" title="Lihat Laporan Penagihan">
                Laporan Tunggakan
            </a>
        </div>
    </div>

    @if(!empty($migrationNeeded))
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-300 text-amber-900 shadow-xs flex items-start gap-3.5">
        <span class="p-2 rounded-xl bg-amber-200 text-amber-900 mt-0.5 shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
        </span>
        <div class="space-y-1">
            <h3 class="text-sm font-black text-amber-950">Pemberitahuan Sistem: Migrasi Database Diperlukan</h3>
            <p class="text-xs text-amber-800 leading-relaxed">
                Struktur tabel baru untuk modul <strong>Pengelolaan Pinjaman Menunggak & Foto Usaha</strong> belum termigrasi pada database server Anda.
                Aplikasi tetap berjalan normal tanpa error crash, namun data penagihan baru dapat ditampilkan setelah migrasi dijalankan.
            </p>
            <div class="pt-1">
                <span class="text-[11px] font-semibold text-amber-900">Jalankan perintah ini di Terminal VPS / aaPanel:</span>
                <code class="block w-fit mt-1 px-3 py-1.5 bg-amber-900 text-amber-100 rounded-lg text-xs font-mono font-bold tracking-wide select-all">php artisan migrate --force</code>
            </div>
        </div>
    </div>
    @endif

    {{-- Kartu Ringkasan KPI Portofolio --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card border-l-4 border-l-red-500">
            <span class="stat-label">Total Pinjaman Menunggak</span>
            <div class="stat-value text-red-600">{{ number_format($stats['total_overdue']) }} <span class="text-xs text-slate-500 font-normal">Debitur</span></div>
            <span class="stat-meta text-xs text-slate-500">Melewati tanggal jatuh tempo</span>
        </div>

        <div class="stat-card border-l-4 border-l-amber-500">
            <span class="stat-label">Jatuh Tempo Mendekati (&le; 7 Hari)</span>
            <div class="stat-value text-amber-600">{{ number_format($stats['total_due_soon']) }} <span class="text-xs text-slate-500 font-normal">Debitur</span></div>
            <span class="stat-meta text-xs text-slate-500">Prioritas pengingat (reminder)</span>
        </div>

        <div class="stat-card border-l-4 border-l-red-700">
            <span class="stat-label">Akumulasi Denda Berjalan</span>
            <div class="stat-value text-red-700">Rp {{ number_format($stats['total_penalty'], 0, ',', '.') }}</div>
            <span class="stat-meta text-xs text-slate-500">Tarif transparan maks 10%/angsuran</span>
        </div>

        <div class="stat-card border-l-4 border-l-blue-600">
            <span class="stat-label">Pokok Pinjaman Menunggak</span>
            <div class="stat-value text-slate-900">Rp {{ number_format($stats['total_outstanding'], 0, ',', '.') }}</div>
            <span class="stat-meta text-xs text-slate-500">Sisa pokok dalam pemulihan</span>
        </div>
    </div>

    {{-- Bar Klasifikasi Kolektibilitas OJK / Kemenkop UKM --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200">
        <h2 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Klasifikasi Kolektibilitas Portofolio (Kolektibilitas 2 s/d 5)</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center text-xs">
            <a href="{{ route('loans.overdue.manage', ['collectibility' => 'dpk']) }}" class="p-2.5 rounded-lg border {{ request('collectibility') === 'dpk' ? 'bg-amber-100 border-amber-400 font-bold' : 'bg-amber-50 border-amber-200' }} hover:bg-amber-100 transition">
                <div class="font-bold text-amber-900">Kol-2: DPK (1-30 Hari)</div>
                <div class="text-lg font-black text-amber-800">{{ $stats['dpk_count'] }} <span class="text-xs font-normal">Debitur</span></div>
            </a>
            <a href="{{ route('loans.overdue.manage', ['collectibility' => 'kurang_lancar']) }}" class="p-2.5 rounded-lg border {{ request('collectibility') === 'kurang_lancar' ? 'bg-orange-100 border-orange-400 font-bold' : 'bg-orange-50 border-orange-200' }} hover:bg-orange-100 transition">
                <div class="font-bold text-orange-900">Kol-3: Kurang Lancar (SP-1)</div>
                <div class="text-lg font-black text-orange-800">{{ $stats['kurang_lancar_count'] }} <span class="text-xs font-normal">Debitur</span></div>
            </a>
            <a href="{{ route('loans.overdue.manage', ['collectibility' => 'diragukan']) }}" class="p-2.5 rounded-lg border {{ request('collectibility') === 'diragukan' ? 'bg-rose-100 border-rose-400 font-bold' : 'bg-rose-50 border-rose-200' }} hover:bg-rose-100 transition">
                <div class="font-bold text-rose-900">Kol-4: Diragukan (SP-2)</div>
                <div class="text-lg font-black text-rose-800">{{ $stats['diragukan_count'] }} <span class="text-xs font-normal">Debitur</span></div>
            </a>
            <a href="{{ route('loans.overdue.manage', ['collectibility' => 'macet']) }}" class="p-2.5 rounded-lg border {{ request('collectibility') === 'macet' ? 'bg-red-200 border-red-500 font-bold' : 'bg-red-50 border-red-300' }} hover:bg-red-100 transition">
                <div class="font-bold text-red-900">Kol-5: Macet (SP-3/NPL)</div>
                <div class="text-lg font-black text-red-800">{{ $stats['macet_count'] }} <span class="text-xs font-normal">Debitur</span></div>
            </a>
        </div>
    </div>

    {{-- BAGIAN RESTRUKTURISASI / PERMOHONAN KERINGANAN PENDING (JIKA ADA) --}}
    @if($pendingRestructurings->isNotEmpty())
    <div class="bg-amber-50/70 border border-amber-300 rounded-2xl p-5 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="inline-flex p-1.5 rounded-lg bg-amber-200 text-amber-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </span>
                <h2 class="text-sm font-bold text-amber-950">Permohonan Keringanan & Restrukturisasi Menunggu Keputusan Manajer ({{ $pendingRestructurings->count() }})</h2>
            </div>
            <span class="badge badge-warning text-[10px]">Memerlukan Otorisasi Manajer / Admin</span>
        </div>

        <div class="table-responsive bg-white rounded-xl border border-amber-200">
            <table>
                <thead>
                    <tr>
                        <th>No. Pinjaman</th>
                        <th>Anggota</th>
                        <th>Jenis Permohonan</th>
                        <th>Nominal Denda Dimohonkan</th>
                        <th>Alasan Permohonan</th>
                        <th>Diajukan Oleh</th>
                        <th>Tindakan Manajer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pendingRestructurings as $pr)
                    <tr>
                        <td><strong>{{ $pr->loan?->loan_no ?? '-' }}</strong></td>
                        <td>
                            <div class="font-bold text-slate-800">{{ $pr->loan?->member?->user?->name ?? 'Anggota' }}</div>
                            <div class="text-[11px] text-slate-500">{{ $pr->loan?->member?->member_no ?? '-' }}</div>
                        </td>
                        <td><span class="badge badge-info">{{ $pr->type_label }}</span></td>
                        <td>
                            @if($pr->requested_waiver_amount > 0)
                                <strong class="text-red-700">Rp {{ number_format($pr->requested_waiver_amount, 0, ',', '.') }}</strong>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="max-w-xs text-xs text-slate-700">{{ $pr->reason }}</td>
                        <td>
                            <span class="text-xs font-semibold text-slate-800">{{ $pr->requestedBy?->name ?? 'Pemohon' }}</span>
                            <span class="block text-[10px] text-slate-500">({{ ucfirst($pr->requestedBy?->role ?? 'staf') }})</span>
                        </td>
                        <td>
                            @if(in_array(auth()->user()->role, ['manager', 'admin']))
                                <button type="button" onclick="openDecideModal({{ $pr->id }}, '{{ $pr->loan?->loan_no ?? '' }}', {{ $pr->requested_waiver_amount }}, '{{ addslashes($pr->reason) }}')" class="btn btn-primary btn-sm text-xs">
                                    Putuskan
                                </button>
                            @else
                                <span class="text-xs text-slate-400 italic">Menunggu Manajer</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Toolbar Filter & Pencarian --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200 flex flex-wrap items-center justify-between gap-3">
        <form action="{{ route('loans.overdue.manage') }}" method="GET" class="flex flex-wrap items-center gap-2 flex-1">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control text-xs min-w-[220px]" placeholder="Cari No. Pinjaman, Nama, No. Anggota...">

            <select name="collectibility" class="form-control text-xs w-auto">
                <option value="">Semua Kolektibilitas</option>
                <option value="due_soon" {{ request('collectibility') === 'due_soon' ? 'selected' : '' }}>Jatuh Tempo Mendekati (&le; 7 Hari)</option>
                <option value="dpk" {{ request('collectibility') === 'dpk' ? 'selected' : '' }}>Kol-2: DPK (1-30 Hari)</option>
                <option value="kurang_lancar" {{ request('collectibility') === 'kurang_lancar' ? 'selected' : '' }}>Kol-3: Kurang Lancar (31-60 Hari)</option>
                <option value="diragukan" {{ request('collectibility') === 'diragukan' ? 'selected' : '' }}>Kol-4: Diragukan (61-90 Hari)</option>
                <option value="macet" {{ request('collectibility') === 'macet' ? 'selected' : '' }}>Kol-5: Macet (&gt; 90 Hari)</option>
            </select>

            <select name="stage" class="form-control text-xs w-auto">
                <option value="">Semua Tahapan Penagihan</option>
                <option value="normal" {{ request('stage') === 'normal' ? 'selected' : '' }}>Normal</option>
                <option value="reminder" {{ request('stage') === 'reminder' ? 'selected' : '' }}>Reminder / Pengingat</option>
                <option value="sp1" {{ request('stage') === 'sp1' ? 'selected' : '' }}>Surat Peringatan 1 (SP-1)</option>
                <option value="sp2" {{ request('stage') === 'sp2' ? 'selected' : '' }}>Surat Peringatan 2 (SP-2)</option>
                <option value="sp3" {{ request('stage') === 'sp3' ? 'selected' : '' }}>Surat Peringatan 3 / Somasi</option>
                <option value="restructured" {{ request('stage') === 'restructured' ? 'selected' : '' }}>Dalam Restrukturisasi</option>
            </select>

            <button type="submit" class="btn btn-primary btn-sm text-xs">Filter</button>
            @if(request()->hasAny(['search', 'collectibility', 'stage']))
                <a href="{{ route('loans.overdue.manage') }}" class="btn btn-secondary btn-sm text-xs">Reset</a>
            @endif
        </form>
    </div>

    {{-- Tabel Utama Pinjaman Menunggak --}}
    <div class="card p-0 overflow-hidden shadow-xs border border-slate-200">
        <div class="p-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800">Daftar Pinjaman Menunggak & Jatuh Tempo</h2>
            <span class="text-xs text-slate-500">Menampilkan {{ $loans->total() }} rekening pinjaman</span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No. Pinjaman</th>
                        <th>Anggota & Kontak</th>
                        <th>Produk & Tenor</th>
                        <th>Sisa Pokok</th>
                        <th>Hari Keterlambatan</th>
                        <th>Denda Berjalan</th>
                        <th>Kolektibilitas</th>
                        <th>Tindak Lanjut & Janji Bayar</th>
                        <th>Aksi Petugas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $l)
                    @php
                        $maxDays = $l->getMaxDaysOverdue();
                    @endphp
                    <tr class="{{ $maxDays > 60 ? 'bg-red-50/30' : ($maxDays > 0 ? 'bg-amber-50/20' : '') }}">
                        <td>
                            <a href="{{ route('loans.show', $l->id) }}" class="font-bold text-emerald-800 hover:underline">
                                {{ $l->loan_no }}
                            </a>
                            @if($l->isBusinessLoan())
                                <span class="block text-[10px] text-emerald-700 font-semibold">&bull; Modal Usaha: {{ $l->business_name ?? 'UMKM' }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="font-bold text-slate-800">{{ $l->member?->user?->name ?? 'Anggota' }}</div>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $l->member?->member_no ?? '-' }} &bull; {{ $l->member?->user?->phone ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="text-xs text-slate-700 font-medium">{{ $l->loanProduct?->name ?? 'Pinjaman' }}</div>
                            <div class="text-[10px] text-slate-500">{{ $l->tenor_display }}</div>
                        </td>
                        <td>
                            <strong class="text-slate-900">Rp {{ number_format($l->outstanding_principal, 0, ',', '.') }}</strong>
                        </td>
                        <td>
                            @if($maxDays > 0)
                                <span class="font-black text-red-600">{{ $maxDays }} Hari</span>
                            @else
                                <span class="badge badge-info text-[10px]">Jatuh Tempo &le; 7 Hari</span>
                            @endif
                        </td>
                        <td>
                            @if($l->outstanding_penalty > 0)
                                <span class="font-black text-red-700">Rp {{ number_format($l->outstanding_penalty, 0, ',', '.') }}</span>
                            @else
                                <span class="text-slate-400">Rp 0</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $l->collectibility_badge_class }}">
                                {{ $l->collectibility_label }}
                            </span>
                            <span class="block text-[10px] text-slate-500 mt-0.5">Tahap: {{ strtoupper($l->collection_stage ?? 'normal') }}</span>
                        </td>
                        <td class="text-xs">
                            @if($l->promised_payment_date)
                                <div class="bg-amber-100 text-amber-900 p-1.5 rounded-md font-semibold text-[11px]">
                                    Janji Bayar: {{ \Carbon\Carbon::parse($l->promised_payment_date)->format('d/m/Y') }}
                                    @if($l->promised_payment_amount)
                                        <div class="text-[10px] font-normal">Est: Rp {{ number_format($l->promised_payment_amount, 0, ',', '.') }}</div>
                                    @endif
                                </div>
                            @elseif($l->last_collected_at)
                                <span class="text-[11px] text-slate-600">Kontak: {{ \Carbon\Carbon::parse($l->last_collected_at)->format('d/m/Y H:i') }}</span>
                            @else
                                <span class="text-slate-400 italic text-[11px]">Belum ada catatan</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex items-center gap-1.5">
                                <button type="button" onclick="openCollectionModal({{ $l->id }}, '{{ $l->loan_no }}', '{{ addslashes($l->member?->user?->name ?? 'Anggota') }}', '{{ $l->member?->user?->phone ?? '-' }}')" class="btn btn-primary btn-sm text-[11px] py-1 px-2" title="Catat interaksi telepon, kunjungan, SP, atau janji bayar">
                                    Catat Aksi
                                </button>
                                <a href="{{ route('loans.show', $l->id) }}" class="btn btn-secondary btn-sm text-[11px] py-1 px-2" title="Lihat rincian lengkap pinjaman">
                                    Rincian
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-8 text-slate-500">
                            Tidak ada data pinjaman menunggak atau jatuh tempo yang sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($loans->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $loans->links() }}
        </div>
        @endif
    </div>
</div>

{{-- MODAL CATAT TINDAKAN PENAGIHAN (COLLECTION MODAL) --}}
<div id="collectionModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
            <div>
                <h3 class="text-base font-bold text-slate-900" id="modalLoanTitle">Catat Aktivitas Penagihan</h3>
                <p class="text-xs text-slate-500" id="modalMemberSub"></p>
            </div>
            <button type="button" onclick="closeCollectionModal()" class="text-slate-400 hover:text-slate-700 text-xl font-bold">&times;</button>
        </div>

        <form id="collectionForm" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
            @csrf
            <div class="form-group">
                <label for="col_action_type" class="form-label text-xs font-bold text-slate-700">Jenis Tindakan Penagihan *</label>
                <select name="action_type" id="col_action_type" class="form-control text-xs" required onchange="toggleSpFields(this.value)">
                    <option value="phone">Panggilan Telepon</option>
                    <option value="whatsapp">Pesan WhatsApp Resmi</option>
                    <option value="field_visit">Kunjungan Lapangan / Penagihan ke Rumah/Usaha</option>
                    <option value="letter_sp1">Penerbitan Surat Peringatan 1 (SP-1)</option>
                    <option value="letter_sp2">Penerbitan Surat Peringatan 2 (SP-2)</option>
                    <option value="letter_sp3">Penerbitan Surat Peringatan 3 / Somasi (SP-3)</option>
                    <option value="mediation">Mediasi di Kantor Koperasi</option>
                    <option value="promise_to_pay">Pencatatan Komitmen / Janji Bayar</option>
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="form-group">
                    <label for="col_result_status" class="form-label text-xs font-bold text-slate-700">Hasil Interaksi *</label>
                    <select name="result_status" id="col_result_status" class="form-control text-xs" required>
                        <option value="promise_to_pay">Janji Bayar (Commitment)</option>
                        <option value="cooperative">Kooperatif / Memohon Keringanan</option>
                        <option value="partial_paid">Bayar Sebagian</option>
                        <option value="unreachable">Tidak Dapat Dihubungi / Kosong</option>
                        <option value="uncooperative">Tidak Kooperatif / Menolak Bayar</option>
                        <option value="dispute">Sengketa / Keberatan Perhitungan</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="col_promised_date" class="form-label text-xs font-bold text-slate-700">Tanggal Janji Bayar</label>
                    <input type="date" name="promised_date" id="col_promised_date" min="{{ date('Y-m-d') }}" class="form-control text-xs">
                </div>
            </div>

            <div class="form-group">
                <label for="col_promised_amount" class="form-label text-xs font-bold text-slate-700">Estimasi Nominal Janji Bayar (Rp)</label>
                <input type="number" name="promised_amount" id="col_promised_amount" min="0" step="50000" class="form-control text-xs" placeholder="Contoh: 1500000">
            </div>

            <div class="form-group">
                <label for="col_notes" class="form-label text-xs font-bold text-slate-700">Catatan Interaksi & Respon Anggota *</label>
                <textarea name="notes" id="col_notes" rows="3" class="form-control text-xs" required placeholder="Jelaskan kondisi debitur, alasan keterlambatan, dan kesepakatan yang dicapai..."></textarea>
            </div>

            <div class="form-group">
                <label for="col_proof_photo" class="form-label text-xs font-bold text-slate-700">Unggah Foto Bukti Kunjungan / Tanda Terima SP (Opsional)</label>
                <input type="file" name="proof_photo" id="col_proof_photo" accept="image/*" class="form-control text-xs file:mr-2 file:py-1 file:px-2 file:rounded-md file:border-0 file:text-xs file:bg-slate-100">
                <span class="text-[11px] text-slate-500">Maksimal 5MB (JPG/PNG).</span>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeCollectionModal()" class="btn btn-secondary btn-sm text-xs">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm text-xs font-bold">Simpan Aktivitas Penagihan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KEPUTUSAN RESTRUKTURISASI MANAJER --}}
<div id="decideRestructuringModal" class="hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xl max-w-md w-full">
        <div class="p-5 border-b border-slate-100 bg-amber-50 rounded-t-2xl">
            <h3 class="text-base font-bold text-amber-950">Putusan Keringanan / Restrukturisasi</h3>
            <p class="text-xs text-amber-800" id="decideModalSub"></p>
        </div>

        <form id="decideRestructuringForm" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <span class="text-xs font-semibold text-slate-500">Alasan Permohonan Debitur:</span>
                <p id="decideModalReason" class="text-xs text-slate-800 bg-slate-50 p-2.5 rounded-lg border border-slate-200 mt-1 italic"></p>
            </div>

            <div class="form-group">
                <label for="decide_decision" class="form-label text-xs font-bold text-slate-700">Keputusan Manajer *</label>
                <select name="decision" id="decide_decision" class="form-control text-xs" required>
                    <option value="approved">SETUJUI Keringanan</option>
                    <option value="rejected">TOLAK Permohonan</option>
                </select>
            </div>

            <div class="form-group">
                <label for="decide_approved_amount" class="form-label text-xs font-bold text-slate-700">Nominal Pembebasan Denda yang Disetujui (Rp)</label>
                <input type="number" name="approved_waiver_amount" id="decide_approved_amount" min="0" step="10000" class="form-control text-xs font-bold text-emerald-800">
                <span class="text-[11px] text-slate-500">Denda berjalan pinjaman ini akan dipotong otomatis sebesar nominal yang disetujui.</span>
            </div>

            <div class="form-group">
                <label for="decide_notes" class="form-label text-xs font-bold text-slate-700">Catatan Putusan Manajer</label>
                <textarea name="manager_notes" id="decide_notes" rows="2" class="form-control text-xs" placeholder="Contoh: Disetujui pembebasan denda 50% dengan syarat pokok angsuran dilunasi minggu ini"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeDecideModal()" class="btn btn-secondary btn-sm text-xs">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm text-xs font-bold">Simpan Putusan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCollectionModal(loanId, loanNo, memberName, phone) {
        const modal = document.getElementById('collectionModal');
        const form = document.getElementById('collectionForm');
        form.action = '/pinjaman/' + loanId + '/catat-penagihan';
        document.getElementById('modalLoanTitle').innerText = 'Catat Penagihan: Pinjaman #' + loanNo;
        document.getElementById('modalMemberSub').innerText = 'Anggota: ' + memberName + ' (HP: ' + phone + ')';
        modal.classList.remove('hidden');
    }

    function closeCollectionModal() {
        document.getElementById('collectionModal').classList.add('hidden');
    }

    function openDecideModal(restructureId, loanNo, requestedAmount, reason) {
        const modal = document.getElementById('decideRestructuringModal');
        const form = document.getElementById('decideRestructuringForm');
        form.action = '/kelola-tunggakan/' + restructureId + '/putusan';
        document.getElementById('decideModalSub').innerText = 'Pinjaman #' + loanNo;
        document.getElementById('decideModalReason').innerText = reason || 'Tidak ada alasan rinci';
        document.getElementById('decide_approved_amount').value = requestedAmount;
        modal.classList.remove('hidden');
    }

    function closeDecideModal() {
        document.getElementById('decideRestructuringModal').classList.add('hidden');
    }
</script>
@endsection

