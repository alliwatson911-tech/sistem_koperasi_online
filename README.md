# Sistem Informasi Manajemen Koperasi Simpan Pinjam (KSP Sahabat Sejahtera)

Sistem Informasi Manajemen Koperasi Simpan Pinjam online berbasis web menggunakan **Laravel**, **Blade**, dan **MySQL**, yang dirancang dengan standar integritas data keuangan tinggi, pembukuan berpasangan (*double-entry bookkeeping*), otorisasi peran bertingkat, dan prinsip perlindungan anggota.

---

## 🏛 Profil Koperasi
- **Nama Koperasi**: KSP Sahabat Sejahtera
- **Nomor Badan Hukum**: AHU-0012345.AH.01.26.TAHUN 2026
- **Prinsip Operasional**: Melayani anggota terdaftar secara tertib, transparan, kekeluargaan, dan amanah sesuai UU Perkoperasian No. 25 Tahun 1992.

---

## 🚀 Fitur Unggulan Sistem

1. **Modul Keanggotaan & KYC**:
   - Pendaftaran online calon anggota baru dengan NIK, kontak, pekerjaan, dan rekening bank.
   - Unggah dokumen verifikasi sensitif (KTP, Swafoto, Bukti Penghasilan) ke penyimpanan privat.
   - Verifikasi berjenjang oleh Teller.
   - Aktivasi otomatis anggota saat Simpanan Pokok pertama kali disetorkan.

2. **Modul Simpanan Anggota**:
   - Pengelolaan 3 jenis simpanan: **Pokok** (Rp 100.000), **Wajib** (Rp 25.000/bln), dan **Sukarela** (bebas).
   - Layanan setor tunai/transfer, penarikan simpanan sukarela, buku mutasi, dan saldo *real-time*.
   - Otomatisasi jurnal akuntansi setiap kali ada setoran atau penarikan kas.

3. **Modul Pinjaman & Pembiayaan**:
   - Katalog produk pinjaman dengan batas plafon, tenor, dan suku bunga kompetitif.
   - **Fleksibilitas Satuan Tenor (Mingguan & Bulanan)**: Mendukung produk pinjaman berjangka **Mingguan** (contoh: *Pinjaman Mingguan Mikro* untuk pedagang pasar tenor 4-24 minggu) dan **Bulanan** (3-36 bulan) dengan suku bunga per periode yang terpisah dan terstandarisasi.
   - **Kalkulator Simulasi Interaktif & Transparansi Biaya Setara Per Tahun (APR)**: Mendukung metode bunga **Anuitas**, **Efektif (Saldo Menurun)**, dan **Flat**. Menampilkan perbandingan jujur tingkat bunga tahunan nominal, bunga efektif (AER), dan **Total Cost APR** (termasuk biaya administrasi) agar anggota dapat membandingkan produk mingguan dan bulanan secara adil tanpa biaya tersembunyi.
   - **Aritmetika Tanggal Kalender Sebenarnya (*True Calendar Arithmetic*)**: Generator jadwal angsuran menggunakan penambahan tanggal kalender nyata (tepat 7 hari untuk mingguan; penambahan bulan kalender tanpa *overflow* untuk bulanan, aman di akhir bulan 31 dan Februari non-kabisat/kabisat), dengan aturan pembulatan bulat Rupiah dan angsuran terakhir menyerap sisa pokok persis hingga nol.
   - **Fitur Pelunasan Dipercepat (*Early Repayment*)**: Anggota dapat melunasi pinjaman sebelum jatuh tempo akhir dengan pembebasan bunga masa depan 100% (*interest rebate*), hanya membayar sisa pokok dan denda tertunggak jika ada.
   - **Pembatasan Denda Keterlambatan (*Penalty Cap*)**: Denda harian 0,1% per hari dari pokok tertunggak, tidak berbunga-berbunga (*non-compounding*), dan dibatasi maksimal 10% per angsuran sesuai standar perlindungan anggota.
   - Pengajuan pinjaman mandiri secara online oleh anggota aktif.
   - **Antrean Penerimaan Berkas oleh Teller**: Pengajuan masuk ke antrean teller untuk verifikasi fisik/kelengkapan berkas (KTP, Slip Gaji, Formulir, Rekening Koran). Teller mengisi checklist dan catatan penerimaan (`complete` $\rightarrow$ status `received`) atau meminta perbaikan berkas (`needs_revision`).
   - **Penegakan Maker-Checker (Pemisahan Tugas 4 Mata)**: Petugas yang menerima berkas dilarang menyetujui pinjaman; Analis yang mereview kelayakan dilarang memberikan keputusan final; Manajer yang menyetujui dilarang mencairkan dana.
   - Alur persetujuan bertingkat: Verifikasi **Teller** $\rightarrow$ Analisis kelayakan 5C oleh **Analis Kredit** $\rightarrow$ Keputusan akhir oleh **Manajer**.
   - **Lini Masa Penanganan Lengkap (*Timeline*)**: Jejak audit komprehensif dari pengajuan anggota, penerimaan teller, review analis, persetujuan manajer, hingga pencairan kasir ditampilkan transparan pada rincian pinjaman.
   - Penandatanganan **Akad Pinjaman Digital** oleh anggota sebelum pencairan dana.
   - Pencairan dana oleh **Teller** dengan pemotongan biaya administrasi otomatis dan pembentukan jadwal angsuran.
   - Pembayaran angsuran dengan mekanisme *waterfall*: **Denda $\rightarrow$ Bunga $\rightarrow$ Pokok $\rightarrow$ Lebihan ke Simpanan Sukarela**.
   - Penanganan keterlambatan otomatis dan perhitungan denda harian via *artisan scheduler*.

