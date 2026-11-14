<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — SERVICES OVERVIEW PAGE (pages/services.php)
 * Covers all 7 official service categories, sub-services, who needs them, and consultation CTAs.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';
require_once dirname(__DIR__) . '/includes/enquiry_form.php';

$allServices = skyca_get_all_services();
$settings    = skyca_get_settings();

$breadcrumbs = [
    ['name' => 'Home', 'path' => '/'],
    ['name' => 'Services', 'path' => '/services'],
];

$pageMeta = [
    'title'       => 'IT Services & Software Development Solutions | Skyca Digital Private Limited',
    'description' => 'Explore Skyca Digital Private Limited’s 7 core IT service pillars: Custom Software Development, Web & Mobile Apps, SaaS & Cloud, AI & IT Consulting, Digital Marketing & Cyber Security, Data Analytics & Support, and E-Commerce Solutions.',
    'path'        => '/services',
    'breadcrumbs' => $breadcrumbs,
];

require dirname(__DIR__) . '/includes/header.php';
?>

<!-- Page Hero Header (Luxury Midnight 3D & Vector Stage) -->
<section class="page-hero bg-navy text-light hero-3d-stage">
    <div class="hero-bg-grid" aria-hidden="true"></div>
    <div class="hero-3d-orb hero-3d-orb-1" aria-hidden="true"></div>
    <div class="hero-3d-orb hero-3d-orb-2" aria-hidden="true"></div>
    <div class="container">
        <?php skyca_render_breadcrumbs($breadcrumbs); ?>
        <div class="inner-hero-split">
            <div class="page-hero-content">
                <span class="section-eyebrow eyebrow-light">Full-Spectrum IT Capabilities</span>
                <h1 class="page-hero-title">Information Technology &amp; Software Development Services</h1>
                <p class="page-hero-subtitle">
                    Skyca Digital Private Limited delivers practical, well-engineered technology solutions for startups, small and medium businesses, entrepreneurs, and established organizations. Explore our seven service domains below or request a tailored consultation.
                </p>
                <div class="hero-cta-group mt-6">
                    <a href="#services-enquiry" class="btn btn-primary btn-lg btn-3d-primary">Request a Service Consultation</a>
                    <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="btn btn-outline-light btn-lg">Call <?php echo skyca_e($settings['business_phone']); ?></a>
                </div>
            </div>

            <div class="inner-hero-visual scene-3d" aria-hidden="true">
                <div class="glass-hero-vector-card card-3d-surface">
                    <div class="ghv-header">
                        <span class="ghv-dot dot-teal"></span>
                        <span class="ghv-dot dot-blue"></span>
                        <span class="ghv-dot dot-gold"></span>
                        <span class="ghv-title">SKYCA_7_PILLAR_MATRIX</span>
                    </div>
                    <svg viewBox="0 0 420 200" class="ghv-svg" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="16" y="20" width="118" height="48" rx="10" fill="#0F172A" stroke="#38BDF8" stroke-width="1.5"/>
                        <text x="75" y="48" text-anchor="middle" fill="#F8FAFC" font-size="10" font-weight="700" font-family="Inter, sans-serif">01. Custom Software</text>
                        <rect x="151" y="20" width="118" height="48" rx="10" fill="#0F172A" stroke="#2DD4BF" stroke-width="1.5"/>
                        <text x="210" y="48" text-anchor="middle" fill="#5EEAD4" font-size="10" font-weight="700" font-family="Inter, sans-serif">02. Web &amp; Mobile</text>
                        <rect x="286" y="20" width="118" height="48" rx="10" fill="#0F172A" stroke="#FBBF24" stroke-width="1.5"/>
                        <text x="345" y="48" text-anchor="middle" fill="#FDE68A" font-size="10" font-weight="700" font-family="Inter, sans-serif">03. SaaS &amp; Cloud</text>

                        <path d="M75 68 V96 H345 V68" stroke="#38BDF8" stroke-width="1.6" stroke-dasharray="4 3" class="svg-flow-line"/>
                        <path d="M210 68 V130" stroke="#2DD4BF" stroke-width="2" stroke-dasharray="4 3" class="svg-flow-line"/>

                        <rect x="130" y="80" width="160" height="44" rx="12" fill="#09152E" stroke="#38BDF8" stroke-width="2"/>
                        <text x="210" y="106" text-anchor="middle" fill="#FFFFFF" font-size="11" font-weight="800" font-family="Plus Jakarta Sans, sans-serif">04. AI &amp; IT Consulting</text>

                        <rect x="16" y="138" width="118" height="46" rx="10" fill="#0F172A" stroke="#2DD4BF" stroke-width="1.5"/>
                        <text x="75" y="165" text-anchor="middle" fill="#E2E8F0" font-size="9.5" font-weight="700" font-family="Inter, sans-serif">05. Growth &amp; Cyber</text>
                        <rect x="151" y="138" width="118" height="46" rx="10" fill="#0F172A" stroke="#38BDF8" stroke-width="1.5"/>
                        <text x="210" y="165" text-anchor="middle" fill="#E2E8F0" font-size="9.5" font-weight="700" font-family="Inter, sans-serif">06. Data Analytics</text>
                        <rect x="286" y="138" width="118" height="46" rx="10" fill="#0F172A" stroke="#FBBF24" stroke-width="1.5"/>
                        <text x="345" y="165" text-anchor="middle" fill="#FDE68A" font-size="9.5" font-weight="700" font-family="Inter, sans-serif">07. E-Commerce</text>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Quick Category Jump Navigation -->
