<?php

namespace Database\Factories;

use App\Models\Course;
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
        $startDate = fake()->dateTimeBetween('2025-09-01', '2025-09-15');

        return [
            'student_id' => Student::inRandomOrder()->first()?->id ?? Student::factory(),
            'course_id' => Course::inRandomOrder()->first()?->id ?? Course::factory(),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => fake()->dateTimeBetween($startDate, '2025-12-20')->format('Y-m-d'),
        ];
    }
}
