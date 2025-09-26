<?php

namespace Middlewares;

use Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Nyholm\Psr7\Response as NyholmResponse;

final class AuthMiddleware
{
    public function __invoke(Request $request, Handler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization'); 
        $token = str_replace('Bearer ', '', $authHeader);

        if (empty($token)) {
            return $this->unauthorizedResponse('Token ausente');
        }

        $user = $this->getUserByToken($token);

        if (!$user) {
            return $this->unauthorizedResponse('Token inválido ou expirado');
        }

        $request = $request->withAttribute('auth_user', $user);

        return $handler->handle($request);
    }

    private function getUserByToken(string $token): ?User
    {
        return User::where('validator', $token)
            ->where('is_active', 1)
            ->first();
    }

    private function unauthorizedResponse(string $message): Response
    {
        $response = new NyholmResponse();
        $response->getBody()->write(json_encode(['error' => $message]));
        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }
}
