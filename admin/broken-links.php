<?php
require_once __DIR__ . '/config.php';
require_login();
set_time_limit(300);

$pdo = get_db();
$base_url = cbd_site_base_url();

// ── Step 1: Build valid URL set from DB (fast check, no HTTP) ─────────────────
$valid_paths = ['/', '/blog', '/about', '/contact', '/portfolio', '/careers'];

// All published blog posts
$post_rows = $pdo->query("SELECT slug FROM posts WHERE status='published'")->fetchAll(PDO::FETCH_COLUMN);
foreach ($post_rows as $s) $valid_paths[] = '/blog/' . $s;

// All active services
$svc_rows = $pdo->query("SELECT slug FROM services WHERE status=1 AND deleted_at IS NULL")->fetchAll(PDO::FETCH_COLUMN);
foreach ($svc_rows as $s) $valid_paths[] = '/' . $s;

$valid_set = array_flip($valid_paths); // for O(1) lookup

// ── Step 2: Collect all pages to scan ────────────────────────────────────────
$pages = [];

// Blog posts
$posts = $pdo->query("SELECT id, slug, title, content FROM posts WHERE status='published' ORDER BY id DESC")->fetchAll();
foreach ($posts as $p) {
    $pages[] = [
        'type'    => 'Blog',
        'title'   => $p['title'],
        'url'     => '/blog/' . $p['slug'],
        'content' => $p['content'] ?? '',
    ];
}

// Service pages — scan intro_text + sections JSON
$svcs = $pdo->query("SELECT id, slug, subcategory, intro_text, sections, faq FROM services WHERE status=1 AND deleted_at IS NULL ORDER BY id DESC")->fetchAll();
foreach ($svcs as $s) {
    $content = ($s['intro_text'] ?? '') . ' ' . ($s['sections'] ?? '') . ' ' . ($s['faq'] ?? '');
    $pages[] = [
        'type'    => 'Service',
        'title'   => $s['subcategory'] ?: $s['slug'],
        'url'     => '/' . $s['slug'],
        'content' => $content,
    ];
}

// ── Step 3: Extract all internal href links from content ──────────────────────
function extract_internal_links(string $html, string $base_url): array {
    $links = [];
    // Match href and src attributes
    preg_match_all('/(?:href|src)=["\']([^"\'#\s]+)["\']/', $html, $m);
    foreach ($m[1] as $href) {
        if (empty($href)) continue;
        if (strpos($href, 'mailto:') === 0) continue;
        if (strpos($href, 'javascript:') === 0) continue;
        if (strpos($href, 'tel:') === 0) continue;
        if (strpos($href, 'data:') === 0) continue;

        // External link pointing away from our domain — skip
        if (strpos($href, 'http') === 0 && strpos($href, $base_url) !== 0) continue;

        // Make path-only (strip base)
        if (strpos($href, $base_url) === 0) {
            $href = substr($href, strlen($base_url));
        }
        if (empty($href)) $href = '/';

        // Skip asset files
        if (preg_match('/\.(jpg|jpeg|png|gif|webp|svg|css|js|ico|pdf|woff|ttf)(\?.*)?$/i', $href)) continue;

        $links[] = $href;
    }
    return array_unique($links);
}

// ── Step 4: HTTP check for links not in DB (unknown paths) ───────────────────
$http_cache = []; // url => status code

function http_check(string $path, string $base_url): int {
    global $http_cache;
    if (isset($http_cache[$path])) return $http_cache[$path];

    $url = $base_url . $path;
    $ch  = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_NOBODY         => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 BrokenLinkChecker/1.0',
    ]);
    curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $http_cache[$path] = $code;
    return $code;
}

// ── Step 5: Scan all pages ────────────────────────────────────────────────────
$results     = []; // broken links
$total_links = 0;
$total_pages = count($pages);

foreach ($pages as $page) {
    $links = extract_internal_links($page['content'], $base_url);
    foreach ($links as $path) {
        $total_links++;

        // Fast DB check first
        $clean_path = strtok($path, '?'); // strip query string
        $clean_path = rtrim($clean_path, '/') ?: '/';

        if (isset($valid_set[$clean_path])) continue; // ✅ known valid

        // Unknown path — do HTTP check
        $status = http_check($clean_path, $base_url);

        if ($status === 0 || $status >= 400) {
            $results[] = [
                'source_type'  => $page['type'],
                'source_title' => $page['title'],
                'source_url'   => $page['url'],
                'broken_link'  => $path,
                'status'       => $status ?: 'Timeout/No Response',
            ];
        }
    }
}

