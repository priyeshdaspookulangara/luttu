<?php
require_once __DIR__ . '/../includes/db_connect.php';
require_once __DIR__ . '/../classes/User.php';

$user = new User(new Database($conn));
$user->logout();

header('Location: login.php');
exit;
?>
