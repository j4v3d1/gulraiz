<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderConfirmed extends Notification implements ShouldQueue
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
            ->subject(__('Thanks for your order! Next step: tell us about your business'))
            ->greeting(__('Hi :name,', ['name' => $notifiable->name]))
            ->line(__('Your payment went through and your :template website is booked in.', ['template' => $this->order->siteTemplate?->name ?? __('new')]))
            ->line(__('To get started, fill in a short form with your business details, logo and photos.'))
            ->action(__('Fill in your details'), route('portal.orders.intake.create', $this->order))
            ->line(__('Questions? Just reply to this email.'));
    }
}
