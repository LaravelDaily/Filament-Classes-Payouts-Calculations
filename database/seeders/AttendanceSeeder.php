<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classSchedule = ClassSchedule::first();
        $student = Student::where('email', 'alice@example.com')->first();
        
        Attendance::create([
            'class_schedule_id' => $classSchedule->id,
            'student_id' => $student->id,
        ]);
    }
}
