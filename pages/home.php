<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — HOME PAGE (pages/home.php)
 * Implements all 10 required homepage sections with capability-focused B2B copy.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';
require_once dirname(__DIR__) . '/includes/enquiry_form.php';

$allServices = skyca_get_all_services();
$settings    = skyca_get_settings();

$homeFaqs = [
    [
        'q' => 'What types of businesses does Skyca Digital Private Limited work with?',
        'a' => 'We work with early-stage and growth startups, small and medium businesses (SMBs), entrepreneurs launching new digital products, and established companies looking to modernize operations through custom software, web and mobile applications, cloud infrastructure, AI integration, cybersecurity, and e-commerce technology.'
    ],
    [
        'q' => 'How do we start a project or request a quotation?',
        'a' => 'Simply submit your requirements through our project enquiry form, email us at info@skycadigital.com, or call +91 6002064873. Our technical team will review your goals, schedule a discovery discussion if needed, and prepare a structured scope and estimate.'
    ],
    [
        'q' => 'Can you build a custom solution tailored to our existing business workflow?',
        'a' => 'Yes. Rather than forcing your organization into rigid templates, we analyze your current workflows, user roles, and data structures to architect software and web platforms that fit how your business actually operates.'
    ],
    [
        'q' => 'Do you provide ongoing maintenance and technical support after launch?',
        'a' => 'Yes. We offer post-launch technical support, software maintenance, performance optimization, security patching, and iterative feature enhancements so your application remains reliable as your business grows.'
    ],
    [
        'q' => 'How do you handle application security and data privacy?',
        'a' => 'We integrate defensive security practices from day one—including parameterized database queries to prevent SQL injection, output encoding against XSS, CSRF token verification, strong password hashing, role-based access control, and strict environment secret management.'
    ],
    [
        'q' => 'Can you help us prepare landing pages and conversion tracking for Google Ads or Meta Ads?',
        'a' => 'Yes. We design fast, service-specific landing pages with clear call-to-action flows, server-verified lead capture, UTM attribution storage, and consent-aware conversion tracking for Google Analytics 4, Google Ads, and Meta Pixel.'
    ],
];

$pageMeta = [
    'title'       => 'Skyca Digital Private Limited | IT Services, Custom Software, Web, Cloud & AI Solutions',
    'description' => 'Technology Solutions That Move Your Business Forward. Skyca Digital Private Limited delivers custom software development, web & mobile apps, SaaS & cloud architecture, AI integration, cybersecurity, data analytics, and e-commerce solutions.',
    'path'        => '/',
    'faqs'        => $homeFaqs,
];

require dirname(__DIR__) . '/includes/header.php';
?>

