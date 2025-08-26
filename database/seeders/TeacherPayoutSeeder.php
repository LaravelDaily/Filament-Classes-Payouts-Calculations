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
        $classSchedules = ClassSchedule::all();
        $teachers = User::whereHas('role', function ($query) {
            $query->where('name', 'Teacher');
        })->get();
        
        // Create 40 teacher payouts for various schedules and teachers
        TeacherPayout::factory()->count(40)->create([
            'class_schedule_id' => $classSchedules->random()->id,
            'teacher_id' => $teachers->random()->id,
        ]);
    }
}
