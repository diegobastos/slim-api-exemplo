<?php

/**
 * Arquivo de rota raiz da API
 * Acesso: público
 */

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Illuminate\Database\Capsule\Manager as DB;

$app->get("/", function (Request $request, Response $response) {
    $ret = [
        "status" => 200,
        "message" => "Api Exemplo",
    ];
    
    $response->getBody()->write(json_encode($ret));
    return $response;
});

$app->get('/test-db', function ($request, $response) {
    DB::beginTransaction();
    DB::rollBack();
    $response->getBody()->write(json_encode(['ok' => true]));
    return $response->withHeader('Content-Type', 'application/json');
});