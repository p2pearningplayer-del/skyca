<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — TERMS AND CONDITIONS PAGE (pages/terms_conditions.php)
 */

require_once dirname(__DIR__) . '/config/config.php';

$settings = skyca_get_settings();

$breadcrumbs = [
    ['name' => 'Home', 'path' => '/'],
    ['name' => 'Terms and Conditions', 'path' => '/terms-and-conditions'],
];

$pageMeta = [
    'title'       => 'Terms and Conditions | Skyca Digital Private Limited',
    'description' => 'Website Terms and Conditions for Skyca Digital Private Limited governing the use of skycadigital.com, intellectual property, and business enquiry submissions.',
    'path'        => '/terms-and-conditions',
    'breadcrumbs' => $breadcrumbs,
];

require dirname(__DIR__) . '/includes/header.php';
?>

<section class="page-hero bg-navy text-light">
    <div class="container">
        <?php skyca_render_breadcrumbs($breadcrumbs); ?>
        <div class="page-hero-content">
            <span class="section-eyebrow eyebrow-light">Website Governance</span>
            <h1 class="page-hero-title">Terms and Conditions</h1>
            <p class="page-hero-subtitle">
                Terms governing access to and use of the <?php echo skyca_e(COMPANY_LEGAL_NAME); ?> website (<?php echo skyca_e(SITE_URL); ?>).
            </p>
        </div>
    </div>
</section>

<section class="section bg-white">
    <div class="container container-narrow legal-document">
        <div class="legal-review-banner" role="note">
            <strong>Professional Legal Review Notice:</strong>
            <span>These Website Terms and Conditions are provided for general corporate governance of <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong>. Formal client engagements, software deliverables, warranties, and dispute resolution clauses should be governed by a written Master Services Agreement (MSA) or Statement of Work (SOW) reviewed by qualified legal counsel.</span>
        </div>

        <p class="legal-meta"><strong>Last Updated:</strong> October 2026 &bull; <strong>Entity:</strong> <?php echo skyca_e(COMPANY_LEGAL_NAME); ?></p>

        <h2>1. Acceptance of Terms</h2>
        <p>
            By accessing and using <a href="<?php echo skyca_e(SITE_URL); ?>"><?php echo skyca_e(SITE_URL); ?></a>, you agree to comply with these Terms and Conditions. If you do not agree with any part of these terms, please discontinue use of the website.
        </p>

        <h2>2. Scope of Website Information &amp; No Automatic Contract</h2>
        <p>
            The content on this website describes the IT, software development, web/mobile engineering, cloud, AI, cybersecurity, data analytics, and e-commerce capabilities of <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong> for general informational purposes.
        </p>
        <p>
            Submitting a project enquiry form or receiving an automated reference confirmation does not constitute a binding commercial contract or service level agreement. All formal project scopes, timelines, pricing, and deliverables are established exclusively through mutually executed written agreements or statements of work.
        </p>

        <h2>3.Acceptable Use &amp; Security</h2>
        <p>When using this website, you agree not to:</p>
        <ul>
            <li>Submit false, fraudulent, defamatory, or unauthorized third-party information through our enquiry forms.</li>
            <li>Attempt unauthorized access to administrative routes, server configurations, or backend databases.</li>
            <li>Use automated scraping tools, spam bots, denial-of-service floods, or vulnerability scanners against the website without prior written authorization.</li>
        </ul>

        <h2>4. Intellectual Property Rights</h2>
        <p>
            Unless otherwise stated, the official <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong> logo, brand identity, page copy, graphics, and website source code are the intellectual property of <?php echo skyca_e(COMPANY_LEGAL_NAME); ?> and may not be copied, reproduced, or impersonated without written permission.
        </p>

        <h2>5. No Unverified Guarantees</h2>
        <p>
            While we engineer software, websites, security improvements, and digital marketing foundations to high professional standards, outcomes such as commercial revenue, search engine ranking positions, paid advertising returns, or absolute immunity from all cyber threats depend on external factors outside our control and are not guaranteed unless explicitly stated in a signed agreement.
        </p>

        <h2>6. Limitation of Liability</h2>
        <p>
            To the fullest extent permitted by applicable law, <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong> shall not be liable for any indirect, incidental, or consequential damages arising out of your access to or inability to use this informational website.
        </p>

        <h2>7. Governing Law &amp; Contact</h2>
        <p>
            These website terms are governed by the laws of India.<em>[Note for Legal Review: Specify the exact city/state courts of exclusive jurisdiction in India once confirmed by company counsel.]</em>
        </p>
        <p>
            For questions regarding these Terms and Conditions, contact us at <a href="mailto:<?php echo skyca_e($settings['business_email']); ?>"><?php echo skyca_e($settings['business_email']); ?></a> or call <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>"><?php echo skyca_e($settings['business_phone']); ?></a>.
        </p>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
