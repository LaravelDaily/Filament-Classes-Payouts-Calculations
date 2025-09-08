<?php

namespace Database\Seeders;

use App\Models\TeacherPayout;
use App\Models\User;
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

        if ($teachers->isEmpty()) {
            return;
        }

        $months = ['2025-01', '2025-02', '2025-03', '2025-04', '2025-05'];

        // Create payouts ensuring unique teacher-month combinations
        foreach ($teachers as $teacher) {
            foreach ($months as $month) {
                TeacherPayout::firstOrCreate(
                    [
                        'teacher_id' => $teacher->id,
                        'month' => $month,
                    ],
                    [
                        'total_pay' => fake()->randomFloat(2, 50, 500),
                        'is_paid' => fake()->boolean(30), // 30% chance of being paid
                        'paid_at' => fake()->boolean(30) ? fake()->dateTimeThisYear() : null,
                    ]
                );
            }
        }
    }
}
