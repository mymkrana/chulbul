<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo    = get_db();
$errors = [];
$values = [
    'title'      => '',
    'slug'       => '',
    'excerpt'    => '',
    'content'    => '',
    'meta_title' => '',
    'meta_desc'  => '',
    'keywords'   => '',
    'read_time'  => '',
    'date'       => date('Y-m-d'),
    'status'     => 'published',
];
$selected_tag_ids = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    // Collect & sanitize
    $values['title']      = trim($_POST['title']      ?? '');
    $values['slug']       = trim($_POST['slug']       ?? '');
    $values['excerpt']    = trim($_POST['excerpt']    ?? '');
    $selected_tag_ids     = array_filter(array_map('intval', $_POST['tag_ids'] ?? []));
    $values['content']    = $_POST['content']          ?? '';
    $values['meta_title'] = trim($_POST['meta_title'] ?? '');
    $values['meta_desc']  = trim($_POST['meta_desc']  ?? '');
    $values['keywords']   = trim($_POST['keywords']   ?? '');
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
        // Check slug uniqueness
        $chk = $pdo->prepare("SELECT id FROM posts WHERE slug = ?");
        $chk->execute([$values['slug']]);
        if ($chk->fetch()) {
            $errors[] = 'A post with this slug already exists. Please choose a different slug.';
        }
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO posts (slug, title, meta_title, meta_desc, date, read_time, excerpt, content, status)
            VALUES (:slug, :title, :meta_title, :meta_desc, :date, :read_time, :excerpt, :content, :status)
        ");
        $stmt->execute([
            ':slug'       => $values['slug'],
            ':title'      => $values['title'],
            ':meta_title' => $values['meta_title'],
            ':meta_desc'  => $values['meta_desc'],
            ':date'       => $values['date'] ?: date('Y-m-d'),
            ':read_time'  => $values['read_time'],
            ':excerpt'    => $values['excerpt'],
            ':content'    => $values['content'],
            ':status'     => $values['status'],
        ]);
        $new_post_id = (int)$pdo->lastInsertId();
        // Save tags
        if ($selected_tag_ids) {
            $ti = $pdo->prepare('INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)');
            foreach ($selected_tag_ids as $tid) $ti->execute([$new_post_id, $tid]);
        }
        // Social media trigger — sirf published post pe
        if ($values['status'] === 'published') {
            // Get tag names for hashtags
            $selected_tag_names = [];
            if ($selected_tag_ids && !empty($all_tags)) {
                foreach ($all_tags as $t) {
                    if (in_array($t['id'], $selected_tag_ids)) {
                        $selected_tag_names[] = $t['name'];
                    }
                }
            }
            trigger_social_post($values['title'], $values['excerpt'], $values['slug'], $values['content'], $selected_tag_names);
        }
        set_flash('Post "' . $values['title'] . '" created successfully!', 'success');
        header('Location: posts.php');
        exit;
    }
}

