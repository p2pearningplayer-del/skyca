<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — FRONT CONTROLLER & ROUTER (index.php)
 * Handles clean URL routing for all public pages, service landing pages,
 * XML sitemap, API endpoints, and 404 fallback.
 */

// Support PHP built-in server for local development and testing
if (PHP_SAPI === 'cli-server') {
    $uriPath = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
    $fullFile = __DIR__ . str_replace('/', DIRECTORY_SEPARATOR, $uriPath);
    // Block direct access to protected folders even on cli-server
    if (preg_match('#^/(config|database|includes|pages|storage|tests|\.env|\.git)#i', $uriPath)) {
        http_response_code(403);
        exit('403 Forbidden');
    }
    if ($uriPath !== '/' && is_file($fullFile)) {
        return false;
    }
}

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/services_data.php';
require_once __DIR__ . '/includes/security.php';

// Determine normalized request route
$rawRoute = $_GET['route'] ?? (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$route = trim((string) $rawRoute, '/');

// Route API and Admin endpoints when invoked via built-in server or front controller
if ($route === 'api/enquiry') {
    require __DIR__ . '/api/enquiry.php';
    exit;
}
if ($route === 'api/admin') {
    require __DIR__ . '/api/admin_api.php';
    exit;
}
if ($route === 'admin' || str_starts_with($route, 'admin/')) {
    if (str_starts_with($route, 'admin/')) {
        $subTab = substr($route, strlen('admin/'));
        if ($subTab !== '' && empty($_GET['tab'])) {
            $_GET['tab'] = $subTab;
        }
    }
    require __DIR__ . '/admin.php';
    exit;
}
if ($route === 'sitemap.xml') {
    require __DIR__ . '/pages/sitemap_xml.php';
    exit;
}

// Legacy / convenience redirects (301 Permanent)
$redirects = [
    'home'          => '/',
    'index.html'    => '/',
    'about-us'      => '/about',
    'contact-us'    => '/contact',
    'faqs'          => '/faq',
    'privacy'       => '/privacy-policy',
    'terms'         => '/terms-and-conditions',
    'cookies'       => '/cookie-policy',
];
if (isset($redirects[$route])) {
    header('Location: ' . $redirects[$route], true, 301);
    exit;
}

// Match Primary Routes
switch ($route) {
    case '':
        require __DIR__ . '/pages/home.php';
        break;

    case 'services':
        require __DIR__ . '/pages/services.php';
        break;

    case 'about':
        require __DIR__ . '/pages/about.php';
        break;

    case 'contact':
        require __DIR__ . '/pages/contact.php';
        break;

    case 'faq':
        require __DIR__ . '/pages/faq.php';
        break;

    case 'privacy-policy':
        require __DIR__ . '/pages/privacy_policy.php';
        break;

    case 'terms-and-conditions':
        require __DIR__ . '/pages/terms_conditions.php';
        break;

    case 'cookie-policy':
        require __DIR__ . '/pages/cookie_policy.php';
        break;

    case 'thank-you':
        require __DIR__ . '/pages/thank_you.php';
        break;

    default:
        // Check for individual service landing pages: /services/{slug}
        if (str_starts_with($route, 'services/')) {
            $slug = substr($route, strlen('services/'));
            $activeService = skyca_get_service_by_slug($slug);
            if ($activeService !== null) {
                require __DIR__ . '/pages/service_detail.php';
                break;
            }
        }

        // Unmatched route -> Custom 404 Page
        http_response_code(404);
        require __DIR__ . '/pages/404.php';
        break;
}
