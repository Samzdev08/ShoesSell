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


    public static function getAll()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query('SELECT * FROM ' . self::$table . ' ORDER BY id DESC LIMIT 10');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
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

    public static function getList()
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->query('SELECT * FROM ' . self::$table . ' ORDER BY id DESC');
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }
}
