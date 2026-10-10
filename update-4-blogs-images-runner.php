<?php
declare(strict_types=1);

/**
 * Update featured images for the 4 high-impression blogs
 */

const CBD_IMG_UPDATE_SECRET = 'cbd_img_update_2026_q882x';

$token = (string)($_GET['token'] ?? $_POST['token'] ?? $_SERVER['HTTP_X_CBD_PUBLISH_TOKEN'] ?? '');
if (PHP_SAPI !== 'cli' && !hash_equals(CBD_IMG_UPDATE_SECRET, $token)) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Access denied'], JSON_THROW_ON_ERROR);
    exit;
}

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Robots-Tag: noindex, nofollow');

require_once __DIR__ . '/includes/database.php';

$pdo = cbd_database();
if (!$pdo) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed'], JSON_THROW_ON_ERROR);
    exit;
}

$image_map = [
    'web-design-bristol-costs-agencies-guide-2026' => [
        'img' => '../assets/images/uploads/ai-15-powerful-marketing-strategies-for-web-1780730844.jpg',
        'alt' => 'Web Design in Bristol (2026): Costs, Agency Comparison & Small Business Guide'
    ],
    'chicago-web-design-costs-b2b-financial-guide-2026' => [
        'img' => '../assets/images/uploads/b2b-lead-generation-website-architecture-2026.jpg',
        'alt' => 'Chicago Web Design & Development (2026): Costs, B2B & Financial Services Guide'
    ],
    'birmingham-web-design-jewellery-quarter-guide-2026' => [
        'img' => '../assets/images/uploads/blog-ecommerce-website-design-best-practices-1779443750.jpg',
        'alt' => 'Birmingham Web Design & Ecommerce (2026): Jewellery Quarter & Midlands Agency Guide'
    ],
    'web-design-abu-dhabi-ecommerce-guide-2026' => [
        'img' => '../assets/images/uploads/ecommerce-website-design-price-dubai-2026-1783422590.webp',
        'alt' => 'Web Design & Ecommerce in Abu Dhabi (2026): Pricing (AED), Bilingual UX & Business Guide'
    ],
];

$updated = [];

try {
    $pdo->beginTransaction();
    foreach ($image_map as $slug => $data) {
        $q = $pdo->prepare('SELECT content FROM posts WHERE slug = ? FOR UPDATE');
        $q->execute([$slug]);
        $content = $q->fetchColumn();
        if ($content) {
            $new_img_tag = sprintf(
                '<img class="w-full rounded-2xl shadow-sm mb-8" src="%s" alt="%s" loading="lazy">',
                htmlspecialchars($data['img']),
                htmlspecialchars($data['alt'])
            );
            $new_content = preg_replace('/<img[^>]+>/i', $new_img_tag, $content, 1);
            $up = $pdo->prepare('UPDATE posts SET content = ?, updated_at = NOW() WHERE slug = ?');
            $up->execute([$new_content, $slug]);
            $updated[$slug] = $data['img'];
        }
    }
    $pdo->commit();
    echo json_encode([
        'success' => true,
        'message' => 'Featured images successfully updated for all 4 blogs',
        'updated' => $updated,
        'timestamp' => date('c')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
