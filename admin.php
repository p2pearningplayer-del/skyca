<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — SECURE ADMIN & ADS CRM PORTAL (admin.php)
 * Server-authenticated lead CRM pipeline, Google/Meta Ads attribution analytics,
 * external CRM webhook configuration, CSV export, and company settings.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/services_data.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/seo.php';

skyca_start_session();

header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Robots-Tag: noindex, nofollow');

$tab    = (string) ($_GET['tab'] ?? 'leads');
$action = (string) ($_GET['action'] ?? $_POST['action'] ?? '');

// 1. HANDLE LOGOUT
if ($tab === 'logout' || $action === 'logout') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }
    @session_destroy();
    header('Location: /admin?logged_out=1');
    exit;
}

// 2. HANDLE LOGIN SUBMISSION (POST)
$loginError = '';
$dbReady    = true;
$adminCount = 0;

try {
    $pdo = skyca_db();
    $cntStmt = $pdo->query('SELECT COUNT(*) FROM admin_users WHERE is_active = 1');
    $adminCount = (int) ($cntStmt ? $cntStmt->fetchColumn() : 0);
} catch (Throwable $e) {
    $dbReady = false;
}

if (!skyca_is_admin_logged_in() && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && $action === 'login') {
    $csrf = (string) ($_POST['csrf_token'] ?? '');
    if (!skyca_verify_csrf($csrf)) {
        $loginError = 'Invalid security token. Please reload the page and try again.';
    } else {
        $rate = skyca_check_rate_limit('admin_login', 5, 900, 900, true);
        if (!$rate['allowed']) {
            $loginError = 'Too many failed login attempts. Please wait 15 minutes before trying again.';
        } elseif (!$dbReady) {
            $loginError = 'Database service is unavailable.';
        } else {
            $loginInput = trim((string) ($_POST['login_id'] ?? ''));
            $password   = (string) ($_POST['password'] ?? '');

            if ($loginInput === '' || $password === '') {
                $loginError = 'Please enter both your username/email and password.';
            } else {
                $stmt = $pdo->prepare('
                    SELECT id, username, email, password_hash, full_name, is_active
                    FROM admin_users
                    WHERE (username = :u OR email = :e) AND is_active = 1
                    LIMIT 1
                ');
                $stmt->execute([':u' => $loginInput, ':e' => strtolower($loginInput)]);
                $user = $stmt->fetch();

                if ($user && password_verify($password, (string) $user['password_hash'])) {
                    skyca_reset_rate_limit('admin_login');
                    session_regenerate_id(true);
                    $_SESSION['skyca_admin_id']       = (int) $user['id'];
                    $_SESSION['skyca_admin_username'] = (string) $user['username'];
                    $_SESSION['skyca_admin_email']    = (string) $user['email'];
                    $_SESSION['skyca_admin_name']     = (string) $user['full_name'];
                    $_SESSION['skyca_csrf_token']     = bin2hex(random_bytes(32));

                    $updLogin = $pdo->prepare('UPDATE admin_users SET last_login_at = :now WHERE id = :id');
                    $updLogin->execute([':now' => date('Y-m-d H:i:s'), ':id' => $user['id']]);

                    header('Location: /admin');
                    exit;
                } else {
                    $loginError = 'Invalid credentials. Please verify your username/email and password.';
                }
            }
        }
    }
}

// 3. RENDER LOGIN SCREEN IF NOT AUTHENTICATED
if (!skyca_is_admin_logged_in()) {
    $csrfToken = skyca_csrf_token();
    ?>
    <!DOCTYPE html>
    <html lang="en-IN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex, nofollow">
        <title>Admin &amp; Ads CRM Login | <?php echo skyca_e(COMPANY_LEGAL_NAME); ?></title>
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?php echo skyca_e(skyca_asset('assets/css/styles.css')); ?>">
    </head>
    <body class="admin-login-body">
        <main class="admin-login-wrapper hero-3d-stage">
            <div class="hero-bg-grid" aria-hidden="true"></div>
            <div class="hero-3d-orb hero-3d-orb-1" aria-hidden="true"></div>
            <div class="hero-3d-orb hero-3d-orb-2" aria-hidden="true"></div>
            <div class="admin-login-card card-3d-surface">
                <div class="admin-login-brand">
                    <a href="/">
                        <img src="/assets/images/skyca-logo-horizontal.png" alt="<?php echo skyca_e(COMPANY_LEGAL_NAME); ?>" width="240" height="65">
                    </a>
                    <h1>Ads &amp; Lead CRM Portal</h1>
                    <p>Authorized Administrator Access Only</p>
                </div>

                <?php if (!empty($_GET['logged_out'])): ?>
                    <div class="form-alert alert-success">You have been securely logged out.</div>
                <?php endif; ?>

                <?php if ($loginError !== ''): ?>
                    <div class="form-alert alert-error" role="alert"><?php echo skyca_e($loginError); ?></div>
                <?php endif; ?>

                <form method="POST" action="/admin" class="admin-login-form" novalidate>
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="csrf_token" value="<?php echo skyca_e($csrfToken); ?>">

                    <div class="form-group">
                        <label for="login_id" class="form-label">Username or Admin Email</label>
                        <input
                            type="text"
                            id="login_id"
                            name="login_id"
                            class="form-control"
                            placeholder="admin or info@skycadigital.com"
                            required
                            autocomplete="username"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            placeholder="Enter administrator password"
                            required
                            autocomplete="current-password"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg mt-4">
                        Sign In to CRM Dashboard
                    </button>
                </form>

                <div class="admin-login-footer">
                    <a href="/">&larr; Return to <?php echo skyca_e(SITE_URL); ?></a>
                </div>
            </div>
        </main>
    </body>
    </html>
    <?php
    exit;
}

// 4. AUTHENTICATED ADMIN & CRM LOGIC
$currentAdmin   = skyca_current_admin();
$csrfToken      = skyca_csrf_token();
$serviceOptions = skyca_get_service_options();
$settings       = skyca_get_settings();

$searchQuery    = skyca_clean_input((string) ($_GET['q'] ?? ''), 120, false);
$filterStatus   = skyca_clean_input((string) ($_GET['status'] ?? ''), 30, false);
$filterService  = skyca_clean_input((string) ($_GET['service'] ?? ''), 150, false);
$filterChannel  = skyca_clean_input((string) ($_GET['ad_channel'] ?? ''), 80, false);
$filterPriority = skyca_clean_input((string) ($_GET['priority'] ?? ''), 20, false);
$dateFrom       = skyca_clean_input((string) ($_GET['date_from'] ?? ''), 20, false);
$dateTo         = skyca_clean_input((string) ($_GET['date_to'] ?? ''), 20, false);
$testMode       = skyca_clean_input((string) ($_GET['test_mode'] ?? 'live'), 15, false);
$page           = max(1, (int) ($_GET['page'] ?? 1));
$perPage        = 20;

$whereClauses = ['1=1'];
$params       = [];

if ($testMode === 'live') {
    $whereClauses[] = 'is_test = 0';
} elseif ($testMode === 'test') {
    $whereClauses[] = 'is_test = 1';
}

$validStatuses   = ['New', 'Contacted', 'Qualified', 'Proposal Sent', 'Converted', 'Closed'];
$validPriorities = ['Hot', 'Warm', 'Normal'];

if ($filterStatus !== '' && in_array($filterStatus, $validStatuses, true)) {
    $whereClauses[]    = 'status = :status';
    $params[':status'] = $filterStatus;
}

if ($filterPriority !== '' && in_array($filterPriority, $validPriorities, true)) {
    $whereClauses[]      = 'lead_priority = :priority';
    $params[':priority'] = $filterPriority;
}

if ($filterChannel !== '') {
    $whereClauses[]        = 'ad_channel = :ad_channel';
    $params[':ad_channel'] = $filterChannel;
}

if ($filterService !== '') {
    $whereClauses[]     = 'requested_service = :service';
    $params[':service'] = $filterService;
}

if ($dateFrom !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
    $whereClauses[]       = 'created_at >= :date_from';
    $params[':date_from'] = $dateFrom . ' 00:00:00';
}

if ($dateTo !== '' && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
    $whereClauses[]     = 'created_at <= :date_to';
    $params[':date_to'] = $dateTo . ' 23:59:59';
}

if ($searchQuery !== '') {
    $whereClauses[] = '(lead_reference LIKE :q1 OR full_name LIKE :q2 OR business_email LIKE :q3 OR phone LIKE :q4 OR company_name LIKE :q5 OR utm_campaign LIKE :q6)';
    $likeVal = '%' . $searchQuery . '%';
    $params[':q1'] = $likeVal;
    $params[':q2'] = $likeVal;
    $params[':q3'] = $likeVal;
    $params[':q4'] = $likeVal;
    $params[':q5'] = $likeVal;
    $params[':q6'] = $likeVal;
}

$whereSql = implode(' AND ', $whereClauses);

// 5. HANDLE AUTHORIZED CSV EXPORT
if ($action === 'export_csv') {
    $exportStmt = $pdo->prepare("
        SELECT
            lead_reference, status, lead_priority, deal_value, ad_channel,
            full_name, business_email, phone, company_name, requested_service,
            estimated_budget, project_timeline, next_followup_at, assigned_to,
            project_description, landing_page, referrer, utm_source, utm_medium,
            utm_campaign, utm_content, utm_term, gclid, fbclid, consent_timestamp,
            is_test, internal_notes, created_at, updated_at
        FROM leads
        WHERE {$whereSql}
        ORDER BY id DESC
        LIMIT 5000
    ");
    $exportStmt->execute($params);

    $filename = 'skyca_crm_leads_export_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");

    fputcsv($out, [
        'Lead Reference', 'CRM Status', 'Priority', 'Deal Value (INR)', 'Ad Channel',
        'Full Name', 'Business Email', 'Phone', 'Company Name', 'Requested Service',
        'Estimated Budget', 'Project Timeline', 'Next Follow-up Date', 'Assigned To',
        'Project Description', 'Landing Page', 'Referrer', 'UTM Source', 'UTM Medium',
        'UTM Campaign', 'UTM Content', 'UTM Term', 'GCLID', 'FBCLID', 'Consent Timestamp',
        'Is Test Lead', 'Internal Notes', 'Created At', 'Updated At'
    ]);

    while ($row = $exportStmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($out, [
            skyca_csv_safe($row['lead_reference']),
            skyca_csv_safe($row['status']),
            skyca_csv_safe($row['lead_priority']),
            skyca_csv_safe((string) ($row['deal_value'] ?? '0')),
            skyca_csv_safe($row['ad_channel']),
            skyca_csv_safe($row['full_name']),
            skyca_csv_safe($row['business_email']),
            skyca_csv_safe($row['phone']),
            skyca_csv_safe($row['company_name']),
            skyca_csv_safe($row['requested_service']),
            skyca_csv_safe($row['estimated_budget']),
            skyca_csv_safe($row['project_timeline']),
            skyca_csv_safe($row['next_followup_at']),
            skyca_csv_safe($row['assigned_to']),
            skyca_csv_safe($row['project_description']),
            skyca_csv_safe($row['landing_page']),
            skyca_csv_safe($row['referrer']),
            skyca_csv_safe($row['utm_source']),
            skyca_csv_safe($row['utm_medium']),
            skyca_csv_safe($row['utm_campaign']),
            skyca_csv_safe($row['utm_content']),
            skyca_csv_safe($row['utm_term']),
            skyca_csv_safe($row['gclid']),
            skyca_csv_safe($row['fbclid']),
            skyca_csv_safe($row['consent_timestamp']),
            ((int) $row['is_test'] === 1) ? 'TEST' : 'LIVE',
            skyca_csv_safe($row['internal_notes']),
            skyca_csv_safe($row['created_at']),
            skyca_csv_safe($row['updated_at']),
        ]);
    }
    fclose($out);
    exit;
}

// 6. FETCH DASHBOARD KPI STATS & PAGINATED LEADS
$kpiCondition = ($testMode === 'live') ? 'WHERE is_test = 0' : (($testMode === 'test') ? 'WHERE is_test = 1' : '');
$statsStmt = $pdo->query("
    SELECT
        COUNT(*) AS total_count,
        SUM(CASE WHEN status = 'New' THEN 1 ELSE 0 END) AS new_count,
        SUM(CASE WHEN status = 'Contacted' THEN 1 ELSE 0 END) AS contacted_count,
        SUM(CASE WHEN status IN ('Qualified', 'Proposal Sent') THEN 1 ELSE 0 END) AS qualified_count,
        SUM(CASE WHEN status = 'Converted' THEN 1 ELSE 0 END) AS converted_count,
        SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) AS closed_count,
        SUM(CASE WHEN lead_priority = 'Hot' THEN 1 ELSE 0 END) AS hot_count,
        SUM(CASE WHEN ad_channel IN ('Google Ads', 'Meta Ads', 'Microsoft Ads', 'LinkedIn Ads') THEN 1 ELSE 0 END) AS paid_ads_count,
        SUM(CASE WHEN status != 'Closed' THEN COALESCE(deal_value, 0) ELSE 0 END) AS pipeline_value,
        SUM(CASE WHEN status = 'Converted' THEN COALESCE(deal_value, 0) ELSE 0 END) AS won_value
    FROM leads
    {$kpiCondition}
");
$kpis = $statsStmt ? $statsStmt->fetch() : [];

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM leads WHERE {$whereSql}");
$countStmt->execute($params);
$totalFiltered = (int) $countStmt->fetchColumn();
$totalPages    = max(1, (int) ceil($totalFiltered / $perPage));
$page          = min($page, $totalPages);
$offset        = ($page - 1) * $perPage;

$leadsStmt = $pdo->prepare("
    SELECT
        id, lead_reference, full_name, business_email, phone, company_name,
        requested_service, estimated_budget, project_timeline, utm_source,
        utm_campaign, ad_channel, lead_priority, deal_value, next_followup_at,
        assigned_to, status, is_test, created_at
    FROM leads
    WHERE {$whereSql}
    ORDER BY id DESC
    LIMIT {$perPage} OFFSET {$offset}
");
$leadsStmt->execute($params);
$leadsList = $leadsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Ads &amp; Sales CRM Dashboard | <?php echo skyca_e(COMPANY_LEGAL_NAME); ?></title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo skyca_e(skyca_asset('assets/css/styles.css')); ?>">
</head>
<body class="admin-body" data-csrf="<?php echo skyca_e($csrfToken); ?>">
    <!-- Top Admin Bar -->
    <header class="admin-header">
        <div class="container admin-header-inner">
            <div class="admin-brand">
                <a href="/admin" class="admin-logo-link">
                    <img src="/assets/images/skyca-logo-horizontal.png" alt="Skyca Digital" width="190" height="51">
                </a>
                <span class="admin-portal-badge">Ads &amp; Lead CRM</span>
            </div>

            <nav class="admin-nav" aria-label="Admin Navigation">
                <a href="/admin?tab=leads" class="admin-nav-link <?php echo $tab === 'leads' ? 'active' : ''; ?>">CRM Pipeline &amp; Leads</a>
                <a href="/admin?tab=crm_ads" class="admin-nav-link <?php echo $tab === 'crm_ads' ? 'active' : ''; ?>">Ads &amp; CRM Hub</a>
                <a href="/admin?tab=seo" class="admin-nav-link <?php echo $tab === 'seo' ? 'active' : ''; ?>">SEO &amp; Search Hub</a>
                <a href="/admin?tab=settings" class="admin-nav-link <?php echo $tab === 'settings' ? 'active' : ''; ?>">Company &amp; DB Settings</a>
                <a href="/admin?tab=password" class="admin-nav-link <?php echo $tab === 'password' ? 'active' : ''; ?>">Change Password</a>
            </nav>

            <div class="admin-user-actions">
                <span class="admin-user-pill"><?php echo skyca_e($currentAdmin['username'] ?? 'admin'); ?></span>
                <a href="/" target="_blank" class="btn btn-outline btn-sm">View Website</a>
                <a href="/admin?tab=logout" class="btn btn-secondary btn-sm">Log Out</a>
            </div>
        </div>
    </header>

    <main class="container admin-main">
        <?php if ($tab === 'leads'): ?>
            <!-- EXECUTIVE CRM & ADS KPI CARDS -->
            <section class="admin-kpi-grid" aria-label="CRM and Ads Summary Metrics">
                <div class="kpi-card">
                    <span class="kpi-label">Total Enquiries</span>
                    <strong class="kpi-value"><?php echo (int) ($kpis['total_count'] ?? 0); ?></strong>
                    <span class="kpi-sub">New: <?php echo (int) ($kpis['new_count'] ?? 0); ?></span>
                </div>
                <div class="kpi-card kpi-hot">
                    <span class="kpi-label">Hot Priority Leads</span>
                    <strong class="kpi-value"><?php echo (int) ($kpis['hot_count'] ?? 0); ?></strong>
                    <span class="kpi-sub">High-Intent / Urgent</span>
                </div>
                <div class="kpi-card kpi-ads">
                    <span class="kpi-label">Paid Ads Leads</span>
                    <strong class="kpi-value"><?php echo (int) ($kpis['paid_ads_count'] ?? 0); ?></strong>
                    <span class="kpi-sub">Google &amp; Meta Ads</span>
                </div>
                <div class="kpi-card kpi-qualified">
                    <span class="kpi-label">Qualified / Proposal</span>
                    <strong class="kpi-value"><?php echo (int) ($kpis['qualified_count'] ?? 0); ?></strong>
                    <span class="kpi-sub">Contacted: <?php echo (int) ($kpis['contacted_count'] ?? 0); ?></span>
                </div>
                <div class="kpi-card kpi-pipeline">
                    <span class="kpi-label">Active Pipeline</span>
                    <strong class="kpi-value">₹<?php echo number_format((float) ($kpis['pipeline_value'] ?? 0)); ?></strong>
                    <span class="kpi-sub">Open Deal Value</span>
                </div>
                <div class="kpi-card kpi-converted">
                    <span class="kpi-label">Converted (Won)</span>
                    <strong class="kpi-value"><?php echo (int) ($kpis['converted_count'] ?? 0); ?></strong>
                    <span class="kpi-sub">Won: ₹<?php echo number_format((float) ($kpis['won_value'] ?? 0)); ?></span>
                </div>
            </section>

            <!-- CRM SEARCH, ADS CHANNEL FILTER & EXPORT BAR -->
            <section class="admin-filter-card">
                <form method="GET" action="/admin" class="admin-filter-form">
                    <input type="hidden" name="tab" value="leads">
                    <div class="admin-filter-grid">
                        <div>
                            <label for="f-q" class="form-label">Search CRM</label>
                            <input type="text" id="f-q" name="q" value="<?php echo skyca_e($searchQuery); ?>" class="form-control" placeholder="Name, email, phone, campaign, ref...">
                        </div>
                        <div>
                            <label for="f-status" class="form-label">Pipeline Stage</label>
                            <select id="f-status" name="status" class="form-control">
                                <option value="">All Stages</option>
                                <?php foreach ($validStatuses as $st): ?>
                                    <option value="<?php echo skyca_e($st); ?>" <?php echo $filterStatus === $st ? 'selected' : ''; ?>><?php echo skyca_e($st); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="f-channel" class="form-label">Ad Channel</label>
                            <select id="f-channel" name="ad_channel" class="form-control">
                                <option value="">All Channels</option>
                                <?php foreach (['Google Ads', 'Meta Ads', 'Microsoft Ads', 'LinkedIn Ads', 'Organic Search', 'Direct / Organic', 'Referral'] as $chOpt): ?>
                                    <option value="<?php echo skyca_e($chOpt); ?>" <?php echo $filterChannel === $chOpt ? 'selected' : ''; ?>><?php echo skyca_e($chOpt); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="f-priority" class="form-label">Lead Priority</label>
                            <select id="f-priority" name="priority" class="form-control">
                                <option value="">All Priorities</option>
                                <?php foreach ($validPriorities as $prOpt): ?>
                                    <option value="<?php echo skyca_e($prOpt); ?>" <?php echo $filterPriority === $prOpt ? 'selected' : ''; ?>><?php echo skyca_e($prOpt); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="f-service" class="form-label">Service Required</label>
                            <select id="f-service" name="service" class="form-control">
                                <option value="">All Services</option>
                                <?php foreach ($serviceOptions as $srvOpt): ?>
                                    <option value="<?php echo skyca_e($srvOpt); ?>" <?php echo $filterService === $srvOpt ? 'selected' : ''; ?>><?php echo skyca_e($srvOpt); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="f-test" class="form-label">Lead Type</label>
                            <select id="f-test" name="test_mode" class="form-control">
                                <option value="live" <?php echo $testMode === 'live' ? 'selected' : ''; ?>>Genuine Leads Only</option>
                                <option value="all" <?php echo $testMode === 'all' ? 'selected' : ''; ?>>All (Include Test Data)</option>
                                <option value="test" <?php echo $testMode === 'test' ? 'selected' : ''; ?>>Test Enquiries Only</option>
                            </select>
                        </div>
                    </div>

                    <div class="admin-filter-actions">
                        <div class="filter-btn-group">
                            <button type="submit" class="btn btn-primary btn-sm">Filter CRM</button>
                            <a href="/admin?tab=leads" class="btn btn-outline btn-sm">Reset</a>
                        </div>
                        <?php
                        $exportQuery = http_build_query([
                            'action'     => 'export_csv',
                            'q'          => $searchQuery,
                            'status'     => $filterStatus,
                            'ad_channel' => $filterChannel,
                            'priority'   => $filterPriority,
                            'service'    => $filterService,
                            'date_from'  => $dateFrom,
                            'date_to'    => $dateTo,
                            'test_mode'  => $testMode,
                        ]);
                        ?>
                        <a href="/admin?<?php echo skyca_e($exportQuery); ?>" class="btn btn-secondary btn-sm">
                            Export CRM Leads to CSV (<?php echo $totalFiltered; ?>)
                        </a>
                    </div>
                </form>
            </section>

            <!-- CRM LEADS DATA TABLE -->
            <section class="admin-table-card">
                <div class="admin-table-responsive">
                    <table class="admin-leads-table">
                        <thead>
                            <tr>
                                <th>Ref ID &amp; Priority</th>
                                <th>Client &amp; Quick Contact</th>
                                <th>Service &amp; Deal Value</th>
                                <th>Ads Channel &amp; Campaign</th>
                                <th>Stage &amp; Follow-up</th>
                                <th>CRM Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($leadsList)): ?>
                                <tr>
                                    <td colspan="6" class="empty-table-cell">
                                        No CRM enquiries match the current filter criteria.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($leadsList as $row): ?>
                                    <?php
                                    $waPhone = preg_replace('/\D+/', '', (string) $row['phone']);
                                    $waText  = rawurlencode("Hello " . $row['full_name'] . ", this is Skyca Digital Private Limited regarding your enquiry (" . $row['lead_reference'] . ") for " . $row['requested_service'] . ".");
                                    ?>
                                    <tr>
                                        <td>
                                            <strong class="lead-ref-mono"><?php echo skyca_e($row['lead_reference']); ?></strong>
                                            <?php if ((int) $row['is_test'] === 1): ?>
                                                <span class="badge-test">TEST</span>
                                            <?php endif; ?>
                                            <div class="mt-1">
                                                <span class="priority-badge priority-<?php echo strtolower(skyca_e($row['lead_priority'] ?? 'normal')); ?>">
                                                    <?php echo skyca_e($row['lead_priority'] ?? 'Normal'); ?>
                                                </span>
                                            </div>
                                            <div class="table-subtext"><?php echo skyca_e($row['created_at']); ?></div>
                                        </td>
                                        <td>
                                            <strong><?php echo skyca_e($row['full_name']); ?></strong>
                                            <?php if (!empty($row['company_name'])): ?>
                                                <div class="table-subtext"><?php echo skyca_e($row['company_name']); ?></div>
                                            <?php endif; ?>
                                            <div class="table-subtext">
                                                <?php echo skyca_e($row['business_email']); ?> &bull; <?php echo skyca_e($row['phone']); ?>
                                            </div>
                                            <div class="crm-quick-actions">
                                                <?php if ($waPhone !== ''): ?>
                                                    <a href="https://wa.me/<?php echo skyca_e($waPhone); ?>?text=<?php echo $waText; ?>" target="_blank" rel="noopener" class="crm-mini-btn crm-wa-btn" title="WhatsApp Follow-up">WhatsApp</a>
                                                <?php endif; ?>
                                                <a href="tel:<?php echo skyca_e($row['phone']); ?>" class="crm-mini-btn" title="Call Client">Call</a>
                                                <a href="mailto:<?php echo skyca_e($row['business_email']); ?>?subject=<?php echo rawurlencode('Regarding Your Enquiry ' . $row['lead_reference'] . ' — Skyca Digital'); ?>" class="crm-mini-btn" title="Email Client">Email</a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="service-cell-title"><?php echo skyca_e($row['requested_service']); ?></div>
                                            <div class="deal-value-tag">
                                                Deal Value: <strong>₹<?php echo number_format((float) ($row['deal_value'] ?? 0)); ?></strong>
                                            </div>
                                            <div class="table-subtext">
                                                Budget: <?php echo skyca_e($row['estimated_budget'] ?: 'N/A'); ?> |
                                                Timeline: <?php echo skyca_e($row['project_timeline'] ?: 'N/A'); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="channel-badge"><?php echo skyca_e($row['ad_channel'] ?: 'Direct / Organic'); ?></span>
                                            <div class="table-subtext">
                                                Source: <?php echo skyca_e($row['utm_source'] ?: 'Direct'); ?>
                                                <?php if (!empty($row['utm_campaign'])): ?>
                                                    <br>Campaign: <strong><?php echo skyca_e($row['utm_campaign']); ?></strong>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-pill status-<?php echo strtolower((string) preg_replace('/\s+/', '-', skyca_e($row['status']))); ?>">
                                                <?php echo skyca_e($row['status']); ?>
                                            </span>
                                            <?php if (!empty($row['next_followup_at'])): ?>
                                                <div class="table-subtext">Follow-up: <strong><?php echo skyca_e($row['next_followup_at']); ?></strong></div>
                                            <?php endif; ?>
                                            <?php if (!empty($row['assigned_to'])): ?>
                                                <div class="table-subtext">Owner: <?php echo skyca_e($row['assigned_to']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-primary btn-sm js-open-lead-modal"
                                                data-lead-id="<?php echo (int) $row['id']; ?>"
                                            >
                                                Manage CRM
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="admin-pagination">
                        <span>Showing page <?php echo $page; ?> of <?php echo $totalPages; ?> (<?php echo $totalFiltered; ?> total records)</span>
                        <div class="pagination-links">
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <?php
                                $pageQuery = http_build_query([
                                    'tab'        => 'leads',
                                    'q'          => $searchQuery,
                                    'status'     => $filterStatus,
                                    'ad_channel' => $filterChannel,
                                    'priority'   => $filterPriority,
                                    'service'    => $filterService,
                                    'test_mode'  => $testMode,
                                    'page'       => $p,
                                ]);
                                ?>
                                <a href="/admin?<?php echo skyca_e($pageQuery); ?>" class="page-num-btn <?php echo $p === $page ? 'active' : ''; ?>">
                                    <?php echo $p; ?>
                                </a>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </section>

            <!-- LEAD CRM MODAL -->
            <div class="admin-modal-backdrop hidden" id="lead-detail-modal" role="dialog" aria-modal="true" aria-labelledby="modal-lead-title">
                <div class="admin-modal-card">
                    <div class="admin-modal-header">
                        <h2 id="modal-lead-title">CRM Lead &amp; Ads Attribution Profile</h2>
                        <button type="button" class="modal-close-btn" id="close-lead-modal-btn" aria-label="Close modal">&times;</button>
                    </div>
                    <div class="admin-modal-body" id="modal-lead-body">
                        <p>Loading CRM lead profile...</p>
                    </div>
                </div>
            </div>

        <?php elseif ($tab === 'crm_ads'): ?>
            <?php
            // Fetch Ad Channel Attribution Breakdown
            $channelStmt = $pdo->query("
                SELECT
                    ad_channel,
                    COUNT(*) AS leads_count,
                    SUM(CASE WHEN lead_priority = 'Hot' THEN 1 ELSE 0 END) AS hot_count,
                    SUM(CASE WHEN status = 'Converted' THEN 1 ELSE 0 END) AS converted_count,
                    SUM(COALESCE(deal_value, 0)) AS total_deal_value
                FROM leads
                {$kpiCondition}
                GROUP BY ad_channel
                ORDER BY leads_count DESC
            ");
            $channelStats = $channelStmt ? $channelStmt->fetchAll() : [];

            // Fetch Top UTM Campaigns Breakdown
            $campaignStmt = $pdo->query("
                SELECT
                    COALESCE(NULLIF(utm_campaign, ''), '(Direct / Unspecified Campaign)') AS campaign_name,
                    COALESCE(NULLIF(utm_source, ''), 'Direct') AS source_name,
                    COALESCE(NULLIF(utm_medium, ''), '-') AS medium_name,
                    COUNT(*) AS leads_count,
                    SUM(CASE WHEN status = 'Converted' THEN 1 ELSE 0 END) AS converted_count,
                    SUM(COALESCE(deal_value, 0)) AS pipeline_value
                FROM leads
                {$kpiCondition}
                GROUP BY campaign_name, source_name, medium_name
                ORDER BY leads_count DESC
                LIMIT 15
            ");
            $campaignStats = $campaignStmt ? $campaignStmt->fetchAll() : [];
            ?>

            <!-- 1. AD CHANNEL ROI & ATTRIBUTION ANALYTICS -->
            <section class="admin-panel-card">
                <h2>Ads Attribution &amp; Channel ROI Analytics</h2>
                <p class="text-muted mb-4">
                    Real-time breakdown of incoming business enquiries by advertising network (Google Ads <code>gclid</code>, Meta Ads <code>fbclid</code>, LinkedIn Ads, Organic Search, and Direct traffic).
                </p>

                <div class="admin-table-responsive">
                    <table class="admin-leads-table">
                        <thead>
                            <tr>
                                <th>Acquisition Channel</th>
                                <th>Total Leads</th>
                                <th>Hot Leads 🔥</th>
                                <th>Converted (Won)</th>
                                <th>Conversion Rate</th>
                                <th>Pipeline Value (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($channelStats)): ?>
                                <tr>
                                    <td colspan="6" class="empty-table-cell">No live campaign enquiries recorded yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($channelStats as $ch): ?>
                                    <?php
                                    $cnt  = (int) $ch['leads_count'];
                                    $conv = (int) $ch['converted_count'];
                                    $rate = $cnt > 0 ? round(($conv / $cnt) * 100, 1) : 0.0;
                                    ?>
                                    <tr>
                                        <td><span class="channel-badge"><?php echo skyca_e($ch['ad_channel']); ?></span></td>
                                        <td><strong><?php echo $cnt; ?></strong></td>
                                        <td><?php echo (int) $ch['hot_count']; ?></td>
                                        <td><?php echo $conv; ?></td>
                                        <td><?php echo $rate; ?>%</td>
                                        <td><strong>₹<?php echo number_format((float) $ch['total_deal_value']); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- 2. UTM CAMPAIGN PERFORMANCE TABLE -->
            <section class="admin-panel-card">
                <h2>UTM Campaign Performance Breakdown</h2>
                <p class="text-muted mb-4">
                    Use UTM parameters on your Google Ads and Meta Ads destination URLs (e.g., <code>https://skycadigital.com/services/software-development?utm_source=google_ads&amp;utm_medium=cpc&amp;utm_campaign=custom_software_q4</code>) to track campaign-level ROI automatically.
                </p>

                <div class="admin-table-responsive">
                    <table class="admin-leads-table">
                        <thead>
                            <tr>
                                <th>UTM Campaign</th>
                                <th>UTM Source</th>
                                <th>UTM Medium</th>
                                <th>Leads Captured</th>
                                <th>Converted</th>
                                <th>Pipeline Value (₹)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($campaignStats)): ?>
                                <tr>
                                    <td colspan="6" class="empty-table-cell">No UTM campaign data recorded yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($campaignStats as $cmp): ?>
                                    <tr>
                                        <td><strong><?php echo skyca_e($cmp['campaign_name']); ?></strong></td>
                                        <td><?php echo skyca_e($cmp['source_name']); ?></td>
                                        <td><?php echo skyca_e($cmp['medium_name']); ?></td>
                                        <td><strong><?php echo (int) $cmp['leads_count']; ?></strong></td>
                                        <td><?php echo (int) $cmp['converted_count']; ?></td>
                                        <td><strong>₹<?php echo number_format((float) $cmp['pipeline_value']); ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- 3. ADS PIXELS & EXTERNAL CRM WEBHOOK INTEGRATION -->
            <section class="admin-panel-card">
                <h2>Google Ads, Meta Pixel &amp; External CRM Webhook Integration</h2>
                <p class="text-muted mb-6">
                    Configure your Google Analytics 4, Google Ads Conversion Tracking, Meta Pixel, and real-time External CRM Webhook (compatible with Zoho CRM, HubSpot, LeadSquared, Pipedrive, Make, and Zapier).
                </p>

                <div id="admin-ads-crm-alert" class="form-alert hidden" role="alert"></div>

                <form id="admin-ads-crm-form">
                    <input type="hidden" name="action" value="save_ads_crm_settings">
                    <input type="hidden" name="csrf_token" value="<?php echo skyca_e($csrfToken); ?>">

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Google Analytics 4 Measurement ID</label>
                            <input type="text" name="google_analytics_id" class="form-control" value="<?php echo skyca_e($settings['google_analytics_id']); ?>" placeholder="G-XXXXXXXXXX">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Google Search Console Verification Code</label>
                            <input type="text" name="google_site_verification" class="form-control" value="<?php echo skyca_e($settings['google_site_verification']); ?>" placeholder="Meta tag verification token">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Google Ads Conversion ID</label>
                            <input type="text" name="google_ads_conversion_id" class="form-control" value="<?php echo skyca_e($settings['google_ads_conversion_id']); ?>" placeholder="AW-XXXXXXXXX">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Google Ads Conversion Label</label>
                            <input type="text" name="google_ads_conversion_label" class="form-control" value="<?php echo skyca_e($settings['google_ads_conversion_label']); ?>" placeholder="AbCdEfGhIjKlMnOpQr">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Meta (Facebook / Instagram) Pixel ID</label>
                            <input type="text" name="meta_pixel_id" class="form-control" value="<?php echo skyca_e($settings['meta_pixel_id']); ?>" placeholder="123456789012345">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Meta Conversions API (CAPI) Token (Optional)</label>
                            <input type="password" name="meta_capi_token" class="form-control" value="<?php echo skyca_e($settings['meta_capi_token']); ?>" placeholder="EAAB...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">External CRM Webhook URL (Zoho / HubSpot / Zapier / Make)</label>
                            <input type="url" id="crm-webhook-url-input" name="crm_webhook_url" class="form-control" value="<?php echo skyca_e($settings['crm_webhook_url']); ?>" placeholder="https://hooks.zapier.com/... or https://your-crm.com/webhook">
                        </div>
                        <div class="form-group">
                            <label class="form-label">CRM Webhook Signing Secret (HMAC-SHA256 Header)</label>
                            <input type="text" id="crm-webhook-secret-input" name="crm_webhook_secret" class="form-control" value="<?php echo skyca_e($settings['crm_webhook_secret']); ?>" placeholder="Optional secret for X-Skyca-Signature header">
                        </div>
                    </div>

                    <div class="filter-btn-group mt-4">
                        <button type="submit" class="btn btn-primary">Save Ads &amp; CRM Configuration</button>
                        <button type="button" class="btn btn-secondary" id="test-crm-webhook-btn">Send Test Lead to CRM Webhook</button>
                    </div>
                </form>
            </section>

        <?php elseif ($tab === 'seo'): ?>
            <?php $seoAuditPages = skyca_get_seo_audit_matrix(); ?>

            <!-- 1. TECHNICAL SEO & AI SEARCH ENDPOINTS OVERVIEW -->
            <section class="admin-kpi-grid" aria-label="Technical SEO Health">
                <div class="kpi-card">
                    <span class="kpi-label">Indexable Pages</span>
                    <strong class="kpi-value"><?php echo count($seoAuditPages); ?></strong>
                    <span class="kpi-sub">100% Canonicalized</span>
                </div>
                <div class="kpi-card">
                    <span class="kpi-label">XML + Image Sitemap</span>
                    <strong class="kpi-value"><a href="/sitemap.xml" target="_blank" style="color:var(--brand-primary);">Active &nearr;</a></strong>
                    <span class="kpi-sub"><code>/sitemap.xml</code></span>
                </div>
                <div class="kpi-card">
                    <span class="kpi-label">AI Search (GEO)</span>
                    <strong class="kpi-value"><a href="/llms.txt" target="_blank" style="color:var(--brand-primary);">Active &nearr;</a></strong>
                    <span class="kpi-sub"><code>/llms.txt</code> (ChatGPT / AI)</span>
                </div>
                <div class="kpi-card">
                    <span class="kpi-label">Robots Directive</span>
                    <strong class="kpi-value"><a href="/robots.txt" target="_blank" style="color:var(--brand-primary);">Active &nearr;</a></strong>
                    <span class="kpi-sub"><code>/robots.txt</code></span>
                </div>
                <div class="kpi-card">
                    <span class="kpi-label">JSON-LD Entities</span>
                    <strong class="kpi-value">7 Types</strong>
                    <span class="kpi-sub">Org, Service, FAQ, ItemList</span>
                </div>
                <div class="kpi-card">
                    <span class="kpi-label">Regional Hreflang</span>
                    <strong class="kpi-value">en-IN</strong>
                    <span class="kpi-sub">+ Global <code>x-default</code></span>
                </div>
            </section>

            <!-- 2. SEARCH CONSOLE VERIFICATION & GLOBAL SEO META CONFIGURATION -->
            <section class="admin-panel-card">
                <h2>Search Engine Verification &amp; Global SEO Metadata</h2>
                <p class="text-muted mb-6">
                    Configure Google Search Console, Bing Webmaster Tools, Homepage Title/Description overrides, and Global B2B Focus Keywords for <strong><?php echo skyca_e(SITE_URL); ?></strong>.
                </p>

                <div id="admin-seo-alert" class="form-alert hidden" role="alert"></div>

                <form id="admin-seo-form">
                    <input type="hidden" name="action" value="save_settings">
                    <input type="hidden" name="csrf_token" value="<?php echo skyca_e($csrfToken); ?>">

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Google Search Console Verification Code (<code>google-site-verification</code>)</label>
                            <input type="text" name="google_site_verification" class="form-control" value="<?php echo skyca_e($settings['google_site_verification']); ?>" placeholder="Paste token from Google Search Console HTML tag">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Bing Webmaster Verification Code (<code>msvalidate.01</code>)</label>
                            <input type="text" name="bing_site_verification" class="form-control" value="<?php echo skyca_e($settings['bing_site_verification']); ?>" placeholder="Paste token from Bing Webmaster Tools">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Homepage SEO Title Override <span class="optional-tag">(Leave blank to use default optimized title)</span></label>
                        <input type="text" name="seo_home_title" class="form-control" value="<?php echo skyca_e($settings['seo_home_title']); ?>" placeholder="Skyca Digital Private Limited | IT Services, Custom Software, Web, Cloud &amp; AI Solutions">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Homepage Meta Description Override <span class="optional-tag">(Recommended: 140–165 characters)</span></label>
                        <textarea name="seo_home_description" rows="2" class="form-control" placeholder="Technology Solutions That Move Your Business Forward. Skyca Digital Private Limited delivers custom software development, web &amp; mobile apps, SaaS &amp; cloud architecture, AI integration, cybersecurity, data analytics, and e-commerce solutions."><?php echo skyca_e($settings['seo_home_description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Global B2B Focus Keywords (Comma-Separated)</label>
                        <textarea name="seo_default_keywords" rows="2" class="form-control"><?php echo skyca_e($settings['seo_default_keywords']); ?></textarea>
                    </div>

                    <div class="filter-btn-group mt-4">
                        <button type="submit" class="btn btn-primary">Save SEO &amp; Search Console Settings</button>
                        <a href="/sitemap.xml" target="_blank" class="btn btn-secondary">Inspect XML Sitemap</a>
                        <a href="/llms.txt" target="_blank" class="btn btn-outline">Inspect AI llms.txt</a>
                    </div>
                </form>
            </section>

            <!-- 3. LIVE 15-PAGE ON-PAGE SEO & SCHEMA.ORG AUDIT MATRIX -->
            <section class="admin-panel-card">
                <h2>Live On-Page SEO &amp; Schema.org Structured Data Matrix</h2>
                <p class="text-muted mb-4">
                    Every indexable route on <strong><?php echo skyca_e(SITE_URL); ?></strong> with its live Title tag, Meta Description character count, Canonical URL, Sitemap Priority, and active JSON-LD Rich Result schemas.
                </p>

                <div class="admin-table-responsive">
                    <table class="admin-leads-table">
                        <thead>
                            <tr>
                                <th>Route &amp; Page Type</th>
                                <th>SEO Title &amp; Meta Description</th>
                                <th>Length Check</th>
                                <th>Active Schema.org JSON-LD</th>
                                <th>Priority</th>
                                <th>Verify</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($seoAuditPages as $seoRow): ?>
                                <?php
                                $titleLen = mb_strlen($seoRow['title']);
                                $descLen  = mb_strlen($seoRow['description']);
                                $fullUrl  = skyca_canonical_url($seoRow['path']);
                                ?>
                                <tr>
                                    <td>
                                        <a href="<?php echo skyca_e($seoRow['path']); ?>" target="_blank" class="lead-ref-mono">
                                            <strong><?php echo skyca_e($seoRow['path']); ?></strong>
                                        </a>
                                        <div class="table-subtext"><?php echo skyca_e($seoRow['page_type']); ?></div>
                                    </td>
                                    <td>
                                        <div class="service-cell-title"><?php echo skyca_e($seoRow['title']); ?></div>
                                        <div class="table-subtext"><?php echo skyca_e($seoRow['description']); ?></div>
                                    </td>
                                    <td>
                                        <span class="status-pill status-converted">Title: <?php echo $titleLen; ?> ch</span>
                                        <div class="table-subtext mt-2">Desc: <?php echo $descLen; ?> ch</div>
                                    </td>
                                    <td>
                                        <div class="cap-badges">
                                            <?php foreach ($seoRow['schemas'] as $scName): ?>
                                                <span class="channel-badge"><?php echo skyca_e($scName); ?></span>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td><strong><?php echo skyca_e($seoRow['priority']); ?></strong></td>
                                    <td>
                                        <div class="crm-quick-actions">
                                            <a href="<?php echo skyca_e($seoRow['path']); ?>" target="_blank" class="crm-mini-btn">Open Page</a>
                                            <a href="https://search.google.com/test/rich-results?url=<?php echo rawurlencode($fullUrl); ?>" target="_blank" rel="noopener" class="crm-mini-btn">Rich Test</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

        <?php elseif ($tab === 'settings'): ?>
            <?php
            $activeDriver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            $runtimeDbCfg = skyca_load_runtime_db_config();
            ?>
            <section class="admin-panel-card">
                <h2>MySQL Database Connection (<code>u591469534_skyca</code>)</h2>
                <p class="text-muted mb-4">
                    Active Storage Engine:
                    <strong><?php echo $activeDriver === 'mysql' ? 'MySQL (u591469534_skyca — Connected)' : 'Protected Local PDO Database (Ready — Enter Hostinger MySQL credentials below or in .env to switch to MySQL)'; ?></strong>
                </p>

                <div id="admin-db-alert" class="form-alert hidden" role="alert"></div>

                <form id="admin-db-form">
                    <input type="hidden" name="action" value="save_db_config">
                    <input type="hidden" name="csrf_token" value="<?php echo skyca_e($csrfToken); ?>">

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Database Host</label>
                            <input type="text" name="db_host" class="form-control" value="<?php echo skyca_e($runtimeDbCfg['db_host'] ?? skyca_env('DATABASE_HOST', 'localhost')); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Database Name</label>
                            <input type="text" name="db_name" class="form-control" value="<?php echo skyca_e($runtimeDbCfg['db_name'] ?? skyca_env('DATABASE_NAME', 'u591469534_skyca')); ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">MySQL Username (from Hostinger hPanel)</label>
                            <input type="text" name="db_user" class="form-control" value="<?php echo skyca_e($runtimeDbCfg['db_user'] ?? skyca_env('DATABASE_USER', '')); ?>" placeholder="e.g., u591469534_skyca" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">MySQL Password</label>
                            <input type="password" name="db_pass" class="form-control" placeholder="Enter your Hostinger MySQL password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Test &amp; Save MySQL Connection</button>
                </form>
            </section>

            <section class="admin-panel-card">
                <h2>Verified Company Profile &amp; Notification Settings</h2>
                <p class="text-muted mb-6">
                    Update optional verified company details here. Any populated field will automatically display on the About Us, Contact Us, Footer, and Structured Data sections. Leave unverified fields blank.
                </p>

                <div id="admin-settings-alert" class="form-alert hidden" role="alert"></div>

                <form id="admin-settings-form" class="admin-settings-form">
                    <input type="hidden" name="action" value="save_settings">
                    <input type="hidden" name="csrf_token" value="<?php echo skyca_e($csrfToken); ?>">

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label class="form-label">Registered Office Street Address</label>
                            <input type="text" name="company_address" class="form-control" value="<?php echo skyca_e($settings['company_address']); ?>" placeholder="Leave blank until verified">
                        </div>
                        <div class="form-group">
                            <label class="form-label">City</label>
                            <input type="text" name="company_city" class="form-control" value="<?php echo skyca_e($settings['company_city']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">State / Province</label>
                            <input type="text" name="company_state" class="form-control" value="<?php echo skyca_e($settings['company_state']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Postal / PIN Code</label>
                            <input type="text" name="company_postal_code" class="form-control" value="<?php echo skyca_e($settings['company_postal_code']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Corporate Identification Number (CIN)</label>
                            <input type="text" name="company_cin" class="form-control" value="<?php echo skyca_e($settings['company_cin']); ?>" placeholder="Optional verified CIN">
                        </div>
                        <div class="form-group">
                            <label class="form-label">GSTIN</label>
                            <input type="text" name="company_gstin" class="form-control" value="<?php echo skyca_e($settings['company_gstin']); ?>" placeholder="Optional verified GSTIN">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Business Working Hours</label>
                            <input type="text" name="company_working_hours" class="form-control" value="<?php echo skyca_e($settings['company_working_hours']); ?>">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Verified WhatsApp Business Number (with country code)</label>
                            <input type="text" name="whatsapp_number" class="form-control" value="<?php echo skyca_e($settings['whatsapp_number']); ?>" placeholder="e.g., +916002064873 (Leave blank to hide WhatsApp CTA)">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Consultation Booking URL (Calendly / Cal.com)</label>
                            <input type="url" name="consultation_booking_url" class="form-control" value="<?php echo skyca_e($settings['consultation_booking_url']); ?>" placeholder="https://...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Verified LinkedIn Company URL</label>
                            <input type="url" name="social_linkedin_url" class="form-control" value="<?php echo skyca_e($settings['social_linkedin_url']); ?>" placeholder="https://linkedin.com/company/...">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Save Company Settings</button>
                </form>
            </section>

        <?php elseif ($tab === 'password'): ?>
            <section class="admin-panel-card max-w-lg">
                <h2>Change Administrator Password</h2>
                <p class="text-muted mb-6">Ensure your new password is at least 10 characters long and uses a mix of letters, numbers, and symbols.</p>

                <div id="admin-password-alert" class="form-alert hidden" role="alert"></div>

                <form id="admin-password-form">
                    <input type="hidden" name="action" value="change_password">
                    <input type="hidden" name="csrf_token" value="<?php echo skyca_e($csrfToken); ?>">

                    <div class="form-group">
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">New Password (minimum 10 characters)</label>
                        <input type="password" name="new_password" class="form-control" required minlength="10" autocomplete="new-password">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required minlength="10" autocomplete="new-password">
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">Update Password</button>
                </form>
            </section>
        <?php endif; ?>
    </main>

    <script src="<?php echo skyca_e(skyca_asset('assets/js/admin.js')); ?>" defer></script>
</body>
</html>
