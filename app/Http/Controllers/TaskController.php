<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use App\Repositories\UploadRepository;
use App\Http\Requests\TaskProgressRequest;
use App\Repositories\ActivityLogRepository;
use App\Repositories\QuestionSetRepository;
use App\Http\Requests\TaskRecurrenceRequest;
use App\Repositories\TaskCategoryRepository;
use App\Repositories\TaskPriorityRepository;
use App\Http\Requests\TaskConfigurationRequest;
use App\Repositories\ClientRepository;
use App\Repositories\ContractorRepository;
use App\Repositories\ProjectRepository;

class TaskController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $task_category;
    protected $task_priority;
    protected $user;
    protected $upload;
    protected $question_set;
    protected $client;
    protected $contractor;
    protected $project;


    protected $module = 'task';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        TaskRepository $repo,
        ActivityLogRepository $activity,
        TaskCategoryRepository $task_category,
        TaskPriorityRepository $task_priority,
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
        $this->task_category = $task_category;
        $this->task_priority = $task_priority;
        $this->user = $user;
        $this->upload = $upload;
        $this->question_set = $question_set;
        $this->client = $client;
        $this->contractor = $contractor;
        $this->project = $project;

        $this->middleware('prohibited.test.mode')->only(['destroy']);
    }

    /**
     * Used to fetch Pre-Requisites for Task
     * @get ("/api/task/pre-requisite")
     * @return Response
     */
    public function preRequisite()
    {
        $this->authorize('preRequisite', Task::class);

        $task_categories = $this->task_category->selectAll();
        $task_priorities = $this->task_priority->selectAll();
        $question_sets = $this->question_set->selectAll();
        $clients = $this->client->selectAll();
        $contractors = $this->contractor->selectAll();
        $projects = $this->project->selectForCouple(); // $this->project->selectAll();

        $users = generateSelectOption($this->user->getAccessibleUser(\Auth::user()->id, 1)->get()->pluck('name_with_designation_and_department', 'id')->all());

        return $this->success(compact('task_categories', 'task_priorities', 'users','question_sets', 'clients', 'contractors', 'projects'));
    }

    /**
     * Used to get all Tasks
     * @get ("/api/task")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Task::class);

        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**rtyui
     * Used to store Task
     * @post ("/api/task")
     * @param ({
     *      @Parameter("title", type="string", required="true", description="Title of Task"),
     *      @Parameter("description", type="text", required="true", description="Description of Task"),
     *      @Parameter("start_date", type="date", required="true", description="Start Date of Task"),
     *      @Parameter("due_date", type="date", required="true", description="Due Date of Task"),
     *      @Parameter("task_category_id", type="integer", required="true", description="Category of Task"),
     *      @Parameter("task_priority_id", type="integer", required="true", description="Priority of Task"),
     * *      @Parameter("client_id", type="integer", required="true", description="Client"),
     * *      @Parameter("contractor_id", type="integer", required="true", description="Contractor"),
     * *      @Parameter("project_id", type="integer", required="false", description="Project"),
     * })
     * @return Response
     */
    public function store(TaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $task = $this->repo->create($this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('task.added')]);
    }

    /**
     * Used to get Task detail
     * @get ("/api/task/{uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task")
     * })
     * @return Response
     */
    public function show($uuid)
    {
        $this->authorize('view', Task::class);

        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->accessible($task);

        $selected_task_category = ($task->task_category_id) ? ['id' => $task->task_category_id,'name' => $task->TaskCategory->name] : [];
        $selected_task_priority = ($task->task_priority_id) ? ['id' => $task->task_priority_id,'name' => $task->TaskPriority->name] : [];
        $selected_question_set  = ($task->question_set_id) ? ['id' => $task->question_set_id,'name' => $task->QuestionSet->name] : [];
        $user_id = $task->user()->pluck('user_id')->all();

        $selected_users = generateSelectOption($this->user->listByNameWithDesignationForSelectedId($task->user()->pluck('user_id')->all()));
        $attachments =  $this->upload->getAttachment($this->module, $task->id);
        $starred_tasks = $task->starredTask()->pluck('user_id')->all();
        $question_set = ($task->question_set_id) ? $this->question_set->findOrFail($task->question_set_id) : null;
        $is_locked = in_array($task->sign_off_status, ['requested','approved']) ? 1 : 0;

        $selected_client = ($task->client_id) ? ['id' => $task->client_id,'name' => $task->client->name] : [];
        $selected_contractor = ($task->contractor_id) ? ['id' => $task->contractor_id,'name' => $task->contractor->name] : [];
        $selected_project = ($task->project_id) ? ['id' => $task->project_id,'name' => $task->project->name] : [];

        $is_editable = $this->repo->editable($task, 1);

        return $this->success(compact('task', 'selected_task_category', 'selected_task_priority', 'selected_question_set', 'selected_users', 'user_id', 'is_editable', 'attachments', 'starred_tasks','question_set','is_locked', 'selected_client', 'selected_contractor', 'selected_project'));
    }

    /**
     * Used to update Task
     * @patch ("/api/task/{uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("description", type="text", required="true", description="Description of Task"),
     *      @Parameter("title", type="string", required="true", description="Title of Task"),
     *      @Parameter("start_date", type="date", required="true", description="Start Date of Task"),
     *      @Parameter("due_date", type="date", required="true", description="Due Date of Task"),
     *      @Parameter("task_category_id", type="integer", required="true", description="Category of Task"),
     *      @Parameter("task_priority_id", type="integer", required="true", description="Priority of Task"),
     * *      @Parameter("client_id", type="integer", required="true", description="Client"),
     * *      @Parameter("contractor:id", type="integer", required="true", description="Contractor"),
     * *      @Parameter("project_id", type="integer", required="false", description="Project"),
     * })
     * @return Response
     */
    public function update(TaskRequest $request, $uuid)
    {
        $this->authorize('update', Task::class);

        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->editable($task);

        $this->repo->statusLocked($task);


// dd($this->request->all());
// dd($this->request->get('project_id'));

        $task = $this->repo->update($task, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('task.updated')]);
    }

    /**
     * Used to delete Task
     * @delete ("/api/task/{uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     * })
     * @return Response
     */
    public function destroy($uuid)
    {
        $this->authorize('delete', Task::class);

        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->editable($task);

        $this->repo->statusLocked($task);

        $this->upload->delete($this->module, $task->id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($task);

        return $this->success(['message' => trans('task.deleted')]);
    }

    /**
     * Used to download Task Attachment
     * @get ("/task/{uuid}/attachment/{attachment_uuid}/download")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response download
     */
    public function download($uuid, $attachment_uuid)
    {
        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->accessible($task);

        $attachment =  $this->upload->getAttachment($this->module, $task->id, $attachment_uuid);

        try {
            \Storage::exists($attachment->filename);
        } catch (\Exception $e) {
        }

        $this->activity->record([
            'module'        => 'attachment',
            'module_id'     => $attachment->id,
            'sub_module'    => $this->module,
            'sub_module_id' => $task->id,
            'activity'      => 'downloaded'
        ]);

        $download_path = storage_path('app/'.$attachment->filename);
        return response()->download($download_path, $attachment->user_filename);
    }

    /**
     * Used to update Task configuration
     * @get ("/api/task/{uuid}/config")
     * @return Response
     */
    public function configuration(TaskConfigurationRequest $request, $uuid)
    {
        $this->authorize('update', Task::class);

        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->editable($task);

        $this->repo->statusLocked($task);

        $task = $this->repo->configuration($task, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('task.updated')]);
    }

    /**
     * Used to toggle Task star
     * @post ("/api/task/{uuid}/star")
     * @param ({
     *      @Parameter("uuid", type="integer", required="true", description="Unique Id of Task"),
     * })
     * @return Response
     */
    public function toggleStar($uuid)
    {
        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->accessible($task);

        if ($task->StarredTask->where('user_id', \Auth::user()->id)->count()) {
            \App\StarredTask::whereTaskId($task->id)->whereUserId(\Auth::user()->id)->delete();
            $activity = 'unstarred';
        } else {
            \App\StarredTask::forceCreate(['task_id' => $task->id,'user_id' => \Auth::user()->id]);
            $activity = 'starred';
        }

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task->id,
            'activity' => $activity
        ]);

        return $this->success(compact('activity'));
    }

    /**
     * Used to toggle Task archive
     * @post ("/api/task/{uuid}/archive")
     * @param ({
     *      @Parameter("uuid", type="integer", required="true", description="Unique Id of Task"),
     * })
     * @return Response
     */
    public function toggleArchive($uuid)
    {
        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->isNotApproved($task);

        $this->repo->isNotOwner($task);

        $task->is_archived = !$task->is_archived;
        $task->save();

        $activity = ($task->is_archived) ? 'archived' : 'unarchived';

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task->id,
            'activity' => $activity
        ]);

        return $this->success(['message' => trans('activity.'.$activity, ['activity' => trans('task.task')])]);
    }

    /**
     * Used to update Task progress
     * @post ("/api/task/{uuid}/progress")
     * @param ({
     *      @Parameter("uuid", type="integer", required="true", description="Unique Id of Task"),
     *      @Parameter("progress", type="integer", required="true", description="Progress of Task, Min 0, Max 100"),
     * })
     * @return Response
     */
    public function updateProgress(TaskProgressRequest $request, $uuid)
    {
        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->accessible($task);

        $this->repo->statusLocked($task);

        $task = $this->repo->updateProgress($task, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task->id,
            'sub_module' => 'progress',
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('task.updated')]);
    }

    /**
     * Used to update Task questions
     * @post ("/api/task/{uuid}/rating")
     * @param ({
     *      @Parameter("uuid", type="integer", required="true", description="Unique Id of Task")
     * })
     * @return Response
     */
    public function answer($uuid)
    {
        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->accessible($task);

        $this->repo->statusLocked($task);

        $task = $this->repo->answer($task, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task->id,
            'sub_module' => 'answer',
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('task.answered')]);
    }

    /**
     * Used to update Task recurrence
     * @post ("/api/task/{uuid}/recurrence")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("is_recurring", type="boolean", required="true", description="Is task recurring?"),
     *      @Parameter("recurrence_start_date", type="date", required_if="is_recurring = true", description="Start date of Recurrence"),
     *      @Parameter("recurrence_end_date", type="date", required_if="is_recurring = true", description="End date of Recurrence"),
     *      @Parameter("recurring_frequency", type="integer", required_if="is_recurring = true", description="Frequency of Recurrence"),
     * })
     * @return Response
     */
    public function Recurrence(TaskRecurrenceRequest $request, $uuid)
    {
        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->editable($task);

        $task = $this->repo->updateRecurrence($task, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('task.updated')]);
    }

    /**
     * Used to get Recurring Task
     * @get ("/api/task/{uuid}/recurring")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     * })
     * @return Response
     */
    public function listRecurring($uuid)
    {
        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->editable($task);

        $recurring_frequencies = $this->repo->listRecurringFrequency();

        $selected_recurring_frequency = $task->is_recurring ? ['id' => $task->recurring_frequency, 'name' => nestedArraySearch('id', $task->recurring_frequency, 'name', $recurring_frequencies)] : [];

        $recurring_tasks = $this->repo->paginate([
            'recurring_task_id' => $task->id,
            'page_length' => request('page_length')
        ]);

        $next_recurring_date = $task->next_recurring_date;

        return $this->success(compact('recurring_frequencies', 'selected_recurring_frequency', 'recurring_tasks', 'task','next_recurring_date'));
    }

    /**
     * Used to copy Task
     * @post ("/api/task/{uuid}/copy")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     * })
     * @return Response
     */
    public function copy($uuid)
    {
        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->editable($task);

        $new_task = $this->repo->copy($task, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task->id,
            'activity' => 'copied'
        ]);

        return $this->success(['message' => trans('activity.copied', ['activity' => trans('task.task')]),'new_task' => $new_task]);
    }

    /**
     * Used to rate users if rating type is task based
     * @post ("/api/task/{uuid}/rating")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("row", type="array", required="true", description="Array of user_id, rating and remarks"),
     * })
     * @return Response
     */
    public function taskRating($uuid)
    {
        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->isNotApproved($task);

        $this->repo->isNotOwner($task);

        $this->repo->rating($task, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task->id,
            'activity' => 'rated'
        ]);

        return $this->success(['message' => trans('task.rating_updated')]);
    }

    /**
     * Used to rate users if rating type is sub task based
     * @post ("/api/task/{uuid}/rating/sub-task")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("user_id", type="integer", required="true", description="Id of User"),
     *      @Parameter("row", type="array", required="true", description="Array of rating and remarks"),
     * })
     * @return Response
     */
    public function subTaskRating($uuid)
    {
        $task = $this->repo->findByUuidOrFail($uuid);

        $this->repo->isNotApproved($task);

        $this->repo->isNotOwner($task);

        $this->repo->subTaskRating($task, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task->id,
            'activity' => 'rated'
        ]);

        return $this->success(['message' => trans('task.rating_updated')]);
    }

    /**
     * Used to get task rating chart
     * @post ("/api/task/rating/top-chart")
     * @param ({
     * })
     * @return Response
     */
    public function ratingChart()
    {
        return $this->ok($this->repo->ratingChart());
    }

    /**
     * Used to get task graph for dashboard
     * @post ("/api/task/graph/dashboard")
     * @param ({
     * })
     * @return Response
     */
    public function graph()
    {
        return $this->ok($this->repo->graph());
    }

    /**
     * Used to generate report of task summary
     * @post ("/api/report/task/summary")
     * @param ({
     * })
     * @return Response
     */
    public function summary()
    {
        return $this->ok($this->repo->summary());
    }
}
