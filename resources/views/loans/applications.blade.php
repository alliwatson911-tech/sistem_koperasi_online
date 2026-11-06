@extends('layouts.app')

@section('title', 'Daftar Pengajuan Pinjaman')

@section('content')
<!-- Navigasi Kembali ke Halaman Sebelumnya (Kiri Atas) -->
<div class="mb-4">
    <a href="{{ route('dashboard') }}" 
       class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white hover:bg-[#F8F5F0] text-[#0F4C81] hover:text-[#1B263B] border border-[#E8E2D8] text-xs font-bold transition-all shadow-2xs"
       title="Kembali ke halaman sebelumnya">
        <svg class="w-4 h-4 text-[#0F4C81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali ke Halaman Sebelumnya</span>
    </a>
</div>

<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Pengelolaan Pengajuan Pinjaman</h1>
            <p class="text-sm text-slate-500 mt-1">
                Alur verifikasi berjenjang: <span class="font-semibold text-emerald-700">Antrean Teller</span> &rarr; <span class="font-semibold text-blue-700">Analisis Kredit</span> &rarr; <span class="font-semibold text-purple-700">Persetujuan Manajer</span> &rarr; Akad & Pencairan Dana.
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if(auth()->user()->isTeller())
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 text-xs font-semibold">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Teller: {{ auth()->user()->displayNameWithCode }}
            </span>
            @endif
        </div>
    </div>

    <!-- Ringkasan Statistik Kartu (Stats Cards) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <a href="{{ route('loans.applications') }}" class="p-3.5 rounded-xl border bg-white shadow-sm transition hover:shadow-md {{ !request('status') ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-slate-200' }}">
            <span class="text-xs font-medium text-slate-500 block">Semua Pengajuan</span>
            <div class="text-xl font-bold text-slate-800 mt-1">{{ number_format($stats['all'] ?? 0) }}</div>
        </a>
        <a href="{{ route('loans.applications', ['status' => 'submitted']) }}" class="p-3.5 rounded-xl border bg-white shadow-sm transition hover:shadow-md {{ request('status') === 'submitted' ? 'border-amber-500 ring-2 ring-amber-100' : 'border-slate-200' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-amber-700">Antrean Teller</span>
                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            </div>
            <div class="text-xl font-bold text-amber-800 mt-1">{{ number_format($stats['submitted'] ?? 0) }}</div>
        </a>
        <a href="{{ route('loans.applications', ['status' => 'received']) }}" class="p-3.5 rounded-xl border bg-white shadow-sm transition hover:shadow-md {{ request('status') === 'received' ? 'border-teal-500 ring-2 ring-teal-100' : 'border-slate-200' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-teal-700">Diterima Teller</span>
                <span class="w-2 h-2 rounded-full bg-teal-500"></span>
            </div>
            <div class="text-xl font-bold text-teal-800 mt-1">{{ number_format($stats['received'] ?? 0) }}</div>
        </a>
        <a href="{{ route('loans.applications', ['status' => 'needs_revision']) }}" class="p-3.5 rounded-xl border bg-white shadow-sm transition hover:shadow-md {{ request('status') === 'needs_revision' ? 'border-orange-500 ring-2 ring-orange-100' : 'border-slate-200' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-orange-700">Perlu Revisi</span>
                <span class="w-2 h-2 rounded-full bg-orange-500"></span>
            </div>
            <div class="text-xl font-bold text-orange-800 mt-1">{{ number_format($stats['needs_revision'] ?? 0) }}</div>
        </a>
        <a href="{{ route('loans.applications', ['status' => 'in_review']) }}" class="p-3.5 rounded-xl border bg-white shadow-sm transition hover:shadow-md {{ request('status') === 'in_review' ? 'border-blue-500 ring-2 ring-blue-100' : 'border-slate-200' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-blue-700">Ditinjau Analis</span>
                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
            </div>
            <div class="text-xl font-bold text-blue-800 mt-1">{{ number_format($stats['in_review'] ?? 0) }}</div>
        </a>
        <a href="{{ route('loans.applications', ['status' => 'approved']) }}" class="p-3.5 rounded-xl border bg-white shadow-sm transition hover:shadow-md {{ request('status') === 'approved' ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-slate-200' }}">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-emerald-700">Disetujui</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            </div>
            <div class="text-xl font-bold text-emerald-800 mt-1">{{ number_format($stats['approved'] ?? 0) }}</div>
        </a>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4">
        <form action="{{ route('loans.applications') }}" method="GET" class="flex flex-col md:flex-row gap-3 items-center justify-between">
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <input type="hidden" name="status" value="{{ request('status') }}">
                <div class="relative w-full sm:w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari No Pengajuan, Nama, NIK..." class="w-full pl-9 pr-3 py-2 text-sm border border-slate-300 rounded-xl focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                @if(isset($tellers) && $tellers->isNotEmpty())
                <select name="teller_id" class="text-sm border border-slate-300 rounded-xl px-3 py-2 bg-white text-slate-700 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">-- Semua Teller --</option>
                    @foreach($tellers as $t)
                        <option value="{{ $t->id }}" {{ request('teller_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->name }} ({{ $t->employee_code_formatted }})
                        </option>
                    @endforeach
                </select>
                @endif

                <button type="submit" class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold rounded-xl shadow-sm transition">
                    Terapkan
                </button>

                @if(request()->hasAny(['search', 'teller_id', 'status']))
                <a href="{{ route('loans.applications') }}" class="px-3 py-2 text-slate-500 hover:text-slate-800 text-sm font-medium">
                    Reset
                </a>
                @endif
            </div>

            <div class="text-xs text-slate-500">
                Menampilkan <span class="font-bold text-slate-800">{{ $applications->total() }}</span> total pengajuan
            </div>
        </form>
    </div>

    <!-- Tabel Pengajuan Pinjaman -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold text-slate-600 uppercase tracking-wider">
                        <th class="py-3.5 px-4">No. Pengajuan</th>
                        <th class="py-3.5 px-4">Anggota Pemohon</th>
                        <th class="py-3.5 px-4">Produk Pinjaman</th>
                        <th class="py-3.5 px-4">Plafon & Tenor</th>
                        <th class="py-3.5 px-4">Status Pengajuan</th>
                        <th class="py-3.5 px-4">Teller Penerima</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($applications as $app)
                    @php
                        $badgeClasses = match($app->status) {
                            'submitted' => 'bg-amber-100 text-amber-800 border-amber-300',
                            'received' => 'bg-teal-100 text-teal-800 border-teal-300',
                            'needs_revision' => 'bg-orange-100 text-orange-800 border-orange-300',
                            'in_review' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                            'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
                            default => 'bg-slate-100 text-slate-800 border-slate-300',
                        };

                        $statusLabel = match($app->status) {
                            'submitted' => 'Antrean Teller',
                            'received' => 'Diterima Teller',
                            'needs_revision' => 'Perlu Revisi',
                            'in_review' => 'Ditinjau Analis',
                            'approved' => 'Disetujui Manajer',
                            'rejected' => 'Ditolak',
                            default => strtoupper($app->status),
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/75 transition">
                        <td class="py-4 px-4 font-mono font-bold text-slate-800">
                            {{ $app->application_no }}
                            <span class="block font-sans text-xs font-normal text-slate-400 mt-0.5">
                                {{ $app->submitted_at ? $app->submitted_at->format('d/m/Y H:i') : $app->created_at->format('d/m/Y H:i') }} WITA
                            </span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-bold text-slate-800">{{ $app->member->user->name }}</div>
                            <div class="text-xs text-slate-500 font-mono">{{ $app->member->member_no }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="font-semibold text-slate-800">{{ $app->loanProduct->name }}</span>
                            <span class="block text-xs text-slate-400">{{ ucfirst($app->loanProduct->calculation_method) }} ({{ $app->interest_rate_per_period }}%/{{ strtolower($app->tenor_unit_label) }})</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-extrabold text-emerald-700">Rp {{ number_format($app->amount, 0, ',', '.') }}</div>
                            <div class="text-xs text-slate-500">{{ $app->tenor_count }} {{ $app->tenor_unit_label }}</div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeClasses }}">
                                {{ $statusLabel }}
                            </span>
                            @if($app->status === 'needs_revision')
                            <span class="block text-xs text-orange-600 mt-1 max-w-xs truncate" title="{{ $app->revision_notes }}">
                                Catatan: {{ $app->revision_notes }}
                            </span>
                            @endif
                        </td>
                        <td class="py-4 px-4">
                            @if($app->receivedBy)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs font-bold">
                                        TL
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-800 text-xs">{{ $app->receivedBy->name }}</div>
                                        <span class="inline-block px-1.5 py-0.2 rounded bg-slate-100 text-slate-600 text-[10px] font-mono">
                                            {{ $app->receivedBy->employee_code_formatted }}
                                        </span>
                                    </div>
                                </div>
                            @elseif($app->assignedTo)
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-amber-100 text-amber-800 flex items-center justify-center text-xs font-bold">
                                        TG
                                    </div>
                                    <div>
                                        <div class="text-xs text-slate-700 font-medium">Ditugaskan ke:</div>
                                        <div class="font-bold text-slate-800 text-xs">{{ $app->assignedTo->name }}</div>
                                        <span class="inline-block px-1.5 py-0.2 rounded bg-amber-50 text-amber-700 text-[10px] font-mono">
                                            {{ $app->assignedTo->employee_code_formatted }}
                                        </span>
                                    </div>
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-400">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Menunggu Antrean
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-center">
                            <a href="{{ route('loans.applications.show', $app->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                                <span>Periksa</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400">
                            <div class="inline-flex p-3 rounded-full bg-slate-100 mb-2">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <p class="font-medium text-sm text-slate-600">Tidak ada pengajuan pinjaman pada kriteria ini.</p>
                            <p class="text-xs text-slate-400 mt-0.5">Silakan ubah filter status atau kata kunci pencarian.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($applications->hasPages())
        <div class="p-4 border-t border-slate-200">
            {{ $applications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
