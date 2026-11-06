@extends('layouts.app')

@php
    $isEdit = $billboard->exists;
    $titleText = $isEdit ? 'Ubah Pengumuman Billboard' : 'Tambah Pengumuman Billboard Baru';
    $actionUrl = $isEdit ? route('admin.billboards.update', $billboard->id) : route('admin.billboards.store');
@endphp

@section('title', $titleText)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb & Nav Header -->
    <div class="pb-4 border-b border-slate-200">
        <div class="mb-3">
            <a href="{{ route('admin.billboards.index') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-slate-700 hover:text-slate-900 border border-slate-200 text-xs font-bold transition-all shadow-2xs" title="Kembali ke halaman sebelumnya">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali ke Halaman Sebelumnya</span>
            </a>
        </div>
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">{{ $titleText }}</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Konfigurasi teks, kategori, tautan rujukan, dan urutan pengumuman papan informasi resmi.</p>
        </div>
    </div>

    <!-- Form Container -->
    <form action="{{ $actionUrl }}" method="POST" class="space-y-6" id="billboardForm">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-5">
            <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                Parameter Teks & Informasi Utama
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Tab Navigasi Title -->
                <div class="form-group">
                    <label for="tab_title" class="form-label text-xs font-bold text-slate-700">
                        Label Tab Navigasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="tab_title" name="tab_title" required maxlength="100" class="form-control text-sm"
                           value="{{ old('tab_title', $billboard->tab_title) }}"
                           placeholder="Contoh: RAT & SHU / Modal UMKM / QRIS">
                    <span class="text-2xs text-slate-400 mt-1 block">Teks singkat pada tombol tab billboard (maksimal 2-3 kata).</span>
                    @error('tab_title') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                </div>

                <!-- Urutan Tampilan & Status Aktif -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="form-group">
                        <label for="order" class="form-label text-xs font-bold text-slate-700">Urutan Slide</label>
                        <input type="number" id="order" name="order" min="1" max="99" class="form-control text-sm font-mono"
                               value="{{ old('order', $billboard->order ?? 1) }}">
                        <span class="text-2xs text-slate-400 mt-1 block">Prioritas urutan dari kiri.</span>
                    </div>

                    <div class="form-group flex flex-col justify-end">
                        <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer hover:bg-slate-100 transition">
                            <input type="checkbox" name="is_active" value="1" class="rounded text-emerald-600 focus:ring-emerald-500 w-4 h-4"
                                   {{ old('is_active', $billboard->is_active ?? true) ? 'checked' : '' }}>
                            <span class="text-xs font-bold text-slate-800">Status Aktif</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Kategori Badge & Meta Text -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="grid grid-cols-2 gap-3">
                    <div class="form-group">
                        <label for="badge_text" class="form-label text-xs font-bold text-slate-700">
                            Teks Badge Kategori <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="badge_text" name="badge_text" required maxlength="100" class="form-control text-sm"
                               value="{{ old('badge_text', $billboard->badge_text ?? 'Pengumuman Utama') }}"
                               placeholder="Contoh: Pengumuman Utama">
                        @error('badge_text') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label for="badge_color" class="form-label text-xs font-bold text-slate-700">Warna Badge</label>
                        <select id="badge_color" name="badge_color" class="form-control text-sm">
                            <option value="warning" {{ old('badge_color', $billboard->badge_color) === 'warning' ? 'selected' : '' }}>Kuning (Warning)</option>
                            <option value="success" {{ old('badge_color', $billboard->badge_color) === 'success' ? 'selected' : '' }}>Hijau (Success)</option>
                            <option value="info" {{ old('badge_color', $billboard->badge_color) === 'info' ? 'selected' : '' }}>Biru (Info)</option>
                            <option value="primary" {{ old('badge_color', $billboard->badge_color) === 'primary' ? 'selected' : '' }}>Emerald (KSP)</option>
                            <option value="danger" {{ old('badge_color', $billboard->badge_color) === 'danger' ? 'selected' : '' }}>Merah (Danger)</option>
                            <option value="gray" {{ old('badge_color', $billboard->badge_color) === 'gray' ? 'selected' : '' }}>Abu-abu (Netral)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="meta_text" class="form-label text-xs font-bold text-slate-700">Keterangan Waktu / Audiens</label>
                    <input type="text" id="meta_text" name="meta_text" maxlength="150" class="form-control text-sm"
                           value="{{ old('meta_text', $billboard->meta_text) }}"
                           placeholder="Contoh: Dipublikasikan: 05 November 2026 atau Berlaku untuk Anggota Aktif">
                    <span class="text-2xs text-slate-400 mt-1 block">Ditampilkan di samping badge kategori.</span>
                </div>
            </div>

            <!-- Judul Utama -->
            <div class="form-group">
                <label for="title" class="form-label text-xs font-bold text-slate-700">
                    Judul Utama Pengumuman <span class="text-red-500">*</span>
                </label>
                <input type="text" id="title" name="title" required maxlength="255" class="form-control text-sm font-semibold"
                       value="{{ old('title', $billboard->title) }}"
                       placeholder="Contoh: Rapat Anggota Tahunan (RAT) Tahun Buku Berjalan & Alokasi Pembagian SHU">
                @error('title') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Konten Lengkap -->
            <div class="form-group">
                <label for="content" class="form-label text-xs font-bold text-slate-700">
                    Isi Pengumuman Lengkap <span class="text-red-500">*</span>
                </label>
                <textarea id="content" name="content" rows="4" required class="form-control text-sm leading-relaxed"
                          placeholder="Jelaskan secara rinci pengumuman resmi koperasi...">{{ old('content', $billboard->content) }}</textarea>
                @error('content') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
            </div>

            <!-- Tautan dan Status Pelaksanaan -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t border-slate-100">
                <div class="form-group">
                    <label for="status_text" class="form-label text-xs font-bold text-slate-700">Keterangan Status (Opsional)</label>
                    <input type="text" id="status_text" name="status_text" maxlength="150" class="form-control text-sm"
                           value="{{ old('status_text', $billboard->status_text) }}"
                           placeholder="Contoh: Status: Pelaksanaan Berjalan">
                </div>

                <div class="form-group">
                    <label for="link_url" class="form-label text-xs font-bold text-slate-700">Tautan Tujuan / URL (Opsional)</label>
                    <input type="text" id="link_url" name="link_url" maxlength="255" class="form-control text-sm"
                           value="{{ old('link_url', $billboard->link_url) }}"
                           placeholder="Contoh: #panduan / #produk / https://...">
                </div>

                <div class="form-group">
                    <label for="link_text" class="form-label text-xs font-bold text-slate-700">Teks Tombol Tautan (Opsional)</label>
                    <input type="text" id="link_text" name="link_text" maxlength="100" class="form-control text-sm"
                           value="{{ old('link_text', $billboard->link_text) }}"
                           placeholder="Contoh: Pelajari Alur SHU &rarr;">
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Simpan -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('admin.billboards.index') }}" class="btn btn-secondary text-xs font-bold px-4 py-2.5">
                Batal
            </a>
            <button type="submit" class="btn btn-primary text-xs font-bold px-6 py-2.5 shadow-sm">
                {{ $isEdit ? 'Simpan Perubahan Pengumuman' : 'Publikasikan Pengumuman' }}
            </button>
        </div>
    </form>
</div>
@endsection