<!-- 2. HERO SECTION -->
<section class="hero-section">
    <div class="hero-bg-grid" aria-hidden="true"></div>
    <div class="container hero-container">
        <div class="hero-content">
            <div class="hero-eyebrow-pill">
                <span class="pulse-dot" aria-hidden="true"></span>
                <span>Skyca Digital Private Limited &mdash; Enterprise IT &amp; Software Engineering</span>
            </div>

            <h1 class="hero-headline">
                Technology Solutions That Move Your Business Forward.
            </h1>

            <p class="hero-subheadline">
                From custom software and web development to cloud solutions, AI, cybersecurity, and digital growth, Skyca Digital helps businesses turn technology into measurable progress.
            </p>

            <div class="hero-cta-group">
                <a href="#project-enquiry" class="btn btn-primary btn-lg">
                    <span>Discuss Your Project</span>
                    <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
                <a href="/services" class="btn btn-secondary btn-lg">
                    <span>Explore Our Services</span>
                </a>
            </div>

            <!-- Capability-Focused Trust Pillars (Zero Fabricated Numbers) -->
            <div class="hero-trust-strip">
                <div class="hero-trust-item">
                    <svg class="icon-sm text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/></svg>
                    <div>
                        <strong>Security-First Engineering</strong>
                        <span>OWASP-aligned code &amp; data protection</span>
                    </div>
                </div>
                <div class="hero-trust-item">
                    <svg class="icon-sm text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                    <div>
                        <strong>Clean, Maintainable Code</strong>
                        <span>Modular architecture built to scale</span>
                    </div>
                </div>
                <div class="hero-trust-item">
                    <svg class="icon-sm text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <div>
                        <strong>Milestone-Driven Delivery</strong>
                        <span>Transparent scope &amp; regular demos</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Hero Right Visual: Technical Architecture & Service Blueprint Card -->
        <div class="hero-visual">
            <div class="architecture-showcase-card">
                <div class="arch-card-header">
                    <div class="arch-brand-lockup">
                        <picture>
                            <source srcset="/assets/images/skyca-logo-mark.webp" type="image/webp">
                            <img src="/assets/images/skyca-logo-mark.png" alt="Skyca Digital Emblem" width="44" height="44" class="arch-emblem">
                        </picture>
                        <div>
                            <span class="arch-card-title">Skyca Digital Architecture Stack</span>
                            <span class="arch-card-subtitle">End-to-End Business Technology Lifecycle</span>
                        </div>
                    </div>
                    <span class="arch-status-badge">Production Ready</span>
                </div>

                <!-- Interactive / Visual System Layers -->
                <div class="arch-layers">
                    <div class="arch-layer">
                        <div class="arch-layer-num">01</div>
                        <div class="arch-layer-info">
                            <strong>Client &amp; Digital Experience Layer</strong>
                            <span>Corporate Websites &bull; Custom Web Portals &bull; Mobile Apps &bull; E-Commerce</span>
                        </div>
                    </div>
                    <div class="arch-layer">
                        <div class="arch-layer-num">02</div>
                        <div class="arch-layer-info">
                            <strong>Application, API &amp; AI Automation Layer</strong>
                            <span>Custom Software &bull; Multi-Tenant SaaS &bull; REST APIs &bull; AI Workflows</span>
                        </div>
                    </div>
                    <div class="arch-layer">
                        <div class="arch-layer-num">03</div>
                        <div class="arch-layer-info">
                            <strong>Data, Cloud &amp; Security Foundation</strong>
                            <span>MySQL / Cloud DBs &bull; Analytics Dashboards &bull; Application Hardening</span>
                        </div>
                    </div>
                </div>

                <!-- Visual SVG System Diagram -->
                <div class="arch-diagram-box" aria-hidden="true">
                    <svg viewBox="0 0 460 110" class="arch-svg" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="8" y="18" width="118" height="74" rx="10" fill="#EFF6FF" stroke="#BFDBFE" stroke-width="1.5"/>
                        <text x="67" y="48" text-anchor="middle" fill="#0F172A" font-size="11" font-weight="700" font-family="Inter, sans-serif">Web &amp; Mobile</text>
                        <text x="67" y="66" text-anchor="middle" fill="#033EB6" font-size="10" font-weight="600" font-family="Inter, sans-serif">Responsive UI</text>

                        <path d="M126 55H168" stroke="#058DF0" stroke-width="2" stroke-dasharray="4 3"/>
                        <circle cx="147" cy="55" r="4" fill="#033EB6"/>

                        <rect x="168" y="12" width="124" height="86" rx="10" fill="#0B1528" stroke="#033EB6" stroke-width="2"/>
                        <text x="230" y="45" text-anchor="middle" fill="#FFFFFF" font-size="11" font-weight="700" font-family="Inter, sans-serif">Skyca Core Engine</text>
                        <text x="230" y="63" text-anchor="middle" fill="#38BDF8" font-size="10" font-weight="600" font-family="Inter, sans-serif">APIs &bull; AI &bull; Security</text>
                        <text x="230" y="80" text-anchor="middle" fill="#94A3B8" font-size="9" font-family="Inter, sans-serif">Validated Workflows</text>

                        <path d="M292 55H334" stroke="#058DF0" stroke-width="2" stroke-dasharray="4 3"/>
                        <circle cx="313" cy="55" r="4" fill="#033EB6"/>

                        <rect x="334" y="18" width="118" height="74" rx="10" fill="#EFF6FF" stroke="#BFDBFE" stroke-width="1.5"/>
                        <text x="393" y="48" text-anchor="middle" fill="#0F172A" font-size="11" font-weight="700" font-family="Inter, sans-serif">Cloud &amp; Data</text>
                        <text x="393" y="66" text-anchor="middle" fill="#033EB6" font-size="10" font-weight="600" font-family="Inter, sans-serif">Analytics &amp; Growth</text>
                    </svg>
                </div>

                <div class="arch-card-footer">
                    <span>Direct Engineering Consultation:</span>
                    <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="arch-phone-link"><?php echo skyca_e($settings['business_phone']); ?></a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 3. SERVICES OVERVIEW SECTION -->
