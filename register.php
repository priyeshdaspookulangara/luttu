<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/classes/Database.php';
require_once __DIR__ . '/classes/Customer.php';

$database = new Database($conn);
$customer = new Customer($database);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            if ($customer->register($username, $password, $email)) {
                $success = 'Registration successful! <a href="login.php" style="color:white;text-decoration:underline">Login here</a>';
            } else {
                $error = 'Registration failed. Username or email may already be taken.';
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}

$page_title = 'Register - ShopPV';
require_once __DIR__ . '/includes/header.php';
?>

<!-- ════════════════════════ REGISTER ═════════════════════════════ -->
<div class="page active" id="register">
  <div class="login-outer">
    <div class="login-visual">
      <img src="https://images.unsplash.com/photo-1599490659213-e2b9527bd087?auto=format&fit=crop&q=80&w=800" class="abs-img" alt="Register Background" style="opacity: 0.2;">
      <div>
        <div class="login-visual-quote">"Start your<br><em>reward journey.</em>"</div>
        <p class="login-visual-sub">Create your account and start earning Reward Points (PV) on every order. It's time to shop smart.</p>
      </div>
    </div>
    <div class="login-form-wrap">
      <div class="login-form-inner">
        <div class="login-form-title">Join<br>ShopPV</div>
        <p class="login-form-sub">Create your free customer account today</p>

        <?php if ($error): ?>
            <div style="background:var(--terra);color:var(--white);padding:10px;margin-bottom:20px;border-radius:var(--r);font-size:.85rem"><?php echo h($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div style="background:var(--sage);color:var(--white);padding:15px;margin-bottom:20px;border-radius:var(--r);font-size:.85rem"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-field">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-input" placeholder="Choose a username" required>
            </div>
            <div class="form-field">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" placeholder="you@example.com" required>
            </div>
            <div class="form-field">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" placeholder="••••••••" required>
            </div>
            <div class="form-field">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn btn-fill" style="width:100%;justify-content:center">Register Now</button>
        </form>
        <p style="font-size:.82rem;color:#999;text-align:center;margin-top:20px">Already have an account? <a href="login.php" style="color:var(--terra);font-weight:600">Sign in here →</a></p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