4. **Modul Payment Gateway & Rekonsiliasi Bank (Fase 3)**:
   - **Lapisan Abstraksi Lengkap**: Mengimplementasikan `PaymentGatewayInterface` dengan driver **Fake** (deterministik untuk testing & lokal) dan driver **Midtrans** (Snap & Core API sandbox/production).
   - **Tagihan Fleksibel**: Anggota dapat membayar 1 angsuran, beberapa angsuran sekaligus, pembayaran sebagian (*partial payment*), atau setoran simpanan sukarela.
   - **Saluran Pembayaran Digital**: Mendukung **QRIS Instant** (semua bank & e-wallet) serta **Virtual Account** (BCA, BNI, BRI, Mandiri Bill, Permata) dengan batas kedaluwarsa 24 jam.
   - **Waterfall Allocation Otomatis**: Alokasi dana dengan prioritas tegas **Denda $\rightarrow$ Bunga $\rightarrow$ Pokok Pinjaman**, mendahulukan angsuran terlama.
   - **Perlindungan Kelebihan Bayar (*Overpayment Protection*)**: Kelebihan dana setelah pinjaman lunas otomatis dikreditkan ke **Simpanan Sukarela** anggota.
   - **Webhook Aman & Idempotensi Mutlak**: Verifikasi tanda tangan digital (SHA-512), penyimpanan payload mentah di `payment_webhook_events`, dan penguncian baris (`lockForUpdate`) untuk mencegah pembayaran ganda (*double-payment*).
   - **Bagan Akun Baru & Jurnal Double-Entry**:
     - Akun `1301` (*Dana dalam Penyelesaian Gateway*) & Akun `5103` (*Biaya Transaksi Payment Gateway*).
     - Saat bayar berhasil: Dr 1301, Cr 1201 (Pokok), Cr 4101 (Bunga), Cr 4103 (Denda), Cr 2103 (Simpanan jika lebih bayar).
     - Saat settlement ke bank: Dr 1102 (Bank Neto), Dr 5103 (Biaya Fee), Cr 1301 (Bruto).
   - **Dasbor Manajemen Petugas & Admin**: Ringkasan saldo kliring, filter status, pencarian, rekonsiliasi manual per transaksi, formulir batch settlement ke bank, dan ekspor CSV.
   - **Bukti Pembayaran Resmi**: Kuitansi elektronik siap cetak/PDF dengan kop resmi koperasi dan segel QR verifikasi digital.
   - **Scheduler Otomatis**: `koperasi:expire-gateway-invoices` (pemeriksaan kedaluwarsa berkala) dan `koperasi:reconcile-gateway` (sinkronisasi transaksi pending).

5. **Modul Akuntansi Sederhana (Double-Entry Ledger)**:
   - Bagan Akun Standar Koperasi (*Chart of Accounts*) terstruktur: Aset, Kewajiban, Ekuitas, Pendapatan, dan Beban.
   - Pencatatan jurnal otomatis berpasangan (*double-entry*) untuk setiap transaksi keuangan.
   - Validasi ketat keseimbangan debit dan kredit (*zero imbalance error*).
   - Buku Besar (*General Ledger*) per akun dan Neraca Saldo (*Trial Balance*).
   - Perhitungan dan pembagian **Sisa Hasil Usaha (SHU)** tahunan otomatis berdasarkan Jasa Modal (25%) dan Jasa Usaha (20%), langsung dikreditkan ke simpanan sukarela anggota saat Tutup Buku.

