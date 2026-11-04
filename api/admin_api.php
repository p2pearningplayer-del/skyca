<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — AUTHENTICATED ADMIN & ADS CRM API (api/admin_api.php)
 * Protected by server-side session authentication, CSRF verification, and prepared SQL queries.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once dirname(__DIR__) . '/config/services_data.php';
require_once dirname(__DIR__) . '/includes/security.php';
require_once dirname(__DIR__) . '/includes/db.php';

skyca_start_session();

header('Cache-Control: no-store, no-cache, must-revalidate');
header('X-Robots-Tag: noindex, nofollow');

if (!skyca_is_admin_logged_in()) {
    http_response_code(401);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'Authentication required.']);
    exit;
}

$admin = skyca_current_admin();
$action = (string) ($_GET['action'] ?? $_POST['action'] ?? '');

try {
    $pdo = skyca_db();

    // ------------------------------------------------------------------------
    // ACTION 1: GET FULL LEAD DETAILS + CRM NOTES HISTORY (GET)
    // ------------------------------------------------------------------------
    if ($action === 'get_lead' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
        $leadId = (int) ($_GET['id'] ?? 0);
        if ($leadId <= 0) {
            http_response_code(400);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'message' => 'Invalid lead ID.']);
            exit;
        }

        $stmt = $pdo->prepare('SELECT * FROM leads WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $leadId]);
        $lead = $stmt->fetch();

        if (!$lead) {
            http_response_code(404);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'message' => 'Lead record not found.']);
            exit;
        }

        unset($lead['submission_hash'], $lead['ip_hash']);

        $notesStmt = $pdo->prepare('SELECT id, admin_username, note_text, created_at FROM lead_notes WHERE lead_id = :lead_id ORDER BY id DESC');
        $notesStmt->execute([':lead_id' => $leadId]);
        $notes = $notesStmt->fetchAll();

        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => true,
            'lead'    => $lead,
            'notes'   => $notes,
        ]);
        exit;
    }

    // All mutating actions require POST + valid CSRF token
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $csrf = (string) ($_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!skyca_verify_csrf($csrf)) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode(['success' => false, 'message' => 'Invalid or expired CSRF security token.']);
            exit;
        }

        // --------------------------------------------------------------------
        // ACTION 2: UPDATE LEAD CRM PIPELINE, PRIORITY, DEAL VALUE & NOTES
        // --------------------------------------------------------------------
        if ($action === 'update_lead') {
            $leadId          = (int) ($_POST['lead_id'] ?? 0);
            $newStatus       = skyca_clean_input((string) ($_POST['status'] ?? ''), 30, false);
            $leadPriority    = skyca_clean_input((string) ($_POST['lead_priority'] ?? 'Normal'), 20, false);
            $dealValue       = isset($_POST['deal_value']) ? max(0.0, (float) $_POST['deal_value']) : null;
            $nextFollowupAt  = skyca_clean_input((string) ($_POST['next_followup_at'] ?? ''), 20, false);
            $assignedTo      = skyca_clean_input((string) ($_POST['assigned_to'] ?? ''), 100, false);
            $internalNotes   = skyca_clean_input((string) ($_POST['internal_notes'] ?? ''), 4000, true);
            $newNoteText     = skyca_clean_input((string) ($_POST['new_note'] ?? ''), 2000, true);

            $validStatuses   = ['New', 'Contacted', 'Qualified', 'Proposal Sent', 'Converted', 'Closed'];
            $validPriorities = ['Hot', 'Warm', 'Normal'];

            if ($leadId <= 0 || !in_array($newStatus, $validStatuses, true)) {
                http_response_code(422);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['success' => false, 'message' => 'Invalid lead ID or status value.']);
                exit;
            }
            if (!in_array($leadPriority, $validPriorities, true)) {
                $leadPriority = 'Normal';
            }
            if ($nextFollowupAt !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $nextFollowupAt)) {
                $nextFollowupAt = '';
            }

            // Preserve existing deal_value if not explicitly posted
            if ($dealValue === null) {
                $curStmt = $pdo->prepare('SELECT deal_value FROM leads WHERE id = :id LIMIT 1');
                $curStmt->execute([':id' => $leadId]);
                $dealValue = (float) ($curStmt->fetchColumn() ?: 0.0);
            }

            $now = date('Y-m-d H:i:s');
            $pdo->beginTransaction();

            $upd = $pdo->prepare('
                UPDATE leads
                SET status = :status,
                    lead_priority = :lead_priority,
                    deal_value = :deal_value,
                    next_followup_at = :next_followup_at,
                    assigned_to = :assigned_to,
                    internal_notes = :internal_notes,
                    updated_at = :updated_at
                WHERE id = :id
            ');
            $upd->execute([
                ':status'           => $newStatus,
                ':lead_priority'    => $leadPriority,
                ':deal_value'       => $dealValue,
                ':next_followup_at' => $nextFollowupAt !== '' ? $nextFollowupAt : null,
                ':assigned_to'      => $assignedTo !== '' ? $assignedTo : null,
                ':internal_notes'   => $internalNotes !== '' ? $internalNotes : null,
                ':updated_at'       => $now,
                ':id'               => $leadId,
            ]);

            if ($newNoteText !== '') {
                $noteIns = $pdo->prepare('
                    INSERT INTO lead_notes (lead_id, admin_username, note_text, created_at)
                    VALUES (:lead_id, :admin_username, :note_text, :created_at)
                ');
                $noteIns->execute([
                    ':lead_id'        => $leadId,
                    ':admin_username' => $admin['username'] ?? 'admin',
                    ':note_text'      => "[Status: {$newStatus} | Priority: {$leadPriority}] " . $newNoteText,
                    ':created_at'     => $now,
                ]);
            }

            $pdo->commit();

            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'success' => true,
                'message' => 'CRM lead record updated successfully.',
            ]);
            exit;
        }

        // --------------------------------------------------------------------
        // ACTION 3: CHANGE ADMIN PASSWORD SECURELY
        // --------------------------------------------------------------------
        if ($action === 'change_password') {
            $currentPassword = (string) ($_POST['current_password'] ?? '');
            $newPassword     = (string) ($_POST['new_password'] ?? '');
            $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

            if ($currentPassword === '' || $newPassword === '') {
                http_response_code(422);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['success' => false, 'message' => 'Current and new passwords are required.']);
                exit;
            }

            if (strlen($newPassword) < 10) {
                http_response_code(422);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['success' => false, 'message' => 'New password must be at least 10 characters long.']);
                exit;
            }

            if ($newPassword !== $confirmPassword) {
                http_response_code(422);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['success' => false, 'message' => 'New password and confirmation do not match.']);
                exit;
            }

            $stmt = $pdo->prepare('SELECT id, password_hash FROM admin_users WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $admin['id']]);
            $userRow = $stmt->fetch();

            if (!$userRow || !password_verify($currentPassword, (string) $userRow['password_hash'])) {
                http_response_code(403);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['success' => false, 'message' => 'Current password verification failed.']);
                exit;
            }

            $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE admin_users SET password_hash = :hash, updated_at = :now WHERE id = :id');
            $upd->execute([
                ':hash' => $newHash,
                ':now'  => date('Y-m-d H:i:s'),
                ':id'   => $admin['id'],
            ]);

            session_regenerate_id(true);

            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'success' => true,
                'message' => 'Administrator password updated successfully.',
            ]);
            exit;
        }

        // --------------------------------------------------------------------
        // ACTION 4: SAVE VERIFIED COMPANY, ADS PIXEL & CRM WEBHOOK SETTINGS
        // --------------------------------------------------------------------
        if ($action === 'save_settings' || $action === 'save_ads_crm_settings') {
            $allowedKeys = [
                'company_address',
                'company_city',
                'company_state',
                'company_postal_code',
                'company_cin',
                'company_gstin',
                'company_working_hours',
                'whatsapp_number',
                'consultation_booking_url',
                'social_linkedin_url',
                'social_github_url',
                'social_x_url',
                'social_facebook_url',
                'social_instagram_url',
                'email_notifications_enabled',
                'google_analytics_id',
                'google_ads_conversion_id',
                'google_ads_conversion_label',
                'meta_pixel_id',
                'meta_capi_token',
                'crm_webhook_url',
                'crm_webhook_secret',
                'google_site_verification',
                'bing_site_verification',
                'seo_home_title',
                'seo_home_description',
                'seo_default_keywords',
            ];

            $now = date('Y-m-d H:i:s');
            $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);

            $pdo->beginTransaction();
            foreach ($allowedKeys as $key) {
                if (array_key_exists($key, $_POST)) {
                    $val = skyca_clean_input((string) $_POST[$key], 500, false);
                    if ($driver === 'mysql') {
                        $stmt = $pdo->prepare('
                            INSERT INTO site_settings (setting_key, setting_value, updated_at)
                            VALUES (:k, :v, :u)
                            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = VALUES(updated_at)
                        ');
                    } else {
                        $stmt = $pdo->prepare('
                            INSERT INTO site_settings (setting_key, setting_value, updated_at)
                            VALUES (:k, :v, :u)
                            ON CONFLICT(setting_key) DO UPDATE SET setting_value = excluded.setting_value, updated_at = excluded.updated_at
                        ');
                    }
                    $stmt->execute([':k' => $key, ':v' => $val, ':u' => $now]);
                }
            }
            $pdo->commit();

            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'success' => true,
                'message' => 'Configuration saved successfully.',
            ]);
            exit;
        }

        // --------------------------------------------------------------------
        // ACTION 5: TEST EXTERNAL CRM WEBHOOK CONNECTION
        // --------------------------------------------------------------------
        if ($action === 'test_crm_webhook') {
            $webhookUrl = trim((string) ($_POST['crm_webhook_url'] ?? ''));
            $secret     = (string) ($_POST['crm_webhook_secret'] ?? '');

            if ($webhookUrl === '' || !filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
                http_response_code(422);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['success' => false, 'message' => 'Please enter a valid HTTPS Webhook URL first.']);
                exit;
            }

            $sampleBody = (string) json_encode([
                'event'     => 'skyca.lead.test_ping',
                'timestamp' => date('c'),
                'source'    => SITE_URL,
                'lead'      => [
                    'lead_reference'    => 'SKY-TEST-WEBHOOK',
                    'full_name'         => '[TEST] Skyca CRM Webhook Verification',
                    'business_email'    => 'info@skycadigital.com',
                    'phone'             => '+91 6002064873',
                    'requested_service' => 'SaaS Solutions & Cloud Services',
                    'ad_channel'        => 'Google Ads',
                    'lead_priority'     => 'Hot',
                    'deal_value'        => 500000,
                    'utm_source'        => 'google_ads',
                    'utm_campaign'      => 'test_campaign',
                ],
            ], JSON_UNESCAPED_SLASHES);

            $headers = [
                'Content-Type: application/json',
                'User-Agent: SkycaDigital-CRM-Webhook/1.0',
            ];
            if ($secret !== '') {
                $headers[] = 'X-Skyca-Signature: ' . hash_hmac('sha256', $sampleBody, $secret);
            }

            $ch = curl_init($webhookUrl);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $sampleBody,
                CURLOPT_HTTPHEADER     => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 6,
            ]);
            curl_exec($ch);
            $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            unset($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    'success' => true,
                    'message' => "CRM Webhook responded with HTTP {$httpCode} OK!",
                ]);
                exit;
            }

            http_response_code(422);
            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'success' => false,
                'message' => "Webhook endpoint returned HTTP {$httpCode}. Please verify the receiver URL.",
            ]);
            exit;
        }

        // --------------------------------------------------------------------
        // ACTION 6: CONFIGURE & VERIFY MYSQL DATABASE (u591469534_skyca)
        // --------------------------------------------------------------------
        if ($action === 'save_db_config') {
            $dbHost = skyca_clean_input((string) ($_POST['db_host'] ?? 'localhost'), 120, false);
            $dbPort = skyca_clean_input((string) ($_POST['db_port'] ?? '3306'), 10, false);
            $dbName = skyca_clean_input((string) ($_POST['db_name'] ?? 'u591469534_skyca'), 100, false);
            $dbUser = skyca_clean_input((string) ($_POST['db_user'] ?? ''), 100, false);
            $dbPass = (string) ($_POST['db_pass'] ?? '');

            if ($dbUser === '' || $dbPass === '') {
                http_response_code(422);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['success' => false, 'message' => 'Please enter both MySQL Database User and Password.']);
                exit;
            }

            try {
                $testDsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $dbHost, $dbPort, $dbName);
                $testPdo = new PDO($testDsn, $dbUser, $dbPass, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::ATTR_TIMEOUT            => 8,
                ]);
                skyca_ensure_schema_mysql($testPdo);
                skyca_bootstrap_initial_admin($testPdo);

                $cfgFile = SKYCA_STORAGE_DIR . DIRECTORY_SEPARATOR . 'db_config.json';
                @file_put_contents($cfgFile, json_encode([
                    'db_host' => $dbHost,
                    'db_port' => $dbPort,
                    'db_name' => $dbName,
                    'db_user' => $dbUser,
                    'db_pass' => $dbPass,
                ], JSON_PRETTY_PRINT), LOCK_EX);

                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    'success' => true,
                    'message' => "Connected to MySQL database ({$dbName}) and verified tables successfully!",
                ]);
                exit;
            } catch (Throwable $dbErr) {
                http_response_code(422);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode([
                    'success' => false,
                    'message' => 'MySQL connection test failed. Please verify your Hostinger MySQL username and password for ' . $dbName . '.',
                ]);
                exit;
            }
        }
    }

    http_response_code(400);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'Unsupported admin action.']);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
        @$pdo->rollBack();
    }
    error_log('[SkycaAdminAPI Error] ' . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['success' => false, 'message' => 'An internal error occurred while processing the admin action.']);
}
