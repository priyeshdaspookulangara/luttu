<?php
require_once 'includes/db_connect.php';
require_once 'classes/Database.php';
require_once 'classes/Product.php';
require_once 'classes/User.php';

$database = new Database($conn);
$prod = new Product($database);
$user = new User($database);

$id = $_GET['id'] ?? null;
$p = $prod->getById($id);

if (!$p) {
    header('Location: index.php');
    exit;
}

$images = $prod->getImages($p['id']);
$attributes = json_decode($p['attributes'], true);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($p['name']); ?> - Detail</title>
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

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-6">
                <div id="productCarousel" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php $first = true; foreach ($images as $img): ?>
                        <div class="carousel-item <?php echo $first ? 'active' : ''; ?>">
                            <img src="<?php echo h($img['image_path']); ?>" class="d-block w-100" alt="...">
                        </div>
                        <?php $first = false; endforeach; ?>
                        <?php if (empty($images)): ?>
                            <div class="carousel-item active">
                                <img src="https://via.placeholder.com/500" class="d-block w-100" alt="...">
                            </div>
                        <?php endif; ?>
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#productCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#productCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
            </div>
            <div class="col-md-6">
                <h1 class="mb-3"><?php echo h($p['name']); ?></h1>
                <h4 class="text-primary mb-3">₹<?php echo h($p['price']); ?></h4>
                <p class="text-success fw-bold">Earn <?php echo h($p['pv_value']); ?> PV on purchase!</p>
                <div class="text-muted mb-4"><?php echo nl2br(h($p['description'])); ?></div>

                <?php if (!empty($attributes)): ?>
                <div class="mb-4">
                    <h5>Specifications:</h5>
                    <table class="table table-sm">
                        <?php foreach ($attributes as $key => $val): ?>
                        <tr>
                            <th width="150"><?php echo h($key); ?>:</th>
                            <td><?php echo h($val); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>

                <form action="checkout.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo h($p['id']); ?>">
                    <input type="hidden" name="order_id" value="<?php echo 'ORD-' . strtoupper(uniqid()); ?>">
                    <button type="submit" class="btn btn-lg btn-primary w-100">Buy Now & Earn PV</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
