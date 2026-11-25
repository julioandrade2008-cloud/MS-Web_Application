<?php

require_once __DIR__ . '/includes/authorization.php';
requireRole('Customer');

require_once __DIR__ . '/includes/db.php';

header('Cache-Control: no-store');

$userId = (int) $_SESSION['userId'];

$cardId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if (!$cardId) {
    http_response_code(400);
    exit('Invalid Gift Card ID.');
}

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

    // Retrieve the selected gift card.
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
        http_response_code(404);
        exit('Gift Card not found.');
    }

    // Retrieve the logged-in customer's accounts.
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

} catch (Throwable $e) {

    error_log($e->getMessage());

    http_response_code(500);
    exit('Unable to retrieve redemption information.');
}

// Generate a CSRF token.
if (empty($_SESSION['redemption_token'])) {

    $_SESSION['redemption_token'] =
        bin2hex(random_bytes(32));
}

$pageTitle = 'Redeem Gift Card';
$bodyClass = '';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';

?>

<h1>Redeem Gift Card</h1>

<div class="details-container">

    <h2><?= escapeHTML($card['cardName']) ?></h2>

    <p>
        <strong>Gift Card Value:</strong>
        $<?= number_format((float)$card['cardValue'], 2) ?>
    </p>

    <p>
        <strong>Points Required:</strong>
        <?= number_format((int)$card['points']) ?>
    </p>

    <?php if (empty($accounts)): ?>

        <p>No rewards account found.</p>

    <?php else: ?>

        <form action="process-redemption.php"
              method="POST">

            <input type="hidden"
                   name="cardId"
                   value="<?= (int)$card['cardId'] ?>">

            <input type="hidden"
                   name="csrf_token"
                   value="<?= escapeHTML(
                       $_SESSION['redemption_token']
                   ) ?>">

            <label for="accountId">
                <strong>Select Rewards Account:</strong>
            </label>

            <select name="accountId"
                    id="accountId"
                    required>

                <?php foreach ($accounts as $account): ?>

                    <option value="<?= (int)$account['accountId'] ?>">

                        <?= escapeHTML($account['accountType']) ?>

                        -

                        <?= number_format((int)$account['points']) ?>

                        points available

                    </option>

                <?php endforeach; ?>

            </select>

            <br><br>

            <button type="submit">
                Confirm Redemption
            </button>

        </form>

    <?php endif; ?>

    <br>

    <a class="back-link"
       href="card-list.php">
        Cancel and Return to Gift Card List
    </a>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
