<?php

namespace App\Http\Controllers;

use App\TaskComplexity;
use Illuminate\Http\Request;
use App\Http\Requests\TaskComplexityRequest;
use App\Repositories\TaskComplexityRepository;
use App\Repositories\ActivityLogRepository;

class TaskComplexityController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'task-complexity';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        TaskComplexityRepository $repo,
        ActivityLogRepository $activity
    ) {
        $this->request  = $request;
        $this->repo     = $repo;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all TaskComplexitys
     * @get ("/api/task-complexity")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', TaskComplexity::class);

        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store TaskComplexity
     * @post ("/api/task-complexity")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of TaskComplexity"),
     *       @Parameter("code", type="string", required="true", description="Code of TaskComplexity"),
     *      @Parameter("description", type="text", required="optional", description="TaskComplexity description")
     * })
     * @return Response
     */
    public function store(TaskComplexityRequest $request)
    {
        $this->authorize('create', TaskComplexity::class);

        $item = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $item->id,
            'activity'  => 'added'
        ]);

        return $this->success(['message' => trans('task_complexity.added')]);
    }

    /**
     * Used to get TaskComplexity detail
     * @get ("/api/task-complexity/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of TaskComplexity"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $this->authorize('view', TaskComplexity::class);

        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update TaskComplexity
     * @patch ("/api/task-complexity/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of TaskComplexity"),
     *      @Parameter("name", type="string", required="true", description="Name of TaskComplexity"),
     *      @Parameter("code", type="string", required="true", description="Code of TaskComplexity"),
     *      @Parameter("description", type="text", required="optional", description="TaskComplexity description")
     * })
     * @return Response
     */
    public function update(TaskComplexityRequest $request, $id)
    {
        $this->authorize('update', TaskComplexity::class);

        $item = $this->repo->findOrFail($id);
        
        $item = $this->repo->update($item, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $item->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('task_complexity.updated')]);
    }

    /**
     * Used to delete TaskComplexity
     * @delete ("/api/task-complexity/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of TaskComplexity"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $item = $this->repo->deletable($id);

        $this->authorize('delete', $item);

        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $item->id,
            'sub_module' => $item->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($item);

        return $this->success(['message' => trans('task_complexity.deleted')]);
    }
}
