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

        if($stmt->rowCount() > 0){
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
        return ['success' => false, 'message' => 'Adresse email non trouvée.', ];
    }
    
}