<?php
namespace App\Repositories;

use App\Job;
use Carbon\Carbon;
use App\Jobs\SendMail;
use App\JobSignOffLog;
use App\Jobs\SendMailToJobUser;
use App\Repositories\JobRepository;
use Illuminate\Validation\ValidationException;

class JobSignOffLogRepository
{
    protected $job_sign_off_log;
    protected $job;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        JobSignOffLog $job_sign_off_log,
        JobRepository $job
    ) {
        $this->job_sign_off_log = $job_sign_off_log;
        $this->job = $job;
    }

    /**
     * Get job sign off log query
     *
     * @return JobSignOffLog query
     */
    public function getQuery()
    {
        return $this->job_sign_off_log;
    }

    /**
     * Count job sign off log
     *
     * @return integer
     */
    public function count()
    {
        return $this->job_sign_off_log->count();
    }

    /**
     * List all job sign off log by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->job_sign_off_log->all()->pluck('title', 'id')->all();
    }

    /**
     * List all job sign off log by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->job_sign_off_log->all(['title', 'id']);
    }

    /**
     * Get all job sign off log
     *
     * @return array
     */
    public function getAll()
    {
        return $this->job_sign_off_log->all();
    }

    /**
     * Find job sign off log with given id or throw an error.
     *
     * @param integer $id
     * @return JobSignOff
     */
    public function findOrFail($id)
    {
        $job_sign_off_log = $this->job_sign_off_log->find($id);

        if (! $job_sign_off_log) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job_sign_off_log')]);
        }

        return $job_sign_off_log;
    }

    /**
     * Paginate all job sign off log using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($job_id, $params)
    {
        $sort_by            = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order              = isset($params['order']) ? $params['order'] : 'desc';
        $page_length        = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->job_sign_off_log->with('userAdded', 'userAdded.profile', 'job')->filterbyJobId($job_id)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Validate input data for sign off request.
     *
     * @param Job $job
     * @param string $status
     * @return void
     */
    public function validateRequest(Job $job, $status = null)
    {
        $assigned_users = $job->user()->pluck('user_id')->all();
        array_push($assigned_users, $job->user_id);

        if ($this->job->getJobProgress($job) < 100) {
            throw ValidationException::withMessages(['message' => trans('job.job_not_completed')]);
        }

        if (!in_array(\Auth::user()->id, $assigned_users)) {
            throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
        }

        if ($job->sign_off_status === 'approved') {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        if ($status === 'requested' && $job->sign_off_status != null && $job->sign_off_status != 'rejected' && $job->sign_off_status != 'cancelled') {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        if ($status === 'cancelled' && $job->sign_off_status != 'requested') {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }
    }

    /**
     * Validate input data for sign off action.
     *
     * @param Job $job
     * @param string $status
     * @return void
     */
    public function validateAction(Job $job, $status = null)
    {
        if ($this->job->getJobProgress($job) < 100) {
            throw ValidationException::withMessages(['message' => trans('job.job_not_completed')]);
        }

        if ($status === 'approved' && ($job->sign_off_status === 'approved' || $job->sign_off_status === 'cancelled')) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        if ($status === 'rejected' && ($job->sign_off_status === 'rejected' || $job->sign_off_status === 'cancelled')) {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }
    }

    /**
     * Perform sign off request operation.
     *
     * @param Job $job
     * @param array $params
     * @return JobSignOffLog
     */
    public function request(Job $job, $params)
    {
        $status = isset($params['status']) ? $params['status'] : null;

        $this->validateRequest($job);

        $job_sign_off_log = $this->job_sign_off_log->forceCreate($this->formatParams($params, $job->id));

        $job = $this->updateJob($job_sign_off_log, $job);

        SendMail::dispatch($job->UserAdded->email, [
            'slug'      => ($status == 'requested') ? 'job-sign-off-request' : 'job-sign-off-request-cancel',
            'user'      => $job->UserAdded,
            'job'      => $job,
            'module'    => 'job',
            'module_id' => $job->id
        ]);

        return $job_sign_off_log;
    }

    /**
     * Perform sign off action operation.
     *
     * @param Job $job
     * @param array $params
     * @return JobSignOffLog
     */
    public function action(Job $job, $params)
    {
        $status = isset($params['status']) ? $params['status'] : null;

        $this->validateAction($job, $status);

        $job_sign_off_log = $this->job_sign_off_log->forceCreate($this->formatParams($params, $job->id));

        $job = $this->updateJob($job_sign_off_log, $job);

        SendMailToJobUser::dispatch($job, (($status === 'approved') ? 'job-sign-off-approve' : 'job-sign-off-reject'));

        return $job_sign_off_log;
    }

    /**
     * Update job status after sign off request/action.
     *
     * @param Job $job
     * @param JobSignOffLog $job_sign_off_log
     * @return Job
     */
    private function updateJob(JobSignOffLog $job_sign_off_log, Job $job)
    {
        $job->completed_at = ($job_sign_off_log->status === 'approved') ? Carbon::now() : null;
        $job->sign_off_status = $job_sign_off_log->status;
        $job->save();

        return $job;
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
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $job_id = null, $action = 'create')
    {
        $formatted = [
            'remarks' => isset($params['sign_off_remarks']) ? $params['sign_off_remarks'] : null,
            'status'  => isset($params['status']) ? $params['status'] : null,
            'job_id' => $job_id,
            'user_id' => \Auth::user()->id,
        ];

        return $formatted;
    }
}
