@extends('layouts.app')

@section('title', 'Pinjaman Saya')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight">Pinjaman & Pembiayaan Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Pantau status pengajuan online, penandatanganan akad digital, serta riwayat jadwal angsuran.</p>
        </div>
        <div>
            <a href="{{ route('loans.apply') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Ajukan Pinjaman Baru
            </a>
        </div>
    </div>

    <!-- Alert Khusus Bila Ada Pengajuan Membutuhkan Revisi (Needs Revision) -->
    @foreach($applications->where('status', 'needs_revision') as $revApp)
    <div class="rounded-2xl p-5 bg-orange-50 border border-orange-200 shadow-sm space-y-3">
        <div class="flex items-start gap-3">
            <div class="p-2.5 rounded-xl bg-orange-600 text-white shadow-sm mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
            <div class="space-y-1 flex-1">
                <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-orange-950">
                        Pengajuan #{{ $revApp->application_no }} Membutuhkan Perbaikan Dokumen
                    </h3>
                    <span class="text-xs font-bold px-2.5 py-0.5 rounded-full bg-orange-200 text-orange-900">
                        Perlu Revisi
                    </span>
                </div>
                <p class="text-xs text-orange-800">
                    Pengajuan pinjaman Anda sebesar Rp {{ number_format($revApp->amount) }} dikembalikan oleh Teller
                    @if($revApp->receivedBy)
                        <strong>{{ $revApp->receivedBy->name }} ({{ $revApp->receivedBy->employee_code_formatted }})</strong>
                    @endif
                    untuk dilengkapi.
                </p>
                <div class="p-3 rounded-xl bg-white/80 border border-orange-200 text-xs italic text-orange-950 mt-1">
                    "{{ $revApp->revision_notes }}"
                </div>

                <div class="pt-2">
                    <button type="button" onclick="document.getElementById('resubmit-form-{{ $revApp->id }}').classList.toggle('hidden')" class="px-3 py-1.5 rounded-lg bg-orange-600 hover:bg-orange-700 text-white text-xs font-bold shadow-sm transition">
                        Buka Formulir Perbaikan & Kirim Ulang Berkas
                    </button>
                </div>

                <!-- Form Kirim Ulang -->
                <div id="resubmit-form-{{ $revApp->id }}" class="hidden pt-3 border-t border-orange-200 mt-2">
                    <form action="{{ route('loans.applications.resubmit', $revApp->id) }}" method="POST" class="space-y-3 bg-white p-4 rounded-xl border border-orange-200">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Perbarui Keterangan Tujuan / Dokumen Pendukung</label>
                            <textarea name="notes" rows="2" required class="w-full text-xs border border-slate-300 rounded-lg p-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Saya sudah memperbarui data rekening dan foto KTP yang lebih jelas..."></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" onclick="document.getElementById('resubmit-form-{{ $revApp->id }}').classList.add('hidden')" class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-600 text-xs font-semibold">
                                Batal
                            </button>
                            <button type="submit" onclick="return confirm('Kirimkan kembali berkas pengajuan ini ke Teller?')" class="px-4 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow transition">
                                Kirimkan Kembali ke Teller
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    <!-- Pinjaman Aktif & Selesai -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-800">Fasilitas Pinjaman Terbuka</h2>
                <p class="text-xs text-slate-400">Daftar kredit pinjaman yang sedang berjalan maupun yang telah lunas</p>
            </div>
        </div>

        @if($loans->isEmpty())
            <div class="py-10 text-center text-slate-400">
                <svg class="w-10 h-10 mx-auto text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <p class="text-sm font-semibold text-slate-600">Anda belum memiliki fasilitas pinjaman yang disetujui.</p>
                <p class="text-xs text-slate-400 mt-0.5">Silakan ajukan pinjaman melalui tombol "+ Ajukan Pinjaman Baru".</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3 px-3">No. Pinjaman</th>
                            <th class="py-3 px-3">Produk</th>
                            <th class="py-3 px-3">Plafon Pokok</th>
                            <th class="py-3 px-3">Sisa Pokok</th>
                            <th class="py-3 px-3">Tenor</th>
                            <th class="py-3 px-3">Status</th>
                            <th class="py-3 px-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($loans as $loan)
                        @php
                            $loanBadge = match($loan->status) {
                                'active' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                'overdue' => 'bg-rose-100 text-rose-800 border-rose-300',
                                'paid_off' => 'bg-slate-100 text-slate-700 border-slate-300',
                                'approved_awaiting_disbursement' => 'bg-blue-100 text-blue-800 border-blue-300',
                                default => 'bg-slate-100 text-slate-700 border-slate-300',
                            };
                            $loanLabel = match($loan->status) {
                                'approved_awaiting_disbursement' => ($loan->agreed_at ? 'MENUNGGU PENCAIRAN TELLER' : 'MENUNGGU AKAD DIGITAL'),
                                'active' => 'AKTIF',
                                'overdue' => 'MENUNGGAK',
                                'paid_off' => 'LUNAS',
                                default => strtoupper($loan->status),
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3.5 px-3 font-mono font-bold text-slate-800">{{ $loan->loan_no }}</td>
                            <td class="py-3.5 px-3 font-semibold text-slate-700">{{ $loan->loanProduct->name }}</td>
                            <td class="py-3.5 px-3 font-mono">Rp {{ number_format($loan->principal_amount, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3 font-mono font-black text-emerald-700">Rp {{ number_format($loan->outstanding_principal, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3">{{ $loan->tenor_display }}</td>
                            <td class="py-3.5 px-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $loanBadge }}">
                                    {{ $loanLabel }}
                                </span>
                            </td>
                            <td class="py-3.5 px-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('loans.show', $loan->id) }}" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                        Detail & Akad
                                    </a>
                                    @if(in_array($loan->status, ['active', 'overdue']))
                                        <a href="{{ route('loans.pay', $loan->id) }}" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm transition">
                                            Bayar
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Riwayat Pengajuan Pinjaman Online -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h2 class="text-base font-bold text-slate-800">Riwayat Pengajuan Pinjaman Mandiri</h2>
                <p class="text-xs text-slate-400">Jejak seluruh berkas pengajuan online beserta tahapan penanganan</p>
            </div>
        </div>

        @if($applications->isEmpty())
            <div class="py-8 text-center text-slate-400 text-xs">
                Belum ada berkas pengajuan pinjaman online.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3 px-3">No. Pengajuan</th>
                            <th class="py-3 px-3">Tanggal</th>
                            <th class="py-3 px-3">Produk</th>
                            <th class="py-3 px-3">Plafon Diajukan</th>
                            <th class="py-3 px-3">Tenor</th>
                            <th class="py-3 px-3">Teller Penerima</th>
                            <th class="py-3 px-3">Status Pengajuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($applications as $app)
                        @php
                            $appBadge = match($app->status) {
                                'submitted' => 'bg-amber-100 text-amber-800 border-amber-300',
                                'received' => 'bg-teal-100 text-teal-800 border-teal-300',
                                'needs_revision' => 'bg-orange-100 text-orange-800 border-orange-300',
                                'in_review' => 'bg-blue-100 text-blue-800 border-blue-300',
                                'approved' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                                'rejected' => 'bg-rose-100 text-rose-800 border-rose-300',
                                default => 'bg-slate-100 text-slate-700 border-slate-300',
                            };

                            $appLabel = match($app->status) {
                                'submitted' => 'Antrean Teller',
                                'received' => 'Diterima Teller',
                                'needs_revision' => 'Perlu Revisi',
                                'in_review' => 'Ditinjau Analis',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                default => strtoupper($app->status),
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/75 transition">
                            <td class="py-3.5 px-3 font-mono font-bold text-slate-800">{{ $app->application_no }}</td>
                            <td class="py-3.5 px-3 text-slate-500">{{ $app->submitted_at ? $app->submitted_at->format('d/m/Y H:i') : $app->created_at->format('d/m/Y H:i') }}</td>
                            <td class="py-3.5 px-3 font-medium text-slate-700">{{ $app->loanProduct->name }}</td>
                            <td class="py-3.5 px-3 font-mono font-bold text-slate-800">Rp {{ number_format($app->amount, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-3">{{ $app->tenor_count }} {{ $app->tenor_unit_label }}</td>
                            <td class="py-3.5 px-3">
                                @if($app->receivedBy)
                                    <span class="inline-flex items-center gap-1 text-teal-800 font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-teal-500"></span>
                                        {{ $app->receivedBy->name }} ({{ $app->receivedBy->employee_code_formatted }})
                                    </span>
                                @elseif($app->assignedTo)
                                    <span class="text-slate-500">
                                        Ditugaskan: {{ $app->assignedTo->name }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Antrean Terbuka</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $appBadge }}">
                                    {{ $appLabel }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
