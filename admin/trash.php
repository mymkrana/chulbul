<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo = get_db();

// ── Actions ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';
    $type   = $_POST['type']   ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($id && in_array($type, ['service','post','category'])) {
        $table = match($type) {
            'service'  => 'services',
            'post'     => 'posts',
            'category' => 'categories',
        };

        if ($action === 'restore') {
            $pdo->prepare("UPDATE $table SET deleted_at = NULL WHERE id = ?")->execute([$id]);
            set_flash(ucfirst($type) . ' restored successfully.', 'success');
        } elseif ($action === 'delete_permanent') {
            $pdo->prepare("DELETE FROM $table WHERE id = ?")->execute([$id]);
            set_flash(ucfirst($type) . ' permanently deleted.', 'success');
        } elseif ($action === 'empty_trash') {
            $pdo->exec("DELETE FROM services  WHERE deleted_at IS NOT NULL");
            $pdo->exec("DELETE FROM posts     WHERE deleted_at IS NOT NULL");
            $pdo->exec("DELETE FROM categories WHERE deleted_at IS NOT NULL");
            set_flash('Trash emptied.', 'success');
        }
    }

    header('Location: trash.php');
    exit;
}

// ── Load trash items ───────────────────────────────────
$services = $posts = $categories = [];
if ($pdo) {
    $services   = $pdo->query("SELECT id, slug AS name, 'service' AS type, deleted_at FROM services WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC")->fetchAll();
    $posts      = $pdo->query("SELECT id, title AS name, 'post' AS type, deleted_at FROM posts WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC")->fetchAll();
    $categories = $pdo->query("SELECT id, name, 'category' AS type, deleted_at FROM categories WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC")->fetchAll();
}

$all   = array_merge($services, $posts, $categories);
usort($all, fn($a,$b) => strtotime($b['deleted_at']) - strtotime($a['deleted_at']));
$total = count($all);

