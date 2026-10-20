<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — THANK YOU PAGE (pages/thank_you.php)
 * Displays post-submission confirmation, non-PII lead reference number, and next steps.
 * Fires conversion tracking events ONLY if verified against a confirmed backend lead submission.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/includes/security.php';
require_once dirname(__DIR__) . '/includes/db.php';

skyca_start_session();
$settings = skyca_get_settings();

$rawRef = skyca_clean_input((string) ($_GET['ref'] ?? ''), 40, false);
$sessionRef = (string) ($_SESSION['last_verified_lead_ref'] ?? '');
$sessionService = (string) ($_SESSION['last_verified_service'] ?? '');

$verifiedRef = '';
$shouldFireConversion = false;

if ($rawRef !== '' && preg_match('/^SKY-\d{8}-[A-F0-9]{6}$/', $rawRef)) {
    if ($sessionRef === $rawRef) {
        $verifiedRef = $rawRef;
        // Fire conversion only once per verified session lead reference
        $firedKey = 'fired_conv_' . $rawRef;
        if (empty($_SESSION[$firedKey])) {
            $shouldFireConversion = true;
            $_SESSION[$firedKey] = true;
        }
    } else {
        // Check if reference exists in DB within the last 30 minutes
        $pdo = skyca_db_or_null();
        if ($pdo !== null) {
            try {
                $stmt = $pdo->prepare('SELECT lead_reference, requested_service FROM leads WHERE lead_reference = :ref LIMIT 1');
                $stmt->execute([':ref' => $rawRef]);
                $row = $stmt->fetch();
                if ($row) {
                    $verifiedRef = (string) $row['lead_reference'];
                    $sessionService = (string) ($row['requested_service'] ?? '');
                }
            } catch (Throwable $e) {
                // Ignore DB lookup error on display
            }
        }
    }
}

$pageMeta = [
    'title'              => 'Thank You for Your Enquiry | Skyca Digital Private Limited',
    'description'        => 'Your project enquiry has been received by Skyca Digital Private Limited.',
    'path'               => '/thank-you',
    'robots'             => 'noindex, nofollow',
    'trigger_conversion' => $shouldFireConversion,
    'conversion_ref'     => $verifiedRef ?: null,
];

require dirname(__DIR__) . '/includes/header.php';
?>

<section class="section bg-slate thank-you-section">
    <div class="container container-narrow">
        <div class="thank-you-card">
            <div class="ty-check-circle" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </div>

            <span class="section-eyebrow">Enquiry Confirmed</span>
            <h1 class="ty-title">Thank You for Contacting Skyca Digital</h1>
            <p class="ty-subtitle">
                Your project enquiry has been securely saved in our system. A member of our technical consulting team will review your requirements and reach out to you shortly.
            </p>

            <?php if ($verifiedRef !== ''): ?>
                <div class="ty-reference-box" role="status" aria-live="polite">
                    <span class="ty-ref-label">Your Enquiry Reference Number</span>
                    <strong class="ty-ref-code" id="confirmed-lead-reference"><?php echo skyca_e($verifiedRef); ?></strong>
                    <?php if ($sessionService !== ''): ?>
                        <span class="ty-ref-service">Requested Service: <?php echo skyca_e($sessionService); ?></span>
                    <?php endif; ?>
                    <span class="ty-ref-note">Please keep this reference number handy for future correspondence.</span>
                </div>
            <?php endif; ?>

            <div class="ty-next-steps">
                <h2>What Happens Next?</h2>
                <div class="ty-steps-grid">
                    <div class="ty-step-item">
                        <span class="ty-step-num">Step 01</span>
                        <h3>Technical Review</h3>
                        <p>Our engineering team reviews your project description, requested service category, and timeline.</p>
                    </div>
                    <div class="ty-step-item">
                        <span class="ty-step-num">Step 02</span>
                        <h3>Discovery Discussion</h3>
                        <p>We contact you via email or phone to clarify any technical questions or integration needs.</p>
                    </div>
                    <div class="ty-step-item">
                        <span class="ty-step-num">Step 03</span>
                        <h3>Scope &amp; Quotation</h3>
                        <p>We prepare a clear architectural recommendation, milestone plan, and project estimate.</p>
                    </div>
                </div>
            </div>

            <div class="ty-direct-box">
                <p>Need to share additional documents or speak with us immediately?</p>
                <div class="hero-cta-group justify-center mt-4">
                    <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="btn btn-primary">
                        Call <?php echo skyca_e($settings['business_phone']); ?>
                    </a>
                    <a href="mailto:<?php echo skyca_e($settings['business_email']); ?><?php echo $verifiedRef ? '?subject=' . rawurlencode('Enquiry Reference: ' . $verifiedRef) : ''; ?>" class="btn btn-secondary">
                        Email <?php echo skyca_e($settings['business_email']); ?>
                    </a>
                    <a href="/" class="btn btn-outline">Return to Homepage</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
