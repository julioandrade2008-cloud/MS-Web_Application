<?php
require_once __DIR__ . '/includes/authorization.php';
requireRole('Admin');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

// Read and validate the Gift Card ID
$cardId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

if ($cardId === false || $cardId === null) {
    http_response_code(400);
    exit('Invalid Gift Card ID.');
}

try {
    $pdo = getDBConnection();

    // Retrieve the Gift Card before deletion
    $stmt = $pdo->prepare(
        "SELECT cardId, cardName, cardType,
                cardValue, points
         FROM GIFTCARD
         WHERE cardId = :cardId"
    );

    $stmt->execute([
        ':cardId' => $cardId
    ]);

    $card = $stmt->fetch();

    if (!$card) {
        http_response_code(404);
        exit('Gift Card not found.');
    }

    // Delete only after a POST confirmation
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $deleteStmt = $pdo->prepare(
            "DELETE FROM GIFTCARD
             WHERE cardId = :cardId"
        );

        $deleteStmt->execute([
            ':cardId' => $cardId
        ]);

        // Redirect after successful deletion
        header('Location: card-list.php');
        exit;
    }

} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit('Unable to process Gift Card deletion.');
}

$pageTitle = 'Delete Gift Card';
$bodyClass = '';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<h1>Delete Gift Card</h1>

<div class="details-container">

    <h2>Confirm Deletion</h2>

    <p>
        Are you sure you want to delete this Gift Card?
    </p>

    <p>
        <strong>Card Name:</strong>
        <?= escapeHTML($card['cardName']) ?>
    </p>

    <p>
        <strong>Card Type:</strong>
        <?= escapeHTML($card['cardType']) ?>
    </p>

    <p>
        <strong>Card Value:</strong>
        $<?= number_format((float)$card['cardValue'], 2) ?>
    </p>

    <p>
        <strong>Points Required:</strong>
        <?= number_format((int)$card['points']) ?>
    </p>

    <p class="error-message">
        This action cannot be undone.
    </p>

    <form
        action="card-delete.php?id=<?= (int)$cardId ?>"
        method="post">

        <button
            class="form-button"
            type="submit">
            Yes, Delete Gift Card
        </button>

    </form>

    <a
        class="back-link"
        href="card-details.php?id=<?= (int)$cardId ?>">
        Cancel and Return to Gift Card Details
    </a>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
