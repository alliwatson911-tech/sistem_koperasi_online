@extends('layouts.app')

@section('title', 'Rekening Simpanan Saya')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
    <div>
        <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--dark);">Rekening Simpanan Saya</h1>
        <p style="font-size: 0.875rem; color: var(--gray);">KSP Sahabat Sejahtera mengelola 3 jenis simpanan: Pokok, Wajib, dan Sukarela.</p>
    </div>
</div>

<div class="grid-3" style="margin-bottom: 1.5rem;">
    @foreach($accounts as $acc)
    <div class="stat-card" style="border-top: 4px solid {{ $acc->type === 'pokok' ? '#047857' : ($acc->type === 'wajib' ? '#0284c7' : '#f59e0b') }};">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <span class="stat-label">Simpanan {{ ucfirst($acc->type) }}</span>
            <span class="badge badge-gray">{{ $acc->account_no }}</span>
        </div>
        <div class="stat-value">Rp {{ number_format($acc->balance, 0, ',', '.') }}</div>
        <div style="font-size: 0.75rem; color: var(--gray); margin-top: 0.25rem;">
            @if($acc->type === 'pokok')
                Dibayarkan sekali saat menjadi anggota (wajib lunas Rp 500.000).
            @elseif($acc->type === 'wajib')
                Iuran wajib bulanan Rp 50.000/bulan (menjadi dasar pembagian SHU).
            @else
                Tabungan sukarela yang bisa disetor dan ditarik sewaktu-waktu.
            @endif
        </div>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1rem; border-top: 1px solid var(--border); padding-top: 0.75rem;">
            <a href="{{ route('savings.show', $acc->id) }}" style="font-size: 0.8rem; color: var(--primary); font-weight: 600; text-decoration: none;">Mutasi Rekening &rarr;</a>
            <div style="display: flex; gap: 0.35rem;">
                <a href="{{ route('savings.deposit.form', $acc->id) }}" class="btn btn-primary btn-sm">Setor</a>
                @if($acc->type === 'sukarela' && $member->status === 'active')
                    <a href="{{ route('savings.withdraw.form', $acc->id) }}" class="btn btn-secondary btn-sm">Tarik</a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-header">
        <h2 class="card-title">Riwayat Mutasi Simpanan</h2>
    </div>

    @if($recentTransactions->isEmpty())
        <div style="text-align: center; padding: 2rem; color: var(--gray);">Belum ada riwayat transaksi simpanan.</div>
    @else
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Jenis Simpanan</th>
                        <th>Aksi</th>
                        <th>Metode</th>
                        <th>Nominal</th>
                        <th>Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentTransactions as $tx)
                    <tr>
                        <td><code>{{ $tx->transaction_no }}</code></td>
                        <td>{{ $tx->created_at->format('d/m/Y H:i') }}</td>
                        <td><span class="badge badge-info">{{ ucfirst($tx->savingsAccount->type) }}</span></td>
                        <td>
                            <span class="badge badge-{{ $tx->type === 'deposit' ? 'success' : 'danger' }}">
                                {{ $tx->type === 'deposit' ? 'SETORAN' : 'PENARIKAN' }}
                            </span>
                        </td>
                        <td>{{ ucfirst($tx->payment_method) }}</td>
                        <td>
                            <strong style="color: {{ $tx->type === 'deposit' ? 'var(--success)' : 'var(--danger)' }};">
                                {{ $tx->type === 'deposit' ? '+' : '-' }} Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td><strong>Rp {{ number_format($tx->balance_after, 0, ',', '.') }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin-top: 1rem;">
            {{ $recentTransactions->links() }}
        </div>
    @endif
</div>
@endsection
