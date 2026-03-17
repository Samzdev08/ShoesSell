<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

use App\Models\User;

class AuthController
{

    public function showLoginForm(Request $request, Response $response)
    {

        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Connexion',
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'auth/login.php');
    }

    public function showRegisterForm(Request $request, Response $response)
    {
        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Inscription',
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'auth/register.php');
    }

    public function login(Request $request, Response $response)
    {


        $_SESSION['success'] = 'Vous êtes connecté avec succès.';
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    public function register(Request $request, Response $response)
    {

        $data = filter_input_array(INPUT_POST, [
            'nom' => FILTER_SANITIZE_SPECIAL_CHARS,
            'prenom' => FILTER_SANITIZE_SPECIAL_CHARS,
            'adresse' => FILTER_SANITIZE_SPECIAL_CHARS,
            'email' => FILTER_SANITIZE_EMAIL,
            'password' => FILTER_SANITIZE_SPECIAL_CHARS,
            'password_verify' => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        $errors = [];

        if (empty($data['nom']) || empty($data['prenom']) || empty($data['adresse']) || empty($data['email']) || empty($data['password']) || empty($data['password_verify'])) {
            $errors[] = 'Tous les champs sont obligatoires.';
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'L\'adresse email n\'est pas valide.';
        }

        if (strlen($data['password']) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }

        if (preg_match('/[0-9!@#$%^&*()-+]/', $data['nom'])) {
            $errors[] = 'Le nom ne doit pas contenir de chiffres ou de caractères spéciaux.';
        }

        if (preg_match('/[0-9!@#$%^&*()-+]/', $data['prenom'])) {
            $errors[] = 'Le prénom ne doit pas contenir de chiffres ou de caractères spéciaux.';
        }


        if ($data['password'] !== $data['password_verify']) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        if (!empty($errors)) {

        var_dump($errors);

            $view = new PhpRenderer(__DIR__ . '/../Views', [
                'title' => 'Inscription',
                'error' => $errors[0],
                'old_post' => $_POST,
            ]);
            $view->setLayout('layout/index.php');
            return $view->render($response, 'auth/register.php');

        } else {

            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            $lastInsertId = User::create($data);

            if (!$lastInsertId) {
                $view = new PhpRenderer(__DIR__ . '/../Views', [
                    'title' => 'Inscription',
                    'error' => 'Une erreur est survenue lors de la création du compte.',
                    'old_post' => $_POST,
                ]);
                $view->setLayout('layout/index.php');
                return $view->render($response, 'auth/register.php');
            } 
            else {
                $_SESSION['flash']['success'] = 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.';
                return $response->withHeader('Location', '/auth/login')->withStatus(302);
            }
        }
    }


    public function logout(Request $request, Response $response)
    {
        session_destroy();
        $_SESSION['success'] = 'Vous avez été déconnecté avec succès.';
        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
