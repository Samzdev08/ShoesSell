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

    public static function getAllCategories()
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("SELECT id, nom FROM categories ORDER BY nom ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);              
    }

    public static function checkMedia($file)
    {


        if ($file['size'] == 0 || $file['size'] > 1000000)
            return ['success' => false, 'message' => 'Taille de fichier trop grande (max 1 Mo)'];

        $uploadDir = __DIR__ . '/../../public/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

        $finalName   = time() . '_' . $file['name'];
        $destination = $uploadDir . $finalName;
        $extension   = strtolower(pathinfo($finalName, PATHINFO_EXTENSION));


        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($extension, $allowed)) {
            return ['success' => false, 'message' => 'Extension non autorisée. Formats acceptés :JPG, PNG'];
        }

        $nameParts = explode('.', $finalName);
        if (count($nameParts) !== 2) {
            return ['success' => false, 'message' => 'Nom de fichier invalide (une seule extension autorisée).'];
        }


        $allowedMimes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
        ];
        $realMime = mime_content_type($file['tmp_name']);
        if ($realMime !== $allowedMimes[$extension]) {
            return ['success' => false, 'message' => 'Le contenu du fichier ne correspond pas à son extension.'];
        }

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'message' => 'Échec du déplacement du fichier.'];
        }

        return [
            'success' => true,
            'filename' => '/uploads/' . $finalName
        ];
    }

    public static function createShoes($array)
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
        INSERT INTO chaussures (categorie_id, nom, prix, marque, description, image)
        VALUES (:categorie_id, :nom, :prix, :marque, :description, :image)");

        $stmt->execute([
            'categorie_id' => (int)$array['categorie_id'],
            'nom' => $array['nom'],
            'prix' => $array['prix'],
            'marque' => $array['marque'],
            'description' => $array['description'],
            'image' => $array['image']
        ]);

        $chaussure_id = $pdo->lastInsertId();

        return $chaussure_id;
    }

    public static function addSizes(int $id_chaussure, int $taille, int $stock)
    {
        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare("
        INSERT INTO taille_chaussure (chaussure_id, taille, stock)
        VALUES (:chaussure_id, :taille, :stock)");

        $stmt->execute([
            'chaussure_id' => $id_chaussure,
            'taille' => $taille,
            'stock' => $stock,
        ]);

        return true;
    }


    public static function deleteChaussure(int $id): void
    {
        $pdo = Database::getInstance()->getConnection();


        $stmt = $pdo->prepare("SELECT image FROM chaussures WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $chaussure = $stmt->fetch();


        if ($chaussure && !empty($chaussure['image'])) {
            $chemin = __DIR__ . '/../../public' . $chaussure['image'];
            if (file_exists($chemin)) {
                unlink($chemin);
            }
        }


        $stmt = $pdo->prepare("DELETE FROM commande_items WHERE chaussure_id = :id");
        $stmt->execute(['id' => $id]);

        $stmt = $pdo->prepare("DELETE FROM taille_chaussure WHERE chaussure_id = :id");
        $stmt->execute(['id' => $id]);


        $stmt = $pdo->prepare("DELETE FROM chaussures WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }

    public static function updateShoes(array $data, int $id_chaussure): bool
    {
        $pdo = Database::getInstance()->getConnection();

        $stmt = $pdo->prepare("
        UPDATE chaussures
        SET nom = :nom,
            prix = :prix,
            marque = :marque,
            description = :description,
            image = :image,
            categorie_id = :categorie_id
        WHERE id = :id
    ");

        return $stmt->execute([
            'nom' => $data['nom'],
            'prix' => $data['prix'],
            'marque' => $data['marque'],
            'description' => $data['description'],
            'image' => $data['image'],
            'categorie_id' => (int)$data['categorie_id'],
            'id' => $id_chaussure
        ]);

        

    }

    public static function updateSizes(int $id_chaussure, array $sizes): void
{
    $pdo = Database::getInstance()->getConnection();

    foreach ($sizes as $taille => $stock) {

        $stmt = $pdo->prepare("
            UPDATE taille_chaussure
            SET stock = :stock
            WHERE chaussure_id = :id AND taille = :taille
        ");

        $stmt->execute([
            'stock' => $stock,
            'id' => $id_chaussure,
            'taille' => $taille
        ]);
    }
}
}
