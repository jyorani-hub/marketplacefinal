<?php
$page_title = 'Home - Second-Hand Marketplace';
require_once 'includes/header.php';

$stmt = $pdo->query("SELECT * FROM products WHERE stock > 0 ORDER BY created_at DESC LIMIT 8");
$featured = $stmt->fetchAll();
?>

<section class="hero">
    <h1>Buy & Sell Second-Hand</h1>
    <p>Electronics, vinyl, books, clothing, collectibles and more - give items a second life.</p>
</section>

<h2 class="mt-2">Featured Listings</h2>

<?php if (empty($featured)): ?>
    <div class="empty">No products listed yet. <a href="sell.php">Be the first to list something!</a></div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($featured as $p): ?>
            <div class="product-card">
                <a href="product.php?id=<?= (int)$p['id'] ?>">
                    <div class="product-img">
                        <?php if (!empty($p['image_url'])): ?>
                            <img src="<?= e($p['image_url']) ?>" alt="<?= e($p['name']) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?= e($p['name']) ?></h3>
                        <div class="product-price">$<?= number_format($p['price'], 2) ?></div>
                        <div class="product-meta"><?= e($p['category']) ?> · <?= e($p['condition']) ?></div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
    <p class="text-center mt-2"><a href="products.php" class="btn">Browse All Products</a></p>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
