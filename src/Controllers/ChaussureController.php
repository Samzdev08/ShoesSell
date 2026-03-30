<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Chaussure;
use App\Models\TailleChaussure;

class ChaussureController
{
    public function __invoke(Request $request, Response $response): Response
    {
        $marque = $_GET['marque'] ?? null;
        $category = $_GET['category'] ?? null;

        $Allchaussures = Chaussure::getAll(10, $category, $marque);

        if ($_SESSION['user_id']) {

            foreach ($Allchaussures as &$chaussure) {
                $chaussure['in_wishlist'] = in_array($chaussure['id'], Chaussure::getWishlistIds($_SESSION['user_id']));
            }
        }

        $view = new PhpRenderer(__DIR__ . '/../Views/layout', [
            'title' => 'Accueil',
            'chaussures' => $Allchaussures,
            'chaussureUser' => $chaussureUser ?? null,
        ]);
        $view->setLayout('index.php');
        return $view->render($response, 'home.php');
    }

    public function details(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'];
        $chaussure = Chaussure::getById($id);
        $sizes = TailleChaussure::getSizeChaussureById($id);

        if (!$chaussure) {
            $response->getBody()->write('Chaussure non trouvée');
            return $response->withStatus(404);
        }

        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => $chaussure['nom'],
            'chaussure' => $chaussure,
            'sizes' => $sizes,
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'chaussure/details.php');
    }

    public function list(Request $request, Response $response): Response
    {

        $marque = $_GET['marque'] ?? null;
        $category = $_GET['category'] ?? null;

        $Allchaussures = Chaussure::getAll(20, $category, $marque);
        if ($_SESSION['user_id']) {

            foreach ($Allchaussures as &$chaussure) {
                $chaussure['in_wishlist'] = in_array($chaussure['id'], Chaussure::getWishlistIds($_SESSION['user_id']));
            }
        }

        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Catalogue',
            'chaussures' => $Allchaussures,
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'chaussure/list.php');
    }
}
