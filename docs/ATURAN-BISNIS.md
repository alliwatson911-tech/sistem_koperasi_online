# Aturan Bisnis & Formula Finansial KSP Sahabat Sejahtera

Dokumen ini memuat formula matematis resmi, algoritma perhitungan bunga pinjaman, denda keterlambatan, pembagian Sisa Hasil Usaha (SHU), serta aturan operasional simpan pinjam KSP Sahabat Sejahtera.

---

## 1. Parameter Simpanan Anggota

| Jenis Simpanan | Nominal Ketetapan | Karakteristik & Aturan Penarikan |
|---|---|---|
| **Simpanan Pokok** | **Rp 100.000** | Dibayarkan satu kali saat mendaftar menjadi anggota. Menjadi syarat aktivasi status anggota. Tidak dapat ditarik selama masih menjadi anggota aktif koperasi. |
| **Simpanan Wajib** | **Rp 25.000 / bulan** | Disetorkan setiap bulan oleh anggota aktif. Menjadi dasar porsi perhitungan SHU Jasa Modal. Tidak dapat ditarik selama masih menjadi anggota. |
| **Simpanan Sukarela** | Bebas (Kelipatan Rp 10.000) | Dapat disetor dan ditarik sewaktu-waktu oleh anggota di jam operasional kasir Teller atau via transfer bank. Menjadi rekening tujuan penyaluran SHU tahunan. |

---

## 2. Formula Perhitungan Bunga Pinjaman

Sistem mendukung 3 metode perhitungan bunga pembiayaan standar perbankan & koperasi:

### A. Bunga Flat (Tetap)
Bunga dihitung dari plafon pinjaman awal dan nilainya tetap sama setiap bulan hingga akhir tenor.

$$\text{Angsuran Pokok Bulanan} = \left\lfloor \frac{P}{N} \right\rfloor$$
$$\text{Bunga Bulanan} = \text{round}\left( P \times \frac{i}{100} \right)$$
$$\text{Total Angsuran Bulanan} = \text{Angsuran Pokok} + \text{Bunga Bulanan}$$

> **Contoh Kasus Flat:**
> - Plafon Pinjaman ($P$): Rp 12.000.000
> - Tenor ($N$): 12 Bulan
> - Suku Bunga ($i$): 1.5% per bulan
> 
> *Hasil Perhitungan:*
> - Angsuran Pokok = $12.000.000 / 12 = \text{Rp } 1.000.000$
> - Bunga per Bulan = $12.000.000 \times 1.5\% = \text{Rp } 180.000$
> - Total Angsuran per Bulan = $\text{Rp } 1.180.000$
> - Total Bunga 1 Tahun = $\text{Rp } 2.160.000$
> - Total Kewajiban = $\text{Rp } 14.160.000$

---

### B. Bunga Efektif (Saldo Menurun)
Porsi pokok tetap sama setiap bulan, sedangkan bunga dihitung berdasarkan **sisa saldo pokok pinjaman (*remaining balance*)** bulan bersangkutan, sehingga jumlah total angsuran menurun seiring berjalannya waktu.

$$\text{Angsuran Pokok Bulanan} = \left\lfloor \frac{P}{N} \right\rfloor$$
$$\text{Bunga Bulan ke-}k = \text{round}\left( S_{k-1} \times \frac{i}{100} \right)$$
$$\text{Total Angsuran Bulan ke-}k = \text{Angsuran Pokok} + \text{Bunga Bulan ke-}k$$
$$\text{Sisa Pokok } S_k = S_{k-1} - \text{Angsuran Pokok}$$

> **Contoh Kasus Efektif (Saldo Menurun):**
> - Plafon Pinjaman ($P$): Rp 10.000.000
> - Tenor ($N$): 5 Bulan
> - Suku Bunga ($i$): 2.0% per bulan
> 
> *Tabel Amortisasi Jadwal Angsuran:*
> | Bulan | Sisa Pokok Awal | Angsuran Pokok | Bunga (2%) | Total Angsuran | Sisa Pokok Akhir |
> |---|---|---|---|---|---|
> | 1 | Rp 10.000.000 | Rp 2.000.000 | Rp 200.000 | Rp 2.200.000 | Rp 8.000.000 |
> | 2 | Rp 8.000.000 | Rp 2.000.000 | Rp 160.000 | Rp 2.160.000 | Rp 6.000.000 |
> | 3 | Rp 6.000.000 | Rp 2.000.000 | Rp 120.000 | Rp 2.120.000 | Rp 4.000.000 |
> | 4 | Rp 4.000.000 | Rp 2.000.000 | Rp 80.000 | Rp 2.080.000 | Rp 2.000.000 |
> | 5 | Rp 2.000.000 | Rp 2.000.000 | Rp 40.000 | Rp 2.040.000 | Rp 0 |
> | **Total** | | **Rp 10.000.000** | **Rp 600.000** | **Rp 10.600.000** | |

