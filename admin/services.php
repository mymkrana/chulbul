<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo = get_db();

// Toggle status via AJAX
if ($pdo && $_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'toggle_status') {
    header('Content-Type: application/json');
    verify_csrf(true);
    $id  = (int)($_POST['id'] ?? 0);
    $cur = (int)($_POST['status'] ?? 1);
    $new = $cur ? 0 : 1;
    $pdo->prepare("UPDATE services SET status=? WHERE id=?")->execute([$new, $id]);
    echo json_encode(['success' => true, 'new_status' => $new]);
    exit;
}

$search   = trim($_GET['search'] ?? '');
$cat_filter = trim($_GET['cat'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$perPage  = 15;
$offset   = ($page - 1) * $perPage;
$rows     = [];
$total    = 0;

// Load parent categories for filter dropdown
$cat_parents = $pdo ? $pdo->query("SELECT slug, name FROM categories WHERE parent_id IS NULL AND status=1 ORDER BY sort_order, name")->fetchAll() : [];

if ($pdo) {
    $where  = ['s.deleted_at IS NULL'];
    $params = [];
    if ($search !== '') {
        $like = '%' . $search . '%';
        $where[]  = '(s.slug LIKE ? OR s.meta_title LIKE ?)';
        $params[] = $like; $params[] = $like;
    }
    if ($cat_filter !== '') {
        $where[]  = 's.category = ?';
        $params[] = $cat_filter;
    }
    $whereSQL = 'WHERE ' . implode(' AND ', $where);

    $cStmt = $pdo->prepare("SELECT COUNT(*) FROM services s $whereSQL");
    $cStmt->execute($params);
    $total = (int)$cStmt->fetchColumn();

    $stmt = $pdo->prepare("
        SELECT s.id, s.slug, s.category, s.subcategory, s.meta_title, s.status, s.updated_at,
               c.name AS subcategory_name
        FROM services s
        LEFT JOIN categories c
            ON CONVERT(c.slug USING utf8mb4) COLLATE utf8mb4_unicode_ci
             = CONVERT(s.subcategory USING utf8mb4) COLLATE utf8mb4_unicode_ci
            AND c.deleted_at IS NULL
        $whereSQL ORDER BY s.category ASC, s.slug ASC LIMIT ? OFFSET ?");
    foreach ($params as $i => $v) $stmt->bindValue($i + 1, $v);
    $stmt->bindValue(count($params) + 1, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows       = $stmt->fetchAll();
    $totalPages = (int)ceil($total / $perPage);
}

$flash       = get_flash();
$active_page = 'services';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Services — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:pl-60 flex flex-col min-h-screen">
    <!-- Topbar -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
        <div class="flex items-center gap-4">
            <button onclick="openSidebar()" class="lg:hidden text-gray-500 hover:text-gray-700">
                <i class="bi bi-list text-2xl"></i>
            </button>
            <h1 class="text-xl font-extrabold text-[#1e1e5c]">All Services</h1>
            <span class="bg-[#EE483D]/10 text-[#EE483D] text-xs font-bold px-2.5 py-1 rounded-full"><?= $total ?> total</span>
        </div>
        <a href="service-edit.php"
           class="flex items-center gap-2 bg-[#EE483D] hover:bg-red-600 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow">
            <i class="bi bi-plus-lg"></i> Add Service
        </a>
    </header>

    <main class="flex-1 p-6">
        <!-- Flash -->
        <?php if ($flash): ?>
        <div class="mb-4 flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-medium
            <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
            <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' ?>"></i>
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
        <?php endif; ?>

        <!-- Search + Filter -->
        <form method="GET" class="mb-5 flex flex-wrap gap-3">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search slug or title…"
                   class="flex-1 min-w-48">
            <select name="cat">
                <option value="">All Categories</option>
                <?php foreach ($cat_parents as $cp): ?>
                <option value="<?= htmlspecialchars($cp['slug']) ?>" <?= $cat_filter === $cp['slug'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cp['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="hidden">
                <i class="bi bi-search"></i> Filter
            </button>
            <?php if ($search || $cat_filter): ?>
            <a href="services.php" class="border border-gray-200 bg-white text-gray-600 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">Clear</a>
            <?php endif; ?>
        </form>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600">Slug / Title</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600 hidden md:table-cell">Category</th>
                        <th class="text-center px-5 py-3.5 font-semibold text-gray-600">Status</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-600 hidden lg:table-cell">Updated</th>
                        <th class="text-center px-5 py-3.5 font-semibold text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($rows)): ?>
                    <tr><td colspan="5" class="text-center py-16 text-gray-400">
                        <i class="bi bi-inbox text-4xl block mb-2"></i>
                        <?= $search ? 'No services found for "' . htmlspecialchars($search) . '"' : 'No services yet. <a href="service-edit.php" class="text-[#EE483D] font-semibold">Add one</a>' ?>
                    </td></tr>
                    <?php else: ?>
                    <?php foreach ($rows as $row): ?>
                    <tr class="hover:bg-gray-50 transition" id="row-<?= $row['id'] ?>">
                        <td class="px-5 py-4">
                            <p class="font-bold text-gray-900"><?= htmlspecialchars($row['subcategory_name'] ?? $row['slug']) ?></p>
                            <p class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($row['slug']) ?></p>
                        </td>
                        <td class="px-5 py-4 hidden md:table-cell">
                            <span class="bg-blue-50 text-blue-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                                <?= htmlspecialchars($row['category'] ?? '—') ?>
                            </span>
                            <?php if (!empty($row['subcategory'])): ?>
                            <span class="ml-1 bg-purple-50 text-purple-600 text-xs font-medium px-2 py-0.5 rounded-full">
                                <?= htmlspecialchars($row['subcategory']) ?>
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <button onclick="toggleStatus(<?= $row['id'] ?>, <?= (int)$row['status'] ?>)"
                                    id="status-btn-<?= $row['id'] ?>"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold transition
                                           <?= $row['status'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' ?>">
                                <span id="status-dot-<?= $row['id'] ?>" class="w-1.5 h-1.5 rounded-full <?= $row['status'] ? 'bg-green-500' : 'bg-gray-400' ?>"></span>
                                <span id="status-lbl-<?= $row['id'] ?>"><?= $row['status'] ? 'Active' : 'Inactive' ?></span>
                            </button>
                        </td>
                        <td class="px-5 py-4 text-gray-400 text-xs hidden lg:table-cell">
                            <?= date('d M Y', strtotime($row['updated_at'])) ?>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?= cbd_base_path() ?>/<?= htmlspecialchars($row['slug']) ?>" target="_blank"
                                   class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition" title="View">
                                    <i class="bi bi-box-arrow-up-right text-xs"></i>
                                </a>
                                <a href="service-edit.php?id=<?= $row['id'] ?>"
                                   class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-100 transition" title="Edit">
                                    <i class="bi bi-pencil text-xs"></i>
                                </a>
                                <form method="POST" action="service-delete.php" onsubmit="return confirm('Delete \'<?= htmlspecialchars($row['slug'], ENT_QUOTES) ?>\'?')">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition" title="Delete">
                                        <i class="bi bi-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if (($totalPages ?? 1) > 1): ?>
        <div class="flex items-center justify-center gap-2 mt-6">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="?page=<?= $i ?><?= $search ? '&search=' . urlencode($search) : '' ?><?= $cat_filter ? '&cat=' . urlencode($cat_filter) : '' ?>"
               class="w-9 h-9 rounded-xl flex items-center justify-center text-sm font-semibold transition
                      <?= $i === $page ? 'bg-[#EE483D] text-white' : 'bg-white text-gray-600 hover:bg-gray-100 border border-gray-200' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </main>
</div>

<script>
function toggleStatus(id, currentStatus) {
    fetch('services.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=toggle_status&id=' + id + '&status=' + currentStatus + '&csrf_token=' + encodeURIComponent('<?= csrf_token() ?>')
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) return;
        const isActive = data.new_status === 1;
        const btn = document.getElementById('status-btn-' + id);
        const dot = document.getElementById('status-dot-' + id);
        const lbl = document.getElementById('status-lbl-' + id);
        btn.className = btn.className.replace(/bg-\w+-\d+\s+text-\w+-\d+\s+hover:bg-\w+-\d+/g, '');
        if (isActive) {
            btn.classList.add('bg-green-100','text-green-700','hover:bg-green-200');
            dot.className = 'w-1.5 h-1.5 rounded-full bg-green-500';
            lbl.textContent = 'Active';
        } else {
            btn.classList.add('bg-gray-100','text-gray-500','hover:bg-gray-200');
            dot.className = 'w-1.5 h-1.5 rounded-full bg-gray-400';
            lbl.textContent = 'Inactive';
        }
    });
}
</script>
<script src="assets/admin.js"></script>
</body>
</html>
