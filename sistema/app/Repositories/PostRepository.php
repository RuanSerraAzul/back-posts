<?php

namespace App\Repositories;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;

class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    public function __construct(Post $model)
    {
        parent::__construct($model);
    }

    public function findByUser(int $userId)
    {
        return $this->model->where('user_id', $userId)->get();
    }

    public function findByTag(string $tag)
    {
        return $this->model->whereJsonContains('tags', $tag)->get();
    }

    public function search(string $term)
    {
        return $this->model
            ->where('title', 'like', "%{$term}%")
            ->orWhere('content', 'like', "%{$term}%")
            ->orWhere('author', 'like', "%{$term}%")
            ->orWhereJsonContains('tags', $term)
            ->paginate();
    }
} 