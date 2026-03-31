<?php
require_once __DIR__ . '/../../includes/db_connect.php';
require_once __DIR__ . '/../../classes/Database.php';
require_once __DIR__ . '/../../classes/User.php';
require_once __DIR__ . '/../../classes/Category.php';

$database = new Database($conn);
$user = new User($database);
$cat = new Category($database);

if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$base_url = $protocol . $_SERVER['HTTP_HOST'] . rtrim(str_replace(['/admin', '/user'], '', dirname($_SERVER['SCRIPT_NAME'])), '/') . '/';
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo isset($page_title) ? h($page_title) : 'ShopPV Admin'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/admin.css">
    <style>
        .mdk-drawer-layout { display: flex; min-height: 100vh; }
        .mdk-drawer { width: 260px; flex-shrink: 0; }
        .mdk-drawer-layout__content { flex-grow: 1; padding: 20px; }
        .navbar { background: #212529; color: #fff; padding: 10px 20px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 1000; }
        .navbar-brand { color: #fff; text-decoration: none; font-weight: 700; font-size: 1.25rem; display: flex; align-items: center; gap: 10px; }
    </style>
</head>
<body class="layout-default">

    <div class="navbar">
        <a href="<?php echo $base_url; ?>admin/dashboard.php" class="navbar-brand">
            <i class="fas fa-user-shield"></i>
            <span>ShopPV Admin</span>
        </a>
        <div style="display:flex; align-items:center; gap:20px">
            <span style="font-size:.85rem; color:rgba(255,255,255,.6)">Logged in as <strong><?php echo h($_SESSION['username']); ?></strong></span>
            <a href="<?php echo $base_url; ?>logout.php" style="color:#fff; text-decoration:none; font-size:.85rem"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div class="mdk-drawer-layout">
        <!-- Sidebar -->
        <div class="mdk-drawer sidebar-light" id="default-drawer">
            <div class="sidebar-heading" style="padding: 24px; font-size: .65rem; text-transform: uppercase; color: #aaa; letter-spacing: 1.5px;">Menu</div>
            <ul class="sidebar-menu" style="list-style:none; padding:0">
                <li class="sidebar-menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'dashboard.php') !== false ? 'active' : ''; ?>">
                    <a class="sidebar-menu-button" href="dashboard.php">
                        <i class="sidebar-menu-icon fas fa-chart-pie"></i>
                        <span class="sidebar-menu-text">Dashboard</span>
                    </a>
                </li>
                <li class="sidebar-menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'categories.php') !== false ? 'active' : ''; ?>">
                    <a class="sidebar-menu-button" href="categories.php">
                        <i class="sidebar-menu-icon fas fa-folder"></i>
                        <span class="sidebar-menu-text">Categories</span>
                    </a>
                </li>
                <li class="sidebar-menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'products.php') !== false ? 'active' : ''; ?>">
                    <a class="sidebar-menu-button" href="products.php">
                        <i class="sidebar-menu-icon fas fa-box"></i>
                        <span class="sidebar-menu-text">Products</span>
                    </a>
                </li>
                <li class="sidebar-menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'pv_settings.php') !== false ? 'active' : ''; ?>">
                    <a class="sidebar-menu-button" href="pv_settings.php">
                        <i class="sidebar-menu-icon fas fa-cog"></i>
                        <span class="sidebar-menu-text">PV Settings</span>
                    </a>
                </li>
                <li class="sidebar-menu-item <?php echo strpos($_SERVER['PHP_SELF'], 'withdrawals.php') !== false ? 'active' : ''; ?>">
                    <a class="sidebar-menu-button" href="withdrawals.php">
                        <i class="sidebar-menu-icon fas fa-money-bill-wave"></i>
                        <span class="sidebar-menu-text">Withdrawals</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a class="sidebar-menu-button" href="<?php echo $base_url; ?>index.php">
                        <i class="sidebar-menu-icon fas fa-external-link-alt"></i>
                        <span class="sidebar-menu-text">View Frontend</span>
                    </a>
                </li>
            </ul>
        </div>

        <div class="mdk-drawer-layout__content page">
            <div class="container-fluid page__container">
