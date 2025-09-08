<?php

namespace Database\Factories;

use App\Models\CourseClass;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'course_class_id' => CourseClass::inRandomOrder()->first()?->id ?? CourseClass::factory(),
            'student_id' => Student::inRandomOrder()->first()?->id ?? Student::factory(),
        ];
    }
}
