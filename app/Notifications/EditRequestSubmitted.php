<?php

namespace App\Notifications;

use App\Models\EditRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EditRequestSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public EditRequest $editRequest) {}

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
            ->subject(__('New edit request for order #:id', ['id' => $this->editRequest->order_id]))
            ->line(str($this->editRequest->description)->limit(300)->toString())
            ->action(__('View edit requests'), route('admin.edit-requests.index'));
    }
}
