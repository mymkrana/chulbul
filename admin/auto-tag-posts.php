<?php
require_once __DIR__ . '/config.php';
require_login();

// ── Single post processing (AJAX call) ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'])) {
    header('Content-Type: application/json');
    verify_csrf(true);

    $post_id = (int)$_POST['post_id'];
    $pdo     = get_db();
    if (!$pdo) { echo json_encode(['error' => 'DB failed']); exit; }

    // Get post — full content
    $stmt = $pdo->prepare("SELECT id, title, excerpt, content FROM posts WHERE id = ? AND status = 'published'");
    $stmt->execute([$post_id]);
    $post = $stmt->fetch();
    if (!$post) { echo json_encode(['error' => 'Post not found']); exit; }

    // Get existing tags
    $avail_tags = $pdo->query("SELECT id, name, slug FROM tags ORDER BY name")->fetchAll();

    // Full content — strip HTML, take first 800 chars (enough to understand topic)
    $full_text  = trim(strip_tags($post['content']));
    $full_text  = preg_replace('/\s+/', ' ', $full_text);
    $content_for_ai = mb_substr($full_text, 0, 800);

    // Tag list for AI
    $tag_list = implode(', ', array_map(fn($t) => '"' . $t['name'] . '" (id:' . $t['id'] . ')', $avail_tags));

    $prompt = <<<PROMPT
You are tagging blog posts for a web design agency.

Read the full post content below and understand what the post is MAINLY about.

POST TITLE: {$post['title']}
POST CONTENT:
{$content_for_ai}

AVAILABLE TAGS (use these IDs):
{$tag_list}

YOUR JOB:
1. Understand the core topic of the post from its content.
2. Select 2-3 tags from the available list that BEST match what this post is about.
3. ALWAYS prefer existing tags. Only suggest a new_tag if NO existing tag fits.
4. If available tags are sufficient, leave new_tag as empty string "".

NEW TAG RULES (strict — for SEO, tags must be broad & reusable across many posts):
- A new tag must be a SINGLE broad topic word or a well-known 2-word category.
  GOOD examples: "WordPress", "Shopify", "SEO", "Ecommerce", "Figma", "Branding", "Web Design", "Digital Marketing".
  BAD examples (NEVER create these): "Shopify vs Joomla", "Travel Website", "Travel Website Development", "Best Ecommerce Platform" — these are too specific and create thin one-post pages.
- NO comparison phrases ("X vs Y"), NO niche+"website"/"development"/"design" combos, max 2 words, no full sentences.

RULES:
- selected_ids must be integers from the available tag IDs above
- Maximum 3 tags total
- Choose based on CONTENT, not just title