---

### C. Bunga Anuitas
Jumlah total angsuran bulanan tetap konstan. Komposisi di awal tenor didominasi porsi bunga, kemudian secara bertahap porsi pokok meningkat dan porsi bunga menurun.

Menggunakan formula standar anuitas:
$$A = P \times \frac{i}{1 - (1 + i)^{-N}}$$

Di mana:
- $A$ = Angsuran bulanan tetap (Anuitas)
- $P$ = Plafon pinjaman awal
- $i$ = Suku bunga bulanan (desimal)
- $N$ = Tenor dalam bulan

Porsi bunga bulan ke-$k$:
$$B_k = S_{k-1} \times i$$
Porsi pokok bulan ke-$k$:
$$K_k = A - B_k$$

*Catatan Implementasi:* Pada bulan terakhir ($k = N$), sistem secara otomatis menyesuaikan porsi pokok sebesar sisa pokok riil untuk memastikan tidak ada selisih akibat pembulatan desimal.

---

## 3. Biaya Administrasi & Ketentuan Pencairan

1. **Biaya Administrasi Pinjaman**:
   $$\text{Biaya Admin} = \text{round}\left( P \times \frac{\text{Admin \%}}{100} \right) + \text{Admin Tetap}$$
   Biaya administrasi dipotong langsung di awal saat pencairan dana oleh Teller.
2. **Kas Bersih yang Diterima Anggota**:
   $$\text{Kas Diterima} = \text{Plafon Pokok} - \text{Total Biaya Admin}$$

---

## 4. Denda Keterlambatan & Waterfall Pembayaran

### A. Formula Denda Harian
Jika tanggal hari ini melewati batas tanggal jatuh tempo (`due_date`) pada jadwal angsuran, scheduler harian (pukul 00:05 WIB) menghitung denda harian proporsional:

$$\text{Hari Terlambat} = \text{Hari Ini} - \text{Due Date}$$
$$\text{Denda Keterlambatan} = \text{round}\left( \text{Pokok Angsuran Tertunggak} \times \text{Tarif Denda Harian} \times \text{Hari Terlambat} \right)$$
*Tarif Denda Default: 0.1% (0.0010) per hari.*

### B. Urutan Prioritas Pelunasan (*Payment Waterfall*)
Ketika debitur melakukan pembayaran angsuran, sistem memprioritaskan alokasi dana dengan urutan sebagai berikut:
1. **Denda Keterlambatan**: Dilunasi terlebih dahulu.
2. **Bunga Pinjaman**: Pendapatan bunga koperasi.
3. **Pokok Pinjaman**: Mengurangi sisa pokok pinjaman.
4. **Lebihan Pembayaran**: Jika dana yang disetor melebihi total tagihan angsuran, kelebihan dana otomatis dikreditkan ke rekening **Simpanan Sukarela** anggota.

---

## 5. Formula Pembagian Sisa Hasil Usaha (SHU)

Berdasarkan UU Perkoperasian No. 25 Tahun 1992 dan AD/ART KSP Sahabat Sejahtera, Sisa Hasil Usaha berasal dari pendapatan koperasi yang diperoleh dalam satu tahun buku dikurangi dengan penyusutan dan beban-beban dalam tahun buku bersangkutan:

$$\text{SHU Bersih} = \text{Total Pendapatan (Akun 4xxx)} - \text{Total Beban Operasional (Akun 5xxx)}$$

### Persentase Alokasi SHU
1. **Dana Cadangan Koperasi (40%)**: Memperkuat modal sendiri koperasi.
2. **Jasa Modal / Simpanan (25%)**: Dibagikan kepada anggota sebanding dengan simpanan pokok dan wajib.
3. **Jasa Usaha / Pinjaman (20%)**: Dibagikan kepada anggota sebanding dengan kontribusi bunga pinjaman yang telah dilunasi.
4. **Dana Pengurus, Pengawas & Karyawan (5%)**: Insentif kinerja pengelola.
5. **Dana Pendidikan Koperasi (5%)**: Pembinaan dan pelatihan anggota.
6. **Dana Sosial & Pembangunan Daerah (5%)**: Bantuan sosial masyarakat.

