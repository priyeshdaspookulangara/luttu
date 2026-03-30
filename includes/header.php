<?php
require_once __DIR__ . '/db_connect.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';
require_once __DIR__ . '/../classes/Category.php';
require_once __DIR__ . '/../classes/Wallet.php';

$database = new Database($conn);
$user = new User($database);
$cat = new Category($database);
$wallet = new Wallet($database);

$is_logged_in = $user->isLoggedIn();
$is_admin = $user->isAdmin();

// Robust base path detection
$current_dir = basename(getcwd());
$rel_path = ($current_dir == 'admin' || $current_dir == 'user') ? '../' : './';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? h($page_title) : 'ShopPV — Reward Your Crunch'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Serif+Display:ital@0;1&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $rel_path; ?>assets/css/styles.css">
</head>
<body>

<div class="toast" id="toast"><div class="toast-dot"></div> <span class="toast-text">Added to cart</span></div>

<!-- ═══════════════════════════ NAV ═══════════════════════════ -->
<nav>
  <div class="nav-logo" onclick="window.location.href='<?php echo $rel_path; ?>index.php'">
    <em>S</em>HOPPV
  </div>
  <div class="nav-center">
    <a class="nav-item" href="<?php echo $rel_path; ?>index.php">Home</a>
    <div class="nav-item" id="menuTrigger">
      Products <span class="chevron">▾</span>
    </div>
    <?php if ($is_logged_in): ?>
        <a class="nav-item" href="<?php echo $rel_path; ?>user/dashboard.php">My Wallet</a>
        <?php if ($is_admin): ?>
            <a class="nav-item" href="<?php echo $rel_path; ?>admin/dashboard.php">Admin Panel</a>
        <?php endif; ?>
    <?php endif; ?>
  </div>
  <div class="nav-right">
    <?php if ($is_logged_in): ?>
        <div class="nav-action" onclick="window.location.href='<?php echo $rel_path; ?>logout.php'">⊙ Logout (<?php echo h($_SESSION['username']); ?>)</div>
    <?php else: ?>
        <div class="nav-action" onclick="window.location.href='<?php echo $rel_path; ?>login.php'">⊙ Login</div>
    <?php endif; ?>
    <div class="nav-action">⌕ Search</div>
    <div class="nav-action cart" onclick="window.location.href='<?php echo $rel_path; ?>user/dashboard.php'">
      Wallet <span class="cart-count">PV</span>
    </div>
  </div>
</nav>

<!-- MEGA MENU -->
<div class="mega-wrap" id="megaMenu">
  <div class="mega-inner">
    <div class="mega-sidebar">
      <div class="mega-sidebar-label">Categories</div>
      <?php
      $categories_nav = $cat->getAll();
      foreach ($categories_nav as $c): ?>
        <a class="mega-cat" href="<?php echo $rel_path; ?>index.php?category=<?php echo $c['id']; ?>">
            <?php echo h($c['name']); ?>
        </a>
      <?php endforeach; ?>
    </div>
    <div class="mega-body">
      <div class="mega-group-title">Quick Access</div>
      <div class="mega-link" onclick="window.location.href='<?php echo $rel_path; ?>index.php'">
        <div class="mega-link-name">🗂️ All Products</div>
        <div class="mega-link-sub">Browse everything</div>
      </div>
    </div>
    <div class="mega-promo">
      <div>
        <div class="mega-promo-tag">Reward System</div>
        <div class="mega-promo-title">EARN<br>PV<br>NOW</div>
        <div class="mega-promo-body">Every purchase credits PV to your wallet. Convert to cash easily.</div>
      </div>
      <button class="mega-promo-btn" onclick="window.location.href='<?php echo $rel_path; ?>user/dashboard.php'">View Wallet →</button>
    </div>
  </div>
</div>

<!-- TICKER -->
<div class="ticker">
  <div class="ticker-inner">
    EARN 100% PV ON SELECTED ITEMS &nbsp;·&nbsp; CONVERT PV TO CASH &nbsp;·&nbsp; SECURE WITHDRAWALS &nbsp;·&nbsp; NEW ARRIVALS EVERY WEEK &nbsp;·&nbsp;
    EARN 100% PV ON SELECTED ITEMS &nbsp;·&nbsp; CONVERT PV TO CASH &nbsp;·&nbsp; SECURE WITHDRAWALS &nbsp;·&nbsp; NEW ARRIVALS EVERY WEEK &nbsp;·&nbsp;
  </div>
</div>
