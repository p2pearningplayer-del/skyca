<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — INDIVIDUAL SERVICE LANDING PAGE (pages/service_detail.php)
 * Optimized for commercial search intent and Google/Meta Ads landing campaigns.
 * Automatically preselects the current service in the embedded project enquiry form.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';
require_once dirname(__DIR__) . '/includes/enquiry_form.php';

if (!isset($activeService) || !is_array($activeService)) {
    http_response_code(404);
    require __DIR__ . '/404.php';
    exit;
}

$allServices = skyca_get_all_services();
$settings    = skyca_get_settings();
$servicePath = '/services/' . $activeService['slug'];

$breadcrumbs = [
    ['name' => 'Home', 'path' => '/'],
    ['name' => 'Services', 'path' => '/services'],
    ['name' => $activeService['title'], 'path' => $servicePath],
];

$pageMeta = [
    'title'       => $activeService['meta_title'],
    'description' => $activeService['meta_description'],
    'path'        => $servicePath,
    'breadcrumbs' => $breadcrumbs,
    'service'     => $activeService,
    'faqs'        => $activeService['faqs'] ?? [],
];

require dirname(__DIR__) . '/includes/header.php';
?>

<!-- 1. SERVICE-SPECIFIC HERO SECTION -->
<section class="page-hero service-landing-hero bg-navy text-light">
    <div class="container">
        <?php skyca_render_breadcrumbs($breadcrumbs); ?>

        <div class="service-hero-grid">
            <div class="service-hero-main">
                <div class="hero-eyebrow-pill eyebrow-pill-dark">
                    <span>Service Category 0<?php echo (int) $activeService['id']; ?> &bull; <?php echo skyca_e($activeService['hero_badge']); ?></span>
                </div>

                <p class="service-official-category-label"><?php echo skyca_e($activeService['title']); ?></p>
                <h1 class="page-hero-title"><?php echo skyca_e($activeService['hero_headline']); ?></h1>
                <p class="page-hero-subtitle"><?php echo skyca_e($activeService['hero_subheadline']); ?></p>

                <div class="hero-cta-group mt-6">
                    <a href="#service-enquiry-section" class="btn btn-primary btn-lg">
                        <span>Request a Quote for <?php echo skyca_e($activeService['short_title']); ?></span>
                        <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                    <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="btn btn-outline-light btn-lg">
                        <span>Speak With Our Team</span>
                    </a>
                </div>
            </div>

            <div class="service-hero-card">
                <div class="service-quick-summary-card">
                    <div class="sq-icon" aria-hidden="true">
                        <?php echo $activeService['icon_svg']; ?>
                    </div>
                    <h2>Included Capabilities</h2>
                    <ul class="sq-list">
                        <?php foreach ($activeService['sub_services'] as $sub): ?>
                            <li>
                                <svg class="bullet-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span><?php echo skyca_e($sub['name']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="#service-enquiry-section" class="btn btn-primary btn-block mt-4">Get Started With This Service</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 2. BUSINESS PROBLEMS THIS SERVICE ADDRESSES -->
