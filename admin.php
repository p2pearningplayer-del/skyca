<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — SECURE ADMIN DASHBOARD (admin.php)
 * Server-authenticated lead management, status workflow, CSV export,
 * password management, and optional verified company profile configuration.
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/services_data.php';
require_once __DIR__ . '/includes/security.php';
require_once __DIR__ . '/includes/db.php';

skyca_start_session();

header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Robots-Tag: noindex, nofollow');

$tab    = (string) ($_GET['tab'] ?? 'leads');
$action = (string) ($_GET['action'] ?? $_POST['action'] ?? '');

// ----------------------------------------------------------------------------
// 1. HANDLE LOGOUT
// ----------------------------------------------------------------------------
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

// ----------------------------------------------------------------------------
// 2. HANDLE LOGIN SUBMISSION (POST)
// ----------------------------------------------------------------------------
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
            $loginError = 'Database connection is not yet configured on this server. Check your .env file.';
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

// ----------------------------------------------------------------------------
// 3. RENDER LOGIN SCREEN IF NOT AUTHENTICATED
// ----------------------------------------------------------------------------
if (!skyca_is_admin_logged_in()) {
    $csrfToken = skyca_csrf_token();
    ?>
    <!DOCTYPE html>
    <html lang="en-IN">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex, nofollow">
        <title>Admin Login | <?php echo skyca_e(COMPANY_LEGAL_NAME); ?></title>
        <link rel="icon" type="image/x-icon" href="/favicon.ico">
        <link rel="stylesheet" href="<?php echo skyca_e(skyca_asset('assets/css/styles.css')); ?>">
    </head>
    <body class="admin-login-body">
        <main class="admin-login-wrapper">
            <div class="admin-login-card">
                <div class="admin-login-brand">
                    <a href="/">
                        <img src="/assets/images/skyca-logo-horizontal.png" alt="<?php echo skyca_e(COMPANY_LEGAL_NAME); ?>" width="240" height="65">
                    </a>
                    <h1>Lead Management Portal</h1>
                    <p>Authorized Administrator Access Only</p>
                </div>

                <?php if (!empty($_GET['logged_out'])): ?>
                    <div class="form-alert alert-success">You have been securely logged out.</div>
                <?php endif; ?>

                <?php if ($loginError !== ''): ?>
                    <div class="form-alert alert-error" role="alert"><?php echo skyca_e($loginError); ?></div>
                <?php endif; ?>

                <?php if (!$dbReady): ?>
                    <div class="form-alert alert-warning">
                        <strong>Database Configuration Required:</strong>
                        <span>Please configure <code>DATABASE_USER</code> and <code>DATABASE_PASSWORD</code> for <code>u591469534_skyca</code> in your server <code>.env</code> file.</span>
                    </div>
                <?php elseif ($adminCount === 0): ?>
                    <div class="form-alert alert-warning">
                        <strong>Initial Admin Setup Required:</strong>
                        <span>No admin user exists yet. Set <code>ADMIN_INITIAL_PASSWORD</code> in <code>.env</code> or run <code>php database/setup_admin.php</code> on the server.</span>
                    </div>
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
                        Sign In to Dashboard
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

// ----------------------------------------------------------------------------
// 4. AUTHENTICATED ADMIN DASHBOARD LOGIC & QUERY FILTERS
// ----------------------------------------------------------------------------
$currentAdmin   = skyca_current_admin();
$csrfToken      = skyca_csrf_token();
$serviceOptions = skyca_get_service_options();
$settings       = skyca_get_settings();

$searchQuery   = skyca_clean_input((string) ($_GET['q'] ?? ''), 120, false);
$filterStatus  = skyca_clean_input((string) ($_GET['status'] ?? ''), 30, false);
$filterService = skyca_clean_input((string) ($_GET['service'] ?? ''), 150, false);
$dateFrom      = skyca_clean_input((string) ($_GET['date_from'] ?? ''), 20, false);
$dateTo        = skyca_clean_input((string) ($_GET['date_to'] ?? ''), 20, false);
$testMode      = skyca_clean_input((string) ($_GET['test_mode'] ?? 'live'), 15, false); // 'live', 'all', 'test'
$page          = max(1, (int) ($_GET['page'] ?? 1));
$perPage       = 20;

$whereClauses = ['1=1'];
$params       = [];

if ($testMode === 'live') {
    $whereClauses[] = 'is_test = 0';
} elseif ($testMode === 'test') {
    $whereClauses[] = 'is_test = 1';
}

$validStatuses = ['New', 'Contacted', 'Qualified', 'Converted', 'Closed'];
if ($filterStatus !== '' && in_array($filterStatus, $validStatuses, true)) {
    $whereClauses[]    = 'status = :status';
    $params[':status'] = $filterStatus;
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
    $whereClauses[] = '(lead_reference LIKE :q1 OR full_name LIKE :q2 OR business_email LIKE :q3 OR phone LIKE :q4 OR company_name LIKE :q5)';
    $likeVal = '%' . $searchQuery . '%';
    $params[':q1'] = $likeVal;
    $params[':q2'] = $likeVal;
    $params[':q3'] = $likeVal;
    $params[':q4'] = $likeVal;
    $params[':q5'] = $likeVal;
}

$whereSql = implode(' AND ', $whereClauses);

// ----------------------------------------------------------------------------
// 5. HANDLE AUTHORIZED CSV EXPORT
// ----------------------------------------------------------------------------
if ($action === 'export_csv') {
    $exportStmt = $pdo->prepare("
        SELECT
            lead_reference, status, full_name, business_email, phone,
            company_name, requested_service, estimated_budget, project_timeline,
            project_description, landing_page, referrer, utm_source, utm_medium,
            utm_campaign, utm_content, utm_term, consent_timestamp, is_test,
            internal_notes, created_at, updated_at
        FROM leads
        WHERE {$whereSql}
        ORDER BY id DESC
        LIMIT 5000
    ");
    $exportStmt->execute($params);

    $filename = 'skyca_leads_export_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $out = fopen('php://output', 'w');
    // Write UTF-8 BOM for Excel compatibility
    fwrite($out, "\xEF\xBB\xBF");

    fputcsv($out, [
        'Lead Reference', 'Status', 'Full Name', 'Business Email', 'Phone',
        'Company Name', 'Requested Service', 'Estimated Budget', 'Project Timeline',
        'Project Description', 'Landing Page', 'Referrer', 'UTM Source', 'UTM Medium',
        'UTM Campaign', 'UTM Content', 'UTM Term', 'Consent Timestamp', 'Is Test Lead',
        'Internal Notes', 'Created At', 'Updated At'
    ]);

    while ($row = $exportStmt->fetch(PDO::FETCH_ASSOC)) {
        fputcsv($out, [
            skyca_csv_safe($row['lead_reference']),
            skyca_csv_safe($row['status']),
            skyca_csv_safe($row['full_name']),
            skyca_csv_safe($row['business_email']),
            skyca_csv_safe($row['phone']),
            skyca_csv_safe($row['company_name']),
            skyca_csv_safe($row['requested_service']),
            skyca_csv_safe($row['estimated_budget']),
            skyca_csv_safe($row['project_timeline']),
            skyca_csv_safe($row['project_description']),
            skyca_csv_safe($row['landing_page']),
            skyca_csv_safe($row['referrer']),
            skyca_csv_safe($row['utm_source']),
            skyca_csv_safe($row['utm_medium']),
            skyca_csv_safe($row['utm_campaign']),
            skyca_csv_safe($row['utm_content']),
            skyca_csv_safe($row['utm_term']),
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

// ----------------------------------------------------------------------------
// 6. FETCH DASHBOARD KPI STATS & PAGINATED LEADS
// ----------------------------------------------------------------------------
$kpiCondition = ($testMode === 'live') ? 'WHERE is_test = 0' : (($testMode === 'test') ? 'WHERE is_test = 1' : '');
$statsStmt = $pdo->query("
    SELECT
        COUNT(*) AS total_count,
        SUM(CASE WHEN status = 'New' THEN 1 ELSE 0 END) AS new_count,
        SUM(CASE WHEN status = 'Contacted' THEN 1 ELSE 0 END) AS contacted_count,
        SUM(CASE WHEN status = 'Qualified' THEN 1 ELSE 0 END) AS qualified_count,
        SUM(CASE WHEN status = 'Converted' THEN 1 ELSE 0 END) AS converted_count,
        SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) AS closed_count
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
        utm_campaign, status, is_test, created_at
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
    <title>Lead Management Dashboard | <?php echo skyca_e(COMPANY_LEGAL_NAME); ?></title>
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
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
                <span class="admin-portal-badge">Lead &amp; Operations Portal</span>
            </div>

            <nav class="admin-nav" aria-label="Admin Navigation">
                <a href="/admin?tab=leads" class="admin-nav-link <?php echo $tab === 'leads' ? 'active' : ''; ?>">Enquiries &amp; Leads</a>
                <a href="/admin?tab=settings" class="admin-nav-link <?php echo $tab === 'settings' ? 'active' : ''; ?>">Company &amp; Notification Settings</a>
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
            <!-- KPI SUMMARY CARDS -->
            <section class="admin-kpi-grid" aria-label="Lead Summary Metrics">
                <div class="kpi-card">
                    <span class="kpi-label">Total Enquiries</span>
                    <strong class="kpi-value"><?php echo (int) ($kpis['total_count'] ?? 0); ?></strong>
                </div>
                <div class="kpi-card kpi-new">
                    <span class="kpi-label">New</span>
                    <strong class="kpi-value"><?php echo (int) ($kpis['new_count'] ?? 0); ?></strong>
                </div>
                <div class="kpi-card kpi-contacted">
                    <span class="kpi-label">Contacted</span>
                    <strong class="kpi-value"><?php echo (int) ($kpis['contacted_count'] ?? 0); ?></strong>
                </div>
                <div class="kpi-card kpi-qualified">
                    <span class="kpi-label">Qualified</span>
                    <strong class="kpi-value"><?php echo (int) ($kpis['qualified_count'] ?? 0); ?></strong>
                </div>
                <div class="kpi-card kpi-converted">
                    <span class="kpi-label">Converted</span>
                    <strong class="kpi-value"><?php echo (int) ($kpis['converted_count'] ?? 0); ?></strong>
                </div>
                <div class="kpi-card kpi-closed">
                    <span class="kpi-label">Closed</span>
                    <strong class="kpi-value"><?php echo (int) ($kpis['closed_count'] ?? 0); ?></strong>
                </div>
            </section>

            <!-- SEARCH, FILTER & EXPORT BAR -->
            <section class="admin-filter-card">
                <form method="GET" action="/admin" class="admin-filter-form">
                    <input type="hidden" name="tab" value="leads">
                    <div class="admin-filter-grid">
                        <div>
                            <label for="f-q" class="form-label">Search Keyword</label>
                            <input type="text" id="f-q" name="q" value="<?php echo skyca_e($searchQuery); ?>" class="form-control" placeholder="Name, email, phone, company, ref...">
                        </div>
                        <div>
                            <label for="f-status" class="form-label">Lead Status</label>
                            <select id="f-status" name="status" class="form-control">
                                <option value="">All Statuses</option>
                                <?php foreach ($validStatuses as $st): ?>
                                    <option value="<?php echo skyca_e($st); ?>" <?php echo $filterStatus === $st ? 'selected' : ''; ?>><?php echo skyca_e($st); ?></option>
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
                            <label for="f-date-from" class="form-label">From Date</label>
                            <input type="date" id="f-date-from" name="date_from" value="<?php echo skyca_e($dateFrom); ?>" class="form-control">
                        </div>
                        <div>
                            <label for="f-date-to" class="form-label">To Date</label>
                            <input type="date" id="f-date-to" name="date_to" value="<?php echo skyca_e($dateTo); ?>" class="form-control">
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
                            <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                            <a href="/admin?tab=leads" class="btn btn-outline btn-sm">Reset</a>
                        </div>
                        <?php
                        $exportQuery = http_build_query([
                            'action'    => 'export_csv',
                            'q'         => $searchQuery,
                            'status'    => $filterStatus,
                            'service'   => $filterService,
                            'date_from' => $dateFrom,
                            'date_to'   => $dateTo,
                            'test_mode' => $testMode,
                        ]);
                        ?>
                        <a href="/admin?<?php echo skyca_e($exportQuery); ?>" class="btn btn-secondary btn-sm">
                            Export Filtered Leads to CSV (<?php echo $totalFiltered; ?>)
                        </a>
                    </div>
                </form>
            </section>

            <!-- LEADS DATA TABLE -->
            <section class="admin-table-card">
                <div class="admin-table-responsive">
                    <table class="admin-leads-table">
                        <thead>
                            <tr>
                                <th>Ref ID &amp; Date</th>
                                <th>Client Details</th>
                                <th>Service &amp; Scope</th>
                                <th>Attribution</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($leadsList)): ?>
                                <tr>
                                    <td colspan="6" class="empty-table-cell">
                                        No project enquiries match the current filter criteria.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($leadsList as $row): ?>
                                    <tr>
                                        <td>
                                            <strong class="lead-ref-mono"><?php echo skyca_e($row['lead_reference']); ?></strong>
                                            <?php if ((int) $row['is_test'] === 1): ?>
                                                <span class="badge-test">TEST</span>
                                            <?php endif; ?>
                                            <div class="table-subtext"><?php echo skyca_e($row['created_at']); ?></div>
                                        </td>
                                        <td>
                                            <strong><?php echo skyca_e($row['full_name']); ?></strong>
                                            <?php if (!empty($row['company_name'])): ?>
                                                <div class="table-subtext"><?php echo skyca_e($row['company_name']); ?></div>
                                            <?php endif; ?>
                                            <div class="table-subtext">
                                                <a href="mailto:<?php echo skyca_e($row['business_email']); ?>"><?php echo skyca_e($row['business_email']); ?></a>
                                                &bull;
                                                <a href="tel:<?php echo skyca_e($row['phone']); ?>"><?php echo skyca_e($row['phone']); ?></a>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="service-cell-title"><?php echo skyca_e($row['requested_service']); ?></div>
                                            <div class="table-subtext">
                                                Budget: <?php echo skyca_e($row['estimated_budget'] ?: 'N/A'); ?> |
                                                Timeline: <?php echo skyca_e($row['project_timeline'] ?: 'N/A'); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="table-subtext">
                                                <?php echo skyca_e($row['utm_source'] ?: 'Direct / Organic'); ?>
                                                <?php if (!empty($row['utm_campaign'])): ?>
                                                    (<?php echo skyca_e($row['utm_campaign']); ?>)
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="status-pill status-<?php echo strtolower(skyca_e($row['status'])); ?>">
                                                <?php echo skyca_e($row['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-outline btn-sm js-open-lead-modal"
                                                data-lead-id="<?php echo (int) $row['id']; ?>"
                                            >
                                                View / Manage
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>
                    <div class="admin-pagination">
                        <span>Showing page <?php echo $page; ?> of <?php echo $totalPages; ?> (<?php echo $totalFiltered; ?> total records)</span>
                        <div class="pagination-links">
                            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                                <?php
                                $pageQuery = http_build_query([
                                    'tab'       => 'leads',
                                    'q'         => $searchQuery,
                                    'status'    => $filterStatus,
                                    'service'   => $filterService,
                                    'date_from' => $dateFrom,
                                    'date_to'   => $dateTo,
                                    'test_mode' => $testMode,
                                    'page'      => $p,
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

            <!-- LEAD DETAILS & STATUS WORKFLOW MODAL -->
            <div class="admin-modal-backdrop hidden" id="lead-detail-modal" role="dialog" aria-modal="true" aria-labelledby="modal-lead-title">
                <div class="admin-modal-card">
                    <div class="admin-modal-header">
                        <h2 id="modal-lead-title">Enquiry Details</h2>
                        <button type="button" class="modal-close-btn" id="close-lead-modal-btn" aria-label="Close modal">&times;</button>
                    </div>
                    <div class="admin-modal-body" id="modal-lead-body">
                        <p>Loading enquiry details...</p>
                    </div>
                </div>
            </div>

        <?php elseif ($tab === 'settings'): ?>
            <!-- OPTIONAL VERIFIED COMPANY PROFILE & NOTIFICATION SETTINGS -->
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
            <!-- SECURE ADMIN PASSWORD CHANGE -->
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
