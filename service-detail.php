<?php
require_once __DIR__ . '/includes/http.php';

$slug = trim(strtolower((string)($_GET['slug'] ?? '')));
if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
    require __DIR__ . '/404.php';
    exit;
}

$pdo = cbd_database();
if (!$pdo) {
    require __DIR__ . '/503.php';
    exit;
}

try {
    $statement = $pdo->prepare(
        "SELECT slug FROM services
         WHERE slug = ? AND status = 1 AND deleted_at IS NULL
         LIMIT 1"
    );
    $statement->execute([$slug]);
    $canonicalSlug = $statement->fetchColumn();
} catch (Throwable $error) {
    error_log('Legacy service redirect lookup failed: ' . $error->getMessage());
    require __DIR__ . '/503.php';
    exit;
}

if ($canonicalSlug === false) {
    require __DIR__ . '/404.php';
    exit;
}

cbd_redirect_path('/' . rawurlencode((string)$canonicalSlug));
