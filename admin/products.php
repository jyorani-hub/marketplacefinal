<?php
$page_title = 'Manage Products';
require_once '_header.php';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: products.php?deleted=1');
    exit;
}

// Handle edit
$editing = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([(int)$_GET['edit']]);
    $editing = $stmt->fetch();
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $id = (int)$_POST['id'];
    $stmt = $pdo->prepare("UPDATE products SET name=?, description=?, price=?, category=?, `condition`=?, stock=? WHERE id=?");
    $stmt->execute([
        trim($_POST['name']),
        trim($_POST['description']),
        (float)$_POST['price'],
        $_POST['category'],
        $_POST['condition'],
        (int)$_POST['stock'],
        $id
    ]);
    header('Location: products.php?updated=1');
    exit;
}

$products = $pdo->query("SELECT p.*, u.name AS seller_name FROM products p LEFT JOIN users u ON p.seller_id = u.id ORDER BY p.created_at DESC")->fetchAll();
$categories = ['Electronics', 'Vinyl', 'CDs', 'Books', 'Clothing', 'Collectibles', 'Other'];
$conditions = ['New', 'Like New', 'Good', 'Used', 'Fair'];
?>

<h1>Manage Products</h1>

<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">Product deleted.</div><?php endif; ?>
<?php if (isset($_GET['updated'])): ?><div class="alert alert-success">Product updated.</div><?php endif; ?>

<?php if ($editing): ?>
    <div class="form-box" style="max-width:560px;">
        <h2>Edit Product</h2>
        <form method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" value="<?= (int)$editing['id'] ?>">
            <div class="form-group"><label>Name</label><input type="text" name="name" value="<?= e($editing['name']) ?>" required></div>
            <div class="form-group"><label>Description</label><textarea name="description"><?= e($editing['description']) ?></textarea></div>
            <div class="form-group"><label>Price</label><input type="number" step="0.01" name="price" value="<?= e($editing['price']) ?>" required></div>
            <div class="form-group">
                <label>Category</label>
                <select name="category">
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= e($c) ?>" <?= $editing['category'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Condition</label>
                <select name="condition">
                    <?php foreach ($conditions as $c): ?>
                        <option value="<?= e($c) ?>" <?= $editing['condition'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Stock</label><input type="number" name="stock" min="0" value="<?= (int)$editing['stock'] ?>" required></div>
            <button type="submit" class="btn">Save Changes</button>
            <a href="products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
<?php endif; ?>

<table>
    <thead><tr><th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Seller</th><th>Actions</th></tr></thead>
    <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><?= (int)$p['id'] ?></td>
                <td><?= e($p['name']) ?></td>
                <td><?= e($p['category']) ?></td>
                <td>$<?= number_format($p['price'], 2) ?></td>
                <td><?= (int)$p['stock'] ?></td>
                <td><?= e($p['seller_name'] ?? '-') ?></td>
                <td class="actions">
                    <a href="products.php?edit=<?= (int)$p['id'] ?>" class="btn btn-secondary" style="padding:4px 10px;font-size:0.82rem;">Edit</a>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                        <button type="submit" class="btn btn-danger confirm-delete" style="padding:4px 10px;font-size:0.82rem;">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '_footer.php'; ?>
