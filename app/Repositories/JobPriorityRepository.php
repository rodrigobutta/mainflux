<?php
namespace App\Repositories;

use App\JobPriority;
use Illuminate\Validation\ValidationException;

class JobPriorityRepository
{
    protected $job_priority;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        JobPriority $job_priority
    ) {
        $this->job_priority = $job_priority;
    }

    /**
     * Get job priority query
     *
     * @return JobPriority query
     */
    public function getQuery()
    {
        return $this->job_priority;
    }

    /**
     * Count job priority
     *
     * @return integer
     */
    public function count()
    {
        return $this->job_priority->count();
    }

    /**
     * List all job priorities by name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->job_priority->all()->pluck('name', 'id')->all();
    }

    /**
     * List all job priorities by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->job_priority->all()->pluck('id')->all();
    }

    /**
     * List all job priorities by name & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->job_priority->all(['name', 'id']);
    }

    /**
     * Get all job priorities
     *
     * @return array
     */
    public function getAll()
    {
        return $this->job_priority->all();
    }

    /**
     * Find job priority with given id or throw an error.
     *
     * @param integer $id
     * @return JobPriority
     */
    public function findOrFail($id)
    {
        $job_priority = $this->job_priority->find($id);

        if (! $job_priority) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job_priority')]);
        }

        return $job_priority;
    }

    /**
     * Paginate all job priorities using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'name';
        $order       = isset($params['order']) ? $params['order'] : 'asc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->job_priority->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new job priority.
     *
     * @param array $params
     * @return JobPriority
     */
    public function create($params)
    {
        return $this->job_priority->forceCreate($this->formatParams($params));
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
     * Update given job priority.
     *
     * @param JobPriority $job_priority
     * @param array $params
     *
     * @return JobPriority
     */
    public function update(JobPriority $job_priority, $params)
    {
        $job_priority->forceFill($this->formatParams($params, 'update'))->save();

        return $job_priority;
    }

    /**
     * Find job priority & check it can be deleted or not.
     *
     * @param integer $id
     * @return JobPriority
     */
    public function deletable($id)
    {
        $job_priority = $this->findOrFail($id);

        if ($job_priority->jobs()->count()) {
            throw ValidationException::withMessages(['message' => trans('job.job_priority_has_many_jobs')]);
        }
        
        return $job_priority;
    }

    /**
     * Delete job priority.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(JobPriority $job_priority)
    {
        return $job_priority->delete();
    }

    /**
     * Delete multiple job priorities.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->job_priority->whereIn('id', $ids)->delete();
    }
}
