<?php


namespace App\Controllers;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;
use App\Models\Commande;
use App\Models\CommandeItem;
use App\Models\User;
use App\Models\Wishlist;

class WishlistController
{
    public function wishlist(Request $request, Response $response)
    {
        $wishlist = Wishlist::getWishlistByUserId($_SESSION['user_id']);
        $view = new PhpRenderer(__DIR__ . '/../Views', [
            'title' => 'Ma Wishlist',
            'wishlist' => $wishlist
        ]);
        $view->setLayout('layout/index.php');
        return $view->render($response, 'user/wishlist.php');
    }
    public function addToWishlist(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $check = Wishlist::isInWishlist($_SESSION['user_id'], $data['chaussure_id']);
        if (!$check) {
            Wishlist::addToWishlist($_SESSION['user_id'], $data['chaussure_id']);
            $_SESSION['flash']['success'] = 'Produit ajouté à la wishlist avec succès.';
        } else {
            Wishlist::removeFromWishlist($_SESSION['user_id'], $data['chaussure_id']);
            $_SESSION['flash']['error'] = 'Produit retiré de la wishlist.';
        }
        return $response->withHeader('Location', '/')->withStatus(302);

    }
    
    public function removeFromWishlist(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        Wishlist::removeFromWishlist($_SESSION['user_id'], $data['chaussure_id']);
        $_SESSION['flash']['success'] = 'Produit retiré de la wishlist avec succès.';
        return $response->withHeader('Location', '/wishlist/')->withStatus(302);
    }
}