// All tags from DB
$all_tags = [];
try { $all_tags = $pdo->query('SELECT * FROM tags ORDER BY name')->fetchAll(); } catch (Exception $e) {}
$active_page = 'add-post';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Post — Chulbul Design Admin</title>
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
        <div>
            <h1 class="text-xl font-extrabold text-[#1e1e5c]">Add New Post</h1>
            <p class="text-xs text-gray-400">Create and publish a new blog post</p>
        </div>
    </header>

    <main class="flex-1 p-6">

        <!-- Errors -->
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

        <form method="POST" action="add-post.php" id="post-form">
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
                                   placeholder="Enter an engaging post title…"
                                   class="text-base font-semibold" required>
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
                            <p class="text-xs text-gray-400 mt-1.5">Auto-generated from title. Edit if needed.</p>
                        </div>
                    </div>

                    <!-- Excerpt -->
                    <div class="section-card">
                        <div class="section-title"><i class="bi bi-text-paragraph"></i> Excerpt</div>
                        <textarea id="excerpt" name="excerpt" rows="3"
                                  placeholder="Short summary shown in blog listing cards…"
                                 ><?= htmlspecialchars($values['excerpt']) ?></textarea>
                    </div>

                    <!-- Content (TinyMCE) -->
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
                            <label for="meta_title">
                                Meta Title
                                <span class="text-gray-400 font-normal" id="meta-title-counter">0/60</span>
                            </label>
                            <input type="text" id="meta_title" name="meta_title"
                                   maxlength="60"
                                   value="<?= htmlspecialchars($values['meta_title']) ?>"
                                   placeholder="SEO page title — 50-60 chars, keyword first">
                            <p class="text-xs text-gray-400 mt-1">Ideal: 50-60 characters. Keyword pehle likhna.</p>
                        </div>
                        <div>
                            <label for="meta_desc">
                                Meta Description
                                <span class="text-gray-400 font-normal" id="meta-counter">0/155</span>
                            </label>
                            <textarea id="meta_desc" name="meta_desc" rows="3"
                                      maxlength="155"
                                      placeholder="Search result description — 140-155 chars, keyword + soft CTA…"
                                     ><?= htmlspecialchars($values['meta_desc']) ?></textarea>
                            <p class="text-xs text-gray-400 mt-1">Ideal: 140-155 characters.</p>
                        </div>

                        <!-- Keywords Tag Input -->
                        <div>
                            <label>
                                <i class="bi bi-tags text-[#49499A] mr-1"></i> SEO Keywords
                                <span class="text-gray-400 font-normal text-xs ml-1">(press Enter or comma to add)</span>
                            </label>
                            <input type="hidden" id="keywords" name="keywords" value="<?= htmlspecialchars($values['keywords']) ?>">
                            <div id="kw-box"
                                 class="min-h-[42px] flex flex-wrap gap-1.5 cursor-text"
                                 onclick="document.getElementById('kw-input').focus()">
                                <!-- tags rendered here by JS -->
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
                                class="w-full bg-[#EE483D] hover:bg-red-600 text-white font-bold py-3 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-red-200">
                            <i class="bi bi-check-lg text-lg"></i> Save Post
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

                    <!-- AI Generate -->
                    <div class="section-card" id="ai-gen-card">
                        <div class="section-title"><i class="bi bi-stars"></i> AI Se Generate Karo</div>

                        <label class="text-xs font-semibold text-gray-500 mb-1 block">
                            <i class="bi bi-clipboard-fill text-[#49499A]"></i> Content Paste (HTML)
                        </label>
                        <textarea id="ai-paste" rows="3"
                            placeholder="Article text ya HTML yahan paste karo…"
                            class="w-full mb-3 text-xs font-mono resize-y"
                            style="border-radius:8px;border:1.5px solid #e5e7eb;padding:8px 10px;background:#fafafa"></textarea>

                        <label class="text-xs font-semibold text-gray-500 mb-1 block">
                            <i class="bi bi-bullseye text-[#EE483D]"></i> Target Keywords
                            <span class="font-normal text-gray-400">(optional)</span>
                        </label>
                        <input type="text" id="ai-keywords"
                               placeholder="e.g. figma updates may 2026, figma new features"
                               class="w-full mb-3 text-xs">

                        <button type="button" onclick="runAiGen()"
                                id="ai-gen-btn"
                                class="w-full bg-[#49499A] hover:bg-[#1e1e5c] text-white font-bold py-2.5 rounded-xl transition flex items-center justify-center gap-2 text-sm">
                            <i class="bi bi-stars"></i> Generate Post
                        </button>
                        <div id="ai-gen-status" class="mt-3 text-xs hidden"></div>
                    </div>

                    <!-- Post Details -->
                    <div class="section-card space-y-4">
                        <div class="section-title"><i class="bi bi-info-circle"></i> Post Details</div>
                        <div>
                            <label for="date">Publish Date</label>
                            <input type="date" id="date" name="date"
                                   value="<?= htmlspecialchars($values['date']) ?>"
                                  >
                        </div>
                        <div>
                            <label for="read_time">Read Time</label>
                            <input type="text" id="read_time" name="read_time"
                                   value="<?= htmlspecialchars($values['read_time']) ?>"
                                   placeholder="e.g. 5 min read"
                                  >
                        </div>
                    </div>

                </div>
            </div>
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
    autosave_prefix: 'cbd-add-post-',
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
        status.className = 'mt-2 text-xs text-gray-500'; status.classList.remove('hidden'); status.textContent = 'Image upload ho rahi hai…';

        var fd = new FormData(); fd.append('file', f); fd.append('csrf_token', document.querySelector('[name=csrf_token]').value);
        try {
            var res = await fetch('upload-image.php', { method:'POST', body: fd });
            var data = await res.json();
            if (!data.success) throw new Error(data.error || 'upload failed');

            var url = data.url;
            var relUrl = url.replace(/^\/(chulbuldesign\/)?/, '../');
            var imgTag = '<img src="' + relUrl + '" alt="' + (document.getElementById('title')?.value || 'Featured image') + '" class="w-full rounded-2xl shadow-sm mb-8" loading="lazy">';

            var ed = tinymce.get('content');
            var html = ed ? ed.getContent() : document.getElementById('content').value;
            if (/<img[^>]*>/i.test(html)) {
                html = html.replace(/<img[^>]*>/i, imgTag);
            } else {
                html = imgTag + '\n' + html;
            }
            if (ed) ed.setContent(html); else document.getElementById('content').value = html;

            document.getElementById('fi-preview').src = url;
            document.getElementById('fi-preview-wrap').classList.remove('hidden');
            status.className = 'mt-2 text-xs text-green-600';
            status.textContent = '✅ Featured image set! Save dabao to publish.';
        } catch(e) {
            status.className = 'mt-2 text-xs text-red-600';
            status.textContent = 'Error: ' + e.message;
        } finally {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-upload"></i> Upload Featured Image';
        }
    });
})();