### Formula SHU per Anggota
$$\text{SHU Jasa Modal Anggota}_m = \left( \frac{\text{Simpanan Pokok}_m + \text{Simpanan Wajib}_m}{\sum \text{Seluruh Simpanan Pokok \& Wajib Anggota Aktif}} \right) \times \text{Alokasi Jasa Modal (25\%)}$$

$$\text{SHU Jasa Pinjaman Anggota}_m = \left( \frac{\text{Total Bunga Pinjaman yang Dibayar}_m}{\sum \text{Seluruh Bunga yang Diterima Koperasi Tahun Ini}} \right) \times \text{Alokasi Jasa Usaha (20\%)}$$

$$\text{Total SHU Diterima Anggota}_m = \text{SHU Jasa Modal}_m + \text{SHU Jasa Pinjaman}_m$$

---

## 6. Aturan Bisnis Verifikasi & Penerimaan Teller (Fase 1)

Untuk menjamin kehati-hatian (*prudential banking principle*) dan kepatuhan koperasi simpan pinjam tertutup, setiap permohonan pinjaman mandiri anggota harus melalui tahap verifikasi berkas oleh Teller sebelum dapat diproses ke komite pembiayaan (Analis Kredit & Manajer).

### A. Prinsip Pemisahan Tugas (*Segregation of Duties / Maker-Checker*)
Sistem menerapkan pencegahan benturan kepentingan (*conflict of interest*) secara terprogram di level Service & Controller:
1. **Penerima $\neq$ Penyetuju**: Petugas yang menerima dan memverifikasi berkas pengajuan sebagai Teller **TIDAK BOLEH** menyetujui pengajuan tersebut sebagai Manajer, meskipun petugas tersebut memiliki hak akses Admin.
2. **Analis $\neq$ Penyetuju**: Petugas yang melakukan penilaian kelayakan 5C dan rekomendasi sebagai Analis Kredit **TIDAK BOLEH** menyetujui pengajuan tersebut sebagai Manajer.
3. **Penyetuju $\neq$ Pencair Dana**: Manajer yang memberikan otorisasi persetujuan pembiayaan **TIDAK BOLEH** merangkap sebagai petugas yang mencairkan kas/dana pinjaman.

### B. Mekanisme Penugasan & Antrean Teller
1. **Mode Antrean Bebas (*Queue Claim*)**: Pengajuan pinjaman baru secara default masuk ke antrean terbuka. Teller aktif dapat mengambil (*claim*) berkas tersebut ke akun tugasnya. Sistem menggunakan penguncian baris basis data (`lockForUpdate`) dalam transaksi atomik untuk mencegah *race condition* (dua teller mengklaim berkas yang sama secara simultan).
2. **Mode Beban Kerja Otomatis (*Auto-Workload Balancing*)**: Bila diaktifkan di `settings` (`teller_assignment_mode = auto_workload`), sistem secara otomatis menugaskan pengajuan baru ke Teller aktif dengan jumlah antrean berjalan paling sedikit.
3. **Pengalihan Penugasan oleh Pengurus**: Admin atau Manajer berwenang mengalihkan tugas verifikasi dari seorang Teller ke Teller lain dengan wajib mencantumkan alasan tertulis (*audit trail* tercatat di `audit_logs`).

### C. Batas Waktu Toleransi (*SLA Reception Deadline*) & Pengingat Otomatis
1. **Batas Waktu Standar**: 24 jam kerja (`application_reception_deadline_hours = 24`).
2. **Scheduler Pengingat**: Perintah *artisan scheduler* `koperasi:check-reception-deadline` dijalankan setiap jam kerja. Apabila terdapat pengajuan berstatus `submitted` yang belum diterima teller melebihi batas 24 jam, sistem secara otomatis mengirimkan notifikasi peringatan bertingkat ke seluruh Manajer dan Admin Koperasi agar dapat segera dievaluasi atau dialihkan.

