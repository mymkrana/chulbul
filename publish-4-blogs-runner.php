<?php
declare(strict_types=1);

/**
 * 4 High-Impression Blogs Production Migration Runner
 */

const CBD_BLOG_PUBLISH_SECRET = 'cbd_publish_token_4blogs_2026_z91p';

$token = (string)($_GET['token'] ?? $_POST['token'] ?? $_SERVER['HTTP_X_CBD_PUBLISH_TOKEN'] ?? '');
if (PHP_SAPI !== 'cli' && !hash_equals(CBD_BLOG_PUBLISH_SECRET, $token)) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Access denied'], JSON_THROW_ON_ERROR);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

define('CBD_DEPLOY_BLOG_AUTHORIZED', true);
require_once __DIR__ . '/scripts/publish-blog-bundle.php';

try {
    $result = cbd_publish_blog_bundle('blogs_high_impression_cities_2026.json');
    echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
