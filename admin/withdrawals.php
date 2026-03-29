<?php
require_once '../includes/db_connect.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Wallet.php';

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

    $id = $_POST['id'];
    $status = $_POST['status'];

    if ($wallet->updateWithdrawalStatus($id, $status)) {
        $message = "Withdrawal request " . h($status) . " successfully!";
    }
}

$requests = $wallet->getWithdrawalRequests();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Withdrawals - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
            <div class="navbar-nav">
                <a class="nav-link" href="categories.php">Categories</a>
                <a class="nav-link" href="products.php">Products</a>
                <a class="nav-link" href="pv_settings.php">PV Settings</a>
                <a class="nav-link active" href="withdrawals.php">Withdrawals</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>Withdrawal Requests</h2>
        <?php if ($message): ?>
            <div class="alert alert-success mt-3"><?php echo h($message); ?></div>
        <?php endif; ?>

        <table class="table table-striped mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>PV Amount</th>
                    <th>Cash Amount</th>
                    <th>Requested At</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requests as $r): ?>
                <tr>
                    <td><?php echo h($r['id']); ?></td>
                    <td><?php echo h($r['username']); ?></td>
                    <td><?php echo h($r['amount_pv']); ?></td>
                    <td><?php echo h($r['amount_cash']); ?></td>
                    <td><?php echo h($r['requested_at']); ?></td>
                    <td>
                        <span class="badge bg-<?php
                            echo $r['status'] === 'pending' ? 'warning' : ($r['status'] === 'approved' ? 'success' : 'danger');
                        ?>"><?php echo ucfirst(h($r['status'])); ?></span>
                    </td>
                    <td>
                        <?php if ($r['status'] === 'pending'): ?>
                        <form method="POST" style="display:inline-block">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo h($r['id']); ?>">
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-sm btn-success">Approve</button>
                        </form>
                        <form method="POST" style="display:inline-block">
                            <?php csrf_field(); ?>
                            <input type="hidden" name="id" value="<?php echo h($r['id']); ?>">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-sm btn-danger">Reject</button>
                        </form>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
