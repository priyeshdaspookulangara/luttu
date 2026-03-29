<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'ecommerce_pv_wallet';

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'helpers.php';
?>
