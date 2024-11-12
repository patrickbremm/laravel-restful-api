<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

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
    public function canChangePost(User $user, Post $post): Response
    {
        // Permitir atualizar/deletar apenas se o usuário for o autor do post
        return $user->id === $post->user_id
            ? Response::allow()
            : Response::deny('Você não é o criador do Post.');
    }
}
