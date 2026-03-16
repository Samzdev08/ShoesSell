<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

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
        $data = $request->getParsedBody();


        $username = $data['username'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $password_verify = $data['password_verify'] ?? '';

        $_SESSION['success'] = 'Votre compte a été créé avec succès. Vous pouvez maintenant vous connecter.';
        return $response->withHeader('Location', '/auth/login')->withStatus(302);
    }


    public function logout(Request $request, Response $response)
    {
        session_destroy();
        $_SESSION['success'] = 'Vous avez été déconnecté avec succès.';
        return $response->withHeader('Location', '/')->withStatus(302);
    }
}
