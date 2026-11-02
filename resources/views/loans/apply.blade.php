@extends('layouts.app')

@section('title', 'Formulir Pengajuan Pinjaman')

@section('content')
<div style="max-width: 650px; margin: 1.5rem auto;">
    <div class="card">
        <div class="card-header">
            <div>
                <h1 class="card-title">Pengajuan Pinjaman Koperasi Online</h1>
                <p style="font-size: 0.8rem; color: var(--gray);">Khusus anggota aktif KSP Sahabat Sejahtera. Proses analisis 1-2 hari kerja.</p>
            </div>
        </div>

        <form action="{{ route('loans.apply.submit') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Pilih Produk Pinjaman *</label>
                <select name="loan_product_id" id="apply_product_id" class="form-control" required onchange="updateProductSpecs(this)">
                    @foreach($products as $p)
                        <option value="{{ $p->id }}"
                                data-unit="{{ $p->tenor_unit_label }}"
                                data-min-tenor="{{ $p->min_tenor }}"
                                data-max-tenor="{{ $p->max_tenor }}"
                                data-min-amount="{{ $p->min_amount }}"
                                data-max-amount="{{ $p->max_amount }}"
                                data-rate="{{ $p->interest_rate_per_period }}"
                                {{ (old('loan_product_id', $selectedProductId) == $p->id) ? 'selected' : '' }}>
                            {{ $p->name }} &bull; Bunga: {{ $p->interest_rate_per_period }}%/{{ strtolower($p->tenor_unit_label) }} (Tenor {{ $p->min_tenor }}-{{ $p->max_tenor }} {{ $p->tenor_unit_label }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label" id="label_amount">Nominal Pengajuan (Rp) *</label>
                    <input type="number" id="input_amount" name="amount" class="form-control" value="{{ old('amount', request('amount', 5000000)) }}" required min="500000" step="250000">
                    <span class="form-text" id="help_amount">Plafon pinjaman sesuai produk pilihan</span>
                </div>
                <div class="form-group">
                    <label class="form-label" id="label_tenor">Tenor Pinjaman *</label>
                    <input type="number" id="input_tenor" name="tenor_count" class="form-control" value="{{ old('tenor_count', old('tenor_months', request('tenor', 12))) }}" required min="1" max="60">
                    <span class="form-text" id="help_tenor">Jangka waktu pengembalian pinjaman</span>
                </div>
            </div>

            <script>
                function updateProductSpecs(select) {
                    const opt = select.options[select.selectedIndex];
                    const unit = opt.getAttribute('data-unit') || 'Bulan';
                    const minTenor = opt.getAttribute('data-min-tenor') || 1;
                    const maxTenor = opt.getAttribute('data-max-tenor') || 60;
                    const minAmount = opt.getAttribute('data-min-amount') || 500000;
                    const maxAmount = opt.getAttribute('data-max-amount') || 50000000;

                    document.getElementById('label_tenor').innerText = 'Tenor Pinjaman (' + unit + ') *';
                    document.getElementById('help_tenor').innerText = 'Jangka waktu: ' + minTenor + ' s/d ' + maxTenor + ' ' + unit;
                    document.getElementById('input_tenor').min = minTenor;
                    document.getElementById('input_tenor').max = maxTenor;

                    document.getElementById('input_amount').min = minAmount;
                    document.getElementById('input_amount').max = maxAmount;
                    document.getElementById('help_amount').innerText = 'Plafon: Rp ' + Number(minAmount).toLocaleString('id-ID') + ' s/d Rp ' + Number(maxAmount).toLocaleString('id-ID');
                }
                document.addEventListener('DOMContentLoaded', function() {
                    const select = document.getElementById('apply_product_id');
                    if (select) updateProductSpecs(select);
                });
            </script>

            <div class="form-group">
                <label class="form-label">Tujuan Penggunaan Dana Pinjaman *</label>
                <textarea name="purpose" rows="3" class="form-control" required placeholder="Jelaskan secara singkat tujuan penggunaan pinjaman (misal: penambahan modal bahan baku, pembelian peralatan usaha, biaya pendidikan anak)">{{ old('purpose') }}</textarea>
            </div>

            <div class="card" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 0.8rem; color: #475569; margin: 1.25rem 0;">
                <p><strong>Prinsip Transparansi & Perlindungan Anggota:</strong></p>
                <ul style="margin-left: 1.25rem; margin-top: 0.35rem;">
                    <li>Tidak ada biaya tersembunyi. Biaya administrasi & bunga tertera jelas di simulasi dan akad.</li>
                    <li>Koperasi <strong>tidak pernah</strong> mengakses kontak, galeri, atau data perangkat pribadi anggota.</li>
                    <li>Pengingat jatuh tempo hanya disampaikan lewat notifikasi sistem dan WhatsApp resmi dengan bahasa santun.</li>
                    <li>Setelah disetujui Manajer, dana baru dicairkan setelah anggota membaca dan menandatangani akad digital.</li>
                </ul>
            </div>

            <div class="form-group" style="margin-bottom: 1.25rem;">
                <label style="display: flex; align-items: flex-start; gap: 0.5rem; font-size: 0.825rem; cursor: pointer;">
                    <input type="checkbox" name="agree_terms" value="1" required style="margin-top: 0.2rem;">
                    <span>Saya menyatakan data ini benar, bersedia mematuhi AD/ART Koperasi, dan menyetujui seluruh ketentuan pinjaman tanpa paksaan.</span>
                </label>
                @error('agree_terms') <div style="color:var(--danger); font-size:0.75rem;">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.95rem;">Kirim Pengajuan Pinjaman</button>
        </form>
    </div>
</div>
@endsection
