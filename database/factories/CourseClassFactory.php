<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CourseClass>
 */
class CourseClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startHour = fake()->numberBetween(8, 18);
        $startTime = sprintf('%02d:%02d:00', $startHour, fake()->randomElement([0, 30]));
        $endHour = $startHour + fake()->numberBetween(1, 3);
        $endTime = sprintf('%02d:%02d:00', $endHour, fake()->randomElement([0, 30]));

        $studentCount = fake()->numberBetween(0, 20);
        $basePay = config('teacher_pay.base_pay', 50.00);
        $bonusPerStudent = config('teacher_pay.bonus_per_student', 2.50);
        $bonusPay = $studentCount * $bonusPerStudent;
        $totalPay = $basePay + $bonusPay;

        // Default to deterministic values to avoid flaky tests.
        // Tests can explicitly set substitute fields when needed.
        return [
            'course_id' => Course::inRandomOrder()->first()?->id ?? Course::factory(),
            'scheduled_date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'teacher_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'substitute_teacher_id' => null,
            'student_count' => $studentCount,
            'teacher_base_pay' => $basePay,
            'teacher_bonus_pay' => $bonusPay,
            'teacher_total_pay' => $totalPay,
            'substitute_base_pay' => 0,
            'substitute_bonus_pay' => 0,
            'substitute_total_pay' => 0,
        ];
    }
}
