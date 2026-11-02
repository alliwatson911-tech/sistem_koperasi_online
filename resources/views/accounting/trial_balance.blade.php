@extends('layouts.app')

@section('title', 'Neraca Saldo (Trial Balance)')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Neraca Saldo (Trial Balance)</h1>
        <p class="text-sm text-gray-500">Rekapitulasi saldo akhir seluruh akun riil dan nominal per hari ini.</p>
    </div>
    <div class="flex items-center gap-2">
        @if($isBalanced)
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-300">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            Keseimbangan Terpenuhi (Balance)
        </span>
        @else
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-300">
            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Peringatan: Selisih Pembukuan
        </span>
        @endif
    </div>
</div>

<!-- Ringkasan Debit vs Kredit -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Total Saldo Debit</span>
        <span class="font-bold text-emerald-800 text-xl">Rp {{ number_format($totalDebit, 0, ',', '.') }}</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Total Saldo Kredit</span>
        <span class="font-bold text-blue-800 text-xl">Rp {{ number_format($totalCredit, 0, ',', '.') }}</span>
    </div>
    <div class="p-4 rounded-xl shadow-sm border {{ $isBalanced ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }}">
        <span class="text-xs {{ $isBalanced ? 'text-emerald-800' : 'text-red-800' }} block uppercase font-semibold">Selisih (Koreksi)</span>
        <span class="font-bold text-xl {{ $isBalanced ? 'text-emerald-900' : 'text-red-900' }}">
            Rp {{ number_format(abs($totalDebit - $totalCredit), 0, ',', '.') }}
        </span>
    </div>
</div>

<!-- Tabel Neraca Saldo -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold border-b border-gray-200">
                <tr>
                    <th class="px-5 py-3">Kode Akun</th>
                    <th class="px-5 py-3">Nama Akun</th>
                    <th class="px-5 py-3">Kategori</th>
                    <th class="px-5 py-3">Posisi Normal</th>
                    <th class="px-5 py-3 text-right">Debit (Rp)</th>
                    <th class="px-5 py-3 text-right">Kredit (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($trialBalances as $tb)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-mono font-semibold text-gray-800">
                        <a href="{{ route('accounting.ledger', ['account_id' => $tb['account']->id]) }}" class="text-emerald-700 hover:underline">
                            {{ $tb['account']->code }}
                        </a>
                    </td>
                    <td class="px-5 py-3 font-medium text-gray-900">{{ $tb['account']->name }}</td>
                    <td class="px-5 py-3 text-xs uppercase font-semibold text-gray-500">
                        <span class="px-2 py-0.5 rounded bg-gray-100">{{ $tb['account']->category }}</span>
                    </td>
                    <td class="px-5 py-3 text-xs uppercase font-mono text-gray-500">{{ $tb['account']->normal_balance }}</td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-800">
                        {{ $tb['debit'] > 0 ? number_format($tb['debit'], 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-5 py-3 text-right font-semibold text-gray-800">
                        {{ $tb['credit'] > 0 ? number_format($tb['credit'], 0, ',', '.') : '-' }}
                    </td>
                </tr>
                @endforeach
                <tr class="bg-gray-100 font-bold text-gray-900 text-base">
                    <td colspan="4" class="px-5 py-3 text-right uppercase">Jumlah Total Neraca Saldo:</td>
                    <td class="px-5 py-3 text-right text-emerald-800">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                    <td class="px-5 py-3 text-right text-blue-800">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
