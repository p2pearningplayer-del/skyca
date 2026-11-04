<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — DATABASE CONNECTION & SCHEMA MANAGER
 * Uses PDO with real parameterized queries (ATTR_EMULATE_PREPARES = false).
 * Connects to MySQL (u591469534_skyca) when credentials are provided via .env or
 * Admin runtime configuration, and seamlessly falls back to a protected local PDO
 * database (storage/skyca_database.sqlite) so the live website and /admin portal
 * work out-of-the-box immediately after Git deployment.
 */

require_once dirname(__DIR__) . '/config/config.php';

/**
 * Load optional runtime DB credentials configured via the Admin Panel (stored outside Git in storage/).
 */
function skyca_load_runtime_db_config(): array
{
    $cfgFile = SKYCA_STORAGE_DIR . DIRECTORY_SEPARATOR . 'db_config.json';
    if (is_file($cfgFile) && is_readable($cfgFile)) {
        $raw = @file_get_contents($cfgFile);
        $decoded = is_string($raw) ? json_decode($raw, true) : null;
        if (is_array($decoded)) {
            return $decoded;
        }
    }
    return [];
}

/**
 * Return the active PDO database connection.
 */
function skyca_db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $driver = strtolower(trim(skyca_env('DATABASE_DRIVER', 'mysql')));
    $runtimeCfg = skyca_load_runtime_db_config();

    try {
        if ($driver === 'sqlite') {
            $sqlitePath = skyca_env('TEST_SQLITE_PATH', SKYCA_STORAGE_DIR . DIRECTORY_SEPARATOR . 'skyca_test.sqlite');
            $pdo = skyca_connect_sqlite($sqlitePath);
        } else {
            $host    = $runtimeCfg['db_host'] ?? skyca_env('DATABASE_HOST', 'localhost');
            $port    = $runtimeCfg['db_port'] ?? skyca_env('DATABASE_PORT', '3306');
            $dbName  = $runtimeCfg['db_name'] ?? skyca_env('DATABASE_NAME', 'u591469534_skyca');
            $user    = $runtimeCfg['db_user'] ?? skyca_env('DATABASE_USER', '');
            $pass    = $runtimeCfg['db_pass'] ?? skyca_env('DATABASE_PASSWORD', '');
            $charset = skyca_env('DATABASE_CHARSET', 'utf8mb4');

            if ($user !== '' && $pass !== '') {
                try {
                    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $host, $port, $dbName, $charset);
                    $pdo = new PDO($dsn, $user, $pass, [
                        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::ATTR_EMULATE_PREPARES   => false,
                        PDO::ATTR_TIMEOUT            => 8,
                    ]);
                    $pdo->exec("SET time_zone = '+05:30'");
                    skyca_ensure_schema_mysql($pdo);
                } catch (Throwable $mysqlErr) {
                    error_log('[SkycaDB Warning] MySQL connection failed, using protected local PDO storage: ' . $mysqlErr->getMessage());
                    $prodSqlite = SKYCA_STORAGE_DIR . DIRECTORY_SEPARATOR . 'skyca_database.sqlite';
                    $pdo = skyca_connect_sqlite($prodSqlite);
                }
            } else {
                // Seamless fallback to protected local PDO database when MySQL .env credentials are not yet entered
                $prodSqlite = SKYCA_STORAGE_DIR . DIRECTORY_SEPARATOR . 'skyca_database.sqlite';
                $pdo = skyca_connect_sqlite($prodSqlite);
            }
        }

        skyca_bootstrap_initial_admin($pdo);
        return $pdo;
    } catch (Throwable $e) {
        error_log('[SkycaDB Error] Connection failed: ' . $e->getMessage());
        throw new RuntimeException('Unable to connect to the database service at this time.');
    }
}

/**
 * Create a hardened PDO SQLite connection and ensure schema tables exist.
 */
function skyca_connect_sqlite(string $sqlitePath): PDO
{
    $dir = dirname($sqlitePath);
    if (!is_dir($dir)) {
        @mkdir($dir, 0750, true);
    }
    $pdo = new PDO('sqlite:' . $sqlitePath, null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $pdo->exec('PRAGMA foreign_keys = ON;');
    $pdo->exec('PRAGMA journal_mode = WAL;');
    skyca_ensure_schema_sqlite($pdo);
    return $pdo;
}

/**
 * Return PDO connection if available, or null without throwing.
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

    $stmt = $pdo->query("SHOW TABLES LIKE 'leads'");
    $leadsExists = (bool) $stmt->fetchColumn();
    $stmt2 = $pdo->query("SHOW TABLES LIKE 'admin_users'");
    $adminExists = (bool) $stmt2->fetchColumn();

    if ($leadsExists && $adminExists) {
        return;
    }

    $sql = (string) file_get_contents($schemaFile);
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
 * Ensure schema compatibility for PDO SQLite storage.
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
 * Bootstrap the initial admin account if admin_users is empty.
 * Uses ADMIN_INITIAL_PASSWORD from .env if set, or initializes the default
 * bcrypt hash so the administrator can log in immediately after Git deployment
 * and rotate their password inside /admin?tab=password.
 */
function skyca_bootstrap_initial_admin(PDO $pdo): void
{
    $countStmt = $pdo->query('SELECT COUNT(*) FROM admin_users');
    $count = (int) ($countStmt ? $countStmt->fetchColumn() : 0);
    if ($count > 0) {
        return;
    }

    $adminEmail = filter_var(skyca_env('ADMIN_EMAIL', BUSINESS_EMAIL), FILTER_VALIDATE_EMAIL) ?: BUSINESS_EMAIL;
    $initPassword = skyca_env('ADMIN_INITIAL_PASSWORD', '');

    if ($initPassword !== '' && strlen($initPassword) >= 8) {
        $hash = password_hash($initPassword, PASSWORD_DEFAULT);
    } else {
        // Pre-computed bcrypt hash for initial bootstrap (changeable anytime in /admin?tab=password)
        $hash = '$2y$10$9t5r7P8wV2kX1mQ4zL6nOeH3jC5vB8yN0pM2qR4sT6uV8wX0yZ2a.';
        // Generate valid standard PHP bcrypt hash for initial setup
        $hash = password_hash('Skyca@2026#Admin', PASSWORD_DEFAULT);
    }

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
