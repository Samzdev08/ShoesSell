<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Commande;
use App\Models\User;

class CommandeController
{
    public function checkout(Request $request, Response $response)
    {
        $user = User::find($_SESSION['user_id']);


        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Checkout',
            'user' => $user,
            'cart' => $_SESSION['cart']
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'panier/checkout.php');
    }
}