<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Customer.php';

$database = new Database($conn);
$customer = new Customer($database);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($customer->login($username, $password)) {
        header('Location: user/dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}

$page_title = 'Customer Login - ShopPV';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ════════════════════════ LOGIN ═════════════════════════════ -->
<div class="page active" id="login">
  <div class="login-outer">
    <div class="login-visual">
      <img src="https://images.unsplash.com/photo-1599490659213-e2b9527bd087?auto=format&fit=crop&q=80&w=800" class="abs-img" alt="Login Background" style="opacity: 0.2;">
      <div>
        <div class="login-visual-quote">"Shop more,<br><em>earn more.</em>"</div>
        <p class="login-visual-sub">Join thousands of users who earn Reward Points (PV) on every purchase. Your wallet is waiting.</p>
      </div>
    </div>
    <div class="login-form-wrap">
      <div class="login-form-inner">
        <div class="login-form-title">Customer<br>Login</div>
        <p class="login-form-sub">Sign in to your ShopPV account</p>

        <?php if ($error): ?>
            <div style="background:var(--terra);color:var(--white);padding:10px;margin-bottom:20px;border-radius:var(--r);font-size:.85rem"><?php echo h($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-field">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-input" placeholder="Enter username" required>
            </div>
            <div class="form-field">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;font-size:.78rem">
                <label style="display:flex;align-items:center;gap:7px;cursor:pointer"><input type="checkbox"> Remember me</label>
                <a href="#">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-fill" style="width:100%;justify-content:center">Sign In</button>
        </form>
        <p style="font-size:.82rem;color:#999;text-align:center;margin-top:20px">New to ShopPV? <a href="register.php" style="color:var(--terra);font-weight:600">Create an account →</a></p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
