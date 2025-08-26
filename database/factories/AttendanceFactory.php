<?php

namespace Database\Factories;

use App\Models\ClassSchedule;
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
            'class_schedule_id' => ClassSchedule::inRandomOrder()->first()?->id ?? ClassSchedule::factory(),
            'student_id' => Student::inRandomOrder()->first()?->id ?? Student::factory(),
        ];
    }
}
