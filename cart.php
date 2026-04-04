<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Cart.php';

$database = new Database($conn);
$cart = new Cart();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    if ($action === 'add') {
        $cart->add($product_id, $quantity);
        header('Location: cart.php');
        exit;
    } elseif ($action === 'remove') {
        $cart->remove($product_id);
        header('Location: cart.php');
        exit;
    } elseif ($action === 'update') {
        $cart->update($product_id, $quantity);
        header('Location: cart.php');
        exit;
    }
}

$items = $cart->getItems();
$cart_products = [];
$total_price = 0;
$total_pv = 0;

if (!empty($items)) {
    require_once __DIR__ . '/classes/Product.php';
    $prod_manager = new Product($database);
    foreach ($items as $id => $qty) {
        $p = $prod_manager->getById($id);
        if ($p) {
            $p['quantity'] = $qty;
            $p['subtotal'] = $p['price'] * $qty;
            $p['subtotal_pv'] = $p['pv_value'] * $qty;
            $cart_products[] = $p;
            $total_price += $p['subtotal'];
            $total_pv += $p['subtotal_pv'];
        }
    }
}

$page_title = 'My Bag — ShopPV';
require_once __DIR__ . '/includes/header.php';
?>

<div class="pg-hero">
    <div class="pg-hero-eyebrow">Shopping Bag</div>
    <div class="pg-hero-title">Your <em>Selection</em></div>
</div>

<div class="section">
    <?php if (empty($cart_products)): ?>
        <div style="text-align:center; padding:100px 0;">
            <h2 style="font-family:var(--display); font-size:2.5rem; margin-bottom:20px">Your bag is empty</h2>
            <p style="color:#666; margin-bottom:40px">Looks like you haven't added any crunchy delights yet.</p>
            <a href="index.php" class="btn btn-fill">Start Shopping</a>
        </div>
    <?php else: ?>
        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:60px">
            <div>
                <h2 style="font-family:var(--display); font-size:1.8rem; margin-bottom:30px">Items in Bag</h2>
                <?php foreach ($cart_products as $p): ?>
                    <div style="display:flex; gap:24px; padding-bottom:30px; margin-bottom:30px; border-bottom:1px solid var(--sand); align-items:center">
                        <div style="width:120px; height:120px; background:var(--cream); border-radius:var(--r); overflow:hidden">
                            <img src="<?php echo h($p['image_path'] ?? 'https://via.placeholder.com/150'); ?>" class="img-cover">
                        </div>
                        <div style="flex:1">
                            <div style="font-size:.7rem; color:var(--terra); font-weight:700; text-transform:uppercase; letter-spacing:1px; margin-bottom:4px"><?php echo h($p['category_name']); ?></div>
                            <h3 style="font-family:var(--display); font-size:1.4rem; margin:0"><?php echo h($p['name']); ?></h3>
                            <div style="font-size:.85rem; color:#888; margin-top:4px">₹<?php echo h($p['price']); ?> × <?php echo h($p['quantity']); ?></div>
                            <div style="font-size:.85rem; color:var(--success-color); font-weight:700; margin-top:4px">Earns <?php echo h($p['subtotal_pv']); ?> PV</div>
                        </div>
                        <div style="text-align:right">
                            <div style="font-family:var(--display); font-size:1.5rem; margin-bottom:10px">₹<?php echo number_format($p['subtotal'], 2); ?></div>
                            <form method="POST">
                                <input type="hidden" name="action" value="remove">
                                <input type="hidden" name="product_id" value="<?php echo h($p['id']); ?>">
                                <button type="submit" style="background:none; border:none; color:var(--terra); font-size:.8rem; cursor:pointer; text-decoration:underline">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div>
                <div style="background:var(--white); padding:40px; border-radius:var(--r-lg); border:1.5px solid var(--sand); position:sticky; top:40px">
                    <h2 style="font-family:var(--display); font-size:1.8rem; margin-bottom:24px">Order Summary</h2>
                    <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:.9rem">
                        <span>Subtotal</span>
                        <span>₹<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; margin-bottom:12px; font-size:.9rem">
                        <span>Shipping</span>
                        <span style="color:var(--success-color)">FREE</span>
                    </div>
                    <div style="border-top:1px solid var(--sand); margin:20px 0; padding-top:20px; display:flex; justify-content:space-between; align-items:baseline">
                        <span style="font-weight:700; font-size:1.1rem">Total Amount</span>
                        <span style="font-family:var(--display); font-size:2.5rem">₹<?php echo number_format($total_price, 2); ?></span>
                    </div>
                    <div style="background:var(--cream); padding:20px; border-radius:var(--r); margin-bottom:30px; text-align:center">
                        <div style="font-size:.65rem; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--terra); margin-bottom:4px">Total Rewards</div>
                        <div style="font-family:var(--display); font-size:1.8rem; color:var(--ink)"><?php echo number_format($total_pv, 2); ?> PV</div>
                    </div>

                    <form action="checkout.php" method="POST">
                        <input type="hidden" name="checkout_type" value="cart">
                        <button type="submit" class="btn btn-fill" style="width:100%; justify-content:center; padding:18px">Checkout Now</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
