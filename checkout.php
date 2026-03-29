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

$page_title = 'Checkout Success - ShopPV';
require_once 'includes/header.php';
?>

<!-- ════════════════════════ SUCCESS ═════════════════════════════ -->
<div class="page active" id="success" style="padding:100px 0; text-align:center;">
    <div style="max-width:600px;margin:0 auto;background:var(--white);padding:60px;border-radius:var(--r-lg);box-shadow:0 10px 40px rgba(0,0,0,0.05);border:1px solid var(--sand);">
        <?php if ($success): ?>
            <div style="margin-bottom:30px">
                <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16" style="color:var(--sage)">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
            </div>
            <h1 class="pdp-title" style="font-size:3rem">Payment Successful!</h1>
            <p style="font-size:1.1rem;color:#777;margin-bottom:30px">Your order <strong><?php echo h($order_id); ?></strong> has been placed securely.</p>

            <div style="background:var(--cream);padding:30px;border-radius:var(--r-lg);margin-bottom:40px;border:1.5px solid var(--sand)">
                <div style="font-size:.65rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--terra);margin-bottom:10px">Your Rewards</div>
                <h2 style="font-family:var(--display);font-size:2.5rem;color:var(--ink)">Earned <strong><?php echo h($pv_value); ?> PV</strong> Rewards!</h2>
            </div>

            <div style="display:flex;gap:16px;justify-content:center">
                <a href="index.php" class="btn btn-fill">Back to Shop</a>
                <a href="user/dashboard.php" class="btn btn-outline">View Wallet</a>
            </div>
        <?php else: ?>
            <h1 class="pdp-title">Payment Failed.</h1>
            <p>Please try again or contact support.</p>
            <a href="index.php" class="btn btn-terra mt-4">Back to Shop</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
