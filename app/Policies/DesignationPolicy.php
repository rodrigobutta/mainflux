<?php

namespace App\Policies;

use App\User;
use App\Designation;
use Illuminate\Auth\Access\HandlesAuthorization;

class DesignationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can fetch pre-requisites for designation.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function preRequisite(User $user)
    {
        return ($user->can('create-designation') || $user->can('edit-designation'));
    }

    /**
     * Determine whether the user can list all the designation.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-designation');
    }

    /**
     * Determine whether the user can create designations.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-designation');
    }

    /**
     * Determine whether the user can view the designation.
     *
     * @param  \App\User  $user
     * @param  \App\Designation  $designation
     * @param  array $sub_ordinates
     * @return mixed
     */
    public function view(User $user, Designation $designation, $sub_ordinates)
    {
        return ($user->can('access-all-designation') || ($user->can('access-subordinate-designation') && in_array($designation->id, $sub_ordinates)));
    }

    /**
     * Determine whether the user can update the designation.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-designation');
    }

    /**
     * Determine whether the user can delete the designation.
     *
     * @param  \App\User  $user
     * @param  \App\Designation  $designation
     * @return mixed
     */
    public function delete(User $user, Designation $designation)
    {
        return $user->can('delete-designation') && ! $designation->is_hidden;
    }
}
