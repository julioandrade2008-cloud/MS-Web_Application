<?php
// Database connection for AirAsia HW3

function getDBConnection(): PDO
{
    // Locate the SQLite database
    $databasePath = __DIR__ . '/../database/rewards.db';

    // Ensure the database file exists
    if (!file_exists($databasePath)) {
        throw new RuntimeException(
            'Database file not found.'
        );
    }

    // Create the PDO connection
    $pdo = new PDO('sqlite:' . $databasePath);

    // Configure PDO
    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

    // Enable foreign key enforcement
    $pdo->exec('PRAGMA foreign_keys = ON');

    return $pdo;
}
