<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Repositories\ActivityLogRepository;
use App\Http\Requests\TaskSignOffLogRequest;
use App\Repositories\TaskSignOffLogRepository;

class TaskSignOffLogController extends Controller
{
    protected $request;
    protected $repo;
    protected $activity;
    protected $task;
    protected $module = 'task_sign_off';

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        Request $request,
        TaskSignOffLogRepository $repo,
        ActivityLogRepository $activity,
        TaskRepository $task
    ) {
        $this->request = $request;
        $this->repo = $repo;
        $this->activity = $activity;
        $this->task = $task;
    }
    
    /**
     * Used to get all Task sign off logs
     * @get ("/api/task/{uuid}/sign-off")
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
     * Used to request Task sign off
     * @post ("/api/task/{uuid}/sign-off")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("status", type="string", required="true", description="Status of Sign Off, can be requested or cancelled"),
     *      @Parameter("sign_off_remarks", type="text", required="true", description="Sign Off Remarks"),
     * })
     * @return Response
     */
    public function store(TaskSignOffLogRequest $request, $uuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->accessible($task);

        $task_sign_off_log = $this->repo->request($task, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task_sign_off_log->id,
            'sub_module' => 'task',
            'sub_module_id' => $task->id,
            'activity' => $task->sign_off_status
        ]);

        return $this->success(['message' => trans('task.task_sign_off_request_submitted')]);
    }

    /**
     * Used to update Task sign off
     * @post ("/api/task/{uuid}/sign-off-action")
     * @param ({
     *      @Parameter("uuid", type="string", required="true", description="Unique Id of Task"),
     *      @Parameter("status", type="string", required="true", description="Status of Sign Off, can be approved or rejected"),
     *      @Parameter("sign_off_action_remarks", type="text", required="true", description="Sign Off Remarks"),
     * })
     * @return Response
     */
    public function storeAction(TaskSignOffLogRequest $request, $uuid)
    {
        $task = $this->task->findByUuidOrFail($uuid);

        $this->task->isNotOwner($task);

        $task_sign_off_log = $this->repo->action($task, $this->request->all());

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $task_sign_off_log->id,
            'sub_module' => 'task',
            'sub_module_id' => $task->id,
            'activity' => $task->sign_off_status
        ]);

        return $this->success(['message' => trans('task.task_sign_off_log_updated')]);
    }
}
