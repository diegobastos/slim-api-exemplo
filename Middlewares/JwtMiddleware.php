<?php

namespace Middlewares;

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Models\AuthUserDTO;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Nyholm\Psr7\Response as NyholmResponse;
use Services\UserService;

final class JwtMiddleware
{
    private static function getSecret(): string
    {
        return $_ENV['JWT_TOKEN'] ?? 'fallback_secret';
    }

    public function __invoke(Request $request, Handler $handler): Response
    {
        $token = $this->extractToken($request);
        if (!$token) {
            return $this->unauthorized('Token ausente ou malformado');
        }

        try {
            $decoded = JWT::decode($token, new Key(self::getSecret(), 'HS256'));

            $user = UserService::getById($decoded->sub);
            if (!$user) {
                throw new Exception("Usuário não encontrado", 401);
            }
            $userDTO = new AuthUserDTO($user);
            $request = $request->withAttribute('auth_user', $userDTO);
            
            return $handler->handle($request);
        } catch (\Firebase\JWT\ExpiredException) {
            return $this->unauthorized('Token expirado');
        } catch (\Firebase\JWT\SignatureInvalidException) {
            return $this->unauthorized('Assinatura inválida');
        } catch (\UnexpectedValueException) {
            return $this->unauthorized('Token inválido');
        } catch (\Throwable $e) {
            return $this->unauthorized('Erro ao validar token');
        }
    }

    private function extractToken(Request $request): ?string
    {
        $authHeader = $request->getHeaderLine('Authorization');
        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function unauthorized(string $message): Response
    {
        $response = new NyholmResponse();
        $response->getBody()->write(json_encode([
            'error' => $message,
            'timestamp' => date('c')
        ]));

        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json');
    }
}
