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

$page_title = 'Admin Dashboard — ShopPV';
require_once '../includes/header.php';
?>

<div class="pg-hero">
    <div class="pg-hero-eyebrow">Admin Panel</div>
    <div class="pg-hero-title">Admin <em>Dashboard</em></div>
</div>

<div class="section">
    <div class="prod-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:40px">
        <div style="background:var(--ink);color:var(--white);padding:30px;border-radius:var(--r-lg)">
            <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--terra);margin-bottom:10px">Quick Nav</div>
            <a href="categories.php" class="btn btn-ghost btn-sm" style="width:100%;margin-bottom:8px">Categories</a>
            <a href="products.php" class="btn btn-ghost btn-sm" style="width:100%;margin-bottom:8px">Products</a>
            <a href="pv_settings.php" class="btn btn-ghost btn-sm" style="width:100%;margin-bottom:8px">PV Settings</a>
            <a href="withdrawals.php" class="btn btn-ghost btn-sm" style="width:100%">Withdrawals</a>
        </div>
        <div style="background:var(--white);padding:30px;border-radius:var(--r-lg);border:1px solid var(--sand)">
            <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#888;margin-bottom:10px">Total PV Issued</div>
            <h1 style="font-family:var(--display);font-size:3rem;color:var(--ink)">0 PV</h1>
        </div>
        <div style="background:var(--white);padding:30px;border-radius:var(--r-lg);border:1px solid var(--sand)">
            <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#888;margin-bottom:10px">Pending Withdrawals</div>
            <h1 style="font-family:var(--display);font-size:3rem;color:var(--terra)">0</h1>
        </div>
        <div style="background:var(--white);padding:30px;border-radius:var(--r-lg);border:1px solid var(--sand)">
            <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#888;margin-bottom:10px">Active Users</div>
            <h1 style="font-family:var(--display);font-size:3rem;color:var(--ink)">0</h1>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
