<?php

namespace Database\Factories;

use App\Models\ClassType;
use App\Models\User;
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
            'teacher_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'name' => fake()->randomElement($classNames),
            'description' => fake()->paragraph(),
            // Use rounded prices (e.g., 30.00, 45.00, 70.00) within a realistic range
            // Generate as multiples of 5 or 10 to avoid awkward cents like 52.49
            'price_per_student' => (function () {
                $min = 25;   // minimum price
                $max = 100;  // maximum price
                $steps = [5, 10];
                $step = $steps[array_rand($steps)];

                $minStep = (int) ceil($min / $step);
                $maxStep = (int) floor($max / $step);
                $price = random_int($minStep, $maxStep) * $step; // integer like 30, 45, 70

                return (float) $price; // DB will store as decimal(10,2)
            })(),
        ];
    }
}
