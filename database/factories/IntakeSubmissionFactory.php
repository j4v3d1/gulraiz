<?php

namespace Database\Factories;

use App\Models\IntakeSubmission;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IntakeSubmission>
 */
class IntakeSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory()->paid(),
            'business_name' => fake()->company(),
            'contact_email' => fake()->companyEmail(),
            'contact_phone' => fake()->phoneNumber(),
            'desired_domain' => fake()->domainName(),
            'about_text' => fake()->paragraph(),
            'services_text' => fake()->paragraph(),
            'colour_preferences' => 'Blue and white',
        ];
    }
}
