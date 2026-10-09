<?php
require_once __DIR__ . '/config.php';
require_login();   // admin login required (replaces weak ?key= gate)
set_time_limit(600);
ini_set('memory_limit', '256M');

$base_url = cbd_site_base_url();

// ── Config ────────────────────────────────────────────────────────────────────
$MAX_PAGES   = 500;   // max pages to crawl
$TIMEOUT     = 8;     // seconds per request
$SKIP_EXTS   = ['jpg','jpeg','png','gif','webp','svg','ico','pdf','css','js','woff','woff2','ttf','zip','xml'];
$SKIP_PATHS  = ['/admin', '/login', '/logout', '/setup', '/install', '/migrate', '/fix_blog', '/opcache'];

// ── Crawler ───────────────────────────────────────────────────────────────────
$queue      = [$base_url . '/'];   // start from homepage
$visited    = [];                   // url => ['status', 'title', 'links_found']
$broken     = [];                   // broken links: [source_url, broken_url, status]
$link_map   = [];                   // broken_url => [source pages]

function normalize_url(string $url, string $base): string {
    // Remove fragment
    $url = strtok($url, '#');
    // Remove trailing slash except root
    if ($url !== $base . '/') $url = rtrim($url, '/');
    return $url;
}

function is_internal(string $url, string $base): bool {
    return strpos($url, $base) === 0;
}

function should_skip(string $url, array $skip_exts, array $skip_paths, string $base): bool {
    $path = str_replace($base, '', $url);
    // Skip extensions
    $ext = strtolower(pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION));
    if (in_array($ext, $skip_exts)) return true;
    // Skip admin paths
    foreach ($skip_paths as $sp) {
        if (strpos($path, $sp) === 0) return true;
    }
    return false;
}

function fetch_page(string $url, int $timeout): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT      => 'ChulbulBot/1.0 (Site Audit)',
        CURLOPT_HTTPHEADER     => ['Accept: text/html'],
    ]);
    $html    = curl_exec($ch);
    $status  = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $final   = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
    curl_close($ch);
    return ['html' => $html ?: '', 'status' => $status, 'final_url' => $final];
}

function extract_links(string $html, string $base, string $current_url): array {
    $links = [];
    // Extract all href
    preg_match_all('/href=["\']([^"\']+)["\']/', $html, $m);
    foreach ($m[1] as $href) {
        $href = trim($href);
        if (empty($href) || $href === '/') { $links[] = $base . '/'; continue; }
        if (strpos($href, '#') === 0) continue;
        if (strpos($href, 'mailto:') === 0) continue;
        if (strpos($href, 'tel:') === 0) continue;
        if (strpos($href, 'javascript:') === 0) continue;
        if (strpos($href, 'data:') === 0) continue;

        // Relative URL → absolute
        if (strpos($href, 'http') !== 0) {
            if (strpos($href, '/') === 0) {
                $href = $base . $href;
            } else {
                // Relative to current page
                $href = rtrim(dirname($current_url), '/') . '/' . $href;
            }
        }

        // Remove fragment
        $href = strtok($href, '#');
        $links[] = rtrim($href, '/') ?: $base . '/';
    }
    return array_unique($links);
}

function get_page_title(string $html): string {
    if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $m)) {
        return trim(html_entity_decode(strip_tags($m[1])));
    }
    return 'Untitled';
}

// ── CRAWL LOOP ────────────────────────────────────────────────────────────────
$crawled = 0;
while (!empty($queue) && $crawled < $MAX_PAGES) {

    $url = array_shift($queue);
    $url = normalize_url($url, $base_url);

    if (isset($visited[$url])) continue;
    if (!is_internal($url, $base_url)) continue;
    if (should_skip($url, $SKIP_EXTS, $SKIP_PATHS, $base_url)) continue;

    // Fetch page
    $result  = fetch_page($url, $TIMEOUT);
    $status  = $result['status'];
    $html    = $result['html'];
    $title   = $html ? get_page_title($html) : '';

    $visited[$url] = [
        'status'   => $status,
        'title'    => $title ?: basename(parse_url($url, PHP_URL_PATH) ?: $url),
        'links'    => 0,
        'broken'   => 0,
    ];
    $crawled++;

    if ($status >= 400 || !$html) continue;

    // Extract all links from this page
    $links = extract_links($html, $base_url, $url);
    $visited[$url]['links'] = count($links);

    foreach ($links as $link) {
        $link = normalize_url($link, $base_url);
        if (empty($link)) continue;

        if (is_internal($link, $base_url)) {
            // Queue for crawling if not visited
            if (!isset($visited[$link]) && !in_array($link, $queue)) {
                if (!should_skip($link, $SKIP_EXTS, $SKIP_PATHS, $base_url)) {
                    $queue[] = $link;
                }
            }
        } else {
            // External link — skip (not our concern for now)
            continue;
        }
    }
}

