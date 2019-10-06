<?php

namespace App\Jobs;

use App\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Repositories\EmailLogRepository;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Repositories\EmailTemplateRepository;

class SendMailToJobUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $job;
    protected $slug;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Job $job, $slug)
    {
        $this->job = $job;
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
        
        foreach ($this->job->User as $user) {
            $mail_data = $emailTemplate->getContent(['template' => $template,'job' => $this->job,'user' => $user]);

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
                'module'    => 'job',
                'module_id' => $this->job->id
            ]);
        }
    }
}
