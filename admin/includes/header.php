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
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo isset($page_title) ? h($page_title) : 'ShopPV Admin'; ?></title>

    <meta name="robots" content="noindex">

    <!-- App CSS (Hospital Template) -->
    <link type="text/css" href="css/simplebar.min.css" rel="stylesheet">
    <link type="text/css" href="css/app.css" rel="stylesheet">
    <link type="text/css" href="css/vendor-material-icons.css" rel="stylesheet">
    <link type="text/css" href="css/vendor-fontawesome-free.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link type="text/css" href="css/vendor-flatpickr.css" rel="stylesheet">
    <link type="text/css" href="css/vendor-flatpickr-airbnb.css" rel="stylesheet">

    <!-- ShopPV Admin Custom Styles -->
    <link rel="stylesheet" href="<?php echo $base_url; ?>assets/css/admin.css">

    <style>
        /* Alignment with hospital layout */
        .mdk-drawer-layout__content { padding-bottom: 60px; }
        .navbar-main { border-bottom: 1px solid rgba(255,255,255,.1); }
    </style>
</head>
<body class="layout-default">

    <div class="preloader"></div>

    <div class="mdk-header-layout js-mdk-header-layout">
        <!-- Header -->
        <div id="header" class="mdk-header js-mdk-header m-0" data-fixed="">
            <div class="mdk-header__content">
                <div class="navbar navbar-expand-sm navbar-main navbar-dark bg-dark pr-0" id="navbar" data-primary="">
                    <div class="container-fluid p-0">
                        <button class="navbar-toggler navbar-toggler-right d-block d-md-none" type="button" data-toggle="sidebar">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <a href="dashboard.php" class="navbar-brand">
                            <i class="fas fa-user-shield mr-2"></i>
                            <span>ShopPV Admin</span>
                        </a>

                        <ul class="nav navbar-nav ml-auto d-none d-md-flex">
                            <li class="nav-item">
                                <a href="<?php echo $base_url; ?>index.php" class="nav-link" title="View Storefront">
                                    <i class="material-icons">store</i>
                                </a>
                            </li>
                        </ul>

                        <ul class="nav navbar-nav d-none d-sm-flex border-left navbar-height align-items-center">
                            <li class="nav-item dropdown">
                                <a href="#account_menu" class="nav-link dropdown-toggle" data-toggle="dropdown" data-caret="false">
                                    <span class="ml-1 d-flex-inline">
                                        <span class="text-light"><?php echo h($_SESSION['username']); ?></span>
                                    </span>
                                </a>
                                <div id="account_menu" class="dropdown-menu dropdown-menu-right">
                                    <div class="dropdown-item-text dropdown-item-text--lh">
                                        <div><strong>Admin User</strong></div>
                                    </div>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item active" href="dashboard.php">Dashboard</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="<?php echo $base_url; ?>logout.php">Logout</a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="mdk-header-layout__content">
            <div class="mdk-drawer-layout js-mdk-drawer-layout" data-push="" data-responsive-width="992px">
                <div class="mdk-drawer-layout__content page">
                    <div class="container-fluid page__container">
