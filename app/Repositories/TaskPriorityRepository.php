<?php
namespace App\Repositories;

use App\TaskPriority;
use Illuminate\Validation\ValidationException;

class TaskPriorityRepository
{
    protected $task_priority;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        TaskPriority $task_priority
    ) {
        $this->task_priority = $task_priority;
    }

    /**
     * Get task priority query
     *
     * @return TaskPriority query
     */
    public function getQuery()
    {
        return $this->task_priority;
    }

    /**
     * Count task priority
     *
     * @return integer
     */
    public function count()
    {
        return $this->task_priority->count();
    }

    /**
     * List all task priorities by name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->task_priority->all()->pluck('name', 'id')->all();
    }

    /**
     * List all task priorities by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->task_priority->all()->pluck('id')->all();
    }

    /**
     * List all task priorities by name & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->task_priority->all(['name', 'id']);
    }

    /**
     * Get all task priorities
     *
     * @return array
     */
    public function getAll()
    {
        return $this->task_priority->all();
    }

    /**
     * Find task priority with given id or throw an error.
     *
     * @param integer $id
     * @return TaskPriority
     */
    public function findOrFail($id)
    {
        $task_priority = $this->task_priority->find($id);

        if (! $task_priority) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task_priority')]);
        }

        return $task_priority;
    }

    /**
     * Paginate all task priorities using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'name';
        $order       = isset($params['order']) ? $params['order'] : 'asc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->task_priority->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new task priority.
     *
     * @param array $params
     * @return TaskPriority
     */
    public function create($params)
    {
        return $this->task_priority->forceCreate($this->formatParams($params));
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $action = 'create')
    {
        $formatted = [
            'name'        => isset($params['name']) ? $params['name'] : null,
            'description' => isset($params['description']) ? $params['description'] : null
        ];

        return $formatted;
    }

    /**
     * Update given task priority.
     *
     * @param TaskPriority $task_priority
     * @param array $params
     *
     * @return TaskPriority
     */
    public function update(TaskPriority $task_priority, $params)
    {
        $task_priority->forceFill($this->formatParams($params, 'update'))->save();

        return $task_priority;
    }

    /**
     * Find task priority & check it can be deleted or not.
     *
     * @param integer $id
     * @return TaskPriority
     */
    public function deletable($id)
    {
        $task_priority = $this->findOrFail($id);

        if ($task_priority->tasks()->count()) {
            throw ValidationException::withMessages(['message' => trans('task.task_priority_has_many_tasks')]);
        }
        
        return $task_priority;
    }

    /**
     * Delete task priority.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(TaskPriority $task_priority)
    {
        return $task_priority->delete();
    }

    /**
     * Delete multiple task priorities.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->task_priority->whereIn('id', $ids)->delete();
    }
}
