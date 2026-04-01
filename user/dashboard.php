<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Wallet.php';
require_once __DIR__ . '/../classes/Order.php';

$database = new Database($conn);
$user = new User($database);
$wallet = new Wallet($database);
$order_system = new Order($database);

if (!$user->isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount_pv = $_POST['amount_pv'] ?? 0;
    if ($wallet->createWithdrawalRequest($user_id, (float)$amount_pv)) {
        $message = "Withdrawal request submitted successfully!";
    } else {
        $error = "Failed to submit request. Check your balance and threshold.";
    }
}

$balance_pv = $wallet->getBalance($user_id);
$settings = $wallet->getCurrentPVSettings();
$cash_per_pv = $settings['cash_per_pv'] ?? 0;
$min_threshold = $settings['min_withdrawal'] ?? 0;
$balance_cash = $balance_pv * $cash_per_pv;

$transactions = $wallet->getTransactions($user_id);
$withdrawals = $wallet->getWithdrawalRequests($user_id);
$customer_orders = $order_system->getByUserId($user_id);

$page_title = 'Customer Dashboard — My Rewards & Orders';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="pg-hero">
    <div class="pg-hero-eyebrow">Customer Center</div>
    <div class="pg-hero-title">My <em>Wallet & Orders</em></div>
</div>

