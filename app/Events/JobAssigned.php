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

class JobAssigned implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $job;
    public $unid;

    public function __construct($user,$job)
    {   
        $this->unid = Str::uuid();

        $this->job = $job;
        $this->user = $user;        
    }

    public function broadcastWith()
    {        
        return [
            'unid' => $this->unid, //$this->user->name,
            'user' => 'USU: ' . $this->user->name,             
            'jobId' => $this->job->id, 
            'link' => '#customLink', 
            'linkTarget' => '_self', 
            'text' => 'Asignacion de tarea ' . $this->job->title,
        ];
    }

    public function broadcastOn()
    {
        return ['web-notifications'];
    }

    public function broadcastAs()
    {
        return 'newNotification';
    }  
    
}