<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — DEDICATED FAQ PAGE (pages/faq.php)
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';

$settings = skyca_get_settings();

$faqCategories = [
    'General & Project Engagement' => [
        [
            'q' => 'What services does Skyca Digital Private Limited provide?',
            'a' => 'Skyca Digital Private Limited provides seven core categories of IT and software services: (1) Information Technology Services & Software Development, (2) Website & Mobile Application Development, (3) SaaS Solutions & Cloud Services, (4) Artificial Intelligence Solutions & IT Consulting, (5) Digital Marketing & Cyber Security Services, (6) Data Analytics & Technology Support Services, and (7) E-Commerce Solutions and Related Technology Activities.'
        ],
        [
            'q' => 'How do we request a consultation or project quotation?',
            'a' => 'You can submit your project requirements using the enquiry form on our Contact page or any service page, email us at info@skycadigital.com, or call +91 6002064873. Once submitted, you will receive an enquiry reference ID and our team will review your requirements.'
        ],
        [
            'q' => 'Do you work with startups and small businesses as well as established companies?',
            'a' => 'Yes. We tailor our architecture and project milestones to fit early-stage startups launching an MVP, small and medium businesses digitizing operations, and larger organizations integrating complex software systems.'
        ],
        [
            'q' => 'How are project timelines and budgets estimated?',
            'a' => 'After reviewing your functional requirements, required integrations, and hosting environment, we break the project into clear engineering milestones and provide a transparent written estimate before work begins.'
        ],
    ],
    'Software, Web, Mobile & E-Commerce Development' => [
        [
            'q' => 'Will we own the source code of our custom software or website?',
            'a' => 'Yes. Upon completion of the project and settlement of agreed milestones, ownership of the custom source code and database schema developed specifically for your organization is handed over as outlined in your project agreement.'
        ],
        [
            'q' => 'Are your websites and applications mobile-responsive?',
            'a' => 'Yes. Every website, web portal, and e-commerce storefront we build is engineered to be responsive across Android smartphones, iPhones, tablets, laptops, and desktop displays.'
        ],
        [
            'q' => 'Can you integrate payment gateways into our e-commerce or SaaS platform?',
            'a' => 'Yes. We integrate authorized payment gateways using their official server-side SDKs and cryptographic webhook verification. We never store raw payment card numbers or sensitive banking credentials in your application database.'
        ],
        [
            'q' => 'Can you upgrade or fix an existing application built by another team?',
            'a' => 'Yes. Under our Data Analytics & Technology Support Services and Website Maintenance offerings, we can audit your existing codebase and database, diagnose technical issues, and implement structured improvements.'
        ],
    ],
    'Security, SEO & Paid Campaign Readiness' => [
        [
            'q' => 'How do you protect websites and applications against common cyber threats?',
            'a' => 'We implement defense-in-depth measures including parameterized SQL queries (preventing SQL injection), output escaping (preventing XSS), CSRF token validation, strong password hashing, rate limiting, and secure HTTP headers. While no system can guarantee absolute immunity against every conceivable threat, these standards significantly harden your application.'
        ],
        [
            'q' => 'Do you promise guaranteed #1 Google rankings or fixed advertising ROI?',
            'a' => 'No. Search engine rankings and paid ad performance depend on market competition, search algorithms, and ad budgets. We focus on delivering verifiable technical SEO foundations, fast conversion-oriented landing pages, and accurate attribution tracking.'
        ],
        [
            'q' => 'How is our privacy protected when we submit an enquiry?',
            'a' => 'Your enquiry details are transmitted over HTTPS, validated on our server, and stored securely in our database solely for responding to your business request. We do not expose personal data in URLs or sell lead data to third parties.'
        ],
    ],
];

$flatFaqs = [];
foreach ($faqCategories as $catFaqs) {
    foreach ($catFaqs as $item) {
        $flatFaqs[] = $item;
    }
}

$breadcrumbs = [
    ['name' => 'Home', 'path' => '/'],
    ['name' => 'Frequently Asked Questions', 'path' => '/faq'],
];

$pageMeta = [
    'title'       => 'Frequently Asked Questions (FAQ) | Skyca Digital Private Limited',
    'description' => 'Answers to common questions about Skyca Digital Private Limited’s software development process, web & mobile apps, cloud & AI services, security practices, and project consultations.',
    'path'        => '/faq',
    'breadcrumbs' => $breadcrumbs,
    'faqs'        => $flatFaqs,
];

require dirname(__DIR__) . '/includes/header.php';
?>

