<?php
require_once __DIR__ . '/includes/authorization.php';
requireRole('Admin');

require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/validation.php';

// Default form values
$data = [
    'cardName' => '',
    'cardType' => '',
    'cardValue' => '',
    'points' => ''
];

$errors = [];
$databaseError = '';

// Process the form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve submitted values
    foreach ($data as $field => $value) {
        $data[$field] = trim(
            (string)($_POST[$field] ?? '')
        );
    }

    // Validate submitted information
    $errors = validateGiftCard($data);

    // Save only if validation succeeds
    if (empty($errors)) {
        try {
            $pdo = getDBConnection();

            $stmt = $pdo->prepare(
                "INSERT INTO GIFTCARD
                 (cardName, cardType, cardValue, points)
                 VALUES
                 (:cardName, :cardType, :cardValue, :points)"
            );

            $stmt->execute([
                ':cardName' => $data['cardName'],
                ':cardType' => $data['cardType'],
                ':cardValue' => $data['cardValue'],
                ':points' => $data['points']
            ]);

            // Return to Gift Card List
            header('Location: card-list.php');
            exit;

        } catch (Throwable $e) {
            error_log($e->getMessage());

            $databaseError =
                'Unable to save Gift Card. Please try again.';
        }
    }
}

$pageTitle = 'Add Gift Card';
$bodyClass = 'add-page';

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/nav.php';
?>

<h1>Add Gift Card</h1>

<div class="form-container">

    <?php if ($databaseError !== ''): ?>
        <p role="alert">
            <?= escapeHTML($databaseError) ?>
        </p>
    <?php endif; ?>

    <form action="card-add.php"
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
            Add Gift Card
        </button>

    </form>

    <a href="card-list.php" class="back-link">
        Back to Gift Card List
    </a>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
