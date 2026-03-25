<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AuthAdminMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Pas connecté
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté.';
            $response = new SlimResponse();
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }

        // Connecté mais pas admin
        if ($_SESSION['user_role'] !== 'admin') {
            $_SESSION['flash']['error'] = 'Accès réservé aux administrateurs.';
            $response = new SlimResponse();
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $handler->handle($request);
    }
}