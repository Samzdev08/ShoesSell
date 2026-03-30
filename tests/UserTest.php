<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

/**
 * Unit / integration tests for the User model.
 *
 * Every test method resets the SQLite in-memory database so that each
 * test is fully isolated.
 */
class UserTest extends TestCase
{
    // ── Helpers ──────────────────────────────────────────────────────────────

    protected function setUp(): void
    {
        resetTestDatabase();
    }

    /**
     * Inserts a user directly via PDO (bypasses password hashing so we can
     * control the hash ourselves).
     */
    private function insertUser(array $data): int
    {
        $pdo  = $GLOBALS['test_pdo'];
        $stmt = $pdo->prepare(
            'INSERT INTO users (nom, prenom, email, adresse, mot_de_passe, role)
             VALUES (:nom, :prenom, :email, :adresse, :mot_de_passe, :role)'
        );
        $stmt->execute($data);
        return (int) $pdo->lastInsertId();
    }

    // ── Constructor ───────────────────────────────────────────────────────────

    public function testConstructorSetsAllProperties(): void
    {
        $user = new User(1, 'Dupont', 'Jean', '1 rue de la Paix', 'jean@example.com', 'hashed');

        $this->assertSame(1, $user->id);
        $this->assertSame('Dupont', $user->nom);
        $this->assertSame('Jean', $user->prenom);
        $this->assertSame('1 rue de la Paix', $user->adresse);
        $this->assertSame('jean@example.com', $user->email);
        $this->assertSame('hashed', $user->password);
    }

    // ── verifyEmail ───────────────────────────────────────────────────────────

    public function testVerifyEmailReturnsTrueForExistingEmail(): void
    {
        $this->insertUser([
            'nom'          => 'Martin',
            'prenom'       => 'Alice',
            'email'        => 'alice@example.com',
            'adresse'      => '5 av. Victor Hugo',
            'mot_de_passe' => password_hash('secret', PASSWORD_DEFAULT),
            'role'         => 'user',
        ]);

        $this->assertTrue(User::verifyEmail('alice@example.com'));
    }

    public function testVerifyEmailReturnsFalseForUnknownEmail(): void
    {
        $this->assertFalse(User::verifyEmail('nobody@example.com'));
    }

    // ── create ────────────────────────────────────────────────────────────────

    public function testCreateReturnsNewUserId(): void
    {
        $id = User::create([
            'nom'      => 'Blanc',
            'prenom'   => 'Pierre',
            'email'    => 'pierre@example.com',
            'adresse'  => '10 rue des Lilas',
            'password' => password_hash('password123', PASSWORD_DEFAULT),
        ]);

        $this->assertNotEmpty($id);
        $this->assertIsString($id); // PDO::lastInsertId() returns a string
    }

    public function testCreatePersistsUserInDatabase(): void
    {
        User::create([
            'nom'      => 'Lemaire',
            'prenom'   => 'Claire',
            'email'    => 'claire@example.com',
            'adresse'  => '3 impasse du Moulin',
            'password' => password_hash('abc123', PASSWORD_DEFAULT),
        ]);

        $this->assertTrue(User::verifyEmail('claire@example.com'));
    }

    // ── find ──────────────────────────────────────────────────────────────────

    public function testFindReturnsUserArrayById(): void
    {
        $id = $this->insertUser([
            'nom'          => 'Petit',
            'prenom'       => 'Louis',
            'email'        => 'louis@example.com',
            'adresse'      => '7 bd Saint-Michel',
            'mot_de_passe' => password_hash('pw', PASSWORD_DEFAULT),
            'role'         => 'user',
        ]);

        $user = User::find($id);

        $this->assertIsArray($user);
        $this->assertSame('Petit', $user['nom']);
        $this->assertSame('louis@example.com', $user['email']);
    }

    public function testFindReturnsNullForNonExistentId(): void
    {
        $user = User::find(9999);

        $this->assertFalse($user); // PDOStatement::fetch returns false when no row found
    }

    // ── login ─────────────────────────────────────────────────────────────────

