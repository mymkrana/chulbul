<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo = get_db();

$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 50;
$offset = ($page - 1) * $limit;
$filter_action = trim($_GET['action'] ?? '');
$filter_entity = trim($_GET['entity'] ?? '');

$rows = $actions_list = $entities_list = [];
$total = 0;
$table_exists = false;

if ($pdo) {
    try {
        // Check table exists
        $pdo->query("SELECT 1 FROM admin_audit_log LIMIT 1");
        $table_exists = true;

        $conditions = [];
        $params     = [];
        if ($filter_action) { $conditions[] = 'action = ?'; $params[] = $filter_action; }
        if ($filter_entity) { $conditions[] = 'entity = ?'; $params[] = $filter_entity; }
        $where = $conditions ? implode(' AND ', $conditions) : '1=1';

        $cs = $pdo->prepare("SELECT COUNT(*) FROM admin_audit_log WHERE $where");
        $cs->execute($params);
        $total = (int)$cs->fetchColumn();

        $rs = $pdo->prepare("SELECT * FROM admin_audit_log WHERE $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset");
        $rs->execute($params);
        $rows = $rs->fetchAll();

        $actions_list  = $pdo->query("SELECT DISTINCT action FROM admin_audit_log ORDER BY action")->fetchAll(PDO::FETCH_COLUMN);
        $entities_list = $pdo->query("SELECT DISTINCT entity FROM admin_audit_log WHERE entity IS NOT NULL ORDER BY entity")->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        $table_exists = false;
    }
}

$pages = max(1, (int)ceil($total / $limit));

$ACTION_META = [
    'login_success'    => ['bg-green-100  text-green-800',  'bi-check-circle-fill',   'Login'],
    'login_failed'     => ['bg-red-100    text-red-800',    'bi-x-circle-fill',       'Login Failed'],
    'password_changed' => ['bg-yellow-100 text-yellow-800', 'bi-key-fill',            'Password Changed'],
    'post_created'     => ['bg-blue-100   text-blue-800',   'bi-plus-circle-fill',    'Post Created'],
    'post_updated'     => ['bg-indigo-100 text-indigo-800', 'bi-pencil-fill',         'Post Updated'],
    'post_deleted'     => ['bg-red-100    text-red-800',    'bi-trash-fill',          'Post Deleted'],
    'service_created'  => ['bg-blue-100   text-blue-800',   'bi-plus-circle-fill',    'Service Created'],
    'service_updated'  => ['bg-indigo-100 text-indigo-800', 'bi-pencil-fill',         'Service Updated'],
    'service_deleted'  => ['bg-red-100    text-red-800',    'bi-trash-fill',          'Service Deleted'],
    'tag_created'      => ['bg-teal-100   text-teal-800',   'bi-tags-fill',           'Tag Created'],
    'tag_deleted'      => ['bg-orange-100 text-orange-800', 'bi-tags-fill',           'Tag Deleted'],
    'logout'           => ['bg-gray-100   text-gray-700',   'bi-box-arrow-right',     'Logout'],
];

function action_badge(string $action, array $meta): string {
    [$cls, $icon, $label] = $meta[$action] ?? ['bg-gray-100 text-gray-600', 'bi-dot', $action];
    return "<span class=\"inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-full $cls\">
        <i class=\"bi $icon\"></i> $label</span>";
}

