<?php
namespace App\Repositories;

use App\SubTask;
use Illuminate\Support\Str;
use App\Repositories\TaskRepository;
use App\Repositories\UploadRepository;
use Illuminate\Validation\ValidationException;

class SubTaskRepository
{
    protected $sub_task;
    protected $task;
    protected $upload;
    protected $module = 'sub_task';

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        SubTask $sub_task,
        TaskRepository $task,
        UploadRepository $upload
    ) {
        $this->sub_task = $sub_task;
        $this->task = $task;
        $this->upload = $upload;
    }

    /**
     * Get sub task query
     *
     * @return SubTask query
     */
    public function getQuery()
    {
        return $this->sub_task;
    }

    /**
     * Count sub task
     *
     * @return integer
     */
    public function count()
    {
        return $this->sub_task->count();
    }

    /**
     * List all sub task by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->sub_task->all()->pluck('title', 'id')->all();
    }

    /**
     * List all sub task by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->sub_task->all(['title', 'id']);
    }

    /**
     * Get all sub task
     *
     * @return array
     */
    public function getAll()
    {
        return $this->sub_task->all();
    }

    /**
     * Find sub task with given id or throw an error.
     *
     * @param integer $id
     * @return SubTask
     */
    public function findOrFail($id)
    {
        $sub_task = $this->sub_task->find($id);

        if (! $sub_task) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_sub_task')]);
        }

        return $sub_task;
    }

    /**
     * Find sub task with given uuid or throw an error.
     *
     * @param Task $task
     * @param string $uuid
     * @return SubTask
     */
    public function findByUuidOrFail($task_id, $uuid)
    {
        $sub_task = $this->sub_task->with('userAdded', 'userAdded.profile', 'task')->filterByTaskId($task_id)->filterByUuid($uuid)->first();

        if (! $sub_task) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_sub_task')]);
        }

        return $sub_task;
    }

    /**
     * Paginate all sub task using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($task_id, $params)
    {
        $sort_by            = isset($params['sort_by']) ? $params['sort_by'] : 'title';
        $order              = isset($params['order']) ? $params['order'] : 'asc';
        $page_length        = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->sub_task->with('userAdded', 'userAdded.profile', 'task')->filterByTaskId($task_id)->orderBy($sort_by, $order)->paginate($page_length);
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

        $sub_task = $this->sub_task->forceCreate($this->formatParams($params, $task_id));

        $this->processUpload($sub_task, $params);

        return $sub_task;
    }

    /**
     * Validate unique title with task.
     *
     * @param array $params
     * @param integer $id [default null]
     * @return null
     */

    public function validateTitle($task_id, $params, $id = null)
    {
        $query = $this->sub_task->filterByTaskId($task_id);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        if ($query->filterByExactTitle($params['title'])->count()) {
            throw ValidationException::withMessages(['title' => trans('validation.unique', ['attribute' => trans('task.sub_task_title')])]);
        }
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
            'title'       => isset($params['title']) ? $params['title'] : null,
            'description' => isset($params['description']) ? $params['description'] : null
        ];

        if ($action === 'create') {
            $formatted['task_id']      = $task_id;
            $formatted['user_id']      = \Auth::user()->id;
            $formatted['uuid']         = Str::uuid();
            $formatted['upload_token'] = isset($params['upload_token']) ? $params['upload_token'] : null;
        }

        return $formatted;
    }

    /**
     * Fix sub task attachments
     *
     * @param SubTask $sub_task
     * @param array $params
     * @param string $action
     * @return void
     */
    public function processUpload(SubTask $sub_task, $params = array(), $action = 'create')
    {
        $upload_token = isset($params['upload_token']) ? $params['upload_token'] : null;

        if ($action === 'create') {
            $this->upload->store($this->module, $sub_task->id, $upload_token);
        } else {
            $this->upload->update($this->module, $sub_task->id, $upload_token);
        }
    }

    /**
     * Update given sub task.
     *
     * @param SubTask $sub_task
     * @param array $params
     *
     * @return SubTask
     */
    public function update(SubTask $sub_task, $params)
    {
        $this->validateTitle($sub_task->task_id, $params, $sub_task->id);

        $sub_task->forceFill($this->formatParams($params, null, 'update'))->save();

        $this->processUpload($sub_task, $params, 'update');

        return $sub_task;
    }

    /**
     * Check if authenticated user can edit sub task or not.
     *
     * @param SubTask $sub_task
     * @return void
     */
    public function editable(SubTask $sub_task)
    {
        $task = $this->task->findOrFail($sub_task->task_id);

        // Admin Users, Users with permission to access all the task & User who created the task & User who created the sub task can edit the sub task
        if (\Auth::user()->hasRole(config('system.default_role.admin')) || \Auth::user()->can('access-all-task') || $task->user_id == \Auth::user()->id || $sub_task->user_id == \Auth::user()->id) {
            return;
        }

        throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
    }

    /**
     * Delete sub task.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(SubTask $sub_task)
    {
        return $sub_task->delete();
    }

    /**
     * Delete multiple sub task.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->sub_task->whereIn('id', $ids)->delete();
    }
}
