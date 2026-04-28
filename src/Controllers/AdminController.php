<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Chaussure;
use App\Models\User;
use App\Models\TailleChaussure;
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
    public function renderResponse(Response $response): Response
    {
        $categories = Admin::getAllCategories();

        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title'    => 'Créer une chaussure',
            'categories' => $categories,
            '$_POST' => $_POST
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'admin/addShoes.php');
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

    public function showShoesList(Request $request, Response $response)
    {
        $categories = Admin::getAllCategories();

        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Gestion des chaussures',
            'categories' => $categories
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'admin/addShoes.php');
    }

    public function addShoesForm(Request $request, Response $response)
    {

        $data = filter_input_array(INPUT_POST, [
            'nom'         => FILTER_SANITIZE_SPECIAL_CHARS,
            'marque'      => FILTER_SANITIZE_SPECIAL_CHARS,
            'description' => FILTER_SANITIZE_SPECIAL_CHARS,
            'prix'        => FILTER_VALIDATE_FLOAT,
            'categorie_id' => FILTER_VALIDATE_INT
        ]);

        $data['stocks'] = $request->getParsedBody()['stocks'] ?? [];




        if (empty($data['nom']) || empty($data['marque']) || empty($data['description']) || empty($data['prix']) || ($data['categorie_id']) === null) {
            $_SESSION['flash']['error'] = 'Veuillez remplir tous les champs';
            return $this->renderResponse($response);
        }
        if (!preg_match('/^[\p{L}0-9\s\-]+$/u', $data['marque'])) {


            $_SESSION['flash']['error'] = 'Veuillez mettre de bon caractère stp pour la marque';
            return $this->renderResponse($response);
        }


        foreach ($data['stocks'] as $taille => $quantite) {
            if ((int)$quantite < 0 || (int)$quantite > 10) {
                $_SESSION['flash']['error'] = 'Veuillez entrer un chiffre valide (max. 10)';
                return $this->renderResponse($response);
            }
        }


        if (empty($_FILES['image']['tmp_name'])) {
            $_SESSION['flash']['error'] = 'Veuillez ajouter une image pour la chaussure';
            return $this->renderResponse($response);
        }

        $checkMedia = Admin::checkMedia($_FILES['image']);
        if (!$checkMedia['success']) {
            $_SESSION['flash']['error'] = $checkMedia['message'];
            return $this->renderResponse($response);
        }

        $data['image'] = $checkMedia['filename'];

        $lastInsertId = Admin::createShoes($data);

        foreach ($data['stocks'] as $taille => $q) {

            $q = (int)$q;

            $isValide = Admin::addSizes($lastInsertId, $taille, $q);
            if (!$isValide) {

                $_SESSION['flash']['error'] = 'Ajout des tailles echouées';
                return $this->renderResponse($response);
            }
        }


        $_SESSION['flash']['success'] = 'Chaussure créé avec succès';

        return $response
            ->withHeader('Location', '/chaussure/' . $lastInsertId)
            ->withStatus(302);
    }

    public function deleteShoes(Request $request, Response $response, $args)
    {


        $id_chaussure = (int)$args['id'];

        $idDelete = Admin::deleteChaussure($id_chaussure);
        var_dump($idDelete);

        $_SESSION['flash']['success'] = 'Chaussure supprimée avec succès.';
        return $response->withHeader('Location', '/catalogue')->withStatus(302);
    }

    public function updateForm(Request $request, Response $response, $args)
    {

        $id = (int)$args['id'];

        $sizes = TailleChaussure::getSizes($id);
        $categories = Admin::getAllCategories();
        $chaussure = Chaussure::getById($id);


        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title'      => 'Modifier une chaussure',
            'categories'      => $categories,
            'chaussure' => $chaussure,
            'sizes' => $sizes
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'chaussure/updateForm.php');
    }

    public function updateFormPost(Request $request, Response $response, $args)
    {   

        $id_chaussure = (int)$args['id'];


        $data = filter_input_array(INPUT_POST, [
            'nom'         => FILTER_SANITIZE_SPECIAL_CHARS,
            'marque'      => FILTER_SANITIZE_SPECIAL_CHARS,
            'description' => FILTER_SANITIZE_SPECIAL_CHARS,
            'prix'        => FILTER_VALIDATE_FLOAT,
            'categorie_id' => FILTER_VALIDATE_INT
        ]);

        $data['stocks'] = $request->getParsedBody()['stocks'] ?? [];




        if (empty($data['nom']) || empty($data['marque']) || empty($data['description']) || empty($data['prix']) || ($data['categorie_id']) === null) {
            $_SESSION['flash']['error'] = 'Veuillez remplir tous les champs';
            return $this->renderResponse($response);
        }
        if (!preg_match('/^[\p{L}0-9\s\-]+$/u', $data['marque'])) {


            $_SESSION['flash']['error'] = 'Veuillez mettre de bon caractère stp pour la marque';
            return $this->renderResponse($response);
        }


        foreach ($data['stocks'] as $taille => $quantite) {
            if ((int)$quantite < 0 || (int)$quantite > 10) {
                $_SESSION['flash']['error'] = 'Veuillez entrer un chiffre valide (min. 0, max. 10)';
                return $this->renderResponse($response);
            }
        }


        if (empty($_FILES['image']['tmp_name'])) {

            $data['image'] = $request->getParsedBody()['image_url'];

        } else {
            $checkMedia = Admin::checkMedia($_FILES['image']);
            if (!$checkMedia['success']) {
                $_SESSION['flash']['error'] = $checkMedia['message'];
                return $this->renderResponse($response);
            }

            $data['image'] = $checkMedia['filename'];
        }

        Admin::updateShoes($data, $id_chaussure);
        Admin::updateSizes($id_chaussure, $data['stocks']);

        $_SESSION['flash']['success'] = 'Chaussure modifiée avec succès';

        return $response
            ->withHeader('Location', '/chaussure/' . $id_chaussure)
            ->withStatus(302);
    }
}
