<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

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
        $stmt = $db->prepare('
        INSERT INTO commandes (user_id, montant, shipping_nom, shipping_prenom, shipping_adresse, shipping_npa, shipping_ville)
        VALUES (:user_id, :total, :nom, :prenom, :adresse, :npa, :ville)
    ');
        $stmt->execute([
            'user_id' => $array['user_id'],
            'total'   => $array['total'],
            'nom'     => $array['nom'],
            'prenom'  => $array['prenom'],
            'adresse' => $array['shipping_adresse'],
            'npa'     => $array['shipping_npa'],
            'ville'   => $array['shipping_ville'],
        ]);
        return $db->lastInsertId();
    }
    public static function recentOrders($id_user, $limit = 5)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('
        SELECT c.*, COUNT(ci.id) AS nb_articles
        FROM commandes c
        LEFT JOIN commande_items ci ON ci.commande_id = c.id
        WHERE c.user_id = :id_user
        GROUP BY c.id
        ORDER BY c.date_commande DESC
        LIMIT :limit
    ');
        $stmt->bindValue('id_user', $id_user, PDO::PARAM_INT);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function orderById($id_user, $order_id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM commandes WHERE user_id = :id_user AND id = :order_id');
        $stmt->execute(['id_user' => $id_user, 'order_id' => $order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
