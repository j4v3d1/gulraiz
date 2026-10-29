<?php

namespace Database\Factories;

use App\Enums\EditRequestStatus;
use App\Enums\OrderStatus;
use App\Models\EditRequest;
use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EditRequest>
 */
class EditRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory()->withStatus(OrderStatus::Live),
            'description' => fake()->paragraph(),
            'status' => EditRequestStatus::Open,
        ];
    }
}
