<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class TaskAssignation extends Notification implements ShouldQueue
{
    use Queueable;

    // protected $user;
    // protected $task;
    // protected $unid;

    protected $event;

    // public function __construct($unid,$user,$task)
    // {
    //     $this->user = $user;
    //     $this->task = $task;
    //     $this->unid = $unid;
    // }


    public function __construct($event)
    {        
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['database'];
    }
  
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
                    ->line('Thank you for using our application!');
    }
   
    public function toArray($notifiable)
    {

        // return [
        //     // 'id' => $notifiable->id,
        //     'unid' => $this->unid,
        //     'user' => 'USU2: ' . $this->user->name, 
        //     'task_id' => $this->task->id,
        //     'text' => "task assigned to me"
        // ];

        return $this->event->broadcastWith();
    }
}
