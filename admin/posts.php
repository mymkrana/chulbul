<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo = get_db();

// Handle AJAX status toggle
if ($pdo !== null && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    header('Content-Type: application/json');
    verify_csrf(true);
    $id        = (int)($_POST['id'] ?? 0);
    $curStatus = ($_POST['status'] ?? '') === 'published' ? 'published' : 'draft';
    $newStatus = $curStatus === 'published' ? 'draft' : 'published';
    $pdo->prepare("UPDATE posts SET status = ? WHERE id = ?")->execute([$newStatus, $id]);
    echo json_encode(['success' => true, 'new_status' => $newStatus]);
    exit;
}

// Params
$search     = trim($_GET['search']  ?? '');
$tag_filter = (int)($_GET['tag_id'] ?? 0);
$page       = max(1, (int)($_GET['page'] ?? 1));
$perPage    = 10;
$offset     = ($page - 1) * $perPage;

$posts      = [];
$total      = 0;
$totalPages = 0;
$all_tags   = [];

if ($pdo !== null) {
    // All tags for filter dropdown
    try { $all_tags = $pdo->query("SELECT id, name FROM tags ORDER BY name")->fetchAll(); } catch (Exception $e) {}

    $where  = ["p.deleted_at IS NULL"];
    $params = [];

    if ($search !== '') {
        $where[]  = "(p.title LIKE ?)";
        $params[] = '%' . $search . '%';
    }
    if ($tag_filter > 0) {
        $where[]  = "EXISTS (SELECT 1 FROM post_tags pt2 WHERE pt2.post_id = p.id AND pt2.tag_id = ?)";
        $params[] = $tag_filter;
    }

    $whereSQL = 'WHERE ' . implode(' AND ', $where);

    $cStmt = $pdo->prepare("SELECT COUNT(*) FROM posts p $whereSQL");
    $cStmt->execute($params);
    $total = (int)$cStmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT p.id, p.slug, p.title, p.status, p.date, p.created_at, COALESCE(p.views,0) AS views
                           FROM posts p $whereSQL ORDER BY p.created_at DESC LIMIT ? OFFSET ?");
    foreach ($params as $i => $v) $stmt->bindValue($i + 1, $v);
    $stmt->bindValue(count($params) + 1, $perPage, PDO::PARAM_INT);
    $stmt->bindValue(count($params) + 2, $offset,  PDO::PARAM_INT);
    $stmt->execute();

    $posts      = $stmt->fetchAll();
    $totalPages = (int)ceil($total / $perPage);
}

// Build pagination URL helper
function pg_url(int $p, string $search, int $tag_id): string {
    $q = ['page' => $p];
    if ($search)  $q['search'] = $search;
    if ($tag_id)  $q['tag_id'] = $tag_id;
    return 'posts.php?' . http_build_query($q);
}

