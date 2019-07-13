<?php
namespace App\Repositories;

use App\Designation;
use App\Repositories\DepartmentRepository;
use Illuminate\Validation\ValidationException;

class DesignationRepository
{
    protected $designation;
    protected $department;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Designation $designation,
        DepartmentRepository $department
    ) {
        $this->designation = $designation;
        $this->department = $department;
    }

    /**
     * Get designation query
     *
     * @return Designation query
     */
    public function getQuery()
    {
        return $this->designation;
    }

    /**
     * Count designation
     *
     * @return integer
     */
    public function count()
    {
        return $this->designation->count();
    }

    /**
     * List all designations by designation with department name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->designation->all()->pluck('designation_with_department', 'id')->all();
    }

    /**
     * List all designations by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->designation->all()->pluck('id')->all();
    }

    /**
     * List all designations by department+designation name & id
     *
     * @return array
     */
    public function listAllFilterById($designation_ids)
    {
        return $this->designation->whereIn('id', $designation_ids)->get()->pluck('designation_with_department', 'id')->all();
    }

    /**
     * Get all designations
     *
     * @return array
     */
    public function getAll()
    {
        return $this->designation->all();
    }

    /**
     * List all top designations for authenticated user
     *
     * @return array
     */
    public function listTopDesignations()
    {
        return $this->designation->whereIn('id', $this->getSubordinate(\Auth::user(), 1))->get()->pluck('designation_with_department', 'id')->all();
    }

    /**
     * Get hidden designation
     *
     * @return array
     */
    public function getHidden()
    {
        return $this->designation->filterByIsHidden(1)->first();
    }

    /**
     * Get default designation
     *
     * @return array
     */
    public function getDefault()
    {
        return $this->designation->filterByIsDefault(1)->first();
    }

    /**
     * List top designation for edit
     *
     * @return array
     */
    public function listEditTopDesignation($id)
    {
        $auth_user = \Auth::user();

        $child_designations = $this->getChild($id);
        array_push($child_designations, $auth_user->Profile->designation_id);

        // array_diff is used to remove child designations from the lists.

        if ($auth_user->can('access-all-designation')) {
            $top_designations = array_diff($this->designation->where('id', '!=', $id)->get()->pluck('designation_with_department', 'id')->all(), $child_designations);
        } elseif ($auth_user->can('access-subordinate-designation')) {
            $top_designations = array_diff($this->designation->where('id', '!=', $id)->whereIn('id', $child_designations)->get()->pluck('designation_with_department', 'id')->all(), $child_designations);
        } else {
            $top_designations = [];
        }

        return $top_designations;
    }

    /**
     * Find designation with given id or throw an error.
     *
     * @param integer $id
     * @return Designation
     */
    public function findOrFail($id)
    {
        $designation = $this->designation->find($id);

        if (! $designation) {
            throw ValidationException::withMessages(['message' => trans('designation.could_not_find')]);
        }

        return $designation;
    }

    /**
     * Paginate all designations using given params.
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
        $top_designation_id = isset($params['top_designation_id']) ? $params['top_designation_id'] : '';

        $query = $this->designation->with('department', 'parent')->filterByName($name)->filterByDepartmentId($department_id)->filterByTopDesignationId($top_designation_id);

        if ($sort_by === 'department_id') {
            $query->select('designations.*', \DB::raw('(select name from departments where designations.department_id = departments.id) as department_name'))->orderBy('department_name', $order);
        } else {
            $query->orderBy($sort_by, $order);
        }

        return $query->paginate($page_length);
    }

    /**
     * Get all subordinate designation's id for given user.
     *
     * @param object $user
     * @param boolean $self (Pass 1 to include given user's designation)
     * @return array
     */
    public function getSubordinate($user = null, $self = 0)
    {
        $user = ($user) ? : \Auth::user();

        if ($user->is_hidden) {
            return $this->designation->all()->pluck('id')->all();
        } elseif ($user->can('access-all-designation')) {
            return $this->designation->filterByIsHidden(0)->get()->pluck('id')->all();
        } elseif ($user->can('access-subordinate-designation')) {
            $childs = $this->getChild($user->Profile->designation_id, 1);
            if ($self) {
                array_push($childs, $user->Profile->designation_id);
            }
            return $childs;
        } else {
            return ($self) ? $this->designation->filterById($user->Profile->designation_id)->pluck('id')->all() : [];
        }
    }

    /**
     * List all subordinate designation for given designation.
     *
     * @param integer $designation_id
     * @param boolean $type (Pass 0 to include given user's name & id, 1for only id)
     * @param boolean $level
     * @return array
     */
    public function getChild($designation_id = '', $type = 0, $level = 1)
    {
        $auth_user = \Auth::user();

        $designation_id = ($designation_id) ? : $auth_user->Profile->designation_id;

        $designation_name = $this->listAll();

        if ($auth_user->hasRole(config('system.default_role.admin'))) {
            $children =  $this->designation->all()->pluck('id')->all();
        }

        if (!config('config.designation_subordinate_level')) {
            $children =  $this->designation->filterByTopDesignationId($designation_id)->get()->pluck('id')->all();
        }

        $tree = array();
        $designations = $this->designation->whereNotNull('top_designation_id')->get();

        foreach ($designations as $designation) {
            $tree[$designation->id] = ['parent_id' => $designation->top_designation_id];
        }

        $children = getChilds($tree, $designation_id, $level);

        if ($type) {
            return $children;
        }

        $children_with_name = array();
        foreach ($children as $child) {
            $children_with_name[$child] = !empty($designation_name[$child]) ? $designation_name[$child] : null;
        }

        return $children_with_name;
    }

    /**
     * Create a new designation.
     *
     * @param array $params
     * @return Designation
     */
    public function create($params)
    {
        $this->validateInputId($params);

        $this->validateDesignationName($params);

        $top_designation_id = isset($params['top_designation_id']) ? $params['top_designation_id'] : null;

        if ($top_designation_id) {
            $top_designation = $this->findOrFail($params['top_designation_id']);
        }
        
        return $this->designation->forceCreate($this->formatParams($params));
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
        $designation_ids = $this->listId();

        $department_id = isset($params['department_id']) ? $params['department_id'] : null;
        $top_designation_id = isset($params['top_designation_id']) ? $params['top_designation_id'] : null;

        if (! in_array($department_id, $department_ids)) {
            throw ValidationException::withMessages(['message' => trans('department.could_not_find')]);
        }

        if ($top_designation_id && ! in_array($top_designation_id, $designation_ids)) {
            throw ValidationException::withMessages(['message' => trans('designation.could_not_find')]);
        }
    }

    /**
     * Validate unique designation name with department.
     *
     * @param array $params
     * @param integer $id [default null]
     * @return null
     */
    public function validateDesignationName($params, $id = null)
    {
        $query = $this->designation->whereNotNull('id');

        if ($id) {
            $query->where('id', '!=', $id);
        }

        if ($query->filterByDepartmentId($params['department_id'])->filterByExactName($params['name'])->count()) {
            throw ValidationException::withMessages(['name' => trans('designation.designation_exists')]);
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
            $this->designation->whereNotNull('id')->update(['is_default' => 0]);
        }

        $system_admin_designation = $this->designation->filterByIsHidden(1)->first();

        $formatted = [
            'name'               => isset($params['name']) ? $params['name'] : null,
            'department_id'      => isset($params['department_id']) ? $params['department_id'] : null,
            'top_designation_id' => (isset($params['top_designation_id']) && $params['top_designation_id']) ? $params['top_designation_id'] : ($system_admin_designation ? $system_admin_designation->id : null),
            'description'        => isset($params['description']) ? $params['description'] : null,
            'is_default'         => $is_default
        ];

        if ($is_hidden) {
            unset($formatted['top_designation_id']);
        }

        return $formatted;
    }

    /**
     * Update given designation.
     *
     * @param Designation $designation
     * @param array $params
     *
     * @return Designation
     */
    public function update(Designation $designation, $params)
    {
        $this->validateInputId($params);

        $this->validateDesignationName($params, $designation->id);

        $top_designation_id = (isset($params['top_designation_id']) && $params['top_designation_id']) ? $params['top_designation_id'] : null;

        if ($top_designation_id && (in_array($top_designation_id, $this->getChild($designation->id, 1)) || $designation->id === $top_designation_id)) {
            throw ValidationException::withMessages(['top_designation_id' => trans('designation.top_designation_cannot_become_child')]);
        }

        $designation->forceFill($this->formatParams($params, 'update', $designation->is_hidden))->save();

        return $designation;
    }

    /**
     * Find designation & check it can be deleted or not.
     *
     * @param integer $id
     * @return Designation
     */
    public function deletable($id)
    {
        $designation = $this->findOrFail($id);

        if ($designation->profiles()->count()) {
            throw ValidationException::withMessages(['message' => trans('designation.has_many_users')]);
        }
        
        return $designation;
    }

    /**
     * Delete designation.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Designation $designation)
    {
        return $designation->delete();
    }

    /**
     * Delete multiple designations.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->designation->whereIn('id', $ids)->delete();
    }
}
