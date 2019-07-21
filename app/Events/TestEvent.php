<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Str; 

class TestEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $unid;
    public $message;
    public $userid;

    public function __construct($message, $userid)
    {
        $this->unid = Str::uuid();
        
        $this->message = $message;
        $this->userid = $userid;
    }

    public function broadcastWith()
    {        
        return [
            'unid' => $this->unid, 
            'user' => 'USU: ' . $this->userid,
            'link' => '#enappmanejodistinto', 
            'linkTarget' => '_self', 
            'text' => $this->message,
        ];
    }

    public function broadcastOn()
    {
        return ['app-notifications'];
    }

    public function broadcastAs()
    {
        return 'newNotification';
    }

}