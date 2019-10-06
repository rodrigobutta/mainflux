<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\JobRepository;
use App\Repositories\UploadRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\JobAttachmentRepository;

class JobAttachmentController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $job;
    protected $upload;
    protected $module = 'job_attachment';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        JobAttachmentRepository $repo,
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
     * Used to get all Attachments
     * @get ("/api/job/{uuid}/attachment")
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
     * Used to store Attachment
     * @post ("/api/job/{uuid}/attachment")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("title", type="string", required="true", description="Title of Attachment"),
     *      @Parameter("description", type="text", required="true", description="Description of Attachment"),
     * })
     * @return Response
     */
    public function store($uuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $job_attachment = $this->repo->create($job->id, $this->request->all());

        $this->activity->record([
            'module'        => $this->module,
            'module_id'     => $job_attachment->id,
            'sub_module'    => 'job',
            'sub_module_id' => $job->id,
            'activity'      => 'added'
        ]);

        return $this->success(['message' => trans('job.job_attachment_added')]);
    }

    /**
     * Used to get Attachment detail
     * @get ("/api/job/{uuid}/attachment/{attachment_uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response
     */
    public function show($uuid, $auuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $job_attachment = $this->repo->findByUuidOrFail($job->id, $auuid);
        $attachments =  $this->upload->getAttachment($this->module, $job_attachment->id);

        return $this->success(compact('job_attachment', 'attachments'));
    }

    /**
     * Used to update Attachment
     * @patch ("/api/job/{uuid}/attachment/{attachment_uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     *      @Parameter("title", type="string", required="true", description="Title of Attachment"),
     *      @Parameter("description", type="text", required="true", description="Description of Attachment"),
     * })
     * @return Response
     */
    public function update($uuid, $auuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $job_attachment = $this->repo->findByUuidOrFail($job->id, $auuid);

        $job_attachment = $this->repo->update($job_attachment, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job_attachment->id,
            'sub_module' => 'job',
            'sub_module_id' => $job->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('job.job_attachment_updated')]);
    }

    /**
     * Used to delete Attachment
     * @delete ("/api/job/{uuid}/attachment/{attachment_uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response
     */
    public function destroy($uuid, $auuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $job_attachment = $this->repo->findByUuidOrFail($job->id, $auuid);

        $this->upload->delete($this->module, $job_attachment->id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job_attachment->id,
            'sub_module' => 'job',
            'sub_module_id' => $job->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($job_attachment);

        return $this->success(['message' => trans('job.job_attachment_deleted')]);
    }

    /**
     * Used to download Job Attachment
     * @get ("/job/{uuid}/attachment/{auuid}/attachment/{attachment_uuid}/download")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("auuid", type="string", required="true", description="Unique Id of Attachment"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response download
     */
    public function download($uuid, $auuid, $attachment_uuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $job_attachment = $this->repo->findByUuidOrFail($job->id, $auuid);

        $attachment =  $this->upload->getAttachment($this->module, $job_attachment->id, $attachment_uuid);

        try {
            \Storage::exists($attachment->filename);
        } catch (\Exception $e) {
        }

        $this->activity->record([
            'module'        => 'attachment',
            'module_id'     => $attachment->id,
            'sub_module'    => $this->module,
            'sub_module_id' => $job_attachment->id,
            'activity'      => 'downloaded'
        ]);

        $download_path = storage_path('app/'.$attachment->filename);
        return response()->download($download_path, $attachment->user_filename);
    }
}
