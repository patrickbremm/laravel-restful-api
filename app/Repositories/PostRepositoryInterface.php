<?php

namespace App\Repositories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;

interface PostRepositoryInterface
{
    public function all(): Collection;
    public function create(array $data): Post;
    public function find(int $id): Post;
    public function update(Post $post, array $data): bool;
    public function delete(Post $post): bool;
}