<?php
namespace App\Repositories;

use App\JobCategory;
use Illuminate\Validation\ValidationException;

class JobCategoryRepository
{
    protected $job_category;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        JobCategory $job_category
    ) {
        $this->job_category = $job_category;
    }

    /**
     * Get job category query
     *
     * @return JobCategory query
     */
    public function getQuery()
    {
        return $this->job_category;
    }

    /**
     * Count job category
     *
     * @return integer
     */
    public function count()
    {
        return $this->job_category->count();
    }

    /**
     * List all job categories by name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->job_category->all()->pluck('name', 'id')->all();
    }

    /**
     * List all job categories by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->job_category->all()->pluck('id')->all();
    }

    /**
     * List all job categories by name & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->job_category->all(['name', 'id']);
    }

    /**
     * Get all job categories
     *
     * @return array
     */
    public function getAll()
    {
        return $this->job_category->all();
    }

    /**
     * Find job category with given id or throw an error.
     *
     * @param integer $id
     * @return JobCategory
     */
    public function findOrFail($id)
    {
        $job_category = $this->job_category->find($id);

        if (! $job_category) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job_category')]);
        }

        return $job_category;
    }

    /**
     * Paginate all job categories using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'name';
        $order       = isset($params['order']) ? $params['order'] : 'asc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->job_category->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new job category.
     *
     * @param array $params
     * @return JobCategory
     */
    public function create($params)
    {
        return $this->job_category->forceCreate($this->formatParams($params));
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
     * Update given job category.
     *
     * @param JobCategory $job_category
     * @param array $params
     *
     * @return JobCategory
     */
    public function update(JobCategory $job_category, $params)
    {
        $job_category->forceFill($this->formatParams($params, 'update'))->save();

        return $job_category;
    }

    /**
     * Find job category & check it can be deleted or not.
     *
     * @param integer $id
     * @return JobCategory
     */
    public function deletable($id)
    {
        $job_category = $this->findOrFail($id);

        if ($job_category->jobs()->count()) {
            throw ValidationException::withMessages(['message' => trans('job.job_category_has_many_jobs')]);
        }
        
        return $job_category;
    }

    /**
     * Delete job category.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(JobCategory $job_category)
    {
        return $job_category->delete();
    }

    /**
     * Delete multiple job categories.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->job_category->whereIn('id', $ids)->delete();
    }
}
