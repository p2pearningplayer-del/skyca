<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — REUSABLE PROJECT ENQUIRY FORM COMPONENT
 * Supports preselected service category (from individual service pages or ?service= query param),
 * captures full UTM & landing page attribution, and enforces CSRF + spam protection.
 */

require_once dirname(__DIR__) . '/config/services_data.php';
require_once __DIR__ . '/security.php';

/**
 * Render the project enquiry form.
 *
 * @param array $options {
 *     @type string $preselected_service  Exact service form_value or slug to preselect
 *     @type string $form_id              DOM id of the form element
 *     @type string $heading              Optional custom form heading
 *     @type string $subheading           Optional custom form subheading
 * }
 */
function skyca_render_enquiry_form(array $options = []): void
{
    $serviceOptions = skyca_get_service_options();
    $allServices    = skyca_get_all_services();

    $preselected = trim((string) ($options['preselected_service'] ?? ($_GET['service'] ?? '')));
    // If a slug was passed (e.g., "software-development"), resolve its full form_value
    if (isset($allServices[$preselected])) {
        $preselected = $allServices[$preselected]['form_value'];
    }

    $formId     = $options['form_id'] ?? 'skyca-enquiry-form';
    $heading    = $options['heading'] ?? 'Request a Free Technical Consultation & Project Quote';
    $subheading = $options['subheading'] ?? 'Tell us about your business goals and technical requirements. Our engineering team will review your enquiry and respond with clear next steps.';

    $csrfToken = skyca_csrf_token();
    $timeToken = skyca_form_timestamp_token();

    $currentUri = $_SERVER['REQUEST_URI'] ?? '/';
    $httpRef    = $_SERVER['HTTP_REFERER'] ?? '';
    ?>
    <div class="enquiry-form-card" id="enquiry-form">
        <div class="enquiry-form-header">
            <span class="section-eyebrow">Project Consultation</span>
            <h3 class="enquiry-form-title"><?php echo skyca_e($heading); ?></h3>
            <p class="enquiry-form-subtitle"><?php echo skyca_e($subheading); ?></p>
        </div>

        <!-- Alert Banner for Server / Validation Responses -->
        <div class="form-alert hidden" id="<?php echo skyca_e($formId); ?>-alert" role="alert" aria-live="polite"></div>

        <form
            id="<?php echo skyca_e($formId); ?>"
            class="skyca-lead-form"
            action="/api/enquiry"
            method="POST"
            novalidate
        >
            <input type="hidden" name="csrf_token" value="<?php echo skyca_e($csrfToken); ?>">
            <input type="hidden" name="form_token" value="<?php echo skyca_e($timeToken); ?>">

            <!-- Attribution Hidden Fields (Populated by Server + Enhanced by JS Session Attribution) -->
            <input type="hidden" name="landing_page" class="attr-landing-page" value="<?php echo skyca_e(skyca_clean_input($currentUri, 500)); ?>">
            <input type="hidden" name="referrer" class="attr-referrer" value="<?php echo skyca_e(skyca_clean_input($httpRef, 500)); ?>">
            <input type="hidden" name="utm_source" class="attr-utm-source" value="<?php echo skyca_e(skyca_clean_input($_GET['utm_source'] ?? '', 150)); ?>">
            <input type="hidden" name="utm_medium" class="attr-utm-medium" value="<?php echo skyca_e(skyca_clean_input($_GET['utm_medium'] ?? '', 150)); ?>">
            <input type="hidden" name="utm_campaign" class="attr-utm-campaign" value="<?php echo skyca_e(skyca_clean_input($_GET['utm_campaign'] ?? '', 150)); ?>">
            <input type="hidden" name="utm_content" class="attr-utm-content" value="<?php echo skyca_e(skyca_clean_input($_GET['utm_content'] ?? '', 150)); ?>">
            <input type="hidden" name="utm_term" class="attr-utm-term" value="<?php echo skyca_e(skyca_clean_input($_GET['utm_term'] ?? '', 150)); ?>">

            <!-- Anti-Spam Honeypot (Must remain empty) -->
            <div class="hp-field-wrap" aria-hidden="true">
                <label for="<?php echo skyca_e($formId); ?>-hp">Company Website URL (Leave blank)</label>
                <input type="text" id="<?php echo skyca_e($formId); ?>-hp" name="website_url_hp" tabindex="-1" autocomplete="off" value="">
            </div>

            <div class="form-grid-2">
                <!-- 1. Full Name -->
                <div class="form-group">
                    <label for="<?php echo skyca_e($formId); ?>-full-name" class="form-label">
                        Full Name <span class="required-star" aria-hidden="true">*</span>
                    </label>
                    <input
                        type="text"
                        id="<?php echo skyca_e($formId); ?>-full-name"
                        name="full_name"
                        class="form-control"
                        placeholder="e.g., Rajesh Sharma"
                        required
                        minlength="2"
                        maxlength="120"
                        autocomplete="name"
                    >
                    <span class="field-error" data-error-for="full_name"></span>
                </div>

                <!-- 2. Business Email -->
                <div class="form-group">
                    <label for="<?php echo skyca_e($formId); ?>-email" class="form-label">
                        Business Email <span class="required-star" aria-hidden="true">*</span>
                    </label>
                    <input
                        type="email"
                        id="<?php echo skyca_e($formId); ?>-email"
                        name="business_email"
                        class="form-control"
                        placeholder="name@company.com"
                        required
                        maxlength="190"
                        autocomplete="email"
                    >
                    <span class="field-error" data-error-for="business_email"></span>
                </div>
            </div>

            <div class="form-grid-2">
                <!-- 3. Phone Number with Country Code -->
                <div class="form-group">
                    <label for="<?php echo skyca_e($formId); ?>-phone" class="form-label">
                        Phone Number <span class="required-star" aria-hidden="true">*</span>
                    </label>
                    <div class="phone-input-group">
                        <select
                            name="country_code"
                            id="<?php echo skyca_e($formId); ?>-country-code"
                            class="form-control country-code-select"
                            aria-label="Country Calling Code"
                        >
                            <option value="+91" selected>IN (+91)</option>
                            <option value="+1">US/CA (+1)</option>
                            <option value="+44">UK (+44)</option>
                            <option value="+971">UAE (+971)</option>
                            <option value="+65">SG (+65)</option>
                            <option value="+61">AU (+61)</option>
                            <option value="+49">DE (+49)</option>
                            <option value="+">Other (+)</option>
                        </select>
                        <input
                            type="tel"
                            id="<?php echo skyca_e($formId); ?>-phone"
                            name="phone"
                            class="form-control phone-number-input"
                            placeholder="98765 43210"
                            required
                            minlength="7"
                            maxlength="20"
                            autocomplete="tel-national"
                        >
                    </div>
                    <span class="field-error" data-error-for="phone"></span>
                </div>

                <!-- 4. Company / Organization Name (Optional) -->
                <div class="form-group">
                    <label for="<?php echo skyca_e($formId); ?>-company" class="form-label">
                        Company or Organization <span class="optional-tag">(Optional)</span>
                    </label>
                    <input
                        type="text"
                        id="<?php echo skyca_e($formId); ?>-company"
                        name="company_name"
                        class="form-control"
                        placeholder="Your company or startup name"
                        maxlength="150"
                        autocomplete="organization"
                    >
                    <span class="field-error" data-error-for="company_name"></span>
                </div>
            </div>

            <!-- 5. Service Required -->
            <div class="form-group">
                <label for="<?php echo skyca_e($formId); ?>-service" class="form-label">
                    Service Required <span class="required-star" aria-hidden="true">*</span>
                </label>
                <select
                    id="<?php echo skyca_e($formId); ?>-service"
                    name="requested_service"
                    class="form-control"
                    required
                >
                    <option value="" <?php echo $preselected === '' ? 'selected' : ''; ?> disabled>Select the primary service you need...</option>
                    <?php foreach ($serviceOptions as $srvOption): ?>
                        <option value="<?php echo skyca_e($srvOption); ?>" <?php echo ($preselected === $srvOption) ? 'selected' : ''; ?>>
                            <?php echo skyca_e($srvOption); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="field-error" data-error-for="requested_service"></span>
            </div>

            <div class="form-grid-2">
                <!-- 6. Estimated Project Budget (Optional) -->
                <div class="form-group">
                    <label for="<?php echo skyca_e($formId); ?>-budget" class="form-label">
                        Estimated Project Budget <span class="optional-tag">(Optional)</span>
                    </label>
                    <select
                        id="<?php echo skyca_e($formId); ?>-budget"
                        name="estimated_budget"
                        class="form-control"
                    >
                        <option value="">Select an approximate range...</option>
                        <option value="Under ₹50,000">Under ₹50,000</option>
                        <option value="₹50,000 – ₹1,50,000">₹50,000 – ₹1,50,000</option>
                        <option value="₹1,50,000 – ₹5,00,000">₹1,50,000 – ₹5,00,000</option>
                        <option value="₹5,00,000 – ₹15,00,000">₹5,00,000 – ₹15,00,000</option>
                        <option value="₹15,00,000+ / Enterprise Scope">₹15,00,000+ / Enterprise Scope</option>
                        <option value="Need Consultation / Estimate First">Need Consultation / Estimate First</option>
                    </select>
                </div>

                <!-- 7. Project Timeline (Optional) -->
                <div class="form-group">
                    <label for="<?php echo skyca_e($formId); ?>-timeline" class="form-label">
                        Project Timeline <span class="optional-tag">(Optional)</span>
                    </label>
                    <select
                        id="<?php echo skyca_e($formId); ?>-timeline"
                        name="project_timeline"
                        class="form-control"
                    >
                        <option value="">Select target timeline...</option>
                        <option value="Immediate / Urgent Start">Immediate / Urgent Start</option>
                        <option value="Within 1 Month">Within 1 Month</option>
                        <option value="1 to 3 Months">1 to 3 Months</option>
                        <option value="3 to 6 Months">3 to 6 Months</option>
                        <option value="Flexible / Planning Phase">Flexible / Planning Phase</option>
                    </select>
                </div>
            </div>

            <!-- 8. Project Description -->
            <div class="form-group">
                <label for="<?php echo skyca_e($formId); ?>-description" class="form-label">
                    Project Description &amp; Requirements <span class="required-star" aria-hidden="true">*</span>
                </label>
                <textarea
                    id="<?php echo skyca_e($formId); ?>-description"
                    name="project_description"
                    class="form-control"
                    rows="4"
                    placeholder="Briefly describe what you want to build, key features, target users, or any technical challenges you want to solve..."
                    required
                    minlength="15"
                    maxlength="4000"
                ></textarea>
                <span class="field-error" data-error-for="project_description"></span>
            </div>

            <!-- 9. Privacy Policy Consent Checkbox -->
            <div class="form-group consent-group">
                <label class="consent-checkbox-label" for="<?php echo skyca_e($formId); ?>-consent">
                    <input
                        type="checkbox"
                        id="<?php echo skyca_e($formId); ?>-consent"
                        name="consent_given"
                        value="1"
                        required
                    >
                    <span>
                        I agree to allow <strong><?php echo skyca_e(COMPANY_LEGAL_NAME); ?></strong> to process the information submitted in this form to respond to my business enquiry, in accordance with the <a href="/privacy-policy" target="_blank" rel="noopener">Privacy Policy</a>. <span class="required-star" aria-hidden="true">*</span>
                    </span>
                </label>
                <span class="field-error" data-error-for="consent_given"></span>
            </div>

            <div class="form-submit-row">
                <button type="submit" class="btn btn-primary btn-lg btn-submit-enquiry">
                    <span class="btn-submit-text">Submit Project Enquiry</span>
                    <span class="btn-submit-spinner hidden" aria-hidden="true">Submitting...</span>
                    <svg class="icon-xs btn-submit-arrow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </button>
                <p class="form-privacy-note">
                    <svg class="icon-xs" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    <span>Encrypted transmission. Your details are never sold or shared with third-party marketers.</span>
                </p>
            </div>
        </form>
    </div>
    <?php
}