// Sort by source page
usort($results, fn($a, $b) => strcmp($a['source_url'], $b['source_url']));

// ── Output ────────────────────────────────────────────────────────────────────
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Broken Links Audit — Chulbul Design</title>
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f7; color: #1d1d1f; }
.wrap { max-width: 1200px; margin: 0 auto; padding: 32px 20px; }
h1 { font-size: 28px; font-weight: 700; margin-bottom: 8px; }
.sub { color: #666; margin-bottom: 28px; font-size: 15px; }

/* Stats */
.stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 32px; }
.stat { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
.stat .num { font-size: 32px; font-weight: 800; }
.stat .label { font-size: 13px; color: #666; margin-top: 4px; }
.stat.red .num { color: #e53e3e; }
.stat.green .num { color: #38a169; }
.stat.blue .num { color: #3182ce; }

/* Table */
.table-wrap { background: #fff; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.08); overflow: hidden; }
.table-header { padding: 20px 24px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center; }
.table-header h2 { font-size: 17px; font-weight: 600; }
table { width: 100%; border-collapse: collapse; }
th { background: #f9f9f9; padding: 12px 16px; text-align: left; font-size: 12px; font-weight: 600; color: #666; text-transform: uppercase; letter-spacing: .5px; border-bottom: 1px solid #eee; }
td { padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 14px; vertical-align: top; }
tr:last-child td { border-bottom: none; }
tr:hover td { background: #fafafa; }

.badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 12px; font-weight: 600; }
.badge-blog { background: #ebf4ff; color: #2b6cb0; }
.badge-service { background: #f0fff4; color: #276749; }
.badge-404 { background: #fff5f5; color: #c53030; }
.badge-0 { background: #fffaf0; color: #c05621; }

.source-title { font-weight: 500; }
.source-url { font-size: 12px; color: #888; margin-top: 2px; }
.broken-url { font-family: monospace; color: #e53e3e; word-break: break-all; }

.empty { text-align: center; padding: 60px 20px; color: #888; }
.empty .icon { font-size: 48px; margin-bottom: 12px; }
</style>
</head>
<body>
<div class="wrap">
    <h1>🔗 Broken Links Audit</h1>
    <p class="sub">Scanned on <?= date('d M Y, h:i A') ?> — Base URL: <code><?= htmlspecialchars($base_url) ?></code></p>

    <div class="stats">
        <div class="stat blue">
            <div class="num"><?= $total_pages ?></div>
            <div class="label">Pages Scanned</div>
        </div>
        <div class="stat blue">
            <div class="num"><?= $total_links ?></div>
            <div class="label">Total Links Found</div>
        </div>
        <div class="stat <?= count($results) > 0 ? 'red' : 'green' ?>">
            <div class="num"><?= count($results) ?></div>
            <div class="label">Broken Links</div>
        </div>
        <div class="stat green">
            <div class="num"><?= $total_links - count($results) ?></div>
            <div class="label">Working Links</div>
        </div>
    </div>

    <div class="table-wrap">
        <div class="table-header">
            <h2>Broken Links (<?= count($results) ?>)</h2>
            <?php if (count($results) > 0): ?>
            <span style="font-size:13px;color:#666"><?= count(array_unique(array_column($results,'source_url'))) ?> pages affected</span>
            <?php endif; ?>
        </div>

        <?php if (empty($results)): ?>
        <div class="empty">
            <div class="icon">✅</div>
            <div>No broken links found!</div>
        </div>
        <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Source Page</th>
                    <th>Broken Link</th>
                    <th>Status</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($results as $i => $r): ?>
                <tr>
                    <td style="color:#999;width:40px"><?= $i + 1 ?></td>
                    <td>
                        <div class="source-title"><?= htmlspecialchars($r['source_title']) ?></div>
                        <div class="source-url"><a href="<?= htmlspecialchars($base_url . $r['source_url']) ?>" target="_blank"><?= htmlspecialchars($r['source_url']) ?></a></div>
                    </td>
                    <td>
                        <div class="broken-url"><?= htmlspecialchars($r['broken_link']) ?></div>
                    </td>
                    <td>
                        <?php $s = $r['status']; ?>
                        <span class="badge badge-<?= is_numeric($s) ? $s : '0' ?>">
                            <?= htmlspecialchars((string)$s) ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge badge-<?= strtolower($r['source_type']) ?>"><?= $r['source_type'] ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
