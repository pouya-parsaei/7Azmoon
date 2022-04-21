<?php

namespace App\Repositories\Json;

use App\Entities\User\UserEntity;
use App\Entities\User\UserJsonEntity;

class JsonUserRepository extends JsonBaseRepository implements \App\Repositories\Contracts\UserRepositoryInterface
{
    protected string $jsonModel = 'users.json';

    public function updateUserWithPosts(int $id, array $data)
    {
        // TODO: Implement updateUserWithPosts() method.
    }

    public function store(array $data): UserEntity
    {
        $newUser = parent::store($data);
        return new UserJsonEntity($newUser);
    }

    public function find(int $id): UserEntity
    {
        $user = parent::find($id);
        return new UserJsonEntity($user);
    }
}
