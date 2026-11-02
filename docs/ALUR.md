# Dokumentasi Alur Sistem Informasi KSP Sahabat Sejahtera

Dokumen ini menjelaskan alur operasional, peran pengguna, aturan bisnis, dan tata kelola transaksi pada Sistem Informasi Manajemen Koperasi Simpan Pinjam online (KSP Sahabat Sejahtera), dirancang agar mudah dipahami oleh Pengurus, Pengawas, Pengelola, maupun Tim IT.

---

## 1. Peran dan Tanggung Jawab Pengguna

| Peran | Kode Sistem | Tanggung Jawab Utama |
|---|---|---|
| **Anggota** | `member` | Melakukan pendaftaran mandiri, mengunggah dokumen KYC (KTP, Swafoto, Bukti Penghasilan), menyetor simpanan (pokok, wajib, sukarela), melakukan simulasi pinjaman, mengajukan pinjaman online, menandatangani akad digital, membayar angsuran, serta menerima pembagian SHU. |
| **Teller / Petugas Kas** | `teller` | Melakukan verifikasi fisik/data calon anggota, menerima setoran simpanan pokok untuk aktivasi anggota, memproses setoran dan penarikan kas simpanan sukarela, mencairkan dana pinjaman yang telah disetujui, dan menerima/memverifikasi pembayaran angsuran. |
| **Analis Kredit** | `analyst` | Melakukan analisis kelayakan permohonan pinjaman (5C: Character, Capacity, Capital, Collateral, Condition), memeriksa likuiditas simpanan pemohon, dan memberikan rekomendasi (setuju/tolak) beserta catatan mitigasi risiko. |
| **Manajer** | `manager` | Memberikan keputusan akhir persetujuan/penolakan pinjaman berjenjang berdasarkan rekomendasi analis, memantau portofolio pinjaman, rasio kredit bermasalah (NPL), dan menyetujui tutup buku/SHU tahunan. |
| **Admin / Pengurus** | `admin` | Mengelola data master (Bagan Akun/CoA, Produk Pinjaman, Kebijakan Simpanan), memantau jurnal akuntansi otomatis, memantau buku besar dan neraca saldo, mengeksekusi tutup buku akhir periode, dan melihat jejak audit (*audit logs*). |

---

## 2. Prinsip Integritas dan Perlindungan Anggota

Sistem ini dibangun berlandaskan nilai-nilai jati diri koperasi dan prinsip perlindungan konsumen keuangan:
1. **Integritas Moneter Integer**: Seluruh nilai uang disimpan dalam satuan Rupiah bulat (`BIGINT` / `INTEGER`), tanpa pembulatan desimal mengambang (*floating point*), menjamin akurasi mutlak hingga Rp 1.
2. **Double-Entry & Immutability**: Setiap mutasi keuangan menghasilkan jurnal akuntansi otomatis berpasangan dengan total Debit sama dengan total Kredit. Catatan keuangan bersifat *append-only* (tidak dapat diedit atau dihapus). Koreksi kesalahan dilakukan melalui mekanisme jurnal pembalik (*reversing entry*).
3. **Transparansi Tanpa Biaya Tersembunyi**: Anggota dapat melihat simulasi lengkap sebelum mengajukan pinjaman (jadwal angsuran bulanan, porsi pokok, porsi bunga, biaya administrasi, dan total kewajiban).
4. **Bebas dari Praktik Penagihan Predatori**: Sistem tidak memiliki akses ke buku kontak, galeri, atau data perangkat pengguna. Pengingat jatuh tempo dikirimkan secara santun dan profesional melalui notifikasi sistem internal.

---

## 3. Alur Pendaftaran & Verifikasi Anggota

```mermaid
flowchart TD
    A([Calon Anggota Mendaftar Online]) --> B[Mengisi NIK, Biodata Diri, Pekerjaan, dan Kontak]
    B --> C[Mengunggah Dokumen KYC: KTP, Swafoto, & Bukti Penghasilan]
    C --> D[Status Anggota: PENDING]
    D --> E{Teller Memeriksa Berkas KYC}
    E -- Ditolak / Tidak Valid --> E1[Status Ditolak / Meminta Perbaikan Dokumen] --> B
    E -- Terverifikasi Sah --> F[Status Anggota: VERIFIED]
    F --> G[Calon Anggota Menyetor Simpanan Pokok Rp 100.000 ke Teller/Bank]
    G --> H[Teller Mencatat Setoran Simpanan Pokok]
    H --> I[[Sistem Menjurnal Otomatis:<br/>Debit: Kas Koperasi (1101)<br/>Kredit: Simpanan Pokok (2101)]]
    I --> J[Status Anggota: ACTIVE]
    J --> K([Anggota Berhak Menabung & Mengajukan Pinjaman])
```

