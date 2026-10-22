# CreditReady — Cameroon SME Credit Readiness

A responsive bilingual (English/French) PHP + MariaDB starter application based on the Group 5 CreditReady brief.

## Included features

- English / French interface switch.
- Responsive landing page and mobile-friendly dashboard.
- Business-owner and lender accounts, with role-specific workspaces.
- Business profile and activity recording for sales, expenses, purchases, invoices and other activity.
- Transparent readiness checklist and percentage based on completeness of the submitted file (not a credit score).
- Lender criteria setup: amount range in XAF, sectors, years operating, registration and business-bank-account requirements.
- Rule-based comparison of business details against lender criteria.
- Explicit, lender-specific consent before a lender can view a business profile.
- Consent management and revocation with an event trail.
- Lender view of consented business profiles and decision recording with mandatory reasons.
- Printable business summary (browser print / save as PDF).
- Prepared database tables for document metadata and readiness history.
- CSRF checks on state-changing forms, prepared SQL statements, password hashing, output escaping and session ID regeneration.

## Requirements

- PHP 8.1+ (8.2+ recommended) with PDO MySQL enabled.
- MariaDB 10.5+ or MySQL 8+.
- Apache / XAMPP / WAMP or another PHP-capable web server.

## Local setup with XAMPP

1. Copy the `CreditReady` folder into `C:\xampp\htdocs\`.
2. Start Apache and MySQL in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Import `database.sql` using the Import tab. It creates the `creditready` database and tables.
5. If your database username/password differs from the local XAMPP defaults, update `config.php` or set the `CREDITREADY_DB_HOST`, `CREDITREADY_DB_NAME`, `CREDITREADY_DB_USER`, and `CREDITREADY_DB_PASS` environment variables.
6. Visit `http://localhost/CreditReady/`.
7. Register a Business Owner account and a separate Lender account in another browser session or after signing out.

If installed in a different path, set `APP_BASE_URL` in `config.php` to that path, for example `/projects/CreditReady`. Leave it empty when the application is at the domain root.

## Demonstration flow

1. Register as a business owner (e.g. a tailor, market trader, farmer or transport operator).
2. Fill in business profile fields and add at least three activity records.
3. Register a lender account separately and publish lending criteria.
4. Sign back in as the business owner, open Lenders, review match explanations, and explicitly consent to share with a lender.
5. Sign in as the lender, open Matched businesses, review only consented profiles, and record a decision with a reason.
6. Return to the owner account and revoke consent in Manage data sharing.
7. Print the business summary to PDF.

## Important implementation notes

- The readiness percentage is a transparent completeness checklist only. It is not a credit score, risk rating, or approval prediction.
- Matching uses the lender criteria currently stored in the database. It is an initial rule-based implementation, not a guarantee that a lender will approve a loan.
- This prototype uses manual information entry. The `documents` table is prepared for a secure upload module, but upload/download routes are not implemented in this build.
- The monthly progress table is prepared, but a complete historical trend interface is not yet included.
- Admin role is reserved in the schema, but public registration intentionally cannot create admin users and full admin management is not implemented.
- Production use requires a security review, HTTPS, secure file storage, backup/retention policy, rate limiting, audit review, data-protection/legal review for Cameroon and each target jurisdiction, and verification of each lender's published criteria.
- Do not use real customer financial data in this classroom/demo build until security and privacy controls have been reviewed.

## Stack restriction

The interface uses HTML and CSS with small progressive-enhancement JavaScript only for the registration form and print action; the application logic is PHP and SQL/MariaDB. If the project requirement literally forbids all JavaScript, remove the short script at the bottom of `register.php` (server-side validation remains) and replace the print button in `summary.php` with instructions to use the browser's Print menu.
