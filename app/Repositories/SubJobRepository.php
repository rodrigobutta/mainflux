<?php
namespace App\Repositories;

use App\SubJob;
use Illuminate\Support\Str;
use App\Repositories\JobRepository;
use App\Repositories\UploadRepository;
use Illuminate\Validation\ValidationException;

class SubJobRepository
{
    protected $sub_job;
    protected $job;
    protected $upload;
    protected $module = 'sub_job';

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        SubJob $sub_job,
        JobRepository $job,
        UploadRepository $upload
    ) {
        $this->sub_job = $sub_job;
        $this->job = $job;
        $this->upload = $upload;
    }

    /**
     * Get sub job query
     *
     * @return SubJob query
     */
    public function getQuery()
    {
        return $this->sub_job;
    }

    /**
     * Count sub job
     *
     * @return integer
     */
    public function count()
    {
        return $this->sub_job->count();
    }

    /**
     * List all sub job by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->sub_job->all()->pluck('title', 'id')->all();
    }

    /**
     * List all sub job by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->sub_job->all(['title', 'id']);
    }

    /**
     * Get all sub job
     *
     * @return array
     */
    public function getAll()
    {
        return $this->sub_job->all();
    }

    /**
     * Find sub job with given id or throw an error.
     *
     * @param integer $id
     * @return SubJob
     */
    public function findOrFail($id)
    {
        $sub_job = $this->sub_job->find($id);

        if (! $sub_job) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_sub_job')]);
        }

        return $sub_job;
    }

    /**
     * Find sub job with given uuid or throw an error.
     *
     * @param Job $job
     * @param string $uuid
     * @return SubJob
     */
    public function findByUuidOrFail($job_id, $uuid)
    {
        $sub_job = $this->sub_job->with('userAdded', 'userAdded.profile', 'job')->filterByJobId($job_id)->filterByUuid($uuid)->first();

        if (! $sub_job) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_sub_job')]);
        }

        return $sub_job;
    }

    /**
     * Paginate all sub job using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($job_id, $params)
    {
        $sort_by            = isset($params['sort_by']) ? $params['sort_by'] : 'title';
        $order              = isset($params['order']) ? $params['order'] : 'asc';
        $page_length        = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->sub_job->with('userAdded', 'userAdded.profile', 'job')->filterByJobId($job_id)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new job.
     *
     * @param array $params
     * @return Job
     */
    public function create($job_id, $params)
    {
        $this->validateTitle($job_id, $params);

        $sub_job = $this->sub_job->forceCreate($this->formatParams($params, $job_id));

        $this->processUpload($sub_job, $params);

        return $sub_job;
    }

    /**
     * Validate unique title with job.
     *
     * @param array $params
     * @param integer $id [default null]
     * @return null
     */

    public function validateTitle($job_id, $params, $id = null)
    {
        $query = $this->sub_job->filterByJobId($job_id);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        if ($query->filterByExactTitle($params['title'])->count()) {
            throw ValidationException::withMessages(['title' => trans('validation.unique', ['attribute' => trans('job.sub_job_title')])]);
        }
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $job_id = null, $action = 'create')
    {
        $formatted = [
            'title'       => isset($params['title']) ? $params['title'] : null,
            'description' => isset($params['description']) ? $params['description'] : null
        ];

        if ($action === 'create') {
            $formatted['job_id']      = $job_id;
            $formatted['user_id']      = \Auth::user()->id;
            $formatted['uuid']         = Str::uuid();
            $formatted['upload_token'] = isset($params['upload_token']) ? $params['upload_token'] : null;
        }

        return $formatted;
    }

    /**
     * Fix sub job attachments
     *
     * @param SubJob $sub_job
     * @param array $params
     * @param string $action
     * @return void
     */
    public function processUpload(SubJob $sub_job, $params = array(), $action = 'create')
    {
        $upload_token = isset($params['upload_token']) ? $params['upload_token'] : null;

        if ($action === 'create') {
            $this->upload->store($this->module, $sub_job->id, $upload_token);
        } else {
            $this->upload->update($this->module, $sub_job->id, $upload_token);
        }
    }

    /**
     * Update given sub job.
     *
     * @param SubJob $sub_job
     * @param array $params
     *
     * @return SubJob
     */
    public function update(SubJob $sub_job, $params)
    {
        $this->validateTitle($sub_job->job_id, $params, $sub_job->id);

        $sub_job->forceFill($this->formatParams($params, null, 'update'))->save();

        $this->processUpload($sub_job, $params, 'update');

        return $sub_job;
    }

    /**
     * Check if authenticated user can edit sub job or not.
     *
     * @param SubJob $sub_job
     * @return void
     */
    public function editable(SubJob $sub_job)
    {
        $job = $this->job->findOrFail($sub_job->job_id);

        // Admin Users, Users with permission to access all the job & User who created the job & User who created the sub job can edit the sub job
        if (\Auth::user()->hasRole(config('system.default_role.admin')) || \Auth::user()->can('access-all-job') || $job->user_id == \Auth::user()->id || $sub_job->user_id == \Auth::user()->id) {
            return;
        }

        throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
    }

    /**
     * Delete sub job.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(SubJob $sub_job)
    {
        return $sub_job->delete();
    }

    /**
     * Delete multiple sub job.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->sub_job->whereIn('id', $ids)->delete();
    }
}
