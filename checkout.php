<?php
require_once 'includes/db_connect.php';
require_once 'classes/Database.php';
require_once 'classes/Product.php';
require_once 'classes/User.php';
require_once 'classes/Wallet.php';

$database = new Database($conn);
$prod = new Product($database);
$user = new User($database);
$wallet = new Wallet($database);

if (!$user->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$id = $_POST['product_id'] ?? null;
$p = $prod->getById($id);

if (!$p) {
    header('Location: index.php');
    exit;
}

// Requirement: Prevent PV double-crediting (idempotency)
$order_id = $_POST['order_id'] ?? 'ORD-' . strtoupper(uniqid());
$pv_value = $p['pv_value'];

$success = false;

// Check if this order_id has already been processed for this user
$check_sql = "SELECT id FROM wallet_transactions WHERE reference_id = ? AND user_id = ?";
$stmt = $conn->prepare($check_sql);
$stmt->bind_param("si", $order_id, $_SESSION['user_id']);
$stmt->execute();
$res = $stmt->get_result();
if ($res->num_rows === 0) {
    if ($wallet->addTransaction($_SESSION['user_id'], $pv_value, 'credit', "Earned PV from order $order_id", $order_id)) {
        $success = true;
    }
} else {
    // Already processed
    $success = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Success - PV Wallet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light text-center py-5">
    <div class="container">
        <?php if ($success): ?>
            <div class="card shadow p-5">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                </div>
                <h1 class="mb-3">Payment Successful!</h1>
                <p class="lead mb-4">Your order <strong><?php echo h($order_id); ?></strong> has been placed.</p>
                <div class="alert alert-info">
                    <h4 class="mb-0">You earned <strong><?php echo h($pv_value); ?> PV</strong> rewards!</h4>
                </div>
                <div class="mt-4">
                    <a href="index.php" class="btn btn-primary me-3">Back to Shop</a>
                    <a href="user/dashboard.php" class="btn btn-outline-primary">View Wallet</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">
                <h4>Payment Failed. Please try again.</h4>
                <a href="index.php" class="btn btn-secondary mt-3">Back to Shop</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
