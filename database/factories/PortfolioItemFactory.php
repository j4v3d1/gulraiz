<?php

namespace Database\Factories;

use App\Models\PortfolioItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PortfolioItem>
 */
class PortfolioItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->company().' website',
            'client_name' => fake()->company(),
            'url' => fake()->url(),
            'summary' => fake()->sentence(12),
            'is_published' => true,
            'sort_order' => 0,
        ];
    }
}
