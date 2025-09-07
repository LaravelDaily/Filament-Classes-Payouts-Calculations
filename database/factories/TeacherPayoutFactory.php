<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TeacherPayout>
 */
class TeacherPayoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $totalPay = fake()->randomFloat(2, 100, 500);

        return [
            'teacher_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'month' => fake()->dateTimeBetween('-6 months', '+1 month')->format('Y-m'),
            'total_pay' => $totalPay,
            'is_paid' => fake()->boolean(30), // 30% chance of being paid
            'paid_at' => fake()->boolean(30) ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }
}