5. **Dasbor & Laporan**:
   - Dasbor spesifik sesuai hak akses pengguna (Anggota vs Petugas/Pengurus).
   - Laporan rekapitulasi simpanan, portofolio pinjaman, pemantauan kredit menunggak (NPL), dan arus kas (*cash flow*).
   - Fitur ekspor cetak ramah printer / PDF.

6. **Etika & Perlindungan Anggota**:
   - Nilai uang disimpan dalam bilangan bulat Rupiah (`BIGINT`), bebas dari kesalahan pembulatan *float*.
   - Catatan keuangan bersifat kekal (*immutable*), koreksi dilakukan melalui jurnal pembalik (*reversing entry*).
   - Bebas dari praktik intimidatif: Tidak ada akses ke kontak, media, atau perangkat keras anggota. Pengingat jatuh tempo dikirimkan secara santun dan kekeluargaan.

---

## 🛠 Kebutuhan Sistem & Teknologi
- **PHP**: Versi 8.2 atau lebih baru (ekstensi `pdo_mysql`, `pdo_sqlite`, `intl`, `gd`, `zip` aktif).
- **Web Server & Database**: Apache / Nginx, MySQL 8.0+ atau MariaDB 10.4+ (XAMPP didukung penuh).
- **Composer**: Versi 2.x.
- **Framework**: Laravel 12/13 dengan Blade Templating dan Tailwind CSS.

---

## 📦 Panduan Instalasi & Menjalankan di Localhost

### 1. Salin Proyek & Konfigurasi Lingkungan
Buka terminal PowerShell / Command Prompt di direktori proyek:
```bash
cd C:\xampp\htdocs\koperasi
copy .env.example .env
```

Pastikan konfigurasi database di file `.env` sudah sesuai:
```ini
APP_NAME="KSP Sahabat Sejahtera"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ksp_koperasi
DB_USERNAME=koperasi
DB_PASSWORD=koperasi123
```

### 2. Generate Application Key & Symlink Storage
```bash
php artisan key:generate
php artisan storage:link
```

### 3. Migrasi Database & Seeder Data Contoh
Jalankan migrasi bersih beserta seeder data awal (Chart of Accounts, Produk Pinjaman, Pengaturan, Akun Contoh 5 Peran, Pinjaman & Jurnal Aktif):
```bash
php artisan migrate:fresh --seed
```

### 4. Menjalankan Scheduler & Perintah Artisan
- **Periksa Jatuh Tempo & Denda Harian**:
  ```bash
  php artisan koperasi:update-overdue
  ```
  *(Jadwalkan harian pukul 00:05 WIB di server produksi).*

- **Pemeriksaan Batas Waktu Antrean Penerimaan Teller (> 24 Jam)**:
  ```bash
  php artisan koperasi:check-reception-deadline
  ```
  *(Jadwalkan setiap jam untuk memantau pengajuan anggota yang belum diterima teller dan mengirimkan notifikasi peringatan ke Manajer & Admin).*

### 5. Pengaturan Parameter Bisnis Dinamis
Parameter operasional disimpan pada tabel `settings` dan dapat diubah secara aman oleh Admin:
- `teller_assignment_mode`: Mode penugasan teller (`queue` = diambil mandiri dari antrean, `auto_workload` = otomatis ditugaskan ke teller dengan beban kerja terendah, `round_robin` = bergiliran merata).
- `application_reception_deadline_hours`: Batas waktu maksimum pengajuan harus diterima teller sebelum memicu peringatan keterlambatan (default: `24` jam kerja).
- `teller_display_mode`: Format tampilan identitas teller ke anggota (`full_name_and_code`, `first_name_and_code`, `code_only`).

### 6. Jalankan Web Server Lokal
```bash
php artisan serve --host=127.0.0.1 --port=8000
```
Buka peramban (browser) di tautan: **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## 👥 Akun Contoh Setiap Peran (Demo Accounts)

Semua akun menggunakan kata sandi (*password*): **`password123`**

