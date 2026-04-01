<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../classes/Database.php';
require_once __DIR__ . '/../classes/User.php';

$database = new Database($conn);
$user = new User($database);

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($user->login($username, $password)) {
        if ($user->isAdmin()) {
            header('Location: dashboard.php');
            exit;
        } else {
            // Log out and show error if not admin
            $user->logout();
            $error = 'Access denied. Administrator privileges required.';
        }
    } else {
        $error = 'Invalid admin credentials.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - ShopPV</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body { background: var(--ink); display: flex; align-items: center; justify-content: center; min-height: 100vh; margin:0; font-family: 'DM Sans', sans-serif; }
        .login-card { background: var(--white); padding: 40px; border-radius: var(--r-lg); width: 100%; max-width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); }
        .brand { font-family: 'Bebas Neue', sans-serif; font-size: 2.5rem; text-align: center; margin-bottom: 30px; letter-spacing: 2px; }
        .brand em { color: var(--terra); font-style: normal; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="brand"><em>S</em>HOPPV ADMIN</div>

        <?php if ($error): ?>
            <div style="background:var(--terra); color:#fff; padding:12px; border-radius:var(--r); margin-bottom:20px; font-size:.85rem; text-align:center"><?php echo h($error); ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-field">
                <label class="form-label">Admin Username</label>
                <input type="text" name="username" class="form-input" required autofocus>
            </div>
            <div class="form-field">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" required>
            </div>
            <button type="submit" class="btn btn-fill" style="width:100%; justify-content:center; margin-top:10px">Secure Login</button>
        </form>
        <div style="text-align:center; margin-top:20px; font-size:.75rem; color:#999">
            <a href="../index.php" style="color:var(--terra)">← Back to Storefront</a>
        </div>
    </div>
</body>
</html>
