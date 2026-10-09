<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo   = get_db();
$flash = get_flash();
$active_page = 'blog-categories';

// ── Helpers ──────────────────────────────────────────────
function bc_slug(string $s): string {
    return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $s), '-'));
}

// ── POST actions ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name      = trim($_POST['name'] ?? '');
        $parent_id = intval($_POST['parent_id'] ?? 0) ?: null;
        $slug      = bc_slug($_POST['slug'] ?? $name);
        $desc      = trim($_POST['description'] ?? '');
        $sort      = intval($_POST['sort_order'] ?? 0);
        if ($name && $pdo) {
            // unique slug
            $base = $slug; $i = 1;
            $slug_chk = $pdo->prepare("SELECT id FROM blog_categories WHERE slug = ?");
            $slug_chk->execute([$slug]);
            while ($slug_chk->fetchColumn()) {
                $slug = $base . '-' . $i++;
                $slug_chk->execute([$slug]);
            }
            $pdo->prepare('INSERT INTO blog_categories (parent_id,name,slug,description,sort_order) VALUES (?,?,?,?,?)')
                ->execute([$parent_id, $name, $slug, $desc, $sort]);
            set_flash("Category \"$name\" added!", 'success');
        }
    }

    if ($action === 'edit') {
        $id        = intval($_POST['id']);
        $name      = trim($_POST['name'] ?? '');
        $parent_id = intval($_POST['parent_id'] ?? 0) ?: null;
        $slug      = bc_slug($_POST['slug'] ?? $name);
        $desc      = trim($_POST['description'] ?? '');
        $sort      = intval($_POST['sort_order'] ?? 0);
        $status    = intval($_POST['status'] ?? 1);
        if ($id && $name && $parent_id != $id && $pdo) {
            $pdo->prepare('UPDATE blog_categories SET parent_id=?,name=?,slug=?,description=?,sort_order=?,status=? WHERE id=?')
                ->execute([$parent_id, $name, $slug, $desc, $sort, $status, $id]);
            set_flash('Category updated!', 'success');
        }
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        if ($id && $pdo) {
            $row = $pdo->prepare('SELECT parent_id FROM blog_categories WHERE id=?');
            $row->execute([$id]);
            $r = $row->fetch();
            $pdo->prepare('UPDATE blog_categories SET parent_id=? WHERE parent_id=?')->execute([$r['parent_id'] ?? null, $id]);
            $pdo->prepare('UPDATE posts SET blog_category_id=NULL WHERE blog_category_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM blog_categories WHERE id=?')->execute([$id]);
            set_flash('Category deleted.', 'info');
        }
    }

    header('Location: blog-categories.php');
    exit;
}

