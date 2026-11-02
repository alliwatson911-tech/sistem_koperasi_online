@extends('layouts.app')

@section('title', 'Katalog Produk Pinjaman Koperasi')

@section('content')
<div style="margin-bottom: 1.5rem; text-align: center;">
    <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--dark);">Katalog Produk Pinjaman Anggota</h1>
    <p style="font-size: 0.9rem; color: var(--gray); max-width: 650px; margin: 0.5rem auto 0;">
        Fasilitas permodalan dan pinjaman kebutuhan keluarga khusus anggota aktif KSP Sahabat Sejahtera dengan prinsip transparansi penuh tanpa biaya tersembunyi.
    </p>
</div>

<div class="grid-3">
    @foreach($products as $p)
    <div class="card" style="border-top: 4px solid var(--primary); display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                <span class="badge badge-info">{{ $p->code }}</span>
                <span class="badge badge-gray">Metode: {{ ucfirst($p->calculation_method) }}</span>
            </div>
            <h2 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $p->name }}</h2>
            <p style="font-size: 0.825rem; color: var(--gray); margin-bottom: 1rem;">{{ $p->description }}</p>

            <div style="background: #f8fafc; border-radius: 8px; padding: 0.85rem; font-size: 0.825rem; margin-bottom: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                    <span style="color: var(--gray);">Suku Bunga:</span>
                    <strong>{{ $p->interest_rate_per_period }}% / {{ strtolower($p->tenor_unit_label) }} ({{ $p->interest_rate_annual }}% p.a.)</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                    <span style="color: var(--gray);">Plafon Pinjaman:</span>
                    <strong>Rp {{ number_format($p->min_amount/1000000, 1) }}jt - Rp {{ number_format($p->max_amount/1000000, 1) }}jt</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                    <span style="color: var(--gray);">Pilihan Tenor:</span>
                    <strong>{{ $p->min_tenor }} s.d. {{ $p->max_tenor }} {{ $p->tenor_unit_label }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.35rem;">
                    <span style="color: var(--gray);">Biaya Admin:</span>
                    <strong>{{ $p->admin_fee_percentage }}% + Rp {{ number_format($p->admin_fee_fixed, 0, ',', '.') }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between;">
                    <span style="color: var(--gray);">Denda Keterlambatan:</span>
                    <strong style="color: var(--danger);">{{ $p->penalty_rate_daily * 100 }}% / hari (Maks 10%)</strong>
                </div>
            </div>
        </div>

        <div style="display: flex; gap: 0.5rem; margin-top: 1rem;">
            <a href="{{ route('loans.simulation', ['product_id' => $p->id, 'amount' => $p->min_amount, 'tenor' => $p->min_tenor]) }}" class="btn btn-secondary btn-sm" style="flex: 1;">Simulasi Angsuran</a>
            @auth
                @if(auth()->user()->isMember())
                    <a href="{{ route('loans.apply', ['product_id' => $p->id]) }}" class="btn btn-primary btn-sm" style="flex: 1;">Ajukan Sekarang</a>
                @endif
            @endauth
        </div>
    </div>
    @endforeach
</div>
@endsection
