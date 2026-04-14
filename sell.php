<?php
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_login();

$error = '';
$success = '';
$categories = ['Electronics', 'Vinyl', 'CDs', 'Books', 'Clothing', 'Collectibles', 'Other'];
$conditions = ['New', 'Like New', 'Good', 'Used', 'Fair'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $category = $_POST['category'] ?? '';
    $condition = $_POST['condition'] ?? '';
    $stock = (int)($_POST['stock'] ?? 1);
    $image_url = '';

    if (strlen($name) < 3) {
        $error = 'Product name must be at least 3 characters.';
    } elseif ($price <= 0) {
        $error = 'Price must be greater than 0.';
    } elseif (!in_array($category, $categories)) {
        $error = 'Please select a valid category.';
    } elseif ($stock < 1) {
        $error = 'Stock must be at least 1.';
    } else {
        // Handle file upload (optional)
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['image']['size'] < 5 * 1024 * 1024) {
                $filename = uniqid('prod_') . '.' . $ext;
                $target = __DIR__ . '/uploads/' . $filename;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
                    $image_url = 'uploads/' . $filename;
                }
            } else {
                $error = 'Image must be jpg/png/gif/webp and under 5MB.';
            }
        }

        if (!$error) {
            $stmt = $pdo->prepare("
                INSERT INTO products (name, description, price, image_url, category, `condition`, stock, seller_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $description, $price, $image_url, $category, $condition, $stock, $_SESSION['user_id']]);
            $success = 'Product listed successfully!';
            $_POST = [];
        }
    }
}

$page_title = 'Sell an Item';
require_once 'includes/header.php';
?>

<div class="form-box" style="max-width:560px;">
    <h1>List an Item for Sale</h1>
    <?php if ($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?= e($success) ?> <a href="products.php">View listings</a></div><?php endif; ?>

    <form method="post" enctype="multipart/form-data" id="product-form">
        <div class="form-group">
            <label for="name">Product Name</label>
            <input type="text" id="name" name="name" value="<?= e($_POST['name'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description"><?= e($_POST['description'] ?? '') ?></textarea>
        </div>
        <div class="form-group">
            <label for="price">Price ($)</label>
            <input type="number" id="price" name="price" step="0.01" min="0.01" value="<?= e($_POST['price'] ?? '') ?>" required>
        </div>
        <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" required>
                <option value="">-- Select --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?= e($c) ?>" <?= (($_POST['category'] ?? '') === $c) ? 'selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="condition">Condition</label>
            <select id="condition" name="condition">
                <?php foreach ($conditions as $cond): ?>
                    <option value="<?= e($cond) ?>" <?= (($_POST['condition'] ?? 'Used') === $cond) ? 'selected' : '' ?>><?= e($cond) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="stock">Stock / Quantity</label>
            <input type="number" id="stock" name="stock" min="1" value="<?= e($_POST['stock'] ?? '1') ?>" required>
        </div>
        <div class="form-group">
            <label for="image">Image (optional)</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn">List Item</button>
    </form>
</div>

<?php require_once 'includes/footer.php'; ?>
