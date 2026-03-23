<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Commande;
use App\Models\CommandeItem;
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
    public function addOrder(Request $request, Response $response)
    {
        $data = filter_input_array(INPUT_POST, [
            'nom'              => FILTER_SANITIZE_SPECIAL_CHARS,
            'prenom'           => FILTER_SANITIZE_SPECIAL_CHARS,
            'shipping_adresse' => FILTER_SANITIZE_SPECIAL_CHARS,
            'shipping_npa'     => FILTER_SANITIZE_NUMBER_INT,
            'shipping_ville'   => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        $data['user_id'] = $_SESSION['user_id'];
        $data['total']   = array_sum(array_map(fn($i) => $i['prix'] * $i['quantite'], $_SESSION['cart']));
        $data['cart']    = $_SESSION['cart'];

        if (empty($data['nom']) || empty($data['prenom']) || empty($data['shipping_adresse']) || empty($data['shipping_npa']) || empty($data['shipping_ville'])) {
            $_SESSION['flash']['error'] = 'Veuillez remplir tous les champs obligatoires.';
            return $response->withHeader('Location', '/commande/checkout')->withStatus(302);
        }

        if (!is_numeric($data['shipping_npa']) || strlen($data['shipping_npa']) != 4) {
            $_SESSION['flash']['error'] = 'Veuillez entrer un NPA valide (4 chiffres).';
            return $response->withHeader('Location', '/commande/checkout')->withStatus(302);
        }

        if (!preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $data['nom']) || !preg_match('/^[a-zA-ZÀ-ÿ\s\-]+$/', $data['prenom'])) {
            $_SESSION['flash']['error'] = 'Le nom et le prénom ne doivent contenir que des lettres.';
            return $response->withHeader('Location', '/commande/checkout')->withStatus(302);
        }

        $commandeId = Commande::create($data);
        foreach ($data['cart'] as $item) {
           CommandeItem::create([
                'chaussure_id' => $item['id'],
                'taille'       => $item['taille'],
                'quantite'     => $item['quantite'],
                'prix'         => (float) $item['prix'],
            ], $commandeId);
        }


        $_SESSION['cart'] = [];
        $_SESSION['flash']['success'] = 'Votre commande a été passée avec succès !';
        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
