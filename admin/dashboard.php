<?php
require_once __DIR__ . '/includes/header.php';
?>

<div class="page__heading d-flex align-items-center">
    <div class="flex">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Admin Dashboard</li>
            </ol>
        </nav>
        <h1 class="m-0">Admin Overview</h1>
    </div>
</div>

<div class="row card-group-row">
    <div class="col-lg-3 col-md-6 card-group-row__col">
        <div class="card card-group-row__card card-body flex-row align-items-center">
            <div class="card-icon-custom"><i class="fas fa-coins"></i></div>
            <div class="card-content-custom">
                <h3>Total PV Issued</h3>
                <p class="mb-0">0</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 card-group-row__col">
        <div class="card card-group-row__card card-body flex-row align-items-center">
            <div class="card-icon-custom"><i class="fas fa-hourglass-half" style="color:var(--warning-color)"></i></div>
            <div class="card-content-custom">
                <h3>Pending Withdrawals</h3>
                <p class="mb-0">0</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 card-group-row__col">
        <div class="card card-group-row__card card-body flex-row align-items-center">
            <div class="card-icon-custom"><i class="fas fa-users"></i></div>
            <div class="card-content-custom">
                <h3>Active Users</h3>
                <p class="mb-0">0</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 card-group-row__col">
        <div class="card card-group-row__card card-body flex-row align-items-center">
            <div class="card-icon-custom"><i class="fas fa-shopping-cart"></i></div>
            <div class="card-content-custom">
                <h3>Total Orders</h3>
                <p class="mb-0">0</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header card-header-large bg-white">
        <h4 class="card-header__title">Welcome to ShopPV Admin</h4>
    </div>
    <div class="card-body">
        <p class="text-muted">Use the sidebar navigation to manage your store's categories, products, and user rewards settings. All transactions are recorded in the ledger for audit purposes.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
