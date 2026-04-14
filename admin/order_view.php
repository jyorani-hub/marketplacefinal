<?php
$page_title = 'Order Details';
require_once '_header.php';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT o.*, u.name AS user_name, u.email FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    echo '<div class="empty">Order not found.</div>';
    require_once '_footer.php';
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>

<h1>Order #<?= (int)$order['id'] ?></h1>

<div style="background:#fff;padding:20px;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,0.05);">
    <p><strong>Customer:</strong> <?= e($order['user_name'] ?? '-') ?> (<?= e($order['email'] ?? '-') ?>)</p>
    <p><strong>Date:</strong> <?= e($order['order_date']) ?></p>
    <p><strong>Status:</strong> <?= e($order['status']) ?></p>
    <p><strong>Total:</strong> $<?= number_format($order['total_price'], 2) ?></p>
</div>

<h2 class="mt-2">Items</h2>
<table>
    <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
    <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['product_name']) ?></td>
                <td><?= (int)$item['quantity'] ?></td>
                <td>$<?= number_format($item['price'], 2) ?></td>
                <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p class="mt-2"><a href="orders.php" class="btn btn-secondary">← Back to Orders</a></p>

<?php require_once '_footer.php'; ?>
