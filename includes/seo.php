<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — ENTERPRISE SEO METADATA, JSON-LD SCHEMA & ANALYTICS ENGINE
 * Generates page-specific titles, descriptions, focus keywords, canonical URLs, hreflang tags,
 * Open Graph & Twitter/X cards, and comprehensive Schema.org JSON-LD graphs
 * (Organization + ProfessionalService, WebSite, WebPage/AboutPage/ContactPage, ItemList,
 * BreadcrumbList, Service OfferCatalog, and FAQPage).
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';
require_once __DIR__ . '/security.php';

/**
 * Generate contextual B2B focus keywords for a given page or service.
 */
function skyca_resolve_page_keywords(array $pageMeta, array $settings): string
{
    if (!empty($pageMeta['keywords'])) {
        return (string) $pageMeta['keywords'];
    }

    $serviceData = $pageMeta['service'] ?? null;
    if (is_array($serviceData) && !empty($serviceData['title'])) {
        $kw = [
            $serviceData['title'],
            $serviceData['short_title'] ?? '',
            COMPANY_LEGAL_NAME,
            COMPANY_BRAND_NAME,
        ];
        if (!empty($serviceData['sub_services'])) {
            foreach ($serviceData['sub_services'] as $sub) {
                if (!empty($sub['name'])) {
                    $kw[] = $sub['name'];
                }
            }
        }
        if (!empty($serviceData['tech_stack'])) {
            foreach (array_slice($serviceData['tech_stack'], 0, 4) as $tech) {
                $kw[] = $tech . ' development India';
            }
        }
        return implode(', ', array_unique(array_filter($kw)));
    }

    return (string) ($settings['seo_default_keywords'] ?? 'Skyca Digital Private Limited, IT services India, custom software development, web and mobile app development, SaaS and cloud solutions, AI consulting, cybersecurity, data analytics, e-commerce solutions');
}

/**
 * Render complete <head> SEO, Open Graph, Hreflang, Schema.org JSON-LD, and consent-aware tracking config.
 */
