<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;

class EloquentBaseRepository implements RepositoryInterface
{
    protected $model;

    public function store(array $data)
    {
        return $this->model::create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->model::where('id', $id)->first()->update($data);
    }

    public function all(array $where)
    {
        $query = $this->model::query();

        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }

        return $query->get();
    }

    public function find(int $id)
    {
        return $this->model::find($id);
    }

    public function delete(int $id):bool
    {
     return $this->model::destroy($id);
    }

    public function deleteBy(array $where)
    {
        $query = $this->model::query();

        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }

        return $query->delete();

    }

    public function paginate(int $page, int $pageSize = 20, string $search = null):array
    {
        if(is_null($search)){
            return $this->model::paginate($pageSize,['full_name','mobile','email'],null,$page)->toArray();
        }

        return $this->model::orWhere('full_name',$search)
            ->orWhere('mobile',$search)
            ->orWhere('email',$search)
        ->paginate($pageSize,['full_name','mobile','email'],null,$page)->toArray();

    }
}
