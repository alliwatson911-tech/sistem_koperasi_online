@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div style="max-width: 800px; margin: 0 auto;">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Profil Anggota Koperasi</h1>
            <span class="badge badge-{{ $member->status === 'active' ? 'success' : ($member->status === 'verified' ? 'info' : 'warning') }}">
                STATUS: {{ strtoupper($member->status) }}
            </span>
        </div>

        <div class="grid-2" style="font-size: 0.875rem;">
            <div>
                <p style="color: var(--gray); font-size: 0.75rem; text-transform: uppercase;">Nama Lengkap</p>
                <p style="font-weight: 600; margin-bottom: 0.75rem;">{{ $user->name }}</p>

                <p style="color: var(--gray); font-size: 0.75rem; text-transform: uppercase;">Nomor Anggota (No. ID)</p>
                <p style="font-weight: 600; margin-bottom: 0.75rem;">{{ $member->member_no ?? 'Belum Terbit (Menunggu Verifikasi)' }}</p>

                <p style="color: var(--gray); font-size: 0.75rem; text-transform: uppercase;">Nomor Induk Kependudukan (NIK)</p>
                <p style="font-weight: 600; margin-bottom: 0.75rem;">{{ $member->nik }}</p>

                <p style="color: var(--gray); font-size: 0.75rem; text-transform: uppercase;">Tempat, Tanggal Lahir</p>
                <p style="font-weight: 600; margin-bottom: 0.75rem;">{{ $member->birth_place }}, {{ $member->birth_date->format('d F Y') }}</p>

                <p style="color: var(--gray); font-size: 0.75rem; text-transform: uppercase;">Jenis Kelamin</p>
                <p style="font-weight: 600; margin-bottom: 0.75rem;">{{ $member->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
            </div>

            <div>
                <p style="color: var(--gray); font-size: 0.75rem; text-transform: uppercase;">Alamat Email</p>
                <p style="font-weight: 600; margin-bottom: 0.75rem;">{{ $user->email }}</p>

                <p style="color: var(--gray); font-size: 0.75rem; text-transform: uppercase;">Nomor WhatsApp</p>
                <p style="font-weight: 600; margin-bottom: 0.75rem;">{{ $user->phone }}</p>

                <p style="color: var(--gray); font-size: 0.75rem; text-transform: uppercase;">Pekerjaan</p>
                <p style="font-weight: 600; margin-bottom: 0.75rem;">{{ $member->occupation }}</p>

                <p style="color: var(--gray); font-size: 0.75rem; text-transform: uppercase;">Penghasilan Bulanan</p>
                <p style="font-weight: 600; margin-bottom: 0.75rem;">Rp {{ number_format($member->monthly_income, 0, ',', '.') }}</p>

                <p style="color: var(--gray); font-size: 0.75rem; text-transform: uppercase;">Rekening Bank Pencairan</p>
                <p style="font-weight: 600; margin-bottom: 0.75rem;">{{ $member->bank_name ?? '-' }} ({{ $member->bank_account ?? '-' }})</p>
            </div>
        </div>

        <div style="margin-top: 1rem; border-top: 1px solid var(--border); padding-top: 1rem;">
            <p style="color: var(--gray); font-size: 0.75rem; text-transform: uppercase;">Alamat Lengkap</p>
            <p style="font-weight: 500;">{{ $member->address }}</p>
        </div>
    </div>
</div>
@endsection