function skyca_render_seo_head(array $pageMeta = []): void
{
    $settings    = skyca_get_settings();
    $allServices = skyca_get_all_services();

    $path = $pageMeta['path'] ?? '/';

    // Allow Admin SEO Hub to override homepage title & description if configured
    $defaultHomeTitle = !empty($settings['seo_home_title'])
        ? $settings['seo_home_title']
        : 'Skyca Digital Private Limited | IT Services, Custom Software, Web, Cloud & AI Solutions';
    $defaultHomeDesc = !empty($settings['seo_home_description'])
        ? $settings['seo_home_description']
        : 'Technology Solutions That Move Your Business Forward. Skyca Digital Private Limited delivers custom software development, web & mobile apps, SaaS & cloud architecture, AI integration, cybersecurity, data analytics, and e-commerce solutions.';

    $title       = ($path === '/' && !empty($settings['seo_home_title']))
        ? $settings['seo_home_title']
        : ($pageMeta['title'] ?? $defaultHomeTitle);
    $description = ($path === '/' && !empty($settings['seo_home_description']))
        ? $settings['seo_home_description']
        : ($pageMeta['description'] ?? $defaultHomeDesc);

    $keywords    = skyca_resolve_page_keywords($pageMeta, $settings);
    $canonical   = $pageMeta['canonical'] ?? skyca_canonical_url($path);
    $robots      = $pageMeta['robots'] ?? 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
    $ogType      = $pageMeta['og_type'] ?? 'website';
    $ogImage     = $pageMeta['og_image'] ?? (SITE_URL . '/assets/images/og-cover.jpg');
    $breadcrumbs = $pageMeta['breadcrumbs'] ?? [];
    $serviceData = $pageMeta['service'] ?? null;
    $faqs        = $pageMeta['faqs'] ?? [];

    // Core SEO Meta Tags
    echo '<title>' . skyca_e($title) . "</title>\n";
    echo '    <meta name="description" content="' . skyca_e($description) . '">' . "\n";
    echo '    <meta name="keywords" content="' . skyca_e($keywords) . '">' . "\n";
    echo '    <meta name="author" content="' . skyca_e(COMPANY_LEGAL_NAME) . '">' . "\n";
    echo '    <meta name="publisher" content="' . skyca_e(COMPANY_LEGAL_NAME) . '">' . "\n";
    echo '    <meta name="robots" content="' . skyca_e($robots) . '">' . "\n";
    echo '    <meta name="googlebot" content="' . skyca_e($robots) . '">' . "\n";
    echo '    <meta name="geo.region" content="IN">' . "\n";
    echo '    <meta name="geo.country" content="India">' . "\n";
    echo '    <link rel="canonical" href="' . skyca_e($canonical) . '">' . "\n";

    // Hreflang Regional & Global Targeting
    if (!str_contains($robots, 'noindex')) {
        echo '    <link rel="alternate" hreflang="en-in" href="' . skyca_e($canonical) . '">' . "\n";
        echo '    <link rel="alternate" hreflang="en" href="' . skyca_e($canonical) . '">' . "\n";
        echo '    <link rel="alternate" hreflang="x-default" href="' . skyca_e($canonical) . '">' . "\n";
    }

    // Search Engine Verification Meta Tags
    if (!empty($settings['google_site_verification'])) {
        echo '    <meta name="google-site-verification" content="' . skyca_e($settings['google_site_verification']) . '">' . "\n";
    }
    if (!empty($settings['bing_site_verification'])) {
        echo '    <meta name="msvalidate.01" content="' . skyca_e($settings['bing_site_verification']) . '">' . "\n";
    }

    // Open Graph Metadata
    echo '    <meta property="og:locale" content="en_IN">' . "\n";
    echo '    <meta property="og:type" content="' . skyca_e($ogType) . '">' . "\n";
    echo '    <meta property="og:site_name" content="' . skyca_e(COMPANY_LEGAL_NAME) . '">' . "\n";
    echo '    <meta property="og:title" content="' . skyca_e($title) . '">' . "\n";
    echo '    <meta property="og:description" content="' . skyca_e($description) . '">' . "\n";
    echo '    <meta property="og:url" content="' . skyca_e($canonical) . '">' . "\n";
    echo '    <meta property="og:image" content="' . skyca_e($ogImage) . '">' . "\n";
    echo '    <meta property="og:image:secure_url" content="' . skyca_e($ogImage) . '">' . "\n";
    echo '    <meta property="og:image:width" content="1200">' . "\n";
    echo '    <meta property="og:image:height" content="630">' . "\n";
    echo '    <meta property="og:image:alt" content="' . skyca_e($title) . '">' . "\n";

    // Twitter / X Card Metadata
    echo '    <meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '    <meta name="twitter:title" content="' . skyca_e($title) . '">' . "\n";
    echo '    <meta name="twitter:description" content="' . skyca_e($description) . '">' . "\n";
    echo '    <meta name="twitter:image" content="' . skyca_e($ogImage) . '">' . "\n";

    // Build Rich Schema.org JSON-LD Graph (Strictly factual verified data only)
    $graph = [];

    $knowsAbout = [];
    foreach ($allServices as $srvItem) {
        $knowsAbout[] = $srvItem['title'];
        foreach ($srvItem['sub_services'] as $subItem) {
            $knowsAbout[] = $subItem['name'];
        }
    }

    // 1. Organization & ProfessionalService Schema
    $orgSchema = [
        '@type'         => ['Organization', 'ProfessionalService'],
        '@id'           => SITE_URL . '/#organization',
        'name'          => COMPANY_LEGAL_NAME,
        'legalName'     => COMPANY_LEGAL_NAME,
        'alternateName' => COMPANY_BRAND_NAME,
        'slogan'        => 'Technology Solutions That Move Your Business Forward.',
        'description'   => $defaultHomeDesc,
        'url'           => SITE_URL . '/',
        'image'         => SITE_URL . '/assets/images/og-cover.jpg',
        'logo'          => [
            '@type'  => 'ImageObject',
            '@id'    => SITE_URL . '/#logo',
            'url'    => SITE_URL . '/assets/images/skyca-logo-original.jpg',
            'width'  => 800,
            'height' => 590,
            'caption' => COMPANY_LEGAL_NAME,
        ],
        'email'         => $settings['business_email'],
        'telephone'     => $settings['business_phone'],
        'areaServed'    => [
            ['@type' => 'Country', 'name' => 'India'],
            ['@type' => 'Place', 'name' => 'Worldwide'],
        ],
        'knowsAbout'    => array_values(array_unique($knowsAbout)),
        'contactPoint'  => [
            [
                '@type'             => 'ContactPoint',
                'telephone'         => $settings['business_phone'],
                'email'             => $settings['business_email'],
                'contactType'       => 'customer support and sales',
                'availableLanguage' => ['English', 'Hindi'],
                'areaServed'        => 'IN',
            ],
        ],
    ];

    $sameAs = [];
    foreach (['social_linkedin_url', 'social_github_url', 'social_x_url', 'social_facebook_url', 'social_instagram_url'] as $socialKey) {
        if (!empty($settings[$socialKey]) && filter_var($settings[$socialKey], FILTER_VALIDATE_URL)) {
            $sameAs[] = $settings[$socialKey];
        }
    }
    if (!empty($sameAs)) {
        $orgSchema['sameAs'] = $sameAs;
    }

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
        '@type'         => 'WebSite',
        '@id'           => SITE_URL . '/#website',
        'url'           => SITE_URL . '/',
        'name'          => COMPANY_LEGAL_NAME,
        'alternateName' => COMPANY_BRAND_NAME,
        'description'   => $defaultHomeDesc,
        'publisher'     => ['@id' => SITE_URL . '/#organization'],
        'inLanguage'    => 'en-IN',
    ];

    // 3. Contextual WebPage / AboutPage / ContactPage / CollectionPage Schema
    $webPageType = 'WebPage';
    if ($path === '/about') {
        $webPageType = 'AboutPage';
    } elseif ($path === '/contact') {
        $webPageType = 'ContactPage';
    } elseif ($path === '/services') {
        $webPageType = 'CollectionPage';
    }

    $webPageSchema = [
        '@type'       => $webPageType,
        '@id'         => $canonical . '#webpage',
        'url'         => $canonical,
        'name'        => $title,
        'description' => $description,
        'isPartOf'    => ['@id' => SITE_URL . '/#website'],
        'about'       => ['@id' => SITE_URL . '/#organization'],
        'inLanguage'  => 'en-IN',
        'primaryImageOfPage' => [
            '@type' => 'ImageObject',
            'url'   => $ogImage,
        ],
        'speakable'   => [
            '@type'       => 'SpeakableSpecification',
            'cssSelector' => ['h1', '.hero-subheadline', '.page-hero-subtitle'],
        ],
    ];
    $graph[] = $webPageSchema;

    // 4. ItemList Schema for Services Directory (Homepage & /services) — Powers Rich Sitelinks
    if ($path === '/' || $path === '/services') {
        $serviceListItems = [];
        $pos = 1;
        foreach ($allServices as $srvItem) {
            $serviceListItems[] = [
                '@type'       => 'ListItem',
                'position'    => $pos++,
                'name'        => $srvItem['title'],
                'description' => $srvItem['summary'],
                'url'         => skyca_canonical_url('/services/' . $srvItem['slug']),
            ];
        }
        $graph[] = [
            '@type'           => 'ItemList',
            '@id'             => $canonical . '#services-list',
            'name'            => COMPANY_LEGAL_NAME . ' — Official IT & Software Services Catalog',
            'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
            'numberOfItems'   => count($serviceListItems),
            'itemListElement' => $serviceListItems,
        ];
    }

    // 5. BreadcrumbList Schema (if breadcrumbs supplied)
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

    // 6. Service Schema (if on an individual service landing page)
    if (is_array($serviceData) && !empty($serviceData['title'])) {
        $hasOfferCatalog = [];
        if (!empty($serviceData['sub_services'])) {
            foreach ($serviceData['sub_services'] as $sub) {
                $hasOfferCatalog[] = [
                    '@type' => 'Offer',
                    'itemOffered' => [
                        '@type'       => 'Service',
                        'name'        => $sub['name'],
                        'description' => $sub['description'],
                    ],
                ];
            }
        }
        $graph[] = [
            '@type'       => 'Service',
            '@id'         => $canonical . '#service',
            'name'        => $serviceData['title'],
            'serviceType' => $serviceData['short_title'] ?? $serviceData['title'],
            'description' => $serviceData['summary'] ?? $description,
            'url'         => $canonical,
            'provider'    => ['@id' => SITE_URL . '/#organization'],
            'areaServed'  => [
                '@type' => 'Country',
                'name'  => 'India',
            ],
            'hasOfferCatalog' => [
                '@type'           => 'OfferCatalog',
                'name'            => $serviceData['title'] . ' Capabilities',
                'itemListElement' => $hasOfferCatalog,
            ],
        ];
    }

    // 7. FAQPage Schema (if FAQs present on the page)
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
        'ga4Id'                => $settings['google_analytics_id'] ?: null,
        'googleAdsId'          => $settings['google_ads_conversion_id'] ?: null,
        'googleAdsLabel'       => $settings['google_ads_conversion_label'] ?: null,
        'metaPixelId'          => $settings['meta_pixel_id'] ?: null,
        'isThankYouConversion' => !empty($pageMeta['trigger_conversion']),
        'leadRef'              => $pageMeta['conversion_ref'] ?? null,
    ];
    echo '    <script>window.SKYCA_TRACKING = ' . json_encode($trackingConfig, JSON_UNESCAPED_SLASHES) . ";</script>\n";
}

