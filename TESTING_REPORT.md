# Testing Checklist, Verification Results & Known External Dependencies

## 1. Automated Verification Suite Results (`php tests/run_tests.php`)

All **60 automated integration, SEO, and security assertions passed (0 failures)**:

- [x] **Database & Schema Initialization:** Non-destructive `leads`, `lead_notes`, `admin_users`, `rate_limits`, and `site_settings` table creation verified.
- [x] **Homepage (`/`):** Verified HTTP 200, headline, supporting copy, primary/secondary CTAs, 7 service cards, Why Choose Skyca Digital, 5-stage process, industries, capabilities, embedded enquiry form, FAQ accordion, and footer.
- [x] **Services Overview (`/services`):** Verified HTTP 200 and all 7 official service categories and sub-services.
- [x] **All 7 Individual Service Landing Pages (`/services/*`):**
  - `/services/software-development`
  - `/services/web-mobile-development`
  - `/services/saas-cloud-solutions`
  - `/services/ai-it-consulting`
  - `/services/digital-marketing-cybersecurity`
  - `/services/data-analytics-support`
  - `/services/ecommerce-solutions`
  - Verified unique `<title>`, `<meta name="description">`, canonical tag, `Service` + `FAQPage` + `BreadcrumbList` JSON-LD schema, and **preselected service option** in the enquiry form on each page.
- [x] **About Us (`/about`), Contact Us (`/contact`), FAQ (`/faq`), Legal Pages (`/privacy-policy`, `/terms-and-conditions`, `/cookie-policy`), Thank You (`/thank-you`), and Custom 404 Page:** All verified HTTP 200 (and HTTP 404 for unknown routes).
- [x] **XML Sitemap (`/sitemap.xml`) & `robots.txt`:** Valid XML output containing all canonical public URLs.
- [x] **Brand Assets & Broken Links Check:** All 6 referenced CSS, JS, and undistorted official logo assets (`skyca-logo-horizontal.png`, `skyca-logo-horizontal.webp`, `skyca-logo-full.png`, `skyca-logo-full.webp`, `skyca-logo-mark.png`, `skyca-logo-mark.webp`) verified on disk and served cleanly.
- [x] **Contact Form Validation & Spam Protection:**
  - Invalid CSRF token rejected with `HTTP 403`.
  - Honeypot bot submission rejected with `HTTP 400`.
  - Invalid/missing required fields rejected server-side with `HTTP 422` and field-level error messages.
- [x] **Database Insertion & Duplicate Prevention:**
  - Valid enquiry stored transactionally (`HTTP 201`) with generated `SKY-YYYYMMDD-XXXXXX` reference, UTM attribution, and `is_test = 1` flag for test data.
  - Immediate duplicate submission blocked (`HTTP 200`, `"duplicate": true`) without inserting a duplicate database row.
- [x] **SQL Injection & XSS Defenses:**
  - SQLi payloads (`Robert'); DROP TABLE leads;--`) safely stored as literal text via PDO prepared statements (`ATTR_EMULATE_PREPARES = false`).
  - Stored XSS payloads (`<script>alert(1)</script>`) strictly escaped as `&lt;script&gt;alert(1)&lt;/script&gt;` in the Admin Dashboard.
- [x] **Secure Admin Panel (`/admin` & `/api/admin`):**
  - Unauthenticated requests to `/api/admin` blocked with `HTTP 401`.
  - Invalid admin login credentials rejected.
  - Authenticated session login, lead inspection, status update (`New` -> `Qualified`), internal note logging, and CSV export (with formula-injection sanitization `'=cmd|...`) verified.

---

## 2. Remaining Actions Required from the Project Owner in Production

Because secrets and private credentials must never be committed to the public GitHub repository (`https://github.com/p2pearningplayer-del/skyca.git`), complete these steps on your Hostinger server after pulling the `main` branch:

1. **Create `.env` on Hostinger:** Copy `.env.example` to `.env` on your server and enter your live MySQL `DATABASE_USER` and `DATABASE_PASSWORD` for database `u591469534_skyca`.
2. **Initialize Admin Password:** Set `ADMIN_INITIAL_PASSWORD` in `.env` (or run `php database/setup_admin.php info@skycadigital.com "YourStrongPassword"` via SSH) to create your live administrator login for `https://skycadigital.com/admin`.
3. **Configure SMTP Credentials (Optional but Recommended):** Set `SMTP_ENABLED=true`, `SMTP_USER=info@skycadigital.com`, and `SMTP_PASSWORD` in `.env` to enable live email alerts to `info@skycadigital.com`.
4. **Add Tracking IDs When Ready:** Populate `GOOGLE_ANALYTICS_ID`, `GOOGLE_ADS_CONVERSION_ID`, `GOOGLE_ADS_CONVERSION_LABEL`, and/or `META_PIXEL_ID` in `.env` when launching paid campaigns.
5. **Legal & Optional Company Details:** Have a qualified legal advisor review `/privacy-policy`, `/terms-and-conditions`, and `/cookie-policy`, and enter any optional verified company details (office address, CIN, GSTIN, WhatsApp number) in `/admin?tab=settings`.
