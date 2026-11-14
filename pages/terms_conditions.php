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

<section class="page-hero bg-navy text-light hero-3d-stage">
    <div class="hero-bg-grid" aria-hidden="true"></div>
    <div class="hero-3d-orb hero-3d-orb-1" aria-hidden="true"></div>
    <div class="hero-3d-orb hero-3d-orb-2" aria-hidden="true"></div>
    <div class="container">
        <?php skyca_render_breadcrumbs($breadcrumbs); ?>
        <div class="inner-hero-split">
            <div class="page-hero-content">
                <span class="section-eyebrow eyebrow-light">Website Governance</span>
                <h1 class="page-hero-title">Terms and Conditions</h1>
                <p class="page-hero-subtitle">
                    Terms governing access to and use of the <?php echo skyca_e(COMPANY_LEGAL_NAME); ?> website (<?php echo skyca_e(SITE_URL); ?>).
                </p>
            </div>
            <div class="inner-hero-visual scene-3d" aria-hidden="true">
                <div class="glass-hero-vector-card card-3d-surface">
                    <div class="ghv-header">
                        <span class="ghv-dot dot-teal"></span>
                        <span class="ghv-dot dot-blue"></span>
                        <span class="ghv-dot dot-gold"></span>
                        <span class="ghv-title">CORPORATE_TERMS // ROC_GUWAHATI</span>
                    </div>
                    <svg viewBox="0 0 400 150" class="ghv-svg" role="img" aria-label="Corporate Governance Terms">
                        <rect x="20" y="20" width="360" height="110" rx="14" fill="rgba(15,23,42,0.85)" stroke="#38bdf8" stroke-width="1.4"/>
                        <rect x="44" y="42" width="56" height="66" rx="8" fill="rgba(5,141,240,0.22)" stroke="#38bdf8" stroke-width="1.8"/>
                        <line x1="56" y1="60" x2="88" y2="60" stroke="#7dd3fc" stroke-width="2"/>
                        <line x1="56" y1="74" x2="88" y2="74" stroke="#2dd4bf" stroke-width="2"/>
                        <line x1="56" y1="88" x2="78" y2="88" stroke="#fbbf24" stroke-width="2"/>
                        <text x="122" y="58" fill="#ffffff" font-family="Inter, sans-serif" font-size="12" font-weight="800">TRANSPARENT ENGAGEMENT TERMS</text>
                        <text x="122" y="78" fill="#99f6e4" font-family="Inter, sans-serif" font-size="10.5" font-weight="600">Milestone-Based SOW &amp; IP Handover</text>
                        <text x="122" y="98" fill="#fde68a" font-family="Inter, sans-serif" font-size="10" font-weight="700">Jurisdiction: Assam, India</text>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-white">
    <div class="container container-narrow legal-document legal-document-card">
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

        <h2>7. Governing Law &amp; Registered Office Contact</h2>
        <p>
            These website terms are governed by the laws of India, under the jurisdiction of the competent courts in Assam, India (Registrar of Companies: <?php echo skyca_e($settings['company_roc'] ?: 'RoC Guwahati'); ?>; CIN: <code><?php echo skyca_e($settings['company_cin'] ?: 'U62013AS2026PTC030738'); ?></code>).
        </p>
        <p>
            <strong>Registered Office:</strong> <?php echo skyca_e($settings['company_address']); ?><?php if (!empty($settings['company_city'])): ?>, <?php echo skyca_e($settings['company_city']); ?><?php endif; ?><?php if (!empty($settings['company_state'])): ?>, <?php echo skyca_e($settings['company_state']); ?><?php endif; ?><?php if (!empty($settings['company_postal_code'])): ?> &ndash; <?php echo skyca_e($settings['company_postal_code']); ?>, India<?php endif; ?>.<br>
            For questions regarding these Terms and Conditions, contact us at <a href="mailto:<?php echo skyca_e($settings['business_email']); ?>"><?php echo skyca_e($settings['business_email']); ?></a> or call <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>"><?php echo skyca_e($settings['business_phone']); ?></a>.
        </p>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
