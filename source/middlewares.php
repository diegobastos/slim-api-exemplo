<?php

use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpMethodNotAllowedException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

return function ($app) {
    /**
     * Middleware para fazer o parser do corpo da requisição
     */
    $app->addBodyParsingMiddleware();

    /**
     * Middleware de roteamento
     */
    $app->addRoutingMiddleware();

    /**
     * Middleware de erro padrão
     */
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
    
    /**
     * Tratamento genérico de exceções
     */
    $app->add(function (Request $request, RequestHandlerInterface $handler): Response {
        try {
            return $handler->handle($request);
        } catch (Throwable $e) {
            $response = new \Nyholm\Psr7\Response();
            $response = $response->withHeader('Content-Type', 'application/json');
    
            $error = [
                'status' => 500,
                'message' => 'Erro interno no servidor.',
                'details' => $e->getMessage(),
            ];
    
            $jsonError = json_encode($error, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $response->getBody()->write($jsonError);
            return $response->withStatus(500);
        }
    });

    /**
     * Tratamento de rotas não encontradas com JSON customizado
     */
    $errorMiddleware->setErrorHandler(HttpNotFoundException::class, function (Request $request, Throwable $exception, bool $displayErrorDetails) {
        $response = new \Nyholm\Psr7\Response();
        $response = $response->withHeader('Content-Type', 'application/json');
        
        $ret = [
            "status" => 404,
            "message" => "Não encontrado",
            "data" => $request->getUri()->getPath()
        ];
        $response->getBody()->write(json_encode($ret));
        return $response->withStatus(404);
    });

    /**
     * Tratamento de métodos HTTP não permitidos (405 Method Not Allowed)
     */
    $errorMiddleware->setErrorHandler(HttpMethodNotAllowedException::class, function (Request $request, Throwable $exception, bool $displayErrorDetails) {
        $response = new \Nyholm\Psr7\Response();
        $response = $response->withHeader('Content-Type', 'application/json');
        
        $ret = [
            "status" => 405,
            "message" => "Método não permitido",
            "method" => $request->getMethod(),
            "data" => $request->getUri()->getPath()
        ];
        $response->getBody()->write(json_encode($ret));
        return $response->withStatus(405);
    });
};
