<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo = get_db();
$id  = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    set_flash('Invalid post ID.', 'error');
    header('Location: posts.php');
    exit;
}

// Load post
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    set_flash('Post not found.', 'error');
    header('Location: posts.php');
    exit;
}

$errors = [];
$values = $post; // Start with DB values

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $values['title']      = trim($_POST['title']      ?? '');
    $values['slug']       = trim($_POST['slug']       ?? '');
    $values['excerpt']    = trim($_POST['excerpt']    ?? '');
    $selected_tag_ids     = array_filter(array_map('intval', $_POST['tag_ids'] ?? []));
    $values['content']    = $_POST['content']          ?? '';
    $values['meta_title'] = trim($_POST['meta_title'] ?? '');
    $values['meta_desc']  = trim($_POST['meta_desc']  ?? '');
    $values['keywords']   = trim($_POST['keywords']   ?? ''); // UI only, not saved to DB
    $values['read_time']  = trim($_POST['read_time']  ?? '');
    $values['date']       = trim($_POST['date']       ?? date('Y-m-d'));
    $values['status']     = in_array($_POST['status'] ?? '', ['published','draft']) ? $_POST['status'] : 'published';

    // Validation
    if ($values['title'] === '') $errors[] = 'Title is required.';
    if ($values['slug']  === '') $errors[] = 'Slug is required.';
    if ($values['content'] === '') $errors[] = 'Content is required.';

    // Clean slug
    $values['slug'] = preg_replace('/[^a-z0-9\-]/', '', strtolower($values['slug']));
    $values['slug'] = preg_replace('/-+/', '-', trim($values['slug'], '-'));

    if (empty($errors)) {
        // Slug uniqueness check (exclude self)
        $chk = $pdo->prepare("SELECT id FROM posts WHERE slug = ? AND id != ?");
        $chk->execute([$values['slug'], $id]);
        if ($chk->fetch()) {
            $errors[] = 'Another post already uses this slug.';
        }
    }

    if (empty($errors)) {
        $upd = $pdo->prepare("
            UPDATE posts SET
                slug      = :slug,
                title     = :title,
                meta_title= :meta_title,
                meta_desc = :meta_desc,
                date      = :date,
                read_time = :read_time,
                excerpt   = :excerpt,
                content   = :content,
                status    = :status
            WHERE id = :id
        ");
        $upd->execute([
            ':slug'      => $values['slug'],
            ':title'     => $values['title'],
            ':meta_title'=> $values['meta_title'],
            ':meta_desc' => $values['meta_desc'],
            ':date'      => $values['date'] ?: date('Y-m-d'),
            ':read_time' => $values['read_time'],
            ':excerpt'   => $values['excerpt'],
            ':content'   => $values['content'],
            ':status'    => $values['status'],
            ':id'        => $id,
        ]);
        // Save tags — delete old, insert new
        $pdo->prepare('DELETE FROM post_tags WHERE post_id = ?')->execute([$id]);
        if ($selected_tag_ids) {
            $ti = $pdo->prepare('INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)');
            foreach ($selected_tag_ids as $tid) $ti->execute([$id, $tid]);
        }
        set_flash('Post updated successfully!', 'success');
        header('Location: posts.php');
        exit;
    }
}

// All tags + selected tags for this post
$all_tags = [];
$selected_tag_ids = [];
try {
    $all_tags = $pdo->query('SELECT * FROM tags ORDER BY name')->fetchAll();
    $selected_tag_ids = $pdo->prepare('SELECT tag_id FROM post_tags WHERE post_id = ?');
    $selected_tag_ids->execute([$id]);
    $selected_tag_ids = $selected_tag_ids->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}
