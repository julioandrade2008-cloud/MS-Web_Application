<?php
// Shared HTML header for AirAsia HW3

$pageTitle = $pageTitle ?? 'AirAsia Rewards';
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">

    <title>
        <?= htmlspecialchars(
            $pageTitle,
            ENT_QUOTES,
            'UTF-8'
        ) ?>
    </title>

    <link rel="stylesheet" href="css/styles.css">
</head>

<body class="<?= htmlspecialchars(
    $bodyClass,
    ENT_QUOTES,
    'UTF-8'
) ?>">
