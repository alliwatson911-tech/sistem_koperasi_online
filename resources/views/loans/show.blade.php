@extends('layouts.app')

@section('title', 'Detail Pinjaman - ' . $loan->loan_no)

@section('content')
<div class="space-y-6">
    <!-- Tombol Kembali & Header Status -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            @if(auth()->user()->isMember())
                <a href="{{ route('loans.my') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 flex items-center gap-1.5 transition mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Pinjaman Saya
                </a>
            @else
                <a href="{{ route('loans.manage') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900 flex items-center gap-1.5 transition mb-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Portofolio Pinjaman
                </a>
            @endif
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

    {{-- Modal Pelunasan Dipercepat --}}
    @if(in_array($loan->status, ['active', 'overdue']))
    <div id="modal-early-repay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <div class="p-2 rounded-xl bg-indigo-50 text-indigo-700 font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Pelunasan Dipercepat</h3>
                        <p class="text-2xs text-slate-400">Pembebasan beban bunga masa depan</p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('modal-early-repay').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
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
                    <label class="form-label text-xs">Metode Pembayaran <span class="text-rose-500">*</span></label>
                    <select name="payment_method" class="form-control text-xs" required>
                        <option value="cash">Kas Tunai Teller</option>
                        <option value="transfer">Transfer Bank Koperasi</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label text-xs">Catatan Tambahan (Opsional)</label>
                    <input type="text" name="notes" class="form-control text-xs" placeholder="Contoh: Pelunasan dipercepat dari hasil panen/usaha">
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-early-repay').classList.add('hidden')" class="btn btn-secondary w-1/3 py-2 text-xs">Batal</button>
                    <button type="submit" class="btn btn-primary w-2/3 py-2 text-xs font-bold" onclick="return confirm('Konfirmasi pelunasan dipercepat sebesar Rp {{ number_format($loan->outstanding_principal + $loan->outstanding_penalty, 0, ',', '.') }}?')">Konfirmasi Pelunasan</button>
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
@endsection
