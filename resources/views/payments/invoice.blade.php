@extends('layouts.app')

@section('title', 'Tagihan Pembayaran - ' . $payment->order_id)

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    {{-- Breadcrumb & Back button --}}
    <div class="mb-4 flex items-center justify-between">
        @if($payment->transaction_type === 'loan_installment')
            <a href="{{ route('loans.show', $payment->payable_id) }}" class="inline-flex items-center text-sm font-semibold text-emerald-700 hover:text-emerald-900">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Rincian Pinjaman
            </a>
        @else
            <a href="{{ route('savings.show', $payment->payable_id) }}" class="inline-flex items-center text-sm font-semibold text-emerald-700 hover:text-emerald-900">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke Rekening Simpanan
            </a>
        @endif

        @if($payment->isPaid())
            <a href="{{ route('payments.receipt', $payment->order_id) }}" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Kuitansi Resmi
            </a>
        @endif
    </div>

    {{-- Main Invoice Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- Header Tagihan --}}
        <div class="p-6 sm:p-8 bg-gradient-to-r {{ $payment->isPaid() ? 'from-emerald-700 to-teal-800' : ($payment->isExpired() ? 'from-slate-700 to-slate-800' : 'from-emerald-800 to-emerald-950') }} text-white">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <span class="inline-block px-2.5 py-1 rounded-md text-xs font-bold tracking-wider uppercase {{ $payment->isPaid() ? 'bg-emerald-500/30 text-emerald-200' : 'bg-white/10 text-emerald-100' }}">
                        {{ $payment->transaction_type === 'loan_installment' ? 'Angsuran Pinjaman Koperasi' : 'Setoran Simpanan Sukarela' }}
                    </span>
                    <h1 class="text-2xl font-black mt-1">Invoice {{ $payment->order_id }}</h1>
                    <p class="text-xs text-emerald-100/80 mt-1">
                        Peminjam / Anggota: <strong class="text-white">{{ $payment->member->user->name }}</strong> ({{ $payment->member->member_no }})
                    </p>
                </div>
                <div class="text-left sm:text-right">
                    <div class="text-xs text-emerald-100/80">Total Tagihan</div>
                    <div class="text-3xl font-black tracking-tight text-white">
                        Rp {{ number_format($payment->gross_amount, 0, ',', '.') }}
                    </div>
                    <div class="mt-2">
                        {!! $payment->status_badge !!}
                    </div>
                </div>
            </div>
        </div>

        {{-- Countdown Timer jika status Pending --}}
        @if($payment->isPending())
        <div id="countdownBanner" class="bg-amber-50 border-b border-amber-200 px-6 py-3 flex items-center justify-between text-xs text-amber-900 font-medium">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-600 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                <span>Batas waktu pembayaran: <strong id="expiryTimeText">{{ $payment->expires_at->format('d M Y, H:i') }} WITA</strong></span>
            </div>
            <div class="font-mono text-amber-800 font-bold bg-amber-100 px-2.5 py-1 rounded">
                Sisa Waktu: <span id="countdownTimer">--:--:--</span>
            </div>
        </div>
        @endif

        {{-- Body Pembayaran: QRIS atau Virtual Account --}}
        <div class="p-6 sm:p-8">
            @if($payment->isPaid())
                <div class="text-center py-6">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <h2 class="text-xl font-bold text-slate-800">Pembayaran Berhasil Diverifikasi!</h2>
                    <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                        Dana telah diterima oleh sistem pada <strong>{{ $payment->paid_at?->format('d F Y, H:i:s') }} WITA</strong> dan telah dialokasikan secara atomik ke buku besar koperasi.
                    </p>
                    <div class="mt-6 flex flex-wrap justify-center gap-3">
                        <a href="{{ route('payments.receipt', $payment->order_id) }}" class="btn btn-primary btn-md">
                            Unduh / Cetak Kuitansi Resmi
                        </a>
                        @if($payment->transaction_type === 'loan_installment')
                            <a href="{{ route('loans.show', $payment->payable_id) }}" class="btn btn-secondary btn-md">
                                Lihat Rincian Pinjaman
                            </a>
                        @else
                            <a href="{{ route('savings.show', $payment->payable_id) }}" class="btn btn-secondary btn-md">
                                Lihat Saldo Simpanan
                            </a>
                        @endif
                    </div>
                </div>
            @elseif($payment->isExpired())
                <div class="text-center py-8">
                    <div class="w-14 h-14 bg-slate-100 text-slate-500 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-lg font-bold text-slate-800">Tagihan Telah Kedaluwarsa</h2>
                    <p class="text-sm text-slate-500 mt-1 max-w-md mx-auto">
                        Masa berlaku pembayaran telah berakhir pada {{ $payment->expires_at->format('d M Y H:i') }} WITA. Silakan buat instruksi pembayaran baru.
                    </p>
                    @if($payment->transaction_type === 'loan_installment')
                        <a href="{{ route('loans.pay', $payment->payable_id) }}" class="btn btn-primary btn-sm mt-4 inline-block">
                            Buat Pembayaran Baru
                        </a>
                    @endif
                </div>
            @else
                {{-- Mode Pending: Menampilkan QRIS atau Nomor VA --}}
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                    {{-- Sisi Kiri: Media Pembayaran (QRIS atau VA) --}}
                    <div class="md:col-span-6 bg-slate-50 border border-slate-200 rounded-xl p-5 text-center">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2">
                            Saluran Pembayaran
                        </div>
                        <h3 class="text-base font-bold text-slate-800 mb-4">{{ $payment->method_label }}</h3>

                        @if($payment->payment_method_type === 'qris')
                            {{-- Tampilan QRIS --}}
                            <div class="bg-white p-4 rounded-xl border border-slate-200 inline-block shadow-sm mb-3">
                                <img src="{{ $payment->qr_url ?: 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&data=' . urlencode($payment->qr_string) }}" 
                                     alt="QRIS KSP Sahabat Sejahtera" 
                                     class="w-48 h-48 mx-auto object-contain">
                            </div>
                            <div class="flex items-center justify-center gap-1.5 text-xs text-slate-500 mb-2">
                                <span class="font-semibold text-slate-700">QRIS Standar Bank Indonesia</span>
                            </div>
                            <p class="text-xs text-slate-500">
                                Buka aplikasi m-Banking (BCA, Mandiri, BRI, BNI) atau e-Wallet (GoPay, OVO, Dana, ShopeePay), lalu scan kode QR di atas.
                            </p>
                        @elseif(str_starts_with($payment->payment_method_type, 'va_'))
                            {{-- Tampilan Virtual Account --}}
                            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm mb-4">
                                <div class="text-xs text-slate-400 mb-1">Nomor Rekening Virtual Account</div>
                                <div class="font-mono text-2xl font-black text-emerald-800 tracking-wider select-all" id="vaNumberText">
                                    {{ $payment->va_number }}
                                </div>
                                <button type="button" onclick="copyVA()" class="mt-3 inline-flex items-center px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                    <span id="copyBtnText">Salin Nomor VA</span>
                                </button>
                            </div>
                            <p class="text-xs text-slate-500">
                                Transfer sesuai nominal tepat ke nomor Virtual Account di atas melalui ATM, Internet Banking, atau Mobile Banking.
                            </p>
                        @endif

                        {{-- Status Refresh Helper --}}
                        <div class="mt-5 pt-4 border-t border-slate-200 flex items-center justify-center gap-2 text-xs text-slate-500">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            <span>Menunggu deteksi otomatis dari gateway...</span>
                        </div>
                    </div>

                    {{-- Sisi Kanan: Rincian Alokasi Keuangan --}}
                    <div class="md:col-span-6 space-y-4">
                        <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                            <h4 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Rincian Pembayaran</h4>
                            <div class="space-y-2 text-xs">
                                <div class="flex justify-between text-slate-600">
                                    <span>Nominal Pokok Transaksi:</span>
                                    <span class="font-semibold text-slate-900">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                                </div>
                                @if($payment->fee_amount > 0)
                                <div class="flex justify-between text-slate-600">
                                    <span>Biaya Gateway ({{ $payment->fee_bearer === 'member' ? 'Dibebankan' : 'Ditanggung Koperasi' }}):</span>
                                    <span class="font-semibold text-slate-900">Rp {{ number_format($payment->fee_amount, 0, ',', '.') }}</span>
                                </div>
                                @endif
                                <div class="border-t border-slate-200 pt-2 flex justify-between text-sm font-bold text-emerald-800">
                                    <span>Total Yang Harus Dibayar:</span>
                                    <span>Rp {{ number_format($payment->gross_amount, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Panduan Pembayaran --}}
                        <div class="bg-white rounded-xl p-4 border border-slate-200 text-xs text-slate-600 space-y-2">
                            <h4 class="font-bold text-slate-700 uppercase tracking-wider">Petunjuk Penting</h4>
                            <ul class="list-disc pl-4 space-y-1">
                                <li>Pembayaran diverifikasi secara <strong>otomatis dan langsung (real-time)</strong> tanpa perlu konfirmasi manual.</li>
                                <li>Urutan alokasi otomatis keuangan pinjaman: <strong>Denda &rarr; Bunga &rarr; Pokok Pinjaman</strong>.</li>
                                <li>Bila terjadi kelebihan bayar, dana sisa otomatis dikreditkan ke <strong>Simpanan Sukarela Anda</strong>.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Polling Script & Countdown --}}
