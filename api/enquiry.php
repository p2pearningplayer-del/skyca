<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — PROJECT ENQUIRY & ADS CRM CAPTURE API (/api/enquiry)
 * Validates, rate-limits, deduplicates, classifies Ad Channel & Lead Priority,
 * stores leads transactionally, triggers SMTP emails, and dispatches CRM webhooks.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';
require_once dirname(__DIR__) . '/includes/security.php';
require_once dirname(__DIR__) . '/includes/db.php';
require_once dirname(__DIR__) . '/includes/mailer.php';

skyca_start_session();

$wantsJson = (
    (isset($_SERVER['HTTP_ACCEPT']) && str_contains(strtolower((string) $_SERVER['HTTP_ACCEPT']), 'application/json')) ||
    (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string) $_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
);

function skyca_enquiry_respond(int $statusCode, array $payload, bool $asJson): never
{
    http_response_code($statusCode);
    if ($asJson) {
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    if (!empty($payload['success']) && !empty($payload['redirect_url'])) {
        header('Location: ' . $payload['redirect_url']);
        exit;
    }

    $_SESSION['skyca_form_error'] = $payload['message'] ?? 'Unable to process your submission.';
    header('Location: /contact#enquiry-form');
    exit;
}

/**
 * Automatically classify the Ad Channel from click IDs, UTM tags, and HTTP Referrer.
 */
function skyca_classify_ad_channel(string $gclid, string $fbclid, string $msclkid, string $utmSource, string $utmMedium, string $referrer): string
{
    $src = strtolower($utmSource);
    $med = strtolower($utmMedium);
    $ref = strtolower($referrer);

    if ($gclid !== '' || str_contains($src, 'google') && in_array($med, ['cpc', 'ppc', 'paid', 'ads', 'gdn'], true) || $src === 'google_ads') {
        return 'Google Ads';
    }
    if ($fbclid !== '' || in_array($src, ['facebook', 'instagram', 'meta', 'fb', 'ig', 'meta_ads'], true)) {
        return 'Meta Ads';
    }
    if ($msclkid !== '' || str_contains($src, 'bing') || str_contains($src, 'microsoft')) {
        return 'Microsoft Ads';
    }
    if (str_contains($src, 'linkedin')) {
        return 'LinkedIn Ads';
    }
    if (in_array($med, ['cpc', 'ppc', 'paid', 'paidsocial', 'display'], true) || $src !== '') {
        return 'Campaign (' . ($utmSource ?: $utmMedium) . ')';
    }
    if (str_contains($ref, 'google.') || str_contains($ref, 'bing.') || str_contains($ref, 'duckduckgo.') || str_contains($ref, 'yahoo.')) {
        return 'Organic Search';
    }
    if ($ref !== '' && !str_contains($ref, 'skycadigital.com')) {
        return 'Referral';
    }
    return 'Direct / Organic';
}

/**
 * Estimate initial deal pipeline value (INR) and priority from selected budget & timeline.
 */
function skyca_score_lead_crm(string $budget, string $timeline, string $adChannel): array
{
    $dealValue = 0.0;
    if (str_contains($budget, '15,00,000+')) {
        $dealValue = 1500000.00;
    } elseif (str_contains($budget, '5,00,000')) {
        $dealValue = 500000.00;
    } elseif (str_contains($budget, '1,50,000')) {
        $dealValue = 150000.00;
    } elseif (str_contains($budget, '50,000')) {
        $dealValue = 50000.00;
    }

    $isPaidAd = in_array($adChannel, ['Google Ads', 'Meta Ads', 'Microsoft Ads', 'LinkedIn Ads'], true);
    $isUrgent = str_contains(strtolower($timeline), 'immediate') || str_contains(strtolower($timeline), '1 month');

    if ($dealValue >= 500000 || ($isPaidAd && $isUrgent) || ($dealValue >= 150000 && $isUrgent)) {
        $priority = 'Hot';
    } elseif ($isPaidAd || $dealValue >= 50000 || $isUrgent) {
        $priority = 'Warm';
    } else {
        $priority = 'Normal';
    }

    return [
        'deal_value'    => $dealValue,
        'lead_priority' => $priority,
    ];
}

/**
 * Push lead payload to an external CRM Webhook (HubSpot, Zoho, LeadSquared, Make, Zapier, Custom) if configured.
 */
function skyca_dispatch_crm_webhook(PDO $pdo, int $leadId, array $leadPayload): bool
{
    $settings   = skyca_get_settings();
    $webhookUrl = trim((string) ($settings['crm_webhook_url'] ?? ''));
    if ($webhookUrl === '' || !filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
        return false;
    }

    $secret = (string) ($settings['crm_webhook_secret'] ?? '');
    $jsonBody = (string) json_encode([
        'event'     => 'skyca.lead.created',
        'timestamp' => date('c'),
        'source'    => SITE_URL,
        'lead'      => $leadPayload,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

    $headers = [
        'Content-Type: application/json',
        'User-Agent: SkycaDigital-CRM-Webhook/1.0',
    ];
    if ($secret !== '') {
        $headers[] = 'X-Skyca-Signature: ' . hash_hmac('sha256', $jsonBody, $secret);
    }

    try {
        $ch = curl_init($webhookUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonBody,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 5,
        ]);
        curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        unset($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            $upd = $pdo->prepare('UPDATE leads SET crm_webhook_synced = 1 WHERE id = :id');
            $upd->execute([':id' => $leadId]);
            return true;
        }
    } catch (Throwable $e) {
        error_log('[SkycaCRM Webhook Error] ' . $e->getMessage());
    }
    return false;
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    skyca_enquiry_respond(405, [
        'success' => false,
        'message' => 'Method not allowed. Please submit the form using POST.',
    ], true);
}

$input = $_POST;
$contentType = strtolower((string) ($_SERVER['CONTENT_TYPE'] ?? ''));
if (str_contains($contentType, 'application/json')) {
    $rawBody = (string) file_get_contents('php://input');
    $decoded = json_decode($rawBody, true);
    if (is_array($decoded)) {
        $input = $decoded;
    }
    $wantsJson = true;
}

// 1. Honeypot Spam Check
$honeypot = trim((string) ($input['website_url_hp'] ?? ''));
if ($honeypot !== '') {
    skyca_enquiry_respond(400, [
        'success' => false,
        'message' => 'Spam validation failed. Please refresh the page and try again.',
    ], $wantsJson);
}

// 2. CSRF Token Check
$csrfToken = (string) ($input['csrf_token'] ?? '');
if (!skyca_verify_csrf($csrfToken)) {
    skyca_enquiry_respond(403, [
        'success' => false,
        'message' => 'Your session security token expired. Please refresh the page and submit again.',
    ], $wantsJson);
}

// 3. Form Timestamp Check
$formToken = (string) ($input['form_token'] ?? '');
if (!skyca_verify_form_timestamp($formToken, 0)) {
    skyca_enquiry_respond(400, [
        'success' => false,
        'message' => 'Form session expired. Please reload the page and submit your enquiry again.',
    ], $wantsJson);
}

// 4. Rate Limiting Check
$rateCheck = skyca_check_rate_limit('enquiry_submit', 8, 900, 900, true);
if (!$rateCheck['allowed']) {
    skyca_enquiry_respond(429, [
        'success' => false,
        'message' => 'Too many enquiry attempts from your network. Please wait a few minutes or contact us directly at ' . BUSINESS_PHONE . '.',
    ], $wantsJson);
}

// 5. Extract, Sanitize & Validate Form Inputs
$errors = [];

$fullName = skyca_clean_input((string) ($input['full_name'] ?? ''), 120, false);
if (mb_strlen($fullName, 'UTF-8') < 2) {
    $errors['full_name'] = 'Please enter your full name (at least 2 characters).';
}

$businessEmail = strtolower(skyca_clean_input((string) ($input['business_email'] ?? ''), 190, false));
if ($businessEmail === '' || !filter_var($businessEmail, FILTER_VALIDATE_EMAIL)) {
    $errors['business_email'] = 'Please enter a valid business email address.';
}

$countryCode = skyca_clean_input((string) ($input['country_code'] ?? '+91'), 10, false);
if (!preg_match('/^\+\d{0,4}$/', $countryCode)) {
    $countryCode = '+91';
}
$rawPhone = skyca_clean_input((string) ($input['phone'] ?? ''), 30, false);
$phoneDigits = (string) preg_replace('/\D+/', '', $rawPhone);
if (strlen($phoneDigits) < 7 || strlen($phoneDigits) > 15) {
    $errors['phone'] = 'Please enter a valid phone number (7 to 15 digits).';
}
$formattedPhone = str_starts_with($rawPhone, '+') ? $rawPhone : trim($countryCode . ' ' . $rawPhone);
$formattedPhone = skyca_clean_input($formattedPhone, 40, false);

$companyName = skyca_clean_input((string) ($input['company_name'] ?? ''), 150, false);

$requestedService = skyca_clean_input((string) ($input['requested_service'] ?? ''), 150, false);
$validServices = skyca_get_service_options();
if ($requestedService === '' || !in_array($requestedService, $validServices, true)) {
    $errors['requested_service'] = 'Please select a valid service from the list.';
}

$estimatedBudget = skyca_clean_input((string) ($input['estimated_budget'] ?? ''), 100, false);
$projectTimeline = skyca_clean_input((string) ($input['project_timeline'] ?? ''), 100, false);

$projectDescription = skyca_clean_input((string) ($input['project_description'] ?? ''), 4000, true);
if (mb_strlen($projectDescription, 'UTF-8') < 15) {
    $errors['project_description'] = 'Please provide at least 15 characters describing your project requirements.';
}

$consentVal = (string) ($input['consent_given'] ?? '');
$consentGiven = in_array(strtolower($consentVal), ['1', 'true', 'yes', 'on'], true);
if (!$consentGiven) {
    $errors['consent_given'] = 'Please confirm your consent to the Privacy Policy before submitting.';
}

if (!empty($errors)) {
    skyca_enquiry_respond(422, [
        'success' => false,
        'message' => 'Please review the highlighted fields and correct any errors.',
        'errors'  => $errors,
    ], $wantsJson);
}

// 6. Sanitize Attribution & Ad Click IDs
$landingPage = skyca_clean_input((string) ($input['landing_page'] ?? ''), 500, false);
$referrer    = skyca_clean_input((string) ($input['referrer'] ?? ''), 500, false);
$utmSource   = skyca_clean_input((string) ($input['utm_source'] ?? ''), 150, false);
$utmMedium   = skyca_clean_input((string) ($input['utm_medium'] ?? ''), 150, false);
$utmCampaign = skyca_clean_input((string) ($input['utm_campaign'] ?? ''), 150, false);
$utmContent  = skyca_clean_input((string) ($input['utm_content'] ?? ''), 150, false);
$utmTerm     = skyca_clean_input((string) ($input['utm_term'] ?? ''), 150, false);
$gclid       = skyca_clean_input((string) ($input['gclid'] ?? ''), 255, false);
$fbclid      = skyca_clean_input((string) ($input['fbclid'] ?? ''), 255, false);
$msclkid     = skyca_clean_input((string) ($input['msclkid'] ?? ''), 255, false);

$adChannel   = skyca_classify_ad_channel($gclid, $fbclid, $msclkid, $utmSource, $utmMedium, $referrer);
$crmScoring  = skyca_score_lead_crm($estimatedBudget, $projectTimeline, $adChannel);

// 7. Determine if this is a Test Enquiry
$isTest = (
    !empty($input['is_test']) ||
    (isset($_SERVER['HTTP_X_SKYCA_TEST']) && $_SERVER['HTTP_X_SKYCA_TEST'] === '1') ||
    str_starts_with(strtoupper($fullName), '[TEST]') ||
    str_ends_with($businessEmail, '@example.com') ||
    str_ends_with($businessEmail, '.test')
) ? 1 : 0;

$submissionHash = hash('sha256', strtolower($businessEmail) . '|' . $requestedService . '|' . strtolower(trim($projectDescription)));
$ipHash         = skyca_client_ip_hash();
$now            = date('Y-m-d H:i:s');
$windowCutoff   = date('Y-m-d H:i:s', time() - 900);

try {
    $pdo = skyca_db();

    $dupStmt = $pdo->prepare('
        SELECT lead_reference
        FROM leads
        WHERE submission_hash = :hash AND created_at >= :cutoff
        LIMIT 1
    ');
    $dupStmt->execute([
        ':hash'   => $submissionHash,
        ':cutoff' => $windowCutoff,
    ]);
    $existingRef = $dupStmt->fetchColumn();

    if (is_string($existingRef) && $existingRef !== '') {
        $_SESSION['last_verified_lead_ref'] = $existingRef;
        skyca_enquiry_respond(200, [
            'success'        => true,
            'duplicate'      => true,
            'lead_reference' => $existingRef,
            'message'        => 'Your enquiry has already been received under reference ' . $existingRef . '.',
            'redirect_url'   => '/thank-you?ref=' . rawurlencode($existingRef),
        ], $wantsJson);
    }

    $leadReference = skyca_generate_lead_reference();

    $pdo->beginTransaction();

    $insertStmt = $pdo->prepare('
        INSERT INTO leads (
            lead_reference,
            full_name,
            business_email,
            phone,
            company_name,
            requested_service,
            estimated_budget,
            project_timeline,
            project_description,
            landing_page,
            referrer,
            utm_source,
            utm_medium,
            utm_campaign,
            utm_content,
            utm_term,
            gclid,
            fbclid,
            msclkid,
            ad_channel,
            lead_priority,
            deal_value,
            consent_status,
            consent_timestamp,
            status,
            is_test,
            submission_hash,
            ip_hash,
            email_notification_sent,
            created_at,
            updated_at
        ) VALUES (
            :lead_reference,
            :full_name,
            :business_email,
            :phone,
            :company_name,
            :requested_service,
            :estimated_budget,
            :project_timeline,
            :project_description,
            :landing_page,
            :referrer,
            :utm_source,
            :utm_medium,
            :utm_campaign,
            :utm_content,
            :utm_term,
            :gclid,
            :fbclid,
            :msclkid,
            :ad_channel,
            :lead_priority,
            :deal_value,
            1,
            :consent_timestamp,
            :status,
            :is_test,
            :submission_hash,
            :ip_hash,
            0,
            :created_at,
            :updated_at
        )
    ');

    $insertStmt->execute([
        ':lead_reference'      => $leadReference,
        ':full_name'           => $fullName,
        ':business_email'      => $businessEmail,
        ':phone'               => $formattedPhone,
        ':company_name'        => $companyName !== '' ? $companyName : null,
        ':requested_service'   => $requestedService,
        ':estimated_budget'    => $estimatedBudget !== '' ? $estimatedBudget : null,
        ':project_timeline'    => $projectTimeline !== '' ? $projectTimeline : null,
        ':project_description' => $projectDescription,
        ':landing_page'        => $landingPage !== '' ? $landingPage : null,
        ':referrer'            => $referrer !== '' ? $referrer : null,
        ':utm_source'          => $utmSource !== '' ? $utmSource : null,
        ':utm_medium'          => $utmMedium !== '' ? $utmMedium : null,
        ':utm_campaign'        => $utmCampaign !== '' ? $utmCampaign : null,
        ':utm_content'         => $utmContent !== '' ? $utmContent : null,
        ':utm_term'            => $utmTerm !== '' ? $utmTerm : null,
        ':gclid'               => $gclid !== '' ? $gclid : null,
        ':fbclid'              => $fbclid !== '' ? $fbclid : null,
        ':msclkid'             => $msclkid !== '' ? $msclkid : null,
        ':ad_channel'          => $adChannel,
        ':lead_priority'       => $crmScoring['lead_priority'],
        ':deal_value'          => $crmScoring['deal_value'],
        ':consent_timestamp'   => $now,
        ':status'              => 'New',
        ':is_test'             => $isTest,
        ':submission_hash'     => $submissionHash,
        ':ip_hash'             => $ipHash,
        ':created_at'          => $now,
        ':updated_at'          => $now,
    ]);
    $leadId = (int) $pdo->lastInsertId();

    $pdo->commit();

    $_SESSION['last_verified_lead_ref'] = $leadReference;
    $_SESSION['last_verified_service']  = $requestedService;

    $leadDataPayload = [
        'lead_reference'      => $leadReference,
        'full_name'           => $fullName,
        'business_email'      => $businessEmail,
        'phone'               => $formattedPhone,
        'company_name'        => $companyName,
        'requested_service'   => $requestedService,
        'estimated_budget'    => $estimatedBudget,
        'project_timeline'    => $projectTimeline,
        'project_description' => $projectDescription,
        'ad_channel'          => $adChannel,
        'lead_priority'       => $crmScoring['lead_priority'],
        'deal_value'          => $crmScoring['deal_value'],
        'landing_page'        => $landingPage,
        'utm_source'          => $utmSource,
        'utm_medium'          => $utmMedium,
        'utm_campaign'        => $utmCampaign,
        'gclid'               => $gclid,
        'fbclid'              => $fbclid,
    ];

    // Trigger SMTP notification and optional External CRM Webhook push
    $emailResult = skyca_dispatch_lead_emails($pdo, $leadId, $leadDataPayload);
    skyca_dispatch_crm_webhook($pdo, $leadId, $leadDataPayload);

    skyca_enquiry_respond(201, [
        'success'        => true,
        'lead_reference' => $leadReference,
        'ad_channel'     => $adChannel,
        'lead_priority'  => $crmScoring['lead_priority'],
        'email_status'   => $emailResult['status'] ?? 'not_configured',
        'message'        => 'Thank you! Your project enquiry has been recorded under reference ' . $leadReference . '.',
        'redirect_url'   => '/thank-you?ref=' . rawurlencode($leadReference),
    ], $wantsJson);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        @$pdo->rollBack();
    }
    error_log('[SkycaEnquiry Error] ' . $e->getMessage());

    skyca_enquiry_respond(500, [
        'success' => false,
        'message' => 'We encountered a temporary technical issue saving your enquiry. Please try again or reach us directly at ' . BUSINESS_EMAIL . ' or ' . BUSINESS_PHONE . '.',
    ], $wantsJson);
}
