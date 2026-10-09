<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo   = get_db();
$flash = get_flash();
$active_page = 'blog-tags';

function tag_slug(string $s): string {
    return strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $s), '-'));
}

// ── POST actions ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $slug = tag_slug($_POST['slug'] ?? $name);
        if ($name && $pdo) {
            // Unique slug
            $base = $slug; $i = 1;
            $slug_chk = $pdo->prepare("SELECT id FROM tags WHERE slug = ?");
            $slug_chk->execute([$slug]);
            while ($slug_chk->fetchColumn()) {
                $slug = $base . '-' . $i++;
                $slug_chk->execute([$slug]);
            }
            try {
                $pdo->prepare('INSERT INTO tags (name, slug) VALUES (?, ?)')->execute([$name, $slug]);
                set_flash("Tag \"$name\" added!", 'success');
            } catch (Exception $e) {
                set_flash('Tag already exists.', 'error');
            }
        }
    }

    if ($action === 'edit') {
        $id   = intval($_POST['id']);
        $name = trim($_POST['name'] ?? '');
        $slug = tag_slug($_POST['slug'] ?? $name);
        if ($id && $name && $pdo) {
            $pdo->prepare('UPDATE tags SET name=?, slug=? WHERE id=?')->execute([$name, $slug, $id]);
            set_flash('Tag updated!', 'success');
        }
    }

    if ($action === 'delete') {
        $id = intval($_POST['id']);
        if ($id && $pdo) {
            $pdo->prepare('DELETE FROM post_tags WHERE tag_id=?')->execute([$id]);
            $pdo->prepare('DELETE FROM tags WHERE id=?')->execute([$id]);
            set_flash('Tag deleted.', 'info');
        }
    }

    header('Location: blog-tags.php');
    exit;
}

