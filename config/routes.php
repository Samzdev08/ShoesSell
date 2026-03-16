<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Controllers\ChaussureController;
use App\Controllers\AuthController;

$app->get('/', ChaussureController::class);
$app->get('/catalogue', [ChaussureController::class, 'list']);
$app->get('/chaussure/{id}',[ChaussureController::class, 'details']);


$group = $app->group('/auth', function ($group) {
    $group->get('/login', [AuthController::class, 'showLoginForm']);
    $group->get('/register', [AuthController::class, 'showRegisterForm']);
    $group->post('/login/post', [AuthController::class, 'login']);
    $group->post('/register/post', [AuthController::class, 'register']);
    $group->post('/logout', [AuthController::class, 'logout']);
});