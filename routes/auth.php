<?php

/**
 * Arquivo de rotas para lidar com o recurso de autenticação de usuários
 */

use Services\AuthService;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->group("/auth", function (RouteCollectorProxy $app){

    $app->post("/login", function (Request $request, Response $response, array $args) {
        $response = $response->withHeader('Content-Type', 'application/json');
        $data = $request->getParsedBody();

        try {
            $token = AuthService::login($data['user'], $data['password']);
            $response->getBody()->write(json_encode(['token' => $token]));
            return $response->withStatus(200);
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response->withStatus($e->getCode() ?: 500);            
        }
    });

    $app->post("/register", function (Request $request, Response $response, array $args) {
        $response = $response->withHeader('Content-Type', 'application/json');
        $data = $request->getParsedBody();

        try {
            $user = AuthService::register($data);
            $token = AuthService::login($user->username, $data['password']);
            $response->getBody()->write(json_encode(['token' => $token]));
            return $response->withStatus(200);
            
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response->withStatus($e->getCode() ?: 500);            
        }
    });
});
