<?php
require_once __DIR__ . '/config.php';
require_login();
require_post_request();
verify_csrf();

$id  = (int)($_POST['id'] ?? 0);
$pdo = get_db();

if ($id && $pdo) {
    $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ? AND deleted_at IS NULL");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        $pdo->prepare("UPDATE categories SET deleted_at = NOW() WHERE id = ?")->execute([$id]);
        set_flash('Category "' . $row['name'] . '" moved to trash. <a href="trash.php" class="underline font-bold">View Trash</a>', 'success');
    } else {
        set_flash('Category not found.', 'error');
    }
} else {
    set_flash('Invalid request.', 'error');
}

header('Location: categories.php');
exit;
