<?php

namespace App\Http\Controllers;

use App\TaskFamily;
use Illuminate\Http\Request;
use App\Http\Requests\TaskFamilyRequest;
use App\Repositories\TaskFamilyRepository;
use App\Repositories\ActivityLogRepository;

class TaskFamilyController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'task-family';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        TaskFamilyRepository $repo,
        ActivityLogRepository $activity
    ) {
        $this->request  = $request;
        $this->repo     = $repo;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all TaskFamilys
     * @get ("/api/task-family")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', TaskFamily::class);

        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store TaskFamily
     * @post ("/api/task-family")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of TaskFamily"),
     *       @Parameter("code", type="string", required="true", description="Code of TaskFamily"),
     *      @Parameter("description", type="text", required="optional", description="TaskFamily description")
     * })
     * @return Response
     */
    public function store(TaskFamilyRequest $request)
    {
        $this->authorize('create', TaskFamily::class);

        $item = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $item->id,
            'activity'  => 'added'
        ]);

        return $this->success(['message' => trans('task_family.added')]);
    }

    /**
     * Used to get TaskFamily detail
     * @get ("/api/task-family/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of TaskFamily"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $this->authorize('view', TaskFamily::class);

        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update TaskFamily
     * @patch ("/api/task-family/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of TaskFamily"),
     *      @Parameter("name", type="string", required="true", description="Name of TaskFamily"),
     *      @Parameter("code", type="string", required="true", description="Code of TaskFamily"),
     *      @Parameter("description", type="text", required="optional", description="TaskFamily description")
     * })
     * @return Response
     */
    public function update(TaskFamilyRequest $request, $id)
    {
        $this->authorize('update', TaskFamily::class);

        $item = $this->repo->findOrFail($id);
        
        $item = $this->repo->update($item, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $item->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('task_family.updated')]);
    }

    /**
     * Used to delete TaskFamily
     * @delete ("/api/task-family/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of TaskFamily"),
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

        return $this->success(['message' => trans('task_family.deleted')]);
    }
}