<section class="section bg-white" id="services-overview">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Our Core Capabilities</span>
            <h2 class="section-title">Comprehensive IT &amp; Software Development Services</h2>
            <p class="section-subtitle">
                We deliver structured, business-aligned technology solutions across seven specialized practice areas—helping you build new products, automate operations, and grow securely.
            </p>
        </div>

        <div class="services-grid">
            <?php foreach ($allServices as $srv): ?>
                <article class="service-card">
                    <div class="service-card-top">
                        <div class="service-icon-box" aria-hidden="true">
                            <?php echo $srv['icon_svg']; ?>
                        </div>
                        <span class="service-num-badge">0<?php echo (int) $srv['id']; ?></span>
                    </div>

                    <h3 class="service-card-title">
                        <a href="/services/<?php echo skyca_e($srv['slug']); ?>">
                            <?php echo skyca_e($srv['title']); ?>
                        </a>
                    </h3>

                    <p class="service-card-summary">
                        <?php echo skyca_e($srv['summary']); ?>
                    </p>

                    <ul class="service-card-bullets">
                        <?php foreach ($srv['sub_services'] as $sub): ?>
                            <li>
                                <svg class="bullet-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                <span><?php echo skyca_e($sub['name']); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="service-card-footer">
                        <a href="/services/<?php echo skyca_e($srv['slug']); ?>" class="service-learn-link">
                            <span>Explore Service</span>
                            <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                        <a href="/contact?service=<?php echo rawurlencode($srv['slug']); ?>#enquiry-form" class="service-quote-link">
                            Request Quote
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="section-bottom-cta text-center">
            <a href="/services" class="btn btn-secondary btn-lg">View Detailed Services Breakdown</a>
        </div>
    </div>
</section>

<!-- 4. WHY CHOOSE SKYCA DIGITAL SECTION -->
<section class="section bg-slate" id="why-skyca">
    <div class="container">
        <div class="why-grid">
            <div class="why-intro">
                <span class="section-eyebrow">Why Choose Skyca Digital</span>
                <h2 class="section-title">Engineering Discipline Meets Practical Business Focus</h2>
                <p class="section-subtitle">
                    Technology projects succeed when clear communication, sound architecture, and commercial practicality work together. Here is how Skyca Digital Private Limited approaches every client engagement:
                </p>

                <div class="why-brand-box">
                    <picture>
                        <source srcset="/assets/images/skyca-logo-full.webp" type="image/webp">
                        <img src="/assets/images/skyca-logo-full.png" alt="Skyca Digital Private Limited Official Logo" width="180" height="160" loading="lazy" class="why-logo-img">
                    </picture>
                    <div class="why-brand-text">
                        <h3>Transparent Corporate Partnership</h3>
                        <p>No inflated promises, no black-box development, and no lock-in traps. We document our work clearly and align every technical decision with your operational goals.</p>
                        <a href="/about" class="text-link">Learn more about our philosophy &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="why-cards-grid">
                <div class="why-feature-card">
                    <div class="why-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <h3>Clear Scope &amp; Technical Clarity</h3>
                    <p>Before writing code, we clarify workflows, user roles, deliverables, and milestones so you know exactly what is being built and how it supports your business.</p>
                </div>

                <div class="why-feature-card">
                    <div class="why-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
                    </div>
                    <h3>Maintainable, Modern Architecture</h3>
                    <p>We write clean, well-structured code with proper database indexing, modular components, and clear documentation so your software remains easy to maintain and extend.</p>
                </div>

                <div class="why-feature-card">
                    <div class="why-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3>Security &amp; Validation by Design</h3>
                    <p>Every form, API endpoint, and database query is built with server-side validation, parameterized SQL queries, XSS protection, and least-privilege access controls.</p>
                </div>

                <div class="why-feature-card">
                    <div class="why-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <h3>Conversion &amp; Performance Focused</h3>
                    <p>Whether building a corporate website, e-commerce store, or SaaS platform, we optimize for mobile responsiveness, fast page loads, technical SEO, and clear user conversion paths.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 5. OUR DEVELOPMENT AND DELIVERY PROCESS -->
