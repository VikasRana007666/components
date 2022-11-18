<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Post $id)
    {
        return ($user->role === "admin") || ($user->id === $id->user_id)
            ? Response::allow()
            : Response::deny('You do not own this post.');
    }

    public function create(User $user)
    {
        return ($user->role === "admin") || ($user->role === "user")
            ? Response::allow()
            : Response::deny('You Are Not The Admin.');
    }
}
