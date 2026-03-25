<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

use App\Services\Database;
use PDO;

class Admin
{

    public static function getAllUser()
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT id, nom, prenom, email, role,  adresse, created_at
            FROM users
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getAllOrders()
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT 
                c.id                AS commande_id,
                c.statut,
                c.montant,
                c.date_commande,
                c.shipping_nom,
                c.shipping_prenom,
                c.shipping_adresse,
                c.shipping_npa,
                c.shipping_ville,
                u.id                AS user_id,
                u.nom               AS user_nom,
                u.prenom            AS user_prenom,
                u.email             AS user_email
            FROM commandes c
            JOIN users u ON u.id = c.user_id
            ORDER BY c.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function updateOrderStatus(int $commandeId, string $statut)
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            UPDATE commandes
            SET statut = :statut
            WHERE id = :id
        ");
        $stmt->execute([
            ':statut' => $statut,
            ':id'     => $commandeId
        ]);
        return $stmt->rowCount();
    }


    public static function getOrderItemsById(int $commandeId)
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
            SELECT 
                c.id                AS commande_id,
                c.statut,
                c.montant,
                c.shipping_nom,
                c.shipping_prenom,
                c.shipping_adresse,
                c.shipping_npa,
                c.shipping_ville,
                ci.id               AS item_id,
                ci.taille,
                ci.quantite,
                ci.prix             AS prix_unitaire,
                (ci.quantite * ci.prix) AS total_ligne,
                ch.nom              AS chaussure_nom,
                ch.marque,
                ch.description
            FROM commandes c
            JOIN commande_items ci  ON ci.commande_id = c.id
            JOIN chaussures ch      ON ch.id = ci.chaussure_id
            WHERE c.id = :id
        ");
        $stmt->execute([':id' => $commandeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function deleteUser(int $userId): bool
    {
        $pdo = Database::getInstance()->getConnection();

      
        $stmt = $pdo->prepare("DELETE FROM commandes WHERE user_id = :id");
        $stmt->execute([':id' => $userId]);

        
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = :id");
        $stmt->execute([':id' => $userId]);

        
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id AND role != 'admin'");
        $stmt->execute([':id' => $userId]);

        return $stmt->rowCount() > 0;
    }
}
