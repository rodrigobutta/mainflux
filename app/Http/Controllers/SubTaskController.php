<?php

namespace App\Http\Controllers;

use App\SubTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Requests\SubTaskRequest;
use App\Repositories\UploadRepository;
use App\Repositories\SubTaskRepository;
use App\Repositories\ActivityLogRepository;

class SubTaskController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $task;
    protected $upload;
    protected $module = 'sub_task';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        SubTaskRepository $repo,
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
     * Used to get all Sub Tasks
     * @get ("/api/task/{uuid}/sub-task")
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
     * Used to store Sub Task
     * @post ("/api/task/{uuid}/sub-task")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("title", type="string", required="true", description="Title of Sub Task"),
     *      @Parameter("description", type="text", required="true", description="Description of Sub Task"),
     * })
     * @return Response
     */
    public function store(SubTaskRequest $request, $uuid)
    {
        $this->authorize('create', SubTask::class);

        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $this->task->statusLocked($task);

        $sub_task = $this->repo->create($task->id, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $sub_task->id,
            'sub_module' => 'task',
            'sub_module_id' => $task->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('task.sub_task_added')]);
    }

    /**
     * Used to get Sub Task detail
     * @get ("/api/task/{uuid}/sub-task/{suuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("suuid", type="string", required="true", description="Unique Id of Sub Task"),
     * })
     * @return Response
     */
    public function show($uuid, $suuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $sub_task = $this->repo->findByUuidOrFail($task->id, $suuid);
        $attachments =  $this->upload->getAttachment($this->module, $sub_task->id);

        return $this->success(compact('sub_task', 'attachments'));
    }

    /**
     * Used to update Sub Task
     * @patch ("/api/task/{uuid}/sub-task/{suuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("suuid", type="string", required="true", description="Unique Id of Sub Task"),
     *      @Parameter("title", type="string", required="true", description="Title of Sub Task"),
     *      @Parameter("description", type="text", required="true", description="Description of Sub Task"),
     * })
     * @return Response
     */
    public function update(SubTaskRequest $request, $uuid, $suuid)
    {
        $this->authorize('update', SubTask::class);

        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $this->task->statusLocked($task);

        $sub_task = $this->repo->findByUuidOrFail($task->id, $suuid);

        $this->repo->editable($sub_task);

        $sub_task = $this->repo->update($sub_task, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $sub_task->id,
            'sub_module' => 'task',
            'sub_module_id' => $task->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('task.sub_task_updated')]);
    }

    /**
     * Used to toggle Sub Task status
     * @post ("/api/task/{uuid}/sub-task/{suuid}/toggle-status")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("suuid", type="string", required="true", description="Unique Id of Sub Task"),
     * })
     * @return Response
     */
    public function toggleStatus($uuid, $suuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $this->task->statusLocked($task);

        $sub_task = $this->repo->findByUuidOrFail($task->id, $suuid);

        $sub_task->completed_at = ($sub_task->status) ? null : Carbon::now();
        $sub_task->status = !$sub_task->status;
        $sub_task->save();

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $sub_task->id,
            'sub_module' => 'task',
            'sub_module_id' => $task->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('task.sub_task_updated')]);
    }

    /**
     * Used to delete Sub Task
     * @delete ("/api/task/{uuid}/sub-task/{suuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("suuid", type="string", required="true", description="Unique Id of Sub Task"),
     * })
     * @return Response
     */
    public function destroy($uuid, $suuid)
    {
        $this->authorize('delete', SubTask::class);

        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $this->task->statusLocked($task);

        $sub_task = $this->repo->findByUuidOrFail($task->id, $suuid);

        $this->repo->editable($sub_task);

        $this->upload->delete($this->module, $sub_task->id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $sub_task->id,
            'sub_module' => 'task',
            'sub_module_id' => $task->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($sub_task);

        return $this->success(['message' => trans('task.sub_task_deleted')]);
    }

    /**
     * Used to download Sub Task Attachment
     * @get ("/task/{uuid}/sub-task/{suuid}/attachment/{attachment_uuid}/download")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("suuid", type="string", required="true", description="Unique Id of Sub Task"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response download
     */
    public function download($uuid, $suuid, $attachment_uuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $sub_task = $this->repo->findByUuidOrFail($task->id, $suuid);

        $attachment =  $this->upload->getAttachment($this->module, $sub_task->id, $attachment_uuid);

        try {
            \Storage::exists($attachment->filename);
        } catch (\Exception $e) {
        }

        $this->activity->record([
            'module'        => 'attachment',
            'module_id'     => $attachment->id,
            'sub_module'    => $this->module,
            'sub_module_id' => $sub_task->id,
            'activity'      => 'downloaded'
        ]);

        $download_path = storage_path('app/'.$attachment->filename);
        return response()->download($download_path, $attachment->user_filename);
    }
}