/**
 * Return the full 15-page indexable SEO audit matrix for the Admin SEO Hub.
 */
function skyca_get_seo_audit_matrix(): array
{
    $settings    = skyca_get_settings();
    $allServices = skyca_get_all_services();

    $homeTitle = !empty($settings['seo_home_title'])
        ? $settings['seo_home_title']
        : 'Skyca Digital Private Limited | IT Services, Custom Software, Web, Cloud & AI Solutions';
    $homeDesc = !empty($settings['seo_home_description'])
        ? $settings['seo_home_description']
        : 'Technology Solutions That Move Your Business Forward. Skyca Digital Private Limited delivers custom software development, web & mobile apps, SaaS & cloud architecture, AI integration, cybersecurity, data analytics, and e-commerce solutions.';

    $matrix = [
        [
            'path'        => '/',
            'page_type'   => 'Homepage',
            'title'       => $homeTitle,
            'description' => $homeDesc,
            'schemas'     => ['Organization', 'ProfessionalService', 'WebSite', 'WebPage', 'ItemList', 'FAQPage'],
            'priority'    => '1.0',
        ],
        [
            'path'        => '/services',
            'page_type'   => 'Services Hub',
            'title'       => 'IT Services & Software Development Solutions | Skyca Digital Private Limited',
            'description' => 'Explore Skyca Digital Private Limited’s 7 core IT service pillars: custom software development, web & mobile apps, SaaS & cloud services, AI consulting, digital marketing & cybersecurity, data analytics, and e-commerce solutions.',
            'schemas'     => ['Organization', 'CollectionPage', 'ItemList', 'BreadcrumbList'],
            'priority'    => '0.9',
        ],
    ];

    foreach ($allServices as $srv) {
        $matrix[] = [
            'path'        => '/services/' . $srv['slug'],
            'page_type'   => 'Service Landing Page',
            'title'       => $srv['meta_title'],
            'description' => $srv['meta_description'],
            'schemas'     => ['Organization', 'Service', 'OfferCatalog', 'FAQPage', 'BreadcrumbList'],
            'priority'    => '0.9',
        ];
    }

    $matrix[] = [
        'path'        => '/about',
        'page_type'   => 'Corporate Profile',
        'title'       => 'About Us | Skyca Digital Private Limited — IT & Software Engineering',
        'description' => 'Learn about Skyca Digital Private Limited’s mission, engineering philosophy, delivery standards, and commitment to building reliable software, web, cloud, and AI solutions.',
        'schemas'     => ['Organization', 'AboutPage', 'BreadcrumbList'],
        'priority'    => '0.8',
    ];
    $matrix[] = [
        'path'        => '/contact',
        'page_type'   => 'Lead Conversion Page',
        'title'       => 'Contact Us | Request a Free IT Consultation — Skyca Digital Private Limited',
        'description' => 'Contact Skyca Digital Private Limited at info@skycadigital.com or +91 6002064873. Request a project quotation or schedule a technical consultation for custom software, web, cloud, or AI development.',
        'schemas'     => ['Organization', 'ContactPage', 'BreadcrumbList'],
        'priority'    => '0.9',
    ];
    $matrix[] = [
        'path'        => '/faq',
        'page_type'   => 'Knowledge Base',
        'title'       => 'Frequently Asked Questions (FAQ) | Skyca Digital Private Limited',
        'description' => 'Answers to common client questions about Skyca Digital Private Limited’s software development process, source code ownership, security practices, maintenance, and project quotations.',
        'schemas'     => ['Organization', 'FAQPage', 'BreadcrumbList'],
        'priority'    => '0.7',
    ];
    $matrix[] = [
        'path'        => '/privacy-policy',
        'page_type'   => 'Legal / Trust',
        'title'       => 'Privacy Policy | Skyca Digital Private Limited',
        'description' => 'Privacy Policy explaining how Skyca Digital Private Limited collects, processes, and safeguards business enquiry data and website analytics.',
        'schemas'     => ['Organization', 'WebPage', 'BreadcrumbList'],
        'priority'    => '0.4',
    ];
    $matrix[] = [
        'path'        => '/terms-and-conditions',
        'page_type'   => 'Legal / Trust',
        'title'       => 'Terms and Conditions | Skyca Digital Private Limited',
        'description' => 'Website Terms and Conditions governing the use of https://skycadigital.com and general engagement principles of Skyca Digital Private Limited.',
        'schemas'     => ['Organization', 'WebPage', 'BreadcrumbList'],
        'priority'    => '0.4',
    ];
    $matrix[] = [
        'path'        => '/cookie-policy',
        'page_type'   => 'Legal / Trust',
        'title'       => 'Cookie Policy | Skyca Digital Private Limited',
        'description' => 'Cookie Policy detailing how Skyca Digital Private Limited uses essential security cookies and optional consent-based analytics and marketing cookies.',
        'schemas'     => ['Organization', 'WebPage', 'BreadcrumbList'],
        'priority'    => '0.3',
    ];

    return $matrix;
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
