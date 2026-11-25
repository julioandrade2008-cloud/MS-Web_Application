<?php

require_once __DIR__ . '/includes/authorization.php';
requireRole('Customer');

require_once __DIR__ . '/includes/db.php';

header('Cache-Control: no-store');

// Only allow POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    http_response_code(405);
    header('Allow: POST');
    exit('Invalid request method.');
}

$userId = (int) $_SESSION['userId'];

$token = $_POST['csrf_token'] ?? '';

// Validate the CSRF token.
if (!is_string($token) ||
    empty($_SESSION['redemption_token']) ||
    !hash_equals($_SESSION['redemption_token'], $token)) {

    http_response_code(403);
    exit('Invalid form submission.');
}

// Prevent reuse of the same confirmation token.
unset($_SESSION['redemption_token']);

$cardId = filter_input(
    INPUT_POST,
    'cardId',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

$accountId = filter_input(
    INPUT_POST,
    'accountId',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$cardId || !$accountId) {

    http_response_code(400);
    exit('Invalid Gift Card or Account ID.');
}

$success = false;
$error = '';
$pdo = null;

function escapeHTML($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

try {

    $pdo = getDBConnection();

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    // Begin the redemption transaction.
    $pdo->beginTransaction();

    // Retrieve gift card information from the database.
    $stmt = $pdo->prepare(
        'SELECT cardId, cardName, cardValue, points
         FROM GIFTCARD
         WHERE cardId = :cardId'
    );

    $stmt->execute([
        ':cardId' => $cardId
    ]);

    $card = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$card) {
        throw new DomainException(
            'Gift Card not found.'
        );
    }

    $requiredPoints = filter_var(
        $card['points'],
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    );

    if ($requiredPoints === false) {
        throw new DomainException(
            'Invalid Gift Card point value.'
        );
    }

    // Verify account ownership.
    $stmt = $pdo->prepare(
        'SELECT accountId, points
         FROM ACCOUNT
         WHERE accountId = :accountId
         AND userId = :userId'
    );

    $stmt->execute([
        ':accountId' => $accountId,
        ':userId' => $userId
    ]);

    $account = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$account) {
        throw new DomainException(
            'Invalid rewards account.'
        );
    }

    // Check available points.
    if ((int)$account['points'] < $requiredPoints) {

        throw new DomainException(
            'Insufficient points to redeem this Gift Card.'
        );
    }

    // Deduct points only from the customer's own
    // account and only if sufficient points remain.
    $stmt = $pdo->prepare(
        'UPDATE ACCOUNT
         SET points = points - :requiredPoints
         WHERE accountId = :accountId
         AND userId = :userId
         AND points >= :minimumPoints'
    );

    $stmt->execute([
        ':requiredPoints' => $requiredPoints,
        ':accountId' => $accountId,
        ':userId' => $userId,
        ':minimumPoints' => $requiredPoints
    ]);

    if ($stmt->rowCount() !== 1) {

        throw new DomainException(
            'Unable to deduct points. Please try again.'
        );
    }

    // Create the redemption record.
    $stmt = $pdo->prepare(
        'INSERT INTO REDEMPTION
         (date, accountId, cardId, pointsRedeemed)
         VALUES
         (:date, :accountId, :cardId, :pointsRedeemed)'
    );

    $stmt->execute([
        ':date' => gmdate('Y-m-d H:i:s'),
        ':accountId' => $accountId,
        ':cardId' => $cardId,
        ':pointsRedeemed' => $requiredPoints
    ]);

    $redeemId = (int) $pdo->lastInsertId();

    // Retrieve the updated balance.
    $stmt = $pdo->prepare(
        'SELECT points
         FROM ACCOUNT
         WHERE accountId = :accountId
         AND userId = :userId'
    );

    $stmt->execute([
        ':accountId' => $accountId,
        ':userId' => $userId
    ]);

    $remainingPoints = (int) $stmt->fetchColumn();

    // Commit both database changes.
    $pdo->commit();

    $success = true;

} catch (DomainException $e) {

    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    $error = $e->getMessage();

} catch (Throwable $e) {

    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log($e->getMessage());

    $error = 'Unable to complete redemption. Please try again.';
}

$pageTitle = 'Redemption Result';
$bodyClass = '';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';

?>

<h1>Redemption Result</h1>

<div class="details-container">

    <?php if ($success): ?>

        <h2 style="color: green;">
            Gift Card redeemed successfully.
        </h2>

        <p>
            <strong>Redemption ID:</strong>
            <?= $redeemId ?>
        </p>

        <p>
            <strong>Gift Card:</strong>
            <?= escapeHTML($card['cardName']) ?>
        </p>

        <p>
            <strong>Gift Card Value:</strong>
            $<?= number_format((float)$card['cardValue'], 2) ?>
        </p>

        <p>
            <strong>Points Redeemed:</strong>
            <?= number_format($requiredPoints) ?>
        </p>

        <p>
            <strong>Remaining Points:</strong>
            <?= number_format($remainingPoints) ?>
        </p>

    <?php else: ?>

        <h2 style="color: red;">
            Redemption unsuccessful.
        </h2>

        <p role="alert">
            <?= escapeHTML($error) ?>
        </p>

    <?php endif; ?>

    <a href="customer-profile.php">
        View My Profile
    </a>

    <br><br>

    <a href="card-list.php">
        Return to Gift Card List
    </a>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
