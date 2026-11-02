@extends('layouts.app')

@section('title', 'Detail Anggota - ' . $member->user->name)

@section('content')
<div style="margin-bottom: 1rem;">
    <a href="{{ route('members.index') }}" style="color: var(--primary); text-decoration: none; font-size: 0.85rem; font-weight: 600;">&larr; Kembali ke Daftar Anggota</a>
</div>

<div class="grid-3">
    {{-- Profil Anggota --}}
    <div class="card" style="grid-column: span 1;">
        <div style="text-align: center; margin-bottom: 1.25rem;">
            <div style="width: 72px; height: 72px; background: var(--primary-light); color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.75rem; font-weight: 800; margin: 0 auto 0.75rem;">
                {{ substr($member->user->name, 0, 1) }}
            </div>
            <h2 style="font-size: 1.2rem; font-weight: 700;">{{ $member->user->name }}</h2>
            <div style="font-size: 0.8rem; color: var(--gray);">No: {{ $member->member_no ?? 'Belum terverifikasi' }}</div>
            <div style="margin-top: 0.5rem;">
                <span class="badge badge-{{ $member->status === 'active' ? 'success' : ($member->status === 'verified' ? 'info' : ($member->status === 'pending' ? 'warning' : 'danger')) }}">
                    STATUS: {{ strtoupper($member->status) }}
                </span>
            </div>
        </div>

        <div style="border-top: 1px solid var(--border); padding-top: 0.75rem; font-size: 0.825rem;">
            <div style="margin-bottom: 0.5rem;"><strong>NIK:</strong> {{ $member->nik }}</div>
            <div style="margin-bottom: 0.5rem;"><strong>Email:</strong> {{ $member->user->email }}</div>
            <div style="margin-bottom: 0.5rem;"><strong>Telepon/WA:</strong> {{ $member->user->phone }}</div>
            <div style="margin-bottom: 0.5rem;"><strong>TTL:</strong> {{ $member->birth_place }}, {{ $member->birth_date->format('d M Y') }}</div>
            <div style="margin-bottom: 0.5rem;"><strong>Jenis Kelamin:</strong> {{ $member->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
            <div style="margin-bottom: 0.5rem;"><strong>Pekerjaan:</strong> {{ $member->occupation }}</div>
            <div style="margin-bottom: 0.5rem;"><strong>Penghasilan:</strong> Rp {{ number_format($member->monthly_income, 0, ',', '.') }}/bulan</div>
            <div style="margin-bottom: 0.5rem;"><strong>Rekening:</strong> {{ $member->bank_name }} - {{ $member->bank_account }}</div>
            <div style="margin-bottom: 0.5rem;"><strong>Alamat:</strong> {{ $member->address }}</div>
        </div>

        @if(in_array(auth()->user()->role, ['teller', 'admin']) && $member->status === 'pending')
        <div style="border-top: 1px solid var(--border); margin-top: 1rem; padding-top: 1rem;">
            <h3 style="font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; color: var(--primary);">Tindakan Verifikasi</h3>
            <form action="{{ route('members.verify', $member->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Catatan Pemeriksaan</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Contoh: Berkas KTP jelas dan terkonfirmasi valid."></textarea>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" name="action" value="approve" class="btn btn-primary btn-sm" style="flex: 1;" onclick="return confirm('Verifikasi anggota ini dan terbitkan nomor anggota?')">Setujui Verifikasi</button>
                    <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm" style="flex: 1;" onclick="return confirm('Tolak pendaftaran anggota ini?')">Tolak</button>
                </div>
            </form>
        </div>
        @endif
    </div>

    {{-- Dokumen & Rekening Simpanan --}}
    <div style="grid-column: span 2;">
        {{-- Dokumen Verifikasi --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Dokumen Verifikasi Anggota</h3>
            </div>
            @if($member->documents->isEmpty())
                <p style="color: var(--gray); font-size: 0.85rem;">Tidak ada dokumen yang diunggah.</p>
            @else
                <div class="grid-2">
                    @foreach($member->documents as $doc)
                    <div style="border: 1px solid var(--border); border-radius: 8px; padding: 0.75rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                            <strong style="text-transform: uppercase; font-size: 0.8rem;">{{ $doc->document_type }}</strong>
                            <span class="badge badge-{{ $doc->status === 'approved' ? 'success' : ($doc->status === 'pending' ? 'warning' : 'danger') }}">{{ $doc->status }}</span>
                        </div>
                        <div style="background: #f8fafc; border-radius: 6px; padding: 0.5rem; text-align: center; margin-bottom: 0.5rem;">
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" style="font-size: 0.8rem; color: var(--primary); font-weight: 600; text-decoration: none;">
                                [Lihat Berkas: {{ $doc->original_name }}]
                            </a>
                        </div>
                        @if($doc->verified_at)
                            <div style="font-size: 0.7rem; color: var(--gray);">Diverifikasi pada: {{ $doc->verified_at->format('d/m/Y H:i') }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Saldo Simpanan --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Rekening Simpanan Anggota</h3>
            </div>
            <div class="grid-3" style="margin-bottom: 1rem;">
                @foreach($member->savingsAccounts as $sacc)
                <div class="stat-card" style="padding: 0.85rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span class="stat-label">Simpanan {{ ucfirst($sacc->type) }}</span>
                    </div>
                    <div style="font-size: 1.15rem; font-weight: 700;">Rp {{ number_format($sacc->balance, 0, ',', '.') }}</div>
                    <div style="margin-top: 0.5rem;">
                        <a href="{{ route('savings.deposit.form', $sacc->id) }}" class="btn btn-primary btn-sm" style="width: 100%;">Setor Kas/Transfer</a>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Fasilitas Pinjaman --}}
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Pinjaman Anggota</h3>
            </div>
            @if($member->loans->isEmpty())
                <p style="color: var(--gray); font-size: 0.85rem;">Anggota ini belum pernah memiliki pinjaman.</p>
            @else
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>No. Pinjaman</th>
                                <th>Produk</th>
                                <th>Plafon</th>
                                <th>Sisa Pokok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($member->loans as $l)
                            <tr>
                                <td><strong>{{ $l->loan_no }}</strong></td>
                                <td>{{ $l->loanProduct->name }}</td>
                                <td>Rp {{ number_format($l->principal_amount, 0, ',', '.') }}</td>
                                <td><strong style="color: var(--primary);">Rp {{ number_format($l->outstanding_principal, 0, ',', '.') }}</strong></td>
                                <td><span class="badge badge-{{ $l->status === 'active' ? 'success' : ($l->status === 'overdue' ? 'danger' : 'info') }}">{{ $l->status }}</span></td>
                                <td><a href="{{ route('loans.show', $l->id) }}" class="btn btn-secondary btn-sm">Lihat Detail</a></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
