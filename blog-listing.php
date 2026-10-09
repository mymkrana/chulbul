<?php
require_once __DIR__ . '/includes/http.php';

$target = cbd_base_path() . '/blog';
$tag = trim((string)($_GET['tag'] ?? ''));
if ($tag !== '' && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $tag)) {
    $target .= '?tag=' . rawurlencode($tag);
}

header('Location: ' . $target, true, 301);
exit;
