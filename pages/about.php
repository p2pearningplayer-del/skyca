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

<!-- Page Hero (Luxury Midnight 3D & Vector Stage) -->
<section class="page-hero bg-navy text-light hero-3d-stage">
    <div class="hero-bg-grid" aria-hidden="true"></div>
    <div class="hero-3d-orb hero-3d-orb-1" aria-hidden="true"></div>
    <div class="hero-3d-orb hero-3d-orb-2" aria-hidden="true"></div>
    <div class="container">
        <?php skyca_render_breadcrumbs($breadcrumbs); ?>
        <div class="inner-hero-split">
            <div class="page-hero-content">
                <span class="section-eyebrow eyebrow-light">Corporate Profile &amp; Governance</span>
                <h1 class="page-hero-title">About <?php echo skyca_e(COMPANY_LEGAL_NAME); ?></h1>
                <p class="page-hero-subtitle">
                    An Information Technology and Software Development company dedicated to helping organizations design, build, secure, and scale practical digital systems.
                </p>
                <div class="hero-cta-group mt-6">
                    <a href="/contact#enquiry-form" class="btn btn-primary btn-lg btn-3d-primary">Partner With Skyca Digital</a>
                    <a href="/portfolio" class="btn btn-outline-light btn-lg">Explore Solution Blueprints</a>
                </div>
            </div>

            <div class="inner-hero-visual scene-3d" aria-hidden="true">
                <div class="glass-hero-vector-card card-3d-surface">
                    <div class="ghv-header">
                        <span class="ghv-dot dot-teal"></span>
                        <span class="ghv-dot dot-blue"></span>
                        <span class="ghv-dot dot-gold"></span>
                        <span class="ghv-title">CIN: U62013AS2026PTC030738</span>
                    </div>
                    <svg viewBox="0 0 420 190" class="ghv-svg" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="210" cy="95" r="68" stroke="#1E3A8A" stroke-width="1.5" stroke-dasharray="5 4"/>
                        <circle cx="210" cy="95" r="46" fill="#09152E" stroke="#2DD4BF" stroke-width="2"/>
                        <polygon points="210,66 234,80 210,94 186,80" fill="#38BDF8"/>
                        <polygon points="186,80 210,94 210,118 186,104" fill="#058DF0"/>
                        <polygon points="234,80 210,94 210,118 234,104" fill="#033EB6"/>
                        <rect x="20" y="30" width="112" height="44" rx="10" fill="#0F172A" stroke="#38BDF8" stroke-width="1.4"/>
                        <text x="76" y="56" text-anchor="middle" fill="#F8FAFC" font-size="9.5" font-weight="700" font-family="Inter, sans-serif">Clean Architecture</text>
                        <rect x="288" y="30" width="112" height="44" rx="10" fill="#0F172A" stroke="#FBBF24" stroke-width="1.4"/>
                        <text x="344" y="56" text-anchor="middle" fill="#FDE68A" font-size="9.5" font-weight="700" font-family="Inter, sans-serif">RoC Guwahati Active</text>
                        <rect x="20" y="116" width="112" height="44" rx="10" fill="#0F172A" stroke="#2DD4BF" stroke-width="1.4"/>
                        <text x="76" y="142" text-anchor="middle" fill="#5EEAD4" font-size="9.5" font-weight="700" font-family="Inter, sans-serif">OWASP Security</text>
                        <rect x="288" y="116" width="112" height="44" rx="10" fill="#0F172A" stroke="#38BDF8" stroke-width="1.4"/>
                        <text x="344" y="142" text-anchor="middle" fill="#F8FAFC" font-size="9.5" font-weight="700" font-family="Inter, sans-serif">7 Core IT Pillars</text>
                    </svg>
                </div>
            </div>
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
                        <source srcset="<?php echo skyca_e(skyca_asset('assets/images/skyca-logo-full.webp')); ?>" type="image/webp">
                        <img
                            src="<?php echo skyca_e(skyca_asset('assets/images/skyca-logo-full.png')); ?>"
                            alt="<?php echo skyca_e(COMPANY_LEGAL_NAME); ?> Official Logo"
                            width="280"
                            height="250"
                            class="about-full-logo"
                        >
                    </picture>
                </div>

                <!-- Verified Corporate Information Block (Official MCA / RoC Details) -->
                <div class="verified-corp-table-wrap">
                    <h3 class="corp-table-title">Verified Corporate Information</h3>
                    <dl class="corp-info-dl">
                        <div class="corp-info-row">
                            <dt>Company Name</dt>
                            <dd><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></dd>
                        </div>
                        <?php if (!empty($settings['company_cin'])): ?>
                        <div class="corp-info-row">
                            <dt>CIN</dt>
                            <dd><code><?php echo skyca_e($settings['company_cin']); ?></code></dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_incorporation_date'])): ?>
                        <div class="corp-info-row">
                            <dt>Date of Incorporation</dt>
                            <dd><?php echo skyca_e($settings['company_incorporation_date']); ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_status'])): ?>
                        <div class="corp-info-row">
                            <dt>Company Status</dt>
                            <dd><span class="status-pill status-converted"><?php echo skyca_e($settings['company_status']); ?></span></dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_class'])): ?>
                        <div class="corp-info-row">
                            <dt>Class / Category</dt>
                            <dd><?php echo skyca_e($settings['company_class']); ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_roc'])): ?>
                        <div class="corp-info-row">
                            <dt>Registrar of Companies</dt>
                            <dd><?php echo skyca_e($settings['company_roc']); ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_authorized_capital'])): ?>
                        <div class="corp-info-row">
                            <dt>Authorized Share Capital</dt>
                            <dd><?php echo skyca_e($settings['company_authorized_capital']); ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_paidup_capital'])): ?>
                        <div class="corp-info-row">
                            <dt>Paid-up Capital</dt>
                            <dd><?php echo skyca_e($settings['company_paidup_capital']); ?></dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_address'])): ?>
                        <div class="corp-info-row">
                            <dt>Registered Office Address</dt>
                            <dd>
                                <?php echo skyca_e($settings['company_address']); ?>
                                <?php if (!empty($settings['company_city'])): ?>, <?php echo skyca_e($settings['company_city']); ?><?php endif; ?>
                                <?php if (!empty($settings['company_state'])): ?>, <?php echo skyca_e($settings['company_state']); ?><?php endif; ?>
                                <?php if (!empty($settings['company_postal_code'])): ?> &ndash; <?php echo skyca_e($settings['company_postal_code']); ?>, India<?php endif; ?>
                            </dd>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_gstin'])): ?>
                        <div class="corp-info-row">
                            <dt>GSTIN</dt>
                            <dd><?php echo skyca_e($settings['company_gstin']); ?></dd>
                        </div>
                        <?php endif; ?>
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
