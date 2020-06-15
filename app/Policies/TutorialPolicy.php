<?php

namespace App\Policies;

use App\Tutorial;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TutorialPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\User  $user
     * @param  \App\Tutorial  $tutorial
     * @return mixed
     */
    public function view(User $user, Tutorial $tutorial)
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\User  $user
     * @param  \App\Tutorial  $tutorial
     * @return mixed
     */
    public function update(User $user, Tutorial $tutorial)
    {
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Tutorial  $tutorial
     * @return mixed
     */
    public function delete(User $user, Tutorial $tutorial)
    {
        return true;
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\User  $user
     * @param  \App\Tutorial  $tutorial
     * @return mixed
     */
    public function restore(User $user, Tutorial $tutorial)
    {
        return true;
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\User  $user
     * @param  \App\Tutorial  $tutorial
     * @return mixed
     */
    public function forceDelete(User $user, Tutorial $tutorial)
    {
        return true;
    }
}
