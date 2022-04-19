<?php

namespace App\Entities\User;

class UserJsonEntity implements UserEntity
{

    public function __construct(private array|null $user)
    {

    }

    public function getId(): int
    {
        return $this->user['id'];
    }

    public function getFullName(): string
    {
        return $this->user['full_name'];
    }

    public function getEmail(): string
    {
        return $this->user['email'];
    }
}