### State Diagram Keanggotaan
```mermaid
stateDiagram-v2
    [*] --> pending: Pendaftaran Mandiri
    pending --> verified: Teller Memvalidasi Dokumen KYC
    pending --> resigned: Ditolak / Dibatalkan
    verified --> active: Setoran Simpanan Pokok Lunas
    active --> suspended: Pelanggaran / Disiplin Koperasi
    suspended --> active: Pemulihan Status oleh Pengurus
    active --> resigned: Mengundurkan Diri (Simpanan Dikembalikan)
    resigned --> [*]
```

---

## 4. Alur Simpanan Koperasi (Pokok, Wajib, Sukarela)

```mermaid
flowchart TD
    A([Anggota Aktif]) --> B{Pilih Jenis Transaksi}
    
    B -- Setor Simpanan Wajib Bulanan --> C[Teller Memproses Setoran Simpanan Wajib]
    C --> C1[[Jurnal Otomatis:<br/>Debit: Kas/Bank (1101/1102)<br/>Kredit: Simpanan Wajib (2102)]]
    C1 --> C2[Saldo Simpanan Wajib Bertambah]
    
    B -- Setor Simpanan Sukarela --> D[Anggota / Teller Menginput Nominal Setoran]
    D --> D1[[Jurnal Otomatis:<br/>Debit: Kas/Bank (1101/1102)<br/>Kredit: Simpanan Sukarela (2103)]]
    D1 --> D2[Saldo Simpanan Sukarela Bertambah]
    
    B -- Penarikan Simpanan Sukarela --> E[Anggota Mengajukan Penarikan Dana]
    E --> E1{Cek Saldo Mencukupi?}
    E1 -- Tidak --> E2[Transaksi Ditolak: Saldo Tidak Cukup]
    E1 -- Ya --> E3[Teller Menyerahkan Uang Tunai / Transfer]
    E3 --> E4[[Jurnal Otomatis:<br/>Debit: Simpanan Sukarela (2103)<br/>Kredit: Kas/Bank (1101/1102)]]
    E4 --> E5[Saldo Simpanan Sukarela Berkurang]
```

---

## 5. Alur Pengajuan, Verifikasi Teller, Analisis Kredit, dan Pencairan Pinjaman

```mermaid
flowchart TD
    A([Anggota Aktif]) --> B[Simulasi Pinjaman:<br/>Plafon, Tenor, Estimasi Angsuran & Biaya Setara p.a.]
    B --> C[Mengisi Formulir Pengajuan Pinjaman & Tujuan Usaha]
    C --> D[Menyetujui Ketentuan Transparansi & Syarat Pinjaman]
    D --> E[Pengajuan Berstatus: SUBMITTED<br/>Masuk Antrean Penerimaan Teller]
    
    E --> T1{Penugasan Teller}
    T1 -- Otomatis / Diambil Sendiri --> T2[Teller Mengambil / Ditugaskan]
    T2 --> T3{Teller Memverifikasi Checklist Dokumen}
    
    T3 -- Berkas Kurang / Tidak Sah --> T_REV[Status: NEEDS_REVISION<br/>Dikembalikan ke Anggota]
    T_REV --> T_FIX[Anggota Melengkapi Dokumen & Catatan Perbaikan]
    T_FIX --> E
    
    T3 -- Berkas Lengkap & Sah --> T_REC[Status: RECEIVED<br/>Disimpan: Kode Teller, Checklist, Waktu & Catatan]
    
    T_REC --> F[Analis Kredit Melakukan Penilaian Kelayakan 5C]
    F --> G{Hasil Analisis Kredit}
    G -- Tidak Layak --> G1[Analis Menolak dengan Catatan Mitigasi] --> H1[Status: REJECTED]
    G -- Layak --> H[Status: IN_REVIEW dengan Rekomendasi Plafon]
    
    H --> I[Manajer Menelaah Berkas & Rekomendasi Analis<br/>Validasi Maker-Checker: Bukan Teller Penerima / Analis]
    I --> J{Keputusan Akhir Manajer}
    J -- Tolak Pinjaman --> H1
    J -- Setujui Pinjaman --> K[Status: APPROVED<br/>Terbentuk Akun Pinjaman: approved_awaiting_disbursement]
    
    K --> L[Anggota Membaca & Menandatangani Akad Pinjaman Digital]
    L --> M[Teller Memverifikasi Akad & Menyerahkan Kas / Transfer<br/>Validasi Maker-Checker: Bukan Manajer yang Menyetujui]
    M --> N[[Sistem Menjalankan Transaksi Atomik:<br/>1. Potong Biaya Administrasi Otomatis<br/>2. Cairkan Kas Bersih ke Anggota<br/>3. Hasilkan Jadwal Angsuran Otomatis<br/>4. Catat Jurnal Double-Entry Berpasangan]]
    N --> O[Status Pinjaman: ACTIVE]
```

