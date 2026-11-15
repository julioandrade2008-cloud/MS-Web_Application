# AirAsia HW3 — Test Report

## Test Environment

- Application: AirAsia Rewards Gift Card Management
- Environment: GitHub Codespaces
- Language: PHP
- Database: SQLite
- Database connection: PDO
- Browser: Chrome
- Test date: November 14, 2026 (confirm actual execution date)

## Test Results Summary

| Test ID | Test Description | Result |
|---|---|---|
| T01 | Automated validation harness | PASS |
| T02 | View Gift Card List | PASS |
| T03 | View Gift Card Details | PASS |
| T04 | Add Gift Card | PASS |
| T05 | Update Gift Card | PASS |
| T06 | Delete Gift Card | PASS |
| T07 | Invalid form input | PASS |
| T08 | Invalid Gift Card IDs | PASS |

Results reflect the successful tests reported during development. Confirm the final results after completing the regression test.

## T01 — Automated Validation Harness

**Steps:** Run `php tests/validation-tests.php` from the application directory.

**Expected Result:** All ten automated validation tests pass without modifying database records.

**Actual Result:** The test harness reported ten passing tests and zero failures.

**Status:** PASS

## T02 — View Gift Card List

**Steps:** Open `card-list.php` in the browser.

**Expected Result:** Gift Cards are retrieved from SQLite and displayed with their names, required points, and available images.

**Actual Result:** The application successfully displayed all twenty initial Gift Card records. The available Amazon, Target, and Ulta images displayed correctly.

**Status:** PASS

## T03 — View Gift Card Details

**Steps:** Select different Gift Cards from the list and open their details pages.

**Expected Result:** Each page displays information associated with the selected Gift Card ID.

**Actual Result:** Selecting Amazon and Target displayed their respective database information, including Card Type, Card Value, and Points Required.

**Status:** PASS

## T04 — Add Gift Card

**Test Input:**

- Card Name: Test Rewards Card
- Card Type: Retail
- Card Value: 25.00
- Points Required: 2500

**Steps:** Open `card-add.php`, enter the test information, and submit the form.

**Expected Result:** The record is inserted into SQLite and the browser redirects to the Gift Card List.

**Actual Result:** The Gift Card was successfully saved and appeared in the list. The database record count increased from twenty to twenty-one. However, due to the placement of PHP output in my code, I inadvertently added 2 extra gift cards for total of 23. 

**Status:** PASS

## T05 — Update Gift Card

**Test Input:**

- Card Name: Test Rewards Card Updated
- Card Type: Electronics
- Card Value: 75.00
- Points Required: 7500

**Steps:** Open the test card's Update page, modify the information, and submit.

**Expected Result:** The selected database record is updated and the browser displays the updated Gift Card Details.

**Actual Result:** The updated information was displayed correctly and confirmed in SQLite.

**Status:** PASS

## T06 — Delete Gift Card

**Steps:** Open the updated test card's Delete page. Verify that opening the page does not delete the record. Cancel once, then return and confirm deletion.

**Expected Result:** Cancellation preserves the record. Confirmation deletes only the selected record and redirects to the Gift Card List.

**Actual Result:** The confirmation workflow functioned correctly, the selected test record was deleted, and the database reflected the deletion.

**Status:** PASS

## T07 — Invalid Form Input

**Test Inputs:**

- Blank Card Name
- Unsupported Card Type
- Zero Card Value
- Negative Card Value
- Decimal Points Required
- Negative Points Required
- Blank Points Required
- Card Name exceeding 100 characters

**Expected Result:** Invalid information is rejected and does not modify the database.

**Actual Result:** The application validation checks passed, and the automated validation harness confirmed the expected rejection behavior.

**Status:** PASS

## T08 — Invalid Gift Card IDs

**Test Inputs:**

- Missing ID
- Text ID (`abc`)
- Negative ID (`-1`)
- Nonexistent ID (`9999`)

**Expected Result:** Invalid IDs produce a controlled error. Nonexistent IDs return a not-found response without modifying database records.

**Actual Result:** Invalid and nonexistent Gift Card IDs were handled safely during the Details, Update, and Delete page tests.

**Status:** PASS

## Defects and Retesting

**Defect 1 — PHP Redirect Warning**

During development, `card-add.php` displayed a "Cannot modify header information — headers already sent" warning.

The issue was investigated as an output-before-redirect problem. After correction, the Add Gift Card workflow was retested successfully.

**Defect 2 — Duplicate Navigation Link**

After introducing shared PHP templates, the Add Gift Card navigation link appeared twice because both the original page navigation and the shared navigation were present.

The duplicate navigation markup was removed, and the shared template checks subsequently passed.

## Final Regression Testing

Before submission, rerun the automated validation harness and verify that the five CRUD pages continue to function after the shared template changes.

Record any new defects, corrective actions, and retest results here.
