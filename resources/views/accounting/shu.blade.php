@extends('layouts.app')

@section('title', 'Sisa Hasil Usaha (SHU) & Tutup Buku')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Sisa Hasil Usaha (SHU) & Tutup Buku</h1>
        <p class="text-sm text-gray-500">Perhitungan pembagian laba bersih koperasi kepada anggota berdasarkan jasa modal (simpanan) dan jasa usaha (pinjaman).</p>
    </div>
    <div class="flex items-center gap-2">
        <form action="{{ route('accounting.shu') }}" method="GET" class="flex items-center gap-2">
            <label class="text-xs font-semibold text-gray-600">Tahun Buku:</label>
            <input type="number" name="year" value="{{ $year }}" min="2020" max="2050" class="w-24 text-sm border-gray-300 rounded-lg p-1.5 focus:ring-emerald-500 font-bold">
            <button type="submit" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg">Pilih</button>
        </form>
    </div>
</div>

<!-- Kartu Ringkasan Kinerja SHU Tahun Berjalan -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Total Pendapatan (Revenue)</span>
        <span class="font-bold text-emerald-700 text-lg">Rp {{ number_format($preview['total_revenue'], 0, ',', '.') }}</span>
        <span class="text-xs text-gray-400 block mt-1">Bunga pinjaman, adm, denda</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">Total Beban Operasional</span>
        <span class="font-bold text-red-600 text-lg">Rp {{ number_format($preview['total_expense'], 0, ',', '.') }}</span>
        <span class="text-xs text-gray-400 block mt-1">Beban operasional & umum</span>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
        <span class="text-xs text-gray-500 block uppercase font-semibold">SHU Bersih Sebelum Dibagi</span>
        <span class="font-bold text-emerald-900 text-xl">Rp {{ number_format($preview['net_shu'], 0, ',', '.') }}</span>
        <span class="text-xs text-gray-400 block mt-1">Laba bersih tahun buku {{ $year }}</span>
    </div>
    <div class="bg-emerald-50 rounded-xl shadow-sm border border-emerald-200 p-4">
        <span class="text-xs text-emerald-800 block uppercase font-semibold">Dana Cadangan Koperasi (40%)</span>
        <span class="font-bold text-emerald-900 text-lg">Rp {{ number_format($preview['dana_cadangan'], 0, ',', '.') }}</span>
        <span class="text-xs text-emerald-700 block mt-1">Penguatan modal koperasi</span>
    </div>
</div>

<!-- Alokasi SHU & Aksi Tutup Buku -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-5">
        <h2 class="text-base font-semibold text-gray-800 mb-3 pb-2 border-b border-gray-100">Alokasi Distribusi Sisa Hasil Usaha</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm mb-4">
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                <span class="text-xs text-gray-500 font-semibold block uppercase">Jasa Modal / Simpanan (25%)</span>
                <span class="font-bold text-emerald-800 text-base">Rp {{ number_format($preview['dana_jasa_simpanan'], 0, ',', '.') }}</span>
                <span class="text-xs text-gray-400 block">Dibagi proporsional saldo pokok & wajib</span>
            </div>
            <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                <span class="text-xs text-gray-500 font-semibold block uppercase">Jasa Usaha / Pinjaman (20%)</span>
                <span class="font-bold text-emerald-800 text-base">Rp {{ number_format($preview['dana_jasa_pinjaman'], 0, ',', '.') }}</span>
                <span class="text-xs text-gray-400 block">Dibagi proporsional kontribusi bunga pinjaman</span>
            </div>
            <div class="text-xs text-gray-500">
                <span class="font-semibold block text-gray-700">Total Simpanan Anggota Aktif:</span>
                Rp {{ number_format($preview['total_global_savings'], 0, ',', '.') }}
            </div>
            <div class="text-xs text-gray-500">
                <span class="font-semibold block text-gray-700">Total Bunga Angsuran Tahun Ini:</span>
                Rp {{ number_format($preview['total_global_loan_interest'], 0, ',', '.') }}
            </div>
        </div>
        <p class="text-xs text-gray-500 italic bg-amber-50 p-3 rounded-lg border border-amber-200 text-amber-900">
            *Catatan: Saat Tutup Buku diproses, SHU masing-masing anggota akan secara otomatis dikreditkan langsung ke rekening <strong>Simpanan Sukarela</strong> masing-masing anggota dan dijurnal ke akun ekuitas secara otomatis.
        </p>
    </div>

    <!-- Tombol Tutup Buku -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 flex flex-col justify-between">
        <div>
            <h2 class="text-base font-semibold text-gray-800 mb-2">Proses Tutup Buku</h2>
            <p class="text-xs text-gray-500 mb-4">
                Tindakan ini akan mengunci pembukuan tahun {{ $year }}, membuat jurnal penutup SHU, dan menyetorkan bagian SHU ke rekening simpanan sukarela anggota.
            </p>
        </div>
        @if(Auth::user()->isAdmin() || Auth::user()->isManager())
        <form action="{{ route('accounting.shu.close') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin melakukan TUTUP BUKU tahun {{ $year }}? Tindakan ini permanen dan akan menjurnal distribusi SHU.');">
            @csrf
            <input type="hidden" name="year" value="{{ $year }}">
            <button type="submit" class="w-full py-3 px-4 bg-emerald-700 hover:bg-emerald-800 text-white rounded-lg text-sm font-bold shadow transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Tutup Buku & Bagikan SHU {{ $year }}
            </button>
        </form>
        @else
        <div class="p-3 bg-gray-100 rounded text-center text-xs text-gray-600">
            Hanya Admin atau Manajer yang dapat mengeksekusi tutup buku.
        </div>
        @endif
    </div>
