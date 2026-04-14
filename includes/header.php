<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
$page_title = $page_title ?? 'Second-Hand Marketplace';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($page_title) ?></title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header class="site-header">
    <div class="container header-inner">
        <a href="index.php" class="logo">♻ Marketplace</a>
        <nav class="main-nav">
            <a href="index.php">Home</a>
            <a href="products.php">Browse</a>
            <?php if (is_logged_in()): ?>
                <a href="sell.php">Sell</a>
                <a href="cart.php">Cart</a>
                <a href="orders.php">My Orders</a>
                <?php if (is_admin()): ?>
                    <a href="admin/index.php">Admin</a>
                <?php endif; ?>
                <a href="logout.php">Logout (<?= e($_SESSION['user_name']) ?>)</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
