<?php
namespace App\Repositories;

use App\TaskRelevance;
use Illuminate\Validation\ValidationException;

class TaskRelevanceRepository
{
    protected $task_relevance;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        TaskRelevance $task_relevance
    ) {
        $this->task_relevance = $task_relevance;
    }

    /**
     * Get task relevance query
     *
     * @return TaskRelevance query
     */
    public function getQuery()
    {
        return $this->task_relevance;
    }

    /**
     * Count task relevance
     *
     * @return integer
     */
    public function count()
    {
        return $this->task_relevance->count();
    }

    /**
     * List all task relevances by name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->task_relevance->all()->pluck('name', 'id')->all();
    }


    /**
     * List all task relevance by name & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->task_relevance->all(['name', 'id']);
    }

      /**
     * List all task relevances by task relevance name & id
     *
     * @return array
     */
    public function listAllFilterById($task_relevance_ids)
    {
        return $this->task_relevance->whereIn('id', $task_relevance_ids)->get()->pluck('name', 'id')->all();
    }

    /**
     * List all task relevances by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->task_relevance->all()->pluck('id')->all();
    }

    /**
     * Get all task relevances
     *
     * @return array
     */
    public function getAll()
    {
        return $this->task_relevance->all();
    }

    /**
     * Find task relevance with given id or throw an error.
     *
     * @param integer $id
     * @return TaskRelevance
     */
    public function findOrFail($id)
    {
        $task_relevance = $this->task_relevance->find($id);

        if (! $task_relevance) {
            throw ValidationException::withMessages(['message' => trans('task-relevance.could_not_find')]);
        }

        return $task_relevance;
    }

    /**
     * Paginate all task relevances using given params.
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

        return $this->task_relevance->filterByName($name)->filterByCode($code)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new task relevance.
     *
     * @param array $params
     * @return TaskRelevance
     */
    public function create($params)
    {
        return $this->task_relevance->forceCreate($this->formatParams($params));
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
     * Update given task relevance.
     *
     * @param TaskRelevance $task_relevance
     * @param array $params
     *
     * @return TaskRelevance
     */
    public function update(TaskRelevance $task_relevance, $params)
    {
        $task_relevance->forceFill($this->formatParams($params, 'update'))->save();

        return $task_relevance;
    }

    /**
     * Find task relevance & check it can be deleted or not.
     *
     * @param integer $id
     * @return TaskRelevance
     */
    public function deletable($id)
    {
        $task_relevance = $this->findOrFail($id);

        if ($task_relevance->designations()->count()) {
            throw ValidationException::withMessages(['message' => trans('task-relevance.has_many_designations')]);
        }
        
        return $task_relevance;
    }

    /**
     * Delete task relevance.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(TaskRelevance $task_relevance)
    {
        return $task_relevance->delete();
    }

    /**
     * Delete multiple task relevances.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->task_relevance->whereIn('id', $ids)->delete();
    }
}
