<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — SOLUTION PORTFOLIO & REFERENCE ARCHITECTURES (pages/portfolio.php)
 * Showcases capability-focused engineering blueprints, interactive 3D vector architectures,
 * and technical solution patterns across all 7 practice areas (zero fabricated client claims).
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';
require_once dirname(__DIR__) . '/includes/enquiry_form.php';

$settings    = skyca_get_settings();
$allServices = skyca_get_all_services();

$breadcrumbs = [
    ['name' => 'Home', 'path' => '/'],
    ['name' => 'Portfolio & Solution Blueprints', 'path' => '/portfolio'],
];

$blueprints = [
    [
        'id'          => 'BP-01',
        'category'    => 'SaaS Solutions & Cloud Services',
        'slug'        => 'saas-cloud-solutions',
        'accent'      => 'blue',
        'title'       => 'Multi-Tenant Enterprise SaaS & Subscription Platform Architecture',
        'summary'     => 'A modular cloud-native software architecture engineered for role-based tenant isolation, automated recurring billing webhooks, audit logging, and high-concurrency API gateways.',
        'highlights'  => [
            'Isolated tenant data schemas & row-level security governance',
            'Automated onboarding, RBAC permissions & subscription lifecycle webhooks',
            'Horizontal cloud scaling with zero-downtime schema migrations',
        ],
        'tech'        => ['Next.js / React', 'Node.js / PHP 8.3', 'MySQL / PostgreSQL', 'REST & Webhooks', 'Cloud CI/CD'],
    ],
    [
        'id'          => 'BP-02',
        'category'    => 'Information Technology Services & Software Development',
        'slug'        => 'software-development',
        'accent'      => 'teal',
        'title'       => 'Custom ERP, CRM & Internal Workflow Automation Suite',
        'summary'     => 'Bespoke operational software replacing fragmented spreadsheets with unified inventory, lead pipeline tracking, multi-stage approvals, and real-time executive reporting.',
        'highlights'  => [
            'Custom lead & deal pipeline engine with automated priority scoring',
            'Two-way webhook synchronization with accounting and external CRMs',
            'Granular staff access controls and tamper-evident activity logs',
        ],
        'tech'        => ['TypeScript', 'PHP 8.x PDO', 'MySQL', 'HMAC Webhooks', 'Role-Based Access'],
    ],
    [
        'id'          => 'BP-03',
        'category'    => 'E-Commerce Solutions and Related Technology Activities',
        'slug'        => 'ecommerce-solutions',
        'accent'      => 'gold',
        'title'       => 'High-Conversion D2C & B2B Omnichannel Commerce Engine',
        'summary'     => 'Performance-engineered online storefront and wholesale ordering portal featuring real-time catalog synchronization, server-verified payment gateway integration, and automated dispatch workflows.',
        'highlights'  => [
            'Sub-second catalog filtering & Core Web Vitals optimization',
            'Cryptographically verified payment gateway webhooks (Razorpay / Stripe)',
            'Automated GST invoicing, inventory locking & order status notifications',
        ],
        'tech'        => ['Next.js / PWA', 'RESTful Commerce API', 'MySQL', 'Payment SDKs', 'CDN Caching'],
    ],
    [
        'id'          => 'BP-04',
        'category'    => 'Artificial Intelligence Solutions & IT Consulting',
        'slug'        => 'ai-it-consulting',
        'accent'      => 'blue',
        'title'       => 'Knowledge-Grounded AI Assistant & Document Triage Pipeline',
        'summary'     => 'Enterprise AI workflow integration combining Retrieval-Augmented Generation (RAG), structured prompt guardrails, and human-in-the-loop approval queues for customer support and internal operations.',
        'highlights'  => [
            'Grounded strictly in verified company documentation and knowledge bases',
            'Automated lead qualification, ticket classification & summary extraction',
            'Strict API key isolation, token rate limiting & PII redaction controls',
        ],
        'tech'        => ['Python / Node.js', 'LLM APIs & RAG', 'Vector Search', 'JSON Schema Guardrails', 'Webhook Triage'],
    ],
    [
        'id'          => 'BP-05',
        'category'    => 'Digital Marketing & Cyber Security Services',
        'slug'        => 'digital-marketing-cybersecurity',
        'accent'      => 'teal',
        'title'      => 'Hardened Ad-Conversion Platform & Attribution CRM System',
        'summary'     => 'Full-funnel acquisition architecture pairing high-speed landing pages, GCLID/FBCLID/UTM attribution persistence, and OWASP Top 10 application security hardening.',
        'highlights'  => [
            'End-to-end Google Ads (GCLID) & Meta Ads (FBCLID) session attribution',
            'Server-verified conversion triggers with zero PII leakage in URLs',
            'CSRF tokens, parameterized SQL queries, CSP/HSTS headers & rate limiting',
        ],
        'tech'        => ['Semantic HTML5', 'GA4 & Google Ads API', 'OWASP Hardening', 'Schema.org JSON-LD', 'Ads CRM'],
    ],
    [
        'id'          => 'BP-06',
        'category'    => 'Data Analytics & Technology Support Services',
        'slug'        => 'data-analytics-support',
        'accent'      => 'gold',
        'title'       => 'Executive BI Telemetry & Real-Time KPI Command Center',
        'summary'     => 'Centralized analytics dashboard aggregating sales pipelines, campaign ROI, and operational SLA metrics from relational databases into interactive visual reports.',
        'highlights'  => [
            'Unified SQL aggregation across sales, marketing & operations datasets',
            'Role-filtered KPI scorecards with formula-safe CSV/Excel exports',
            'Proactive uptime monitoring, query indexing & technical support workflows',
        ],
        'tech'        => ['SQL Analytics', 'Interactive SVG Charts', 'MySQL Indexing', 'Automated ETL', 'SLA Monitoring'],
    ],
];

