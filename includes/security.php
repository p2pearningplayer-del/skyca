<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — SECURITY, SESSION, CSRF, XSS & RATE LIMITING
 */

require_once dirname(__DIR__) . '/config/config.php';

/**
 * Start a hardened PHP session if not already active.
 */
function skyca_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    if (headers_sent()) {
        return;
    }

    $isHttps = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443) ||
        (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
    );

    session_name('SKYCA_SESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    @session_start();
}

/**
 * Strictly escape any string for safe HTML rendering (prevents Reflected & Stored XSS).
 */
function skyca_e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Sanitize and bound text input length, removing null bytes and unsafe control characters.
 */
function skyca_clean_input(?string $value, int $maxLength = 255, bool $allowNewlines = false): string
{
    $str = trim((string) $value);
    if ($str === '') {
        return '';
    }
    // Strip null bytes
    $str = str_replace("\0", '', $str);

    if ($allowNewlines) {
        // Normalize line endings and strip non-printable control chars except \n
        $str = str_replace(["\r\n", "\r"], "\n", $str);
        $str = (string) preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $str);
    } else {
        $str = (string) preg_replace('/[\x00-\x1F\x7F]/u', ' ', $str);
        $str = (string) preg_replace('/\s+/u', ' ', $str);
    }

    if (function_exists('mb_substr')) {
        return mb_substr(trim($str), 0, $maxLength, 'UTF-8');
    }
    return substr(trim($str), 0, $maxLength);
}

/**
 * Generate or return the active session CSRF token.
 */
