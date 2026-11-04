@extends('layouts.app')

@section('title', 'Formulir Pengajuan Pinjaman')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4">
    <div class="card shadow-sm border border-slate-200">
        <div class="card-header pb-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h1 class="card-title text-xl font-bold text-slate-900">Pengajuan Pinjaman Koperasi Online</h1>
                <p class="text-xs text-slate-500 mt-0.5">Khusus anggota aktif KSP Sahabat Sejahtera &bull; Layanan Cepat, Transparan, & Melayani.</p>
            </div>
            <span class="badge badge-success text-xs font-semibold px-2.5 py-1">Anggota Aktif</span>
        </div>

        <form action="{{ route('loans.apply.submit') }}" method="POST" enctype="multipart/form-data" class="pt-4 space-y-5" id="loanApplyForm">
            @csrf

            {{-- 1. PILIH PRODUK PINJAMAN --}}
            <div class="form-group">
                <label for="apply_product_id" class="form-label font-bold text-slate-700">Pilih Produk Pinjaman <span class="text-red-500" aria-hidden="true">*</span></label>
                <select name="loan_product_id" id="apply_product_id" class="form-control focus:ring-2 focus:ring-emerald-700 focus:border-emerald-700" required onchange="updateProductSpecs(this)">
                    @foreach($products as $p)
                        <option value="{{ $p->id }}"
                                data-code="{{ $p->code }}"
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
                <p class="form-text text-xs text-slate-500 mt-1">Pilih skema pinjaman yang paling sesuai dengan kebutuhan perputaran dana Anda.</p>
            </div>

            {{-- 2. NOMINAL & TENOR --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="input_amount" class="form-label font-bold text-slate-700" id="label_amount">Nominal Pengajuan (Rp) <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="number" id="input_amount" name="amount" class="form-control focus:ring-2 focus:ring-emerald-700" value="{{ old('amount', request('amount', 5000000)) }}" required min="500000" step="100000" aria-describedby="help_amount">
                    <span class="form-text text-xs text-slate-500 mt-1" id="help_amount">Plafon pinjaman sesuai produk pilihan</span>
                </div>
                <div class="form-group">
                    <label for="input_tenor" class="form-label font-bold text-slate-700" id="label_tenor">Tenor Pinjaman <span class="text-red-500" aria-hidden="true">*</span></label>
                    <input type="number" id="input_tenor" name="tenor_count" class="form-control focus:ring-2 focus:ring-emerald-700" value="{{ old('tenor_count', old('tenor_months', request('tenor', 12))) }}" required min="1" max="60" aria-describedby="help_tenor">
                    <span class="form-text text-xs text-slate-500 mt-1" id="help_tenor">Jangka waktu pengembalian pinjaman</span>
                </div>
            </div>

            {{-- 3. TUJUAN PENGGUNAAN --}}
            <div class="form-group">
                <label for="input_purpose" class="form-label font-bold text-slate-700">Tujuan Penggunaan Dana Pinjaman <span class="text-red-500" aria-hidden="true">*</span></label>
                <textarea id="input_purpose" name="purpose" rows="3" class="form-control focus:ring-2 focus:ring-emerald-700" required placeholder="Jelaskan rencana penggunaan pinjaman (contoh: Penambahan stok bahan dagang warung sembako, peremajaan etalase toko, atau ekspansi alat produksi)">{{ old('purpose') }}</textarea>
            </div>

            {{-- 4. BAGIAN FOTO USAHA & PROFIL USAHA (MODAL USAHA / UMKM) --}}
            <fieldset id="businessInfoSection" class="border border-emerald-200 rounded-xl p-4 bg-emerald-50/40 space-y-4">
                <legend class="text-sm font-bold text-emerald-800 px-2 bg-emerald-100 rounded-md py-0.5 inline-flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                    Data & Foto Tempat Usaha (Khusus Pinjaman Modal Usaha / Produktif)
                </legend>

                <p class="text-xs text-slate-600">
                    Koperasi mewajibkan verifikasi fisik usaha untuk pinjaman produktif modal kerja. Harap lengkapi profil usaha dan ambil/unggah foto tempat usaha Anda yang aktif beroperasi.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="business_name" class="form-label text-xs font-semibold text-slate-700">Nama Usaha / Toko / Lapak</label>
                        <input type="text" id="business_name" name="business_name" class="form-control text-sm" value="{{ old('business_name') }}" placeholder="Contoh: Toko Berkah Sembako">
                    </div>
                    <div class="form-group">
                        <label for="business_type" class="form-label text-xs font-semibold text-slate-700">Bidang / Jenis Usaha</label>
                        <select id="business_type" name="business_type" class="form-control text-sm">
                            <option value="">Pilih Jenis Usaha...</option>
                            <option value="Perdagangan / Retail / Sembako" {{ old('business_type') === 'Perdagangan / Retail / Sembako' ? 'selected' : '' }}>Perdagangan / Retail / Sembako</option>
                            <option value="Kuliner / Rumah Makan / Katering" {{ old('business_type') === 'Kuliner / Rumah Makan / Katering' ? 'selected' : '' }}>Kuliner / Rumah Makan / Katering</option>
                            <option value="Jasa / Bengkel / Salon / Laundry" {{ old('business_type') === 'Jasa / Bengkel / Salon / Laundry' ? 'selected' : '' }}>Jasa / Bengkel / Salon / Laundry</option>
                            <option value="Pertanian / Perkebunan / Peternakan" {{ old('business_type') === 'Pertanian / Perkebunan / Peternakan' ? 'selected' : '' }}>Pertanian / Perkebunan / Peternakan</option>
                            <option value="Kerajinan / Konveksi / Industri Rumah Tangga" {{ old('business_type') === 'Kerajinan / Konveksi / Industri Rumah Tangga' ? 'selected' : '' }}>Kerajinan / Konveksi / Industri Rumah Tangga</option>
                            <option value="Lainnya" {{ old('business_type') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="business_duration_years" class="form-label text-xs font-semibold text-slate-700">Lama Usaha Berjalan (Tahun)</label>
                        <input type="number" id="business_duration_years" name="business_duration_years" min="0" max="80" class="form-control text-sm" value="{{ old('business_duration_years', 2) }}" placeholder="Contoh: 3">
                    </div>
                    <div class="form-group">
                        <label for="business_monthly_revenue" class="form-label text-xs font-semibold text-slate-700">Estimasi Omset / Omzet per Bulan (Rp)</label>
                        <input type="number" id="business_monthly_revenue" name="business_monthly_revenue" min="0" step="500000" class="form-control text-sm" value="{{ old('business_monthly_revenue') }}" placeholder="Contoh: 15000000">
                    </div>
                </div>

                <div class="form-group">
                    <label for="business_address" class="form-label text-xs font-semibold text-slate-700">Alamat Lengkap Lokasi Usaha</label>
                    <textarea id="business_address" name="business_address" rows="2" class="form-control text-sm" placeholder="Contoh: Pasar Segar Kios No. 14, Jl. Pengayoman, Makassar">{{ old('business_address') }}</textarea>
                </div>

                {{-- WIDGET PENGAMBILAN FOTO USAHA (KAMERA INTERAKTIF + UPLOAD) --}}
                <div class="bg-white rounded-xl border border-slate-200 p-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Foto Tempat Usaha / Kegiatan Usaha</span>
                        <div class="inline-flex rounded-lg border border-slate-200 p-0.5 bg-slate-50 text-xs">
                            <button type="button" id="tabUploadBtn" onclick="switchPhotoTab('upload')" class="px-3 py-1 font-semibold rounded-md bg-white text-emerald-800 shadow-xs transition">Unggah Berkas</button>
                            <button type="button" id="tabCameraBtn" onclick="switchPhotoTab('camera')" class="px-3 py-1 font-semibold rounded-md text-slate-600 hover:text-slate-900 transition">Ambil dari Kamera</button>
                        </div>
                    </div>

                    {{-- Mode 1: Unggah Berkas --}}
                    <div id="modeUploadContainer" class="space-y-2">
                        <label for="business_photo_input" class="sr-only">Pilih File Foto Usaha</label>
                        <input type="file" id="business_photo_input" name="business_photo" accept="image/jpeg,image/png,image/jpg,image/webp" class="form-control text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-800 hover:file:bg-emerald-200" onchange="previewUploadImage(this)">
                        <span class="text-[11px] text-slate-500 block">Format: JPG, PNG, atau WebP. Ukuran maksimal 5MB.</span>
                        <div id="uploadPreviewWrapper" class="hidden mt-2">
                            <img id="uploadPreviewImg" src="" alt="Pratinjau Foto Usaha" class="h-36 rounded-lg object-cover border border-slate-200 shadow-sm">
                        </div>
                    </div>

                    {{-- Mode 2: Kamera Interaktif --}}
                    <div id="modeCameraContainer" class="hidden space-y-3">
                        <div id="cameraStatusText" aria-live="polite" class="text-xs text-slate-500">
                            Klik tombol "Buka Kamera" untuk mengaktifkan webcam atau kamera ponsel Anda.
                        </div>

                        <div class="flex gap-2">
                            <button type="button" id="openCameraBtn" onclick="startCameraStream()" class="btn btn-secondary btn-sm text-xs flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Buka Kamera
                            </button>
                            <button type="button" id="snapPhotoBtn" onclick="takeSnapshot()" class="hidden btn btn-primary btn-sm text-xs flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Ambil Foto (Snap)
                            </button>
                            <button type="button" id="retakePhotoBtn" onclick="retakeSnapshot()" class="hidden btn btn-secondary btn-sm text-xs">
                                Ambil Ulang
                            </button>
                            <button type="button" id="closeCameraBtn" onclick="stopCameraStream()" class="hidden btn btn-secondary btn-sm text-xs text-red-600">
                                Tutup Kamera
                            </button>
                        </div>

                        {{-- Video Stream Box --}}
                        <div id="videoStreamWrapper" class="hidden relative rounded-xl overflow-hidden bg-slate-900 border border-slate-800 max-w-sm">
                            <video id="cameraVideo" autoplay playsinline class="w-full h-48 object-cover"></video>
                            <div class="absolute inset-x-0 bottom-2 text-center text-[11px] text-white/80 bg-black/40 py-1">
                                Pastikan lokasi dan plang/aktivitas usaha tampak jelas
                            </div>
                        </div>

                        {{-- Hidden Canvas & Result Preview --}}
                        <canvas id="cameraCanvas" class="hidden"></canvas>
                        <input type="hidden" name="business_photo_camera" id="businessPhotoCameraInput">
                        
                        <div id="cameraPreviewWrapper" class="hidden mt-2">
                            <span class="text-xs font-semibold text-emerald-800 block mb-1">Foto Berhasil Diambil:</span>
                            <img id="cameraResultImg" src="" alt="Hasil Foto Kamera Usaha" class="h-40 rounded-lg object-cover border border-emerald-500 shadow-sm">
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- 5. KLAUSUL TRANSPARANSI --}}
            <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-xs text-slate-600 space-y-1.5">
                <p class="font-bold text-slate-800">Prinsip Transparansi & Perlindungan Anggota:</p>
                <ul class="list-disc list-inside space-y-1 text-slate-600">
                    <li>Tidak ada biaya tersembunyi. Biaya administrasi & bunga tertera jujur di akad dan simulasi.</li>
                    <li>Koperasi <strong>tidak pernah</strong> mengakses kontak, log panggilan, atau data pribadi anggota.</li>
                    <li>Verifikasi tempat usaha dilakukan secara santun dan profesional oleh Teller / Analis resmi.</li>
                    <li>Setelah persetujuan Manajer, dana pinjaman dicairkan setelah anggota menandatangani akad digital.</li>
                </ul>
            </div>

            {{-- 6. PERSETUJUAN --}}
            <div class="form-group pt-1">
                <label class="flex items-start gap-2.5 text-xs text-slate-700 cursor-pointer">
                    <input type="checkbox" name="agree_terms" value="1" required class="mt-0.5 rounded text-emerald-700 focus:ring-emerald-700 w-4 h-4" {{ old('agree_terms') ? 'checked' : '' }}>
                    <span>Saya menyatakan data yang saya isi adalah benar, bersedia mematuhi AD/ART Koperasi, dan menyetujui seluruh ketentuan pinjaman tanpa paksaan.</span>
                </label>
                @error('agree_terms') <div class="text-red-600 text-xs mt-1 font-semibold">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn-primary w-full py-3 text-sm font-bold shadow-md hover:shadow-lg transition">
                Kirim Pengajuan Pinjaman Koperasi
            </button>
        </form>
    </div>
</div>

{{-- SCRIPT INTERAKTIF KAMERA & UPDATE SPEK PRODUK --}}
<script>
    let localStream = null;

    function updateProductSpecs(select) {
        const opt = select.options[select.selectedIndex];
        const code = opt.getAttribute('data-code') || '';
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

        // Otomatis highlight jika produk modal usaha
        const bSection = document.getElementById('businessInfoSection');
        if (code === 'PRD-MDR' || code === 'PRD-MGN') {
            bSection.classList.add('ring-2', 'ring-emerald-500');
        } else {
            bSection.classList.remove('ring-2', 'ring-emerald-500');
        }
    }

    function switchPhotoTab(mode) {
        const upBtn = document.getElementById('tabUploadBtn');
        const camBtn = document.getElementById('tabCameraBtn');
        const upContainer = document.getElementById('modeUploadContainer');
        const camContainer = document.getElementById('modeCameraContainer');

        if (mode === 'upload') {
            upBtn.classList.add('bg-white', 'text-emerald-800', 'shadow-xs');
            upBtn.classList.remove('text-slate-600');
            camBtn.classList.remove('bg-white', 'text-emerald-800', 'shadow-xs');
            camBtn.classList.add('text-slate-600');

            upContainer.classList.remove('hidden');
            camContainer.classList.add('hidden');
            stopCameraStream();
        } else {
            camBtn.classList.add('bg-white', 'text-emerald-800', 'shadow-xs');
            camBtn.classList.remove('text-slate-600');
            upBtn.classList.remove('bg-white', 'text-emerald-800', 'shadow-xs');
            upBtn.classList.add('text-slate-600');

            camContainer.classList.remove('hidden');
            upContainer.classList.add('hidden');
        }
    }

    function previewUploadImage(input) {
        const previewWrapper = document.getElementById('uploadPreviewWrapper');
        const previewImg = document.getElementById('uploadPreviewImg');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewWrapper.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    async function startCameraStream() {
        const video = document.getElementById('cameraVideo');
        const videoWrapper = document.getElementById('videoStreamWrapper');
        const openBtn = document.getElementById('openCameraBtn');
        const snapBtn = document.getElementById('snapPhotoBtn');
        const closeBtn = document.getElementById('closeCameraBtn');
        const statusText = document.getElementById('cameraStatusText');

        try {
            statusText.innerText = "Mengakses kamera perangkat...";
            localStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false
            });
            video.srcObject = localStream;
            videoWrapper.classList.remove('hidden');
            openBtn.classList.add('hidden');
            snapBtn.classList.remove('hidden');
            closeBtn.classList.remove('hidden');
            statusText.innerText = "Kamera aktif. Arahkan lensa ke tempat usaha Anda lalu tekan 'Ambil Foto'.";
        } catch (err) {
            statusText.innerText = "Tidak dapat mengakses kamera: " + (err.message || "Izin ditolak atau perangkat tidak mendukung.");
            alert("Akses kamera tidak diizinkan atau perangkat tidak memiliki kamera aktif. Silakan gunakan tab 'Unggah Berkas'.");
            switchPhotoTab('upload');
        }
    }

    function takeSnapshot() {
        const video = document.getElementById('cameraVideo');
        const canvas = document.getElementById('cameraCanvas');
        const resultImg = document.getElementById('cameraResultImg');
        const previewWrapper = document.getElementById('cameraPreviewWrapper');
        const hiddenInput = document.getElementById('businessPhotoCameraInput');
        const snapBtn = document.getElementById('snapPhotoBtn');
        const retakeBtn = document.getElementById('retakePhotoBtn');
        const statusText = document.getElementById('cameraStatusText');

        if (!localStream) return;

        canvas.width = video.videoWidth || 640;
        canvas.height = video.videoHeight || 480;
        const ctx = canvas.getContext('2d');
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const dataUrl = canvas.toDataURL('image/jpeg', 0.85);
        resultImg.src = dataUrl;
        hiddenInput.value = dataUrl;

        previewWrapper.classList.remove('hidden');
        snapBtn.classList.add('hidden');
        retakeBtn.classList.remove('hidden');
        statusText.innerText = "Foto tempat usaha berhasil diabadikan! Anda dapat mengambil ulang bila dirasa kurang jelas.";

        stopCameraStream();
    }

    function retakeSnapshot() {
        const previewWrapper = document.getElementById('cameraPreviewWrapper');
        const hiddenInput = document.getElementById('businessPhotoCameraInput');
        const retakeBtn = document.getElementById('retakePhotoBtn');

        previewWrapper.classList.add('hidden');
        hiddenInput.value = '';
        retakeBtn.classList.add('hidden');

        startCameraStream();
    }

    function stopCameraStream() {
        const videoWrapper = document.getElementById('videoStreamWrapper');
        const openBtn = document.getElementById('openCameraBtn');
        const snapBtn = document.getElementById('snapPhotoBtn');
        const closeBtn = document.getElementById('closeCameraBtn');

        if (localStream) {
            localStream.getTracks().forEach(track => track.stop());
            localStream = null;
        }

        videoWrapper.classList.add('hidden');
        openBtn.classList.remove('hidden');
        snapBtn.classList.add('hidden');
        closeBtn.classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const select = document.getElementById('apply_product_id');
        if (select) updateProductSpecs(select);
    });
</script>
@endsection
