<?php

namespace Services;

use Exception;
use Models\User;

final class AuthService
{
    public static function login(string $user, string $password): string
    {
        $user = User::where('email', $user)
                      ->orWhere('username', $user)->first();

        if (!$user || !password_verify($password, $user->password_hash)) {
            throw new Exception('Credenciais inválidas', 401);
        }        

        return TokenService::generate($user->id);
    }

    public static function register(array $data): User
    {
        return UserService::create($data);
    }
}
