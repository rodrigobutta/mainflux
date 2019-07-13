<?php

namespace App\Jobs;

use App\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Repositories\EmailLogRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Repositories\EmailTemplateRepository;

class SendMailToTaskUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $task;
    protected $slug;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Task $task, $slug)
    {
        $this->task = $task;
        $this->slug = $slug;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(EmailLogRepository $email, EmailTemplateRepository $emailTemplate)
    {
        $template = $emailTemplate->findBySlug($this->slug);
        
        foreach ($this->task->User as $user) {
            $mail_data = $emailTemplate->getContent(['template' => $template,'task' => $this->task,'user' => $user]);

            $mail['email']   = $user->email;
            $mail['subject'] = $mail_data['subject'];
            $body            = $mail_data['body'];

            \Mail::send('emails.email', compact('body'), function ($message) use ($mail) {
                $message->to($mail['email'])->subject($mail['subject']);
            });

            $email->record([
                'to'        => $mail['email'],
                'subject'   => $mail['subject'],
                'body'      => $body,
                'module'    => 'task',
                'module_id' => $this->task->id
            ]);
        }
    }
}
