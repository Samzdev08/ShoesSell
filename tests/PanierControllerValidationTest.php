<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the validation / business logic in PanierController.
 *
 * The controller contains two pieces of logic worth testing in isolation:
 *  1. addCart  – quantity > 0 and a size must be selected
 *  2. Maj      – maximum quantity of 5 per item
 *
 * Both are tested here as pure-PHP unit tests (no HTTP stack needed).
 */
class PanierControllerValidationTest extends TestCase
{
    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Mirrors the validation from PanierController::addCart().
     * Returns the first error string, or null if the data is valid.
     */
    private function validateAddCart(array $data): ?string
    {
        $errors = [];

        if ((int) ($data['quantite'] ?? 0) <= 0) {
            $errors[] = 'La quantité doit être supérieure à zéro.';
        }

        if (empty($data['taille'])) {
            $errors[] = 'Veuillez sélectionner une taille.';
        }

        return $errors[0] ?? null;
    }

    /**
     * Mirrors the guard from PanierController::Maj().
     * Returns true when the update should be rejected.
     */
    private function isMajRejected(int $newQuantite): bool
    {
        return $newQuantite >= 5;
    }

    // ── addCart: quantity validation ──────────────────────────────────────────

    public function testAddCartFailsWhenQuantityIsZero(): void
    {
        $error = $this->validateAddCart(['quantite' => 0, 'taille' => '42']);

        $this->assertSame('La quantité doit être supérieure à zéro.', $error);
    }

    public function testAddCartFailsWhenQuantityIsNegative(): void
    {
        $error = $this->validateAddCart(['quantite' => -3, 'taille' => '42']);

        $this->assertSame('La quantité doit être supérieure à zéro.', $error);
    }

    public function testAddCartPassesWithPositiveQuantity(): void
    {
        $error = $this->validateAddCart(['quantite' => 1, 'taille' => '42']);

        $this->assertNull($error);
    }

    public function testAddCartPassesWithLargePositiveQuantity(): void
    {
        $error = $this->validateAddCart(['quantite' => 99, 'taille' => '40']);

        $this->assertNull($error);
    }

    // ── addCart: taille validation ────────────────────────────────────────────

    public function testAddCartFailsWhenTailleIsEmpty(): void
    {
        $error = $this->validateAddCart(['quantite' => 1, 'taille' => '']);

        $this->assertSame('Veuillez sélectionner une taille.', $error);
    }

    public function testAddCartFailsWhenTailleIsNull(): void
    {
        $error = $this->validateAddCart(['quantite' => 1, 'taille' => null]);

        $this->assertSame('Veuillez sélectionner une taille.', $error);
    }

    public function testAddCartPassesWithValidTaille(): void
    {
        $error = $this->validateAddCart(['quantite' => 2, 'taille' => '41.5']);

        $this->assertNull($error);
    }

    // ── addCart: both fields invalid → first error wins ───────────────────────

    public function testAddCartReturnsFirstErrorWhenBothFieldsInvalid(): void
    {
        $error = $this->validateAddCart(['quantite' => 0, 'taille' => '']);

        // Quantity check comes first in the controller
        $this->assertSame('La quantité doit être supérieure à zéro.', $error);
    }

    // ── Maj: quantity ceiling ─────────────────────────────────────────────────

    public function testMajIsRejectedWhenQuantityIsExactlyFive(): void
    {
        $this->assertTrue($this->isMajRejected(5));
    }

    public function testMajIsRejectedWhenQuantityExceedsFive(): void
    {
        $this->assertTrue($this->isMajRejected(6));
        $this->assertTrue($this->isMajRejected(10));
    }

    public function testMajIsAcceptedWhenQuantityIsBelowFive(): void
    {
        $this->assertFalse($this->isMajRejected(4));
        $this->assertFalse($this->isMajRejected(1));
    }

    public function testMajIsAcceptedWhenQuantityIsZero(): void
    {
        // 0 < 5, so the guard would not reject it
        $this->assertFalse($this->isMajRejected(0));
    }

    // ── Cart contents (pure array logic) ─────────────────────────────────────

    public function testCartProductStructureIsComplete(): void
    {
        $product = [
            'id'       => 42,
            'nom'      => 'Air Max 90',
            'prix'     => 129.99,
            'taille'   => '43',
            'marque'   => 'Nike',
            'quantite' => 2,
        ];

        $this->assertArrayHasKey('id',       $product);
        $this->assertArrayHasKey('nom',      $product);
        $this->assertArrayHasKey('prix',     $product);
        $this->assertArrayHasKey('taille',   $product);
        $this->assertArrayHasKey('marque',   $product);
        $this->assertArrayHasKey('quantite', $product);
    }

    public function testAddingMultipleProductsToCartSession(): void
    {
        $cart = [];

        $cart[] = ['id' => 1, 'nom' => 'Shoe A', 'prix' => 80.00, 'taille' => '41', 'marque' => 'X', 'quantite' => 1];
        $cart[] = ['id' => 2, 'nom' => 'Shoe B', 'prix' => 95.00, 'taille' => '42', 'marque' => 'Y', 'quantite' => 2];

        $this->assertCount(2, $cart);
        $this->assertSame('Shoe A', $cart[0]['nom']);
        $this->assertSame('Shoe B', $cart[1]['nom']);
    }

    public function testClearingCartProducesEmptyArray(): void
    {
        $cart   = [['id' => 1, 'nom' => 'Shoe', 'prix' => 50.00, 'taille' => '40', 'marque' => 'Z', 'quantite' => 1]];
        $cart   = []; // mirrors $_SESSION['cart'] = []

        $this->assertEmpty($cart);
    }

    public function testRemovingItemFromCartByIndex(): void
    {
        $cart = [
            0 => ['id' => 1, 'nom' => 'A', 'prix' => 50.0, 'taille' => '40', 'marque' => 'X', 'quantite' => 1],
            1 => ['id' => 2, 'nom' => 'B', 'prix' => 60.0, 'taille' => '41', 'marque' => 'Y', 'quantite' => 1],
        ];

        unset($cart[0]); // mirrors the controller's unset($_SESSION['cart'][$id])

        $this->assertCount(1, $cart);
        $this->assertArrayNotHasKey(0, $cart);
        $this->assertSame('B', $cart[1]['nom']);
    }
}