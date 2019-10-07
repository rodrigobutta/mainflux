<?php

namespace App\Policies;

use App\User;
use App\TaskComplexity;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskComplexityPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list all the task complexity.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-task-complexity');
    }

    /**
     * Determine whether the user can view the task complexity.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-task-complexity');
    }

    /**
     * Determine whether the user can create task complexitys.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-task-complexity');
    }

    /**
     * Determine whether the user can update the task complexity.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-task-complexity');
    }

    /**
     * Determine whether the user can delete the task complexity.
     *
     * @param  \App\User  $user
     * @param  \App\TaskComplexity  $item
     * @return mixed
     */
    public function delete(User $user, TaskComplexity $item)
    {
        return $user->can('delete-task-complexity') && ! $item->is_hidden;
    }
}
