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

class TaskAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $task;
    public $unid;

    public function __construct($user,$task)
    {        
        $this->task = $task;
        $this->user = $user;
        $this->unid = Str::uuid();
    }

    public function broadcastWith()
    {
        // This must always be an array. Since it will be parsed with json_encode()
        return [
            'unid' => $this->unid, //$this->user->name,
            'user' => 'USU: ' . $this->user->name,             
            'taskId' => $this->task->id, 
            'link' => '#customLink', 
            'linkTarget' => '_self', 
            'text' => 'Asignacion de tarea ' . $this->task->title,
        ];
    }

    public function broadcastAs()
    {
        return 'newNotification';
    }

    public function broadcastOn()
    {
        return ['notifications'];
    }

    
}