| Peran | Kode Pegawai | Alamat Email | Nama Pengguna | Akses Halaman Utama |
|---|---|---|---|---|
| **Admin / Pengurus** | `ADM-001` | `admin@ksp.test` | Admin Utama KSP | Pengaturan Koperasi, Bagan Akun (CoA), Jurnal Umum, Alihkan Penugasan Teller, Tutup Buku SHU, Seluruh Laporan. |
| **Manajer** | `MGR-001` | `manajer@ksp.test` | Bpk. Hendra Wijaya | Dasbor Eksekutif, Keputusan Persetujuan/Penolakan Pinjaman, Pengawasan Portofolio & NPL, Review Tutup Buku. |
| **Analis Kredit** | `ANL-001` | `analis@ksp.test` | Ibu Rina Kusuma | Antrean Pengajuan Berstatus `received`, Analisis Kelayakan 5C, Rekomendasi Plafon & Catatan Analisis. |
| **Teller 1 / Kasir** | `TLR-001` | `teller@ksp.test` | Ahmad Kasir | Antrean Pengajuan `submitted`, Klaim & Penerimaan Berkas, Checklist Verifikasi, Setoran Simpanan, Pencairan Kasir. |
| **Teller 2 / Kasir** | `TLR-002` | `teller2@ksp.test` | Siti Fatimah | Teller cadangan untuk pengujian antrean bergiliran (*workload balancing*) dan pengalihan penugasan. |
| **Anggota 1 (Aktif)** | - | `budi@anggota.test` | Budi Santoso | Dasbor Anggota, Mutasi Simpanan, Fasilitas Pinjaman Berjalan, Riwayat Lini Masa Pinjaman, Akad Digital. |
| **Anggota 2 (Aktif)** | - | `siti@anggota.test` | Siti Aminah | Pengajuan Pinjaman Baru (tersedia dalam antrean penerimaan teller untuk demo live). |
| **Anggota 3 (Pending)** | - | `agus@anggota.test` | Agus Pratama | Calon Anggota Baru (Menunggu verifikasi KYC & setoran simpanan pokok). |

---

## 🧪 Menjalankan Pengujian Otomatis (*Feature Tests*)

Sistem dilengkapi suite pengujian otomatis menyeluruh (47 pengujian, 300 asersi) untuk menguji seluruh logika perbankan koperasi:

```bash
php artisan test
```

### Cakupan Pengujian:
1. `PaymentGatewayTest` (**Fase 3**): Menguji subsistem Payment Gateway secara komprehensif:
   - Contoh Baku Alokasi Waterfall Fase 3: Tunggakan denda Rp 5.000, bunga Rp 20.000, pokok Rp 491.066 dibayar Rp 300.000 $\rightarrow$ denda lunas Rp 5.000, bunga lunas Rp 20.000, pokok terbayar Rp 275.000, angsuran berstatus `partial`, dan jurnal double-entry tercatat seimbang (Dr 1301 Rp 300.000, Cr 1201 Rp 275.000, Cr 4101 Rp 20.000, Cr 4103 Rp 5.000).
   - Alokasi beruntun antarjadwal (*cascading multi-schedules*).
   - Pengkreditan otomatis kelebihan bayar (*overpayment*) ke Simpanan Sukarela Anggota (Cr 2103).
   - Setoran simpanan sukarela lewat gateway (Dr 1301, Cr 2103).
   - Verifikasi dan penolakan signature webhook palsu/cacat.
   - Idempotensi mutlak pencegahan proses ganda pada webhook ulang (*duplicate webhook protection*).
   - Scheduler kedaluwarsa tagihan (`koperasi:expire-gateway-invoices`).
   - Scheduler rekonsiliasi transaksi pending (`koperasi:reconcile-gateway`).
   - Rekonsiliasi settlement kliring ke Bank Koperasi (Dr 1102 Neto, Dr 5103 Biaya Gateway, Cr 1301 Bruto).
   - Alur rute checkout anggota, halaman invoice QRIS/VA, status polling JSON, serta cetak kuitansi resmi.
   - Dasbor staf, filter multi-kriteria, ekspor CSV, dan rekonsiliasi manual.
2. `WeeklyLoanTest` (**Fase 2**): Menguji seluruh kalkulasi tenor mingguan dan bulanan berbasis contoh tolok ukur (*benchmark*):
   - Contoh Baku 1 (Mingguan): Plafon Rp 5.000.000, 10 minggu, bunga 0,4% per minggu anuitas $\rightarrow$ Angsuran Rp 511.066/minggu (toleransi Rp 10), bunga minggu pertama Rp 20.000, pokok Rp 491.066, total bayar Rp 5.110.660, dan penyerapan selisih sisa pokok persis pada angsuran ke-10.
   - Contoh Baku 2 (Bulanan): Plafon Rp 10.000.000, 12 bulan, bunga 1,5% per bulan anuitas $\rightarrow$ Angsuran Rp 916.800/bulan (toleransi Rp 12), bunga bulan pertama Rp 150.000, pokok Rp 766.800.
   - Contoh Baku 3 (Alokasi Waterfall): Tunggakan denda Rp 5.000, bunga Rp 20.000, pokok Rp 491.066 dibayar Rp 300.000 $\rightarrow$ denda lunas Rp 5.000, bunga lunas Rp 20.000, pokok terbayar Rp 275.000, dan status jadwal menjadi `partial`.
   - Aritmetika Tanggal Kalender Sebenarnya (mingguan tepat 7 hari, bulanan tanpa overflow tanggal 31 & Februari).
   - Transparansi Biaya Setara Per Tahun (Total Cost APR, bunga nominal, dan effective rate AER).
   - Pembatasan denda keterlambatan maksimal 10% per angsuran secara *non-compounding*.
   - Pelunasan dipercepat (*early repayment*) dengan pembebasan bunga masa depan.
   - Siklus utuh pinjaman mingguan (*lifecycle test*).
