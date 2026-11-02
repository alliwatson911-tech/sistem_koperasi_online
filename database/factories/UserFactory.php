<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '08' . fake()->numerify('##########'),
            'role' => User::ROLE_MEMBER,
            'is_active' => true,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password123'),
            'remember_token' => Str::random(10),
        ];
    }

    /** Pengguna dengan peran admin. */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_ADMIN]);
    }

    /** Pengguna dengan peran teller. */
    public function teller(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_TELLER]);
    }

    /** Pengguna dengan peran analis. */
    public function analyst(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_ANALYST]);
    }

    /** Pengguna dengan peran manajer. */
    public function manager(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_MANAGER]);
    }

    /** Pengguna dengan peran anggota. */
    public function member(): static
    {
        return $this->state(fn (array $attributes) => ['role' => User::ROLE_MEMBER]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
