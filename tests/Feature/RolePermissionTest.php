<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\Member;
use App\Models\Setting;
use App\Models\User;
use App\Services\SavingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected User $memberUser;
    protected User $tellerUser;
    protected User $analystUser;
    protected User $managerUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        // Akun dasar
        ChartOfAccount::create(['code' => '1101', 'name' => 'Kas Koperasi', 'type' => 'asset', 'category' => 'asset', 'normal_balance' => 'debit']);
        ChartOfAccount::create(['code' => '2101', 'name' => 'Simpanan Pokok', 'type' => 'liability', 'category' => 'liability', 'normal_balance' => 'credit']);
        ChartOfAccount::create(['code' => '2102', 'name' => 'Simpanan Wajib', 'type' => 'liability', 'category' => 'liability', 'normal_balance' => 'credit']);
        ChartOfAccount::create(['code' => '2103', 'name' => 'Simpanan Sukarela', 'type' => 'liability', 'category' => 'liability', 'normal_balance' => 'credit']);

        Setting::set('simpanan_pokok_amount', 100000);
        Setting::set('simpanan_wajib_amount', 25000);

        $this->memberUser = User::factory()->create(['role' => 'member']);
        $member = Member::create([
            'user_id' => $this->memberUser->id,
            'member_no' => 'ANG-ROLE-01',
            'nik' => '3201010101017777',
            'birth_date' => '1995-03-15',
            'birth_place' => 'Semarang',
            'gender' => 'L',
            'address' => 'Jl. Pengurus No. 5',
            'occupation' => 'PNS',
            'monthly_income' => 5000000,
            'status' => Member::STATUS_ACTIVE,
        ]);
        app(SavingsService::class)->createDefaultAccounts($member);

        $this->tellerUser = User::factory()->create(['role' => 'teller']);
        $this->analystUser = User::factory()->create(['role' => 'analyst']);
        $this->managerUser = User::factory()->create(['role' => 'manager']);
        $this->adminUser = User::factory()->create(['role' => 'admin']);
    }

    public function test_guests_are_redirected_to_login()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/simpanan');
        $response->assertRedirect('/login');

        $response = $this->get('/akuntansi/jurnal');
        $response->assertRedirect('/login');
    }

    public function test_member_cannot_access_staff_or_admin_routes()
    {
        $this->actingAs($this->memberUser);

        // Anggota tidak boleh melihat daftar seluruh anggota koperasi
        $response = $this->get('/anggota');
        $response->assertStatus(403);

        // Anggota tidak boleh mengakses jurnal akuntansi internal
        $response = $this->get('/akuntansi/jurnal');
        $response->assertStatus(403);

        // Anggota tidak boleh mengakses neraca saldo
        $response = $this->get('/akuntansi/neraca-saldo');
        $response->assertStatus(403);

        // Anggota tidak boleh mengakses halaman tutup buku SHU
        $response = $this->get('/akuntansi/shu');
        $response->assertStatus(403);
    }

    public function test_member_can_access_member_features()
    {
        $this->actingAs($this->memberUser);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        $response = $this->get('/simpanan');
        $response->assertStatus(200);

        $response = $this->get('/simulasi-pinjaman');
        $response->assertStatus(200);

        $response = $this->get('/pinjaman-saya');
        $response->assertStatus(200);
    }

    public function test_teller_can_access_operational_cash_routes_but_not_accounting()
    {
        $this->actingAs($this->tellerUser);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        $response = $this->get('/anggota');
        $response->assertStatus(200);

        $response = $this->get('/simpanan');
        $response->assertStatus(200);

        // Teller tidak boleh akses tutup buku SHU
        $response = $this->get('/akuntansi/shu');
        $response->assertStatus(403);
    }

    public function test_admin_and_manager_have_full_management_access()
    {
        $this->actingAs($this->adminUser);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        $response = $this->get('/akuntansi/jurnal');
        $response->assertStatus(200);

        $response = $this->get('/akuntansi/neraca-saldo');
        $response->assertStatus(200);

        $response = $this->get('/akuntansi/shu');
        $response->assertStatus(200);

        $response = $this->get('/laporan/simpanan');
        $response->assertStatus(200);

        $response = $this->get('/laporan/pinjaman');
        $response->assertStatus(200);

        $response = $this->get('/laporan/arus-kas');
        $response->assertStatus(200);
    }
}
