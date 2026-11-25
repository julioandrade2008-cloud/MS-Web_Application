<nav class="page-navigation"
     aria-label="Main navigation">



<a href="card-list.php">Gift Card List</a>

<?php if (($_SESSION['role'] ?? '') === 'Admin'): ?>

    <a href="card-add.php">Add Gift Card</a>
    <a href="cust-add.php">Add Customer</a>

<?php endif; ?>

<?php if (($_SESSION['role'] ?? '') === 'Customer'): ?>

    <a href="customer-profile.php">My Profile</a>

<?php endif; ?>

<?php if (isset($_SESSION['userId'])): ?>

    <a href="logout.php">Logout</a>

<?php endif; ?>


</nav>
