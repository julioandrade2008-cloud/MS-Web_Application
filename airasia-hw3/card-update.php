<?php

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

// Retrieve and validate the Gift Card ID
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

$errors = [];
$databaseError = '';

try {
    $pdo = getDBConnection();

    // Retrieve the existing Gift Card
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

    // Prepopulate the form
    $data = [
        'cardName' => $card['cardName'],
        'cardType' => $card['cardType'],
        'cardValue' => $card['cardValue'],
        'points' => $card['points']
    ];

    // Process the submitted update
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        foreach ($data as $field => $value) {
            $data[$field] = trim(
                (string)($_POST[$field] ?? '')
            );
        }

        // Reuse the same validation as card-add.php
        $errors = validateGiftCard($data);

        if (empty($errors)) {

            $updateStmt = $pdo->prepare(
                "UPDATE GIFTCARD
                 SET cardName = :cardName,
                     cardType = :cardType,
                     cardValue = :cardValue,
                     points = :points
                 WHERE cardId = :cardId"
            );

            $updateStmt->execute([
                ':cardName' => $data['cardName'],
                ':cardType' => $data['cardType'],
                ':cardValue' => $data['cardValue'],
                ':points' => $data['points'],
                ':cardId' => $cardId
            ]);

            // Redirect to updated Gift Card details
            header(
                'Location: card-details.php?id=' . $cardId
            );
            exit;
        }
    }

} catch (Throwable $e) {
    error_log($e->getMessage());
    http_response_code(500);
    $databaseError =
        'Unable to load or update Gift Card.';
}

$pageTitle = 'Available Gift Cards';
$bodyClass = 'list-page';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<h1>Update Gift Card</h1>

<div class="form-container">

    <?php if ($databaseError !== ''): ?>
        <p class="error-message" role="alert">
            <?= escapeHTML($databaseError) ?>
        </p>
    <?php else: ?>

    <form
        action="card-update.php?id=<?= (int)$cardId ?>"
        method="post"
        novalidate>

        <div class="form-group">
            <label for="cardName">Card Name</label>

            <input
                type="text"
                id="cardName"
                name="cardName"
                maxlength="100"
                value="<?= escapeHTML($data['cardName']) ?>"
                required>

            <?php if (isset($errors['cardName'])): ?>
                <p class="error-message" role="alert">
                    <?= escapeHTML($errors['cardName']) ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="cardType">Card Type</label>

            <select
                id="cardType"
                name="cardType"
                required>

                <option value="">Select Card Type</option>

                <?php foreach (allowedCardTypes() as $type): ?>
                    <option
                        value="<?= escapeHTML($type) ?>"
                        <?= $data['cardType'] === $type
                            ? 'selected' : '' ?>>
                        <?= escapeHTML($type) ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <?php if (isset($errors['cardType'])): ?>
                <p class="error-message" role="alert">
                    <?= escapeHTML($errors['cardType']) ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="cardValue">Card Value ($)</label>

            <input
                type="number"
                id="cardValue"
                name="cardValue"
                min="0.01"
                step="0.01"
                value="<?= escapeHTML($data['cardValue']) ?>"
                required>

            <?php if (isset($errors['cardValue'])): ?>
                <p class="error-message" role="alert">
                    <?= escapeHTML($errors['cardValue']) ?>
                </p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="points">Points Required</label>

            <input
                type="number"
                id="points"
                name="points"
                min="1"
                step="1"
                value="<?= escapeHTML($data['points']) ?>"
                required>

            <?php if (isset($errors['points'])): ?>
                <p class="error-message" role="alert">
                    <?= escapeHTML($errors['points']) ?>
                </p>
            <?php endif; ?>
        </div>

        <button class="form-button" type="submit">
            Update Gift Card
        </button>

    </form>

    <?php endif; ?>

    <a
        class="back-link"
        href="card-details.php?id=<?= (int)$cardId ?>">
        Back to Gift Card Details
    </a>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
