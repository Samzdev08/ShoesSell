<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Chaussure;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Chaussure model.
 */
class ChaussureTest extends TestCase
{
    protected function setUp(): void
    {
        resetTestDatabase();
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function insertCategory(string $nom): int
    {
        $pdo  = $GLOBALS['test_pdo'];
        $stmt = $pdo->prepare('INSERT INTO categories (nom) VALUES (:nom)');
        $stmt->execute(['nom' => $nom]);
        return (int) $pdo->lastInsertId();
    }

    private function insertChaussure(array $data): int
    {
        $pdo  = $GLOBALS['test_pdo'];
        $stmt = $pdo->prepare(
            'INSERT INTO chaussures (nom, prix, marque, description, categorie_id)
             VALUES (:nom, :prix, :marque, :description, :categorie_id)'
        );
        $stmt->execute($data);
        return (int) $pdo->lastInsertId();
    }

    private function insertTaille(int $chaussureId, float $taille, int $stock): void
    {
        $pdo  = $GLOBALS['test_pdo'];
        $stmt = $pdo->prepare(
            'INSERT INTO taille_chaussure (chaussure_id, taille, stock) VALUES (:cid, :taille, :stock)'
        );
        $stmt->execute(['cid' => $chaussureId, 'taille' => $taille, 'stock' => $stock]);
    }

    // ── Constructor ───────────────────────────────────────────────────────────

    public function testConstructorSetsAllProperties(): void
    {
        $shoe = new Chaussure(5, 'Air Max', 129.99, 'Nike', 'Une belle chaussure de sport.');

        $this->assertSame(5, $shoe->id);
        $this->assertSame('Air Max', $shoe->nom);
        $this->assertSame(129.99, $shoe->prix);
        $this->assertSame('Nike', $shoe->marque);
        $this->assertSame('Une belle chaussure de sport.', $shoe->description);
    }

    // ── getAll ────────────────────────────────────────────────────────────────

    public function testGetAllReturnsAllChaussures(): void
    {
        $catId = $this->insertCategory('Sneakers');
        $this->insertChaussure(['nom' => 'Air Force 1', 'prix' => 110.00, 'marque' => 'Nike',    'description' => '', 'categorie_id' => $catId]);
        $this->insertChaussure(['nom' => 'Superstar',   'prix' => 95.00,  'marque' => 'Adidas',  'description' => '', 'categorie_id' => $catId]);

        $result = Chaussure::getAll();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetAllRespectsLimit(): void
    {
        $catId = $this->insertCategory('Running');
        for ($i = 1; $i <= 5; $i++) {
            $this->insertChaussure(['nom' => "Shoe $i", 'prix' => 80.00, 'marque' => 'Brand', 'description' => '', 'categorie_id' => $catId]);
        }

        $result = Chaussure::getAll(3);

        $this->assertCount(3, $result);
    }

    public function testGetAllFiltersByMarque(): void
    {
        $catId = $this->insertCategory('Lifestyle');
        $this->insertChaussure(['nom' => 'Stan Smith',  'prix' => 90.00,  'marque' => 'Adidas', 'description' => '', 'categorie_id' => $catId]);
        $this->insertChaussure(['nom' => 'Chuck Taylor', 'prix' => 75.00, 'marque' => 'Converse', 'description' => '', 'categorie_id' => $catId]);

        $result = Chaussure::getAll(null, null, 'Adidas');

        $this->assertCount(1, $result);
        $this->assertSame('Stan Smith', $result[0]['nom']);
    }

    public function testGetAllFiltersByCategory(): void
    {
        $cat1 = $this->insertCategory('Running');
        $cat2 = $this->insertCategory('Lifestyle');
        $this->insertChaussure(['nom' => 'Shoe A', 'prix' => 80.00, 'marque' => 'Brand', 'description' => '', 'categorie_id' => $cat1]);
        $this->insertChaussure(['nom' => 'Shoe B', 'prix' => 80.00, 'marque' => 'Brand', 'description' => '', 'categorie_id' => $cat2]);

        $result = Chaussure::getAll(null, $cat1, null);

        $this->assertCount(1, $result);
        $this->assertSame('Shoe A', $result[0]['nom']);
    }

    public function testGetAllReturnsEmptyArrayWhenNoChaussures(): void
    {
        $result = Chaussure::getAll();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    // ── getById ───────────────────────────────────────────────────────────────

    public function testGetByIdReturnsCorrectRecord(): void
    {
        $catId = $this->insertCategory('Basketball');
        $id    = $this->insertChaussure([
            'nom'         => 'Jordan 1',
            'prix'        => 180.00,
            'marque'      => 'Nike',
            'description' => 'Iconic sneaker',
            'categorie_id' => $catId,
        ]);

        $result = Chaussure::getById($id);

        $this->assertIsArray($result);
        $this->assertSame('Jordan 1', $result['nom']);
        $this->assertSame('Basketball', $result['categorie']); // joined column
    }

    public function testGetByIdReturnsFalseForUnknownId(): void
    {
        $result = Chaussure::getById(99999);

        $this->assertFalse($result);
    }

    // ── getWishlistIds ────────────────────────────────────────────────────────

    public function testGetWishlistIdsReturnsChaussureIds(): void
    {
        $catId  = $this->insertCategory('Sneakers');
        $shoe1  = $this->insertChaussure(['nom' => 'A', 'prix' => 100, 'marque' => 'X', 'description' => '', 'categorie_id' => $catId]);
        $shoe2  = $this->insertChaussure(['nom' => 'B', 'prix' => 100, 'marque' => 'X', 'description' => '', 'categorie_id' => $catId]);
        $userId = 42;

        $pdo = $GLOBALS['test_pdo'];
        $pdo->exec("INSERT INTO wishlist (user_id, chaussure_id) VALUES ($userId, $shoe1), ($userId, $shoe2)");

        $ids = Chaussure::getWishlistIds($userId);

        $this->assertIsArray($ids);
        // fetchAll(FETCH_COLUMN) returns strings in SQLite, ints in MySQL.
        // Cast to int for a portable comparison.
        $ids = array_map('intval', $ids);
        $this->assertContains($shoe1, $ids);
        $this->assertContains($shoe2, $ids);
    }

    public function testGetWishlistIdsReturnsEmptyArrayForUserWithNoWishlist(): void
    {
        $ids = Chaussure::getWishlistIds(9999);

        $this->assertIsArray($ids);
        $this->assertEmpty($ids);
    }

    // ── en_stock computed column ──────────────────────────────────────────────

    public function testGetAllIncludesEnStockColumn(): void
    {
        $catId = $this->insertCategory('Running');
        $id    = $this->insertChaussure([
            'nom'         => 'React Infinity',
            'prix'        => 160.00,
            'marque'      => 'Nike',
            'description' => '',
            'categorie_id' => $catId,
        ]);
        $this->insertTaille($id, 42.0, 5);

        $result = Chaussure::getAll();

        $this->assertArrayHasKey('en_stock', $result[0]);
        $this->assertGreaterThan(0, (int) $result[0]['en_stock']);
    }
}