### State Diagram Siklus Pengajuan & Pinjaman
```mermaid
stateDiagram-v2
    [*] --> submitted: Anggota Mengajukan Online
    submitted --> received: Teller Menerima & Dokumen Lengkap
    submitted --> needs_revision: Teller Meminta Kelengkapan Dokumen
    needs_revision --> submitted: Anggota Memperbaiki & Kirim Ulang
    
    received --> in_review: Analis Kredit Merekomendasikan
    received --> rejected: Ditolak oleh Analis Kredit
    
    in_review --> approved: Disetujui Manajer (Maker-Checker Valid)
    in_review --> rejected: Ditolak Manajer
    
    approved --> approved_awaiting_disbursement: Diterbitkan Fasilitas Kredit
    approved_awaiting_disbursement --> active: Ditandatangani Akad & Dicairkan Teller
    
    active --> overdue: Lewat Jatuh Tempo Tanpa Pembayaran Penuh
    overdue --> active: Pembayaran Angsuran Menunggak
    active --> paid_off: Seluruh Pokok & Bunga Lunas / Pelunasan Dipercepat
    overdue --> paid_off: Seluruh Pokok, Bunga & Denda Lunas / Pelunasan Dipercepat
    overdue --> written_off: Dihapusbukukan (Persetujuan Rapat Pengurus)
    paid_off --> [*]
    written_off --> [*]
```

### Sequence Diagram Penerimaan Teller & Maker-Checker
```mermaid
sequenceDiagram
    autonumber
    actor Member as Anggota
    actor Teller as Teller (TLR-001)
    actor Analyst as Analis Kredit (ANL-001)
    actor Manager as Manajer (MGR-001)
    participant System as Sistem KSP (Laravel)
    participant DB as Basis Data (MySQL/Atomic)

    Member->>System: Mengajukan Pinjaman Mandiri
    System->>DB: INSERT loan_applications (status: submitted)
    System->>Teller: Notifikasi Pengajuan Masuk ke Antrean

    Teller->>System: Klaim & Verifikasi Checklist Dokumen (KTP, Slip Gaji, Formulir)
    alt Dokumen Tidak Lengkap
        Teller->>System: requestRevisionByTeller(catatan_revisi)
        System->>DB: UPDATE status: needs_revision
        System->>Member: Notifikasi Pengembalian Berkas & Catatan Teller
        Member->>System: resubmitByMember(perbaikan)
        System->>DB: UPDATE status: submitted
    else Dokumen Lengkap & Sah
        Teller->>System: receiveByTeller(checklist, catatan_penerimaan)
        System->>DB: UPDATE status: received, received_by: TLR-001, received_at: now()
        System->>Member: Notifikasi Pengajuan Telah Diterima Teller
        System->>Analyst: Notifikasi Berkas Siap Dianalisis
    end

    Analyst->>System: reviewByAnalyst('recommend', catatan_5C)
    Note over System: Validasi Maker-Checker:<br/>Analis != Teller Penerima
    System->>DB: UPDATE status: in_review, INSERT loan_approvals
    System->>Manager: Notifikasi Rekomendasi Pinjaman

    Manager->>System: approveByManager('approve', catatan_keputusan)
    Note over System: Validasi Maker-Checker:<br/>Manajer != Teller Penerima && Manajer != Analis
    System->>DB: UPDATE status: approved, INSERT loans (awaiting_disbursement)
    System->>Member: Notifikasi Persetujuan Pinjaman & Tanda Tangan Akad

    Member->>System: signAgreement()
    System->>DB: UPDATE loans (agreed_at: now())
    
    Teller->>System: disburse(loan, 'transfer')
    Note over System: Validasi Maker-Checker:<br/>Pencair Dana != Manajer yang Menyetujui
    System->>DB: DB::transaction: Potong Admin, Buat Jadwal Angsuran, Jurnal Double-Entry
    System->>Member: Dana Diterima, Fasilitas Pinjaman Aktif
```

