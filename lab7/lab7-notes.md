# Lab 7 Notes — Peer Tutoring Hub

## Part 1 — AI Application Generation

### 1. AI prompt used

I uploaded the Lab 6 application and the Lab 7 design and reference materials, then used this prompt:

> Generate code for all user management use cases UM-01 through UM-10 in the User Management Design Document.
>
> Use the Application Builder, Test Harness Generator, and Traceability Auditor documents as implementation guidance. Follow the authentication, authorization, session management, password hashing, CSRF protection, and role based access practices in the Authentication Reading Handout.
>
> Use lab6.zip as the existing Peer Tutoring Hub application to extend. Preserve the Lab 6 three tier architecture and update the application as needed to support user registration, login, logout, profile management, administrator access, and protected tutoring request workflows.
>
> Return a complete Lab 7 application package with all updated and new files.

### 2. Uploaded source files

1. `lab6.zip` — existing Peer Tutoring Hub application.
2. `Peer_Tutoring_Hub_User_Management_With_Diagrams.docx` — user management design document and use cases UM-01 through UM-10.
3. `IS6465_Authentication_Reading_Handout_v1.2_[16Nov2026].pdf` — authentication, sessions, and authorization guidance.
4. `application-builder(1).txt` — application implementation guidance.
5. `test-harness-generator.txt` — automated test guidance.
6. `traceability-auditor.txt` — requirements traceability and review guidance.

### 3. How Lab 7 extends Lab 6

Lab 6 allowed users to submit, view, edit, and cancel tutoring requests using PHP, PDO, and SQLite. Lab 7 builds on that application by adding user accounts, registration, login and logout, profile management, and administrator functions. It also adds security checks, including password hashing, PHP sessions, CSRF tokens, and role-based authorization. The existing tutoring request workflow is preserved but now restricts student access to their own records.

### 4. Generated code verified against the reading handout

I reviewed the generated `lab7/includes/auth.php` file and compared its `loginSession()` function with the **Start the authenticated session** section of the Authentication Reading Handout (page 4). The generated function calls `session_regenerate_id(true)` after login and stores the authenticated user's ID, role, display name, and last activity time in `$_SESSION`. This matches the handout's recommendation to regenerate the session ID after login and store a limited amount of user information on the server. I verified the presence of this code in the generated file; actual login behavior is checked separately during application testing.

```php
function loginSession(array $u): void {
    startSecureSession();
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $u['user_id'];
    $_SESSION['role'] = $u['role'];
    $_SESSION['display_name'] = $u['full_name'];
    $_SESSION['last_activity'] = time();
    csrfToken();
}
```

---

## Part 2 — SQLite Database Setup

### 1. Database setup commands

The database setup instructions included the following two commands from inside the `lab7` folder:

```bash
sqlite3 tutoring.db < setup-database.sql
sqlite3 tutoring.db < upgrade-to-user-management.sql
```

The first command creates the original tutoring database tables. The second command upgrades the database for user management.

### 2. Database table verification

After completing the setup, I checked the database tables using:

```bash
sqlite3 database/tutoring.db
.tables
.quit
```

I confirmed that the three required tables appeared: `students`, `tutoring_requests`, and `users`.

### 3. Database table output

![Table display](images/part2.png)

I also ran `PRAGMA table_info(students);` and confirmed that `students.user_id` exists. A separate query confirmed that the administrator account had the `Administrator` role and `Active` status.


---

## Part 3 — Administrator Account Creation

I created a test administrator account using the administrator creation script, then verified the account in SQLite. I ran:

```sql
SELECT user_id, full_name, email, role, status
FROM users
WHERE email = 'admin@example.com';
```

**Actual result:**

```text
1|Test Administrator|admin@example.com|Administrator|Active
```

The database showed that the administrator account existed, had the `Administrator` role, and was `Active`.

---

## Part 4 — Run the Application

I started the PHP application in Codespaces using:

```bash
php -S 0.0.0.0:8000
```

The application opened through forwarded port 8000 and displayed the login form. While logged out, I tried to open `/php/profile.php`. The application returned me to the login page rather than showing the profile. **Result: Pass** for this unauthenticated-access check.

---

## Part 5 — Student Workflow Testing

I tested the Peer Tutoring Hub as a student using a test account. The table records what I did, what I expected, and what actually happened.

