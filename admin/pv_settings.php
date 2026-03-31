<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';

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

    $cash_per_pv = (float)($_POST['cash_per_pv'] ?? 0);
    $min_withdrawal = (float)($_POST['min_withdrawal'] ?? 0);
    $effective_from = $_POST['effective_from'] ?? date('Y-m-d');

    $sql = "INSERT INTO pv_settings (cash_per_pv, min_withdrawal, effective_from) VALUES (?, ?, ?)";
    $database->insert($sql, [$cash_per_pv, $min_withdrawal, $effective_from], "dds");
    $message = 'PV Settings updated successfully!';
}

$sql = "SELECT * FROM pv_settings ORDER BY effective_from DESC";
$all_settings = $database->query($sql);

$page_title = 'PV Settings — ShopPV Admin';
require_once __DIR__ . '/includes/header.php';
?>

<div class="page__heading d-flex align-items-center">
    <div class="flex">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="dashboard.php">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">PV Settings</li>
            </ol>
        </nav>
        <h1 class="m-0">Reward Configuration</h1>
    </div>
</div>

<?php if ($message): ?>
    <div style="background:var(--success-color); color:#fff; padding:15px; border-radius:5px; margin-bottom:20px"><?php echo h($message); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-header card-header-large bg-white">
        <h4 class="card-header__title">Set New PV Conversion Rate</h4>
    </div>
    <div class="card-body form-card">
        <form method="POST">
            <?php csrf_field(); ?>
            <div class="form-group">
                <label>Cash per 1 PV (₹)</label>
                <input type="number" step="0.01" name="cash_per_pv" class="form-control-admin" required placeholder="e.g. 10.00">
            </div>
            <div class="form-group">
                <label>Min Withdrawal Threshold (PV)</label>
                <input type="number" step="0.01" name="min_withdrawal" class="form-control-admin" required placeholder="e.g. 100.00">
            </div>
            <div class="form-group">
                <label>Effective Date</label>
                <input type="date" name="effective_from" class="form-control-admin" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div style="grid-column: 1 / -1">
                <button type="submit" style="width:auto; padding: 10px 40px">Apply New Settings</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header card-header-large bg-white">
        <h4 class="card-header__title">Settings History & Audit</h4>
    </div>
    <div class="card-body" style="padding:0">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cash per PV</th>
                        <th>Min Withdrawal</th>
                        <th>Effective From</th>
                        <th>Date Created</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($all_settings && $all_settings->num_rows > 0): ?>
                        <?php foreach ($all_settings as $s): ?>
                        <tr>
                            <td>#<?php echo h($s['id']); ?></td>
                            <td><strong>₹<?php echo h($s['cash_per_pv']); ?></strong></td>
                            <td><?php echo h($s['min_withdrawal']); ?> PV</td>
                            <td><span style="background:var(--primary-color); color:#fff; padding:3px 10px; border-radius:4px; font-size:.75rem"><?php echo h($s['effective_from']); ?></span></td>
                            <td><span style="font-size:.8rem; color:#888"><?php echo h($s['created_at']); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center; padding:30px; color:#aaa">No settings records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