<section class="section bg-white" id="process">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">How We Work</span>
            <h2 class="section-title">Our Structured 5-Stage Development &amp; Delivery Process</h2>
            <p class="section-subtitle">
                A predictable, transparent engineering workflow that keeps your stakeholders informed from initial discovery through production launch and ongoing support.
            </p>
        </div>

        <div class="process-timeline">
            <div class="process-step-card">
                <div class="process-step-badge">Stage 01</div>
                <h3>Discovery &amp; Requirement Analysis</h3>
                <p>We review your business objectives, target users, existing bottlenecks, and hosting constraints to define a realistic functional specification and project roadmap.</p>
            </div>

            <div class="process-step-card">
                <div class="process-step-badge">Stage 02</div>
                <h3>Architecture &amp; UI/UX Design</h3>
                <p>We design the database schema, system architecture, API contracts, and responsive user interfaces—ensuring usability and brand alignment before engineering begins.</p>
            </div>

            <div class="process-step-card">
                <div class="process-step-badge">Stage 03</div>
                <h3>Iterative Engineering &amp; Integration</h3>
                <p>Our developers build the frontend, backend logic, database migrations, and third-party integrations in structured milestones with regular progress reviews.</p>
            </div>

            <div class="process-step-card">
                <div class="process-step-badge">Stage 04</div>
                <h3>QA, Security &amp; Performance Testing</h3>
                <p>We test across mobile and desktop devices, verify form validation and database error handling, audit against SQLi/XSS/CSRF, and optimize load performance.</p>
            </div>

            <div class="process-step-card">
                <div class="process-step-badge">Stage 05</div>
                <h3>Deployment, Handover &amp; Support</h3>
                <p>We configure your production environment, SSL, DNS, and backups, deliver complete documentation, and provide ongoing maintenance and technical support.</p>
            </div>
        </div>
    </div>
</section>

<!-- 6. INDUSTRIES AND BUSINESS USE CASES WE CAN SERVE -->
<section class="section bg-navy text-light" id="industries">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow eyebrow-light">Who We Serve</span>
            <h2 class="section-title text-white">Industries &amp; Business Use Cases We Support</h2>
            <p class="section-subtitle text-slate-300">
                Our engineering capabilities adapt to the operational realities of startups, small and medium businesses, and established enterprises across diverse sectors.
            </p>
        </div>

        <div class="industries-grid">
            <div class="industry-card">
                <h3>Startups &amp; SaaS Founders</h3>
                <p>MVP development, multi-tenant cloud platforms, subscription workflows, API integrations, and scalable database architectures designed to take new software products to market.</p>
                <span class="industry-tag">Product Engineering &bull; Cloud &amp; SaaS</span>
            </div>

            <div class="industry-card">
                <h3>Small &amp; Medium Businesses (SMBs)</h3>
                <p>Professional corporate websites, lead management systems, internal process automation, custom CRM/ERP portals, and Google Ads landing pages that drive measurable enquiries.</p>
                <span class="industry-tag">Web Platforms &bull; Process Automation</span>
            </div>

            <div class="industry-card">
                <h3>Retail, D2C &amp; Wholesale Commerce</h3>
                <p>Custom online stores, B2B ordering portals, product catalog and inventory synchronization, secure payment gateway integration, and checkout speed optimization.</p>
                <span class="industry-tag">E-Commerce &bull; Order Workflows</span>
            </div>

            <div class="industry-card">
                <h3>Professional &amp; Financial Services</h3>
                <p>Secure client onboarding portals, document workflow automation, role-based dashboards, appointment/consultation systems, and application security hardening.</p>
                <span class="industry-tag">Client Portals &bull; Security Hardening</span>
            </div>

            <div class="industry-card">
                <h3>Logistics, Operations &amp; Field Teams</h3>
                <p>Mobile-responsive field reporting apps, dispatch and order tracking systems, real-time operational KPI dashboards, and automated alert workflows.</p>
                <span class="industry-tag">Mobile Web Apps &bull; Data Analytics</span>
            </div>

            <div class="industry-card">
                <h3>Education, Healthcare &amp; Service Platforms</h3>
                <p>Booking and enquiry platforms, knowledge-grounded AI support assistants, structured reporting dashboards, and reliable ongoing software maintenance.</p>
                <span class="industry-tag">Web Applications &bull; Applied AI</span>
            </div>
        </div>
    </div>
