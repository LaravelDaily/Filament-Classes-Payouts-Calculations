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
        $user = User::where('email', 'john@example.com')->first();
        
        TeacherPayConfig::create([
            'user_id' => $user->id,
            'base_pay' => 20.00,
            'bonus_per_student' => 5.00,
        ]);
    }
}
