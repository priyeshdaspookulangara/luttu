<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Wallet.php';

$database = new Database($conn);
$user = new User($database);
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

    $id = (int)$_POST['id'];
    $status = $_POST['status'];

    if ($wallet->updateWithdrawalStatus($id, $status)) {
        $message = "Withdrawal request " . h($status) . " successfully!";
    }
}

$requests_list = $wallet->getWithdrawalRequests(); // Returns Array

$page_title = 'Withdrawal Management — ShopPV Admin';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page__heading d-flex align-items-center">
    <div class="flex">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">Withdrawals</li>
            </ol>
        </nav>
        <h1 class="m-0">Withdrawal Requests</h1>
    </div>
</div>

<?php if ($message): ?>
    <div style="background:var(--success-color); color:#fff; padding:15px; border-radius:5px; margin-bottom:20px"><?php echo h($message); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header card-header-large bg-white">
        <h4 class="card-header__title">Pending and Processed Payouts</h4>
    </div>
    <div class="card-body" style="padding:0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>PV Amount</th>
                        <th>Cash Amount</th>
                        <th>Requested</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($requests_list)): ?>
                        <?php foreach ($requests_list as $r): ?>
                        <tr>
                            <td>#<?php echo h($r['id']); ?></td>
                            <td><strong><?php echo h($r['username']); ?></strong></td>
                            <td><?php echo h($r['amount_pv']); ?> PV</td>
                            <td><span style="color:var(--success-color); font-weight:600">₹<?php echo h($r['amount_cash']); ?></span></td>
                            <td><span style="font-size:.8rem; color:#888"><?php echo h($r['requested_at']); ?></span></td>
                            <td>
                                <span style="padding: 5px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: #fff; background: <?php
                                    echo $r['status'] === 'pending' ? 'var(--warning-color)' : ($r['status'] === 'approved' ? 'var(--success-color)' : 'var(--danger-color)');
                                ?>">
                                    <?php echo h($r['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($r['status'] === 'pending'): ?>
                                <div style="display:flex; gap:10px">
                                    <form method="POST">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo h($r['id']); ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" style="background:var(--success-color); border:none; color:#fff; padding:4px 12px; border-radius:4px; font-size:.75rem; cursor:pointer">Approve</button>
                                    </form>
                                    <form method="POST">
                                        <?php csrf_field(); ?>
                                        <input type="hidden" name="id" value="<?php echo h($r['id']); ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" style="background:var(--danger-color); border:none; color:#fff; padding:4px 12px; border-radius:4px; font-size:.75rem; cursor:pointer">Reject</button>
                                    </form>
                                </div>
                                <?php else: ?>
                                    <span style="font-size:.75rem; color:#aaa">Processed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center; padding:30px; color:#aaa">No withdrawal requests found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
