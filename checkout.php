<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Customer.php';
require_once __DIR__ . '/classes/Product.php';
require_once __DIR__ . '/classes/Wallet.php';
require_once __DIR__ . '/classes/Order.php';
require_once __DIR__ . '/classes/MLM.php';
require_once __DIR__ . '/classes/Cart.php';

$database = new Database($conn);
$prod_manager = new Product($database);
$customer = new Customer($database);
$wallet = new Wallet($database);
$order_manager = new Order($database);
$mlm = new MLM($database);
$cart = new Cart();

if (!$customer->isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$user_id = (int)$customer->getSessionId();
$order_id = 'ORD-' . strtoupper(uniqid());
$checkout_type = $_POST['checkout_type'] ?? 'single';

$items_to_process = [];
if ($checkout_type === 'cart') {
    $cart_items = $cart->getItems();
    foreach ($cart_items as $pid => $qty) {
        $p = $prod_manager->getById($pid);
        if ($p) {
            $items_to_process[] = [
                'id' => $p['id'],
                'price' => $p['price'],
                'pv' => $p['pv_value'] * $qty,
                'qty' => $qty
            ];
        }
    }
} else {
    $id = $_POST['product_id'] ?? null;
    $p = $prod_manager->getById($id);
    if ($p) {
        $items_to_process[] = [
            'id' => $p['id'],
            'price' => $p['price'],
            'pv' => $p['pv_value'],
            'qty' => 1
        ];
    }
}

if (empty($items_to_process)) {
    header('Location: index.php');
    exit;
}

$total_pv = 0;
$success = true;

// Use transaction logic (manually since Database class is simple)
foreach ($items_to_process as $item) {
    if (!$order_manager->create($user_id, (int)$item['id'], $order_id, (float)$item['price'], (float)$item['pv'])) {
        $success = false;
        break;
    }
    $total_pv += $item['pv'];
}

if ($success) {
    // Check idempotency for wallet
    $check_sql = "SELECT id FROM wallet_transactions WHERE reference_id = ? AND user_id = ?";
    $res = $database->query($check_sql, [$order_id, $user_id], "si");

    if (empty($res)) {
        if ($wallet->addTransaction($user_id, $total_pv, 'credit', "Earned PV from order $order_id", $order_id)) {
            // Distribute MLM Commissions
            $mlm->distributeCommission($user_id, $order_id, $total_pv);

            if ($checkout_type === 'cart') {
                $cart->clear();
            }
        } else {
            $success = false;
        }
    }
}

$page_title = 'Checkout Success - ShopPV';
require_once __DIR__ . '/includes/header.php';
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
            <h1 class="pdp-title" style="font-size:3rem">Order Placed!</h1>
            <p style="font-size:1.1rem;color:#777;margin-bottom:30px">Your order <strong><?php echo h($order_id); ?></strong> has been confirmed.</p>

            <div style="background:var(--cream);padding:30px;border-radius:var(--r-lg);margin-bottom:40px;border:1.5px solid var(--sand)">
                <div style="font-size:.65rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--terra);margin-bottom:10px">Rewards Credited</div>
                <h2 style="font-family:var(--display);font-size:2.5rem;color:var(--ink)">You earned <strong><?php echo number_format($total_pv, 2); ?> PV</strong>!</h2>
            </div>

            <div style="display:flex;gap:16px;justify-content:center">
                <a href="index.php" class="btn btn-fill">Continue Shopping</a>
                <a href="user/dashboard.php" class="btn btn-outline">My Dashboard</a>
            </div>
        <?php else: ?>
            <h1 class="pdp-title">Oops!</h1>
            <p>Something went wrong with your transaction.</p>
            <a href="index.php" class="btn btn-terra mt-4">Return to Shop</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
