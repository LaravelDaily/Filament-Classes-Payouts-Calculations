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
        
        return [
            'class_schedule_id' => ClassSchedule::inRandomOrder()->first()?->id ?? ClassSchedule::factory(),
            'teacher_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'base_pay' => $basePay,
            'bonus_pay' => $bonusPay,
            'total_pay' => $basePay + $bonusPay,
        ];
    }
}
