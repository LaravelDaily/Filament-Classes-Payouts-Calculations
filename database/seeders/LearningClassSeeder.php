<?php

namespace Database\Seeders;

use App\Models\ClassType;
use App\Models\LearningClass;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LearningClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groupType = ClassType::where('name', 'Group')->first();
        $oneOnOneType = ClassType::where('name', 'One-on-One')->first();
        
        // Create 8 group classes
        LearningClass::factory()->count(8)->create([
            'class_type_id' => $groupType->id,
        ]);
        
        // Create 5 one-on-one classes
        LearningClass::factory()->count(5)->create([
            'class_type_id' => $oneOnOneType->id,
        ]);
    }
}
