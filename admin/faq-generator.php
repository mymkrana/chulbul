<?php
require_once 'config.php';
require_login();

$pdo = get_db();
$last_error = '';

// ── AI functions ──────────────────────────────────────────────────────────────
function faq_groq_call(string $model, string $prompt, float $temp): array {
    $body = json_encode(['model'=>$model,'messages'=>[['role'=>'user','content'=>$prompt]],'temperature'=>$temp,'max_tokens'=>1200]);
    $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$body,CURLOPT_TIMEOUT=>60,CURLOPT_SSL_VERIFYPEER => true,CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.GROQ_API_KEY]]);
    $resp = curl_exec($ch); $cerr = curl_error($ch); curl_close($ch);
    if ($cerr) return ['text'=>'','error'=>"curl:$cerr",'rate_limit'=>false];
    $d = json_decode($resp,true);
    $rl = !empty($d['error']['code']) && str_contains($d['error']['code'],'rate_limit');
    $t = $d['choices'][0]['message']['content'] ?? '';
    return ['text'=>$t,'error'=>$d['error']['message']??'','rate_limit'=>$rl];
}

function faq_gemini_call(string $prompt, float $temp): array {
    $body = json_encode(['contents'=>[['parts'=>[['text'=>$prompt]]]],'generationConfig'=>['temperature'=>$temp,'maxOutputTokens'=>1200]]);
    $models = ['gemini-2.0-flash','gemini-1.5-flash-latest','gemini-2.0-flash-lite'];
    $last = '';
    foreach ($models as $model) {
        $url = 'https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.GEMINI_API_KEY;
        $ch = curl_init($url);
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$body,CURLOPT_TIMEOUT=>60,CURLOPT_SSL_VERIFYPEER => true,CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,CURLOPT_HTTPHEADER=>['Content-Type: application/json','x-goog-api-key: '.GEMINI_API_KEY]]);
        $resp = curl_exec($ch); curl_close($ch);
        $d = json_decode($resp,true);
        if (!empty($d['error'])) { $last .= "[$model] ".$d['error']['message']." | "; continue; }
        $t = $d['candidates'][0]['content']['parts'][0]['text'] ?? '';
        if ($t) return ['text'=>$t,'error'=>''];
        $last .= "[$model] empty | ";
    }
    return ['text'=>'','error'=>$last];
}

function faq_run_ai(string $prompt): string {
    global $last_error;
    $r = faq_groq_call('llama-3.3-70b-versatile', $prompt, 0.85);
    if ($r['text']) return $r['text'];
    if ($r['rate_limit']) { sleep(12); $r2 = faq_groq_call('llama-3.3-70b-versatile',$prompt,0.85); if ($r2['text']) return $r2['text']; }
    $last_error = 'Groq 70B: '.$r['error'];
    $rg = faq_gemini_call($prompt, 0.85);
    if ($rg['text']) return $rg['text'];
    $last_error .= ' | Gemini: '.$rg['error'];
    $or_models = ['deepseek/deepseek-chat-v3-0324:free','meta-llama/llama-3.3-70b-instruct:free','mistralai/mistral-7b-instruct:free'];
    foreach ($or_models as $orm) {
        $b = json_encode(['model'=>$orm,'messages'=>[['role'=>'user','content'=>$prompt]],'temperature'=>0.85,'max_tokens'=>1200]);
        $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$b,CURLOPT_TIMEOUT=>60,CURLOPT_SSL_VERIFYPEER => true,CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.OPENROUTER_API_KEY,'HTTP-Referer: https://chulbuldesign.com']]);
        $resp = curl_exec($ch); curl_close($ch);
        $d = json_decode($resp,true);
        $t = $d['choices'][0]['message']['content'] ?? '';
        if ($t) return $t;
        $last_error .= " | OR[$orm]: ".($d['error']['message'] ?? substr($resp,0,80));
    }
    return '';
}

function has_faq(string $content): bool {
    return (bool) preg_match('/<h3[^>]*>[^<]*\?<\/h3>/i', $content);
}

function clean_ai_faq(string $raw): string {
    // Keep only h2/h3/p tags that are part of FAQ
    preg_match_all('/<h[23][^>]*>.*?<\/h[23]>|<p[^>]*>.*?<\/p>/is', $raw, $m);
    return implode("\n", $m[0]);
}

// ── Handle AJAX generate request ──────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'generate_one') {
    verify_csrf();
    header('Content-Type: application/json');
    $id = (int)($_POST['post_id'] ?? 0);
    if (!$id) { echo json_encode(['ok'=>false,'msg'=>'No post ID']); exit; }

    $post = $pdo->prepare("SELECT id,slug,title,category,excerpt,content FROM posts WHERE id=?");
    $post->execute([$id]);
    $post = $post->fetch();
    if (!$post) { echo json_encode(['ok'=>false,'msg'=>'Post not found']); exit; }

    if (has_faq($post['content'])) {
        echo json_encode(['ok'=>true,'skipped'=>true,'msg'=>'Already has FAQ']);
        exit;
    }

    $excerpt = strip_tags(mb_substr($post['excerpt'],0,300));
    $prompt = <<<PROMPT