// Slug auto-generation
let slugManuallyEdited = <?= $values['slug'] !== '' ? 'true' : 'false' ?>;

document.getElementById('title').addEventListener('input', function () {
    if (!slugManuallyEdited) {
        document.getElementById('slug').value = this.value
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim();
    }
});

document.getElementById('slug').addEventListener('input', function () {
    slugManuallyEdited = this.value.length > 0;
});

// Meta title counter
const metaTitle        = document.getElementById('meta_title');
const metaTitleCounter = document.getElementById('meta-title-counter');
function updateTitleCounter() {
    const len = metaTitle.value.length;
    metaTitleCounter.textContent = len + '/60';
    if (len < 50)       metaTitleCounter.className = 'text-amber-500 font-normal';
    else if (len <= 60) metaTitleCounter.className = 'text-green-600 font-bold';
    else                metaTitleCounter.className = 'text-red-500 font-bold';
}
metaTitle.addEventListener('input', updateTitleCounter);
updateTitleCounter();

// Meta description counter
const metaDesc    = document.getElementById('meta_desc');
const metaCounter = document.getElementById('meta-counter');
function updateCounter() {
    const len = metaDesc.value.length;
    metaCounter.textContent = len + '/155';
    if (len < 140)       metaCounter.className = 'text-amber-500 font-normal';
    else if (len <= 155) metaCounter.className = 'text-green-600 font-bold';
    else                 metaCounter.className = 'text-red-500 font-bold';
}
metaDesc.addEventListener('input', updateCounter);
updateCounter();

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

// ── AI Generate ─────────────────────────────────────────────────
async function runAiGen() {
    const paste    = document.getElementById('ai-paste').value.trim();
    const keywords = document.getElementById('ai-keywords').value.trim();
    const btn      = document.getElementById('ai-gen-btn');
    const status   = document.getElementById('ai-gen-status');

    if (!paste) { alert('HTML content paste karo!'); return; }

    btn.disabled  = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Generating…';
    status.className  = 'mt-3 text-xs text-gray-500';
    status.textContent = paste.length > 14000
        ? 'Long article chunks mein generate ho raha hai… (2-5 min, page open rakhein)'
        : 'AI content generate ho raha hai… (30-90 sec)';
    status.classList.remove('hidden');

    const fd = new FormData();
    fd.append('csrf_token',    document.querySelector('[name=csrf_token]').value);
    fd.append('type',          'post');
    fd.append('auto_tags',     '1');
    fd.append('paste_content', paste);
    if (keywords) fd.append('target_keywords', keywords);

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
        } else if (data.post_id) {
            status.className  = 'mt-3 text-xs text-green-600';
            status.textContent = '✅ Post ban gayi! Redirecting…';
            setTimeout(() => window.location.href = 'edit-post.php?id=' + data.post_id + '&imported=1', 1200);
        }
    } catch(e) {
        status.className  = 'mt-3 text-xs text-red-600';
        status.textContent = 'Network error: ' + e.message;
    } finally {
        btn.disabled  = false;
        btn.innerHTML = '<i class="bi bi-stars"></i> Generate Post';
    }
}

// ── Tag Chip Toggle ──────────────────────────────────────────────
document.querySelectorAll('.tag-cb').forEach(function(cb) {
    cb.addEventListener('change', function() {
        var chip = this.nextElementSibling;
        if (this.checked) chip.classList.add('tag-active');
        else chip.classList.remove('tag-active');
    });
});
</script>
</body>
</html>
