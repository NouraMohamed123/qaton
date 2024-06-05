<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserLogout extends Notification implements ShouldQueue
{
    use Queueable;
    private $message;
    public $id ;
    private $title;
    /**
     * Create a new notification instance.
     */
    public function __construct($message,$booked,$title)
    {
        $this->message = $message;
        $this->title = $title;
        $this->id = $booked->apartment_id;

    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
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
            'title' => $this->title,
            'message' =>$this->message,
            'id'=>$this->id,
            'key' => 'exit',
        ];
    }
}
