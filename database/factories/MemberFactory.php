<?php

namespace Database\Factories;

use App\Models\Member;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Member>
 */
class MemberFactory extends Factory
{
    protected $model = Member::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'member_no' => 'KSP-' . date('Ym') . '-' . fake()->unique()->numerify('####'),
            'nik' => fake()->unique()->numerify('3201##############'),
            'birth_date' => fake()->dateTimeBetween('-50 years', '-20 years')->format('Y-m-d'),
            'birth_place' => fake()->city(),
            'gender' => fake()->randomElement(['L', 'P']),
            'address' => fake()->address(),
            'occupation' => fake()->jobTitle(),
            'monthly_income' => fake()->numberBetween(3000000, 15000000),
            'bank_name' => 'BCA',
            'bank_account' => fake()->numerify('##########'),
            'status' => Member::STATUS_PENDING,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => Member::STATUS_ACTIVE,
            'activated_at' => now(),
        ]);
    }
}
