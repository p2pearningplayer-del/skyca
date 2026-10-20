<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — ABOUT US PAGE (pages/about.php)
 * Strictly factual corporate profile with dynamic editable company fields.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';

$settings    = skyca_get_settings();
$allServices = skyca_get_all_services();

$breadcrumbs = [
    ['name' => 'Home', 'path' => '/'],
    ['name' => 'About Us', 'path' => '/about'],
];

$pageMeta = [
    'title'       => 'About Us | Skyca Digital Private Limited — IT & Software Engineering',
    'description' => 'Learn about Skyca Digital Private Limited’s mission, engineering philosophy, service standards, and commitment to building reliable software and digital solutions.',
    'path'        => '/about',
    'breadcrumbs' => $breadcrumbs,
];

require dirname(__DIR__) . '/includes/header.php';
?>

<!-- Page Hero -->
<section class="page-hero bg-navy text-light">
    <div class="container">
        <?php skyca_render_breadcrumbs($breadcrumbs); ?>
        <div class="page-hero-content">
            <span class="section-eyebrow eyebrow-light">Corporate Profile</span>
            <h1 class="page-hero-title">About <?php echo skyca_e(COMPANY_LEGAL_NAME); ?></h1>
            <p class="page-hero-subtitle">
                An Information Technology and Software Development company dedicated to helping organizations design, build, secure, and scale practical digital systems.
            </p>
        </div>
    </div>
</section>

<!-- Mission & Corporate Identity -->
<section class="section bg-white">
    <div class="container">
        <div class="about-overview-grid">
            <div class="about-narrative">
                <span class="section-eyebrow">Our Mission &amp; Purpose</span>
                <h2 class="section-title">Turning Complex Technology Into Dependable Business Progress</h2>
                <p class="lead-paragraph">
                    <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong> was established with a clear objective: to provide startups, small and medium businesses, entrepreneurs, and established organizations with dependable software engineering and IT consulting—without unnecessary complexity or opaque agency jargon.
                </p>
                <p>
                    Across industries, businesses face a common challenge: off-the-shelf software is often too rigid for their operations, while poorly architected custom tools become difficult to maintain and secure. We bridge that gap by combining structured requirements analysis, modern full-stack engineering, and defensive security practices.
                </p>
                <p>
                    Whether we are developing a corporate web platform, architecting a multi-tenant SaaS application, integrating practical AI workflows, hardening an existing application, or building an e-commerce store, our focus remains on long-term reliability, usability, and measurable operational value.
                </p>
            </div>

            <div class="about-identity-card">
                <div class="official-brand-showcase">
                    <picture>
                        <source srcset="/assets/images/skyca-logo-full.webp" type="image/webp">
                        <img
                            src="/assets/images/skyca-logo-full.png"
                            alt="<?php echo skyca_e(COMPANY_LEGAL_NAME); ?> Official Logo"
                            width="280"
                            height="250"
                            class="about-full-logo"
                        >
                    </picture>
                </div>

                <!-- Verified Corporate Information Block (Supports Editable Optional Fields) -->
                <div class="verified-corp-table-wrap">
                    <h3 class="corp-table-title">Verified Corporate Information</h3>
                    <dl class="corp-info-dl">
                        <div class="corp-info-row">
                            <dt>Legal Entity Name</dt>
                            <dd><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></dd>
                        </div>
                        <div class="corp-info-row">
                            <dt>Primary Industry</dt>
                            <dd>Information Technology &amp; Software Development</dd>
                        </div>
                        <div class="corp-info-row">
                            <dt>Official Website</dt>
                            <dd><a href="<?php echo skyca_e(SITE_URL); ?>"><?php echo skyca_e(SITE_URL); ?></a></dd>
                        </div>
                        <div class="corp-info-row">
                            <dt>Business Email</dt>
                            <dd><a href="mailto:<?php echo skyca_e($settings['business_email']); ?>"><?php echo skyca_e($settings['business_email']); ?></a></dd>
                        </div>
                        <div class="corp-info-row">
                            <dt>Business Phone</dt>
                            <dd><a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>"><?php echo skyca_e($settings['business_phone']); ?></a></dd>
                        </div>
                        <?php if (!empty($settings['company_working_hours'])): ?>
                        <div class="corp-info-row">
                            <dt>Consultation Hours</dt>
                            <dd><?php echo skyca_e($settings['company_working_hours']); ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_address'])): ?>
                        <div class="corp-info-row">
                            <dt>Registered Address</dt>
                            <dd>
                                <?php echo skyca_e($settings['company_address']); ?>
                                <?php if (!empty($settings['company_city'])): ?>, <?php echo skyca_e($settings['company_city']); ?><?php endif; ?>
                                <?php if (!empty($settings['company_state'])): ?>, <?php echo skyca_e($settings['company_state']); ?><?php endif; ?>
                                <?php if (!empty($settings['company_postal_code'])): ?> - <?php echo skyca_e($settings['company_postal_code']); ?><?php endif; ?>
                            </dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_cin'])): ?>
                        <div class="corp-info-row">
                            <dt>CIN</dt>
                            <dd><?php echo skyca_e($settings['company_cin']); ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_gstin'])): ?>
                        <div class="corp-info-row">
                            <dt>GSTIN</dt>
                            <dd><?php echo skyca_e($settings['company_gstin']); ?></dd>
                        </div>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service Philosophy & Operating Principles -->
