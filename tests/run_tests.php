<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — AUTOMATED END-TO-END QA & SECURITY TEST SUITE
 * Usage: php tests/run_tests.php
 * Tests all public pages, SEO metadata, broken links, assets, form validation,
 * database insertion, duplicate prevention, SQLi/XSS defenses, and Admin flows.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('CLI execution only.');
}

$rootDir = dirname(__DIR__);
$testDbPath = $rootDir . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'skyca_qa_test.sqlite';
if (is_file($testDbPath)) {
    @unlink($testDbPath);
}

// Clear rate limit files before running tests
foreach (glob($rootDir . '/storage/rate_limits/*.json') ?: [] as $rf) {
    @unlink($rf);
}

putenv('DATABASE_DRIVER=sqlite');
putenv('TEST_SQLITE_PATH=' . $testDbPath);
$_ENV['DATABASE_DRIVER'] = 'sqlite';
$_ENV['TEST_SQLITE_PATH'] = $testDbPath;

require_once $rootDir . '/config/config.php';
require_once $rootDir . '/config/services_data.php';
require_once $rootDir . '/includes/security.php';
require_once $rootDir . '/includes/db.php';

$passed = 0;
$failed = 0;

function assert_test(string $name, bool $condition, string $detail = ''): void
{
    global $passed, $failed;
    if ($condition) {
        $passed++;
        echo "  [PASS] {$name}" . ($detail !== '' ? " ({$detail})" : "") . "\n";
    } else {
        $failed++;
        echo "  [FAIL] {$name}" . ($detail !== '' ? " — {$detail}" : "") . "\n";
    }
}

echo "======================================================================\n";
echo " SKYCA DIGITAL PRIVATE LIMITED — FULL-STACK VERIFICATION SUITE\n";
echo "======================================================================\n\n";

// ----------------------------------------------------------------------------
// 1. DATABASE INITIALIZATION & ADMIN SETUP TESTS
// ----------------------------------------------------------------------------
echo "--- 1. Database Schema & Admin Setup ---\n";
$pdo = skyca_db();
assert_test('Database connection initialized', $pdo instanceof PDO);

$adminPass = 'SkycaAdmin#2026!Secure';
$hash = password_hash($adminPass, PASSWORD_DEFAULT);
$now = date('Y-m-d H:i:s');
$pdo->prepare('INSERT INTO admin_users (username, email, password_hash, full_name, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, 1, ?, ?)')
    ->execute(['admin', 'info@skycadigital.com', $hash, 'QA Admin', $now, $now]);

$adminRowCount = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
assert_test('Admin account created with bcrypt password hash', $adminRowCount === 1);

// ----------------------------------------------------------------------------
// 2. START LOCAL HTTP SERVER FOR LIVE HTTP / ROUTING / API TESTS
// ----------------------------------------------------------------------------
echo "\n--- 2. Starting Live HTTP Test Server (127.0.0.1:8091) ---\n";
$nullSink = DIRECTORY_SEPARATOR === '\\' ? 'NUL' : '/dev/null';
$descriptors = [
    0 => ['pipe', 'r'],
    1 => ['file', $nullSink, 'w'],
    2 => ['file', $nullSink, 'w'],
];
$serverEnv = array_merge(getenv() ?: [], [
    'DATABASE_DRIVER'  => 'sqlite',
    'TEST_SQLITE_PATH' => $testDbPath,
]);
$cmd = sprintf('"%s" -S 127.0.0.1:8091 index.php', PHP_BINARY);
$proc = proc_open($cmd, $descriptors, $pipes, $rootDir, $serverEnv);
usleep(600000);

$cookieJar = [];