Return ONLY this JSON, nothing else:
{"selected_ids":[2,5],"new_tag":""}
PROMPT;

    // ── AI helpers ─────────────────────────────────────────────────────────────
    function _at_groq(string $model, string $prompt, string $key): string {
        $body = json_encode(['model'=>$model,'messages'=>[['role'=>'user','content'=>$prompt]],'temperature'=>0.1,'max_tokens'=>100]);
        $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$body,CURLOPT_TIMEOUT=>30,CURLOPT_SSL_VERIFYPEER => true,CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.$key]]);
        $r = curl_exec($ch); curl_close($ch);
        $d = json_decode($r, true);
        return $d['choices'][0]['message']['content'] ?? '';
    }
    function _at_gemini(string $prompt, string $key): string {
        $body = json_encode(['contents'=>[['parts'=>[['text'=>$prompt]]]],'generationConfig'=>['temperature'=>0.1,'maxOutputTokens'=>100]]);
        $url  = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key='.$key;
        $ch = curl_init($url);
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$body,CURLOPT_TIMEOUT=>30,CURLOPT_SSL_VERIFYPEER => true,CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,CURLOPT_HTTPHEADER=>['Content-Type: application/json']]);
        $r = curl_exec($ch); curl_close($ch);
        $d = json_decode($r, true);
        return $d['candidates'][0]['content']['parts'][0]['text'] ?? '';
    }
    function _at_openrouter(string $model, string $prompt, string $key): string {
        $body = json_encode(['model'=>$model,'messages'=>[['role'=>'user','content'=>$prompt]],'temperature'=>0.1,'max_tokens'=>100]);
        $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$body,CURLOPT_TIMEOUT=>40,CURLOPT_SSL_VERIFYPEER => true,CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.$key,'HTTP-Referer: https://chulbuldesign.com']]);
        $r = curl_exec($ch); curl_close($ch);
        $d = json_decode($r, true);
        return $d['choices'][0]['message']['content'] ?? '';
    }

    // Try providers in order: Groq 70B → Groq 8B → Gemini → OpenRouter
    $raw = _at_groq('llama-3.3-70b-versatile', $prompt, GROQ_API_KEY);
    if (!$raw) $raw = _at_groq('llama-3.1-8b-instant', $prompt, GROQ_API_KEY);
    if (!$raw) $raw = _at_gemini($prompt, GEMINI_API_KEY);
    if (!$raw) $raw = _at_openrouter('mistralai/mistral-7b-instruct:free', $prompt, OPENROUTER_API_KEY);
    if (!$raw) $raw = _at_openrouter('meta-llama/llama-3.2-3b-instruct:free', $prompt, OPENROUTER_API_KEY);
    if (!$raw) $raw = _at_openrouter('qwen/qwen-2.5-7b-instruct:free', $prompt, OPENROUTER_API_KEY);

    // Parse AI response — clean markdown fences
    $raw = preg_replace('/```(?:json)?\s*|\s*```/', '', trim($raw));
    preg_match('/\{[^}]+\}/', $raw, $m);
    $ai = json_decode($m[0] ?? '{}', true);

    $selected_ids = array_values(array_filter(array_map('intval', $ai['selected_ids'] ?? [])));
    $new_tag_name = trim($ai['new_tag'] ?? '');

    // Validate — only keep IDs that actually exist
    $valid_ids    = array_map('intval', array_column($avail_tags, 'id'));
    $selected_ids = array_values(array_filter($selected_ids, fn($id) => in_array((int)$id, $valid_ids)));
    $selected_ids = array_slice($selected_ids, 0, 3);

    // ── Keyword fallback if AI returned nothing ────────────────────────────────
    if (empty($selected_ids)) {
        $combined = strtolower($post['title'] . ' ' . $full_text);

        // Build slug-based lookup from actual DB tags (future-proof for any server)
        $slug_to_id = [];
        foreach ($avail_tags as $t) {
            $slug_to_id[$t['slug']] = (int)$t['id'];
        }

        // keyword → tag slug (not hardcoded IDs)
        $keyword_slug_map = [
            'wordpress'         => 'wordpress',
            'shopify'           => 'shopify',
            'seo'               => 'seo',
            'search engine'     => 'seo',
            'web design'        => 'web-design',
            'website design'    => 'web-design',
            'web development'   => 'web-development',
            'web developer'     => 'web-development',
            'digital marketing' => 'digital-marketing',
            'social media'      => 'digital-marketing',
            'mobile app'        => 'mobile-app',
            'android'           => 'mobile-app',
            'ios app'           => 'mobile-app',
            'ui/ux'             => 'ui-ux-design',
            ' ui '              => 'ui-ux-design',
            ' ux '              => 'ui-ux-design',
            'user experience'   => 'ui-ux-design',
            'user interface'    => 'ui-ux-design',
            'ecommerce'         => 'ecommerce',
            'e-commerce'        => 'ecommerce',
            'online store'      => 'ecommerce',
            'branding'          => 'branding',
            ' brand '           => 'branding',
            'logo'              => 'branding',
        ];

        foreach ($keyword_slug_map as $kw => $slug) {
            if (isset($slug_to_id[$slug]) && strpos($combined, $kw) !== false) {
                $tid = $slug_to_id[$slug];
                if (!in_array($tid, $selected_ids)) $selected_ids[] = $tid;
                if (count($selected_ids) >= 2) break;
            }
        }
    }

    $created_tag  = null;

    // ── SEO guard: only allow CLEAN, broad, reusable tags ─────────────────────
    // Rejects phrase-like tags that create thin one-post archive pages.
    $at_is_good_tag = function (string $name): bool {
        $n = trim(mb_strtolower($name));
        if ($n === '' || mb_strlen($n) > 20) return false;
        $words = preg_split('/\s+/', $n);
        if (count($words) > 2) return false;                          // max 2 words
        if (preg_match('/\b(vs|versus)\b/', $n)) return false;        // no comparisons
        // reject niche + generic-suffix combos like "travel website", "x development"
        if (count($words) === 2 &&
            preg_match('/\b(website|development|design|company|services?|portal|page|platform|tool|tools|guide|tips)\b/', $words[1])) {
            // allow established 2-word categories only
            $ok_two = ['web design','web development','digital marketing','social media','content marketing'];
            if (!in_array($n, $ok_two)) return false;
        }
        return true;
    };

    // Create new tag if AI suggested one AND it passes the SEO guard
    if ($new_tag_name && $at_is_good_tag($new_tag_name)) {
        $new_slug = preg_replace('/[^a-z0-9]+/', '-', strtolower($new_tag_name));
        $new_slug = trim($new_slug, '-');
        try {
            // Check if already exists
            $exists = $pdo->prepare("SELECT id FROM tags WHERE slug = ? OR name = ?");
            $exists->execute([$new_slug, $new_tag_name]);
            $exist_id = $exists->fetchColumn();
            if ($exist_id) {
                // Already exists — just use it
                if (!in_array((int)$exist_id, $selected_ids)) {
                    $selected_ids[] = (int)$exist_id;
                }
            } else {
                $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)")->execute([$new_tag_name, $new_slug]);
                $new_id = (int)$pdo->lastInsertId();
                $selected_ids[] = $new_id;
                $created_tag = ['id' => $new_id, 'name' => $new_tag_name, 'slug' => $new_slug];
            }
        } catch (Exception $e) { /* slug conflict — skip */ }
    }

    // Save to post_tags
    $final_ids = array_unique(array_slice($selected_ids, 0, 3));
    if ($final_ids) {
        $pdo->prepare('DELETE FROM post_tags WHERE post_id = ?')->execute([$post_id]);
        $ti = $pdo->prepare('INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)');
        foreach ($final_ids as $tid) $ti->execute([$post_id, $tid]);
    }

    // Get tag names for response
    $tag_names = [];
    foreach ($avail_tags as $t) {
        if (in_array($t['id'], $final_ids)) $tag_names[] = $t['name'];
    }
    if ($created_tag && in_array($created_tag['id'], $final_ids)) {
        $tag_names[] = $created_tag['name'] . ' ✨';
    }

    echo json_encode([
        'success'     => true,
        'post_id'     => $post_id,
        'title'       => $post['title'],
        'tags'        => $tag_names,
        'new_tag'     => $created_tag ? $created_tag['name'] : null,
        'tag_count'   => count($final_ids),
    ]);
    exit;
}

