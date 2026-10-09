<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo     = get_db();
$id      = (int)($_GET['id'] ?? 0);
$pid     = (int)($_GET['parent_id'] ?? 0);
$cat     = null;
$is_edit = false;

if ($id && $pdo) {
    $cat     = $pdo->prepare("SELECT * FROM categories WHERE id=?")->execute([$id]) ? $pdo->prepare("SELECT * FROM categories WHERE id=?")->execute([$id]) : null;
    $stmt    = $pdo->prepare("SELECT * FROM categories WHERE id=?");
    $stmt->execute([$id]);
    $cat     = $stmt->fetch();
    if ($cat) {
        $is_edit = true;
        $pid     = (int)($cat['parent_id'] ?? 0);
    }
}

// All parent categories for dropdown
$parents = $pdo ? $pdo->query("SELECT id,name FROM categories WHERE parent_id IS NULL ORDER BY sort_order,name")->fetchAll() : [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $name      = trim($_POST['name'] ?? '');
    $slug      = trim($_POST['slug'] ?? '');
    $parent_id = (int)($_POST['parent_id'] ?? 0) ?: null;
    $sort      = (int)($_POST['sort_order'] ?? 0);
    $status    = (int)($_POST['status'] ?? 1);

    if (!$name || !$slug) {
        $err = 'Name and slug are required.';
    } else {
        try {
            if ($is_edit) {
                $pdo->prepare("UPDATE categories SET name=?,slug=?,parent_id=?,sort_order=?,status=? WHERE id=?")
                    ->execute([$name,$slug,$parent_id,$sort,$status,$id]);
                set_flash("Category updated: {$name}", 'success');
            } else {
                $pdo->prepare("INSERT INTO categories (name,slug,parent_id,sort_order,status) VALUES(?,?,?,?,?)")
                    ->execute([$name,$slug,$parent_id,$sort,$status]);
                set_flash("Category added: {$name}", 'success');
            }
            header('Location: categories.php');
            exit;
        } catch (PDOException $e) {
            $err = 'Slug already exists or DB error: ' . $e->getMessage();
        }
    }
}

$v = fn($k) => htmlspecialchars($cat[$k] ?? ($_POST[$k] ?? ''));
$active_page = 'categories';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $is_edit ? 'Edit Category' : 'Add Category' ?> — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:pl-60 flex flex-col min-h-screen">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
        <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
        <a href="categories.php" class="text-gray-400 hover:text-gray-600"><i class="bi bi-arrow-left text-lg"></i></a>
        <h1 class="text-xl font-extrabold text-[#1e1e5c]"><?= $is_edit ? 'Edit Category' : 'Add Category' ?></h1>
    </header>

    <main class="flex-1 p-6 max-w-lg mx-auto w-full">
        <?php if (!empty($err)): ?>
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm px-5 py-3 rounded-xl"><?= htmlspecialchars($err) ?></div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

                <div class="mb-5">
                    <label>Category Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="<?= $v('name') ?>" placeholder="e.g. Web Design & Dev" required
                           oninput="autoSlug(this.value)">
                </div>

                <div class="mb-5">
                    <label>Slug <span class="text-red-500">*</span> <span class="text-xs text-gray-400 font-normal">(used in URL & DB)</span></label>
                    <input type="text" name="slug" id="slug" value="<?= $v('slug') ?>" placeholder="web-design" required>
                </div>

                <div class="mb-5">
                    <label>Parent Category <span class="text-xs text-gray-400 font-normal">(leave empty for main category)</span></label>
                    <select name="parent_id">
                        <option value="">— Main Category —</option>
                        <?php foreach ($parents as $p): ?>
                        <option value="<?= $p['id'] ?>" <?= $pid === (int)$p['id'] ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-5 grid grid-cols-2 gap-4">
                    <div>
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="<?= $v('sort_order') ?: 0 ?>" min="0" max="99">
                    </div>
                    <div>
                        <label>Status</label>
                        <select name="status">
                            <option value="1" <?= ($cat['status']??1)==1 ? 'selected':'' ?>>Active</option>
                            <option value="0" <?= ($cat['status']??1)==0 ? 'selected':'' ?>>Inactive</option>
                        </select>
                    </div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="flex-1 bg-[#EE483D] hover:bg-red-600 text-white font-bold py-3 rounded-xl transition">
                        <?= $is_edit ? 'Update Category' : 'Save Category' ?>
                    </button>
                    <a href="categories.php"
                       class="px-5 py-3 border border-gray-200 rounded-xl text-sm font-medium text-gray-600 hover:bg-gray-50 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
function autoSlug(val) {
    if (document.getElementById('slug').dataset.manual) return;
    document.getElementById('slug').value = val.toLowerCase()
        .replace(/[^a-z0-9\s-]/g,'').trim().replace(/\s+/g,'-');
}
document.getElementById('slug').addEventListener('input', () => {
    document.getElementById('slug').dataset.manual = '1';
});
</script>
</body>
</html>
