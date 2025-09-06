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
        
        // Create realistic class schedules for September 2025
        $workdays = [];
        for ($day = 1; $day <= 30; $day++) {
            $date = \Carbon\Carbon::create(2025, 9, $day);
            if ($date->isWeekday()) {
                $workdays[] = $date->format('Y-m-d');
            }
        }
        
        $timeSlots = [
            ['start' => '09:00:00', 'end' => '10:30:00'],
            ['start' => '10:45:00', 'end' => '12:15:00'],
            ['start' => '13:00:00', 'end' => '14:30:00'],
            ['start' => '14:45:00', 'end' => '16:15:00'],
            ['start' => '16:30:00', 'end' => '18:00:00'],
        ];
        
        // Create schedules for each class throughout September
        foreach ($learningClasses as $class) {
            // Each class meets 2-3 times per week
            $meetingsPerWeek = fake()->numberBetween(2, 3);
            $weekCount = 4; // 4 weeks in September
            
            for ($week = 0; $week < $weekCount; $week++) {
                for ($meeting = 0; $meeting < $meetingsPerWeek; $meeting++) {
                    $dayOffset = $meeting * fake()->numberBetween(1, 2); // Space out meetings
                    $weekStart = $week * 7;
                    $dayIndex = $weekStart + $dayOffset;
                    
                    if (isset($workdays[$dayIndex])) {
                        $timeSlot = fake()->randomElement($timeSlots);
                        
                        ClassSchedule::create([
                            'learning_class_id' => $class->id,
                            'scheduled_date' => $workdays[$dayIndex],
                            'start_time' => $timeSlot['start'],
                            'end_time' => $timeSlot['end'],
                            'teacher_id' => $teachers->random()->id,
                            'substitute_teacher_id' => fake()->boolean(15) ? $teachers->random()->id : null,
                        ]);
                    }
                }
            }
        }
    }
}
