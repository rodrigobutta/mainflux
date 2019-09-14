<?php

namespace App\Policies;

use App\User;
use App\Contractor;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractorPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list all the contractor.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-contractor');
    }

    /**
     * Determine whether the user can view the contractor.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-contractor');
    }

    /**
     * Determine whether the user can create contractors.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-contractor');
    }

    /**
     * Determine whether the user can update the contractor.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-contractor');
    }

    /**
     * Determine whether the user can delete the contractor.
     *
     * @param  \App\User  $user
     * @param  \App\Contractor  $contractor
     * @return mixed
     */
    public function delete(User $user, Contractor $contractor)
    {
        return $user->can('delete-contractor') && ! $contractor->is_hidden;
    }
}
