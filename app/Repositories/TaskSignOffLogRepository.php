<?php
namespace App\Repositories;

use App\Task;
use Carbon\Carbon;
use App\Jobs\SendMail;
use App\TaskSignOffLog;
use App\Jobs\SendMailToTaskUser;
use App\Repositories\TaskRepository;
use Illuminate\Validation\ValidationException;

class TaskSignOffLogRepository
{
    protected $task_sign_off_log;
    protected $task;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        TaskSignOffLog $task_sign_off_log,
        TaskRepository $task
    ) {
        $this->task_sign_off_log = $task_sign_off_log;
        $this->task = $task;
    }

    /**
     * Get task sign off log query
     *
     * @return TaskSignOffLog query
     */
    public function getQuery()
    {
        return $this->task_sign_off_log;
    }

    /**
     * Count task sign off log
     *
     * @return integer
     */
    public function count()
    {
        return $this->task_sign_off_log->count();
    }

    /**
     * List all task sign off log by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->task_sign_off_log->all()->pluck('title', 'id')->all();
    }

    /**
     * List all task sign off log by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->task_sign_off_log->all(['title', 'id']);
    }

    /**
     * Get all task sign off log
     *
     * @return array
     */
    public function getAll()
    {
        return $this->task_sign_off_log->all();
    }

    /**
     * Find task sign off log with given id or throw an error.
     *
     * @param integer $id
     * @return TaskSignOff
     */
    public function findOrFail($id)
    {
        $task_sign_off_log = $this->task_sign_off_log->find($id);

        if (! $task_sign_off_log) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task_sign_off_log')]);
        }

        return $task_sign_off_log;
    }

    /**
     * Paginate all task sign off log using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($task_id, $params)
    {
        $sort_by            = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order              = isset($params['order']) ? $params['order'] : 'desc';
        $page_length        = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->task_sign_off_log->with('userAdded', 'userAdded.profile', 'task')->filterbyTaskId($task_id)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Validate input data for sign off request.
     *
     * @param Task $task
     * @param string $status
     * @return void
     */
    public function validateRequest(Task $task, $status = null)
    {
        $assigned_users = $task->user()->pluck('user_id')->all();
        array_push($assigned_users, $task->user_id);

        if ($this->task->getTaskProgress($task) < 100) {
            throw ValidationException::withMessages(['message' => trans('task.task_not_completed')]);
        }

        if (!in_array(\Auth::user()->id, $assigned_users)) {
            throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
        }

        if ($task->sign_off_status === 'approved') {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        if ($status === 'requested' && $task->sign_off_status != null && $task->sign_off_status != 'rejected' && $task->sign_off_status != 'cancelled') {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        if ($status === 'cancelled' && $task->sign_off_status != 'requested') {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }
    }

    /**
     * Validate input data for sign off action.
     *
     * @param Task $task
     * @param string $status
     * @return void
     */
    public function validateAction(Task $task, $status = null)
    {
        if ($this->task->getTaskProgress($task) < 100) {
            throw ValidationException::withMessages(['message' => trans('task.task_not_completed')]);
        }

        if ($status === 'approved' && ($task->sign_off_status === 'approved' || $task->sign_off_status === 'cancelled')) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        if ($status === 'rejected' && ($task->sign_off_status === 'rejected' || $task->sign_off_status === 'cancelled')) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }
    }

    /**
     * Perform sign off request operation.
     *
     * @param Task $task
     * @param array $params
     * @return TaskSignOffLog
     */
    public function request(Task $task, $params)
    {
        $status = isset($params['status']) ? $params['status'] : null;

        $this->validateRequest($task);

        $task_sign_off_log = $this->task_sign_off_log->forceCreate($this->formatParams($params, $task->id));

        $task = $this->updateTask($task_sign_off_log, $task);

        SendMail::dispatch($task->UserAdded->email, [
            'slug'      => ($status == 'requested') ? 'task-sign-off-request' : 'task-sign-off-request-cancel',
            'user'      => $task->UserAdded,
            'task'      => $task,
            'module'    => 'task',
            'module_id' => $task->id
        ]);

        return $task_sign_off_log;
    }

    /**
     * Perform sign off action operation.
     *
     * @param Task $task
     * @param array $params
     * @return TaskSignOffLog
     */
    public function action(Task $task, $params)
    {
        $status = isset($params['status']) ? $params['status'] : null;

        $this->validateAction($task, $status);

        $task_sign_off_log = $this->task_sign_off_log->forceCreate($this->formatParams($params, $task->id));

        $task = $this->updateTask($task_sign_off_log, $task);

        SendMailToTaskUser::dispatch($task, (($status === 'approved') ? 'task-sign-off-approve' : 'task-sign-off-reject'));

        return $task_sign_off_log;
    }

    /**
     * Update task status after sign off request/action.
     *
     * @param Task $task
     * @param TaskSignOffLog $task_sign_off_log
     * @return Task
     */
    private function updateTask(TaskSignOffLog $task_sign_off_log, Task $task)
    {
        $task->completed_at = ($task_sign_off_log->status === 'approved') ? Carbon::now() : null;
        $task->sign_off_status = $task_sign_off_log->status;
        $task->save();

        return $task;
    }

    /**
     * Create a new task.
     *
     * @param array $params
     * @return Task
     */
    public function create($task_id, $params)
    {
        $this->validateTitle($task_id, $params);

        $task_note = $this->task_note->forceCreate($this->formatParams($params, $task_id));

        $this->processUpload($task_note, $params);

        return $task_note;
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $task_id = null, $action = 'create')
    {
        $formatted = [
            'remarks' => isset($params['sign_off_remarks']) ? $params['sign_off_remarks'] : null,
            'status'  => isset($params['status']) ? $params['status'] : null,
            'task_id' => $task_id,
            'user_id' => \Auth::user()->id,
        ];

        return $formatted;
    }
}
