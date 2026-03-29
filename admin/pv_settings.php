<?php
$page_title = 'PV Settings — Admin';
require_once '../includes/header.php';

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

<div class="pg-hero">
    <div class="pg-hero-eyebrow">Admin Panel</div>
    <div class="pg-hero-title">PV Conversion <em>Settings</em></div>
</div>

<div class="section">
    <?php if ($message): ?>
        <div style="background:var(--sage);color:var(--white);padding:15px;margin-bottom:20px;border-radius:var(--r);font-size:.9rem"><?php echo h($message); ?></div>
    <?php endif; ?>

    <div style="background:var(--white);padding:30px;border-radius:var(--r-lg);border:1px solid var(--sand);margin-bottom:40px">
        <div style="font-size:.62rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#888;margin-bottom:20px">Set New PV Rate</div>
        <form method="POST">
            <?php csrf_field(); ?>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:20px">
                <div class="form-field">
                    <label class="form-label">Cash per 1 PV (₹)</label>
                    <input type="number" step="0.01" name="cash_per_pv" class="form-input" required>
                </div>
                <div class="form-field">
                    <label class="form-label">Min Withdrawal (PV)</label>
                    <input type="number" step="0.01" name="min_withdrawal" class="form-input" required>
                </div>
                <div class="form-field">
                    <label class="form-label">Effective From</label>
                    <input type="date" name="effective_from" class="form-input" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-terra">Update Settings</button>
        </form>
    </div>

    <div style="background:var(--white);border-radius:var(--r-lg);border:1px solid var(--sand);overflow:hidden">
        <div style="background:var(--cream);padding:16px 24px;border-bottom:1px solid var(--sand);font-weight:700;font-size:.8rem;text-transform:uppercase;letter-spacing:1px">Settings History</div>
        <table style="width:100%;border-collapse:collapse;font-size:.85rem">
            <thead style="background:var(--cream);color:#888;font-size:.72rem;text-transform:uppercase;letter-spacing:1px">
                <tr>
                    <th style="padding:12px 24px;text-align:left">ID</th>
                    <th style="padding:12px 24px;text-align:left">Cash per PV</th>
                    <th style="padding:12px 24px;text-align:left">Min Withdrawal</th>
                    <th style="padding:12px 24px;text-align:left">Effective From</th>
                    <th style="padding:12px 24px;text-align:left">Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($all_settings as $s): ?>
                <tr style="border-bottom:1px solid var(--sand)">
                    <td style="padding:12px 24px"><?php echo h($s['id']); ?></td>
                    <td style="padding:12px 24px;font-weight:700">₹<?php echo h($s['cash_per_pv']); ?></td>
                    <td style="padding:12px 24px"><?php echo h($s['min_withdrawal']); ?> PV</td>
                    <td style="padding:12px 24px"><?php echo h($s['effective_from']); ?></td>
                    <td style="padding:12px 24px;color:#888"><?php echo h($s['created_at']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