// ── Fetch all posts for UI ────────────────────────────────────────────────────
$pdo   = get_db();
$posts = $pdo ? $pdo->query("SELECT id, title FROM posts WHERE status='published' ORDER BY id ASC")->fetchAll() : [];
$tags  = $pdo ? $pdo->query("SELECT id, name FROM tags ORDER BY name")->fetchAll() : [];
$active_page = 'auto-tag-posts';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Auto Tag Posts — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-50 text-gray-800">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:ml-60 min-h-screen flex flex-col">

    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
        <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
        <a href="posts.php" class="text-gray-400 hover:text-gray-600"><i class="bi bi-arrow-left text-lg"></i></a>
        <div class="flex-1">
            <h1 class="text-xl font-extrabold text-[#1e1e5c]">Auto Tag Posts</h1>
            <p class="text-xs text-gray-400">AI har post ka content padhega aur automatically tags assign karega</p>
        </div>
        <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1.5 rounded-full font-semibold">
            <?= count($posts) ?> posts &nbsp;·&nbsp; <?= count($tags) ?> tags available
        </span>
    </header>

    <main class="flex-1 p-6 max-w-3xl mx-auto w-full">

        <!-- Info card -->
        <div class="bg-[#1e1e5c] text-white rounded-2xl p-5 mb-6">
            <p class="font-bold text-sm mb-2"><i class="bi bi-stars text-amber-400 mr-2"></i>Kaise kaam karta hai?</p>
            <div class="text-xs text-white/70 space-y-1">
                <p>→ Har post ka <strong class="text-white">pura content</strong> AI ko bhejega (title + full body, 800 chars)</p>
                <p>→ AI samjhega post kis topic ke baare mein hai</p>
                <p>→ Existing tags me se 2-3 best matches choose karega</p>
                <p>→ Agar koi suitable tag nahi → AI naya tag create karega ✨</p>
                <p>→ Sab changes DB me save ho jayenge</p>
            </div>
        </div>

        <!-- Run button -->
        <div class="flex items-center gap-4 mb-6" id="run-section">
            <button onclick="startAutoTag()"
                    id="run-btn"
                    class="inline-flex items-center gap-2 bg-[#EE483D] hover:bg-red-600 text-white font-bold px-6 py-3 rounded-xl transition shadow-lg shadow-red-200">
                <i class="bi bi-stars"></i> Sab Posts Auto-Tag Karo
            </button>
            <span id="progress-text" class="text-sm text-gray-400 hidden"></span>
        </div>

        <!-- Progress bar -->
        <div id="progress-bar-wrap" class="hidden mb-6">
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div id="progress-bar" class="bg-[#49499A] h-2 rounded-full transition-all duration-500" style="width:0%"></div>
            </div>
        </div>

        <!-- Posts list -->
        <div class="space-y-3" id="posts-list">
            <?php foreach ($posts as $p): ?>
            <div class="bg-white rounded-xl border border-gray-100 px-5 py-3.5 flex items-center gap-4" id="post-row-<?= $p['id'] ?>">
                <div class="w-7 h-7 rounded-full border-2 border-gray-200 flex items-center justify-center flex-shrink-0 text-xs" id="post-icon-<?= $p['id'] ?>">
                    <i class="bi bi-clock text-gray-300"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-[#1e1e5c] truncate"><?= htmlspecialchars($p['title']) ?></p>
                    <p class="text-xs text-gray-400 mt-0.5" id="post-tags-<?= $p['id'] ?>">Pending…</p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Summary -->
        <div id="summary-card" class="hidden mt-6 bg-green-50 border border-green-200 rounded-2xl p-5">
            <p class="font-bold text-green-700 flex items-center gap-2 mb-2">
                <i class="bi bi-check-circle-fill"></i> Sab posts tag ho gaye!
            </p>
            <p class="text-sm text-green-600" id="summary-text"></p>
            <div class="mt-3 flex gap-3">
                <a href="posts.php" class="text-sm font-semibold text-[#49499A] hover:text-[#EE483D] transition">
                    ← Posts list dekho
                </a>
                <a href="blog-tags.php" class="text-sm font-semibold text-[#49499A] hover:text-[#EE483D] transition">
                    Tags manage karo →
                </a>
            </div>
        </div>

    </main>
</div>

<script>
const posts = <?= json_encode(array_map(fn($p) => ['id' => $p['id'], 'title' => $p['title']], $posts)) ?>;
const csrf  = '<?= csrf_token() ?>';
let newTagsCreated = 0;
let totalTagged    = 0;
let errors         = 0;

async function startAutoTag() {
    const btn = document.getElementById('run-btn');
    btn.disabled  = true;
    btn.innerHTML = '<i class="bi bi-arrow-repeat animate-spin"></i> Running…';
    document.getElementById('progress-text').classList.remove('hidden');
    document.getElementById('progress-bar-wrap').classList.remove('hidden');

    for (let i = 0; i < posts.length; i++) {
        const p = posts[i];
        setIcon(p.id, 'active');
        document.getElementById('progress-text').textContent = (i + 1) + ' / ' + posts.length + ' processing…';
        updateBar(i, posts.length);

        try {
            const fd = new FormData();
            fd.append('post_id',    p.id);
            fd.append('csrf_token', csrf);

            const res  = await fetch('auto-tag-posts.php', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.success) {
                totalTagged++;
                if (data.new_tag) newTagsCreated++;
                const tagHtml = data.tags.map(t =>
                    `<span class="inline-flex items-center gap-0.5 bg-[#f0f0ff] text-[#49499A] text-xs font-bold px-2 py-0.5 rounded-full">#${t}</span>`
                ).join(' ');
                document.getElementById('post-tags-' + p.id).innerHTML = tagHtml || '<span class="text-gray-300">No tags assigned</span>';
                setIcon(p.id, 'done');
            } else {
                errors++;
                document.getElementById('post-tags-' + p.id).textContent = 'Error: ' + (data.error || 'unknown');
                setIcon(p.id, 'error');
            }
        } catch(e) {
            errors++;
            document.getElementById('post-tags-' + p.id).textContent = 'Network error';
            setIcon(p.id, 'error');
        }

        // Small delay between AI calls to avoid rate limiting
        if (i < posts.length - 1) await sleep(800);
    }

    updateBar(posts.length, posts.length);
    document.getElementById('progress-text').textContent = 'Complete!';
    btn.innerHTML = '<i class="bi bi-check-lg"></i> Done!';

    // Show summary
    document.getElementById('summary-card').classList.remove('hidden');
    document.getElementById('summary-text').textContent =
        totalTagged + ' posts tagged' +
        (newTagsCreated ? ', ' + newTagsCreated + ' naye tags create hue ✨' : '') +
        (errors ? ', ' + errors + ' errors' : '') + '.';
}

function setIcon(id, state) {
    const el = document.getElementById('post-icon-' + id);
    const row = document.getElementById('post-row-' + id);
    if (state === 'active') {
        el.className = 'w-7 h-7 rounded-full border-2 border-[#EE483D] bg-red-50 flex items-center justify-center flex-shrink-0 text-xs';
        el.innerHTML = '<i class="bi bi-arrow-repeat animate-spin text-[#EE483D]"></i>';
        row.classList.add('border-[#49499A]/20', 'bg-[#f8f8ff]');
    } else if (state === 'done') {
        el.className = 'w-7 h-7 rounded-full border-2 border-green-400 bg-green-50 flex items-center justify-center flex-shrink-0 text-xs';
        el.innerHTML = '<i class="bi bi-check-lg text-green-500"></i>';
        row.classList.remove('border-[#49499A]/20', 'bg-[#f8f8ff]');
    } else if (state === 'error') {
        el.className = 'w-7 h-7 rounded-full border-2 border-red-300 bg-red-50 flex items-center justify-center flex-shrink-0 text-xs';
        el.innerHTML = '<i class="bi bi-x text-red-400"></i>';
    }
}

function updateBar(done, total) {
    document.getElementById('progress-bar').style.width = Math.round((done / total) * 100) + '%';
}

function sleep(ms) { return new Promise(r => setTimeout(r, ms)); }
</script>
<script src="assets/admin.js"></script>
</body>
</html>
