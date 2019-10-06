<?php

namespace App\Policies;

use App\Job;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can fetch pre-requisites for job.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function preRequisite(User $user)
    {
        return ($user->can('list-job') || $user->can('create-job') || $user->can('edit-job'));
    }

    /**
     * Determine whether the user can list all the job.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-job');
    }

    /**
     * Determine whether the user can create jobs.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-job');
    }

    /**
     * Determine whether the user can view the job.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-job');
    }

    /**
     * Determine whether the user can update the job.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-job');
    }

    /**
     * Determine whether the user can delete the job.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->can('delete-job');
    }
}