// ── BROKEN LINK DETECTION ─────────────────────────────────────────────────────
// All visited pages with 4xx/5xx/0 status are broken
// Find which pages LINK to them
foreach ($visited as $page_url => $page_data) {
    if ($page_data['status'] < 400 && $page_data['status'] !== 0) continue;

    // Re-fetch this page? No — find who links to it
    // We need to re-scan each good page's links
}

// Re-scan: for each good page, check which of its links are broken
$broken_list = [];
foreach ($visited as $page_url => $page_data) {
    if ($page_data['status'] >= 400 || $page_data['status'] === 0) continue;

    $result = fetch_page($page_url, $TIMEOUT);
    $html   = $result['html'];
    if (!$html) continue;

    $links = extract_links($html, $base_url, $page_url);
    foreach ($links as $link) {
        $link = normalize_url($link, $base_url);
        if (!is_internal($link, $base_url)) continue;
        if (should_skip($link, $SKIP_EXTS, $SKIP_PATHS, $base_url)) continue;

        // Check status
        if (isset($visited[$link])) {
            $link_status = $visited[$link]['status'];
        } else {
            // Not visited (skipped or new) — quick check
            $r = fetch_page($link, 5);
            $link_status = $r['status'];
            $visited[$link] = ['status' => $link_status, 'title' => '', 'links' => 0, 'broken' => 0];
        }

        if ($link_status === 0 || $link_status >= 400) {
            $broken_list[] = [
                'source_url'   => $page_url,
                'source_title' => $page_data['title'],
                'broken_url'   => $link,
                'status'       => $link_status ?: 'Timeout',
            ];
            $visited[$page_url]['broken']++;
        }
    }
}

// Deduplicate broken list
$broken_unique = [];
$seen_pairs    = [];
foreach ($broken_list as $b) {
    $key = $b['source_url'] . '||' . $b['broken_url'];
    if (!isset($seen_pairs[$key])) {
        $seen_pairs[$key]  = true;
        $broken_unique[]   = $b;
    }
}

// Sort by broken URL (group same broken links together)
usort($broken_unique, fn($a, $b) => strcmp($a['broken_url'], $b['broken_url']));

// Stats
$total_pages   = count($visited);
$ok_pages      = count(array_filter($visited, fn($p) => $p['status'] > 0 && $p['status'] < 400));
$broken_pages  = count(array_filter($visited, fn($p) => $p['status'] >= 400 || $p['status'] === 0));
$total_broken  = count($broken_unique);
$pages_affected = count(array_unique(array_column($broken_unique, 'source_url')));

