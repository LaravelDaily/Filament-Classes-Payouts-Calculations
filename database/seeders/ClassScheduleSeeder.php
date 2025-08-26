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
        $learningClasses = LearningClass::all();
        $teachers = User::whereHas('role', function ($query) {
            $query->where('name', 'Teacher');
        })->get();
        
        // Create 30 class schedules with various classes and teachers
        ClassSchedule::factory()->count(30)->create([
            'learning_class_id' => $learningClasses->random()->id,
            'teacher_id' => $teachers->random()->id,
        ]);
    }
}
