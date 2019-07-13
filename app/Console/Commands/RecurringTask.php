<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendMailToTaskUser;
use App\Repositories\TaskRepository;
use App\Repositories\ConfigurationRepository;

class RecurringTask extends Command
{
    protected $config;
    protected $task;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recurring-task';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Recurring Task, Create new task on scheduled time by copying task property & sub task';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        ConfigurationRepository $config,
        TaskRepository $task
    ) {
        parent::__construct();
        $this->config = $config;
        $this->task = $task;
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

        $recurring_tasks = $this->task->getRecurringTaskByDate();
        foreach ($recurring_tasks as $recurring_task) {
            $new_task = $this->task->copy($recurring_task,[
                'task_configuration' => 1,
                'zero_progress' => 1,
                'sub_task' => 1,
                'attachments' => 1,
                'notes' => 1,
                'send_task_assign_email' => 1
            ]);

            SendMailToTaskUser::dispatch($new_task, 'task-assign-email');

            $recurring_task = $this->task->updateNextRecurringDate($recurring_task);
        }

        $this->info('Recurring task created.');
    }
}
