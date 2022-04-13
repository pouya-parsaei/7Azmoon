<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function updateUserWithPosts(int $id,array $data);
}
