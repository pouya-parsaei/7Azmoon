<?php

namespace App\Repositories\Eloquent;

use App\Entities\User\UserEloquentEntity;
use App\Entities\User\UserEntity;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;

class EloquentUserRepository extends EloquentBaseRepository implements UserRepositoryInterface
{
    protected $model = User::class;

    public function updateUserWithPosts(int $id, array $data)
    {
        // TODO
    }

    public function store(array $data): UserEntity
    {
        $newUser = parent::store($data);
        return new UserEloquentEntity($newUser);
    }

    public function update(int $id, array $data): UserEntity
    {
        return !parent::update($id, $data) ? throw new \Exception('خطا در بروزرسانی اطلاعات') :
            new UserEloquentEntity(parent::find($id));

    }


}
