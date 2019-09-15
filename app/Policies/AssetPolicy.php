<?php

namespace App\Policies;

use App\User;
use App\Asset;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can fetch pre-requisites for asset.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function preRequisite(User $user)
    {
        return ($user->can('create-asset') || $user->can('edit-asset'));
    }

    /**
     * Determine whether the user can list all the asset.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function list(User $user)
    {
        return $user->can('list-asset');
    }

    /**
     * Determine whether the user can create assets.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->can('create-asset');
    }

    /**
     * Determine whether the user can view the asset.
     *
     * @param  \App\User  $user
     * @param  \App\Asset  $asset
     * @param  array $sub_ordinates
     * @return mixed
     */
    public function view(User $user, Asset $asset, $sub_ordinates)
    {
        return ($user->can('access-all-asset') || ($user->can('access-subordinate-asset') && in_array($asset->id, $sub_ordinates)));
    }

    /**
     * Determine whether the user can update the asset.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function update(User $user)
    {
        return $user->can('edit-asset');
    }

    /**
     * Determine whether the user can delete the asset.
     *
     * @param  \App\User  $user
     * @param  \App\Asset  $asset
     * @return mixed
     */
    public function delete(User $user, Asset $asset)
    {
        return $user->can('delete-asset') && ! $asset->is_hidden;
    }
}
