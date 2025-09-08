<?php

namespace Database\Seeders;

use App\Models\ClassType;
use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groupType = ClassType::where('name', 'Group')->first();
        $oneOnOneType = ClassType::where('name', 'One-on-One')->first();

        // Create 8 group classes
        Course::factory()->count(8)->create([
            'class_type_id' => $groupType->id,
        ]);

        // Create 5 one-on-one classes
        Course::factory()->count(5)->create([
            'class_type_id' => $oneOnOneType->id,
        ]);
    }
}
