<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — CUSTOM 404 NOT FOUND PAGE (pages/404.php)
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';

http_response_code(404);

$pageMeta = [
    'title'       => 'Page Not Found (404) | Skyca Digital Private Limited',
    'description' => 'The page you requested could not be found on skycadigital.com.',
    'path'        => '/404',
    'robots'      => 'noindex, follow',
];

require dirname(__DIR__) . '/includes/header.php';
?>

<section class="section bg-slate not-found-section">
    <div class="container container-narrow text-center">
        <div class="not-found-card">
            <span class="not-found-code">404</span>
            <h1 class="not-found-title">Page Not Found</h1>
            <p class="not-found-desc">
                The page you are looking for may have been moved, renamed, or does not exist. Use the links below to explore our IT services or get in touch with our team.
            </p>
            <div class="hero-cta-group justify-center mt-6">
                <a href="/" class="btn btn-primary btn-lg">Return to Homepage</a>
                <a href="/services" class="btn btn-secondary btn-lg">Explore Our Services</a>
                <a href="/contact" class="btn btn-outline btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
