<?php

namespace App\Policies;

use App\User;
use App\Location;
use Illuminate\Auth\Access\HandlesAuthorization;

class LocationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can fetch pre-requisites for location.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function preRequisite(User $user)
    {
        return ($user->can('create-location') || $user->can('edit-location'));
    }

    /**
     * Determine whether the user can list all the location.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-location');
    }

    /**
     * Determine whether the user can create locations.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-location');
    }

    /**
     * Determine whether the user can view the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @param  array $sub_ordinates
     * @return mixed
     */
    public function view(User $user, Location $location, $sub_ordinates)
    {
        return ($user->can('access-all-location') || ($user->can('access-subordinate-location') && in_array($location->id, $sub_ordinates)));
    }

    /**
     * Determine whether the user can update the location.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-location');
    }

    /**
     * Determine whether the user can delete the location.
     *
     * @param  \App\User  $user
     * @param  \App\Location  $location
     * @return mixed
     */
    public function delete(User $user, Location $location)
    {
        return $user->can('delete-location') && ! $location->is_hidden;
    }
}
