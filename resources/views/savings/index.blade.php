@extends('layouts.app')

@section('title', 'Kelola Simpanan Anggota')

@section('content')
<!-- Navigasi Kembali ke Halaman Sebelumnya (Kiri Atas) -->
<div class="mb-4">
    <a href="{{ route('dashboard') }}" 
       class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white hover:bg-[#F8F5F0] text-[#0F4C81] hover:text-[#1B263B] border border-[#E8E2D8] text-xs font-bold transition-all shadow-2xs"
       title="Kembali ke halaman sebelumnya">
        <svg class="w-4 h-4 text-[#0F4C81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        <span>Kembali ke Halaman Sebelumnya</span>
    </a>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <h1 class="card-title">Kelola Rekening Simpanan Anggota</h1>
            <p style="font-size: 0.8rem; color: var(--gray);">Total seluruh dana simpanan anggota terhimpun: <strong>Rp {{ number_format($totalSavings, 0, ',', '.') }}</strong></p>
        </div>
    </div>

    {{-- Filter --}}
    <form action="{{ route('savings.index') }}" method="GET" style="display: flex; gap: 0.75rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
        <input type="text" name="search" class="form-control" style="flex: 1; min-width: 200px;" value="{{ request('search') }}" placeholder="Cari No. Rekening, Nama Anggota...">
        <select name="type" class="form-control" style="width: auto;">
            <option value="">Semua Jenis Simpanan</option>
            <option value="pokok" {{ request('type') === 'pokok' ? 'selected' : '' }}>Simpanan Pokok</option>
            <option value="wajib" {{ request('type') === 'wajib' ? 'selected' : '' }}>Simpanan Wajib</option>
            <option value="sukarela" {{ request('type') === 'sukarela' ? 'selected' : '' }}>Simpanan Sukarela</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request()->hasAny(['search', 'type']))
            <a href="{{ route('savings.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Rekening</th>
                    <th>Nama Anggota</th>
                    <th>No. Anggota</th>
                    <th>Jenis Simpanan</th>
                    <th>Saldo Saat Ini</th>
                    <th>Status</th>
                    <th>Aksi Petugas</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $acc)
                <tr>
                    <td><code>{{ $acc->account_no }}</code></td>
                    <td><strong>{{ $acc->member->user->name }}</strong></td>
                    <td>{{ $acc->member->member_no ?? 'Belum ada' }}</td>
                    <td>
                        <span class="badge badge-{{ $acc->type === 'pokok' ? 'success' : ($acc->type === 'wajib' ? 'info' : 'warning') }}">
                            {{ ucfirst($acc->type) }}
                        </span>
                    </td>
                    <td><strong style="color: var(--primary);">Rp {{ number_format($acc->balance, 0, ',', '.') }}</strong></td>
                    <td><span class="badge badge-success">{{ strtoupper($acc->status) }}</span></td>
                    <td>
                        <div style="display: flex; gap: 0.35rem;">
                            <a href="{{ route('savings.show', $acc->id) }}" class="btn btn-secondary btn-sm">Buku Mutasi</a>
                            <a href="{{ route('savings.deposit.form', $acc->id) }}" class="btn btn-primary btn-sm">Setor Kas</a>
                            @if($acc->type === 'sukarela')
                                <a href="{{ route('savings.withdraw.form', $acc->id) }}" class="btn btn-secondary btn-sm">Tarik Kas</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--gray);">
                        Tidak ada rekening simpanan yang cocok.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.25rem;">
        {{ $accounts->links() }}
    </div>
</div>
@endsection
