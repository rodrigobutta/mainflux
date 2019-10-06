<?php

namespace App\Policies;

use App\User;
use App\TaskRelevance;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskRelevancePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list all the task relevance.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-task-relevance');
    }

    /**
     * Determine whether the user can view the task relevance.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-task-relevance');
    }

    /**
     * Determine whether the user can create task relevances.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-task-relevance');
    }

    /**
     * Determine whether the user can update the task relevance.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-task-relevance');
    }

    /**
     * Determine whether the user can delete the task relevance.
     *
     * @param  \App\User  $user
     * @param  \App\TaskRelevance  $item
     * @return mixed
     */
    public function delete(User $user, TaskRelevance $item)
    {
        return $user->can('delete-task-relevance') && ! $item->is_hidden;
    }
}