### D. Checklist Verifikasi Dokumen & Status Transisi Sah
Teller wajib memeriksa checklist 5 parameter dokumen sebelum menerima berkas:
- **KTP / Identitas Diri Asli & Berlaku** *(Wajib)*: Kesesuaian NIK, foto wajah, nama lengkap dengan data dukcapil.
- **Formulir Pengajuan & Persetujuan Tertulis** *(Wajib)*: Konfirmasi akad dan persetujuan pemotongan biaya administrasi.
- **Bukti Penghasilan / Slip Gaji / Mutasi Rekening Usaha** *(Wajib)*: Verifikasi kemampuan bayar riil.
- **Status Keanggotaan Aktif & Bebas Tunggakan** *(Wajib)*: Memastikan Simpanan Pokok lunas dan tidak memiliki kredit macet.
- **Kesesuaian Rasio Plafon Pinjaman** *(Opsional)*: Memastikan beban angsuran $\le 40\%$ dari penghasilan bulanan.

**Transisi Status yang Sah:**
- `submitted` $\rightarrow$ `received`: Jika seluruh checklist wajib terpenuhi. Berkas diteruskan ke Analis Kredit.
- `submitted` $\rightarrow$ `needs_revision`: Jika berkas belum lengkap/buram. Pengajuan dikembalikan ke akun anggota disertai catatan perbaikan spesifik.
- `needs_revision` $\rightarrow$ `submitted`: Anggota melengkapi kekurangan berkas dan mengirimkan kembali (*resubmit*).
- `received` $\rightarrow$ `in_review`: Analis Kredit menganalisis dan memberi rekomendasi plafon.
- `in_review` $\rightarrow$ `approved` atau `rejected`: Keputusan akhir oleh Manajer Koperasi.
- `approved` $\rightarrow$ `active`: Penandatanganan akad digital oleh anggota dan pencairan dana bersih oleh Teller.

---

## 7. Aturan Bisnis Tenor Mingguan, Aritmetika Kalender, & Transparansi Biaya Setara Tahunan (Fase 2)

### A. Skema Satuan Tenor Mingguan & Bulanan
Sistem mendukung fleksibilitas satuan periode pinjaman:
1. **Satuan Tenor (`tenor_unit`)**:
   - `week` (Mingguan): Khusus produk mikro pedagang pasar dan UMKM perputaran harian/mingguan (contoh: 4 sampai 24 minggu).
   - `month` (Bulanan): Untuk pinjaman multiguna, modal kerja, dan konsumtif (contoh: 3 sampai 36 bulan).
2. **Karakteristik Produk Mingguan Baku (`PRD-MGN`)**:
   - Plafon: Rp 500.000 s/d Rp 5.000.000.
   - Tenor: 4 s/d 24 minggu.
   - Suku Bunga: 0,40% per minggu anuitas atas sisa pokok pinjaman.
   - Biaya Administrasi: 1,00% + Rp 25.000 (dipotong saat pencairan).
   - Denda Keterlambatan: 0,10% per hari dari angsuran pokok yang tertunggak (maksimal 10% per angsuran, tidak berbunga-berbunga).

### B. Contoh Baku Perhitungan (Tolok Ukur / Benchmark Test)

#### 1. Contoh Baku Mingguan (Anuitas 10 Minggu)
- **Plafon Pokok ($P$)**: Rp 5.000.000
- **Tenor ($n$)**: 10 minggu
- **Suku Bunga per Periode ($i$)**: 0,40% per minggu ($0,0040$)
- **Formula Angsuran Anuitas ($A$)**:
  $$A = P \times \frac{i}{1 - (1+i)^{-n}} = 5.000.000 \times \frac{0,004}{1 - (1,004)^{-10}} \approx \text{Rp } 511.066\text{ per minggu}$$
- **Rincian Periode Pertama**:
  - Porsi Bunga Minggu Ke-1: $5.000.000 \times 0,004 = \text{Rp } 20.000$
  - Porsi Pokok Minggu Ke-1: $511.066 - 20.000 = \text{Rp } 491.066$
  - Sisa Saldo Pokok: $\text{Rp } 4.508.934$
- **Total Pengembalian Pokok + Bunga**: Sekitar $\text{Rp } 5.110.660$.
- **Aturan Penyerapan Selisih Pembulatan**: Angsuran ke-10 (terakhir) menyerap seluruh sisa pokok yang tersisa sehingga total pokok yang terbayar tepat Rp 5.000.000 (tidak ada selisih Rp 1 pun).

