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
        
        LearningClass::create([
            'class_type_id' => $groupType->id,
            'name' => 'Math Group Class',
            'description' => 'Group mathematics class for beginners',
            'price_per_student' => 50.00,
        ]);
    }
}
