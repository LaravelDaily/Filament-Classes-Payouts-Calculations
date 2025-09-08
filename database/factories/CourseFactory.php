<?php

namespace Database\Factories;

use App\Models\ClassType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $classNames = [
            'Mathematics Fundamentals',
            'Advanced Algebra',
            'Calculus I',
            'Statistics and Probability',
            'Physics Introduction',
            'Chemistry Basics',
            'Biology and Life Sciences',
            'English Literature',
            'Creative Writing',
            'Spanish for Beginners',
            'French Conversation',
            'Computer Science Fundamentals',
            'Web Development',
            'Data Science Introduction',
            'Art and Design',
            'Music Theory',
            'History of World Civilizations',
            'Psychology Introduction',
            'Business Administration',
            'Marketing Principles',
        ];

        return [
            'class_type_id' => ClassType::inRandomOrder()->first()->id,
            'name' => fake()->randomElement($classNames),
            'description' => fake()->paragraph(),
            'price_per_student' => fake()->randomFloat(2, 25, 100),
        ];
    }
}
