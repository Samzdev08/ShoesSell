<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

use App\Services\Database;
use PDO;

class Chaussure
{

    protected static $table = 'chaussures';
    public $id;
    public $nom;
    public $prix;
    public $marque;
    public $description;

    public function __construct($id, $nom, $prix, $marque, $description)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prix = $prix;
        $this->marque = $marque;
        $this->description = $description;
    }


    public static function getAll($limit = null, $category = null, $marque = null)
    {
        $params = [];
        $db = Database::getInstance()->getConnection();
        $sql = 'SELECT *, 
        (SELECT COUNT(*) FROM taille_chaussure s 
        WHERE s.chaussure_id = ' . self::$table . '.id 
        AND s.stock >= 1) as en_stock
        FROM ' . self::$table . ' WHERE 1=1';

        if ($category) {
            $sql .= ' AND categorie_id = :category';
            $params[':category'] = (int)$category;
        }
        if ($marque) {
            $sql .= ' AND (marque LIKE :marque OR nom LIKE :marque1)';
            $params[':marque'] = "%$marque%";
            $params[':marque1'] = "%$marque%";
        }

        $sql .= ' ORDER BY created_at DESC';

        if ($limit) {
            $sql .= ' LIMIT :limit';
            $params[':limit'] = (int)$limit;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getById($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
        SELECT c.*, categories.nom AS categorie
        FROM " . self::$table . " c
        LEFT JOIN categories ON c.categorie_id = categories.id
        WHERE c.id = :id ");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result;
    }


    public static function getWishlistIds($user_id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT chaussure_id FROM wishlist WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN); // retourne [1, 3, 6, ...]
    }
}