| Test | What I did | Expected result | Actual result | Status |
|---|---|---|---|---|
| 1. Register student | Submitted the registration form with the Student account type. | Account is created and registration confirmation appears. | The application displayed “Account created. You may now log in.” | **Pass** |
| 2. Student login | Logged in with the newly registered student credentials. | Login succeeds and the protected profile is accessible. | The application opened `/php/profile.php` and displayed Tom Holland and `tholland@sample.com`. | **Pass** |
| 3. Update profile | Changed the name from Tom Holland to Thomas Holland and saved. | The updated name is saved and displayed. | The name was updated successfully; Thomas Holland appeared on later request pages. | **Pass** |
| 4. Create request | Submitted a tutoring request for IS 6570, topic Study plan, preferred date November 23, 2026, evening. | A request is created with a unique ID and `Open` status. | Request #2 displayed the entered details and `Open` status. | **Pass** |
| 5. Edit request | Edited Request #2 to add review of key cybersecurity concepts for the final exam. | Changes save without changing request ID or `Open` status. | The updated description appeared on Request #2; its status remained `Open`. | **Pass** |
| 6. View requests | Opened the tutoring requests list after editing. | The student can see their saved request and updated information. | Request #2 appeared with Thomas Holland, IS 6570, the updated description, November 23, 2026, and `Open` status. | **Pass** |
| 7. Logout | Clicked Logout while signed in. | Session ends, browser redirects to login, and protected pages are inaccessible. | Logout function worked successfully | **Pass** |

### Logout defect and correction

When I clicked **Logout**, PHP reported:

```text
Warning: setcookie() expects parameter 4 to be string, array given
Warning: Cannot modify header information - headers already sent
```

The warnings pointed to `includes/auth.php` and `php/logout.php`. I inspected `logoutSession()` and found that the `setcookie()` call passed an options array in the wrong argument position. The recommended correction is to use the options-array form of `setcookie()` and place the expiration time inside the array:

```php
setcookie(session_name(), '', [
    'expires' => time() - 42000,
    'path' => $params['path'],
    'domain' => $params['domain'],
    'secure' => $params['secure'],
    'httponly' => $params['httponly'],
    'samesite' => $params['samesite'] ?? 'Lax'
]);
```

**Retest status:** Pass. 

### Evidence

![Request details](images/part5.png)

## Part 6 – Administrator Workflow Testing

**1. What is authentication in this application?**

Authentication is the process of verifying a user's identity when they log into the Peer Tutoring Hub. The application checks the email and password entered against the account information stored in the database. Once the credentials are verified, the user can access the application based on their account.

**2. What is authorization in this application?**

Authorization determines what a user is allowed to access after logging in. The application uses different roles, such as Student and Administrator, to control access to certain pages and functions. For example, administrators can manage users, while students have access to their own profiles and tutoring requests.

**3. How is student access different from administrator access?**

Students can view and update their own profiles and create, edit, or cancel their own tutoring requests. Administrators have broader access, allowing them to view registered users, manage accounts, and view tutoring requests from different students. I verified this when my student account displayed only Request #2, while the administrator account displayed both Request #1 and Request #2.

**4. Which functions or pages should be limited to administrators?**

The Manage Users page and functions that allow changes to user roles and account statuses should be restricted to administrators. Administrators should also have access to viewing and managing tutoring requests across different student accounts. Regular students should not have permission to change another user's role, deactivate accounts, or access another student's private information.

**5. What happens after logout if a user tries to visit a protected page directly?**

After logging out, the user's authenticated session should be terminated. If the user attempts to access a protected page such as `profile.php`, the application should redirect them to the login page instead of displaying protected information. I verified this behavior during the student workflow testing after correcting an issue with the logout function.

### Administrator Screenshot

**Screenshot:** 

![Manage Users list](images/part6.png)

## Part 7 – Trace Registration and Login

### Student Registration Workflow

**1. Which file displays the Create Account form?**

The `php/register.php` file displays the Create Account form. It contains the fields for full name, email, password, password confirmation, and account type.

**2. Which file receives the submitted registration form?**

The `php/register.php` file also receives the submitted form using the POST method. It processes the request by checking the CSRF token and calling the registration service.

**3. Which file or function creates and verifies the CSRF token?**

The `includes/auth.php` file contains the `csrfToken()`, `csrfField()`, and `verifyCsrfToken()` functions. These functions generate the token, add it to the registration form, and verify it when the form is submitted. This helps protect against Cross-Site Request Forgery attacks.

**4. Which service validates the registration information?**

The `services/userService.php` file contains the `registerUser()` function. It uses `validateIdentity()` and `validatePassword()` to check the user's information. It also verifies that the passwords match, the email is not already registered, and the selected role is Student or Tutor.

**5. Where is the password hashed?**

The password is hashed inside the `registerUser()` function in `services/userService.php`, using PHP's `password_hash()` function with `PASSWORD_DEFAULT`. This ensures that the database stores a password hash instead of the actual password.

**6. Which repository files insert user and student data?**

The `repositories/userPDO.php` file contains the `insertUser()` function, which inserts the new account into the users table.

