<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Controllers\ChaussureController;
use App\Controllers\AuthController;
use App\Controllers\PanierController;
use App\Controllers\CommandeController;

$app->get('/', ChaussureController::class);
$app->get('/catalogue', [ChaussureController::class, 'list']);
$app->get('/chaussure/{id}',[ChaussureController::class, 'details']);


$group = $app->group('/auth', function ($group) {
    $group->get('/login', [AuthController::class, 'showLoginForm']);
    $group->get('/register', [AuthController::class, 'showRegisterForm']);
    $group->post('/login/post', [AuthController::class, 'login']);
    $group->post('/register/post', [AuthController::class, 'register']);
    $group->get('/logout', [AuthController::class, 'logout']);
});

$group = $app->group('/panier', function ($group) {
    $group->post('/ajouter', [PanierController::class, 'addCart']);
    $group->get('/', [PanierController::class, 'viewCart']);
    $group->get('/remove/{id}', [PanierController::class, 'removeFromCart']);
    $group->get('/vider', [PanierController::class, 'clearCart']);
})->add(new App\Middleware\AuthMiddleware());

$app->get('/commande/checkout', [CommandeController::class, 'checkout'])->add(new App\Middleware\AuthMiddleware());