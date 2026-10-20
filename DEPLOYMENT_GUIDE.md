# Deployment, Hosting, DNS & SSL Configuration Guide (`skycadigital.com`)

## 1. Live Hosting Environment Inspection Summary

Before building the deployment configuration, we inspected `https://skycadigital.com` and confirmed:
- **Hosting Provider:** Hostinger (`platform: hostinger`, `panel: hpanel`, `Server: hcdn`)
- **Active Server Runtime:** `PHP 8.3.35`
- **DNS A / AAAA Records:** Already pointed to Hostinger edge IPs (`93.127.173.114`, `147.79.69.57`, `2a02:4780:...`)
- **HTTPS / SSL Status:** Active HTTP/3 (`h3=":443"`) SSL certificate installed on `skycadigital.com`
- **Target MySQL Database:** `u591469534_skyca`

---

## 2. Step-by-Step Hostinger hPanel Git Deployment

1. Log in to **Hostinger hPanel** and select **skycadigital.com**.
2. Remove the default Hostinger placeholder file (`default.php` or `index.html`) from `public_html` if present.
3. Navigate to **Advanced -> Git**:
   - **Repository:** `https://github.com/p2pearningplayer-del/skyca.git`
   - **Branch:** `main`
   - **Install Path:** Leave blank (deploys directly to `public_html`)
4. Click **Deploy** (and optionally enable **Auto-Deployment** webhook so future commits to `main` deploy automatically).

---

## 3. Configuring `.env` & MySQL (`u591469534_skyca`)

1. In Hostinger hPanel, go to **Databases -> Management** and locate database `u591469534_skyca`. Note your MySQL username (typically `u591469534_...`) and password.
2. Open **Files -> File Manager**:
   - Preferably create `.env` in `/domains/skycadigital.com/.env` (one folder above `public_html` so it sits completely outside the web root), **or** inside `public_html/.env` (which is blocked from public access by `.htaccess`).
   - Copy the contents of `.env.example` into `.env` and fill in:
     - `DATABASE_USER` and `DATABASE_PASSWORD`
     - `ADMIN_INITIAL_PASSWORD` (at least 10 characters; used once to initialize the `/admin` account)
     - `IP_HASH_SALT` (a random 64-character string)
     - `SMTP_ENABLED=true` and `SMTP_PASSWORD` once your `info@skycadigital.com` mailbox password is ready.
3. Open **phpMyAdmin** for `u591469534_skyca`:
   - Check if any tables exist.
   - Import `database/schema.sql` (or simply load `https://skycadigital.com/admin` once after setting `.env`—the application will run non-destructive `CREATE TABLE IF NOT EXISTS` automatically).

---

## 4. DNS, SSL & Security Verification

- **HTTPS Redirects:** `.htaccess` automatically enforces `301` redirects from `http://` to `https://skycadigital.com` and from `www.skycadigital.com` to `https://skycadigital.com`.
- **Protected Directories:** `.htaccess` blocks direct HTTP requests to `/config/`, `/database/`, `/includes/`, `/pages/`, `/storage/`, `/tests/`, and any `.env`, `.sql`, `.sqlite`, `.log`, or `.md` files.
- **Google Search Console & Analytics:**
  - Submit `https://skycadigital.com/sitemap.xml` in Google Search Console.
  - When ready to run Google Analytics 4, Google Ads, or Meta Ads, add `GOOGLE_ANALYTICS_ID`, `GOOGLE_ADS_CONVERSION_ID`, `GOOGLE_ADS_CONVERSION_LABEL`, or `META_PIXEL_ID` in `.env`.
