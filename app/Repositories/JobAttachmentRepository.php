<?php
namespace App\Repositories;

use App\JobAttachment;
use Illuminate\Support\Str;
use App\Repositories\JobRepository;
use App\Repositories\UploadRepository;
use Illuminate\Validation\ValidationException;

class JobAttachmentRepository
{
    protected $job_attachment;
    protected $job;
    protected $upload;
    protected $module = 'job_attachment';

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        JobAttachment $job_attachment,
        JobRepository $job,
        UploadRepository $upload
    ) {
        $this->job_attachment = $job_attachment;
        $this->job = $job;
        $this->upload = $upload;
    }

    /**
     * Get job attachment query
     *
     * @return JobAttachment query
     */
    public function getQuery()
    {
        return $this->job_attachment;
    }

    /**
     * Count job attachment
     *
     * @return integer
     */
    public function count()
    {
        return $this->job_attachment->count();
    }

    /**
     * List all job attachment by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->job_attachment->all()->pluck('title', 'id')->all();
    }

    /**
     * List all job attachment by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->job_attachment->all(['title', 'id']);
    }

    /**
     * Get all job attachment
     *
     * @return array
     */
    public function getAll()
    {
        return $this->job_attachment->all();
    }

    /**
     * Find job attachment with given id or throw an error.
     *
     * @param integer $id
     * @return JobAttachment
     */
    public function findOrFail($id)
    {
        $job_attachment = $this->job_attachment->with('user', 'user.profile', 'job')->find($id);

        if (! $job_attachment) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job_attachment')]);
        }

        return $job_attachment;
    }

    /**
     * Find job attachment with given uuid or throw an error.
     *
     * @param Job $job
     * @param string $uuid
     * @return JobAttachment
     */
    public function findByUuidOrFail($job_id, $uuid)
    {
        $job_attachment = $this->job_attachment->with('user', 'user.profile', 'job')->filterByJobId($job_id)->filterByUuid($uuid)->first();

        if (! $job_attachment) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job_attachment')]);
        }

        return $job_attachment;
    }

    /**
     * Paginate all job attachment using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($job_id, $params)
    {
        $sort_by            = isset($params['sort_by']) ? $params['sort_by'] : 'title';
        $order              = isset($params['order']) ? $params['order'] : 'asc';
        $page_length        = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->job_attachment->with('user', 'user.profile', 'job')->filterByJobId($job_id)->orderBy($sort_by, $order)->paginate($page_length);
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

        $job_attachment = $this->job_attachment->forceCreate($this->formatParams($params, $job_id));

        $this->processUpload($job_attachment, $params);

        return $job_attachment;
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
        $query = $this->job_attachment->filterByJobId($job_id)->filterByUserId(\Auth::user()->id);

        if ($id) {
            $query->where('id', '!=', $id);
        }

        if ($query->filterByExactTitle($params['title'])->count()) {
            throw ValidationException::withMessages(['title' => trans('validation.unique', ['attribute' => trans('job.job_attachment_title')])]);
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
     * Fix job attachment attachments
     *
     * @param JobAttachment $job_attachment
     * @param array $params
     * @param string $action
     * @return void
     */
    public function processUpload(JobAttachment $job_attachment, $params = array(), $action = 'create')
    {
        $upload_token = isset($params['upload_token']) ? $params['upload_token'] : null;

        if ($action === 'create') {
            $this->upload->store($this->module, $job_attachment->id, $upload_token);
        } else {
            $this->upload->update($this->module, $job_attachment->id, $upload_token);
        }
    }

    /**
     * Update given job attachment.
     *
     * @param JobAttachment $job_attachment
     * @param array $params
     *
     * @return JobAttachment
     */
    public function update(JobAttachment $job_attachment, $params)
    {
        $this->validateTitle($job_attachment->job_id, $params, $job_attachment->id);

        $job_attachment->forceFill($this->formatParams($params, null, 'update'))->save();

        $this->processUpload($job_attachment, $params, 'update');

        return $job_attachment;
    }

    /**
     * Delete job attachment.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(JobAttachment $job_attachment)
    {
        return $job_attachment->delete();
    }

    /**
     * Delete multiple job attachment.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->job_attachment->whereIn('id', $ids)->delete();
    }
}
