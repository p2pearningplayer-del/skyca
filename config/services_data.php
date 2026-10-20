<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — COMPLETE SERVICES CATALOG & LANDING PAGE DATA
 * Covers all 7 official corporate service categories, sub-services, deliverables,
 * workflows, FAQs, and internal linking metadata.
 * Strictly factual, capability-focused B2B copy with zero exaggerated guarantees.
 */

function skyca_get_all_services(): array
{
    return [
        'software-development' => [
            'id' => 1,
            'slug' => 'software-development',
            'title' => 'Information Technology Services & Software Development',
            'short_title' => 'IT Services & Custom Software',
            'nav_label' => 'Software Development & IT',
            'form_value' => 'Information Technology Services & Software Development',
            'meta_title' => 'Custom Software Development & IT Services | Skyca Digital Private Limited',
            'meta_description' => 'Custom software development, business process automation, application architecture, system integration, and IT consulting by Skyca Digital Private Limited.',
            'hero_badge' => 'Core Engineering & Enterprise IT',
            'hero_headline' => 'Custom Software Engineering Built Around Your Business Operations',
            'hero_subheadline' => 'We design, build, and integrate maintainable software systems that eliminate manual bottlenecks, connect fragmented tools, and support long-term operational growth.',
            'summary' => 'Tailored software engineering, workflow automation, and system integration designed for organizations whose processes have outgrown spreadsheets and rigid off-the-shelf software.',
            'who_needs_it' => 'Ideal for growing SMBs, startups building proprietary platforms, operations-heavy enterprises replacing legacy tools, and organizations needing seamless integration between ERP, CRM, billing, and internal portals.',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline><line x1="14" y1="4" x2="10" y2="20"></line></svg>',
            'sub_services' => [
                [
                    'name' => 'Custom software development',
                    'description' => 'End-to-end engineering of bespoke web, desktop, and backend software applications tailored specifically to your organization’s operational rules, user roles, and compliance requirements.'
                ],
                [
                    'name' => 'Business process automation',
                    'description' => 'Replacing repetitive manual data entry, multi-step approvals, document generation, and cross-departmental handoffs with reliable, auditable automated workflows.'
                ],
                [
                    'name' => 'Application architecture and integration',
                    'description' => 'Designing modular, maintainable system architectures and connecting disparate third-party platforms, databases, accounting tools, and internal APIs into a unified operational ecosystem.'
                ],
                [
                    'name' => 'Technology consulting',
                    'description' => 'Objective assessment of your current technology stack, build-versus-buy analysis, technical feasibility reviews, and roadmapping before committing development budgets.'
                ],
            ],
            'business_problems' => [
                [
                    'title' => 'Fragmented Internal Systems & Data Silos',
                    'description' => 'Teams waste hours copying records between disconnected spreadsheets, accounting packages, and CRM tools, leading to version conflicts and operational delays.'
                ],
                [
                    'title' => 'Rigid Off-the-Shelf Software Limitations',
                    'description' => 'Generic SaaS subscriptions force your business to change its proven workflows or charge prohibitive per-seat fees for features your team rarely uses.'
                ],
                [
                    'title' => 'Legacy Codebases Slowing Down Delivery',
                    'description' => 'Outdated applications with poor documentation and brittle dependencies make adding new business capabilities risky and slow.'
                ],
                [
                    'title' => 'Lack of Role-Based Governance & Audit Trails',
                    'description' => 'Growing organizations require granular staff permissions, approval workflows, and activity logs that informal tools cannot enforce.'
                ],
            ],
            'solution_overview' => 'Skyca Digital approaches software development as an operational investment rather than just writing code. We begin by mapping your real-world workflows, data structures, and user permissions. Using modern, well-documented technology stacks and clean architectural patterns, we deliver custom software that fits how your team works today while remaining modular enough to evolve as your business expands.',
            'deliverables' => [
                'Detailed System Requirements Specification (SRS) & data schema documentation',
                'Clean, modular, version-controlled source code with clear architectural separation',
                'Role-based access control (RBAC), authentication, and activity audit logging',
                'RESTful API layer and webhooks for third-party software integration',
                'Automated database migration scripts and environment configuration templates',
                'Deployment documentation, administrator handover, and post-launch technical support',
            ],
            'workflow' => [
                ['step' => '01', 'title' => 'Process & Requirements Discovery', 'desc' => 'We interview key stakeholders, map existing workflows, identify bottlenecks, and define measurable technical requirements.'],
                ['step' => '02', 'title' => 'System Architecture & Prototyping', 'desc' => 'We design database schemas, API contracts, security models, and interactive wireframes for review before full engineering begins.'],
                ['step' => '03', 'title' => 'Iterative Development Sprints', 'desc' => 'Features are engineered in structured milestones with regular staging demonstrations so you can track tangible progress.'],
                ['step' => '04', 'title' => 'Quality Assurance & Integration Testing', 'desc' => 'Comprehensive functional testing, edge-case validation, security checks, and third-party API integration verification.'],
                ['step' => '05', 'title' => 'Production Deployment & Handover', 'desc' => 'Controlled production launch, team onboarding, technical documentation delivery, and ongoing maintenance options.'],
            ],
            'tech_stack' => ['TypeScript / Node.js', 'PHP 8.x', 'Python', 'React / Next.js', 'MySQL / PostgreSQL', 'REST APIs', 'Docker & Linux'],
            'faqs' => [
                [
                    'q' => 'How do we decide between custom software and an off-the-shelf tool?',
                    'a' => 'During our initial consultation, we evaluate whether existing commercial software can meet your needs cost-effectively. We recommend custom development when your core workflows are unique, when integration between multiple tools is critical, or when per-user licensing costs become unsustainable at scale.'
                ],
                [
                    'q' => 'Will our company own the source code once the project is completed?',
                    'a' => 'Yes. Upon completion and final settlement of the agreed project scope, full ownership of the custom source code, database schemas, and technical documentation developed specifically for your project is transferred to your organization as defined in our service agreement.'
                ],
                [
                    'q' => 'Can you integrate custom software with our existing accounting or CRM tools?',
                    'a' => 'Yes. As long as your existing tools provide documented APIs, webhooks, or structured import/export capabilities, we can build secure two-way or scheduled synchronization bridges.'
                ],
                [
                    'q' => 'How do you handle changes in requirements during development?',
                    'a' => 'We work in structured milestones. If new requirements arise mid-project, we document the change impact on timeline and budget transparently so you can decide whether to include it in the current phase or schedule it for a subsequent release.'
                ],
            ],
            'related_slugs' => ['web-mobile-development', 'saas-cloud-solutions', 'ai-it-consulting'],
        ],

        'web-mobile-development' => [
            'id' => 2,
            'slug' => 'web-mobile-development',
            'title' => 'Website & Mobile Application Development',
            'short_title' => 'Web & Mobile App Development',
            'nav_label' => 'Web & Mobile Development',
            'form_value' => 'Website & Mobile Application Development',
            'meta_title' => 'Website & Mobile App Development Services India | Skyca Digital',
            'meta_description' => 'Professional corporate websites, custom web applications, mobile apps, progressive web apps (PWAs), and website maintenance by Skyca Digital Private Limited.',
            'hero_badge' => 'Web Platforms & Mobile Experiences',
            'hero_headline' => 'High-Performance Websites and Mobile Applications Designed to Convert',
            'hero_subheadline' => 'From authoritative corporate websites and customer portals to responsive mobile applications and PWAs, we build fast, accessible digital experiences across every device.',
            'summary' => 'Custom corporate websites, interactive web portals, mobile applications, and progressive web apps engineered for speed, usability, search visibility, and lead conversion.',
            'who_needs_it' => 'Built for businesses establishing or upgrading their digital presence, service companies running paid acquisition campaigns, enterprises launching customer/partner portals, and startups bringing mobile-first products to market.',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>',
            'sub_services' => [
                [
                    'name' => 'Corporate and business websites',
                    'description' => 'Fast, SEO-structured, conversion-focused corporate websites that establish brand credibility, communicate your services clearly, and capture qualified business enquiries.'
                ],
                [
                    'name' => 'Custom web applications',
                    'description' => 'Interactive client portals, booking systems, vendor dashboards, and browser-based business applications with secure backend databases and role-based workflows.'
                ],
                [
                    'name' => 'E-commerce websites',
                    'description' => 'Mobile-responsive online storefronts engineered for smooth product discovery, secure checkout flows, and straightforward catalog administration.'
                ],
                [
                    'name' => 'Mobile application development',
                    'description' => 'Android, iOS, and cross-platform mobile applications designed with intuitive touch interfaces, reliable API synchronization, and clean device performance.'
                ],
                [
                    'name' => 'Progressive web applications',
                    'description' => 'Installable, fast-loading Progressive Web Apps (PWAs) that deliver an app-like mobile experience directly through modern web browsers without mandatory app-store friction.'
                ],
                [
                    'name' => 'Website maintenance and improvements',
                    'description' => 'Ongoing technical updates, performance tuning, Core Web Vitals optimization, broken-link fixes, UI refinements, and feature additions for existing websites.'
                ],
            ],
            'business_problems' => [
                [
                    'title' => 'Outdated Websites Hurting Brand Credibility',
                    'description' => 'Slow, generic, or poorly structured websites cause prospective B2B and B2C buyers to leave before submitting an enquiry.'
                ],
                [
                    'title' => 'Poor Mobile Usability & Slow Load Times',
                    'description' => 'With a majority of traffic arriving on mobile devices, layout shifts, unoptimized assets, and clunky forms directly reduce conversion rates.'
                ],
                [
                    'title' => 'Disconnected Website & Lead Capture Workflows',
                    'description' => 'Enquiry forms that fail silently, lack spam protection, or lose marketing attribution make it impossible to measure ROI from search and ad campaigns.'
                ],
                [
                    'title' => 'High Cost of Maintaining Separate Codebases',
                    'description' => 'Without a unified API and thoughtful frontend architecture, keeping web and mobile experiences consistent becomes expensive and error-prone.'
                ],
            ],
            'solution_overview' => 'We combine conversion-focused UI/UX design with disciplined frontend and backend engineering. Every website and mobile application we build is structured around real user journeys—making it easy for visitors to understand your value proposition, navigate complex information on any screen size, and take clear action.',
            'deliverables' => [
                'Responsive UI/UX design adapted for Android, iOS, tablet, laptop, and desktop viewports',
                'Clean semantic HTML5, modern CSS/Tailwind styling, and optimized interactive scripts',
                'Secure server-side form handling, database persistence, and lead management dashboard',
                'Technical SEO setup including metadata, canonical tags, XML sitemap, and structured data',
                'Cross-browser and multi-device responsiveness verification',
                'Production deployment assistance, SSL setup guidance, and maintenance support',
            ],
            'workflow' => [
                ['step' => '01', 'title' => 'Information Architecture & Content Planning', 'desc' => 'We structure your sitemap, navigation hierarchy, conversion paths, and page messaging around your target audience.'],
                ['step' => '02', 'title' => 'UI/UX Design & Responsive Layouts', 'desc' => 'We craft visual layouts grounded in your brand identity with clear typography, accessible contrast, and purposeful CTAs.'],
                ['step' => '03', 'title' => 'Frontend & Backend Implementation', 'desc' => 'We build the responsive interface, server-side routes, database tables, and administrative controls.'],
                ['step' => '04', 'title' => 'Performance, SEO & Security Verification', 'desc' => 'We optimize images, validate forms against XSS/SQLi, test mobile viewports, and configure search metadata.'],
                ['step' => '05', 'title' => 'Launch & Post-Go-Live Support', 'desc' => 'We deploy to your production hosting environment, verify live forms and SSL, and provide ongoing technical care.'],
            ],
            'tech_stack' => ['Next.js / React', 'TypeScript', 'Tailwind CSS', 'PHP 8.x', 'React Native / Flutter', 'MySQL', 'PWA Standards'],
            'faqs' => [
                [
                    'q' => 'Will our website work properly on all smartphones and tablets?',
                    'a' => 'Yes. Every website we develop uses a mobile-first responsive architecture and is tested across Android phones, iPhones, tablets, laptops, and desktop resolutions.'
                ],
                [
                    'q' => 'Can you redesign or improve an existing website without losing our current URLs?',
                    'a' => 'Yes. When upgrading an existing website, we audit your current URL structure and implement 301 redirects where necessary so existing bookmarks, links, and search equity are preserved.'
                ],
                [
                    'q' => 'Do you include an admin panel to manage enquiries?',
                    'a' => 'Yes. Our business websites include a secure backend database and authenticated admin dashboard where your team can view, filter, update, and export project enquiries.'
                ],
                [
                    'q' => 'Should we build a native mobile app or a Progressive Web App (PWA)?',
                    'a' => 'It depends on your hardware requirements and distribution model. If you need deep device hardware access or app store discovery, a native or cross-platform mobile app is suitable. If you want instant browser accessibility across all devices with lower maintenance overhead, a PWA or responsive web app is often more practical.'
                ],
            ],
            'related_slugs' => ['software-development', 'ecommerce-solutions', 'digital-marketing-cybersecurity'],
        ],

        'saas-cloud-solutions' => [
            'id' => 3,
            'slug' => 'saas-cloud-solutions',
            'title' => 'SaaS Solutions & Cloud Services',
            'short_title' => 'SaaS & Cloud Solutions',
            'nav_label' => 'SaaS & Cloud Services',
            'form_value' => 'SaaS Solutions & Cloud Services',
            'meta_title' => 'SaaS Product Development & Cloud Services | Skyca Digital',
            'meta_description' => 'SaaS application development, multi-tenant cloud architecture, API engineering, cloud deployment, and scalability support by Skyca Digital Private Limited.',
            'hero_badge' => 'Cloud Architecture & SaaS Engineering',
            'hero_headline' => 'Build, Launch, and Scale Resilient SaaS Products and Cloud Applications',
            'hero_subheadline' => 'We help software founders and enterprises architect multi-tenant SaaS platforms, design robust APIs, and deploy maintainable cloud infrastructure.',
            'summary' => 'End-to-end SaaS product development, cloud application architecture, API engineering, and deployment pipelines designed for reliability and sustainable growth.',
            'who_needs_it' => 'Designed for startup founders launching an MVP or full commercial SaaS product, B2B software vendors modernizing legacy on-premise tools into cloud subscriptions, and companies requiring high-reliability API ecosystems.',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"></path></svg>',
            'sub_services' => [
                [
                    'name' => 'SaaS product development',
                    'description' => 'Full-lifecycle development of subscription-based software platforms, including tenant isolation, organization workspaces, role management, and billing webhook integration.'
                ],
                [
                    'name' => 'Cloud application architecture',
                    'description' => 'Designing stateless application tiers, managed database topologies, caching layers, and background job queues suited for modern cloud environments.'
                ],
                [
                    'name' => 'Cloud deployment and integration',
                    'description' => 'Setting up repeatable deployment workflows, environment configuration management, SSL certificates, domain routing, and automated backup strategies.'
                ],
                [
                    'name' => 'API development and integration',
                    'description' => 'Building secure, versioned RESTful APIs with token authentication, rate limiting, request validation, and clear developer documentation.'
                ],
                [
                    'name' => 'Application scalability and maintenance',
                    'description' => 'Database query optimization, indexing, caching strategies, uptime monitoring, and ongoing engineering support as user traffic and data volumes grow.'
                ],
            ],
            'business_problems' => [
                [
                    'title' => 'Need to Validate a SaaS Product Quickly Without Technical Debt',
                    'description' => 'Founders often struggle to balance speed-to-market with an architecture that will not require a complete rewrite once paying customers onboard.'
                ],
                [
                    'title' => 'Multi-Tenant Security & Data Isolation Risks',
                    'description' => 'SaaS applications require strict logical or physical separation of customer data, workspace permissions, and subscription entitlements.'
                ],
                [
                    'title' => 'Fragile Manual Server Deployments',
                    'description' => 'Unstructured server configurations and manual file uploads lead to configuration drift, unexpected downtime, and difficult rollbacks.'
                ],
                [
                    'title' => 'Slow APIs & Database Bottlenecks Under Load',
                    'description' => 'Unindexed queries and synchronous blocking tasks degrade user experience as concurrent users and dataset sizes increase.'
                ],
            ],
            'solution_overview' => 'Skyca Digital builds cloud and SaaS systems using pragmatic, battle-tested architectures. Rather than over-engineering complex microservice meshes when a modular monolith or clean multi-tier architecture is more cost-effective, we right-size your cloud infrastructure for your current stage while ensuring clean horizontal and vertical scaling paths.',
            'deliverables' => [
                'Multi-tenant SaaS application codebase with authentication and workspace management',
                'Secure, documented REST API endpoints with rate limiting and input validation',
                'Optimized relational database schema with proper indexing and migration scripts',
                'Subscription & payment gateway webhook integration readiness',
                'Environment-separated configuration (.env templates) and deployment runbooks',
                'Performance profiling, query optimization, and ongoing maintenance options',
            ],
            'workflow' => [
                ['step' => '01', 'title' => 'Product Scope & Architecture Blueprint', 'desc' => 'We define core MVP or platform features, tenant isolation strategy, data models, and cloud hosting requirements.'],
                ['step' => '02', 'title' => 'Core Platform & API Engineering', 'desc' => 'We build the authentication engine, tenant workspaces, database layer, and core API endpoints.'],
                ['step' => '03', 'title' => 'Frontend Dashboard & Billing Workflows', 'desc' => 'We implement responsive user dashboards, admin panels, and third-party service integrations.'],
                ['step' => '04', 'title' => 'Load, Security & Tenant Isolation Testing', 'desc' => 'We verify cross-tenant access controls, input sanitization, API rate limits, and database query efficiency.'],
                ['step' => '05', 'title' => 'Cloud Provisioning & Continuous Support', 'desc' => 'We deploy to your chosen cloud or hosting provider, configure backups, and support post-launch iterations.'],
            ],
            'tech_stack' => ['Node.js / TypeScript', 'Next.js', 'PHP 8.x', 'MySQL / PostgreSQL', 'Redis Caching', 'REST / Webhooks', 'AWS / VPS / Cloud Hosting'],
            'faqs' => [
                [
                    'q' => 'Can you help us build a SaaS MVP first and expand it later?',
                    'a' => 'Yes. We frequently help founders scope a lean, high-value Minimum Viable Product (MVP) focused on core user workflows, built on a clean database and API foundation so additional modules can be added in phases.'
                ],
                [
                    'q' => 'Which cloud providers do you work with?',
                    'a' => 'We design applications that can run on standard Linux cloud instances, VPS environments, managed PaaS platforms, AWS, Google Cloud, DigitalOcean, or compatible web hosting depending on your budget and technical requirements.'
                ],
                [
                    'q' => 'How do you keep different customers’ data separated in a SaaS application?',
                    'a' => 'We enforce strict tenant-scoped queries, middleware authorization checks, and role-based access controls at the API and database layers so users can only access records belonging to their authorized organization.'
                ],
                [
                    'q' => 'Can you build APIs for our mobile app or partner integrations?',
                    'a' => 'Yes. We design clean, documented JSON REST APIs with secure token authentication, pagination, rate limiting, and structured error handling.'
                ],
            ],
            'related_slugs' => ['software-development', 'ai-it-consulting', 'data-analytics-support'],
        ],

        'ai-it-consulting' => [
            'id' => 4,
            'slug' => 'ai-it-consulting',
            'title' => 'Artificial Intelligence Solutions & IT Consulting',
            'short_title' => 'AI Solutions & IT Consulting',
            'nav_label' => 'AI & IT Consulting',
            'form_value' => 'Artificial Intelligence Solutions & IT Consulting',
            'meta_title' => 'AI Solutions, Workflow Automation & IT Consulting | Skyca Digital',
            'meta_description' => 'Practical AI-powered business applications, AI workflow automation, intelligent chatbot integration, and strategic IT consulting by Skyca Digital Private Limited.',
            'hero_badge' => 'Applied AI & Technical Strategy',
            'hero_headline' => 'Practical AI Integration and Objective IT Consulting for Real Business Workflows',
            'hero_subheadline' => 'Cut through the hype with practical AI workflow automation, intelligent assistant integrations, and strategic technology consulting focused on measurable operational utility.',
            'summary' => 'Applied AI integrations, document and workflow automation, intelligent support assistants, and strategic IT architecture consulting grounded in business practicality and data privacy.',
            'who_needs_it' => 'Suited for organizations looking to automate document processing, customer triage, or internal knowledge retrieval using AI APIs, as well as leadership teams seeking independent technical architecture guidance.',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a4 4 0 0 1 4 4v2a4 4 0 0 1-8 0V6a4 4 0 0 1 4-4Z"></path><path d="M16 14H8a4 4 0 0 0-4 4v2h16v-2a4 4 0 0 0-4-4Z"></path><circle cx="19" cy="6" r="2"></circle><circle cx="5" cy="6" r="2"></circle></svg>',
            'sub_services' => [
                [
                    'name' => 'AI-powered business applications',
                    'description' => 'Integrating large language model (LLM) APIs, semantic search, and structured extraction into web and internal software applications with human-in-the-loop controls.'
                ],
                [
                    'name' => 'AI workflow automation',
                    'description' => 'Automating text classification, enquiry routing, document summarization, data extraction, and draft generation within your existing operational pipelines.'
                ],
                [
                    'name' => 'Intelligent chatbot integration',
                    'description' => 'Deploying website and internal knowledge-base assistants grounded in your approved documentation, with clear escalation paths to human support staff.'
                ],
                [
                    'name' => 'AI implementation consulting',
                    'description' => 'Evaluating where AI adds genuine operational value versus where deterministic software rules are safer, faster, and more cost-effective.'
                ],
                [
                    'name' => 'Technical architecture and IT consulting',
                    'description' => 'Advising businesses on technology stack selection, system modernization, vendor evaluation, database design, and scalable engineering practices.'
                ],
            ],
            'business_problems' => [
                [
                    'title' => 'Unclear ROI & Risk in AI Adoption',
                    'description' => 'Many businesses want to leverage AI capabilities but need clarity on API costs, data privacy boundaries, and reliability before deploying to customers.'
                ],
                [
                    'title' => 'Repetitive Customer Queries & Triage Overhead',
                    'description' => 'Support and sales teams spend excessive time answering recurring policy or product questions that could be pre-qualified and routed automatically.'
                ],
                [
                    'title' => 'Unstructured Documents Slowing Operations',
                    'description' => 'Manually reading, categorizing, and extracting key fields from incoming emails, forms, and documents creates bottlenecks.'
                ],
                [
                    'title' => 'Complex Technology Decisions Without In-House Architects',
                    'description' => 'Non-technical founders and growing SMBs often need an experienced technical partner to review architectures and prevent costly mistakes.'
                ],
            ],
            'solution_overview' => 'We focus on practical, deterministic-first engineering enhanced by AI where appropriate. Instead of treating AI as a black box, we combine structured validation, guardrails, prompt engineering, and retrieval over your verified company knowledge base—ensuring your team stays in control of outputs and data security.',
            'deliverables' => [
                'AI feasibility & use-case assessment report with cost/benefit breakdown',
                'Custom AI API integrations with strict input/output schema validation and guardrails',
                'Knowledge-grounded assistant or chatbot with human handover workflows',
                'Automated document/text processing pipelines integrated with your database',
                'Technical architecture blueprints, system audit reports, and technology roadmaps',
                'Data privacy configuration ensuring sensitive data is handled appropriately',
            ],
            'workflow' => [
                ['step' => '01', 'title' => 'Use-Case & Data Readiness Audit', 'desc' => 'We evaluate your workflows and data sources to determine whether AI or rule-based automation is the right fit.'],
                ['step' => '02', 'title' => 'Architecture & Guardrail Design', 'desc' => 'We design API integrations, prompt templates, fallback logic, and privacy boundaries.'],
                ['step' => '03', 'title' => 'Prototype & Accuracy Evaluation', 'desc' => 'We build a working prototype and test it against real-world business scenarios and edge cases.'],
                ['step' => '04', 'title' => 'System Integration & Human-in-the-Loop UI', 'desc' => 'We embed the solution into your web platform, CRM, or internal dashboard with review controls.'],
                ['step' => '05', 'title' => 'Monitoring & Iterative Refinement', 'desc' => 'We monitor token usage, latency, and output quality to keep the system reliable in production.'],
            ],
            'tech_stack' => ['OpenAI / Gemini / Anthropic APIs', 'RAG Pipelines', 'Python / Node.js / PHP', 'Structured JSON Outputs', 'Webhook Automation', 'REST APIs'],
            'faqs' => [
                [
                    'q' => 'Do we need to train our own AI model from scratch?',
                    'a' => 'In most business cases, training a custom foundation model from scratch is unnecessary and overly expensive. Combining established AI APIs with Retrieval-Augmented Generation (RAG), structured prompts, and your own business database delivers faster, more maintainable results.'
                ],
                [
                    'q' => 'How do you prevent an AI chatbot from making up incorrect answers?',
                    'a' => 'We restrict the assistant to answer strictly from your provided knowledge base documents, implement confidence thresholds, avoid open-ended commitments, and route complex or sensitive questions directly to your human team.'
                ],
                [
                    'q' => 'Can we engage Skyca Digital purely for IT and architecture consulting?',
                    'a' => 'Yes. You can engage us for technical architecture reviews, database design, software specification writing, or technology roadmap consulting prior to starting development.'
                ],
                [
                    'q' => 'How is our company data protected when using AI features?',
                    'a' => 'We configure enterprise/API-tier endpoints that do not use customer API payloads to train public models, and we strip unnecessary personally identifiable information (PII) before processing.'
                ],
            ],
            'related_slugs' => ['software-development', 'saas-cloud-solutions', 'data-analytics-support'],
        ],

        'digital-marketing-cybersecurity' => [
            'id' => 5,
            'slug' => 'digital-marketing-cybersecurity',
            'title' => 'Digital Marketing & Cyber Security Services',
            'short_title' => 'Digital Marketing & Cyber Security',
            'nav_label' => 'Marketing & Cyber Security',
            'form_value' => 'Digital Marketing & Cyber Security Services',
            'meta_title' => 'Digital Marketing, SEO, Conversion Pages & Cyber Security | Skyca Digital',
            'meta_description' => 'Technical SEO, Google Ads campaign support, high-conversion landing pages, application security assessments, and website hardening by Skyca Digital Private Limited.',
            'hero_badge' => 'Digital Growth & Application Security',
            'hero_headline' => 'Attract Qualified Traffic and Protect Your Digital Assets',
            'hero_subheadline' => 'We pair transparent search optimization, conversion-engineered landing pages, and paid campaign readiness with practical website hardening and secure development practices.',
            'summary' => 'Technical SEO, conversion-focused landing pages, paid campaign engineering support, and practical application security hardening to grow and protect your online presence.',
            'who_needs_it' => 'Designed for businesses running or preparing for Google Ads and Meta Ads campaigns, companies seeking sustainable organic search structure, and organizations wanting to harden their websites and web applications against common security vulnerabilities.',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><path d="m9 12 2 2 4-4"></path></svg>',
            'sub_services' => [
                [
                    'name' => 'Search engine optimization',
                    'description' => 'Technical and on-page SEO implementation including clean URL architecture, semantic heading hierarchy, structured schema markup, XML sitemaps, Core Web Vitals tuning, and intent-aligned service content.'
                ],
                [
                    'name' => 'Google Ads and digital campaign support',
                    'description' => 'Landing page alignment, UTM attribution capture, consent-aware conversion tracking (GA4, Google Ads, Meta Pixel), and campaign landing flow optimization.'
                ],
                [
                    'name' => 'Conversion-focused landing pages',
                    'description' => 'Fast-loading, message-matched landing pages designed specifically for paid search and social campaigns to maximize qualified enquiry submissions.'
                ],
                [
                    'name' => 'Security assessments and hardening',
                    'description' => 'Reviewing web applications and server configurations against OWASP best practices to identify and remediate common vulnerabilities such as SQL injection, XSS, CSRF, and insecure headers.'
                ],
                [
                    'name' => 'Website security improvements',
                    'description' => 'Implementing HTTPS/TLS enforcement, Content Security Policy (CSP) headers, rate limiting, spam protection, secure authentication cookies, and file access restrictions.'
                ],
                [
                    'name' => 'Secure software development guidance',
                    'description' => 'Helping development teams establish parameterized database queries, secrets management outside source control, input validation, and least-privilege access controls.'
                ],
            ],
            'business_problems' => [
                [
                    'title' => 'Ad Spend Wasted on Slow, Low-Converting Pages',
                    'description' => 'Sending paid clicks from Google Ads or Meta Ads to a slow, cluttered homepage without clear service alignment results in high bounce rates and few enquiries.'
                ],
                [
                    'title' => 'Inaccurate or Missing Conversion Attribution',
                    'description' => 'Without server-verified conversion firing and UTM tracking stored with each lead, businesses cannot tell which campaigns generate real enquiries.'
                ],
                [
                    'title' => 'Technical SEO Barriers Blocking Search Visibility',
                    'description' => 'Missing meta tags, broken canonical links, slow mobile rendering, and poor page hierarchy prevent search engines from properly indexing your services.'
                ],
                [
                    'title' => 'Exposed Credentials, Spam & Web Vulnerabilities',
                    'description' => 'Unprotected forms, weak admin panels, and unpatched scripts expose business websites to automated spam, data leaks, and service disruption.'
                ],
            ],
            'solution_overview' => 'Digital growth and cybersecurity go hand in hand: your website must be discoverable and persuasive to prospective clients while remaining resilient against automated abuse and common web vulnerabilities. We never promise unrealistic overnight search rankings or absolute security guarantees; instead, we implement transparent, verifiable engineering standards for SEO, paid campaign attribution, and defensive web security.',
            'deliverables' => [
                'Technical SEO audit and on-page implementation (titles, meta descriptions, canonicals, JSON-LD schema)',
                'Dedicated, fast-loading campaign landing pages with preselected service enquiry forms',
                'UTM parameter capture and consent-aware GA4 / Google Ads / Meta Pixel event wiring',
                'Web application security checklist review (SQLi, XSS, CSRF, authentication, rate limiting)',
                'HTTP security headers (.htaccess / server configuration) and secret-exposure remediation',
                'Clear analytics and lead-source reporting inside the Lead Management Dashboard',
            ],
            'workflow' => [
                ['step' => '01', 'title' => 'Technical SEO, Conversion & Security Audit', 'desc' => 'We inspect your current website performance, search indexability, form conversion paths, and security posture.'],
                ['step' => '02', 'title' => 'Landing Page & On-Page SEO Engineering', 'desc' => 'We structure high-intent service pages, optimize load speed, and implement clean metadata and schema markup.'],
                ['step' => '03', 'title' => 'Application Hardening & Form Protection', 'desc' => 'We configure HTTPS redirects, security headers, CSRF defenses, rate limiting, and parameterized backend validation.'],
                ['step' => '04', 'title' => 'Attribution & Conversion Tracking Setup', 'desc' => 'We wire UTM persistence, Thank You page verification, and consent-aware analytics tags.'],
                ['step' => '05', 'title' => 'Performance Review & Ongoing Optimization', 'desc' => 'We monitor lead quality, page speed, and security logs to support continuous improvement.'],
            ],
            'tech_stack' => ['Technical SEO & Schema.org', 'Google Analytics 4 (GA4)', 'Google Ads Tracking', 'OWASP Top 10 Defenses', 'HTTPS / TLS / CSP Headers', 'Core Web Vitals'],
            'faqs' => [
                [
                    'q' => 'Do you guarantee first-page Google rankings or a specific number of leads?',
                    'a' => 'No ethical agency can guarantee specific Google rankings or fixed ad returns, as search algorithms and auction dynamics depend on market competition and budget. We focus on what we can control and verify: strong technical SEO foundations, fast conversion-oriented landing pages, accurate tracking, and transparent campaign structure.'
                ],
                [
                    'q' => 'How do you track which Google Ad or campaign generated an enquiry?',
                    'a' => 'Our platform automatically captures the visitor’s landing page, HTTP referrer, and UTM parameters (utm_source, utm_medium, utm_campaign, utm_content, utm_term) upon arrival and stores them securely alongside the enquiry in the database.'
                ],
                [
                    'q' => 'Do you send customer emails or phone numbers to advertising trackers?',
                    'a' => 'No. To protect user privacy and comply with data minimization principles, conversion events fire only after the backend confirms the enquiry was saved, and we never transmit personal details such as phone numbers, emails, or project descriptions in URL parameters or standard ad event payloads.'
                ],
                [
                    'q' => 'What does your website security hardening cover?',
                    'a' => 'We implement defense-in-depth measures including prepared SQL statements, output escaping against XSS, CSRF tokens, strong password hashing, login rate limiting, environment variable secret isolation, and HTTP security headers. While no system can claim 100% immunity from all cyber threats, these practices significantly reduce attack surface.'
                ],
            ],
            'related_slugs' => ['web-mobile-development', 'ecommerce-solutions', 'data-analytics-support'],
        ],

        'data-analytics-support' => [
            'id' => 6,
            'slug' => 'data-analytics-support',
            'title' => 'Data Analytics & Technology Support Services',
            'short_title' => 'Data Analytics & Tech Support',
            'nav_label' => 'Data Analytics & Support',
            'form_value' => 'Data Analytics & Technology Support Services',
            'meta_title' => 'Business Data Analytics, Dashboards & IT Support | Skyca Digital',
            'meta_description' => 'Business data analytics, custom reporting dashboards, data visualization, technical troubleshooting, and software maintenance by Skyca Digital Private Limited.',
            'hero_badge' => 'Business Intelligence & Application Care',
            'hero_headline' => 'Turn Raw Operational Data Into Clear Decisions and Keep Systems Running Smoothly',
            'hero_subheadline' => 'We build custom reporting dashboards, KPI visualizations, and structured data pipelines—backed by responsive technical troubleshooting and software maintenance.',
            'summary' => 'Custom management dashboards, business data reporting, SQL/data visualization, and reliable ongoing software maintenance and technical troubleshooting.',
            'who_needs_it' => 'Built for management teams needing unified visibility into sales, leads, inventory, or financial KPIs, as well as businesses requiring dependable technical support and maintenance for existing web and software systems.',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>',
            'sub_services' => [
                [
                    'name' => 'Business data analytics',
                    'description' => 'Structuring, cleaning, and querying operational databases to uncover actionable trends in lead conversions, sales performance, customer retention, and resource utilization.'
                ],
                [
                    'name' => 'Reporting and dashboard development',
                    'description' => 'Building custom, role-based web dashboards and automated scheduled reports so leadership and operations teams can monitor key metrics in real time.'
                ],
                [
                    'name' => 'Data visualization',
                    'description' => 'Translating complex spreadsheets and multi-table SQL datasets into clear, interactive charts, funnel breakdowns, and exportable summaries.'
                ],
                [
                    'name' => 'Technical troubleshooting',
                    'description' => 'Diagnosing and resolving application bugs, database bottlenecks, broken API integrations, server configuration issues, and deployment errors.'
                ],
                [
                    'name' => 'Software maintenance and technical support',
                    'description' => 'Preventive maintenance, runtime upgrades, dependency patching, backup verification, and ongoing engineering support to keep business applications healthy.'
                ],
            ],
            'business_problems' => [
                [
                    'title' => 'Hours Spent Building Manual Weekly Reports',
                    'description' => 'Managers and analysts repeatedly export CSVs from multiple systems every week just to assemble basic operational numbers.'
                ],
                [
                    'title' => 'Lack of Real-Time Visibility Into Lead & Sales Funnels',
                    'description' => 'Without a centralized dashboard, decision-makers cannot quickly filter performance by date range, service category, or acquisition channel.'
                ],
                [
                    'title' => 'Unresolved Bugs & Degrading Application Performance',
                    'description' => 'Existing software applications left without regular maintenance accumulate database bloat, broken features, and compatibility issues after server upgrades.'
                ],
                [
                    'title' => 'No Dedicated Technical Partner After Initial Launch',
                    'description' => 'Many businesses have working applications built by previous freelancers or vendors who are no longer available when urgent issues occur.'
                ],
            ],
            'solution_overview' => 'Skyca Digital helps organizations get more value and reliability out of their technology investments. On the analytics side, we design clean database queries and intuitive dashboards that put accurate numbers in front of decision-makers. On the support side, we provide structured troubleshooting, code audits, and ongoing maintenance so your critical software stays dependable.',
            'deliverables' => [
                'Custom web-based KPI and reporting dashboards with date, status, and category filters',
                'Optimized SQL queries, database views, and automated CSV/report export tools',
                'Root-cause diagnostic reports and verified bug fixes for existing applications',
                'Database indexing, query tuning, and server error-log remediation',
                'Scheduled maintenance checklists, runtime updates, and backup configuration guidance',
                'Documented support workflows for ongoing feature enhancements',
            ],
            'workflow' => [
                ['step' => '01', 'title' => 'System & Data Source Assessment', 'desc' => 'We inspect your existing databases, application codebase, error logs, or reporting requirements.'],
                ['step' => '02', 'title' => 'KPI Definition or Issue Diagnosis', 'desc' => 'For analytics, we define exact metrics and filters; for support, we isolate root causes and reproduce issues safely.'],
                ['step' => '03', 'title' => 'Dashboard Engineering or Remediation', 'desc' => 'We build the reporting views, optimize database queries, or patch application defects in a staging environment.'],
                ['step' => '04', 'title' => 'Validation & Data Accuracy Checks', 'desc' => 'We reconcile dashboard totals against source records and run regression tests on software fixes.'],
                ['step' => '05', 'title' => 'Deployment & Preventive Maintenance', 'desc' => 'We deploy updates cleanly and establish ongoing monitoring and maintenance routines.'],
            ],
            'tech_stack' => ['SQL / MySQL / PostgreSQL', 'Custom Admin Dashboards', 'Python Data Pipelines', 'Charting & BI Views', 'PHP / Node.js Debugging', 'Linux Server Diagnostics'],
            'faqs' => [
                [
                    'q' => 'Can you take over maintenance for a website or application built by another developer?',
                    'a' => 'Yes. We start with a structured code and database audit to assess the current architecture, security posture, and deployment setup, after which we provide a clear remediation and maintenance plan.'
                ],
                [
                    'q' => 'Can we export data from your custom dashboards to Excel or CSV?',
                    'a' => 'Yes. We build authorized CSV and structured data export features with date and category filters so your team can easily use the data in external accounting or presentation tools.'
                ],
                [
                    'q' => 'Will analytics queries slow down our live production database?',
                    'a' => 'We design reporting queries with proper composite indexes, pagination, and caching strategies—or configure read-replica/summary tables for large datasets—so live user transactions remain fast.'
                ],
                [
                    'q' => 'How do we request urgent technical troubleshooting?',
                    'a' => 'You can submit a priority enquiry through our contact page or call/email our team directly with details of the issue, hosting environment, and error symptoms.'
                ],
            ],
            'related_slugs' => ['software-development', 'saas-cloud-solutions', 'digital-marketing-cybersecurity'],
        ],

        'ecommerce-solutions' => [
            'id' => 7,
            'slug' => 'ecommerce-solutions',
            'title' => 'E-Commerce Solutions and Related Technology Activities',
            'short_title' => 'E-Commerce Solutions',
            'nav_label' => 'E-Commerce Solutions',
            'form_value' => 'E-Commerce Solutions and Related Technology Activities',
            'meta_title' => 'E-Commerce Website Development & Payment Integration | Skyca Digital',
            'meta_description' => 'Custom online store development, product catalog and inventory integration, payment gateway setup, order workflows, and e-commerce optimization by Skyca Digital.',
            'hero_badge' => 'Digital Commerce & Order Workflows',
            'hero_headline' => 'Scalable E-Commerce Platforms and Streamlined Order Operations',
            'hero_subheadline' => 'Build a fast, trustworthy online store with structured product catalogs, secure payment gateway integration, and automated order management workflows.',
            'summary' => 'Custom B2C and B2B e-commerce store development, catalog and inventory integration, secure payment gateway onboarding, and checkout performance optimization.',
            'who_needs_it' => 'Designed for retail brands launching direct-to-consumer (D2C) online stores, wholesalers and distributors building B2B ordering portals, and existing merchants needing faster checkout performance or custom inventory integrations.',
            'icon_svg' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>',
            'sub_services' => [
                [
                    'name' => 'Online store development',
                    'description' => 'Designing and engineering responsive B2C and B2B e-commerce storefronts with intuitive category navigation, search filters, product variant management, and mobile-first checkout.'
                ],
                [
                    'name' => 'Product catalog and inventory integration',
                    'description' => 'Structuring complex product attributes, SKU variations, bulk pricing rules, and real-time stock synchronization with warehouse or accounting systems.'
                ],
                [
                    'name' => 'Payment gateway integration',
                    'description' => 'Integrating reputable Indian and international payment gateways using official SDKs and server-side webhook signature verification—without storing sensitive card details on your server.'
                ],
                [
                    'name' => 'Order management workflows',
                    'description' => 'Automating order confirmation notifications, invoice generation, fulfillment status tracking, return/cancellation handling, and admin dispatch dashboards.'
                ],
                [
                    'name' => 'E-commerce performance optimization',
                    'description' => 'Improving product page load times, image optimization, database indexing for catalog search, and reducing checkout friction to lower cart abandonment.'
                ],
            ],
            'business_problems' => [
                [
                    'title' => 'High Cart Abandonment on Mobile Devices',
                    'description' => 'Slow product pages, confusing multi-step forms, and unoptimized mobile checkouts cause shoppers to abandon purchases before payment.'
                ],
                [
                    'title' => 'Inventory Mismatches & Overselling',
                    'description' => 'Disconnected stock records between offline operations and the online store lead to cancelled orders and poor customer experience.'
                ],
                [
                    'title' => 'Unreliable Payment Webhook & Order Status Handling',
                    'description' => 'Improperly integrated payment callbacks can result in successful payments not being recorded as confirmed orders when network interruptions occur.'
                ],
                [
                    'title' => 'B2B Ordering Complexity',
                    'description' => 'Wholesale and trade buyers often need tiered pricing, GST invoice details, bulk order forms, or quotation requests that standard retail templates lack.'
                ],
            ],
            'solution_overview' => 'Skyca Digital engineers e-commerce systems that balance storefront speed with reliable backend operations. We ensure payment processing is handled strictly through PCI-compliant gateway providers with cryptographic webhook verification, while giving your operations team a clear administrative interface to manage products, inventory, and order fulfillment.',
            'deliverables' => [
                'Responsive e-commerce storefront with category filtering, search, and product detail pages',
                'Shopping cart and streamlined mobile-friendly checkout workflow',
                'Secure payment gateway integration with server-side webhook verification',
                'Admin dashboard for product catalog, SKU inventory, customer orders, and status updates',
                'Product schema markup (JSON-LD), SEO-friendly URLs, and image optimization',
                'Order notification emails and invoice/receipt generation templates',
            ],
            'workflow' => [
                ['step' => '01', 'title' => 'Catalog, Pricing & Fulfillment Mapping', 'desc' => 'We document your SKU structure, tax/shipping rules, payment providers, and order fulfillment steps.'],
                ['step' => '02', 'title' => 'Storefront UX & Checkout Architecture', 'desc' => 'We design clean product discovery layouts and a low-friction mobile and desktop checkout flow.'],
                ['step' => '03', 'title' => 'Catalog, Cart & Payment Gateway Engineering', 'desc' => 'We build the catalog database, cart state, payment gateway callbacks, and admin order management panel.'],
                ['step' => '04', 'title' => 'Sandbox Payment & Edge-Case Testing', 'desc' => 'We test successful payments, failed transactions, webhook retries, stock deductions, and mobile responsiveness.'],
                ['step' => '05', 'title' => 'Production Go-Live & Store Handover', 'desc' => 'We switch payment gateways to live mode, verify SSL security, and train your team on catalog and order management.'],
            ],
            'tech_stack' => ['Custom Web E-Commerce', 'Next.js / PHP 8.x', 'MySQL Transaction Safety', 'Razorpay / Stripe / PayU Webhooks', 'Inventory REST APIs', 'Product Schema SEO'],
            'faqs' => [
                [
                    'q' => 'Does our website store customer credit card or UPI credentials?',
                    'a' => 'No. We never store raw payment card numbers, CVVs, or banking credentials on your application database. All payment credentials are processed directly by authorized, PCI-DSS compliant payment gateways, and our backend verifies cryptographic transaction signatures.'
                ],
                [
                    'q' => 'Can you build B2B e-commerce portals with quotation requests or bulk pricing?',
                    'a' => 'Yes. We can configure B2B workflows including minimum order quantities (MOQ), tiered volume pricing, GSTIN capture on checkout, or a hybrid cart that allows buyers to request a formal quotation.'
                ],
                [
                    'q' => 'Can our team add and edit products ourselves after launch?',
                    'a' => 'Yes. Every e-commerce solution includes an administrative interface where authorized staff can manage categories, products, images, pricing, stock availability, and order statuses.'
                ],
                [
                    'q' => 'Can you optimize an existing slow online store?',
                    'a' => 'Yes. We can audit your current store to optimize image delivery, database queries, caching headers, third-party scripts, and checkout steps.'
                ],
            ],
            'related_slugs' => ['web-mobile-development', 'digital-marketing-cybersecurity', 'software-development'],
        ],
    ];
}

/**
 * Retrieve a single service by its URL slug, or null if not found.
 */
function skyca_get_service_by_slug(string $slug): ?array
{
    $services = skyca_get_all_services();
    return $services[$slug] ?? null;
}

/**
 * Return flat list of valid service names for form validation and dropdowns.
 */
function skyca_get_service_options(): array
{
    $options = [];
    foreach (skyca_get_all_services() as $service) {
        $options[] = $service['form_value'];
    }
    $options[] = 'Multiple Services / Full Digital Transformation';
    $options[] = 'General IT Consultation / Other';
    return $options;
}
