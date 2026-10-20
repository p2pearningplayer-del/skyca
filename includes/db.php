<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — DATABASE CONNECTION & SCHEMA MANAGER
 * Uses PDO with real parameterized queries (ATTR_EMULATE_PREPARES = false).
 * Credentials are loaded strictly from environment configuration (.env).
 */

require_once dirname(__DIR__) . '/config/config.php';

/**
 * Return the active PDO database connection or throw a sanitized RuntimeException.
 */
function skyca_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $driver = strtolower(trim(skyca_env('DATABASE_DRIVER', 'mysql')));

    try {
        if ($driver === 'sqlite') {
            // Isolated local test database driver (used only when explicitly configured for local testing)
            $sqlitePath = skyca_env('TEST_SQLITE_PATH', SKYCA_STORAGE_DIR . DIRECTORY_SEPARATOR . 'skyca_test.sqlite');
            $pdo = new PDO('sqlite:' . $sqlitePath, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            $pdo->exec('PRAGMA foreign_keys = ON;');
            skyca_ensure_schema_sqlite($pdo);
        } else {
            $host    = skyca_env('DATABASE_HOST', 'localhost');
            $port    = skyca_env('DATABASE_PORT', '3306');
            $dbName  = skyca_env('DATABASE_NAME', 'u591469534_skyca');
            $user    = skyca_env('DATABASE_USER', '');
            $pass    = skyca_env('DATABASE_PASSWORD', '');
            $charset = skyca_env('DATABASE_CHARSET', 'utf8mb4');

            if ($user === '') {
                throw new RuntimeException('Database configuration is incomplete.');
            }

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $dbName, $charset);
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::ATTR_TIMEOUT            => 8,
            ]);
            $pdo->exec("SET time_zone = '+05:30'");
            skyca_ensure_schema_mysql($pdo);
        }

        skyca_bootstrap_initial_admin($pdo);
        return $pdo;
    } catch (Throwable $e) {
        // Log sanitized error message without exposing credentials
        error_log('[SkycaDB Error] Connection failed: ' . $e->getMessage());
        throw new RuntimeException('Unable to connect to the database service at this time.');
    }
}

/**
 * Return PDO connection if available, or null without throwing (useful for optional reads).
 */
function skyca_db_or_null(): ?PDO
{
    try {
        return skyca_db();
    } catch (Throwable $e) {
        return null;
    }
}

/**
 * Safely ensure required tables exist in MySQL without modifying or dropping existing tables.
 */
function skyca_ensure_schema_mysql(PDO $pdo): void
{
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $schemaFile = SKYCA_ROOT . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'schema.sql';
    if (!is_file($schemaFile)) {
        return;
    }

    // Check if leads and admin_users tables already exist to avoid running DDL on every request
    $stmt = $pdo->query("SHOW TABLES LIKE 'leads'");
    $leadsExists = (bool) $stmt->fetchColumn();
    $stmt2 = $pdo->query("SHOW TABLES LIKE 'admin_users'");
    $adminExists = (bool) $stmt2->fetchColumn();

    if ($leadsExists && $adminExists) {
        return;
    }

    $sql = (string) file_get_contents($schemaFile);
    // Split statements safely on semicolons at end of lines
    $statements = preg_split('/;\s*[\r\n]+/', $sql) ?: [];
    foreach ($statements as $statement) {
        $clean = trim($statement);
        if ($clean === '' || str_starts_with($clean, '--')) {
            continue;
        }
        $pdo->exec($clean);
    }
}

/**
 * Ensure schema compatibility for local SQLite automated testing.
 */
function skyca_ensure_schema_sqlite(PDO $pdo): void
{
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS leads (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            lead_reference TEXT NOT NULL UNIQUE,
            full_name TEXT NOT NULL,
            business_email TEXT NOT NULL,
            phone TEXT NOT NULL,
            company_name TEXT DEFAULT NULL,
            requested_service TEXT NOT NULL,
            estimated_budget TEXT DEFAULT NULL,
            project_timeline TEXT DEFAULT NULL,
            project_description TEXT NOT NULL,
            landing_page TEXT DEFAULT NULL,
            referrer TEXT DEFAULT NULL,
            utm_source TEXT DEFAULT NULL,
            utm_medium TEXT DEFAULT NULL,
            utm_campaign TEXT DEFAULT NULL,
            utm_content TEXT DEFAULT NULL,
            utm_term TEXT DEFAULT NULL,
            consent_status INTEGER NOT NULL DEFAULT 1,
            consent_timestamp TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'New',
            internal_notes TEXT DEFAULT NULL,
            is_test INTEGER NOT NULL DEFAULT 0,
            submission_hash TEXT DEFAULT NULL,
            ip_hash TEXT DEFAULT NULL,
            email_notification_sent INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
        );
        CREATE INDEX IF NOT EXISTS idx_leads_status ON leads(status);
        CREATE INDEX IF NOT EXISTS idx_leads_service ON leads(requested_service);
        CREATE INDEX IF NOT EXISTS idx_leads_created_at ON leads(created_at);
        CREATE INDEX IF NOT EXISTS idx_leads_submission_hash ON leads(submission_hash);

        CREATE TABLE IF NOT EXISTS lead_notes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            lead_id INTEGER NOT NULL,
            admin_username TEXT NOT NULL,
            note_text TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
            FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS admin_users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            full_name TEXT NOT NULL DEFAULT 'System Administrator',
            is_active INTEGER NOT NULL DEFAULT 1,
            last_login_at TEXT DEFAULT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
        );

        CREATE TABLE IF NOT EXISTS rate_limits (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            action_key TEXT NOT NULL,
            ip_hash TEXT NOT NULL,
            attempts INTEGER NOT NULL DEFAULT 1,
            window_start TEXT NOT NULL,
            locked_until TEXT DEFAULT NULL,
            UNIQUE(action_key, ip_hash)
        );

        CREATE TABLE IF NOT EXISTS site_settings (
            setting_key TEXT PRIMARY KEY,
            setting_value TEXT DEFAULT NULL,
            updated_at TEXT NOT NULL DEFAULT (datetime('now', 'localtime'))
        );
    ");
}

/**
 * Bootstrap the initial admin account if admin_users is empty and ADMIN_INITIAL_PASSWORD is set in .env.
 */
function skyca_bootstrap_initial_admin(PDO $pdo): void
{
    $initPassword = skyca_env('ADMIN_INITIAL_PASSWORD', '');
    if ($initPassword === '' || strlen($initPassword) < 8) {
        return;
    }

    $countStmt = $pdo->query('SELECT COUNT(*) FROM admin_users');
    $count = (int) ($countStmt ? $countStmt->fetchColumn() : 0);
    if ($count > 0) {
        return;
    }

    $adminEmail = filter_var(skyca_env('ADMIN_EMAIL', BUSINESS_EMAIL), FILTER_VALIDATE_EMAIL) ?: BUSINESS_EMAIL;
    $hash = password_hash($initPassword, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare('
        INSERT INTO admin_users (username, email, password_hash, full_name, is_active, created_at, updated_at)
        VALUES (:username, :email, :hash, :full_name, 1, :now, :now)
    ');
    $now = date('Y-m-d H:i:s');
    $stmt->execute([
        ':username'  => 'admin',
        ':email'     => $adminEmail,
        ':hash'      => $hash,
        ':full_name' => 'Skyca Digital Administrator',
        ':now'       => $now,
    ]);
}
