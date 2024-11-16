<?php

namespace App\Repositories;

use App\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository implements RepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find(int $id)
    {
        return $this->model->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $model = $this->find($id);
        if ($model) {
            $model->update($data);
        }
        return $model;
    }

    public function delete(int $id)
    {
        return $this->model->destroy($id);
    }

    public function paginate(int $perPage = 15)
    {
        return $this->model->paginate($perPage);
    }

    public function paginateWithFilter(string $tag = null, int $perPage = 15)
    {
        $query = $this->model->query();
        
        if ($tag) {
            $query->whereJsonContains('tags', $tag);
        }
        
        return $query->paginate($perPage);
    }
} 