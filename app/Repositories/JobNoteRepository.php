<?php
namespace App\Repositories;

use App\JobNote;
use Illuminate\Support\Str;
use App\Repositories\JobRepository;
use App\Repositories\UploadRepository;
use Illuminate\Validation\ValidationException;

class JobNoteRepository
{
    protected $job_note;
    protected $job;
    protected $upload;
    protected $module = 'job_note';

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        JobNote $job_note,
        JobRepository $job,
        UploadRepository $upload
    ) {
        $this->job_note = $job_note;
        $this->job = $job;
        $this->upload = $upload;
    }

    /**
     * Get job note query
     *
     * @return JobNote query
     */
    public function getQuery()
    {
        return $this->job_note;
    }

    /**
     * Count job note
     *
     * @return integer
     */
    public function count()
    {
        return $this->job_note->count();
    }

    /**
     * List all job note by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->job_note->all()->pluck('title', 'id')->all();
    }

    /**
     * List all job note by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->job_note->all(['title', 'id']);
    }

    /**
     * Get all job note
     *
     * @return array
     */
    public function getAll()
    {
        return $this->job_note->all();
    }

    /**
     * Find job note with given id or throw an error.
     *
     * @param integer $id
     * @return JobNote
     */
    public function findOrFail($id)
    {
        $job_note = $this->job_note->find($id);

        if (! $job_note) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job_note')]);
        }

        return $job_note;
    }

    /**
     * Find job note with given uuid or throw an error.
     *
     * @param Job $job
     * @param string $uuid
     * @return JobNote
     */
    public function editable($job_id, $uuid)
    {
        $job_note = $this->job_note->with('user','user.profile','job')->filterByJobId($job_id)->filterByUuid($uuid)->whereUserId(\Auth::user()->id)->first();

        if (! $job_note) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job_note')]);
        }

        return $job_note;
    }

    /**
     * Find job note with given uuid or throw an error.
     *
     * @param Job $job
     * @param string $uuid
     * @return JobNote
     */
    public function findByUuidOrFail($job_id, $uuid)
    {
        $job_note = $this->job_note->with('user','user.profile','job')->filterByJobId($job_id)->filterByUuid($uuid)->where(function($q){
            $q->where(function($q1){
                $q1->whereIsPublic(0)->whereUserId(\Auth::user()->id);
            })->orWhere(function($q2){
                $q2->whereIsPublic(1);
            });
        })->first();

        if (! $job_note) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job_note')]);
        }

        return $job_note;
    }

    /**
     * Paginate all job note using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($job_id, $params)
    {
        $sort_by            = isset($params['sort_by']) ? $params['sort_by'] : 'title';
        $order              = isset($params['order']) ? $params['order'] : 'asc';
        $page_length        = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->job_note->with('user','user.profile','job')->filterByJobId($job_id)->where(function($q){
            $q->where(function($q1){
                $q1->whereIsPublic(0)->whereUserId(\Auth::user()->id);
            })->orWhere(function($q2){
                $q2->whereIsPublic(1);
            });
        })->orderBy($sort_by, $order)->paginate($page_length);
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

        $job_note = $this->job_note->forceCreate($this->formatParams($params, $job_id));

        $this->processUpload($job_note, $params);

        return $job_note;
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
        $query = $this->job_note->filterByJobId($job_id)->filterByUserId(\Auth::user()->id);

        if ($id) {
            $query->where('id','!=',$id);
        }

        if ($query->filterByExactTitle($params['title'])->count()) {
            throw ValidationException::withMessages(['title' => trans('validation.unique',['attribute' => trans('job.job_note_title')])]);
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
            'description' => isset($params['description']) ? $params['description'] : null,
            'is_public' => (isset($params['is_public']) && $params['is_public']) ? 1 : 0
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
     * Fix job note notes
     *
     * @param JobNote $job_note
     * @param array $params
     * @param string $action
     * @return void
     */
    public function processUpload(JobNote $job_note, $params = array(), $action = 'create')
    {
        $upload_token = isset($params['upload_token']) ? $params['upload_token'] : null;

        if ($action === 'create') {
            $this->upload->store($this->module, $job_note->id, $upload_token);
        } else {
            $this->upload->update($this->module, $job_note->id, $upload_token);
        }
    }

    /**
     * Update given job note.
     *
     * @param JobNote $job_note
     * @param array $params
     *
     * @return JobNote
     */
    public function update(JobNote $job_note, $params)
    {
        $this->validateTitle($job_note->job_id, $params, $job_note->id);

        $job_note->forceFill($this->formatParams($params, null, 'update'))->save();

        $this->processUpload($job_note, $params, 'update');

        return $job_note;
    }

    /**
     * Delete job note.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(JobNote $job_note)
    {
        return $job_note->delete();
    }

    /**
     * Delete multiple job note.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->job_note->whereIn('id', $ids)->delete();
    }
}