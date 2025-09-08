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

        // Create realistic weekly schedules
        WeeklySchedule::factory()->spanish()->create([
            'course_id' => $spanishCourse->id,
        ]);

        WeeklySchedule::factory()->spanishThursday()->create([
            'course_id' => $spanishCourse->id,
        ]);

        WeeklySchedule::factory()->mathMorning()->create([
            'course_id' => $mathCourse->id,
        ]);

        WeeklySchedule::factory()->englishEvening()->create([
            'course_id' => $englishCourse->id,
        ]);

        // Create some random additional schedules
        WeeklySchedule::factory(8)->create();
    }
}
