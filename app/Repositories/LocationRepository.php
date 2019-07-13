<?php
namespace App\Repositories;

use App\Location;
use Illuminate\Validation\ValidationException;

class LocationRepository
{
    protected $location;

    /**
     * Instantiate a new instance.
     *
     * @return void
     */
    public function __construct(
        Location $location
    ) {
        $this->location = $location;
    }

    /**
     * Get location query
     *
     * @return Location query
     */
    public function getQuery()
    {
        return $this->location;
    }

    /**
     * Count location
     *
     * @return integer
     */
    public function count()
    {
        return $this->location->count();
    }

    /**
     * List all locations by location name & id
     *
     * @return array
     */
    public function listAll()
    {
        return $this->location->all()->pluck('name', 'id')->all();
    }

    /**
     * List all locations by location id
     *
     * @return array
     */
    public function listId()
    {
        return $this->location->all()->pluck('id')->all();
    }

    /**
     * List all locations by location name & id
     *
     * @return array
     */
    public function listAllFilterById($location_ids)
    {
        return $this->location->whereIn('id', $location_ids)->get()->pluck('name', 'id')->all();
    }

    /**
     * Get all locations
     *
     * @return array
     */
    public function getAll()
    {
        return $this->location->all();
    }

    /**
     * List all top locations for authenticated user
     *
     * @return array
     */
    public function listTopLocations()
    {
        return $this->location->whereIn('id', $this->getSubordinate(\Auth::user(), 1))->get()->pluck('name', 'id')->all();
    }

    /**
     * Get default location
     *
     * @return array
     */
    public function getDefault()
    {
        return $this->location->filterByIsDefault(1)->first();
    }

    /**
     * List top location for edit
     *
     * @return array
     */
    public function listEditTopLocation($id)
    {
        $auth_user = \Auth::user();

        $child_locations = $this->getChild($id);
        array_push($child_locations, $auth_user->Profile->location_id);

        // array_diff is used to remove child locations from the lists.

        if ($auth_user->can('access-all-location')) {
            $top_locations = array_diff($this->location->where('id', '!=', $id)->get()->pluck('name', 'id')->all(), $child_locations);
        } elseif ($auth_user->can('access-subordinate-location')) {
            $top_locations = array_diff($this->location->where('id', '!=', $id)->whereIn('id', $child_locations)->get()->pluck('name', 'id')->all(), $child_locations);
        } else {
            $top_locations = [];
        }

        return $top_locations;
    }

    /**
     * Find location with given id or throw an error.
     *
     * @param integer $id
     * @return Location
     */
    public function findOrFail($id)
    {
        $location = $this->location->find($id);

        if (! $location) {
            throw ValidationException::withMessages(['message' => trans('location.could_not_find')]);
        }

        return $location;
    }

    /**
     * Paginate all locations using given params.
     *
     * @param array $params
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginate($params)
    {
        $sort_by         = isset($params['sort_by']) ? $params['sort_by'] : 'created_at';
        $order           = isset($params['order']) ? $params['order'] : 'desc';
        $page_length     = isset($params['page_length']) ? $params['page_length'] : config('config.page_length');
        $name            = isset($params['name']) ? $params['name'] : '';
        $top_location_id = isset($params['top_location_id']) ? $params['top_location_id'] : '';

        return $this->location->with('parent')->filterByName($name)->filterByTopLocationId($top_location_id)->orderBy($sort_by, $order)->paginate($page_length);
    }

    /**
     * Get all subordinate location's id for given user.
     *
     * @param object $user
     * @param boolean $self (Pass 1 to include given user's location)
     * @return array
     */
    public function getSubordinate($user = null, $self = 0)
    {
        $user = ($user) ? : \Auth::user();

        if ($user->is_hidden) {
            return $this->location->all()->pluck('id')->all();
        } elseif ($user->can('access-all-location')) {
            return $this->location->all()->pluck('id')->all();
        } elseif ($user->can('access-subordinate-location')) {
            $childs = $this->getChild($user->Profile->location_id, 1);
            if ($self) {
                array_push($childs, $user->Profile->location_id);
            }
            return $childs;
        } else {
            return ($self) ? $this->location->filterById($user->Profile->location_id)->pluck('id')->all() : [];
        }
    }

