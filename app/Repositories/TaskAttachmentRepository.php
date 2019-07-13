<?php
namespace App\Repositories;

use App\TaskAttachment;
use Illuminate\Support\Str;
use App\Repositories\TaskRepository;
use App\Repositories\UploadRepository;
use Illuminate\Validation\ValidationException;

class TaskAttachmentRepository
{
    protected $task_attachment;
    protected $task;
    protected $upload;
    protected $module = 'task_attachment';

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        TaskAttachment $task_attachment,
        TaskRepository $task,
        UploadRepository $upload
    ) {
        $this->task_attachment = $task_attachment;
        $this->task = $task;
        $this->upload = $upload;
    }

    /**
     * Get task attachment query
     *
     * @return TaskAttachment query
     */
    public function getQuery()
    {
        return $this->task_attachment;
    }

    /**
     * Count task attachment
     *
     * @return integer
     */
    public function count()
    {
        return $this->task_attachment->count();
    }

    /**
     * List all task attachment by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->task_attachment->all()->pluck('title', 'id')->all();
    }

    /**
     * List all task attachment by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->task_attachment->all(['title', 'id']);
    }

    /**
     * Get all task attachment
     *
     * @return array
     */
    public function getAll()
    {
        return $this->task_attachment->all();
    }

    /**
     * Find task attachment with given id or throw an error.
     *
     * @param integer $id
     * @return TaskAttachment
     */
    public function findOrFail($id)
    {
        $task_attachment = $this->task_attachment->with('user', 'user.profile', 'task')->find($id);

        if (! $task_attachment) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task_attachment')]);
        }

        return $task_attachment;
    }

    /**
     * Find task attachment with given uuid or throw an error.
     *
     * @param Task $task
     * @param string $uuid
     * @return TaskAttachment
     */
    public function findByUuidOrFail($task_id, $uuid)
    {
        $task_attachment = $this->task_attachment->with('user', 'user.profile', 'task')->filterByTaskId($task_id)->filterByUuid($uuid)->first();

        if (! $task_attachment) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task_attachment')]);
        }

        return $task_attachment;
    }

    /**
     * Paginate all task attachment using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($task_id, $params)
    {
        $sort_by            = isset($params['sort_by']) ? $params['sort_by'] : 'title';
        $order              = isset($params['order']) ? $params['order'] : 'asc';
        $page_length        = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->task_attachment->with('user', 'user.profile', 'task')->filterByTaskId($task_id)->orderBy($sort_by, $order)->paginate($page_length);
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

        $task_attachment = $this->task_attachment->forceCreate($this->formatParams($params, $task_id));

        $this->processUpload($task_attachment, $params);

        return $task_attachment;
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
        $query = $this->task_attachment->filterByTaskId($task_id)->filterByUserId(\Auth::user()->id);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        if ($query->filterByExactTitle($params['title'])->count()) {
            throw ValidationException::withMessages(['title' => trans('validation.unique', ['attribute' => trans('task.task_attachment_title')])]);
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
     * Fix task attachment attachments
     *
     * @param TaskAttachment $task_attachment
     * @param array $params
     * @param string $action
     * @return void
     */
    public function processUpload(TaskAttachment $task_attachment, $params = array(), $action = 'create')
    {
        $upload_token = isset($params['upload_token']) ? $params['upload_token'] : null;

        if ($action === 'create') {
            $this->upload->store($this->module, $task_attachment->id, $upload_token);
        } else {
            $this->upload->update($this->module, $task_attachment->id, $upload_token);
        }
    }

    /**
     * Update given task attachment.
     *
     * @param TaskAttachment $task_attachment
     * @param array $params
     *
     * @return TaskAttachment
     */
    public function update(TaskAttachment $task_attachment, $params)
    {
        $this->validateTitle($task_attachment->task_id, $params, $task_attachment->id);

        $task_attachment->forceFill($this->formatParams($params, null, 'update'))->save();

        $this->processUpload($task_attachment, $params, 'update');

        return $task_attachment;
    }

    /**
     * Delete task attachment.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(TaskAttachment $task_attachment)
    {
        return $task_attachment->delete();
    }

    /**
     * Delete multiple task attachment.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->task_attachment->whereIn('id', $ids)->delete();
    }
}
