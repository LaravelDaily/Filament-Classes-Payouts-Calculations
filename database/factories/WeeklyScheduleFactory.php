<?php

namespace Database\Factories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeeklySchedule>
 */
class WeeklyScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $dayOfWeek = fake()->numberBetween(1, 7);
        $startHour = fake()->numberBetween(8, 18);
        $startTime = sprintf('%02d:%02d:00', $startHour, fake()->randomElement([0, 30]));
        $endHour = $startHour + fake()->numberBetween(1, 3);
        $endTime = sprintf('%02d:%02d:00', $endHour, fake()->randomElement([0, 30]));

        return [
            'course_id' => Course::inRandomOrder()->first()?->id ?? Course::factory(),
            'day_of_week' => $dayOfWeek,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }

    public function spanish(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 2, // Tuesday
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
        ]);
    }

    public function spanishThursday(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 4, // Thursday
            'start_time' => '11:00:00',
            'end_time' => '13:00:00',
        ]);
    }

    public function mathMorning(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 1, // Monday
            'start_time' => '09:00:00',
            'end_time' => '10:30:00',
        ]);
    }

    public function englishEvening(): static
    {
        return $this->state(fn (array $attributes) => [
            'day_of_week' => 3, // Wednesday
            'start_time' => '18:00:00',
            'end_time' => '20:00:00',
        ]);
    }
}
