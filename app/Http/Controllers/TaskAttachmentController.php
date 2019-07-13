<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Repositories\UploadRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\TaskAttachmentRepository;

class TaskAttachmentController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $task;
    protected $upload;
    protected $module = 'task_attachment';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        TaskAttachmentRepository $repo,
        ActivityLogRepository $activity,
        TaskRepository $task,
        UploadRepository $upload
    ) {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->task = $task;
        $this->upload = $upload;
    }

    /**
     * Used to get all Attachments
     * @get ("/api/task/{uuid}/attachment")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     * })
     * @return Response
     */
    public function index($uuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        return $this->ok($this->repo->paginate($task->id, $this->request->all()));
    }

    /**
     * Used to store Attachment
     * @post ("/api/task/{uuid}/attachment")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("title", type="string", required="true", description="Title of Attachment"),
     *      @Parameter("description", type="text", required="true", description="Description of Attachment"),
     * })
     * @return Response
     */
    public function store($uuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $task_attachment = $this->repo->create($task->id, $this->request->all());

        $this->activity->record([
            'module'        => $this->module,
            'module_id'     => $task_attachment->id,
            'sub_module'    => 'task',
            'sub_module_id' => $task->id,
            'activity'      => 'added'
        ]);

        return $this->success(['message' => trans('task.task_attachment_added')]);
    }

    /**
     * Used to get Attachment detail
     * @get ("/api/task/{uuid}/attachment/{attachment_uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response
     */
    public function show($uuid, $auuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $task_attachment = $this->repo->findByUuidOrFail($task->id, $auuid);
        $attachments =  $this->upload->getAttachment($this->module, $task_attachment->id);

        return $this->success(compact('task_attachment', 'attachments'));
    }

    /**
     * Used to update Attachment
     * @patch ("/api/task/{uuid}/attachment/{attachment_uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     *      @Parameter("title", type="string", required="true", description="Title of Attachment"),
     *      @Parameter("description", type="text", required="true", description="Description of Attachment"),
     * })
     * @return Response
     */
    public function update($uuid, $auuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $task_attachment = $this->repo->findByUuidOrFail($task->id, $auuid);

        $task_attachment = $this->repo->update($task_attachment, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task_attachment->id,
            'sub_module' => 'task',
            'sub_module_id' => $task->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('task.task_attachment_updated')]);
    }

    /**
     * Used to delete Attachment
     * @delete ("/api/task/{uuid}/attachment/{attachment_uuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response
     */
    public function destroy($uuid, $auuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $task_attachment = $this->repo->findByUuidOrFail($task->id, $auuid);

        $this->upload->delete($this->module, $task_attachment->id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task_attachment->id,
            'sub_module' => 'task',
            'sub_module_id' => $task->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($task_attachment);

        return $this->success(['message' => trans('task.task_attachment_deleted')]);
    }

    /**
     * Used to download Task Attachment
     * @get ("/task/{uuid}/attachment/{auuid}/attachment/{attachment_uuid}/download")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("auuid", type="string", required="true", description="Unique Id of Attachment"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response download
     */
    public function download($uuid, $auuid, $attachment_uuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $task_attachment = $this->repo->findByUuidOrFail($task->id, $auuid);

        $attachment =  $this->upload->getAttachment($this->module, $task_attachment->id, $attachment_uuid);

        try {
            \Storage::exists($attachment->filename);
        } catch (\Exception $e) {
        }

        $this->activity->record([
            'module'        => 'attachment',
            'module_id'     => $attachment->id,
            'sub_module'    => $this->module,
            'sub_module_id' => $task_attachment->id,
            'activity'      => 'downloaded'
        ]);

        $download_path = storage_path('app/'.$attachment->filename);
        return response()->download($download_path, $attachment->user_filename);
    }
}