// ── Fetch ─────────────────────────────────────────────────
$all = $pdo ? $pdo->query('
    SELECT bc.*,
           (SELECT COUNT(*) FROM posts p WHERE p.blog_category_id=bc.id) AS post_count
    FROM blog_categories bc
    ORDER BY bc.parent_id IS NOT NULL, bc.parent_id, bc.sort_order, bc.name
')->fetchAll() : [];

$parents  = array_values(array_filter($all, fn($r) => !$r['parent_id']));
$children = [];
foreach ($all as $r) {
    if ($r['parent_id']) $children[$r['parent_id']][] = $r;
}

$edit_cat = null;
if (isset($_GET['edit']) && $pdo) {
    $s = $pdo->prepare('SELECT * FROM blog_categories WHERE id=?');
    $s->execute([intval($_GET['edit'])]);
    $edit_cat = $s->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog Categories — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
.sub-rows { display:none; }
.sub-rows.open { display:table-row-group; }
</style>
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:pl-60 flex flex-col min-h-screen">

  <!-- Topbar -->
  <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
    <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
    <i class="bi bi-tags-fill text-xl text-[#49499A]"></i>
    <h1 class="text-xl font-extrabold text-[#1e1e5c]">Blog Categories</h1>
    <a href="posts.php" class="ml-auto flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800">
      <i class="bi bi-arrow-left"></i> Back to Posts
    </a>
  </header>

  <main class="flex-1 p-6 max-w-5xl mx-auto w-full">

    <!-- Flash -->
    <?php if ($flash): ?>
    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2
      <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-blue-50 text-blue-700 border border-blue-200' ?>">
      <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : 'info-circle-fill' ?>"></i>
      <?= htmlspecialchars($flash['msg']) ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

      <!-- ── ADD / EDIT FORM ── -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
          <h2 class="font-bold text-[#1e1e5c] mb-4 flex items-center gap-2">
            <i class="bi bi-<?= $edit_cat ? 'pencil-fill text-amber-500' : 'plus-circle-fill text-[#49499A]' ?>"></i>
            <?= $edit_cat ? 'Edit Category' : 'Add New Category' ?>
          </h2>
          <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="<?= $edit_cat ? 'edit' : 'add' ?>">
            <?php if ($edit_cat): ?><input type="hidden" name="id" value="<?= $edit_cat['id'] ?>"><?php endif; ?>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Category Name *</label>
              <input type="text" name="name" required placeholder="e.g. Web Design Tips"
                     value="<?= htmlspecialchars($edit_cat['name'] ?? '') ?>"
                     oninput="autoSlug(this)"
                     class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#49499A]">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Slug</label>
              <input type="text" name="slug" id="slug-field" placeholder="auto-generated"
                     value="<?= htmlspecialchars($edit_cat['slug'] ?? '') ?>"
                     class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:border-[#49499A]">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Parent <small class="text-gray-400">(blank = top-level)</small></label>
              <select name="parent_id" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#49499A]">
                <option value="">— None (Top Level) —</option>
                <?php foreach ($parents as $p): ?>
                  <?php if ($edit_cat && $p['id'] == $edit_cat['id']) continue; ?>
                  <option value="<?= $p['id'] ?>" <?= ($edit_cat['parent_id'] ?? '') == $p['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['name']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Sort Order</label>
                <input type="number" name="sort_order" min="0" max="99"
                       value="<?= $edit_cat['sort_order'] ?? 0 ?>"
                       class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#49499A]">
              </div>
              <?php if ($edit_cat): ?>
              <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Status</label>
                <select name="status" class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#49499A]">
                  <option value="1" <?= ($edit_cat['status'] ?? 1) == 1 ? 'selected' : '' ?>>Active</option>
                  <option value="0" <?= ($edit_cat['status'] ?? 1) == 0 ? 'selected' : '' ?>>Inactive</option>
                </select>
              </div>
              <?php endif; ?>
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Description <small class="text-gray-400">(optional)</small></label>
              <textarea name="description" rows="2" placeholder="Short description..."
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#49499A] resize-none"><?= htmlspecialchars($edit_cat['description'] ?? '') ?></textarea>
            </div>

            <div class="flex gap-2 pt-1">
              <button type="submit"
                      class="flex-1 bg-[#49499A] hover:bg-[#3a3a7a] text-white font-bold py-2.5 rounded-xl text-sm flex items-center justify-center gap-2 transition">
                <i class="bi bi-<?= $edit_cat ? 'check-lg' : 'plus-lg' ?>"></i>
                <?= $edit_cat ? 'Update' : 'Add Category' ?>
              </button>
              <?php if ($edit_cat): ?>
              <a href="blog-categories.php"
                 class="px-4 py-2.5 rounded-xl text-sm font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 flex items-center gap-1 transition">
                Cancel
              </a>
              <?php endif; ?>
            </div>
          </form>
        </div>
      </div>

      <!-- ── CATEGORY TREE ── -->
      <div class="lg:col-span-3">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-[#1e1e5c] flex items-center gap-2">
              <i class="bi bi-diagram-3-fill text-[#49499A]"></i> Category Tree
            </h2>
            <span class="text-xs text-gray-400"><?= count($all) ?> total</span>
          </div>

          <?php if (empty($parents)): ?>
          <div class="text-center py-12 text-gray-400">
            <i class="bi bi-tags text-4xl block mb-2"></i>
            <p class="text-sm">No categories yet. Add one!</p>
          </div>
          <?php else: ?>
          <table class="w-full text-sm">
            <tbody>
            <?php foreach ($parents as $p):
                $has_children = !empty($children[$p['id']]);
            ?>
              <!-- Parent Row -->
              <tr class="border-b border-gray-50 hover:bg-gray-50 transition <?= $has_children ? 'cursor-pointer' : '' ?>"
                  <?= $has_children ? "onclick=\"toggleSub('sub_{$p['id']}')" . '"' : '' ?>>
                <td class="px-4 py-3 w-8">
                  <span class="inline-block w-2 h-2 rounded-full <?= $p['status'] ? 'bg-green-400' : 'bg-gray-300' ?>"></span>
                </td>
                <td class="px-2 py-3">
                  <span class="inline-flex items-center gap-1 text-xs font-bold bg-blue-50 text-blue-600 px-2 py-0.5 rounded-full">
                    <i class="bi bi-folder-fill"></i> Parent
                  </span>
                </td>
                <td class="px-2 py-3 flex-1">
                  <p class="font-semibold text-gray-800"><?= htmlspecialchars($p['name']) ?>
                    <?php if ($has_children): ?>
                    <span class="ml-1 text-xs text-gray-400">(<?= count($children[$p['id']]) ?> sub)</span>
                    <?php endif; ?>
                  </p>
                  <p class="text-xs text-gray-400 font-mono"><?= $p['slug'] ?></p>
                </td>
                <td class="px-2 py-3 text-center">
                  <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                    <?= $p['post_count'] ?> posts
                  </span>
                </td>
                <td class="px-4 py-3 text-right whitespace-nowrap" onclick="event.stopPropagation()">
                  <a href="?edit=<?= $p['id'] ?>"
                     class="inline-flex items-center gap-1 text-xs font-semibold bg-green-50 text-green-700 border border-green-200 px-2.5 py-1 rounded-lg hover:bg-green-100 transition mr-1">
                    <i class="bi bi-pencil"></i> Edit
                  </a>
                  <form method="POST" class="inline" onsubmit="return confirm('Delete? Subcategories will be moved up.')">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                    <button type="submit"
                            class="inline-flex items-center gap-1 text-xs font-semibold bg-red-50 text-red-600 border border-red-200 px-2.5 py-1 rounded-lg hover:bg-red-100 transition">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>

              <!-- Children Rows -->
              <?php if ($has_children): ?>
              <tbody id="sub_<?= $p['id'] ?>" class="sub-rows">
              <?php foreach ($children[$p['id']] as $ch): ?>
              <tr class="border-b border-gray-50 bg-gray-50/50 hover:bg-indigo-50/30 transition">
                <td class="pl-8 py-2.5 w-8">
                  <span class="inline-block w-2 h-2 rounded-full <?= $ch['status'] ? 'bg-green-400' : 'bg-gray-300' ?>"></span>
                </td>
                <td class="px-2 py-2.5">
                  <span class="inline-flex items-center gap-1 text-xs font-bold bg-purple-50 text-purple-600 px-2 py-0.5 rounded-full">
                    <i class="bi bi-arrow-return-right"></i> Sub
                  </span>
                </td>
                <td class="px-2 py-2.5">
                  <p class="font-medium text-gray-700"><?= htmlspecialchars($ch['name']) ?></p>
                  <p class="text-xs text-gray-400 font-mono"><?= $ch['slug'] ?></p>
                </td>
                <td class="px-2 py-2.5 text-center">
                  <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                    <?= $ch['post_count'] ?> posts
                  </span>
                </td>
                <td class="px-4 py-2.5 text-right whitespace-nowrap">
                  <a href="?edit=<?= $ch['id'] ?>"
                     class="inline-flex items-center gap-1 text-xs font-semibold bg-green-50 text-green-700 border border-green-200 px-2.5 py-1 rounded-lg hover:bg-green-100 transition mr-1">
                    <i class="bi bi-pencil"></i> Edit
                  </a>
                  <form method="POST" class="inline" onsubmit="return confirm('Delete this subcategory?')">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $ch['id'] ?>">
                    <button type="submit"
                            class="inline-flex items-center gap-1 text-xs font-semibold bg-red-50 text-red-600 border border-red-200 px-2.5 py-1 rounded-lg hover:bg-red-100 transition">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
              <?php endforeach; ?>
              </tbody>
              <?php endif; ?>

            <?php endforeach; ?>
            </tbody>
          </table>
          <?php endif; ?>
        </div>
      </div>

    </div><!-- /grid -->
  </main>
</div>

<script>
function autoSlug(input) {
    const sf = document.getElementById('slug-field');
    if (!sf._edited) {
        sf.value = input.value.toLowerCase().trim()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-').replace(/-+/g, '-');
    }
}
document.getElementById('slug-field').addEventListener('input', function(){ this._edited = true; });

function toggleSub(id) {
    const el = document.getElementById(id);
    if (el) el.classList.toggle('open');
}
</script>
</body>
</html>
