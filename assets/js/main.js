/**
 * SKYCA DIGITAL PRIVATE LIMITED — MAIN FRONTEND APPLICATION CONTROLLER (main.js)
 * Handles UTM attribution capture, accessible mega-menu & mobile navigation,
 * FAQ accordions, client-side + server-verified enquiry submission, and
 * consent-aware analytics & conversion tracking.
 */

(function () {
    'use strict';

    // ------------------------------------------------------------------------
    // 1. UTM & ATTRIBUTION CAPTURE (PERSISTED ACROSS SESSION)
    // ------------------------------------------------------------------------
    function captureAttribution() {
        try {
            var storageKey = 'skyca_attribution_v1';
            var existingRaw = sessionStorage.getItem(storageKey);
            var attr = existingRaw ? JSON.parse(existingRaw) : {};

            var params = new URLSearchParams(window.location.search);
            var utmKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'];

            if (!attr.landing_page) {
                attr.landing_page = window.location.pathname + window.location.search;
            }
            if (!attr.referrer && document.referrer && document.referrer.indexOf(window.location.hostname) === -1) {
                attr.referrer = document.referrer;
            }

            utmKeys.forEach(function (k) {
                var val = params.get(k);
                if (val) {
                    attr[k] = val.substring(0, 150);
                }
            });

            sessionStorage.setItem(storageKey, JSON.stringify(attr));

            // Populate hidden form fields across the page
            var fieldMap = {
                '.attr-landing-page': attr.landing_page || window.location.pathname,
                '.attr-referrer': attr.referrer || document.referrer || '',
                '.attr-utm-source': attr.utm_source || '',
                '.attr-utm-medium': attr.utm_medium || '',
                '.attr-utm-campaign': attr.utm_campaign || '',
                '.attr-utm-content': attr.utm_content || '',
                '.attr-utm-term': attr.utm_term || ''
            };

            Object.keys(fieldMap).forEach(function (selector) {
                var inputs = document.querySelectorAll(selector);
                inputs.forEach(function (input) {
                    if (fieldMap[selector]) {
                        input.value = fieldMap[selector];
                    }
                });
            });
        } catch (e) {
            // Ignore storage errors in restricted private browsing modes
        }
    }

    // ------------------------------------------------------------------------
    // 2. DESKTOP MEGA-MENU & MOBILE NAVIGATION
    // ------------------------------------------------------------------------
    function initNavigation() {
        var dropdownItem = document.getElementById('services-dropdown-item');
        var dropdownBtn = document.getElementById('services-dropdown-btn');
        var megaMenu = document.getElementById('services-mega-menu');

        if (dropdownItem && dropdownBtn && megaMenu) {
            function closeMegaMenu() {
                dropdownItem.classList.remove('open');
                dropdownBtn.setAttribute('aria-expanded', 'false');
            }

            function openMegaMenu() {
                dropdownItem.classList.add('open');
                dropdownBtn.setAttribute('aria-expanded', 'true');
            }

            dropdownBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                var isExpanded = dropdownBtn.getAttribute('aria-expanded') === 'true';
                if (isExpanded) {
                    closeMegaMenu();
                } else {
                    openMegaMenu();
                }
            });

            dropdownItem.addEventListener('mouseenter', function () {
                if (window.innerWidth >= 1024) {
                    openMegaMenu();
                }
            });

            dropdownItem.addEventListener('mouseleave', function () {
                if (window.innerWidth >= 1024) {
                    closeMegaMenu();
                }
            });

            document.addEventListener('click', function (e) {
                if (!dropdownItem.contains(e.target)) {
                    closeMegaMenu();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeMegaMenu();
                }
            });
        }

        // Mobile Drawer Toggle
        var mobileToggle = document.getElementById('mobile-menu-toggle');
        var mobileDrawer = document.getElementById('mobile-nav-drawer');
        if (mobileToggle && mobileDrawer) {
            var openIcon = mobileToggle.querySelector('.menu-icon-open');
            var closeIcon = mobileToggle.querySelector('.menu-icon-close');

            mobileToggle.addEventListener('click', function () {
                var isOpen = !mobileDrawer.classList.contains('hidden');
                if (isOpen) {
                    mobileDrawer.classList.add('hidden');
                    mobileToggle.setAttribute('aria-expanded', 'false');
                    if (openIcon) openIcon.classList.remove('hidden');
                    if (closeIcon) closeIcon.classList.add('hidden');
                } else {
                    mobileDrawer.classList.remove('hidden');
                    mobileToggle.setAttribute('aria-expanded', 'true');
                    if (openIcon) openIcon.classList.add('hidden');
                    if (closeIcon) closeIcon.classList.remove('hidden');
                }
            });
        }

        // Mobile Services Sublist Accordion
        var mobileServicesToggle = document.getElementById('mobile-services-toggle');
        var mobileServicesSublist = document.getElementById('mobile-services-sublist');
        if (mobileServicesToggle && mobileServicesSublist) {
            mobileServicesToggle.addEventListener('click', function () {
                var isHidden = mobileServicesSublist.classList.contains('hidden');
                if (isHidden) {
                    mobileServicesSublist.classList.remove('hidden');
                    mobileServicesToggle.setAttribute('aria-expanded', 'true');
                } else {
                    mobileServicesSublist.classList.add('hidden');
                    mobileServicesToggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    }

    // ------------------------------------------------------------------------
    // 3. ACCESSIBLE FAQ ACCORDIONS
    // ------------------------------------------------------------------------
    function initFaqAccordions() {
        var triggers = document.querySelectorAll('.faq-trigger');
        triggers.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var panelId = btn.getAttribute('aria-controls');
                var panel = panelId ? document.getElementById(panelId) : null;
                if (!panel) return;

                var isExpanded = btn.getAttribute('aria-expanded') === 'true';
                btn.setAttribute('aria-expanded', isExpanded ? 'false' : 'true');
                if (isExpanded) {
                    panel.classList.add('hidden');
                } else {
                    panel.classList.remove('hidden');
                }
            });
        });
    }

    // ------------------------------------------------------------------------
    // 4. PROJECT ENQUIRY FORM VALIDATION & SUBMISSION
    // ------------------------------------------------------------------------
    function initEnquiryForms() {
        var forms = document.querySelectorAll('.skyca-lead-form');
        forms.forEach(function (form) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                var formId = form.getAttribute('id') || 'skyca-enquiry-form';
                var alertBox = document.getElementById(formId + '-alert');
                var submitBtn = form.querySelector('.btn-submit-enquiry');
                var submitText = form.querySelector('.btn-submit-text');
                var submitSpinner = form.querySelector('.btn-submit-spinner');

                // Clear previous field errors
                form.querySelectorAll('.field-error').forEach(function (el) {
                    el.textContent = '';
                });
                form.querySelectorAll('.form-control').forEach(function (el) {
                    el.classList.remove('input-error');
                });
                if (alertBox) {
                    alertBox.className = 'form-alert hidden';
                    alertBox.textContent = '';
                }

                // Client-side validation
                var hasErrors = false;
                function setFieldError(fieldName, message) {
                    hasErrors = true;
                    var errSpan = form.querySelector('[data-error-for="' + fieldName + '"]');
                    if (errSpan) errSpan.textContent = message;
                    var inputEl = form.querySelector('[name="' + fieldName + '"]');
                    if (inputEl) inputEl.classList.add('input-error');
                }

                var fullName = (form.querySelector('[name="full_name"]').value || '').trim();
                if (fullName.length < 2) {
                    setFieldError('full_name', 'Please enter your full name (at least 2 characters).');
                }

                var email = (form.querySelector('[name="business_email"]').value || '').trim();
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    setFieldError('business_email', 'Please enter a valid business email address.');
                }

                var phone = (form.querySelector('[name="phone"]').value || '').trim();
                var phoneDigits = phone.replace(/\D+/g, '');
                if (phoneDigits.length < 7 || phoneDigits.length > 15) {
                    setFieldError('phone', 'Please enter a valid phone number (7 to 15 digits).');
                }

                var service = (form.querySelector('[name="requested_service"]').value || '').trim();
                if (!service) {
                    setFieldError('requested_service', 'Please select the service you require.');
                }

                var desc = (form.querySelector('[name="project_description"]').value || '').trim();
                if (desc.length < 15) {
                    setFieldError('project_description', 'Please describe your project requirements (at least 15 characters).');
                }

                var consentInput = form.querySelector('[name="consent_given"]');
                if (!consentInput || !consentInput.checked) {
                    setFieldError('consent_given', 'Please agree to the Privacy Policy to submit your enquiry.');
                }

                if (hasErrors) {
                    if (alertBox) {
                        alertBox.className = 'form-alert alert-error';
                        alertBox.textContent = 'Please check the highlighted fields below and try again.';
                    }
                    return;
                }

                // Enter loading state & prevent duplicate clicks
                if (submitBtn) submitBtn.disabled = true;
                if (submitText) submitText.classList.add('hidden');
                if (submitSpinner) submitSpinner.classList.remove('hidden');

                var formData = new FormData(form);

                fetch(form.getAttribute('action') || '/api/enquiry', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                    .then(function (resp) {
                        return resp.json().then(function (data) {
                            return { status: resp.status, data: data };
                        });
                    })
                    .then(function (result) {
                        var data = result.data || {};
                        if (result.status >= 200 && result.status < 300 && data.success) {
                            if (alertBox) {
                                alertBox.className = 'form-alert alert-success';
                                alertBox.textContent = data.message || 'Enquiry submitted successfully! Redirecting...';
                            }
                            if (data.redirect_url) {
                                window.location.href = data.redirect_url;
                                return;
                            }
                        } else {
                            if (data.errors && typeof data.errors === 'object') {
                                Object.keys(data.errors).forEach(function (fieldKey) {
                                    setFieldError(fieldKey, data.errors[fieldKey]);
                                });
                            }
                            if (alertBox) {
                                alertBox.className = 'form-alert alert-error';
                                alertBox.textContent = data.message || 'Unable to process your submission. Please try again.';
                            }
                            if (submitBtn) submitBtn.disabled = false;
                            if (submitText) submitText.classList.remove('hidden');
                            if (submitSpinner) submitSpinner.classList.add('hidden');
                        }
                    })
                    .catch(function () {
                        if (alertBox) {
                            alertBox.className = 'form-alert alert-error';
                            alertBox.textContent = 'Network error occurred while submitting. Please check your connection or contact us at info@skycadigital.com.';
                        }
                        if (submitBtn) submitBtn.disabled = false;
                        if (submitText) submitText.classList.remove('hidden');
                        if (submitSpinner) submitSpinner.classList.add('hidden');
                    });
            });
        });
    }

    // ------------------------------------------------------------------------
    // 5. CONSENT-AWARE COOKIE BANNER & MARKETING TRACKING (GA4 / GOOGLE ADS / META)
    // ------------------------------------------------------------------------
    var trackingInitialized = false;

    function loadMarketingScripts(consent) {
        if (trackingInitialized) return;
        var cfg = window.SKYCA_TRACKING || {};

        // 1. Google Analytics 4 / Google Ads (gtag.js)
        var gtagPrimaryId = (consent.analytics && cfg.ga4Id) ? cfg.ga4Id : ((consent.marketing && cfg.googleAdsId) ? cfg.googleAdsId : null);
        if (gtagPrimaryId) {
            trackingInitialized = true;
            window.dataLayer = window.dataLayer || [];
            window.gtag = function () { window.dataLayer.push(arguments); };
            window.gtag('js', new Date());

            if (consent.analytics && cfg.ga4Id) {
                window.gtag('config', cfg.ga4Id, { anonymize_ip: true });
            }
            if (consent.marketing && cfg.googleAdsId) {
                window.gtag('config', cfg.googleAdsId);
            }

            var s = document.createElement('script');
            s.async = true;
            s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(gtagPrimaryId);
            document.head.appendChild(s);
        }

        // 2. Meta Pixel (Strictly when configured & marketing consent granted)
        if (consent.marketing && cfg.metaPixelId && !window.fbq) {
            !function (f, b, e, v, n, t, s) {
                if (f.fbq) return; n = f.fbq = function () {
                    n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
                };
                if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
                n.queue = []; t = b.createElement(e); t.async = !0;
                t.src = v; s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s);
            }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
            window.fbq('init', cfg.metaPixelId);
            window.fbq('track', 'PageView');
        }

        // 3. Fire Verified Lead Conversion Event on Thank You Page (Zero PII passed)
        if (cfg.isThankYouConversion && cfg.leadRef) {
            if (typeof window.gtag === 'function') {
                if (consent.analytics && cfg.ga4Id) {
                    window.gtag('event', 'generate_lead', {
                        transaction_id: cfg.leadRef,
                        currency: 'INR',
                        value: 1.0
                    });
                }
                if (consent.marketing && cfg.googleAdsId && cfg.googleAdsLabel) {
                    window.gtag('event', 'conversion', {
                        send_to: cfg.googleAdsId + '/' + cfg.googleAdsLabel,
                        transaction_id: cfg.leadRef
                    });
                }
            }
            if (consent.marketing && typeof window.fbq === 'function') {
                window.fbq('track', 'Lead', {
                    content_name: 'Project Enquiry',
                    status: 'verified'
                }, { eventID: cfg.leadRef });
            }
        }
    }

    function initCookieConsent() {
        var consentKey = 'skyca_cookie_consent_v1';
        var banner = document.getElementById('cookie-consent-banner');
        var prefsPanel = document.getElementById('cookie-preferences-panel');
        var analyticsCb = document.getElementById('consent-analytics-checkbox');
        var marketingCb = document.getElementById('consent-marketing-checkbox');
        var acceptBtn = document.getElementById('cookie-accept-btn');
        var rejectBtn = document.getElementById('cookie-reject-btn');
        var customizeBtn = document.getElementById('cookie-customize-btn');
        var footerOpenBtn = document.getElementById('open-cookie-settings-btn');
        var pageOpenBtn = document.getElementById('page-open-cookie-settings-btn');

        function saveConsent(analytics, marketing) {
            var payload = {
                essential: true,
                analytics: !!analytics,
                marketing: !!marketing,
                timestamp: new Date().toISOString()
            };
            try {
                localStorage.setItem(consentKey, JSON.stringify(payload));
            } catch (e) {}
            if (banner) banner.classList.add('hidden');
            loadMarketingScripts(payload);
        }

        var savedRaw = null;
        try {
            savedRaw = localStorage.getItem(consentKey);
        } catch (e) {}

        if (savedRaw) {
            try {
                var parsed = JSON.parse(savedRaw);
                loadMarketingScripts(parsed);
            } catch (e) {}
        } else if (banner) {
            banner.classList.remove('hidden');
        }

        if (acceptBtn) {
            acceptBtn.addEventListener('click', function () {
                if (prefsPanel && !prefsPanel.classList.contains('hidden')) {
                    saveConsent(analyticsCb ? analyticsCb.checked : true, marketingCb ? marketingCb.checked : true);
                } else {
                    saveConsent(true, true);
                }
            });
        }

        if (rejectBtn) {
            rejectBtn.addEventListener('click', function () {
                if (analyticsCb) analyticsCb.checked = false;
                if (marketingCb) marketingCb.checked = false;
                saveConsent(false, false);
            });
        }

        if (customizeBtn && prefsPanel) {
            customizeBtn.addEventListener('click', function () {
                prefsPanel.classList.toggle('hidden');
                if (acceptBtn) {
                    acceptBtn.textContent = prefsPanel.classList.contains('hidden') ? 'Accept All' : 'Save Preferences';
                }
            });
        }

        function reopenBanner() {
            if (banner) {
                banner.classList.remove('hidden');
                if (prefsPanel) prefsPanel.classList.remove('hidden');
                if (acceptBtn) acceptBtn.textContent = 'Save Preferences';
            }
        }

        if (footerOpenBtn) footerOpenBtn.addEventListener('click', reopenBanner);
        if (pageOpenBtn) pageOpenBtn.addEventListener('click', reopenBanner);
    }

    document.addEventListener('DOMContentLoaded', function () {
        captureAttribution();
        initNavigation();
        initFaqAccordions();
        initEnquiryForms();
        initCookieConsent();
    });
})();
