<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrderReceived extends Notification implements ShouldQueue
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
            ->subject(__('New order #:id: :plan plan', ['id' => $this->order->id, 'plan' => $this->order->plan->label()]))
            ->line(__(':name (:email) has subscribed to the :plan plan.', [
                'name' => $this->order->user->name,
                'email' => $this->order->user->email,
                'plan' => $this->order->plan->label(),
            ]))
            ->line(__('Template: :template', ['template' => $this->order->siteTemplate?->name ?? __('(deleted)')]))
            ->action(__('View order'), route('admin.orders.show', $this->order));
    }
}
