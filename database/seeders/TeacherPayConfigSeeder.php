<?php

namespace Database\Seeders;

use App\Models\TeacherPayConfig;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TeacherPayConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teachers = User::whereHas('role', function ($query) {
            $query->where('name', 'Teacher');
        })->get();
        
        // Create pay configs for all teachers
        foreach ($teachers as $teacher) {
            TeacherPayConfig::factory()->create([
                'user_id' => $teacher->id,
            ]);
        }
    }
}
