<?php

namespace App\Http\Controllers;

use App\TaskFrequency;
use Illuminate\Http\Request;
use App\Http\Requests\TaskFrequencyRequest;
use App\Repositories\TaskFrequencyRepository;
use App\Repositories\ActivityLogRepository;

class TaskFrequencyController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'task-frequency';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        TaskFrequencyRepository $repo,
        ActivityLogRepository $activity
    ) {
        $this->request  = $request;
        $this->repo     = $repo;
        $this->activity = $activity;

        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all TaskFrequencys
     * @get ("/api/task-frequency")
     * @return Response
     */
    public function index()
    {
        $this->authorize('list', TaskFrequency::class);

        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store TaskFrequency
     * @post ("/api/task-frequency")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of TaskFrequency"),
     *       @Parameter("code", type="string", required="true", description="Code of TaskFrequency"),
     *      @Parameter("description", type="text", required="optional", description="TaskFrequency description")
     * })
     * @return Response
     */
    public function store(TaskFrequencyRequest $request)
    {
        $this->authorize('create', TaskFrequency::class);

        $item = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $item->id,
            'activity'  => 'added'
        ]);

        return $this->success(['message' => trans('task-frequency.added')]);
    }

    /**
     * Used to get TaskFrequency detail
     * @get ("/api/task-frequency/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of TaskFrequency"),
     * })
     * @return Response
     */
    public function show($id)
    {
        $this->authorize('view', TaskFrequency::class);

        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update TaskFrequency
     * @patch ("/api/task-frequency/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of TaskFrequency"),
     *      @Parameter("name", type="string", required="true", description="Name of TaskFrequency"),
     *      @Parameter("code", type="string", required="true", description="Code of TaskFrequency"),
     *      @Parameter("description", type="text", required="optional", description="TaskFrequency description")
     * })
     * @return Response
     */
    public function update(TaskFrequencyRequest $request, $id)
    {
        $this->authorize('update', TaskFrequency::class);

        $item = $this->repo->findOrFail($id);
        
        $item = $this->repo->update($item, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $item->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('task-frequency.updated')]);
    }

    /**
     * Used to delete TaskFrequency
     * @delete ("/api/task-frequency/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of TaskFrequency"),
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

        return $this->success(['message' => trans('task-frequency.deleted')]);
    }
}
