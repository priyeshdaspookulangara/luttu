<?php
// Report all PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'ecommerce_pv_wallet';

// Check if mysqli is loaded
if (!extension_loaded('mysqli')) {
    die('The mysqli extension is not loaded.');
}

try {
    // Create connection (using @ to suppress warnings as we handle the exception)
    $conn = @new mysqli($host, $user, $pass, $db);

    // Check connection
    if ($conn->connect_error) {
        // Log or show a user-friendly error instead of 500
        echo "Database connection failed. Please ensure MySQL is running and the database 'ecommerce_pv_wallet' exists.";
        exit;
    }

    // Set charset
    $conn->set_charset("utf8mb4");

    // Start session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    require_once 'helpers.php';
} catch (Exception $e) {
    echo "An unexpected error occurred: " . $e->getMessage();
    exit;
}
?>
