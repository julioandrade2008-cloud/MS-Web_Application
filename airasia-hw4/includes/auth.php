<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireLogin()
{
    if (!isset($_SESSION['userId'])) {
        header('Location: login.php');
        exit;
    }
}
