<?php

require_once __DIR__ . '/includes/authorization.php';
requireRole('Customer');

require_once __DIR__ . '/includes/db.php';

header('Cache-Control: no-store');

$userId = (int) $_SESSION['userId'];

$customer = false;
$accounts = [];
$error = '';

try {

    $pdo = getDBConnection();

    // Retrieve the logged-in customer's information.
    $stmt = $pdo->prepare(
        'SELECT userId, userName, firstName, lastName
         FROM USER
         WHERE userId = :userId
         AND role = :role'
    );

    $stmt->execute([
        ':userId' => $userId,
        ':role' => 'Customer'
    ]);

    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {

        $error = 'Customer information not found.';

    } else {

        // Retrieve accounts belonging to this customer.
        $stmt = $pdo->prepare(
            'SELECT accountId, accountType, points
             FROM ACCOUNT
             WHERE userId = :userId
             ORDER BY accountId'
        );

        $stmt->execute([
            ':userId' => $userId
        ]);

        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (Throwable $e) {

    error_log($e->getMessage());

    $error = 'Unable to retrieve customer information.';
}

function profileEscape($value)
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>AirAsia - Customer Profile</title>

    <link rel="stylesheet" href="css/styles.css">
</head>

<body>

<?php require __DIR__ . '/includes/nav.php'; ?>

<main class="container">

    <h1>My Profile</h1>

    <?php if ($error !== ''): ?>

        <p role="alert" style="color: red;">
            <?= profileEscape($error) ?>
        </p>

    <?php else: ?>

        <h2>Customer Information</h2>

        <p>
            Customer ID:
            <?= profileEscape($customer['userId']) ?>
        </p>

        <p>
            First Name:
            <?= profileEscape($customer['firstName']) ?>
        </p>

        <p>
            Last Name:
            <?= profileEscape($customer['lastName']) ?>
        </p>

        <p>
            Username:
            <?= profileEscape($customer['userName']) ?>
        </p>

        <h2>Rewards Account</h2>

        <?php if (empty($accounts)): ?>

            <p>No rewards accounts found.</p>

        <?php else: ?>

            <?php foreach ($accounts as $account): ?>

                <div>

                    <p>
                        Account ID:
                        <?= profileEscape($account['accountId']) ?>
                    </p>

                    <p>
                        Account Type:
                        <?= profileEscape($account['accountType']) ?>
                    </p>

                    <p>
                        Available Points:
                        <strong>
                            <?= number_format(
                                (int) $account['points']
                            ) ?>
                        </strong>
                    </p>

                </div>

                <hr>

            <?php endforeach; ?>

        <?php endif; ?>

    <?php endif; ?>

    <a href="card-list.php">Return to Gift Card List</a>

</main>

</body>
</html>
