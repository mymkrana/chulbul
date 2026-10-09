<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo  = get_db();
$days = (int)($_GET['days'] ?? 0);                 // 0 = all time
$ev_period   = $days > 0 ? "AND created_at >= (NOW() - INTERVAL {$days} DAY)" : "";
$lead_period = $days > 0 ? "WHERE created_at >= (NOW() - INTERVAL {$days} DAY)" : "";

function q1($pdo, $sql) { try { return $pdo ? $pdo->query($sql)->fetchColumn() : 0; } catch (Exception $e) { return 0; } }
function qa($pdo, $sql) { try { return $pdo ? $pdo->query($sql)->fetchAll() : []; } catch (Exception $e) { return []; } }

$visitors  = (int) q1($pdo, "SELECT COUNT(DISTINCT visitor_id) FROM lp_events WHERE event_type='pageview' AND visitor_id<>'' $ev_period");
$pageviews = (int) q1($pdo, "SELECT COUNT(*) FROM lp_events WHERE event_type='pageview' $ev_period");
$clicks    = (int) q1($pdo, "SELECT COUNT(*) FROM lp_events WHERE event_type='click' $ev_period");
$avgtime   = (int) q1($pdo, "SELECT ROUND(AVG(secs)) FROM lp_events WHERE event_type='time' AND secs>0 $ev_period");
$leadCount = (int) q1($pdo, "SELECT COUNT(*) FROM leads $lead_period");
$clickRows = qa($pdo, "SELECT label, COUNT(*) c FROM lp_events WHERE event_type='click' $ev_period GROUP BY label ORDER BY c DESC");
$leads     = qa($pdo, "SELECT * FROM leads $lead_period ORDER BY created_at DESC LIMIT 300");

$conv = $visitors > 0 ? round($leadCount / $visitors * 100, 1) : 0;
$mm = floor($avgtime / 60); $ss = $avgtime % 60;
$avgfmt = $avgtime > 0 ? ($mm > 0 ? "{$mm}m {$ss}s" : "{$ss}s") : '—';

$LABELS = [
  'wa_top'=>'WhatsApp · Header','wa_hero'=>'WhatsApp · Hero','wa_price'=>'WhatsApp · Price box',
  'wa_form'=>'WhatsApp · Form area','wa_sticky'=>'WhatsApp · Sticky (mobile)',
  'call_top'=>'Call · Header','call_sticky'=>'Call · Sticky (mobile)','call_footer'=>'Call · Footer',
  'form_submit'=>'✅ Form Submitted',
];
$maxClick = 0; foreach ($clickRows as $r) $maxClick = max($maxClick, (int)$r['c']);

