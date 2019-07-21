<?php

namespace App\Listeners;

use JWTAuth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
// use Mint\Service\Repositories\InitRepository;

use App\Events\TaskAssigned;
use App\Notifications\TaskAssignation;

class TaskAssignedListener
{
    protected $init;

    
    // public function __construct(InitRepository $init)
    // {
    //     $this->init = $init;
    // }

    public function handle(TaskAssigned $event)
    {
        // $this->init->check();

        $user = $event->user;
        
        // $task = $event->task;
        // $unid = $event->unid;
        // $user->notify(new TaskAssignation($unid,$user,$task));    


        $user->notify(new TaskAssignation($event));    




    }
}
