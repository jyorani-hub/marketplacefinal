<?php
$page_title = 'Manage Orders';
require_once '_header.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    $id = (int)$_POST['id'];
    $status = $_POST['status'] ?? 'Pending';
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    header('Location: orders.php?updated=1');
    exit;
}

$orders = $pdo->query("
    SELECT o.*, u.name AS user_name, u.email
    FROM orders o
    LEFT JOIN users u ON o.user_id = u.id
    ORDER BY o.order_date DESC
")->fetchAll();

$statuses = ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled'];
?>

<h1>Manage Orders</h1>

<?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Order status updated.</div><?php endif; ?>

<?php if (empty($orders)): ?>
    <div class="empty">No orders yet.</div>
<?php else: ?>
    <table>
        <thead><tr><th>#</th><th>User</th><th>Email</th><th>Total</th><th>Date</th><th>Status</th><th></th></tr></thead>
        <tbody>
            <?php foreach ($orders as $o): ?>
                <tr>
                    <td>#<?= (int)$o['id'] ?></td>
                    <td><?= e($o['user_name'] ?? '-') ?></td>
                    <td><?= e($o['email'] ?? '-') ?></td>
                    <td>$<?= number_format($o['total_price'], 2) ?></td>
                    <td><?= e($o['order_date']) ?></td>
                    <td>
                        <form method="post" style="display:flex;gap:5px;">
                            <input type="hidden" name="action" value="update_status">
                            <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                            <select name="status" style="padding:4px;">
                                <?php foreach ($statuses as $s): ?>
                                    <option value="<?= e($s) ?>" <?= $o['status'] === $s ? 'selected' : '' ?>><?= e($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-secondary" style="padding:4px 10px;font-size:0.82rem;">Save</button>
                        </form>
                    </td>
                    <td><a href="order_view.php?id=<?= (int)$o['id'] ?>" class="btn" style="padding:4px 10px;font-size:0.82rem;">View</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php require_once '_footer.php'; ?>
