<?php

require_once __DIR__ . '/../includes/validation.php';

// Count test results
$passed = 0;
$failed = 0;

// Run a validation test and display PASS or FAIL
function runTest(
    string $testName,
    array $input,
    bool $shouldPass,
    ?string $expectedError = null
): void {
    global $passed, $failed;

    $errors = validateGiftCard($input);

    if ($shouldPass) {
        $success = empty($errors);
    } else {
        $success = $expectedError !== null
            && isset($errors[$expectedError]);
    }

    if ($success) {
        echo "PASS: $testName\n";
        $passed++;
    } else {
        echo "FAIL: $testName\n";
        echo "  Actual errors: "
            . json_encode($errors) . "\n";
        $failed++;
    }
}

// Baseline: Valid Gift Card
$validCard = [
    'cardName' => 'Amazon',
    'cardType' => 'Retail',
    'cardValue' => '50.00',
    'points' => '5000'
];

// Test 1: Valid Gift Card
runTest(
    'Valid Gift Card',
    $validCard,
    true
);

// Test 2: Blank Card Name
runTest(
    'Blank Card Name',
    array_merge($validCard, [
        'cardName' => ''
    ]),
    false,
    'cardName'
);

// Test 3: Invalid Card Type
runTest(
    'Invalid Card Type',
    array_merge($validCard, [
        'cardType' => 'InvalidType'
    ]),
    false,
    'cardType'
);

// Test 4: Zero Card Value
runTest(
    'Zero Card Value',
    array_merge($validCard, [
        'cardValue' => '0'
    ]),
    false,
    'cardValue'
);

// Test 5: Negative Card Value
runTest(
    'Negative Card Value',
    array_merge($validCard, [
        'cardValue' => '-25'
    ]),
    false,
    'cardValue'
);

// Test 6: Invalid Points
runTest(
    'Invalid Points - Decimal',
    array_merge($validCard, [
        'points' => '2.5'
    ]),
    false,
    'points'
);

// Test 7: Negative Points
runTest(
    'Negative Points',
    array_merge($validCard, [
        'points' => '-100'
    ]),
    false,
    'points'
);

// Test 8: Blank Points
runTest(
    'Blank Points',
    array_merge($validCard, [
        'points' => ''
    ]),
    false,
    'points'
);

// Test 9: Card Name Too Long
runTest(
    'Card Name Over 100 Characters',
    array_merge($validCard, [
        'cardName' => str_repeat('A', 101)
    ]),
    false,
    'cardName'
);

// Test 10: Invalid Gift Card ID
$idValid = validateCardId('10');
$idInvalid = validateCardId('abc');
$idNegative = validateCardId('-1');

if ($idValid && !$idInvalid && !$idNegative) {
    echo "PASS: Gift Card ID Validation\n";
    $passed++;
} else {
    echo "FAIL: Gift Card ID Validation\n";
    $failed++;
}

// Summary
echo "\n-------------------------\n";
echo "Validation Test Summary\n";
echo "-------------------------\n";
echo "Passed: $passed\n";
echo "Failed: $failed\n";
echo "Total: " . ($passed + $failed) . "\n";

// Return failure status if any test fails
exit($failed > 0 ? 1 : 0);
