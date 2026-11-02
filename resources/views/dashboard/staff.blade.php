@extends('layouts.app')

@section('title', 'Dasbor Operasional Koperasi')

@section('content')
<!-- Header Dasbor Petugas/Pengurus -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-4 border-b border-slate-200/80">
    <div>
        <div class="flex items-center gap-2 mb-1">
            <span class="role-badge role-{{ $user->role }} px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider">
                {{ strtoupper($user->role) }}
            </span>
            <span class="text-xs text-slate-400">&bull; Panel Kerja Terpadu</span>
        </div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Panel Operasional Koperasi</h1>
        <p class="text-xs sm:text-sm text-slate-500">KSP Sahabat Sejahtera &bull; Ringkasan performa dan antrean layanan harian</p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        @if(in_array($user->role, ['teller', 'admin']))
            <a href="{{ route('members.index', ['status' => 'pending']) }}" class="btn btn-secondary btn-sm flex items-center gap-1.5 shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                Verifikasi Anggota
            </a>
        @endif
        @if(in_array($user->role, ['analyst', 'manager', 'admin']))
            <a href="{{ route('loans.applications') }}" class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-2xs">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Antrean Pinjaman
            </a>
        @endif
        @if(in_array($user->role, ['admin', 'manager']))
            <a href="{{ route('accounting.journals') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5 shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Jurnal Umum
            </a>
        @endif
    </div>
</div>

<!-- 4 Kartu Metrik Ringkasan Utama -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Anggota Aktif -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs border-t-4 border-t-emerald-600 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between text-slate-500 text-xs font-semibold mb-2">
            <span>TOTAL ANGGOTA AKTIF</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-slate-900 tracking-tight">
            {{ number_format($stats['active_members']) }}
            <span class="text-xs font-normal text-slate-400">/ {{ $stats['total_members'] }} total</span>
        </div>
        <div class="text-xs mt-2 font-medium">
            @if($stats['pending_members'] > 0)
                <span class="text-amber-700 bg-amber-50 px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                    {{ $stats['pending_members'] }} verifikasi pending
                </span>
            @else
                <span class="text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full inline-flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Semua telah terverifikasi
                </span>
            @endif
        </div>
    </div>

    <!-- Total Simpanan -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs border-t-4 border-t-sky-600 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between text-slate-500 text-xs font-semibold mb-2">
            <span>TOTAL DANA SIMPANAN</span>
            <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-700 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-slate-900 tracking-tight">
            Rp {{ number_format($stats['total_savings'] / 1000000, 1) }} jt
        </div>
        <div class="text-xs text-slate-500 mt-2 truncate font-mono">
            Rp {{ number_format($stats['total_savings'], 0, ',', '.') }}
        </div>
    </div>

    <!-- Portofolio Pinjaman Aktif -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs border-t-4 border-t-teal-600 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between text-slate-500 text-xs font-semibold mb-2">
            <span>SISA POKOK PINJAMAN</span>
            <div class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-slate-900 tracking-tight">
            Rp {{ number_format($stats['total_loans_active'] / 1000000, 1) }} jt
        </div>
        <div class="text-xs text-slate-500 mt-2">
            Portofolio pembiayaan berjalan
        </div>
    </div>

    <!-- NPL / Pinjaman Menunggak -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs border-t-4 border-t-rose-600 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between text-slate-500 text-xs font-semibold mb-2">
            <span>PINJAMAN MENUNGGAK (NPL)</span>
            <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>
        <div class="text-2xl font-extrabold text-rose-600 tracking-tight">
            Rp {{ number_format($stats['total_loans_overdue'] / 1000000, 1) }} jt
        </div>
        <div class="text-xs mt-2">
            <a href="{{ route('reports.overdue') }}" class="text-rose-600 font-semibold hover:underline flex items-center gap-1">
                Laporan Kolektibilitas &rarr;
            </a>
        </div>
    </div>
</div>

