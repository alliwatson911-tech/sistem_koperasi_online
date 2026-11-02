@extends('layouts.app')

@section('title', 'Setor Simpanan - ' . $account->account_no)

@section('content')
<div class="max-w-xl mx-auto py-6 px-4">
    <div class="mb-4">
        <a href="{{ route('savings.show', $account->id) }}" class="inline-flex items-center text-sm font-semibold text-emerald-700 hover:text-emerald-900">
            &larr; Kembali ke Rincian Rekening
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="p-6 bg-slate-50 border-b border-slate-200">
            <h1 class="text-xl font-black text-slate-900">Setoran Simpanan {{ ucfirst($account->type) }}</h1>
            <p class="text-xs text-slate-500 mt-1">
                No. Rekening: <strong class="text-slate-800">{{ $account->account_no }}</strong> &bull; Anggota: <strong class="text-slate-800">{{ $account->member->user->name }}</strong>
            </p>
        </div>

        <div class="p-6">
            <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 mb-6 flex justify-between items-center">
                <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Saldo Saat Ini:</span>
                <span class="text-lg font-black text-emerald-800">Rp {{ number_format($account->balance, 0, ',', '.') }}</span>
            </div>

            @php
                $pokokTarget = (int) \App\Models\Setting::get('simpanan_pokok_nominal', 500000);
                $wajibTarget = (int) \App\Models\Setting::get('simpanan_wajib_nominal', 50000);
                $suggested = old('amount');
                if (!$suggested) {
                    if ($account->type === 'pokok' && $account->balance < $pokokTarget) {
                        $suggested = $pokokTarget - $account->balance;
                    } elseif ($account->type === 'wajib') {
                        $suggested = $wajibTarget;
                    } else {
                        $suggested = 100000;
                    }
                }
            @endphp

            {{-- Tabs --}}
            <div class="flex rounded-lg bg-slate-100 p-1 mb-6 text-xs font-bold">
                <button type="button" id="tabGatewayBtn" onclick="switchTab('gateway')" class="flex-1 py-2 rounded-md bg-white text-emerald-800 shadow-sm transition">
                    ⚡ Payment Gateway (QRIS / VA)
                </button>
                <button type="button" id="tabManualBtn" onclick="switchTab('manual')" class="flex-1 py-2 rounded-md text-slate-600 hover:text-slate-900 transition">
                    🏢 Setor Teller / Transfer Manual
                </button>
            </div>

            {{-- Section 1: Gateway --}}
            <div id="sectionGateway">
                <form action="{{ route('savings.deposit.gateway', $account->id) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nominal Setoran (Rp) *</label>
                        <input type="number" name="amount" class="form-control text-sm w-full py-2 px-3 rounded-lg border-slate-300" value="{{ $suggested }}" required min="10000" step="5000">
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Saluran Pembayaran Gateway *</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-emerald-500 bg-white cursor-pointer transition">
                                <input type="radio" name="payment_method_type" value="qris" checked class="text-emerald-600">
                                <div>
                                    <div class="font-bold text-slate-800">QRIS Real-Time</div>
                                    <div class="text-[10px] text-slate-400">Scan via m-Banking/e-Wallet</div>
                                </div>
                            </label>

                            <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 hover:border-emerald-500 bg-white cursor-pointer transition">
                                <input type="radio" name="payment_method_type" value="va_bca" class="text-emerald-600">
                                <div>
                                    <div class="font-bold text-slate-800">BCA Virtual Account</div>
                                    <div class="text-[10px] text-slate-400">Verifikasi Otomatis</div>
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
                                    <div class="font-bold text-slate-800">BRI Virtual Account</div>
                                    <div class="text-[10px] text-slate-400">BRImo / ATM BRI</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Catatan Setoran (Opsional)</label>
                        <input type="text" name="notes" class="form-control text-xs w-full py-2 px-3 rounded-lg border-slate-300" placeholder="Contoh: Setoran tabungan harian anggota">
                    </div>

                    <button type="submit" class="btn btn-primary btn-md w-full py-2.5 font-bold shadow-sm">
                        Lanjut ke Pembayaran QRIS / VA &rarr;
                    </button>
                </form>
            </div>

            {{-- Section 2: Manual --}}
            <div id="sectionManual" class="hidden">
                <form action="{{ route('savings.deposit.submit', $account->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nominal Setoran (Rp) *</label>
                        <input type="number" name="amount" class="form-control text-sm w-full py-2 px-3 rounded-lg border-slate-300" value="{{ $suggested }}" required min="10000" step="5000">
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Metode Penyetoran *</label>
                        <select name="payment_method" class="form-control text-xs w-full py-2 px-3 rounded-lg border-slate-300" required>
                            <option value="cash">Setor Tunai ke Teller Kantor</option>
                            <option value="transfer">Transfer Bank Koperasi (Unggah Bukti)</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Bukti Transfer (Jika Transfer)</label>
                        <input type="file" name="proof_file" class="form-control text-xs w-full py-1.5 px-3 rounded-lg border-slate-300" accept="image/*,.pdf">
                    </div>

                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Keterangan / Catatan</label>
                        <input type="text" name="description" class="form-control text-xs w-full py-2 px-3 rounded-lg border-slate-300" placeholder="Contoh: Setoran Simpanan Sukarela">
                    </div>

                    <button type="submit" class="btn btn-secondary btn-md w-full py-2.5 font-bold" onclick="return confirm('Konfirmasi pencatatan setoran manual ini?')">
                        Simpan Setoran Manual
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
