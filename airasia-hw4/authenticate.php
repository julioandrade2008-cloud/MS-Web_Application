<?php
session_start();

require_once __DIR__ . '/includes/db.php';

// Only accept POST requests.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Retrieve submitted credentials.
$userName = $_POST['userName'] ?? '';
$password = $_POST['password'] ?? '';

// Validate input on the server.
if (!is_string($userName) || !is_string($password)) {
    $_SESSION['login_error'] = 'Invalid username or password.';
    header('Location: login.php');
    exit;
}

$userName = trim($userName);

if ($userName === '' || $password === '' ||
    strlen($userName) > 100) {
    $_SESSION['login_error'] = 'Invalid username or password.';
    header('Location: login.php');
    exit;
}

try {
    $pdo = getDBConnection();

    // Retrieve the user using a prepared statement.
    $stmt = $pdo->prepare(
        'SELECT userId, userName, password,
                firstName, lastName, role
         FROM USER
         WHERE userName = :userName'
    );

    $stmt->execute([
        ':userName' => $userName
    ]);

    $user = $stmt->fetch();

    // Verify the password and assigned role.
    if ($user &&
        password_verify($password, $user['password']) &&
        in_array($user['role'], ['Admin', 'Customer'], true)) {

        // Prevent session fixation.
        session_regenerate_id(true);

        // Store authenticated user information.
        $_SESSION = [
            'userId' => (int) $user['userId'],
            'userName' => $user['userName'],
            'firstName' => $user['firstName'],
            'lastName' => $user['lastName'],
            'role' => $user['role']
        ];

        header('Location: card-list.php');
        exit;
    }

    // Use a generic message for invalid credentials.
    $_SESSION['login_error'] = 'Invalid username or password.';

} catch (Exception $e) {

    error_log($e->getMessage());

    $_SESSION['login_error'] =
        'Unable to log in. Please try again.';
}

header('Location: login.php');
exit;
