<?php
namespace App\Repositories;

use App\TaskCategory;
use Illuminate\Validation\ValidationException;

class TaskCategoryRepository
{
    protected $task_category;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        TaskCategory $task_category
    ) {
        $this->task_category = $task_category;
    }

    /**
     * Get task category query
     *
     * @return TaskCategory query
     */
    public function getQuery()
    {
        return $this->task_category;
    }

    /**
     * Count task category
     *
     * @return integer
     */
    public function count()
    {
        return $this->task_category->count();
    }

    /**
     * List all task categories by name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->task_category->all()->pluck('name', 'id')->all();
    }

    /**
     * List all task categories by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->task_category->all()->pluck('id')->all();
    }

    /**
     * List all task categories by name & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->task_category->all(['name', 'id']);
    }

    /**
     * Get all task categories
     *
     * @return array
     */
    public function getAll()
    {
        return $this->task_category->all();
    }

    /**
     * Find task category with given id or throw an error.
     *
     * @param integer $id
     * @return TaskCategory
     */
    public function findOrFail($id)
    {
        $task_category = $this->task_category->find($id);

        if (! $task_category) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task_category')]);
        }

        return $task_category;
    }

    /**
     * Paginate all task categories using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'name';
        $order       = isset($params['order']) ? $params['order'] : 'asc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->task_category->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new task category.
     *
     * @param array $params
     * @return TaskCategory
     */
    public function create($params)
    {
        return $this->task_category->forceCreate($this->formatParams($params));
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
     * Update given task category.
     *
     * @param TaskCategory $task_category
     * @param array $params
     *
     * @return TaskCategory
     */
    public function update(TaskCategory $task_category, $params)
    {
        $task_category->forceFill($this->formatParams($params, 'update'))->save();

        return $task_category;
    }

    /**
     * Find task category & check it can be deleted or not.
     *
     * @param integer $id
     * @return TaskCategory
     */
    public function deletable($id)
    {
        $task_category = $this->findOrFail($id);

        if ($task_category->tasks()->count()) {
            throw ValidationException::withMessages(['message' => trans('task.task_category_has_many_tasks')]);
        }
        
        return $task_category;
    }

    /**
     * Delete task category.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(TaskCategory $task_category)
    {
        return $task_category->delete();
    }

    /**
     * Delete multiple task categories.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->task_category->whereIn('id', $ids)->delete();
    }
}
