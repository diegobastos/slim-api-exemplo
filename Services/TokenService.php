<?php

namespace Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

final class TokenService
{
    private const ALGO = 'HS256';

    private static function getSecretKey(): string
    {
        return $_ENV['JWT_TOKEN'];
    }

    public static function generate(int $userId): string
    {
        $payload = [
            'sub' => $userId,
            'iat' => time(),
            'exp' => time() + $_ENV['TOKEN_EXPIRES']
        ];

        return JWT::encode($payload, self::getSecretKey(), self::ALGO);
    }

    public static function validate(string $token): object
    {
        return JWT::decode($token, new Key(self::getSecretKey(), self::ALGO));
    }
}