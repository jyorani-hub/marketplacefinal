<?php
$page_title = 'Admin Dashboard';
require_once '_header.php';

$stats = [
    'users' => $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn(),
    'products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
    'revenue' => $pdo->query("SELECT COALESCE(SUM(total_price), 0) FROM orders")->fetchColumn(),
];

$recent = $pdo->query("SELECT o.*, u.name AS user_name FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.order_date DESC LIMIT 5")->fetchAll();
?>

<h1>Admin Dashboard</h1>

<div class="admin-stats">
    <div class="stat-card"><div class="num"><?= (int)$stats['users'] ?></div><div class="label">Users</div></div>
    <div class="stat-card"><div class="num"><?= (int)$stats['products'] ?></div><div class="label">Products</div></div>
    <div class="stat-card"><div class="num"><?= (int)$stats['orders'] ?></div><div class="label">Orders</div></div>
    <div class="stat-card"><div class="num">$<?= number_format($stats['revenue'], 2) ?></div><div class="label">Revenue</div></div>
</div>

<h2 class="mt-2">Recent Orders</h2>
<?php if (empty($recent)): ?>
    <div class="empty">No orders yet.</div>
<?php else: ?>
    <table>
        <thead><tr><th>#</th><th>User</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
            <?php foreach ($recent as $o): ?>
                <tr>
                    <td>#<?= (int)$o['id'] ?></td>
                    <td><?= e($o['user_name'] ?? 'Unknown') ?></td>
                    <td>$<?= number_format($o['total_price'], 2) ?></td>
                    <td><?= e($o['status']) ?></td>
                    <td><?= e($o['order_date']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once '_footer.php'; ?>