$flash       = get_flash();
$active_page = 'posts';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>All Posts — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-50 text-gray-800">

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:ml-60 min-h-screen flex flex-col">

    <!-- Top bar -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
        <div class="flex items-center gap-4">
            <button onclick="openSidebar()" class="lg:hidden text-gray-500 hover:text-gray-800">
                <i class="bi bi-list text-2xl"></i>
            </button>
            <div>
                <h1 class="text-xl font-extrabold text-[#1e1e5c]">All Posts</h1>
                <p class="text-xs text-gray-400"><?= $total ?> post<?= $total !== 1 ? 's' : '' ?> total</p>
            </div>
        </div>
        <a href="add-post.php"
           class="inline-flex items-center gap-2 bg-[#EE483D] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-red-600 transition shadow-md shadow-red-200">
            <i class="bi bi-plus-lg"></i> Add New Post
        </a>
    </header>

    <main class="flex-1 p-6 space-y-5">

        <?php if ($pdo === null): ?>
        <div class="flex items-start gap-4 bg-yellow-50 border border-yellow-300 rounded-2xl px-5 py-4">
            <i class="bi bi-exclamation-triangle-fill text-yellow-500 text-2xl flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="font-bold text-yellow-800">Database not connected</p>
                <p class="text-yellow-700 text-sm mt-2">Check the server database configuration.</p>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($flash): ?>
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border
            <?= $flash['type'] === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' ?>">
            <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill text-green-500' : 'bi-exclamation-circle-fill text-red-500' ?>"></i>
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
        <?php endif; ?>

        <!-- Search + Tag Filter -->
        <form method="GET" action="posts.php" class="flex flex-wrap gap-3">
            <!-- Search -->
            <div class="relative flex-1 min-w-[200px] max-w-sm">
                <i class="bi bi-search absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                       placeholder="Search by title…"
                       class="w-full pl-10">
            </div>

            <!-- Tag Filter -->
            <?php if ($all_tags): ?>
            <select name="tag_id" onchange="this.form.submit()"
                    class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-white text-gray-700 focus:outline-none focus:ring-2 focus:ring-[#49499A]/30 min-w-[160px]">
                <option value="">All Tags</option>
                <?php foreach ($all_tags as $t): ?>
                <option value="<?= $t['id'] ?>" <?= $tag_filter === (int)$t['id'] ? 'selected' : '' ?>>
                    # <?= htmlspecialchars($t['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php endif; ?>

            <button type="submit"
                    class="bg-[#49499A] text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-[#1e1e5c] transition flex items-center gap-1.5">
                <i class="bi bi-search"></i> Search
            </button>

            <?php if ($search || $tag_filter): ?>
            <a href="posts.php"
               class="bg-gray-100 text-gray-600 px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-200 transition flex items-center gap-1.5">
                <i class="bi bi-x-lg"></i> Clear
            </a>
            <?php endif; ?>
        </form>

        <!-- Active filter badge -->
        <?php if ($tag_filter && $all_tags): ?>
        <?php $active_tag_name = ''; foreach ($all_tags as $t) { if ((int)$t['id'] === $tag_filter) { $active_tag_name = $t['name']; break; } } ?>
        <?php if ($active_tag_name): ?>
        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-500">Tag filter:</span>
            <span class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1 rounded-full bg-[#49499A] text-white">
                <i class="bi bi-hash" style="font-size:9px"></i><?= htmlspecialchars($active_tag_name) ?>
            </span>
        </div>
        <?php endif; ?>
        <?php endif; ?>

        <!-- Posts table -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Title</th>
                            <th class="text-left px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-400 hidden md:table-cell">
                                <i class="bi bi-eye mr-1"></i>Views
                            </th>
                            <th class="text-left px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-400 hidden sm:table-cell">Status</th>
                            <th class="text-left px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-400 hidden lg:table-cell">Date</th>
                            <th class="text-right px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" id="posts-tbody">
                        <?php if (empty($posts)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <i class="bi bi-inbox text-4xl block mb-3 opacity-40"></i>
                                <?= $search ? 'No posts match your search.' : ($tag_filter ? 'Is tag mein koi post nahi.' : ($pdo ? 'No posts yet.' : 'Database not connected.')) ?>
                                <?php if (!$search && !$tag_filter && $pdo): ?>
                                <a href="add-post.php" class="text-[#49499A] font-semibold ml-1">Create your first post</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($posts as $p): ?>
                        <tr class="hover:bg-gray-50 transition" id="row-<?= $p['id'] ?>">

                            <!-- Title + slug -->
                            <td class="px-6 py-4">
                                <p class="font-semibold text-[#1e1e5c] truncate max-w-[280px]">
                                    <?= htmlspecialchars($p['title']) ?>
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">/blog/<?= htmlspecialchars($p['slug']) ?></p>
                            </td>

                            <!-- Views -->
                            <td class="px-6 py-4 hidden md:table-cell">
                                <?php $views = (int)$p['views']; ?>
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold
                                    <?= $views >= 100 ? 'text-green-600' : ($views >= 10 ? 'text-[#49499A]' : 'text-gray-400') ?>">
                                    <i class="bi bi-eye"></i>
                                    <?= $views >= 1000 ? number_format($views/1000, 1) . 'k' : $views ?>
                                </span>
                            </td>

                            <!-- Status toggle -->
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <button
                                    onclick="toggleStatus(<?= $p['id'] ?>, '<?= $p['status'] ?>')"
                                    id="status-btn-<?= $p['id'] ?>"
                                    title="Click to toggle"
                                    class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1.5 rounded-full transition cursor-pointer border-0
                                        <?= $p['status'] === 'published' ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' ?>">
                                    <span class="w-1.5 h-1.5 rounded-full <?= $p['status'] === 'published' ? 'bg-green-500' : 'bg-yellow-500' ?>"
                                          id="status-dot-<?= $p['id'] ?>"></span>
                                    <span id="status-text-<?= $p['id'] ?>"><?= ucfirst($p['status']) ?></span>
                                </button>
                            </td>

                            <!-- Date -->
                            <td class="px-6 py-4 text-gray-400 text-xs hidden lg:table-cell">
                                <?= $p['date'] ? date('d M Y', strtotime($p['date'])) : '—' ?>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="edit-post.php?id=<?= $p['id'] ?>"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-[#49499A] hover:text-[#1e1e5c] bg-[#49499A]/10 hover:bg-[#49499A]/20 px-3 py-1.5 rounded-lg transition">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form method="POST" action="delete-post.php" onsubmit="return confirm('Delete this post?')">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                                            <i class="bi bi-trash3"></i>
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

            <?php if ($totalPages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
                <p class="text-sm text-gray-400">
                    Showing <?= min($offset + 1, $total) ?>–<?= min($offset + $perPage, $total) ?> of <?= $total ?>
                </p>
                <div class="flex items-center gap-1">
                    <?php if ($page > 1): ?>
                    <a href="<?= pg_url($page - 1, $search, $tag_filter) ?>"
                       class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-[#49499A] transition text-sm">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <?php endif; ?>
                    <?php for ($i = max(1,$page-2); $i <= min($totalPages,$page+2); $i++): ?>
                    <a href="<?= pg_url($i, $search, $tag_filter) ?>"
                       class="w-9 h-9 flex items-center justify-center rounded-lg text-sm font-semibold transition
                              <?= $i === $page ? 'bg-[#49499A] text-white' : 'border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-[#49499A]' ?>">
                        <?= $i ?>
                    </a>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                    <a href="<?= pg_url($page + 1, $search, $tag_filter) ?>"
                       class="w-9 h-9 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 hover:text-[#49499A] transition text-sm">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </main>
</div>

<script>
function toggleStatus(id, currentStatus) {
    const btn  = document.getElementById('status-btn-' + id);
    const dot  = document.getElementById('status-dot-' + id);
    const text = document.getElementById('status-text-' + id);
    btn.disabled = true; btn.style.opacity = '0.6';
    const fd = new FormData();
    fd.append('action','toggle_status'); fd.append('id',id); fd.append('status',currentStatus); fd.append('csrf_token','<?= csrf_token() ?>');
    fetch('posts.php',{method:'POST',body:fd})
        .then(r=>r.json())
        .then(data=>{
            if(data.success){
                const s=data.new_status;
                text.textContent=s.charAt(0).toUpperCase()+s.slice(1);
                btn.setAttribute('onclick',`toggleStatus(${id},'${s}')`);
                if(s==='published'){btn.className='inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1.5 rounded-full transition cursor-pointer border-0 bg-green-100 text-green-700 hover:bg-green-200';dot.className='w-1.5 h-1.5 rounded-full bg-green-500';}
                else{btn.className='inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1.5 rounded-full transition cursor-pointer border-0 bg-yellow-100 text-yellow-700 hover:bg-yellow-200';dot.className='w-1.5 h-1.5 rounded-full bg-yellow-500';}
            }
        })
        .catch(()=>alert('Failed.'))
        .finally(()=>{btn.disabled=false;btn.style.opacity='1';});
}
</script>
<script src="assets/admin.js"></script>
</body>
</html>
