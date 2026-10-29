<?php

namespace Database\Factories;

use App\Models\SiteTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<SiteTemplate>
 */
class SiteTemplateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $name = fake()->unique()->words(2, true),
            'slug' => Str::slug($name),
            'industry' => fake()->randomElement(['Restaurant', 'Salon & Spa', 'Trades', 'Real Estate', 'Fitness', 'Professional Services']),
            'summary' => fake()->sentence(),
            'description' => fake()->paragraphs(2, true),
            'features' => ['Mobile friendly', 'Contact form', 'Google Maps', 'Photo gallery'],
            'demo_url' => fake()->url(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
