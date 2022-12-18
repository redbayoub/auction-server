<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake()->text(30),
            'description' => fake()->realText(300),
            'startingPrice' => (int) fake()->randomFloat(2, 1, 1000),
            'auction_closes_at' => now()->setMilliseconds(0)->addMinutes((int) fake()->randomFloat(2, 1, 1000)),
            'image' => fake()->imageUrl(),
        ];
    }
}