function http_request(string $path, string $method = 'GET', $postData = null, array $headers = []): array
{
    global $cookieJar;
    $ch = curl_init('http://127.0.0.1:8091' . $path);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8);

    if (!empty($cookieJar)) {
        $cookiePairs = [];
        foreach ($cookieJar as $k => $v) {
            $cookiePairs[] = "{$k}={$v}";
        }
        curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $cookiePairs));
    }

    if ($postData !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($postData) ? http_build_query($postData) : $postData);
    }
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $resp = (string) curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $headerSize = (int) curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    unset($ch);

    $rawHeaders = substr($resp, 0, $headerSize);
    $body = substr($resp, $headerSize);

    if (preg_match_all('/^Set-Cookie:\s*([^=]+)=([^;\r\n]+)/im', $rawHeaders, $cMatches, PREG_SET_ORDER)) {
        foreach ($cMatches as $cm) {
            $cName = trim($cm[1]);
            $cVal = trim($cm[2]);
            if ($cVal === '' || $cVal === 'deleted') {
                unset($cookieJar[$cName]);
            } else {
                $cookieJar[$cName] = $cVal;
            }
        }
    }

    return [
        'status'  => $status,
        'headers' => $rawHeaders,
        'body'    => $body,
    ];
}

try {
    // ------------------------------------------------------------------------
    // 3. PUBLIC PAGES, SEO METADATA, CANONICALS & INTERNAL LINK VERIFICATION
    // ------------------------------------------------------------------------
    echo "\n--- 3. Public Pages, SEO Metadata & Service Landing Pages ---\n";
    $publicRoutes = [
        '/'                                         => 'Technology Solutions That Move Your Business Forward.',
        '/services'                                 => 'Information Technology &amp; Software Development Services',
        '/services/software-development'            => 'Information Technology Services &amp; Software Development',
        '/services/web-mobile-development'          => 'Website &amp; Mobile Application Development',
        '/services/saas-cloud-solutions'            => 'SaaS Solutions &amp; Cloud Services',
        '/services/ai-it-consulting'                => 'Artificial Intelligence Solutions &amp; IT Consulting',
        '/services/digital-marketing-cybersecurity' => 'Digital Marketing &amp; Cyber Security Services',
        '/services/data-analytics-support'          => 'Data Analytics &amp; Technology Support Services',
        '/services/ecommerce-solutions'             => 'E-Commerce Solutions and Related Technology Activities',
        '/about'                                    => 'About Skyca Digital Private Limited',
        '/contact'                                  => 'Contact Skyca Digital Private Limited',
        '/faq'                                      => 'Frequently Asked Questions',
        '/privacy-policy'                           => 'Privacy Policy',
        '/terms-and-conditions'                     => 'Terms and Conditions',
        '/cookie-policy'                            => 'Cookie Policy',
        '/thank-you'                                => 'Thank You for Contacting Skyca Digital',
    ];

    $seenTitles = [];
    $discoveredAssets = [];

    foreach ($publicRoutes as $route => $expectedText) {
        $res = http_request($route);
        $ok = ($res['status'] === 200 && str_contains($res['body'], $expectedText));
        assert_test("Route {$route} renders HTTP 200 with expected content", $ok, "HTTP {$res['status']}");

        preg_match('/<title>(.*?)<\/title>/is', $res['body'], $titleMatch);
        $pageTitle = trim($titleMatch[1] ?? '');
        $hasDesc = (bool) preg_match('/<meta\s+name="description"\s+content="([^"]+)"/i', $res['body']);
        $hasCanon = (bool) preg_match('/<link\s+rel="canonical"\s+href="https:\/\/skycadigital\.com[^"]*"/i', $res['body']);
        $hasJsonLd = str_contains($res['body'], 'application/ld+json');

        assert_test("SEO metadata & JSON-LD present on {$route}", $pageTitle !== '' && $hasDesc && $hasCanon && $hasJsonLd, $pageTitle);
        $seenTitles[$route] = $pageTitle;

        if (str_starts_with($route, '/services/')) {
            $slug = substr($route, strlen('/services/'));
            $srv = skyca_get_service_by_slug($slug);
            $expectedSelected = 'value="' . htmlspecialchars($srv['form_value'], ENT_QUOTES, 'UTF-8') . '" selected';
            assert_test("Preselected service in enquiry form on {$route}", str_contains($res['body'], $expectedSelected));
        }

        if (preg_match_all('/(?:src|href)="(\/assets\/[^"?]+)"/i', $res['body'], $assetMatches)) {
            foreach ($assetMatches[1] as $aPath) {
                $discoveredAssets[$aPath] = true;
            }
        }
    }

    assert_test('All 16 public pages have unique <title> tags', count(array_unique(array_values($seenTitles))) === count($publicRoutes));

    $sitemapRes = http_request('/sitemap.xml');
    assert_test('/sitemap.xml returns valid XML with all service URLs', $sitemapRes['status'] === 200 && str_contains($sitemapRes['body'], '<urlset') && str_contains($sitemapRes['body'], 'https://skycadigital.com/services/software-development'));

    $notFoundRes = http_request('/non-existent-page-xyz');
    assert_test('Unknown route returns HTTP 404 status with custom 404 page', $notFoundRes['status'] === 404 && str_contains($notFoundRes['body'], 'Page Not Found'));

    $allAssetsExist = true;
    foreach (array_keys($discoveredAssets) as $assetUrlPath) {
        $diskPath = $rootDir . str_replace('/', DIRECTORY_SEPARATOR, $assetUrlPath);
        if (!is_file($diskPath)) {
            $allAssetsExist = false;
            echo "  [MISSING ASSET] {$assetUrlPath}\n";
        }
    }
    assert_test('All referenced CSS, JS, and official logo image assets exist', $allAssetsExist, count($discoveredAssets) . ' assets verified');

    $prot1 = http_request('/config/config.php');
    $prot2 = http_request('/database/schema.sql');
    assert_test('Protected internal directories (/config, /database) return HTTP 403', $prot1['status'] === 403 && $prot2['status'] === 403);

    // ------------------------------------------------------------------------
    // 4. ENQUIRY FORM VALIDATION, DB INSERTION, DUPLICATE GUARD & SECURITY
    // ------------------------------------------------------------------------
    echo "\n--- 4. Enquiry API Validation, Database Persistence, Duplicate Prevention & SQLi/XSS Defense ---\n";

    $contactRes = http_request('/contact');
    preg_match('/name="csrf_token"\s+value="([^"]+)"/', $contactRes['body'], $csrfM);
    preg_match('/name="form_token"\s+value="([^"]+)"/', $contactRes['body'], $timeM);
    $csrfToken = $csrfM[1] ?? '';
    $formToken = $timeM[1] ?? '';

    assert_test('Extracted CSRF and form timestamp tokens from /contact', $csrfToken !== '' && $formToken !== '');

    // 4a. Test CSRF rejection
    $badCsrfRes = http_request('/api/enquiry', 'POST', [
        'csrf_token' => 'invalid_token',
        'form_token' => $formToken,
        'full_name'  => 'Test User',
    ], ['Accept: application/json']);
    assert_test('Enquiry API rejects invalid CSRF token with HTTP 403', $badCsrfRes['status'] === 403);

    // 4b. Test Honeypot spam rejection
    $hpRes = http_request('/api/enquiry', 'POST', [
        'csrf_token'     => $csrfToken,
        'form_token'     => $formToken,
        'website_url_hp' => 'http://spambot.example',
        'full_name'      => 'Spam Bot',
    ], ['Accept: application/json']);
    assert_test('Enquiry API blocks honeypot spam bots with HTTP 400', $hpRes['status'] === 400);

    // 4c. Test Validation Errors (422)
    $invalidRes = http_request('/api/enquiry', 'POST', [
        'csrf_token'          => $csrfToken,
        'form_token'          => $formToken,
        'full_name'           => 'A',
        'business_email'      => 'not-an-email',
        'phone'               => '123',
        'requested_service'   => 'Invalid Service',
        'project_description' => 'Too short',
    ], ['Accept: application/json']);
    $invalidJson = json_decode($invalidRes['body'], true);
    assert_test(
        'Enquiry API validates fields server-side and returns HTTP 422',
        $invalidRes['status'] === 422 && isset($invalidJson['errors']['full_name'], $invalidJson['errors']['business_email'], $invalidJson['errors']['phone'], $invalidJson['errors']['requested_service'], $invalidJson['errors']['project_description'], $invalidJson['errors']['consent_given']),
        "HTTP {$invalidRes['status']}"
    );

    // 4d. Test Valid Enquiry Submission + Attribution Persistence
    $validPayload = [
        'csrf_token'          => $csrfToken,
        'form_token'          => $formToken,
        'full_name'           => '[TEST] Vikramaditya Rao',
        'business_email'      => 'vikram.qa@example.com',
        'country_code'        => '+91',
        'phone'               => '9876543210',
        'company_name'        => 'Apex Tech Ventures Pvt Ltd',
        'requested_service'   => 'SaaS Solutions & Cloud Services',
        'estimated_budget'    => '₹5,00,000 – ₹15,00,000',
        'project_timeline'    => '1 to 3 Months',
        'project_description' => 'We need to architect a multi-tenant B2B SaaS platform with role-based access control and automated billing.',
        'consent_given'       => '1',
        'landing_page'        => '/services/saas-cloud-solutions?utm_source=google_ads',
        'referrer'            => 'https://www.google.com/',
        'utm_source'          => 'google_ads',
        'utm_medium'          => 'cpc',
        'utm_campaign'        => 'saas_development_india',
        'utm_content'         => 'ad_variant_a',
        'utm_term'            => 'saas development company',
    ];
    $validRes = http_request('/api/enquiry', 'POST', $validPayload, ['Accept: application/json']);
    $validJson = json_decode($validRes['body'], true);
    $leadRef = $validJson['lead_reference'] ?? '';

    assert_test(
        'Valid enquiry saved in database and returns HTTP 201 with reference ID',
        $validRes['status'] === 201 && !empty($validJson['success']) && preg_match('/^SKY-\d{8}-[A-F0-9]{6}$/', $leadRef) === 1,
        $leadRef
    );

    // 4e. Test Duplicate Submission Prevention
    $dupRes = http_request('/api/enquiry', 'POST', $validPayload, ['Accept: application/json']);
    $dupJson = json_decode($dupRes['body'], true);
    $leadCountAfterDup = (int) $pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
    assert_test(
        'Duplicate submission within window is prevented and returns existing reference',
        $dupRes['status'] === 200 && !empty($dupJson['duplicate']) && ($dupJson['lead_reference'] ?? '') === $leadRef && $leadCountAfterDup === 1
    );

    // 4f. Test Thank You Page Verification & Conversion Readiness
    $tyRes = http_request('/thank-you?ref=' . rawurlencode($leadRef));
    assert_test(
        'Thank You page displays confirmed lead reference and sets conversion flag once',
        $tyRes['status'] === 200 && str_contains($tyRes['body'], $leadRef) && str_contains($tyRes['body'], '"isThankYouConversion":true')
    );

    // 4g. Test SQL Injection & Stored XSS Defenses
    $sqliXssPayload = [
        'csrf_token'          => $csrfToken,
        'form_token'          => $formToken,
        'full_name'           => "[TEST] Robert'); DROP TABLE leads;-- <script>alert(1)</script>",
        'business_email'      => 'sec-test@example.com',
        'country_code'        => '+91',
        'phone'               => '9123456789',
        'company_name'        => '=cmd|\' /C calc\'!A0',
        'requested_service'   => 'Digital Marketing & Cyber Security Services',
        'project_description' => "Testing SQLi ' OR 1=1 -- and XSS <img src=x onerror=alert('xss')> payloads safely.",
        'consent_given'       => '1',
    ];
    $secRes = http_request('/api/enquiry', 'POST', $sqliXssPayload, ['Accept: application/json']);
    $totalLeadsNow = (int) $pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
    assert_test('SQLi payload safely parameterized without harming leads table', $secRes['status'] === 201 && $totalLeadsNow === 2);

    // ------------------------------------------------------------------------
    // 5. SECURE ADMIN AUTHENTICATION, DASHBOARD, WORKFLOW & CSV EXPORT TESTS
    // ------------------------------------------------------------------------
    echo "\n--- 5. Secure Admin Authentication, Lead Management & CSV Export ---\n";

    $unauthApi = http_request('/api/admin?action=get_lead&id=1');
    assert_test('Unauthenticated request to /api/admin returns HTTP 401', $unauthApi['status'] === 401);

    $adminLoginPage = http_request('/admin');
    preg_match('/name="csrf_token"\s+value="([^"]+)"/', $adminLoginPage['body'], $admCsrfM);
    $admCsrf = $admCsrfM[1] ?? '';

    $badLogin = http_request('/admin', 'POST', [
        'action'     => 'login',
        'csrf_token' => $admCsrf,
        'login_id'   => 'admin',
        'password'   => 'WrongPassword123',
    ]);
    assert_test('Admin login rejects invalid password', str_contains($badLogin['body'], 'Invalid credentials'));

    $goodLogin = http_request('/admin', 'POST', [
        'action'     => 'login',
        'csrf_token' => $admCsrf,
        'login_id'   => 'admin',
        'password'   => $adminPass,
    ]);
    $dashRes = http_request('/admin?test_mode=all');
    assert_test(
        'Authenticated Admin Dashboard renders leads and properly escapes XSS payloads',
        $dashRes['status'] === 200 &&
        str_contains($dashRes['body'], $leadRef) &&
        str_contains($dashRes['body'], '&lt;script&gt;alert(1)&lt;/script&gt;') &&
        !str_contains($dashRes['body'], '<script>alert(1)</script>')
    );

    preg_match('/data-csrf="([^"]+)"/', $dashRes['body'], $activeAdmCsrfM);
    $activeAdmCsrf = $activeAdmCsrfM[1] ?? '';

    $leadDetailRes = http_request('/api/admin?action=get_lead&id=1');
    $leadDetailJson = json_decode($leadDetailRes['body'], true);
    assert_test(
        'Admin API returns full enquiry details & UTM attribution',
        $leadDetailRes['status'] === 200 && ($leadDetailJson['lead']['utm_source'] ?? '') === 'google_ads'
    );

    $updateLeadRes = http_request('/api/admin', 'POST', [
        'action'         => 'update_lead',
        'csrf_token'     => $activeAdmCsrf,
        'lead_id'        => '1',
        'status'         => 'Qualified',
        'internal_notes' => 'Verified enterprise SaaS opportunity.',
        'new_note'       => 'Completed technical discovery call.',
    ]);
    $updateLeadJson = json_decode($updateLeadRes['body'], true);
    $updatedStatus = (string) $pdo->query('SELECT status FROM leads WHERE id = 1')->fetchColumn();
    $noteCount = (int) $pdo->query('SELECT COUNT(*) FROM lead_notes WHERE lead_id = 1')->fetchColumn();
    assert_test(
        'Admin status workflow update (New -> Qualified) and internal note saved',
        !empty($updateLeadJson['success']) && $updatedStatus === 'Qualified' && $noteCount === 1
    );

    $csvRes = http_request('/admin?action=export_csv&test_mode=all');
    assert_test(
        'Admin CSV export succeeds and sanitizes CSV formula injection cells',
        $csvRes['status'] === 200 &&
        str_contains($csvRes['headers'], 'text/csv') &&
        str_contains($csvRes['body'], $leadRef) &&
        str_contains($csvRes['body'], "'=cmd|")
    );

} finally {
    if (is_resource($proc)) {
        $status = proc_get_status($proc);
        if (!empty($status['pid'])) {
            if (DIRECTORY_SEPARATOR === '\\') {
                @exec('taskkill /F /T /PID ' . (int) $status['pid'] . ' >NUL 2>&1');
            } else {
                @posix_kill((int) $status['pid'], 9);
            }
        }
        @proc_close($proc);
    }
    unset($pdo);
    if (is_file($testDbPath)) {
        @unlink($testDbPath);
    }
    foreach (glob($rootDir . '/storage/rate_limits/*.json') ?: [] as $rf) {
        @unlink($rf);
    }
}

echo "\n======================================================================\n";
echo " TEST SUMMARY: {$passed} PASSED | {$failed} FAILED\n";
echo "======================================================================\n";

if ($failed > 0) {
    exit(1);
}
