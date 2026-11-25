<?php

require_once __DIR__ . '/auth.php';

function hasRole($role)
{
    return isset($_SESSION['role']) &&
           $_SESSION['role'] === $role;
}

function requireRole($role)
{
    requireLogin();

    if (!hasRole($role)) {
        http_response_code(403);

        echo '<h1>Access Denied</h1>';
        echo '<p>You do not have permission to access this page.</p>';
        echo '<a href="card-list.php">Return to Gift Card List</a>';

        exit;
    }
}
