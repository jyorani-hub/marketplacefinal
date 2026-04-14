<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();
$page_title = $page_title ?? 'Admin - Marketplace';
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
        <a href="../index.php" class="logo">♻ Marketplace Admin</a>
        <nav class="main-nav">
            <a href="index.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a href="orders.php">Orders</a>
            <a href="users.php">Users</a>
            <a href="../index.php">Back to Site</a>
            <a href="../logout.php">Logout</a>
        </nav>
    </div>
</header>
<main class="container">