$active_page = 'audit-log';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Audit Log — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:pl-60 flex flex-col min-h-screen">
  <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
    <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
    <i class="bi bi-shield-check text-xl text-[#EE483D]"></i>
    <h1 class="text-xl font-extrabold text-[#1e1e5c]">Admin Audit Log</h1>
    <span class="ml-auto text-xs text-gray-400">Last 50 entries per page</span>
  </header>

  <main class="flex-1 p-6 max-w-7xl mx-auto w-full">

    <?php if (!$table_exists): ?>
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 text-center">
        <i class="bi bi-info-circle text-3xl text-blue-400 mb-2 block"></i>
        <p class="text-blue-700 font-semibold">Abhi koi audit events record nahi hue.</p>
        <p class="text-blue-500 text-sm mt-1">Pehla login/action hone ke baad yahan data dikhega.</p>
    </div>
    <?php else: ?>

    <!-- Filters -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6 flex flex-wrap items-center gap-3">
      <form method="GET" action="" class="flex flex-wrap gap-3 items-center w-full">
        <select name="action" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#EE483D]">
          <option value="">All Actions</option>
          <?php foreach ($actions_list as $a): ?>
          <option value="<?= htmlspecialchars($a) ?>" <?= $filter_action === $a ? 'selected' : '' ?>>
              <?= htmlspecialchars($ACTION_META[$a][2] ?? $a) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <select name="entity" class="border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#EE483D]">
          <option value="">All Entities</option>
          <?php foreach ($entities_list as $e): ?>
          <option value="<?= htmlspecialchars($e) ?>" <?= $filter_entity === $e ? 'selected' : '' ?>>
              <?= htmlspecialchars($e) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="bg-[#EE483D] text-white text-sm font-semibold px-4 py-2 rounded-xl hover:bg-red-600 transition">
            <i class="bi bi-funnel"></i> Filter
        </button>
        <?php if ($filter_action || $filter_entity): ?>
        <a href="audit-log.php" class="text-sm text-gray-400 hover:text-gray-600">
            <i class="bi bi-x-circle"></i> Clear
        </a>
        <?php endif; ?>
        <span class="ml-auto text-sm text-gray-500 font-medium"><?= number_format($total) ?> events</span>
      </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
      <?php if (!$rows): ?>
        <p class="text-sm text-gray-400 p-6 text-center">Is filter ke liye koi events nahi hain.</p>
      <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
            <tr>
              <th class="text-left px-5 py-3">Time</th>
              <th class="text-left px-5 py-3">Admin</th>
              <th class="text-left px-5 py-3">Action</th>
              <th class="text-left px-5 py-3">Entity</th>
              <th class="text-left px-5 py-3">Detail</th>
              <th class="text-left px-5 py-3">IP</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <?php foreach ($rows as $r): ?>
            <tr class="hover:bg-gray-50">
              <td class="px-5 py-3 text-gray-400 text-xs whitespace-nowrap">
                <?= date('d M y, h:i:s A', strtotime($r['created_at'])) ?>
              </td>
              <td class="px-5 py-3 font-semibold text-[#1e1e5c]">
                <?= htmlspecialchars($r['admin_user'] ?? '—') ?>
              </td>
              <td class="px-5 py-3">
                <?= action_badge($r['action'], $ACTION_META) ?>
              </td>
              <td class="px-5 py-3 text-gray-600 text-xs">
                <?php if ($r['entity']): ?>
                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">
                    <?= htmlspecialchars($r['entity']) ?>
                    <?= $r['entity_id'] ? ' #' . (int)$r['entity_id'] : '' ?>
                </span>
                <?php else: ?>—<?php endif; ?>
              </td>
              <td class="px-5 py-3 text-gray-500 max-w-xs truncate" title="<?= htmlspecialchars($r['detail'] ?? '') ?>">
                <?= htmlspecialchars($r['detail'] ?: '—') ?>
              </td>
              <td class="px-5 py-3 text-gray-400 text-xs font-mono">
                <?= htmlspecialchars($r['ip'] ?? '—') ?>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($pages > 1): ?>
      <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
        <span class="text-sm text-gray-400">Page <?= $page ?> of <?= $pages ?></span>
        <div class="flex gap-2">
          <?php if ($page > 1): ?>
          <a href="?page=<?= $page-1 ?>&action=<?= urlencode($filter_action) ?>&entity=<?= urlencode($filter_entity) ?>"
             class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm hover:bg-gray-50 transition">
            <i class="bi bi-chevron-left"></i> Prev
          </a>
          <?php endif; ?>
          <?php if ($page < $pages): ?>
          <a href="?page=<?= $page+1 ?>&action=<?= urlencode($filter_action) ?>&entity=<?= urlencode($filter_entity) ?>"
             class="px-3 py-1.5 border border-gray-200 rounded-lg text-sm hover:bg-gray-50 transition">
            Next <i class="bi bi-chevron-right"></i>
          </a>
          <?php endif; ?>
        </div>
      </div>
      <?php endif; ?>
      <?php endif; ?>
    </div>

    <?php endif; ?>

    <p class="text-xs text-gray-400 mt-4">
        <i class="bi bi-info-circle"></i>
        Audit log me sirf admin actions track hote hain — login, password change, post/service create/delete.
        Log 90 din baad manually clear karna hoga.
    </p>
  </main>
</div>
<script src="assets/admin.js"></script>
</body>
</html>
