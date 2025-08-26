<?php

namespace Database\Seeders;

use App\Models\ClassSchedule;
use App\Models\TeacherPayout;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherPayoutSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $classSchedule = ClassSchedule::first();
        $teacher = User::where('email', 'john@example.com')->first();
        
        TeacherPayout::create([
            'class_schedule_id' => $classSchedule->id,
            'teacher_id' => $teacher->id,
            'base_pay' => 20.00,
            'bonus_pay' => 10.00,
            'total_pay' => 30.00,
        ]);
    }
}
