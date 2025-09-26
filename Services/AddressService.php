<?php

namespace Services;

use Exception;
use Models\Address;
use Illuminate\Database\Capsule\Manager as DB;
use Models\User;

final class AddressService
{

    public static function getAll(int $perPage = 10, int $page = 1)
    {
        $users = Address::paginate($perPage, ['*'], 'page', $page);
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

    public static function getById(int $id): ?Address
    {
        return Address::find($id);
    }

    public static function getByUser(User $user): ?Address
    {
        return Address::where('username', $user)->first();
    }

    public static function create(array $data): Address
    {
        $user = UserService::getById($data['user_id'] ?? 0);

        if (!$user) {
            throw new Exception('Usuário é obrigatório', 400);
        }

        try {
            return Address::create([
                'user_id' => $user->id,
                'street' => $data['street'],
                'num' => $data['num'],
                'complement' => $data['complement'],
                'neighborhood' => $data['neighborhood'],
                'city' => $data['city'],
                'state' => $data['state'],
                'country' => $data['country'],
                'zip_code' => $data['zip_code'],
            ]);
        } catch (Exception $e) {
            print_r($e->getMessage());
            die();
        }
    }

    public static function update(int $id, array $data): ?Address
    {
        $address = Address::find($id);
        if (!$address) {
            throw new Exception('Endereço não encontrado', 404);
        }

        if (isset($data['user_id'])) {
            throw new Exception('Usuário não pode ser alterado');
        }

        $address->fill($data);
        $address->save();

        return $address;
    }

    public static function delete(int $id): bool
    {
        $address = Address::find($id);
        if (!$address) {
            throw new Exception('Endereço não encontrado', 404);
        }

        return $address->delete();
    }
}
