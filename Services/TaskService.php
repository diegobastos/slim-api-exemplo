<?php

namespace Services;

use Exception;
use Illuminate\Database\Capsule\Manager as DB;
use Models\Task;
use Models\User;

final class TaskService
{

    public static function getAll(int $perPage = 10, int $page = 1)
    {
        $users = Task::paginate($perPage, ['*'], 'page', $page);
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

    public static function getById(int $id): ?Task
    {
        return Task::find($id);
    }

    public static function getByUser(User $user): ?Task
    {
        return Task::where('username', $user)->first();
    }

    public static function create(array $data): Task
    {
        $user = UserService::getById($data['user_id'] ?? 0);

        if (!$user) {
            throw new Exception('Usuário é obrigatório', 400);
        }

        try {
            return Task::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'description' => $data['description'],
                'is_completed' => $data['is_completed'] ?? 0,
            ]);
        } catch (Exception $e) {
            print_r($e->getMessage());
            die();
        }
    }

    public static function update(int $id, array $data): ?Task
    {
        $task = Task::find($id);
        if (!$task) {
            throw new Exception('Tarefa não encontrada', 404);
        }

        if (isset($data['user_id'])) {
            throw new Exception('Usuário não pode ser alterado');
        }

        $task->fill($data);
        $task->save();

        return $task;
    }

    public static function delete(int $id): bool
    {
        $task = Task::find($id);
        if (!$task) {
            throw new Exception('Tarefa não encontrada', 404);
        }

        return $task->delete();
    }
}