</section>

<!-- 7. FEATURED CAPABILITIES AND TECHNOLOGY EXPERTISE -->
<section class="section bg-white" id="capabilities">
    <div class="container">
        <div class="section-header text-center">
            <span class="section-eyebrow">Technology Stack &amp; Capabilities</span>
            <h2 class="section-title">Practical Expertise Across the Modern Technology Stack</h2>
            <p class="section-subtitle">
                We select reliable, well-supported technologies matched to your actual hosting environment, team capabilities, and long-term maintenance needs.
            </p>
        </div>

        <div class="capabilities-matrix">
            <div class="cap-group-card">
                <h3 class="cap-group-title">Frontend &amp; User Interfaces</h3>
                <p class="cap-group-desc">Responsive, accessible, fast-loading interfaces engineered for Core Web Vitals and multi-device compatibility.</p>
                <div class="cap-badges">
                    <span>HTML5 &amp; Semantic SEO</span>
                    <span>Tailwind CSS</span>
                    <span>TypeScript / ES6+</span>
                    <span>React &amp; Next.js</span>
                    <span>Progressive Web Apps (PWA)</span>
                    <span>WCAG Accessibility</span>
                </div>
            </div>

            <div class="cap-group-card">
                <h3 class="cap-group-title">Backend, APIs &amp; Business Logic</h3>
                <p class="cap-group-desc">Secure server-side applications, RESTful APIs, webhook processors, and background automation engines.</p>
                <div class="cap-badges">
                    <span>PHP 8.x</span>
                    <span>Node.js &amp; TypeScript</span>
                    <span>Python</span>
                    <span>RESTful API Design</span>
                    <span>Webhook Integrations</span>
                    <span>SMTP &amp; Transactional Mail</span>
                </div>
            </div>

            <div class="cap-group-card">
                <h3 class="cap-group-title">Databases &amp; Data Analytics</h3>
                <p class="cap-group-desc">Structured relational schemas, parameterized queries, indexing optimization, and custom KPI dashboards.</p>
                <div class="cap-badges">
                    <span>MySQL &amp; MariaDB</span>
                    <span>PostgreSQL</span>
                    <span>PDO Prepared Statements</span>
                    <span>Schema Migration Scripts</span>
                    <span>BI &amp; KPI Dashboards</span>
                    <span>CSV / Data Pipelines</span>
                </div>
            </div>

            <div class="cap-group-card">
                <h3 class="cap-group-title">Security, Cloud &amp; Growth Readiness</h3>
                <p class="cap-group-desc">Defensive web security, cloud deployment readiness, technical SEO, and consent-aware ad conversion tracking.</p>
                <div class="cap-badges">
                    <span>OWASP Top 10 Defenses</span>
                    <span>CSRF / XSS / SQLi Protection</span>
                    <span>HTTPS &amp; Security Headers</span>
                    <span>Google Analytics 4 (GA4)</span>
                    <span>Google Ads &amp; UTM Attribution</span>
                    <span>Schema.org JSON-LD</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 8. PROJECT ENQUIRY CTA & EMBEDDED LEAD GENERATION SECTION -->
