<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Enums\Plan;
use App\Models\Order;
use App\Models\SiteTemplate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'site_template_id' => SiteTemplate::factory(),
            'plan' => Plan::Monthly,
            'status' => OrderStatus::PendingPayment,
            'commitment_accepted_at' => now(),
        ];
    }

    /**
     * A paid order waiting for the client's intake form.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OrderStatus::AwaitingIntake,
        ])->afterMaking(function (Order $order): void {
            $order->paid_at = now();
            $order->commitment_ends_at = $order->plan === Plan::Monthly ? now()->addMonths(12) : null;
        })->afterCreating(function (Order $order): void {
            $order->forceFill(['subscription_type' => "order-{$order->id}"])->save();
        });
    }

    public function withStatus(OrderStatus $status): static
    {
        return $this->paid()->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }

    public function yearly(): static
    {
        return $this->state(fn (array $attributes) => [
            'plan' => Plan::Yearly,
        ]);
    }
}
