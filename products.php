<?php
$page_title = 'Browse Products';
require_once 'includes/header.php';

$categories = ['Electronics', 'Vinyl', 'CDs', 'Books', 'Clothing', 'Collectibles', 'Other'];
$category = isset($_GET['category']) ? $_GET['category'] : '';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$sql = "SELECT * FROM products WHERE stock > 0";
$params = [];

if ($category && in_array($category, $categories)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}
if ($search !== '') {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}
$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<h1>Browse Products</h1>

<form method="get" class="filters">
    <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search..." style="flex:1;min-width:160px;padding:6px 10px;border:1px solid #d1d5db;border-radius:5px;">
    <button type="submit" class="btn">Search</button>
</form>

<div class="filters">
    <a href="products.php" class="<?= $category === '' ? 'active' : '' ?>">All</a>
    <?php foreach ($categories as $c): ?>
        <a href="products.php?category=<?= urlencode($c) ?>" class="<?= $category === $c ? 'active' : '' ?>"><?= e($c) ?></a>
    <?php endforeach; ?>
</div>

<?php if (empty($products)): ?>
    <div class="empty">No products found.</div>
<?php else: ?>
    <div class="product-grid">
        <?php foreach ($products as $p): ?>
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
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
