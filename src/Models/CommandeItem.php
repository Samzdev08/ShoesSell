<?php

namespace App\Models;

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
        $stmt = $db->prepare('INSERT INTO commande_items (commande_id, produit_id, quantite, prix_unitaire) VALUES (:commande_id, :produit_id, :quantite, :prix_unitaire)');
        $stmt->execute([
            'commande_id' => $commande_id,
            'produit_id' => $array['produit_id'],
            'quantite' => $array['quantite'],
            'prix_unitaire' => $array['prix_unitaire']
        ]);

        return $db->lastInsertId();
    }
}