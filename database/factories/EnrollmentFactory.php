<?php

namespace Database\Factories;

use App\Models\LearningClass;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Enrollment>
 */
class EnrollmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('now', '+1 month');
        
        return [
            'student_id' => Student::inRandomOrder()->first()?->id ?? Student::factory(),
            'learning_class_id' => LearningClass::inRandomOrder()->first()?->id ?? LearningClass::factory(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => fake()->boolean(70) ? fake()->dateTimeBetween($startDate, '+6 months')->format('Y-m-d') : null,
        ];
    }
}
