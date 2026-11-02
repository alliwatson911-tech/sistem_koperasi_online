@extends('layouts.app')

@section('title', 'Laporan Arus Kas (Cash Flow)')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Laporan Arus Kas (Cash Flow)</h1>
        <p class="text-sm text-gray-500">Mutasi kas masuk dan kas keluar pada akun Kas dan Bank Koperasi.</p>
    </div>
    <div class="flex items-center gap-2">
        <button onclick="window.print()" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            Cetak / Ekspor PDF
        </button>
    </div>
</div>

<!-- Filter Bulan & Tahun -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
    <form action="{{ route('reports.cash_flow') }}" method="GET" class="flex flex-wrap items-center gap-4">
        <div>
            <label class="text-xs font-semibold text-gray-600 mr-2">Bulan:</label>
            <select name="month" class="text-sm border-gray-300 rounded-lg p-2 focus:ring-emerald-500">
                @for($m = 1; $m <= 12; $m++)
                <option value="{{ sprintf('%02d', $m) }}" {{ (int) $month === $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create(2026, $m, 1)->translatedFormat('F') }}
                </option>
                @endfor
            </select>
        </div>
        <div>
            <label class="text-xs font-semibold text-gray-600 mr-2">Tahun:</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="2050" class="w-24 text-sm border-gray-300 rounded-lg p-2 focus:ring-emerald-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-sm font-semibold transition">
            Tampilkan
        </button>
    </form>
</div>

<!-- Ringkasan Arus Kas -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Total Penerimaan Kas (Inflow)</span>
        <span class="font-bold text-emerald-700 text-xl">Rp {{ number_format($totalInflow, 0, ',', '.') }}</span>
        <span class="text-xs text-gray-400 block mt-1">Setoran simpanan & angsuran</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Total Pengeluaran Kas (Outflow)</span>
        <span class="font-bold text-red-600 text-xl">Rp {{ number_format($totalOutflow, 0, ',', '.') }}</span>
        <span class="text-xs text-gray-400 block mt-1">Pencairan pinjaman & penarikan</span>
    </div>
    <div class="p-4 rounded-xl shadow-sm border {{ $netCashFlow >= 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200' }}">
        <span class="text-xs {{ $netCashFlow >= 0 ? 'text-emerald-800' : 'text-red-800' }} block uppercase font-semibold">Arus Kas Bersih (Net Cash Flow)</span>
        <span class="font-bold text-xl {{ $netCashFlow >= 0 ? 'text-emerald-900' : 'text-red-900' }}">
            Rp {{ number_format($netCashFlow, 0, ',', '.') }}
        </span>
        <span class="text-xs text-gray-500 block mt-1">{{ $netCashFlow >= 0 ? 'Surplus Likuiditas' : 'Defisit Likuiditas' }}</span>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Penerimaan Kas (Inflow) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 bg-emerald-50 border-b border-emerald-100 flex justify-between items-center">
            <h2 class="text-sm font-semibold text-emerald-900">Arus Masuk (Penerimaan Kas)</h2>
            <span class="font-bold text-emerald-800 text-sm">Rp {{ number_format($totalInflow, 0, ',', '.') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-2">Tgl</th>
                        <th class="px-4 py-2">Akun / Keterangan</th>
                        <th class="px-4 py-2 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($inflows as $in)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-500">{{ \Carbon\Carbon::parse($in->journal->date)->format('d/m') }}</td>
                        <td class="px-4 py-2">
                            <span class="font-medium text-gray-800 block">{{ $in->journal->description }}</span>
                            <span class="text-gray-400 font-mono">{{ $in->account->name }}</span>
                        </td>
                        <td class="px-4 py-2 text-right font-semibold text-emerald-700">
                            Rp {{ number_format($in->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-400">Tidak ada penerimaan kas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pengeluaran Kas (Outflow) -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 bg-red-50 border-b border-red-100 flex justify-between items-center">
            <h2 class="text-sm font-semibold text-red-900">Arus Keluar (Pengeluaran Kas)</h2>
            <span class="font-bold text-red-800 text-sm">Rp {{ number_format($totalOutflow, 0, ',', '.') }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-gray-50 text-gray-700 uppercase font-semibold">
                    <tr>
                        <th class="px-4 py-2">Tgl</th>
                        <th class="px-4 py-2">Akun / Keterangan</th>
                        <th class="px-4 py-2 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($outflows as $out)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 text-gray-500">{{ \Carbon\Carbon::parse($out->journal->date)->format('d/m') }}</td>
                        <td class="px-4 py-2">
                            <span class="font-medium text-gray-800 block">{{ $out->journal->description }}</span>
                            <span class="text-gray-400 font-mono">{{ $out->account->name }}</span>
                        </td>
                        <td class="px-4 py-2 text-right font-semibold text-red-600">
                            Rp {{ number_format($out->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-4 py-6 text-center text-gray-400">Tidak ada pengeluaran kas.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
