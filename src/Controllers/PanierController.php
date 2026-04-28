<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;


class PanierController
{
    public function addCart(Request $request, Response $response)
    {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

        $data = $request->getParsedBody();

        $errors = [];

        if ($data['quantite'] <= 0) {
            $errors[] = 'La quantité doit être supérieure à zéro.';
        }

        if ($data['taille'] == '' || $data['taille'] == null) {
            $errors[] = 'Veuillez sélectionner une taille.';
        }

        if (!empty($errors)) {

            $_SESSION['flash']['error'] = $errors[0];
            return $response->withHeader('Location', '/chaussure/' . $data['chaussure_id'])->withStatus(302);
        }

        $product = [
            'id' => $data['chaussure_id'],
            'nom' => $data['nom'],
            'image' => $data['image'],
            'prix' => $data['prix'],
            'taille' => $data['taille'],
            'marque' => $data['marque'],
            'quantite' => $data['quantite']
        ];

        $_SESSION['cart'][] = $product;
        $_SESSION['flash']['success'] = 'Produit ajouté au panier avec succès.';
        return $response->withHeader('Location', '/panier/')->withStatus(302);
    }

    public function viewCart(Request $request, Response $response)
    {
        $_SESSION['cart'] = $_SESSION['cart'] ?? [];


        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Mon Panier',
            'cart' => $_SESSION['cart'],
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'panier/cart.php');
    }

    public function clearCart(Request $request, Response $response)
    {
        $_SESSION['cart'] = [];
        $_SESSION['flash']['success'] = 'Panier vidé avec succès.';
        return $response->withHeader('Location', '/panier/')->withStatus(302);
    }

    public function removeFromCart(Request $request, Response $response, $args)
    {

        $id = $args['id'];
        unset($_SESSION['cart'][$id]);
        $_SESSION['flash']['success'] = 'Produit retiré du panier avec succès.';
        return $response->withHeader('Location', '/panier/')->withStatus(302);
    }

    public function Maj(Request $request, Response $response)
    {

        $data = $request->getParsedBody();
        $newQuantite = $data['quantite'];

        if ($newQuantite >= 5) {

            $_SESSION['flash']['error'] = 'Cheh.';
            return $response->withHeader('Location', '/panier/')->withStatus(302);
        }

        $i = $data['id'];

        foreach ($_SESSION['cart'] as &$item) {


            $_SESSION['cart'][$i]['quantite'] = $newQuantite;
        }

        $_SESSION['flash']['success'] = 'Quantité modifiée avec succès.';
        return $response->withHeader('Location', '/panier/')->withStatus(302);
    }
}
