<?php

require_once __DIR__ . '/includes/db.php';

// Function to safely display database values
function escapeHTML($value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Retrieve Gift Cards from SQLite
try {
    $pdo = getDBConnection();

    $stmt = $pdo->query(
        "SELECT cardId, cardName, cardType,
                cardValue, points
         FROM GIFTCARD
         ORDER BY cardId"
    );

    $cards = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log($e->getMessage());
    http_response_code(500);
    exit("Unable to load Gift Cards.");
}

$pageTitle = 'Available Gift Cards';
$bodyClass = 'list-page';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<h1>Available Gift Cards</h1>

<div class="gift-card-container">

<?php if (empty($cards)): ?>

    <p>No Gift Cards are currently available.</p>

<?php else: ?>

    <?php foreach ($cards as $card): ?>

        <?php
        // Match existing HW2 images by card name
        $imageName = strtolower(
            trim($card['cardName'])
        );

        $imagePath = "images/" . $imageName . ".png";

        // Only display an image if it exists
        $hasImage = file_exists(
            __DIR__ . '/' . $imagePath
        );
        ?>

        <article>

            <?php if ($hasImage): ?>
                <img
                    src="<?= escapeHTML($imagePath) ?>"
                    alt="<?= escapeHTML($card['cardName']) ?> gift card">
            <?php endif; ?>

            <a href="card-details.php?id=<?= (int)$card['cardId'] ?>">
                <?= escapeHTML($card['cardName']) ?>
            </a>

            <p>
                <?= number_format((int)$card['points']) ?>
                points
            </p>

        </article>

    <?php endforeach; ?>

<?php endif; ?>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>

