<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginFeedbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_login_shows_account_error_when_email_does_not_exist(): void
    {
        $response = $this->from('/login')->post('/login', [
            'email' => 'tidakada@koperasi.id',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'Akun salah.',
        ]);
        $response->assertSessionMissing('errors.password');
    }

    public function test_login_shows_password_error_when_password_is_incorrect(): void
    {
        $user = User::first();

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password-999',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'password' => 'Kata sandi salah.',
        ]);
        $response->assertSessionMissing('errors.email');
    }

    public function test_login_succeeds_with_correct_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'petugas.test@koperasi.id',
            'password' => Hash::make('secret123'),
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email' => 'petugas.test@koperasi.id',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_when_account_is_inactive(): void
    {
        $user = User::factory()->create([
            'email' => 'nonaktif@koperasi.id',
            'password' => Hash::make('secret123'),
            'is_active' => false,
        ]);

        $response = $this->from('/login')->post('/login', [
            'email' => 'nonaktif@koperasi.id',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors([
            'email' => 'Akun Anda dinonaktifkan oleh pengurus koperasi.',
        ]);
    }

    public function test_login_view_renders_exaggerated_motion_and_specific_error_elements(): void
    {
        // 1. Uji tampilan saat error akun salah
        $responseEmailError = $this->followingRedirects()
            ->from('/login')
            ->post('/login', [
                'email' => 'salah@koperasi.id',
                'password' => 'secret123',
            ]);

        $responseEmailError->assertOk();
        $responseEmailError->assertSee('input-exaggerated-shake');
        $responseEmailError->assertSee('Akun salah.');
        $responseEmailError->assertSee('id="emailFeedback"', false);
        $responseEmailError->assertDontSee('Kata sandi salah.');

        // 2. Uji tampilan saat error sandi salah
        $user = User::first();
        $responsePassError = $this->followingRedirects()
            ->from('/login')
            ->post('/login', [
                'email' => $user->email,
                'password' => 'salah-total-999',
            ]);

        $responsePassError->assertOk();
        $responsePassError->assertSee('input-exaggerated-shake');
        $responsePassError->assertSee('Kata sandi salah.');
        $responsePassError->assertSee('id="passwordFeedback"', false);
        $responsePassError->assertDontSee('Akun salah.');
    }
}
