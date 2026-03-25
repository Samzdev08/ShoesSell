<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\User;
use App\Models\Wishlist;

class UserController
{
    public function profile(Request $request, Response $response)
    {
        $user = User::find($_SESSION['user_id']);
        $stats = User::statsByIduser($_SESSION['user_id']);
        $pendingOrders = User::orderPending($_SESSION['user_id']);
        $recentOrders = Commande::recentOrders($_SESSION['user_id']);

        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Mon Profil',
            'user' => $user,
            'stats' => $stats,
            'pendingOrders' => $pendingOrders,
            'recentOrders' => $recentOrders

        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'user/compte.php');
    }

    public function orderDetails(Request $request, Response $response, array $args)
    {
        $orderId = $args['id'];
        $order = Commande::orderById($_SESSION['user_id'], $orderId);
        $user = User::find($_SESSION['user_id']);
        if (!$order) {
            $response->getBody()->write('Commande non trouvée');
            return $response->withStatus(404);
        }
        $items = CommandeItem::getItemsByCommandeId($orderId);
        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Détails de la Commande',
            'order' => $order,
            'user' => $user,
            'items' => $items
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'user/order_details.php');
    }

    public function orderHistory(Request $request, Response $response)
    {
        $orders = Commande::recentOrders($_SESSION['user_id'], 20);
        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Historique des Commandes',
            'orders' => $orders
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'user/order_list.php');
    }

    public function updateProfile(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $id = $_SESSION['user_id'];

        if (empty($data['nom']) || empty($data['prenom']) || empty($data['email']) || empty($data['adresse'])) {
            $_SESSION['flash']['error'] = 'Tous les champs sont requis.';
            return $response->withHeader('Location', '/profil/')->withStatus(302);
        }
        if (preg_match('/[0-9!@#$%^&*()-+]/', $data['nom']) || preg_match('/[0-9!@#$%^&*()-+]/', $data['prenom'])) {
            $_SESSION['flash']['error'] = 'Le nom et le prénom ne doivent pas contenir de chiffres ou de caractères spéciaux.';
            return $response->withHeader('Location', '/profil/')->withStatus(302);
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash']['error'] = 'L\'adresse email n\'est pas valide.';
            return $response->withHeader('Location', '/profil/')->withStatus(302);
        }

        User::updateProfile($id, $data);
        $_SESSION['flash']['success'] = 'Profil mis à jour avec succès.';
        return $response->withHeader('Location', '/profil/')->withStatus(302);
    }

    public function changePassword(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $id = $_SESSION['user_id'];

        if (empty($data['current_password']) || empty($data['new_password']) || empty($data['confirm_password'])) {
            $_SESSION['flash']['error'] = 'Tous les champs sont requis.';
            return $response->withHeader('Location', '/profil/')->withStatus(302);
        }

        if ($data['new_password'] !== $data['confirm_password']) {
            $_SESSION['flash']['error'] = 'Le nouveau mot de passe et la confirmation ne correspondent pas.';
            return $response->withHeader('Location', '/profil/')->withStatus(302);
        }

        if (strlen($data['new_password']) < 6) {
            $_SESSION['flash']['error'] = 'Le nouveau mot de passe doit contenir au moins 6 caractères.';
            return $response->withHeader('Location', '/profil/')->withStatus(302);
        }

        $changeResult = User::changePassword($id, $data['new_password'], $data['current_password']);

        if (!$changeResult['success']) {
            $_SESSION['flash']['error'] = $changeResult['message'];
            return $response->withHeader('Location', '/profil/')->withStatus(302);
        }

        $_SESSION['flash']['success'] = 'Mot de passe changé avec succès.';
        return $response->withHeader('Location', '/profil/')->withStatus(302);
    }

    public function deleteAccount(Request $request, Response $response): Response
    {
        $userId = $_SESSION['user_id'];

        $deleted = User::deleteAccount($userId);

        if ($deleted) {
            
            session_destroy();
            $_SESSION['flash']['success'] = 'Compte supprimée avec succès';
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        $_SESSION['flash']['error'] = 'Erreur lors de la suppression du compte.';
        return $response->withHeader('Location', '/profil/')->withStatus(302);
    }
}
