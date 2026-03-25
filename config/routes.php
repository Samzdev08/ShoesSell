<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Controllers\ChaussureController;
use App\Controllers\AuthController;
use App\Controllers\PanierController;
use App\Controllers\UserController;
use App\Controllers\CommandeController;
use App\Controllers\WishlistController;
use App\Controllers\AdminController;

$app->get('/', ChaussureController::class);
$app->get('/catalogue', [ChaussureController::class, 'list']);
$app->get('/chaussure/{id}', [ChaussureController::class, 'details']);


$group = $app->group('/auth', function ($group) {
    $group->get('/login', [AuthController::class, 'showLoginForm'])->add(new App\Middleware\CheckConn());
    $group->get('/register', [AuthController::class, 'showRegisterForm'])->add(new App\Middleware\CheckConn());
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
$app->post('/commande/add', [CommandeController::class, 'addOrder'])->add(new App\Middleware\AuthMiddleware());
$app->get('/commande/{id}/facture', [CommandeController::class, 'facture'])->add(new App\Middleware\AuthMiddleware());

$group = $app->group('/profil', function ($group) {
    $group->get('/', [UserController::class, 'profile']);
    $group->post('/delete', [UserController::class, 'deleteAccount']);
    $group->post('/update', [UserController::class, 'updateProfile']);
    $group->post('/update-password', [UserController::class, 'changePassword']);
    $group->get('/orders', [UserController::class, 'orderHistory']);
    $group->get('/orders/{id}', [UserController::class, 'orderDetails']);
    $group->get('/delete', [UserController::class, 'deleteAccount']);
})->add(new App\Middleware\AuthMiddleware());

$group = $app->group('/wishlist', function ($group) {

    $group->get('/', [WishlistController::class, 'wishlist']);
    $group->post('/add', [WishlistController::class, 'addToWishlist']);
    $group->post('/remove', [WishlistController::class, 'removeFromWishlist']);

})->add(new App\Middleware\AuthMiddleware());

$app->group('/admin', function ($group) {
    $group->get('/users', [AdminController::class, 'getAllUsers']);
    $group->post('/commandes/{id}/statut', [AdminController::class, 'updateOrderStatus']);
    $group->get('/commandes/{id}/items', [AdminController::class, 'getOrderItems']);
    $group->post('/admin/users/{id}/delete', [AdminController::class, 'deleteUser']);
})->add(new App\Middleware\AuthAdminMiddleware());
