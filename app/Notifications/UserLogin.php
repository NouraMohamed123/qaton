<?php

namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class UserLogin extends Notification  implements ShouldQueue
{
  use Queueable;

  private $apartment;
  private $message;
  private $time;
    /**
     * Create a new notification instance.
     */
    public function __construct($message,$time,$apartment)
    {
        $this->apartment = $apartment;
        $this->message = $message;
        $this->time = $time;

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
    // public function toArray(object $notifiable): array
    // {
    //     return [
    //         'message' => 'Your check-in time is today at 4 p.m'. $this->apartment->name,
    //     ];
    // }
    public function toArray(object $notifiable): array
    {
        return [
            'message' =>$this->message,
            'time' => 'Your check-in time is today at '. $this->time,
            'website_link'=>$this->apartment->website_link,
            'login_instructions'=>$this->apartment->login_instructions,
            'internet_name'=>$this->apartment->internet_name,
            'internet_password'=>$this->apartment->internet_password,
            'instructions_prohibitions'=>$this->apartment->instructions_prohibitions,
            'apartment_features'=>$this->apartment->apartment_features,
            'contact_numbers'=>$this->apartment->contact_numbers,
            'access_video'=>asset('uploads/access_video/' . $this->apartment->access_video),
            'secret_door'=> $this->apartment->secret_door,
        ];
    }
}