$pageMeta = [
    'title'       => 'Solution Portfolio & Engineering Blueprints | Skyca Digital Private Limited',
    'description' => 'Explore Skyca Digital Private Limited’s enterprise software architectures, SaaS blueprints, AI workflow systems, e-commerce engines, and cybersecurity-hardened web platforms.',
    'path'        => '/portfolio',
    'breadcrumbs' => $breadcrumbs,
];

require dirname(__DIR__) . '/includes/header.php';
?>

<!-- LUXURY MIDNIGHT PAGE HERO WITH 3D VECTOR SHOWCASE -->
<section class="page-hero bg-navy text-light hero-3d-stage">
    <div class="hero-bg-grid" aria-hidden="true"></div>
    <div class="hero-3d-orb hero-3d-orb-1" aria-hidden="true"></div>
    <div class="hero-3d-orb hero-3d-orb-2" aria-hidden="true"></div>
    <div class="container">
        <?php skyca_render_breadcrumbs($breadcrumbs); ?>
        <div class="inner-hero-split">
            <div class="page-hero-content">
                <span class="section-eyebrow eyebrow-light">Engineering Architecture &amp; Solution Blueprints</span>
                <h1 class="page-hero-title">Production-Grade Digital Systems &amp; Reference Architectures</h1>
                <p class="page-hero-subtitle">
                    Explore the architectural blueprints and full-stack systems engineered by <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong> across custom software, multi-tenant SaaS, AI automation, e-commerce, and security-hardened web platforms.
                </p>
                <div class="hero-cta-group mt-6">
                    <a href="#portfolio-enquiry" class="btn btn-primary btn-lg btn-3d-primary">Request a Custom Architecture Blueprint</a>
                    <a href="/services" class="btn btn-outline-light btn-lg">Explore All 7 Services</a>
                </div>
            </div>

            <div class="inner-hero-visual scene-3d" aria-hidden="true">
                <div class="glass-hero-vector-card card-3d-surface">
                    <div class="ghv-header">
                        <span class="ghv-dot dot-teal"></span>
                        <span class="ghv-dot dot-blue"></span>
                        <span class="ghv-dot dot-gold"></span>
                        <span class="ghv-title">SKYCA_BLUEPRINT_ENGINE_v4.2</span>
                    </div>
                    <svg viewBox="0 0 420 210" class="ghv-svg" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="bpGrad1" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#38BDF8"/>
                                <stop offset="100%" stop-color="#033EB6"/>
                            </linearGradient>
                            <linearGradient id="bpGrad2" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#2DD4BF"/>
                                <stop offset="100%" stop-color="#0D9488"/>
                            </linearGradient>
                            <linearGradient id="bpGrad3" x1="0" y1="0" x2="1" y2="1">
                                <stop offset="0%" stop-color="#FBBF24"/>
                                <stop offset="100%" stop-color="#D97706"/>
                            </linearGradient>
                        </defs>
                        <!-- Isometric 3D Layered Platform Stack -->
                        <polygon points="210,22 350,72 210,122 70,72" fill="#0F172A" stroke="url(#bpGrad1)" stroke-width="2"/>
                        <polygon points="210,56 350,106 210,156 70,106" fill="#091326" stroke="url(#bpGrad2)" stroke-width="2" opacity="0.92"/>
                        <polygon points="210,90 350,140 210,190 70,140" fill="#050B18" stroke="url(#bpGrad3)" stroke-width="2" opacity="0.88"/>
                        <!-- Connecting Vertical 3D Data Pillars -->
                        <line x1="210" y1="22" x2="210" y2="190" stroke="#38BDF8" stroke-width="1.5" stroke-dasharray="4 3" class="svg-flow-line"/>
                        <circle cx="210" cy="72" r="6" fill="#38BDF8"/>
                        <circle cx="210" cy="106" r="6" fill="#2DD4BF"/>
                        <circle cx="210" cy="140" r="6" fill="#FBBF24"/>
                        <text x="210" y="68" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">UI / UX &amp; PWA Layer</text>
                        <text x="210" y="102" text-anchor="middle" fill="#5EEAD4" font-size="10" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">API, SaaS &amp; AI Core</text>
                        <text x="210" y="136" text-anchor="middle" fill="#FDE68A" font-size="10" font-weight="700" font-family="Plus Jakarta Sans, sans-serif">Cloud DB &amp; Security</text>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- BLUEPRINTS SHOWCASE GRID -->
