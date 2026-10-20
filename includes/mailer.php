<?php
declare(strict_types=1);

/**
 * SKYCA DIGITAL PRIVATE LIMITED — TRANSACTIONAL SMTP EMAIL NOTIFICATION ENGINE
 * Sends admin notifications to info@skycadigital.com and optional client confirmations.
 * Never loses stored database leads if SMTP is unconfigured or temporarily unreachable.
 */

require_once dirname(__DIR__) . '/config/config.php';
require_once __DIR__ . '/security.php';

/**
 * Send notification email to info@skycadigital.com and optional confirmation to the client.
 * Updates `email_notification_sent = 1` on success to prevent duplicate emails on retries.
 */
function skyca_dispatch_lead_emails(PDO $pdo, int $leadId, array $lead): array
{
    // Avoid sending duplicate emails if already marked sent
    $checkStmt = $pdo->prepare('SELECT email_notification_sent FROM leads WHERE id = :id LIMIT 1');
    $checkStmt->execute([':id' => $leadId]);
    $alreadySent = (int) $checkStmt->fetchColumn();
    if ($alreadySent === 1) {
        return ['sent' => false, 'status' => 'already_sent'];
    }

    $smtpEnabled = skyca_env_bool('SMTP_ENABLED', false);
    $smtpHost    = trim(skyca_env('SMTP_HOST', ''));
    $smtpUser    = trim(skyca_env('SMTP_USER', ''));
    $smtpPass    = skyca_env('SMTP_PASSWORD', '');

    if (!$smtpEnabled || $smtpHost === '' || $smtpUser === '' || $smtpPass === '') {
        // Honestly report that SMTP is not yet configured without pretending delivery succeeded
        return [
            'sent'   => false,
            'status' => 'not_configured',
            'reason' => 'SMTP service is not configured in .env (SMTP_ENABLED/SMTP_PASSWORD). Lead is safely stored in MySQL.',
        ];
    }

    $adminTo = filter_var(skyca_env('ADMIN_NOTIFICATION_EMAIL', BUSINESS_EMAIL), FILTER_VALIDATE_EMAIL) ?: BUSINESS_EMAIL;
    $ref     = (string) ($lead['lead_reference'] ?? 'N/A');
    $name    = (string) ($lead['full_name'] ?? '');
    $email   = (string) ($lead['business_email'] ?? '');
    $phone   = (string) ($lead['phone'] ?? '');
    $company = (string) ($lead['company_name'] ?: 'Not specified');
    $service = (string) ($lead['requested_service'] ?? '');
    $budget  = (string) ($lead['estimated_budget'] ?: 'Not specified');
    $time    = (string) ($lead['project_timeline'] ?: 'Not specified');
    $desc    = (string) ($lead['project_description'] ?? '');
    $utmSrc  = (string) ($lead['utm_source'] ?: 'Direct / Organic');
    $utmMed  = (string) ($lead['utm_medium'] ?: '-');
    $utmCmp  = (string) ($lead['utm_campaign'] ?: '-');
    $landing = (string) ($lead['landing_page'] ?: '/');

    $adminSubject = "[New Business Enquiry {$ref}] {$service} — {$name}";
    $adminHtml = '
    <div style="font-family: Arial, sans-serif; max-width: 640px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
        <div style="background-color: #0b1528; color: #ffffff; padding: 20px 24px;">
            <h2 style="margin: 0; font-size: 18px;">New Website Enquiry — ' . skyca_e($ref) . '</h2>
            <p style="margin: 4px 0 0; font-size: 13px; color: #94a3b8;">Skyca Digital Private Limited (' . skyca_e(SITE_URL) . ')</p>
        </div>
        <div style="padding: 24px; background-color: #ffffff; color: #1e293b;">
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr><td style="padding: 8px 0; font-weight: bold; width: 170px; border-bottom: 1px solid #f1f5f9;">Reference ID:</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . skyca_e($ref) . '</td></tr>
                <tr><td style="padding: 8px 0; font-weight: bold; border-bottom: 1px solid #f1f5f9;">Full Name:</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . skyca_e($name) . '</td></tr>
                <tr><td style="padding: 8px 0; font-weight: bold; border-bottom: 1px solid #f1f5f9;">Business Email:</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;"><a href="mailto:' . skyca_e($email) . '">' . skyca_e($email) . '</a></td></tr>
                <tr><td style="padding: 8px 0; font-weight: bold; border-bottom: 1px solid #f1f5f9;">Phone Number:</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;"><a href="tel:' . skyca_e($phone) . '">' . skyca_e($phone) . '</a></td></tr>
                <tr><td style="padding: 8px 0; font-weight: bold; border-bottom: 1px solid #f1f5f9;">Company / Org:</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . skyca_e($company) . '</td></tr>
                <tr><td style="padding: 8px 0; font-weight: bold; border-bottom: 1px solid #f1f5f9;">Service Required:</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9; color: #033eb6; font-weight: bold;">' . skyca_e($service) . '</td></tr>
                <tr><td style="padding: 8px 0; font-weight: bold; border-bottom: 1px solid #f1f5f9;">Estimated Budget:</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . skyca_e($budget) . '</td></tr>
                <tr><td style="padding: 8px 0; font-weight: bold; border-bottom: 1px solid #f1f5f9;">Project Timeline:</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">' . skyca_e($time) . '</td></tr>
                <tr><td style="padding: 8px 0; font-weight: bold; border-bottom: 1px solid #f1f5f9;">Attribution:</td><td style="padding: 8px 0; border-bottom: 1px solid #f1f5f9;">Source: ' . skyca_e($utmSrc) . ' | Medium: ' . skyca_e($utmMed) . ' | Campaign: ' . skyca_e($utmCmp) . ' | Landing: ' . skyca_e($landing) . '</td></tr>
            </table>
            <div style="margin-top: 18px; padding: 16px; background-color: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0;">
                <strong style="display: block; margin-bottom: 8px; font-size: 13px; color: #475569;">PROJECT DESCRIPTION:</strong>
                <div style="font-size: 14px; line-height: 1.6; white-space: pre-wrap;">' . skyca_e($desc) . '</div>
            </div>
            <p style="margin-top: 20px; font-size: 13px; color: #64748b;">
                Manage this enquiry in the <a href="' . skyca_e(SITE_URL . '/admin') . '">Skyca Digital Admin Dashboard</a>.
            </p>
        </div>
    </div>';

    try {
        $sentAdmin = skyca_smtp_send(
            $adminTo,
            $adminSubject,
            $adminHtml,
            $email
        );

        if ($sentAdmin) {
            $upd = $pdo->prepare('UPDATE leads SET email_notification_sent = 1 WHERE id = :id');
            $upd->execute([':id' => $leadId]);

            // Optional confirmation email to the client
            if (skyca_env_bool('SEND_CLIENT_CONFIRMATION_EMAIL', true) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $clientSubject = "We received your enquiry ({$ref}) — Skyca Digital Private Limited";
                $clientHtml = '
                <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
                    <div style="background-color: #0b1528; color: #ffffff; padding: 20px 24px;">
                        <h2 style="margin: 0; font-size: 18px;">Thank You for Contacting Skyca Digital</h2>
                    </div>
                    <div style="padding: 24px; background-color: #ffffff; color: #1e293b; font-size: 14px; line-height: 1.6;">
                        <p>Dear ' . skyca_e($name) . ',</p>
                        <p>Thank you for reaching out to <strong>Skyca Digital Private Limited</strong> regarding <strong>' . skyca_e($service) . '</strong>.</p>
                        <p>Your enquiry has been recorded under reference number <strong>' . skyca_e($ref) . '</strong>. A member of our technical consulting team will review your project requirements and get in touch with you shortly.</p>
                        <p style="margin-top: 20px;">
                            <strong>Direct Contact Details:</strong><br>
                            Email: <a href="mailto:' . skyca_e(BUSINESS_EMAIL) . '">' . skyca_e(BUSINESS_EMAIL) . '</a><br>
                            Phone: <a href="tel:' . skyca_e(BUSINESS_PHONE_RAW) . '">' . skyca_e(BUSINESS_PHONE) . '</a><br>
                            Website: <a href="' . skyca_e(SITE_URL) . '">' . skyca_e(SITE_URL) . '</a>
                        </p>
                        <p style="color: #64748b; font-size: 12px; margin-top: 24px;">Best regards,<br>Team Skyca Digital Private Limited</p>
                    </div>
                </div>';
                @skyca_smtp_send($email, $clientSubject, $clientHtml, BUSINESS_EMAIL);
            }

            return ['sent' => true, 'status' => 'sent'];
        }

        return ['sent' => false, 'status' => 'failed'];
    } catch (Throwable $e) {
        error_log('[SkycaMailer Error] ' . $e->getMessage());
        return ['sent' => false, 'status' => 'error'];
    }
}

