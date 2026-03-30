<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

// The Database singleton lives in config/database.php which is NOT under src/,
// so the PSR-4 autoloader won't find it. We load it manually.
require_once __DIR__ . '/../config/database.php';

/**
 * Bootstrap for PHPUnit tests.
 *
 * Injects a SQLite in-memory PDO into the Database singleton so that
 * all model tests run without needing a real MySQL server.
 */

// Start a session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── 1. Custom PDOStatement: fixes rowCount() for SELECT in SQLite ─────────────
//
// Standard SQLite PDO returns 0 for rowCount() after SELECT statements.
// Several model methods (User::verifyEmail, Wishlist::isInWishlist) rely on
// rowCount() after SELECT, so we patch the statement class to buffer row counts.
//
class TestPDOStatement extends PDOStatement
{
    private int  $bufferedRowCount = 0;
    private bool $isSelect         = false;
    private ?array $lastParams      = null;

    protected function __construct() {}

    public function execute(?array $params = null): bool
    {
        $this->bufferedRowCount = 0;
        $this->isSelect         = false;
        $this->lastParams       = $params;

        $result = parent::execute($params);

        $sql = strtolower(ltrim($this->queryString));
        if (str_starts_with($sql, 'select')) {
            $this->isSelect = true;
            // Count rows by fetching all, then close & re-execute so
            // normal fetch() calls still work correctly.
            $rows = parent::fetchAll(PDO::FETCH_ASSOC);
            $this->bufferedRowCount = count($rows);
            $this->closeCursor();
            parent::execute($params);
        }

        return $result;
    }

    public function rowCount(): int
    {
        if ($this->isSelect) {
            return $this->bufferedRowCount;
        }
        return parent::rowCount();
    }
}

// ── 2. Build the in-memory SQLite database ────────────────────────────────────
$pdo = new PDO('sqlite::memory:');
$pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);
$pdo->setAttribute(PDO::ATTR_STATEMENT_CLASS,    [TestPDOStatement::class]);

// ── 3. Create the schema (mirrors init.sql, adapted for SQLite) ───────────────
$pdo->exec("CREATE TABLE IF NOT EXISTS categories (
    id   INTEGER PRIMARY KEY AUTOINCREMENT,
    nom  VARCHAR(255) NOT NULL
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    nom          VARCHAR(255) NOT NULL,
    prenom       VARCHAR(255) NOT NULL,
    email        VARCHAR(255) NOT NULL UNIQUE,
    adresse      TEXT,
    mot_de_passe VARCHAR(255) NOT NULL,
    role         VARCHAR(50)  NOT NULL DEFAULT 'user',
    created_at   DATETIME     DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS chaussures (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    nom          VARCHAR(255)   NOT NULL,
    prix         DECIMAL(10, 2) NOT NULL,
    marque       VARCHAR(255),
    description  TEXT,
    categorie_id INTEGER,
    image        VARCHAR(255),
    created_at   DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS taille_chaussure (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    chaussure_id INTEGER NOT NULL,
    taille       FLOAT   NOT NULL,
    stock        INTEGER NOT NULL DEFAULT 0
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS commandes (
    id               INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id          INTEGER        NOT NULL,
    montant          DECIMAL(10, 2) NOT NULL,
    statut           VARCHAR(50)    NOT NULL DEFAULT 'en_attente',
    shipping_nom     VARCHAR(255),
    shipping_prenom  VARCHAR(255),
    shipping_adresse TEXT,
    shipping_npa     VARCHAR(10),
    shipping_ville   VARCHAR(255),
    date_commande    DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS commande_items (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    commande_id  INTEGER        NOT NULL,
    chaussure_id INTEGER        NOT NULL,
    quantite     INTEGER        NOT NULL,
    prix         DECIMAL(10, 2) NOT NULL,
    taille       FLOAT
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS wishlist (
    id           INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id      INTEGER NOT NULL,
    chaussure_id INTEGER NOT NULL
)");

// ── 4. Inject the SQLite PDO into Database::$instance via Reflection ──────────
//
// A lightweight proxy: models call Database::getInstance()->getConnection(),
// so this only needs to implement getConnection().
//
class TestDatabaseProxy
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}

$proxy   = new TestDatabaseProxy($pdo);
$refProp = new ReflectionProperty(App\Services\Database::class, 'instance');
$refProp->setAccessible(true);
$refProp->setValue(null, $proxy);

// ── 5. Expose the PDO globally so tests can seed / reset data ─────────────────
$GLOBALS['test_pdo'] = $pdo;

/**
 * Truncates all tables and resets auto-increment counters.
 * Call this in PHPUnit setUp() to keep tests isolated.
 */
function resetTestDatabase(): void
{
    $pdo = $GLOBALS['test_pdo'];
    $pdo->exec('DELETE FROM wishlist');
    $pdo->exec('DELETE FROM commande_items');
    $pdo->exec('DELETE FROM commandes');
    $pdo->exec('DELETE FROM taille_chaussure');
    $pdo->exec('DELETE FROM chaussures');
    $pdo->exec('DELETE FROM users');
    $pdo->exec('DELETE FROM categories');
    // Reset auto-increment counters (sqlite_sequence only exists after first INSERT)
    try {
        $pdo->exec(
            "DELETE FROM sqlite_sequence WHERE name IN " .
            "('users','chaussures','commandes','wishlist','taille_chaussure','commande_items','categories')"
        );
    } catch (\PDOException $e) {
        // Table may not exist yet on first run – safe to ignore
    }
}