<?php

namespace Tests\Feature;

use App\Models\LoanProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    /**
     * Uji halaman Beranda / Dashboard Publik
     */
    public function test_home_page_is_accessible()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('KSP Sahabat Sejahtera');
        $response->assertSee('Dashboard');
    }

    /**
     * Uji halaman Katalog Produk Lengkap (/produk dan /produk-pinjaman)
     */
    public function test_products_page_is_accessible_with_loans_and_savings()
    {
        $response = $this->get(route('products.index'));

        $response->assertStatus(200);
        $response->assertSee('Katalog Produk Simpanan');
        $response->assertSee('Pinjaman Mingguan Mikro');
        $response->assertSee('Simpanan Pokok');
        $response->assertSee('Simpanan Wajib');
        $response->assertSee('Simpanan Sukarela Berkah');
        $response->assertSee('Simpanan Berjangka');

        // Uji alias /produk-pinjaman
        $aliasResponse = $this->get(route('loans.products'));
        $aliasResponse->assertStatus(200);
        $aliasResponse->assertSee('Katalog Produk Simpanan');
    }

    /**
     * Uji halaman Panduan Layanan (/panduan)
     */
    public function test_guide_page_is_accessible()
    {
        $response = $this->get(route('guide'));

        $response->assertStatus(200);
        $response->assertSee('Panduan Lengkap Layanan Koperasi');
        $response->assertSee('Tata Cara Pendaftaran Anggota');
        $response->assertSee('e-KYC');
        $response->assertSee('Panduan Menabung');
        $response->assertSee('Alur Pengajuan, Persetujuan &amp; Pencairan Pinjaman', false);
        $response->assertSee('Pertanyaan yang Sering Diajukan (FAQ)');
    }

    /**
     * Uji halaman Simulasi Pinjaman (/simulasi dan /simulasi-pinjaman)
     */
    public function test_simulation_page_is_accessible()
    {
        $response = $this->get(route('loans.simulation'));

        $response->assertStatus(200);
        $response->assertSee('Simulasi Perhitungan Angsuran Pinjaman');
        $response->assertSee('Kalkulator Finansial Transparan');

        // Uji alias /simulasi
        $aliasResponse = $this->get(route('loans.simulation.alias'));
        $aliasResponse->assertStatus(200);
        $aliasResponse->assertSee('Simulasi Perhitungan Angsuran Pinjaman');
    }

    /**
     * Uji halaman Tentang Kami (/tentang-kami)
     */
    public function test_about_page_is_accessible()
    {
        $response = $this->get(route('about'));

        $response->assertStatus(200);
        $response->assertSee('Tentang KSP Sahabat Sejahtera');
        $response->assertSee('Legalitas &amp; Pengesahan Resmi', false);
        $response->assertSee('AHU-0012345.AH.01.26.TAHUN 2026');
        $response->assertSee('7 Prinsip Koperasi Indonesia');
        $response->assertSee('Alamat Kantor Pusat');
        $response->assertSee('Makassar');
    }

    /**
     * Uji integritas link navigasi navbar
     */
    public function test_navbar_links_point_to_distinct_pages()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee(route('home'));
        $response->assertSee(route('products.index'));
        $response->assertSee(route('guide'));
        $response->assertSee(route('loans.simulation'));
        $response->assertSee(route('about'));
    }
}

