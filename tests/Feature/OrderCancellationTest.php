<?php

namespace Tests\Feature;

use App\Actions\CancelOrderSubscription;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Cashier\Subscription;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class OrderCancellationTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancelling_inside_the_minimum_term_schedules_the_end_for_the_term_end(): void
    {
        $this->freezeSecond();
        $order = Order::factory()->withStatus(OrderStatus::Live)->create();
        $order->forceFill(['commitment_ends_at' => now()->addMonths(9)])->save();

        $subscription = $this->subscriptionMock(function (MockInterface $mock) use ($order): void {
            $mock->shouldReceive('cancelAt')
                ->once()
                ->withArgs(fn ($endsAt) => $order->commitment_ends_at->equalTo($endsAt))
                ->andReturnUsing(fn () => tap($mock, fn () => $mock->ends_at = $order->commitment_ends_at));
            $mock->shouldNotReceive('cancel');
        });

        $endsAt = (new CancelOrderSubscription)->handle($this->orderReturning($order, $subscription));

        $this->assertTrue($endsAt->equalTo(now()->addMonths(9)));
        $this->assertNotNull($order->fresh()->cancellation_requested_at);
    }

    public function test_cancelling_after_the_minimum_term_ends_at_the_end_of_the_period(): void
    {
        $order = Order::factory()->withStatus(OrderStatus::Live)->create();
        $order->forceFill(['commitment_ends_at' => now()->subDay()])->save();

        $subscription = $this->subscriptionMock(function (MockInterface $mock): void {
            $mock->shouldReceive('cancel')->once()->andReturnUsing(fn () => tap($mock, fn () => $mock->ends_at = now()->addDays(12)));
            $mock->shouldNotReceive('cancelAt');
        });

        $endsAt = (new CancelOrderSubscription)->handle($this->orderReturning($order, $subscription));

        $this->assertTrue($endsAt->isSameDay(now()->addDays(12)));
    }

    public function test_yearly_orders_cancel_at_the_end_of_the_paid_year(): void
    {
        $order = Order::factory()->yearly()->withStatus(OrderStatus::Live)->create();
        $this->assertNull($order->commitment_ends_at);

        $subscription = $this->subscriptionMock(function (MockInterface $mock): void {
            $mock->shouldReceive('cancel')->once()->andReturnUsing(fn () => tap($mock, fn () => $mock->ends_at = now()->addMonths(5)));
        });

        (new CancelOrderSubscription)->handle($this->orderReturning($order, $subscription));
    }

    public function test_client_cancels_from_their_account(): void
    {
        $order = Order::factory()->withStatus(OrderStatus::Live)->create();

        $this->mock(CancelOrderSubscription::class, function (MockInterface $mock): void {
            $mock->shouldReceive('handle')->once()->andReturn(now()->addMonths(3));
        });

        $this->actingAs($order->user)
            ->post(route('portal.orders.cancellation.store', $order))
            ->assertRedirect(route('portal.orders.show', $order))
            ->assertSessionHas('status');
    }

    public function test_client_cannot_cancel_someone_elses_subscription(): void
    {
        $order = Order::factory()->withStatus(OrderStatus::Live)->create();

        $this->mock(CancelOrderSubscription::class, fn (MockInterface $mock) => $mock->shouldNotReceive('handle'));

        $this->actingAs(User::factory()->create())
            ->post(route('portal.orders.cancellation.store', $order))
            ->assertForbidden();
    }

    public function test_cancellation_cannot_be_requested_twice(): void
    {
        $order = Order::factory()->withStatus(OrderStatus::Live)->create();
        $order->forceFill(['cancellation_requested_at' => now()])->save();

        $this->actingAs($order->user)
            ->post(route('portal.orders.cancellation.store', $order))
            ->assertForbidden();
    }

    private function subscriptionMock(callable $expectations): Subscription&MockInterface
    {
        $mock = Mockery::mock(Subscription::class)->makePartial();
        $expectations($mock);

        return $mock;
    }

    private function orderReturning(Order $order, Subscription $subscription): Order
    {
        $partial = Mockery::mock($order)->makePartial();
        $partial->shouldReceive('subscription')->andReturn($subscription);

        return $partial;
    }
}
