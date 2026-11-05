@extends('layouts.app')

@section('title', 'Portofolio Pinjaman Anggota')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Portofolio Pinjaman Anggota</h1>
        <p class="text-sm text-gray-500">Kelola dan pantau seluruh fasilitas pinjaman aktif, menunggak, dan lunas.</p>
    </div>
    <div class="flex items-center gap-3 flex-wrap">
        <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('dashboard') }}" 
           onclick="if(window.history.length > 1){ window.history.back(); return false; }"
           class="btn btn-secondary btn-sm inline-flex items-center gap-1.5 shadow-2xs font-bold"
           title="Kembali ke halaman sebelumnya">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Halaman Sebelumnya</span>
        </a>
        <div class="bg-emerald-50 border border-emerald-200 px-4 py-2 rounded-xl text-right">
            <span class="text-xs text-emerald-800 font-semibold block">Total Sisa Pokok Beredar (Outstanding):</span>
            <span class="text-xl font-bold text-emerald-900">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</span>
        </div>
    </div>
</div>

<!-- Filter & Pencarian -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form action="{{ route('loans.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Cari Anggota / No Pinjaman</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="No pinjaman, nama..." class="w-full text-sm border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Status Pinjaman</label>
            <select name="status" class="w-full text-sm border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">Semua Status</option>
                <option value="approved_awaiting_disbursement" {{ request('status') === 'approved_awaiting_disbursement' ? 'selected' : '' }}>Menunggu Akad / Cair</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif Lancar</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Menunggak</option>
                <option value="paid_off" {{ request('status') === 'paid_off' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
        <div class="flex items-end gap-2 sm:col-span-1 md:col-span-2">
            <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-sm font-semibold transition">
                Filter Data
            </button>
            <a href="{{ route('loans.index') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Tabel Pinjaman -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3">No. Pinjaman</th>
                    <th class="px-4 py-3">Anggota</th>
                    <th class="px-4 py-3">Produk & Tenor</th>
                    <th class="px-4 py-3 text-right">Plafon Awal</th>
                    <th class="px-4 py-3 text-right">Sisa Pokok</th>
                    <th class="px-4 py-3 text-right">Denda</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($loans as $loan)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-semibold text-gray-800">
                        <a href="{{ route('loans.show', $loan->id) }}" class="text-emerald-700 hover:underline">
                            {{ $loan->loan_no }}
                        </a>
                        <span class="block text-xs text-gray-400 font-normal">
                            {{ $loan->disbursed_at ? \Carbon\Carbon::parse($loan->disbursed_at)->format('d/m/Y') : 'Belum cair' }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-medium text-gray-800 block">{{ $loan->member->user->name }}</span>
                        <span class="text-xs text-gray-500 font-mono">{{ $loan->member->member_no }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-gray-800 font-medium block">{{ $loan->loanProduct->name }}</span>
                        <span class="text-xs text-gray-500">{{ $loan->tenor_display }} ({{ ucfirst($loan->calculation_method) }})</span>
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-800">
                        Rp {{ number_format($loan->principal_amount, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right font-bold {{ $loan->outstanding_principal > 0 ? 'text-amber-700' : 'text-gray-400' }}">
                        Rp {{ number_format($loan->outstanding_principal, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold {{ $loan->outstanding_penalty > 0 ? 'text-red-600' : 'text-gray-400' }}">
                        Rp {{ number_format($loan->outstanding_penalty, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $badge = [
                                'approved_awaiting_disbursement' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                'active' => 'bg-green-100 text-green-800 border-green-300',
                                'overdue' => 'bg-red-100 text-red-800 border-red-300',
                                'paid_off' => 'bg-blue-100 text-blue-800 border-blue-300',
                                'written_off' => 'bg-gray-100 text-gray-800 border-gray-300',
                            ];
                        @endphp
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge[$loan->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                            {{ strtoupper(str_replace('_', ' ', $loan->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center space-x-1">
                        <a href="{{ route('loans.show', $loan->id) }}" class="inline-block px-2.5 py-1 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded text-xs font-semibold">
                            Rincian
                        </a>
                        @if(in_array($loan->status, ['active', 'overdue']))
                        <a href="{{ route('loans.pay', $loan->id) }}" class="inline-block px-2.5 py-1 bg-emerald-700 hover:bg-emerald-800 text-white rounded text-xs font-semibold">
                            Bayar
                        </a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                        Tidak ada data pinjaman yang cocok dengan kriteria.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($loans->hasPages())
    <div class="p-4 border-t border-gray-200">
        {{ $loans->links() }}
    </div>
    @endif
</div>
@endsection