function skyca_csrf_token(): string
{
    skyca_start_session();
    if (empty($_SESSION['skyca_csrf_token']) || !is_string($_SESSION['skyca_csrf_token'])) {
        $_SESSION['skyca_csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['skyca_csrf_token'];
}

/**
 * Verify a submitted CSRF token in constant time.
 */
function skyca_verify_csrf(?string $submittedToken): bool
{
    skyca_start_session();
    $sessionToken = $_SESSION['skyca_csrf_token'] ?? '';
    if (!is_string($sessionToken) || $sessionToken === '' || !is_string($submittedToken) || $submittedToken === '') {
        return false;
    }
    return hash_equals($sessionToken, $submittedToken);
}

/**
 * Create a signed timestamp token for enquiry forms to detect instant bot submissions.
 */
function skyca_form_timestamp_token(): string
{
    $ts = (string) time();
    $secret = skyca_env('IP_HASH_SALT', 'skyca_default_hmac_key_2026');
    $sig = hash_hmac('sha256', $ts, $secret);
    return base64_encode($ts . '.' . $sig);
}

/**
 * Verify form timestamp token (must be valid signature and within 2 hours).
 */
function skyca_verify_form_timestamp(?string $token, int $minElapsedSeconds = 0): bool
{
    if (!is_string($token) || $token === '') {
        return false;
    }
    $decoded = base64_decode($token, true);
    if (!is_string($decoded) || !str_contains($decoded, '.')) {
        return false;
    }
    [$tsStr, $sig] = explode('.', $decoded, 2);
    if (!ctype_digit($tsStr)) {
        return false;
    }
    $secret = skyca_env('IP_HASH_SALT', 'skyca_default_hmac_key_2026');
    $expected = hash_hmac('sha256', $tsStr, $secret);
    if (!hash_equals($expected, $sig)) {
        return false;
    }
    $elapsed = time() - (int) $tsStr;
    return ($elapsed >= $minElapsedSeconds && $elapsed <= 7200);
}

/**
 * Return a privacy-preserving HMAC-SHA256 hash of the client IP address.
 */
function skyca_client_ip_hash(): string
{
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP']
        ?? $_SERVER['HTTP_X_REAL_IP']
        ?? $_SERVER['REMOTE_ADDR']
        ?? '127.0.0.1';

    if (str_contains((string) $ip, ',')) {
        $parts = explode(',', (string) $ip);
        $ip = trim($parts[0]);
    }

    $salt = skyca_env('IP_HASH_SALT', 'skyca_default_hmac_key_2026');
    return hash_hmac('sha256', (string) $ip, $salt);
}

/**
 * Enforce rate limiting per action_key and hashed client IP.
 * Returns ['allowed' => bool, 'retry_after' => int].
 */
function skyca_check_rate_limit(
    string $actionKey,
    int $maxAttempts = 8,
    int $windowSeconds = 900,
    int $lockoutSeconds = 900,
    bool $increment = true
): array {
    $ipHash = skyca_client_ip_hash();
    $now = time();

    // Use file-backed rate limit store in storage/rate_limits for reliability even before DB connection
    $fileKey = hash('sha256', $actionKey . ':' . $ipHash);
    $filePath = SKYCA_STORAGE_DIR . DIRECTORY_SEPARATOR . 'rate_limits' . DIRECTORY_SEPARATOR . $fileKey . '.json';

    $record = [
        'attempts'     => 0,
        'window_start' => $now,
        'locked_until' => 0,
    ];

    if (is_file($filePath)) {
        $raw = @file_get_contents($filePath);
        $decoded = is_string($raw) ? json_decode($raw, true) : null;
        if (is_array($decoded)) {
            $record = array_merge($record, $decoded);
        }
    }

    if ((int) $record['locked_until'] > $now) {
        return [
            'allowed'     => false,
            'retry_after' => (int) $record['locked_until'] - $now,
        ];
    }

    if (($now - (int) $record['window_start']) > $windowSeconds) {
        $record['attempts'] = 0;
        $record['window_start'] = $now;
        $record['locked_until'] = 0;
    }

    if ($increment) {
        $record['attempts'] = (int) $record['attempts'] + 1;
        if ($record['attempts'] > $maxAttempts) {
            $record['locked_until'] = $now + $lockoutSeconds;
            @file_put_contents($filePath, json_encode($record), LOCK_EX);
            return [
                'allowed'     => false,
                'retry_after' => $lockoutSeconds,
            ];
        }
        @file_put_contents($filePath, json_encode($record), LOCK_EX);
    }

    return [
        'allowed'     => true,
        'retry_after' => 0,
    ];
}

/**
 * Reset rate limit counter upon successful admin login.
 */
function skyca_reset_rate_limit(string $actionKey): void
{
    $ipHash = skyca_client_ip_hash();
    $fileKey = hash('sha256', $actionKey . ':' . $ipHash);
    $filePath = SKYCA_STORAGE_DIR . DIRECTORY_SEPARATOR . 'rate_limits' . DIRECTORY_SEPARATOR . $fileKey . '.json';
    if (is_file($filePath)) {
        @unlink($filePath);
    }
}

/**
 * Sanitize cell value against CSV Formula Injection when exporting leads to CSV.
 */
function skyca_csv_safe(?string $value): string
{
    $str = (string) $value;
    if ($str === '') {
        return '';
    }
    $firstChar = $str[0];
    if (in_array($firstChar, ['=', '+', '-', '@', "\t", "\r"], true)) {
        return "'" . $str;
    }
    return $str;
}

/**
 * Check whether an administrator is currently logged in.
 */
function skyca_is_admin_logged_in(): bool
{
    skyca_start_session();
    return !empty($_SESSION['skyca_admin_id']) && !empty($_SESSION['skyca_admin_username']);
}

/**
 * Return current logged-in admin details.
 */
function skyca_current_admin(): ?array
{
    skyca_start_session();
    if (!skyca_is_admin_logged_in()) {
        return null;
    }
    return [
        'id'        => (int) $_SESSION['skyca_admin_id'],
        'username'  => (string) $_SESSION['skyca_admin_username'],
        'email'     => (string) ($_SESSION['skyca_admin_email'] ?? ''),
        'full_name' => (string) ($_SESSION['skyca_admin_name'] ?? 'Administrator'),
    ];
}

/**
 * Generate a unique, human-readable lead reference code (e.g., SKY-20261021-8F4A).
 */
function skyca_generate_lead_reference(): string
{
    $datePart = date('Ymd');
    $randPart = strtoupper(bin2hex(random_bytes(3)));
    return "SKY-{$datePart}-{$randPart}";
}
