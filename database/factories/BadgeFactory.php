<?php

namespace Database\Factories;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Badge>
 */
class BadgeFactory extends Factory
{

    private static int $order = 1;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name,
            'required_count' => fake()->randomNumber(strict: true),
            'order' => self::$order++,
        ];
    }

    public static function resetOrder(): void
    {
        self::$order = 1;
    }
}
