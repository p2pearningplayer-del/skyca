<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — GLOBAL HEADER & NAVIGATION
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/seo.php';

skyca_start_session();
$settings    = skyca_get_settings();
$allServices = skyca_get_all_services();
$currentPath = $pageMeta['path'] ?? '/';
?>
<!DOCTYPE html>
<html lang="en-IN" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#033eb6">
    <?php skyca_render_seo_head($pageMeta ?? []); ?>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/assets/images/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/assets/images/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo skyca_e(skyca_asset('assets/css/styles.css')); ?>">
</head>
<body class="site-body">
    <a href="#main-content" class="skip-to-content">Skip to main content</a>

    <!-- Top Corporate Contact Strip -->
    <div class="topbar">
        <div class="container topbar-inner">
            <div class="topbar-left">
                <span class="topbar-badge">IT &amp; Software Engineering Partner</span>
                <span class="topbar-divider" aria-hidden="true">|</span>
                <span class="topbar-company"><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></span>
            </div>
            <div class="topbar-right">
                <a href="mailto:<?php echo skyca_e($settings['business_email']); ?>" class="topbar-link" aria-label="Email Skyca Digital at <?php echo skyca_e($settings['business_email']); ?>">
                    <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    <span><?php echo skyca_e($settings['business_email']); ?></span>
                </a>
                <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="topbar-link" aria-label="Call Skyca Digital at <?php echo skyca_e($settings['business_phone']); ?>">
                    <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span><?php echo skyca_e($settings['business_phone']); ?></span>
                </a>
            </div>
        </div>
    </div>

    <!-- Main Sticky Navigation Header -->
    <header class="site-header" id="site-header">
        <div class="container header-inner">
            <!-- Official Brand Logo -->
            <a href="/" class="brand-logo-link" aria-label="<?php echo skyca_e(COMPANY_LEGAL_NAME); ?> Home">
                <picture>
                    <source srcset="/assets/images/skyca-logo-horizontal.webp" type="image/webp">
                    <img
                        src="/assets/images/skyca-logo-horizontal.png"
                        alt="<?php echo skyca_e(COMPANY_LEGAL_NAME); ?> Official Logo"
                        width="320"
                        height="86"
                        class="brand-logo-img"
                        fetchpriority="high"
                    >
                </picture>
            </a>

            <!-- Desktop Navigation -->
            <nav class="desktop-nav" aria-label="Primary Navigation">
                <ul class="nav-list">
                    <li class="nav-item">
                        <a href="/" class="nav-link <?php echo $currentPath === '/' ? 'active' : ''; ?>">Home</a>
                    </li>
                    <li class="nav-item has-dropdown" id="services-dropdown-item">
                        <button
                            type="button"
                            class="nav-link dropdown-trigger <?php echo str_starts_with($currentPath, '/services') ? 'active' : ''; ?>"
                            aria-expanded="false"
                            aria-controls="services-mega-menu"
                            id="services-dropdown-btn"
                        >
                            <span>Services</span>
                            <svg class="dropdown-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                        </button>

                        <!-- Mega Menu for All 7 Official Service Categories -->
                        <div class="mega-menu" id="services-mega-menu" role="region" aria-label="Skyca Digital Services Menu">
                            <div class="mega-menu-header">
                                <div>
                                    <p class="mega-menu-eyebrow">End-to-End IT Capabilities</p>
                                    <h3 class="mega-menu-title">Enterprise Technology &amp; Software Solutions</h3>
                                </div>
                                <a href="/services" class="mega-menu-all-link">
                                    <span>View All 7 Service Categories</span>
                                    <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                            </div>
                            <div class="mega-menu-grid">
                                <?php foreach ($allServices as $srv): ?>
                                    <a href="/services/<?php echo skyca_e($srv['slug']); ?>" class="mega-service-card <?php echo $currentPath === '/services/' . $srv['slug'] ? 'active' : ''; ?>">
                                        <div class="mega-service-icon" aria-hidden="true">
                                            <?php echo $srv['icon_svg']; ?>
                                        </div>
                                        <div class="mega-service-body">
                                            <span class="mega-service-title"><?php echo skyca_e($srv['title']); ?></span>
                                            <span class="mega-service-desc"><?php echo skyca_e($srv['short_title']); ?></span>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                            <div class="mega-menu-footer">
                                <span>Not sure which technology solution fits your requirements?</span>
                                <a href="/contact#enquiry-form" class="mega-footer-cta">Request a Technical Consultation &rarr;</a>
                            </div>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="/#process" class="nav-link">Our Process</a>
                    </li>
                    <li class="nav-item">
                        <a href="/about" class="nav-link <?php echo $currentPath === '/about' ? 'active' : ''; ?>">About</a>
                    </li>
                    <li class="nav-item">
                        <a href="/faq" class="nav-link <?php echo $currentPath === '/faq' ? 'active' : ''; ?>">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a href="/contact" class="nav-link <?php echo $currentPath === '/contact' ? 'active' : ''; ?>">Contact</a>
                    </li>
                </ul>
            </nav>

            <!-- Header Action CTA -->
            <div class="header-actions">
                <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="header-phone-pill" title="Call <?php echo skyca_e($settings['business_phone']); ?>">
                    <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    <span class="sr-only-tablet"><?php echo skyca_e($settings['business_phone']); ?></span>
                </a>
                <a href="/contact#enquiry-form" class="btn btn-primary header-cta-btn">
                    <span>Get a Free Consultation</span>
                    <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>

                <!-- Mobile Navigation Toggle -->
                <button
                    type="button"
                    class="mobile-menu-toggle"
                    id="mobile-menu-toggle"
                    aria-expanded="false"
                    aria-controls="mobile-nav-drawer"
                    aria-label="Open navigation menu"
                >
                    <svg class="menu-icon-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    <svg class="menu-icon-close hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation Drawer -->
        <div class="mobile-nav-drawer hidden" id="mobile-nav-drawer" aria-label="Mobile Navigation">
            <div class="container mobile-nav-inner">
                <ul class="mobile-nav-list">
                    <li><a href="/" class="mobile-nav-link <?php echo $currentPath === '/' ? 'active' : ''; ?>">Home</a></li>
                    <li class="mobile-services-group">
                        <div class="mobile-services-header">
                            <a href="/services" class="mobile-nav-link <?php echo $currentPath === '/services' ? 'active' : ''; ?>">All Services Overview</a>
                            <button type="button" class="mobile-sub-toggle" id="mobile-services-toggle" aria-expanded="true" aria-label="Toggle service categories">
                                <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
                            </button>
                        </div>
                        <ul class="mobile-sub-list" id="mobile-services-sublist">
                            <?php foreach ($allServices as $srv): ?>
                                <li>
                                    <a href="/services/<?php echo skyca_e($srv['slug']); ?>" class="mobile-sub-link <?php echo $currentPath === '/services/' . $srv['slug'] ? 'active' : ''; ?>">
                                        <?php echo skyca_e($srv['title']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <li><a href="/#process" class="mobile-nav-link">Development Process</a></li>
                    <li><a href="/about" class="mobile-nav-link <?php echo $currentPath === '/about' ? 'active' : ''; ?>">About Us</a></li>
                    <li><a href="/faq" class="mobile-nav-link <?php echo $currentPath === '/faq' ? 'active' : ''; ?>">FAQs</a></li>
                    <li><a href="/contact" class="mobile-nav-link <?php echo $currentPath === '/contact' ? 'active' : ''; ?>">Contact Us</a></li>
                </ul>

                <div class="mobile-nav-contact">
                    <a href="/contact#enquiry-form" class="btn btn-primary btn-block">Get a Free Consultation</a>
                    <div class="mobile-direct-links">
                        <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="mobile-direct-item">
                            <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <span><?php echo skyca_e($settings['business_phone']); ?></span>
                        </a>
                        <a href="mailto:<?php echo skyca_e($settings['business_email']); ?>" class="mobile-direct-item">
                            <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <span><?php echo skyca_e($settings['business_email']); ?></span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <main id="main-content">
