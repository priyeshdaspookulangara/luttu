<?php
require_once __DIR__ . '/includes/db_connect.php';
require_once __DIR__ . '/classes/Customer.php';

$customer = new Customer(new Database($conn));
$customer->logout();

header('Location: login.php');
exit;
?>
