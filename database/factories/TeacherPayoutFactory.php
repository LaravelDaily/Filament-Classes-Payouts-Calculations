<?php

namespace Database\Factories;

use App\Models\ClassSchedule;
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
        $basePay = fake()->randomFloat(2, 15, 50);
        $bonusPay = fake()->randomFloat(2, 5, 25);
        $studentCount = fake()->numberBetween(0, 20);
        $isSubstitute = fake()->boolean(20); // 20% chance of being substitute

        return [
            'class_schedule_id' => ClassSchedule::inRandomOrder()->first()?->id ?? ClassSchedule::factory(),
            'teacher_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'month' => fake()->dateTimeBetween('-6 months', '+1 month')->format('Y-m'),
            'student_count' => $studentCount,
            'is_substitute' => $isSubstitute,
            'base_pay' => $basePay,
            'bonus_pay' => $bonusPay,
            'total_pay' => $basePay + $bonusPay,
            'is_paid' => fake()->boolean(30), // 30% chance of being paid
            'paid_at' => fake()->boolean(30) ? fake()->dateTimeBetween('-1 month', 'now') : null,
        ];
    }
}
