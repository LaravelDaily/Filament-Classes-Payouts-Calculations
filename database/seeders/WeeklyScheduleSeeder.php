<?php

namespace Database\Seeders;

use App\Models\LearningClass;
use App\Models\User;
use App\Models\WeeklySchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WeeklyScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have some learning classes and users first
        if (LearningClass::count() === 0) {
            LearningClass::factory(5)->create();
        }
        
        if (User::count() === 0) {
            User::factory(10)->create();
        }

        $spanishClass = LearningClass::first();
        $mathClass = LearningClass::skip(1)->first();
        $englishClass = LearningClass::skip(2)->first();
        
        $teacher1 = User::first();
        $teacher2 = User::skip(1)->first();
        $teacher3 = User::skip(2)->first();

        // Create realistic weekly schedules
        WeeklySchedule::factory()->spanish()->create([
            'learning_class_id' => $spanishClass->id,
            'teacher_id' => $teacher1->id,
            'start_date' => now()->startOfMonth(),
        ]);

        WeeklySchedule::factory()->spanishThursday()->create([
            'learning_class_id' => $spanishClass->id,
            'teacher_id' => $teacher1->id,
            'start_date' => now()->startOfMonth(),
        ]);

        WeeklySchedule::factory()->mathMorning()->create([
            'learning_class_id' => $mathClass->id,
            'teacher_id' => $teacher2->id,
            'start_date' => now()->startOfMonth(),
        ]);

        WeeklySchedule::factory()->englishEvening()->create([
            'learning_class_id' => $englishClass->id,
            'teacher_id' => $teacher3->id,
            'start_date' => now()->startOfMonth(),
        ]);

        // Create some random additional schedules
        WeeklySchedule::factory(8)->create();
    }
}
