<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — AUTHENTICATED ADMIN API (api/admin_api.php)
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
    // ACTION 1: GET FULL LEAD DETAILS + NOTES HISTORY (GET)
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

        // Remove internal hash fields before returning to UI
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
        // ACTION 2: UPDATE LEAD STATUS & ADD INTERNAL NOTE
        // --------------------------------------------------------------------
        if ($action === 'update_lead') {
            $leadId        = (int) ($_POST['lead_id'] ?? 0);
            $newStatus     = skyca_clean_input((string) ($_POST['status'] ?? ''), 30, false);
            $internalNotes = skyca_clean_input((string) ($_POST['internal_notes'] ?? ''), 4000, true);
            $newNoteText   = skyca_clean_input((string) ($_POST['new_note'] ?? ''), 2000, true);

            $validStatuses = ['New', 'Contacted', 'Qualified', 'Converted', 'Closed'];
            if ($leadId <= 0 || !in_array($newStatus, $validStatuses, true)) {
                http_response_code(422);
                header('Content-Type: application/json; charset=UTF-8');
                echo json_encode(['success' => false, 'message' => 'Invalid lead ID or status value.']);
                exit;
            }

            $now = date('Y-m-d H:i:s');
            $pdo->beginTransaction();

            $upd = $pdo->prepare('
                UPDATE leads
                SET status = :status,
                    internal_notes = :internal_notes,
                    updated_at = :updated_at
                WHERE id = :id
            ');
            $upd->execute([
                ':status'         => $newStatus,
                ':internal_notes' => $internalNotes !== '' ? $internalNotes : null,
                ':updated_at'     => $now,
                ':id'             => $leadId,
            ]);

            if ($newNoteText !== '') {
                $noteIns = $pdo->prepare('
                    INSERT INTO lead_notes (lead_id, admin_username, note_text, created_at)
                    VALUES (:lead_id, :admin_username, :note_text, :created_at)
                ');
                $noteIns->execute([
                    ':lead_id'        => $leadId,
                    ':admin_username' => $admin['username'] ?? 'admin',
                    ':note_text'      => "[Status: {$newStatus}] " . $newNoteText,
                    ':created_at'     => $now,
                ]);
            }

            $pdo->commit();

            header('Content-Type: application/json; charset=UTF-8');
            echo json_encode([
                'success' => true,
                'message' => 'Lead record updated successfully.',
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
        // ACTION 4: SAVE VERIFIED COMPANY & NOTIFICATION SETTINGS
        // --------------------------------------------------------------------
        if ($action === 'save_settings') {
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
                'message' => 'Company profile and notification settings saved.',
            ]);
            exit;
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
