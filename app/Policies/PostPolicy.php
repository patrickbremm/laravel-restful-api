<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    // Verifica se o usuário pode deletar o post
    public function delete(User $user, Post $post)
    {
        // Permitir deletar apenas se o usuário for o autor do post
        return $user->id === $post->user_id;
    }
}
