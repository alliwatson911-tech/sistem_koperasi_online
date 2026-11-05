<?php

use App\Models\BillboardAnnouncement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('billboard_announcements', function (Blueprint $table) {
            $table->id();
            $table->string('tab_title', 100);
            $table->string('badge_text', 100);
            $table->string('badge_color', 50)->default('warning');
            $table->string('meta_text', 150)->nullable();
            $table->string('title', 255);
            $table->text('content');
            $table->string('status_text', 150)->nullable();
            $table->string('link_url', 255)->nullable();
            $table->string('link_text', 100)->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed 4 pengumuman resmi default agar tampilan landing page billboard langsung terisi rapih
        $defaults = [
            [
                'tab_title' => 'RAT & SHU',
                'badge_text' => 'Pengumuman Utama',
                'badge_color' => 'warning',
                'meta_text' => 'Dipublikasikan: 01 November 2026',
                'title' => 'Rapat Anggota Tahunan (RAT) Tahun Buku Berjalan & Alokasi Pembagian SHU',
                'content' => 'Pengurus mengumumkan bahwa Rapat Anggota Tahunan (RAT) telah menetapkan alokasi Sisa Hasil Usaha (SHU) sebesar 40% untuk Jasa Modal dan Jasa Anggota. Anggota yang telah melunasi Simpanan Pokok dan Wajib dapat langsung memeriksa simulasi hak perolehan SHU pada menu akuntansi di dashboard anggota.',
                'status_text' => 'Status: Pelaksanaan Berjalan',
                'link_url' => '#panduan',
                'link_text' => 'Pelajari Alur SHU →',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'tab_title' => 'Modal UMKM',
                'badge_text' => 'Program Permodalan',
                'badge_color' => 'success',
                'meta_text' => 'Berlaku untuk Anggota Aktif',
                'title' => 'Skema Pinjaman Mikro Mingguan Khusus Pelaku Usaha UMKM',
                'content' => 'KSP Sahabat Sejahtera meluncurkan fasilitas pembiayaan mikro mingguan (tenor 4 s.d. 24 minggu) dengan margin anuitas bersahabat mulai 0.40% per minggu. Persyaratan mudah: cukup upload foto tempat usaha saat mengajukan permohonan melalui sistem koperasi online ini.',
                'status_text' => null,
                'link_url' => '#produk',
                'link_text' => 'Lihat Rincian Plafon Produk Pinjaman →',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'tab_title' => 'Pembayaran QRIS & VA',
                'badge_text' => 'Teknologi Pembayaran',
                'badge_color' => 'info',
                'meta_text' => 'Layanan Otomatis 24 Jam',
                'title' => 'Setoran Simpanan Sukarela & Angsuran Melalui QRIS dan Virtual Account Bank',
                'content' => 'Anggota kini dapat melakukan setoran simpanan sukarela dan pembayaran angsuran menggunakan QRIS dinamis serta Virtual Account resmi Bank Mandiri, BRI, BCA, dan BNI. Transaksi terverifikasi otomatis melalui webhook server-to-server tanpa perlu konfirmasi manual bukti transfer.',
                'status_text' => null,
                'link_url' => '#panduan',
                'link_text' => 'Lihat Panduan Pembayaran Digital →',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'tab_title' => 'Hasil Audit',
                'badge_text' => 'Kepatuhan Hukum',
                'badge_color' => 'gray',
                'meta_text' => 'Audit Berkala Kemenkop UKM',
                'title' => 'Hasil Audit Pengawasan Koperasi: Predikat Sehat & Akuntabel',
                'content' => 'Berdasarkan hasil pengawasan dan audit independen tata kelola koperasi, KSP Sahabat Sejahtera dinyatakan berstatus KOPERASI SEHAT dengan rasio likuiditas aman, kepatuhan batas maksimal denda non-compounding, serta pemisahan tugas kewenangan (Maker-Checker Segregation of Duties).',
                'status_text' => null,
                'link_url' => '#tentang-kami',
                'link_text' => 'Informasi Legalitas & Kontak Pengurus →',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($defaults as $item) {
            BillboardAnnouncement::create($item);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billboard_announcements');
    }
};

