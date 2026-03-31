<?php
require_once 'includes/db_connect.php';
require_once 'classes/Database.php';

$database = new Database($conn);

echo "<h1>ShopPV Debugger</h1>";

// 1. Connection Check
if ($conn->connect_error) {
    echo "<p style='color:red'>❌ Connection Failed: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color:green'>✅ MySQL Connected Successfully</p>";
}

// 2. Tables Check
$tables = ['users', 'categories', 'products', 'product_images', 'pv_settings', 'wallet_transactions', 'withdrawal_requests'];
foreach ($tables as $table) {
    try {
        $database->query("SELECT 1 FROM $table LIMIT 1");
        echo "<p style='color:green'>✅ Table '$table' exists</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>❌ Table '$table' missing: " . $e->getMessage() . "</p>";
    }
}

// 3. Admin User Check
try {
    $res = $database->query("SELECT id, username FROM users WHERE role = 'admin'");
    if (!empty($res)) {
        echo "<p style='color:green'>✅ Admin user found: " . h($res[0]['username']) . "</p>";
    } else {
        echo "<p style='color:orange'>⚠️ No Admin user found in 'users' table</p>";
    }
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Admin check failed: " . $e->getMessage() . "</p>";
}

// 4. Products Check
try {
    $res = $database->query("SELECT id, name FROM products");
    echo "<p style='color:green'>✅ Found " . count($res) . " products in database</p>";
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Product check failed: " . $e->getMessage() . "</p>";
}

echo "<hr><p><a href='index.php'>Back to Home</a></p>";
?>
