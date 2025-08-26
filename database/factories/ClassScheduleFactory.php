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
        return [
            'learning_class_id' => LearningClass::inRandomOrder()->first()?->id ?? LearningClass::factory(),
            'scheduled_date' => fake()->dateTimeBetween('now', '+3 months')->format('Y-m-d'),
            'start_time' => fake()->time('H:i:s'),
            'end_time' => fake()->time('H:i:s'),
            'teacher_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'substitute_teacher_id' => fake()->boolean(20) ? (User::inRandomOrder()->first()?->id ?? User::factory()) : null,
        ];
    }
}
