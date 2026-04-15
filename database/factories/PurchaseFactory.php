<?php

namespace Database\Factories;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Purchase>
 */
class PurchaseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();

        return [
            'identifier' => fake()->uuid(),
            'user_id' => $user->id,
            'email' => $user->email,
            'amount' => fake()->numberBetween(100, 150_000),
        ];
    }
}
