<?php
namespace App\Repositories;

use App\TaskComplexity;
use Illuminate\Validation\ValidationException;

class TaskComplexityRepository
{
    protected $task_complexity;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        TaskComplexity $task_complexity
    ) {
        $this->task_complexity = $task_complexity;
    }

    /**
     * Get task complexity query
     *
     * @return TaskComplexity query
     */
    public function getQuery()
    {
        return $this->task_complexity;
    }

    /**
     * Count task complexity
     *
     * @return integer
     */
    public function count()
    {
        return $this->task_complexity->count();
    }

    /**
     * List all task complexitys by name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->task_complexity->all()->pluck('name', 'id')->all();
    }


    /**
     * List all task complexity by name & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->task_complexity->all(['name', 'id']);
    }

      /**
     * List all task complexitys by task complexity name & id
     *
     * @return array
     */
    public function listAllFilterById($task_complexity_ids)
    {
        return $this->task_complexity->whereIn('id', $task_complexity_ids)->get()->pluck('name', 'id')->all();
    }

    /**
     * List all task complexitys by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->task_complexity->all()->pluck('id')->all();
    }

    /**
     * Get all task complexitys
     *
     * @return array
     */
    public function getAll()
    {
        return $this->task_complexity->all();
    }

    /**
     * Find task complexity with given id or throw an error.
     *
     * @param integer $id
     * @return TaskComplexity
     */
    public function findOrFail($id)
    {
        $task_complexity = $this->task_complexity->find($id);

        if (! $task_complexity) {
            throw ValidationException::withMessages(['message' => trans('task-complexity.could_not_find')]);
        }

        return $task_complexity;
    }

    /**
     * Paginate all task complexitys using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order       = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $name        = isset($params['name']) ? $params['name'] : '';
        $code        = isset($params['code']) ? $params['code'] : '';

        return $this->task_complexity->filterByName($name)->filterByCode($code)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new task complexity.
     *
     * @param array $params
     * @return TaskComplexity
     */
    public function create($params)
    {
        return $this->task_complexity->forceCreate($this->formatParams($params));
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
            'code'        => isset($params['code']) ? $params['code'] : null,
            'description' => isset($params['description']) ? $params['description'] : null
        ];

        return $formatted;
    }

    /**
     * Update given task complexity.
     *
     * @param TaskComplexity $task_complexity
     * @param array $params
     *
     * @return TaskComplexity
     */
    public function update(TaskComplexity $task_complexity, $params)
    {
        $task_complexity->forceFill($this->formatParams($params, 'update'))->save();

        return $task_complexity;
    }

    /**
     * Find task complexity & check it can be deleted or not.
     *
     * @param integer $id
     * @return TaskComplexity
     */
    public function deletable($id)
    {
        $task_complexity = $this->findOrFail($id);

        if ($task_complexity->designations()->count()) {
            throw ValidationException::withMessages(['message' => trans('task-complexity.has_many_designations')]);
        }
        
        return $task_complexity;
    }

    /**
     * Delete task complexity.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(TaskComplexity $task_complexity)
    {
        return $task_complexity->delete();
    }

    /**
     * Delete multiple task complexitys.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->task_complexity->whereIn('id', $ids)->delete();
    }
}
