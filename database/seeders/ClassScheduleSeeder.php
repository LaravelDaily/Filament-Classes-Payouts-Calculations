<?php

namespace Database\Seeders;

use App\Models\ClassSchedule;
use App\Models\LearningClass;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $learningClass = LearningClass::where('name', 'Math Group Class')->first();
        $teacher = User::where('email', 'john@example.com')->first();
        
        ClassSchedule::create([
            'learning_class_id' => $learningClass->id,
            'scheduled_date' => '2025-09-01',
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'teacher_id' => $teacher->id,
        ]);
    }
}
