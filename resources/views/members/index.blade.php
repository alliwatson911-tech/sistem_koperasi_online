@extends('layouts.app')

@section('title', 'Daftar Anggota Koperasi')

@section('content')
<div class="card">
    <div class="card-header flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h1 class="card-title">Daftar Anggota Koperasi</h1>
            <p style="font-size: 0.8rem; color: var(--gray);">Kelola basis data keanggotaan, verifikasi berkas, dan status keaktifan.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ url()->previous() != url()->current() ? url()->previous() : route('dashboard') }}" 
               onclick="if(window.history.length > 1){ window.history.back(); return false; }"
               class="btn btn-secondary btn-sm inline-flex items-center gap-1.5 shadow-2xs font-bold"
               title="Kembali ke halaman sebelumnya">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Kembali ke Halaman Sebelumnya</span>
            </a>
        </div>
    </div>

    {{-- Filter & Search Form --}}
    <form action="{{ route('members.index') }}" method="GET" style="display: flex; gap: 0.75rem; margin-bottom: 1.25rem; flex-wrap: wrap;">
        <input type="text" name="search" class="form-control" style="flex: 1; min-width: 200px;" value="{{ request('search') }}" placeholder="Cari nama, NIK, No. Anggota, email, HP...">
        <select name="status" class="form-control" style="width: auto;">
            <option value="">Semua Status</option>
            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified (Belum Bayar Pokok)</option>
            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active (Aktif)</option>
            <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
            <option value="resigned" {{ request('status') === 'resigned' ? 'selected' : '' }}>Resigned / Ditolak</option>
        </select>
        <button type="submit" class="btn btn-primary">Filter</button>
        @if(request()->hasAny(['search', 'status']))
            <a href="{{ route('members.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No. Anggota</th>
                    <th>Nama & Kontak</th>
                    <th>NIK</th>
                    <th>Pekerjaan & Pendapatan</th>
                    <th>Simpanan Pokok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $m)
                <tr>
                    <td>
                        @if($m->member_no)
                            <strong>{{ $m->member_no }}</strong>
                        @else
                            <span style="color: var(--gray); font-style: italic;">Belum ada</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $m->user->name }}</strong>
                        <div style="font-size: 0.75rem; color: var(--gray);">{{ $m->user->email }} &bull; {{ $m->user->phone }}</div>
                    </td>
                    <td><code>{{ $m->nik }}</code></td>
                    <td>
                        <div>{{ $m->occupation }}</div>
                        <span style="font-size: 0.75rem; color: var(--gray);">Rp {{ number_format($m->monthly_income, 0, ',', '.') }}/bln</span>
                    </td>
                    <td>
                        @php $pokok = $m->getSimpananPokokAccount(); @endphp
                        Rp {{ number_format($pokok->balance ?? 0, 0, ',', '.') }}
                    </td>
                    <td>
                        <span class="badge badge-{{ $m->status === 'active' ? 'success' : ($m->status === 'verified' ? 'info' : ($m->status === 'pending' ? 'warning' : 'danger')) }}">
                            {{ strtoupper($m->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('members.show', $m->id) }}" class="btn btn-secondary btn-sm">Buka Detail</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem; color: var(--gray);">
                        Tidak ada data anggota yang cocok dengan filter pencarian.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 1.25rem;">
        {{ $members->links() }}
    </div>
</div>
@endsection
