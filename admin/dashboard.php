<?php
require_once '../includes/db_connect.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

$database = new Database($conn);
$user = new User($database);

if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - PV Wallet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="navbar-nav">
                <a class="nav-link" href="categories.php">Categories</a>
                <a class="nav-link" href="products.php">Products</a>
                <a class="nav-link" href="pv_settings.php">PV Settings</a>
                <a class="nav-link" href="withdrawals.php">Withdrawals</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Dashboard</h2>
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white p-3">
                    <h5>Total Orders</h5>
                    <h3>0</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white p-3">
                    <h5>Total PV Issued</h5>
                    <h3>0</h3>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark p-3">
                    <h5>Pending Withdrawals</h5>
                    <h3>0</h3>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
