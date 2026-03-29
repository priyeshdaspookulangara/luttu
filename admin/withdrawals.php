<?php
$page_title = 'Manage Withdrawals — Admin';
require_once '../includes/header.php';
require_once '../classes/Wallet.php';

$wallet = new Wallet($database);

if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $id = $_POST['id'];
    $status = $_POST['status'];

    if ($wallet->updateWithdrawalStatus($id, $status)) {
        $message = "Withdrawal request " . h($status) . " successfully!";
    }
}

$requests_list = $wallet->getWithdrawalRequests();
?>

<div class="pg-hero">
    <div class="pg-hero-eyebrow">Admin Panel</div>
    <div class="pg-hero-title">Withdrawal <em>Requests</em></div>
</div>

<div class="section">
    <?php if ($message): ?>
        <div style="background:var(--sage);color:var(--white);padding:15px;margin-bottom:20px;border-radius:var(--r);font-size:.9rem"><?php echo h($message); ?></div>
    <?php endif; ?>

    <div style="background:var(--white);border-radius:var(--r-lg);border:1px solid var(--sand);overflow:hidden">
        <table style="width:100%;border-collapse:collapse;font-size:.85rem">
            <thead style="background:var(--cream);color:#888;font-size:.72rem;text-transform:uppercase;letter-spacing:1px">
                <tr>
                    <th style="padding:12px 24px;text-align:left">ID</th>
                    <th style="padding:12px 24px;text-align:left">User</th>
                    <th style="padding:12px 24px;text-align:left">PV Amount</th>
                    <th style="padding:12px 24px;text-align:left">Cash Amount</th>
                    <th style="padding:12px 24px;text-align:left">Requested At</th>
                    <th style="padding:12px 24px;text-align:left">Status</th>
                    <th style="padding:12px 24px;text-align:left">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests_list as $r): ?>
                <tr style="border-bottom:1px solid var(--sand)">
                    <td style="padding:12px 24px"><?php echo h($r['id']); ?></td>
                    <td style="padding:12px 24px;font-weight:700"><?php echo h($r['username']); ?></td>
                    <td style="padding:12px 24px"><?php echo h($r['amount_pv']); ?> PV</td>
                    <td style="padding:12px 24px">₹<?php echo h($r['amount_cash']); ?></td>
                    <td style="padding:12px 24px;color:#888"><?php echo h($r['requested_at']); ?></td>
                    <td style="padding:12px 24px">
                        <span style="background:<?php
                            echo $r['status'] === 'pending' ? '#f59e0b' : ($r['status'] === 'approved' ? 'var(--sage)' : 'var(--terra)');
                        ?>;color:var(--white);padding:2px 8px;border-radius:50px;font-size:.65rem;font-weight:700;text-transform:uppercase">
                            <?php echo ucfirst(h($r['status'])); ?>
                        </span>
                    </td>
                    <td style="padding:12px 24px">
                        <?php if ($r['status'] === 'pending'): ?>
                        <div style="display:flex;gap:8px">
                            <form method="POST">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo h($r['id']); ?>">
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--sage);border:1px solid var(--sage);padding:4px 10px">Approve</button>
                            </form>
                            <form method="POST">
                                <?php csrf_field(); ?>
                                <input type="hidden" name="id" value="<?php echo h($r['id']); ?>">
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-ghost btn-sm" style="color:var(--terra);border:1px solid var(--terra);padding:4px 10px">Reject</button>
                            </form>
                        </div>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