<div class="section">
    <?php if ($message): ?>
        <div style="background:var(--sage);color:var(--white);padding:15px;margin-bottom:20px;border-radius:var(--r);font-size:.9rem"><?php echo h($message); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div style="background:var(--terra);color:var(--white);padding:15px;margin-bottom:20px;border-radius:var(--r);font-size:.9rem"><?php echo h($error); ?></div>
    <?php endif; ?>

    <div class="prod-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:40px">
        <div style="background:var(--ink);color:var(--white);padding:30px;border-radius:var(--r-lg)">
            <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--terra);margin-bottom:10px">Total PV Balance</div>
            <h1 style="font-family:var(--display);font-size:3.5rem"><?php echo number_format($balance_pv, 2); ?> PV</h1>
        </div>
        <div style="background:var(--terra);color:var(--white);padding:30px;border-radius:var(--r-lg)">
            <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.6);margin-bottom:10px">Cash Value</div>
            <h1 style="font-family:var(--display);font-size:3.5rem">₹<?php echo number_format($balance_cash, 2); ?></h1>
            <p style="font-size:.75rem;opacity:.8">1 PV = ₹<?php echo h($cash_per_pv); ?></p>
        </div>
        <div style="background:var(--white);padding:30px;border-radius:var(--r-lg);border:1px solid var(--sand)">
            <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#888;margin-bottom:10px">Redeem PV for Cash</div>
            <p style="font-size:.78rem;color:#999;margin-bottom:12px">Min Threshold: <?php echo h($min_threshold); ?> PV</p>
            <form method="POST" style="display:flex;gap:10px">
                <input type="number" step="0.01" name="amount_pv" class="form-input" placeholder="PV Amount" min="<?php echo h($min_threshold); ?>" max="<?php echo h($balance_pv); ?>" required>
                <button type="submit" class="btn btn-terra btn-sm" <?php echo ($balance_pv < $min_threshold) ? 'disabled' : ''; ?>>Withdraw</button>
            </form>
        </div>
    </div>

    <!-- ORDER HISTORY -->
    <div style="margin-bottom:40px">
        <h2 style="font-family:var(--display); font-size:2rem; margin-bottom:20px">My Purchase History</h2>
        <div style="background:var(--white); border-radius:var(--r-lg); border:1px solid var(--sand); overflow:hidden">
            <table style="width:100%; border-collapse:collapse; font-size:.85rem">
                <thead style="background:var(--cream); color:#888; font-size:.72rem; text-transform:uppercase; letter-spacing:1px">
                    <tr>
                        <th style="padding:15px 24px; text-align:left">Order ID</th>
                        <th style="padding:15px 24px; text-align:left">Product</th>
                        <th style="padding:15px 24px; text-align:left">Date</th>
                        <th style="padding:15px 24px; text-align:left">Price</th>
                        <th style="padding:15px 24px; text-align:left">PV Earned</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($customer_orders)): ?>
                        <?php foreach ($customer_orders as $o): ?>
                        <tr style="border-bottom:1px solid var(--sand)">
                            <td style="padding:15px 24px"><strong><?php echo h($o['order_id']); ?></strong></td>
                            <td style="padding:15px 24px"><?php echo h($o['product_name']); ?></td>
                            <td style="padding:15px 24px"><?php echo date('Y-m-d', strtotime($o['created_at'])); ?></td>
                            <td style="padding:15px 24px">₹<?php echo h($o['price']); ?></td>
                            <td style="padding:15px 24px; color:var(--success-color); font-weight:700">+<?php echo h($o['pv_earned']); ?> PV</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding:40px; color:#aaa">You haven't placed any orders yet. <a href="../index.php" style="color:var(--terra)">Start shopping!</a></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:40px">
        <div style="background:var(--white);border-radius:var(--r-lg);border:1px solid var(--sand);overflow:hidden">
            <div style="background:var(--cream);padding:16px 24px;border-bottom:1px solid var(--sand);font-weight:700;font-size:.8rem;text-transform:uppercase;letter-spacing:1px">Points Ledger</div>
            <div style="padding:0">
                <table style="width:100%;border-collapse:collapse;font-size:.85rem">
                    <thead style="background:var(--cream);color:#888;font-size:.72rem;text-transform:uppercase;letter-spacing:1px">
                        <tr>
                            <th style="padding:12px 24px;text-align:left">Type</th>
                            <th style="padding:12px 24px;text-align:left">PV</th>
                            <th style="padding:12px 24px;text-align:left">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transactions)): ?>
                            <?php foreach ($transactions as $t): ?>
                            <tr style="border-bottom:1px solid var(--sand)">
                                <td style="padding:12px 24px">
                                    <span style="background:<?php echo $t['transaction_type'] === 'credit' ? 'var(--sage)' : 'var(--terra)'; ?>;color:var(--white);padding:2px 8px;border-radius:50px;font-size:.65rem;font-weight:700;text-transform:uppercase">
                                        <?php echo h($t['transaction_type']); ?>
                                    </span>
                                </td>
                                <td style="padding:12px 24px;font-weight:700"><?php echo ($t['transaction_type'] === 'credit' ? '+' : '-') . h($t['amount_pv']); ?></td>
                                <td style="padding:12px 24px; font-size:.75rem"><?php echo h($t['description']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div style="background:var(--white);border-radius:var(--r-lg);border:1px solid var(--sand);overflow:hidden">
            <div style="background:var(--cream);padding:16px 24px;border-bottom:1px solid var(--sand);font-weight:700;font-size:.8rem;text-transform:uppercase;letter-spacing:1px">Withdrawal Status</div>
            <div style="padding:0">
                <table style="width:100%;border-collapse:collapse;font-size:.85rem">
                    <thead style="background:var(--cream);color:#888;font-size:.72rem;text-transform:uppercase;letter-spacing:1px">
                        <tr>
                            <th style="padding:12px 24px;text-align:left">PV</th>
                            <th style="padding:12px 24px;text-align:left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($withdrawals)): ?>
                            <?php foreach ($withdrawals as $w): ?>
                            <tr style="border-bottom:1px solid var(--sand)">
                                <td style="padding:12px 24px;font-weight:700"><?php echo h($w['amount_pv']); ?></td>
                                <td style="padding:12px 24px">
                                    <span style="background:<?php
                                        echo $w['status'] === 'pending' ? '#f59e0b' : ($w['status'] === 'approved' ? 'var(--sage)' : 'var(--terra)');
                                    ?>;color:var(--white);padding:2px 8px;border-radius:50px;font-size:.65rem;font-weight:700;text-transform:uppercase">
                                        <?php echo h($w['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
