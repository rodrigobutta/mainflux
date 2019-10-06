<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Repositories\JobRepository;
use App\Repositories\TodoRepository;
use App\Repositories\UserRepository;
use App\Repositories\ActivityLogRepository;
use App\Repositories\AnnouncementRepository;

class HomeController extends Controller
{
    protected $job;
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
        JobRepository $job,
        UserRepository $user,
        ActivityLogRepository $activity,
        TodoRepository $todo,
        AnnouncementRepository $announcement
    ) {
        $this->job = $job;
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
        $jobs = $this->job->fetchJobs()->get();

        $job_stats = [
            'total' => $jobs->count(),
            'owned' => $this->job->fetchJobs()->whereUserId(\Auth::user()->id)->count(),
            'unassigned' => $this->job->fetchJobs()->doesntHave('user')->count(),
            'pending' => $jobs->where('due_date', '>=', date('Y-m-d'))->where('sign_off_status', '!=', 'approved')->count(),
            'overdue' => $jobs->where('due_date', '<', date('Y-m-d'))->where('sign_off_status', '!=', 'approved')->count(),
            'completed' => $this->job->fetchJobs()->where('sign_off_status', '=', 'approved')->count()
        ];

        $activity_logs = (config('config.activity_log')) ? $this->activity->getAccessibleUserActivityLog() : [];

        $announcements = (config('config.announcement')) ? $this->announcement->getUserAnnouncement() : [];

        return $this->success(compact('job_stats', 'activity_logs', 'announcements'));
    }

    /**
     * Used to get demo message for test mode
     */
    public function demoMessage()
    {
        return view('message')->render();
    }
}
