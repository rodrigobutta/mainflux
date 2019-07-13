<?php
namespace App\Repositories;

use App\Permission;
use Illuminate\Validation\ValidationException;

class PermissionRepository
{
    protected $permission;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    /**
     * Get all permissions
     *
     * @return Permission
     */
    public function getAll()
    {
        return $this->permission->all();
    }

    /**
     * List all permissions by name
     *
     * @return Permission
     */
    public function listByName($ids = [])
    {
        if (count($ids)) {
            return $this->permission->whereIn('id', $ids)->get()->pluck('name')->all();
        } else {
            return $this->permission->all()->pluck('name')->all();
        }
    }

    /**
     * List all names
     *
     * @return Permission
     */
    public function listName()
    {
        return $this->permission->all()->pluck('name')->all();
    }

    /**
     * Find activity log with given id or throw an error.
     *
     * @param integer $id
     * @return Permission
     */
    public function findOrFail($id)
    {
        $permission = $this->permission->find($id);

        if (! $permission) {
            throw ValidationException::withMessages(['message' => trans('permission.could_not_find')]);
        }

        return $permission;
    }

    /**
     * Paginate all activity logs using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by     = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order       = isset($params['order']) ? $params['order'] : 'desc';
        $page_length = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');

        return $this->permission->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Record a new activity.
     *
     * @param array $params
     * @return Permission
     */
    public function record($params)
    {
        return $this->permission->forceCreate($this->formatParams($params));
    }

    /**
     * Prepare given params for inserting into database.
     *
     * @param array $params
     * @param string $type
     * @return array
     */
    private function formatParams($params)
    {
        $formatted = [
            'user_id'       => isset($params['userId']) ? $params['userId'] : \Auth::user()->id,
            'module'        => isset($params['module']) ? $params['module'] : null,
            'module_id'     => isset($params['module_id']) ? $params['module_id'] : null,
            'sub_module'    => isset($params['sub_module']) ? $params['sub_module'] : null,
            'sub_module_id' => isset($params['sub_moduleId']) ? $params['sub_moduleId'] : null,
            'activity' => isset($params['activity']) ? $params['activity'] : null,
            'ip'            => getClientIp(),
            'user_agent'    => \Request::header('User-Agent')
        ];

        return $formatted;
    }

    /**
     * Delete activity log.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Permission $permission)
    {
        return $permission->delete();
    }

    /**
     * Delete multiple activity logs.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->permission->whereIn('id', $ids)->delete();
    }
}
