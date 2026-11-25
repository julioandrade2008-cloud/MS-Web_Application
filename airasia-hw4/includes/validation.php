<?php

// Allowed Gift Card types
function allowedCardTypes(): array
{
    return [
        'Retail',
        'Beauty',
        'Electronics',
        'Food',
        'Home Improvement'
    ];
}

// Validate submitted Gift Card information
function validateGiftCard(array $data): array
{
    $errors = [];

    $cardName = trim((string)($data['cardName'] ?? ''));
    $cardType = trim((string)($data['cardType'] ?? ''));
    $cardValue = trim((string)($data['cardValue'] ?? ''));
    $points = trim((string)($data['points'] ?? ''));

    // Validate Card Name
    if ($cardName === '') {
        $errors['cardName'] = 'Card Name is required.';
    } elseif (strlen($cardName) > 100) {
        $errors['cardName'] =
            'Card Name cannot exceed 100 characters.';
    }

    // Validate Card Type
    if (!in_array($cardType, allowedCardTypes(), true)) {
        $errors['cardType'] =
            'Please select a valid Card Type.';
    }

    // Validate Card Value
    if (
        $cardValue === '' ||
        !preg_match('/^\d+(\.\d{1,2})?$/D', $cardValue) ||
        (float)$cardValue <= 0
    ) {
        $errors['cardValue'] =
            'Card Value must be positive and have no more than two decimal places.';
    }

    // Validate Points Required
    if (
        filter_var(
            $points,
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        ) === false
    ) {
        $errors['points'] =
            'Points Required must be a positive whole number.';
    }

    return $errors;
}

// Escape values before displaying in HTML
function escapeHTML($value): string
{
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// Validate a Gift Card ID
function validateCardId($id): bool
{
    return filter_var(
        $id,
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    ) !== false;
}
