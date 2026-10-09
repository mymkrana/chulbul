<?php
/**
 * site-audit.php
 * Full website crawler + link checker + page type analyzer
 * Checks: DB-driven vs hardcoded pages, broken links (404), working links
 */
require_once __DIR__ . '/config.php';
require_login();

// ── Config ────────────────────────────────────────────────────────────────────
$is_live  = cbd_is_production_host();
$base_url = cbd_site_base_url();
$base_path = cbd_base_path();
$root_dir  = dirname(__DIR__);

// ── DB Connection ─────────────────────────────────────────────────────────────
$_pdo = get_db();

// ── Known page types ──────────────────────────────────────────────────────────
$page_types = [
    // DB-driven dynamic pages
    'dynamic' => [
        '/'                          => 'Homepage (index.php)',
        '/blog'                      => 'Blog Listing (DB)',
        '/cities'                    => 'Cities Listing',
    ],
    // Hub pages (service-category.php - hardcoded array)
    'hardcoded_hub' => [
        '/web-design-development'       => 'Web Design Hub',
        '/ecommerce-solutions'          => 'Ecommerce Hub',
        '/mobile-software-development'  => 'Mobile Hub',
        '/technologies'                 => 'Technologies Hub',
        '/seo-digital-marketing'        => 'SEO Hub',
        '/ui-ux-branding'               => 'UI/UX Hub',
        '/website-support'              => 'Support Hub',
        '/ai-automation'                => 'AI Hub',
    ],
    // Core static pages
    'hardcoded_core' => [
        '/about'      => 'About Page',
        '/contact-us' => 'Contact Page',
    ],
];

// ── Build full URL list to check ──────────────────────────────────────────────
$urls_to_check = [];

// 1. Core + hub pages
foreach ($page_types as $type => $pages) {
    foreach ($pages as $path => $label) {
        $urls_to_check[$path] = ['label' => $label, 'type' => $type, 'source' => 'known'];
    }
}

// 2. Industry pages
foreach (['ecommerce','education','healthcare','startup','news-portal','travel','restaurant','real-estate'] as $ind) {
    $urls_to_check["/industry/$ind"] = ['label' => "Industry: $ind", 'type' => 'dynamic', 'source' => 'industry'];
}

// 3. City pages — from the shared location database
$city_slugs = [];
if ($_pdo) {
    try {
        $city_slugs = $_pdo->query(
            "SELECT ci.slug FROM cities ci
             INNER JOIN countries co ON co.id = ci.country_id
             WHERE ci.status='published' AND co.status='published'
             ORDER BY ci.slug"
        )->fetchAll(PDO::FETCH_COLUMN);
    } catch (Throwable $error) {
        $city_slugs = [];
    }
}
foreach ($city_slugs as $city) {
    $urls_to_check["/city/$city"] = ['label' => "City: $city", 'type' => 'dynamic', 'source' => 'city'];
}

// 4. Blog posts from DB
$blog_posts = [];
if ($_pdo) {
    try {
        $blog_posts = $_pdo->query("SELECT slug, title FROM posts WHERE status='published' ORDER BY date DESC")->fetchAll();
    } catch (Exception $e) {}
}
foreach ($blog_posts as $post) {
    $urls_to_check["/blog/{$post['slug']}"] = ['label' => "Blog: {$post['title']}", 'type' => 'dynamic', 'source' => 'blog'];
}

// 5. Service pages from DB
$services = [];
if ($_pdo) {
    try {
        $services = $_pdo->query("SELECT slug, meta_title FROM services WHERE status=1 AND deleted_at IS NULL ORDER BY slug")->fetchAll();
    } catch (Exception $e) {}
}
foreach ($services as $svc) {
    $label = $svc['meta_title'] ?: $svc['slug'];
    $urls_to_check["/{$svc['slug']}"] = ['label' => "Service: $label", 'type' => 'dynamic', 'source' => 'service_db'];
}

