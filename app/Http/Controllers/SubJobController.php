<?php

namespace App\Http\Controllers;

use App\SubJob;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\JobRepository;
use App\Http\Requests\SubJobRequest;
use App\Repositories\UploadRepository;
use App\Repositories\SubJobRepository;
use App\Repositories\ActivityLogRepository;

class SubJobController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $job;
    protected $upload;
    protected $module = 'sub_job';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        SubJobRepository $repo,
        ActivityLogRepository $activity,
        JobRepository $job,
        UploadRepository $upload
    ) {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->job = $job;
        $this->upload = $upload;
    }

    /**
     * Used to get all Sub Jobs
     * @get ("/api/job/{uuid}/sub-job")
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
     * Used to store Sub Job
     * @post ("/api/job/{uuid}/sub-job")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("title", type="string", required="true", description="Title of Sub Job"),
     *      @Parameter("description", type="text", required="true", description="Description of Sub Job"),
     * })
     * @return Response
     */
    public function store(SubJobRequest $request, $uuid)
    {
        $this->authorize('create', SubJob::class);

        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $this->job->statusLocked($job);

        $sub_job = $this->repo->create($job->id, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $sub_job->id,
            'sub_module' => 'job',
            'sub_module_id' => $job->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('job.sub_job_added')]);
    }

    /**
     * Used to get Sub Job detail
     * @get ("/api/job/{uuid}/sub-job/{suuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("suuid", type="string", required="true", description="Unique Id of Sub Job"),
     * })
     * @return Response
     */
    public function show($uuid, $suuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $sub_job = $this->repo->findByUuidOrFail($job->id, $suuid);
        $attachments =  $this->upload->getAttachment($this->module, $sub_job->id);

        return $this->success(compact('sub_job', 'attachments'));
    }

    /**
     * Used to update Sub Job
     * @patch ("/api/job/{uuid}/sub-job/{suuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("suuid", type="string", required="true", description="Unique Id of Sub Job"),
     *      @Parameter("title", type="string", required="true", description="Title of Sub Job"),
     *      @Parameter("description", type="text", required="true", description="Description of Sub Job"),
     * })
     * @return Response
     */
    public function update(SubJobRequest $request, $uuid, $suuid)
    {
        $this->authorize('update', SubJob::class);

        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $this->job->statusLocked($job);

        $sub_job = $this->repo->findByUuidOrFail($job->id, $suuid);

        $this->repo->editable($sub_job);

        $sub_job = $this->repo->update($sub_job, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $sub_job->id,
            'sub_module' => 'job',
            'sub_module_id' => $job->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('job.sub_job_updated')]);
    }

    /**
     * Used to toggle Sub Job status
     * @post ("/api/job/{uuid}/sub-job/{suuid}/toggle-status")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("suuid", type="string", required="true", description="Unique Id of Sub Job"),
     * })
     * @return Response
     */
    public function toggleStatus($uuid, $suuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $this->job->statusLocked($job);

        $sub_job = $this->repo->findByUuidOrFail($job->id, $suuid);

        $sub_job->completed_at = ($sub_job->status) ? null : Carbon::now();
        $sub_job->status = !$sub_job->status;
        $sub_job->save();

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $sub_job->id,
            'sub_module' => 'job',
            'sub_module_id' => $job->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('job.sub_job_updated')]);
    }

    /**
     * Used to delete Sub Job
     * @delete ("/api/job/{uuid}/sub-job/{suuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("suuid", type="string", required="true", description="Unique Id of Sub Job"),
     * })
     * @return Response
     */
    public function destroy($uuid, $suuid)
    {
        $this->authorize('delete', SubJob::class);

        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $this->job->statusLocked($job);

        $sub_job = $this->repo->findByUuidOrFail($job->id, $suuid);

        $this->repo->editable($sub_job);

        $this->upload->delete($this->module, $sub_job->id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $sub_job->id,
            'sub_module' => 'job',
            'sub_module_id' => $job->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($sub_job);

        return $this->success(['message' => trans('job.sub_job_deleted')]);
    }

    /**
     * Used to download Sub Job Attachment
     * @get ("/job/{uuid}/sub-job/{suuid}/attachment/{attachment_uuid}/download")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("suuid", type="string", required="true", description="Unique Id of Sub Job"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response download
     */
    public function download($uuid, $suuid, $attachment_uuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $sub_job = $this->repo->findByUuidOrFail($job->id, $suuid);

        $attachment =  $this->upload->getAttachment($this->module, $sub_job->id, $attachment_uuid);

        try {
            \Storage::exists($attachment->filename);
        } catch (\Exception $e) {
        }

        $this->activity->record([
            'module'        => 'attachment',
            'module_id'     => $attachment->id,
            'sub_module'    => $this->module,
            'sub_module_id' => $sub_job->id,
            'activity'      => 'downloaded'
        ]);

        $download_path = storage_path('app/'.$attachment->filename);
        return response()->download($download_path, $attachment->user_filename);
    }
}
