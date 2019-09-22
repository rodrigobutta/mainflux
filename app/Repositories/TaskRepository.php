<?php
namespace App\Repositories;

use App\Task;
use App\Answer;
use App\SubTask;
use App\SubTaskRating;
use App\User;

use Illuminate\Support\Str;
use App\Jobs\SendMailToTaskUser;
use App\Repositories\UserRepository;
use App\Repositories\UploadRepository;
use App\Repositories\QuestionSetRepository;
use App\Repositories\TaskCategoryRepository;
use App\Repositories\TaskPriorityRepository;
use Illuminate\Validation\ValidationException;
use App\Repositories\ClientRepository;
use App\Repositories\ContractorRepository;
use App\Repositories\ProjectRepository;

// use App\Notifications\TaskAssignation;
use App\Events\TaskAssigned;

class TaskRepository
{
    protected $task;
    protected $user;
    protected $upload;
    protected $sub_task_rating;
    protected $sub_task;
    protected $question_set;
    protected $answer;
    protected $task_category;
    protected $task_priority;
    protected $client;
    protected $contractor;
    protected $project;
    protected $module = 'task';

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Task $task,
        UserRepository $user,
        UploadRepository $upload,
        SubTaskRating $sub_task_rating,
        SubTask $sub_task,
        QuestionSetRepository $question_set,
        Answer $answer,
        TaskCategoryRepository $task_category,
        TaskPriorityRepository $task_priority,
        ClientRepository $client,
        ContractorRepository $contractor,
        ProjectRepository $project
    ) {
        $this->task = $task;
        $this->user = $user;
        $this->upload = $upload;
        $this->sub_task_rating = $sub_task_rating;
        $this->sub_task = $sub_task;
        $this->question_set = $question_set;
        $this->answer = $answer;
        $this->task_category = $task_category;
        $this->task_priority = $task_priority;
        $this->client = $client;
        $this->contractor = $contractor;
        $this->project = $project;
    }

    /**
     * Get task query
     *
     * @return Task query
     */
    public function getQuery()
    {
        return $this->task;
    }

    /**
     * Count task
     *
     * @return integer
     */
    public function count()
    {
        return $this->task->count();
    }

    /**
     * List all task by title & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->task->all()->pluck('title', 'id')->all();
    }

    /**
     * List all task by title & id for select option
     *
     * @return array
     */
    public function selectAll()
    {
        return $this->task->all(['title', 'id']);
    }

    /**
     * Get all tasks
     *
     * @return array
     */
    public function getAll()
    {
        return $this->task->all();
    }

    /**
     * Get recurring tasks by date
     *
     * @return array
     */
    public function getRecurringTaskByDate($date = null)
    {
        $date = ($date) ? : date('Y-m-d');
        return $this->task->filterByIsRecurring(1)->filterByNextRecurringDate($date)->get();
    }

    /**
     * Find task with given id or throw an error.
     *
     * @param integer $id
     * @return Task
     */
    public function findOrFail($id)
    {
        $task = $this->task->with('userAdded', 'userAdded.profile', 'user', 'user.profile', 'user.profile.designation', 'user.profile.designation.department', 'taskPriority', 'taskCategory', 'subTask', 'subTask.subTaskRating','answers', 'client', 'contractor', 'project')->find($id);

        if (! $task) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task')]);
        }

        return $task;
    }

    /**
     * Find task with given uuid or throw an error.
     *
     * @param string $uuid
     * @return Task
     */
    public function findByUuidOrFail($uuid)
    {
        $task = $this->task->with('userAdded', 'userAdded.profile', 'user', 'user.profile', 'user.profile.designation', 'user.profile.designation.department', 'taskPriority', 'taskCategory', 'subTask', 'subTask.subTaskRating', 'answers','questionSet','questionSet.questions', 'client', 'contractor', 'project')->whereUuid($uuid)->first();

        if (! $task) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task')]);
        }

        return $task;
    }

    /**
     * Fetch accessible task for authenticated user.
     *
     * @return Task query
     */
    public function fetchTasks()
    {
        $query = $this->task->with('userAdded', 'userAdded.profile', 'user', 'user.profile', 'user.profile.designation', 'user.profile.designation.department', 'taskPriority', 'taskCategory', 'subTask', 'subTask.subTaskRating', 'answers', 'client', 'contractor', 'project');

        // Accessible if logged in user has permission to access all the task
        // Accessible if logged in user has role of admin
        // Accessible if logged in user has permission to access subordinates and his subordinates users are assigned with the task or owner of task
        // Accessible if logged in user is assigned with the task or owner of the task

        if (\Auth::user()->can('access-all-task') || \Auth::user()->hasRole(config('system.default_role.admin'))) {
        } elseif (\Auth::user()->can('access-subordinate-task')) {
            $subordinate_users = $this->user->getAccessibleUserId(\Auth::user()->id, 1);
            $query->where(function ($q) use ($subordinate_users) {
                $q->whereHas('user', function ($q1) use ($subordinate_users) {
                    $q1->whereIn('user_id', $subordinate_users);
                })->orWhereIn('user_id', $subordinate_users);
            });
        } else {
            $query->where(function ($q) {
                $q->whereHas('user', function ($q1) {
                    $q1->where('user_id', \Auth::user()->id);
                })->orWhere('user_id', '=', \Auth::user()->id);
            });
        }

        return  $query;
    }

    /**
     * Paginate all tasks using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by            = isset($params['sort_by']) ? $params['sort_by'] : 'title';
        $order              = isset($params['order']) ? $params['order'] : 'asc';
        $page_length        = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $number             = isset($params['number']) ? $params['number'] : null;
        $title              = isset($params['title']) ? $params['title'] : null;
        $task_category_id   = isset($params['task_category_id']) ? $params['task_category_id'] : [];
        $task_priority_id   = isset($params['task_priority_id']) ? $params['task_priority_id'] : [];
        $client_id   = isset($params['client_id']) ? $params['client_id'] : [];
        $contractor_id   = isset($params['contractor_id']) ? $params['contractor_id'] : [];
        $project_id   = isset($params['project_id']) ? $params['project_id'] : [];
        $user_id            = isset($params['user_id']) ? $params['user_id'] : [];
        $type               = isset($params['type']) ? $params['type'] : null;
        $starred            = isset($params['starred']) ? $params['starred'] : null;
        $is_archived        = isset($params['is_archived']) ? $params['is_archived'] : 'unarchived';
        $start_date_start   = isset($params['start_date_start']) ? $params['start_date_start'] : null;
        $start_date_end     = isset($params['start_date_end']) ? $params['start_date_end'] : null;
        $due_date_start     = isset($params['due_date_start']) ? $params['due_date_start'] : null;
        $due_date_end       = isset($params['due_date_end']) ? $params['due_date_end'] : null;
        $completed_at_start = isset($params['completed_at_start']) ? $params['completed_at_start'] : null;
        $completed_at_end   = isset($params['completed_at_end']) ? $params['completed_at_end'] : null;
        $is_recurring       = isset($params['is_recurring']) ? $params['is_recurring'] : null;
        $status             = isset($params['status']) ? $params['status'] : null;
        $recurring_task_id  = isset($params['recurring_task_id']) ? $params['recurring_task_id'] : null;

        $query = $this->fetchTasks()->filterByStarred($starred)->filterByNumber($number)->filterByTitle($title)->filterByIsArchived($is_archived)->filterByTaskCategoryId($task_category_id)->filterByClientId($client_id)->filterByContractorId($contractor_id)->filterByProjectId($project_id)->filterByTaskPriorityId($task_priority_id)->filterByIsRecurring($is_recurring)->filterByUserId($user_id)->filterByType($type)->filterByStatus($status)->filterByRecurringTaskId($recurring_task_id)->startDateBetween([
            'start_date' => $start_date_start,
            'end_date' => $start_date_end
        ])->dueDateBetween([
            'start_date' => $due_date_start,
            'end_date' => $due_date_end
        ])->completedAtDateBetween([
            'start_date' => $completed_at_start,
            'end_date' => $completed_at_end
        ]);

        if ($sort_by == 'task_category_id') {
            $query->select('tasks.*', \DB::raw('(select name from task_categories where tasks.task_category_id = task_categories.id) as task_category_name'))->orderBy('task_category_name', $order);
        } elseif ($sort_by == 'task_priority_id') {
            $query->select('tasks.*', \DB::raw('(select name from task_priorities where tasks.task_priority_id = task_priorities.id) as task_priority_name'))->orderBy('task_priority_name', $order);
        } else {
            $query->orderBy($sort_by, $order);
        }

        return $query->paginate($page_length);
    }

    /**
     * Copy given task to new task.
     *
     * @param Task $task
     * @param array $params
     * @return Task $new_task
     */
    public function copy(Task $task, $params = array())
    {
        $new_task = $this->copyTask($task, $params);

        $this->copyAssignedUser($task, $new_task, $params);

        $this->copySubTasks($task, $new_task, $params);

        $this->copyAttachments($task, $new_task, $params);

        $this->copyNotes($task, $new_task, $params);

        return $new_task;
    }

    /**
     * Copy task assigned user into new task.
     *
     * @param Task $task
     * @param Task $new_task
     * @param array $params
     * @return void
     */
    private function copyAssignedUser(Task $task, Task $new_task, $params = array())
    {
        $set_assigned_user = (isset($params['assigned_user']) && $params['assigned_user']) ? 1 : 0;

        $new_task->user()->sync($set_assigned_user ? $task->user()->pluck('user_id')->all() : []);
    }

    /**
     * Copy task data into new task.
     *
     * @param Task $task
     * @param array $params
     * @return void
     */
    private function copyTask(Task $task, $params = array())
    {
        $set_task_configuration = (isset($params['task_configuration']) && $params['task_configuration']) ? 1 : 0;
        $set_zero_progress      = (isset($params['zero_progress']) && $params['zero_progress']) ? 1 : 0;

        $new_task                        = $task->replicate();
        $new_task->uuid                  = Str::uuid();
        $new_task->upload_token          = Str::uuid();
        $new_task->progress_type         = $set_task_configuration ? $task->progress_type : config('config.task_progress_type');
        $new_task->rating_type           = $set_task_configuration ? $task->rating_type : config('config.task_rating_type');
        $new_task->completed_at          = null;
        $new_task->sign_off_status       = null;
        $new_task->is_archived           = 0;
        $new_task->is_cancelled          = 0;
        $new_task->progress              = $set_zero_progress ? 0 : $task->progress;
        $new_task->user_id               = $task->user_id;
        $new_task->is_recurring          = 0;
        $new_task->recurrence_start_date = null;
        $new_task->recurrence_end_date   = null;
        $new_task->next_recurring_date   = null;
        $new_task->recurring_frequency   = 0;
        $new_task->recurring_task_id     = null;
        $new_task->save();

        $this->upload->copy('task', $task->id, $new_task->upload_token, $new_task->id);

        return $new_task;
    }

    /**
     * Copy sub task into new sub task.
     *
     * @param Task $task
     * @param Task $new_task
     * @param array $params
     * @return void
     */
    private function copySubTasks(Task $task, Task $new_task, $params = array())
    {
        $set_sub_task = (isset($params['sub_task']) && $params['sub_task']) ? 1 : 0;

        if (! $set_sub_task) {
            return;
        }

        foreach ($task->SubTask as $sub_task) {
            $new_sub_task = $sub_task->replicate();
            $new_sub_task->uuid = Str::uuid();
            $new_sub_task->task_id = $new_task->id;
            $new_sub_task->status = 0;
            $new_sub_task->completed_at = null;
            $new_sub_task->user_id = $sub_task->user_id;
            $new_sub_task->upload_token = Str::uuid();
            $new_sub_task->save();

            $this->upload->copy('sub_task', $sub_task->id, $new_sub_task->upload_token, $new_sub_task->id);
        }
    }

    /**
     * Copy task attachments into new task.
     *
     * @param Task $task
     * @param Task $new_task
     * @param array $params
     * @return void
     */
    private function copyAttachments(Task $task, Task $new_task, $params = array())
    {
        $set_attachments = (isset($params['attachments']) && $params['attachments']) ? 1 : 0;

        if (! $set_attachments) {
            return;
        }

        foreach ($task->TaskAttachment as $task_attachment) {
            $new_task_attachment = $task_attachment->replicate();
            $new_task_attachment->uuid = Str::uuid();
            $new_task_attachment->task_id = $new_task->id;
            $new_task_attachment->user_id = $task_attachment->user_id;
            $new_task_attachment->upload_token = Str::uuid();
            $new_task_attachment->save();

            $this->upload->copy('task_attachment', $task_attachment->id, $new_task_attachment->upload_token, $new_task_attachment->id);
        }
    }

    /**
     * Copy task notes into new task.
     *
     * @param Task $task
     * @param Task $new_task
     * @param array $params
     * @return void
     */
    private function copyNotes(Task $task, Task $new_task, $params = array())
    {
        $set_notes = (isset($params['notes']) && $params['notes']) ? 1 : 0;

        if (! $set_notes) {
            return;
        }

        foreach ($task->TaskNote as $task_note) {
            $new_task_note = $task_note->replicate();
            $new_task_note->uuid = Str::uuid();
            $new_task_note->task_id = $new_task->id;
            $new_task_note->user_id = $task_note->user_id;
            $new_task_note->upload_token = Str::uuid();
            $new_task_note->save();

            $this->upload->copy('task_note', $task_note->id, $new_task_note->upload_token, $new_task_note->id);
        }
    }

    /**
     * Create a new task.
     *
     * @param array $params
     * @return Task
     */
    public function create($params)
    {
        $this->validateInputId($params);

        $task = $this->task->forceCreate($this->formatParams($params));

        $task = $this->assignUser($task, $params);

        $this->notify($task, $params);

        $this->processUpload($task, $params);

        return $task;
    }

    /**
     * Validate input ids.
     *
     * @param array $params
     * @return null
     */

    public function validateInputId($params)
    {
        $task_category_ids = $this->task_category->listId();
        $task_priority_ids = $this->task_priority->listId();
        $question_set_ids  = $this->question_set->listId();
        $client_ids = $this->client->listId();
        $contractor_ids  = $this->contractor->listId();
        $project_ids  = $this->project->listId();

        $task_category_id = isset($params['task_category_id']) ? $params['task_category_id'] : null;
        $task_priority_id = isset($params['task_priority_id']) ? $params['task_priority_id'] : null;
        $question_set_id  = isset($params['question_set_id']) ? $params['question_set_id'] : [];
        $client_id = isset($params['client_id']) ? $params['client_id'] : null;
        $contractor_id = isset($params['contractor_id']) ? $params['contractor_id'] : null;
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;

        if (! in_array($task_category_id, $task_category_ids)) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task_category')]);
        }

        if (! in_array($task_priority_id, $task_priority_ids)) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_task_priority')]);
        }

        if ($question_set_id && ! in_array($question_set_id, $question_set_ids)) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_question_set')]);
        }

        if ($client_id && ! in_array($client_id, $client_ids)) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_client')]);
        }

        if ($contractor_id && ! in_array($contractor_id, $contractor_ids)) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_contractor')]);
        }

        if ($project_id && ! in_array($project_id, $project_ids)) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find_project')]);
        }
    }

    /**
     * Notify assigned users after creating task.
     *
     * @param Task $task
     * @param array $params
     * @return void
     */
    private function notify(Task $task, $params = array())
    {
        $send_task_assign_email = isset($params['send_task_assign_email']) ? $params['send_task_assign_email'] : null;

        if (! $send_task_assign_email || ! $task->User->count()) {
            return;
        }

        SendMailToTaskUser::dispatch($task, 'task-assign-email');
    }

    /**
     * Fix task attachments
     *
     * @param Task $task
     * @param array $params
     * @param string $action
     * @return void
     */
    public function processUpload(Task $task, $params = array(), $action = 'create')
    {
        $upload_token = isset($params['upload_token']) ? $params['upload_token'] : null;

        if ($action === 'create') {
            $this->upload->store($this->module, $task->id, $upload_token);
        } else {
            $this->upload->update($this->module, $task->id, $upload_token);
        }
    }

    /**
     * Assign users into given task.
     *
     * @param Task $task
     * @param array $params
     * @return Task
     */
    private function assignUser(Task $task, $params)
    {
        $users = isset($params['user_id']) ? $params['user_id'] : [];


        // TODO SOLO ASIGNACIONES NUEVAS EN EL UPDATE


        // $user = \Auth::user();        
        // $user->notify(new TaskAssigned($task));


        $users = User::find($users);   //\Auth::user();        
        foreach ($users as $user) {

            event(new TaskAssigned($user,$task));
            // $user->notify(new TaskAssignation($task));    
        }
        




        $task->user()->sync($users);

        return $task;
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $action = 'create')
    {


        $formatted = [
            'task_category_id' => isset($params['task_category_id']) ? $params['task_category_id'] : null,
            'task_priority_id' => isset($params['task_priority_id']) ? $params['task_priority_id'] : null,
            'question_set_id'  => (isset($params['question_set_id']) && $params['question_set_id']) ? $params['question_set_id'] : null,
            'title'            => isset($params['title']) ? $params['title'] : null,
            'start_date'       => isset($params['start_date']) ? toDate($params['start_date']) : null,
            'due_date'         => isset($params['due_date']) ? toDate($params['due_date']) : null,
            'description'      => isset($params['description']) ? $params['description'] : null,
            'client_id' => isset($params['client_id']) ? $params['client_id'] : null,
            'contractor_id' => isset($params['contractor_id']) ? $params['contractor_id'] : null,
            'project_id' => isset($params['project_id']) && $params['project_id'] != '' ? $params['project_id'] : null
        ];


        // dd($formatted);

        if ($action === 'create') {
            $formatted['user_id']       = \Auth::user()->id;
            $formatted['uuid']          = Str::uuid();
            $formatted['upload_token']  = isset($params['upload_token']) ? $params['upload_token'] : null;
            $formatted['progress_type'] = config('config.task_progress_type');
            $formatted['rating_type']   = config('config.task_rating_type');
        }

        return $formatted;
    }

    /**
     * Update given task.
     *
     * @param Task $task
     * @param array $params
     *
     * @return Task
     */
    public function update(Task $task, $params)
    {
        $this->validateInputId($params);
        
        $question_set_id = isset($params['question_set_id']) ? $params['question_set_id'] : null;
        if ($task->question_set_id != $question_set_id) {
            $task->answers()->delete();
        }
        
        $task->forceFill($this->formatParams($params, 'update'))->save();

        $task = $this->assignUser($task, $params);

        $this->processUpload($task, $params, 'update');

        return $task;
    }

    /**
     * Update given task progress.
     *
     * @param Task $task
     * @param array $params
     * @return Task
     */
    public function updateProgress(Task $task, $params)
    {
        $progress = isset($params['progress']) ? $params['progress'] : 0;

        if ($task->progress_type != 'manual') {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $task->progress = request('progress');
        $task->save();

        return  $task;
    }

    /**
     * Update given task recurrence.
     *
     * @param Task $task
     * @param array $params
     * @return void
     */
    public function updateRecurrence(Task $task, $params)
    {
        $recurrence_start_date = isset($params['recurrence_start_date']) ? $params['recurrence_start_date'] : null;
        $recurrence_end_date   = isset($params['recurrence_end_date']) ? $params['recurrence_end_date'] : null;
        $is_recurring          = (isset($params['is_recurring']) && $params['is_recurring']) ? 1 : 0;
        $recurring_frequency   = isset($params['recurring_frequency']) ? $params['recurring_frequency'] : null;

        if ($recurrence_start_date < $task->start_date || $recurrence_end_date < $task->start_date) {
            throw ValidationException::withMessages(['message' => trans('task.recurrence_date_cannot_less_than_task_date')]);
        }

        $task->is_recurring          = $is_recurring;
        $task->recurrence_start_date = $is_recurring ? $recurrence_start_date : null;
        $task->recurrence_end_date   = $is_recurring ? $recurrence_end_date : null;
        $task->recurring_frequency   = $is_recurring ? $recurring_frequency : 0;
        $task->next_recurring_date   = $is_recurring ? date('Y-m-d', strtotime($recurrence_start_date. ' + '.$recurring_frequency.' days')) : null;
        $task->save();

        return $task;
    }

    /**
     * Get all recurring frequencies.
     *
     * @return array
     */
    public function listRecurringFrequency()
    {
        return [
            ['id' => '7', 'name' => trans('task.weekly')],
            ['id' => '15', 'name' => trans('task.fortnightly')],
            ['id' => '30', 'name' => trans('task.monthly')],
            ['id' => '60', 'name' => trans('task.bi_monthly')],
            ['id' => '90', 'name' => trans('task.quarterly')],
            ['id' => '180', 'name' => trans('task.bi_annually')],
            ['id' => '365', 'name' => trans('task.annually')]
        ];
    }

    /**
     * Update task next recurring date
     *
     * @param Task $task
     * @return Task
     */
    public function updateNextRecurringDate($task)
    {
        $next_recurring_date = date('Y-m-d', strtotime($task->next_recurring_date. ' + '.$task->recurring_frequency.' days'));
        $task->next_recurring_date = ($task->recurrence_end_date > $next_recurring_date) ? $next_recurring_date : null;
        $task->save();

        return $task;
    }

    /**
     * Determine authenticated user can access given task or not.
     *
     * @param Task $task
     * @return void
     */
    public function accessible(Task $task)
    {
        $subordinate_users = $this->user->getAccessibleUserId(\Auth::user()->id, 1);

        $task_assigned_users = $task->user()->pluck('user_id')->all();

        /**
         * Admin Users, Users with permission to access all the task & User who created the task can access the task
         * Users with permission access subordinate task can access the task if task is created by his subordinates or task is assigned to its subordinates
         * Task is accessible when user is assigned task
         */
        if (
            \Auth::user()->hasRole(config('system.default_role.admin')) ||
            \Auth::user()->can('access-all-task') ||
            $task->user_id == \Auth::user()->id ||
            in_array(\Auth::user()->id, $task_assigned_users) ||
            (\Auth::user()->can('access-subordinate-task') && (in_array($task->user_id, $subordinate_users) || count(array_intersect($subordinate_users, $task_assigned_users))))
        ) {
            return;
        }

        throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
    }

    /**
     * Determine authenticated user can edit given task or not.
     *
     * @param Task $task
     * @param boolean $should_return
     * @return boolean
     */
    public function editable(Task $task, $should_return = 0)
    {
        $subordinate_users = $this->user->getAccessibleUserId(\Auth::user()->id, 1);

        // Admin Users, Users with permission to access all the task & User who created the task can edit the task
        // Users with permission access subordinate task can edit the task if task is created by his subordinates
        if (
            \Auth::user()->hasRole(config('system.default_role.admin')) ||
            \Auth::user()->can('access-all-task') ||
            $task->user_id == \Auth::user()->id ||
            (\Auth::user()->can('access-subordinate-task') && in_array($task->user_id, $subordinate_users))
        ) {
            return 1;
        }

        if ($should_return) {
            return 0;
        }

        throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
    }

    /**
     * Verify if task status is locked i.e. task status is either requested or approved.
     *
     * @param Task $task
     * @return void
     */
    public function statusLocked(Task $task)
    {
        if (in_array($task->sign_off_status, ['requested','approved'])) {
            throw ValidationException::withMessages(['message' => trans('task.no_task_action_on_sign_off_status', ['status' => trans('task.'.$task->sign_off_status),'action' => trans('task.update')])]);
        }
    }

    /**
     * Verify if task status is not approved.
     *
     * @param Task $task
     * @return void
     */
    public function isNotApproved(Task $task)
    {
        if ($task->sign_off_status != 'approved') {
            throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
        }
    }

    /**
     * Verify if authenticated user is not owner of the task.
     *
     * @param Task $task
     * @return void
     */
    public function isNotOwner(Task $task)
    {
        if ($task->user_id != \Auth::user()->id) {
            throw ValidationException::withMessages(['message' => trans('general.permission_denied')]);
        }
    }

    public function configuration(Task $task, $params)
    {
        $progress_type = isset($params['progress_type']) ? $params['progress_type'] : null;
        $rating_type = isset($params['rating_type']) ? $params['rating_type'] : null;

        if ($progress_type === 'question' && ! $task->question_set_id) {
            throw ValidationException::withMessages(['progress_type' => trans('task.question_set_required_for_question_progress_type')]);
        }

        $task->progress_type = request('progress_type');
        $task->rating_type = request('rating_type');
        $task->save();

        return $task;
    }

    /**
     * Rate given task as given inputs if rating type is task based rating.
     *
     * @param Task $task
     * @param array $params
     * @return void
     */
    public function rating(Task $task, $params = array())
    {
        if ($task->rating_type != 'task_based') {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $rows = isset($params['row']) ? $params['row'] : [];

        foreach ($rows as $row) {
            if (!$row['rating']) {
                throw ValidationException::withMessages(['message' => trans('validation.required', ['attribute' => trans('task.rating')])]);
            }
        }

        $this->deleteSubTaskRating($task);

        foreach ($rows as $row) {
            $user_id = $row['user_id'];
            $rating  = $row['rating'];
            $remarks = $row['remarks'];

            $task->user()->sync([$user_id => [
                'rating' => $row['rating'],
                'remarks' => $row['remarks']
            ]], false);
        }
    }

    /**
     * Rate given task as given inputs if rating type is sub task based rating.
     *
     * @param Task $task
     * @param array $params
     * @return void
     */
    public function subTaskRating(Task $task, $params = array())
    {
        if ($task->rating_type != 'sub_task_based') {
            throw ValidationException::withMessages(['message' => trans('general.invalid_action')]);
        }

        $rows = isset($params['row']) ? $params['row'] : [];
        $user_id = isset($params['user_id']) ? $params['user_id'] : null;

        foreach ($rows as $row) {
            if (!$row['rating']) {
                throw ValidationException::withMessages(['message' => trans('validation.required', ['attribute' => trans('task.rating')])]);
            }
        }

        $this->deleteTaskRating($task);

        foreach ($rows as $row) {
            $sub_task_rating = $this->sub_task_rating->firstOrNew(['sub_task_id' => $row['sub_task_id'],'user_id' => $user_id]);
            $sub_task_rating->sub_task_id = $row['sub_task_id'];
            $sub_task_rating->user_id = $user_id;
            $sub_task_rating->rating = $row['rating'];
            $sub_task_rating->remarks = $row['remarks'];
            $sub_task_rating->save();
        }
    }

    /**
     * Used to delete rating if rating type is task based
     * @param ({
     *      @Parameter("task", type="object", required="true", description="Task  object")
     * })
     * @return nothing
     */

    public function deleteTaskRating($task)
    {
        foreach ($task->User as $user) {
            $task->user()->sync([$user->id => [
                'rating' => null,
                'remarks' => null
            ]], false);
        }
    }

    /**
     * Used to delete rating if rating type is sub task based
     * @param ({
     *      @Parameter("task", type="object", required="true", description="Task  object")
     * })
     * @return nothing
     */

    public function deleteSubTaskRating($task)
    {
        $this->sub_task_rating->whereIn('sub_task_id', $task->SubTask->pluck('id')->all())->delete();
    }

    /**
     * Find task & check it can be deleted or not.
     *
     * @param integer $id
     * @return Task
     */
    public function deletable($id)
    {
        $task = $this->findOrFail($id);

        if ($task->tasks()->count()) {
            throw ValidationException::withMessages(['message' => trans('task.task_has_many_tasks')]);
        }
        
        return $task;
    }

    /**
     * Delete task.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Task $task)
    {
        return $task->delete();
    }

    /**
     * Delete multiple task.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->task->whereIn('id', $ids)->delete();
    }

    /**
     * Get given task progress.
     *
     * @param Task $task
     * @return integer
     */
    public function getTaskProgress(Task $task)
    {
        if ($task->progress_type == 'manual') {
            return $task->progress;
        } elseif ($task->progress_type == 'question') {
            return ($task->answers()->count()) ? 100 : 0;
        } else {
            $sub_tasks = $this->sub_task->filterByTaskId($task->id)->get();
            return ($sub_tasks->count()) ? (($sub_tasks->where('status', 1)->count()/$sub_tasks->count())*100) : 0;
        }
    }

    /**
     * Get task color based upon the status.
     *
     * @param Task $task
     * @return string
     */
    public function getTaskColor(Task $task)
    {
        if ($task->status == 'unassigned') {
            return '#727B84';
        } elseif ($task->status == 'pending') {
            return '#009EFB';
        } elseif ($task->status == 'overdue') {
            return '#F2F7F8';
        } elseif ($task->status == 'late') {
            return '#FFD071';
        } elseif ($task->status == 'completed') {
            return '#88DD92';
        } else {
            return;
        }
    }

    /**
     * Get data for top rating chart.
     *
     * @return array
     */
    public function ratingChart()
    {
        $users = $this->user->getAll();
        $all_sub_task_ratings = $this->sub_task_rating->all();

        foreach ($users as $user) {
            $rating = $this->getUserRating($user, $all_sub_task_ratings);
            $average_rating = ($rating['task_count']) ? $rating['rating']/$rating['task_count'] : 0;
            $chart[] = [
                    'rating' => $average_rating,
                    'task_count' => $rating['task_count'],
                    'id' => $user->id,
                    'name' => $user->name_with_designation_and_department,
                    'user' => $user
                ];
        }

        usort($chart, function ($a, $b) {
            if ($a['rating']==$b['rating']) {
                return 0;
            }
            return $a['rating'] < $b['rating']?1:-1;
        });

        $i = 0;
        $j = 1;
        $top_charts = array();
        foreach ($chart as $key => $value) {
            $i++;
            if ($i <= 5 && $value['rating']) {
                $value['rank'] = $j;
                $top_charts[] = $value;
                $j++;
            }
        }
        return $top_charts;
    }

    /**
     * Get data for graph.
     *
     * @return array
     */
    public function graph()
    {
        $tasks = $this->fetchTasks()->get();

        $graph_data['task_category'] = $this->categoryWiseGraph($tasks);

        $graph_data['task_priority'] = $this->priorityWiseGraph($tasks);

        $graph_data['task_status'] = $this->statusWiseGraph($tasks);

        return $graph_data;
    }

    /**
     * Get data for graph based on task category.
     *
     * @param Task $tasks
     * @return array
     */
    private function categoryWiseGraph($tasks)
    {
        $categories = array();
        $task_categories = array();
        foreach ($tasks as $task) {
            array_push($categories, $task->TaskCategory->name);
        }

        foreach (array_count_values($categories) as $key => $value) {
            $task_categories[] = array('name' => $key,'total' => $value);
        }

        return generateDoughnutGraphData($task_categories, trans('task.task_category'));
    }

    /**
     * Get data for graph based on task priority.
     *
     * @param Task $tasks
     * @return array
     */
    private function priorityWiseGraph($tasks)
    {
        $priorities = array();
        $task_priorities = array();
        foreach ($tasks as $task) {
            array_push($priorities, $task->TaskPriority->name);
        }

        foreach (array_count_values($priorities) as $key => $value) {
            $task_priorities[] = array('name' => $key,'total' => $value);
        }

        return generateDoughnutGraphData($task_priorities, trans('task.task_priority'));
    }

    /**
     * Get color of task based on task status.
     *
     * @param string $status
     * @return array
     */
    public function statusWiseColor($status)
    {
        if ($status == 'unassigned') {
            return '#727B84';
        } elseif ($status == 'pending') {
            return '#009EFB';
        } elseif ($status == 'overdue') {
            return '#F2F7F8';
        } elseif ($status == 'late') {
            return '#FFD071';
        } elseif ($status == 'completed') {
            return '#88DD92';
        } else {
            return;
        }
    }

    /**
     * Get data for graph based on task status.
     *
     * @param Task $tasks
     * @return array
     */
    private function statusWiseGraph($tasks)
    {
        $status = array();
        $task_status = array();
        foreach ($tasks as $task) {
            if (!$task->User->count()) {
                array_push($status, 'unassigned');
            } elseif ($task->sign_off_status != 'approved' && $task->due_date > date('Y-m-d')) {
                array_push($status, 'pending');
            } elseif ($task->sign_off_status != 'approved' && $task->due_date < date('Y-m-d')) {
                array_push($status, 'overdue');
            } elseif ($task->sign_off_status == 'approved' && $task->completed_at < getEndOfDate($task->due_date)) {
                array_push($status, 'completed');
            } elseif ($task->sign_off_status == 'approved' && $task->completed_at > getEndOfDate($task->due_date)) {
                array_push($status, 'late');
            }
        }

        foreach (array_count_values($status) as $key => $value) {
            $task_status[] = array('name' => trans('task.'.$key),'total' => $value,'color' => $this->statusWiseColor($key));
        }

        return generateDoughnutGraphData($task_status, trans('task.status'));
    }

    /**
     * Get data for task summary.
     *
     * @return array
     */
    public function summary()
    {
        $users = $this->user->getAccessibleUser(\Auth::user()->id, 1)->get();

        $tasks = $this->getAll();
        $all_sub_task_ratings = $this->sub_task_rating->all();
        foreach ($users as $user) {
            $owned_tasks = $tasks->where('user_id', $user->id)->count();

            $assigned_tasks = $user->Task->count();

            $pending_tasks = $user->Task->filter(function ($item) {
                return (data_get($item, 'sign_off_status') != 'approved');
            })->filter(function ($item) {
                return (data_get($item, 'due_date') > date('Y-m-d'));
            })->count();

            $overdue_tasks = $user->Task->filter(function ($item) {
                return (data_get($item, 'sign_off_status') != 'approved');
            })->filter(function ($item) {
                return (data_get($item, 'due_date') < date('Y-m-d'));
            })->count();

            $completed_tasks = $user->Task->where('sign_off_status', 'approved')->filter(function ($item) {
                return (data_get($item, 'completed_at') <= getEndOfDate(data_get($item, 'due_date')));
            })->count();

            $rating = $this->getUserRating($user, $all_sub_task_ratings);
            $average_rating = ($rating['task_count']) ? $rating['rating']/$rating['task_count'] : 0;

            $data[] = array(
                    'user' => $user,
                    'owned_tasks' => $owned_tasks,
                    'assigned_tasks' => $assigned_tasks,
                    'pending_tasks' => $pending_tasks,
                    'overdue_tasks' => $overdue_tasks,
                    'completed_tasks' => $completed_tasks,
                    'rating' => $average_rating
                );
        }
        return $data;
    }

    /**
     * Get user rating for given user.
     *
     * @param User $user
     * @param array $all_sub_task_ratings
     * @return array
     */
    public function getUserRating($user, $all_sub_task_ratings)
    {
        $rating = 0;
        $task_count = 0;
        foreach ($user->Task as $task) {
            if ($task->sign_off_status == 'approved') {
                if ($task->rating_type == 'task_based' && $task->pivot->rating) {
                    $rating += $task->pivot->rating;
                    $task_count++;
                } elseif ($task->rating_type == 'sub_task_based') {
                    $sub_task_ratings = $all_sub_task_ratings->where('user_id', $user->id)->whereIn('sub_task_id', $task->SubTask->pluck('id')->all())->all();
                    if (count($sub_task_ratings)) {
                        $sub_task_rating_value = 0;
                        foreach ($sub_task_ratings as $sub_task_rating) {
                            $sub_task_rating_value += $sub_task_rating->rating;
                        }
                        $rating += $sub_task_rating_value/count($sub_task_ratings);
                        $task_count++;
                    }
                }
            }
        }

        return array('rating' => $rating,'task_count' => $task_count);
    }

    /**
     * Store user's answer.
     *
     * @param Task $task
     * @param array $params
     * @return Task
     */
    public function answer(Task $task, $params)
    {
        $answers = isset($params['answers']) ? $params['answers'] : [];
        $question_set_id = isset($params['question_set_id']) ? $params['question_set_id'] : null;

        $question_set = $this->question_set->findOrFail($question_set_id);

        $all_question_id = array();
        foreach ($answers as $answer) {
            $all_question_id[] = $answer['id'];

            if (! $answer['answer']) {
                throw ValidationException::withMessages(['answer_'.$answer['id'] => trans('validation.required', ['attribute' => trans('task.answer')])]);
            }
        }

        if (array_diff($all_question_id, $question_set->questions()->pluck('id')->all())) {
            throw ValidationException::withMessages(['message' => trans('task.invalid_questions')]);
        }

        foreach ($answers as $answer) {
            $question_id = $answer['id'];
            $ans = ($answer['answer'] === 'yes') ? 1 : 0;
            $ans_description = $answer['description'];
            $task_answer = $this->answer->firstOrCreate([
                'task_id' => $task->id,
                'question_id' => $question_id
            ]);

            $task_answer->answer = $ans;
            $task_answer->description = $ans_description;
            $task_answer->save();
        }

        return $task;
    }
}
