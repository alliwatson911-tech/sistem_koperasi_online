@extends('layouts.app')

@section('title', 'Dasbor Anggota')

@section('content')
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <div class="flex items-center gap-2">
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Halo, {{ $member->user->name ?? $user->name }}!</h1>
            <span class="badge badge-{{ $member->status === 'active' ? 'success' : ($member->status === 'verified' ? 'info' : 'warning') }}">
                {{ strtoupper($member->status ?? 'pending') }}
            </span>
        </div>
        <p class="text-sm text-slate-500 mt-0.5">
            Nomor Anggota: <span class="font-mono font-bold text-slate-700">{{ $member->member_no ?? 'Menunggu Verifikasi Teller' }}</span>
        </p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('loans.simulation') }}" class="btn btn-secondary">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            Simulasi Pinjaman
        </a>
        @if($member && $member->status === 'active')
            <a href="{{ route('loans.apply') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Ajukan Pinjaman
            </a>
        @endif
    </div>
</div>

{{-- Banner Status Verifikasi --}}
@if($member && $member->status === 'pending')
    <div class="alert alert-info shadow-sm mb-6">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="text-sm">
                <strong class="font-bold block text-blue-900">Pendaftaran Anda Sedang Diverifikasi:</strong>
                Berkas identitas KYC Anda sedang ditinjau oleh Teller/Petugas koperasi. Anda akan segera dapat melakukan setoran Simpanan Pokok setelah disetujui.
            </div>
        </div>
    </div>
@elseif($member && $member->status === 'verified')
    <div class="alert alert-success shadow-sm mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <div class="text-sm">
                <strong class="font-bold block text-emerald-900">Berkas Terverifikasi!</strong>
                Silakan lakukan setoran <strong>Simpanan Pokok (Rp {{ number_format(\App\Models\Setting::get('simpanan_pokok_amount', 100000), 0, ',', '.') }})</strong> untuk mengaktifkan status keanggotaan penuh dan menikmati fasilitas pinjaman.
            </div>
        </div>
        @php $pokok = $member->getSimpananPokokAccount(); @endphp
        @if($pokok)
            <a href="{{ route('savings.deposit.form', $pokok->id) }}" class="btn btn-primary btn-sm shrink-0">
                Setor Simpanan Pokok Sekarang &rarr;
            </a>
        @endif
    </div>
@endif

