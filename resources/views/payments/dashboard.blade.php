@extends('layouts.app')

@section('title', 'Dasbor Payment Gateway & Rekonsiliasi')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 border-b border-slate-200 mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Manajemen Payment Gateway</h1>
            <p class="text-xs text-slate-500 mt-1">
                Monitor transaksi digital, status penyelesaian kliring (1301), dan rekonsiliasi mutasi ke Bank Koperasi (1102).
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('payments.export') }}" class="btn btn-secondary btn-sm flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Ekspor CSV
            </a>
        </div>
    </div>

    {{-- Ringkasan Statistik Kartu --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="ksp-card bg-white p-4 rounded-xl border border-slate-200">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Diterima (Bruto)</div>
            <div class="text-lg font-black text-slate-900 mt-1">
                Rp {{ number_format($summary['total_gross'], 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-emerald-600 mt-0.5">Semua Transaksi Lunas</div>
        </div>

        <div class="ksp-card bg-white p-4 rounded-xl border border-slate-200">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Biaya Gateway (5103)</div>
            <div class="text-lg font-black text-rose-700 mt-1">
                Rp {{ number_format($summary['total_fee'], 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">MDR & Potongan Biaya</div>
        </div>

        <div class="ksp-card bg-emerald-50/60 p-4 rounded-xl border border-emerald-200">
            <div class="text-[11px] font-bold uppercase tracking-wider text-emerald-800">Dana dalam Penyelesaian (1301)</div>
            <div class="text-lg font-black text-emerald-900 mt-1">
                Rp {{ number_format($summary['unsettled_funds'], 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-emerald-700 mt-0.5">Belum Settle ke Bank</div>
        </div>

        <div class="ksp-card bg-white p-4 rounded-xl border border-slate-200">
            <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Terselesaikan ke Bank (1102)</div>
            <div class="text-lg font-black text-slate-900 mt-1">
                Rp {{ number_format($summary['total_settled'], 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">Sudah Masuk Rekening Bank</div>
        </div>

        <div class="ksp-card bg-amber-50/60 p-4 rounded-xl border border-amber-200">
            <div class="text-[11px] font-bold uppercase tracking-wider text-amber-800">Tagihan Menunggu</div>
            <div class="text-lg font-black text-amber-900 mt-1">
                {{ $summary['pending_count'] }}
            </div>
            <div class="text-[10px] text-amber-700 mt-0.5">Status Pending</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-xl border border-slate-200 mb-6 shadow-sm">
        <form method="GET" action="{{ route('payments.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Cari Order / Anggota</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="No. Order / Nama / VA" class="form-control text-xs w-full py-1.5 px-2.5 rounded-lg border-slate-300">
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Status Pembayaran</label>
                <select name="status" class="form-control text-xs w-full py-1.5 px-2.5 rounded-lg border-slate-300">
                    <option value="">Semua Status</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Lunas (Paid)</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Status Settlement</label>
                <select name="settlement" class="form-control text-xs w-full py-1.5 px-2.5 rounded-lg border-slate-300">
                    <option value="">Semua Settlement</option>
                    <option value="unsettled" {{ request('settlement') === 'unsettled' ? 'selected' : '' }}>Belum Settle (1301)</option>
                    <option value="settled" {{ request('settlement') === 'settled' ? 'selected' : '' }}>Sudah Settle (1102)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Saluran Pembayaran</label>
                <select name="method" class="form-control text-xs w-full py-1.5 px-2.5 rounded-lg border-slate-300">
                    <option value="">Semua Saluran</option>
                    <option value="qris" {{ request('method') === 'qris' ? 'selected' : '' }}>QRIS</option>
                    <option value="va_bca" {{ request('method') === 'va_bca' ? 'selected' : '' }}>BCA VA</option>
                    <option value="va_bni" {{ request('method') === 'va_bni' ? 'selected' : '' }}>BNI VA</option>
                    <option value="va_bri" {{ request('method') === 'va_bri' ? 'selected' : '' }}>BRI VA</option>
                    <option value="va_mandiri" {{ request('method') === 'va_mandiri' ? 'selected' : '' }}>Mandiri Bill</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-semibold text-slate-600 mb-1">Gateway</label>
                <select name="gateway" class="form-control text-xs w-full py-1.5 px-2.5 rounded-lg border-slate-300">
                    <option value="">Semua Gateway</option>
                    <option value="fake" {{ request('gateway') === 'fake' ? 'selected' : '' }}>Fake Driver</option>
                    <option value="midtrans" {{ request('gateway') === 'midtrans' ? 'selected' : '' }}>Midtrans</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="btn btn-primary btn-sm w-full py-2">Filter</button>
                <a href="{{ route('payments.dashboard') }}" class="btn btn-secondary btn-sm py-2">Reset</a>
            </div>
        </form>
    </div>

    {{-- Form Batch Settlement untuk Manager/Admin --}}
    @if(in_array(auth()->user()->role, ['manager', 'admin']))
    <form id="settlementForm" method="POST" action="{{ route('payments.settlement') }}" class="mb-6">
        @csrf
        <div class="bg-gradient-to-r from-emerald-900 to-teal-950 rounded-xl p-4 text-white shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center text-emerald-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold">Pencairan (Settlement) Kliring ke Rekening Bank</h3>
                    <p class="text-xs text-emerald-200/80">
                        Pilih transaksi lunas yang belum disettle di bawah, lalu proses pemindahbukuan ke Bank Koperasi (Akun 1102).
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                <div class="text-right">
                    <div class="text-[10px] text-emerald-200 uppercase font-semibold">Terpilih: <span id="selectedCount">0</span> transaksi</div>
                    <div class="text-sm font-black text-emerald-300">Neto: Rp <span id="selectedNetAmount">0</span></div>
                </div>
                <input type="hidden" name="bank_account_code" value="1102">
                <button type="submit" id="btnSettle" disabled class="btn btn-primary btn-sm bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed">
                    Proses Settlement
                </button>
            </div>
        </div>
    @endif

        {{-- Tabel Daftar Transaksi --}}
        <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-xs text-left">
                    <thead class="bg-slate-50 text-slate-600 font-semibold border-b border-slate-200 uppercase tracking-wider text-[11px]">
                        <tr>
                            @if(in_array(auth()->user()->role, ['manager', 'admin']))
                            <th class="p-3 w-8 text-center">
                                <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAll(this)" class="rounded border-slate-300 text-emerald-600">
                            </th>
                            @endif
                            <th class="p-3">Order ID / Tanggal</th>
                            <th class="p-3">Anggota</th>
                            <th class="p-3">Tipe / Saluran</th>
                            <th class="p-3 text-right">Nominal (Rp)</th>
                            <th class="p-3 text-right">Fee Gateway (Rp)</th>
                            <th class="p-3 text-center">Status</th>
                            <th class="p-3 text-center">Settlement</th>
                            <th class="p-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($payments as $p)
                        <tr class="hover:bg-slate-50/75 transition">
                            @if(in_array(auth()->user()->role, ['manager', 'admin']))
                            <td class="p-3 text-center">
                                @if($p->isPaid() && !$p->settlement_id)
                                    <input type="checkbox" name="payment_ids[]" value="{{ $p->id }}" 
                                           data-amount="{{ $p->amount }}" 
                                           data-fee="{{ $p->fee_amount }}" 
                                           onchange="updateSelectedSummary()" 
                                           class="settle-checkbox rounded border-slate-300 text-emerald-600">
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            @endif
                            <td class="p-3">
                                <a href="{{ route('payments.invoice', $p->order_id) }}" class="font-mono font-bold text-emerald-700 hover:underline">
                                    {{ $p->order_id }}
                                </a>
                                <div class="text-[10px] text-slate-400">{{ $p->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                            <td class="p-3">
                                <div class="font-bold text-slate-800">{{ $p->member->user->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">{{ $p->member->member_no ?? '-' }}</div>
                            </td>
                            <td class="p-3">
                                <div class="font-semibold text-slate-700">{{ $p->method_label }}</div>
                                <div class="text-[10px] text-slate-400 uppercase tracking-wider">
                                    {{ $p->transaction_type === 'loan_installment' ? 'Angsuran Pinjaman' : 'Simpanan Sukarela' }} &bull; {{ $p->gateway }}
                                </div>
                            </td>
                            <td class="p-3 text-right font-mono font-bold text-slate-900">
                                Rp {{ number_format($p->amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-right font-mono text-slate-600">
                                Rp {{ number_format($p->fee_amount, 0, ',', '.') }}
                            </td>
                            <td class="p-3 text-center">
                                {!! $p->status_badge !!}
                                @if($p->paid_at)
                                <div class="text-[10px] text-emerald-700 mt-0.5">{{ $p->paid_at->format('d/m/y H:i') }}</div>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                @if($p->settlement)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ $p->settlement->settlement_no }}
                                    </span>
                                @elseif($p->isPaid())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Kliring (1301)
                                    </span>
                                @else
                                    <span class="text-slate-400 text-[10px]">-</span>
                                @endif
                            </td>
                            <td class="p-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('payments.invoice', $p->order_id) }}" class="p-1 rounded text-slate-500 hover:text-emerald-700 hover:bg-slate-100" title="Buka Invoice">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    @if($p->isPaid())
                                    <a href="{{ route('payments.receipt', $p->order_id) }}" class="p-1 rounded text-slate-500 hover:text-emerald-700 hover:bg-slate-100" title="Kuitansi Resmi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </a>
                                    @else
                                    <form method="POST" action="{{ route('payments.reconcile', $p->id) }}" class="inline" onsubmit="return confirm('Cek status transaksi ke gateway sekarang?')">
                                        @csrf
                                        <button type="submit" class="p-1 rounded text-slate-500 hover:text-emerald-700 hover:bg-slate-100" title="Sinkronkan Status Gateway">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                Tidak ada transaksi pembayaran yang sesuai filter pencarian.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($payments->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $payments->links() }}
            </div>
            @endif
        </div>

    @if(in_array(auth()->user()->role, ['manager', 'admin']))
    </form>
    @endif

    {{-- Riwayat Batch Settlement Terbaru --}}
    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
        <h3 class="text-sm font-bold text-slate-800 uppercase tracking-wider mb-3">10 Riwayat Settlement Gateway ke Bank Terakhir</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left">
                <thead class="bg-slate-50 text-slate-500 uppercase font-semibold border-b border-slate-200">
                    <tr>
                        <th class="p-2.5">No. Settlement</th>
                        <th class="p-2.5">Tanggal Settle</th>
                        <th class="p-2.5 text-center">Jumlah Transaksi</th>
                        <th class="p-2.5 text-right">Bruto (Cr 1301)</th>
                        <th class="p-2.5 text-right">Biaya Gateway (Dr 5103)</th>
                        <th class="p-2.5 text-right">Neto Bank (Dr 1102)</th>
                        <th class="p-2.5">Eksekutor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($settlements as $s)
                    <tr>
                        <td class="p-2.5 font-mono font-bold text-slate-900">{{ $s->settlement_no }}</td>
                        <td class="p-2.5 text-slate-600">{{ $s->settled_at?->format('d/m/Y H:i') }}</td>
                        <td class="p-2.5 text-center font-semibold">{{ $s->total_transactions }}</td>
                        <td class="p-2.5 text-right font-mono text-slate-800">Rp {{ number_format($s->gross_amount, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right font-mono text-rose-700">Rp {{ number_format($s->fee_amount, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-right font-mono font-bold text-emerald-800">Rp {{ number_format($s->net_amount, 0, ',', '.') }}</td>
                        <td class="p-2.5 text-slate-600">{{ $s->creator->name ?? 'Sistem' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center text-slate-400">Belum ada riwayat batch settlement yang tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function updateSelectedSummary() {
        const checkboxes = document.querySelectorAll('.settle-checkbox:checked');
        let totalGross = 0;
        let totalFee = 0;

        checkboxes.forEach(cb => {
            totalGross += parseInt(cb.dataset.amount || 0);
            totalFee += parseInt(cb.dataset.fee || 0);
        });

        const net = Math.max(0, totalGross - totalFee);

        const countEl = document.getElementById('selectedCount');
        const netEl = document.getElementById('selectedNetAmount');
        const btnSettle = document.getElementById('btnSettle');

        if (countEl) countEl.innerText = checkboxes.length;
        if (netEl) netEl.innerText = new Intl.NumberFormat('id-ID').format(net);
        if (btnSettle) {
            btnSettle.disabled = checkboxes.length === 0;
        }
    }

    function toggleSelectAll(masterCb) {
        const checkboxes = document.querySelectorAll('.settle-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = masterCb.checked;
        });
        updateSelectedSummary();
    }
</script>
@endsection