    /**
     * List all subordinate location for given location.
     *
     * @param integer $location_id
     * @param boolean $type (Pass 0 to include given user's name & id, 1 for only id)
     * @param boolean $level
     * @return array
     */
    public function getChild($location_id = '', $type = 0, $level = 1)
    {
        $auth_user = \Auth::user();

        $location_id = ($location_id) ? : $auth_user->Profile->location_id;

        $location_name = $this->listAll();

        if ($auth_user->hasRole(config('system.default_role.admin'))) {
            $children =  $this->location->all()->pluck('id')->all();
        }

        if (!config('config.location_subordinate_level')) {
            $children =  $this->location->filterByTopLocationId($location_id)->get()->pluck('id')->all();
        }

        $tree = array();
        $locations = $this->location->whereNotNull('top_location_id')->get();

        foreach ($locations as $location) {
            $tree[$location->id] = ['parent_id' => $location->top_location_id];
        }

        $children = getChilds($tree, $location_id, $level);

        if ($type) {
            return $children;
        }

        $children_with_name = array();
        foreach ($children as $child) {
            $children_with_name[$child] = !empty($location_name[$child]) ? $location_name[$child] : null;
        }

        return $children_with_name;
    }

    /**
     * Create a new location.
     *
     * @param array $params
     * @return Location
     */
    public function create($params)
    {
        $this->validateInputId($params);

        $this->validateLocationName($params);

        $top_location_id = isset($params['top_location_id']) ? $params['top_location_id'] : null;

        if ($top_location_id) {
            $top_location = $this->findOrFail($params['top_location_id']);
        }
        
        return $this->location->forceCreate($this->formatParams($params));
    }

    /**
     * Validate input ids.
     *
     * @param array $params
     * @return null
     */

    public function validateInputId($params)
    {
        $location_ids = $this->listId();

        $top_location_id = isset($params['top_location_id']) ? $params['top_location_id'] : null;

        if ($top_location_id && ! in_array($top_location_id, $location_ids)) {
            throw ValidationException::withMessages(['message' => trans('location.could_not_find')]);
        }
    }

    /**
     * Validate unique location name.
     *
     * @param array $params
     * @param integer $id [default null]
     * @return null
     */
    public function validateLocationName($params, $id = null)
    {
        $query = $this->location->whereNotNull('id');

        if ($id) {
            $query->where('id', '!=', $id);
        }

        if ($query->filterByExactName($params['name'])->count()) {
            throw ValidationException::withMessages(['name' => trans('location.location_exists')]);
        }
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
        $is_default = (isset($params['is_default']) && $params['is_default']) ? 1 : 0;

        if ($is_default) {
            $this->location->whereNotNull('id')->update(['is_default' => 0]);
        }

        $system_admin_location = $this->location->first();

        $formatted = [
            'name'            => isset($params['name']) ? $params['name'] : null,
            'top_location_id' => (isset($params['top_location_id']) && $params['top_location_id']) ? $params['top_location_id'] : ($system_admin_location ? $system_admin_location->id : null),
            'description'     => isset($params['description']) ? $params['description'] : null,
            'is_default'      => $is_default
        ];

        return $formatted;
    }

    /**
     * Update given location.
     *
     * @param Location $location
     * @param array $params
     *
     * @return Location
     */
    public function update(Location $location, $params)
    {
        $this->validateInputId($params);

        $this->validateLocationName($params, $location->id);

        $top_location_id = (isset($params['top_location_id']) && $params['top_location_id']) ? $params['top_location_id'] : null;

        if ($top_location_id && (in_array($top_location_id, $this->getChild($location->id, 1)) || $location->id === $top_location_id)) {
            throw ValidationException::withMessages(['top_location_id' => trans('location.top_location_cannot_become_child')]);
        }

        $location->forceFill($this->formatParams($params, 'update'))->save();

        return $location;
    }

    /**
     * Find location & check it can be deleted or not.
     *
     * @param integer $id
     * @return Location
     */
    public function deletable($id)
    {
        $location = $this->findOrFail($id);

        if ($location->profiles()->count()) {
            throw ValidationException::withMessages(['message' => trans('location.has_many_users')]);
        }
        
        return $location;
    }

    /**
     * Delete location.
     *
     * @param integer $id
     * @return bool|null
     */
    public function delete(Location $location)
    {
        return $location->delete();
    }

    /**
     * Delete multiple locations.
     *
     * @param array $ids
     * @return bool|null
     */
    public function deleteMultiple($ids)
    {
        return $this->location->whereIn('id', $ids)->delete();
    }
}
