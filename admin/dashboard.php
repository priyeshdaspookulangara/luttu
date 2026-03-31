<?php
require_once __DIR__ . '/includes/header.php';
?>

<div class="page__heading d-flex align-items-center">
    <div class="flex">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="#">Admin</a></li>
                <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
            </ol>
        </nav>
        <h1 class="m-0">Admin Dashboard</h1>
    </div>
</div>

<div class="row card-group-row" style="display:flex; flex-wrap:wrap; gap:20px; margin-bottom:30px">
    <div class="col-lg-3 col-md-6" style="flex:1; min-width:250px">
        <div class="card card-body flex-row align-items-center" style="display:flex; padding:24px">
            <div class="card-icon-custom"><i class="fas fa-coins"></i></div>
            <div class="card-content-custom">
                <h3>Total PV Issued</h3>
                <p>0 PV</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="flex:1; min-width:250px">
        <div class="card card-body flex-row align-items-center" style="display:flex; padding:24px">
            <div class="card-icon-custom"><i class="fas fa-hourglass-half" style="color:var(--warning-color)"></i></div>
            <div class="card-content-custom">
                <h3>Pending Withdrawals</h3>
                <p>0</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="flex:1; min-width:250px">
        <div class="card card-body flex-row align-items-center" style="display:flex; padding:24px">
            <div class="card-icon-custom"><i class="fas fa-users"></i></div>
            <div class="card-content-custom">
                <h3>Active Users</h3>
                <p>0</p>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6" style="flex:1; min-width:250px">
        <div class="card card-body flex-row align-items-center" style="display:flex; padding:24px">
            <div class="card-icon-custom"><i class="fas fa-shopping-bag"></i></div>
            <div class="card-content-custom">
                <h3>Total Orders</h3>
                <p>0</p>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h4 class="card-header__title">Quick Overview</h4>
    </div>
    <div class="card-body">
        <p class="text-muted">Welcome to the ShopPV Admin Panel. Use the sidebar to manage products, categories, and wallet settings.</p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
