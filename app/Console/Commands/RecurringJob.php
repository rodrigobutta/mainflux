<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendMailToJobUser;
use App\Repositories\JobRepository;
use App\Repositories\ConfigurationRepository;

class RecurringJob extends Command
{
    protected $config;
    protected $job;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Recurring Job, Create new job on scheduled time by copying job property & sub job';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        ConfigurationRepository $config,
        JobRepository $job
    ) {
        parent::__construct();
        $this->config = $config;
        $this->job = $job;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->config->setDefault();

        if (isTestMode()) {
            $this->error(trans('general.restricted_test_mode_action'));
            return;
        }

        $recurring_jobs = $this->job->getRecurringJobByDate();
        foreach ($recurring_jobs as $recurring_job) {
            $new_job = $this->job->copy($recurring_job,[
                'job_configuration' => 1,
                'zero_progress' => 1,
                'sub_job' => 1,
                'attachments' => 1,
                'notes' => 1,
                'send_job_assign_email' => 1
            ]);

            SendMailToJobUser::dispatch($new_job, 'job-assign-email');

            $recurring_job = $this->job->updateNextRecurringDate($recurring_job);
        }

        $this->info('Recurring job created.');
    }
}