// ── Fetch all tags with post count ────────────────────────────────
$all_tags = $pdo ? $pdo->query('
    SELECT t.*, COUNT(pt.post_id) AS post_count
    FROM tags t
    LEFT JOIN post_tags pt ON pt.tag_id = t.id
    GROUP BY t.id
    ORDER BY t.name
')->fetchAll() : [];

$edit_tag = null;
if (isset($_GET['edit']) && $pdo) {
    $s = $pdo->prepare('SELECT * FROM tags WHERE id=?');
    $s->execute([intval($_GET['edit'])]);
    $edit_tag = $s->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Blog Tags — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:pl-60 flex flex-col min-h-screen">

  <!-- Topbar -->
  <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
    <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
    <i class="bi bi-tags-fill text-xl text-[#49499A]"></i>
    <h1 class="text-xl font-extrabold text-[#1e1e5c]">Blog Tags</h1>
    <a href="posts.php" class="ml-auto flex items-center gap-2 text-sm text-gray-500 hover:text-gray-800">
      <i class="bi bi-arrow-left"></i> Back to Posts
    </a>
  </header>

  <main class="flex-1 p-6 max-w-5xl mx-auto w-full">

    <!-- Flash -->
    <?php if ($flash): ?>
    <div class="mb-4 px-4 py-3 rounded-xl text-sm font-medium flex items-center gap-2
      <?= $flash['type'] === 'success' ? 'bg-green-50 text-green-700 border border-green-200' :
         ($flash['type'] === 'error'   ? 'bg-red-50 text-red-700 border border-red-200' :
                                         'bg-blue-50 text-blue-700 border border-blue-200') ?>">
      <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle-fill' : ($flash['type'] === 'error' ? 'x-circle-fill' : 'info-circle-fill') ?>"></i>
      <?= htmlspecialchars($flash['msg']) ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

      <!-- ── ADD / EDIT FORM ── -->
      <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
          <h2 class="font-bold text-[#1e1e5c] mb-4 flex items-center gap-2">
            <i class="bi bi-<?= $edit_tag ? 'pencil-fill text-amber-500' : 'plus-circle-fill text-[#49499A]' ?>"></i>
            <?= $edit_tag ? 'Edit Tag' : 'Add New Tag' ?>
          </h2>
          <form method="POST" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="<?= $edit_tag ? 'edit' : 'add' ?>">
            <?php if ($edit_tag): ?><input type="hidden" name="id" value="<?= $edit_tag['id'] ?>"><?php endif; ?>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Tag Name *</label>
              <input type="text" name="name" required placeholder="e.g. Web Design"
                     value="<?= htmlspecialchars($edit_tag['name'] ?? '') ?>"
                     oninput="autoSlug(this)"
                     class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:border-[#49499A]">
            </div>

            <div>
              <label class="block text-xs font-semibold text-gray-600 mb-1">Slug</label>
              <input type="text" name="slug" id="slug-field" placeholder="auto-generated"
                     value="<?= htmlspecialchars($edit_tag['slug'] ?? '') ?>"
                     class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-mono focus:outline-none focus:border-[#49499A]">
            </div>

            <div class="flex gap-2 pt-1">
              <button type="submit"
                      class="flex-1 bg-[#49499A] hover:bg-[#3a3a7a] text-white font-bold py-2.5 rounded-xl text-sm flex items-center justify-center gap-2 transition">
                <i class="bi bi-<?= $edit_tag ? 'check-lg' : 'plus-lg' ?>"></i>
                <?= $edit_tag ? 'Update Tag' : 'Add Tag' ?>
              </button>
              <?php if ($edit_tag): ?>
              <a href="blog-tags.php"
                 class="px-4 py-2.5 rounded-xl text-sm font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200 flex items-center gap-1 transition">
                Cancel
              </a>
              <?php endif; ?>
            </div>
          </form>
        </div>

        <!-- Quick preview of all tags as chips -->
        <?php if (!empty($all_tags)): ?>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-4">
          <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">All Tags Preview</p>
          <div class="flex flex-wrap gap-2">
            <?php foreach ($all_tags as $t): ?>
            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold bg-[#f0f0ff] text-[#49499A] border border-[#49499A]/20">
              <i class="bi bi-hash" style="font-size:10px"></i><?= htmlspecialchars($t['name']) ?>
              <span class="ml-1 text-[#49499A]/50"><?= $t['post_count'] ?></span>
            </span>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- ── TAG LIST ── -->
      <div class="lg:col-span-3">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
          <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-[#1e1e5c] flex items-center gap-2">
              <i class="bi bi-tags-fill text-[#49499A]"></i> All Tags
            </h2>
            <span class="text-xs text-gray-400"><?= count($all_tags) ?> tags</span>
          </div>

          <?php if (empty($all_tags)): ?>
          <div class="text-center py-12 text-gray-400">
            <i class="bi bi-tags text-4xl block mb-2"></i>
            <p class="text-sm">No tags yet. Add one!</p>
          </div>
          <?php else: ?>
          <table class="w-full text-sm">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-100">
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Tag</th>
                <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase">Slug</th>
                <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 uppercase">Posts</th>
                <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase">Actions</th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($all_tags as $t): ?>
            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
              <td class="px-4 py-3">
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-[#f0f0ff] text-[#49499A] border border-[#49499A]/20">
                  <i class="bi bi-hash" style="font-size:10px"></i><?= htmlspecialchars($t['name']) ?>
                </span>
              </td>
              <td class="px-4 py-3 font-mono text-xs text-gray-400"><?= htmlspecialchars($t['slug']) ?></td>
              <td class="px-4 py-3 text-center">
                <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full"><?= $t['post_count'] ?> posts</span>
              </td>
              <td class="px-4 py-3 text-right whitespace-nowrap">
                <a href="?edit=<?= $t['id'] ?>"
                   class="inline-flex items-center gap-1 text-xs font-semibold bg-green-50 text-green-700 border border-green-200 px-2.5 py-1 rounded-lg hover:bg-green-100 transition mr-1">
                  <i class="bi bi-pencil"></i> Edit
                </a>
                <form method="POST" class="inline" onsubmit="return confirm('Delete tag \'<?= htmlspecialchars($t['name']) ?>\'? It will be removed from all posts.')">
                  <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $t['id'] ?>">
                  <button type="submit"
                          class="inline-flex items-center gap-1 text-xs font-semibold bg-red-50 text-red-600 border border-red-200 px-2.5 py-1 rounded-lg hover:bg-red-100 transition">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
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
</script>
</body>
</html>