// ── HTTP Check function ───────────────────────────────────────────────────────
function check_url(string $url): array {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => 8,
        CURLOPT_NOBODY         => false,
        CURLOPT_USERAGENT      => 'ChulbulSiteAudit/1.0',
        CURLOPT_HTTPHEADER     => ['Accept: text/html'],
    ]);
    $body    = curl_exec($ch);
    $status  = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $time    = round(curl_getinfo($ch, CURLINFO_TOTAL_TIME) * 1000);
    $redirect = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
    curl_close($ch);

    // Check if page has real content or is 404 page
    $is_404_page = ($status === 404) ||
                   (strpos($body, '404') !== false && strpos($body, 'Page Not Found') !== false);

    return [
        'status'    => $status,
        'time_ms'   => $time,
        'redirect'  => $redirect ?: '',
        'is_ok'     => $status >= 200 && $status < 300 && !$is_404_page,
        'is_redirect' => $status >= 300 && $status < 400,
        'is_error'  => $status >= 400 || $is_404_page,
        'body_len'  => strlen($body ?? ''),
    ];
}

// ── Run audit (AJAX mode) ─────────────────────────────────────────────────────
if (isset($_GET['action'])) {

    if ($_GET['action'] === 'get_urls') {
        header('Content-Type: application/json');
        $list = [];
        foreach ($urls_to_check as $path => $info) {
            $list[] = ['path' => $path, 'label' => $info['label'], 'type' => $info['type'], 'source' => $info['source']];
        }
        echo json_encode(['urls' => $list, 'total' => count($list)]);
        exit;
    }

    if ($_GET['action'] === 'check_url') {
        header('Content-Type: application/json');
        $path   = $_GET['path'] ?? '/';
        $result = check_url($base_url . $path);
        echo json_encode($result);
        exit;
    }
}

