@extends('layouts.app')

@section('title', 'Buku Besar (General Ledger)')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Buku Besar Akuntansi (General Ledger)</h1>
        <p class="text-sm text-gray-500">Mutasi dan saldo rinci per akun rekening sesuai Bagan Akun Standar Koperasi.</p>
    </div>
    <div class="flex items-center gap-2">
        <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('dashboard') }}" 
           onclick="if(window.history.length > 1){ window.history.back(); return false; }"
           class="btn btn-secondary btn-sm inline-flex items-center gap-1.5 shadow-2xs font-bold"
           title="Kembali ke halaman sebelumnya">
            <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Halaman Sebelumnya</span>
        </a>
    </div>
</div>

<!-- Filter Akun & Rentang Tanggal -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-6">
    <form action="{{ route('accounting.ledger') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="md:col-span-2">
            <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Rekening Akun <span class="text-red-500">*</span></label>
            <select name="account_id" required class="w-full text-sm border-gray-300 rounded-lg p-2.5 focus:ring-emerald-500 focus:border-emerald-500 font-mono">
                <option value="">-- Pilih Bagan Akun --</option>
                @foreach($accounts as $acc)
                <option value="{{ $acc->id }}" {{ (request('account_id') == $acc->id) ? 'selected' : '' }}>
                    [{{ $acc->code }}] {{ $acc->name }} ({{ ucfirst($acc->category) }} - Normal: {{ ucfirst($acc->normal_balance) }})
                </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full text-sm border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full text-sm border-gray-300 rounded-lg p-2 focus:ring-emerald-500 focus:border-emerald-500">
        </div>
        <div>
            <button type="submit" class="w-full py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-sm font-semibold transition">
                Tampilkan Buku Besar
            </button>
        </div>
    </form>
</div>

@if($selectedAccount)
<!-- Ringkasan Akun -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Akun Terpilih</span>
        <span class="font-bold text-gray-800 text-base font-mono">[{{ $selectedAccount->code }}]</span>
        <span class="block text-sm text-gray-700 font-medium">{{ $selectedAccount->name }}</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Total Mutasi Debit</span>
        <span class="font-bold text-emerald-700 text-lg">Rp {{ number_format($totalDebit, 0, ',', '.') }}</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Total Mutasi Kredit</span>
        <span class="font-bold text-blue-700 text-lg">Rp {{ number_format($totalCredit, 0, ',', '.') }}</span>
    </div>
    <div class="bg-emerald-50 rounded-xl shadow-sm border border-emerald-200 p-4">
        <span class="text-xs text-emerald-800 block uppercase font-semibold">Saldo Akhir (Posisi {{ ucfirst($selectedAccount->normal_balance) }})</span>
        <span class="font-bold text-emerald-900 text-xl">Rp {{ number_format($balance, 0, ',', '.') }}</span>
    </div>
</div>

<!-- Tabel Mutasi Buku Besar -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">No. Jurnal</th>
                    <th class="px-4 py-3">Deskripsi Transaksi</th>
                    <th class="px-4 py-3">Catatan Baris</th>
                    <th class="px-4 py-3 text-right">Debit (Rp)</th>
                    <th class="px-4 py-3 text-right">Kredit (Rp)</th>
                    <th class="px-4 py-3 text-right">Saldo Berjalan (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php $runningBalance = 0; @endphp
                @forelse($entries as $entry)
                @php
                    if ($selectedAccount->normal_balance === 'debit') {
                        $runningBalance += ($entry->type === 'debit' ? $entry->amount : -$entry->amount);
                    } else {
                        $runningBalance += ($entry->type === 'credit' ? $entry->amount : -$entry->amount);
                    }
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 text-xs">{{ \Carbon\Carbon::parse($entry->journal->date)->format('d/m/Y') }}</td>
                    <td class="px-4 py-3 font-mono font-semibold text-gray-800 text-xs">{{ $entry->journal->journal_no }}</td>
                    <td class="px-4 py-3 text-xs text-gray-800">{{ $entry->journal->description }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">{{ $entry->memo ?: '-' }}</td>
                    <td class="px-4 py-3 text-right font-medium text-gray-800">
                        {{ $entry->type === 'debit' ? number_format($entry->amount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-gray-800">
                        {{ $entry->type === 'credit' ? number_format($entry->amount, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-4 py-3 text-right font-bold {{ $runningBalance >= 0 ? 'text-gray-900' : 'text-red-600' }}">
                        Rp {{ number_format($runningBalance, 0, ',', '.') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        Belum ada mutasi transaksi untuk akun ini pada periode yang dipilih.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@else
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center text-gray-500">
    <svg class="w-12 h-12 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
    <p class="font-medium text-base text-gray-700">Silakan pilih akun buku besar dari formulir di atas untuk melihat mutasi dan saldo.</p>
</div>
@endif
@endsection
