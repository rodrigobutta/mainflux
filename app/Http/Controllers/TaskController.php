<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRelevanceRepository;
use App\Repositories\TaskFrequencyRepository;
use App\Repositories\TaskComplexityRepository;
use App\Repositories\TaskFamilyRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\TaskRepository;

class TaskController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $project;
    protected $task_relevance;
    protected $task_frequency;
    protected $task_complexity;
    protected $task_family;
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
        ProjectRepository $project,
        TaskRelevanceRepository $task_relevance,
        TaskFrequencyRepository $task_frequency,
        TaskComplexityRepository $task_complexity,
        TaskFamilyRepository $task_family
    ) {
        $this->request    = $request;
        $this->repo       = $repo;
        $this->activity   = $activity;
        $this->project = $project;
        $this->task_relevance = $task_relevance;
        $this->task_frequency = $task_frequency;
        $this->task_complexity = $task_complexity;
        $this->task_family = $task_family;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to fetch Pre-Requisites for task
     * @get ("/api/task/pre-requisite")
     * @return Response
     */
    public function preRequisite()
    {
        $this->authorize('preRequisite', Task::class);

        $projects      = generateSelectOption($this->project->listAll());
        $task_relevances      = generateSelectOption($this->task_relevance->listAll());
        $task_frequencys      = generateSelectOption($this->task_frequency->listAll());
        $task_complexitys      = generateSelectOption($this->task_complexity->listAll());
        $task_familys      = generateSelectOption($this->task_family->listAll());
        
        return $this->success(compact('projects', 'task_relevances', 'task_frequencys', 'task_complexitys', 'task_familys'));
    }

    /**
     * Used to get all Taskes
     * @get ("/api/task")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', Task::class);

        $tasks     = $this->repo->paginate($this->request->all());
        $projects      = $this->project->getAll();
        $task_relevances      = $this->task_relevance->getAll();
        $task_frequencys      = $this->task_frequency->getAll();
        $task_complexitys      = $this->task_complexity->getAll();
        $task_familys      = $this->task_family->getAll();
        
        return $this->success(compact('tasks', 'projects','task_relevances','task_frequencys','task_complexitys','task_familys'));
    }

    /**
     * Used to store Task
     * @post ("/api/task")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Task"),
     *      @Parameter("project_id", type="integer", required="true", description="Id of Project"),
     *      @Parameter("task_relevance_id", type="integer", required="true", description="Id of task relevance")
     *      @Parameter("task_frequency_id", type="integer", required="true", description="Id of task frequency")
     *       @Parameter("task_complexity_id", type="integer", required="true", description="Id of task complexity")
     *       @Parameter("task_family_id", type="integer", required="true", description="Id of task family")
     * })
     * @return Response
     */
    public function store(TaskRequest $request)
    {
        $this->authorize('create', Task::class);

        $task = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $task->id,
            'activity'  => 'added'
        ]);

        $new_task = ['id' => $task->id, 'name' => $task->task_with_project];

        return $this->success(['message' => trans('task.added'),'new_task' => $new_task]);
    }

    /**
     * Used to get Task detail
     * @get ("/api/task/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Task"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $task = $this->repo->findOrFail($id);

        $this->authorize('view', Task::class);

      
        $selected_project = ['name' => $task->Project->name, 'id' => $task->project_id];
        $selected_task_relevance = ['name' => $task->TaskRelevance->name, 'id' => $task->client_id];
        $selected_task_frequency = ['name' => $task->TaskFrequency->name, 'id' => $task->client_id];
        $selected_task_complexity = ['name' => $task->TaskComplexity->name, 'id' => $task->client_id];
        $selected_task_family = ['name' => $task->TaskFamily->name, 'id' => $task->client_id];

        return $this->success(compact('task', 'selected_project', 'selected_task_relevance', 'selected_task_frequency', 'selected_task_complexity', 'selected_task_family'));
    }

    /**
     * Used to update Task
     * @patch ("/api/task/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Task"),
     *      @Parameter("name", type="string", required="true", description="Name of Task"),
     *      @Parameter("project_id", type="integer", required="true", description="Id of Project"),
     *      @Parameter("task_relevance_id", type="integer", required="true", description="Id of Task Relevance")
     *       @Parameter("task_frequency_id", type="integer", required="true", description="Id of Task Frequency")
     *       @Parameter("task_complexity_id", type="integer", required="true", description="Id of Task Complexity")
     *       @Parameter("task_family_id", type="integer", required="true", description="Id of Task Family")
     * })
     * @return Response
     */
    public function update(TaskRequest $request, $id)
    {
        $task = $this->repo->findOrFail($id);

        $this->authorize('view', Task::class);

        $this->authorize('update', Task::class);
        
        $task = $this->repo->update($task, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $task->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('task.updated')]);
    }

    /**
     * Used to delete Task
     * @delete ("/api/task/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Task"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $task = $this->repo->deletable($id);

        $this->authorize('view', Task::class);

        $this->authorize('delete', $task);
        
        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $task->id,
            'sub_module' => $task->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($task);

        return $this->success(['message' => trans('task.deleted')]);
    }
}
