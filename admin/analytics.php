<?php
require_once __DIR__ . '/config.php';
require_login();
define('CBD_ANALYTICS_REPORT', true);
$analyticsQueryError = false;

$pdo = get_db();
$allowedDays = [1, 7, 30, 90, 365, 0];
$days = (int)($_GET['days'] ?? 30);
if (!in_array($days, $allowedDays, true)) $days = 30;
$offset = max(0, $days - 1);
$since = $days > 0 ? "(CURDATE() - INTERVAL {$offset} DAY)" : "'1970-01-01 00:00:00'";

function analytics_h($value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function analytics_value(?PDO $pdo, string $sql, array $params = [], $fallback = 0)
{
    if (!$pdo) return $fallback;
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $value = $stmt->fetchColumn();
        return $value === false ? $fallback : $value;
    } catch (Throwable $error) {
        $GLOBALS['analyticsQueryError'] = true;
        error_log('Analytics report query unavailable');
        return $fallback;
    }
}

function analytics_rows(?PDO $pdo, string $sql, array $params = []): array
{
    if (!$pdo) return [];
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch (Throwable $error) {
        $GLOBALS['analyticsQueryError'] = true;
        error_log('Analytics report query unavailable');
        return [];
    }
}

function analytics_time($seconds): string
{
    $seconds = max(0, (int)$seconds);
    if ($seconds < 60) return $seconds . 's';
    $minutes = intdiv($seconds, 60);
    $remaining = $seconds % 60;
    if ($minutes < 60) return $minutes . 'm ' . $remaining . 's';
    return intdiv($minutes, 60) . 'h ' . ($minutes % 60) . 'm';
}

$schemaReady = false;
if ($pdo) {
    try {
        $schemaReady = (bool)$pdo->query("SHOW TABLES LIKE 'analytics_sessions'")->fetchColumn()
            && (bool)$pdo->query("SHOW TABLES LIKE 'analytics_pageviews'")->fetchColumn()
            && (bool)$pdo->query("SHOW TABLES LIKE 'analytics_events'")->fetchColumn();
    } catch (Throwable $error) {
        $schemaReady = false;
    }
}

$sessions = $visitors = $pageviews = $clicks = $keyActions = $liveNow = 0;
$avgTime = $avgScroll = $engagementRate = 0;
$topPages = $topClicks = $referrers = $devices = $browsers = $countries = $campaigns = $journeys = $trend = $pageList = [];
$heatmap = $pageClicks = $sessionPages = $sessionEvents = [];
$mapDevice = is_string($_GET['map_device'] ?? null) ? $_GET['map_device'] : 'desktop';
if (!in_array($mapDevice, ['desktop','tablet','mobile'],true)) $mapDevice = 'desktop';
$mapWidths = ['desktop'=>'pv.viewport_width > 1024', 'tablet'=>'pv.viewport_width BETWEEN 769 AND 1024', 'mobile'=>'pv.viewport_width BETWEEN 1 AND 768'];
$mapCondition = $mapWidths[$mapDevice];
$selectedPage = trim((string)($_GET['page'] ?? ''));
if (strlen($selectedPage) > 512 || ($selectedPage !== '' && $selectedPage[0] !== '/')) $selectedPage = '';
$selectedSession = strtolower(trim((string)($_GET['session'] ?? '')));
if (!preg_match('/^[a-f0-9]{32}$/', $selectedSession)) $selectedSession = '';