{{-- Kartu Saldo Simpanan --}}
<div class="mb-8">
    <div class="flex items-center justify-between mb-3">
        <h2 class="text-base font-bold text-slate-800 tracking-tight">Saldo Rekening Simpanan Anda</h2>
        <a href="{{ route('savings.index') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">Lihat Semua Mutasi &rarr;</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach($savingsAccounts as $acc)
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs relative overflow-hidden transition-all hover:shadow-md">
            <div class="w-1.5 h-full absolute top-0 left-0 {{ $acc->type === 'pokok' ? 'bg-emerald-600' : ($acc->type === 'wajib' ? 'bg-sky-600' : 'bg-amber-500') }}"></div>
            <div class="pl-2">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                        Simpanan {{ ucfirst($acc->type) }}
                    </span>
                    <span class="text-[11px] font-mono font-medium text-slate-400 bg-slate-50 px-2 py-0.5 rounded border border-slate-100">
                        {{ $acc->account_no }}
                    </span>
                </div>
                <div class="text-2xl font-black text-slate-900 mb-4 tracking-tight">
                    Rp {{ number_format($acc->balance, 0, ',', '.') }}
                </div>
                <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                    <a href="{{ route('savings.show', $acc->id) }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-900 flex items-center gap-1">
                        Buku Mutasi
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                    <div class="flex items-center gap-1.5">
                        <a href="{{ route('savings.deposit.form', $acc->id) }}" class="btn btn-primary btn-sm">Setor</a>
                        @if($acc->type === 'sukarela' && $member->status === 'active')
                            <a href="{{ route('savings.withdraw.form', $acc->id) }}" class="btn btn-secondary btn-sm">Tarik</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Fasilitas Pinjaman & Tagihan Berjalan --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Kolom Kiri: Pinjaman Aktif -->
    <div class="lg:col-span-2 space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-base font-bold text-slate-800 tracking-tight">Pinjaman Aktif Anda</h2>
            <a href="{{ route('loans.my') }}" class="text-xs font-semibold text-emerald-700 hover:text-emerald-900">Riwayat Pengajuan &rarr;</a>
        </div>

        @forelse($activeLoans as $loan)
        <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pb-3 mb-3 border-b border-slate-100">
                <div>
                    <span class="text-xs font-bold text-slate-400 font-mono">{{ $loan->loan_no }}</span>
                    <h3 class="text-base font-bold text-slate-900">{{ $loan->loanProduct->name }}</h3>
                </div>
                <div>
                    <span class="badge badge-{{ $loan->status === 'active' ? 'success' : ($loan->status === 'overdue' ? 'danger' : 'info') }}">
                        {{ strtoupper(str_replace('_', ' ', $loan->status)) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs mb-4">
                <div>
                    <span class="text-slate-400 block font-medium">Plafon Awal:</span>
                    <span class="font-bold text-slate-800 text-sm">Rp {{ number_format($loan->principal_amount, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Sisa Pokok:</span>
                    <span class="font-bold text-emerald-700 text-sm">Rp {{ number_format($loan->outstanding_principal, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Sisa Bunga:</span>
                    <span class="font-bold text-slate-800 text-sm">Rp {{ number_format($loan->outstanding_interest, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Tunggakan Denda:</span>
                    <span class="font-bold {{ $loan->outstanding_penalty > 0 ? 'text-red-600' : 'text-slate-400' }} text-sm">
                        Rp {{ number_format($loan->outstanding_penalty, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2 border-t border-slate-100">
                <span class="text-xs text-slate-500">Tenor: {{ $loan->tenor_months }} Bulan &bull; Metode: {{ ucfirst($loan->calculation_method) }}</span>
                <div class="flex items-center gap-2">
                    <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-secondary btn-sm">Rincian Kontrak</a>
                    @if(in_array($loan->status, ['active', 'overdue']))
                        <a href="{{ route('loans.pay', $loan->id) }}" class="btn btn-primary btn-sm">Bayar Angsuran</a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-2xl p-8 border border-slate-200 text-center text-slate-500">
            <svg class="w-12 h-12 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <p class="font-semibold text-slate-700 text-sm">Anda tidak memiliki kewajiban pinjaman aktif saat ini.</p>
            <p class="text-xs text-slate-400 mt-1">Butuh modal kerja atau dana kebutuhan keluarga? Simulasikan angsuran pinjaman sekarang.</p>
            <div class="mt-4">
                <a href="{{ route('loans.simulation') }}" class="btn btn-primary btn-sm">Mulai Simulasi Pinjaman</a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Kolom Kanan: Tagihan Jatuh Tempo Berikutnya -->
    <div class="space-y-4">
        <h2 class="text-base font-bold text-slate-800 tracking-tight">Jadwal Angsuran Terdekat</h2>

        @if(!empty($upcomingSchedules) && $upcomingSchedules->isNotEmpty())
            <div class="bg-white rounded-2xl p-5 border border-slate-200 shadow-xs">
                @foreach($upcomingSchedules as $sched)
                <div class="p-3.5 rounded-xl border border-slate-100 bg-slate-50/70 mb-3 last:mb-0">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs font-bold text-slate-800">Bulan Ke-{{ $sched->installment_no }}</span>
                        <span class="badge badge-{{ $sched->status === 'overdue' ? 'danger' : 'warning' }}">
                            {{ strtoupper($sched->status) }}
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 mb-2">
                        Jatuh Tempo: <span class="font-bold text-slate-700">{{ \Carbon\Carbon::parse($sched->due_date)->format('d F Y') }}</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-slate-200">
                        <span class="text-sm font-extrabold text-slate-900">Rp {{ number_format($sched->total_amount, 0, ',', '.') }}</span>
                        <a href="{{ route('loans.pay', $sched->loan_id) }}" class="btn btn-primary btn-sm">Bayar</a>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-white rounded-2xl p-6 border border-slate-200 text-center text-slate-400 text-xs">
                Tidak ada jadwal angsuran tertunggak atau mendekati jatuh tempo.
            </div>
        @endif
    </div>
</div>
@endsection
