<!-- IT481_Unit_9_Assignment -->

<?php
session_start();

// Security headers
header("Content-Security-Policy: default-src 'self';");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: DENY");

// Regenerate session ID to prevent session fixation
session_regenerate_id(true);

// Secure session cookie settings
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Input validation and sanitization
    $server = htmlspecialchars(trim($_POST['server']), ENT_QUOTES, 'UTF-8');
    $database = htmlspecialchars(trim($_POST['database']), ENT_QUOTES, 'UTF-8');
    $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
    $password = htmlspecialchars(trim($_POST['password']), ENT_QUOTES, 'UTF-8');

    // Database connection using secure parameters
    $connectionInfo = array("Database" => $database, "UID" => $username, "PWD" => $password, "CharacterSet" => "UTF-8");
    $conn = sqlsrv_connect($server, $connectionInfo);

    if ($conn === false) {
        // Log the error instead of displaying it to the user
        error_log(print_r(sqlsrv_errors(), true));
        die("An error occurred. Please try again later.");
    } else {
        // Storing only non-sensitive data in session
        $_SESSION['server'] = $server;
        $_SESSION['database'] = $database;
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;

        header("Location: dashboard.php");
        exit();
    }
}
?>