<section class="section bg-slate" id="project-enquiry">
    <div class="container">
        <div class="enquiry-split-layout">
            <div class="enquiry-info-column">
                <span class="section-eyebrow">Start a Conversation</span>
                <h2 class="section-title">Let’s Discuss Your Software or Technology Requirements</h2>
                <p class="section-subtitle">
                    Whether you need a new corporate website, a custom software application, a SaaS MVP, an e-commerce store, or technical consulting, our team is ready to help you scope the right solution.
                </p>

                <div class="enquiry-benefit-list">
                    <div class="enquiry-benefit-item">
                        <div class="benefit-num">1</div>
                        <div>
                            <h4>Requirement &amp; Feasibility Review</h4>
                            <p>We review your project description and clarify functional, hosting, and integration needs.</p>
                        </div>
                    </div>
                    <div class="enquiry-benefit-item">
                        <div class="benefit-num">2</div>
                        <div>
                            <h4>Recommended Architecture &amp; Approach</h4>
                            <p>We recommend a practical technology stack suited to your timeline, budget, and growth plans.</p>
                        </div>
                    </div>
                    <div class="enquiry-benefit-item">
                        <div class="benefit-num">3</div>
                        <div>
                            <h4>Transparent Scope &amp; Quotation</h4>
                            <p>Receive a clear milestone breakdown and estimate with no obligation.</p>
                        </div>
                    </div>
                </div>

                <div class="direct-contact-card">
                    <h3>Prefer Direct Contact?</h3>
                    <p>Reach out to Skyca Digital Private Limited directly via email or phone:</p>
                    <div class="direct-contact-items">
                        <a href="mailto:<?php echo skyca_e($settings['business_email']); ?>" class="direct-contact-row">
                            <svg class="icon-sm text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            <div>
                                <span class="dc-label">Business Email</span>
                                <strong class="dc-val"><?php echo skyca_e($settings['business_email']); ?></strong>
                            </div>
                        </a>
                        <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="direct-contact-row">
                            <svg class="icon-sm text-brand" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            <div>
                                <span class="dc-label">Business Phone</span>
                                <strong class="dc-val"><?php echo skyca_e($settings['business_phone']); ?></strong>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="enquiry-form-column">
                <?php
                skyca_render_enquiry_form([
                    'form_id'    => 'home-enquiry-form',
                    'heading'    => 'Request a Consultation or Quotation',
                    'subheading' => 'Fill out the form below and we will get back to you promptly with a reference number and next steps.',
                ]);
                ?>
            </div>
        </div>
    </div>
</section>

<!-- 9. FAQ SECTION -->
<section class="section bg-white" id="home-faq">
    <div class="container container-narrow">
        <div class="section-header text-center">
            <span class="section-eyebrow">Common Questions</span>
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">
                Clear answers to common questions about our software development services, project workflows, ownership, and support.
            </p>
        </div>

        <div class="faq-accordion" role="region" aria-label="Frequently Asked Questions">
            <?php foreach ($homeFaqs as $idx => $faq): ?>
                <div class="faq-item">
                    <h3 class="faq-question-heading">
                        <button
                            type="button"
                            class="faq-trigger"
                            aria-expanded="<?php echo $idx === 0 ? 'true' : 'false'; ?>"
                            aria-controls="home-faq-panel-<?php echo $idx; ?>"
                            id="home-faq-btn-<?php echo $idx; ?>"
                        >
                            <span><?php echo skyca_e($faq['q']); ?></span>
                            <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </button>
                    </h3>
                    <div
                        id="home-faq-panel-<?php echo $idx; ?>"
                        role="region"
                        aria-labelledby="home-faq-btn-<?php echo $idx; ?>"
                        class="faq-panel <?php echo $idx === 0 ? '' : 'hidden'; ?>"
                    >
                        <p><?php echo skyca_e($faq['a']); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="section-bottom-cta text-center">
            <p class="mb-4 text-muted">Have additional questions about your project or technical stack?</p>
            <a href="/faq" class="btn btn-secondary">View Complete FAQ Knowledge Base</a>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
