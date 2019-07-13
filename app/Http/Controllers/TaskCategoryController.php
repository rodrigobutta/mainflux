<?php

namespace App\Http\Controllers;

use App\TaskCategory;
use Illuminate\Http\Request;
use App\Http\Requests\TaskCategoryRequest;
use App\Repositories\TaskCategoryRepository;
use App\Repositories\ActivityLogRepository;

class TaskCategoryController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'task_category';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        TaskCategoryRepository $repo,
        ActivityLogRepository $activity
    ) {
        $this->request  = $request;
        $this->repo     = $repo;
        $this->activity = $activity;
        
        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all Task Categories
     * @get ("/api/task-category")
     * @return Response
     */
    public function index()
    {
        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store Task Category
     * @post ("/api/task-category")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Task Category"),
     *      @Parameter("description", type="text", required="optional", description="Description of Task Category"),
     * })
     * @return Response
     */
    public function store(TaskCategoryRequest $request)
    {
        $task_category = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $task_category->id,
            'activity'  => 'added'
        ]);

        return $this->success(['message' => trans('task.task_category_added')]);
    }

    /**
     * Used to get Task Category detail
     * @get ("/api/task-category/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Task Category"),
     * })
     * @return Response
     */
    public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update Task Category
     * @patch ("/api/task-category/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Task Category"),
     *      @Parameter("name", type="string", required="true", description="Name of Task Category"),
     *      @Parameter("description", type="text", required="optional", description="Description of Task Category")
     * })
     * @return Response
     */
    public function update(TaskCategoryRequest $request, $id)
    {
        $task_category = $this->repo->findOrFail($id);
        
        $task_category = $this->repo->update($task_category, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $task_category->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('task.task_category_updated')]);
    }

    /**
     * Used to delete Task Category
     * @delete ("/api/task-category/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Task Category"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $task_category = $this->repo->deletable($id);

        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $task_category->id,
            'sub_module' => $task_category->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($task_category);

        return $this->success(['message' => trans('task.task_category_deleted')]);
    }
}
