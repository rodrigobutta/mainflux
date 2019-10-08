<?php

namespace App\Http\Controllers;

use App\TaskRelevance;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRelevanceRequest;
use App\Repositories\TaskRelevanceRepository;
use App\Repositories\ActivityLogRepository;

class TaskRelevanceController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'task-relevance';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        TaskRelevanceRepository $repo,
        ActivityLogRepository $activity
    ) {
        $this->request  = $request;
        $this->repo     = $repo;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all TaskRelevances
     * @get ("/api/task-relevance")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', TaskRelevance::class);

        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store TaskRelevance
     * @post ("/api/task-relevance")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of TaskRelevance"),
     *       @Parameter("code", type="string", required="true", description="Code of TaskRelevance"),
     *      @Parameter("description", type="text", required="optional", description="TaskRelevance description")
     * })
     * @return Response
     */
    public function store(TaskRelevanceRequest $request)
    {
        $this->authorize('create', TaskRelevance::class);

        $item = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $item->id,
            'activity'  => 'added'
        ]);

        return $this->success(['message' => trans('task_relevance.added')]);
    }

    /**
     * Used to get TaskRelevance detail
     * @get ("/api/task-relevance/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of TaskRelevance"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $this->authorize('view', TaskRelevance::class);

        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update TaskRelevance
     * @patch ("/api/task-relevance/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of TaskRelevance"),
     *      @Parameter("name", type="string", required="true", description="Name of TaskRelevance"),
     *      @Parameter("code", type="string", required="true", description="Code of TaskRelevance"),
     *      @Parameter("description", type="text", required="optional", description="TaskRelevance description")
     * })
     * @return Response
     */
    public function update(TaskRelevanceRequest $request, $id)
    {
        $this->authorize('update', TaskRelevance::class);

        $item = $this->repo->findOrFail($id);
        
        $item = $this->repo->update($item, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $item->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('task_relevance.updated')]);
    }

    /**
     * Used to delete TaskRelevance
     * @delete ("/api/task-relevance/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of TaskRelevance"),
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

        return $this->success(['message' => trans('task_relevance.deleted')]);
    }
}
