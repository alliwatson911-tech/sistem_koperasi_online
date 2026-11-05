@extends('layouts.app')

@section('title', 'Kalkulator Simulasi Pinjaman')

@section('content')
<div class="mb-6 pb-4 border-b border-slate-200/80">
    <div class="flex items-center gap-2 mb-1">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200/60">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            Kalkulator Finansial Transparan
        </span>
    </div>
    <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Simulasi Perhitungan Angsuran Pinjaman</h1>
    <p class="text-xs sm:text-sm text-slate-500">Hitung perkiraan angsuran bulanan, total bunga, dan rincian jadwal pinjaman secara adil dan terbuka.</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Form Input Parameter Simulasi (Kiri) -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-xs p-5 sticky top-24">
            <div class="flex items-center gap-2 mb-4 pb-3 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Parameter Pinjaman</h2>
                    <p class="text-2xs text-slate-400">Atur plafon dan tenor yang diinginkan</p>
                </div>
            </div>

            <form action="{{ route('loans.simulation') }}" method="GET" class="space-y-4">
                <div class="form-group">
                    <label for="sim_product_id" class="form-label">Pilih Produk Pinjaman <span class="text-rose-500">*</span></label>
                    <select name="product_id" id="sim_product_id" class="form-control text-xs" onchange="this.form.submit()" required>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}"
                                    {{ ($selectedProduct && $selectedProduct->id == $p->id) ? 'selected' : '' }}>
                                {{ $p->name }} ({{ $p->interest_rate_per_period }}%/{{ strtolower($p->tenor_unit_label) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="sim_amount" class="form-label">Nominal Plafon (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" id="sim_amount" name="amount" class="form-control text-xs"
                           value="{{ request('amount', ($selectedProduct && $selectedProduct->isDaily() ? 500000 : ($selectedProduct && $selectedProduct->isWeekly() ? 3000000 : 10000000))) }}"
                           required min="{{ $selectedProduct->min_amount ?? 100000 }}" max="{{ $selectedProduct->max_amount ?? 100000000 }}" step="{{ $selectedProduct && $selectedProduct->isDaily() ? 50000 : 250000 }}">
                    <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                        @if($selectedProduct && $selectedProduct->isDaily())
                            <button type="button" onclick="document.getElementById('sim_amount').value=100000" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">100 Rb</button>
                            <button type="button" onclick="document.getElementById('sim_amount').value=250000" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">250 Rb</button>
                            <button type="button" onclick="document.getElementById('sim_amount').value=500000" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">500 Rb</button>
                            <button type="button" onclick="document.getElementById('sim_amount').value=750000" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">750 Rb</button>
                            <button type="button" onclick="document.getElementById('sim_amount').value=1000000" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">1 Jt</button>
                        @elseif($selectedProduct && $selectedProduct->isWeekly())
                            <button type="button" onclick="document.getElementById('sim_amount').value=1000000" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">1 Jt</button>
                            <button type="button" onclick="document.getElementById('sim_amount').value=2500000" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">2.5 Jt</button>
                            <button type="button" onclick="document.getElementById('sim_amount').value=5000000" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">5 Jt</button>
                        @else
                            <button type="button" onclick="document.getElementById('sim_amount').value=5000000" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">5 Jt</button>
                            <button type="button" onclick="document.getElementById('sim_amount').value=10000000" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">10 Jt</button>
                            <button type="button" onclick="document.getElementById('sim_amount').value=25000000" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">25 Jt</button>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    @php
                        $unitLabel = $selectedProduct ? $selectedProduct->tenor_unit_label : 'Bulan';
                        $defaultTenor = $selectedProduct && $selectedProduct->isDaily() ? 14 : ($selectedProduct && $selectedProduct->isWeekly() ? 10 : 12);
                    @endphp
                    <label for="sim_tenor" class="form-label">Jangka Waktu / Tenor ({{ $unitLabel }}) <span class="text-rose-500">*</span></label>
                    <input type="number" id="sim_tenor" name="tenor" class="form-control text-xs"
                           value="{{ request('tenor', $defaultTenor) }}"
                           required min="{{ $selectedProduct->min_tenor ?? 1 }}" max="{{ $selectedProduct->max_tenor ?? 60 }}">
                    <div class="flex items-center gap-1.5 mt-1.5 flex-wrap">
                        @if($selectedProduct && $selectedProduct->isDaily())
                            <button type="button" onclick="document.getElementById('sim_tenor').value=7" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">7 Hari</button>
                            <button type="button" onclick="document.getElementById('sim_tenor').value=10" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">10 Hari</button>
                            <button type="button" onclick="document.getElementById('sim_tenor').value=14" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">14 Hari</button>
                            <button type="button" onclick="document.getElementById('sim_tenor').value=21" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">21 Hari</button>
                            <button type="button" onclick="document.getElementById('sim_tenor').value=30" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">30 Hari</button>
                        @elseif($selectedProduct && $selectedProduct->isWeekly())
                            <button type="button" onclick="document.getElementById('sim_tenor').value=4" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">4 Mgg</button>
                            <button type="button" onclick="document.getElementById('sim_tenor').value=8" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">8 Mgg</button>
                            <button type="button" onclick="document.getElementById('sim_tenor').value=10" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">10 Mgg</button>
                            <button type="button" onclick="document.getElementById('sim_tenor').value=16" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">16 Mgg</button>
                            <button type="button" onclick="document.getElementById('sim_tenor').value=24" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">24 Mgg</button>
                        @else
                            <button type="button" onclick="document.getElementById('sim_tenor').value=6" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">6 Bln</button>
                            <button type="button" onclick="document.getElementById('sim_tenor').value=12" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">12 Bln</button>
                            <button type="button" onclick="document.getElementById('sim_tenor').value=24" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">24 Bln</button>
                            <button type="button" onclick="document.getElementById('sim_tenor').value=36" class="text-3xs px-2 py-0.5 rounded bg-slate-100 text-slate-600 hover:bg-slate-200">36 Bln</button>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="sim_method" class="form-label">Metode Perhitungan Bunga</label>
                    <select name="calculation_method" id="sim_method" class="form-control text-xs">
                        <option value="annuity" {{ request('calculation_method', $selectedProduct->calculation_method ?? 'annuity') === 'annuity' ? 'selected' : '' }}>Anuitas (Angsuran Tetap Tiap Periode)</option>
                        <option value="effective" {{ request('calculation_method', $selectedProduct->calculation_method ?? '') === 'effective' ? 'selected' : '' }}>Efektif (Saldo Menurun - Adil)</option>
                        <option value="flat" {{ request('calculation_method', $selectedProduct->calculation_method ?? '') === 'flat' ? 'selected' : '' }}>Flat (Pokok & Bunga Tetap)</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary w-full py-2.5 text-xs font-bold shadow-2xs">
                    Hitung Simulasi Angsuran
                </button>
            </form>
        </div>
    </div>

    <!-- Hasil Perhitungan & Tabel Angsuran (Kanan) -->
    <div class="lg:col-span-2 space-y-6">
        @if($simulationResult)
            <!-- Ringkasan Biaya Simulasi -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs border-l-4 border-l-emerald-600">
                    <span class="text-2xs font-semibold text-slate-500 uppercase tracking-wider block">Angsuran {{ $simulationResult['tenor_unit_label'] }} Ke-1</span>
                    <div class="text-lg font-extrabold text-emerald-700 tracking-tight mt-1">
                        Rp {{ number_format($simulationResult['first_installment'] ?? $simulationResult['first_month_installment'], 0, ',', '.') }}
                    </div>
                    <span class="text-3xs text-slate-400 mt-1 block">{{ ucfirst($simulationResult['method']) }} ({{ $simulationResult['rate_per_period'] }}%/{{ strtolower($simulationResult['tenor_unit_label']) }})</span>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs border-l-4 border-l-sky-600">
                    <span class="text-2xs font-semibold text-slate-500 uppercase tracking-wider block">Total Bunga / Jasa</span>
                    <div class="text-lg font-extrabold text-slate-900 tracking-tight mt-1">
                        Rp {{ number_format($simulationResult['total_interest'], 0, ',', '.') }}
                    </div>
                    <span class="text-3xs text-slate-400 mt-1 block">Akumulasi jasa pinjaman</span>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs border-l-4 border-l-indigo-600">
                    <span class="text-2xs font-semibold text-slate-500 uppercase tracking-wider block">Total Biaya (Bunga + Adm)</span>
                    <div class="text-lg font-extrabold text-indigo-700 tracking-tight mt-1">
                        Rp {{ number_format($simulationResult['total_cost'], 0, ',', '.') }}
                    </div>
                    <span class="text-3xs text-slate-400 mt-1 block">Adm: Rp {{ number_format($simulationResult['admin_fee'], 0, ',', '.') }}</span>
                </div>

                <div class="bg-white rounded-2xl p-4 border border-slate-200 shadow-xs border-l-4 border-l-amber-600">
                    <span class="text-2xs font-semibold text-slate-500 uppercase tracking-wider block">Total Pengembalian</span>
                    <div class="text-lg font-extrabold text-slate-900 tracking-tight mt-1">
                        Rp {{ number_format($simulationResult['total_payable'], 0, ',', '.') }}
                    </div>
                    <span class="text-3xs text-slate-400 mt-1 block">Pokok + Semua Biaya</span>
                </div>
            </div>

            <!-- Kartu Transparansi Biaya Setara Per Tahun (APR & AER) -->
            <div class="bg-gradient-to-r from-emerald-950 via-slate-900 to-indigo-950 rounded-2xl p-5 text-white shadow-md border border-emerald-800/40">
                <div class="flex items-start justify-between gap-4 flex-col sm:flex-row">
                    <div>
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-3xs font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            Transparansi Finansial Perlindungan Anggota
                        </div>
                        <h3 class="text-base font-extrabold text-white tracking-tight">Perbandingan Biaya Setara Per Tahun (APR)</h3>
                        <p class="text-xs text-slate-300 mt-0.5">Memudahkan anggota membandingkan biaya riil produk mingguan vs bulanan secara adil dan terbuka.</p>
                    </div>
                    <div class="text-right sm:self-center bg-white/10 px-4 py-2 rounded-xl backdrop-blur-xs border border-white/10 shrink-0">
                        <span class="text-3xs uppercase tracking-wider text-emerald-300 font-bold block">Biaya Setara Tahunan (APR)</span>
                        <span class="text-2xl font-black text-white tracking-tight">{{ number_format($simulationResult['annual_equivalent_cost_rate'], 2, ',', '.') }}% <span class="text-xs font-normal text-slate-300">/ thn</span></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 pt-4 border-t border-white/10 text-xs">
                    <div>
                        <span class="text-3xs text-slate-400 uppercase font-semibold block">Bunga Nominal Tahunan</span>
                        <span class="text-sm font-bold text-white">{{ number_format($simulationResult['annual_nominal_rate'], 2, ',', '.') }}% / tahun</span>
                        <span class="text-3xs text-slate-400 block">({{ $simulationResult['rate_per_period'] }}% &times; {{ $simulationResult['periods_per_year'] }} periode)</span>
                    </div>
                    <div>
                        <span class="text-3xs text-slate-400 uppercase font-semibold block">Bunga Efektif Tahunan (AER)</span>
                        <span class="text-sm font-bold text-white">{{ number_format($simulationResult['annual_effective_rate'], 2, ',', '.') }}% / tahun</span>
                        <span class="text-3xs text-slate-400 block">Tingkat bunga tahunan berjenjang</span>
                    </div>
                    <div class="col-span-2 sm:col-span-1">
                        <span class="text-3xs text-slate-400 uppercase font-semibold block">Total Beban Finansial</span>
                        <span class="text-sm font-bold text-emerald-400">Rp {{ number_format($simulationResult['total_cost'], 0, ',', '.') }}</span>
                        <span class="text-3xs text-slate-400 block">Bunga Rp {{ number_format($simulationResult['total_interest'], 0, ',', '.') }} + Adm Rp {{ number_format($simulationResult['admin_fee'], 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Tabel Jadwal Simulasi -->
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-50/50">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">Jadwal Rincian Angsuran Tiap {{ $simulationResult['tenor_unit_label'] }}</h2>
                        <p class="text-2xs text-slate-500">Plafon Rp {{ number_format($simulationResult['principal'], 0, ',', '.') }} &bull; Tenor {{ count($simulationResult['schedules']) }} {{ $simulationResult['tenor_unit_label'] }} &bull; Jatuh tempo kalender aktual</p>
                    </div>
                    @auth
                        @if(auth()->user()->isMember())
                            <a href="{{ route('loans.apply', ['product_id' => $selectedProduct->id ?? '', 'amount' => $simulationResult['principal'], 'tenor' => count($simulationResult['schedules'])]) }}" class="btn btn-primary btn-sm text-xs font-bold shadow-2xs">
                                Ajukan Pinjaman Ini &rarr;
                            </a>
                        @endif
                    @endauth
                </div>

                <div class="table-responsive">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50/70 border-b border-slate-100 text-slate-500 font-semibold uppercase text-3xs">
                            <tr>
                                <th class="py-2.5 px-4">Periode</th>
                                <th class="py-2.5 px-3">Jatuh Tempo</th>
                                <th class="py-2.5 px-3">Angsuran Pokok</th>
                                <th class="py-2.5 px-3">Bunga / Jasa</th>
                                <th class="py-2.5 px-3">Total Angsuran</th>
                                <th class="py-2.5 px-4 text-right">Sisa Saldo Pokok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($simulationResult['schedules'] as $s)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="py-2.5 px-4 font-bold text-slate-800">{{ $s['period_label'] ?? ($simulationResult['tenor_unit_label'] . ' ' . $s['installment_no']) }}</td>
                                <td class="py-2.5 px-3 font-mono text-slate-600 text-2xs">{{ \Carbon\Carbon::parse($s['due_date'])->format('d/m/Y') }}</td>
                                <td class="py-2.5 px-3 font-mono text-slate-700">Rp {{ number_format($s['principal_amount'], 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 font-mono text-slate-700">Rp {{ number_format($s['interest_amount'], 0, ',', '.') }}</td>
                                <td class="py-2.5 px-3 font-mono font-bold text-emerald-700">Rp {{ number_format($s['total_amount'], 0, ',', '.') }}</td>
                                <td class="py-2.5 px-4 font-mono text-slate-600 text-right">Rp {{ number_format($s['remaining_principal'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-100/70 font-bold text-slate-900 border-t-2 border-slate-200">
                                <td class="py-3 px-4" colspan="2">TOTAL</td>
                                <td class="py-3 px-3 font-mono">Rp {{ number_format($simulationResult['principal'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 font-mono">Rp {{ number_format($simulationResult['total_interest'], 0, ',', '.') }}</td>
                                <td class="py-3 px-3 font-mono text-emerald-800">Rp {{ number_format($simulationResult['principal'] + $simulationResult['total_interest'], 0, ',', '.') }}</td>
                                <td class="py-3 px-4 text-right text-slate-400">-</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-slate-400 text-xs">
                Pilih produk pinjaman dan masukkan nominal untuk melihat rincian simulasi angsuran.
            </div>
        @endif
    </div>
</div>
@endsection