#### 2. Contoh Baku Bulanan (Anuitas 12 Bulan)
- **Plafon Pokok ($P$)**: Rp 10.000.000
- **Tenor ($n$)**: 12 bulan
- **Suku Bunga per Periode ($i$)**: 1,50% per bulan ($0,0150$)
- **Formula Angsuran Anuitas ($A$)**:
  $$A = 10.000.000 \times \frac{0,015}{1 - (1,015)^{-12}} \approx \text{Rp } 916.800\text{ per bulan}$$
- **Rincian Periode Pertama**:
  - Porsi Bunga Bulan Ke-1: $10.000.000 \times 0,015 = \text{Rp } 150.000$
  - Porsi Pokok Bulan Ke-1: $916.800 - 150.000 = \text{Rp } 766.800$

#### 3. Contoh Baku Alokasi Pembayaran Berjenjang (*Waterfall Allocation*)
Tunggakan satu angsuran terdiri dari:
- Denda: Rp 5.000
- Bunga: Rp 20.000
- Pokok: Rp 491.066
- Anggota melakukan pembayaran: **Rp 300.000**
- **Hasil Alokasi Sistematis**:
  1. Denda Rp 5.000 lunas (sisa bayar Rp 295.000).
  2. Bunga Rp 20.000 lunas (sisa bayar Rp 275.000).
  3. Pokok Rp 275.000 terbayar dari kewajiban Rp 491.066 (sisa pokok angsuran Rp 216.066).
  4. Status angsuran menjadi `partial`.

### C. Aritmetika Tanggal Kalender Sebenarnya (True Calendar Arithmetic)
Sistem menolak asumsi kasar 30 hari per bulan yang tidak adil. Perhitungan tanggal jatuh tempo menggunakan kalender nyata:
1. **Jadwal Mingguan**: Tepat 7 hari kalender dari tanggal pencairan (`addWeeks($i)` atau `addDays(7 * $i)`). Contoh: Pencairan Kamis 15 Januari 2026 $\rightarrow$ jatuh tempo berturut-turut: 22 Januari, 29 Januari, 5 Februari, 12 Februari 2026.
2. **Jadwal Bulanan**: Menggunakan penambahan bulan tanpa *overflow* (`addMonthsNoOverflow($i)`). Contoh: Pencairan 31 Januari 2026 $\rightarrow$ jatuh tempo: 28 Februari 2026 (atau 29 pada kabisat), 31 Maret 2026, 30 April 2026.

### D. Transparansi Finansial & Biaya Setara Per Tahun (APR & AER)
Untuk melindungi anggota dari jebakan bunga semu (seperti bunga mingguan yang terkesan kecil padahal beban tahunannya tinggi), kalkulator simulasi dan rincian pinjaman wajib menampilkan 3 indikator tahunan:
1. **Suku Bunga Nominal Tahunan**:
   $$\text{Bunga Nominal} = \text{Suku Bunga per Periode} \times \text{Jumlah Periode per Tahun}$$
   *(52 untuk mingguan; 12 untuk bulanan. Contoh: 0,40% per minggu = 20,80% per tahun).*
2. **Tingkat Bunga Efektif Tahunan (*Annual Effective Rate / AER*)**:
   $$\text{AER} = \left((1 + i)^{\text{Periode per Tahun}} - 1\right) \times 100\%$$
   *(Contoh: $(1 + 0,004)^{52} - 1 \approx 23,08\%\text{ per tahun})$.*
3. **Biaya Setara Tahunan Termasuk Administrasi (*Total Cost APR*)**:
   $$\text{Total Cost APR} = \left( \frac{\text{Total Bunga} + \text{Biaya Administrasi}}{\text{Plafon Pokok}} \right) \times \left( \frac{\text{Periode per Tahun}}{\text{Tenor}} \right) \times 100\%$$

### E. Aturan Denda Keterlambatan & Pembatasan Maksimal (*Penalty Cap*)
1. **Tarif Harian**: 0,10% per hari keterlambatan dihitung dari porsi pokok angsuran yang belum terbayar (`$unpaidPrincipal`).
2. **Prinsip Non-Compounding**: Denda dihitung secara linear dan tidak pernah berbunga-berbunga.
3. **Batas Maksimal 10% per Angsuran**: Akumulasi denda pada satu baris jadwal angsuran dibatasi secara tegas maksimal **10%** dari porsi pokok angsuran tersebut, terlepas dari seberapa lama hari keterlambatan berlangsung.