if ($schemaReady) {
    $sessions = (int)analytics_value($pdo, "SELECT COUNT(DISTINCT session_id) FROM analytics_pageviews WHERE started_at >= {$since} AND started_at <= NOW()");
    $visitors = (int)analytics_value($pdo, "SELECT COUNT(DISTINCT visitor_id) FROM analytics_pageviews WHERE started_at >= {$since} AND started_at <= NOW()");
    $pageviews = (int)analytics_value($pdo, "SELECT COUNT(*) FROM analytics_pageviews WHERE started_at >= {$since} AND started_at <= NOW()");
    $avgTime = (int)analytics_value($pdo, "SELECT ROUND(AVG(engaged_seconds)) FROM analytics_pageviews WHERE started_at >= {$since} AND started_at <= NOW()");
    $avgScroll = (int)analytics_value($pdo, "SELECT ROUND(AVG(max_scroll)) FROM analytics_pageviews WHERE started_at >= {$since} AND started_at <= NOW()");
    $clicks = (int)analytics_value($pdo, "SELECT COUNT(*) FROM analytics_events WHERE created_at >= {$since} AND created_at <= NOW() AND event_type IN ('link_click','button_click','element_click','outbound_click','conversion')");
    $keyActions = (int)analytics_value($pdo, "SELECT COUNT(*) FROM analytics_events WHERE created_at >= {$since} AND created_at <= NOW() AND event_type IN ('conversion','form_submit')");
    $liveNow = (int)analytics_value($pdo, "SELECT COUNT(DISTINCT session_id) FROM analytics_pageviews WHERE last_seen_at >= (NOW() - INTERVAL 1 MINUTE)");
    $engagedSessions = (int)analytics_value($pdo, "SELECT COUNT(*) FROM (SELECT session_id FROM analytics_pageviews WHERE started_at >= {$since} AND started_at <= NOW() GROUP BY session_id HAVING SUM(engaged_seconds) >= 10 OR MAX(max_scroll) >= 50 OR COUNT(*) > 1) engaged");
    $engagementRate = $sessions > 0 ? round(($engagedSessions / $sessions) * 100, 1) : 0;

    $topPages = analytics_rows($pdo, "
        SELECT pv.page_path,
               COUNT(*) AS pageviews,
               COUNT(DISTINCT pv.session_id) AS sessions,
               ROUND(AVG(pv.engaged_seconds)) AS avg_time,
               ROUND(AVG(pv.max_scroll)) AS avg_scroll,
               COALESCE(MAX(ev.clicks), 0) AS clicks
        FROM analytics_pageviews pv
        LEFT JOIN (
            SELECT page_path, COUNT(*) AS clicks
            FROM analytics_events
            WHERE created_at >= {$since} AND created_at <= NOW()
              AND event_type IN ('link_click','button_click','element_click','outbound_click','conversion')
            GROUP BY page_path
        ) ev ON ev.page_path = pv.page_path
        WHERE pv.started_at >= {$since} AND pv.started_at <= NOW()
        GROUP BY pv.page_path
        ORDER BY pageviews DESC, sessions DESC
        LIMIT 50
    ");

    $topClicks = analytics_rows($pdo, "
        SELECT page_path, event_type, event_name, element_text, target_path, COUNT(*) AS total
        FROM analytics_events
        WHERE created_at >= {$since} AND created_at <= NOW() AND event_type IN ('link_click','button_click','element_click','outbound_click','conversion')
        GROUP BY page_path, event_type, event_name, element_text, target_path
        ORDER BY total DESC
        LIMIT 30
    ");

    $referrers = analytics_rows($pdo, "
        SELECT COALESCE(NULLIF(referrer, ''), 'Direct / unknown') AS label, COUNT(*) AS total
        FROM analytics_sessions
        WHERE started_at >= {$since} AND started_at <= NOW()
        GROUP BY label ORDER BY total DESC LIMIT 12
    ");
    $devices = analytics_rows($pdo, "
        SELECT COALESCE(NULLIF(device_type, ''), 'Unknown') AS label, COUNT(*) AS total
        FROM analytics_sessions
        WHERE started_at >= {$since} AND started_at <= NOW()
        GROUP BY label ORDER BY total DESC
    ");
    $browsers = analytics_rows($pdo, "
        SELECT COALESCE(NULLIF(browser, ''), 'Unknown') AS label, COUNT(*) AS total
        FROM analytics_sessions
        WHERE started_at >= {$since} AND started_at <= NOW()
        GROUP BY label ORDER BY total DESC LIMIT 8
    ");
    $countries = analytics_rows($pdo, "
        SELECT country_code AS label, COUNT(*) AS total
        FROM analytics_sessions
        WHERE started_at >= {$since} AND started_at <= NOW() AND country_code IS NOT NULL AND country_code <> ''
        GROUP BY country_code ORDER BY total DESC LIMIT 8
    ");
    $campaigns = analytics_rows($pdo, "
        SELECT COALESCE(NULLIF(utm_source, ''), 'No UTM') AS source,
               COALESCE(NULLIF(utm_campaign, ''), '—') AS campaign,
               COUNT(*) AS total
        FROM analytics_sessions
        WHERE started_at >= {$since} AND started_at <= NOW()
        GROUP BY source, campaign ORDER BY total DESC LIMIT 12
    ");
    $journeys = analytics_rows($pdo, "
        SELECT session_id, MIN(started_at) AS started_at, MAX(last_seen_at) AS last_seen_at,
               SUM(engaged_seconds) AS engaged_seconds, MAX(max_scroll) AS max_scroll,
               COUNT(*) AS pages,
               GROUP_CONCAT(page_path ORDER BY started_at SEPARATOR ' → ') AS journey
        FROM analytics_pageviews
        WHERE started_at >= {$since} AND started_at <= NOW()
        GROUP BY session_id
        ORDER BY started_at DESC
        LIMIT 40
    ");

    $trendDays = $days === 0 ? 30 : max(1, min(30, $days));
    $trendOffset = $trendDays - 1;
    $trend = analytics_rows($pdo, "
        SELECT DATE(started_at) AS day, COUNT(*) AS pageviews, COUNT(DISTINCT session_id) AS sessions
        FROM analytics_pageviews
        WHERE started_at >= (CURDATE() - INTERVAL {$trendOffset} DAY) AND started_at <= NOW()
        GROUP BY DATE(started_at)
        ORDER BY day ASC
    ");


    $byDay = array_column($trend, null, 'day');
    $trend = [];
    $today = (string)analytics_value($pdo, 'SELECT CURDATE()', [], date('Y-m-d'));
    for ($i = $trendDays - 1; $i >= 0; $i--) {
        $day = (new DateTimeImmutable($today))->modify("-{$i} days")->format('Y-m-d');
        $trend[] = $byDay[$day] ?? ['day'=>$day,'pageviews'=>0,'sessions'=>0];
    }

    $pageList = analytics_rows($pdo, "SELECT DISTINCT page_path FROM analytics_pageviews ORDER BY page_path ASC LIMIT 500");
    if ($selectedPage === '' && !empty($topPages)) $selectedPage = (string)$topPages[0]['page_path'];

    if ($selectedPage !== '') {
        $heatmap = analytics_rows($pdo, "
            SELECT ROUND(click_x / 5) * 5 AS x, ROUND(click_y / 5) * 5 AS y, COUNT(*) AS total
            FROM analytics_events e JOIN analytics_pageviews pv ON pv.view_id = e.view_id
            WHERE e.page_path = ? AND e.created_at >= {$since} AND e.created_at <= NOW() AND {$mapCondition}
              AND click_x IS NOT NULL AND click_y IS NOT NULL
            GROUP BY ROUND(click_x / 5) * 5, ROUND(click_y / 5) * 5
            ORDER BY total DESC LIMIT 120
        ", [$selectedPage]);
        $pageClicks = analytics_rows($pdo, "
            SELECT event_type, event_name, element_text, target_path, COUNT(*) AS total
            FROM analytics_events
            WHERE page_path = ? AND created_at >= {$since} AND event_type IN ('link_click','button_click','element_click','outbound_click','conversion')
            GROUP BY event_type, event_name, element_text, target_path
            ORDER BY total DESC LIMIT 20
        ", [$selectedPage]);
    }

    if ($selectedSession !== '') {
        $sessionPages = analytics_rows($pdo, "
            SELECT pv.view_id, pv.page_path, pv.page_title, pv.engaged_seconds, pv.max_scroll,
                   pv.started_at, pv.last_seen_at, COUNT(e.id) AS clicks
            FROM analytics_pageviews pv
            LEFT JOIN analytics_events e
              ON e.view_id = pv.view_id
             AND e.event_type IN ('link_click','button_click','element_click','outbound_click','conversion')
            WHERE pv.session_id = ? AND pv.started_at >= {$since}
            GROUP BY pv.view_id, pv.page_path, pv.page_title, pv.engaged_seconds, pv.max_scroll, pv.started_at, pv.last_seen_at
            ORDER BY pv.started_at ASC
        ", [$selectedSession]);
        $sessionEvents = analytics_rows($pdo, "
            SELECT created_at, page_path, event_type, event_name, element_text, target_path
            FROM analytics_events
            WHERE session_id = ? AND created_at >= {$since}
            ORDER BY created_at ASC LIMIT 250
        ", [$selectedSession]);
    }
}

require __DIR__ . '/includes/analytics-insights.php';
$trendMax = 1;
foreach ($trend as $row) $trendMax = max($trendMax, (int)$row['pageviews']);
$heatMax = 1;
foreach ($heatmap as $row) $heatMax = max($heatMax, (int)$row['total']);
$active_page = 'analytics';
$periodLabels = [1 => 'Today', 7 => '7 days', 30 => '30 days', 90 => '90 days', 365 => '1 year', 0 => 'All time'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Site Analytics — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="assets/analytics.css?v=<?= (int)filemtime(__DIR__ . '/assets/analytics.css') ?>">
</head>
<body class="bg-gray-50 text-gray-800 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:ml-60 min-h-screen flex flex-col">
    <header class="aq-header bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-10 shadow-sm">
        <button onclick="openSidebar()" class="lg:hidden text-gray-500" aria-label="Open navigation"><i class="bi bi-list text-2xl"></i></button>
        <div class="w-10 h-10 rounded-xl bg-[#EE483D]/10 text-[#EE483D] flex items-center justify-center"><i class="bi bi-graph-up-arrow text-xl"></i></div>
        <div>
            <h1 class="text-xl font-extrabold text-[#1e1e5c]">Site Analytics</h1>
            <p class="text-xs text-gray-400">First-party traffic, engagement and conversion activity</p>
        </div>
        <div class="ml-auto flex items-center gap-2 text-sm font-semibold text-green-700 bg-green-50 border border-green-200 rounded-full px-3 py-1.5">
            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span><?= $liveNow ?> seen in last minute
        </div>
    </header>

    <main class="flex-1 p-4 sm:p-6 max-w-[1500px] mx-auto w-full space-y-6">
        <?php if (!$schemaReady): ?>
        <div class="bg-amber-50 border border-amber-300 rounded-2xl p-5 flex items-start gap-4">
            <i class="bi bi-database-exclamation text-amber-600 text-2xl"></i>
            <div><h2 class="font-extrabold text-amber-900">Analytics database setup required</h2><p class="text-sm text-amber-800 mt-1">Import <code>database/migrations/2026-09-08-site-analytics.sql</code>, then refresh this page.</p></div>
        </div>
        <?php else: ?>

        <div class="flex flex-wrap items-center gap-2">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 mr-1">Period</span>
            <?php foreach ($periodLabels as $value => $label): ?>
            <a href="?days=<?= $value ?><?= $selectedPage !== '' ? '&amp;page=' . rawurlencode($selectedPage) : '' ?>" class="px-3 py-2 rounded-xl text-sm font-bold border transition <?= $days === $value ? 'bg-[#1e1e5c] border-[#1e1e5c] text-white' : 'bg-white border-gray-200 text-gray-600 hover:border-[#49499A]' ?>"><?= analytics_h($label) ?></a>
            <?php endforeach; ?>
            <span class="ml-auto text-xs text-gray-400"><i class="bi bi-shield-check text-green-600"></i> Pseudonymous IDs · no form values · no raw IP</span>
        </div>

        <?php
        $cards = [
            ['Sessions', $sessions, 'Tracked visits', 'bi-people-fill', '#EE483D'],
            ['Unique visitors', $visitors, '90-day browser IDs', 'bi-person-check-fill', '#49499A'],
            ['Page views', $pageviews, 'Pages opened', 'bi-eye-fill', '#0ea5e9'],
            ['Avg. engaged time', analytics_time($avgTime), 'Focused time; 60s idle limit', 'bi-clock-history', '#16a34a'],
            ['Engagement rate', $engagementRate . '%', '10s, 50% scroll or 2+ pages', 'bi-activity', '#d97706'],
            ['Average scroll', $avgScroll . '%', 'Page depth reached', 'bi-arrow-down-circle-fill', '#7c3aed'],
            ['Recorded clicks', $clicks, 'Links, buttons and page clicks', 'bi-hand-index-thumb-fill', '#db2777'],
            ['Contact clicks', $contactClicks, 'WhatsApp, phone or email intent', 'bi-bullseye', '#059669'],
        ];
        ?>
        <section class="grid grid-cols-2 lg:grid-cols-4 gap-4" aria-label="Analytics overview">
            <?php foreach ($cards as $card): ?>
            <div class="analytics-card bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <div class="flex items-center justify-between gap-3"><span class="text-xs font-extrabold uppercase tracking-wider text-gray-400"><?= analytics_h($card[0]) ?></span><i class="bi <?= analytics_h($card[3]) ?> text-lg" style="color:<?= analytics_h($card[4]) ?>"></i></div>
                <p class="text-3xl font-extrabold text-[#1e1e5c] mt-3"><?= analytics_h($card[1]) ?></p>
                <p class="text-xs text-gray-400 mt-1"><?= analytics_h($card[2]) ?></p>
            </div>
            <?php endforeach; ?>
        </section>

        <?php if ($analyticsQueryError): ?><div class="aq-notice" role="alert">Some queries could not load. Do not interpret missing values as zero; check the database migration and server logs.</div><?php endif; ?>
        <p class="text-xs text-gray-500">Calendar-day filters use the database server clock. Last loaded: <?= analytics_h((string)analytics_value($pdo, 'SELECT NOW()', [], 'unknown')) ?>. <a class="underline" href="<?= analytics_h('?' . http_build_query(['days'=>$days,'page'=>$selectedPage,'map_device'=>$mapDevice])) ?>">Refresh report</a></p>
        <?php require __DIR__ . '/includes/analytics-insights-panel.php'; ?>
        <section class="grid xl:grid-cols-[1.65fr_1fr] gap-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-start justify-between gap-4 mb-5"><div><h2 class="font-extrabold text-[#1e1e5c]">Traffic trend</h2><p class="text-xs text-gray-400 mt-1">Daily page views and sessions</p></div><span class="text-xs font-bold text-[#49499A] bg-[#49499A]/10 px-3 py-1 rounded-full">Last <?= isset($trendDays) ? $trendDays : 30 ?> days</span></div>
                <?php if (!$trend): ?><p class="h-40 flex items-center justify-center text-sm text-gray-400">Traffic record hote hi chart yahan dikhega.</p><?php else: ?>
                <div class="analytics-chart flex items-end gap-2 border-b border-gray-200 pt-3" aria-label="Daily traffic bar chart">
                    <?php foreach ($trend as $row): $height = max(0, round(((int)$row['pageviews'] / $trendMax) * 145)); ?>
                    <div class="flex-1 h-full flex flex-col items-center justify-end group relative" title="<?= analytics_h(date('d M', strtotime($row['day']))) ?>: <?= (int)$row['pageviews'] ?> views, <?= (int)$row['sessions'] ?> sessions">
                        <span class="hidden group-hover:block absolute -top-2 bg-[#1e1e5c] text-white text-[10px] px-2 py-1 rounded whitespace-nowrap z-10"><?= (int)$row['pageviews'] ?> views</span>
                        <div class="analytics-bar w-full max-w-7 rounded-t-lg" style="height:<?= $height ?>px"></div>
                        <span class="text-[9px] text-gray-400 mt-2 whitespace-nowrap"><?= analytics_h(date('d', strtotime($row['day']))) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-extrabold text-[#1e1e5c]">Devices</h2><p class="text-xs text-gray-400 mt-1 mb-5">Sessions by device type</p>
                <?php $deviceMax = 1; foreach ($devices as $row) $deviceMax = max($deviceMax, (int)$row['total']); ?>
                <div class="space-y-4">
                <?php foreach ($devices as $row): $pct = round(((int)$row['total'] / $deviceMax) * 100); ?>
                    <div><div class="flex justify-between text-sm mb-1.5"><span class="font-semibold text-gray-600"><i class="bi <?= $row['label'] === 'Mobile' ? 'bi-phone' : ($row['label'] === 'Tablet' ? 'bi-tablet' : 'bi-laptop') ?> mr-2 text-[#49499A]"></i><?= analytics_h($row['label']) ?></span><strong class="text-[#1e1e5c]"><?= (int)$row['total'] ?></strong></div><div class="h-2.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-[#49499A] rounded-full" style="width:<?= $pct ?>%"></div></div></div>
                <?php endforeach; ?>
                <?php if (!$devices): ?><p class="text-sm text-gray-400">No device data yet.</p><?php endif; ?>
                </div>
                <div class="border-t border-gray-100 mt-6 pt-5"><h3 class="text-xs font-extrabold uppercase tracking-wider text-gray-400 mb-3">Browsers</h3><div class="flex flex-wrap gap-2"><?php foreach ($browsers as $row): ?><span class="inline-flex items-center gap-2 bg-gray-50 border border-gray-100 rounded-lg px-2.5 py-1.5 text-xs text-gray-600"><strong><?= analytics_h($row['label']) ?></strong><?= (int)$row['total'] ?></span><?php endforeach; ?><?php if (!$browsers): ?><span class="text-xs text-gray-400">No browser data yet.</span><?php endif; ?></div></div>
                <?php if ($countries): ?><div class="border-t border-gray-100 mt-5 pt-5"><h3 class="text-xs font-extrabold uppercase tracking-wider text-gray-400 mb-3">Countries</h3><div class="flex flex-wrap gap-2"><?php foreach ($countries as $row): ?><span class="inline-flex items-center gap-2 bg-gray-50 border border-gray-100 rounded-lg px-2.5 py-1.5 text-xs text-gray-600"><strong><?= analytics_h($row['label']) ?></strong><?= (int)$row['total'] ?></span><?php endforeach; ?></div><p class="text-[10px] text-gray-400 mt-2">Country appears when the hosting/CDN supplies a country header.</p></div><?php endif; ?>
            </div>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100"><h2 class="font-extrabold text-[#1e1e5c]">Top pages</h2><p class="text-xs text-gray-400 mt-1">Traffic, engaged time, scroll depth and clicks for each page</p></div>
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wider"><tr><th class="text-left px-5 py-3">Page</th><th class="text-right px-4 py-3">Views</th><th class="text-right px-4 py-3">Sessions</th><th class="text-right px-4 py-3">Avg. time</th><th class="text-right px-4 py-3">Scroll</th><th class="text-right px-4 py-3">Clicks</th><th class="px-4 py-3">Map</th></tr></thead><tbody class="divide-y divide-gray-100">
                <?php foreach ($topPages as $row): ?>
                <tr class="hover:bg-gray-50"><td class="px-5 py-3 font-semibold text-[#1e1e5c] max-w-md truncate" title="<?= analytics_h($row['page_path']) ?>"><?= analytics_h($row['page_path']) ?></td><td class="px-4 py-3 text-right font-bold"><?= (int)$row['pageviews'] ?></td><td class="px-4 py-3 text-right"><?= (int)$row['sessions'] ?></td><td class="px-4 py-3 text-right whitespace-nowrap"><?= analytics_h(analytics_time($row['avg_time'])) ?></td><td class="px-4 py-3 text-right"><?= (int)$row['avg_scroll'] ?>%</td><td class="px-4 py-3 text-right"><?= (int)$row['clicks'] ?></td><td class="px-4 py-3 text-center"><a href="?days=<?= $days ?>&amp;page=<?= rawurlencode($row['page_path']) ?>#click-map" class="text-[#EE483D] hover:text-red-700" aria-label="View click map for <?= analytics_h($row['page_path']) ?>"><i class="bi bi-crosshair"></i></a></td></tr>
                <?php endforeach; ?>
                <?php if (!$topPages): ?><tr><td colspan="7" class="px-6 py-10 text-center text-gray-400">No page data yet.</td></tr><?php endif; ?>
            </tbody></table></div>
        </section>

        <section id="click-map" class="grid xl:grid-cols-[1.3fr_1fr] gap-6 scroll-mt-24">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex flex-wrap gap-4 items-center"><div><h2 class="font-extrabold text-[#1e1e5c]">Page click map</h2><p class="text-xs text-gray-400 mt-1">Approximate positions for one viewport group; not a screenshot overlay</p></div>
                    <form method="get" class="ml-auto flex flex-wrap items-center gap-2 max-w-full"><input type="hidden" name="days" value="<?= $days ?>"><select aria-label="Page for click map" name="page" onchange="this.form.submit()" class="w-full max-w-sm border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-600 bg-white"><option value="">Select a page</option><?php foreach ($pageList as $page): ?><option value="<?= analytics_h($page['page_path']) ?>" <?= $selectedPage === $page['page_path'] ? 'selected' : '' ?>><?= analytics_h($page['page_path']) ?></option><?php endforeach; ?></select>
<select aria-label="Viewport group for click map" name="map_device" onchange="this.form.submit()" class="border border-gray-200 rounded-xl px-3 py-2 text-sm"><?php foreach (['desktop'=>'Desktop >1024px','tablet'=>'Tablet 769–1024px','mobile'=>'Mobile ≤768px'] as $key=>$label): ?><option value="<?= $key ?>" <?= $key === $mapDevice ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?></select></form>
                </div>
                <div class="p-6"><div class="click-map relative border border-gray-200 rounded-2xl overflow-hidden"><span class="absolute top-3 left-4 text-[10px] uppercase tracking-widest text-gray-400 font-bold">Top</span><span class="absolute bottom-3 left-4 text-[10px] uppercase tracking-widest text-gray-400 font-bold">Bottom</span><div class="absolute inset-x-0 top-1/2 border-t border-dashed border-gray-200"></div>
                    <?php foreach ($heatmap as $dot): $size = 12 + round(((int)$dot['total'] / $heatMax) * 26); ?><span class="click-dot" title="<?= (int)$dot['total'] ?> clicks" style="left:<?= max(1, min(99, (float)$dot['x'])) ?>%;top:<?= max(1, min(99, (float)$dot['y'])) ?>%;width:<?= $size ?>px;height:<?= $size ?>px"></span><?php endforeach; ?>
                    <?php if (!$heatmap): ?><p class="absolute inset-0 flex items-center justify-center text-sm text-gray-400 px-8 text-center">Selected page par click data aate hi yahan heat map dikhega.</p><?php endif; ?>
                </div></div>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6"><div class="flex items-start justify-between gap-3"><div><h2 class="font-extrabold text-[#1e1e5c]">Most-used elements</h2><p class="text-xs text-gray-400 mt-1 break-all"><?= analytics_h($selectedPage ?: 'Choose a page') ?></p></div><?php if ($selectedPage): ?><a href="<?= analytics_h(cbd_site_base_url() . $selectedPage) ?>" target="_blank" rel="noopener" class="text-[#49499A]" aria-label="Open selected page"><i class="bi bi-box-arrow-up-right"></i></a><?php endif; ?></div>
                <div class="mt-5 space-y-3"><?php foreach ($pageClicks as $row): ?><div class="border border-gray-100 rounded-xl p-3"><div class="flex justify-between gap-3"><span class="text-sm font-semibold text-gray-700 line-clamp-2"><?= analytics_h($row['element_text'] ?: $row['event_name'] ?: 'Unnamed element') ?></span><strong class="text-[#EE483D]"><?= (int)$row['total'] ?></strong></div><p class="text-[11px] text-gray-400 mt-1 truncate"><?= analytics_h($row['target_path'] ?: $row['event_type']) ?></p></div><?php endforeach; ?><?php if (!$pageClicks): ?><p class="text-sm text-gray-400">No element clicks yet.</p><?php endif; ?></div>
            </div>
        </section>

        <section class="grid xl:grid-cols-3 gap-6">
            <?php foreach ([['Referrers', $referrers, 'label'], ['Campaigns', $campaigns, 'source'], ['Top clicks site-wide', $topClicks, 'element_text']] as $panel): ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6"><h2 class="font-extrabold text-[#1e1e5c] mb-4"><?= analytics_h($panel[0]) ?></h2><div class="space-y-3">
                <?php foreach (array_slice($panel[1], 0, 10) as $row): $count = (int)($row['total'] ?? 0); $label = $row[$panel[2]] ?? ''; if ($panel[0] === 'Campaigns' && ($row['campaign'] ?? '—') !== '—') $label .= ' · ' . $row['campaign']; ?>
                <div class="flex items-center justify-between gap-3 text-sm"><span class="text-gray-600 truncate" title="<?= analytics_h($label ?: 'Unnamed') ?>"><?= analytics_h($label ?: ($row['event_name'] ?? 'Unnamed')) ?></span><strong class="text-[#1e1e5c]"><?= $count ?></strong></div>
                <?php endforeach; ?><?php if (!$panel[1]): ?><p class="text-sm text-gray-400">No data yet.</p><?php endif; ?>
            </div></div>
            <?php endforeach; ?>
        </section>

        <section class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100"><h2 class="font-extrabold text-[#1e1e5c]">Recent visitor journeys</h2><p class="text-xs text-gray-400 mt-1">Anonymous session path—no names, email addresses or form values</p></div>
            <div class="overflow-x-auto"><table class="w-full text-sm"><thead class="bg-gray-50 text-gray-400 text-xs uppercase tracking-wider"><tr><th class="text-left px-5 py-3">Started</th><th class="text-left px-5 py-3">Journey</th><th class="text-right px-4 py-3">Pages</th><th class="text-right px-4 py-3">Time</th><th class="text-right px-4 py-3">Scroll</th><th class="px-5 py-3">Details</th></tr></thead><tbody class="divide-y divide-gray-100">
                <?php foreach ($journeys as $row): ?><tr class="hover:bg-gray-50"><td class="px-5 py-3 text-xs text-gray-400 whitespace-nowrap"><?= analytics_h(date('d M, h:i A', strtotime($row['started_at']))) ?></td><td class="px-5 py-3 text-gray-600 max-w-3xl"><span class="line-clamp-2"><?= analytics_h($row['journey']) ?></span></td><td class="px-4 py-3 text-right font-bold"><?= (int)$row['pages'] ?></td><td class="px-4 py-3 text-right whitespace-nowrap"><?= analytics_h(analytics_time($row['engaged_seconds'])) ?></td><td class="px-4 py-3 text-right"><?= (int)$row['max_scroll'] ?>%</td><td class="px-5 py-3 text-center"><a href="?days=<?= $days ?>&amp;page=<?= rawurlencode($selectedPage) ?>&amp;session=<?= analytics_h($row['session_id']) ?>#session-detail" class="inline-flex items-center gap-1 text-xs font-bold text-[#49499A] hover:text-[#EE483D]"><i class="bi bi-list-ul"></i> View</a></td></tr><?php endforeach; ?>
                <?php if (!$journeys): ?><tr><td colspan="6" class="px-6 py-10 text-center text-gray-400">Visitor journeys will appear after tracking begins.</td></tr><?php endif; ?>
            </tbody></table></div>
        </section>

        <?php if ($selectedSession !== ''): ?>
        <section id="session-detail" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden scroll-mt-24">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-4"><div><h2 class="font-extrabold text-[#1e1e5c]">Anonymous session detail</h2><p class="text-xs text-gray-400 mt-1">Session <?= analytics_h(substr($selectedSession, 0, 8)) ?>… · page time, scroll and recorded actions</p></div><a href="?days=<?= $days ?>&amp;page=<?= rawurlencode($selectedPage) ?>" class="text-sm font-bold text-gray-500 hover:text-[#EE483D]"><i class="bi bi-x-lg"></i> Close</a></div>
            <?php if (!$sessionPages): ?><p class="p-6 text-sm text-gray-400">This session is outside the selected period or no longer exists.</p><?php else: ?>
            <div class="grid xl:grid-cols-[1.2fr_1fr]">
                <div class="border-b xl:border-b-0 xl:border-r border-gray-100"><div class="px-5 py-3 bg-gray-50 text-xs font-extrabold uppercase tracking-wider text-gray-400">Pages visited</div><div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="text-xs text-gray-400 border-b border-gray-100"><th class="text-left px-5 py-3">Time</th><th class="text-left px-4 py-3">Page</th><th class="text-right px-4 py-3">Active</th><th class="text-right px-4 py-3">Scroll</th><th class="text-right px-5 py-3">Clicks</th></tr></thead><tbody class="divide-y divide-gray-100"><?php foreach ($sessionPages as $row): ?><tr><td class="px-5 py-3 text-xs text-gray-400 whitespace-nowrap"><?= analytics_h(date('h:i:s A', strtotime($row['started_at']))) ?></td><td class="px-4 py-3 font-semibold text-[#1e1e5c] max-w-sm truncate" title="<?= analytics_h($row['page_path']) ?>"><?= analytics_h($row['page_path']) ?></td><td class="px-4 py-3 text-right whitespace-nowrap"><?= analytics_h(analytics_time($row['engaged_seconds'])) ?></td><td class="px-4 py-3 text-right"><?= (int)$row['max_scroll'] ?>%</td><td class="px-5 py-3 text-right"><?= (int)$row['clicks'] ?></td></tr><?php endforeach; ?></tbody></table></div></div>
                <div><div class="px-5 py-3 bg-gray-50 text-xs font-extrabold uppercase tracking-wider text-gray-400">Action timeline</div><div class="p-5 space-y-4 max-h-[460px] overflow-y-auto"><?php foreach ($sessionEvents as $event): ?><div class="flex gap-3"><div class="w-8 h-8 rounded-lg bg-[#49499A]/10 text-[#49499A] flex items-center justify-center flex-shrink-0"><i class="bi <?= $event['event_type'] === 'scroll' ? 'bi-arrow-down' : ($event['event_type'] === 'conversion' ? 'bi-bullseye' : 'bi-cursor-fill') ?> text-sm"></i></div><div class="min-w-0"><p class="text-sm font-semibold text-gray-700"><?= analytics_h($event['element_text'] ?: $event['event_name'] ?: $event['event_type']) ?></p><p class="text-[11px] text-gray-400 truncate"><?= analytics_h(date('h:i:s A', strtotime($event['created_at']))) ?> · <?= analytics_h($event['page_path']) ?><?= $event['target_path'] ? ' · ' . analytics_h($event['target_path']) : '' ?></p></div></div><?php endforeach; ?><?php if (!$sessionEvents): ?><p class="text-sm text-gray-400">No click or scroll actions were recorded in this session.</p><?php endif; ?></div></div>
            </div>
            <?php endif; ?>
        </section>
        <?php endif; ?>

        <div class="bg-blue-50 border border-blue-200 text-blue-900 rounded-2xl p-4 text-sm"><i class="bi bi-info-circle-fill mr-2"></i>Engaged time is an estimate: visible, focused, and within 60 seconds of activity. Typed values are not tracked. Staff sessions and browser privacy signals are excluded. Successful email handoff does not prove inbox delivery. Tracking begins after installation; earlier totals may use the previous timing rules.</div>
        <?php endif; ?>
    </main>
</div>
<script src="assets/admin.js"></script>
</body>
</html>
