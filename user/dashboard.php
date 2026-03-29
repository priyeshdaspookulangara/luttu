<?php
$page_title = 'User Dashboard — My PV Wallet';
require_once '../includes/header.php';

if (!$user->isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount_pv = $_POST['amount_pv'] ?? 0;
    if ($wallet->createWithdrawalRequest($user_id, $amount_pv)) {
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
?>

<div class="pg-hero">
    <div class="pg-hero-eyebrow">My PV Wallet</div>
    <div class="pg-hero-title">User <em>Dashboard</em></div>
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
            <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--terra);margin-bottom:10px">PV Balance</div>
            <h1 style="font-family:var(--display);font-size:3.5rem"><?php echo number_format($balance_pv, 2); ?> PV</h1>
        </div>
        <div style="background:var(--terra);color:var(--white);padding:30px;border-radius:var(--r-lg)">
            <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:rgba(255,255,255,.6);margin-bottom:10px">Equivalent Cash</div>
            <h1 style="font-family:var(--display);font-size:3.5rem">₹<?php echo number_format($balance_cash, 2); ?></h1>
            <p style="font-size:.75rem;opacity:.8">1 PV = ₹<?php echo h($cash_per_pv); ?></p>
        </div>
        <div style="background:var(--white);padding:30px;border-radius:var(--r-lg);border:1px solid var(--sand)">
            <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#888;margin-bottom:10px">Request Withdrawal</div>
            <p style="font-size:.78rem;color:#999;margin-bottom:12px">Min Threshold: <?php echo h($min_threshold); ?> PV</p>
            <form method="POST" style="display:flex;gap:10px">
                <input type="number" step="0.01" name="amount_pv" class="form-input" placeholder="PV" min="<?php echo h($min_threshold); ?>" max="<?php echo h($balance_pv); ?>" required>
                <button type="submit" class="btn btn-terra btn-sm" <?php echo ($balance_pv < $min_threshold) ? 'disabled' : ''; ?>>Withdraw</button>
            </form>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1.5fr 1fr;gap:40px">
        <div style="background:var(--white);border-radius:var(--r-lg);border:1px solid var(--sand);overflow:hidden">
            <div style="background:var(--cream);padding:16px 24px;border-bottom:1px solid var(--sand);font-weight:700;font-size:.8rem;text-transform:uppercase;letter-spacing:1px">Transaction History</div>
            <div style="padding:0">
                <table style="width:100%;border-collapse:collapse;font-size:.85rem">
                    <thead style="background:var(--cream);color:#888;font-size:.72rem;text-transform:uppercase;letter-spacing:1px">
                        <tr>
                            <th style="padding:12px 24px;text-align:left">Date</th>
                            <th style="padding:12px 24px;text-align:left">Type</th>
                            <th style="padding:12px 24px;text-align:left">PV Amount</th>
                            <th style="padding:12px 24px;text-align:left">Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t): ?>
                        <tr style="border-bottom:1px solid var(--sand)">
                            <td style="padding:12px 24px"><?php echo date('Y-m-d H:i', strtotime($t['created_at'])); ?></td>
                            <td style="padding:12px 24px">
                                <span style="background:<?php echo $t['transaction_type'] === 'credit' ? 'var(--sage)' : 'var(--terra)'; ?>;color:var(--white);padding:2px 8px;border-radius:50px;font-size:.65rem;font-weight:700;text-transform:uppercase">
                                    <?php echo h($t['transaction_type']); ?>
                                </span>
                            </td>
                            <td style="padding:12px 24px;font-weight:700"><?php echo ($t['transaction_type'] === 'credit' ? '+' : '-') . h($t['amount_pv']); ?></td>
                            <td style="padding:12px 24px"><?php echo h($t['description']); ?></td>
                        </tr>
                        <?php endforeach; ?>
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
                            <th style="padding:12px 24px;text-align:left">Date</th>
                            <th style="padding:12px 24px;text-align:left">PV</th>
                            <th style="padding:12px 24px;text-align:left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($withdrawals as $w): ?>
                        <tr style="border-bottom:1px solid var(--sand)">
                            <td style="padding:12px 24px"><?php echo date('Y-m-d', strtotime($w['requested_at'])); ?></td>
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
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
