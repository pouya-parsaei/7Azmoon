<?php

namespace App\Entities\User;

interface UserEntity
{
    public function getId(): int;
    public function getFullName(): string;
    public function getEmail(): string;
}
