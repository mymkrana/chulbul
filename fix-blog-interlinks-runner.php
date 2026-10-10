<?php
declare(strict_types=1);

/**
 * Migration runner to fix and normalize all blog internal links (interlinks).
 * Strictly modifies ONLY href="..." targets. Preserves all other content.
 */

const CBD_INTERLINK_RUNNER_SECRET = 'cbd_interlinks_fix_2026_m893k';

$token = (string)($_GET['token'] ?? $_POST['token'] ?? $_SERVER['HTTP_X_CBD_PUBLISH_TOKEN'] ?? '');
if (PHP_SAPI !== 'cli' && !hash_equals(CBD_INTERLINK_RUNNER_SECRET, $token)) {
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

function normalize_blog_links(string $content, array &$stats): string
{
    if ($content === '') {
        return $content;
    }

    // 1. Non-canonical domain links:
    // https://chulbuldesign.com/ui-ux-branding -> /ui-ux-branding
    // https://chulbuldesign.com/ -> /
    $content = preg_replace_callback(
        '/href=([\'\"])https?:\/\/(?:www\.)?chulbuldesign\.com(\/?[a-zA-Z0-9_\-\.]*)\1/i',
        function ($m) use (&$stats) {
            $quote = $m[1];
            $path = $m[2] ?? '';
            $target = ($path !== '' && $path !== '/') ? '/' . ltrim($path, '/') : '/';
            $key = 'domain:' . $m[0];
            $stats[$key] = ($stats[$key] ?? 0) + 1;
            return 'href=' . $quote . $target . $quote;
        },
        $content
    );

    // 2. Map non-existent /ecommerce-solutions to canonical /ecommerce-development
    $content = preg_replace_callback(
        '/href=([\'\"])(?:\.\.\/|\/)?ecommerce-solutions\/?\1/i',
        function ($m) use (&$stats) {
            $quote = $m[1];
            $stats['ecommerce-solutions'] = ($stats['ecommerce-solutions'] ?? 0) + 1;
            return 'href=' . $quote . '/ecommerce-development' . $quote;
        },
        $content
    );

    // 3. Strip .php extensions on known static routes
    $php_pages = ['about', 'contact-us', 'web-design', 'ecommerce-website-design'];
    foreach ($php_pages as $page) {
        $pattern = '/href=([\'\"])(?:\.\.\/|\/)?' . preg_quote($page, '/') . '\.php\1/i';
        $target = ($page === 'ecommerce-website-design') ? '/ecommerce-website-design' : '/' . $page;
        $content = preg_replace_callback(
            $pattern,
            function ($m) use (&$stats, $target) {
                $quote = $m[1];
                $stats['php_extension:' . $m[0]] = ($stats['php_extension:' . $m[0]] ?? 0) + 1;
                return 'href=' . $quote . $target . $quote;
            },
            $content
        );
    }

    // 4. Missing /city/ prefix on known cities
    $cities = ['delhi', 'gurgaon', 'mumbai', 'bangalore', 'noida', 'pune', 'hyderabad'];
    foreach ($cities as $city) {
        $pattern = '/href=([\'\"])(?:\.\.\/|\/)' . preg_quote($city, '/') . '\/?\1/i';
        $content = preg_replace_callback(
            $pattern,
            function ($m) use (&$stats, $city) {
                $quote = $m[1];
                $stats['city_prefix:' . $city] = ($stats['city_prefix:' . $city] ?? 0) + 1;
                return 'href=' . $quote . '/city/' . $city . $quote;
            },
            $content
        );
    }

    // 5. Convert all relative ../<slug> links to clean root-relative /<slug>
    $content = preg_replace_callback(
        '/href=([\'\"])\.\.\/([a-zA-Z0-9_\-]+)\1/i',
        function ($m) use (&$stats) {
            $quote = $m[1];
            $slug = $m[2];
            $stats['relative:' . $slug] = ($stats['relative:' . $slug] ?? 0) + 1;
            return 'href=' . $quote . '/' . $slug . $quote;
        },
        $content
    );

    return $content;
}

$summary = [
    'scanned' => 0,
    'updated' => 0,
    'replacements_total' => 0,
    'replacements_breakdown' => [],
    'updated_posts' => []
];

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT id, slug, content FROM posts WHERE status = 'published' AND deleted_at IS NULL FOR UPDATE");
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $summary['scanned'] = count($posts);

    $updateStmt = $pdo->prepare("UPDATE posts SET content = ?, updated_at = NOW() WHERE id = ?");

    foreach ($posts as $post) {
        $id = (int)$post['id'];
        $slug = (string)$post['slug'];
        $original = (string)($post['content'] ?? '');

        $postStats = [];
        $normalized = normalize_blog_links($original, $postStats);

        if ($normalized !== $original) {
            $updateStmt->execute([$normalized, $id]);
            $summary['updated']++;
            $summary['updated_posts'][] = [
                'id' => $id,
                'slug' => $slug,
                'replacements_count' => array_sum($postStats),
                'details' => $postStats
            ];

            foreach ($postStats as $key => $count) {
                $summary['replacements_breakdown'][$key] = ($summary['replacements_breakdown'][$key] ?? 0) + $count;
                $summary['replacements_total'] += $count;
            }
        }
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'All blog interlinks successfully checked and repaired',
        'summary' => $summary,
        'timestamp' => date('c')
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
}