<section class="section bg-slate">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Our Service Philosophy</span>
            <h2 class="section-title">Principles That Guide Every Project</h2>
            <p class="section-subtitle">
                We believe trust in an IT partner is earned through transparency, sound engineering, and honest communication.
            </p>
        </div>

        <div class="philosophy-grid">
            <div class="philosophy-card">
                <span class="phil-num">01</span>
                <h3>Problem-First Architecture</h3>
                <p>We never force a predetermined tool onto a problem. We evaluate your existing environment, hosting constraints, budget, and operational goals before recommending a technology stack.</p>
            </div>

            <div class="philosophy-card">
                <span class="phil-num">02</span>
                <h3>Clean &amp; Documented Delivery</h3>
                <p>Software should be an asset, not a liability. We write readable code, use structured database migrations, separate secrets into environment configuration, and provide clear handover documentation.</p>
            </div>

            <div class="philosophy-card">
                <span class="phil-num">03</span>
                <h3>Honest, Realistic Commitments</h3>
                <p>We do not make exaggerated claims about overnight search rankings, guaranteed ad returns, or absolute security immunity. Instead, we commit to verifiable engineering best practices and clear milestones.</p>
            </div>

            <div class="philosophy-card">
                <span class="phil-num">04</span>
                <h3>Security &amp; Privacy Stewardship</h3>
                <p>From parameterized database queries and CSRF protection to strict access control and data minimization, we build systems that respect your business data and your customers’ privacy.</p>
            </div>
        </div>
    </div>
</section>

<!-- Overview of Practice Areas -->
<section class="section bg-white">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">What We Do</span>
            <h2 class="section-title">Our Seven Core Practice Areas</h2>
            <p class="section-subtitle">Explore how Skyca Digital Private Limited can support your organization across the full technology lifecycle:</p>
        </div>

        <div class="about-services-compact-grid">
            <?php foreach ($allServices as $srv): ?>
                <a href="/services/<?php echo skyca_e($srv['slug']); ?>" class="about-service-link-card">
                    <div class="as-icon" aria-hidden="true"><?php echo $srv['icon_svg']; ?></div>
                    <div>
                        <h3><?php echo skyca_e($srv['title']); ?></h3>
                        <p><?php echo skyca_e($srv['summary']); ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="section-bottom-cta text-center mt-8">
            <a href="/contact#enquiry-form" class="btn btn-primary btn-lg">Start a Project Discussion</a>
            <a href="/services" class="btn btn-secondary btn-lg ml-3">Explore All Services</a>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
