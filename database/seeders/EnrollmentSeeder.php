<?php

namespace Database\Seeders;

use App\Models\Enrollment;
use App\Models\LearningClass;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $student = Student::where('email', 'alice@example.com')->first();
        $learningClass = LearningClass::where('name', 'Math Group Class')->first();
        
        Enrollment::create([
            'student_id' => $student->id,
            'learning_class_id' => $learningClass->id,
            'start_date' => '2025-09-01',
        ]);
    }
}
