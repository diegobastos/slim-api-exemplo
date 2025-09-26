<?php

use Middlewares\JwtMiddleware;
use Services\AddressService;
use Services\TaskService;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->group("/tasks", function (RouteCollectorProxy $app) {

    $app->get("/{id}", function (Request $request, Response $response, array $args) {
        $response = $response->withHeader('Content-Type', 'application/json');
        $query = $request->getQueryParams();

        try {
            if (empty($query['id'])) {
                throw new Exception('ID da tarefa ausente', 400);
            }

            $user = TaskService::getById((int) $query['id']);
            if (!$user) {
                throw new Exception('Tarefa não encontrada', 404);
            }

            $response->getBody()->write(json_encode($user));
            return $response->withStatus(200);

        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response->withStatus($e->getCode() ?: 500);
        }
    });

    $app->get("", function (Request $request, Response $response, array $args) {
        $response = $response->withHeader('Content-Type', 'application/json');
        $page = $request->getQueryParams()['page'] ?? 1;
        $perPage = $request->getQueryParams()['ipage'] ?? 10;

        try {
            $tasks = TaskService::getAll($perPage, $page);

            $response->getBody()->write(json_encode($tasks));
            return $response->withStatus(200);

        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response->withStatus($e->getCode() ?: 500);
        }
    });  

    $app->post("", function (Request $request, Response $response, array $args) {
        $response = $response->withHeader('Content-Type', 'application/json');
        $data = $request->getParsedBody();

        try {
            $tasks = TaskService::create($data);
            $response->getBody()->write(json_encode($tasks));
            return $response->withStatus(201);

        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response->withStatus($e->getCode() ?: 500);
        }
    });

    $app->patch("/{id}", function (Request $request, Response $response, array $args) {
        $response = $response->withHeader('Content-Type', 'application/json');
        $data = $request->getParsedBody();
        $id = $args['id'] ?? 0;

        try {
            if ($id == 0) {
                throw new Exception('ID da tarefa ausente', 400);
            }

            $task = TaskService::update($id, $data);
            $response->getBody()->write(json_encode($task));
            return $response->withStatus(200);

        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response->withStatus($e->getCode() ?: 500);
        }
    });

    $app->delete("/{id}", function (Request $request, Response $response, array $args) {
        $response = $response->withHeader('Content-Type', 'application/json');
        $id = $args['id'] ?? 0;

        try {
            if ($id == 0) {
                throw new Exception('ID da tarefa ausente', 400);
            }

            TaskService::delete($id);
            $response->getBody()->write(json_encode(['status' => 'Tarefa removida']));
            return $response->withStatus(200);

        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response->withStatus($e->getCode() ?: 500);
        }
    });

})->add(new JwtMiddleware());
