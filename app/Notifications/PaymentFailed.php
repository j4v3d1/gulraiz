<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentFailed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->error()
            ->subject(__('Payment failed for order #:id', ['id' => $this->order->id]))
            ->line(__('A payment from :name (:email) failed. Stripe will retry it automatically.', [
                'name' => $this->order->user->name,
                'email' => $this->order->user->email,
            ]))
            ->action(__('View order'), route('admin.orders.show', $this->order));
    }
}
