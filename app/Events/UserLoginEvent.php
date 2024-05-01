<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\Broadcaster;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoginEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $message;
    public $time;
    public $website_link;
    public $login_instructions;
    public $internet_name;
    public $instructions_prohibitions;
    public $internet_password;
    public $apartment_features;
    public $contact_numbers;
    public $access_video;
    public $secret_door;





    /**
     * Create a new event instance.
     */
    public function __construct($message,$time,$apartment)
    {
        $this->message = $message;
        $this->time = 'Your check-in time is today at '. $time;
        $this->website_link=$apartment->website_link;
        $this->login_instructions=$apartment->login_instructions;
        $this->internet_name=$apartment->internet_name;
        $this->internet_password=$apartment->internet_password;
        $this->instructions_prohibitions=$apartment->instructions_prohibitions;
        $this->apartment_features=$apartment->apartment_features;
        $this->contact_numbers=$apartment->contact_numbers;
        $this->access_video=asset('uploads/access_video/' . $apartment->access_video);
        $this->secret_door= $apartment->secret_door;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-channel'),
        ];
    }
}
