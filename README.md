# Skyca Digital Private Limited — Official Corporate Website & Lead Management Platform

**Official Website:** [https://skycadigital.com](https://skycadigital.com)  
**Company Name:** Skyca Digital Private Limited  
**Business Email:** `info@skycadigital.com`  
**Business Phone:** `+91 6002064873`  
**GitHub Repository:** `https://github.com/p2pearningplayer-del/skyca.git` (`main` branch)  
**Production Database:** `u591469534_skyca` (MySQL / MariaDB)

---

## 1. Architecture & Hosting Compatibility

Live inspection of `https://skycadigital.com` confirmed that the production domain is hosted on **Hostinger (`hpanel` / `hcdn` / `PHP 8.3.35` + Apache/LiteSpeed + MySQL `u591469534_skyca`)**.

To ensure 100% compatibility with Hostinger hPanel Git auto-deployment and zero reliance on unverified background Node.js daemons on shared/business web hosting, this platform is engineered using a **Server-Side Rendered (SSR) Modular PHP 8.3 + MySQL (PDO Parameterized Queries) + Responsive CSS + Progressive ES6 JavaScript** architecture:

- **Front Controller & Clean URL Router:** `index.php` + `.htaccess`
- **Database Layer:** `includes/db.php` using `PDO` with `PDO::ATTR_EMULATE_PREPARES => false` and transactional lead persistence (`$pdo->beginTransaction()` / `$pdo->commit()`).
- **Non-Destructive Database Schema & Migrations:** `database/schema.sql` and `database/migrate.php` (`CREATE TABLE IF NOT EXISTS` exclusively; never drops or overwrites existing production tables).
- **Security Layer:** `includes/security.php` implementing CSRF token verification, XSS output encoding (`skyca_e()`), honeypot & signed timestamp anti-spam tokens, privacy-safe HMAC-SHA256 IP rate limiting, bcrypt/Argon2 password hashing, and CSV formula-injection sanitization.
- **Email Notification Engine:** `includes/mailer.php` implementing authenticated RFC SMTP (`SSL`/`STARTTLS`) notifications to `info@skycadigital.com` and optional client confirmation emails, with non-blocking error handling so stored leads are never lost if SMTP is unconfigured.
- **SEO & Paid Campaign Readiness:** `includes/seo.php` & `assets/js/main.js` providing unique page titles, meta descriptions, canonical URLs, Open Graph tags, Schema.org JSON-LD (`Organization`, `WebSite`, `Service`, `FAQPage`, `BreadcrumbList`), dynamic `/sitemap.xml`, session-persisted UTM attribution capture, and consent-aware GA4 / Google Ads / Meta Pixel integration points.

---

## 2. Website Pages & Routes

| Route | File | Description |
| :--- | :--- | :--- |
| `/` | `pages/home.php` | High-conversion corporate homepage with all 10 required sections |
| `/services` | `pages/services.php` | Comprehensive overview of all 7 official service categories & sub-services |
| `/services/software-development` | `pages/service_detail.php` | 1. Information Technology Services & Software Development |
| `/services/web-mobile-development` | `pages/service_detail.php` | 2. Website & Mobile Application Development |
| `/services/saas-cloud-solutions` | `pages/service_detail.php` | 3. SaaS Solutions & Cloud Services |
| `/services/ai-it-consulting` | `pages/service_detail.php` | 4. Artificial Intelligence Solutions & IT Consulting |
| `/services/digital-marketing-cybersecurity` | `pages/service_detail.php` | 5. Digital Marketing & Cyber Security Services |
| `/services/data-analytics-support` | `pages/service_detail.php` | 6. Data Analytics & Technology Support Services |
| `/services/ecommerce-solutions` | `pages/service_detail.php` | 7. E-Commerce Solutions and Related Technology Activities |
| `/about` | `pages/about.php` | Factual corporate mission, philosophy & editable verified company profile |
| `/contact` | `pages/contact.php` | Direct contact channels + validated project enquiry form |
| `/faq` | `pages/faq.php` | Categorized FAQ knowledge base with `FAQPage` JSON-LD schema |
| `/privacy-policy` | `pages/privacy_policy.php` | Privacy Policy with professional legal review notice |
| `/terms-and-conditions` | `pages/terms_conditions.php` | Terms and Conditions with professional legal review notice |
| `/cookie-policy` | `pages/cookie_policy.php` | Cookie Policy with interactive consent preference trigger |
| `/thank-you` | `pages/thank_you.php` | Post-enquiry confirmation with reference ID & verified conversion trigger |
| `/sitemap.xml` | `pages/sitemap_xml.php` | Dynamic XML Sitemap for Google Search Console |
| `/admin` | `admin.php` | Authenticated Lead Management & Operations Dashboard |

---

## 3. Environment & Secret Configuration (`.env`)

1. Copy `.env.example` to `.env` on the production server (ideally placed one directory above `public_html`, e.g., `/home/u591469534/domains/skycadigital.com/.env`, or inside `public_html/.env` where `.htaccess` blocks all HTTP access to `.env*` files).
2. Configure your MySQL credentials for `u591469534_skyca`:
   ```env
   DATABASE_HOST=localhost
   DATABASE_PORT=3306
   DATABASE_NAME=u591469534_skyca
   DATABASE_USER=your_hostinger_mysql_user
   DATABASE_PASSWORD=your_hostinger_mysql_password
   ```
3. **Never commit `.env` to Git.** `.gitignore` is configured to exclude `.env` and all secret files.

---

## 4. Database Setup & Admin Initialization

### Option A: Automatic Non-Destructive Setup
Once `.env` contains valid MySQL credentials, the application automatically runs `CREATE TABLE IF NOT EXISTS` for `leads`, `lead_notes`, `admin_users`, `rate_limits`, and `site_settings` on first connection without modifying any existing tables.

### Option B: Manual SQL Import (phpMyAdmin) or CLI Migration
- **Via phpMyAdmin (Hostinger hPanel):** Open database `u591469534_skyca`, click **Import**, and upload `database/schema.sql`.
- **Via SSH CLI:**
  ```bash
  php database/migrate.php
  ```

### Creating / Rotating the Admin Account
Run the CLI setup script over SSH:
```bash
php database/setup_admin.php info@skycadigital.com "YourStrongPassword123!"
```
Or set `ADMIN_EMAIL=info@skycadigital.com` and `ADMIN_INITIAL_PASSWORD="YourStrongPassword123!"` in `.env` prior to the first visit, then remove `ADMIN_INITIAL_PASSWORD` from `.env` once the admin account is created.

---

## 5. SMTP Email Notification Configuration

To enable real-time lead notification emails to `info@skycadigital.com`:
1. In Hostinger hPanel -> **Emails**, ensure the mailbox `info@skycadigital.com` is created.
2. In your server `.env` file, set:
   ```env
   SMTP_ENABLED=true
   SMTP_HOST=smtp.hostinger.com
   SMTP_PORT=465
   SMTP_ENCRYPTION=ssl
   SMTP_USER=info@skycadigital.com
   SMTP_PASSWORD=your_mailbox_password
   MAIL_FROM=info@skycadigital.com
   ADMIN_NOTIFICATION_EMAIL=info@skycadigital.com
   SEND_CLIENT_CONFIRMATION_EMAIL=true
   ```
If `SMTP_ENABLED` is `false` or credentials are omitted, enquiries are still stored safely in MySQL (`u591469534_skyca`) and visible in `/admin`.

---

## 6. Running Local Development & Automated Tests

```bash
# Start local development server
php -S 127.0.0.1:8080 index.php

# Run the 60-assertion end-to-end verification & security test suite
php tests/run_tests.php
```
