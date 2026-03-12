<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Chaussure;

class ChaussureController
{
    public function __invoke(Request $request, Response $response): Response
    {
        $Allchaussures = Chaussure::getAll();

        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Accueil',
            'chaussures' => $Allchaussures,
        ]);
        return $view->render($response, 'layout/index.php');
    }
}