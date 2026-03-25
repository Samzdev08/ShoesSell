<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash']['error'] = 'Vous devez être connecté pour accéder à cette page.';
            $response = new SlimResponse();
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }

        

        if ($_SESSION['user_role'] === 'admin') {
            $_SESSION['flash']['error'] = 'Vous etes admin t\'a pas le droit mon reuf.';
            $response = new SlimResponse();
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $handler->handle($request);
    }
}
