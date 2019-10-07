<?php

namespace App\Policies;

use App\User;
use App\TaskFamily;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskFamilyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list all the task family.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-task-family');
    }

    /**
     * Determine whether the user can view the task family.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-task-family');
    }

    /**
     * Determine whether the user can create task familys.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-task-family');
    }

    /**
     * Determine whether the user can update the task family.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-task-family');
    }

    /**
     * Determine whether the user can delete the task family.
     *
     * @param  \App\User  $user
     * @param  \App\TaskFamily  $item
     * @return mixed
     */
    public function delete(User $user, TaskFamily $item)
    {
        return $user->can('delete-task-family') && ! $item->is_hidden;
    }
}
