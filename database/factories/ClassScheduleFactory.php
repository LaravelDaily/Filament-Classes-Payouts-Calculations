<?php

namespace Database\Factories;

use App\Models\LearningClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClassSchedule>
 */
class ClassScheduleFactory extends Factory
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

        return [
            'learning_class_id' => LearningClass::inRandomOrder()->first()?->id ?? LearningClass::factory(),
            'scheduled_date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'teacher_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'substitute_teacher_id' => fake()->boolean(20) ? (User::inRandomOrder()->first()?->id ?? User::factory()) : null,
        ];
    }
}
