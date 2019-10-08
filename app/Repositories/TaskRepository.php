<?php
namespace App\Repositories;

use App\Task;
use App\Repositories\ProjectRepository;
use App\Repositories\TaskRelevanceRepository;
use App\Repositories\TaskFrequencyRepository;
use App\Repositories\TaskComplexityRepository;
use App\Repositories\TaskFamilyRepository;
use Illuminate\Validation\ValidationException;

class TaskRepository
{
    protected $task;
    protected $project;
    protected $task_relevance;
    protected $task_frequency;
    protected $task_complexity;
    protected $task_family;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Task $task,
        ProjectRepository $project,
        TaskRelevanceRepository $task_relevance,
        TaskFrequencyRepository $task_frequency,
        TaskComplexityRepository $task_complexity,
        TaskFamilyRepository $task_family
    ) {
        $this->task = $task;
        $this->project = $project;
        $this->task_relevance = $task_relevance;
        $this->task_frequency = $task_frequency;
        $this->task_complexity = $task_complexity;
        $this->task_family = $task_family;
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
     * List all tasks by task with project name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->task->all()->pluck('task_with_project', 'id')->all();
    }

    /**
     * List all tasks by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->task->all()->pluck('id')->all();
    }

    /**
     * List all tasks by project+task name & id
     *
     * @return array
     */
    public function listAllFilterById($task_ids)
    {
        return $this->task->whereIn('id', $task_ids)->get()->pluck('task_with_project', 'id')->all();
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
     * Get hidden task
     *
     * @return array
     */
    public function getHidden()
    {
        return $this->task->filterByIsHidden(1)->first();
    }


    /**
     * Find task with given id or throw an error.
     *
     * @param integer $id
     * @return Task
     */
    public function findOrFail($id)
    {
        $task = $this->task->find($id);

        if (! $task) {
            throw ValidationException::withMessages(['message' => trans('task.could_not_find')]);
        }

        return $task;
    }

    /**
     * Paginate all tasks using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order      = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $name = isset($params['name']) ? $params['name'] : '';
        $project_id = isset($params['project_id']) ? $params['project_id'] : '';
        $task_relevance_id = isset($params['task_relevance_id']) ? $params['task_relevance_id'] : '';
        $task_frequency_id = isset($params['task_frequency_id']) ? $params['task_frequency_id'] : '';
        $task_complexity_id = isset($params['task_complexity_id']) ? $params['task_complexity_id'] : '';
        $task_family_id = isset($params['task_family_id']) ? $params['task_family_id'] : '';
       
        $query = $this->task->with('project', 'taskRelevance', 'taskFrequency', 'taskComplexity', 'taskFamily')->filterByName($name)->filterByProjectId($project_id)->filterByTaskRelevanceId($task_relevance_id)->filterByTaskFrequencyId($task_frequency_id)->filterByTaskComplexityId($task_complexity_id)->filterByTaskFamilyId($task_family_id);

        if ($sort_by === 'project_id') {
            $query->select('tasks.*', \DB::raw('(select name from projects where tasks.project_id = projects.id) as project_name'))->orderBy('project_name', $order);
        } else if ($sort_by === 'task_relevance_id') {
            $query->select('tasks.*', \DB::raw('(select name from task_relevances where tasks.task_relevance_id = task_relevances.id) as task_relevance_name'))->orderBy('task_relevance_name', $order);
        } else if ($sort_by === 'task_frequency_id') {
            $query->select('tasks.*', \DB::raw('(select name from task_frequencies where tasks.task_frequency_id = task_frequencys.id) as task_frequency_name'))->orderBy('task_frequency_name', $order);
        } else if ($sort_by === 'task_complexity_id') {
            $query->select('tasks.*', \DB::raw('(select name from task_complexities where tasks.task_complexity_id = task_complexitys.id) as task_complexity_name'))->orderBy('task_complexity_name', $order);
        } else if ($sort_by === 'task_family_id') {
            $query->select('tasks.*', \DB::raw('(select name from task_families where tasks.task_family_id = task_familys.id) as task_family_name'))->orderBy('task_family_name', $order);        
        } else {
            $query->orderBy($sort_by, $order);
        }

        return $query->paginate($page_length);
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

        $this->validateTaskName($params);
 
        return $this->task->forceCreate($this->formatParams($params));
    }

    /**
     * Validate input ids.
     *
     * @param array $params
     * @return null
     */

    public function validateInputId($params)
    {
        $project_ids = $this->project->listId();
        $task_relevance_ids = $this->task_relevance->listId();
        $task_frequency_ids = $this->task_frequency->listId();
        $task_complexity_ids = $this->task_complexity->listId();
        $task_family_ids = $this->task_family->listId();
     
        $project_id = isset($params['project_id']) ? $params['project_id'] : null;
        $task_relevance_id = isset($params['task_relevance_id']) ? $params['task_relevance_id'] : null;
        $task_frequency_id = isset($params['task_frequency_id']) ? $params['task_frequency_id'] : null;
        $task_complexity_id = isset($params['task_complexity_id']) ? $params['task_complexity_id'] : null;
        $task_family_id = isset($params['task_family_id']) ? $params['task_family_id'] : null;
     
        if (! in_array($project_id, $project_ids)) {
            throw ValidationException::withMessages(['message' => trans('project.could_not_find')]);
        }

        if (! in_array($task_relevance_id, $task_relevance_ids)) {
            throw ValidationException::withMessages(['message' => trans('task_relevance.could_not_find')]);
        }

        if (! in_array($task_frequency_id, $task_frequency_ids)) {
            throw ValidationException::withMessages(['message' => trans('task_frequency.could_not_find')]);
        }

        if (! in_array($task_complexity_id, $task_complexity_ids)) {
            throw ValidationException::withMessages(['message' => trans('task_complexity.could_not_find')]);
        }

        if (! in_array($task_family_id, $task_family_ids)) {
            throw ValidationException::withMessages(['message' => trans('task_family.could_not_find')]);
        }

     
    }

    /**
     * Validate unique task name with project.
     *
     * @param array $params
     * @param integer $id [default null]
     * @return null
     */
    public function validateTaskName($params, $id = null)
    {
        $query = $this->task->whereNotNull('id');

        if ($id) {
            $query->where('id', '!=', $id);
        }

        if ($query->filterByProjectId($params['project_id'])->filterByExactName($params['name'])->count()) {
            throw ValidationException::withMessages(['name' => trans('task.task_exists')]);
        }

        if ($query->filterByTaskRelevanceId($params['task_relevance_id'])->filterByExactName($params['name'])->count()) {
            throw ValidationException::withMessages(['name' => trans('task.task_exists')]);
        }

        if ($query->filterByTaskFrequencyId($params['task_frequency_id'])->filterByExactName($params['name'])->count()) {
            throw ValidationException::withMessages(['name' => trans('task.task_exists')]);
        }

        if ($query->filterByTaskComplexityId($params['task_complexity_id'])->filterByExactName($params['name'])->count()) {
            throw ValidationException::withMessages(['name' => trans('task.task_exists')]);
        }

        if ($query->filterByTaskFamilyId($params['task_family_id'])->filterByExactName($params['name'])->count()) {
            throw ValidationException::withMessages(['name' => trans('task.task_exists')]);
        }
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params, $action = 'create', $is_hidden = 0)
    {
       
        $system_admin_task = $this->task->filterByIsHidden(1)->first();

        $formatted = [
            'name'               => isset($params['name']) ? $params['name'] : null,
            'project_id'      => isset($params['project_id']) ? $params['project_id'] : null,
            'task_relevance_id'      => isset($params['task_relevance_id']) ? $params['task_relevance_id'] : null,
            'task_frequency_id'      => isset($params['task_frequency_id']) ? $params['task_frequency_id'] : null,
            'task_complexity_id'      => isset($params['task_complexity_id']) ? $params['task_complexity_id'] : null,
            'task_family_id'      => isset($params['task_family_id']) ? $params['task_family_id'] : null
        ];

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

        $this->validateTaskName($params, $task->id);

        $task->forceFill($this->formatParams($params, 'update', $task->is_hidden))->save();

        return $task;
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

        if ($task->profiles()->count()) {
            throw ValidationException::withMessages(['message' => trans('task.has_many_users')]);
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
     * Delete multiple tasks.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->task->whereIn('id', $ids)->delete();
    }
}
