<?php

namespace App\Http\Controllers;

use App\TaskPriority;
use Illuminate\Http\Request;
use App\Http\Requests\TaskPriorityRequest;
use App\Repositories\TaskPriorityRepository;
use App\Repositories\ActivityLogRepository;

class TaskPriorityController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'task_priority';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        TaskPriorityRepository $repo,
        ActivityLogRepository $activity
    ) {
        $this->request  = $request;
        $this->repo     = $repo;
        $this->activity = $activity;
        
        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all Task Categories
     * @get ("/api/task-priority")
     * @return Response
     */
    public function index()
    {
        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store Task Priority
     * @post ("/api/task-priority")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Task Priority"),
     *      @Parameter("description", type="text", required="optional", description="Description of Task Priority"),
     * })
     * @return Response
     */
    public function store(TaskPriorityRequest $request)
    {
        $task_priority = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $task_priority->id,
            'activity'  => 'added'
        ]);

        return $this->success(['message' => trans('task.task_priority_added')]);
    }

    /**
     * Used to get Task Priority detail
     * @get ("/api/task-priority/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Task Priority"),
     * })
     * @return Response
     */
    public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update Task Priority
     * @patch ("/api/task-priority/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Task Priority"),
     *      @Parameter("name", type="string", required="true", description="Name of Task Priority"),
     *      @Parameter("description", type="text", required="optional", description="Description of Task Priority")
     * })
     * @return Response
     */
    public function update(TaskPriorityRequest $request, $id)
    {
        $task_priority = $this->repo->findOrFail($id);
        
        $task_priority = $this->repo->update($task_priority, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $task_priority->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('task.task_priority_updated')]);
    }

    /**
     * Used to delete Task Priority
     * @delete ("/api/task-priority/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Task Priority"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $task_priority = $this->repo->deletable($id);

        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $task_priority->id,
            'sub_module' => $task_priority->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($task_priority);

        return $this->success(['message' => trans('task.task_priority_deleted')]);
    }
}