// Group broken by broken URL
$broken_grouped = [];
foreach ($broken_unique as $b) {
    $broken_grouped[$b['broken_url']][] = $b;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Site Crawler — Chulbul Design</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f5f5f7;color:#1d1d1f}
.wrap{max-width:1280px;margin:0 auto;padding:32px 20px}
h1{font-size:26px;font-weight:700;margin-bottom:6px}
.sub{color:#666;font-size:14px;margin-bottom:28px}
.stats{display:grid;grid-template-columns:repeat(5,1fr);gap:14px;margin-bottom:28px}
.stat{background:#fff;border-radius:12px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.08);text-align:center}
.stat .num{font-size:28px;font-weight:800}
.stat .lbl{font-size:12px;color:#888;margin-top:3px}
.red .num{color:#e53e3e}.green .num{color:#38a169}.blue .num{color:#3182ce}.orange .num{color:#dd6b20}

/* Tabs */
.tabs{display:flex;gap:8px;margin-bottom:20px}
.tab{padding:8px 20px;border-radius:8px;cursor:pointer;font-size:14px;font-weight:500;border:1px solid #ddd;background:#fff;color:#666}
.tab.active{background:#1d1d1f;color:#fff;border-color:#1d1d1f}

/* Tables */
.card{background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);overflow:hidden;margin-bottom:24px}
.card-head{padding:16px 20px;border-bottom:1px solid #eee;display:flex;justify-content:space-between;align-items:center}
.card-head h2{font-size:16px;font-weight:600}
table{width:100%;border-collapse:collapse}
th{background:#f9f9f9;padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #eee}
td{padding:10px 14px;border-bottom:1px solid #f2f2f2;font-size:13px;vertical-align:top}
tr:last-child td{border-bottom:none}
tr:hover td{background:#fafafa}

.badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600}
.b200{background:#f0fff4;color:#276749}.b301{background:#fffaf0;color:#c05621}
.b404{background:#fff5f5;color:#c53030}.b0{background:#fffaf0;color:#c05621}
.b-ok{background:#f0fff4;color:#276749}

.url{font-family:monospace;font-size:12px;color:#e53e3e;word-break:break-all}
.src{font-size:12px;color:#555}
.src a{color:#3182ce;text-decoration:none}
.hidden{display:none}

.search-bar{padding:12px 16px;border-bottom:1px solid #eee;display:flex;gap:8px}
.search-bar input{flex:1;padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:13px;outline:none}
.search-bar input:focus{border-color:#1d1d1f}
</style>
</head>
<body>
<div class="wrap">

<h1>🕷️ Site Crawler — Chulbul Design</h1>
<p class="sub">Crawled on <?= date('d M Y, h:i A') ?> &nbsp;|&nbsp; Base: <code><?= $base_url ?></code> &nbsp;|&nbsp; Max pages: <?= $MAX_PAGES ?></p>

<!-- Stats -->
<div class="stats">
    <div class="stat blue">
        <div class="num"><?= $total_pages ?></div>
        <div class="lbl">Pages Found</div>
    </div>
    <div class="stat green">
        <div class="num"><?= $ok_pages ?></div>
        <div class="lbl">Pages OK</div>
    </div>
    <div class="stat red">
        <div class="num"><?= $broken_pages ?></div>
        <div class="lbl">Broken Pages</div>
    </div>
    <div class="stat red">
        <div class="num"><?= $total_broken ?></div>
        <div class="lbl">Broken Links</div>
    </div>
    <div class="stat orange">
        <div class="num"><?= $pages_affected ?></div>
        <div class="lbl">Pages Affected</div>
    </div>
</div>

<!-- Tabs -->
<div class="tabs">
    <div class="tab active" onclick="showTab('broken')">🔴 Broken Links (<?= $total_broken ?>)</div>
    <div class="tab" onclick="showTab('pages')">📄 All Pages (<?= $total_pages ?>)</div>
    <div class="tab" onclick="showTab('grouped')">📋 Grouped by URL (<?= count($broken_grouped) ?>)</div>
</div>

<!-- Tab: Broken Links -->
<div id="tab-broken">
    <div class="card">
        <div class="card-head">
            <h2>Broken Links (<?= $total_broken ?>)</h2>
            <span style="font-size:12px;color:#888"><?= $pages_affected ?> pages have broken links</span>
        </div>
        <div class="search-bar">
            <input type="text" id="search-broken" placeholder="Search URL or page title..." oninput="filterTable('tbl-broken', this.value)">
        </div>
        <?php if (empty($broken_unique)): ?>
            <div style="padding:50px;text-align:center;color:#888">✅ No broken links found!</div>
        <?php else: ?>
        <table id="tbl-broken">
            <thead><tr>
                <th>#</th>
                <th>Source Page</th>
                <th>Broken Link</th>
                <th>Status</th>
            </tr></thead>
            <tbody>
            <?php foreach ($broken_unique as $i => $b): ?>
            <tr>
                <td style="color:#bbb;width:36px"><?= $i+1 ?></td>
                <td>
                    <div><?= htmlspecialchars(mb_substr($b['source_title'], 0, 60)) ?></div>
                    <div class="src"><a href="<?= htmlspecialchars($b['source_url']) ?>" target="_blank"><?= htmlspecialchars(str_replace($base_url,'',$b['source_url'])) ?></a></div>
                </td>
                <td><div class="url"><?= htmlspecialchars(str_replace($base_url,'',$b['broken_url'])) ?></div></td>
                <td><span class="badge b<?= is_numeric($b['status']) ? $b['status'] : '0' ?>"><?= htmlspecialchars((string)$b['status']) ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

<!-- Tab: All Pages -->
<div id="tab-pages" class="hidden">
    <div class="card">
        <div class="card-head">
            <h2>All Pages Found (<?= $total_pages ?>)</h2>
        </div>
        <div class="search-bar">
            <input type="text" id="search-pages" placeholder="Search URL or title..." oninput="filterTable('tbl-pages', this.value)">
        </div>
        <table id="tbl-pages">
            <thead><tr>
                <th>#</th>
                <th>URL</th>
                <th>Title</th>
                <th>Status</th>
                <th>Links</th>
                <th>Broken</th>
            </tr></thead>
            <tbody>
            <?php $pi = 1; foreach ($visited as $vurl => $vdata): ?>
            <tr>
                <td style="color:#bbb;width:36px"><?= $pi++ ?></td>
                <td><div class="src"><a href="<?= htmlspecialchars($vurl) ?>" target="_blank"><?= htmlspecialchars(str_replace($base_url,'',$vurl) ?: '/') ?></a></div></td>
                <td style="max-width:280px"><?= htmlspecialchars(mb_substr($vdata['title'],0,60)) ?></td>
                <td><span class="badge b<?= $vdata['status'] ?>"><?= $vdata['status'] ?></span></td>
                <td style="color:#888"><?= $vdata['links'] ?></td>
                <td><?php if($vdata['broken']>0): ?><span style="color:#e53e3e;font-weight:600"><?= $vdata['broken'] ?></span><?php else: ?>—<?php endif; ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Tab: Grouped by broken URL -->
<div id="tab-grouped" class="hidden">
    <div class="card">
        <div class="card-head">
            <h2>Broken URLs — Grouped (<?= count($broken_grouped) ?> unique)</h2>
            <span style="font-size:12px;color:#888">Fix one URL → multiple pages fixed</span>
        </div>
        <?php if (empty($broken_grouped)): ?>
            <div style="padding:50px;text-align:center;color:#888">✅ No broken links!</div>
        <?php else: ?>
        <table>
            <thead><tr>
                <th>Broken URL</th>
                <th>Appears On</th>
                <th>Occurrences</th>
            </tr></thead>
            <tbody>
            <?php foreach ($broken_grouped as $burl => $sources): ?>
            <tr>
                <td><div class="url"><?= htmlspecialchars(str_replace($base_url,'',$burl)) ?></div></td>
                <td>
                    <?php foreach (array_slice($sources, 0, 3) as $s): ?>
                        <div class="src"><a href="<?= htmlspecialchars($s['source_url']) ?>" target="_blank"><?= htmlspecialchars(str_replace($base_url,'',$s['source_url'])) ?></a></div>
                    <?php endforeach; ?>
                    <?php if(count($sources)>3): ?><div class="src" style="color:#888">...and <?= count($sources)-3 ?> more</div><?php endif; ?>
                </td>
                <td><span class="badge b404"><?= count($sources) ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>

</div>

<script>
function showTab(name) {
    document.getElementById('tab-broken').classList.add('hidden');
    document.getElementById('tab-pages').classList.add('hidden');
    document.getElementById('tab-grouped').classList.add('hidden');
    document.getElementById('tab-' + name).classList.remove('hidden');
    document.querySelectorAll('.tab').forEach((t,i) => {
        t.classList.toggle('active', ['broken','pages','grouped'][i] === name);
    });
}
function filterTable(tableId, query) {
    const q = query.toLowerCase();
    document.querySelectorAll('#' + tableId + ' tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>
</body>
</html>