---

## 6. Alur Pembayaran Angsuran & Penanganan Keterlambatan

```mermaid
flowchart TD
    A([Jadwal Angsuran Jatuh Tempo]) --> B{Anggota Membayar Tepat Waktu?}
    
    B -- Ya --> C[Anggota Membayar via Kasir Teller / Transfer]
    C --> D[[Waterfall Pelunasan Angsuran:<br/>1. Alokasi Pokok Angsuran<br/>2. Alokasi Bunga Angsuran<br/>3. Lebihan Dana masuk ke Simpanan Sukarela]]
    D --> E[[Jurnal Otomatis:<br/>Debit: Kas/Bank (1101/1102)<br/>Kredit: Piutang Pinjaman (1201)<br/>Kredit: Pendapatan Bunga (4101)]]
    E --> F[Status Jadwal: PAID]
    F --> G{Semua Angsuran Lunas?}
    G -- Ya --> H[Status Pinjaman: PAID_OFF (LUNAS)]
    G -- Belum --> I[Status Pinjaman: ACTIVE]
    
    B -- Tidak (Terlambat) --> J[[Scheduler Harian 00:05 WIB:<br/>1. Tandai Jadwal: OVERDUE<br/>2. Tandai Pinjaman: OVERDUE<br/>3. Hitung Denda Harian: Pokok x Tarif x Hari Terlambat<br/>4. Catat ke Tabel Penalties]]
    J --> K[Notifikasi Pengingat Santun & Kekeluargaan dikirim ke Anggota]
    K --> L[Anggota Datang Membayar Tunggakan]
    L --> M[[Waterfall Pembayaran Menunggak:<br/>1. Lunasi Denda Terlebih Dahulu<br/>2. Lunasi Bunga Pinjaman<br/>3. Lunasi Sisa Pokok]]
    M --> N[[Jurnal Otomatis Pembayaran Denda:<br/>Debit: Kas/Bank (1101)<br/>Kredit: Pendapatan Denda (4103)<br/>Kredit: Pendapatan Bunga (4101)<br/>Kredit: Piutang Pinjaman (1201)]]
    N --> O[Status Pinjaman Kembali Menjadi ACTIVE / PAID_OFF]
```

### Alur Pelunasan Dipercepat (*Early Repayment*)
```mermaid
flowchart TD
    ER1([Anggota Memilih 'Pelunasan Dipercepat']) --> ER2[Sistem Menghitung Total Pelunasan:<br/>Sisa Pokok Beredar + Denda Tertunggak]
    ER2 --> ER3[[Pembebasan Bunga Masa Depan:<br/>Seluruh sisa bunga periode belum jatuh tempo dibebaskan 100%]]
    ER3 --> ER4[Anggota / Petugas Mengonfirmasi Pembayaran Pelunasan]
    ER4 --> ER5[[Eksekusi Transaksi Atomik:<br/>1. Lunasi seluruh sisa jadwal angsuran (PAID)<br/>2. Update status pinjaman menjadi PAID_OFF (LUNAS)<br/>3. Catat Jurnal Double-Entry:<br/>Debit: Kas/Bank (1101/1102)<br/>Kredit: Piutang Pinjaman Anggota (1201)<br/>Kredit: Pendapatan Denda (jika ada)<br/>4. Catat Audit Log Pelunasan Dipercepat]]
    ER5 --> ER6([Fasilitas Pinjaman Resmi Ditutup / Lunas])
```

---

## 7. Alur Tutup Buku & Pembagian Sisa Hasil Usaha (SHU)

```mermaid
flowchart TD
    A([Akhir Periode Tahun Buku - 31 Desember]) --> B[Admin / Manajer Membuka Modul SHU]
    B --> C[[Sistem Menghitung Otomatis dari Jurnal:<br/>Total Pendapatan (Akun 4xxx) - Total Beban (Akun 5xxx) = Net SHU]]
    C --> D[[Alokasi Standar AD/ART:<br/>1. Dana Cadangan: 40% (Penguatan Modal)<br/>2. Jasa Modal / Simpanan: 25%<br/>3. Jasa Usaha / Pinjaman: 20%<br/>4. Dana Pengurus & Pengawas: 5%<br/>5. Dana Pendidikan Koperasi: 5%<br/>6. Dana Sosial: 5%]]
    D --> E[Sistem Menghitung Bagian Masing-Masing Anggota Aktif]
    E --> F[Admin Mengeksekusi 'Tutup Buku & Bagikan SHU']
    F --> G[[Eksekusi Transaksi Atomik:<br/>1. Kunci Periode Tahun Buku menjadi CLOSED<br/>2. Salurkan Bagian SHU Anggota ke Rekening Simpanan Sukarela<br/>3. Catat Jurnal Penutup Akuntansi Otomatis:<br/>Debit: Ikhtisar Laba Rugi / Pendapatan<br/>Kredit: Beban Operasional<br/>Kredit: Simpanan Sukarela Anggota (2103)<br/>Kredit: Dana Cadangan Koperasi (3201)]]
    G --> H([Anggota Dapat Langsung Menarik atau Mengendapkan SHU])
```

