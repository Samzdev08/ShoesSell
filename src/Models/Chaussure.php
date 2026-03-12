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
        $stmt = $db->query('SELECT * FROM ' . self::$table);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }
}