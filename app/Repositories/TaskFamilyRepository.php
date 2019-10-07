<?php
namespace App\Repositories;

use App\TaskFamily;
use Illuminate\Validation\ValidationException;

class TaskFamilyRepository
{
    protected $task_family;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        TaskFamily $task_family
    ) {
        $this->task_family = $task_family;
    }

    /**
     * Get task family query
     *
     * @return TaskFamily query
     */
    public function getQuery()
    {
        return $this->task_family;
    }

    /**
     * Count task family
     *
     * @return integer
     */
    public function count()
    {
        return $this->task_family->count();
    }

    /**
     * List all task familys by name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->task_family->all()->pluck('name', 'id')->all();
    }


    /**
     * List all task family by name & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->task_family->all(['name', 'id']);
    }

      /**
     * List all task familys by task family name & id
     *
     * @return array
     */
    public function listAllFilterById($task_family_ids)
    {
        return $this->task_family->whereIn('id', $task_family_ids)->get()->pluck('name', 'id')->all();
    }

    /**
     * List all task familys by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->task_family->all()->pluck('id')->all();
    }

    /**
     * Get all task familys
     *
     * @return array
     */
    public function getAll()
    {
        return $this->task_family->all();
    }

    /**
     * Find task family with given id or throw an error.
     *
     * @param integer $id
     * @return TaskFamily
     */
    public function findOrFail($id)
    {
        $task_family = $this->task_family->find($id);

        if (! $task_family) {
            throw ValidationException::withMessages(['message' => trans('task-family.could_not_find')]);
        }

        return $task_family;
    }

    /**
     * Paginate all task familys using given params.
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

        return $this->task_family->filterByName($name)->filterByCode($code)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new task family.
     *
     * @param array $params
     * @return TaskFamily
     */
    public function create($params)
    {
        return $this->task_family->forceCreate($this->formatParams($params));
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
     * Update given task family.
     *
     * @param TaskFamily $task_family
     * @param array $params
     *
     * @return TaskFamily
     */
    public function update(TaskFamily $task_family, $params)
    {
        $task_family->forceFill($this->formatParams($params, 'update'))->save();

        return $task_family;
    }

    /**
     * Find task family & check it can be deleted or not.
     *
     * @param integer $id
     * @return TaskFamily
     */
    public function deletable($id)
    {
        $task_family = $this->findOrFail($id);

        if ($task_family->designations()->count()) {
            throw ValidationException::withMessages(['message' => trans('task-family.has_many_designations')]);
        }
        
        return $task_family;
    }

    /**
     * Delete task family.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(TaskFamily $task_family)
    {
        return $task_family->delete();
    }

    /**
     * Delete multiple task familys.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->task_family->whereIn('id', $ids)->delete();
    }
}
