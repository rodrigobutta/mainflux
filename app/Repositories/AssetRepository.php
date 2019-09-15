<?php
namespace App\Repositories;

use App\Asset;
use App\Repositories\DepartmentRepository;
use App\Repositories\ClientRepository;
use Illuminate\Validation\ValidationException;

class AssetRepository
{
    protected $asset;
    protected $department;
    protected $client;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Asset $asset,
        DepartmentRepository $department,
        ClientRepository $client
    ) {
        $this->asset = $asset;
        $this->department = $department;
        $this->client = $client;
    }

    /**
     * Get asset query
     *
     * @return Asset query
     */
    public function getQuery()
    {
        return $this->asset;
    }

    /**
     * Count asset
     *
     * @return integer
     */
    public function count()
    {
        return $this->asset->count();
    }

    /**
     * List all assets by asset with department name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->asset->all()->pluck('asset_with_department', 'id')->all();
    }

    /**
     * List all assets by id
     *
     * @return array
     */
    public function listId()
    {
        return $this->asset->all()->pluck('id')->all();
    }

    /**
     * List all assets by department+asset name & id
     *
     * @return array
     */
    public function listAllFilterById($asset_ids)
    {
        return $this->asset->whereIn('id', $asset_ids)->get()->pluck('asset_with_department', 'id')->all();
    }

    /**
     * Get all assets
     *
     * @return array
     */
    public function getAll()
    {
        return $this->asset->all();
    }

    /**
     * List all top assets for authenticated user
     *
     * @return array
     */
    public function listTopAssets()
    {
        return $this->asset->whereIn('id', $this->getSubordinate(\Auth::user(), 1))->get()->pluck('asset_with_department', 'id')->all();
    }

    /**
     * Get hidden asset
     *
     * @return array
     */
    public function getHidden()
    {
        return $this->asset->filterByIsHidden(1)->first();
    }

    /**
     * Get default asset
     *
     * @return array
     */
    public function getDefault()
    {
        return $this->asset->filterByIsDefault(1)->first();
    }

    /**
     * List top asset for edit
     *
     * @return array
     */
    public function listEditTopAsset($id)
    {
        $auth_user = \Auth::user();

        $child_assets = $this->getChild($id);
        array_push($child_assets, $auth_user->Profile->asset_id);

        // array_diff is used to remove child assets from the lists.

        if ($auth_user->can('access-all-asset')) {
            $top_assets = array_diff($this->asset->where('id', '!=', $id)->get()->pluck('asset_with_department', 'id')->all(), $child_assets);
        } elseif ($auth_user->can('access-subordinate-asset')) {
            $top_assets = array_diff($this->asset->where('id', '!=', $id)->whereIn('id', $child_assets)->get()->pluck('asset_with_department', 'id')->all(), $child_assets);
        } else {
            $top_assets = [];
        }

        return $top_assets;
    }

    /**
     * Find asset with given id or throw an error.
     *
     * @param integer $id
     * @return Asset
     */
    public function findOrFail($id)
    {
        $asset = $this->asset->find($id);

        if (! $asset) {
            throw ValidationException::withMessages(['message' => trans('asset.could_not_find')]);
        }

        return $asset;
    }

    /**
     * Paginate all assets using given params.
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
        $top_asset_id = isset($params['top_asset_id']) ? $params['top_asset_id'] : '';

        $query = $this->asset->with('department', 'client', 'parent')->filterByName($name)->filterByDepartmentId($department_id)->filterByClientId($client_id)->filterByTopAssetId($top_asset_id);

        if ($sort_by === 'department_id') {
            $query->select('assets.*', \DB::raw('(select name from departments where assets.department_id = departments.id) as department_name'))->orderBy('department_name', $order);
        } else if ($sort_by === 'client_id') {
            $query->select('assets.*', \DB::raw('(select name from clients where assets.client_id = clients.id) as client_name'))->orderBy('client_name', $order);
        } else {
            $query->orderBy($sort_by, $order);
        }

        return $query->paginate($page_length);
    }

    /**
     * Get all subordinate asset's id for given user.
     *
     * @param object $user
     * @param boolean $self (Pass 1 to include given user's asset)
     * @return array
     */
    public function getSubordinate($user = null, $self = 0)
    {
        $user = ($user) ? : \Auth::user();

        if ($user->is_hidden) {
            return $this->asset->all()->pluck('id')->all();
        } elseif ($user->can('access-all-asset')) {
            return $this->asset->filterByIsHidden(0)->get()->pluck('id')->all();
        } elseif ($user->can('access-subordinate-asset')) {
            $childs = $this->getChild($user->Profile->asset_id, 1);
            if ($self) {
                array_push($childs, $user->Profile->asset_id);
            }
            return $childs;
        } else {
            return ($self) ? $this->asset->filterById($user->Profile->asset_id)->pluck('id')->all() : [];
        }
    }

    /**
     * List all subordinate asset for given asset.
     *
     * @param integer $asset_id
     * @param boolean $type (Pass 0 to include given user's name & id, 1for only id)
     * @param boolean $level
     * @return array
     */
    public function getChild($asset_id = '', $type = 0, $level = 1)
    {
        $auth_user = \Auth::user();

        $asset_id = ($asset_id) ? : $auth_user->Profile->asset_id;

        $asset_name = $this->listAll();

        if ($auth_user->hasRole(config('system.default_role.admin'))) {
            $children =  $this->asset->all()->pluck('id')->all();
        }

        if (!config('config.asset_subordinate_level')) {
            $children =  $this->asset->filterByTopAssetId($asset_id)->get()->pluck('id')->all();
        }

        $tree = array();
        $assets = $this->asset->whereNotNull('top_asset_id')->get();

        foreach ($assets as $asset) {
            $tree[$asset->id] = ['parent_id' => $asset->top_asset_id];
        }

        $children = getChilds($tree, $asset_id, $level);

        if ($type) {
            return $children;
        }

        $children_with_name = array();
        foreach ($children as $child) {
            $children_with_name[$child] = !empty($asset_name[$child]) ? $asset_name[$child] : null;
        }

        return $children_with_name;
    }

    /**
     * Create a new asset.
     *
     * @param array $params
     * @return Asset
     */
    public function create($params)
    {
        $this->validateInputId($params);

        $this->validateAssetName($params);

        $top_asset_id = isset($params['top_asset_id']) ? $params['top_asset_id'] : null;

        if ($top_asset_id) {
            $top_asset = $this->findOrFail($params['top_asset_id']);
        }
        
        return $this->asset->forceCreate($this->formatParams($params));
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
        $asset_ids = $this->listId();

        $department_id = isset($params['department_id']) ? $params['department_id'] : null;
        $client_id = isset($params['client_id']) ? $params['client_id'] : null;
        $top_asset_id = isset($params['top_asset_id']) ? $params['top_asset_id'] : null;

        if (! in_array($department_id, $department_ids)) {
            throw ValidationException::withMessages(['message' => trans('department.could_not_find')]);
        }

        if (! in_array($client_id, $client_ids)) {
            throw ValidationException::withMessages(['message' => trans('client.could_not_find')]);
        }

        if ($top_asset_id && ! in_array($top_asset_id, $asset_ids)) {
            throw ValidationException::withMessages(['message' => trans('asset.could_not_find')]);
        }
    }

    /**
     * Validate unique asset name with department.
     *
     * @param array $params
     * @param integer $id [default null]
     * @return null
     */
    public function validateAssetName($params, $id = null)
    {
        $query = $this->asset->whereNotNull('id');

        if ($id) {
            $query->where('id', '!=', $id);
        }

        if ($query->filterByDepartmentId($params['department_id'])->filterByExactName($params['name'])->count()) {
            throw ValidationException::withMessages(['name' => trans('asset.asset_exists')]);
        }

        if ($query->filterByClientId($params['client_id'])->filterByExactName($params['name'])->count()) {
            throw ValidationException::withMessages(['name' => trans('asset.asset_exists')]);
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
            $this->asset->whereNotNull('id')->update(['is_default' => 0]);
        }

        $system_admin_asset = $this->asset->filterByIsHidden(1)->first();

        $formatted = [
            'name'               => isset($params['name']) ? $params['name'] : null,
            'department_id'      => isset($params['department_id']) ? $params['department_id'] : null,
            'client_id'      => isset($params['client_id']) ? $params['client_id'] : null,
            'top_asset_id' => (isset($params['top_asset_id']) && $params['top_asset_id']) ? $params['top_asset_id'] : ($system_admin_asset ? $system_admin_asset->id : null),
            'description'        => isset($params['description']) ? $params['description'] : null,
            'is_default'         => $is_default
        ];

        if ($is_hidden) {
            unset($formatted['top_asset_id']);
        }

        return $formatted;
    }

    /**
     * Update given asset.
     *
     * @param Asset $asset
     * @param array $params
     *
     * @return Asset
     */
    public function update(Asset $asset, $params)
    {
        $this->validateInputId($params);

        $this->validateAssetName($params, $asset->id);

        $top_asset_id = (isset($params['top_asset_id']) && $params['top_asset_id']) ? $params['top_asset_id'] : null;

        if ($top_asset_id && (in_array($top_asset_id, $this->getChild($asset->id, 1)) || $asset->id === $top_asset_id)) {
            throw ValidationException::withMessages(['top_asset_id' => trans('asset.top_asset_cannot_become_child')]);
        }

        $asset->forceFill($this->formatParams($params, 'update', $asset->is_hidden))->save();

        return $asset;
    }

    /**
     * Find asset & check it can be deleted or not.
     *
     * @param integer $id
     * @return Asset
     */
    public function deletable($id)
    {
        $asset = $this->findOrFail($id);

        if ($asset->profiles()->count()) {
            throw ValidationException::withMessages(['message' => trans('asset.has_many_users')]);
        }
        
        return $asset;
    }

    /**
     * Delete asset.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Asset $asset)
    {
        return $asset->delete();
    }

    /**
     * Delete multiple assets.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->asset->whereIn('id', $ids)->delete();
    }
}
