@extends('layouts.app')

@section('title', 'Portofolio Pinjaman Anggota')

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

<!-- Header Portofolio Pinjaman & Ringkasan Outstanding -->
<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6 pb-4 border-b border-slate-200/80">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-[#EAF6F5] text-[#0F4C81] text-[11px] font-bold border border-[#2A9D8F]/30">
                <span class="w-1.5 h-1.5 rounded-full bg-[#2A9D8F]"></span>
                Portofolio Pembiayaan
            </span>
            <span class="text-xs text-slate-400 font-medium">&bull; Layanan Kredit Anggota</span>
        </div>
        <h1 class="text-2xl font-black text-slate-900 tracking-tight">Portofolio Pinjaman Anggota</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola dan pantau seluruh fasilitas pinjaman aktif, menunggak, dan lunas secara komprehensif.</p>
    </div>

    <!-- Ringkasan Outstanding Card -->
    <div class="bg-white border border-[#E8E2D8] rounded-2xl p-4 shadow-xs flex items-center gap-4 shrink-0 border-l-4 border-l-[#0F4C81]">
        <div class="w-10 h-10 rounded-xl bg-[#EAF6F5] text-[#0F4C81] flex items-center justify-center shrink-0 border border-[#2A9D8F]/20">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div>
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block">Total Sisa Pokok (Outstanding)</span>
            <span class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

<!-- Filter & Pencarian Rapih & Proporsional -->
<div class="bg-white rounded-2xl shadow-xs border border-[#E8E2D8] p-5 mb-6">
    <form action="{{ route('loans.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-end">
        <div class="sm:col-span-5">
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Cari Anggota / No. Pinjaman</label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama anggota, no. pinjaman, NIK..." class="w-full pl-9 pr-3 py-2 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#2A9D8F] focus:border-[#2A9D8F] transition-all">
            </div>
        </div>
        <div class="sm:col-span-4">
            <label class="block text-xs font-bold text-slate-700 mb-1.5">Status Pinjaman</label>
            <select name="status" class="w-full py-2 px-3 text-xs sm:text-sm border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#2A9D8F] focus:border-[#2A9D8F] bg-white transition-all">
                <option value="">Semua Status Pinjaman</option>
                <option value="approved_awaiting_disbursement" {{ request('status') === 'approved_awaiting_disbursement' ? 'selected' : '' }}>Menunggu Akad / Cair</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif Lancar</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Menunggak (Overdue)</option>
                <option value="paid_off" {{ request('status') === 'paid_off' ? 'selected' : '' }}>Lunas (Selesai)</option>
            </select>
        </div>
        <div class="sm:col-span-3 flex items-center gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-1 py-2 rounded-xl font-bold flex items-center justify-center gap-1.5 shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                <span>Filter</span>
            </button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('loans.index') }}" class="btn btn-secondary btn-sm px-3.5 py-2 rounded-xl font-bold text-slate-600 hover:text-slate-900" title="Reset filter">
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Tabel Daftar Pinjaman -->
<div class="bg-white rounded-2xl shadow-xs border border-slate-200/90 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm text-slate-600">
            <thead class="bg-slate-50/80 text-slate-700 uppercase text-[11px] font-bold tracking-wider border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3.5">No. Pinjaman</th>
                    <th class="px-5 py-3.5">Anggota</th>
                    <th class="px-5 py-3.5">Produk & Tenor</th>
                    <th class="px-5 py-3.5 text-right">Plafon Awal</th>
                    <th class="px-5 py-3.5 text-right">Sisa Pokok</th>
                    <th class="px-5 py-3.5 text-right">Denda</th>
                    <th class="px-5 py-3.5 text-center">Status</th>
                    <th class="px-5 py-3.5 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($loans as $loan)
                <tr class="hover:bg-slate-50/70 transition-colors">
                    <td class="px-5 py-3.5">
                        <a href="{{ route('loans.show', $loan->id) }}" class="font-bold font-mono text-[#0F4C81] hover:text-[#1B263B] hover:underline">
                            {{ $loan->loan_no }}
                        </a>
                        <span class="block text-[11px] text-slate-400 font-normal mt-0.5">
                            {{ $loan->disbursed_at ? \Carbon\Carbon::parse($loan->disbursed_at)->format('d/m/Y') : 'Belum akad' }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="font-bold text-slate-800 block text-xs sm:text-sm">{{ $loan->member->user->name }}</span>
                        <span class="text-[11px] text-slate-400 font-mono">{{ $loan->member->member_no }}</span>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="text-slate-800 font-semibold block text-xs">{{ $loan->loanProduct->name }}</span>
                        <span class="text-[11px] text-slate-500 font-medium">{{ $loan->tenor_display }} &bull; {{ ucfirst($loan->calculation_method) }}</span>
                    </td>
                    <td class="px-5 py-3.5 text-right font-mono font-medium text-slate-700">
                        Rp {{ number_format($loan->principal_amount, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3.5 text-right font-mono font-bold {{ $loan->outstanding_principal > 0 ? 'text-slate-900' : 'text-slate-400' }}">
                        Rp {{ number_format($loan->outstanding_principal, 0, ',', '.') }}
                    </td>
                    <td class="px-5 py-3.5 text-right font-mono font-bold {{ $loan->outstanding_penalty > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                        @if($loan->outstanding_penalty > 0)
                            Rp {{ number_format($loan->outstanding_penalty, 0, ',', '.') }}
                        @else
                            <span class="text-slate-300">-</span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        @php
                            $badge = [
                                'approved_awaiting_disbursement' => 'bg-amber-50 text-amber-800 border-amber-200',
                                'active' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'overdue' => 'bg-rose-50 text-rose-800 border-rose-200',
                                'paid_off' => 'bg-blue-50 text-blue-800 border-blue-200',
                                'written_off' => 'bg-slate-100 text-slate-800 border-slate-200',
                            ];
                            $statusLabel = [
                                'approved_awaiting_disbursement' => 'Menunggu Akad',
                                'active' => 'Aktif Lancar',
                                'overdue' => 'Menunggak',
                                'paid_off' => 'Lunas',
                                'written_off' => 'Penghapusan',
                            ];
                        @endphp
                        <span class="inline-block px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $badge[$loan->status] ?? 'bg-slate-100 text-slate-800 border-slate-200' }}">
                            {{ $statusLabel[$loan->status] ?? strtoupper(str_replace('_', ' ', $loan->status)) }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-center">
                        <div class="inline-flex items-center gap-1.5">
                            <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-secondary btn-sm px-2.5 py-1 text-xs font-semibold rounded-lg shadow-2xs" title="Lihat detail pinjaman">
                                Rincian
                            </a>
                            @if(in_array($loan->status, ['active', 'overdue']))
                            <a href="{{ route('loans.pay', $loan->id) }}" class="btn btn-primary btn-sm px-2.5 py-1 text-xs font-bold rounded-lg shadow-2xs" title="Bayar angsuran">
                                Bayar
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center text-slate-400 text-xs">
                        <svg class="w-10 h-10 text-slate-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Tidak ada data pinjaman yang cocok dengan kriteria pencarian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($loans->hasPages())
    <div class="p-4 border-t border-slate-200 bg-slate-50/50">
        {{ $loans->links() }}
    </div>
    @endif
</div>
@endsection
