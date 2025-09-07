<?php

namespace Database\Seeders;

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
        $teachers = User::whereHas('role', function ($query) {
            $query->where('name', 'Teacher');
        })->get();
        
        // Create 20 teacher payouts for various months and teachers
        TeacherPayout::factory()->count(20)->create([
            'teacher_id' => $teachers->random()->id,
        ]);
    }
}
