<?php

namespace App\Policies;

use App\Announcement;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnnouncementPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can fetch pre-requisites for announcement.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function preRequisite(User $user)
    {
        return ($user->can('list-announcement') || $user->can('create-announcement') || $user->can('edit-announcement'));
    }

    /**
     * Determine whether the user can list all the announcement.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-announcement');
    }

    /**
     * Determine whether the user can create announcements.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-announcement');
    }

    /**
     * Determine whether the user can view the announcement.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-announcement');
    }

    /**
     * Determine whether the user can update the announcement.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-announcement');
    }

    /**
     * Determine whether the user can delete the announcement.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function delete(User $user)
    {
        return $user->can('delete-announcement');
    }
}
