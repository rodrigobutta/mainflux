<?php
namespace App\Repositories;

use App\TaskNote;
use Illuminate\Support\Str;
use App\Repositories\TaskRepository;
use App\Repositories\UploadRepository;
use Illuminate\Validation\ValidationException;

class TaskNoteRepository
{
    protected $task_note;
    protected $task;
    protected $upload;
    protected $module = 'task_note';

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        TaskNote $task_note,
        TaskRepository $task,
        UploadRepository $upload
    ) {
        $this->task_note = $task_note;
        $this->task = $task;
        $this->upload = $upload;
    }

    /**
     * Get task note query
     *
     * @return TaskNote query
     */
    public function getQuery()
    {
        return $this->task_note;
    }

    /**
     * Count task note
     *
     * @return integer
     */
    public function count()
    {
        return $this->task_note->count();
    }

    /**
     * List all task note by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->task_note->all()->pluck('title', 'id')->all();
    }

    /**
     * List all task note by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->task_note->all(['title', 'id']);
    }

    /**
     * Get all task note
     *
     * @return array
     */
    public function getAll()
    {
        return $this->task_note->all();
    }

    /**
     * Find task note with given id or throw an error.
     *
     * @param integer $id
     * @return TaskNote
     */
    public function findOrFail($id)
    {
        $task_note = $this->task_note->find($id);

        if (! $task_note) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task_note')]);
        }

        return $task_note;
    }

    /**
     * Find task note with given uuid or throw an error.
     *
     * @param Task $task
     * @param string $uuid
     * @return TaskNote
     */
    public function editable($task_id, $uuid)
    {
        $task_note = $this->task_note->with('user','user.profile','task')->filterByTaskId($task_id)->filterByUuid($uuid)->whereUserId(\Auth::user()->id)->first();

        if (! $task_note) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task_note')]);
        }

        return $task_note;
    }

    /**
     * Find task note with given uuid or throw an error.
     *
     * @param Task $task
     * @param string $uuid
     * @return TaskNote
     */
    public function findByUuidOrFail($task_id, $uuid)
    {
        $task_note = $this->task_note->with('user','user.profile','task')->filterByTaskId($task_id)->filterByUuid($uuid)->where(function($q){
            $q->where(function($q1){
                $q1->whereIsPublic(0)->whereUserId(\Auth::user()->id);
            })->orWhere(function($q2){
                $q2->whereIsPublic(1);
            });
        })->first();

        if (! $task_note) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task_note')]);
        }

        return $task_note;
    }

    /**
     * Paginate all task note using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($task_id, $params)
    {
        $sort_by            = isset($params['sort_by']) ? $params['sort_by'] : 'title';
        $order              = isset($params['order']) ? $params['order'] : 'asc';
        $page_length        = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->task_note->with('user','user.profile','task')->filterByTaskId($task_id)->where(function($q){
            $q->where(function($q1){
                $q1->whereIsPublic(0)->whereUserId(\Auth::user()->id);
            })->orWhere(function($q2){
                $q2->whereIsPublic(1);
            });
        })->orderBy($sort_by, $order)->paginate($page_length);
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
     * Validate unique title with task.
     *
     * @param array $params
     * @param integer $id [default null]
     * @return null
     */

    public function validateTitle($task_id, $params, $id = null)
    {
        $query = $this->task_note->filterByTaskId($task_id)->filterByUserId(\Auth::user()->id);

        if ($id) {
            $query->where('id','!=',$id);
        }

        if ($query->filterByExactTitle($params['title'])->count()) {
            throw ValidationException::withMessages(['title' => trans('validation.unique',['attribute' => trans('task.task_note_title')])]);
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
            'description' => isset($params['description']) ? $params['description'] : null,
            'is_public' => (isset($params['is_public']) && $params['is_public']) ? 1 : 0
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
     * Fix task note notes
     *
     * @param TaskNote $task_note
     * @param array $params
     * @param string $action
     * @return void
     */
    public function processUpload(TaskNote $task_note, $params = array(), $action = 'create')
    {
        $upload_token = isset($params['upload_token']) ? $params['upload_token'] : null;

        if ($action === 'create') {
            $this->upload->store($this->module, $task_note->id, $upload_token);
        } else {
            $this->upload->update($this->module, $task_note->id, $upload_token);
        }
    }

    /**
     * Update given task note.
     *
     * @param TaskNote $task_note
     * @param array $params
     *
     * @return TaskNote
     */
    public function update(TaskNote $task_note, $params)
    {
        $this->validateTitle($task_note->task_id, $params, $task_note->id);

        $task_note->forceFill($this->formatParams($params, null, 'update'))->save();

        $this->processUpload($task_note, $params, 'update');

        return $task_note;
    }

    /**
     * Delete task note.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(TaskNote $task_note)
    {
        return $task_note->delete();
    }

    /**
     * Delete multiple task note.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->task_note->whereIn('id', $ids)->delete();
    }
}