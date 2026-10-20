<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — CONTACT US PAGE (pages/contact.php)
 * Displays verified business contact channels and the full validated project enquiry form.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';
require_once dirname(__DIR__) . '/includes/enquiry_form.php';

$settings = skyca_get_settings();

$breadcrumbs = [
    ['name' => 'Home', 'path' => '/'],
    ['name' => 'Contact Us', 'path' => '/contact'],
];

$pageMeta = [
    'title'       => 'Contact Us | Request a Free IT Consultation — Skyca Digital Private Limited',
    'description' => 'Contact Skyca Digital Private Limited for custom software development, web & mobile apps, cloud & SaaS solutions, AI consulting, cybersecurity, and e-commerce projects. Email info@skycadigital.com or call +91 6002064873.',
    'path'        => '/contact',
    'breadcrumbs' => $breadcrumbs,
];

require dirname(__DIR__) . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero bg-navy text-light">
    <div class="container">
        <?php skyca_render_breadcrumbs($breadcrumbs); ?>
        <div class="page-hero-content">
            <span class="section-eyebrow eyebrow-light">Get in Touch</span>
            <h1 class="page-hero-title">Contact Skyca Digital Private Limited</h1>
            <p class="page-hero-subtitle">
                Have a software project in mind, need a technical consultation, or want to request a quotation? Reach out to our team using the enquiry form or direct contact channels below.
            </p>
        </div>
    </div>
</section>

<!-- Main Contact & Enquiry Section -->
<section class="section bg-slate">
    <div class="container">
        <div class="contact-page-grid">
            <!-- Left Column: Verified Contact Details & Process -->
            <div class="contact-details-column">
                <div class="contact-channels-card">
                    <span class="section-eyebrow">Official Contact Channels</span>
                    <h2 class="contact-card-heading">Direct Business Contact</h2>
                    <p class="contact-card-desc">
                        Connect directly with <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong> for project enquiries, partnership discussions, or technical support.
                    </p>

                    <div class="contact-channel-list">
                        <!-- 1. Business Email -->
                        <div class="contact-channel-item">
                            <div class="cc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </div>
                            <div class="cc-body">
                                <span class="cc-label">Business Email</span>
                                <a href="mailto:<?php echo skyca_e($settings['business_email']); ?>" class="cc-value">
                                    <?php echo skyca_e($settings['business_email']); ?>
                                </a>
                                <span class="cc-note">For project scopes, RFPs, and general enquiries</span>
                            </div>
                        </div>

                        <!-- 2. Business Phone -->
                        <div class="contact-channel-item">
                            <div class="cc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            <div class="cc-body">
                                <span class="cc-label">Business Phone</span>
                                <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="cc-value">
                                    <?php echo skyca_e($settings['business_phone']); ?>
                                </a>
                                <span class="cc-note"><?php echo skyca_e($settings['company_working_hours'] ?: 'Monday – Saturday, Business Hours IST'); ?></span>
                            </div>
                        </div>

                        <!-- 3. Official Website -->
                        <div class="contact-channel-item">
                            <div class="cc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                            </div>
                            <div class="cc-body">
                                <span class="cc-label">Official Website</span>
                                <a href="<?php echo skyca_e(SITE_URL); ?>" class="cc-value">
                                    <?php echo skyca_e(SITE_URL); ?>
                                </a>
                                <span class="cc-note"><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></span>
                            </div>
                        </div>

                        <!-- Optional Verified Office Address (if configured) -->
                        <?php if (!empty($settings['company_address'])): ?>
                        <div class="contact-channel-item">
                            <div class="cc-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <div class="cc-body">
                                <span class="cc-label">Office Address</span>
                                <span class="cc-value-text">
                                    <?php echo skyca_e($settings['company_address']); ?>
                                    <?php if (!empty($settings['company_city'])): ?>, <?php echo skyca_e($settings['company_city']); ?><?php endif; ?>
                                    <?php if (!empty($settings['company_state'])): ?>, <?php echo skyca_e($settings['company_state']); ?><?php endif; ?>
                                    <?php if (!empty($settings['company_postal_code'])): ?> - <?php echo skyca_e($settings['company_postal_code']); ?><?php endif; ?>
                                </span>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Optional Verified WhatsApp Business Button (if configured) -->
                        <?php if (!empty($settings['whatsapp_number'])): ?>
                            <?php $waDigits = preg_replace('/\D+/', '', (string) $settings['whatsapp_number']); ?>
                            <?php if ($waDigits !== ''): ?>
                                <div class="mt-4">
                                    <a href="https://wa.me/<?php echo skyca_e($waDigits); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-secondary btn-block">
                                        Chat on Verified WhatsApp Business
                                    </a>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- Optional Consultation Booking Integration (if configured) -->
                        <?php if (!empty($settings['consultation_booking_url']) && filter_var($settings['consultation_booking_url'], FILTER_VALIDATE_URL)): ?>
                            <div class="mt-4">
                                <a href="<?php echo skyca_e($settings['consultation_booking_url']); ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-block">
                                    Schedule a Calendar Consultation
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- What Happens Next Box -->
                <div class="contact-next-steps-card">
                    <h3>What Happens After You Submit an Enquiry?</h3>
                    <ol class="next-steps-list">
                        <li>
                            <strong>Instant Reference Confirmation:</strong>
                            <span>You receive a unique enquiry reference ID immediately upon submission.</span>
                        </li>
                        <li>
                            <strong>Technical Review:</strong>
                            <span>Our engineering team reviews your requirements, timeline, and service category.</span>
                        </li>
                        <li>
                            <strong>Discovery &amp; Proposal:</strong>
                            <span>We reach out via your preferred email or phone number to clarify details and provide a tailored quotation.</span>
                        </li>
                    </ol>
                </div>
            </div>

            <!-- Right Column: Reusable Validated Project Enquiry Form -->
            <div class="contact-form-column">
                <?php
                skyca_render_enquiry_form([
                    'form_id'    => 'contact-page-enquiry-form',
                    'heading'    => 'Send Us Your Project Enquiry',
                    'subheading' => 'Complete the form below. Fields marked with * are required.',
                ]);
                ?>
            </div>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