    public function testLoginSucceedsWithCorrectCredentials(): void
    {
        $rawPassword = 'MySecretPass1';
        $this->insertUser([
            'nom'          => 'Moreau',
            'prenom'       => 'Sophie',
            'email'        => 'sophie@example.com',
            'adresse'      => '2 chemin des Acacias',
            'mot_de_passe' => password_hash($rawPassword, PASSWORD_DEFAULT),
            'role'         => 'user',
        ]);

        $result = User::login(['email' => 'sophie@example.com', 'password' => $rawPassword]);

        $this->assertTrue($result['success']);
        $this->assertArrayHasKey('user', $result);
        $this->assertSame('sophie@example.com', $result['user']['email']);
    }

    public function testLoginFailsWithWrongPassword(): void
    {
        $this->insertUser([
            'nom'          => 'Faure',
            'prenom'       => 'Marc',
            'email'        => 'marc@example.com',
            'adresse'      => '9 rue du Faubourg',
            'mot_de_passe' => password_hash('correct', PASSWORD_DEFAULT),
            'role'         => 'user',
        ]);

        $result = User::login(['email' => 'marc@example.com', 'password' => 'wrong']);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Mot de passe incorrect', $result['message']);
    }

    public function testLoginFailsWithUnknownEmail(): void
    {
        $result = User::login(['email' => 'ghost@example.com', 'password' => 'any']);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('email', strtolower($result['message']));
    }

    // ── updateProfile ─────────────────────────────────────────────────────────

    public function testUpdateProfileChangesUserData(): void
    {
        $id = $this->insertUser([
            'nom'          => 'Roux',
            'prenom'       => 'Emma',
            'email'        => 'emma@example.com',
            'adresse'      => 'Old address',
            'mot_de_passe' => password_hash('pw', PASSWORD_DEFAULT),
            'role'         => 'user',
        ]);

        User::updateProfile($id, [
            'nom'     => 'Rousseau',
            'prenom'  => 'Emma',
            'email'   => 'emma@example.com',
            'adresse' => 'New address',
        ]);

        $updated = User::find($id);
        $this->assertSame('Rousseau', $updated['nom']);
        $this->assertSame('New address', $updated['adresse']);
    }

    // ── changePassword ────────────────────────────────────────────────────────

    public function testChangePasswordSucceedsWithCorrectCurrentPassword(): void
    {
        $current = 'oldPass99';
        $id      = $this->insertUser([
            'nom'          => 'Durand',
            'prenom'       => 'Luc',
            'email'        => 'luc@example.com',
            'adresse'      => '4 allée des Roses',
            'mot_de_passe' => password_hash($current, PASSWORD_DEFAULT),
            'role'         => 'user',
        ]);

        $result = User::changePassword($id, 'newPass99', $current);

        $this->assertTrue($result['success']);

        // Verify the new password actually works for login
        $loginResult = User::login(['email' => 'luc@example.com', 'password' => 'newPass99']);
        $this->assertTrue($loginResult['success']);
    }

    public function testChangePasswordFailsWithWrongCurrentPassword(): void
    {
        $id = $this->insertUser([
            'nom'          => 'Bernard',
            'prenom'       => 'Nina',
            'email'        => 'nina@example.com',
            'adresse'      => '6 place Carnot',
            'mot_de_passe' => password_hash('realPass', PASSWORD_DEFAULT),
            'role'         => 'user',
        ]);

        $result = User::changePassword($id, 'newPass', 'wrongCurrent');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('incorrect', strtolower($result['message']));
    }

    // ── deleteAccount ─────────────────────────────────────────────────────────

    public function testDeleteAccountRemovesUser(): void
    {
        $id = $this->insertUser([
            'nom'          => 'Lefort',
            'prenom'       => 'Tom',
            'email'        => 'tom@example.com',
            'adresse'      => '1 bd du Temple',
            'mot_de_passe' => password_hash('pass', PASSWORD_DEFAULT),
            'role'         => 'user',
        ]);

        $deleted = User::deleteAccount($id);

        $this->assertTrue($deleted);
        $this->assertFalse(User::find($id));
    }

    public function testDeleteAccountReturnsFalseForNonExistentUser(): void
    {
        $result = User::deleteAccount(99999);

        $this->assertFalse($result);
    }
}