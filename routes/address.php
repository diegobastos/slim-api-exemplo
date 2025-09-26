<?php

use Middlewares\JwtMiddleware;
use Services\AddressService;
use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->group("/address", function (RouteCollectorProxy $app) {

    $app->get("/{id}", function (Request $request, Response $response, array $args) {
        $response = $response->withHeader('Content-Type', 'application/json');
        $query = $request->getQueryParams();

        try {
            if (empty($query['id'])) {
                throw new Exception('ID do endereço ausente', 400);
            }

            $address = AddressService::getById((int) $query['id']);
            if (!$address) {
                throw new Exception('Endereço não encontrado', 404);
            }

            $response->getBody()->write(json_encode($address));
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
            $address = AddressService::getAll($perPage, $page);
            

            $response->getBody()->write(json_encode($address));
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
            $address = AddressService::create($data);
            $response->getBody()->write(json_encode($address));
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
                throw new Exception('ID do endereço ausente', 400);
            }

            $address = AddressService::update($id, $data);
            $response->getBody()->write(json_encode($address));
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
                throw new Exception('ID do endereço ausente', 400);
            }

            AddressService::delete($id);
            $response->getBody()->write(json_encode(['status' => 'Endereço removido']));
            return $response->withStatus(200);

        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'error' => $e->getMessage()
            ]));
            return $response->withStatus($e->getCode() ?: 500);
        }
    });

})->add(new JwtMiddleware());
