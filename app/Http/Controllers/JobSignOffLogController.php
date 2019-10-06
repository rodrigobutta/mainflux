<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\JobRepository;
use App\Repositories\ActivityLogRepository;
use App\Http\Requests\JobSignOffLogRequest;
use App\Repositories\JobSignOffLogRepository;

class JobSignOffLogController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $job;
    protected $module = 'job_sign_off';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        JobSignOffLogRepository $repo,
        ActivityLogRepository $activity,
        JobRepository $job
    ) {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->job = $job;
    }
    
    /**
     * Used to get all Job sign off logs
     * @get ("/api/job/{uuid}/sign-off")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     * })
     * @return Response
     */
    public function index($uuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        return $this->ok($this->repo->paginate($job->id, $this->request->all()));
    }

    /**
     * Used to request Job sign off
     * @post ("/api/job/{uuid}/sign-off")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("status", type="string", required="true", description="Status of Sign Off, can be requested or cancelled"),
     *      @Parameter("sign_off_remarks", type="text", required="true", description="Sign Off Remarks"),
     * })
     * @return Response
     */
    public function store(JobSignOffLogRequest $request, $uuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $job_sign_off_log = $this->repo->request($job, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job_sign_off_log->id,
            'sub_module' => 'job',
            'sub_module_id' => $job->id,
            'activity' => $job->sign_off_status
        ]);

        return $this->success(['message' => trans('job.job_sign_off_request_submitted')]);
    }

    /**
     * Used to update Job sign off
     * @post ("/api/job/{uuid}/sign-off-action")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("status", type="string", required="true", description="Status of Sign Off, can be approved or rejected"),
     *      @Parameter("sign_off_action_remarks", type="text", required="true", description="Sign Off Remarks"),
     * })
     * @return Response
     */
    public function storeAction(JobSignOffLogRequest $request, $uuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->isNotOwner($job);

        $job_sign_off_log = $this->repo->action($job, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job_sign_off_log->id,
            'sub_module' => 'job',
            'sub_module_id' => $job->id,
            'activity' => $job->sign_off_status
        ]);

        return $this->success(['message' => trans('job.job_sign_off_log_updated')]);
    }
}
