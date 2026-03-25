<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

use App\Models\User;

class AuthController
{
    public function renderResponse(Response $response, string $error, array $old_post = [], string $file =  'register')
    {
        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Inscription',
            'error' => $error ?? null,
            'old_post' => $old_post,
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'auth/' .$file. '.php');
    }

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
        $data = filter_input_array(INPUT_POST, [
            'email' => FILTER_SANITIZE_EMAIL,
            'password' => FILTER_SANITIZE_SPECIAL_CHARS,
        ]);

        $errors = [];

        if (empty($data['email']) || empty($data['password'])) {
            $errors[] = 'Tous les champs sont obligatoires.';
        }

        if (!empty($errors)) {

           return $this->renderResponse($response, $errors[0], $_POST, 'login');
        }

        $loginResult = User::login($data);

        if (!$loginResult['success']) {
            return $this->renderResponse($response, $loginResult['message'], $_POST, 'login');
        }

        $_SESSION['user_id'] = $loginResult['user']['id'];
        $_SESSION['user_role'] = $loginResult['user']['role'];
        $_SESSION['flash']['success'] = 'Vous êtes connecté avec succès.';
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
            return $this->renderResponse($response, $errors[0], $_POST);
        }


        if (User::verifyEmail($data['email'])) {
            return $this->renderResponse($response, 'Cette adresse email est déjà utilisée.', $_POST);
        }


        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $lastInsertId = User::create($data);


        if (!$lastInsertId) {
            return $this->renderResponse($response, 'Une erreur est survenue lors de la création du compte.', $_POST);
        }


        $_SESSION['flash']['success'] = 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.';
        return $response->withHeader('Location', '/auth/login')->withStatus(302);
    }


    public function logout(Request $request, Response $response)
    {

        $_SESSION['flash']['success'] = 'Vous avez été déconnecté avec succès.';
        session_destroy();
        session_start();
        $_SESSION['flash']['success'] = 'Vous avez été déconnecté avec succès.';
        return $response->withHeader('Location', '/')->withStatus(302);
    }

}
