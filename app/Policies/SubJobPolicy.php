<?php

namespace App\Policies;

use App\SubJob;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubJobPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create jobs.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-sub-job');
    }

    /**
     * Determine whether the user can update the job.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-sub-job');
    }

    /**
     * Determine whether the user can delete the job.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->can('delete-sub-job');
    }
}
