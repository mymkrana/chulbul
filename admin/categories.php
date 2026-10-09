<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo   = get_db();
$flash = get_flash();
$active_page = 'categories';

$search        = trim($_GET['search'] ?? '');
$filter_status = $_GET['status'] ?? '';

$all = $pdo ? $pdo->query("
    SELECT * FROM categories WHERE deleted_at IS NULL ORDER BY ISNULL(parent_id) DESC, parent_id, sort_order, name
")->fetchAll() : [];

$parents  = array_values(array_filter($all, fn($c) => is_null($c['parent_id'])));
$children = [];
foreach ($all as $c) {
    if ($c['parent_id']) $children[$c['parent_id']][] = $c;
}

// Filter parents
$filtered = array_filter($parents, function($p) use ($search, $filter_status, $children) {
    if ($filter_status !== '' && (string)$p['status'] !== $filter_status) return false;
    if ($search !== '') {
        $nameMatch = stripos($p['name'], $search) !== false || stripos($p['slug'], $search) !== false;
        $childMatch = false;
        foreach ($children[$p['id']] ?? [] as $ch) {
            if (stripos($ch['name'], $search) !== false || stripos($ch['slug'], $search) !== false) {
                $childMatch = true; break;
            }
        }
        if (!$nameMatch && !$childMatch) return false;
    }
    return true;
});
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Categories — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
.sub-rows{display:none;}
.sub-rows.open{display:table-row-group;}

.parent-row{cursor:pointer;user-select:none;}
.parent-row:hover td{background:#f8f9fc;}
.parent-row td{background:#fff;}
.parent-row.expanded td{background:#fafbff;}

.chevron{transition:transform .22s ease;display:inline-block;}
.parent-row.expanded .chevron{transform:rotate(90deg);}

.sub-row td{background:#f8faff;}
.sub-row:hover td{background:#f0f4ff;}
.sub-row td:first-child{position:relative;}
.sub-row td:first-child::before{
    content:'';position:absolute;left:2.2rem;top:0;bottom:0;
    width:2px;background:#e5e7eb;
}

code{font-family:'SFMono-Regular',Consolas,monospace;font-size:.75rem;
     background:#f1f5f9;color:#475569;padding:.15rem .45rem;border-radius:.35rem;}
</style>
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:pl-60 flex flex-col min-h-screen">

    <!-- Topbar -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
        <div class="flex items-center gap-4">
            <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
            <h1 class="text-xl font-extrabold text-[#1e1e5c]">Categories</h1>
            <span class="bg-[#EE483D]/10 text-[#EE483D] text-xs font-bold px-2.5 py-1 rounded-full">
                <?= count($filtered) ?> categories
            </span>
        </div>
        <a href="category-edit.php"
           class="flex items-center gap-2 bg-[#EE483D] hover:bg-red-600 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow">
            <i class="bi bi-plus-lg"></i> Add Category
        </a>
    </header>

    <main class="flex-1 p-6">

        <!-- Flash -->
        <?php if ($flash): ?>
        <div class="mb-4 flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-medium
            <?= $flash['type']==='success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
            <i class="bi <?= $flash['type']==='success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' ?>"></i>
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
        <?php endif; ?>

        <!-- Filters -->
        <form method="GET" class="mb-5 flex flex-wrap gap-3">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Search name or slug…"
                   class="flex-1 min-w-48">

            <select name="status">
                <option value="">All Status</option>
                <option value="1" <?= $filter_status==='1'?'selected':'' ?>>Active</option>
                <option value="0" <?= $filter_status==='0'?'selected':'' ?>>Inactive</option>
            </select>

            <button type="submit" class="hidden">
                <i class="bi bi-search"></i> Filter
            </button>
            <?php if ($search || $filter_status !== ''): ?>
            <a href="categories.php" class="border border-gray-200 bg-white text-gray-600 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                Clear
            </a>
            <?php endif; ?>

            <button type="button" onclick="expandAll()"
                    class="ml-auto border border-gray-200 bg-white text-gray-600 px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">
                <i class="bi bi-arrows-expand mr-1"></i> Expand All
            </button>
        </form>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm" id="catTable">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500">Category</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500 hidden md:table-cell">Slug</th>
                        <th class="text-center px-4 py-3.5 font-semibold text-gray-500 hidden lg:table-cell">Subcategories</th>
                        <th class="text-center px-4 py-3.5 font-semibold text-gray-500">Status</th>
                        <th class="text-center px-4 py-3.5 font-semibold text-gray-500">Actions</th>
                    </tr>
                </thead>

                <?php if (empty($filtered)): ?>
                <tbody>
                <tr><td colspan="5" class="text-center py-16 text-gray-400">
                    <i class="bi bi-inbox text-4xl block mb-2"></i>
                    No categories found.
                </td></tr>
                </tbody>

                <?php else: ?>
                <?php foreach ($filtered as $pi => $p):
                    $kids     = $children[$p['id']] ?? [];
                    $kidCount = count($kids);
                    $color    = htmlspecialchars($p['color'] ?? '#3b82f6');
                    $groupId  = 'grp_' . $p['id'];
                ?>

                <!-- ── Parent row ── -->
                <tbody>
                <tr class="parent-row border-b border-gray-100" onclick="toggleGroup('<?= $groupId ?>', this)"
                    data-group="<?= $groupId ?>">

                    <td class="px-5 py-4">
                        <div class="flex items-center gap-3">
                            <i class="bi bi-chevron-right chevron text-gray-400 text-xs"></i>
                            <span class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                                  style="background:<?= $color ?>18">
                                <i class="bi <?= htmlspecialchars($_menu_icons[$p['slug']] ?? 'bi-grid') ?> text-sm" style="color:<?= $color ?>"></i>
                            </span>
                            <div>
                                <p class="font-extrabold text-[#1e1e5c]"><?= htmlspecialchars($p['name']) ?></p>
                                <p class="text-xs text-gray-400 md:hidden"><code><?= htmlspecialchars($p['slug']) ?></code></p>
                            </div>
                        </div>
                    </td>

                    <td class="px-5 py-4 hidden md:table-cell">
                        <code><?= htmlspecialchars($p['slug']) ?></code>
                    </td>

                    <td class="px-4 py-4 text-center hidden lg:table-cell">
                        <?php if ($kidCount): ?>
                        <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-600 text-xs font-bold px-2.5 py-1 rounded-full">
                            <i class="bi bi-diagram-3"></i> <?= $kidCount ?>
                        </span>
                        <?php else: ?>
                        <span class="text-gray-300 text-xs">—</span>
                        <?php endif; ?>
                    </td>

                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full
                            <?= $p['status'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $p['status'] ? 'bg-green-500' : 'bg-gray-400' ?>"></span>
                            <?= $p['status'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>

                    <td class="px-4 py-4" onclick="event.stopPropagation()">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="category-edit.php?parent_id=<?= $p['id'] ?>"
                               class="flex items-center gap-1 text-xs font-semibold text-blue-600 bg-blue-50 px-2.5 py-1.5 rounded-lg hover:bg-blue-100 transition"
                               title="Add subcategory">
                                <i class="bi bi-plus"></i> Sub
                            </a>
                            <a href="category-edit.php?id=<?= $p['id'] ?>"
                               class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-100 transition">
                                <i class="bi bi-pencil text-xs"></i>
                            </a>
                            <form method="POST" action="category-delete.php" onsubmit="return confirm('Delete this category?')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                </tbody>

                <!-- ── Sub rows (collapsed by default) ── -->
                <tbody class="sub-rows <?= $search ? 'open' : '' ?>" id="<?= $groupId ?>">
                <?php if ($kidCount): ?>
                <?php foreach ($kids as $child): ?>
                <tr class="sub-row border-b border-gray-50 last:border-b-0">

                    <td class="py-3 pl-16 pr-5">
                        <div class="flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0 bg-gray-300"></span>
                            <span class="font-semibold text-gray-700"><?= htmlspecialchars($child['name']) ?></span>
                        </div>
                    </td>

                    <td class="px-5 py-3 hidden md:table-cell">
                        <code><?= htmlspecialchars($child['slug']) ?></code>
                    </td>

                    <td class="px-4 py-3 hidden lg:table-cell"></td>

                    <td class="px-4 py-3 text-center">
                        <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full
                            <?= $child['status'] ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-400' ?>">
                            <span class="w-1.5 h-1.5 rounded-full <?= $child['status'] ? 'bg-green-500' : 'bg-gray-400' ?>"></span>
                            <?= $child['status'] ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="category-edit.php?id=<?= $child['id'] ?>"
                               class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-100 transition">
                                <i class="bi bi-pencil text-xs"></i>
                            </a>
                            <form method="POST" action="category-delete.php" onsubmit="return confirm('Delete this category?')">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                <input type="hidden" name="id" value="<?= $child['id'] ?>">
                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php else: ?>
                <tr class="sub-row">
                    <td colspan="5" class="pl-16 py-3 text-xs text-gray-400 italic">
                        No subcategories.
                        <a href="category-edit.php?parent_id=<?= $p['id'] ?>" class="text-[#EE483D]">Add one →</a>
                    </td>
                </tr>
                <?php endif; ?>
                </tbody>

                <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>

    </main>
</div>

<?php
// icon map needed for rendering
$_menu_icons = [
    'web-design'      => 'bi-globe2',
    'ecommerce'       => 'bi-cart4',
    'mobile-software' => 'bi-phone',
    'technologies'    => 'bi-code-square',
    'seo-marketing'   => 'bi-graph-up-arrow',
    'ui-branding'     => 'bi-vector-pen',
    'support-speed'   => 'bi-tools',
    'ai-automation'   => 'bi-robot',
];
?>

<script>
function toggleGroup(id, row) {
    const tbody = document.getElementById(id);
    const isOpen = tbody.classList.contains('open');
    tbody.classList.toggle('open', !isOpen);
    row.classList.toggle('expanded', !isOpen);
}

function expandAll() {
    document.querySelectorAll('.sub-rows').forEach(t => t.classList.add('open'));
    document.querySelectorAll('.parent-row').forEach(r => r.classList.add('expanded'));
}
</script>
<script src="assets/admin.js"></script>
</body>
</html>
