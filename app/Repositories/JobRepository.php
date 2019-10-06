<?php
namespace App\Repositories;

use App\Job;
use App\Answer;
use App\SubJob;
use App\SubJobRating;
use App\User;

use Illuminate\Support\Str;
use App\Jobs\SendMailToJobUser;
use App\Repositories\UserRepository;
use App\Repositories\UploadRepository;
use App\Repositories\QuestionSetRepository;
use App\Repositories\JobCategoryRepository;
use App\Repositories\JobPriorityRepository;
use Illuminate\Validation\ValidationException;
use App\Repositories\ClientRepository;
use App\Repositories\ContractorRepository;
use App\Repositories\ProjectRepository;

// use App\Notifications\JobAssignation;
use App\Events\JobAssigned;

class JobRepository
{
    protected $job;
    protected $user;
    protected $upload;
    protected $sub_job_rating;
    protected $sub_job;
    protected $question_set;
    protected $answer;
    protected $job_category;
    protected $job_priority;
    protected $client;
    protected $contractor;
    protected $project;
    protected $module = 'job';

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Job $job,
        UserRepository $user,
        UploadRepository $upload,
        SubJobRating $sub_job_rating,
        SubJob $sub_job,
        QuestionSetRepository $question_set,
        Answer $answer,
        JobCategoryRepository $job_category,
        JobPriorityRepository $job_priority,
        ClientRepository $client,
        ContractorRepository $contractor,
        ProjectRepository $project
    ) {
        $this->job = $job;
        $this->user = $user;
        $this->upload = $upload;
        $this->sub_job_rating = $sub_job_rating;
        $this->sub_job = $sub_job;
        $this->question_set = $question_set;
        $this->answer = $answer;
        $this->job_category = $job_category;
        $this->job_priority = $job_priority;
        $this->client = $client;
        $this->contractor = $contractor;
        $this->project = $project;
    }

    /**
     * Get job query
     *
     * @return Job query
     */
    public function getQuery()
    {
        return $this->job;
    }

    /**
     * Count job
     *
     * @return integer
     */
    public function count()
    {
        return $this->job->count();
    }

    /**
     * List all job by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->job->all()->pluck('title', 'id')->all();
    }

    /**
     * List all job by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->job->all(['title', 'id']);
    }

    /**
     * Get all jobs
     *
     * @return array
     */
    public function getAll()
    {
        return $this->job->all();
    }

    /**
     * Get recurring jobs by date
     *
     * @return array
     */
    public function getRecurringJobByDate($date = null)
    {
        $date = ($date) ? : date('Y-m-d');
        return $this->job->filterByIsRecurring(1)->filterByNextRecurringDate($date)->get();
    }

    /**
     * Find job with given id or throw an error.
     *
     * @param integer $id
     * @return Job
     */
    public function findOrFail($id)
    {
        $job = $this->job->with('userAdded', 'userAdded.profile', 'user', 'user.profile', 'user.profile.designation', 'user.profile.designation.department', 'jobPriority', 'jobCategory', 'subJob', 'subJob.subJobRating','answers', 'client', 'contractor', 'project')->find($id);

        if (! $job) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job')]);
        }

        return $job;
    }

    /**
     * Find job with given uuid or throw an error.
     *
     * @param string $uuid
     * @return Job
     */
    public function findByUuidOrFail($uuid)
    {
        $job = $this->job->with('userAdded', 'userAdded.profile', 'user', 'user.profile', 'user.profile.designation', 'user.profile.designation.department', 'jobPriority', 'jobCategory', 'subJob', 'subJob.subJobRating', 'answers','questionSet','questionSet.questions', 'client', 'contractor', 'project')->whereUuid($uuid)->first();

        if (! $job) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job')]);
        }

        return $job;
    }

    /**
     * Fetch accessible job for authenticated user.
     *
     * @return Job query
     */
    public function fetchJobs()
    {
        $query = $this->job->with('userAdded', 'userAdded.profile', 'user', 'user.profile', 'user.profile.designation', 'user.profile.designation.department', 'jobPriority', 'jobCategory', 'subJob', 'subJob.subJobRating', 'answers', 'client', 'contractor', 'project');

        // Accessible if logged in user has permission to access all the job
        // Accessible if logged in user has role of admin
        // Accessible if logged in user has permission to access subordinates and his subordinates users are assigned with the job or owner of job
        // Accessible if logged in user is assigned with the job or owner of the job

        if (\Auth::user()->can('access-all-job') || \Auth::user()->hasRole(config('system.default_role.admin'))) {
        } elseif (\Auth::user()->can('access-subordinate-job')) {
            $subordinate_users = $this->user->getAccessibleUserId(\Auth::user()->id, 1);
            $query->where(function ($q) use ($subordinate_users) {
                $q->whereHas('user', function ($q1) use ($subordinate_users) {
                    $q1->whereIn('user_id', $subordinate_users);
                })->orWhereIn('user_id', $subordinate_users);
            });
        } else {
            $query->where(function ($q) {
                $q->whereHas('user', function ($q1) {
                    $q1->where('user_id', \Auth::user()->id);
                })->orWhere('user_id', '=', \Auth::user()->id);
            });
        }

        return  $query;
    }

    /**
     * Paginate all jobs using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by            = isset($params['sort_by']) ? $params['sort_by'] : 'title';
        $order              = isset($params['order']) ? $params['order'] : 'asc';
        $page_length        = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $number             = isset($params['number']) ? $params['number'] : null;
        $title              = isset($params['title']) ? $params['title'] : null;
        $job_category_id   = isset($params['job_category_id']) ? $params['job_category_id'] : [];
        $job_priority_id   = isset($params['job_priority_id']) ? $params['job_priority_id'] : [];
        $client_id   = isset($params['client_id']) ? $params['client_id'] : [];
        $contractor_id   = isset($params['contractor_id']) ? $params['contractor_id'] : [];
        $project_id   = isset($params['project_id']) ? $params['project_id'] : [];
        $user_id            = isset($params['user_id']) ? $params['user_id'] : [];
        $type               = isset($params['type']) ? $params['type'] : null;
        $starred            = isset($params['starred']) ? $params['starred'] : null;
        $is_archived        = isset($params['is_archived']) ? $params['is_archived'] : 'unarchived';
        $start_date_start   = isset($params['start_date_start']) ? $params['start_date_start'] : null;
        $start_date_end     = isset($params['start_date_end']) ? $params['start_date_end'] : null;
        $due_date_start     = isset($params['due_date_start']) ? $params['due_date_start'] : null;
        $due_date_end       = isset($params['due_date_end']) ? $params['due_date_end'] : null;
        $completed_at_start = isset($params['completed_at_start']) ? $params['completed_at_start'] : null;
        $completed_at_end   = isset($params['completed_at_end']) ? $params['completed_at_end'] : null;
        $is_recurring       = isset($params['is_recurring']) ? $params['is_recurring'] : null;
        $status             = isset($params['status']) ? $params['status'] : null;
        $recurring_job_id  = isset($params['recurring_job_id']) ? $params['recurring_job_id'] : null;

        $query = $this->fetchJobs()->filterByStarred($starred)->filterByNumber($number)->filterByTitle($title)->filterByIsArchived($is_archived)->filterByJobCategoryId($job_category_id)->filterByClientId($client_id)->filterByContractorId($contractor_id)->filterByProjectId($project_id)->filterByJobPriorityId($job_priority_id)->filterByIsRecurring($is_recurring)->filterByUserId($user_id)->filterByType($type)->filterByStatus($status)->filterByRecurringJobId($recurring_job_id)->startDateBetween([
            'start_date' => $start_date_start,
            'end_date' => $start_date_end
        ])->dueDateBetween([
            'start_date' => $due_date_start,
            'end_date' => $due_date_end
        ])->completedAtDateBetween([
            'start_date' => $completed_at_start,
            'end_date' => $completed_at_end
        ]);

        if ($sort_by == 'job_category_id') {
            $query->select('jobs.*', \DB::raw('(select name from job_categories where jobs.job_category_id = job_categories.id) as job_category_name'))->orderBy('job_category_name', $order);
        } elseif ($sort_by == 'job_priority_id') {
            $query->select('jobs.*', \DB::raw('(select name from job_priorities where jobs.job_priority_id = job_priorities.id) as job_priority_name'))->orderBy('job_priority_name', $order);
        } else {
            $query->orderBy($sort_by, $order);
        }

        return $query->paginate($page_length);
    }

    /**
     * Copy given job to new job.
     *
     * @param Job $job
     * @param array $params
     * @return Job $new_job
     */
    public function copy(Job $job, $params = array())
    {
        $new_job = $this->copyJob($job, $params);

        $this->copyAssignedUser($job, $new_job, $params);

        $this->copySubJobs($job, $new_job, $params);

        $this->copyAttachments($job, $new_job, $params);

        $this->copyNotes($job, $new_job, $params);

        return $new_job;
    }

    /**
     * Copy job assigned user into new job.
     *
     * @param Job $job
     * @param Job $new_job
     * @param array $params
     * @return void
     */
    private function copyAssignedUser(Job $job, Job $new_job, $params = array())
    {
        $set_assigned_user = (isset($params['assigned_user']) && $params['assigned_user']) ? 1 : 0;

        $new_job->user()->sync($set_assigned_user ? $job->user()->pluck('user_id')->all() : []);
    }

    /**
     * Copy job data into new job.
     *
     * @param Job $job
     * @param array $params
     * @return Job
     */
    private function copyJob(Job $job, $params = array())
    {
        $set_job_configuration = (isset($params['job_configuration']) && $params['job_configuration']) ? 1 : 0;
        $set_zero_progress      = (isset($params['zero_progress']) && $params['zero_progress']) ? 1 : 0;

        $new_job                        = $job->replicate();
        $new_job->uuid                  = Str::uuid();
        $new_job->upload_token          = Str::uuid();
        $new_job->progress_type         = $set_job_configuration ? $job->progress_type : config('config.job_progress_type');
        $new_job->rating_type           = $set_job_configuration ? $job->rating_type : config('config.job_rating_type');
        $new_job->completed_at          = null;
        $new_job->sign_off_status       = null;
        $new_job->is_archived           = 0;
        $new_job->is_cancelled          = 0;
        $new_job->progress              = $set_zero_progress ? 0 : $job->progress;
        $new_job->user_id               = $job->user_id;
        $new_job->is_recurring          = 0;
        $new_job->recurrence_start_date = null;
        $new_job->recurrence_end_date   = null;
        $new_job->next_recurring_date   = null;
        $new_job->recurring_frequency   = 0;
        $new_job->recurring_job_id     = null;
        $new_job->save();

        $this->upload->copy('job', $job->id, $new_job->upload_token, $new_job->id);

        return $new_job;
    }

    /**
     * Copy sub job into new sub job.
     *
     * @param Job $job
     * @param Job $new_job
     * @param array $params
     * @return void
     */
    private function copySubJobs(Job $job, Job $new_job, $params = array())
    {
        $set_sub_job = (isset($params['sub_job']) && $params['sub_job']) ? 1 : 0;

        if (! $set_sub_job) {
            return;
        }

        foreach ($job->SubJob as $sub_job) {
            $new_sub_job = $sub_job->replicate();
            $new_sub_job->uuid = Str::uuid();
            $new_sub_job->job_id = $new_job->id;
            $new_sub_job->status = 0;
            $new_sub_job->completed_at = null;
            $new_sub_job->user_id = $sub_job->user_id;
            $new_sub_job->upload_token = Str::uuid();
            $new_sub_job->save();

            $this->upload->copy('sub_job', $sub_job->id, $new_sub_job->upload_token, $new_sub_job->id);
        }
    }

    /**
     * Copy job attachments into new job.
     *
     * @param Job $job
     * @param Job $new_job
     * @param array $params
     * @return void
     */
    private function copyAttachments(Job $job, Job $new_job, $params = array())
    {
        $set_attachments = (isset($params['attachments']) && $params['attachments']) ? 1 : 0;

        if (! $set_attachments) {
            return;
        }

        foreach ($job->JobAttachment as $job_attachment) {
            $new_job_attachment = $job_attachment->replicate();
            $new_job_attachment->uuid = Str::uuid();
            $new_job_attachment->job_id = $new_job->id;
            $new_job_attachment->user_id = $job_attachment->user_id;
            $new_job_attachment->upload_token = Str::uuid();
            $new_job_attachment->save();

            $this->upload->copy('job_attachment', $job_attachment->id, $new_job_attachment->upload_token, $new_job_attachment->id);
        }
    }

    /**
     * Copy job notes into new job.
     *
     * @param Job $job
     * @param Job $new_job
     * @param array $params
     * @return void
     */
    private function copyNotes(Job $job, Job $new_job, $params = array())
    {
        $set_notes = (isset($params['notes']) && $params['notes']) ? 1 : 0;

        if (! $set_notes) {
            return;
        }

        foreach ($job->JobNote as $job_note) {
            $new_job_note = $job_note->replicate();
            $new_job_note->uuid = Str::uuid();
            $new_job_note->job_id = $new_job->id;
            $new_job_note->user_id = $job_note->user_id;
            $new_job_note->upload_token = Str::uuid();
            $new_job_note->save();

            $this->upload->copy('job_note', $job_note->id, $new_job_note->upload_token, $new_job_note->id);
        }
    }

    /**
     * Create a new job.
     *
     * @param array $params
     * @return Job
     */
    public function create($params)
    {
        $this->validateInputId($params);

        $job = $this->job->forceCreate($this->formatParams($params));

        $job = $this->assignUser($job, $params);

        $this->notify($job, $params);

        $this->processUpload($job, $params);

        return $job;
    }

    /**
     * Validate input ids.
     *
     * @param array $params
     * @return null
     */

    public function validateInputId($params)
    {
        $job_category_ids = $this->job_category->listId();
        $job_priority_ids = $this->job_priority->listId();
        $question_set_ids  = $this->question_set->listId();
        $client_ids = $this->client->listId();
        $contractor_ids  = $this->contractor->listId();
        $project_ids  = $this->project->listId();

        $job_category_id = isset($params['job_category_id']) ? $params['job_category_id'] : null;
        $job_priority_id = isset($params['job_priority_id']) ? $params['job_priority_id'] : null;
        $question_set_id  = isset($params['question_set_id']) ? $params['question_set_id'] : [];
        $client_id = isset($params['client_id']) ? $params['client_id'] : null;
        $contractor_id = isset($params['contractor_id']) ? $params['contractor_id'] : null;
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;

        if (! in_array($job_category_id, $job_category_ids)) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job_category')]);
        }

        if (! in_array($job_priority_id, $job_priority_ids)) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_job_priority')]);
        }

        if ($question_set_id && ! in_array($question_set_id, $question_set_ids)) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_question_set')]);
        }

        if ($client_id && ! in_array($client_id, $client_ids)) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_client')]);
        }

        if ($contractor_id && ! in_array($contractor_id, $contractor_ids)) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_contractor')]);
        }

        if ($project_id && ! in_array($project_id, $project_ids)) {
            throw ValidationException::withMessages(['message' => trans('job.could_not_find_project')]);
        }
    }

    /**
     * Notify assigned users after creating job.
     *
     * @param Job $job
     * @param array $params
     * @return void
     */
    private function notify(Job $job, $params = array())
    {
        $send_job_assign_email = isset($params['send_job_assign_email']) ? $params['send_job_assign_email'] : null;

        if (! $send_job_assign_email || ! $job->User->count()) {
            return;
        }

        SendMailToJobUser::dispatch($job, 'job-assign-email');
    }

    /**
     * Fix job attachments
     *
     * @param Job $job
     * @param array $params
     * @param string $action
     * @return void
     */
    public function processUpload(Job $job, $params = array(), $action = 'create')
    {
        $upload_token = isset($params['upload_token']) ? $params['upload_token'] : null;

        if ($action === 'create') {
            $this->upload->store($this->module, $job->id, $upload_token);
        } else {
            $this->upload->update($this->module, $job->id, $upload_token);
        }
    }

    /**
     * Assign users into given job.
     *
     * @param Job $job
     * @param array $params
     * @return Job
     */
    private function assignUser(Job $job, $params)
    {
        $users = isset($params['user_id']) ? $params['user_id'] : [];


        // TODO SOLO ASIGNACIONES NUEVAS EN EL UPDATE


        // $user = \Auth::user();        
        // $user->notify(new JobAssigned($job));


        $users = User::find($users);   //\Auth::user();        
        foreach ($users as $user) {

            event(new JobAssigned($user,$job));
            // $user->notify(new JobAssignation($job));    
        }
        




        $job->user()->sync($users);

        return $job;
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
            'job_category_id' => isset($params['job_category_id']) ? $params['job_category_id'] : null,
            'job_priority_id' => isset($params['job_priority_id']) ? $params['job_priority_id'] : null,
            'question_set_id'  => (isset($params['question_set_id']) && $params['question_set_id']) ? $params['question_set_id'] : null,
            'title'            => isset($params['title']) ? $params['title'] : null,
            'start_date'       => isset($params['start_date']) ? toDate($params['start_date']) : null,
            'due_date'         => isset($params['due_date']) ? toDate($params['due_date']) : null,
            'description'      => isset($params['description']) ? $params['description'] : null,
            'client_id' => isset($params['client_id']) ? $params['client_id'] : null,
            'contractor_id' => isset($params['contractor_id']) ? $params['contractor_id'] : null,
            'project_id' => isset($params['project_id']) && $params['project_id'] != '' ? $params['project_id'] : null
        ];


        // dd($formatted);

        if ($action === 'create') {
            $formatted['user_id']       = \Auth::user()->id;
            $formatted['uuid']          = Str::uuid();
            $formatted['upload_token']  = isset($params['upload_token']) ? $params['upload_token'] : null;
            $formatted['progress_type'] = config('config.job_progress_type');
            $formatted['rating_type']   = config('config.job_rating_type');
        }

        return $formatted;
    }

    /**
     * Update given job.
     *
     * @param Job $job
     * @param array $params
     *
     * @return Job
     */
    public function update(Job $job, $params)
    {
        $this->validateInputId($params);
        
        $question_set_id = isset($params['question_set_id']) ? $params['question_set_id'] : null;
        if ($job->question_set_id != $question_set_id) {
            $job->answers()->delete();
        }
        
        $job->forceFill($this->formatParams($params, 'update'))->save();

        $job = $this->assignUser($job, $params);

        $this->processUpload($job, $params, 'update');

        return $job;
    }

    /**
     * Update given job progress.
     *
     * @param Job $job
     * @param array $params
     * @return Job
     */
    public function updateProgress(Job $job, $params)
    {
        $progress = isset($params['progress']) ? $params['progress'] : 0;

        if ($job->progress_type != 'manual') {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $job->progress = request('progress');
        $job->save();

        return  $job;
    }

    /**
     * Update given job recurrence.
     *
     * @param Job $job
     * @param array $params
     * @return void
     */
    public function updateRecurrence(Job $job, $params)
    {
        $recurrence_start_date = isset($params['recurrence_start_date']) ? $params['recurrence_start_date'] : null;
        $recurrence_end_date   = isset($params['recurrence_end_date']) ? $params['recurrence_end_date'] : null;
        $is_recurring          = (isset($params['is_recurring']) && $params['is_recurring']) ? 1 : 0;
        $recurring_frequency   = isset($params['recurring_frequency']) ? $params['recurring_frequency'] : null;

        if ($recurrence_start_date < $job->start_date || $recurrence_end_date < $job->start_date) {
            throw ValidationException::withMessages(['message' => trans('job.recurrence_date_cannot_less_than_job_date')]);
        }

        $job->is_recurring          = $is_recurring;
        $job->recurrence_start_date = $is_recurring ? $recurrence_start_date : null;
        $job->recurrence_end_date   = $is_recurring ? $recurrence_end_date : null;
        $job->recurring_frequency   = $is_recurring ? $recurring_frequency : 0;
        $job->next_recurring_date   = $is_recurring ? date('Y-m-d', strtotime($recurrence_start_date. ' + '.$recurring_frequency.' days')) : null;
        $job->save();

        return $job;
    }

    /**
     * Get all recurring frequencies.
     *
     * @return array
     */
    public function listRecurringFrequency()
    {
        return [
            ['id' => '7', 'name' => trans('job.weekly')],
            ['id' => '15', 'name' => trans('job.fortnightly')],
            ['id' => '30', 'name' => trans('job.monthly')],
            ['id' => '60', 'name' => trans('job.bi_monthly')],
            ['id' => '90', 'name' => trans('job.quarterly')],
            ['id' => '180', 'name' => trans('job.bi_annually')],
            ['id' => '365', 'name' => trans('job.annually')]
        ];
    }

    /**
     * Update job next recurring date
     *
     * @param Job $job
     * @return Job
     */
    public function updateNextRecurringDate($job)
    {
        $next_recurring_date = date('Y-m-d', strtotime($job->next_recurring_date. ' + '.$job->recurring_frequency.' days'));
        $job->next_recurring_date = ($job->recurrence_end_date > $next_recurring_date) ? $next_recurring_date : null;
        $job->save();

        return $job;
    }

    /**
     * Determine authenticated user can access given job or not.
     *
     * @param Job $job
     * @return void
     */
    public function accessible(Job $job)
    {
        $subordinate_users = $this->user->getAccessibleUserId(\Auth::user()->id, 1);

        $job_assigned_users = $job->user()->pluck('user_id')->all();

        /**
         * Admin Users, Users with permission to access all the job & User who created the job can access the job
         * Users with permission access subordinate job can access the job if job is created by his subordinates or job is assigned to its subordinates
         * Job is accessible when user is assigned job
         */
        if (
            \Auth::user()->hasRole(config('system.default_role.admin')) ||
            \Auth::user()->can('access-all-job') ||
            $job->user_id == \Auth::user()->id ||
            in_array(\Auth::user()->id, $job_assigned_users) ||
            (\Auth::user()->can('access-subordinate-job') && (in_array($job->user_id, $subordinate_users) || count(array_intersect($subordinate_users, $job_assigned_users))))
        ) {
            return;
        }

        throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
    }

    /**
     * Determine authenticated user can edit given job or not.
     *
     * @param Job $job
     * @param boolean $should_return
     * @return boolean
     */
    public function editable(Job $job, $should_return = 0)
    {
        $subordinate_users = $this->user->getAccessibleUserId(\Auth::user()->id, 1);

        // Admin Users, Users with permission to access all the job & User who created the job can edit the job
        // Users with permission access subordinate job can edit the job if job is created by his subordinates
        if (
            \Auth::user()->hasRole(config('system.default_role.admin')) ||
            \Auth::user()->can('access-all-job') ||
            $job->user_id == \Auth::user()->id ||
            (\Auth::user()->can('access-subordinate-job') && in_array($job->user_id, $subordinate_users))
        ) {
            return 1;
        }

        if ($should_return) {
            return 0;
        }

        throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
    }

    /**
     * Verify if job status is locked i.e. job status is either requested or approved.
     *
     * @param Job $job
     * @return void
     */
    public function statusLocked(Job $job)
    {
        if (in_array($job->sign_off_status, ['requested','approved'])) {
            throw ValidationException::withMessages(['message' => trans('job.no_job_action_on_sign_off_status', ['status' => trans('job.'.$job->sign_off_status),'action' => trans('job.update')])]);
        }
    }

    /**
     * Verify if job status is not approved.
     *
     * @param Job $job
     * @return void
     */
    public function isNotApproved(Job $job)
    {
        if ($job->sign_off_status != 'approved') {
            throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
        }
    }

    /**
     * Verify if authenticated user is not owner of the job.
     *
     * @param Job $job
     * @return void
     */
    public function isNotOwner(Job $job)
    {
        if ($job->user_id != \Auth::user()->id) {
            throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
        }
    }

    public function configuration(Job $job, $params)
    {
        $progress_type = isset($params['progress_type']) ? $params['progress_type'] : null;
        $rating_type = isset($params['rating_type']) ? $params['rating_type'] : null;

        if ($progress_type === 'question' && ! $job->question_set_id) {
            throw ValidationException::withMessages(['progress_type' => trans('job.question_set_required_for_question_progress_type')]);
        }

        $job->progress_type = request('progress_type');
        $job->rating_type = request('rating_type');
        $job->save();

        return $job;
    }

    /**
     * Rate given job as given inputs if rating type is job based rating.
     *
     * @param Job $job
     * @param array $params
     * @return void
     */
    public function rating(Job $job, $params = array())
    {
        if ($job->rating_type != 'job_based') {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $rows = isset($params['row']) ? $params['row'] : [];

        foreach ($rows as $row) {
            if (!$row['rating']) {
                throw ValidationException::withMessages(['message' => trans('validation.required', ['attribute' => trans('job.rating')])]);
            }
        }

        $this->deleteSubJobRating($job);

        foreach ($rows as $row) {
            $user_id = $row['user_id'];
            $rating  = $row['rating'];
            $remarks = $row['remarks'];

            $job->user()->sync([$user_id => [
                'rating' => $row['rating'],
                'remarks' => $row['remarks']
            ]], false);
        }
    }

    /**
     * Rate given job as given inputs if rating type is sub job based rating.
     *
     * @param Job $job
     * @param array $params
     * @return void
     */
    public function subJobRating(Job $job, $params = array())
    {
        if ($job->rating_type != 'sub_job_based') {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $rows = isset($params['row']) ? $params['row'] : [];
        $user_id = isset($params['user_id']) ? $params['user_id'] : null;

        foreach ($rows as $row) {
            if (!$row['rating']) {
                throw ValidationException::withMessages(['message' => trans('validation.required', ['attribute' => trans('job.rating')])]);
            }
        }

        $this->deleteJobRating($job);

        foreach ($rows as $row) {
            $sub_job_rating = $this->sub_job_rating->firstOrNew(['sub_job_id' => $row['sub_job_id'],'user_id' => $user_id]);
            $sub_job_rating->sub_job_id = $row['sub_job_id'];
            $sub_job_rating->user_id = $user_id;
            $sub_job_rating->rating = $row['rating'];
            $sub_job_rating->remarks = $row['remarks'];
            $sub_job_rating->save();
        }
    }

    /**
     * Used to delete rating if rating type is job based
     * @param ({
     *      @Parameter("job", type="object", required="true", description="Job  object")
     * })
     * @return nothing
     */

    public function deleteJobRating($job)
    {
        foreach ($job->User as $user) {
            $job->user()->sync([$user->id => [
                'rating' => null,
                'remarks' => null
            ]], false);
        }
    }

    /**
     * Used to delete rating if rating type is sub job based
     * @param ({
     *      @Parameter("job", type="object", required="true", description="Job  object")
     * })
     * @return nothing
     */

    public function deleteSubJobRating($job)
    {
        $this->sub_job_rating->whereIn('sub_job_id', $job->SubJob->pluck('id')->all())->delete();
    }

    /**
     * Find job & check it can be deleted or not.
     *
     * @param integer $id
     * @return Job
     */
    public function deletable($id)
    {
        $job = $this->findOrFail($id);

        if ($job->jobs()->count()) {
            throw ValidationException::withMessages(['message' => trans('job.job_has_many_jobs')]);
        }
        
        return $job;
    }

    /**
     * Delete job.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Job $job)
    {
        return $job->delete();
    }

    /**
     * Delete multiple job.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->job->whereIn('id', $ids)->delete();
    }

    /**
     * Get given job progress.
     *
     * @param Job $job
     * @return integer
     */
    public function getJobProgress(Job $job)
    {
        if ($job->progress_type == 'manual') {
            return $job->progress;
        } elseif ($job->progress_type == 'question') {
            return ($job->answers()->count()) ? 100 : 0;
        } else {
            $sub_jobs = $this->sub_job->filterByJobId($job->id)->get();
            return ($sub_jobs->count()) ? (($sub_jobs->where('status', 1)->count()/$sub_jobs->count())*100) : 0;
        }
    }

    /**
     * Get job color based upon the status.
     *
     * @param Job $job
     * @return string
     */
    public function getJobColor(Job $job)
    {
        if ($job->status == 'unassigned') {
            return '#727B84';
        } elseif ($job->status == 'pending') {
            return '#009EFB';
        } elseif ($job->status == 'overdue') {
            return '#F2F7F8';
        } elseif ($job->status == 'late') {
            return '#FFD071';
        } elseif ($job->status == 'completed') {
            return '#88DD92';
        } else {
            return;
        }
    }

    /**
     * Get data for top rating chart.
     *
     * @return array
     */
    public function ratingChart()
    {
        $users = $this->user->getAll();
        $all_sub_job_ratings = $this->sub_job_rating->all();

        foreach ($users as $user) {
            $rating = $this->getUserRating($user, $all_sub_job_ratings);
            $average_rating = ($rating['job_count']) ? $rating['rating']/$rating['job_count'] : 0;
            $chart[] = [
                    'rating' => $average_rating,
                    'job_count' => $rating['job_count'],
                    'id' => $user->id,
                    'name' => $user->name_with_designation_and_department,
                    'user' => $user
                ];
        }

        usort($chart, function ($a, $b) {
            if ($a['rating']==$b['rating']) {
                return 0;
            }
            return $a['rating'] < $b['rating']?1:-1;
        });

        $i = 0;
        $j = 1;
        $top_charts = array();
        foreach ($chart as $key => $value) {
            $i++;
            if ($i <= 5 && $value['rating']) {
                $value['rank'] = $j;
                $top_charts[] = $value;
                $j++;
            }
        }
        return $top_charts;
    }

    /**
     * Get data for graph.
     *
     * @return array
     */
    public function graph()
    {
        $jobs = $this->fetchJobs()->get();

        $graph_data['job_category'] = $this->categoryWiseGraph($jobs);

        $graph_data['job_priority'] = $this->priorityWiseGraph($jobs);

        $graph_data['job_status'] = $this->statusWiseGraph($jobs);

        return $graph_data;
    }

    /**
     * Get data for graph based on job category.
     *
     * @param Job $jobs
     * @return array
     */
    private function categoryWiseGraph($jobs)
    {
        $categories = array();
        $job_categories = array();
        foreach ($jobs as $job) {
            array_push($categories, $job->JobCategory->name);
        }

        foreach (array_count_values($categories) as $key => $value) {
            $job_categories[] = array('name' => $key,'total' => $value);
        }

        return generateDoughnutGraphData($job_categories, trans('job.job_category'));
    }

    /**
     * Get data for graph based on job priority.
     *
     * @param Job $jobs
     * @return array
     */
    private function priorityWiseGraph($jobs)
    {
        $priorities = array();
        $job_priorities = array();
        foreach ($jobs as $job) {
            array_push($priorities, $job->JobPriority->name);
        }

        foreach (array_count_values($priorities) as $key => $value) {
            $job_priorities[] = array('name' => $key,'total' => $value);
        }

        return generateDoughnutGraphData($job_priorities, trans('job.job_priority'));
    }

    /**
     * Get color of job based on job status.
     *
     * @param string $status
     * @return array
     */
    public function statusWiseColor($status)
    {
        if ($status == 'unassigned') {
            return '#727B84';
        } elseif ($status == 'pending') {
            return '#009EFB';
        } elseif ($status == 'overdue') {
            return '#F2F7F8';
        } elseif ($status == 'late') {
            return '#FFD071';
        } elseif ($status == 'completed') {
            return '#88DD92';
        } else {
            return;
        }
    }

    /**
     * Get data for graph based on job status.
     *
     * @param Job $jobs
     * @return array
     */
    private function statusWiseGraph($jobs)
    {
        $status = array();
        $job_status = array();
        foreach ($jobs as $job) {
            if (!$job->User->count()) {
                array_push($status, 'unassigned');
            } elseif ($job->sign_off_status != 'approved' && $job->due_date > date('Y-m-d')) {
                array_push($status, 'pending');
            } elseif ($job->sign_off_status != 'approved' && $job->due_date < date('Y-m-d')) {
                array_push($status, 'overdue');
            } elseif ($job->sign_off_status == 'approved' && $job->completed_at < getEndOfDate($job->due_date)) {
                array_push($status, 'completed');
            } elseif ($job->sign_off_status == 'approved' && $job->completed_at > getEndOfDate($job->due_date)) {
                array_push($status, 'late');
            }
        }

        foreach (array_count_values($status) as $key => $value) {
            $job_status[] = array('name' => trans('job.'.$key),'total' => $value,'color' => $this->statusWiseColor($key));
        }

        return generateDoughnutGraphData($job_status, trans('job.status'));
    }

    /**
     * Get data for job summary.
     *
     * @return array
     */
    public function summary()
    {
        $users = $this->user->getAccessibleUser(\Auth::user()->id, 1)->get();

        $jobs = $this->getAll();
        $all_sub_job_ratings = $this->sub_job_rating->all();
        foreach ($users as $user) {
            $owned_jobs = $jobs->where('user_id', $user->id)->count();

            $assigned_jobs = $user->Job->count();

            $pending_jobs = $user->Job->filter(function ($item) {
                return (data_get($item, 'sign_off_status') != 'approved');
            })->filter(function ($item) {
                return (data_get($item, 'due_date') > date('Y-m-d'));
            })->count();

            $overdue_jobs = $user->Job->filter(function ($item) {
                return (data_get($item, 'sign_off_status') != 'approved');
            })->filter(function ($item) {
                return (data_get($item, 'due_date') < date('Y-m-d'));
            })->count();

            $completed_jobs = $user->Job->where('sign_off_status', 'approved')->filter(function ($item) {
                return (data_get($item, 'completed_at') <= getEndOfDate(data_get($item, 'due_date')));
            })->count();

            $rating = $this->getUserRating($user, $all_sub_job_ratings);
            $average_rating = ($rating['job_count']) ? $rating['rating']/$rating['job_count'] : 0;

            $data[] = array(
                    'user' => $user,
                    'owned_jobs' => $owned_jobs,
                    'assigned_jobs' => $assigned_jobs,
                    'pending_jobs' => $pending_jobs,
                    'overdue_jobs' => $overdue_jobs,
                    'completed_jobs' => $completed_jobs,
                    'rating' => $average_rating
                );
        }
        return $data;
    }

    /**
     * Get user rating for given user.
     *
     * @param User $user
     * @param array $all_sub_job_ratings
     * @return array
     */
    public function getUserRating($user, $all_sub_job_ratings)
    {
        $rating = 0;
        $job_count = 0;
        foreach ($user->Job as $job) {
            if ($job->sign_off_status == 'approved') {
                if ($job->rating_type == 'job_based' && $job->pivot->rating) {
                    $rating += $job->pivot->rating;
                    $job_count++;
                } elseif ($job->rating_type == 'sub_job_based') {
                    $sub_job_ratings = $all_sub_job_ratings->where('user_id', $user->id)->whereIn('sub_job_id', $job->SubJob->pluck('id')->all())->all();
                    if (count($sub_job_ratings)) {
                        $sub_job_rating_value = 0;
                        foreach ($sub_job_ratings as $sub_job_rating) {
                            $sub_job_rating_value += $sub_job_rating->rating;
                        }
                        $rating += $sub_job_rating_value/count($sub_job_ratings);
                        $job_count++;
                    }
                }
            }
        }

        return array('rating' => $rating,'job_count' => $job_count);
    }

    /**
     * Store user's answer.
     *
     * @param Job $job
     * @param array $params
     * @return Job
     */
    public function answer(Job $job, $params)
    {
        $answers = isset($params['answers']) ? $params['answers'] : [];
        $question_set_id = isset($params['question_set_id']) ? $params['question_set_id'] : null;

        $question_set = $this->question_set->findOrFail($question_set_id);

        $all_question_id = array();
        foreach ($answers as $answer) {
            $all_question_id[] = $answer['id'];

            if (! $answer['answer']) {
                throw ValidationException::withMessages(['answer_'.$answer['id'] => trans('validation.required', ['attribute' => trans('job.answer')])]);
            }
        }

        if (array_diff($all_question_id, $question_set->questions()->pluck('id')->all())) {
            throw ValidationException::withMessages(['message' => trans('job.invalid_questions')]);
        }

        foreach ($answers as $answer) {
            $question_id = $answer['id'];
            $ans = ($answer['answer'] === 'yes') ? 1 : 0;
            $ans_description = $answer['description'];
            $job_answer = $this->answer->firstOrCreate([
                'job_id' => $job->id,
                'question_id' => $question_id
            ]);

            $job_answer->answer = $ans;
            $job_answer->description = $ans_description;
            $job_answer->save();
        }

        return $job;
    }
}
