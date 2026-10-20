<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — COOKIE POLICY PAGE (pages/cookie_policy.php)
 */

require_once dirname(__DIR__) . '/config/config.php';

$settings = skyca_get_settings();

$breadcrumbs = [
    ['name' => 'Home', 'path' => '/'],
    ['name' => 'Cookie Policy', 'path' => '/cookie-policy'],
];

$pageMeta = [
    'title'       => 'Cookie Policy | Skyca Digital Private Limited',
    'description' => 'Cookie Policy for Skyca Digital Private Limited explaining essential security cookies, UTM attribution storage, and consent-based analytics and advertising cookies.',
    'path'        => '/cookie-policy',
    'breadcrumbs' => $breadcrumbs,
];

require dirname(__DIR__) . '/includes/header.php';
?>

<section class="page-hero bg-navy text-light">
    <div class="container">
        <?php skyca_render_breadcrumbs($breadcrumbs); ?>
        <div class="page-hero-content">
            <span class="section-eyebrow eyebrow-light">Transparency &amp; Consent</span>
            <h1 class="page-hero-title">Cookie Policy</h1>
            <p class="page-hero-subtitle">
                How <?php echo skyca_e(COMPANY_LEGAL_NAME); ?> uses cookies and browser storage on <?php echo skyca_e(SITE_URL); ?>.
            </p>
        </div>
    </div>
</section>

<section class="section bg-white">
    <div class="container container-narrow legal-document">
        <div class="legal-review-banner" role="note">
            <strong>Professional Legal Review Notice:</strong>
            <span>This Cookie Policy outlines the technical cookies and consent controls implemented on <?php echo skyca_e(SITE_URL); ?>. Please have your legal advisor review this document alongside any regional e-privacy requirements if targeting international jurisdictions.</span>
        </div>

        <h2>1. What Are Cookies &amp; Browser Storage?</h2>
        <p>
            Cookies are small text files placed on your device by a website to maintain session state, security, and user preferences. We also use standard browser storage (<code>localStorage</code> and <code>sessionStorage</code>) to remember your cookie consent choices and preserve campaign UTM parameters across pages during your visit.
        </p>

        <h2>2. Categories of Cookies We Use</h2>

        <h3>A. Strictly Necessary Cookies (Always Active)</h3>
        <ul>
            <li><strong><code>SKYCA_SESSID</code>:</strong> Essential PHP session cookie (`HttpOnly`, `SameSite=Lax`) used to validate Cross-Site Request Forgery (CSRF) security tokens on enquiry forms and authenticate authorized administrators.</li>
            <li><strong><code>skyca_cookie_consent</code> (Local Storage):</strong> Stores your chosen cookie preferences (Essential Only vs. Analytics/Marketing) so we do not repeatedly prompt you on every page load.</li>
            <li><strong><code>skyca_attribution</code> (Session Storage):</strong> Temporarily stores the entry landing page and UTM campaign tags during your browsing session so if you submit an enquiry form, we know which page or campaign brought you to us.</li>
        </ul>

        <h3>B. Optional Analytics Cookies (Consent-Based)</h3>
        <p>
            When configured by the website administrator and consented to by you, we may use <strong>Google Analytics 4 (GA4)</strong> cookies (such as <code>_ga</code>) to understand aggregate page visits, device types, and navigation flows. We do not pass personal names, emails, or phone numbers into Google Analytics.
        </p>

        <h3>C. Optional Advertising &amp; Conversion Measurement Cookies (Consent-Based)</h3>
        <p>
            When running paid search or social campaigns (Google Ads or Meta Ads) and consented to by you, conversion tags measure whether an ad click resulted in a completed business enquiry on our Thank You page.
        </p>

        <h2>3. Managing Your Cookie Preferences</h2>
        <p>
            You can view or change your cookie consent choices on our website at any time by clicking the button below or using the "Cookie Preferences" link in our website footer:
        </p>
        <p class="mt-4 mb-6">
            <button type="button" class="btn btn-primary" id="page-open-cookie-settings-btn">
                Manage Cookie Preferences
            </button>
        </p>

        <h2>4. Contact Us</h2>
        <p>
            For questions regarding this Cookie Policy, please contact <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong> at <a href="mailto:<?php echo skyca_e($settings['business_email']); ?>"><?php echo skyca_e($settings['business_email']); ?></a>.
        </p>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