### F. Pelunasan Dipercepat (*Early Repayment*)
Anggota berhak melakukan pelunasan dipercepat sewaktu-waktu:
1. **Hak Pembebasan Bunga Masa Depan (*Rebate / Fee Waiver*)**: Seluruh kewajiban bunga pada periode masa depan yang belum jatuh tempo dihapuskan 100%.
2. **Kewajiban Pembayaran**: Anggota hanya membayar **Sisa Pokok Beredar (`outstanding_principal`)** + **Tunggakan Denda (`outstanding_penalty`)** + bunga yang telah jatuh tempo (jika ada).
3. **Pencatatan**: Seluruh jadwal sisa ditandai `paid`, status pinjaman menjadi `paid_off`, dan jurnal penutupan fasilitas kredit dicatat seketika ke buku besar.

---

## 8. Aturan Bisnis & Integrasi Payment Gateway (Fase 3)

Untuk memudahkan anggota melakukan pembayaran cicilan pinjaman dan setoran simpanan sukarela secara online 24/7 tanpa perlu antre di kantor kas, sistem dilengkapi dengan subsistem Payment Gateway terintegrasi.

### A. Arsitektur Abstraksi & Driver Gateway
1. **Kontrak `PaymentGatewayInterface`**: Menyediakan abstraksi seragam untuk pembuatan tagihan (`createCharge`), verifikasi signature (`verifyWebhook`), parsing notifikasi (`parseWebhook`), pemeriksaan status transaksi (`checkStatus`), dan pembatalan (`cancelPayment`).
2. **Driver Fake (`FakePaymentGateway`)**: Driver deterministik untuk pengujian unit/fitur otomatis dan pengembangan lokal tanpa koneksi internet. Mendukung nomor Virtual Account dan string QRIS deterministik, serta mocking status pembayaran.
3. **Driver Midtrans (`MidtransPaymentGateway`)**: Integrasi penuh dengan API resmi Midtrans (Snap & Core API) yang mendukung QRIS (Bank BCA, Mandiri, BRI, BNI, GoPay, OVO, ShopeePay), Virtual Account (BCA, BNI, BRI, Mandiri Bill, Permata), dan e-Wallet. Menggunakan verifikasi tanda tangan SHA-512 resmi:
   $$\text{Signature} = \text{SHA512}\left(\text{order\_id} + \text{status\_code} + \text{gross\_amount} + \text{ServerKey}\right)$$
4. **Driver Manager (`PaymentGatewayManager`)**: Mengambil konfigurasi driver aktif secara dinamis dari tabel `settings` (`payment_gateway_driver`) atau `.env`.

### B. Pembuatan Tagihan & Integritas Finansial Server-Side
1. **Order ID Unik**: Dibuat dengan format unik:
   - Angsuran Pinjaman: `INV-LN-YYYYMMDD-XXXXXX`
   - Setoran Simpanan: `INV-SV-YYYYMMDD-XXXXXX`
2. **Validasi Server-Side**: Nilai tagihan dan denda dihitung mutlak dari server basis data. Klien web tidak dapat memanipulasi nominal atau tanggal jatuh tempo.
3. **Dukungan Pembayaran Fleksibel**:
   - Pembayaran 1 angsuran penuh.
   - Pembayaran beberapa angsuran sekaligus (meluncur secara berurutan dari angsuran terlama).
   - Pembayaran sebagian (*partial payment*) dengan batas minimal Rp 10.000.
   - Setoran simpanan sukarela anggota.
4. **Kebijakan Biaya Gateway (*Fee Bearer*)**:
   - Ditanggung Koperasi (`fee_bearer = 'cooperative'`): Anggota hanya membayar nominal tagihan pokok murni. Biaya MDR gateway dicatat saat settlement.
   - Dibebankan ke Anggota (`fee_bearer = 'member'`): Biaya layanan gateway ditambahkan secara transparan ke total tagihan dan wajib ditampilkan sebelum pembayaran diproses.

### C. Alokasi Pembayaran Berjenjang (*Waterfall Allocation*) & Penanganan Kelebihan Bayar
Saat webhook pembayaran berhasil diterima:
1. **Urutan Alokasi Wajib**:
   $$\text{Denda Keterlambatan} \longrightarrow \text{Bunga Pinjaman} \longrightarrow \text{Pokok Pinjaman}$$
   *(Diprioritaskan untuk jadwal angsuran dengan nomor urut terkecil/terlama).*
