<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/Customer.php';
require_once __DIR__ . '/../classes/MLM.php';

$database = new Database($conn);
$customer = new Customer($database);
$mlm = new MLM($database);

if (!$customer->isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

$user_id = $customer->getSessionId();

// Correctly get current user data by ID
$sql = "SELECT * FROM users WHERE id = ?";
$res = $database->query($sql, [$user_id], "i");
$user = $res[0];

$downline = $mlm->getDownline($user_id);
$commissions = $mlm->getCommissions($user_id);

$page_title = 'My Network — ShopPV';
require_once __DIR__ . '/../includes/header.php';

function renderDownline($members) {
    if (empty($members)) return '';
    $html = '<ul style="list-style:none; padding-left:20px; border-left:1px dashed var(--sand); margin-top:10px">';
    foreach ($members as $m) {
        $html .= '<li style="margin-bottom:10px">';
        $html .= '<div style="background:var(--cream); padding:10px 15px; border-radius:var(--r); border:1px solid var(--sand); display:inline-block">';
        $html .= '<span style="font-weight:700; color:var(--ink)">' . h($m['username']) . '</span> ';
        $html .= '<span style="font-size:.7rem; color:#999; margin-left:10px">' . h($m['member_code']) . '</span>';
        $html .= '<div style="font-size:.65rem; color:var(--terra); font-weight:700; text-transform:uppercase; margin-top:4px">Level ' . h($m['level']) . '</div>';
        $html .= '</div>';
        $html .= renderDownline($m['children']);
        $html .= '</li>';
    }
    $html .= '</ul>';
    return $html;
}
?>

<div class="pg-hero">
    <div class="pg-hero-eyebrow">Networking & Commissions</div>
    <div class="pg-hero-title">My <em>Network</em></div>
</div>

<div class="section">
    <!-- Referral Info -->
    <div style="background:var(--ink); color:var(--white); padding:40px; border-radius:var(--r-lg); margin-bottom:40px; display:flex; justify-content:space-between; align-items:center">
        <div>
            <div style="font-size:.62rem; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--terra); margin-bottom:10px">Your Personal Member Code</div>
            <h1 style="font-family:var(--display); font-size:3rem; margin:0"><?php echo h($user['member_code']); ?></h1>
            <p style="margin-top:10px; opacity:.7; font-size:.9rem">Share this code with your friends to build your network.</p>
        </div>
        <div style="text-align:right">
            <div style="font-size:.62rem; font-weight:700; letter-spacing:2px; text-transform:uppercase; color:var(--terra); margin-bottom:10px">Referral Link</div>
            <code style="background:rgba(255,255,255,.1); padding:10px 15px; border-radius:var(--r); font-size:.85rem; display:block">
                <?php
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
                echo "http://" . h($host) . rtrim($project_root, '/') . "/register.php?ref=" . h($user['member_code']);
                ?>
            </code>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:40px">
        <!-- COMMISSIONS -->
        <div>
            <h2 style="font-family:var(--display); font-size:1.8rem; margin-bottom:20px">Commission Earnings</h2>
            <div style="background:var(--white); border-radius:var(--r-lg); border:1px solid var(--sand); overflow:hidden">
                <table style="width:100%; border-collapse:collapse; font-size:.85rem">
                    <thead style="background:var(--cream); color:#888; font-size:.72rem; text-transform:uppercase; letter-spacing:1px">
                        <tr>
                            <th style="padding:15px 24px; text-align:left">Date</th>
                            <th style="padding:15px 24px; text-align:left">Buyer</th>
                            <th style="padding:15px 24px; text-align:left">Level</th>
                            <th style="padding:15px 24px; text-align:left">PV Earned</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($commissions)): ?>
                            <?php foreach ($commissions as $c): ?>
                            <tr style="border-bottom:1px solid var(--sand)">
                                <td style="padding:15px 24px"><?php echo date('Y-m-d', strtotime($c['created_at'])); ?></td>
                                <td style="padding:15px 24px"><strong><?php echo h($c['buyer_username']); ?></strong></td>
                                <td style="padding:15px 24px"><span style="background:var(--cream); padding:2px 8px; border-radius:50px; font-size:.7rem">Level <?php echo h($c['level']); ?> (<?php echo h($c['percentage']); ?>%)</span></td>
                                <td style="padding:15px 24px; color:var(--success-color); font-weight:700">+<?php echo h($c['amount_pv']); ?> PV</td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center; padding:40px; color:#aaa">No commissions earned yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- DOWNLINE -->
        <div>
            <h2 style="font-family:var(--display); font-size:1.8rem; margin-bottom:20px">My Downline</h2>
            <div style="background:var(--white); border-radius:var(--r-lg); border:1px solid var(--sand); padding:30px">
                <?php if (!empty($downline)): ?>
                    <?php echo renderDownline($downline); ?>
                <?php else: ?>
                    <p style="color:#aaa; text-align:center; padding:20px">No members in your downline yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
