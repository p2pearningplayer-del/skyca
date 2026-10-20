<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — SEO METADATA, JSON-LD SCHEMA & ANALYTICS READINESS
 * Generates page-specific titles, descriptions, canonical URLs, Open Graph metadata,
 * BreadcrumbList, Organization, WebSite, Service, and FAQPage structured data.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once __DIR__ . '/security.php';

/**
 * Render complete <head> SEO, Open Graph, Schema.org JSON-LD, and consent-aware tracking config.
 */
function skyca_render_seo_head(array $pageMeta = []): void
{
    $settings = skyca_get_settings();

    $title       = $pageMeta['title'] ?? 'Skyca Digital Private Limited | IT Services & Custom Software Development Company';
    $description = $pageMeta['description'] ?? 'Skyca Digital Private Limited provides custom software development, web & mobile applications, SaaS & cloud architecture, AI solutions, cybersecurity, data analytics, and e-commerce technology services.';
    $path        = $pageMeta['path'] ?? '/';
    $canonical   = $pageMeta['canonical'] ?? skyca_canonical_url($path);
    $robots      = $pageMeta['robots'] ?? 'index, follow, max-image-preview:large, max-snippet:-1';
    $ogType      = $pageMeta['og_type'] ?? 'website';
    $ogImage     = $pageMeta['og_image'] ?? (SITE_URL . '/assets/images/og-cover.jpg');
    $breadcrumbs = $pageMeta['breadcrumbs'] ?? [];
    $serviceData = $pageMeta['service'] ?? null;
    $faqs        = $pageMeta['faqs'] ?? [];

    echo '<title>' . skyca_e($title) . "</title>\n";
    echo '    <meta name="description" content="' . skyca_e($description) . '">' . "\n";
    echo '    <meta name="robots" content="' . skyca_e($robots) . '">' . "\n";
    echo '    <link rel="canonical" href="' . skyca_e($canonical) . '">' . "\n";

    if (!empty($settings['google_site_verification'])) {
        echo '    <meta name="google-site-verification" content="' . skyca_e($settings['google_site_verification']) . '">' . "\n";
    }

    // Open Graph Metadata
    echo '    <meta property="og:locale" content="en_IN">' . "\n";
    echo '    <meta property="og:type" content="' . skyca_e($ogType) . '">' . "\n";
    echo '    <meta property="og:site_name" content="' . skyca_e(COMPANY_LEGAL_NAME) . '">' . "\n";
    echo '    <meta property="og:title" content="' . skyca_e($title) . '">' . "\n";
    echo '    <meta property="og:description" content="' . skyca_e($description) . '">' . "\n";
    echo '    <meta property="og:url" content="' . skyca_e($canonical) . '">' . "\n";
    echo '    <meta property="og:image" content="' . skyca_e($ogImage) . '">' . "\n";
    echo '    <meta property="og:image:width" content="1200">' . "\n";
    echo '    <meta property="og:image:height" content="630">' . "\n";
    echo '    <meta property="og:image:alt" content="' . skyca_e(COMPANY_LEGAL_NAME . ' — Technology Solutions') . '">' . "\n";

    // Twitter / X Card Metadata
    echo '    <meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '    <meta name="twitter:title" content="' . skyca_e($title) . '">' . "\n";
    echo '    <meta name="twitter:description" content="' . skyca_e($description) . '">' . "\n";
    echo '    <meta name="twitter:image" content="' . skyca_e($ogImage) . '">' . "\n";

    // Build JSON-LD Graph (Strictly factual verified data only)
    $graph = [];

    // 1. Organization Schema
    $orgSchema = [
        '@type' => 'Organization',
        '@id'   => SITE_URL . '/#organization',
        'name'  => COMPANY_LEGAL_NAME,
        'legalName' => COMPANY_LEGAL_NAME,
        'alternateName' => COMPANY_BRAND_NAME,
        'url'   => SITE_URL . '/',
        'logo'  => [
            '@type'  => 'ImageObject',
            'url'    => SITE_URL . '/assets/images/skyca-logo-original.jpg',
            'width'  => 800,
            'height' => 590,
        ],
        'email' => $settings['business_email'],
        'telephone' => $settings['business_phone'],
        'contactPoint' => [
            [
                '@type' => 'ContactPoint',
                'telephone' => $settings['business_phone'],
                'email' => $settings['business_email'],
                'contactType' => 'customer support and sales',
                'availableLanguage' => ['English', 'Hindi'],
            ],
        ],
    ];

    // Add verified social profiles only if configured
    $sameAs = [];
    foreach (['social_linkedin_url', 'social_github_url', 'social_x_url', 'social_facebook_url', 'social_instagram_url'] as $socialKey) {
        if (!empty($settings[$socialKey]) && filter_var($settings[$socialKey], FILTER_VALIDATE_URL)) {
            $sameAs[] = $settings[$socialKey];
        }
    }
    if (!empty($sameAs)) {
        $orgSchema['sameAs'] = $sameAs;
    }

    // Include LocalBusiness / Physical Address schema ONLY if verified physical address details are configured
    if (!empty($settings['company_address']) && !empty($settings['company_city'])) {
        $orgSchema['address'] = [
            '@type'           => 'PostalAddress',
            'streetAddress'   => $settings['company_address'],
            'addressLocality' => $settings['company_city'],
            'addressRegion'   => $settings['company_state'],
            'postalCode'      => $settings['company_postal_code'],
            'addressCountry'  => $settings['company_country'] ?: 'IN',
        ];
    }

    $graph[] = $orgSchema;

    // 2. WebSite Schema
    $graph[] = [
        '@type' => 'WebSite',
        '@id'   => SITE_URL . '/#website',
        'url'   => SITE_URL . '/',
        'name'  => COMPANY_LEGAL_NAME,
        'publisher' => ['@id' => SITE_URL . '/#organization'],
        'inLanguage' => 'en-IN',
    ];

    // 3. BreadcrumbList Schema (if breadcrumbs supplied)
    if (!empty($breadcrumbs)) {
        $itemList = [];
        foreach ($breadcrumbs as $idx => $crumb) {
            $itemList[] = [
                '@type'    => 'ListItem',
                'position' => $idx + 1,
                'name'     => $crumb['name'],
                'item'     => skyca_canonical_url($crumb['path']),
            ];
        }
        $graph[] = [
            '@type'           => 'BreadcrumbList',
            '@id'             => $canonical . '#breadcrumb',
            'itemListElement' => $itemList,
        ];
    }

    // 4. Service Schema (if on an individual service landing page)
    if (is_array($serviceData) && !empty($serviceData['title'])) {
        $hasOfferCatalog = [];
        if (!empty($serviceData['sub_services'])) {
            foreach ($serviceData['sub_services'] as $sub) {
                $hasOfferCatalog[] = [
                    '@type' => 'Offer',
                    'itemOffered' => [
                        '@type' => 'Service',
                        'name'  => $sub['name'],
                        'description' => $sub['description'],
                    ],
                ];
            }
        }
        $graph[] = [
            '@type'       => 'Service',
            '@id'         => $canonical . '#service',
            'name'        => $serviceData['title'],
            'description' => $serviceData['summary'] ?? $description,
            'url'         => $canonical,
            'provider'    => ['@id' => SITE_URL . '/#organization'],
            'areaServed'  => [
                '@type' => 'Country',
                'name'  => 'India',
            ],
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name'  => $serviceData['title'] . ' Capabilities',
                'itemListElement' => $hasOfferCatalog,
            ],
        ];
    }

    // 5. FAQPage Schema (if FAQs present on the page)
    if (!empty($faqs)) {
        $faqEntities = [];
        foreach ($faqs as $faq) {
            if (!empty($faq['q']) && !empty($faq['a'])) {
                $faqEntities[] = [
                    '@type' => 'Question',
                    'name'  => $faq['q'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text'  => $faq['a'],
                    ],
                ];
            }
        }
        if (!empty($faqEntities)) {
            $graph[] = [
                '@type'      => 'FAQPage',
                '@id'        => $canonical . '#faq',
                'mainEntity' => $faqEntities,
            ];
        }
    }

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@graph'   => $graph,
    ];

    echo '    <script type="application/ld+json">' . json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "</script>\n";

    // Output Consent-Aware Marketing & Analytics Configuration (Never invents IDs; loads only if configured)
    $trackingConfig = [
        'ga4Id'              => $settings['google_analytics_id'] ?: null,
        'googleAdsId'        => $settings['google_ads_conversion_id'] ?: null,
        'googleAdsLabel'     => $settings['google_ads_conversion_label'] ?: null,
        'metaPixelId'        => $settings['meta_pixel_id'] ?: null,
        'isThankYouConversion' => !empty($pageMeta['trigger_conversion']),
        'leadRef'            => $pageMeta['conversion_ref'] ?? null,
    ];
    echo '    <script>window.SKYCA_TRACKING = ' . json_encode($trackingConfig, JSON_UNESCAPED_SLASHES) . ";</script>\n";
}

/**
 * Render clean HTML Breadcrumb UI component for inner pages.
 */
function skyca_render_breadcrumbs(array $breadcrumbs): void
{
    if (empty($breadcrumbs)) {
        return;
    }
    echo '<nav aria-label="Breadcrumb" class="breadcrumb-nav">';
    echo '<ol class="breadcrumb-list">';
    $lastIdx = count($breadcrumbs) - 1;
    foreach ($breadcrumbs as $idx => $crumb) {
        $isLast = ($idx === $lastIdx);
        echo '<li class="breadcrumb-item">';
        if ($isLast) {
            echo '<span aria-current="page" class="breadcrumb-current">' . skyca_e($crumb['name']) . '</span>';
        } else {
            echo '<a href="' . skyca_e(skyca_url($crumb['path'])) . '" class="breadcrumb-link">' . skyca_e($crumb['name']) . '</a>';
            echo '<span class="breadcrumb-sep" aria-hidden="true">/</span>';
        }
        echo '</li>';
    }
    echo '</ol>';
    echo '</nav>';
}
