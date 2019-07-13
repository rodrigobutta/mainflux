<?php

namespace App\Policies;

use App\SubTask;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubTaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can create tasks.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-sub-task');
    }

    /**
     * Determine whether the user can update the task.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-sub-task');
    }

    /**
     * Determine whether the user can delete the task.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->can('delete-sub-task');
    }
}
