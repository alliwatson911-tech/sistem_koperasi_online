@extends('layouts.app')

@section('title', 'Jurnal Umum Akuntansi (Double-Entry)')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Jurnal Umum (Double-Entry Ledger)</h1>
        <p class="text-sm text-gray-500">Pencatatan akuntansi otomatis berpasangan (debit & kredit seimbang) untuk seluruh transaksi keuangan.</p>
    </div>
    <div class="flex items-center gap-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-300">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            Integritas Data: Immutable (Pembalikan Saja)
        </span>
    </div>
</div>

<!-- Filter Jurnal -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form action="{{ route('accounting.journals') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-sm border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-sm border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Transaksi</label>
            <select name="ref_type" class="w-full text-sm border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">Semua Jenis</option>
                <option value="savings_transaction" {{ request('ref_type') === 'savings_transaction' ? 'selected' : '' }}>Transaksi Simpanan</option>
                <option value="loan_disbursement" {{ request('ref_type') === 'loan_disbursement' ? 'selected' : '' }}>Pencairan Pinjaman</option>
                <option value="loan_payment" {{ request('ref_type') === 'loan_payment' ? 'selected' : '' }}>Angsuran Pinjaman</option>
                <option value="shu_closing" {{ request('ref_type') === 'shu_closing' ? 'selected' : '' }}>Tutup Buku SHU</option>
                <option value="manual" {{ request('ref_type') === 'manual' ? 'selected' : '' }}>Jurnal Modal / Penyesuaian</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-sm font-semibold transition">
                Terapkan Filter
            </button>
            <a href="{{ route('accounting.journals') }}" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Daftar Jurnal -->
<div class="space-y-4">
    @forelse($journals as $journal)
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <!-- Header Jurnal -->
        <div class="bg-gray-50 px-5 py-3 border-b border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <span class="font-mono font-bold text-gray-800 text-sm">{{ $journal->journal_no }}</span>
                <span class="text-xs text-gray-500 ml-2">Tanggal: {{ \Carbon\Carbon::parse($journal->date)->format('d/m/Y') }}</span>
                <span class="ml-2 text-xs font-semibold uppercase px-2 py-0.5 rounded bg-gray-200 text-gray-700">
                    {{ str_replace('_', ' ', $journal->reference_type) }}
                </span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-500">Dibuat oleh: {{ $journal->creator->name ?? 'Sistem KSP' }}</span>
                @if($journal->isBalanced())
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-800 border border-green-300">
                    ✓ Seimbang
                </span>
                @else
                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-0.5 rounded-full bg-red-100 text-red-800 border border-red-300">
                    ⚠ Tidak Seimbang
                </span>
                @endif
            </div>
        </div>

        <!-- Deskripsi Transaksi -->
        <div class="px-5 py-2 bg-white text-xs text-gray-600 border-b border-gray-100">
            <strong>Keterangan:</strong> {{ $journal->description }}
        </div>

        <!-- Rincian Entri Debit & Kredit -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-100/70 text-gray-600 font-semibold uppercase">
                    <tr>
                        <th class="px-5 py-2">Kode Akun</th>
                        <th class="px-5 py-2">Nama Akun</th>
                        <th class="px-5 py-2">Keterangan Baris</th>
                        <th class="px-5 py-2 text-right">Debit (Rp)</th>
                        <th class="px-5 py-2 text-right">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php $sumDebit = 0; $sumCredit = 0; @endphp
                    @foreach($journal->entries as $entry)
                    @php
                        if ($entry->type === 'debit') $sumDebit += $entry->amount;
                        if ($entry->type === 'credit') $sumCredit += $entry->amount;
                    @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-2 font-mono font-medium text-gray-700">{{ $entry->account->code }}</td>
                        <td class="px-5 py-2 font-medium {{ $entry->type === 'credit' ? 'pl-8 text-gray-700' : 'text-gray-900 font-semibold' }}">
                            {{ $entry->account->name }}
                        </td>
                        <td class="px-5 py-2 text-gray-500">{{ $entry->memo ?: '-' }}</td>
                        <td class="px-5 py-2 text-right font-medium text-gray-800">
                            {{ $entry->type === 'debit' ? number_format($entry->amount, 0, ',', '.') : '-' }}
                        </td>
                        <td class="px-5 py-2 text-right font-medium text-gray-800">
                            {{ $entry->type === 'credit' ? number_format($entry->amount, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                    <tr class="bg-gray-50 font-bold text-gray-900">
                        <td colspan="3" class="px-5 py-2 text-right uppercase">Total:</td>
                        <td class="px-5 py-2 text-right text-emerald-800">Rp {{ number_format($sumDebit, 0, ',', '.') }}</td>
                        <td class="px-5 py-2 text-right text-emerald-800">Rp {{ number_format($sumCredit, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center text-gray-500">
        Tidak ada jurnal akuntansi yang ditemukan.
    </div>
    @endforelse
</div>

@if($journals->hasPages())
<div class="mt-6">
    {{ $journals->links() }}
</div>
@endif
@endsection
