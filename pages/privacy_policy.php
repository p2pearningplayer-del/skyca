<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — PRIVACY POLICY PAGE (pages/privacy_policy.php)
 */

require_once dirname(__DIR__) . '/config/config.php';

$settings = skyca_get_settings();

$breadcrumbs = [
    ['name' => 'Home', 'path' => '/'],
    ['name' => 'Privacy Policy', 'path' => '/privacy-policy'],
];

$pageMeta = [
    'title'       => 'Privacy Policy | Skyca Digital Private Limited',
    'description' => 'Privacy Policy for Skyca Digital Private Limited explaining how business enquiry information, attribution parameters, and cookies are collected, stored, and protected.',
    'path'        => '/privacy-policy',
    'breadcrumbs' => $breadcrumbs,
];

require dirname(__DIR__) . '/includes/header.php';
?>

<section class="page-hero bg-navy text-light">
    <div class="container">
        <?php skyca_render_breadcrumbs($breadcrumbs); ?>
        <div class="page-hero-content">
            <span class="section-eyebrow eyebrow-light">Legal &amp; Data Protection</span>
            <h1 class="page-hero-title">Privacy Policy</h1>
            <p class="page-hero-subtitle">
                How <?php echo skyca_e(COMPANY_LEGAL_NAME); ?> collects, uses, stores, and safeguards information submitted through <?php echo skyca_e(SITE_URL); ?>.
            </p>
        </div>
    </div>
</section>

<section class="section bg-white">
    <div class="container container-narrow legal-document">
        <div class="legal-review-banner" role="note">
            <strong>Professional Legal Review Notice:</strong>
            <span>This Privacy Policy template is structured for the operational practices of <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong>. Specific statutory clauses, retention schedules, and jurisdictional disclosures (such as under the Digital Personal Data Protection Act, 2023 in India or international privacy frameworks) should be reviewed and finalized by a qualified legal professional prior to formal compliance reliance.</span>
        </div>

        <p class="legal-meta"><strong>Effective Date:</strong> October 2026 &bull; <strong>Entity:</strong> <?php echo skyca_e(COMPANY_LEGAL_NAME); ?></p>

        <h2>1. Introduction</h2>
        <p>
            <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong> ("Skyca Digital", "we", "our", or "us") operates the corporate website <a href="<?php echo skyca_e(SITE_URL); ?>"><?php echo skyca_e(SITE_URL); ?></a>. We respect your privacy and are committed to handling personal and business contact information transparently and securely.
        </p>

        <h2>2. Information We Collect</h2>
        <p>We collect only the information reasonably necessary to respond to business enquiries, deliver requested consultations, and secure our web platform:</p>
        <ul>
            <li><strong>Business Enquiry Details:</strong> Full name, business email address, phone number with country code, optional company/organization name, requested service category, optional estimated budget, optional project timeline, and project description submitted voluntarily through our enquiry forms.</li>
            <li><strong>Campaign &amp; Attribution Metadata:</strong> Landing page URL, HTTP referrer, UTM campaign parameters (<code>utm_source</code>, <code>utm_medium</code>, <code>utm_campaign</code>, <code>utm_content</code>, <code>utm_term</code>), submission timestamp, and privacy consent timestamp.</li>
            <li><strong>Security &amp; Abuse-Prevention Metadata:</strong> A cryptographic hash of the network IP address and session security tokens used strictly to prevent automated spam, brute-force attempts, and duplicate submissions.</li>
            <li><strong>We Do Not Collect Sensitive Financial Data:</strong> Our corporate website does not collect or store payment card numbers, CVV codes, UPI PINs, biometric data, or government identity numbers via public enquiry forms.</li>
        </ul>

        <h2>3. How We Use Your Information</h2>
        <p>Information submitted to Skyca Digital Private Limited is used strictly for legitimate business purposes:</p>
        <ul>
            <li>Reviewing your technical requirements and responding to your consultation or quotation request.</li>
            <li>Communicating with you via email or phone regarding your project enquiry.</li>
            <li>Internal lead management, status tracking, and measuring the effectiveness of our marketing pages.</li>
            <li>Maintaining website security, preventing fraud/spam, and complying with applicable legal obligations.</li>
        </ul>

        <h2>4. Data Storage, Security &amp; Third-Party Sharing</h2>
        <p>
            Enquiries are stored in an access-controlled relational database using parameterized queries and encrypted HTTPS transmission. Access to enquiry records is restricted to authorized personnel of <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong> via server-side authenticated administrative controls.
        </p>
        <p>
            <strong>No Sale of Personal Data:</strong> We do not sell, rent, or trade your personal or business contact details to third-party data brokers or external marketers. Furthermore, we do not transmit your personal email address, phone number, or project description in URL query strings or standard advertising pixel parameters.
        </p>

        <h2>5. Cookies and Analytics</h2>
        <p>
            We use essential cookies to maintain session security (CSRF tokens) and remember your privacy preferences. Optional analytics and advertising measurement tools (such as Google Analytics 4, Google Ads conversion tracking, and Meta Pixel, when configured) are loaded based on your cookie consent preferences. Please review our <a href="/cookie-policy">Cookie Policy</a> for details.
        </p>

        <h2>6. Data Retention &amp; Your Rights</h2>
        <p>
            We retain business enquiry records for as long as necessary to fulfill the consultation request, maintain business correspondence, or comply with applicable tax and corporate record-keeping laws. You may request access to, correction of, or deletion of your enquiry information by contacting us directly.
        </p>

        <h2>7. Contact Information for Privacy Requests</h2>
        <p>If you have any questions about this Privacy Policy or wish to exercise your data rights, please contact:</p>
        <ul>
            <li><strong>Company:</strong> <?php echo skyca_e(COMPANY_LEGAL_NAME); ?></li>
            <li><strong>Email:</strong> <a href="mailto:<?php echo skyca_e($settings['business_email']); ?>"><?php echo skyca_e($settings['business_email']); ?></a></li>
            <li><strong>Phone:</strong> <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>"><?php echo skyca_e($settings['business_phone']); ?></a></li>
            <?php if (!empty($settings['company_address'])): ?>
            <li><strong>Address:</strong> <?php echo skyca_e($settings['company_address']); ?></li>
            <?php endif; ?>
        </ul>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
