<?php
namespace App\Repositories;

use App\Department;
use Illuminate\Validation\ValidationException;

class DepartmentRepository
{
    protected $department;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Department $department
    ) {
        $this->department = $department;
    }

    /**
     * Get department query
     *
     * @return Department query
     */
    public function getQuery()
    {
        return $this->department;
    }

    /**
     * Count department
     *
     * @return integer
     */
    public function count()
    {
        return $this->department->count();
    }

    /**
     * List all departments by name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->department->all()->pluck('name', 'id')->all();
    }

    /**
     * List all departments by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->department->all()->pluck('id')->all();
    }

    /**
     * Get all departments
     *
     * @return array
     */
    public function getAll()
    {
        return $this->department->all();
    }

    /**
     * Find department with given id or throw an error.
     *
     * @param integer $id
     * @return Department
     */
    public function findOrFail($id)
    {
        $department = $this->department->find($id);

        if (! $department) {
            throw ValidationException::withMessages(['message' => trans('department.could_not_find')]);
        }

        return $department;
    }

    /**
     * Paginate all departments using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order       = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $name        = isset($params['name']) ? $params['name'] : '';

        return $this->department->filterByName($name)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Create a new department.
     *
     * @param array $params
     * @return Department
     */
    public function create($params)
    {
        return $this->department->forceCreate($this->formatParams($params));
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
            'name'        => isset($params['name']) ? $params['name'] : null,
            'description' => isset($params['description']) ? $params['description'] : null
        ];

        return $formatted;
    }

    /**
     * Update given department.
     *
     * @param Department $department
     * @param array $params
     *
     * @return Department
     */
    public function update(Department $department, $params)
    {
        $department->forceFill($this->formatParams($params, 'update'))->save();

        return $department;
    }

    /**
     * Find department & check it can be deleted or not.
     *
     * @param integer $id
     * @return Department
     */
    public function deletable($id)
    {
        $department = $this->findOrFail($id);

        if ($department->designations()->count()) {
            throw ValidationException::withMessages(['message' => trans('department.has_many_designations')]);
        }
        
        return $department;
    }

    /**
     * Delete department.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Department $department)
    {
        return $department->delete();
    }

    /**
     * Delete multiple departments.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->department->whereIn('id', $ids)->delete();
    }
}
