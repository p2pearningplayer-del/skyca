<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — AI SEARCH & GENERATIVE ENGINE OPTIMIZATION (pages/llms_txt.php)
 * Serves /llms.txt for AI search engines (Google AI Overviews, ChatGPT Search, Perplexity, Copilot, Claude).
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';

header('Content-Type: text/plain; charset=UTF-8');

$settings    = skyca_get_settings();
$allServices = skyca_get_all_services();

echo "# " . COMPANY_LEGAL_NAME . " (" . SITE_URL . ")\n\n";
echo "> Technology Solutions That Move Your Business Forward. From custom software and web development to cloud solutions, AI, cybersecurity, and digital growth, Skyca Digital Private Limited helps businesses turn technology into measurable progress.\n\n";

echo "## Official Corporate Contact Information\n";
echo "- **Company Legal Name:** " . COMPANY_LEGAL_NAME . "\n";
echo "- **Official Website:** " . SITE_URL . "\n";
echo "- **Business Email:** " . $settings['business_email'] . "\n";
echo "- **Business Phone:** " . $settings['business_phone'] . "\n";
echo "- **Consultation & Quotation Portal:** " . SITE_URL . "/contact\n\n";

echo "## Core IT & Software Development Service Categories\n\n";
foreach ($allServices as $srv) {
    echo "### " . $srv['id'] . ". [" . $srv['title'] . "](" . SITE_URL . "/services/" . $srv['slug'] . ")\n";
    echo $srv['summary'] . "\n";
    foreach ($srv['sub_services'] as $sub) {
        echo "- **" . $sub['name'] . ":** " . $sub['description'] . "\n";
    }
    echo "\n";
}

echo "## Key Corporate Pages\n";
echo "- [Home](" . SITE_URL . "/)\n";
echo "- [All Services Overview](" . SITE_URL . "/services)\n";
echo "- [About Skyca Digital Private Limited](" . SITE_URL . "/about)\n";
echo "- [Contact & Project Enquiry](" . SITE_URL . "/contact)\n";
echo "- [Frequently Asked Questions (FAQ)](" . SITE_URL . "/faq)\n";
echo "- [Privacy Policy](" . SITE_URL . "/privacy-policy)\n";
echo "- [Terms and Conditions](" . SITE_URL . "/terms-and-conditions)\n";
echo "- [Cookie Policy](" . SITE_URL . "/cookie-policy)\n";
echo "- [XML Sitemap](" . SITE_URL . "/sitemap.xml)\n";
