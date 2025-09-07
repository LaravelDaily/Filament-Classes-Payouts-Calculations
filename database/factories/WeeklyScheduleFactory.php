<?php

namespace Database\Factories;

use App\Models\LearningClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeeklySchedule>
 */
class WeeklyScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dayOfWeek = fake()->numberBetween(1, 7);
        $startHour = fake()->numberBetween(8, 18);
        $startTime = sprintf('%02d:%02d:00', $startHour, fake()->randomElement([0, 30]));
        $endHour = $startHour + fake()->numberBetween(1, 3);
        $endTime = sprintf('%02d:%02d:00', $endHour, fake()->randomElement([0, 30]));
        
        $expectedStudentCount = fake()->numberBetween(5, 20);
        $basePay = fake()->randomFloat(2, 40, 80);
        $bonusPerStudent = fake()->randomFloat(2, 1.50, 4.00);
        
        return [
            'learning_class_id' => LearningClass::inRandomOrder()->first()?->id ?? LearningClass::factory(),
            'teacher_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'substitute_teacher_id' => fake()->boolean(30) ? (User::inRandomOrder()->first()?->id ?? User::factory()) : null,
            'day_of_week' => $dayOfWeek,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'expected_student_count' => $expectedStudentCount,
            'teacher_base_pay' => $basePay,
            'teacher_bonus_per_student' => $bonusPerStudent,
            'substitute_base_pay' => fake()->boolean(30) ? fake()->randomFloat(2, 30, 60) : 0,
            'substitute_bonus_per_student' => fake()->boolean(30) ? fake()->randomFloat(2, 1.00, 3.00) : 0,
            'is_active' => fake()->boolean(90),
            'start_date' => fake()->optional(0.7)->dateTimeBetween('-6 months', 'now'),
            'end_date' => fake()->optional(0.3)->dateTimeBetween('+1 month', '+1 year'),
        ];
    }

    public function spanish(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 2, // Tuesday
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'expected_student_count' => 12,
            'teacher_base_pay' => 60.00,
            'teacher_bonus_per_student' => 3.00,
        ]);
    }

    public function spanishThursday(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 4, // Thursday
            'start_time' => '11:00:00',
            'end_time' => '13:00:00',
            'expected_student_count' => 15,
            'teacher_base_pay' => 60.00,
            'teacher_bonus_per_student' => 3.00,
        ]);
    }

    public function mathMorning(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 1, // Monday
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
            'expected_student_count' => 8,
            'teacher_base_pay' => 50.00,
            'teacher_bonus_per_student' => 2.50,
        ]);
    }

    public function englishEvening(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 3, // Wednesday
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
            'expected_student_count' => 10,
            'teacher_base_pay' => 70.00,
            'teacher_bonus_per_student' => 3.50,
        ]);
    }
}
