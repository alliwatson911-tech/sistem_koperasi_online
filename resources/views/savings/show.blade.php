@extends('layouts.app')

@section('title', 'Buku Mutasi Simpanan - ' . $account->account_no)

@section('content')
<div class="mb-4">
    <a href="{{ route('savings.index') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white hover:bg-[#F8F5F0] text-[#0F4C81] hover:text-[#1B263B] border border-[#E8E2D8] text-xs font-bold transition-all shadow-2xs" title="Kembali ke halaman sebelumnya">
        <svg class="w-4 h-4 text-[#0F4C81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        <span>Kembali ke Halaman Sebelumnya</span>
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <span class="badge badge-info" style="font-size: 0.75rem;">REKENING SIMPANAN {{ strtoupper($account->type) }}</span>
            <h1 class="card-title" style="margin-top: 0.25rem;">No. Rekening: {{ $account->account_no }}</h1>
            <p style="font-size: 0.8rem; color: var(--gray);">Pemilik: <strong>{{ $account->member->user->name }}</strong> (No. Anggota: {{ $account->member->member_no ?? '-' }})</p>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 0.75rem; color: var(--gray); text-transform: uppercase;">Saldo Akhir Saat Ini</div>
            <div style="font-size: 1.6rem; font-weight: 800; color: var(--primary);">Rp {{ number_format($account->balance, 0, ',', '.') }}</div>
            <div style="display: flex; gap: 0.5rem; justify-content: flex-end; margin-top: 0.5rem;">
                <a href="{{ route('savings.deposit.form', $account->id) }}" class="btn btn-primary btn-sm">+ Setoran Baru</a>
                @if($account->type === 'sukarela')
                    <a href="{{ route('savings.withdraw.form', $account->id) }}" class="btn btn-secondary btn-sm">- Penarikan Dana</a>
                @endif
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tanggal Transaksi</th>
                    <th>Jenis Transaksi</th>
                    <th>Keterangan</th>
                    <th>Metode Pembayaran</th>
                    <th>Nominal Mutasi</th>
                    <th>Saldo Akumulatif</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $t)
                <tr>
                    <td><code>{{ $t->transaction_no }}</code></td>
                    <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge badge-{{ $t->type === 'deposit' ? 'success' : 'danger' }}">
                            {{ $t->type === 'deposit' ? 'SETORAN' : 'PENARIKAN' }}
                        </span>
                    </td>
                    <td>{{ $t->description }}</td>
                    <td>{{ ucfirst($t->payment_method) }}</td>
                    <td>
                        <strong style="color: {{ $t->type === 'deposit' ? 'var(--success)' : 'var(--danger)' }};">
                            {{ $t->type === 'deposit' ? '+' : '-' }} Rp {{ number_format($t->amount, 0, ',', '.') }}
                        </strong>
                    </td>
                    <td><strong>Rp {{ number_format($t->balance_after, 0, ',', '.') }}</strong></td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--gray);">
                        Belum ada riwayat transaksi pada rekening simpanan ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1rem;">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
