@extends('layouts.app')

@section('title', 'Tarik Simpanan Sukarela - ' . $account->account_no)

@section('content')
<div style="max-width: 520px; margin: 1.5rem auto;">
    <div class="mb-4">
        <a href="{{ route('savings.show', $account->id) }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white hover:bg-[#F8F5F0] text-[#0F4C81] hover:text-[#1B263B] border border-[#E8E2D8] text-xs font-bold transition-all shadow-2xs" title="Kembali ke halaman sebelumnya">
            <svg class="w-4 h-4 text-[#0F4C81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Halaman Sebelumnya</span>
        </a>
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
