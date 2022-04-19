<?php

namespace App\Entities\User;

use App\Models\User;

class UserEloquentEntity implements UserEntity
{
    public function __construct(private User $user)
    {

    }

    public function getId(): int
    {
        return $this->user->id;
    }

    public function getFullName(): string
    {
        return $this->user->full_name;
    }

    public function getEmail(): string
    {
        return $this->user->email;
    }
}
