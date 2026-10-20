<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — GLOBAL FOOTER, STICKY MOBILE CTA & CONSENT MANAGER
 */

$settings    = skyca_get_settings();
$allServices = skyca_get_all_services();
$year        = date('Y');
?>
    </main>

    <!-- Corporate Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-top-cta">
                <div class="footer-top-cta-text">
                    <span class="footer-eyebrow">Ready to Start Your Project?</span>
                    <h2 class="footer-cta-heading">Build Reliable Software &amp; Digital Systems with Skyca Digital</h2>
                    <p class="footer-cta-sub">Share your requirements for a transparent technical consultation, architecture review, and project estimate.</p>
                </div>
                <div class="footer-top-cta-actions">
                    <a href="/contact#enquiry-form" class="btn btn-primary btn-lg">Discuss Your Project</a>
                    <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="btn btn-outline-light btn-lg">
                        <svg class="icon-sm" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        <span><?php echo skyca_e($settings['business_phone']); ?></span>
                    </a>
                </div>
            </div>

            <div class="footer-grid">
                <!-- Col 1: Brand Identity & Verified Contact -->
                <div class="footer-col footer-brand-col">
                    <a href="/" class="footer-logo-badge" aria-label="<?php echo skyca_e(COMPANY_LEGAL_NAME); ?> Home">
                        <picture>
                            <source srcset="/assets/images/skyca-logo-horizontal.webp" type="image/webp">
                            <img
                                src="/assets/images/skyca-logo-horizontal.png"
                                alt="<?php echo skyca_e(COMPANY_LEGAL_NAME); ?> Official Logo"
                                width="260"
                                height="70"
                                loading="lazy"
                            >
                        </picture>
                    </a>
                    <p class="footer-brand-desc">
                        <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong> is an Information Technology and Software Development company delivering custom software, web and mobile applications, cloud and SaaS engineering, practical AI integration, cybersecurity hardening, data analytics, and e-commerce solutions.
                    </p>

                    <ul class="footer-contact-list">
                        <li>
                            <span class="footer-contact-label">Email:</span>
                            <a href="mailto:<?php echo skyca_e($settings['business_email']); ?>" class="footer-contact-link"><?php echo skyca_e($settings['business_email']); ?></a>
                        </li>
                        <li>
                            <span class="footer-contact-label">Phone:</span>
                            <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="footer-contact-link"><?php echo skyca_e($settings['business_phone']); ?></a>
                        </li>
                        <li>
                            <span class="footer-contact-label">Website:</span>
                            <a href="<?php echo skyca_e(SITE_URL); ?>" class="footer-contact-link"><?php echo skyca_e(SITE_URL); ?></a>
                        </li>
                        <?php if (!empty($settings['company_address'])): ?>
                            <li>
                                <span class="footer-contact-label">Address:</span>
                                <span class="footer-contact-text">
                                    <?php echo skyca_e($settings['company_address']); ?>
                                    <?php if (!empty($settings['company_city'])): ?>, <?php echo skyca_e($settings['company_city']); ?><?php endif; ?>
                                    <?php if (!empty($settings['company_state'])): ?>, <?php echo skyca_e($settings['company_state']); ?><?php endif; ?>
                                    <?php if (!empty($settings['company_postal_code'])): ?> - <?php echo skyca_e($settings['company_postal_code']); ?><?php endif; ?>
                                </span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_cin'])): ?>
                            <li>
                                <span class="footer-contact-label">CIN:</span>
                                <span class="footer-contact-text"><?php echo skyca_e($settings['company_cin']); ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($settings['company_gstin'])): ?>
                            <li>
                                <span class="footer-contact-label">GSTIN:</span>
                                <span class="footer-contact-text"><?php echo skyca_e($settings['company_gstin']); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <?php
                    $verifiedSocials = [
                        'LinkedIn'  => $settings['social_linkedin_url'] ?? '',
                        'GitHub'    => $settings['social_github_url'] ?? '',
                        'X'         => $settings['social_x_url'] ?? '',
                        'Facebook'  => $settings['social_facebook_url'] ?? '',
                        'Instagram' => $settings['social_instagram_url'] ?? '',
                    ];
                    $activeSocials = array_filter($verifiedSocials, static fn($u) => !empty($u) && filter_var($u, FILTER_VALIDATE_URL));
                    ?>
                    <?php if (!empty($activeSocials)): ?>
                        <div class="footer-socials" aria-label="Verified Social Profiles">
                            <?php foreach ($activeSocials as $platform => $sUrl): ?>
                                <a href="<?php echo skyca_e($sUrl); ?>" target="_blank" rel="noopener noreferrer" class="footer-social-pill">
                                    <?php echo skyca_e($platform); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Col 2: IT & Software Services -->
                <div class="footer-col">
                    <h3 class="footer-heading">Our IT Services</h3>
                    <ul class="footer-links">
                        <li><a href="/services">All Services Overview</a></li>
                        <?php foreach ($allServices as $srv): ?>
                            <li>
                                <a href="/services/<?php echo skyca_e($srv['slug']); ?>">
                                    <?php echo skyca_e($srv['title']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Col 3: Company & Capabilities -->
                <div class="footer-col">
                    <h3 class="footer-heading">Company &amp; Process</h3>
                    <ul class="footer-links">
                        <li><a href="/">Home</a></li>
                        <li><a href="/about">About Skyca Digital</a></li>
                        <li><a href="/#why-skyca">Why Choose Skyca Digital</a></li>
                        <li><a href="/#process">Development &amp; Delivery Process</a></li>
                        <li><a href="/#industries">Industries &amp; Use Cases</a></li>
                        <li><a href="/#capabilities">Technology Expertise</a></li>
                        <li><a href="/faq">Frequently Asked Questions</a></li>
                        <li><a href="/contact">Contact &amp; Project Enquiry</a></li>
                    </ul>
                </div>

                <!-- Col 4: Legal, Governance & Support -->
                <div class="footer-col">
                    <h3 class="footer-heading">Legal &amp; Governance</h3>
                    <ul class="footer-links">
                        <li><a href="/privacy-policy">Privacy Policy</a></li>
                        <li><a href="/terms-and-conditions">Terms and Conditions</a></li>
                        <li><a href="/cookie-policy">Cookie Policy</a></li>
                        <li><button type="button" class="footer-link-btn" id="open-cookie-settings-btn">Cookie Preferences</button></li>
                        <li><a href="/sitemap.xml">XML Sitemap</a></li>
                    </ul>

                    <div class="footer-consultation-box">
                        <p class="footer-box-title">Direct Consultation</p>
                        <p class="footer-box-text">Speak with our technical team or submit your project scope online.</p>
                        <a href="/contact#enquiry-form" class="footer-box-link">Request a Quotation &rarr;</a>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p class="footer-copyright">
                    &copy; <?php echo skyca_e($year); ?> <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong>. All rights reserved.
                </p>
                <div class="footer-bottom-links">
                    <a href="/privacy-policy">Privacy</a>
                    <span aria-hidden="true">&bull;</span>
                    <a href="/terms-and-conditions">Terms</a>
                    <span aria-hidden="true">&bull;</span>
                    <a href="/cookie-policy">Cookies</a>
                    <span aria-hidden="true">&bull;</span>
                    <a href="/contact">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Sticky Mobile Contact Bar (Visible on Mobile Viewports Only, Non-Obstructing) -->
    <div class="mobile-sticky-bar" role="region" aria-label="Quick Contact Actions">
        <a href="tel:<?php echo skyca_e($settings['business_phone_raw']); ?>" class="mobile-sticky-btn mobile-sticky-call">
            <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            <span>Call Us</span>
        </a>
        <a href="mailto:<?php echo skyca_e($settings['business_email']); ?>" class="mobile-sticky-btn mobile-sticky-email">
            <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            <span>Email</span>
        </a>
        <?php if (!empty($settings['whatsapp_number'])): ?>
            <?php $waDigits = preg_replace('/\D+/', '', (string) $settings['whatsapp_number']); ?>
            <?php if ($waDigits !== ''): ?>
                <a href="https://wa.me/<?php echo skyca_e($waDigits); ?>" target="_blank" rel="noopener noreferrer" class="mobile-sticky-btn mobile-sticky-whatsapp">
                    <span>WhatsApp</span>
                </a>
            <?php endif; ?>
        <?php endif; ?>
        <a href="/contact#enquiry-form" class="mobile-sticky-btn mobile-sticky-primary">
            <span>Discuss Project</span>
        </a>
    </div>

    <!-- Consent-Aware Cookie & Tracking Banner -->
    <div class="cookie-banner hidden" id="cookie-consent-banner" role="dialog" aria-labelledby="cookie-banner-title" aria-describedby="cookie-banner-desc">
        <div class="cookie-banner-inner">
            <div class="cookie-banner-content">
                <h3 id="cookie-banner-title" class="cookie-banner-title">Privacy &amp; Cookie Preferences</h3>
                <p id="cookie-banner-desc" class="cookie-banner-text">
                    We use essential cookies for security and form attribution, and optional analytics/marketing cookies (such as Google Analytics 4 and Google Ads conversion measurement) only with your consent. Read our <a href="/cookie-policy">Cookie Policy</a> and <a href="/privacy-policy">Privacy Policy</a>.
                </p>
                <div class="cookie-preferences-options hidden" id="cookie-preferences-panel">
                    <label class="cookie-toggle-label">
                        <input type="checkbox" checked disabled>
                        <span><strong>Strictly Necessary</strong> (Security, CSRF protection, session state — always active)</span>
                    </label>
                    <label class="cookie-toggle-label">
                        <input type="checkbox" id="consent-analytics-checkbox" checked>
                        <span><strong>Analytics &amp; Performance</strong> (Anonymous traffic measurement via GA4)</span>
                    </label>
                    <label class="cookie-toggle-label">
                        <input type="checkbox" id="consent-marketing-checkbox" checked>
                        <span><strong>Campaign Attribution &amp; Advertising</strong> (Google Ads &amp; Meta campaign conversion measurement)</span>
                    </label>
                </div>
            </div>
            <div class="cookie-banner-actions">
                <button type="button" class="btn btn-outline btn-sm" id="cookie-reject-btn">Essential Only</button>
                <button type="button" class="btn btn-outline btn-sm" id="cookie-customize-btn">Customize</button>
                <button type="button" class="btn btn-primary btn-sm" id="cookie-accept-btn">Accept All</button>
            </div>
        </div>
    </div>

    <script src="<?php echo skyca_e(skyca_asset('assets/js/main.js')); ?>" defer></script>
</body>
</html>