</div>

<!-- Tabel Rincian Pembagian per Anggota -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
    <div class="px-5 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-sm font-semibold text-gray-800">Simulasi / Rincian SHU per Anggota Aktif (Tahun {{ $year }})</h3>
        <span class="text-xs text-gray-500 font-medium">{{ count($preview['member_distributions']) }} Anggota Terdaftar</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-100/70 text-gray-700 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-4 py-2.5">No. Anggota & Nama</th>
                    <th class="px-4 py-2.5 text-right">Saldo Pokok+Wajib</th>
                    <th class="px-4 py-2.5 text-right">SHU Jasa Modal (25%)</th>
                    <th class="px-4 py-2.5 text-right">Bunga Dibayar</th>
                    <th class="px-4 py-2.5 text-right">SHU Jasa Pinjaman (20%)</th>
                    <th class="px-4 py-2.5 text-right font-bold text-gray-800">Total SHU Diterima</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs">
                @forelse($preview['member_distributions'] as $dist)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-4 py-2.5">
                        <span class="font-bold text-gray-800 block">{{ $dist['member']->user->name }}</span>
                        <span class="text-xs text-gray-500 font-mono">{{ $dist['member']->member_no }}</span>
                    </td>
                    <td class="px-4 py-2.5 text-right font-medium">Rp {{ number_format($dist['member_savings'], 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-emerald-700">Rp {{ number_format($dist['simpanan_share'], 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-right font-medium">Rp {{ number_format($dist['member_interest_paid'], 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-right font-semibold text-blue-700">Rp {{ number_format($dist['pinjaman_share'], 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-right font-bold text-emerald-900 text-sm">Rp {{ number_format($dist['total_shu'], 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada anggota aktif yang memenuhi kriteria penerima SHU.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Riwayat Periode Tutup Buku Masa Lalu -->
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 bg-gray-50 border-b border-gray-200">
        <h3 class="text-sm font-semibold text-gray-800">Riwayat Penutupan Buku Periode Sebelumnya</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-100/70 text-gray-700 uppercase text-xs font-semibold">
                <tr>
                    <th class="px-4 py-2.5">Tahun Buku</th>
                    <th class="px-4 py-2.5 text-right">Total Pendapatan</th>
                    <th class="px-4 py-2.5 text-right">Total Beban</th>
                    <th class="px-4 py-2.5 text-right">SHU Bersih</th>
                    <th class="px-4 py-2.5 text-center">Status</th>
                    <th class="px-4 py-2.5">Ditutup Oleh & Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-xs">
                @forelse($periods as $period)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-2.5 font-bold font-mono text-gray-800">{{ $period->year }}</td>
                    <td class="px-4 py-2.5 text-right">Rp {{ number_format($period->total_income, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-right">Rp {{ number_format($period->total_expense, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-right font-bold text-emerald-800">Rp {{ number_format($period->net_shu, 0, ',', '.') }}</td>
                    <td class="px-4 py-2.5 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">DITUTUP</span>
                    </td>
                    <td class="px-4 py-2.5 text-gray-500">
                        {{ $period->closer->name ?? '-' }} ({{ \Carbon\Carbon::parse($period->closed_at)->format('d/m/Y H:i') }})
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada riwayat tutup buku.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
