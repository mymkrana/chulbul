<?php
require_once __DIR__ . '/config.php';
require_login();
require_post_request();
verify_csrf();
$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    set_flash('Invalid post ID.', 'error');
    header('Location: posts.php');
    exit;
}

$pdo  = get_db();
$stmt = $pdo->prepare("SELECT title FROM posts WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    set_flash('Post not found.', 'error');
    header('Location: posts.php');
    exit;
}

$pdo->prepare("UPDATE posts SET deleted_at = NOW() WHERE id = ?")->execute([$id]);
audit_log('post_deleted', 'posts', $id, $post['title']);
set_flash('Post "' . $post['title'] . '" moved to trash. <a href="trash.php" class="underline font-bold">View Trash</a>', 'success');
header('Location: posts.php');
exit;
