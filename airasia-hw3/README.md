# AirAsia Rewards Gift Card Management — HW3

## Project Overview

This project is a PHP web application developed for IS 6465 — Web Based Applications. It extends the static AirAsia Gift Card pages created in HW2 by connecting them to a SQLite database using PHP Data Objects (PDO).

The application supports creating, viewing, updating, and deleting Gift Card records. It also includes server-side validation, shared PHP templates, and an automated validation test harness.

## Technologies Used

- PHP
- SQLite
- PDO (PHP Data Objects)
- HTML and CSS
- GitHub Codespaces
- GitHub for version control

## Application Features

**Gift Card List (`card-list.php`)**

Retrieves Gift Card records from SQLite and displays the card name, image when available, and points required.

**Gift Card Details (`card-details.php`)**

Retrieves a selected Gift Card by its ID and displays its name, type, value, and required points.

**Add Gift Card (`card-add.php`)**

Allows users to enter new Gift Card information. Submitted information is validated before being inserted into SQLite.

**Update Gift Card (`card-update.php`)**

Retrieves existing Gift Card information, populates an editable form, validates submitted changes, and updates the selected database record.

**Delete Gift Card (`card-delete.php`)**

Displays a confirmation page before deleting a selected Gift Card using an HTTP POST request.

## Database

The application uses `database/rewards.db`, a SQLite database containing two tables:

**ACCOUNT**

- `accountId`
- `userId`
- `accountType`
- `points`

**GIFTCARD**

- `cardId`
- `cardName`
- `cardType`
- `cardValue`
- `points`

The initial seed data includes five ACCOUNT records and twenty GIFTCARD records.

The database schema follows the AirAsia Requirements Document.

## Setup Instructions

1. Open the repository in GitHub Codespaces.
2. Navigate to the `airasia-hw3` directory.
3. Confirm PHP and SQLite support are installed.
4. Confirm `database/rewards.db` exists.
5. Start the PHP development server:

   ```bash
   php -S 0.0.0.0:8000
   ```

6. Open the forwarded port 8000 URL.
7. Navigate to `/card-list.php`.

If the database must be recreated, run these commands from `airasia-hw3`:

```bash
sqlite3 database/rewards.db < database/schema.sql
sqlite3 database/rewards.db < database/seed.sql
```

Run the seed script only when initializing a fresh database to avoid inserting duplicate sample records.

## Server-Side Validation

The application validates Gift Card information before adding or updating database records.

Validation includes:

- Card Name is required and cannot exceed 100 characters.
- Card Type must match an allowed category.
- Card Value must be a positive number.
- Points Required must be a positive integer.
- Gift Card IDs must be valid positive integers.

The allowed Card Type categories used in this implementation are Retail, Beauty, Electronics, Food, and Home Improvement. These categories are an implementation assumption based on sample data, rather than an explicit list in the AirAsia Requirements Document.

PDO prepared statements are used for database operations involving submitted information.

## Use Case Traceability

| Use Case | PHP Page | Database Table | Test Evidence |
|---|---|---|---|
| View Gift Card List | `card-list.php` | GIFTCARD | T02 |
| View Gift Card Details | `card-details.php` | GIFTCARD | T03, T08 |
| Add Gift Card | `card-add.php` | GIFTCARD | T04, T07 |
| Update Gift Card | `card-update.php` | GIFTCARD | T05, T07 |
| Delete Gift Card | `card-delete.php` | GIFTCARD | T06, T08 |

## Automated Validation Testing

The automated test harness is located at:

`tests/validation-tests.php`

Run the tests using:

```bash
php tests/validation-tests.php
```

The validation harness includes ten test cases covering valid information, missing values, invalid Card Types, nonpositive Card Values, invalid Points Required, Card Name length, and invalid Gift Card IDs.

The latest reported execution passed all ten tests with zero failures.

## Project Scope

HW3 focuses on Gift Card management using PHP, PDO, and SQLite.

Login, authentication, role-based access control, customer management, and rewards redemption are outside the scope of this homework.

## Additional Documentation

- `test-report.md` — Manual and automated testing results.
- `ai-use.txt` — AI assistance disclosure.
- `tests/validation-results.txt` — Saved automated test output.
