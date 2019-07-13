<?php

namespace App\Events;

use App\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use Illuminate\Support\Str; 

class NotificationPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $userId;
    protected $text;

    // public function __construct(User $user, $text)
    // {
    //     $this->user = $user;
    //     $this->text = $text;
    // }

    public function __construct($userId,$text)
    {        
        $this->text = $text;
        $this->userId = $userId;
    }

    public function broadcastWith()
    {
        // This must always be an array. Since it will be parsed with json_encode()
        return [
            'id' => Str::uuid(), //$this->user->name,
            'user' => 'Nombre de Usuario', //$this->user->name,
            'userId' => $this->userId,
            'text' => $this->text,
        ];
    }

    public function broadcastAs()
    {
        return 'newNotification';
    }

    // public function broadcastOn()
    // {
    //     return new Channel('notifications');
    // }

    public function broadcastOn()
    {
        return ['notifications'];
    }

    
}