<!-- 2 Kolom Antrean: Calon Anggota & Pengajuan Pinjaman -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Antrean Verifikasi Anggota Baru -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Calon Anggota Menunggu Verifikasi</h2>
                <p class="text-xs text-slate-500">Pendaftaran online yang membutuhkan verifikasi berkas</p>
            </div>
            <a href="{{ route('members.index', ['status' => 'pending']) }}" class="btn btn-secondary btn-sm text-xs">
                Semua ({{ $stats['pending_members'] }})
            </a>
        </div>

        @if($pendingVerifications->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs flex-1 flex flex-col items-center justify-center">
                <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Tidak ada pendaftaran anggota baru yang menunggu verifikasi saat ini.
            </div>
        @else
            <div class="table-responsive flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 font-semibold uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4">Nama & Kontak</th>
                            <th class="py-3 px-3">NIK</th>
                            <th class="py-3 px-3">Pekerjaan</th>
                            <th class="py-3 px-3">Berkas</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendingVerifications as $pm)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4">
                                <div class="font-bold text-slate-800">{{ $pm->user->name }}</div>
                                <div class="text-slate-400 font-mono text-2xs">{{ $pm->user->phone }}</div>
                            </td>
                            <td class="py-3 px-3 font-mono text-slate-600">{{ $pm->nik }}</td>
                            <td class="py-3 px-3 text-slate-600">{{ $pm->occupation }}</td>
                            <td class="py-3 px-3">
                                <span class="badge badge-info text-2xs px-2 py-0.5 font-semibold">
                                    {{ $pm->documents->count() }} Dokumen
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('members.show', $pm->id) }}" class="btn btn-primary btn-sm text-xs py-1 px-3">
                                    Periksa
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Antrean Pengajuan Pinjaman Masuk -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div>
                <h2 class="text-base font-bold text-slate-900 tracking-tight">Pengajuan Pinjaman Masuk</h2>
                <p class="text-xs text-slate-500">Permohonan kredit dari anggota aktif dalam antrean evaluasi</p>
            </div>
            <a href="{{ route('loans.applications') }}" class="btn btn-secondary btn-sm text-xs">
                Semua ({{ $stats['pending_loan_applications'] }})
            </a>
        </div>

        @if($pendingApplications->isEmpty())
            <div class="p-8 text-center text-slate-400 text-xs flex-1 flex flex-col items-center justify-center">
                <svg class="w-10 h-10 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Tidak ada pengajuan pinjaman yang menunggu peninjauan saat ini.
            </div>
        @else
            <div class="table-responsive flex-1">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 font-semibold uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4">No. Pengajuan</th>
                            <th class="py-3 px-3">Peminjam</th>
                            <th class="py-3 px-3">Plafon</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendingApplications as $app)
                        <tr class="hover:bg-slate-50/70 transition-colors">
                            <td class="py-3 px-4 font-mono font-bold text-slate-700">{{ $app->application_no }}</td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-800">{{ $app->member->user->name }}</div>
                                <div class="text-slate-400 text-2xs">{{ $app->loanProduct->name }}</div>
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-900">
                                Rp {{ number_format($app->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="badge badge-{{ $app->status === 'submitted' ? 'warning' : 'info' }} text-2xs px-2 py-0.5">
                                    {{ $app->status === 'submitted' ? 'DIAJUKAN' : 'DIANALISIS' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ route('loans.applications.show', $app->id) }}" class="btn btn-primary btn-sm text-xs py-1 px-3">
                                    Proses
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Pinjaman Siap Dicairkan (Menunggu Teller) -->
@if($awaitingDisbursementLoans->isNotEmpty())
<div class="bg-white rounded-2xl border border-teal-200 shadow-sm overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-teal-100 bg-teal-50/60 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-teal-600 text-white flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-teal-900 tracking-tight">Pinjaman Telah Disetujui (Siap Dicairkan)</h2>
                <p class="text-xs text-teal-700">Akad disetujui Manajer. Teller dapat mencairkan dana ke kas tunai atau rekening transfer bank anggota.</p>
            </div>
        </div>
        <span class="badge badge-info px-3 py-1 font-bold text-xs">{{ $awaitingDisbursementLoans->count() }} Antrean Pencairan</span>
    </div>

    <div class="table-responsive">
        <table class="w-full text-left text-xs">
            <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 font-semibold uppercase text-xs">
                <tr>
                    <th class="py-3 px-4">No. Pinjaman</th>
                    <th class="py-3 px-3">Nama Anggota</th>
                    <th class="py-3 px-3">Produk</th>
                    <th class="py-3 px-3">Plafon Pokok</th>
                    <th class="py-3 px-3">Biaya Admin</th>
                    <th class="py-3 px-3">Netto Dicairkan</th>
                    <th class="py-3 px-4 text-right">Aksi Teller</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($awaitingDisbursementLoans as $abl)
                <tr class="hover:bg-teal-50/30 transition-colors">
                    <td class="py-3 px-4 font-mono font-bold text-slate-800">{{ $abl->loan_no }}</td>
                    <td class="py-3 px-3">
                        <div class="font-bold text-slate-800">{{ $abl->member->user->name }}</div>
                        <div class="text-slate-400 font-mono text-2xs">{{ $abl->member->member_no }}</div>
                    </td>
                    <td class="py-3 px-3 text-slate-600">{{ $abl->loanProduct->name }}</td>
                    <td class="py-3 px-3 font-semibold text-slate-800">Rp {{ number_format($abl->principal_amount, 0, ',', '.') }}</td>
                    <td class="py-3 px-3 text-slate-500">Rp {{ number_format($abl->total_admin_fee, 0, ',', '.') }}</td>
                    <td class="py-3 px-3 font-extrabold text-teal-700">
                        Rp {{ number_format($abl->principal_amount - $abl->total_admin_fee, 0, ',', '.') }}
                    </td>
                    <td class="py-3 px-4 text-right">
                        <form action="{{ route('loans.disburse', $abl->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin mencairkan dana pinjaman sebesar Rp {{ number_format($abl->principal_amount - $abl->total_admin_fee, 0, ',', '.') }} kepada anggota ini?');">
                            @csrf
                            <input type="hidden" name="payment_method" value="transfer">
                            <button type="submit" class="btn btn-success btn-sm text-xs py-1.5 px-3 shadow-2xs font-bold">
                                Cairkan Sekarang
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
