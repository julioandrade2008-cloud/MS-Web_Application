<?php
session_start();

if (isset($_SESSION['userId'])) {
    header('Location: card-list.php');
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>AirAsia Rewards - Login</title>

    <link rel="stylesheet"
          href="css/styles.css">
</head>

<body>

<main class="container">

    <h1>AirAsia Rewards</h1>
    <h2>Login</h2>

    <?php if ($error !== ''): ?>
        <p role="alert" style="color: red;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form action="authenticate.php" method="POST">

        <div>
            <label for="userName">Username</label>
            <input type="text"
                   id="userName"
                   name="userName"
                   autocomplete="username"
                   maxlength="100"
                   required>
        </div>

        <br>

        <div>
            <label for="password">Password</label>
            <input type="password"
                   id="password"
                   name="password"
                   autocomplete="current-password"
                   required>
        </div>

        <br>

        <button type="submit">Login</button>

    </form>

</main>

</body>
</html>