$active_page = 'posts';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Post — Chulbul Design Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  .section-card {
    background: #fff;
    border: 1px solid #f3f4f6;
    border-radius: 1.25rem;
    padding: 1.5rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  }
  .section-title {
    font-size: 0.8125rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #9ca3af;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  .tag-chip {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    border: 1.5px solid #e5e7eb;
    background: #f9fafb;
    color: #6b7280;
    transition: all .15s;
  }
  .tag-chip:hover { border-color: #49499A; color: #49499A; background: #f0f0ff; }
  .tag-chip.tag-active { background: #49499A; color: #fff; border-color: #49499A; }
</style>
</head>
<body class="bg-gray-50 text-gray-800">

<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:ml-60 min-h-screen flex flex-col">

    <!-- Top bar -->
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
        <button onclick="openSidebar()" class="lg:hidden text-gray-500 hover:text-gray-800">
            <i class="bi bi-list text-2xl"></i>
        </button>
        <a href="posts.php" class="text-gray-400 hover:text-gray-600 transition">
            <i class="bi bi-arrow-left text-lg"></i>
        </a>
        <div class="flex-1 min-w-0">
            <h1 class="text-xl font-extrabold text-[#1e1e5c]">Edit Post</h1>
            <p class="text-xs text-gray-400 truncate"><?= htmlspecialchars($post['title']) ?></p>
        </div>
        <!-- Delete button in top bar -->
        <button onclick="confirmDelete()"
                class="inline-flex items-center gap-2 bg-red-50 text-red-600 border border-red-200 px-4 py-2 rounded-xl text-sm font-bold hover:bg-red-600 hover:text-white hover:border-red-600 transition">
            <i class="bi bi-trash3"></i> Delete Post
        </button>
    </header>

    <main class="flex-1 p-6">

        <?php if (!empty($errors)): ?>
        <div class="mb-5 bg-red-50 border border-red-200 rounded-xl p-4">
            <p class="text-sm font-bold text-red-700 mb-2 flex items-center gap-2">
                <i class="bi bi-exclamation-triangle-fill"></i> Please fix the following errors:
            </p>
            <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                <?php foreach ($errors as $e): ?>
                <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <form method="POST" action="edit-post.php?id=<?= $id ?>" id="post-form">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

                <!-- Left / Main -->
                <div class="xl:col-span-2 space-y-5">

                    <!-- Title & Slug -->
                    <div class="section-card space-y-4">
                        <div>
                            <label for="title">
                                <i class="bi bi-type-h1 text-[#49499A] mr-1"></i> Post Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title"
                                   value="<?= htmlspecialchars($values['title']) ?>"
                                   placeholder="Enter post title…" required>
                        </div>
                        <div>
                            <label for="slug">
                                <i class="bi bi-link-45deg text-[#49499A] mr-1"></i> Slug <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-400 whitespace-nowrap">/blog/</span>
                                <input type="text" id="slug" name="slug"
                                       value="<?= htmlspecialchars($values['slug']) ?>"
                                       placeholder="url-friendly-slug"
                                       required>
                            </div>
                        </div>
                    </div>

                    <!-- Excerpt -->
                    <div class="section-card">
                        <div class="section-title"><i class="bi bi-text-paragraph"></i> Excerpt</div>
                        <textarea id="excerpt" name="excerpt" rows="3"
                                  placeholder="Short summary for blog listing…"><?= htmlspecialchars($values['excerpt']) ?></textarea>
                    </div>

                    <!-- Content -->
                    <div class="section-card">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                            <div class="section-title" style="margin-bottom:0"><i class="bi bi-body-text"></i> Content <span class="text-red-500">*</span></div>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="runEditorCommand('mceCodeEditor')"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100 transition"
                                        title="Raw HTML dekhein aur edit karein">
                                    <i class="bi bi-code-slash"></i> HTML Code
                                </button>
                                <button type="button" onclick="runEditorCommand('mcePreview')"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                                    <i class="bi bi-eye"></i> Preview
                                </button>
                                <button type="button" onclick="runEditorCommand('mceFullScreen')"
                                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                                    <i class="bi bi-arrows-fullscreen"></i> Fullscreen
                                </button>
                            </div>
                        </div>
                        <textarea id="content" name="content"><?= htmlspecialchars($values['content']) ?></textarea>
                        <p class="text-xs text-gray-400 mt-2"><strong>HTML Code</strong> se raw source edit karein; OK dabate hi visual editor update ho jayega.</p>
                    </div>

                    <!-- SEO -->
                    <div class="section-card space-y-4">
                        <div class="section-title"><i class="bi bi-search"></i> SEO Settings</div>
                        <div>
                            <label for="meta_title">Meta Title</label>
                            <input type="text" id="meta_title" name="meta_title"
                                   value="<?= htmlspecialchars($values['meta_title']) ?>"
                                   placeholder="SEO page title">
                        </div>
                        <div>
                            <label for="meta_desc">
                                Meta Description
                                <span class="text-gray-400 font-normal" id="meta-counter">0/160</span>
                            </label>
                            <textarea id="meta_desc" name="meta_desc" rows="3"
                                      maxlength="160"
                                      placeholder="Brief description for search engines (max 160 chars)…"><?= htmlspecialchars($values['meta_desc']) ?></textarea>
                        </div>

                        <!-- Keywords Tag Input -->
                        <div>
                            <label>
                                <i class="bi bi-tags text-[#49499A] mr-1"></i> SEO Keywords
                                <span class="text-gray-400 font-normal text-xs ml-1">(press Enter or comma to add)</span>
                            </label>
                            <input type="hidden" id="keywords" name="keywords" value="<?= htmlspecialchars($values['keywords'] ?? '') ?>">
                            <div id="kw-box"
                                 class="min-h-[42px] flex flex-wrap gap-1.5 cursor-text border border-gray-200 rounded-xl px-3 py-2 bg-gray-50"
                                 onclick="document.getElementById('kw-input').focus()">
                            </div>
                            <input type="text" id="kw-input" placeholder="Type keyword and press Enter…"
                                   autocomplete="off">
                            <p class="text-xs text-gray-400 mt-1.5">Add 5–8 keywords. Example: <em>web design India, best website designer Delhi</em></p>
                        </div>
                    </div>

                </div>

                <!-- Right / Sidebar -->
                <div class="space-y-5">

                    <!-- Publish -->
                    <div class="section-card space-y-4">
                        <div class="section-title"><i class="bi bi-send"></i> Publish</div>

                        <div>
                            <label>Status</label>
                            <div class="flex gap-3">
                                <label class="flex items-center gap-2 cursor-pointer bg-green-50 border border-green-200 rounded-xl px-4 py-2.5 flex-1 hover:bg-green-100 transition has-[:checked]:border-green-500 has-[:checked]:bg-green-100">
                                    <input type="radio" name="status" value="published"
                                           <?= $values['status'] === 'published' ? 'checked' : '' ?>
                                           class="accent-green-600">
                                    <div>
                                        <p class="text-xs font-bold text-green-700">Published</p>
                                        <p class="text-xs text-green-600/70">Visible on site</p>
                                    </div>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer bg-yellow-50 border border-yellow-200 rounded-xl px-4 py-2.5 flex-1 hover:bg-yellow-100 transition has-[:checked]:border-yellow-500 has-[:checked]:bg-yellow-100">
                                    <input type="radio" name="status" value="draft"
                                           <?= $values['status'] === 'draft' ? 'checked' : '' ?>
                                           class="accent-yellow-600">
                                    <div>
                                        <p class="text-xs font-bold text-yellow-700">Draft</p>
                                        <p class="text-xs text-yellow-600/70">Hidden from site</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <button type="submit"
                                class="w-full bg-[#49499A] hover:bg-[#1e1e5c] text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-[#49499A]/20">
                            <i class="bi bi-check-lg text-lg"></i> Update Post
                        </button>

                        <a href="posts.php"
                           class="w-full text-center block text-sm text-gray-400 hover:text-gray-600 transition py-1">
                            Cancel
                        </a>
                    </div>

                    <!-- Tags -->
                    <div class="section-card">
                        <div class="section-title"><i class="bi bi-tags"></i> Tags</div>
                        <?php if (empty($all_tags)): ?>
                        <p class="text-xs text-gray-400 mb-2">No tags yet.</p>
                        <?php else: ?>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <?php foreach ($all_tags as $tag): ?>
                            <?php $checked = in_array($tag['id'], $selected_tag_ids); ?>
                            <label class="cursor-pointer select-none">
                                <input type="checkbox" name="tag_ids[]" value="<?= $tag['id'] ?>"
                                       class="hidden tag-cb" <?= $checked ? 'checked' : '' ?>>
                                <span class="tag-chip <?= $checked ? 'tag-active' : '' ?>">
                                    <i class="bi bi-hash" style="font-size:10px"></i>
                                    <?= htmlspecialchars($tag['name']) ?>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        <a href="blog-tags.php" target="_blank" style="font-size:12px;color:#6366f1;display:inline-flex;align-items:center;gap:4px">
                            <i class="bi bi-plus-circle"></i> Manage Tags
                        </a>
                    </div>

                    <!-- Post Details -->
                    <div class="section-card space-y-4">
                        <div class="section-title"><i class="bi bi-info-circle"></i> Post Details</div>
                        <div>
                            <label for="date">Publish Date</label>
                            <input type="date" id="date" name="date"
                                   value="<?= htmlspecialchars($values['date'] ?? date('Y-m-d')) ?>">
                        </div>
                        <div>
                            <label for="read_time">Read Time</label>
                            <input type="text" id="read_time" name="read_time"
                                   value="<?= htmlspecialchars($values['read_time']) ?>"
                                   placeholder="e.g. 5 min read">
                        </div>
                    </div>

                    <!-- Featured Image (manual upload) -->
                    <div class="section-card" id="featured-img-card">
                        <div class="section-title"><i class="bi bi-image"></i> Featured Image</div>
                        <p class="text-xs text-gray-500 mb-3">Apni image upload karo — post ke top pe featured image set ho jayegi.</p>
                        <div id="fi-preview-wrap" class="mb-3 hidden">
                            <img id="fi-preview" src="" alt="preview" style="width:100%;border-radius:10px;border:1px solid #eee">
                        </div>
                        <input type="file" id="fi-file" accept="image/jpeg,image/png,image/webp,image/gif" class="hidden">
                        <button type="button" onclick="document.getElementById('fi-file').click()"
                                id="fi-btn"
                                class="w-full bg-[#1e1e5c] hover:bg-[#2d2d8a] text-white font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2 text-sm">
                            <i class="bi bi-upload"></i> Upload Featured Image
                        </button>
                        <div id="fi-status" class="mt-2 text-xs hidden"></div>
                    </div>

                    <!-- AI Regenerate -->
                    <div class="section-card" id="ai-regen-card">
                        <div class="section-title"><i class="bi bi-stars"></i> AI Se Update Karo</div>

                        <!-- Optional source content -->
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">
                            <i class="bi bi-clipboard-fill text-[#49499A]"></i> Source Content
                            <span class="font-normal text-gray-400">(optional)</span>
                        </label>
                        <textarea id="ai-paste" rows="3"
                            placeholder="Existing article text/HTML ya research notes…"
                            class="w-full mb-3 text-xs font-mono resize-y"
                            style="border-radius:8px;border:1.5px solid #e5e7eb;padding:8px 10px;background:#fafafa"></textarea>

                        <!-- Topic / instructions -->
                        <label class="text-xs font-semibold text-gray-500 mb-1 block">
                            <i class="bi bi-magic text-[#EE483D]"></i> Topic / AI Instructions
                        </label>
                        <textarea id="ai-instructions" rows="4"
                            placeholder="Kya likhna/update karna hai, audience, tone aur conversion goal batao. Source blank ho to yeh required hai."
                            class="w-full mb-3 text-xs resize-y"
                            style="border-radius:8px;border:1.5px solid #e5e7eb;padding:8px 10px;background:#fafafa"></textarea>
                        <p class="text-xs text-gray-400 -mt-2 mb-3">Sirf instructions se bhi original 3000+ word SEO post generate ho sakti hai.</p>

                        <!-- Keywords -->
                        <div class="mb-3">
                            <label class="flex items-center gap-1.5 text-xs font-semibold text-gray-500 mb-1">
                                <i class="bi bi-bullseye text-[#EE483D]"></i> Target Keywords
                                <span class="font-normal text-gray-400">(optional)</span>
                            </label>
                            <input type="text" id="ai-keywords"
                                   placeholder="e.g. figma updates may 2026, figma new features"
                                   class="w-full text-xs">
                            <p class="text-xs text-gray-400 mt-1">Numbered format bhi chalega: 1. "primary" 2. "secondary"</p>
                        </div>

                        <button type="button" onclick="runAiRegen()"
                                id="ai-regen-btn"
                                class="w-full bg-[#49499A] hover:bg-[#1e1e5c] text-white font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2 text-sm">
                            <i class="bi bi-stars"></i> Generate &amp; Update
                        </button>
                        <div id="ai-regen-status" class="mt-3 text-xs hidden"></div>
                    </div>

                    <!-- View post link -->
                    <div class="section-card">
                        <div class="section-title"><i class="bi bi-eye"></i> View</div>
                        <?php $view_base = cbd_base_path(); ?>
                        <a href="<?= $view_base ?>/blog/<?= htmlspecialchars($post['slug']) ?>" target="_blank"
                           class="flex items-center gap-2 text-sm text-[#49499A] hover:text-[#EE483D] font-semibold transition">
                            <i class="bi bi-box-arrow-up-right"></i>
                            View live post
                        </a>
                        <p class="text-xs text-gray-400 mt-1">/blog/<?= htmlspecialchars($post['slug']) ?></p>
                    </div>

                </div>
            </div>
        </form>

        <!-- Delete form (submitted by JS) -->
        <form id="delete-form" method="POST" action="delete-post.php" class="hidden">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            <input type="hidden" name="id" value="<?= $id ?>">
        </form>
    </main>
</div>

<!-- TinyMCE -->
<script src="https://cdn.tiny.cloud/1/mrgvq7sac9zjk8vpd29wv2zbs91yhyvmqtkcs98zbi04rfso/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
tinymce.init({
    selector: '#content',
    plugins: 'anchor autolink autosave charmap code codesample emoticons fullscreen image link lists media preview searchreplace table visualblocks visualchars wordcount',
    toolbar: [
        'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough forecolor backcolor | alignleft aligncenter alignright alignjustify',
        'bullist numlist outdent indent | link image media table codesample | searchreplace visualblocks | code preview fullscreen | removeformat'
    ],
    menubar: 'file edit view insert format tools table help',
    toolbar_sticky: true,
    toolbar_mode: 'sliding',
    height: 650,
    min_height: 450,
    resize: 'both',
    skin: 'oxide',
    content_css: 'default',
    content_style: 'body{font-family:Inter,Arial,sans-serif;font-size:16px;line-height:1.7;padding:12px} img{max-width:100%;height:auto} table{border-collapse:collapse;width:100%} td,th{border:1px solid #ddd;padding:8px}',
    browser_spellcheck: true,
    contextmenu: 'link image table',
    convert_urls: false,
    autosave_interval: '30s',
    autosave_retention: '60m',
    autosave_prefix: 'cbd-edit-post-<?= $id ?>-',
    onboarding: false,
    promotion: false,
    branding: false,
});

function runEditorCommand(command) {
    var editor = tinymce.get('content');
    if (!editor) {
        alert('Editor abhi load ho raha hai. Ek second baad dobara try karein.');
        return;
    }
    editor.execCommand(command);
}

document.getElementById('post-form').addEventListener('submit', function () {
    tinymce.triggerSave();
});

// ── Featured Image manual upload ────────────────────────────────────────
(function(){
    var fileInput = document.getElementById('fi-file');
    if (!fileInput) return;
    fileInput.addEventListener('change', async function(){
        var f = this.files[0];
        if (!f) return;
        var btn = document.getElementById('fi-btn');
        var status = document.getElementById('fi-status');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Uploading…';
        status.className = 'mt-2 text-xs text-gray-500'; status.textContent = 'Image upload ho rahi hai…';

        var fd = new FormData(); fd.append('file', f); fd.append('csrf_token', document.querySelector('[name=csrf_token]').value);
        try {
            var res = await fetch('upload-image.php', { method:'POST', body: fd });
            var data = await res.json();
            if (!data.success) throw new Error(data.error || 'upload failed');

            var url = data.url;
            // make path relative for content (../assets/...) to match other images
            var relUrl = url.replace(/^\/(chulbuldesign\/)?/, '../');
            var imgTag = '<img src="' + relUrl + '" alt="' + (document.getElementById('title')?.value || 'Featured image') + '" class="w-full rounded-2xl shadow-sm mb-8" loading="lazy">';

            // Update TinyMCE content: replace first <img> or prepend
            var ed = tinymce.get('content');
            var html = ed ? ed.getContent() : document.getElementById('content').value;
            if (/<img[^>]*>/i.test(html)) {
                html = html.replace(/<img[^>]*>/i, imgTag);   // replace first image
            } else {
                html = imgTag + '\n' + html;                  // prepend
            }
            if (ed) ed.setContent(html); else document.getElementById('content').value = html;

            // preview
            document.getElementById('fi-preview').src = url;
            document.getElementById('fi-preview-wrap').classList.remove('hidden');
            status.className = 'mt-2 text-xs text-green-600';
            status.textContent = '✅ Featured image set! "Update Post" dabao to save.';
        } catch(e) {
            status.className = 'mt-2 text-xs text-red-600';
            status.textContent = 'Error: ' + e.message;
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-upload"></i> Upload Featured Image';
        }
    });
})();

// Slug tracking
let slugManuallyEdited = true; // Always true on edit page

document.getElementById('slug').addEventListener('input', function () {
    slugManuallyEdited = true;
});

// Meta counter
const metaDesc    = document.getElementById('meta_desc');
const metaCounter = document.getElementById('meta-counter');
function updateCounter() {
    const len = metaDesc.value.length;
    metaCounter.textContent = len + '/160';
    metaCounter.className = len > 150 ? 'text-red-500 font-bold' : 'text-gray-400 font-normal';
}
metaDesc.addEventListener('input', updateCounter);
updateCounter();

// Delete confirm
function confirmDelete() {
    if (confirm('Are you sure you want to permanently delete this post? This action cannot be undone.')) {
        document.getElementById('delete-form').submit();
    }
}

// ── Keywords Tag Input ──────────────────────────────────────────
(function () {
    const hiddenInput = document.getElementById('keywords');
    const kwBox       = document.getElementById('kw-box');
    const kwInput     = document.getElementById('kw-input');

    let tags = hiddenInput.value
        ? hiddenInput.value.split(',').map(s => s.trim()).filter(Boolean)
        : [];

    function renderTags() {
        kwBox.innerHTML = '';
        tags.forEach((tag, i) => {
            const chip = document.createElement('span');
            chip.className = 'inline-flex items-center gap-1 bg-[#F4F4FF] border border-[#49499A]/20 text-[#49499A] text-xs font-semibold px-2.5 py-1 rounded-full';
            chip.innerHTML = `${tag} <button type="button" onclick="removeTag(${i})" class="ml-0.5 text-[#49499A] hover:text-red-500 transition font-bold leading-none">&times;</button>`;
            kwBox.appendChild(chip);
        });
        hiddenInput.value = tags.join(', ');
    }

    window.removeTag = function(i) {
        tags.splice(i, 1);
        renderTags();
    };

    function addTag(raw) {
        const val = raw.trim().replace(/,+$/, '').trim();
        if (val && !tags.includes(val)) {
            tags.push(val);
            renderTags();
        }
    }

    kwInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ',') {
            e.preventDefault();
            addTag(this.value);
            this.value = '';
        } else if (e.key === 'Backspace' && this.value === '' && tags.length) {
            tags.pop();
            renderTags();
        }
    });

    kwInput.addEventListener('blur', function () {
        if (this.value.trim()) {
            addTag(this.value);
            this.value = '';
        }
    });

    renderTags();
})();