2. `TellerReceptionTest` (**Fase 1**): Menguji 12 skenario penerimaan teller, mencakup antrean `submitted` $\rightarrow$ klaim oleh teller, pencegahan race condition/double-claim dengan row-level lock (`lockForUpdate`), validasi checklist kelengkapan berkas, alur revisi pengajuan (`needs_revision`) dan pengajuan ulang anggota, penegakan Maker-Checker (teller penerima dilarang menyetujui pinjaman), pengalihan penugasan oleh Admin dengan alasan tercatat di audit log, rekonstruksi lini masa (*timeline*), serta perintah pengingat deadline penerimaan.
3. `LoanCalculationTest`: Memvalidasi keakuratan bunga Flat, Efektif (saldo menurun), Anuitas, biaya administrasi, dan konsistensi amortisasi pokok.
4. `LoanRepaymentTest`: Menguji siklus utuh pembiayaan (Pengajuan $\rightarrow$ Penerimaan Teller $\rightarrow$ Review Analis $\rightarrow$ Persetujuan Manajer $\rightarrow$ Akad Digital $\rightarrow$ Pencairan $\rightarrow$ Pembayaran Angsuran $\rightarrow$ Status Lunas).
5. `PenaltyAndOverdueTest`: Menguji deteksi keterlambatan jatuh tempo, pencatatan denda harian, dan pelunasan bertingkat (*waterfall*).
6. `AccountingJournalTest`: Menguji kepatuhan *double-entry* (Debit == Kredit), pembuatan jurnal otomatis transaksi simpanan, dan prinsip *immutability* jurnal pembalik.
7. `RolePermissionTest`: Menguji pengamanan *middleware* antarperan (pembatasan akses anggota terhadap rute kasir/akuntansi, hak akses operasional kasir, dan kewenangan penuh manajer/admin).
8. `RouteAuditTest`: Audit kepatuhan seluruh rute web dan tampilan blade.

---

## ⚖️ Kepatuhan Regulasi & Tanggung Jawab Operasional

1. **Koperasi Simpan Pinjam Tertutup (*Closed-Loop*)**: Sistem ini secara ketat hanya melayani anggota dan calon anggota terdaftar sesuai Undang-Undang No. 25 Tahun 1992 tentang Perkoperasian dan Peraturan Menteri Koperasi dan UKM RI. Sistem tidak melayani penghimpunan dana maupun pinjaman dari masyarakat umum.
2. **Prinsip Kehati-hatian (*Prudential Principle*)**: Seluruh tahapan persetujuan pembiayaan mewajibkan penelaahan manusia secara bertingkat (*human review*). Sistem otomatisasi hanya bertindak sebagai asisten pencatatan dan peringatan, bukan penentu persetujuan kredit otomatis tanpa telaah petugas.
3. **Pemisahan Kewenangan (*Segregation of Duties*)**: Tidak ada satu pun peran pengguna yang memiliki kewenangan memproses pinjaman dari awal hingga pencairan seorang diri, guna memitigasi risiko *fraud* dan benturan kepentingan (*conflict of interest*).

---

## 📚 Dokumentasi Terperinci
- **[docs/ALUR.md](docs/ALUR.md)**: Diagram Mermaid alur keanggotaan, simpanan, pinjaman, dan tutup buku SHU beserta penjelasan status.
- **[docs/DATABASE.md](docs/DATABASE.md)**: Diagram ERD Mermaid, spesifikasi struktur 20 tabel, dan tata kelola integritas moneter integer.
- **[docs/ATURAN-BISNIS.md](docs/ATURAN-BISNIS.md)**: Formula matematis perhitungan Flat, Efektif, Anuitas, denda, dan pembagian SHU dengan contoh angka nyata.
