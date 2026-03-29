<?php
require_once '../includes/db_connect.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

$database = new Database($conn);
$user = new User($database);

if (!$user->isLoggedIn() || !$user->isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        die("CSRF token validation failed.");
    }

    $cash_per_pv = $_POST['cash_per_pv'] ?? 0;
    $min_withdrawal = $_POST['min_withdrawal'] ?? 0;
    $effective_from = $_POST['effective_from'] ?? date('Y-m-d');

    $sql = "INSERT INTO pv_settings (cash_per_pv, min_withdrawal, effective_from) VALUES (?, ?, ?)";
    $database->insert($sql, [$cash_per_pv, $min_withdrawal, $effective_from], "dds");
    $message = 'PV Settings updated successfully!';
}

$sql = "SELECT * FROM pv_settings ORDER BY effective_from DESC";
$all_settings = $database->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PV Settings - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">Admin Panel</a>
            <div class="navbar-nav">
                <a class="nav-link" href="categories.php">Categories</a>
                <a class="nav-link" href="products.php">Products</a>
                <a class="nav-link active" href="pv_settings.php">PV Settings</a>
                <a class="nav-link" href="withdrawals.php">Withdrawals</a>
                <a class="nav-link" href="../logout.php">Logout</a>
            </div>
        </div>
    </nav>
    <div class="container mt-4">
        <h2>PV Conversion Settings</h2>
        <?php if ($message): ?>
            <div class="alert alert-success mt-3"><?php echo h($message); ?></div>
        <?php endif; ?>

        <div class="card mt-4">
            <div class="card-header">Set New PV Rate</div>
            <div class="card-body">
                <form method="POST">
                    <?php csrf_field(); ?>
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Cash per 1 PV</label>
                            <input type="number" step="0.01" name="cash_per_pv" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Min Withdrawal (PV)</label>
                            <input type="number" step="0.01" name="min_withdrawal" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Effective From</label>
                            <input type="date" name="effective_from" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <h4 class="mt-5">Settings History</h4>
        <table class="table table-striped mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cash per PV</th>
                    <th>Min Withdrawal</th>
                    <th>Effective From</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_settings as $s): ?>
                <tr>
                    <td><?php echo h($s['id']); ?></td>
                    <td><?php echo h($s['cash_per_pv']); ?></td>
                    <td><?php echo h($s['min_withdrawal']); ?></td>
                    <td><?php echo h($s['effective_from']); ?></td>
                    <td><?php echo h($s['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