$flash       = get_flash();
$active_page = 'trash';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Trash — Chulbul Admin</title>
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
            <i class="bi bi-trash3 text-xl text-gray-400"></i>
            <h1 class="text-xl font-extrabold text-[#1e1e5c]">Trash</h1>
            <?php if ($total): ?>
            <span class="bg-red-100 text-red-600 text-xs font-bold px-2.5 py-1 rounded-full"><?= $total ?> item<?= $total !== 1 ? 's' : '' ?></span>
            <?php endif; ?>
        </div>
        <?php if ($total): ?>
        <form method="POST" onsubmit="return confirm('Permanently delete ALL <?= $total ?> items? This cannot be undone.')">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="empty_trash">
            <input type="hidden" name="type"   value="service">
            <input type="hidden" name="id"     value="0">
            <button type="submit"
                    class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-bold px-4 py-2.5 rounded-xl transition shadow">
                <i class="bi bi-trash3-fill"></i> Empty Trash
            </button>
        </form>
        <?php endif; ?>
    </header>

    <main class="flex-1 p-6">

        <!-- Flash -->
        <?php if ($flash): ?>
        <div class="mb-5 flex items-center gap-3 px-5 py-3 rounded-xl text-sm font-medium
            <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' ?>">
            <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-circle-fill' ?>"></i>
            <?= $flash['msg'] ?>
        </div>
        <?php endif; ?>

        <?php if (empty($all)): ?>
        <!-- Empty state -->
        <div class="flex flex-col items-center justify-center py-28 text-center">
            <div class="w-20 h-20 rounded-2xl bg-gray-100 flex items-center justify-center mb-5">
                <i class="bi bi-trash3 text-4xl text-gray-300"></i>
            </div>
            <h2 class="text-lg font-bold text-gray-400 mb-1">Trash is empty</h2>
            <p class="text-sm text-gray-400">Deleted services, posts, and categories will appear here.</p>
        </div>

        <?php else: ?>

        <!-- Tab counts -->
        <div class="flex gap-2 mb-5 flex-wrap">
            <button onclick="filterTab('all')" id="tab-all"
                    class="tab-btn active px-4 py-2 rounded-xl text-sm font-bold transition">
                All <span class="ml-1 opacity-70"><?= $total ?></span>
            </button>
            <?php if ($services): ?>
            <button onclick="filterTab('service')" id="tab-service"
                    class="tab-btn px-4 py-2 rounded-xl text-sm font-bold transition">
                Services <span class="ml-1 opacity-70"><?= count($services) ?></span>
            </button>
            <?php endif; ?>
            <?php if ($posts): ?>
            <button onclick="filterTab('post')" id="tab-post"
                    class="tab-btn px-4 py-2 rounded-xl text-sm font-bold transition">
                Posts <span class="ml-1 opacity-70"><?= count($posts) ?></span>
            </button>
            <?php endif; ?>
            <?php if ($categories): ?>
            <button onclick="filterTab('category')" id="tab-category"
                    class="tab-btn px-4 py-2 rounded-xl text-sm font-bold transition">
                Categories <span class="ml-1 opacity-70"><?= count($categories) ?></span>
            </button>
            <?php endif; ?>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500">Name / Slug</th>
                        <th class="text-center px-4 py-3.5 font-semibold text-gray-500">Type</th>
                        <th class="text-left px-5 py-3.5 font-semibold text-gray-500 hidden md:table-cell">Deleted</th>
                        <th class="text-center px-5 py-3.5 font-semibold text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50" id="trash-body">
                <?php foreach ($all as $item): ?>
                <?php
                    $typeLabel = match($item['type']) {
                        'service'  => ['label' => 'Service',  'bg' => 'bg-blue-50',   'text' => 'text-blue-700',  'icon' => 'bi-gear'],
                        'post'     => ['label' => 'Post',     'bg' => 'bg-purple-50', 'text' => 'text-purple-700','icon' => 'bi-file-text'],
                        'category' => ['label' => 'Category', 'bg' => 'bg-amber-50',  'text' => 'text-amber-700', 'icon' => 'bi-tag'],
                    };
                    $restoreUrl = match($item['type']) {
                        'service'  => 'services.php',
                        'post'     => 'posts.php',
                        'category' => 'categories.php',
                    };
                ?>
                <tr class="hover:bg-gray-50 transition" data-type="<?= $item['type'] ?>">
                    <td class="px-5 py-4">
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($item['name']) ?></p>
                    </td>
                    <td class="px-4 py-4 text-center">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full <?= $typeLabel['bg'] ?> <?= $typeLabel['text'] ?>">
                            <i class="bi <?= $typeLabel['icon'] ?>"></i>
                            <?= $typeLabel['label'] ?>
                        </span>
                    </td>
                    <td class="px-5 py-4 text-gray-400 text-xs hidden md:table-cell">
                        <span title="<?= htmlspecialchars($item['deleted_at']) ?>">
                            <?= date('d M Y, h:i A', strtotime($item['deleted_at'])) ?>
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-2">
                            <!-- Restore -->
                            <form method="POST" class="inline">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="action" value="restore">
                                <input type="hidden" name="type"   value="<?= $item['type'] ?>">
                                <input type="hidden" name="id"     value="<?= $item['id'] ?>">
                                <button type="submit"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-700 text-xs font-bold hover:bg-green-100 transition"
                                        title="Restore">
                                    <i class="bi bi-arrow-counterclockwise"></i> Restore
                                </button>
                            </form>
                            <!-- Permanent delete -->
                            <form method="POST" class="inline"
                                  onsubmit="return confirm('Permanently delete \'<?= htmlspecialchars(addslashes($item['name'])) ?>\'? This cannot be undone.')">
                                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                <input type="hidden" name="action" value="delete_permanent">
                                <input type="hidden" name="type"   value="<?= $item['type'] ?>">
                                <input type="hidden" name="id"     value="<?= $item['id'] ?>">
                                <button type="submit"
                                        class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-600 hover:text-white transition"
                                        title="Delete permanently">
                                    <i class="bi bi-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <p class="text-xs text-gray-400 mt-4 text-center">
            Items in trash are not visible on the website. Restore to make them live again.
        </p>

        <?php endif; ?>
    </main>
</div>

<style>
.tab-btn { background: #fff; color: #6b7280; border: 1.5px solid #e5e7eb; }
.tab-btn.active { background: #1e1e5c; color: #fff; border-color: #1e1e5c; }
</style>

<script>
function filterTab(type) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('tab-' + type).classList.add('active');
    document.querySelectorAll('#trash-body tr').forEach(row => {
        row.style.display = (type === 'all' || row.dataset.type === type) ? '' : 'none';
    });
}
</script>
<script src="assets/admin.js"></script>
</body>
</html>
