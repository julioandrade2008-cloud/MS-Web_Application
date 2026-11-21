# Peer Tutoring Hub — Lab 7

Extends the supplied Lab 6 PHP/PDO/SQLite application with user management UM-01 through UM-10.

## Setup (Codespaces, from `lab7/`)

```bash
sqlite3 database/tutoring.db < database/setup-database.sql
php database/upgrade.php
php database/create-admin.php "Test Administrator" admin@example.com "A-unique-test-password-123"
php -S 0.0.0.0:8000 -t . php/router.php
```

The original Lab 6 ZIP includes `database/tutoring.db`; the package omits it so that the above commands create a fresh lab database. The upgrade SQL is supplied for review and a fresh one-time manual migration; `upgrade.php` is safe to rerun and is recommended. If using the SQL script manually, run it only once. Start the PHP server **from lab7 root** with the router command shown; do not start a bare server without the router because that could expose the SQLite database file.

Create Student/Tutor accounts via `php/register.php`, then sign in via `php/login.php`. Administrator creation is CLI-only. The administrator may search users, update roles and status, and inspect all tutoring requests. Permanent deletion is intentionally blocked (UM-08) because the supplied design explicitly leaves the retention policy unresolved. Students may create/view/edit/cancel their own Open requests; tutors cannot use the request workflow. Pre-existing Lab 6 sample requests have no linked user account and are visible to administrators only.

## Tests

```bash
php tests/validation-tests.php
php tests/user-management-tests.php
php tests/integration-tests.php
```

Integration and user management tests use in-memory SQLite databases. The application is a classroom demonstration, not a production-ready identity service. Use fictional credentials and do not commit real data or secrets.
