<?php

namespace Database\Factories;

use App\Enums\AchievementType;
use App\Models\Achievement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Achievement>
 */
class AchievementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'type' => fake()->randomElement(AchievementType::cases()),
            'threshold' => fake()->randomNumber(strict: true)
        ];
    }
}
