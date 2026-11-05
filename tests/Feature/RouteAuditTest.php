<?php

namespace Tests\Feature;

use App\Models\ChartOfAccount;
use App\Models\Loan;
use App\Models\LoanApplication;
use App\Models\LoanProduct;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RouteAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_audit_all_routes_and_views()
    {
        $this->seed();

        $admin = User::where('role', 'admin')->first();
        $manager = User::where('role', 'manager')->first();
        $analyst = User::where('role', 'analyst')->first();
        $teller = User::where('role', 'teller')->first();
        $budi = User::where('email', 'budi@anggota.test')->first();
        $member = $budi->member;
        $sukarelaAccount = $member->savingsAccounts->where('type', 'sukarela')->first();
        $loan = Loan::first();
        $application = LoanApplication::first();

        $routesToTest = [
            ['role' => 'guest', 'user' => null, 'url' => '/'],
            ['role' => 'guest', 'user' => null, 'url' => '/login'],
            ['role' => 'guest', 'user' => null, 'url' => '/daftar'],
            ['role' => 'guest', 'user' => null, 'url' => '/simulasi-pinjaman'],
            ['role' => 'guest', 'user' => null, 'url' => '/produk-pinjaman'],

            ['role' => 'member', 'user' => $budi, 'url' => '/dashboard'],
            ['role' => 'member', 'user' => $budi, 'url' => '/profil-saya'],
            ['role' => 'member', 'user' => $budi, 'url' => '/simpanan'],
            ['role' => 'member', 'user' => $budi, 'url' => "/simpanan/{$sukarelaAccount->id}"],
            ['role' => 'member', 'user' => $budi, 'url' => "/simpanan/{$sukarelaAccount->id}/setor"],
            ['role' => 'member', 'user' => $budi, 'url' => "/simpanan/{$sukarelaAccount->id}/tarik"],
            ['role' => 'member', 'user' => $budi, 'url' => '/pinjaman-saya'],
            ['role' => 'member', 'user' => $budi, 'url' => '/ajukan-pinjaman'],
            ['role' => 'member', 'user' => $budi, 'url' => "/pinjaman/{$loan->id}"],
            ['role' => 'member', 'user' => $budi, 'url' => "/pinjaman/{$loan->id}/bayar"],

            ['role' => 'teller', 'user' => $teller, 'url' => '/dashboard'],
            ['role' => 'teller', 'user' => $teller, 'url' => '/anggota'],
            ['role' => 'teller', 'user' => $teller, 'url' => "/anggota/{$member->id}"],
            ['role' => 'teller', 'user' => $teller, 'url' => '/kelola-pinjaman'],
            ['role' => 'teller', 'user' => $teller, 'url' => '/pengajuan-pinjaman'],
            ['role' => 'teller', 'user' => $teller, 'url' => "/pengajuan-pinjaman/{$application->id}"],
            ['role' => 'teller', 'user' => $teller, 'url' => '/kelola-tunggakan'],
            ['role' => 'analyst', 'user' => $analyst, 'url' => '/kelola-tunggakan'],
            ['role' => 'teller', 'user' => $teller, 'url' => '/laporan/simpanan'],
            ['role' => 'teller', 'user' => $teller, 'url' => '/laporan/pinjaman'],
            ['role' => 'teller', 'user' => $teller, 'url' => '/laporan/tunggakan'],
            ['role' => 'teller', 'user' => $teller, 'url' => '/laporan/arus-kas'],

            ['role' => 'manager', 'user' => $manager, 'url' => '/akuntansi/jurnal'],
            ['role' => 'manager', 'user' => $manager, 'url' => '/akuntansi/buku-besar'],
            ['role' => 'manager', 'user' => $manager, 'url' => '/akuntansi/neraca-saldo'],
            ['role' => 'manager', 'user' => $manager, 'url' => '/akuntansi/shu'],

            ['role' => 'admin', 'user' => $admin, 'url' => '/dashboard'],
            ['role' => 'admin', 'user' => $admin, 'url' => '/akuntansi/jurnal'],
            ['role' => 'admin', 'user' => $admin, 'url' => '/akuntansi/buku-besar'],
            ['role' => 'admin', 'user' => $admin, 'url' => '/akuntansi/neraca-saldo'],
            ['role' => 'admin', 'user' => $admin, 'url' => '/akuntansi/shu'],
        ];

        $failed = [];
        foreach ($routesToTest as $item) {
            if ($item['user']) {
                $this->actingAs($item['user']);
            }
            try {
                $response = $this->get($item['url']);
                if ($response->status() !== 200) {
                    $failed[] = "{$item['role']} @ {$item['url']} returned {$response->status()}";
                }
            } catch (\Throwable $e) {
                $failed[] = "{$item['role']} @ {$item['url']} EXCEPTION: " . $e->getMessage();
            }
        }

        if (!empty($failed)) {
            $this->fail("Audit failed on routes:\n" . implode("\n", $failed));
        }

        $this->assertTrue(true);
    }
}