---

## 8. Alur Payment Gateway, Webhook Idempotensi & Rekonsiliasi Bank (Fase 3)

### Diagram Alur Checkout & Pembayaran Gateway
```mermaid
sequenceDiagram
    autonumber
    actor Member as Anggota
    participant Web as Sistem Web KSP
    participant Gateway as Midtrans / Gateway API
    participant Bank as Bank / e-Wallet

    Member->>Web: Pilih Angsuran / Setoran Simpanan & Pilih Saluran (QRIS / VA)
    Web->>Gateway: createCharge(order_id, amount, method, customer)
    Gateway-->>Web: Response (QR String / VA Number / Expiry)
    Web->>Web: Buat GatewayPayment (status: pending, expires_at: 24 jam)
    Web-->>Member: Tampilkan Halaman Invoice (QRIS interaktif / Nomor VA)
    
    Member->>Bank: Pindai QRIS / Transfer ke Nomor Virtual Account
    Bank->>Gateway: Notifikasi Pembayaran Berhasil (Dana Diterima)
    Gateway->>Web: Webhook HTTP POST: status settlement, signature
```

### Diagram Alur Pemrosesan Webhook Otomatis & Idempotensi
```mermaid
flowchart TD
    WH1([Webhook Masuk dari Gateway]) --> WH2{Verifikasi Signature Valid?}
    WH2 -- Tidak Valid --> WH2Err[Tolak HTTP 400 Bad Request]
    WH2 -- Valid --> WH3{Cek Idempotensi Event Mentah?}
    WH3 -- Pernah Diproses Sebelumnya --> WH3Dup[Abaikan & Balas HTTP 200 OK Idempotent]
    WH3 -- Event Baru --> WH4[[Kunci Baris Basis Data: DB::transaction + lockForUpdate]]
    
    WH4 --> WH5{Status Gateway?}
    WH5 -- Expire / Cancel / Deny --> WH5A[Update Status GatewayPayment: expired / failed]
    WH5 -- Settlement / Paid --> WH6[[Eksekusi Alokasi Finansial Waterfall]]
    
    WH6 --> WH7[[Urutan Waterfall:<br/>1. Lunasi Denda Tertunggak<br/>2. Lunasi Bunga Pinjaman<br/>3. Lunasi Pokok Pinjaman<br/>4. Lebihan Dana masuk ke Simpanan Sukarela (Cr 2103)]]
    WH7 --> WH8[[Catat Jurnal Pembayaran Gateway Otomatis:<br/>Debit: Dana dalam Penyelesaian Gateway (1301)<br/>Kredit: Piutang Pinjaman Anggota (1201)<br/>Kredit: Pendapatan Bunga (4101)<br/>Kredit: Pendapatan Denda (4103)<br/>Kredit: Simpanan Sukarela (2103 jika ada lebih bayar)]]
    WH8 --> WH9[Update Status GatewayPayment: PAID]
    WH9 --> WH10[Kirim Notifikasi Real-Time ke Anggota & Terbitkan Kuitansi Resmi]
```

### Diagram Alur Rekonsiliasi Settlement ke Bank Koperasi
```mermaid
flowchart LR
    S1[Transaksi Paid Terkumpul di Akun 1301 Kliring] --> S2[Manajer/Admin Memilih Batch Transaksi untuk Dicairkan]
    S2 --> S3[[Sistem Menghitung:<br/>Total Bruto (Cr 1301)<br/>Total Biaya Fee (Dr 5103)<br/>Neto Masuk Bank (Dr 1102)]]
    S3 --> S4[[Catat Jurnal Settlement Otomatis:<br/>Debit: Bank Koperasi 1102 (Neto)<br/>Debit: Biaya Gateway 5103 (Fee MDR)<br/>Kredit: Dana Penyelesaian 1301 (Bruto)]]
    S4 --> S5([Saldo Kas Bank Sinkron dengan Mutasi Rekening Koran])
```

