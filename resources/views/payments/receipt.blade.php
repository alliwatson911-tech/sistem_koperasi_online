@extends('layouts.app')

@section('title', 'Kuitansi Resmi Pembayaran - ' . $payment->order_id)

@section('content')
<style>
    @media print {
        body { background: #fff !important; font-size: 12pt; }
        .no-print { display: none !important; }
        .kuitansi-container { border: none !important; box-shadow: none !important; padding: 0 !important; }
    }
</style>

<div class="max-w-2xl mx-auto py-8 px-4 sm:px-6">
    {{-- Action bar --}}
    <div class="no-print mb-4 flex items-center justify-between">
        <a href="{{ route('payments.invoice', $payment->order_id) }}" class="inline-flex items-center text-sm font-semibold text-emerald-700 hover:text-emerald-900">
            &larr; Kembali ke Rincian Tagihan
        </a>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="btn btn-primary btn-sm flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Kuitansi (PDF)
            </button>
        </div>
    </div>

    {{-- Official Receipt Document --}}
    <div class="kuitansi-container bg-white border border-slate-300 rounded-xl p-8 shadow-sm text-slate-800">
        {{-- Kop Resmi Koperasi --}}
        <div class="border-b-2 border-slate-900 pb-4 mb-6 flex items-start justify-between">
            <div>
                <h1 class="text-xl font-black text-slate-900 tracking-wide uppercase">KSP SAHABAT SEJAHTERA</h1>
                <p class="text-xs text-slate-600 mt-0.5">Koperasi Simpan Pinjam Tertutup Khusus Anggota</p>
                <p class="text-[11px] text-slate-500 font-mono">Badan Hukum: AHU-0012345.AH.01.26.TAHUN.2020 &bull; IUSP: 503/012/KSP/2020</p>
            </div>
            <div class="text-right">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Bukti Penerimaan Kas</div>
                <div class="text-sm font-mono font-bold text-emerald-800">KWT-{{ date('Ymd', strtotime($payment->paid_at ?: now())) }}-{{ substr(md5($payment->order_id), 0, 5) }}</div>
            </div>
        </div>

        {{-- Judul Kuitansi --}}
        <div class="text-center my-4">
            <h2 class="text-lg font-black uppercase tracking-wider text-slate-900">KUITANSI PEMBAYARAN ELEKTRONIK</h2>
            <div class="inline-block px-3 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 mt-1">
                STATUS: LUNAS &bull; DIVERIFIKASI SISTEM
            </div>
        </div>

        {{-- Data Transaksi & Pembayar --}}
        <div class="grid grid-cols-2 gap-4 text-xs my-6 border-y border-slate-200 py-4">
            <div>
                <table class="w-full">
                    <tr>
                        <td class="text-slate-500 py-1 w-28">Telah Diterima Dari</td>
                        <td class="font-bold text-slate-900 py-1">: {{ $payment->member->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-1">No. Anggota</td>
                        <td class="font-mono text-slate-900 py-1">: {{ $payment->member->member_no }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-1">No. Identitas (NIK)</td>
                        <td class="font-mono text-slate-900 py-1">: {{ $payment->member->nik }}</td>
                    </tr>
                </table>
            </div>
            <div>
                <table class="w-full">
                    <tr>
                        <td class="text-slate-500 py-1 w-28">Nomor Order / Ref</td>
                        <td class="font-mono font-bold text-slate-900 py-1">: {{ $payment->order_id }}</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-1">Tanggal & Waktu</td>
                        <td class="text-slate-900 py-1">: {{ $payment->paid_at?->format('d F Y, H:i:s') }} WITA</td>
                    </tr>
                    <tr>
                        <td class="text-slate-500 py-1">Metode Pembayaran</td>
                        <td class="text-slate-900 py-1">: {{ $payment->method_label }} (Gateway: {{ strtoupper($payment->gateway) }})</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Tabel Rincian Alokasi Dana --}}
        <div class="mb-6">
            <div class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Rincian Alokasi Dana Pembayaran:</div>
            <table class="w-full text-xs border border-slate-200 rounded">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 font-semibold border-b border-slate-200">
                        <th class="p-2 text-left">Komponen Pembayaran</th>
                        <th class="p-2 text-right">Alokasi Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @if($payment->transaction_type === 'loan_installment')
                        <tr>
                            <td class="p-2 text-slate-800">
                                <strong>Pokok Pinjaman</strong> (Mengurangi saldo piutang anggota)
                            </td>
                            <td class="p-2 text-right font-mono font-semibold text-slate-900">
                                Rp {{ number_format($payment->principal_portion, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td class="p-2 text-slate-800">
                                <strong>Bunga Pinjaman</strong> (Jasa anuitas / bunga berjalan)
                            </td>
                            <td class="p-2 text-right font-mono font-semibold text-slate-900">
                                Rp {{ number_format($payment->interest_portion, 0, ',', '.') }}
                            </td>
                        </tr>
                        @if($payment->penalty_portion > 0)
                        <tr>
                            <td class="p-2 text-rose-700">
                                <strong>Denda Keterlambatan</strong> (Pelunasan sanksi keterlambatan)
                            </td>
                            <td class="p-2 text-right font-mono font-semibold text-rose-700">
                                Rp {{ number_format($payment->penalty_portion, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                        @if($payment->overpayment_portion > 0)
                        <tr>
                            <td class="p-2 text-emerald-800">
                                <strong>Kelebihan Bayar</strong> (Otomatis disetorkan ke Simpanan Sukarela Anggota)
                            </td>
                            <td class="p-2 text-right font-mono font-semibold text-emerald-800">
                                Rp {{ number_format($payment->overpayment_portion, 0, ',', '.') }}
                            </td>
                        </tr>
                        @endif
                    @else
                        <tr>
                            <td class="p-2 text-slate-800">
                                <strong>Setoran Simpanan Sukarela</strong> (Rekening: {{ $payment->payable?->account_no }})
                            </td>
                            <td class="p-2 text-right font-mono font-semibold text-slate-900">
                                Rp {{ number_format($payment->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="bg-slate-50 border-t border-slate-200 font-bold text-sm">
                        <td class="p-2 text-slate-900">TOTAL PEMBAYARAN DITERIMA KOPERASI</td>
                        <td class="p-2 text-right font-mono text-emerald-900">
                            Rp {{ number_format($payment->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Terbilang & Tanda Tangan Digital --}}
        <div class="border-t border-slate-200 pt-4 grid grid-cols-2 gap-6 items-end text-xs">
            <div>
                <div class="text-slate-400 text-[11px] mb-1">Catatan Akuntansi:</div>
                <p class="text-slate-600 italic">
                    Dicatat pada Jurnal Koperasi No: <strong>{{ $payment->journal?->journal_no ?? 'AUTO-GATEWAY' }}</strong>.
                    Dokumen ini merupakan bukti pembayaran sah yang dihasilkan otomatis oleh sistem tanpa tanda tangan basah.
                </p>
            </div>
            <div class="text-center flex flex-col items-center">
                <div class="text-[10px] text-slate-500 mb-1">Verifikasi Digital KSP Sahabat Sejahtera</div>
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode(route('payments.receipt', $payment->order_id)) }}" 
                     alt="Validasi Digital Koperasi" class="w-20 h-20 mb-1 border border-slate-200 p-1">
                <div class="font-bold text-slate-900 text-xs">Sistem Informasi KSP</div>
                <div class="text-[10px] text-slate-500 font-mono">Keaslian Dokumen Terjamin</div>
            </div>
        </div>
    </div>
</div>
@endsection

