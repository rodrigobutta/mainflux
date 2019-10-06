<?php

namespace App\Listeners;

use JWTAuth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
// use Mint\Service\Repositories\InitRepository;

use App\Events\JobAssigned;
use App\Notifications\JobAssignation;

class JobAssignedListener
{
    protected $init;

    
    // public function __construct(InitRepository $init)
    // {
    //     $this->init = $init;
    // }

    public function handle(JobAssigned $event)
    {
        // $this->init->check();

        $user = $event->user;
        
        // $job = $event->job;
        // $unid = $event->unid;
        // $user->notify(new JobAssignation($unid,$user,$job));    


        $user->notify(new JobAssignation($event));    




    }
}
