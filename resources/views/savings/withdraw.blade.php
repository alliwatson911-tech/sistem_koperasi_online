@extends('layouts.app')

@section('title', 'Tarik Simpanan Sukarela - ' . $account->account_no)

@section('content')
<div style="max-width: 520px; margin: 1.5rem auto;">
    <div style="margin-bottom: 1rem;">
        <a href="{{ route('savings.show', $account->id) }}" style="color: var(--primary); text-decoration: none; font-size: 0.85rem; font-weight: 600;">&larr; Kembali ke Rekening</a>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h1 class="card-title">Penarikan Simpanan Sukarela</h1>
                <p style="font-size: 0.8rem; color: var(--gray);">No. Rekening: <strong>{{ $account->account_no }}</strong> &bull; Anggota: <strong>{{ $account->member->user->name }}</strong></p>
            </div>
        </div>

        <form action="{{ route('savings.withdraw.submit', $account->id) }}" method="POST">
            @csrf

            <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: 8px; padding: 0.85rem; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                <span style="font-size: 0.8rem; color: var(--gray); font-weight: 600;">Saldo Tersedia Ditarik:</span>
                <span style="font-size: 1.15rem; font-weight: 700; color: var(--success);">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
            </div>

            <div class="form-group">
                <label class="form-label">Nominal Penarikan (Rp) *</label>
                <input type="number" name="amount" class="form-control" value="{{ old('amount', 50000) }}" required min="10000" max="{{ $account->balance }}" step="5000" autofocus>
                <span class="form-text">Maksimal penarikan tidak boleh melebihi saldo tersedia.</span>
            </div>

            <div class="form-group">
                <label class="form-label">Keperluan Penarikan / Keterangan</label>
                <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="Contoh: Keperluan dana darurat keluarga">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.65rem;" onclick="return confirm('Apakah Anda yakin ingin memproses penarikan simpanan ini?')">Konfirmasi Penarikan Kas</button>
        </form>
    </div>
</div>
@endsection
