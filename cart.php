<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

$user_id = $_SESSION['user_id'];

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $product_id = (int)($_POST['product_id'] ?? 0);

    if ($action === 'add' && $product_id > 0) {
        // Check if already in cart
        $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $existing = $stmt->fetch();
        if ($existing) {
            $stmt = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
            $stmt->execute([$existing['id']]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
            $stmt->execute([$user_id, $product_id]);
        }
        header('Location: product.php?id=' . $product_id . '&added=1');
        exit;
    }

    if ($action === 'remove') {
        $cart_id = (int)($_POST['cart_id'] ?? 0);
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->execute([$cart_id, $user_id]);
        header('Location: cart.php');
        exit;
    }

    if ($action === 'update') {
        $cart_id = (int)($_POST['cart_id'] ?? 0);
        $qty = max(1, (int)($_POST['quantity'] ?? 1));
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$qty, $cart_id, $user_id]);
        header('Location: cart.php');
        exit;
    }
}

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

$page_title = 'Shopping Cart';
require_once 'includes/header.php';
?>

<h1>Shopping Cart</h1>

<?php if (empty($items)): ?>
    <div class="empty">Your cart is empty. <a href="products.php">Browse products</a></div>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Subtotal</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <td><a href="product.php?id=<?= (int)$item['id'] ?>"><?= e($item['name']) ?></a></td>
                    <td>$<?= number_format($item['price'], 2) ?></td>
                    <td>
                        <form method="post" style="display:flex;gap:5px;">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="cart_id" value="<?= (int)$item['cart_id'] ?>">
                            <input type="number" name="quantity" value="<?= (int)$item['quantity'] ?>" min="1" max="<?= (int)$item['stock'] ?>" style="width:60px;padding:4px;">
                            <button type="submit" class="btn btn-secondary" style="padding:4px 10px;font-size:0.85rem;">Update</button>
                        </form>
                    </td>
                    <td>$<?= number_format($item['price'] * $item['quantity'], 2) ?></td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="action" value="remove">
                            <input type="hidden" name="cart_id" value="<?= (int)$item['cart_id'] ?>">
                            <button type="submit" class="btn btn-danger confirm-delete" style="padding:5px 10px;font-size:0.85rem;">Remove</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align:right;font-weight:700;">Total:</td>
                <td colspan="2" style="font-weight:700;color:#2d7a4f;">$<?= number_format($total, 2) ?></td>
            </tr>
        </tfoot>
    </table>

    <div class="actions mt-2">
        <a href="checkout.php" class="btn">Proceed to Checkout</a>
        <a href="products.php" class="btn btn-secondary">Continue Shopping</a>
    </div>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
