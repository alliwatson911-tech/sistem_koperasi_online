@extends('layouts.app')

@section('title', 'Laporan Pemantauan Kredit Menunggak (NPL / Overdue)')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Kredit Menunggak (NPL / Overdue)</h1>
        <p class="text-sm text-gray-500">Daftar fasilitas pinjaman yang melewati batas jatuh tempo angsuran dan akumulasi denda harian.</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="window.print()" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak Laporan NPL
        </button>
    </div>
</div>

<!-- Alert Perlindungan Anggota -->
<div class="p-4 mb-6 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs flex items-start gap-3">
    <svg class="w-5 h-5 text-blue-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <div>
        <strong class="font-semibold block text-sm mb-0.5">Prinsip Etika Penagihan & Perlindungan Anggota Koperasi:</strong>
        Seluruh komunikasi penagihan atau pengingat jatuh tempo wajib disampaikan secara santun, kekeluargaan, dan tanpa intimidasi. Tidak dibenarkan melakukan penagihan di luar kontak terdaftar debitur, atau menyebarkan data pribadi kepada pihak ketiga.
    </div>
</div>

<!-- Ringkasan Tunggakan -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-red-50 rounded-xl shadow-sm border border-red-200 p-4">
        <span class="text-xs text-red-800 block uppercase font-semibold">Total Pokok Menunggak</span>
        <span class="font-bold text-red-900 text-xl">Rp {{ number_format($totalOverduePrincipal, 0, ',', '.') }}</span>
        <span class="text-xs text-red-700 block mt-1">Sisa pokok berisiko</span>
    </div>
    <div class="bg-amber-50 rounded-xl shadow-sm border border-amber-200 p-4">
        <span class="text-xs text-amber-800 block uppercase font-semibold">Akumulasi Denda Keterlambatan</span>
        <span class="font-bold text-amber-900 text-xl">Rp {{ number_format($totalPenalty, 0, ',', '.') }}</span>
        <span class="text-xs text-amber-700 block mt-1">Denda berjalan sistematis</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Jumlah Debitur Menunggak</span>
        <span class="font-bold text-gray-900 text-xl">{{ $overdueLoans->count() }} Anggota</span>
        <span class="text-xs text-gray-400 block mt-1">Perlu pendekatan pembinaan</span>
    </div>
</div>

<!-- Tabel Pinjaman Menunggak -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 text-gray-700 uppercase text-xs font-semibold border-b border-gray-200">
                <tr>
                    <th class="px-4 py-3">No. Pinjaman</th>
                    <th class="px-4 py-3">Anggota & Telepon</th>
                    <th class="px-4 py-3">Produk</th>
                    <th class="px-4 py-3 text-right">Sisa Pokok</th>
                    <th class="px-4 py-3 text-right">Denda Berjalan</th>
                    <th class="px-4 py-3">Angsuran Terlewat</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($overdueLoans as $loan)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-3 font-mono font-semibold text-gray-800">
                        <a href="{{ route('loans.show', $loan->id) }}" class="text-emerald-700 hover:underline">
                            {{ $loan->loan_no }}
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        <span class="font-semibold text-gray-900 block">{{ $loan->member->user->name }}</span>
                        <span class="text-xs text-gray-500 font-mono">{{ $loan->member->phone }}</span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-700">
                        {{ $loan->loanProduct->name }}
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-red-600">
                        Rp {{ number_format($loan->outstanding_principal, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-amber-700">
                        Rp {{ number_format($loan->outstanding_penalty, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-xs">
                        @if($loan->schedules->count() > 0)
                            <span class="font-medium text-red-700 block">{{ $loan->schedules->count() }} Angsuran Jatuh Tempo</span>
                            <span class="text-gray-400">Paling awal: {{ \Carbon\Carbon::parse($loan->schedules->first()->due_date)->format('d/m/Y') }}</span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('loans.pay', $loan->id) }}" class="inline-block px-3 py-1 bg-emerald-700 hover:bg-emerald-800 text-white rounded text-xs font-semibold">
                            Input Bayar
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                        Alhamdulillah! Saat ini tidak ada kredit yang berstatus menunggak (NPL 0%).
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
