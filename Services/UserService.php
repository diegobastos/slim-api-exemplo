<?php

namespace Services;

use Exception;
use Models\User;
use Illuminate\Database\Capsule\Manager as DB;

final class UserService
{

    public static function getAll(int $perPage = 10, int $page = 1)
    {
        $users = User::paginate($perPage, ['*'], 'page', $page);
        return [
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'per_page'     => $users->perPage(),
                'total'        => $users->total(),
                'last_page'    => $users->lastPage(),
                'has_more'     => $users->hasMorePages(),
            ]
        ];        
    }

    public static function getById(int $id): ?User
    {
        return User::find($id);
    }

    public static function getByUsername(string $username): ?User
    {
        return User::where('username', $username)->first();
    }

    public static function create(array $data): User
    {
        if (empty($data['password'])) {
            throw new Exception('Senha obrigatória', 400);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

        try {
            return User::create([
                'name' => $data['name'],
                'username' => $data['username'],
                'email' => $data['email'],
                'password_hash' => $data['password'],
            ]);
        } catch (Exception $e) {
            print_r($e->getMessage());
            die();
        }
    }

    public static function update(int $id, array $data): ?User
    {
        $user = User::find($id);
        if (!$user) {
            throw new Exception('Usuário não encontrado', 404);
        }

        if (isset($data['password'])) {
            $data['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $user->fill($data);
        $user->save();

        return $user;
    }

    public static function delete(int $id): bool
    {
        $user = User::find($id);
        if (!$user) {
            throw new Exception('Usuário não encontrado', 404);
        }

        return $user->delete();
    }

    public static function sanitize(User $user): array
    {
        return [
            'id'    => $user->id,
            'email' => $user->email,
            'name'  => $user->name
        ];
    }    
}
