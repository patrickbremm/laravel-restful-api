<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    // Defina os atributos que podem ser atribuídos em massa
    protected $fillable = ['title', 'author', 'excerpt', 'text', 'user_id'];

    /**
     * Um post pertence a um usuário (autor).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
