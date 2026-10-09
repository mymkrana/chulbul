<?php
require_once __DIR__ . '/includes/http.php';

$slug = trim(strtolower((string)($_GET['slug'] ?? '')));
if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
    require __DIR__ . '/404.php';
    exit;
}

cbd_redirect_path('/blog/' . rawurlencode($slug));
