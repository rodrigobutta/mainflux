<?php

namespace App\Http\Controllers;

use App\JobPriority;
use Illuminate\Http\Request;
use App\Http\Requests\JobPriorityRequest;
use App\Repositories\JobPriorityRepository;
use App\Repositories\ActivityLogRepository;

class JobPriorityController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $module = 'job_priority';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        JobPriorityRepository $repo,
        ActivityLogRepository $activity
    ) {
        $this->request  = $request;
        $this->repo     = $repo;
        $this->activity = $activity;
        
        $this->middleware('permission:access-configuration');
    }

    /**
     * Used to get all Job Categories
     * @get ("/api/job-priority")
     * @return Response
     */
    public function index()
    {
        return $this->ok($this->repo->paginate($this->request->all()));
    }

    /**
     * Used to store Job Priority
     * @post ("/api/job-priority")
     * @param ({
     *      @Parameter("name", type="string", required="true", description="Name of Job Priority"),
     *      @Parameter("description", type="text", required="optional", description="Description of Job Priority"),
     * })
     * @return Response
     */
    public function store(JobPriorityRequest $request)
    {
        $job_priority = $this->repo->create($this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $job_priority->id,
            'activity'  => 'added'
        ]);

        return $this->success(['message' => trans('job.job_priority_added')]);
    }

    /**
     * Used to get Job Priority detail
     * @get ("/api/job-priority/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Job Priority"),
     * })
     * @return Response
     */
    public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    /**
     * Used to update Job Priority
     * @patch ("/api/job-priority/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Job Priority"),
     *      @Parameter("name", type="string", required="true", description="Name of Job Priority"),
     *      @Parameter("description", type="text", required="optional", description="Description of Job Priority")
     * })
     * @return Response
     */
    public function update(JobPriorityRequest $request, $id)
    {
        $job_priority = $this->repo->findOrFail($id);
        
        $job_priority = $this->repo->update($job_priority, $this->request->all());

        $this->activity->record([
            'module'    => $this->module,
            'module_id' => $job_priority->id,
            'activity'  => 'updated'
        ]);

        return $this->success(['message' => trans('job.job_priority_updated')]);
    }

    /**
     * Used to delete Job Priority
     * @delete ("/api/job-priority/{id}")
     * @param ({
     *      @Parameter("id", type="integer", required="true", description="Id of Job Priority"),
     * })
     * @return Response
     */
    public function destroy($id)
    {
        $job_priority = $this->repo->deletable($id);

        $this->activity->record([
            'module'     => $this->module,
            'module_id'  => $job_priority->id,
            'sub_module' => $job_priority->name,
            'activity'   => 'deleted'
        ]);

        $this->repo->delete($job_priority);

        return $this->success(['message' => trans('job.job_priority_deleted')]);
    }
}