$total_urls = count($urls_to_check);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Site Audit — Chulbul Design</title>
<link rel="stylesheet" href="<?= $base_path ?>/admin/assets/admin.css">
<style>
body { background: #f1f5f9; font-family: 'Plus Jakarta Sans', sans-serif; margin: 0; }
.audit-wrap { max-width: 1400px; margin: 0 auto; padding: 24px 16px; }

/* Stats cards */
.stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 24px; }
@media(min-width:768px){ .stats-grid { grid-template-columns: repeat(6, 1fr); } }
.stat-card { background: #fff; border-radius: 12px; padding: 16px; text-align: center; border: 2px solid #e2e8f0; }
.stat-card .num { font-size: 2rem; font-weight: 800; line-height: 1; }
.stat-card .lbl { font-size: 0.72rem; color: #64748b; margin-top: 4px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; }
.stat-total   .num { color: #1e293b; }
.stat-ok      .num { color: #16a34a; }
.stat-error   .num { color: #dc2626; }
.stat-redirect .num { color: #d97706; }
.stat-dynamic  .num { color: #3b82f6; }
.stat-hardcoded .num { color: #8b5cf6; }

/* Progress */
.progress-wrap { background: #fff; border-radius: 12px; padding: 20px; margin-bottom: 20px; border: 1px solid #e2e8f0; }
.progress-bar-bg { background: #e2e8f0; border-radius: 99px; height: 12px; margin: 10px 0; overflow: hidden; }
.progress-bar-fill { height: 100%; background: linear-gradient(90deg, #EE483D, #49499A); border-radius: 99px; transition: width .3s; width: 0%; }
.progress-text { font-size: .85rem; color: #64748b; display: flex; justify-content: space-between; }

/* Filters */
.filter-bar { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 16px; }
.filter-btn { padding: 6px 14px; border-radius: 99px; border: 2px solid #e2e8f0; background: #fff; font-size: .8rem; font-weight: 600; cursor: pointer; transition: all .2s; }
.filter-btn.active, .filter-btn:hover { background: #EE483D; color: #fff; border-color: #EE483D; }

/* Table */
.audit-table-wrap { background: #fff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; }
.audit-table { width: 100%; border-collapse: collapse; font-size: .83rem; }
.audit-table th { background: #f8fafc; padding: 10px 14px; text-align: left; font-weight: 700; color: #374151; border-bottom: 2px solid #e2e8f0; font-size: .75rem; text-transform: uppercase; letter-spacing: .05em; }
.audit-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
.audit-table tr:last-child td { border-bottom: none; }
.audit-table tr:hover td { background: #fafafa; }

/* Badges */
.badge { display: inline-block; padding: 2px 8px; border-radius: 99px; font-size: .7rem; font-weight: 700; }
.badge-ok      { background: #dcfce7; color: #16a34a; }
.badge-error   { background: #fee2e2; color: #dc2626; }
.badge-redirect { background: #fef3c7; color: #d97706; }
.badge-pending { background: #f1f5f9; color: #94a3b8; }
.badge-dynamic  { background: #dbeafe; color: #2563eb; }
.badge-hardcoded { background: #ede9fe; color: #7c3aed; }
.badge-hub     { background: #fce7f3; color: #db2777; }

.url-path { font-family: monospace; font-size: .8rem; color: #374151; }
.url-label { font-size: .78rem; color: #64748b; }
.time-ms { font-size: .75rem; color: #94a3b8; }

/* Action buttons */
.btn-run { background: linear-gradient(135deg,#EE483D,#d63a2e); color: #fff; border: none; padding: 12px 28px; border-radius: 10px; font-weight: 700; font-size: 1rem; cursor: pointer; transition: opacity .2s; }
.btn-run:hover { opacity: .9; }
.btn-run:disabled { opacity: .5; cursor: not-allowed; }
.btn-stop { background: #1e293b; color: #fff; border: none; padding: 10px 20px; border-radius: 10px; font-weight: 600; font-size: .9rem; cursor: pointer; display: none; }

/* Source tags */
.src-city { color: #0891b2; }
.src-blog { color: #059669; }
.src-service { color: #7c3aed; }
.src-industry { color: #ea580c; }

.hidden-row { display: none; }
</style>
</head>
<body>
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="main-content">
<div class="audit-wrap">

  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px;">
    <div>
      <h1 style="font-size:1.6rem;font-weight:800;color:#1e293b;margin:0">🔍 Full Site Audit</h1>
      <p style="color:#64748b;margin:4px 0 0;font-size:.9rem">Total pages to check: <strong><?= $total_urls ?></strong> | Base: <code style="background:#f1f5f9;padding:2px 6px;border-radius:4px"><?= $base_url ?></code></p>
    </div>
    <div style="display:flex;gap:10px;align-items:center;">
      <button class="btn-run" id="btnRun" onclick="startAudit()">▶ Run Audit</button>
      <button class="btn-stop" id="btnStop" onclick="stopAudit()">⏹ Stop</button>
    </div>
  </div>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card stat-total">  <div class="num" id="s-total">0</div><div class="lbl">Checked</div></div>
    <div class="stat-card stat-ok">     <div class="num" id="s-ok">0</div><div class="lbl">✅ Working</div></div>
    <div class="stat-card stat-error">  <div class="num" id="s-error">0</div><div class="lbl">❌ Broken (404)</div></div>
    <div class="stat-card stat-redirect"><div class="num" id="s-redirect">0</div><div class="lbl">🔀 Redirects</div></div>
    <div class="stat-card stat-dynamic"> <div class="num" id="s-dynamic">0</div><div class="lbl">🗄 DB-Driven</div></div>
    <div class="stat-card stat-hardcoded"><div class="num" id="s-hardcoded">0</div><div class="lbl">📄 Hardcoded</div></div>
  </div>

  <!-- Progress -->
  <div class="progress-wrap">
    <div style="font-weight:700;color:#1e293b;margin-bottom:4px;">Progress</div>
    <div class="progress-bar-bg"><div class="progress-bar-fill" id="progressBar"></div></div>
    <div class="progress-text">
      <span id="progressText">Click "Run Audit" to start</span>
      <span id="progressPct">0%</span>
    </div>
  </div>

  <!-- Filters -->
  <div class="filter-bar">
    <button class="filter-btn active" onclick="filterTable('all', this)">All</button>
    <button class="filter-btn" onclick="filterTable('error', this)">❌ Broken Only</button>
    <button class="filter-btn" onclick="filterTable('ok', this)">✅ Working Only</button>
    <button class="filter-btn" onclick="filterTable('redirect', this)">🔀 Redirects</button>
    <button class="filter-btn" onclick="filterTable('dynamic', this)">🗄 DB-Driven</button>
    <button class="filter-btn" onclick="filterTable('hardcoded', this)">📄 Hardcoded</button>
    <button class="filter-btn" onclick="filterTable('city', this)">🏙 Cities</button>
    <button class="filter-btn" onclick="filterTable('blog', this)">📝 Blog</button>
    <button class="filter-btn" onclick="filterTable('service_db', this)">⚙ Services</button>
    <button class="filter-btn" onclick="filterTable('industry', this)">🏭 Industries</button>
  </div>

  <!-- Table -->
  <div class="audit-table-wrap">
    <table class="audit-table">
      <thead>
        <tr>
          <th>#</th>
          <th>URL Path</th>
          <th>Page / Label</th>
          <th>Type</th>
          <th>Source</th>
          <th>Status</th>
          <th>Time</th>
        </tr>
      </thead>
      <tbody id="auditBody">
        <!-- Rows injected by JS -->
      </tbody>
    </table>
  </div>

  <!-- Export -->
  <div style="margin-top:16px;text-align:right;">
    <button onclick="exportCSV()" style="background:#1e293b;color:#fff;border:none;padding:8px 18px;border-radius:8px;font-weight:600;cursor:pointer;font-size:.85rem;">⬇ Export CSV</button>
  </div>

</div>
</div>

<script>
let allUrls = [];
let results = {};
let running = false;
let stopFlag = false;
let counters = { total: 0, ok: 0, error: 0, redirect: 0, dynamic: 0, hardcoded: 0 };

async function startAudit() {
    document.getElementById('btnRun').disabled = true;
    document.getElementById('btnStop').style.display = 'inline-block';
    running = true;
    stopFlag = false;
    counters = { total: 0, ok: 0, error: 0, redirect: 0, dynamic: 0, hardcoded: 0 };
    results = {};

    // Fetch URL list
    const resp = await fetch('?action=get_urls');
    const data = await resp.json();
    allUrls = data.urls;

    // Build table rows (pending)
    const tbody = document.getElementById('auditBody');
    tbody.innerHTML = '';
    allUrls.forEach((u, i) => {
        const typeLabel = u.type === 'dynamic' ? '<span class="badge badge-dynamic">DB</span>' :
                          u.type === 'hardcoded_hub' ? '<span class="badge badge-hub">Hub</span>' :
                          '<span class="badge badge-hardcoded">Static</span>';
        const srcClass  = 'src-' + u.source;
        const srcLabel  = u.source === 'service_db' ? 'Service' :
                          u.source === 'city' ? 'City' :
                          u.source === 'blog' ? 'Blog' :
                          u.source === 'industry' ? 'Industry' :
                          u.source === 'known' ? 'Core' : u.source;
        tbody.innerHTML += `<tr id="row-${i}" data-type="${u.type}" data-source="${u.source}" data-status="pending">
            <td style="color:#94a3b8;font-size:.75rem">${i+1}</td>
            <td><span class="url-path">${u.path}</span></td>
            <td><span class="url-label">${u.label.length > 60 ? u.label.substring(0,60)+'…' : u.label}</span></td>
            <td>${typeLabel}</td>
            <td><span class="${srcClass}" style="font-size:.75rem;font-weight:600">${srcLabel}</span></td>
            <td id="status-${i}"><span class="badge badge-pending">Pending</span></td>
            <td id="time-${i}"><span class="time-ms">—</span></td>
        </tr>`;
    });

    // Check URLs one by one
    for (let i = 0; i < allUrls.length; i++) {
        if (stopFlag) break;

        const u = allUrls[i];
        const row = document.getElementById(`row-${i}`);

        try {
            const r = await fetch(`?action=check_url&path=${encodeURIComponent(u.path)}`);
            const res = await r.json();

            // Update counters
            counters.total++;
            if (res.is_ok)       counters.ok++;
            else if (res.is_error)   counters.error++;
            else if (res.is_redirect) counters.redirect++;

            if (u.type === 'dynamic') counters.dynamic++;
            else counters.hardcoded++;

            // Status badge
            let badge = '', rowBg = '';
            if (res.is_ok) {
                badge = `<span class="badge badge-ok">✓ ${res.status}</span>`;
                row.setAttribute('data-status', 'ok');
            } else if (res.is_redirect) {
                badge = `<span class="badge badge-redirect">→ ${res.status}</span>`;
                row.setAttribute('data-status', 'redirect');
            } else {
                badge = `<span class="badge badge-error">✗ ${res.status || '404'}</span>`;
                row.setAttribute('data-status', 'error');
                row.style.background = '#fff5f5';
            }

            document.getElementById(`status-${i}`).innerHTML = badge;
            document.getElementById(`time-${i}`).innerHTML = `<span class="time-ms">${res.time_ms}ms</span>`;
            results[u.path] = { ...u, ...res };

        } catch(e) {
            document.getElementById(`status-${i}`).innerHTML = `<span class="badge badge-error">Error</span>`;
            counters.error++;
            counters.total++;
        }

        // Update stats
        updateStats();
        updateProgress(i + 1, allUrls.length);

        // Scroll to current row
        row.scrollIntoView({ block: 'nearest', behavior: 'smooth' });

        // Small delay to not hammer server
        await new Promise(r => setTimeout(r, 120));
    }

    running = false;
    document.getElementById('btnRun').disabled = false;
    document.getElementById('btnStop').style.display = 'none';
    document.getElementById('progressText').textContent = stopFlag ? '⏹ Stopped' : '✅ Audit Complete!';
}

function stopAudit() {
    stopFlag = true;
    document.getElementById('btnStop').style.display = 'none';
}

function updateStats() {
    document.getElementById('s-total').textContent    = counters.total;
    document.getElementById('s-ok').textContent       = counters.ok;
    document.getElementById('s-error').textContent    = counters.error;
    document.getElementById('s-redirect').textContent = counters.redirect;
    document.getElementById('s-dynamic').textContent  = counters.dynamic;
    document.getElementById('s-hardcoded').textContent= counters.hardcoded;
}

function updateProgress(done, total) {
    const pct = Math.round(done / total * 100);
    document.getElementById('progressBar').style.width = pct + '%';
    document.getElementById('progressText').textContent = `Checking ${done} of ${total}…`;
    document.getElementById('progressPct').textContent  = pct + '%';
}

function filterTable(filter, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('#auditBody tr').forEach(row => {
        const type   = row.getAttribute('data-type') || '';
        const source = row.getAttribute('data-source') || '';
        const status = row.getAttribute('data-status') || '';

        let show = false;
        if (filter === 'all')       show = true;
        else if (filter === 'error')     show = status === 'error';
        else if (filter === 'ok')        show = status === 'ok';
        else if (filter === 'redirect')  show = status === 'redirect';
        else if (filter === 'dynamic')   show = type === 'dynamic';
        else if (filter === 'hardcoded') show = type.startsWith('hardcoded');
        else if (filter === 'city')      show = source === 'city';
        else if (filter === 'blog')      show = source === 'blog';
        else if (filter === 'service_db') show = source === 'service_db';
        else if (filter === 'industry')  show = source === 'industry';

        row.style.display = show ? '' : 'none';
    });
}

function exportCSV() {
    let csv = 'Path,Label,Type,Source,Status,Time(ms)\n';
    Object.entries(results).forEach(([path, r]) => {
        const status = r.is_ok ? 'OK' : r.is_redirect ? 'REDIRECT' : 'ERROR';
        csv += `"${path}","${(r.label||'').replace(/"/g,'""')}","${r.type}","${r.source}","${status} ${r.status}","${r.time_ms}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'chulbuldesign-audit-' + new Date().toISOString().slice(0,10) + '.csv';
    a.click();
}
</script>
</body>
</html>
