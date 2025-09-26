<?php

use Middlewares\JwtMiddleware;
use Services\UserService;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->group("/users", function (RouteCollectorProxy $app) {

    $app->get("/{id}", function (Request $request, Response $response, array $args) {
        $response = $response->withHeader('Content-Type', 'application/json');
        $query = $request->getQueryParams();

        try {
            if (empty($query['id'])) {
                throw new Exception('ID do usuário ausente', 400);
            }

            $user = UserService::getById((int) $query['id']);
            if (!$user) {
                throw new Exception('Usuário não encontrado', 404);
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
            $users = UserService::getAll($perPage, $page);
            

            $response->getBody()->write(json_encode($users));
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
            $user = UserService::create($data);
            $response->getBody()->write(json_encode($user));
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
                throw new Exception('ID do usuário ausente', 400);
            }

            $user = UserService::update($id, $data);
            $response->getBody()->write(json_encode($user));
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
                throw new Exception('ID do usuário ausente', 400);
            }

            UserService::delete($id);
            $response->getBody()->write(json_encode(['status' => 'Usuário removido']));
            return $response->withStatus(200);

        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response->withStatus($e->getCode() ?: 500);
        }
    });

})->add(new JwtMiddleware());