function wa_link($phone) {
  $d = preg_replace('/\D/', '', $phone);
  if (strlen($d) === 10) $d = '91' . $d;
  return 'https://wa.me/' . $d;
}
$active_page = 'landing-analytics';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Landing Page Analytics — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:pl-60 flex flex-col min-h-screen">
  <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
    <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
    <i class="bi bi-graph-up-arrow text-xl text-[#EE483D]"></i>
    <h1 class="text-xl font-extrabold text-[#1e1e5c]">Landing Page Analytics</h1>
    <a href="<?= cbd_base_path() ?>/business-starter" target="_blank" class="ml-auto text-sm text-indigo-500 hover:underline flex items-center gap-1"><i class="bi bi-box-arrow-up-right"></i> View Page</a>
  </header>

  <main class="flex-1 p-6 max-w-6xl mx-auto w-full">

    <!-- Period filter -->
    <div class="flex flex-wrap items-center gap-2 mb-6">
      <span class="text-sm font-semibold text-gray-500 mr-1">Period:</span>
      <?php foreach (['0'=>'All time','1'=>'Today','7'=>'7 days','30'=>'30 days'] as $d=>$lbl): ?>
      <a href="?days=<?= $d ?>" class="px-3 py-1.5 rounded-lg text-sm font-semibold transition <?= (string)$days===$d ? 'bg-[#EE483D] text-white' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' ?>"><?= $lbl ?></a>
      <?php endforeach; ?>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
      <?php
      $cards = [
        ['Real Visitors', $visitors, 'bi-people-fill', '#EE483D', 'unique log'],
        ['Page Views', $pageviews, 'bi-eye-fill', '#49499A', 'total opens'],
        ['Button Clicks', $clicks, 'bi-hand-index-thumb-fill', '#16a34a', 'WhatsApp + Call'],
        ['Avg. Time on Page', $avgfmt, 'bi-clock-fill', '#0ea5e9', 'visitor ruka'],
        ['Leads', $leadCount, 'bi-person-lines-fill', '#1e1e5c', 'form bhare'],
        ['Conversion', $conv.'%', 'bi-graph-up-arrow', '#d97706', 'visitor → lead'],
      ];
      foreach ($cards as $c): ?>
      <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-2">
          <span class="text-xs font-bold uppercase tracking-wider text-gray-400"><?= $c[0] ?></span>
          <i class="bi <?= $c[2] ?>" style="color:<?= $c[3] ?>"></i>
        </div>
        <div class="text-3xl font-extrabold text-[#1e1e5c]"><?= $c[1] ?></div>
        <p class="text-xs text-gray-400 mt-1"><?= $c[4] ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Click breakdown -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-8">
      <h2 class="text-base font-extrabold text-[#1e1e5c] mb-4"><i class="bi bi-bar-chart-fill text-[#EE483D]"></i> Konsa Button Kitna Click Hua</h2>
      <?php if (!$clickRows): ?>
        <p class="text-sm text-gray-400">Abhi koi click data nahi. (Page pe visitors aate hi yahan dikhega.)</p>
      <?php else: foreach ($clickRows as $r):
        $lbl = $LABELS[$r['label']] ?? ($r['label'] ?: 'Unknown');
        $pct = $maxClick > 0 ? round($r['c']/$maxClick*100) : 0; ?>
      <div class="mb-3">
        <div class="flex justify-between text-sm mb-1"><span class="text-gray-700 font-medium"><?= htmlspecialchars($lbl) ?></span><span class="font-bold text-[#1e1e5c]"><?= (int)$r['c'] ?></span></div>
        <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full rounded-full" style="width:<?= $pct ?>%;background:linear-gradient(90deg,#EE483D,#49499A)"></div></div>
      </div>
      <?php endforeach; endif; ?>
    </div>

    <!-- Leads table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-base font-extrabold text-[#1e1e5c]"><i class="bi bi-person-lines-fill text-[#49499A]"></i> Leads (<?= $leadCount ?>)</h2>
        <span class="text-xs text-gray-400">Newest first · max 300</span>
      </div>
      <?php if (!$leads): ?>
        <p class="text-sm text-gray-400 p-6">Abhi koi lead nahi aayi.</p>
      <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider">
            <tr><th class="text-left px-5 py-3">Name</th><th class="text-left px-5 py-3">Phone</th><th class="text-left px-5 py-3">Business</th><th class="text-left px-5 py-3">Source</th><th class="text-left px-5 py-3">Date</th><th class="px-5 py-3">Action</th></tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <?php foreach ($leads as $l): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-5 py-3 font-semibold text-[#1e1e5c]"><?= htmlspecialchars($l['name']) ?></td>
              <td class="px-5 py-3"><a href="tel:<?= htmlspecialchars($l['phone']) ?>" class="text-gray-700 hover:text-[#EE483D]"><?= htmlspecialchars($l['phone']) ?></a></td>
              <td class="px-5 py-3 text-gray-600"><?= htmlspecialchars($l['business'] ?: '—') ?></td>
              <td class="px-5 py-3"><span class="text-xs bg-[#f0f0ff] text-[#49499A] px-2 py-0.5 rounded-full"><?= htmlspecialchars($l['source'] ?: '—') ?></span></td>
              <td class="px-5 py-3 text-gray-400 text-xs whitespace-nowrap"><?= date('d M, h:i A', strtotime($l['created_at'])) ?></td>
              <td class="px-5 py-3 text-center"><a href="<?= wa_link($l['phone']) ?>" target="_blank" class="inline-flex items-center gap-1 bg-green-500 hover:bg-green-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg"><i class="bi bi-whatsapp"></i> WhatsApp</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

    <p class="text-xs text-gray-400 mt-5"><i class="bi bi-info-circle"></i> "Real Visitors" = JS-tracked unique browsers (bots filtered). Meta Pixel se alag — ye aapka apna first-party data hai.</p>
  </main>
</div>
<script src="assets/admin.js"></script>
</body>
</html>
