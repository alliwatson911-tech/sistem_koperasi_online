<?php

namespace Tests\Feature;

use App\Models\BillboardAnnouncement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarAndBillboardManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_landing_page_renders_billboard_announcements_and_public_navbar(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        // Memastikan top navbar landing page tetap utuh dengan urutan yang diminta
        $response->assertSee('Dashboard');
        $response->assertSee('Produk');
        $response->assertSee('Panduan');
        $response->assertSee('Simulasi Pinjaman');
        $response->assertSee('Tentang Kami');
        $response->assertSee('Billboard Pengumuman Koperasi');
        $response->assertSee('Papan Informasi Resmi');
        $response->assertSee('RAT & SHU');
    }

    public function test_admin_can_view_billboard_management_index(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($admin)->get(route('admin.billboards.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Billboard Pengumuman Resmi Koperasi');
        $response->assertSee('Tambah Pengumuman Baru');
    }

    public function test_admin_can_create_new_billboard_announcement(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        $response = $this->actingAs($admin)->post(route('admin.billboards.store'), [
            'tab_title' => 'Pengumuman Baru',
            'badge_text' => 'Penting',
            'badge_color' => 'warning',
            'meta_text' => 'Untuk Semua Anggota',
            'title' => 'Judul Pengumuman Baru Koperasi 2026',
            'content' => 'Isi detail pengumuman resmi koperasi untuk seluruh anggota aktif.',
            'status_text' => 'Berlaku Sekarang',
            'link_url' => 'https://koperasi.test',
            'link_text' => 'Pelajari Selengkapnya',
            'order' => 5,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.billboards.index'));
        $this->assertDatabaseHas('billboard_announcements', [
            'tab_title' => 'Pengumuman Baru',
            'title' => 'Judul Pengumuman Baru Koperasi 2026',
            'is_active' => true,
        ]);

        // Cek tampil di landing page
        $landing = $this->get('/');
        $landing->assertSee('Pengumuman Baru');
        $landing->assertSee('Judul Pengumuman Baru Koperasi 2026');
    }

    public function test_admin_can_update_billboard_announcement(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $billboard = BillboardAnnouncement::first();

        $response = $this->actingAs($admin)->put(route('admin.billboards.update', $billboard->id), [
            'tab_title' => 'Tab Diperbarui',
            'badge_text' => 'Update 2026',
            'badge_color' => 'success',
            'meta_text' => 'Diperbarui Admin',
            'title' => 'Judul Setelah Diperbarui',
            'content' => 'Konten billboard setelah berhasil diedit oleh admin koperasi.',
            'status_text' => 'Status Baru',
            'link_url' => '#produk',
            'link_text' => 'Cek Produk',
            'order' => 1,
            'is_active' => '1',
        ]);

        $response->assertRedirect(route('admin.billboards.index'));
        $this->assertDatabaseHas('billboard_announcements', [
            'id' => $billboard->id,
            'tab_title' => 'Tab Diperbarui',
            'title' => 'Judul Setelah Diperbarui',
        ]);
    }

    public function test_admin_can_toggle_billboard_status(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $billboard = BillboardAnnouncement::first();
        $initialStatus = $billboard->is_active;

        $response = $this->actingAs($admin)->post(route('admin.billboards.toggle', $billboard->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('billboard_announcements', [
            'id' => $billboard->id,
            'is_active' => !$initialStatus,
        ]);
    }

    public function test_admin_can_delete_billboard_announcement(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $billboard = BillboardAnnouncement::create([
            'tab_title' => 'Akan Dihapus',
            'badge_text' => 'Hapus',
            'badge_color' => 'gray',
            'title' => 'Pengumuman Sementara',
            'content' => 'Konten sementara yang akan dihapus.',
            'order' => 99,
            'is_active' => true,
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.billboards.destroy', $billboard->id));

        $response->assertRedirect(route('admin.billboards.index'));
        $this->assertDatabaseMissing('billboard_announcements', [
            'id' => $billboard->id,
        ]);
    }

    public function test_non_admin_cannot_access_billboard_management(): void
    {
        $member = User::where('role', User::ROLE_MEMBER)->first();
        $teller = User::where('role', User::ROLE_TELLER)->first();

        // Member 403 Forbidden
        $responseMember = $this->actingAs($member)->get(route('admin.billboards.index'));
        $responseMember->assertStatus(403);

        // Teller 403 Forbidden
        $responseTeller = $this->actingAs($teller)->get(route('admin.billboards.index'));
        $responseTeller->assertStatus(403);
    }

    public function test_internal_dashboard_renders_sidebar_for_authenticated_roles(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        // Memastikan sidebar internal dirender dengan warna solid (tanpa gradasi)
        $response->assertSee('Navigasi Sidebar Stakeholder');
        $response->assertSee('Panel Stakeholder');
        $response->assertSee('Dasbor Operasional');
        $response->assertSee('Kelola Billboard');
        $response->assertSee('Kelola Tunggakan');
        $response->assertSee('Lihat Beranda Publik');
        $response->assertDontSee('bg-gradient-to-b');

        // Memastikan susunan stat cards: 1 kotak besar kiri (Dana Simpanan) & 3 kotak kanan yang interaktif
        $response->assertSee('Total Dana Simpanan Anggota');
        $response->assertSee('Likuiditas Kas Koperasi');
        $response->assertSee(route('savings.index'));
        $response->assertSee('TOTAL ANGGOTA AKTIF');
        $response->assertSee(route('members.index'));
        $response->assertSee('SISA POKOK PINJAMAN');
        $response->assertSee(route('loans.manage'));
        $response->assertSee('PINJAMAN MENUNGGAK (NPL)');
        $response->assertSee(route('reports.overdue'));

        // Footer pada panel stakeholder telah dihilangkan agar ruang kerja bersih & rapi
        $response->assertDontSee('FOOTER OPERASIONAL');
        $response->assertDontSee('Badan Hukum No. AHU-0012345.AH.01.26.TAHUN 2026');
    }

    public function test_target_pages_render_back_to_previous_page_button(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();

        // 1. Halaman Simpanan
        $savings = $this->actingAs($admin)->get(route('savings.index'));
        $savings->assertStatus(200);
        $savings->assertSee('Kembali ke Halaman Sebelumnya');

        // 2. Halaman Kelola Anggota
        $members = $this->actingAs($admin)->get(route('members.index'));
        $members->assertStatus(200);
        $members->assertSee('Kembali ke Halaman Sebelumnya');

        // 3. Halaman Portofolio Pinjaman
        $loans = $this->actingAs($admin)->get(route('loans.manage'));
        $loans->assertStatus(200);
        $loans->assertSee('Kembali ke Halaman Sebelumnya');

        // 4. Halaman Laporan Overdue / NPL
        $overdue = $this->actingAs($admin)->get(route('reports.overdue'));
        $overdue->assertStatus(200);
        $overdue->assertSee('Kembali ke Halaman Sebelumnya');
    }

    public function test_member_dashboard_renders_member_top_navbar_and_footer(): void
    {
        $member = User::where('role', User::ROLE_MEMBER)->first();
        $response = $this->actingAs($member)->get(route('dashboard'));

        $response->assertStatus(200);
        // Memastikan member menggunakan tampilan top navbar sebelumnya
        $response->assertSee('Navigasi Anggota Koperasi');
        $response->assertSee('Dasbor');
        $response->assertSee('Simpanan');
        $response->assertSee('Pinjaman Saya');
        $response->assertSee('Simulasi');
        $response->assertSee('Profil');
        $response->assertSee('MEMBER');
        $response->assertSee('Keluar');
        // Memastikan sidebar internal TIDAK dirender untuk member
        $response->assertDontSee('Navigasi Sidebar Stakeholder');
        // Memastikan footer dikembalikan khusus untuk role member
        $response->assertSee('Developed by');
        $response->assertSee('AHU-0012345.AH.01.26.TAHUN 2026');
    }

    public function test_authenticated_user_on_landing_page_sees_public_top_navbar_with_dashboard_link(): void
    {
        $admin = User::where('role', User::ROLE_ADMIN)->first();
        $response = $this->actingAs($admin)->get('/');

        $response->assertStatus(200);
        // Landing page tetap top navbar
        $response->assertSee('Navigasi Utama Landing Page');
        $response->assertSee('Dasbor Koperasi');
        $response->assertSee('Ubah Billboard');
    }
}

