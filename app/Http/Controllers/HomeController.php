<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\TaskRepository;
use App\Repositories\TodoRepository;
use App\Repositories\UserRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\AnnouncementRepository;

class HomeController extends Controller
{
    protected $task;
    protected $user;
    protected $activity;
    protected $todo;
    protected $announcement;

    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct(
        TaskRepository $task,
        UserRepository $user,
        ActivityLogRepository $activity,
        TodoRepository $todo,
        AnnouncementRepository $announcement
    ) {
        $this->task = $task;
        $this->user = $user;
        $this->activity = $activity;
        $this->todo = $todo;
        $this->announcement = $announcement;
    }

    /**
     * Used to test web route
     */
    public function test()
    {
    }

    /**
     * Used to get Dashboard statistics
     */
    public function dashboard()
    {
        $tasks = $this->task->fetchTasks()->get();

        $task_stats = [
            'total' => $tasks->count(),
            'owned' => $this->task->fetchTasks()->whereUserId(\Auth::user()->id)->count(),
            'unassigned' => $this->task->fetchTasks()->doesntHave('user')->count(),
            'pending' => $tasks->where('due_date', '>=', date('Y-m-d'))->where('sign_off_status', '!=', 'approved')->count(),
            'overdue' => $tasks->where('due_date', '<', date('Y-m-d'))->where('sign_off_status', '!=', 'approved')->count(),
            'completed' => $this->task->fetchTasks()->where('sign_off_status', '=', 'approved')->count()
        ];

        $activity_logs = (config('config.activity_log')) ? $this->activity->getAccessibleUserActivityLog() : [];

        $announcements = (config('config.announcement')) ? $this->announcement->getUserAnnouncement() : [];

        return $this->success(compact('task_stats', 'activity_logs', 'announcements'));
    }

    /**
     * Used to get demo message for test mode
     */
    public function demoMessage()
    {
        return view('message')->render();
    }
}