<section class="section bg-white" id="business-problems">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Challenges We Solve</span>
            <h2 class="section-title">Business &amp; Technical Problems This Service Addresses</h2>
            <p class="section-subtitle">
                Organizations typically engage Skyca Digital for <strong><?php echo skyca_e($activeService['title']); ?></strong> when facing one or more of these operational hurdles:
            </p>
        </div>

        <div class="problems-grid">
            <?php foreach ($activeService['business_problems'] as $idx => $prob): ?>
                <div class="problem-card">
                    <span class="problem-num">Challenge 0<?php echo $idx + 1; ?></span>
                    <h3><?php echo skyca_e($prob['title']); ?></h3>
                    <p><?php echo skyca_e($prob['description']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- 3. SOLUTION OVERVIEW & SUB-SERVICE BREAKDOWN -->
<section class="section bg-slate" id="solution-overview">
    <div class="container">
        <div class="solution-split">
            <div class="solution-narrative">
                <span class="section-eyebrow">Solution Overview</span>
                <h2 class="section-title">How Skyca Digital Delivers <?php echo skyca_e($activeService['short_title']); ?></h2>
                <p class="solution-lead"><?php echo skyca_e($activeService['solution_overview']); ?></p>

                <div class="who-needs-box mt-6">
                    <h3>Who Should Consider This Service?</h3>
                    <p><?php echo skyca_e($activeService['who_needs_it']); ?></p>
                </div>

                <div class="mt-6">
                    <h3 class="subservices-heading">Technologies &amp; Standards We Apply:</h3>
                    <div class="cap-badges">
                        <?php foreach ($activeService['tech_stack'] as $tech): ?>
                            <span><?php echo skyca_e($tech); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="solution-subservices">
                <h3 class="subservices-heading">Detailed Service Breakdown</h3>
                <div class="subservices-stack">
                    <?php foreach ($activeService['sub_services'] as $sub): ?>
                        <div class="subservice-detail-card">
                            <h4>
                                <svg class="bullet-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span><?php echo skyca_e($sub['name']); ?></span>
                            </h4>
                            <p><?php echo skyca_e($sub['description']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 4. DELIVERABLES & PROJECT WORKFLOW -->
<section class="section bg-white" id="deliverables-workflow">
    <div class="container">
        <div class="deliverables-workflow-grid">
            <!-- Deliverables Column -->
            <div class="deliverables-panel">
                <span class="section-eyebrow">What You Receive</span>
                <h2 class="section-title">Key Project Deliverables</h2>
                <p class="section-subtitle">
                    Every engagement is structured around concrete, verifiable deliverables so your team has full clarity and ownership:
                </p>
                <ul class="deliverables-large-list">
                    <?php foreach ($activeService['deliverables'] as $deliv): ?>
                        <li>
                            <div class="deliv-check-icon">
                                <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            </div>
                            <span><?php echo skyca_e($deliv); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Project Workflow Column -->
            <div class="workflow-panel">
                <span class="section-eyebrow">Execution Roadmap</span>
                <h2 class="section-title">Service Delivery Workflow</h2>
                <p class="section-subtitle">
                    How we take your <?php echo skyca_e(strtolower($activeService['short_title'])); ?> project from initial consultation to launch:
                </p>
                <div class="vertical-workflow-list">
                    <?php foreach ($activeService['workflow'] as $wf): ?>
                        <div class="v-workflow-item">
                            <div class="v-workflow-step"><?php echo skyca_e($wf['step']); ?></div>
                            <div class="v-workflow-body">
                                <h3><?php echo skyca_e($wf['title']); ?></h3>
                                <p><?php echo skyca_e($wf['desc']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. PRESELECTED SERVICE ENQUIRY FORM -->
<section class="section bg-slate" id="service-enquiry-section">
    <div class="container">
        <div class="enquiry-split-layout">
            <div class="enquiry-info-column">
                <span class="section-eyebrow">Request a Consultation</span>
                <h2 class="section-title">Discuss Your <?php echo skyca_e($activeService['short_title']); ?> Project</h2>
                <p class="section-subtitle">
                    Use this form to share your requirements for <strong><?php echo skyca_e($activeService['title']); ?></strong>. The service category is already preselected for your convenience.
                </p>

                <div class="direct-contact-card mt-6">
                    <h3>Direct Technical Consultation</h3>
                    <p>You can also reach Skyca Digital Private Limited directly:</p>
                    <div class="direct-contact-items">
                        <a href="mailto:<?php echo skyca_e($settings['business_email']); ?>" class="direct-contact-row">
                            <svg class="icon-sm text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <div>
                                <span class="dc-label">Email Us</span>
                                <strong class="dc-val"><?php echo skyca_e($settings['business_email']); ?></strong>
                            </div>
                        </a>
                        <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="direct-contact-row">
                            <svg class="icon-sm text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <div>
                                <span class="dc-label">Call Us</span>
                                <strong class="dc-val"><?php echo skyca_e($settings['business_phone']); ?></strong>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="enquiry-form-column">
                <?php
                skyca_render_enquiry_form([
                    'form_id'             => 'service-detail-enquiry-form',
                    'preselected_service' => $activeService['form_value'],
                    'heading'             => 'Enquire About ' . $activeService['short_title'],
                    'subheading'          => 'Service preselected: ' . $activeService['title'],
                ]);
                ?>
            </div>
        </div>
    </div>
</section>

<!-- 6. SERVICE-SPECIFIC FAQs -->
<?php if (!empty($activeService['faqs'])): ?>
<section class="section bg-white" id="service-faqs">
    <div class="container container-narrow">
        <div class="section-header text-center">
            <span class="section-eyebrow">Service FAQs</span>
            <h2 class="section-title">Common Questions About <?php echo skyca_e($activeService['short_title']); ?></h2>
        </div>

        <div class="faq-accordion" role="region" aria-label="Service Frequently Asked Questions">
            <?php foreach ($activeService['faqs'] as $idx => $faq): ?>
                <div class="faq-item">
                    <h3 class="faq-question-heading">
                        <button
                            type="button"
                            class="faq-trigger"
                            aria-expanded="<?php echo $idx === 0 ? 'true' : 'false'; ?>"
                            aria-controls="srv-faq-panel-<?php echo $idx; ?>"
                            id="srv-faq-btn-<?php echo $idx; ?>"
                        >
                            <span><?php echo skyca_e($faq['q']); ?></span>
                            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                    </h3>
                    <div
                        id="srv-faq-panel-<?php echo $idx; ?>"
                        role="region"
                        aria-labelledby="srv-faq-btn-<?php echo $idx; ?>"
                        class="faq-panel <?php echo $idx === 0 ? '' : 'hidden'; ?>"
                    >
                        <p><?php echo skyca_e($faq['a']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- 7. INTERNAL LINKS TO RELATED SERVICES -->
<section class="section bg-slate" id="related-services">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Complementary Capabilities</span>
            <h2 class="section-title">Related IT &amp; Software Services</h2>
            <p class="section-subtitle">Explore related technology services from Skyca Digital Private Limited that often pair with <?php echo skyca_e($activeService['short_title']); ?>:</p>
        </div>

        <div class="related-services-grid">
            <?php foreach ($activeService['related_slugs'] as $relSlug): ?>
                <?php if (isset($allServices[$relSlug])): ?>
                    <?php $relSrv = $allServices[$relSlug]; ?>
                    <article class="service-card">
                        <div class="service-card-top">
                            <div class="service-icon-box" aria-hidden="true">
                                <?php echo $relSrv['icon_svg']; ?>
                            </div>
                            <span class="service-num-badge">0<?php echo (int) $relSrv['id']; ?></span>
                        </div>
                        <h3 class="service-card-title">
                            <a href="/services/<?php echo skyca_e($relSrv['slug']); ?>">
                                <?php echo skyca_e($relSrv['title']); ?>
                            </a>
                        </h3>
                        <p class="service-card-summary"><?php echo skyca_e($relSrv['summary']); ?></p>
                        <div class="service-card-footer">
                            <a href="/services/<?php echo skyca_e($relSrv['slug']); ?>" class="service-learn-link">
                                <span>View Service Details</span>
                                <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </a>
                        </div>
                    </article>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