// ── Tag Chip Toggle ──────────────────────────────────────────────
document.querySelectorAll('.tag-cb').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var chip = this.nextElementSibling;
        if (this.checked) chip.classList.add('tag-active');
        else chip.classList.remove('tag-active');
    });
});

// ── AI Regenerate ───────────────────────────────────────────────
async function runAiRegen() {
    const paste        = document.getElementById('ai-paste').value.trim();
    const instructions = document.getElementById('ai-instructions').value.trim();
    const keywords     = document.getElementById('ai-keywords').value.trim();
    const btn          = document.getElementById('ai-regen-btn');
    const status       = document.getElementById('ai-regen-status');

    if (!paste && !instructions) {
        alert('Post ka topic/instructions ya source content dein!');
        return;
    }

    btn.disabled  = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Generating…';
    status.className = 'mt-3 text-xs text-gray-500';
    status.textContent = paste.length > 14000
        ? 'Long article chunks mein update ho raha hai… (2-5 min, page open rakhein)'
        : '3000+ word SEO content + tags update ho rahe hain… (1-5 min, page open rakhein)';

    const fd = new FormData();
    fd.append('type',          'post');
    fd.append('post_id',       '<?= $id ?>');
    fd.append('auto_tags',     '1');
    if (paste) fd.append('paste_content', paste);
    fd.append('csrf_token',    document.querySelector('[name=csrf_token]').value);
    if (keywords) fd.append('target_keywords', keywords);
    if (instructions) fd.append('custom_instructions', instructions);

    try {
        const res  = await fetch('import-save.php', { method: 'POST', body: fd });
        const raw  = await res.text();
        let data;
        try {
            data = JSON.parse(raw);
        } catch (_) {
            throw new Error(/request timeout/i.test(raw)
                ? 'Server timeout hua. Long article ko dobara try karein.'
                : 'Server se invalid response mila (HTTP ' + res.status + ').');
        }

        if (data.error) {
            status.className  = 'mt-3 text-xs text-red-600';
            status.textContent = 'Error: ' + data.error;
        } else {
            // Update tag chips live
            if (data.tag_ids && data.tag_ids.length > 0) {
                document.querySelectorAll('.tag-cb').forEach(function(cb) {
                    const active = data.tag_ids.includes(parseInt(cb.value));
                    cb.checked = active;
                    cb.nextElementSibling.classList.toggle('tag-active', active);
                });
            }

            status.className  = 'mt-3 text-xs text-green-600';
            status.textContent = '✅ Content update ho gaya! Page reload ho rahi hai…';
            setTimeout(() => location.reload(), 1500);
        }
    } catch (e) {
        status.className  = 'mt-3 text-xs text-red-600';
        status.textContent = 'Network error: ' + e.message;
    } finally {
        btn.disabled  = false;
        btn.innerHTML = '<i class="bi bi-stars"></i> Generate &amp; Update';
    }
}
</script>
</body>
</html>
