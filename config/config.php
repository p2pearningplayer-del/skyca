<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — CORE CONFIGURATION & ENVIRONMENT LOADER
 * Safely loads environment variables from outside the web root or protected .env file.
 * Never hardcode database passwords or private API keys in this file.
 */

if (!defined('SKYCA_INIT')) {
    define('SKYCA_INIT', true);
}

define('SKYCA_ROOT', dirname(__DIR__));
define('SKYCA_STORAGE_DIR', SKYCA_ROOT . DIRECTORY_SEPARATOR . 'storage');

// Ensure storage directories exist with safe permissions
foreach (['logs', 'cache', 'rate_limits'] as $subDir) {
    $dirPath = SKYCA_STORAGE_DIR . DIRECTORY_SEPARATOR . $subDir;
    if (!is_dir($dirPath)) {
        @mkdir($dirPath, 0750, true);
    }
}

/**
 * Parse a .env file into environment variables without overwriting existing real server env vars.
 */
function skyca_load_dotenv(string $filePath): void
{
    if (!is_file($filePath) || !is_readable($filePath)) {
        return;
    }

    $lines = @file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (!is_array($lines)) {
        return;
    }

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }
        $eqPos = strpos($trimmed, '=');
        if ($eqPos === false) {
            continue;
        }
        $key = trim(substr($trimmed, 0, $eqPos));
        $val = trim(substr($trimmed, $eqPos + 1));

        if ($key === '') {
            continue;
        }

        // Strip matching surrounding quotes
        if (
            (str_starts_with($val, '"') && str_ends_with($val, '"')) ||
            (str_starts_with($val, "'") && str_ends_with($val, "'"))
        ) {
            $val = substr($val, 1, -1);
        } else {
            // Strip inline comments for unquoted values
            $hashPos = strpos($val, ' #');
            if ($hashPos !== false) {
                $val = trim(substr($val, 0, $hashPos));
            }
        }

        if (getenv($key) === false && !isset($_ENV[$key])) {
            putenv("{$key}={$val}");
            $_ENV[$key] = $val;
            $_SERVER[$key] = $val;
        }
    }
}

// 1. Check parent directory outside public_html first (Hostinger recommended private path)
skyca_load_dotenv(dirname(SKYCA_ROOT) . DIRECTORY_SEPARATOR . '.env');
// 2. Check project root .env (protected by .htaccess)
skyca_load_dotenv(SKYCA_ROOT . DIRECTORY_SEPARATOR . '.env');

/**
 * Retrieve an environment variable safely with a fallback default.
 */
function skyca_env(string $key, string $default = ''): string
{
    if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
        return (string) $_ENV[$key];
    }
    if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
        return (string) $_SERVER[$key];
    }
    $val = getenv($key);
    if ($val !== false && $val !== '') {
        return (string) $val;
    }
    return $default;
}

/**
 * Retrieve a boolean environment variable.
 */
function skyca_env_bool(string $key, bool $default = false): bool
{
    $val = strtolower(trim(skyca_env($key, $default ? 'true' : 'false')));
    return in_array($val, ['1', 'true', 'yes', 'on'], true);
}

// Configure PHP error handling safely
$isDebug = skyca_env_bool('APP_DEBUG', false);
error_reporting(E_ALL);
ini_set('display_errors', $isDebug ? '1' : '0');
ini_set('display_startup_errors', $isDebug ? '1' : '0');
ini_set('log_errors', '1');
ini_set('error_log', SKYCA_STORAGE_DIR . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . 'app_error.log');
date_default_timezone_set('Asia/Kolkata');

// Core Verified Company Constants
define('COMPANY_LEGAL_NAME', skyca_env('COMPANY_LEGAL_NAME', 'Skyca Digital Private Limited'));
define('COMPANY_BRAND_NAME', skyca_env('COMPANY_BRAND_NAME', 'Skyca Digital'));
define('BUSINESS_EMAIL', skyca_env('BUSINESS_EMAIL', 'info@skycadigital.com'));
define('BUSINESS_PHONE', skyca_env('BUSINESS_PHONE', '+91 6002064873'));
define('BUSINESS_PHONE_RAW', skyca_env('BUSINESS_PHONE_RAW', '+916002064873'));
define('SITE_URL', rtrim(skyca_env('SITE_URL', 'https://skycadigital.com'), '/'));

