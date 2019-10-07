<?php

namespace App\Policies;

use App\User;
use App\TaskFrequency;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskFrequencyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list all the task frequency.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-task-frequency');
    }

    /**
     * Determine whether the user can view the task frequency.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-task-frequency');
    }

    /**
     * Determine whether the user can create task frequencys.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-task-frequency');
    }

    /**
     * Determine whether the user can update the task frequency.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-task-frequency');
    }

    /**
     * Determine whether the user can delete the task frequency.
     *
     * @param  \App\User  $user
     * @param  \App\TaskFrequency  $item
     * @return mixed
     */
    public function delete(User $user, TaskFrequency $item)
    {
        return $user->can('delete-task-frequency') && ! $item->is_hidden;
    }
}