The `repositories/studentPDO.php` file contains student database functions, including `insertStudentIfAbsent()`. However, the current registration workflow inserts the student record directly through a prepared SQL statement in `services/userService.php`, rather than calling the student repository.

**7. Which database tables receive records when a student registers?**

Two SQLite tables receive records:

- `users` stores the account information, including name, email, password hash, role, and status.
- `students` stores the student's name, email, and associated `user_id`.

The `user_id` connects the student record to the registered user account.

### User Login Workflow

**1. Which file displays the login form?**

The `php/login.php` file displays the login form. It provides the email and password fields that users enter to access the Peer Tutoring Hub application.

**2. Which file receives the submitted login credentials?**

The `php/login.php` file also receives the submitted login credentials using the POST method. It verifies the CSRF token and passes the submitted information to the login service for authentication.

**3. Which repository retrieves a user by email address?**

The `repositories/userPDO.php` file retrieves user information from the SQLite database using the `findUserByEmail()` function. This function searches the `users` table for the email address submitted during login.

**4. Where is the submitted password verified?**

The submitted password is verified in `services/userService.php` using PHP's `password_verify()` function. The function compares the entered password against the password hash stored in the database without needing to decrypt the stored password.

**5. What session actions occur after successful login?**

After a successful login, the application calls the `loginSession()` function in `includes/auth.php`. This function starts a secure session, regenerates the session ID, and stores the user's ID, role, display name, and last activity time in the session. It also ensures that a CSRF token is available.

**6. Why should the application regenerate the session ID after login?**

Regenerating the session ID helps prevent session fixation attacks. If an attacker obtained or controlled a session ID before login, regenerating it makes that previous ID unusable for the newly authenticated session. This adds another layer of security to the application.

**7. How does the application determine whether the logged-in user is a student or administrator?**

The application retrieves the user's role from the `users` table in the SQLite database. The role is stored in the session after login. Functions such as `requireRole()` in `includes/auth.php` use this information to determine which pages and actions the user is authorized to access. Students are limited to their own tutoring requests, while administrators can access user-management functions and requests from different students.

### Login Workflow Summary

When a user submits the login form, `login.php` receives the email and password and performs the CSRF security check. The application uses the user service and repository to retrieve the account from SQLite and verify the password. If the credentials are correct and the account is active, the application creates an authenticated session and provides access based on the user's role.

### Why Password Hashing and Verification Belong in the Application Layer

Password hashing and verification should occur in the application layer because this is where the authentication and business logic are handled. Keeping these functions in the service layer separates security processing from the presentation pages and database setup scripts. The database should store password hashes rather than plain-text passwords, reducing the risk of exposing actual passwords if the database is compromised.

## Part 8 – Run the Automated Tests

### Test Group 1: Validation Tests

**Command:**

```bash
php tests/validation-tests.php
```

**Complete Terminal Output:**

```text
Xdebug: [Step Debug] Could not connect to debugging client. Tried: localhost:9000 (through xdebug.client_host/xdebug.client_port) :-(
<p>Valid time: PASS</p><p>Missing time: PASS</p><p>Altered value: PASS</p><p>Multiple valid topics: PASS</p><p>Missing topics: PASS</p><p>Altered topic: PASS</p><p>Minimum length: PASS</p><p>Maximum length: PASS</p><p>Too short: PASS</p><p>Too long: PASS</p>
Validation failures: 0
```

**Test Result: Pass – 10 passed, 0 failed.**

All validation tests passed successfully. These tests verified that the application correctly handles valid and invalid input, including missing information, altered values, topic selections, and text length requirements.

### Test Group 2: User Management Tests

**Command:**

```bash
php tests/user-management-tests.php
```

**Complete Terminal Output:**

```text
Xdebug: [Step Debug] Could not connect to debugging client. Tried: localhost:9000 (through xdebug.client_host/xdebug.client_port) :-(
PASS UM-01 student active
PASS UM-01 password hashed
PASS UM-01 public administrator denied
PASS UM-01 duplicate rejected
PASS UM-01 linked student profile
PASS UM-01 tutor registration
PASS UM-09 valid login
PASS UM-09 invalid password denied
PASS UM-04 profile update
PASS UM-04 linked profile synchronized
PASS UM-04 duplicate email denied
PASS UM-05 new password only
PASS UM-05 incorrect current password denied
PASS UM-03 administrator search repository
PASS UM-07 inactive login denied
PASS UM-06 role update
PASS UM-08 deletion blocked pending policy
PASS UM-07 multiple administrators allow deactivation
PASS UM-07 last active admin protected
PASS Student owns created request
PASS Another user cannot read request
PASS Non-student cannot create request
PASS Other user cannot edit request
PASS Other user cannot cancel request
PASS Ownership-filtered listing
Results: 25 passed, 0 failed
```

