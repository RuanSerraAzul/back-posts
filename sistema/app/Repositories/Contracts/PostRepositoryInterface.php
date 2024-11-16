<?php

namespace App\Repositories\Contracts;

interface PostRepositoryInterface extends RepositoryInterface
{
    public function findByUser(int $userId);
    public function findByTag(string $tag);
} 