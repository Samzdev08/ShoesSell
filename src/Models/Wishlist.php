<?php

namespace App\Models;
require_once __DIR__ . '/../../config/database.php';
use App\Services\Database;
use PDO;

class Wishlist
{
    public $id;
    public $user_id;
    public $chaussure_id;

    public function __construct($id, $user_id, $chaussure_id)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->chaussure_id = $chaussure_id;
    }

    public static function addToWishlist($user_id, $chaussure_id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('INSERT INTO wishlist (user_id, chaussure_id) VALUES (:user_id, :chaussure_id)');
        $stmt->execute([
            'user_id' => $user_id,
            'chaussure_id' => $chaussure_id
        ]);

        return $db->lastInsertId();
    }
    public static function isInWishlist($user_id, $chaussure_id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM wishlist WHERE user_id = :user_id AND chaussure_id = :chaussure_id');
        $stmt->execute([
            'user_id' => $user_id,
            'chaussure_id' => $chaussure_id
        ]);

        return $stmt->rowCount() > 0;
    }
    
    public static function removeFromWishlist($user_id, $chaussure_id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('DELETE FROM wishlist WHERE user_id = :user_id AND chaussure_id = :chaussure_id');
        $stmt->execute([
            'user_id' => $user_id,
            'chaussure_id' => $chaussure_id
        ]);

        return $stmt->rowCount() > 0;
    }

    public static function getWishlistByUserId($id_user)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT w.*, c.nom AS chaussure_nom, c.prix AS chaussure_prix, c.marque AS chaussure_marque, cat.nom AS chaussure_categorie

         FROM wishlist w
         JOIN chaussures c ON w.chaussure_id = c.id
         JOIN categories cat ON c.categorie_id = cat.id
         WHERE w.user_id = :id_user ORDER BY w.id DESC');

        $stmt->execute(['id_user' => $id_user]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}