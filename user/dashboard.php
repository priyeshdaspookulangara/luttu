<?php
require_once '../includes/db_connect.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Wallet.php';

$database = new Database($conn);
$user = new User($database);
$wallet = new Wallet($database);

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My PV Wallet - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">ShopPV</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="dashboard.php">Dashboard</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h2 class="mb-4">My PV Wallet</h2>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo h($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo h($error); ?></div>
        <?php endif; ?>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white shadow p-3">
                    <h5>Current PV Balance</h5>
                    <h3 class="fw-bold"><?php echo number_format($balance_pv, 2); ?> PV</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white shadow p-3">
                    <h5>Equivalent Cash</h5>
                    <h3 class="fw-bold">₹<?php echo number_format($balance_cash, 2); ?></h3>
                    <small>1 PV = ₹<?php echo h($cash_per_pv); ?></small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-white shadow p-3">
                    <h5>Request Withdrawal</h5>
                    <p class="small text-muted mb-2">Min. Threshold: <?php echo h($min_threshold); ?> PV</p>
                    <form method="POST">
                        <div class="input-group">
                            <input type="number" step="0.01" name="amount_pv" class="form-control" placeholder="PV" min="<?php echo h($min_threshold); ?>" max="<?php echo h($balance_pv); ?>" required>
                            <button class="btn btn-warning" type="submit" <?php echo ($balance_pv < $min_threshold) ? 'disabled' : ''; ?>>Withdraw</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="card shadow mb-4">
                    <div class="card-header fw-bold">Transaction History</div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount (PV)</th>
                                    <th>Description</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transactions as $t): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d H:i', strtotime($t['created_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $t['transaction_type'] === 'credit' ? 'success' : 'danger'; ?>">
                                            <?php echo ucfirst($t['transaction_type']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo ($t['transaction_type'] === 'credit' ? '+' : '-') . h($t['amount_pv']); ?></td>
                                    <td><?php echo h($t['description']); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="card shadow mb-4">
                    <div class="card-header fw-bold">Withdrawal Requests</div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>PV</th>
                                    <th>Cash</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($withdrawals as $w): ?>
                                <tr>
                                    <td><?php echo date('Y-m-d', strtotime($w['requested_at'])); ?></td>
                                    <td><?php echo h($w['amount_pv']); ?></td>
                                    <td>₹<?php echo h($w['amount_cash']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php
                                            echo $w['status'] === 'pending' ? 'warning' : ($w['status'] === 'approved' ? 'success' : 'danger');
                                        ?>"><?php echo ucfirst($w['status']); ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
