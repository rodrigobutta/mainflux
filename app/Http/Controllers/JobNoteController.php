<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\JobRepository;
use App\Http\Requests\JobNoteRequest;
use App\Repositories\UploadRepository;
use App\Repositories\JobNoteRepository;
use App\Repositories\ActivityLogRepository;

class JobNoteController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $job;
    protected $upload;
    protected $module = 'job_note';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        JobNoteRepository $repo,
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
     * Used to get all Notes
     * @get ("/api/job/{uuid}/note")
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
     * Used to store Note
     * @post ("/api/job/{uuid}/note")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("title", type="string", required="true", description="Title of Note"),
     *      @Parameter("description", type="text", required="true", description="Description of Note"),
     *      @Parameter("is_public", type="boolean", required="true", description="Visibility of Note, 1 for Shared, 0 for Private"),
     * })
     * @return Response
     */
    public function store(JobNoteRequest $request, $uuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $job_note = $this->repo->create($job->id, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job_note->id,
            'sub_module' => 'job',
            'sub_module_id' => $job->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('job.job_note_added')]);
    }

    /**
     * Used to get Note detail
     * @get ("/api/job/{uuid}/note/{nuuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("nuuid", type="string", required="true", description="Unique Id of Note"),
     * })
     * @return Response
     */
    public function show($uuid, $nuuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $job_note = $this->repo->findByUuidOrFail($job->id, $nuuid);
        $attachments =  $this->upload->getAttachment($this->module, $job_note->id);

        return $this->success(compact('job_note', 'attachments'));
    }

    /**
     * Used to update Note
     * @patch ("/api/job/{uuid}/note/{nuuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("nuuid", type="string", required="true", description="Unique Id of Note"),
     *      @Parameter("title", type="string", required="true", description="Title of Note"),
     *      @Parameter("description", type="text", required="true", description="Description of Note"),
     * })
     * @return Response
     */
    public function update(JobNoteRequest $request, $uuid, $nuuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->editable($job, $nuuid);

        $job_note = $this->repo->findByUuidOrFail($job->id, $nuuid);

        $job_note = $this->repo->update($job_note, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job_note->id,
            'sub_module' => 'job',
            'sub_module_id' => $job->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('job.job_note_updated')]);
    }

    /**
     * Used to delete Note
     * @delete ("/api/job/{uuid}/note/{nuuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("nuuid", type="string", required="true", description="Unique Id of Note"),
     * })
     * @return Response
     */
    public function destroy($uuid, $nuuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $job_note = $this->repo->editable($job->id, $nuuid);

        $this->upload->delete($this->module, $job_note->id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $job_note->id,
            'sub_module' => 'job',
            'sub_module_id' => $job->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($job_note);

        return $this->success(['message' => trans('job.job_note_deleted')]);
    }

    /**
     * Used to download Note Attachment
     * @get ("/job/{uuid}/note/{nuuid}/attachment/{attachment_uuid}/download")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Job"),
     *      @Parameter("nuuid", type="string", required="true", description="Unique Id of Note"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response download
     */
    public function download($uuid, $nuuid, $attachment_uuid)
    {
        $job = $this->job->findByUuidOrFail($uuid);

        $this->job->accessible($job);

        $job_note = $this->repo->findByUuidOrFail($job->id, $nuuid);

        $attachment =  $this->upload->getAttachment($this->module, $job_note->id, $attachment_uuid);

        try {
            \Storage::exists($attachment->filename);
        } catch (\Exception $e) {
        }

        $this->activity->record([
            'module'        => 'attachment',
            'module_id'     => $attachment->id,
            'sub_module'    => $this->module,
            'sub_module_id' => $job_note->id,
            'activity'      => 'downloaded'
        ]);

        $download_path = storage_path('app/'.$attachment->filename);
        return response()->download($download_path, $attachment->user_filename);
    }
}
