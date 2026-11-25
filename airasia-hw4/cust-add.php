<?php

require_once __DIR__ . '/includes/authorization.php';
requireRole('Admin');

require_once __DIR__ . '/includes/db.php';

$errors = [];
$success = false;

$firstName = '';
$lastName = '';
$userName = '';
$accountType = 'Standard';
$points = '0';

$accountTypes = [
    'Standard',
    'Silver',
    'Gold',
    'Platinum'
];

// Generate CSRF protection token.
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] =
        bin2hex(random_bytes(32));
}

function customerInput($field)
{
    $value = $_POST[$field] ?? '';

    return is_string($value) ? trim($value) : '';
}

function customerEscape($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = customerInput('firstName');
    $lastName = customerInput('lastName');
    $userName = customerInput('userName');
    $accountType = customerInput('accountType');
    $points = customerInput('points');

    $password = $_POST['password'] ?? '';

    $token = $_POST['csrf_token'] ?? '';

    // Verify the form submission token.
    if (!is_string($token) ||
        !hash_equals($_SESSION['csrf_token'], $token)) {

        $errors[] = 'Invalid form submission.';
    }

    // Validate customer information.
    if ($firstName === '' || strlen($firstName) > 100) {
        $errors[] = 'Enter a valid first name.';
    }

    if ($lastName === '' || strlen($lastName) > 100) {
        $errors[] = 'Enter a valid last name.';
    }

    if ($userName === '' || strlen($userName) > 100) {
        $errors[] = 'Enter a valid username.';
    }

    if (!is_string($password) ||
        strlen($password) < 8 ||
        strlen($password) > 72 ||
        strpos($password, "\0") !== false) {

        $errors[] = 'Password must contain 8 to 72 bytes and no null characters.';
    }

    if (!in_array($accountType, $accountTypes, true)) {
        $errors[] = 'Select a valid account type.';
    }

    $validatedPoints = filter_var(
        $points,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 0]]
    );

    if ($validatedPoints === false) {
        $errors[] = 'Points must be a nonnegative integer.';
    }

    // Continue only when validation passes.
    if (empty($errors)) {

        $pdo = null;

        try {

            $pdo = getDBConnection();

            $pdo->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

            $pdo->exec('PRAGMA foreign_keys = ON');

            // Check for an existing username.
            $stmt = $pdo->prepare(
                'SELECT userId FROM USER
                 WHERE userName = :userName'
            );

            $stmt->execute([
                ':userName' => $userName
            ]);

            if ($stmt->fetchColumn() !== false) {

                $errors[] = 'Username already exists.';

            } else {

                // Hash the password.
                $passwordHash = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                // Start database transaction.
                $pdo->beginTransaction();

                // Create the USER record.
                $stmt = $pdo->prepare(
                    'INSERT INTO USER
                    (userName, password, firstName,
                     lastName, role)
                    VALUES
                    (:userName, :password, :firstName,
                     :lastName, :role)'
                );

                $stmt->execute([
                    ':userName' => $userName,
                    ':password' => $passwordHash,
                    ':firstName' => $firstName,
                    ':lastName' => $lastName,
                    ':role' => 'Customer'
                ]);

                // Retrieve the new user ID.
                $userId = (int) $pdo->lastInsertId();

                // Create the associated ACCOUNT.
                $stmt = $pdo->prepare(
                    'INSERT INTO ACCOUNT
                    (userId, accountType, points)
                    VALUES
                    (:userId, :accountType, :points)'
                );

                $stmt->execute([
                    ':userId' => $userId,
                    ':accountType' => $accountType,
                    ':points' => $validatedPoints
                ]);

                // Save both records.
                $pdo->commit();

                $success = true;

                // Rotate the form token.
                $_SESSION['csrf_token'] =
                    bin2hex(random_bytes(32));
            }

        } catch (Throwable $e) {

            if ($pdo && $pdo->inTransaction()) {
                $pdo->rollBack();
            }

            error_log($e->getMessage());

            $errors[] =
                'Unable to create customer. Please try again.';
        }
    }

    unset($password);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>AirAsia - Add Customer</title>

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<?php require __DIR__ . '/includes/nav.php'; ?>

<main class="container">

    <h1>Add Customer</h1>

    <?php if (!empty($errors)): ?>

        <div role="alert" style="color: red;">

            <?php foreach ($errors as $error): ?>

                <p><?= customerEscape($error) ?></p>

            <?php endforeach; ?>

        </div>

    <?php endif; ?>

    <?php if ($success): ?>

        <h2 style="color: green;">
            Customer created successfully.
        </h2>

        <p>User ID: <?= customerEscape($userId) ?></p>

        <p>
            Name:
            <?= customerEscape($firstName) ?>
            <?= customerEscape($lastName) ?>
        </p>

        <p>Username: <?= customerEscape($userName) ?></p>

        <p>Role: Customer</p>

        <p>
            Account Type:
            <?= customerEscape($accountType) ?>
        </p>

        <p>
            Available Points:
            <?= number_format($validatedPoints) ?>
        </p>

        <a href="cust-add.php">Add Another Customer</a>

        <br><br>

        <a href="card-list.php">Return to Gift Card List</a>

    <?php else: ?>

        <form action="cust-add.php" method="POST">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= customerEscape(
                       $_SESSION['csrf_token']
                   ) ?>">

            <div>
                <label for="firstName">First Name</label>
                <input type="text"
                       id="firstName"
                       name="firstName"
                       maxlength="100"
                       value="<?= customerEscape($firstName) ?>"
                       required>
            </div>

            <br>

            <div>
                <label for="lastName">Last Name</label>
                <input type="text"
                       id="lastName"
                       name="lastName"
                       maxlength="100"
                       value="<?= customerEscape($lastName) ?>"
                       required>
            </div>

            <br>

            <div>
                <label for="userName">Username</label>
                <input type="text"
                       id="userName"
                       name="userName"
                       maxlength="100"
                       value="<?= customerEscape($userName) ?>"
                       required>
            </div>

            <br>

            <div>
                <label for="password">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       autocomplete="new-password"
                       minlength="8"
                       maxlength="72"
                       required>
            </div>

            <br>

            <div>
                <label for="accountType">Account Type</label>

                <select id="accountType"
                        name="accountType"
                        required>

                    <?php foreach ($accountTypes as $type): ?>

                        <option
                            value="<?= customerEscape($type) ?>"
                            <?= $accountType === $type
                                ? 'selected' : '' ?>>

                            <?= customerEscape($type) ?>

                        </option>

                    <?php endforeach; ?>

                </select>
            </div>

            <br>

            <div>
                <label for="points">Initial Points</label>

                <input type="number"
                       id="points"
                       name="points"
                       min="0"
                       step="1"
                       value="<?= customerEscape($points) ?>"
                       required>
            </div>

            <br>

            <button type="submit">Add Customer</button>

            <a href="card-list.php">Cancel</a>

        </form>

    <?php endif; ?>

</main>

</body>
</html>
