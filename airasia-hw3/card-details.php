<?php

require_once __DIR__ . '/includes/db.php';

// Safely display database values in HTML
function escapeHTML($value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Get the Gift Card ID from the URL
$cardId = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT,
    ['options' => ['min_range' => 1]]
);

// Reject missing or invalid IDs
if ($cardId === false || $cardId === null) {
    http_response_code(400);
    exit("Invalid Gift Card ID.");
}

// Retrieve the selected Gift Card
try {
    $pdo = getDBConnection();

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

} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("Unable to retrieve Gift Card.");
}

// Handle a card that does not exist
if (!$card) {
    http_response_code(404);
    exit("Gift Card not found.");
}

// Match existing HW2 images
$imageName = strtolower(trim($card['cardName']));
$imagePath = "images/" . $imageName . ".png";

$hasImage = file_exists(
    __DIR__ . '/' . $imagePath
);

$pageTitle = 'Available Gift Cards';
$bodyClass = 'list-page';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<h1>Gift Card Details</h1>

<div class="details-container">

    <?php if ($hasImage): ?>
        <img
            class="details-image"
            src="<?= escapeHTML($imagePath) ?>"
            alt="<?= escapeHTML($card['cardName']) ?> gift card">
    <?php endif; ?>

    <h2>
        <?= escapeHTML($card['cardName']) ?>
    </h2>

    <p>
        <strong>Card ID:</strong>
        <?= (int)$card['cardId'] ?>
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

    <a
        class="action-link"
        href="card-update.php?id=<?= (int)$card['cardId'] ?>">
        Update Card
    </a>
    
    <a
    class="action-link delete-link"
    href="card-delete.php?id=<?= (int)$card['cardId'] ?>">
    Delete Card
</a>

    <a
        class="back-link"
        href="card-list.php">
        Back to Gift Card List
    </a>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
