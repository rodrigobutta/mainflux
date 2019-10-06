<?php

namespace App\Http\Controllers;

use App\Job;
use Illuminate\Http\Request;
use App\Http\Requests\JobRequest;
use App\Repositories\JobRepository;
use App\Repositories\UserRepository;
use App\Repositories\UploadRepository;
use App\Http\Requests\JobProgressRequest;
use App\Repositories\ActivityLogRepository;
use App\Repositories\QuestionSetRepository;
use App\Http\Requests\JobRecurrenceRequest;
use App\Repositories\JobCategoryRepository;
use App\Repositories\JobPriorityRepository;
use App\Http\Requests\JobConfigurationRequest;
use App\Repositories\ClientRepository;
use App\Repositories\ContractorRepository;
use App\Repositories\ProjectRepository;

class JobController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $job_category;
    protected $job_priority;
    protected $user;
    protected $upload;
    protected $question_set;
    protected $client;
    protected $contractor;
    protected $project;


    protected $module = 'job';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        JobRepository $repo,
        ActivityLogRepository $activity,
        JobCategoryRepository $job_category,
        JobPriorityRepository $job_priority,
        UserRepository $user,
        UploadRepository $upload,
        QuestionSetRepository $question_set,
        ClientRepository $client,
        ContractorRepository $contractor,
        ProjectRepository $project
    ) {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->job_category = $job_category;
        $this->job_priority = $job_priority;
        $this->user = $user;
        $this->upload = $upload;
        $this->question_set = $question_set;
        $this->client = $client;
        $this->contractor = $contractor;
        $this->project = $project;

        $this->middleware('prohibited.test.mode')->only(['destroy']);
    }

    /**
     * Used to fetch Pre-Requisites for Job
     * @get ("/api/job/pre-requisite")
     * @return Response
     */
    public function preRequisite()
    {
        $this->authorize('preRequisite', Job::class);

        $job_categories = $this->job_category->selectAll();
        $job_priorities = $this->job_priority->selectAll();
        $question_sets = $this->question_set->selectAll();
        $clients = $this->client->selectAll();
        $contractors = $this->contractor->selectAll();

        $auth_user = \Auth::user();


        $projects = $this->project->selectForCouple($auth_user->profile->client_id, $auth_user->profile->contractor_id); // $this->project->selectAll();

        $users = generateSelectOption($this->user->getAccessibleUser(\Auth::user()->id, 1)->get()->pluck('name_with_designation_and_department', 'id')->all());

        return $this->success(compact('job_categories', 'job_priorities', 'users','question_sets', 'clients', 'contractors', 'projects'));
    }

    /**
     * Used to get all Jobs
     * @get ("/api/job")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Job::class);

        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**rtyui
     * Used to store Job
     * @post ("/api/job")
     * @param ({
     *      @Parameter("title", type="string", required="true", description="Title of Job"),
     *      @Parameter("description", type="text", required="true", description="Description of Job"),
     *      @Parameter("start_date", type="date", required="true", description="Start Date of Job"),
     *      @Parameter("due_date", type="date", required="true", description="Due Date of Job"),
     *      @Parameter("job_category_id", type="integer", required="true", description="Category of Job"),
     *      @Parameter("job_priority_id", type="integer", required="true", description="Priority of Job"),
     * *      @Parameter("client_id", type="integer", required="true", description="Client"),
     * *      @Parameter("contractor_id", type="integer", required="true", description="Contractor"),
     * *      @Parameter("project_id", type="integer", required="false", description="Project"),
     * })
     * @return Response
     */
    public function store(JobRequest $request)
    {
        $this->authorize('create', Job::class);

        $job = $this->repo->create($this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('job.added')]);
    }

    /**
     * Used to get Job detail
     * @get ("/api/job/{uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job")
     * })
     * @return Response
     */
    public function show($uuid)
    {
        $this->authorize('view', Job::class);

        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->accessible($job);

        $selected_job_category = ($job->job_category_id) ? ['id' => $job->job_category_id,'name' => $job->JobCategory->name] : [];
        $selected_job_priority = ($job->job_priority_id) ? ['id' => $job->job_priority_id,'name' => $job->JobPriority->name] : [];
        $selected_question_set  = ($job->question_set_id) ? ['id' => $job->question_set_id,'name' => $job->QuestionSet->name] : [];
        $user_id = $job->user()->pluck('user_id')->all();

        $selected_users = generateSelectOption($this->user->listByNameWithDesignationForSelectedId($job->user()->pluck('user_id')->all()));
        $attachments =  $this->upload->getAttachment($this->module, $job->id);
        $starred_jobs = $job->starredJob()->pluck('user_id')->all();
        $question_set = ($job->question_set_id) ? $this->question_set->findOrFail($job->question_set_id) : null;
        $is_locked = in_array($job->sign_off_status, ['requested','approved']) ? 1 : 0;

        $selected_client = ($job->client_id) ? ['id' => $job->client_id,'name' => $job->client->name] : [];
        $selected_contractor = ($job->contractor_id) ? ['id' => $job->contractor_id,'name' => $job->contractor->name] : [];
        $selected_project = ($job->project_id) ? ['id' => $job->project_id,'name' => $job->project->name] : [];

        $is_editable = $this->repo->editable($job, 1);

        return $this->success(compact('job', 'selected_job_category', 'selected_job_priority', 'selected_question_set', 'selected_users', 'user_id', 'is_editable', 'attachments', 'starred_jobs','question_set','is_locked', 'selected_client', 'selected_contractor', 'selected_project'));
    }

    /**
     * Used to update Job
     * @patch ("/api/job/{uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("description", type="text", required="true", description="Description of Job"),
     *      @Parameter("title", type="string", required="true", description="Title of Job"),
     *      @Parameter("start_date", type="date", required="true", description="Start Date of Job"),
     *      @Parameter("due_date", type="date", required="true", description="Due Date of Job"),
     *      @Parameter("job_category_id", type="integer", required="true", description="Category of Job"),
     *      @Parameter("job_priority_id", type="integer", required="true", description="Priority of Job"),
     * *      @Parameter("client_id", type="integer", required="true", description="Client"),
     * *      @Parameter("contractor:id", type="integer", required="true", description="Contractor"),
     * *      @Parameter("project_id", type="integer", required="false", description="Project"),
     * })
     * @return Response
     */
    public function update(JobRequest $request, $uuid)
    {
        $this->authorize('update', Job::class);

        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->editable($job);

        $this->repo->statusLocked($job);


// dd($this->request->all());
// dd($this->request->get('project_id'));

        $job = $this->repo->update($job, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('job.updated')]);
    }

    /**
     * Used to delete Job
     * @delete ("/api/job/{uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     * })
     * @return Response
     */
    public function destroy($uuid)
    {
        $this->authorize('delete', Job::class);

        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->editable($job);

        $this->repo->statusLocked($job);

        $this->upload->delete($this->module, $job->id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($job);

        return $this->success(['message' => trans('job.deleted')]);
    }

    /**
     * Used to download Job Attachment
     * @get ("/job/{uuid}/attachment/{attachment_uuid}/download")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response download
     */
    public function download($uuid, $attachment_uuid)
    {
        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->accessible($job);

        $attachment =  $this->upload->getAttachment($this->module, $job->id, $attachment_uuid);

        try {
            \Storage::exists($attachment->filename);
        } catch (\Exception $e) {
        }

        $this->activity->record([
            'module'        => 'attachment',
            'module_id'     => $attachment->id,
            'sub_module'    => $this->module,
            'sub_module_id' => $job->id,
            'activity'      => 'downloaded'
        ]);

        $download_path = storage_path('app/'.$attachment->filename);
        return response()->download($download_path, $attachment->user_filename);
    }

    /**
     * Used to update Job configuration
     * @get ("/api/job/{uuid}/config")
     * @return Response
     */
    public function configuration(JobConfigurationRequest $request, $uuid)
    {
        $this->authorize('update', Job::class);

        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->editable($job);

        $this->repo->statusLocked($job);

        $job = $this->repo->configuration($job, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('job.updated')]);
    }

    /**
     * Used to toggle Job star
     * @post ("/api/job/{uuid}/star")
     * @param ({
     *      @Parameter("uuid", type="integer", required="true", description="Unique Id of Job"),
     * })
     * @return Response
     */
    public function toggleStar($uuid)
    {
        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->accessible($job);

        if ($job->StarredJob->where('user_id', \Auth::user()->id)->count()) {
            \App\StarredJob::whereJobId($job->id)->whereUserId(\Auth::user()->id)->delete();
            $activity = 'unstarred';
        } else {
            \App\StarredJob::forceCreate(['job_id' => $job->id,'user_id' => \Auth::user()->id]);
            $activity = 'starred';
        }

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job->id,
            'activity' => $activity
        ]);

        return $this->success(compact('activity'));
    }

    /**
     * Used to toggle Job archive
     * @post ("/api/job/{uuid}/archive")
     * @param ({
     *      @Parameter("uuid", type="integer", required="true", description="Unique Id of Job"),
     * })
     * @return Response
     */
    public function toggleArchive($uuid)
    {
        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->isNotApproved($job);

        $this->repo->isNotOwner($job);

        $job->is_archived = !$job->is_archived;
        $job->save();

        $activity = ($job->is_archived) ? 'archived' : 'unarchived';

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job->id,
            'activity' => $activity
        ]);

        return $this->success(['message' => trans('activity.'.$activity, ['activity' => trans('job.job')])]);
    }

    /**
     * Used to update Job progress
     * @post ("/api/job/{uuid}/progress")
     * @param ({
     *      @Parameter("uuid", type="integer", required="true", description="Unique Id of Job"),
     *      @Parameter("progress", type="integer", required="true", description="Progress of Job, Min 0, Max 100"),
     * })
     * @return Response
     */
    public function updateProgress(JobProgressRequest $request, $uuid)
    {
        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->accessible($job);

        $this->repo->statusLocked($job);

        $job = $this->repo->updateProgress($job, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job->id,
            'sub_module' => 'progress',
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('job.updated')]);
    }

    /**
     * Used to update Job questions
     * @post ("/api/job/{uuid}/rating")
     * @param ({
     *      @Parameter("uuid", type="integer", required="true", description="Unique Id of Job")
     * })
     * @return Response
     */
    public function answer($uuid)
    {
        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->accessible($job);

        $this->repo->statusLocked($job);

        $job = $this->repo->answer($job, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job->id,
            'sub_module' => 'answer',
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('job.answered')]);
    }

    /**
     * Used to update Job recurrence
     * @post ("/api/job/{uuid}/recurrence")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("is_recurring", type="boolean", required="true", description="Is job recurring?"),
     *      @Parameter("recurrence_start_date", type="date", required_if="is_recurring = true", description="Start date of Recurrence"),
     *      @Parameter("recurrence_end_date", type="date", required_if="is_recurring = true", description="End date of Recurrence"),
     *      @Parameter("recurring_frequency", type="integer", required_if="is_recurring = true", description="Frequency of Recurrence"),
     * })
     * @return Response
     */
    public function Recurrence(JobRecurrenceRequest $request, $uuid)
    {
        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->editable($job);

        $job = $this->repo->updateRecurrence($job, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('job.updated')]);
    }

    /**
     * Used to get Recurring Job
     * @get ("/api/job/{uuid}/recurring")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     * })
     * @return Response
     */
    public function listRecurring($uuid)
    {
        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->editable($job);

        $recurring_frequencies = $this->repo->listRecurringFrequency();

        $selected_recurring_frequency = $job->is_recurring ? ['id' => $job->recurring_frequency, 'name' => nestedArraySearch('id', $job->recurring_frequency, 'name', $recurring_frequencies)] : [];

        $recurring_jobs = $this->repo->paginate([
            'recurring_job_id' => $job->id,
            'page_length' => request('page_length')
        ]);

        $next_recurring_date = $job->next_recurring_date;

        return $this->success(compact('recurring_frequencies', 'selected_recurring_frequency', 'recurring_jobs', 'job','next_recurring_date'));
    }

    /**
     * Used to copy Job
     * @post ("/api/job/{uuid}/copy")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     * })
     * @return Response
     */
    public function copy($uuid)
    {
        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->editable($job);

        $new_job = $this->repo->copy($job, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job->id,
            'activity' => 'copied'
        ]);

        return $this->success(['message' => trans('activity.copied', ['activity' => trans('job.job')]),'new_job' => $new_job]);
    }

    /**
     * Used to rate users if rating type is job based
     * @post ("/api/job/{uuid}/rating")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("row", type="array", required="true", description="Array of user_id, rating and remarks"),
     * })
     * @return Response
     */
    public function jobRating($uuid)
    {
        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->isNotApproved($job);

        $this->repo->isNotOwner($job);

        $this->repo->rating($job, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job->id,
            'activity' => 'rated'
        ]);

        return $this->success(['message' => trans('job.rating_updated')]);
    }

    /**
     * Used to rate users if rating type is sub job based
     * @post ("/api/job/{uuid}/rating/sub-job")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("user_id", type="integer", required="true", description="Id of User"),
     *      @Parameter("row", type="array", required="true", description="Array of rating and remarks"),
     * })
     * @return Response
     */
    public function subJobRating($uuid)
    {
        $job = $this->repo->findByUuidOrFail($uuid);

        $this->repo->isNotApproved($job);

        $this->repo->isNotOwner($job);

        $this->repo->subJobRating($job, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job->id,
            'activity' => 'rated'
        ]);

        return $this->success(['message' => trans('job.rating_updated')]);
    }

    /**
     * Used to get job rating chart
     * @post ("/api/job/rating/top-chart")
     * @param ({
     * })
     * @return Response
     */
    public function ratingChart()
    {
        return $this->ok($this->repo->ratingChart());
    }

    /**
     * Used to get job graph for dashboard
     * @post ("/api/job/graph/dashboard")
     * @param ({
     * })
     * @return Response
     */
    public function graph()
    {
        return $this->ok($this->repo->graph());
    }

    /**
     * Used to generate report of job summary
     * @post ("/api/report/job/summary")
     * @param ({
     * })
     * @return Response
     */
    public function summary()
    {
        return $this->ok($this->repo->summary());
    }
}
