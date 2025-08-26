<?php

namespace Database\Factories;

use App\Models\ClassType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LearningClass>
 */
class LearningClassFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class_type_id' => ClassType::inRandomOrder()->first()->id,
            'name' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'price_per_student' => fake()->randomFloat(2, 25, 100),
        ];
    }
}
