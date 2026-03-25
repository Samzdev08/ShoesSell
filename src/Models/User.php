<?php

namespace App\Models;

require_once __DIR__ . '/../../config/database.php';

use App\Services\Database;
use PDO;

class User
{

    protected static $table = 'users';
    public $id;
    public $nom;
    public $prenom;
    public $adresse;
    public $email;
    public $password;

    public function __construct($id, $nom, $prenom, $adresse, $email, $password)
    {
        $this->id = $id;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->adresse = $adresse;
        $this->email = $email;
        $this->password = $password;
    }

    public static function  verifyEmail($email)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM ' . self::$table . ' WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() > 0) {
            return true;
        }
        return false;
    }

    public static function create($array)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('INSERT INTO ' . self::$table . ' (nom, prenom, email, adresse, mot_de_passe) VALUES (:nom, :prenom, :email, :adresse, :password)');
        $stmt->execute([
            'nom' => $array['nom'],
            'prenom' => $array['prenom'],
            'email' => $array['email'],
            'adresse' => $array['adresse'],
            'password' => $array['password']
        ]);

        return $db->lastInsertId();
    }

    public static function find($id)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM ' . self::$table . ' WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public static function login($array)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT * FROM ' . self::$table . ' WHERE email = :email');
        $stmt->execute(['email' => $array['email']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            if (password_verify($array['password'], $user['mot_de_passe'])) {

                return ['success' => true, 'user' => $user];
            }
            return ['success' => false, 'message' => 'Mot de passe incorrect.'];
        }
        return ['success' => false, 'message' => 'Adresse email non trouvée.',];
    }

    public static function statsByIduser($id_user)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('
        SELECT 
            COUNT(c.id) AS order_count,
            COALESCE(SUM(c.montant), 0) AS total_spent,
            COALESCE(w.count, 0) AS wishlist_count
        FROM commandes c
        LEFT JOIN (
            SELECT user_id, COUNT(*) AS count 
            FROM wishlist 
            GROUP BY user_id
        ) w ON c.user_id = w.user_id
        WHERE c.user_id = :id_user
    ');
        $stmt->execute(['id_user' => $id_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function orderPending($id_user)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT COUNT(*) AS pending_orders FROM commandes WHERE user_id = :id_user AND statut = "en_attente"');
        $stmt->execute(['id_user' => $id_user]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function updateProfile($id_user, $array)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('UPDATE ' . self::$table . ' SET nom = :nom, prenom = :prenom, email = :email, adresse = :adresse WHERE id = :id');
        $stmt->execute([
            'id' => $id_user,
            'nom' => $array['nom'],
            'prenom' => $array['prenom'],
            'email' => $array['email'],
            'adresse' => $array['adresse']
        ]);

        return true;
    }

    public static function changePassword($id_user, $new_password, $current_password)
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare('SELECT mot_de_passe FROM ' . self::$table . ' WHERE id = :id');
        $stmt->execute(['id' => $id_user]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {

            if (password_verify($current_password, $user['mot_de_passe'])) {
                $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $update_stmt = $db->prepare('UPDATE ' . self::$table . ' SET mot_de_passe = :new_password WHERE id = :id');
                $update_stmt->execute([
                    'id' => $id_user,
                    'new_password' => $new_hashed_password
                ]);

                return ['success' => true, 'message' => 'Mot de passe mis à jour avec succès.'];
            }

            return ['success' => false, 'message' => 'Mot de passe actuel incorrect.'];
        }
        return ['success' => false, 'message' => 'Erreur lors de la mise à jour du mot de passe.'];
    }

    public static function deleteAccount(int $userId): bool
    {
        $pdo = Database::getInstance()->getConnection();

        $stmt = $pdo->prepare("DELETE FROM commandes WHERE user_id = :id");
        $stmt->execute([':id' => $userId]);

        
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);

        return $stmt->rowCount() > 0;
    }
}
