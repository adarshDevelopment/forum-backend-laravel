<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomVerifyEmail extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */

    private string $hash;
    public function __construct(public User $user)
    {
        $this->hash = sha1(string: (string)$user->email);
    }

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
        logger('inside toMail inside CustomVerifyemail');
        return (new MailMessage)
            ->line('Verify your Email Address. custom verification email template')
            ->line('The introduction to the notification.')
            // ->action('Notification Action', url('http://localhost:5173/email/verify/{id}/{hash}'))
            ->action('Notification Action', url('http://127.0.0.1:8000/api/email/verify/' . $this->user->id . '/' . $this->hash))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
