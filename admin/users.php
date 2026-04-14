<?php
$page_title = 'Manage Users';
require_once '_header.php';

// Handle delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)$_POST['id'];
    // Don't let admin delete themselves
    if ($id !== (int)$_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
    header('Location: users.php?deleted=1');
    exit;
}

// Toggle admin status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_admin') {
    $id = (int)$_POST['id'];
    if ($id !== (int)$_SESSION['user_id']) {
        $stmt = $pdo->prepare("UPDATE users SET is_admin = 1 - is_admin WHERE id = ?");
        $stmt->execute([$id]);
    }
    header('Location: users.php');
    exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();
?>

<h1>Manage Users</h1>

<?php if (isset($_GET['deleted'])): ?><div class="alert alert-success">User deleted.</div><?php endif; ?>

<table>
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Admin</th><th>Joined</th><th>Actions</th></tr></thead>
    <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= (int)$u['id'] ?></td>
                <td><?= e($u['name']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td><?= $u['is_admin'] ? 'Yes' : 'No' ?></td>
                <td><?= e($u['created_at']) ?></td>
                <td class="actions">
                    <?php if ((int)$u['id'] !== (int)$_SESSION['user_id']): ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="toggle_admin">
                            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                            <button type="submit" class="btn btn-secondary" style="padding:4px 10px;font-size:0.82rem;">Toggle Admin</button>
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                            <button type="submit" class="btn btn-danger confirm-delete" style="padding:4px 10px;font-size:0.82rem;">Delete</button>
                        </form>
                    <?php else: ?>
                        <em>(you)</em>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php require_once '_footer.php'; ?>
