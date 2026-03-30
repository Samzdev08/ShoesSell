<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Wishlist;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the Wishlist model.
 */
class WishlistTest extends TestCase
{
    private int $userId    = 1;
    private int $shoeId    = 10;
    private int $shoeId2   = 20;

    protected function setUp(): void
    {
        resetTestDatabase();

        // Seed minimal data so FK-like logic doesn't break anything
        $pdo = $GLOBALS['test_pdo'];
        $pdo->exec("INSERT INTO users (nom, prenom, email, adresse, mot_de_passe) VALUES ('A','B','a@b.com','addr','hash')");
        $pdo->exec("INSERT INTO categories (nom) VALUES ('Sneakers')");
        $pdo->exec("INSERT INTO chaussures (id, nom, prix, marque, description, categorie_id) VALUES ({$this->shoeId}, 'Shoe1', 99.99, 'Brand', '', 1)");
        $pdo->exec("INSERT INTO chaussures (id, nom, prix, marque, description, categorie_id) VALUES ({$this->shoeId2}, 'Shoe2', 120.00, 'Brand', '', 1)");
        $this->userId = (int) $pdo->query('SELECT id FROM users LIMIT 1')->fetchColumn();
    }

    // ── Constructor ───────────────────────────────────────────────────────────

    public function testConstructorSetsAllProperties(): void
    {
        $wishlist = new Wishlist(3, 7, 42);

        $this->assertSame(3,  $wishlist->id);
        $this->assertSame(7,  $wishlist->user_id);
        $this->assertSame(42, $wishlist->chaussure_id);
    }

    // ── addToWishlist ─────────────────────────────────────────────────────────

    public function testAddToWishlistReturnsNewId(): void
    {
        $id = Wishlist::addToWishlist($this->userId, $this->shoeId);

        $this->assertNotEmpty($id);
    }

    public function testAddToWishlistPersistsEntry(): void
    {
        Wishlist::addToWishlist($this->userId, $this->shoeId);

        $this->assertTrue(Wishlist::isInWishlist($this->userId, $this->shoeId));
    }

    // ── isInWishlist ──────────────────────────────────────────────────────────

    public function testIsInWishlistReturnsTrueWhenPresent(): void
    {
        Wishlist::addToWishlist($this->userId, $this->shoeId);

        $this->assertTrue(Wishlist::isInWishlist($this->userId, $this->shoeId));
    }

    public function testIsInWishlistReturnsFalseWhenAbsent(): void
    {
        $this->assertFalse(Wishlist::isInWishlist($this->userId, $this->shoeId));
    }

    public function testIsInWishlistIsolatedByUser(): void
    {
        $pdo = $GLOBALS['test_pdo'];
        $pdo->exec("INSERT INTO users (nom, prenom, email, adresse, mot_de_passe) VALUES ('C','D','c@d.com','addr','hash')");
        $otherUser = (int) $pdo->lastInsertId();

        Wishlist::addToWishlist($otherUser, $this->shoeId);

        $this->assertFalse(Wishlist::isInWishlist($this->userId, $this->shoeId));
    }

    // ── removeFromWishlist ────────────────────────────────────────────────────

    public function testRemoveFromWishlistReturnsTrueAndDeletesEntry(): void
    {
        Wishlist::addToWishlist($this->userId, $this->shoeId);

        $removed = Wishlist::removeFromWishlist($this->userId, $this->shoeId);

        $this->assertTrue($removed);
        $this->assertFalse(Wishlist::isInWishlist($this->userId, $this->shoeId));
    }

    public function testRemoveFromWishlistReturnsFalseWhenNotPresent(): void
    {
        $removed = Wishlist::removeFromWishlist($this->userId, 9999);

        $this->assertFalse($removed);
    }

    // ── getWishlistByUserId ───────────────────────────────────────────────────

    public function testGetWishlistByUserIdReturnsAllEntries(): void
    {
        Wishlist::addToWishlist($this->userId, $this->shoeId);
        Wishlist::addToWishlist($this->userId, $this->shoeId2);

        $wishlist = Wishlist::getWishlistByUserId($this->userId);

        $this->assertIsArray($wishlist);
        $this->assertCount(2, $wishlist);
    }

    public function testGetWishlistByUserIdReturnsEmptyForUserWithNoWishlist(): void
    {
        $wishlist = Wishlist::getWishlistByUserId(9999);

        $this->assertIsArray($wishlist);
        $this->assertEmpty($wishlist);
    }

    public function testGetWishlistByUserIdContainsChaussureDetails(): void
    {
        Wishlist::addToWishlist($this->userId, $this->shoeId);

        $wishlist = Wishlist::getWishlistByUserId($this->userId);

        $this->assertArrayHasKey('chaussure_nom',      $wishlist[0]);
        $this->assertArrayHasKey('chaussure_prix',     $wishlist[0]);
        $this->assertArrayHasKey('chaussure_marque',   $wishlist[0]);
        $this->assertArrayHasKey('chaussure_categorie', $wishlist[0]);
        $this->assertSame('Shoe1', $wishlist[0]['chaussure_nom']);
    }

    public function testWishlistIsOrderedByIdDesc(): void
    {
        Wishlist::addToWishlist($this->userId, $this->shoeId);
        Wishlist::addToWishlist($this->userId, $this->shoeId2);

        $wishlist = Wishlist::getWishlistByUserId($this->userId);

        // Most recently added (shoeId2) should come first
        $this->assertSame((string) $this->shoeId2, (string) $wishlist[0]['chaussure_id']);
    }
}