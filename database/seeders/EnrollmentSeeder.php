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
        $students = Student::all();
        $learningClasses = LearningClass::all();
        
        // Create realistic enrollments - ensure each class has multiple students
        foreach ($learningClasses as $class) {
            // Each class gets 3-8 students enrolled
            $numberOfStudents = fake()->numberBetween(3, 8);
            $enrolledStudents = $students->random($numberOfStudents);
            
            foreach ($enrolledStudents as $student) {
                // Check if this student is already enrolled in this class to avoid duplicates
                if (!Enrollment::where('student_id', $student->id)
                    ->where('learning_class_id', $class->id)
                    ->exists()) {
                    
                    Enrollment::create([
                        'student_id' => $student->id,
                        'learning_class_id' => $class->id,
                        'start_date' => '2025-09-01', // September start
                        'end_date' => '2025-12-20', // December end
                    ]);
                }
            }
        }
        
        // Also create some additional random enrollments to ensure good data variety
        $additionalEnrollments = fake()->numberBetween(10, 20);
        for ($i = 0; $i < $additionalEnrollments; $i++) {
            $student = $students->random();
            $class = $learningClasses->random();
            
            // Check if this student is already enrolled in this class
            if (!Enrollment::where('student_id', $student->id)
                ->where('learning_class_id', $class->id)
                ->exists()) {
                
                Enrollment::create([
                    'student_id' => $student->id,
                    'learning_class_id' => $class->id,
                    'start_date' => fake()->dateTimeBetween('2025-09-01', '2025-09-15')->format('Y-m-d'),
                    'end_date' => fake()->dateTimeBetween('2025-11-01', '2025-12-20')->format('Y-m-d'),
                ]);
            }
        }
    }
}
