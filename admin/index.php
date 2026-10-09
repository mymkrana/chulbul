<?php
require_once __DIR__ . '/config.php';
require_login();

$db_ok = false;
$total = $published = $drafts = $cats = 0;
$recent = [];

$pdo = get_db();
if ($pdo !== null) {
    $db_ok     = true;
    $total     = (int)$pdo->query("SELECT COUNT(*) FROM posts")->fetchColumn();
    $published = (int)$pdo->query("SELECT COUNT(*) FROM posts WHERE status='published'")->fetchColumn();
    $drafts    = (int)$pdo->query("SELECT COUNT(*) FROM posts WHERE status='draft'")->fetchColumn();
    $cats      = (int)$pdo->query("SELECT COUNT(DISTINCT category) FROM posts WHERE category IS NOT NULL AND category != ''")->fetchColumn();
    $recent    = $pdo->query("SELECT id, title, category, status, date FROM posts ORDER BY created_at DESC LIMIT 5")->fetchAll();
}

$flash       = get_flash();
$active_page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  .stat-card { transition: transform 0.2s, box-shadow 0.2s; }
  .stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.1); }
</style>
</head>
<body class="bg-gray-50 text-gray-800">

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<!-- Main content -->
<div class="lg:ml-60 min-h-screen flex flex-col">

    <!-- Top bar -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-10 shadow-sm">
        <div class="flex items-center gap-4">
            <button onclick="openSidebar()" class="lg:hidden text-gray-500 hover:text-gray-800">
                <i class="bi bi-list text-2xl"></i>
            </button>
            <div>
                <h1 class="text-xl font-extrabold text-[#1e1e5c]">Dashboard</h1>
                <p class="text-xs text-gray-400">Welcome back, <?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?></p>
            </div>
        </div>
        <a href="add-post.php"
           class="inline-flex items-center gap-2 bg-[#EE483D] text-white px-4 py-2 rounded-xl text-sm font-bold hover:bg-red-600 transition shadow-md shadow-red-200">
            <i class="bi bi-plus-lg"></i> New Post
        </a>
    </header>

    <!-- Page content -->
    <main class="flex-1 p-6 space-y-6">

        <!-- DB not connected banner -->
        <?php if (!$db_ok): ?>
        <div class="flex items-start gap-4 bg-yellow-50 border border-yellow-300 rounded-2xl px-5 py-4">
            <i class="bi bi-exclamation-triangle-fill text-yellow-500 text-2xl flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="font-bold text-yellow-800">Database not connected</p>
                <p class="text-yellow-700 text-sm mt-1">The database does not exist or MySQL is not running.</p>
                <a href="setup.php"
                   class="inline-flex items-center gap-1.5 mt-3 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-bold px-4 py-2 rounded-xl transition">
                    <i class="bi bi-gear-fill"></i> Run setup.php to fix this
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Flash message -->
        <?php if ($flash): ?>
        <div class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium border
            <?= $flash['type'] === 'success'
                ? 'bg-green-50 border-green-200 text-green-800'
                : 'bg-red-50 border-red-200 text-red-800' ?>">
            <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill text-green-500' : 'bi-exclamation-circle-fill text-red-500' ?>"></i>
            <?= htmlspecialchars($flash['msg']) ?>
        </div>
        <?php endif; ?>

        <!-- Stats cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Posts</span>
                    <div class="w-10 h-10 bg-[#49499A]/10 rounded-xl flex items-center justify-center">
                        <i class="bi bi-file-earmark-text text-[#49499A] text-lg"></i>
                    </div>
                </div>
                <p class="text-4xl font-extrabold text-[#1e1e5c]"><?= $total ?></p>
            </div>

            <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Published</span>
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                        <i class="bi bi-check-circle text-green-600 text-lg"></i>
                    </div>
                </div>
                <p class="text-4xl font-extrabold text-green-600"><?= $published ?></p>
            </div>

            <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Drafts</span>
                    <div class="w-10 h-10 bg-yellow-100 rounded-xl flex items-center justify-center">
                        <i class="bi bi-pencil text-yellow-600 text-lg"></i>
                    </div>
                </div>
                <p class="text-4xl font-extrabold text-yellow-600"><?= $drafts ?></p>
            </div>

            <div class="stat-card bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Categories</span>
                    <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i class="bi bi-tag text-purple-600 text-lg"></i>
                    </div>
                </div>
                <p class="text-4xl font-extrabold text-purple-600"><?= $cats ?></p>
            </div>

        </div>

        <!-- Recent posts -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-base font-extrabold text-[#1e1e5c] flex items-center gap-2">
                    <i class="bi bi-clock-history text-[#EE483D]"></i> Recent Posts
                </h2>
                <a href="posts.php" class="text-sm text-[#49499A] hover:text-[#EE483D] font-semibold transition">
                    View all <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100">
                            <th class="text-left px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Title</th>
                            <th class="text-left px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-400 hidden md:table-cell">Category</th>
                            <th class="text-left px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-400 hidden sm:table-cell">Status</th>
                            <th class="text-left px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-400 hidden lg:table-cell">Date</th>
                            <th class="text-right px-6 py-3 text-xs font-bold uppercase tracking-wider text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php if (empty($recent)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-400 text-sm">
                                <?php if (!$db_ok): ?>
                                    <i class="bi bi-database-x text-4xl block mb-3 opacity-30"></i>
                                    Database not connected.
                                <?php else: ?>
                                    <i class="bi bi-inbox text-4xl block mb-3 opacity-30"></i>
                                    No posts yet. <a href="add-post.php" class="text-[#49499A] font-semibold">Create your first post</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php else: ?>
                        <?php foreach ($recent as $p): ?>
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-[#1e1e5c] truncate max-w-[280px]">
                                    <?= htmlspecialchars($p['title']) ?>
                                </p>
                            </td>
                            <td class="px-6 py-4 hidden md:table-cell">
                                <span class="text-xs font-medium text-gray-500 bg-gray-100 px-2.5 py-1 rounded-full">
                                    <?= htmlspecialchars($p['category'] ?? '—') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 hidden sm:table-cell">
                                <?php if ($p['status'] === 'published'): ?>
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full bg-green-100 text-green-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Published
                                </span>
                                <?php else: ?>
                                <span class="inline-flex items-center gap-1 text-xs font-bold px-2.5 py-1 rounded-full bg-yellow-100 text-yellow-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Draft
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-gray-400 text-xs hidden lg:table-cell">
                                <?= $p['date'] ? date('d M Y', strtotime($p['date'])) : '—' ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="edit-post.php?id=<?= $p['id'] ?>"
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-[#49499A] hover:text-[#1e1e5c] bg-[#49499A]/10 hover:bg-[#49499A]/20 px-3 py-1.5 rounded-lg transition">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form method="POST" action="delete-post.php" onsubmit="return confirm('Delete this post?')">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
                                        <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="inline-flex items-center gap-1 text-xs font-semibold text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition">
                                            <i class="bi bi-trash3"></i> Delete
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
        </div>

        <!-- Quick actions -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="add-post.php" class="bg-[#49499A] hover:bg-[#1e1e5c] text-white rounded-2xl p-5 flex items-center gap-4 transition shadow-lg shadow-[#49499A]/20">
                <i class="bi bi-plus-circle-fill text-3xl opacity-80"></i>
                <div>
                    <p class="font-bold">Write Post</p>
                    <p class="text-white/60 text-xs">Create and publish blog content</p>
                </div>
            </a>
            <a href="posts.php" class="bg-white hover:bg-gray-50 border border-gray-200 rounded-2xl p-5 flex items-center gap-4 transition shadow-sm">
                <i class="bi bi-list-ul text-3xl text-[#49499A]"></i>
                <div>
                    <p class="font-bold text-[#1e1e5c]">Manage Posts</p>
                    <p class="text-gray-400 text-xs">Edit, delete, change status</p>
                </div>
            </a>
            <a href="<?= cbd_base_path() ?>/blog" target="_blank" class="bg-white hover:bg-gray-50 border border-gray-200 rounded-2xl p-5 flex items-center gap-4 transition shadow-sm">
                <i class="bi bi-eye text-3xl text-[#EE483D]"></i>
                <div>
                    <p class="font-bold text-[#1e1e5c]">View Blog</p>
                    <p class="text-gray-400 text-xs">See the live blog listing</p>
                </div>
            </a>
        </div>

    </main>
</div>
</body>
</html>
