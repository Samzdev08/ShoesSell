<?php

namespace App\Models;


require_once __DIR__ . '/../../config/database.php';

use App\Services\Database;
use PDO;


class CommandeItem
{
    public $id;
    public $commande_id;
    public $produit_id;
    public $quantite;
    public $prix_unitaire;

    public function __construct($id, $commande_id, $produit_id, $quantite, $prix_unitaire)
    {
        $this->id = $id;
        $this->commande_id = $commande_id;
        $this->produit_id = $produit_id;
        $this->quantite = $quantite;
        $this->prix_unitaire = $prix_unitaire;
    }


    public static function create($array, $commande_id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('INSERT INTO commande_items (commande_id, chaussure_id, taille, quantite, prix) 
                          VALUES (:commande_id, :chaussure_id, :taille, :quantite, :prix)');
        $stmt->execute([
            'commande_id'  => $commande_id,
            'chaussure_id' => $array['chaussure_id'],
            'taille'       => $array['taille'],
            'quantite'     => $array['quantite'],
            'prix'         => $array['prix'],
        ]);

        return $db->lastInsertId();
    }
    public static function getItemsByCommandeId($commande_id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT ci.*, c.nom AS chaussure_nom, c.marque AS chaussure_marque, c.image AS chaussure_image
                              FROM commande_items ci
                              JOIN chaussures c ON ci.chaussure_id = c.id
                              WHERE ci.commande_id = :commande_id');
        $stmt->execute(['commande_id' => $commande_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
