<?php

namespace App\Models;

use App\Services\Database;
use PDO;

class Commande
{
    public $id;
    public $user_id;
    public $total;
    public $created_at;

    public function __construct($id, $user_id, $total, $created_at)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->total = $total;
        $this->created_at = $created_at;
    }

    public static function create($array)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('INSERT INTO commandes (user_id, total) VALUES (:user_id, :total)');
        $stmt->execute([
            'user_id' => $array['user_id'],
            'total' => $array['total']
        ]);

        return $db->lastInsertId();
    }
}