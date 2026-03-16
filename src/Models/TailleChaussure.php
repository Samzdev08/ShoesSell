<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';
use App\Services\Database;
use PDO;

class TailleChaussure
{

    protected static $table = 'taille_chaussure';
    public $id;
    public $id_chaussure;
    public $taille;
    public $stock;

    public function __construct($id, $id_chaussure, $taille, $stock)
    {
        $this->id = $id;
        $this->id_chaussure = $id_chaussure;
        $this->taille = $taille;
        $this->stock = $stock;

    }

    public static function getSizeChaussureById($id_chaussure)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM ' . self::$table . ' WHERE chaussure_id = :id_chaussure');
        $stmt->execute(['id_chaussure' => $id_chaussure]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $results;
    }
}