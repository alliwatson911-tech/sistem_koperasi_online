@extends('layouts.app')

@section('title', 'Bayar Angsuran - ' . $loan->loan_no)

@section('content')
<div class="max-w-xl mx-auto py-6 px-4">
    <div class="mb-4">
        <a href="{{ route('loans.show', $loan->id) }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 border border-slate-200 text-xs font-bold transition-all shadow-2xs" title="Kembali ke halaman sebelumnya">
            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            <span>Kembali ke Halaman Sebelumnya</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <h1 class="text-xl font-black text-slate-900">Pembayaran Angsuran Pinjaman</h1>
            <p class="text-xs text-slate-500 mt-1">
                No. Pinjaman: <strong class="text-slate-800">{{ $loan->loan_no }}</strong> &bull; Peminjam: <strong class="text-slate-800">{{ $loan->member->user->name }}</strong>
            </p>
        </div>

        <div class="p-6">
            @if($nextSchedule)
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 text-xs">
                <div class="flex justify-between mb-1.5 text-slate-600">
                    <span>Angsuran Ke:</span>
                    <strong class="text-slate-900">{{ $loan->tenor_unit === 'week' ? 'Minggu' : 'Bulan' }} {{ $nextSchedule->installment_no }}</strong>
                </div>
                <div class="flex justify-between mb-1.5 text-slate-600">
                    <span>Jatuh Tempo:</span>
                    <strong class="{{ $nextSchedule->status === 'overdue' ? 'text-rose-600 font-bold' : 'text-slate-900' }}">
                        {{ $nextSchedule->due_date->format('d F Y') }}
                    </strong>
                </div>
                <div class="flex justify-between mb-1.5 text-slate-600">
                    <span>Pokok + Jasa Bunga:</span>
                    <strong class="text-slate-900">Rp {{ number_format($nextSchedule->total_amount, 0, ',', '.') }}</strong>
                </div>
                @if($nextSchedule->penalty_amount > 0)
                <div class="flex justify-between mb-1.5 text-rose-600 font-semibold">
                    <span>Denda Terlambat ({{ $nextSchedule->days_overdue }} hari):</span>
                    <span>Rp {{ number_format($nextSchedule->penalty_amount, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="border-t border-slate-200 pt-2 flex justify-between text-sm font-bold text-emerald-800">
                    <span>Total Tagihan Tertunggak:</span>
                    <span>Rp {{ number_format($nextSchedule->total_amount + $nextSchedule->penalty_amount - ($nextSchedule->paid_principal + $nextSchedule->paid_interest + $nextSchedule->paid_penalty), 0, ',', '.') }}</span>
                </div>
            </div>
            @endif

            @php
                $suggestedAmount = $nextSchedule
                    ? ($nextSchedule->total_amount + $nextSchedule->penalty_amount - ($nextSchedule->paid_principal + $nextSchedule->paid_interest + $nextSchedule->paid_penalty))
                    : 500000;
            @endphp

            {{-- Pilihan Mode Pembayaran (Tabs) --}}
            <div class="flex rounded-lg bg-slate-100 p-1 mb-6 text-xs font-bold">
                <button type="button" id="tabGatewayBtn" onclick="switchTab('gateway')" class="flex-1 py-2 rounded-md bg-white text-emerald-800 shadow-sm transition">
                    ⚡ Payment Gateway (Otomatis & Real-Time)
                </button>
                <button type="button" id="tabManualBtn" onclick="switchTab('manual')" class="flex-1 py-2 rounded-md text-slate-600 hover:text-slate-900 transition">
                    🏢 Transfer Manual / Teller
                </button>
            </div>

            {{-- 1. Form Pembayaran Gateway (QRIS, VA Bank) --}}
            <div id="sectionGateway">
                <form action="{{ route('loans.pay.gateway', $loan->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="schedule_ids[]" value="{{ $nextSchedule?->id }}">

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nominal Pembayaran (Rp) *</label>
                        <input type="number" name="amount" class="form-control text-sm w-full py-2 px-3 rounded-lg border-slate-300" value="{{ old('amount', $suggestedAmount) }}" required min="10000" step="5000">
                        <p class="text-[11px] text-slate-500 mt-1">
                            * Mendukung pembayaran sebagian atau pelunasan sekaligus. Alokasi otomatis: Denda &rarr; Bunga &rarr; Pokok Pinjaman. Kelebihan bayar otomatis dikreditkan ke Simpanan Sukarela Anda.
                        </p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Saluran Pembayaran *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-emerald-500 bg-white cursor-pointer transition">
                                <input type="radio" name="payment_method_type" value="qris" checked class="text-emerald-600">
                                <div>
                                    <div class="font-bold text-slate-800">QRIS Instant</div>
                                    <div class="text-[10px] text-slate-400">BCA, Livin, BRImo, GoPay, OVO</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-emerald-500 bg-white cursor-pointer transition">
                                <input type="radio" name="payment_method_type" value="va_bca" class="text-emerald-600">
                                <div>
                                    <div class="font-bold text-slate-800">BCA Virtual Account</div>
                                    <div class="text-[10px] text-slate-400">Otomatis Terverifikasi</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-emerald-500 bg-white cursor-pointer transition">
                                <input type="radio" name="payment_method_type" value="va_bni" class="text-emerald-600">
                                <div>
                                    <div class="font-bold text-slate-800">BNI Virtual Account</div>
                                    <div class="text-[10px] text-slate-400">ATM / BNI Mobile</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-emerald-500 bg-white cursor-pointer transition">
                                <input type="radio" name="payment_method_type" value="va_bri" class="text-emerald-600">
                                <div>
                                    <div class="font-bold text-slate-800">BRI Virtual Account (BRIVA)</div>
                                    <div class="text-[10px] text-slate-400">BRImo / ATM BRI</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-emerald-500 bg-white cursor-pointer transition">
                                <input type="radio" name="payment_method_type" value="va_mandiri" class="text-emerald-600">
                                <div>
                                    <div class="font-bold text-slate-800">Mandiri Bill / VA</div>
                                    <div class="text-[10px] text-slate-400">Livin by Mandiri</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-emerald-500 bg-white cursor-pointer transition">
                                <input type="radio" name="payment_method_type" value="gopay" class="text-emerald-600">
                                <div>
                                    <div class="font-bold text-slate-800">GoPay / E-Wallet</div>
                                    <div class="text-[10px] text-slate-400">Aplikasi GoPay</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan Tambahan (Opsional)</label>
                        <input type="text" name="notes" class="form-control text-xs w-full py-2 px-3 rounded-lg border-slate-300" placeholder="Contoh: Pembayaran cicilan minggu ke-2">
                    </div>

                    <button type="submit" class="btn btn-primary btn-md w-full py-2.5 font-bold shadow-sm">
                        Lanjut ke Pembayaran QRIS / VA &rarr;
                    </button>
                </form>
            </div>

            {{-- 2. Form Pembayaran Manual (Transfer Bukti / Kas Teller) --}}
            <div id="sectionManual" class="hidden">
                <form action="{{ route('loans.pay.submit', $loan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nominal Pembayaran (Rp) *</label>
                        <input type="number" name="amount" class="form-control text-sm w-full py-2 px-3 rounded-lg border-slate-300" value="{{ old('amount', $suggestedAmount) }}" required min="10000" step="5000">
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Metode Manual *</label>
                        <select name="payment_method" class="form-control text-xs w-full py-2 px-3 rounded-lg border-slate-300" required>
                            <option value="transfer" {{ old('payment_method') === 'transfer' ? 'selected' : '' }}>Transfer Rekening Bank Koperasi (Unggah Bukti)</option>
                            <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Setor Tunai Langsung ke Teller Kantor</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Unggah Bukti Transfer (Format Gambar / PDF)</label>
                        <input type="file" name="proof_file" class="form-control text-xs w-full py-1.5 px-3 rounded-lg border-slate-300" accept="image/*,.pdf">
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan Tambahan</label>
                        <input type="text" name="notes" class="form-control text-xs w-full py-2 px-3 rounded-lg border-slate-300" placeholder="Contoh: Transfer via ATM BCA ke rek koperasi">
                    </div>

                    <button type="submit" class="btn btn-secondary btn-md w-full py-2.5 font-bold" onclick="return confirm('Konfirmasi pencatatan pembayaran manual ini?')">
                        Kirim Bukti Pembayaran Manual
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(mode) {
        const secGateway = document.getElementById('sectionGateway');
        const secManual = document.getElementById('sectionManual');
        const btnGw = document.getElementById('tabGatewayBtn');
        const btnMan = document.getElementById('tabManualBtn');

        if (mode === 'gateway') {
            secGateway.classList.remove('hidden');
            secManual.classList.add('hidden');
            btnGw.className = 'flex-1 py-2 rounded-md bg-white text-emerald-800 shadow-sm transition';
            btnMan.className = 'flex-1 py-2 rounded-md text-slate-600 hover:text-slate-900 transition';
        } else {
            secGateway.classList.add('hidden');
            secManual.classList.remove('hidden');
            btnGw.className = 'flex-1 py-2 rounded-md text-slate-600 hover:text-slate-900 transition';
            btnMan.className = 'flex-1 py-2 rounded-md bg-white text-emerald-800 shadow-sm transition';
        }
    }
</script>
@endsection
