<?php
namespace App\Repositories;

use App\Project;
use App\Repositories\DepartmentRepository;
use App\Repositories\ClientRepository;
use App\Repositories\ContractorRepository;
use Illuminate\Validation\ValidationException;

class ProjectRepository
{
    protected $project;
    protected $department;
    protected $client;
    protected $contractor;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Project $project,
        DepartmentRepository $department,
        ClientRepository $client,
        ContractorRepository $contractor
    ) {
        $this->project = $project;
        $this->department = $department;
        $this->client = $client;
        $this->contractor = $contractor;
    }

    /**
     * Get project query
     *
     * @return Project query
     */
    public function getQuery()
    {
        return $this->project;
    }

    /**
     * Count project
     *
     * @return integer
     */
    public function count()
    {
        return $this->project->count();
    }

    /**
     * List all projects by project with department name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->project->all()->pluck('project_with_department', 'id')->all();
    }

    /**
     * List all projects by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->project->all()->pluck('id')->all();
    }

    /**
     * List all projects by department+project name & id
     *
     * @return array
     */
    public function listAllFilterById($project_ids)
    {
        return $this->project->whereIn('id', $project_ids)->get()->pluck('project_with_department', 'id')->all();
    }

    /**
     * Get all projects
     *
     * @return array
     */
    public function getAll()
    {
        return $this->project->all();
    }

    /**
     * List all top projects for authenticated user
     *
     * @return array
     */
    public function listTopProjects()
    {
        return $this->project->whereIn('id', $this->getSubordinate(\Auth::user(), 1))->get()->pluck('project_with_department', 'id')->all();
    }

    /**
     * Get hidden project
     *
     * @return array
     */
    public function getHidden()
    {
        return $this->project->filterByIsHidden(1)->first();
    }

    /**
     * Get default project
     *
     * @return array
     */
    public function getDefault()
    {
        return $this->project->filterByIsDefault(1)->first();
    }

    /**
     * List top project for edit
     *
     * @return array
     */
    public function listEditTopProject($id)
    {
        $auth_user = \Auth::user();

        $child_projects = $this->getChild($id);
        array_push($child_projects, $auth_user->Profile->project_id);

        // array_diff is used to remove child projects from the lists.

        if ($auth_user->can('access-all-project')) {
            $top_projects = array_diff($this->project->where('id', '!=', $id)->get()->pluck('project_with_department', 'id')->all(), $child_projects);
        } elseif ($auth_user->can('access-subordinate-project')) {
            $top_projects = array_diff($this->project->where('id', '!=', $id)->whereIn('id', $child_projects)->get()->pluck('project_with_department', 'id')->all(), $child_projects);
        } else {
            $top_projects = [];
        }

        return $top_projects;
    }

    /**
     * Find project with given id or throw an error.
     *
     * @param integer $id
     * @return Project
     */
    public function findOrFail($id)
    {
        $project = $this->project->find($id);

        if (! $project) {
            throw ValidationException::withMessages(['message' => trans('project.could_not_find')]);
        }

        return $project;
    }

    /**
     * Paginate all projects using given params.
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
        $department_id = isset($params['department_id']) ? $params['department_id'] : '';
        $client_id = isset($params['client_id']) ? $params['client_id'] : '';
        $contractor_id = isset($params['contractor_id']) ? $params['contractor_id'] : '';
        $top_project_id = isset($params['top_project_id']) ? $params['top_project_id'] : '';

        $query = $this->project->with('department', 'client', 'contractor', 'parent')->filterByName($name)->filterByDepartmentId($department_id)->filterByClientId($client_id)->filterByContractorId($contractor_id)->filterByTopProjectId($top_project_id);

        if ($sort_by === 'department_id') {
            $query->select('projects.*', \DB::raw('(select name from departments where projects.department_id = departments.id) as department_name'))->orderBy('department_name', $order);
        } else if ($sort_by === 'client_id') {
            $query->select('projects.*', \DB::raw('(select name from clients where projects.client_id = clients.id) as client_name'))->orderBy('client_name', $order);
        } else if ($sort_by === 'contractor_id') {
            $query->select('projects.*', \DB::raw('(select name from contractors where projects.contractor_id = contractors.id) as contractor_name'))->orderBy('contractor_name', $order);        
        } else {
            $query->orderBy($sort_by, $order);
        }

        return $query->paginate($page_length);
    }

    /**
     * Get all subordinate project's id for given user.
     *
     * @param object $user
     * @param boolean $self (Pass 1 to include given user's project)
     * @return array
     */
    public function getSubordinate($user = null, $self = 0)
    {
        $user = ($user) ? : \Auth::user();

        if ($user->is_hidden) {
            return $this->project->all()->pluck('id')->all();
        } elseif ($user->can('access-all-project')) {
            return $this->project->filterByIsHidden(0)->get()->pluck('id')->all();
        } elseif ($user->can('access-subordinate-project')) {
            $childs = $this->getChild($user->Profile->project_id, 1);
            if ($self) {
                array_push($childs, $user->Profile->project_id);
            }
            return $childs;
        } else {
            return ($self) ? $this->project->filterById($user->Profile->project_id)->pluck('id')->all() : [];
        }
    }

    /**
     * List all subordinate project for given project.
     *
     * @param integer $project_id
     * @param boolean $type (Pass 0 to include given user's name & id, 1for only id)
     * @param boolean $level
     * @return array
     */
    public function getChild($project_id = '', $type = 0, $level = 1)
    {
        $auth_user = \Auth::user();

        $project_id = ($project_id) ? : $auth_user->Profile->project_id;

        $project_name = $this->listAll();

        if ($auth_user->hasRole(config('system.default_role.admin'))) {
            $children =  $this->project->all()->pluck('id')->all();
        }

        if (!config('config.project_subordinate_level')) {
            $children =  $this->project->filterByTopProjectId($project_id)->get()->pluck('id')->all();
        }

        $tree = array();
        $projects = $this->project->whereNotNull('top_project_id')->get();

        foreach ($projects as $project) {
            $tree[$project->id] = ['parent_id' => $project->top_project_id];
        }

        $children = getChilds($tree, $project_id, $level);

        if ($type) {
            return $children;
        }

        $children_with_name = array();
        foreach ($children as $child) {
            $children_with_name[$child] = !empty($project_name[$child]) ? $project_name[$child] : null;
        }

        return $children_with_name;
    }

    /**
     * Create a new project.
     *
     * @param array $params
     * @return Project
     */
    public function create($params)
    {
        $this->validateInputId($params);

        $this->validateProjectName($params);

        $top_project_id = isset($params['top_project_id']) ? $params['top_project_id'] : null;

        if ($top_project_id) {
            $top_project = $this->findOrFail($params['top_project_id']);
        }
        
        return $this->project->forceCreate($this->formatParams($params));
    }

    /**
     * Validate input ids.
     *
     * @param array $params
     * @return null
     */

    public function validateInputId($params)
    {
        $department_ids = $this->department->listId();
        $client_ids = $this->client->listId();
        $contractor_ids = $this->contractor->listId();
        $project_ids = $this->listId();

        $department_id = isset($params['department_id']) ? $params['department_id'] : null;
        $client_id = isset($params['client_id']) ? $params['client_id'] : null;
        $contractor_id = isset($params['contractor_id']) ? $params['contractor_id'] : null;
        $top_project_id = isset($params['top_project_id']) ? $params['top_project_id'] : null;

        if (! in_array($department_id, $department_ids)) {
            throw ValidationException::withMessages(['message' => trans('department.could_not_find')]);
        }

        if (! in_array($client_id, $client_ids)) {
            throw ValidationException::withMessages(['message' => trans('client.could_not_find')]);
        }

        if (! in_array($contractor_id, $contractor_ids)) {
            throw ValidationException::withMessages(['message' => trans('contractor.could_not_find')]);
        }

        if ($top_project_id && ! in_array($top_project_id, $project_ids)) {
            throw ValidationException::withMessages(['message' => trans('project.could_not_find')]);
        }
    }

    /**
     * Validate unique project name with department.
     *
     * @param array $params
     * @param integer $id [default null]
     * @return null
     */
    public function validateProjectName($params, $id = null)
    {
        $query = $this->project->whereNotNull('id');

        if ($id) {
            $query->where('id', '!=', $id);
        }

        if ($query->filterByDepartmentId($params['department_id'])->filterByExactName($params['name'])->count()) {
            throw ValidationException::withMessages(['name' => trans('project.project_exists')]);
        }

        if ($query->filterByClientId($params['client_id'])->filterByExactName($params['name'])->count()) {
            throw ValidationException::withMessages(['name' => trans('project.project_exists')]);
        }

        if ($query->filterByContractorId($params['contractor_id'])->filterByExactName($params['name'])->count()) {
            throw ValidationException::withMessages(['name' => trans('project.project_exists')]);
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
        $is_default = (isset($params['is_default']) && $params['is_default']) ? 1 : 0;

        if ($is_default) {
            $this->project->whereNotNull('id')->update(['is_default' => 0]);
        }

        $system_admin_project = $this->project->filterByIsHidden(1)->first();

        $formatted = [
            'name'               => isset($params['name']) ? $params['name'] : null,
            'department_id'      => isset($params['department_id']) ? $params['department_id'] : null,
            'client_id'      => isset($params['client_id']) ? $params['client_id'] : null,
            'contractor_id'      => isset($params['contractor_id']) ? $params['contractor_id'] : null,
            'top_project_id' => (isset($params['top_project_id']) && $params['top_project_id']) ? $params['top_project_id'] : ($system_admin_project ? $system_admin_project->id : null),
            'description'        => isset($params['description']) ? $params['description'] : null,
            'is_default'         => $is_default
        ];

        if ($is_hidden) {
            unset($formatted['top_project_id']);
        }

        return $formatted;
    }

    /**
     * Update given project.
     *
     * @param Project $project
     * @param array $params
     *
     * @return Project
     */
    public function update(Project $project, $params)
    {
        $this->validateInputId($params);

        $this->validateProjectName($params, $project->id);

        $top_project_id = (isset($params['top_project_id']) && $params['top_project_id']) ? $params['top_project_id'] : null;

        if ($top_project_id && (in_array($top_project_id, $this->getChild($project->id, 1)) || $project->id === $top_project_id)) {
            throw ValidationException::withMessages(['top_project_id' => trans('project.top_project_cannot_become_child')]);
        }

        $project->forceFill($this->formatParams($params, 'update', $project->is_hidden))->save();

        return $project;
    }

    /**
     * Find project & check it can be deleted or not.
     *
     * @param integer $id
     * @return Project
     */
    public function deletable($id)
    {
        $project = $this->findOrFail($id);

        if ($project->profiles()->count()) {
            throw ValidationException::withMessages(['message' => trans('project.has_many_users')]);
        }
        
        return $project;
    }

    /**
     * Delete project.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Project $project)
    {
        return $project->delete();
    }

    /**
     * Delete multiple projects.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->project->whereIn('id', $ids)->delete();
    }
}
