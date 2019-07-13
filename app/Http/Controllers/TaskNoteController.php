<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Requests\TaskNoteRequest;
use App\Repositories\UploadRepository;
use App\Repositories\TaskNoteRepository;
use App\Repositories\ActivityLogRepository;

class TaskNoteController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $task;
    protected $upload;
    protected $module = 'task_note';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        TaskNoteRepository $repo,
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
     * Used to get all Notes
     * @get ("/api/task/{uuid}/note")
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
     * Used to store Note
     * @post ("/api/task/{uuid}/note")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("title", type="string", required="true", description="Title of Note"),
     *      @Parameter("description", type="text", required="true", description="Description of Note"),
     *      @Parameter("is_public", type="boolean", required="true", description="Visibility of Note, 1 for Shared, 0 for Private"),
     * })
     * @return Response
     */
    public function store(TaskNoteRequest $request, $uuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $task_note = $this->repo->create($task->id, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task_note->id,
            'sub_module' => 'task',
            'sub_module_id' => $task->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('task.task_note_added')]);
    }

    /**
     * Used to get Note detail
     * @get ("/api/task/{uuid}/note/{nuuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("nuuid", type="string", required="true", description="Unique Id of Note"),
     * })
     * @return Response
     */
    public function show($uuid, $nuuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $task_note = $this->repo->findByUuidOrFail($task->id, $nuuid);
        $attachments =  $this->upload->getAttachment($this->module, $task_note->id);

        return $this->success(compact('task_note', 'attachments'));
    }

    /**
     * Used to update Note
     * @patch ("/api/task/{uuid}/note/{nuuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("nuuid", type="string", required="true", description="Unique Id of Note"),
     *      @Parameter("title", type="string", required="true", description="Title of Note"),
     *      @Parameter("description", type="text", required="true", description="Description of Note"),
     * })
     * @return Response
     */
    public function update(TaskNoteRequest $request, $uuid, $nuuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->editable($task, $nuuid);

        $task_note = $this->repo->findByUuidOrFail($task->id, $nuuid);

        $task_note = $this->repo->update($task_note, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task_note->id,
            'sub_module' => 'task',
            'sub_module_id' => $task->id,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('task.task_note_updated')]);
    }

    /**
     * Used to delete Note
     * @delete ("/api/task/{uuid}/note/{nuuid}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("nuuid", type="string", required="true", description="Unique Id of Note"),
     * })
     * @return Response
     */
    public function destroy($uuid, $nuuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $task_note = $this->repo->editable($task->id, $nuuid);

        $this->upload->delete($this->module, $task_note->id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task_note->id,
            'sub_module' => 'task',
            'sub_module_id' => $task->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($task_note);

        return $this->success(['message' => trans('task.task_note_deleted')]);
    }

    /**
     * Used to download Note Attachment
     * @get ("/task/{uuid}/note/{nuuid}/attachment/{attachment_uuid}/download")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("nuuid", type="string", required="true", description="Unique Id of Note"),
     *      @Parameter("attachment_uuid", type="string", required="true", description="Unique Id of Attachment"),
     * })
     * @return Response download
     */
    public function download($uuid, $nuuid, $attachment_uuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $task_note = $this->repo->findByUuidOrFail($task->id, $nuuid);

        $attachment =  $this->upload->getAttachment($this->module, $task_note->id, $attachment_uuid);

        try {
            \Storage::exists($attachment->filename);
        } catch (\Exception $e) {
        }

        $this->activity->record([
            'module'        => 'attachment',
            'module_id'     => $attachment->id,
            'sub_module'    => $this->module,
            'sub_module_id' => $task_note->id,
            'activity'      => 'downloaded'
        ]);

        $download_path = storage_path('app/'.$attachment->filename);
        return response()->download($download_path, $attachment->user_filename);
    }
}
