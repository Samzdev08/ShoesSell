<?php
namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class CheckConn
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if (isset($_SESSION['user_id'])) {

            $_SESSION['flash']['error'] = 'Vous etes deja connecter';
            $response = new SlimResponse();
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        return $handler->handle($request);
    }
}