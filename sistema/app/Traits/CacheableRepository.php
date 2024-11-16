<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheableRepository
{
    protected function getCacheKey(string $method, array $args = []): string
    {
        return sprintf(
            '%s.%s.%s',
            strtolower(class_basename($this)),
            $method,
            md5(serialize($args))
        );
    }

    protected function remember(string $key, \Closure $callback, int $ttl = 3600)
    {
        return Cache::remember($key, $ttl, $callback);
    }

    public function all()
    {
        $key = $this->getCacheKey(__FUNCTION__);
        return $this->remember($key, fn() => parent::all());
    }

    public function find(int $id)
    {
        $key = $this->getCacheKey(__FUNCTION__, [$id]);
        return $this->remember($key, fn() => parent::find($id));
    }
} 