You are writing an FAQ section for a blog post.

Title: {$post['title']}
Category: {$post['category']}
Excerpt: {$excerpt}

Generate exactly 5 unique FAQ questions and answers that a reader of this blog post would genuinely ask. Each answer must be 2-3 informative sentences. Keep a natural, helpful tone — not corporate fluff.

CRITICAL RULES:
- Every <h3> MUST end with a question mark (?)
- Output ONLY the HTML below — no intro text, no markdown, no code fences
- Format:

<h2>Frequently Asked Questions</h2>
<h3>First question?</h3>
<p>Answer to first question.</p>
<h3>Second question?</h3>
<p>Answer to second question.</p>
<h3>Third question?</h3>
<p>Answer to third question.</p>
<h3>Fourth question?</h3>
<p>Answer to fourth question.</p>
<h3>Fifth question?</h3>
<p>Answer to fifth question.</p>
PROMPT;

    $raw = faq_run_ai($prompt);
    if (!$raw) {
        echo json_encode(['ok'=>false,'msg'=>'AI failed: '.$last_error]);
        exit;
    }

    // Strip markdown fences if AI wrapped in ```html ... ```
    $raw = preg_replace('/```(?:html)?\s*([\s\S]*?)```/i', '$1', trim($raw));

    // Validate: must have at least 3 h3 ending with ?
    preg_match_all('/<h3[^>]*>[^<]*\?<\/h3>/i', $raw, $chk);
    if (count($chk[0]) < 3) {
        echo json_encode(['ok'=>false,'msg'=>"Bad output — only ".count($chk[0])." valid h3 questions found"]);
        exit;
    }

    $new_content = rtrim($post['content']) . "\n\n" . trim($raw);
    $upd = $pdo->prepare("UPDATE posts SET content=?, updated_at=NOW() WHERE id=?");
    $upd->execute([$new_content, $id]);

    echo json_encode(['ok'=>true,'skipped'=>false,'msg'=>'FAQ added ('.count($chk[0]).' questions)']);
    exit;
}