2. **Penanganan Kelebihan Bayar (*Overpayment Protection*)**:
   Jika seluruh kewajiban pinjaman telah lunas penuh dan masih terdapat kelebihan dana, sisa dana **secara otomatis dikreditkan ke rekening Simpanan Sukarela** anggota, dengan transaksi setoran tercatat dan mutasi jurnal `Cr 2103`.

### D. Keamanan Webhook & Idempotensi
1. **Pencegahan Pembayaran Ganda (*Double-Payment Prevention*)**:
   - Setiap mutasi pembayaran dieksekusi di dalam `DB::transaction` dengan *row-level locking* (`lockForUpdate()`) pada tabel `gateway_payments` dan `loans`.
2. **Penyimpanan Peristiwa Mentah (*Raw Event Logging*)**:
   - Setiap payload webhook yang masuk disimpan ke tabel `payment_webhook_events` dengan *composite unique key* `(gateway, event_id)`.
   - Jika webhook dengan event ID yang sama atau order yang sudah lunas dikirimkan ulang oleh gateway (*webhook retry*), sistem merespons secara idempotent tanpa menduplikasi saldo atau membukukan jurnal ganda.

### E. Bagan Akun Baru & Jurnal Double-Entry Payment Gateway
Sistem menambahkan dua akun buku besar resmi untuk kliring pembayaran digital:
- **`1301` / `1-1300`**: *Dana dalam Penyelesaian Gateway* (Aset Lancar, Saldo Normal: Debit)
- **`5103` / `5-1100`**: *Biaya Transaksi Payment Gateway* (Beban Operasional, Saldo Normal: Debit)

#### 1. Jurnal Saat Anggota Membayar Berhasil (Notifikasi Webhook)
$$\begin{aligned}
\text{Debit: } & 1301 \text{ (Dana dalam Penyelesaian Gateway)} & \text{Rp } 300.000 \\
\text{Kredit: } & 1201 \text{ (Piutang Pinjaman Anggota - Pokok)} & \text{Rp } 275.000 \\
\text{Kredit: } & 4101 \text{ (Pendapatan Bunga Pinjaman)} & \text{Rp } 20.000 \\
\text{Kredit: } & 4103 \text{ (Pendapatan Denda Keterlambatan)} & \text{Rp } 5.000 \\
\text{Kredit: } & 2103 \text{ (Simpanan Sukarela - Kelebihan Bayar jika ada)} & -
\end{aligned}$$
*(Untuk setoran simpanan sukarela: Debit 1301, Kredit 2103).*

#### 2. Jurnal Saat Dana Dicairkan (Settlement) ke Bank Koperasi
Ketika dana dari payment gateway ditransfer ke rekening bank operasional koperasi setelah dipotong biaya MDR/administrasi:
$$\begin{aligned}
\text{Debit: } & 1102 \text{ (Bank Koperasi - Kas Neto)} & \text{Rp } 992.000 \\
\text{Debit: } & 5103 \text{ (Biaya Transaksi Payment Gateway)} & \text{Rp } 8.000 \\
\text{Kredit: } & 1301 \text{ (Dana dalam Penyelesaian Gateway - Bruto)} & \text{Rp } 1.000.000
\end{aligned}$$
*Keseimbangan Akuntansi: $\text{Kas Neto} + \text{Biaya Gateway} = \text{Dana Kliring Bruto}$.*

### F. Siklus Status Pembayaran & Scheduler Otomatis
1. **Status Tagihan**:
   - `pending`: Menunggu pembayaran anggota sebelum batas kedaluwarsa (default 24 jam).
   - `paid`: Transaksi lunas, alokasi jadwal berhasil, dan jurnal dibukukan.
   - `expired`: Melewati batas waktu toleransi pembayaran.
   - `failed`: Transaksi ditolak atau dibatalkan oleh gateway/anggota.
   - `refunded`: Dana dikembalikan ke anggota.
2. **Scheduler Otomatis**:
   - `koperasi:expire-gateway-invoices` (Berjalan setiap 15 menit): Memeriksa tagihan `pending` yang `expires_at <= now()` dan menandainya sebagai `expired`.
   - `koperasi:reconcile-gateway` (Berjalan setiap 10 menit): Memeriksa tagihan `pending` yang masih aktif ke API gateway untuk mensinkronisasikan transaksi yang pembayarannya telah berhasil di sisi gateway.
