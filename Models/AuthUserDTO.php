<?php

namespace Models;

use Models\User;

final class AuthUserDTO
{
    public int $id;
    public string $name;
    public string $email;

    public function __construct(User $user)
    {
        $this->id    = $user->id;
        $this->name  = $user->name;
        $this->email = $user->email;
    }

    public function toArray(): array
    {
        return [
            'id'    => $this->id,
            'name'  => $this->name,
            'email' => $this->email
        ];
    }
}