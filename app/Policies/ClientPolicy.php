<?php

namespace App\Policies;

use App\User;
use App\Client;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can list all the client.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-client');
    }

    /**
     * Determine whether the user can view the client.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function view(User $user)
    {
        return $user->can('list-client');
    }

    /**
     * Determine whether the user can create clients.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-client');
    }

    /**
     * Determine whether the user can update the client.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-client');
    }

    /**
     * Determine whether the user can delete the client.
     *
     * @param  \App\User  $user
     * @param  \App\Client  $client
     * @return mixed
     */
    public function delete(User $user, Client $client)
    {
        return $user->can('delete-client') && ! $client->is_hidden;
    }
}
