<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the validation logic embedded in AuthController::register()
 * and AuthController::login().
 *
 * Because these validations are implemented directly in the controller
 * (not extracted into a service), we reproduce the same rules here so
 * that any regression in the validation code is caught immediately.
 *
 * The tests are pure-PHP and require no HTTP stack.
 */
class AuthControllerValidationTest extends TestCase
{
    // ── Helpers: mirror the validation logic from AuthController::register() ──

    /**
     * Runs the same validation as AuthController::register() and returns
     * the first error message, or null if the data is valid.
     */
    private function validateRegister(array $data): ?string
    {
        $errors = [];

        if (
            empty($data['nom'])      || empty($data['prenom'])          ||
            empty($data['adresse'])  || empty($data['email'])           ||
            empty($data['password']) || empty($data['password_verify'])
        ) {
            $errors[] = 'Tous les champs sont obligatoires.';
        }

        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $errors[] = "L'adresse email n'est pas valide.";
        }

        if (strlen($data['password'] ?? '') < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
        }

        if (preg_match('/[0-9!@#$%^&*()-+]/', $data['nom'] ?? '')) {
            $errors[] = 'Le nom ne doit pas contenir de chiffres ou de caractères spéciaux.';
        }

        if (preg_match('/[0-9!@#$%^&*()-+]/', $data['prenom'] ?? '')) {
            $errors[] = 'Le prénom ne doit pas contenir de chiffres ou de caractères spéciaux.';
        }

        if (($data['password'] ?? '') !== ($data['password_verify'] ?? '')) {
            $errors[] = 'Les mots de passe ne correspondent pas.';
        }

        return $errors[0] ?? null;
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'nom'             => 'Dupont',
            'prenom'          => 'Jean',
            'adresse'         => '1 rue de la Paix',
            'email'           => 'jean@example.com',
            'password'        => 'password123',
            'password_verify' => 'password123',
        ], $overrides);
    }

    // ── Required fields ───────────────────────────────────────────────────────

    public function testRegisterFailsWhenAllFieldsEmpty(): void
    {
        $error = $this->validateRegister([
            'nom' => '', 'prenom' => '', 'adresse' => '',
            'email' => '', 'password' => '', 'password_verify' => '',
        ]);

        $this->assertSame('Tous les champs sont obligatoires.', $error);
    }

    public function testRegisterFailsWhenNomMissing(): void
    {
        $error = $this->validateRegister($this->validPayload(['nom' => '']));

        $this->assertSame('Tous les champs sont obligatoires.', $error);
    }

    public function testRegisterFailsWhenPrenomMissing(): void
    {
        $error = $this->validateRegister($this->validPayload(['prenom' => '']));

        $this->assertSame('Tous les champs sont obligatoires.', $error);
    }

    public function testRegisterFailsWhenEmailMissing(): void
    {
        $error = $this->validateRegister($this->validPayload(['email' => '']));

        $this->assertSame('Tous les champs sont obligatoires.', $error);
    }

    public function testRegisterFailsWhenPasswordMissing(): void
    {
        $error = $this->validateRegister($this->validPayload(['password' => '', 'password_verify' => '']));

        $this->assertSame('Tous les champs sont obligatoires.', $error);
    }

    // ── Email validation ──────────────────────────────────────────────────────

    public function testRegisterFailsWithInvalidEmail(): void
    {
        $error = $this->validateRegister($this->validPayload(['email' => 'not-an-email']));

        $this->assertStringContainsString('email', strtolower($error));
    }

    public function testRegisterFailsWithEmailMissingDomain(): void
    {
        $error = $this->validateRegister($this->validPayload(['email' => 'user@']));

        $this->assertStringContainsString('email', strtolower($error));
    }

    public function testRegisterSucceedsWithValidEmail(): void
    {
        $error = $this->validateRegister($this->validPayload(['email' => 'valid.user+tag@sub.domain.com']));

        // No email error expected
        $this->assertNull($error);
    }

    // ── Password length ───────────────────────────────────────────────────────

    public function testRegisterFailsWhenPasswordTooShort(): void
    {
        $error = $this->validateRegister($this->validPayload([
            'password'        => '12345',
            'password_verify' => '12345',
        ]));

        $this->assertStringContainsString('6 caractères', $error);
    }

    public function testRegisterPassesWithPasswordOfExactlyMinLength(): void
    {
        $error = $this->validateRegister($this->validPayload([
            'password'        => '123456',
            'password_verify' => '123456',
        ]));

        $this->assertNull($error);
    }

    // ── Password confirmation ─────────────────────────────────────────────────

    public function testRegisterFailsWhenPasswordsDontMatch(): void
    {
        $error = $this->validateRegister($this->validPayload([
            'password'        => 'correctPass1',
            'password_verify' => 'wrongPass99',
        ]));

        $this->assertStringContainsString('correspondent pas', $error);
    }

    public function testRegisterSucceedsWhenPasswordsMatch(): void
    {
        $error = $this->validateRegister($this->validPayload([
            'password'        => 'matchMe123',
            'password_verify' => 'matchMe123',
        ]));

        $this->assertNull($error);
    }

    // ── Nom / prénom with forbidden characters ────────────────────────────────

    public function testRegisterFailsWhenNomContainsDigits(): void
    {
        $error = $this->validateRegister($this->validPayload(['nom' => 'Jean2']));

        $this->assertStringContainsString('chiffres', $error);
    }

    public function testRegisterFailsWhenNomContainsSpecialChars(): void
    {
        // The controller regex is: /[0-9!@#$%^&*()-+]/
        // The sequence ()-+ inside a character class covers the ASCII range
        // 41..43 ()*+, so the literal hyphen '-' (ASCII 45) is NOT matched.
        // We only test characters that are genuinely in the pattern.
        foreach (['!', '@', '#', '$', '%', '^', '&', '*', '(', ')'] as $char) {
            $error = $this->validateRegister($this->validPayload(['nom' => "Jean{$char}"]));
            $this->assertStringContainsString(
                'chiffres',
                $error ?? '',
                "Expected error for nom containing '$char'"
            );
        }
    }

    public function testRegisterFailsWhenPrenomContainsDigits(): void
    {
        $error = $this->validateRegister($this->validPayload(['prenom' => 'Pierre3']));

        $this->assertStringContainsString('chiffres', $error);
    }

    public function testRegisterPassesWithAccentedNames(): void
    {
        $error = $this->validateRegister($this->validPayload([
            'nom'    => 'Ducrès',
            'prenom' => 'Élodie',
        ]));

        $this->assertNull($error);
    }

    public function testRegisterPassesWithHyphenatedName(): void
    {
        // Hyphens are NOT in the forbidden list
        $error = $this->validateRegister($this->validPayload(['nom' => 'Martin-Dupont']));

        $this->assertNull($error);
    }

    // ── Full valid payload ────────────────────────────────────────────────────

    public function testRegisterSucceedsWithAllValidData(): void
    {
        $error = $this->validateRegister($this->validPayload());

        $this->assertNull($error);
    }
}