/**
 * Low-level authenticated SMTP transmission with CRLF injection prevention.
 */
function skyca_smtp_send(string $to, string $subject, string $htmlBody, string $replyTo = ''): bool
{
    $host       = trim(skyca_env('SMTP_HOST', 'smtp.hostinger.com'));
    $port       = (int) skyca_env('SMTP_PORT', '465');
    $encryption = strtolower(trim(skyca_env('SMTP_ENCRYPTION', 'ssl')));
    $user       = trim(skyca_env('SMTP_USER', ''));
    $pass       = skyca_env('SMTP_PASSWORD', '');
    $fromEmail  = filter_var(skyca_env('MAIL_FROM', BUSINESS_EMAIL), FILTER_VALIDATE_EMAIL) ?: BUSINESS_EMAIL;
    $fromName   = str_replace(["\r", "\n"], '', skyca_env('MAIL_FROM_NAME', COMPANY_LEGAL_NAME));

    $cleanTo = filter_var($to, FILTER_VALIDATE_EMAIL);
    if (!$cleanTo) {
        return false;
    }
    $cleanSubject = str_replace(["\r", "\n"], ' ', $subject);
    $cleanReplyTo = filter_var($replyTo, FILTER_VALIDATE_EMAIL) ?: $fromEmail;

    $remote = ($encryption === 'ssl' ? 'ssl://' : 'tcp://') . $host . ':' . $port;
    $errno  = 0;
    $errstr = '';
    $fp = @stream_socket_client($remote, $errno, $errstr, 8);
    if (!is_resource($fp)) {
        throw new RuntimeException("SMTP connection failed ({$errno}): {$errstr}");
    }

    stream_set_timeout($fp, 8);

    $readResponse = static function ($socket): string {
        $data = '';
        while ($str = fgets($socket, 515)) {
            $data .= $str;
            if (isset($str[3]) && $str[3] === ' ') {
                break;
            }
        }
        return $data;
    };

    $sendCmd = static function ($socket, string $cmd, array $expectedCodes) use ($readResponse): string {
        fwrite($socket, $cmd . "\r\n");
        $resp = $readResponse($socket);
        $code = (int) substr($resp, 0, 3);
        if (!in_array($code, $expectedCodes, true)) {
            throw new RuntimeException("SMTP command encountered unexpected code {$code}");
        }
        return $resp;
    };

    try {
        $greeting = $readResponse($fp);
        if ((int) substr($greeting, 0, 3) !== 220) {
            throw new RuntimeException('Invalid SMTP greeting');
        }

        $sendCmd($fp, 'EHLO skycadigital.com', [250]);

        if ($encryption === 'tls' || $encryption === 'starttls') {
            $sendCmd($fp, 'STARTTLS', [220]);
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('Failed to negotiate STARTTLS');
            }
            $sendCmd($fp, 'EHLO skycadigital.com', [250]);
        }

        $sendCmd($fp, 'AUTH LOGIN', [334]);
        $sendCmd($fp, base64_encode($user), [334]);
        $sendCmd($fp, base64_encode($pass), [235]);

        $sendCmd($fp, "MAIL FROM:<{$fromEmail}>", [250]);
        $sendCmd($fp, "RCPT TO:<{$cleanTo}>", [250, 251]);
        $sendCmd($fp, 'DATA', [354]);

        $headers = [
            'Date: ' . date(DATE_RFC2822),
            "From: \"{$fromName}\" <{$fromEmail}>",
            "To: <{$cleanTo}>",
            "Reply-To: <{$cleanReplyTo}>",
            "Subject: =?UTF-8?B?" . base64_encode($cleanSubject) . "?=",
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            'Content-Transfer-Encoding: base64',
            'X-Mailer: SkycaDigitalMailer/1.0',
        ];

        $escapedBody = chunk_split(base64_encode($htmlBody));
        $payload = implode("\r\n", $headers) . "\r\n\r\n" . $escapedBody . "\r\n.";
        $sendCmd($fp, $payload, [250]);
        @fwrite($fp, "QUIT\r\n");
        @fclose($fp);
        return true;
    } catch (Throwable $e) {
        @fclose($fp);
        throw $e;
    }
}
