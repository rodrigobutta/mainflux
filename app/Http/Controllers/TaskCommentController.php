<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Http\Requests\TaskCommentRequest;
use App\Repositories\ActivityLogRepository;
use App\Repositories\TaskCommentRepository;

class TaskCommentController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $task;
    protected $module = 'task_comment';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        TaskCommentRepository $repo,
        ActivityLogRepository $activity,
        TaskRepository $task
    ) {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->task = $task;
    }

    /**
     * Used to get comments of Task
     * @get ("/api/task/{uuid}/comment")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     * })
     * @return Response
     */
    public function index($uuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        return $this->ok($this->repo->getAll($task->id));
    }

    /**
     * Used to store comment in task
     * @post ("/api/task/{uuid}/comment")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("comment", type="string", required="true", description="Comment from User"),
     * })
     * @return Response
     */
    public function store(TaskCommentRequest $request, $uuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $task_comment = $this->repo->create($task->id, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task_comment->id,
            'activity' => 'commented'
        ]);

        return $this->success(['message' => trans('task.comment_posted')]);
    }

    /**
     * Used to delete Task Comment
     * @delete ("/api/task/{uuid}/comment/{id}")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("id", type="integer", required="true", description="Id of Task Comment"),
     * })
     * @return Response
     */
    public function destroy($uuid, $id)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $task_comment = $this->repo->findOrFail($task->id, $id);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($task_comment);

        return $this->success(['message' => trans('task.task_comment_deleted')]);
    }
}