**Test Result: Pass – 25 passed, 0 failed.**

All 25 user management tests passed. These tests verified account registration, password hashing, login validation, profile management, role updates, account status restrictions, and access controls.

The tests also confirmed that students cannot access or modify tutoring requests belonging to another user. Administrator protections were verified, including preventing the deactivation of the last active administrator.

### Test Group 3: Integration Tests

**Command:**

```bash
php tests/integration-tests.php
```

**Complete Terminal Output:**

```text
Xdebug: [Step Debug] Could not connect to debugging client. Tried: localhost:9000 (through xdebug.client_host/xdebug.client_port) :-(
PASS UC01 Create and Open
PASS Lab 4 checkbox values preserved
FAIL Duplicate email reuses student
FAIL UC02 course and status filter
PASS Invalid reason rejected
FAIL Invalid submission writes nothing
PASS Edit Open request
PASS Updated reason persisted
PASS Cancel Open request
PASS Cancelled row retained
PASS Second cancellation rejected
PASS Unrelated request unchanged
PASS FK enforced
PASS UC02 empty filter result
FAILURES: 3
```

**Test Result: Fail – 11 passed, 3 failed.**

The integration tests verified how the application's services, repositories, and SQLite database work together. Most tests passed, including request creation, editing, cancellation, foreign key enforcement, and several validation checks.

However, three integration tests failed and require further investigation.

### Failed Integration Test Investigation

**1. Failed Test: Duplicate email reuses student**

**What the Test Checks:** This test checks whether the application reuses an existing student record when the same email address is submitted instead of creating a duplicate student record.

**Possible Issue:** The student repository or the relationship between the `students` and `users` tables may need further investigation. The failure could involve how existing student records are identified or reused.

**2. Failed Test: UC02 course and status filter**

**What the Test Checks:** This test checks whether the application correctly filters tutoring requests based on the selected course and request status.

**Possible Issue:** The request repository's filtering logic may need further investigation to determine why the expected records were not returned. The SQL query or the way filter values are passed between the application layers could be contributing to the failure.

**3. Failed Test: Invalid submission writes nothing**

**What the Test Checks:** This test verifies that invalid tutoring request submissions do not create or modify database records.

**Possible Issue:** The request validation service and database transaction handling may need further investigation. It is possible that a database operation occurs before all validation checks are completed, or that the test detects an unexpected database change.

### Automated Testing Summary

| Test Group | Passed | Failed | Result |
|---|---:|---:|---|
| Validation Tests | 10 | 0 | Pass |
| User Management Tests | 25 | 0 | Pass |
| Integration Tests | 11 | 3 | Fail |
| **Total** | **46** | **3** | **49 tests** |

Overall, I ran 49 automated tests for the Peer Tutoring Hub application. Of those tests, 46 passed and 3 failed.

The validation and user management tests passed without any failures. The integration tests identified three areas that may require further investigation involving student records, request filtering, and invalid database submissions.

An Xdebug connection warning appeared during testing, but it did not prevent the tests from executing.

I did not modify any of the automated test files, as instructed in the lab requirements.

## Part 9 – AI Skills Pack Process

### Application Builder

**AI Prompt:**

Explain how a student registration request moves from register.php through authentication checks, PHP services, repositories, PDO, and SQLite in the Peer Tutoring Hub application. Identify the responsibility of each file or layer.

**AI Response:**

The Peer Tutoring Hub application uses a three-tier architecture to separate the presentation layer, business logic, and database operations.

The registration process begins in `php/register.php`, which displays the registration form and receives the submitted information. The application uses security functions from `includes/auth.php` to generate and verify a CSRF token.

Next, the registration information is passed to `services/userService.php`, where the application validates the submitted information, checks for duplicate accounts, and hashes the password.

The `repositories/userPDO.php` file handles user database operations using PDO. The database connection is established through `config/db.php`, which connects the application to SQLite.

Finally, the account information is stored in the `users` table, and a linked student record is created in the `students` table. In the current implementation, the student record is inserted through a prepared SQL statement in the registration service.

**My Evaluation of the AI Response:**

I selected the AI statement that the application uses CSRF protection during registration.

I reviewed the `includes/auth.php` file and confirmed that it contains the `csrfToken()` and `verifyCsrfToken()` functions. The first function generates a random token and stores it in the session, while the second function compares the submitted token against the stored token using `hash_equals()`.

This confirms that the application contains the CSRF protection functions described by the AI. The remaining step is to verify that `register.php` calls these functions during registration.

**Evaluation Result:** Partially verified against the actual PHP code.