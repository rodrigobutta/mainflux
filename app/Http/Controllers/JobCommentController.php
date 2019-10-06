<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\JobRepository;
use App\Http\Requests\JobCommentRequest;
use App\Repositories\ActivityLogRepository;
use App\Repositories\JobCommentRepository;

class JobCommentController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $job;
    protected $module = 'job_comment';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        JobCommentRepository $repo,
        ActivityLogRepository $activity,
        JobRepository $job
    ) {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->job = $job;
    }

    /**
     * Used to get comments of Job
     * @get ("/api/job/{uuid}/comment")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     * })
     * @return Response
     */
    public function index($uuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        return $this->ok($this->repo->getAll($job->id));
    }

    /**
     * Used to store comment in job
     * @post ("/api/job/{uuid}/comment")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("comment", type="string", required="true", description="Comment from User"),
     * })
     * @return Response
     */
    public function store(JobCommentRequest $request, $uuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $job_comment = $this->repo->create($job->id, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job_comment->id,
            'activity' => 'commented'
        ]);

        return $this->success(['message' => trans('job.comment_posted')]);
    }

    /**
     * Used to delete Job Comment
     * @delete ("/api/job/{uuid}/comment/{id}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("id", type="integer", required="true", description="Id of Job Comment"),
     * })
     * @return Response
     */
    public function destroy($uuid, $id)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $job_comment = $this->repo->findOrFail($job->id, $id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($job_comment);

        return $this->success(['message' => trans('job.job_comment_deleted')]);
    }
}
