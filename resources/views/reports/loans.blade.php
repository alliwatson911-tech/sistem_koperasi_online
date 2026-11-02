@extends('layouts.app')

@section('title', 'Laporan Portofolio & Kolektibilitas Pinjaman')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Portofolio Pinjaman</h1>
        <p class="text-sm text-gray-500">Statistik dan rincian penyaluran pembiayaan, sisa pokok beredar, dan tingkat kelancaran kredit.</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="window.print()" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak / Ekspor PDF
        </button>
    </div>
</div>

<!-- Kartu Statistik Pinjaman -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Total Disalurkan</span>
        <span class="font-bold text-gray-800 text-lg">Rp {{ number_format($summary['total_disbursed'], 0, ',', '.') }}</span>
        <span class="text-xs text-gray-400 block mt-1">Akumulasi seluruh plafon</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Sisa Pokok Beredar</span>
        <span class="font-bold text-emerald-800 text-lg">Rp {{ number_format($summary['total_outstanding'], 0, ',', '.') }}</span>
        <span class="text-xs text-gray-400 block mt-1">Outstanding aktif & overdue</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Kredit Aktif Lancar</span>
        <span class="font-bold text-green-700 text-xl">{{ $summary['total_active'] }}</span>
        <span class="text-xs text-gray-400 block mt-1">Debitur tertib</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Kredit Menunggak</span>
        <span class="font-bold text-red-600 text-xl">{{ $summary['total_overdue'] }}</span>
        <span class="text-xs text-gray-400 block mt-1">Perlu pembinaan</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Kredit Lunas</span>
        <span class="font-bold text-blue-700 text-xl">{{ $summary['total_paid_off'] }}</span>
        <span class="text-xs text-gray-400 block mt-1">Selesai angsuran</span>
    </div>
</div>

<!-- Filter Status -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form action="{{ route('reports.loans') }}" method="GET" class="flex flex-wrap items-center gap-4">
        <div>
            <label class="text-xs font-semibold text-gray-600 mr-2">Filter Status:</label>
            <select name="status" class="text-sm border-gray-300 rounded-lg p-2 focus:ring-emerald-500">
                <option value="">Semua Status</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif Lancar</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Menunggak</option>
                <option value="paid_off" {{ request('status') === 'paid_off' ? 'selected' : '' }}>Lunas</option>
            </select>
        </div>
        <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-sm font-semibold transition">
            Terapkan
        </button>
        <a href="{{ route('reports.loans') }}" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium">Reset</a>
    </form>
</div>

<!-- Tabel Laporan Portofolio -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3">No. Pinjaman</th>
                    <th class="px-4 py-3">Nama Anggota</th>
                    <th class="px-4 py-3">Produk & Metode</th>
                    <th class="px-4 py-3 text-right">Plafon Pinjaman</th>
                    <th class="px-4 py-3 text-right">Sisa Pokok</th>
                    <th class="px-4 py-3 text-right">Sisa Bunga</th>
                    <th class="px-4 py-3 text-right">Tunggakan Denda</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($loans as $loan)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-mono font-semibold text-gray-800">
                        <a href="{{ route('loans.show', $loan->id) }}" class="text-emerald-700 hover:underline">
                            {{ $loan->loan_no }}
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-medium text-gray-900 block">{{ $loan->member->user->name }}</span>
                        <span class="text-xs text-gray-500 font-mono">{{ $loan->member->member_no }}</span>
                    </td>
                    <td class="px-4 py-3">
                        <span class="text-gray-800 block font-medium">{{ $loan->loanProduct->name }}</span>
                        <span class="text-xs text-gray-500">{{ $loan->tenor_display }} - {{ ucfirst($loan->calculation_method) }}</span>
                    </td>
                    <td class="px-4 py-3 text-right text-gray-800 font-medium">
                        Rp {{ number_format($loan->principal_amount, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right font-bold {{ $loan->outstanding_principal > 0 ? 'text-amber-700' : 'text-gray-400' }}">
                        Rp {{ number_format($loan->outstanding_principal, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-700">
                        Rp {{ number_format($loan->outstanding_interest, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold {{ $loan->outstanding_penalty > 0 ? 'text-red-600' : 'text-gray-400' }}">
                        Rp {{ number_format($loan->outstanding_penalty, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $badge = [
                                'approved_awaiting_disbursement' => 'bg-yellow-100 text-yellow-800',
                                'active' => 'bg-green-100 text-green-800',
                                'overdue' => 'bg-red-100 text-red-800',
                                'paid_off' => 'bg-blue-100 text-blue-800',
                                'written_off' => 'bg-gray-100 text-gray-800',
                            ];
                        @endphp
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge[$loan->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ strtoupper(str_replace('_', ' ', $loan->status)) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-8 text-center text-gray-500">Tidak ada pinjaman pada filter ini.</td>
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