/**
 * Build a full canonical or relative URL cleanly.
 */
function skyca_url(string $path = ''): string
{
    $clean = '/' . ltrim($path, '/');
    return $clean === '/' ? '/' : rtrim($clean, '/');
}

/**
 * Build a full absolute canonical URL for SEO / Sitemaps / OpenGraph.
 */
function skyca_canonical_url(string $path = ''): string
{
    $clean = '/' . ltrim($path, '/');
    return $clean === '/' ? SITE_URL . '/' : SITE_URL . rtrim($clean, '/');
}

/**
 * Build an asset path with cache-busting version query string.
 */
function skyca_asset(string $path): string
{
    $clean = ltrim($path, '/');
    $fullPath = SKYCA_ROOT . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $clean);
    $ver = is_file($fullPath) ? (string) filemtime($fullPath) : '1.0.0';
    return '/' . $clean . '?v=' . $ver;
}

/**
 * Return merged company profile and marketing settings (from env + optional DB site_settings).
 */
function skyca_get_settings(): array
{
    static $cachedSettings = null;
    if ($cachedSettings !== null) {
        return $cachedSettings;
    }

    $defaults = [
        'company_legal_name'        => COMPANY_LEGAL_NAME,
        'company_brand_name'        => COMPANY_BRAND_NAME,
        'business_email'            => BUSINESS_EMAIL,
        'business_phone'            => BUSINESS_PHONE,
        'business_phone_raw'        => BUSINESS_PHONE_RAW,
        'site_url'                  => SITE_URL,
        'company_address'           => skyca_env('COMPANY_ADDRESS', ''),
        'company_city'              => skyca_env('COMPANY_CITY', ''),
        'company_state'             => skyca_env('COMPANY_STATE', ''),
        'company_postal_code'       => skyca_env('COMPANY_POSTAL_CODE', ''),
        'company_country'           => skyca_env('COMPANY_COUNTRY', 'IN'),
        'company_cin'               => skyca_env('COMPANY_CIN', ''),
        'company_gstin'             => skyca_env('COMPANY_GSTIN', ''),
        'company_working_hours'     => skyca_env('COMPANY_WORKING_HOURS', 'Monday – Saturday, 9:30 AM – 6:30 PM IST'),
        'whatsapp_number'           => skyca_env('WHATSAPP_NUMBER', ''),
        'consultation_booking_url'  => skyca_env('CONSULTATION_BOOKING_URL', ''),
        'social_linkedin_url'       => skyca_env('SOCIAL_LINKEDIN_URL', ''),
        'social_github_url'         => skyca_env('SOCIAL_GITHUB_URL', ''),
        'social_x_url'              => skyca_env('SOCIAL_X_URL', ''),
        'social_facebook_url'       => skyca_env('SOCIAL_FACEBOOK_URL', ''),
        'social_instagram_url'      => skyca_env('SOCIAL_INSTAGRAM_URL', ''),
        'google_analytics_id'       => skyca_env('GOOGLE_ANALYTICS_ID', ''),
        'google_ads_conversion_id'  => skyca_env('GOOGLE_ADS_CONVERSION_ID', ''),
        'google_ads_conversion_label' => skyca_env('GOOGLE_ADS_CONVERSION_LABEL', ''),
        'meta_pixel_id'             => skyca_env('META_PIXEL_ID', ''),
        'google_site_verification'  => skyca_env('GOOGLE_SITE_VERIFICATION', ''),
        'email_notifications_enabled' => skyca_env_bool('SMTP_ENABLED', false) ? '1' : '0',
    ];

    // Overlay optional admin-saved settings from database if available
    if (function_exists('skyca_db_or_null')) {
        try {
            $pdo = skyca_db_or_null();
            if ($pdo !== null) {
                $stmt = $pdo->query('SELECT setting_key, setting_value FROM site_settings');
                if ($stmt) {
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $k = (string) ($row['setting_key'] ?? '');
                        $v = (string) ($row['setting_value'] ?? '');
                        if ($k !== '' && array_key_exists($k, $defaults) && $v !== '') {
                            $defaults[$k] = $v;
                        }
                    }
                }
            }
        } catch (Throwable $e) {
            // Silently continue with env defaults if table not yet initialized
        }
    }

    $cachedSettings = $defaults;
    return $cachedSettings;
}
