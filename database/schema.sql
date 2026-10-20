-- ============================================================================
-- SKYCA DIGITAL PRIVATE LIMITED — MYSQL DATABASE SCHEMA
-- Database Name: u591469534_skyca
-- Engine: InnoDB | Charset: utf8mb4_unicode_ci
-- SAFE MIGRATION POLICY: Uses CREATE TABLE IF NOT EXISTS exclusively.
-- Never drops, truncates, or overwrites existing tables or production data.
-- ============================================================================

SET NAMES utf8mb4;
SET time_zone = '+05:30';

-- ----------------------------------------------------------------------------
-- 1. LEADS TABLE (Primary Business Enquiries & Attribution Storage)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `leads` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `lead_reference` VARCHAR(32) NOT NULL,
    `full_name` VARCHAR(120) NOT NULL,
    `business_email` VARCHAR(190) NOT NULL,
    `phone` VARCHAR(40) NOT NULL,
    `company_name` VARCHAR(150) DEFAULT NULL,
    `requested_service` VARCHAR(150) NOT NULL,
    `estimated_budget` VARCHAR(100) DEFAULT NULL,
    `project_timeline` VARCHAR(100) DEFAULT NULL,
    `project_description` TEXT NOT NULL,
    `landing_page` VARCHAR(500) DEFAULT NULL,
    `referrer` VARCHAR(500) DEFAULT NULL,
    `utm_source` VARCHAR(150) DEFAULT NULL,
    `utm_medium` VARCHAR(150) DEFAULT NULL,
    `utm_campaign` VARCHAR(150) DEFAULT NULL,
    `utm_content` VARCHAR(150) DEFAULT NULL,
    `utm_term` VARCHAR(150) DEFAULT NULL,
    `consent_status` TINYINT(1) NOT NULL DEFAULT 1,
    `consent_timestamp` DATETIME NOT NULL,
    `status` ENUM('New', 'Contacted', 'Qualified', 'Converted', 'Closed') NOT NULL DEFAULT 'New',
    `internal_notes` TEXT DEFAULT NULL,
    `is_test` TINYINT(1) NOT NULL DEFAULT 0,
    `submission_hash` CHAR(64) DEFAULT NULL,
    `ip_hash` CHAR(64) DEFAULT NULL,
    `email_notification_sent` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_leads_reference` (`lead_reference`),
    KEY `idx_leads_status` (`status`),
    KEY `idx_leads_service` (`requested_service`),
    KEY `idx_leads_created_at` (`created_at`),
    KEY `idx_leads_email` (`business_email`),
    KEY `idx_leads_submission_hash` (`submission_hash`),
    KEY `idx_leads_is_test` (`is_test`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 2. LEAD NOTES TABLE (Audit Trail of Admin Status Updates & Internal Notes)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `lead_notes` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `lead_id` BIGINT UNSIGNED NOT NULL,
    `admin_username` VARCHAR(100) NOT NULL,
    `note_text` TEXT NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_lead_notes_lead_id` (`lead_id`),
    CONSTRAINT `fk_lead_notes_lead` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 3. ADMIN USERS TABLE (Authenticated Dashboard Administrators)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(80) NOT NULL,
    `email` VARCHAR(190) NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(120) NOT NULL DEFAULT 'System Administrator',
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `last_login_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_admin_username` (`username`),
    UNIQUE KEY `uq_admin_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 4. RATE LIMITS TABLE (Brute-Force & Form Spam Throttling)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `rate_limits` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `action_key` VARCHAR(64) NOT NULL,
    `ip_hash` CHAR(64) NOT NULL,
    `attempts` INT UNSIGNED NOT NULL DEFAULT 1,
    `window_start` DATETIME NOT NULL,
    `locked_until` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_rate_action_ip` (`action_key`, `ip_hash`),
    KEY `idx_rate_window` (`window_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------------------------
-- 5. SITE SETTINGS TABLE (Optional Verified Company Info & Admin Toggles)
-- ----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `site_settings` (
    `setting_key` VARCHAR(100) NOT NULL,
    `setting_value` TEXT DEFAULT NULL,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
