<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT p.*, u.name AS seller_name FROM products p LEFT JOIN users u ON p.seller_id = u.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    $page_title = 'Not Found';
    require_once 'includes/header.php';
    echo '<div class="empty">Product not found.</div>';
    require_once 'includes/footer.php';
    exit;
}

$page_title = $product['name'];
require_once 'includes/header.php';

$msg = '';
if (isset($_GET['added'])) {
    $msg = '<div class="alert alert-success">Added to cart!</div>';
}
?>

<?= $msg ?>

<div class="product-detail">
    <div class="product-img">
        <?php if (!empty($product['image_url'])): ?>
            <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>">
        <?php endif; ?>
    </div>
    <div>
        <h1><?= e($product['name']) ?></h1>
        <div class="product-meta"><?= e($product['category']) ?> · <?= e($product['condition']) ?> · Stock: <?= (int)$product['stock'] ?></div>
        <div class="price">$<?= number_format($product['price'], 2) ?></div>
        <p class="description"><?= nl2br(e($product['description'])) ?></p>
        <p class="product-meta">Seller: <?= e($product['seller_name'] ?? 'Unknown') ?></p>

        <?php if (is_logged_in()): ?>
            <form method="post" action="cart.php" class="actions">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <button type="submit" class="btn">Add to Cart</button>
                <a href="products.php" class="btn btn-secondary">Back</a>
            </form>
        <?php else: ?>
            <p><a href="login.php" class="btn">Login to purchase</a></p>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