<section class="section bg-white">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Reference Architectures</span>
            <h2 class="section-title">Engineered for Scalability, Security &amp; Commercial Impact</h2>
            <p class="section-subtitle">
                Every system we build follows modular architectural patterns tailored to your operational workflows, traffic demands, and security requirements.
            </p>
        </div>

        <div class="portfolio-grid">
            <?php foreach ($blueprints as $bp): ?>
                <article class="portfolio-blueprint-card card-3d-surface accent-<?php echo skyca_e($bp['accent']); ?>">
                    <div class="pbc-visual-banner" aria-hidden="true">
                        <div class="pbc-badge-row">
                            <span class="pbc-code"><?php echo skyca_e($bp['id']); ?></span>
                            <span class="pbc-cat"><?php echo skyca_e($bp['category']); ?></span>
                        </div>
                        <svg viewBox="0 0 360 90" class="pbc-mini-svg" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="12" y="18" width="90" height="54" rx="10" fill="#0F172A" stroke="#38BDF8" stroke-width="1.5"/>
                            <text x="57" y="49" text-anchor="middle" fill="#E2E8F0" font-size="9.5" font-weight="700" font-family="Inter, sans-serif">Client Edge</text>
                            <path d="M102 45 H138" stroke="#2DD4BF" stroke-width="2" stroke-dasharray="4 3" class="svg-flow-line"/>
                            <rect x="138" y="12" width="104" height="66" rx="12" fill="#09152E" stroke="#2DD4BF" stroke-width="1.8"/>
                            <text x="190" y="42" text-anchor="middle" fill="#FFFFFF" font-size="10" font-weight="800" font-family="Inter, sans-serif">Skyca Engine</text>
                            <text x="190" y="58" text-anchor="middle" fill="#5EEAD4" font-size="8.5" font-weight="600" font-family="Inter, sans-serif"><?php echo skyca_e($bp['id']); ?> Core</text>
                            <path d="M242 45 H276" stroke="#FBBF24" stroke-width="2" stroke-dasharray="4 3" class="svg-flow-line"/>
                            <rect x="276" y="18" width="72" height="54" rx="10" fill="#0F172A" stroke="#FBBF24" stroke-width="1.5"/>
                            <text x="312" y="49" text-anchor="middle" fill="#FDE68A" font-size="9.5" font-weight="700" font-family="Inter, sans-serif">Data/Cloud</text>
                        </svg>
                    </div>

                    <div class="pbc-body">
                        <h3 class="pbc-title">
                            <a href="/services/<?php echo skyca_e($bp['slug']); ?>">
                                <?php echo skyca_e($bp['title']); ?>
                            </a>
                        </h3>
                        <p class="pbc-summary"><?php echo skyca_e($bp['summary']); ?></p>

                        <ul class="service-card-bullets">
                            <?php foreach ($bp['highlights'] as $hl): ?>
                                <li>
                                    <svg class="bullet-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span><?php echo skyca_e($hl); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>

                        <div class="cap-badges mb-4">
                            <?php foreach ($bp['tech'] as $t): ?>
                                <span><?php echo skyca_e($t); ?></span>
                            <?php endforeach; ?>
                        </div>

                        <div class="service-card-footer">
                            <a href="/services/<?php echo skyca_e($bp['slug']); ?>" class="service-learn-link">
                                <span>Explore Practice Area</span>
                                <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </a>
                            <a href="/contact?service=<?php echo rawurlencode($bp['slug']); ?>#enquiry-form" class="service-quote-link">
                                Request Blueprint
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- EMBEDDED CONSULTATION & ENQUIRY SECTION -->
<section class="section bg-slate" id="portfolio-enquiry">
    <div class="container">
        <div class="enquiry-split-layout">
            <div class="enquiry-info-column">
                <span class="section-eyebrow">Custom Architecture &amp; Delivery</span>
                <h2 class="section-title">Ready to Architect Your Custom Software or Digital Platform?</h2>
                <p class="section-subtitle">
                    Share your functional goals, integration requirements, and timeline. Our engineering team will prepare a tailored architecture recommendation and milestone quotation.
                </p>

                <div class="enquiry-benefit-list">
                    <div class="enquiry-benefit-item">
                        <div class="benefit-num">1</div>
                        <div>
                            <h4>100% Custom Source Code Ownership</h4>
                            <p>Clean, modular, documented repositories handed over upon project completion.</p>
                        </div>
                    </div>
                    <div class="enquiry-benefit-item">
                        <div class="benefit-num">2</div>
                        <div>
                            <h4>Security &amp; Scalability Built In</h4>
                            <p>Parameterized queries, RBAC governance, and cloud-ready deployment configurations.</p>
                        </div>
                    </div>
                    <div class="enquiry-benefit-item">
                        <div class="benefit-num">3</div>
                        <div>
                            <h4>Milestone-Driven Transparency</h4>
                            <p>Clear staging demonstrations and predictable engineering sprints.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="enquiry-form-column">
                <?php
                skyca_render_enquiry_form([
                    'form_id'    => 'portfolio-enquiry-form',
                    'heading'    => 'Request a Custom Solution Proposal',
                    'subheading' => 'Tell us about the platform or workflow you want to build.',
                ]);
                ?>
            </div>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
