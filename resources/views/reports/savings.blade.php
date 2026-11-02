@extends('layouts.app')

@section('title', 'Laporan Rekapitulasi Simpanan Anggota')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Rekapitulasi Simpanan Anggota</h1>
        <p class="text-sm text-gray-500">Laporan posisi saldo Simpanan Pokok, Simpanan Wajib, dan Simpanan Sukarela per anggota.</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="window.print()" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak / Ekspor PDF
        </button>
    </div>
</div>

<!-- Ringkasan Saldo Simpanan -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Total Simpanan Pokok</span>
        <span class="font-bold text-gray-800 text-xl">Rp {{ number_format($summary['total_pokok'], 0, ',', '.') }}</span>
        <span class="text-xs text-gray-400 block mt-1">Modal awal keanggotaan</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Total Simpanan Wajib</span>
        <span class="font-bold text-blue-700 text-xl">Rp {{ number_format($summary['total_wajib'], 0, ',', '.') }}</span>
        <span class="text-xs text-gray-400 block mt-1">Setoran bulanan anggota</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Total Simpanan Sukarela</span>
        <span class="font-bold text-emerald-700 text-xl">Rp {{ number_format($summary['total_sukarela'], 0, ',', '.') }}</span>
        <span class="text-xs text-gray-400 block mt-1">Dapat ditarik sewaktu-waktu</span>
    </div>
    <div class="bg-emerald-50 rounded-xl shadow-sm border border-emerald-200 p-4">
        <span class="text-xs text-emerald-800 block uppercase font-semibold">Total Dana Simpanan (Grand Total)</span>
        <span class="font-bold text-emerald-900 text-xl">Rp {{ number_format($summary['grand_total'], 0, ',', '.') }}</span>
        <span class="text-xs text-emerald-700 block mt-1">Likuiditas simpanan koperasi</span>
    </div>
</div>

<!-- Tabel Laporan Simpanan -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3">No. Anggota</th>
                    <th class="px-4 py-3">Nama Anggota</th>
                    <th class="px-4 py-3 text-right">Simpanan Pokok</th>
                    <th class="px-4 py-3 text-right">Simpanan Wajib</th>
                    <th class="px-4 py-3 text-right">Simpanan Sukarela</th>
                    <th class="px-4 py-3 text-right">Total Saldo</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($members as $m)
                @php
                    $pokok = (int) $m->savingsAccounts->where('type', 'pokok')->first()?->balance;
                    $wajib = (int) $m->savingsAccounts->where('type', 'wajib')->first()?->balance;
                    $sukarela = (int) $m->savingsAccounts->where('type', 'sukarela')->first()?->balance;
                    $total = $pokok + $wajib + $sukarela;
                @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-mono font-medium text-gray-800 text-xs">{{ $m->member_no }}</td>
                    <td class="px-4 py-3 font-semibold text-gray-900">{{ $m->user->name }}</td>
                    <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($pokok, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($wajib, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($sukarela, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right font-bold text-emerald-800">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                            {{ strtoupper($m->status) }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">Belum ada data anggota.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($members->hasPages())
    <div class="p-4 border-t border-gray-200">
        {{ $members->links() }}
    </div>
    @endif
</div>
@endsection
