<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Commande;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Commande model.
 */
class CommandeTest extends TestCase
{
    protected function setUp(): void
    {
        resetTestDatabase();
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function createUser(): int
    {
        $pdo  = $GLOBALS['test_pdo'];
        $stmt = $pdo->prepare(
            "INSERT INTO users (nom, prenom, email, adresse, mot_de_passe)
             VALUES ('Test', 'User', 'user@example.com', 'Addr', 'hash')"
        );
        $stmt->execute();
        return (int) $pdo->lastInsertId();
    }

    private function createCommande(int $userId, float $montant = 120.00): int
    {
        return (int) Commande::create([
            'user_id'          => $userId,
            'total'            => $montant,
            'nom'              => 'Dupont',
            'prenom'           => 'Jean',
            'shipping_adresse' => '1 rue de la Paix',
            'shipping_npa'     => '75001',
            'shipping_ville'   => 'Paris',
        ]);
    }

    // ── Constructor ───────────────────────────────────────────────────────────

    public function testConstructorSetsAllProperties(): void
    {
        $commande = new Commande(10, 3, 250.00, '2024-01-15 10:30:00');

        $this->assertSame(10,            $commande->id);
        $this->assertSame(3,             $commande->user_id);
        $this->assertSame(250.00,        $commande->total);
        $this->assertSame('2024-01-15 10:30:00', $commande->created_at);
    }

    // ── create ────────────────────────────────────────────────────────────────

    public function testCreateReturnsNewCommandeId(): void
    {
        $userId = $this->createUser();
        $id     = Commande::create([
            'user_id'          => $userId,
            'total'            => 199.99,
            'nom'              => 'Martin',
            'prenom'           => 'Alice',
            'shipping_adresse' => '5 av. Victor Hugo',
            'shipping_npa'     => '69001',
            'shipping_ville'   => 'Lyon',
        ]);

        $this->assertNotEmpty($id);
    }

    public function testCreatePersistsCommandeInDatabase(): void
    {
        $userId = $this->createUser();
        $id     = $this->createCommande($userId, 89.50);

        $pdo  = $GLOBALS['test_pdo'];
        $stmt = $pdo->prepare('SELECT * FROM commandes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $row  = $stmt->fetch();

        $this->assertIsArray($row);
        $this->assertSame($userId, (int) $row['user_id']);
        $this->assertSame('89.5', (string) (float) $row['montant']);
    }

    // ── orderById ─────────────────────────────────────────────────────────────

    public function testOrderByIdReturnsCorrectOrder(): void
    {
        $userId    = $this->createUser();
        $commandeId = $this->createCommande($userId, 150.00);

        $result = Commande::orderById($userId, $commandeId);

        $this->assertIsArray($result);
        $this->assertSame($commandeId, (int) $result['id']);
        $this->assertSame($userId,     (int) $result['user_id']);
    }

    public function testOrderByIdReturnsFalseForWrongUser(): void
    {
        $userId     = $this->createUser();
        $commandeId = $this->createCommande($userId);

        $result = Commande::orderById(9999, $commandeId);

        $this->assertFalse($result);
    }

    public function testOrderByIdReturnsFalseForNonExistentOrder(): void
    {
        $userId = $this->createUser();
        $result = Commande::orderById($userId, 99999);

        $this->assertFalse($result);
    }

    // ── recentOrders ─────────────────────────────────────────────────────────

    public function testRecentOrdersReturnsOrdersForUser(): void
    {
        $userId = $this->createUser();
        $this->createCommande($userId, 100.00);
        $this->createCommande($userId, 200.00);

        $orders = Commande::recentOrders($userId, 5);

        $this->assertIsArray($orders);
        $this->assertCount(2, $orders);
    }

    public function testRecentOrdersRespectsLimit(): void
    {
        $userId = $this->createUser();
        for ($i = 0; $i < 6; $i++) {
            $this->createCommande($userId, 50.00);
        }

        $orders = Commande::recentOrders($userId, 3);

        $this->assertCount(3, $orders);
    }

    public function testRecentOrdersReturnsEmptyForUserWithNoOrders(): void
    {
        $orders = Commande::recentOrders(9999, 5);

        $this->assertIsArray($orders);
        $this->assertEmpty($orders);
    }

    public function testRecentOrdersDoesNotReturnOrdersFromOtherUsers(): void
    {
        $pdo = $GLOBALS['test_pdo'];
        $pdo->exec("INSERT INTO users (nom, prenom, email, adresse, mot_de_passe) VALUES ('A','B','a@a.com','addr','h')");
        $user1 = (int) $pdo->lastInsertId();
        $pdo->exec("INSERT INTO users (nom, prenom, email, adresse, mot_de_passe) VALUES ('C','D','c@c.com','addr','h')");
        $user2 = (int) $pdo->lastInsertId();

        $this->createCommande($user1, 100.00);
        $this->createCommande($user2, 200.00);

        $orders = Commande::recentOrders($user1, 5);
        $this->assertCount(1, $orders);
        $this->assertSame($user1, (int) $orders[0]['user_id']);
    }

    // ── Default statut ────────────────────────────────────────────────────────

    public function testNewCommandeHasEnAttenteStatus(): void
    {
        $userId     = $this->createUser();
        $commandeId = $this->createCommande($userId);

        $pdo  = $GLOBALS['test_pdo'];
        $stmt = $pdo->prepare('SELECT statut FROM commandes WHERE id = :id');
        $stmt->execute(['id' => $commandeId]);
        $row  = $stmt->fetch();

        $this->assertSame('en_attente', $row['statut']);
    }
}