<div class="category-jump-bar">
    <div class="container">
        <div class="category-jump-list" role="navigation" aria-label="Jump to service category">
            <?php foreach ($allServices as $srv): ?>
                <a href="#category-<?php echo skyca_e($srv['slug']); ?>" class="category-jump-pill">
                    <span class="jump-num">0<?php echo (int) $srv['id']; ?></span>
                    <span><?php echo skyca_e($srv['short_title']); ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- Detailed Breakdown of All 7 Official Service Categories -->
<div class="services-detailed-list">
    <?php foreach ($allServices as $srv): ?>
        <?php $isEven = (((int) $srv['id']) % 2 === 0); ?>
        <section class="section <?php echo $isEven ? 'bg-slate' : 'bg-white'; ?>" id="category-<?php echo skyca_e($srv['slug']); ?>">
            <div class="container">
                <div class="service-overview-block">
                    <div class="service-overview-main">
                        <div class="service-overview-badge-row">
                            <span class="service-pillar-badge">Service Pillar 0<?php echo (int) $srv['id']; ?></span>
                            <span class="service-badge-sub"><?php echo skyca_e($srv['hero_badge']); ?></span>
                        </div>

                        <h2 class="service-overview-title">
                            <a href="/services/<?php echo skyca_e($srv['slug']); ?>">
                                <?php echo skyca_e($srv['title']); ?>
                            </a>
                        </h2>

                        <p class="service-overview-lead">
                            <?php echo skyca_e($srv['solution_overview']); ?>
                        </p>

                        <div class="who-needs-box">
                            <strong>Who May Need This Service:</strong>
                            <p><?php echo skyca_e($srv['who_needs_it']); ?></p>
                        </div>

                        <h3 class="subservices-heading">Core Capabilities Included:</h3>
                        <div class="subservices-grid">
                            <?php foreach ($srv['sub_services'] as $sub): ?>
                                <div class="subservice-item-card">
                                    <h4>
                                        <svg class="bullet-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span><?php echo skyca_e($sub['name']); ?></span>
                                    </h4>
                                    <p><?php echo skyca_e($sub['description']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <aside class="service-overview-sidebar">
                        <div class="service-deliverables-box">
                            <h3>Key Project Deliverables</h3>
                            <ul class="deliverables-check-list">
                                <?php foreach ($srv['deliverables'] as $deliv): ?>
                                    <li>
                                        <svg class="icon-xs text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                        <span><?php echo skyca_e($deliv); ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <div class="tech-tags-wrap">
                                <span class="tech-tags-label">Technologies &amp; Standards:</span>
                                <div class="cap-badges">
                                    <?php foreach ($srv['tech_stack'] as $tech): ?>
                                        <span><?php echo skyca_e($tech); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <div class="sidebar-cta-stack">
                                <a href="/services/<?php echo skyca_e($srv['slug']); ?>" class="btn btn-primary btn-block">
                                    <span>View Dedicated Service Page</span>
                                    <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                </a>
                                <a href="/contact?service=<?php echo rawurlencode($srv['slug']); ?>#enquiry-form" class="btn btn-secondary btn-block">
                                    Request Consultation for This Service
                                </a>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </section>
    <?php endforeach; ?>
</div>

<!-- How to Request a Consultation & Embedded Enquiry Form -->
<section class="section bg-slate" id="services-enquiry">
    <div class="container">
        <div class="enquiry-split-layout">
            <div class="enquiry-info-column">
                <span class="section-eyebrow">How to Engage Us</span>
                <h2 class="section-title">How to Request a Consultation or Project Estimate</h2>
                <p class="section-subtitle">
                    Whether you have a complete technical specification or are still exploring the right approach, our team makes it straightforward to discuss your options.
                </p>

                <div class="enquiry-benefit-list">
                    <div class="enquiry-benefit-item">
                        <div class="benefit-num">1</div>
                        <div>
                            <h4>Select Your Required Service</h4>
                            <p>Choose one of our seven core service pillars or select "Multiple Services / General IT Consultation" in the form.</p>
                        </div>
                    </div>
                    <div class="enquiry-benefit-item">
                        <div class="benefit-num">2</div>
                        <div>
                            <h4>Share Your Business Objectives</h4>
                            <p>Outline the business problem you want to solve, desired features, approximate timeline, and any existing systems.</p>
                        </div>
                    </div>
                    <div class="enquiry-benefit-item">
                        <div class="benefit-num">3</div>
                        <div>
                            <h4>Technical Review &amp; Proposal</h4>
                            <p>We review your submission, schedule a consultation call or email thread, and provide a structured roadmap and quotation.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="enquiry-form-column">
                <?php
                skyca_render_enquiry_form([
                    'form_id' => 'services-page-enquiry-form',
                    'heading' => 'Discuss Your Service Requirements',
                ]);
                ?>
            </div>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