@if($payment->isPending())
<script>
    // 1. Countdown Timer
    const expiryTimestamp = new Date("{{ $payment->expires_at->toIso8601String() }}").getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const distance = expiryTimestamp - now;

        if (distance <= 0) {
            document.getElementById("countdownTimer").innerText = "Kedaluwarsa";
            clearInterval(timerInterval);
            window.location.reload();
            return;
        }

        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        document.getElementById("countdownTimer").innerText = 
            String(hours).padStart(2, '0') + ":" + 
            String(minutes).padStart(2, '0') + ":" + 
            String(seconds).padStart(2, '0');
    }

    const timerInterval = setInterval(updateCountdown, 1000);
    updateCountdown();

    // 2. Auto-polling Status Invoice setiap 3 detik
    const checkStatusUrl = "{{ route('payments.invoice.status', $payment->order_id) }}";
    const statusPolling = setInterval(async () => {
        try {
            const res = await fetch(checkStatusUrl);
            if (res.ok) {
                const data = await res.json();
                if (data.is_paid) {
                    clearInterval(statusPolling);
                    // Refresh halaman untuk menampilkan status Lunas
                    window.location.reload();
                } else if (data.is_expired) {
                    clearInterval(statusPolling);
                    window.location.reload();
                }
            }
        } catch (e) {
            console.error("Gagal memeriksa status pembayaran:", e);
        }
    }, 3000);

    // 3. Salin Nomor VA
    function copyVA() {
        const vaText = document.getElementById("vaNumberText").innerText.trim();
        navigator.clipboard.writeText(vaText).then(() => {
            const btn = document.getElementById("copyBtnText");
            btn.innerText = "Tersalin!";
            setTimeout(() => { btn.innerText = "Salin Nomor VA"; }, 2000);
        });
    }
</script>
@endif
@endsection

