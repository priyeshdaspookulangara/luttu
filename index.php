<?php
require_once 'includes/db_connect.php';
require_once 'classes/Database.php';
require_once 'classes/Product.php';
require_once 'classes/User.php';

$database = new Database($conn);
$prod = new Product($database);
$user = new User($database);

$products = $prod->getAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">ShopPV</a>
            <div class="navbar-nav ms-auto">
                <?php if ($user->isLoggedIn()): ?>
                    <a class="nav-link" href="user/dashboard.php">My Wallet</a>
                    <a class="nav-link" href="logout.php">Logout</a>
                <?php else: ?>
                    <a class="nav-link" href="login.php">Login</a>
                    <a class="nav-link" href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">Our Products</h2>
        <div class="row">
            <?php foreach ($products as $p):
                $images = $prod->getImages($p['id']);
                $primary_image = 'https://via.placeholder.com/200';
                foreach ($images as $img) {
                    if ($img['is_primary']) {
                        $primary_image = $img['image_path'];
                        break;
                    }
                }
            ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?php echo h($primary_image); ?>" class="card-img-top" alt="<?php echo h($p['name']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo h($p['name']); ?></h5>
                        <p class="card-text text-muted"><?php echo h($p['category_name']); ?></p>
                        <p class="card-text fw-bold">₹<?php echo h($p['price']); ?></p>
                        <p class="card-text small text-primary">Earn <?php echo h($p['pv_value']); ?> PV</p>
                    </div>
                    <div class="card-footer bg-transparent border-top-0">
                        <a href="product_detail.php?id=<?php echo h($p['id']); ?>" class="btn btn-outline-primary w-100">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
