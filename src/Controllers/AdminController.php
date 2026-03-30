<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Commande;
use App\Models\User;
use App\Models\Email;
use App\Models\Admin;

class AdminController
{

    public function getAllUsers(Request $request, Response $response)
    {
        $users = Admin::getAllUser();
        $orders = Admin::getAllOrders();

        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Gestion des utilisateurs',
            'users' => $users,
            'order' => $orders
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'admin/users.php');
    }


    public function updateOrderStatus(Request $request, Response $response, array $args)
    {
        $commandeId = (int) $args['id'];
        $userId    = (int) $args['user_id'];
        $data       = $request->getParsedBody();
        $updated    = Admin::updateOrderStatus($commandeId, $data['statut']);

        if ($updated) {


            $user = User::find($userId);


            $email = new Email();
            $email->sendOrderStatusEmail(
                $user['email'],
                $user['prenom'],
                $user['nom'],
                $commandeId,
                $data['statut']
            );

            $_SESSION['flash']['success'] = 'Statut de la commande mis à jour avec succès.';
        } else {
            $_SESSION['flash']['error'] = 'Erreur lors de la mise à jour du statut.';
        }

        return $response->withHeader('Location', '/admin/users')->withStatus(302);
    }


    public function getOrderItems(Request $request, Response $response, array $args)
    {
        $commandeId = (int) $args['id'];
        $items      = Admin::getOrderItemsById($commandeId);

        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title'      => 'Détail de la commande #' . $commandeId,
            'items'      => $items,
            'commandeId' => $commandeId
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'admin/commande-items.php');
    }

    public function deleteUser(Request $request, Response $response, array $args): Response
    {
        $userId = (int) $args['id'];

        $deleted = Admin::deleteUser($userId);


        if ($deleted) {
            $_SESSION['flash']['success'] = 'Utilisateur supprimé avec succès.';
        } else {
            $_SESSION['flash']['error'] = 'Impossible de supprimer cet utilisateur.';
        }

        return $response->withHeader('Location', '/admin/users')->withStatus(302);
    }
}
