<?php
$page_title = 'Member Network — ShopPV Admin';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../classes/MLM.php';

$mlm = new MLM($database);

// Get all root users (those without a sponsor)
$sql = "SELECT * FROM users WHERE sponsor_id IS NULL AND role = 'user'";
$root_users = $database->query($sql);

function renderAdminDownline($members) {
    if (empty($members)) return '';
    $html = '<ul class="list-group list-group-flush ml-4 border-left">';
    foreach ($members as $m) {
        $html .= '<li class="list-group-item">';
        $html .= '<div><strong>' . h($m['username']) . '</strong> <small class="text-muted">(' . h($m['member_code']) . ')</small></div>';
        $html .= '<div class="small text-danger">Level ' . h($m['level']) . '</div>';
        $html .= renderAdminDownline($m['children']);
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}
?>

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1 class="h2 m-0">Member Network</h1>
        <p class="text-muted">Visualizing the MLM hierarchy</p>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white">
        <h4 class="card-header__title">Network Tree</h4>
    </div>
    <div class="card-body">
        <?php if (!empty($root_users)): ?>
            <ul class="list-group list-group-flush">
                <?php foreach ($root_users as $root): ?>
                    <?php $downline = $mlm->getDownline($root['id']); ?>
                    <li class="list-group-item">
                        <div><i class="fas fa-user-circle mr-2"></i> <strong><?php echo h($root['username']); ?></strong> <small class="text-muted">(<?php echo h($root['member_code']); ?> - Root)</small></div>
                        <?php echo renderAdminDownline($downline); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-center text-muted py-5">No members found in the network.</p>
        <?php endif; ?>
    </div>
</div>

<div class="card mt-4">
    <div class="card-header bg-white">
        <h4 class="card-header__title">Recent Commissions</h4>
    </div>
    <div class="table-responsive">
        <table class="table table-flush mb-0">
            <thead class="thead-light">
                <tr>
                    <th>Date</th>
                    <th>Beneficiary</th>
                    <th>Buyer</th>
                    <th>Level</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT c.*, u1.username as beneficiary, u2.username as buyer
                        FROM commissions c
                        JOIN users u1 ON c.user_id = u1.id
                        JOIN users u2 ON c.buyer_id = u2.id
                        ORDER BY c.created_at DESC LIMIT 20";
                $recent_commissions = $database->query($sql);
                if (!empty($recent_commissions)):
                    foreach ($recent_commissions as $rc):
                ?>
                    <tr>
                        <td><?php echo date('Y-m-d H:i', strtotime($rc['created_at'])); ?></td>
                        <td><strong><?php echo h($rc['beneficiary']); ?></strong></td>
                        <td><?php echo h($rc['buyer']); ?></td>
                        <td><span class="badge badge-soft-info">Level <?php echo h($rc['level']); ?></span></td>
                        <td class="text-success">+<?php echo h($rc['amount_pv']); ?> PV</td>
                    </tr>
                <?php
                    endforeach;
                else:
                ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">No commissions recorded yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
