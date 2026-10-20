<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — NON-DESTRUCTIVE DATABASE MIGRATION SCRIPT
 * Usage (CLI): php database/migrate.php
 * Inspects existing database tables before running CREATE TABLE IF NOT EXISTS.
 * Never drops, truncates, or overwrites existing tables.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI execution only.');
}

require_once dirname(__DIR__) . '/includes/db.php';

echo "=================================================================\n";
echo " SKYCA DIGITAL PRIVATE LIMITED — DATABASE MIGRATION RUNNER\n";
echo " Target Database: " . skyca_env('DATABASE_NAME', 'u591469534_skyca') . "\n";
echo "=================================================================\n\n";

try {
    $pdo = skyca_db();
    $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    echo "[INFO] Connected successfully using driver: {$driver}\n";

    // Inspect existing tables
    $existingTables = [];
    if ($driver === 'mysql') {
        $stmt = $pdo->query('SHOW TABLES');
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $existingTables[] = $row[0];
        }
    } else {
        $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            $existingTables[] = $row[0];
        }
    }

    echo "[INFO] Existing tables detected (" . count($existingTables) . "): " . (empty($existingTables) ? 'None' : implode(', ', $existingTables)) . "\n";

    if ($driver === 'mysql') {
        $schemaSql = (string) file_get_contents(__DIR__ . '/schema.sql');
        $statements = preg_split('/;\s*[\r\n]+/', $schemaSql) ?: [];
        foreach ($statements as $statement) {
            $clean = trim($statement);
            if ($clean === '' || str_starts_with($clean, '--')) {
                continue;
            }
            $pdo->exec($clean);
        }
    } else {
        skyca_ensure_schema_sqlite($pdo);
    }

    // Verify required tables
    $required = ['leads', 'lead_notes', 'admin_users', 'rate_limits', 'site_settings'];
    foreach ($required as $table) {
        $countStmt = $pdo->query("SELECT COUNT(*) FROM {$table}");
        $rowCount = (int) $countStmt->fetchColumn();
        echo "  [OK] Table `{$table}` verified ({$rowCount} existing row(s) preserved).\n";
    }

    echo "\n[SUCCESS] Database migration completed safely without destructive operations.\n";
} catch (Throwable $e) {
    fwrite(STDERR, "[ERROR] Migration failed: " . $e->getMessage() . "\n");
    exit(1);
}
