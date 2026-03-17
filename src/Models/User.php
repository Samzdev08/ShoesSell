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
    
}