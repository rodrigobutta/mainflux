<?php
namespace App\Repositories;

use App\TaskFrequency;
use Illuminate\Validation\ValidationException;

class TaskFrequencyRepository
{
    protected $task_frequency;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        TaskFrequency $task_frequency
    ) {
        $this->task_frequency = $task_frequency;
    }

    /**
     * Get task frequency query
     *
     * @return TaskFrequency query
     */
    public function getQuery()
    {
        return $this->task_frequency;
    }

    /**
     * Count task frequency
     *
     * @return integer
     */
    public function count()
    {
        return $this->task_frequency->count();
    }

    /**
     * List all task frequencys by name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->task_frequency->all()->pluck('name', 'id')->all();
    }


    /**
     * List all task frequency by name & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->task_frequency->all(['name', 'id']);
    }

      /**
     * List all task frequencys by task frequency name & id
     *
     * @return array
     */
    public function listAllFilterById($task_frequency_ids)
    {
        return $this->task_frequency->whereIn('id', $task_frequency_ids)->get()->pluck('name', 'id')->all();
    }

    /**
     * List all task frequencys by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->task_frequency->all()->pluck('id')->all();
    }

    /**
     * Get all task frequencys
     *
     * @return array
     */
    public function getAll()
    {
        return $this->task_frequency->all();
    }

    /**
     * Find task frequency with given id or throw an error.
     *
     * @param integer $id
     * @return TaskFrequency
     */
    public function findOrFail($id)
    {
        $task_frequency = $this->task_frequency->find($id);

        if (! $task_frequency) {
            throw ValidationException::withMessages(['message' => trans('task_frequency.could_not_find')]);
        }

        return $task_frequency;
    }

    /**
     * Paginate all task frequencys using given params.
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

        return $this->task_frequency->filterByName($name)->filterByCode($code)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new task frequency.
     *
     * @param array $params
     * @return TaskFrequency
     */
    public function create($params)
    {
        return $this->task_frequency->forceCreate($this->formatParams($params));
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
     * Update given task frequency.
     *
     * @param TaskFrequency $task_frequency
     * @param array $params
     *
     * @return TaskFrequency
     */
    public function update(TaskFrequency $task_frequency, $params)
    {
        $task_frequency->forceFill($this->formatParams($params, 'update'))->save();

        return $task_frequency;
    }

    /**
     * Find task frequency & check it can be deleted or not.
     *
     * @param integer $id
     * @return TaskFrequency
     */
    public function deletable($id)
    {
        $task_frequency = $this->findOrFail($id);

        if ($task_frequency->designations()->count()) {
            throw ValidationException::withMessages(['message' => trans('task_frequency.has_many_designations')]);
        }
        
        return $task_frequency;
    }

    /**
     * Delete task frequency.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(TaskFrequency $task_frequency)
    {
        return $task_frequency->delete();
    }

    /**
     * Delete multiple task frequencys.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->task_frequency->whereIn('id', $ids)->delete();
    }
}
