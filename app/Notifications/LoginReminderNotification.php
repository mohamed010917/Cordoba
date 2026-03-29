<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected int $inactiveDays,
    ) {}

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
            ->subject('We miss you at Simple Hotel System')
            ->greeting('Hello '.$notifiable->name.',')
            ->line('It looks like you have not logged in to your client account for '.$this->inactiveDays.' days.')
            ->line('Sign in to review your reservations, account details, and the latest updates from the hotel team.')
            ->action('Sign in to your account', route('login'))
            ->line('If you no longer need access, you can safely ignore this reminder.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'inactive_days' => $this->inactiveDays,
        ];
    }
}