<section class="page-hero bg-navy text-light hero-3d-stage">
    <div class="hero-bg-grid" aria-hidden="true"></div>
    <div class="hero-3d-orb hero-3d-orb-1" aria-hidden="true"></div>
    <div class="hero-3d-orb hero-3d-orb-2" aria-hidden="true"></div>
    <div class="container">
        <?php skyca_render_breadcrumbs($breadcrumbs); ?>
        <div class="inner-hero-split">
            <div class="page-hero-content">
                <span class="section-eyebrow eyebrow-light">Knowledge Base &amp; Technical Q&amp;A</span>
                <h1 class="page-hero-title">Frequently Asked Questions</h1>
                <p class="page-hero-subtitle">
                    Everything you need to know about engaging Skyca Digital Private Limited for software engineering, web &amp; mobile development, cloud architecture, AI, cybersecurity, and digital growth.
                </p>
            </div>

            <div class="inner-hero-visual scene-3d" aria-hidden="true">
                <div class="glass-hero-vector-card card-3d-surface">
                    <div class="ghv-header">
                        <span class="ghv-dot dot-teal"></span>
                        <span class="ghv-dot dot-blue"></span>
                        <span class="ghv-dot dot-gold"></span>
                        <span class="ghv-title">SKYCA_KNOWLEDGE_BASE // VERIFIED</span>
                    </div>
                    <svg viewBox="0 0 420 175" class="ghv-svg" role="img" aria-label="Knowledge Base Architecture">
                        <defs>
                            <linearGradient id="faqCardGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#058df0" stop-opacity="0.28"/>
                                <stop offset="100%" stop-color="#14b8a6" stop-opacity="0.16"/>
                            </linearGradient>
                        </defs>
                        <rect x="18" y="18" width="384" height="42" rx="10" fill="url(#faqCardGrad)" stroke="#38bdf8" stroke-width="1.3"/>
                        <text x="34" y="43" fill="#ffffff" font-family="Inter, sans-serif" font-size="11" font-weight="800">01 // ARCHITECTURE, SOURCE CODE OWNERSHIP &amp; IP</text>
                        <circle cx="380" cy="39" r="6" fill="#2dd4bf"/>

                        <rect x="18" y="70" width="384" height="42" rx="10" fill="rgba(15,23,42,0.85)" stroke="#2dd4bf" stroke-width="1.3"/>
                        <text x="34" y="95" fill="#99f6e4" font-family="Inter, sans-serif" font-size="11" font-weight="800">02 // OWASP SECURITY, TLS 1.3 &amp; DATA PRIVACY</text>
                        <circle cx="380" cy="91" r="6" fill="#38bdf8"/>

                        <rect x="18" y="122" width="384" height="42" rx="10" fill="rgba(15,23,42,0.85)" stroke="#f59e0b" stroke-width="1.3"/>
                        <text x="34" y="147" fill="#fde68a" font-family="Inter, sans-serif" font-size="11" font-weight="800">03 // TRANSPARENT MILESTONES &amp; SLA SUPPORT</text>
                        <circle cx="380" cy="143" r="6" fill="#fbbf24"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section bg-white">
    <div class="container container-narrow">
        <?php $globalIdx = 0; ?>
        <?php foreach ($faqCategories as $categoryTitle => $items): ?>
            <div class="faq-category-block mb-8">
                <h2 class="faq-category-heading"><?php echo skyca_e($categoryTitle); ?></h2>
                <div class="faq-accordion">
                    <?php foreach ($items as $faq): ?>
                        <?php $globalIdx++; ?>
                        <div class="faq-item">
                            <h3 class="faq-question-heading">
                                <button
                                    type="button"
                                    class="faq-trigger"
                                    aria-expanded="<?php echo $globalIdx === 1 ? 'true' : 'false'; ?>"
                                    aria-controls="page-faq-panel-<?php echo $globalIdx; ?>"
                                    id="page-faq-btn-<?php echo $globalIdx; ?>"
                                >
                                    <span><?php echo skyca_e($faq['q']); ?></span>
                                    <svg class="faq-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                                </button>
                            </h3>
                            <div
                                id="page-faq-panel-<?php echo $globalIdx; ?>"
                                role="region"
                                aria-labelledby="page-faq-btn-<?php echo $globalIdx; ?>"
                                class="faq-panel <?php echo $globalIdx === 1 ? '' : 'hidden'; ?>"
                            >
                                <p><?php echo skyca_e($faq['a']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="faq-still-questions-card">
            <h2>Have a Specific Technical Question?</h2>
            <p>Our team is happy to clarify technical requirements, hosting compatibility, or project timelines directly.</p>
            <div class="hero-cta-group justify-center mt-4">
                <a href="/contact#enquiry-form" class="btn btn-primary">Ask Our Team / Request Quote</a>
                <a href="mailto:<?php echo skyca_e($settings['business_email']); ?>" class="btn btn-secondary">Email <?php echo skyca_e($settings['business_email']); ?></a>
            </div>
        </div>
    </div>
</section>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
