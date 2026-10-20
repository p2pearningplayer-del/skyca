<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — SECURE ADMIN ACCOUNT SETUP / PASSWORD ROTATION
 * Usage (CLI): php database/setup_admin.php <email> <new_password> [username]
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI execution only.');
}

require_once dirname(__DIR__) . '/includes/db.php';

$email    = $argv[1] ?? skyca_env('ADMIN_EMAIL', BUSINESS_EMAIL);
$password = $argv[2] ?? skyca_env('ADMIN_INITIAL_PASSWORD', '');
$username = $argv[3] ?? 'admin';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "[ERROR] Invalid admin email address provided.\n");
    exit(1);
}

if (strlen($password) < 10) {
    fwrite(STDERR, "[ERROR] Admin password must be at least 10 characters long.\n");
    fwrite(STDERR, "Usage: php database/setup_admin.php info@skycadigital.com YourStrongPassword123!\n");
    exit(1);
}

try {
    $pdo = skyca_db();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $now = date('Y-m-d H:i:s');

    $check = $pdo->prepare('SELECT id FROM admin_users WHERE username = :username OR email = :email LIMIT 1');
    $check->execute([':username' => $username, ':email' => $email]);
    $existing = $check->fetch();

    if ($existing) {
        $upd = $pdo->prepare('UPDATE admin_users SET email = :email, password_hash = :hash, is_active = 1, updated_at = :now WHERE id = :id');
        $upd->execute([
            ':email' => $email,
            ':hash'  => $hash,
            ':now'   => $now,
            ':id'    => $existing['id'],
        ]);
        echo "[SUCCESS] Updated credentials for existing admin user `{$username}` ({$email}).\n";
    } else {
        $ins = $pdo->prepare('
            INSERT INTO admin_users (username, email, password_hash, full_name, is_active, created_at, updated_at)
            VALUES (:username, :email, :hash, :full_name, 1, :now, :now)
        ');
        $ins->execute([
            ':username'  => $username,
            ':email'     => $email,
            ':hash'      => $hash,
            ':full_name' => 'Skyca Digital Administrator',
            ':now'       => $now,
        ]);
        echo "[SUCCESS] Created admin user `{$username}` ({$email}).\n";
    }
} catch (Throwable $e) {
    fwrite(STDERR, "[ERROR] Failed to configure admin account: " . $e->getMessage() . "\n");
    exit(1);
}
