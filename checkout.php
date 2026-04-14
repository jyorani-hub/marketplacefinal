<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

$user_id = $_SESSION['user_id'];

// Fetch cart items
$stmt = $pdo->prepare("
    SELECT c.id AS cart_id, c.quantity, p.id, p.name, p.price, p.stock
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
$stmt->execute([$user_id]);
$items = $stmt->fetchAll();

$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}

$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($items)) {
    try {
        $pdo->beginTransaction();

        // Create order
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'Pending')");
        $stmt->execute([$user_id, $total]);
        $order_id = $pdo->lastInsertId();

        // Create order items + update stock
        $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, ?)");
        $stockStmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");

        foreach ($items as $item) {
            $itemStmt->execute([$order_id, $item['id'], $item['name'], $item['price'], $item['quantity']]);
            $stockStmt->execute([$item['quantity'], $item['id']]);
        }

        // Clear cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
        $stmt->execute([$user_id]);

        $pdo->commit();
        $success = $order_id;
    } catch (Exception $ex) {
        $pdo->rollBack();
        $error = 'Checkout failed: ' . $ex->getMessage();
    }
}

$page_title = 'Checkout';
require_once 'includes/header.php';
?>

<h1>Checkout</h1>

<?php if ($success): ?>
    <div class="alert alert-success">Order #<?= (int)$success ?> placed successfully!</div>
    <p><a href="orders.php" class="btn">View My Orders</a> <a href="products.php" class="btn btn-secondary">Keep Shopping</a></p>
<?php elseif (empty($items)): ?>
    <div class="empty">Your cart is empty.</div>
<?php else: ?>
    <?php if (isset($error)): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

    <table>
        <thead>
            <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><?= e($item['name']) ?></td>
                    <td><?= (int)$item['quantity'] ?></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr><td colspan="3" style="text-align:right;font-weight:700;">Total:</td>
                <td style="font-weight:700;color:#2d7a4f;">$<?= number_format($total, 2) ?></td></tr>
        </tfoot>
    </table>

    <form method="post" class="mt-2">
        <p>Click below to confirm your order. (This is a demo - no real payment is processed.)</p>
        <button type="submit" class="btn">Place Order</button>
        <a href="cart.php" class="btn btn-secondary">Back to Cart</a>
    </form>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
