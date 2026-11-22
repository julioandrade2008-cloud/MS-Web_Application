<?php
require_once __DIR__ . '/auth.php';

startSecureSession();
$role = $_SESSION['role'] ?? null;
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="../index.php">
            Peer Tutoring Hub
        </a>

        <div class="navbar-nav">

            <?php if (!$role): ?>
                <!-- Guest navigation -->
                <a class="nav-link" href="login.php">Login</a>
                <a class="nav-link" href="register.php">Register</a>

            <?php else: ?>
                <!-- Authenticated navigation -->
                <a class="nav-link" href="profile.php">My profile</a>
                <a class="nav-link" href="requests.php">Requests</a>

                <?php if ($role === 'Student'): ?>
                    <a class="nav-link" href="request-form.php">
                        Request tutoring
                    </a>
                <?php endif; ?>

                <?php if ($role === 'Administrator'): ?>
                    <a class="nav-link" href="manage-users.php">
                        Manage users
                    </a>
                <?php endif; ?>

                <form method="post" action="logout.php" class="d-inline">
                    <?= csrfField() ?>
                    <button class="btn btn-outline-light btn-sm">
                        Logout
                    </button>
                </form>
            <?php endif; ?>

        </div>
    </div>
</nav>
