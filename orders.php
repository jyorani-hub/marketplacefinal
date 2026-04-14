<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY order_date DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Fetch items for each order
$orderItems = [];
if (!empty($orders)) {
    $ids = array_column($orders, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id IN ($placeholders)");
    $stmt->execute($ids);
    foreach ($stmt->fetchAll() as $row) {
        $orderItems[$row['order_id']][] = $row;
    }
}

$page_title = 'My Orders';
require_once 'includes/header.php';
?>

<h1>My Orders</h1>

<?php if (empty($orders)): ?>
    <div class="empty">You haven't placed any orders yet.</div>
<?php else: ?>
    <?php foreach ($orders as $order): ?>
        <div style="background:#fff;padding:18px;border-radius:8px;margin-top:15px;box-shadow:0 1px 4px rgba(0,0,0,0.05);">
            <div style="display:flex;justify-content:space-between;flex-wrap:wrap;margin-bottom:10px;">
                <strong>Order #<?= (int)$order['id'] ?></strong>
                <span style="color:#777;"><?= e($order['order_date']) ?></span>
            </div>
            <table style="box-shadow:none;margin-top:0;">
                <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
                <tbody>
                    <?php foreach (($orderItems[$order['id']] ?? []) as $item): ?>
                        <tr>
                            <td><?= e($item['product_name']) ?></td>
                            <td><?= (int)$item['quantity'] ?></td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div style="margin-top:10px;display:flex;justify-content:space-between;">
                <span>Status: <strong><?= e($order['status']) ?></strong></span>
                <span style="font-weight:700;color:#2d7a4f;">Total: $<?= number_format($order['total_price'], 2) ?></span>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
