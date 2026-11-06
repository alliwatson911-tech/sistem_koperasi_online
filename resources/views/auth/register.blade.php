@extends('layouts.app')

@section('title', 'Pendaftaran Anggota Koperasi Online')

@section('content')
<div class="max-w-2xl mx-auto my-6 px-4">
    <div class="bg-white rounded-2xl border border-[#E8E2D8] shadow-sm p-6 sm:p-8">
        <div class="border-b border-slate-100 pb-5 mb-6">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-[#EAF6F5] text-[#0F4C81] border border-[#2A9D8F]/30 mb-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Pendaftaran Anggota Baru
            </span>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 tracking-tight">Formulir Pendaftaran Calon Anggota</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Lengkapi data diri dan unggah berkas verifikasi resmi sesuai ketentuan AD/ART KSP Sahabat Sejahtera.</p>
        </div>

        <form action="{{ route('register.post') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Bagian 1: Data Akun & Kontak -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-full bg-[#EAF6F5] text-[#0F4C81] flex items-center justify-center text-xs font-bold">1</span>
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Data Akun & Kontak</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="reg_name" class="form-label">Nama Lengkap (Sesuai KTP) <span class="text-rose-500">*</span></label>
                        <input type="text" id="reg_name" name="name" class="form-control" value="{{ old('name') }}" required placeholder="Contoh: Ahmad Hidayat">
                        @error('name') <div class="text-rose-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="reg_phone" class="form-label">Nomor WhatsApp / HP <span class="text-rose-500">*</span></label>
                        <input type="text" id="reg_phone" name="phone" class="form-control" value="{{ old('phone') }}" required placeholder="08123456789">
                        @error('phone') <div class="text-rose-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div class="form-group">
                        <label for="reg_email" class="form-label">Alamat Email <span class="text-rose-500">*</span></label>
                        <input type="email" id="reg_email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="email@contoh.com">
                        @error('email') <div class="text-rose-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="reg_password" class="form-label">Kata Sandi Akun <span class="text-rose-500">*</span></label>
                        <input type="password" id="reg_password" name="password" class="form-control" required placeholder="Minimal 6 karakter">
                        @error('password') <div class="text-rose-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="form-group mt-3">
                    <label for="reg_password_confirmation" class="form-label">Konfirmasi Kata Sandi <span class="text-rose-500">*</span></label>
                    <input type="password" id="reg_password_confirmation" name="password_confirmation" class="form-control" required placeholder="Ulangi kata sandi">
                </div>
            </div>

            <!-- Bagian 2: Identitas Diri (KTP) -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-full bg-[#EAF6F5] text-[#0F4C81] flex items-center justify-center text-xs font-bold">2</span>
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Identitas Diri (KTP) & Domisili</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="reg_nik" class="form-label">Nomor Induk Kependudukan (NIK) <span class="text-rose-500">*</span></label>
                        <input type="text" id="reg_nik" name="nik" class="form-control" maxlength="16" value="{{ old('nik') }}" required placeholder="16 digit angka KTP">
                        @error('nik') <div class="text-rose-600 text-xs mt-1 font-medium">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group">
                        <label for="reg_gender" class="form-label">Jenis Kelamin <span class="text-rose-500">*</span></label>
                        <select id="reg_gender" name="gender" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="L" {{ old('gender') === 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('gender') === 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div class="form-group">
                        <label for="reg_birth_place" class="form-label">Tempat Lahir <span class="text-rose-500">*</span></label>
                        <input type="text" id="reg_birth_place" name="birth_place" class="form-control" value="{{ old('birth_place') }}" required placeholder="Kota kelahiran">
                    </div>
                    <div class="form-group">
                        <label for="reg_birth_date" class="form-label">Tanggal Lahir <span class="text-rose-500">*</span></label>
                        <input type="date" id="reg_birth_date" name="birth_date" class="form-control" value="{{ old('birth_date') }}" required>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="reg_address" class="form-label">Alamat Lengkap Domisili <span class="text-rose-500">*</span></label>
                    <textarea id="reg_address" name="address" rows="2" class="form-control" required placeholder="Nama jalan, RT/RW, kelurahan, kecamatan, kota/kab">{{ old('address') }}</textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div class="form-group">
                        <label for="reg_occupation" class="form-label">Pekerjaan / Bidang Usaha <span class="text-rose-500">*</span></label>
                        <input type="text" id="reg_occupation" name="occupation" class="form-control" value="{{ old('occupation') }}" required placeholder="Wiraswasta / Karyawan / PNS">
                    </div>
                    <div class="form-group">
                        <label for="reg_monthly_income" class="form-label">Estimasi Penghasilan Bulanan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" id="reg_monthly_income" name="monthly_income" class="form-control" value="{{ old('monthly_income', 5000000) }}" required min="0" step="50000">
                    </div>
                </div>
            </div>

            <!-- Bagian 3: Rekening & Unggah Berkas -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center gap-2 mb-3">
                    <span class="w-6 h-6 rounded-full bg-[#EAF6F5] text-[#0F4C81] flex items-center justify-center text-xs font-bold">3</span>
                    <h2 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Rekening Pencairan & Unggah Berkas</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="form-group">
                        <label for="reg_bank_name" class="form-label">Nama Bank</label>
                        <input type="text" id="reg_bank_name" name="bank_name" class="form-control" value="{{ old('bank_name') }}" placeholder="BCA / Mandiri / BRI / BNI">
                    </div>
                    <div class="form-group">
                        <label for="reg_bank_account" class="form-label">Nomor Rekening Bank</label>
                        <input type="text" id="reg_bank_account" name="bank_account" class="form-control" value="{{ old('bank_account') }}" placeholder="Nomor rekening Anda">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">
                    <div class="form-group">
                        <label for="reg_ktp_doc" class="form-label">Foto / Scan KTP Asli <span class="text-rose-500">*</span></label>
                        <input type="file" id="reg_ktp_doc" name="ktp_document" class="form-control" required accept="image/*,.pdf">
                        <span class="form-text text-xs text-slate-400 mt-1 block">Format: JPG, PNG, atau PDF (maks. 3MB). Pastikan NIK dan foto terbaca jelas.</span>
                    </div>
                    <div class="form-group">
                        <label for="reg_selfie_doc" class="form-label">Foto Swafoto (Selfie)</label>
                        <input type="file" id="reg_selfie_doc" name="selfie_document" class="form-control" accept="image/*">
                        <span class="form-text text-xs text-slate-400 mt-1 block">Opsional: Foto selfie wajah menghadap kamera untuk verifikasi ganda.</span>
                    </div>
                </div>
            </div>

            <!-- Ketentuan Keanggotaan -->
            <div class="rounded-xl p-4 bg-[#F8F5F0] border border-[#E8E2D8] text-xs text-slate-600 space-y-1.5">
                <div class="font-bold text-slate-800 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-[#0F4C81]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Ketentuan & Tata Tertib Keanggotaan:
                </div>
                <ul class="list-disc list-inside space-y-1 text-slate-500 ml-1">
                    <li>Calon anggota akan diverifikasi oleh Petugas/Teller dalam waktu maksimal 1x24 jam kerja.</li>
                    <li>Status anggota resmi <strong>AKTIF</strong> setelah menyetorkan Simpanan Pokok sebesar <strong>Rp {{ number_format(\App\Models\Setting::get('simpanan_pokok_amount', 100000), 0, ',', '.') }}</strong> (dibayarkan sekali selama menjadi anggota).</li>
                    <li>KSP Sahabat Sejahtera menjamin kerahasiaan dokumen dan data pribadi sesuai UU Perlindungan Data Pribadi (PDP).</li>
                </ul>
            </div>

            <button type="submit" class="btn btn-primary w-full py-3 text-sm font-semibold shadow-sm hover:shadow transition-all">
                Kirim Pendaftaran Calon Anggota
            </button>
        </form>

        <div class="text-center mt-6 pt-5 border-t border-slate-100 text-xs text-slate-500">
            Sudah memiliki akun anggota terdaftar? <a href="{{ route('login') }}" class="text-[#0F4C81] font-bold hover:underline">Masuk ke Portal Anggota</a>
        </div>
    </div>
</div>
@endsection