// ── Load all posts ─────────────────────────────────────────────────────────────
$posts = $pdo->query("SELECT id,slug,title,category,status,content FROM posts WHERE deleted_at IS NULL ORDER BY date DESC")->fetchAll();
$total   = count($posts);
$missing = count(array_filter($posts, fn($p) => !has_faq($p['content'])));
$token   = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>FAQ Generator — Chulbul Admin</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{font-family:system-ui,sans-serif;background:#f8f9fa;margin:0;padding:24px;color:#333}
h1{font-size:1.4rem;font-weight:800;color:#1e1e5c;margin:0 0 4px}
.sub{color:#6b7280;font-size:.85rem;margin-bottom:24px}
.stats{display:flex;gap:16px;margin-bottom:24px;flex-wrap:wrap}
.stat{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 20px;min-width:120px}
.stat-n{font-size:1.6rem;font-weight:800;color:#1e1e5c}
.stat-l{font-size:.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em}
.btn{display:inline-flex;align-items:center;gap:8px;padding:10px 22px;border-radius:10px;font-weight:700;font-size:.85rem;cursor:pointer;border:none;transition:.2s}
.btn-primary{background:#EE483D;color:#fff}
.btn-primary:hover{background:#d43c31}
.btn-primary:disabled{background:#f87171;cursor:not-allowed}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;font-size:.83rem}
th{background:#f8f9ff;padding:10px 14px;text-align:left;font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #e5e7eb}
td{padding:10px 14px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
tr:last-child td{border-bottom:none}
.badge{display:inline-block;padding:2px 10px;border-radius:999px;font-size:.7rem;font-weight:700}
.badge-ok{background:#dcfce7;color:#16a34a}
.badge-miss{background:#fee2e2;color:#dc2626}
.badge-skip{background:#fef9c3;color:#ca8a04}
.status-cell{min-width:130px}
#log{margin-top:20px;background:#1e1e5c;color:#a5f3fc;border-radius:12px;padding:16px;font-family:monospace;font-size:.78rem;max-height:280px;overflow-y:auto;display:none}
#log p{margin:2px 0}
.log-ok{color:#4ade80}
.log-err{color:#f87171}
.log-skip{color:#fbbf24}
.log-info{color:#93c5fd}
#progress-bar{height:6px;background:#e5e7eb;border-radius:3px;margin:12px 0;display:none}
#progress-fill{height:6px;background:#EE483D;border-radius:3px;width:0;transition:width .3s}
</style>
</head>
<body>

<h1>🤖 FAQ Generator</h1>
<p class="sub">Auto-generates FAQ sections for blog posts that are missing them. Uses the same AI chain as the importer.</p>

<div class="stats">
    <div class="stat"><div class="stat-n"><?= $total ?></div><div class="stat-l">Total Posts</div></div>
    <div class="stat"><div class="stat-n" id="missing-count"><?= $missing ?></div><div class="stat-l">Missing FAQ</div></div>
    <div class="stat"><div class="stat-n" id="done-count"><?= $total - $missing ?></div><div class="stat-l">Have FAQ</div></div>
</div>

<?php if ($missing > 0): ?>
<button class="btn btn-primary" id="generate-btn" onclick="generateAll()">
    ▶ Generate FAQ for all <?= $missing ?> missing posts
</button>
<?php else: ?>
<p style="color:#16a34a;font-weight:700">✅ All posts already have FAQ sections!</p>
<?php endif; ?>

<div id="progress-bar"><div id="progress-fill"></div></div>
<div id="log"></div>

<br><br>
<table>
<thead><tr><th>#</th><th>Title</th><th>Category</th><th>Status</th><th>FAQ</th></tr></thead>
<tbody id="post-table">
<?php foreach ($posts as $i => $p): ?>
<tr id="row-<?= $p['id'] ?>">
    <td><?= $i+1 ?></td>
    <td><a href="<?= cbd_base_path() ?>/blog/<?= htmlspecialchars($p['slug']) ?>" target="_blank" style="color:#1e1e5c;font-weight:600"><?= htmlspecialchars($p['title']) ?></a></td>
    <td><?= htmlspecialchars($p['category']) ?></td>
    <td><span class="badge" style="background:<?= $p['status']==='published'?'#dcfce7':'#f3f4f6' ?>;color:<?= $p['status']==='published'?'#16a34a':'#6b7280' ?>"><?= $p['status'] ?></span></td>
    <td class="status-cell" id="faq-<?= $p['id'] ?>">
        <?php if (has_faq($p['content'])): ?>
        <span class="badge badge-ok">✓ Has FAQ</span>
        <?php else: ?>
        <span class="badge badge-miss">✗ Missing</span>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<script>
const CSRF = <?= json_encode($token) ?>;
const POSTS = <?= json_encode(array_map(fn($p) => ['id'=>$p['id'],'title'=>mb_substr($p['title'],0,60),'has_faq'=>has_faq($p['content'])], $posts)) ?>;

async function generateAll() {
    const btn = document.getElementById('generate-btn');
    btn.disabled = true;
    btn.textContent = '⏳ Generating…';

    const log = document.getElementById('log');
    const bar = document.getElementById('progress-bar');
    const fill = document.getElementById('progress-fill');
    log.style.display = 'block';
    bar.style.display = 'block';
    log.innerHTML = '';

    const missing = POSTS.filter(p => !p.has_faq);
    let done = 0, errors = 0, skipped = 0;

    for (const post of missing) {
        addLog('info', `⚙ Processing: ${post.title}…`);
        const fd = new FormData();
        fd.append('action', 'generate_one');
        fd.append('post_id', post.id);
        fd.append('csrf_token', CSRF);

        try {
            const res  = await fetch('faq-generator.php', {method:'POST', body:fd});
            const data = await res.json();

            if (data.skipped) {
                skipped++;
                addLog('skip', `⏭ Skipped: ${post.title} — ${data.msg}`);
            } else if (data.ok) {
                done++;
                document.getElementById('faq-'+post.id).innerHTML = '<span class="badge badge-ok">✓ Has FAQ</span>';
                addLog('ok', `✅ Done: ${post.title} — ${data.msg}`);
                // Update counters
                document.getElementById('missing-count').textContent = parseInt(document.getElementById('missing-count').textContent) - 1;
                document.getElementById('done-count').textContent   = parseInt(document.getElementById('done-count').textContent)   + 1;
            } else {
                errors++;
                addLog('err', `❌ Failed: ${post.title} — ${data.msg}`);
            }
        } catch(e) {
            errors++;
            addLog('err', `❌ Network error: ${e.message}`);
        }

        const pct = Math.round(((done+errors+skipped)/missing.length)*100);
        fill.style.width = pct + '%';

        // Small delay to avoid hammering APIs
        await new Promise(r => setTimeout(r, 800));
    }

    addLog('info', `\n🏁 Done — ${done} generated, ${skipped} skipped, ${errors} failed.`);
    btn.textContent = `✅ Finished (${done} generated)`;
    btn.disabled = false;
}

function addLog(type, msg) {
    const log = document.getElementById('log');
    const p = document.createElement('p');
    p.className = 'log-'+type;
    p.textContent = msg;
    log.appendChild(p);
    log.scrollTop = log.scrollHeight;
}
</script>
</body>
</html>
