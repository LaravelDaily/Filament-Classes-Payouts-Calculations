<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use App\Models\WeeklySchedule;
use Illuminate\Database\Seeder;

class WeeklyScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have some courses and users first
        if (Course::count() === 0) {
            Course::factory(5)->create();
        }

        if (User::count() === 0) {
            User::factory(10)->create();
        }

        $spanishCourse = Course::first();
        $mathCourse = Course::skip(1)->first();
        $englishCourse = Course::skip(2)->first();

        $teacher1 = User::first();
        $teacher2 = User::skip(1)->first();
        $teacher3 = User::skip(2)->first();

        // Create realistic weekly schedules
        WeeklySchedule::factory()->spanish()->create([
            'course_id' => $spanishCourse->id,
            'teacher_id' => $teacher1->id,
            'start_date' => now()->startOfMonth(),
        ]);

        WeeklySchedule::factory()->spanishThursday()->create([
            'course_id' => $spanishCourse->id,
            'teacher_id' => $teacher1->id,
            'start_date' => now()->startOfMonth(),
        ]);

        WeeklySchedule::factory()->mathMorning()->create([
            'course_id' => $mathCourse->id,
            'teacher_id' => $teacher2->id,
            'start_date' => now()->startOfMonth(),
        ]);

        WeeklySchedule::factory()->englishEvening()->create([
            'course_id' => $englishCourse->id,
            'teacher_id' => $teacher3->id,
            'start_date' => now()->startOfMonth(),
        ]);

        // Create some random additional schedules
        WeeklySchedule::factory(8)->create();
    }
}
