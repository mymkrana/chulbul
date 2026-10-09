<?php
require_once 'config.php';
require_login();

$pdo = get_db();
$last_error = '';

// ── AI functions ──────────────────────────────────────────────────────────────
function mt_groq_call(string $model, string $prompt, float $temp): array {
    $body = json_encode(['model'=>$model,'messages'=>[['role'=>'user','content'=>$prompt]],'temperature'=>$temp,'max_tokens'=>200]);
    $ch = curl_init('https://api.groq.com/openai/v1/chat/completions');
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$body,CURLOPT_TIMEOUT=>30,CURLOPT_SSL_VERIFYPEER => true,CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.GROQ_API_KEY]]);
    $resp = curl_exec($ch); $cerr = curl_error($ch); curl_close($ch);
    if ($cerr) return ['text'=>'','error'=>"curl:$cerr",'rate_limit'=>false];
    $d = json_decode($resp,true);
    $rl = !empty($d['error']['code']) && str_contains($d['error']['code'],'rate_limit');
    return ['text'=>trim($d['choices'][0]['message']['content']??''),'error'=>$d['error']['message']??'','rate_limit'=>$rl];
}

function mt_gemini_call(string $prompt, float $temp): array {
    $body = json_encode(['contents'=>[['parts'=>[['text'=>$prompt]]]],'generationConfig'=>['temperature'=>$temp,'maxOutputTokens'=>200]]);
    foreach (['gemini-2.0-flash','gemini-1.5-flash-latest'] as $model) {
        $ch = curl_init('https://generativelanguage.googleapis.com/v1beta/models/'.$model.':generateContent?key='.GEMINI_API_KEY);
        curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$body,CURLOPT_TIMEOUT=>30,CURLOPT_SSL_VERIFYPEER => true,CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,CURLOPT_HTTPHEADER=>['Content-Type: application/json','x-goog-api-key: '.GEMINI_API_KEY]]);
        $resp = curl_exec($ch); curl_close($ch);
        $d = json_decode($resp,true);
        $t = trim($d['candidates'][0]['content']['parts'][0]['text']??'');
        if ($t) return ['text'=>$t,'error'=>''];
    }
    return ['text'=>'','error'=>'Gemini failed'];
}

function mt_run_ai(string $prompt): string {
    global $last_error;
    $r = mt_groq_call('llama-3.3-70b-versatile',$prompt,0.7);
    if ($r['text']) return $r['text'];
    if ($r['rate_limit']) { sleep(12); $r2 = mt_groq_call('llama-3.3-70b-versatile',$prompt,0.7); if ($r2['text']) return $r2['text']; }
    $last_error = 'Groq: '.$r['error'];
    $rg = mt_gemini_call($prompt,0.7);
    if ($rg['text']) return $rg['text'];
    $last_error .= ' | Gemini: '.$rg['error'];
    $b = json_encode(['model'=>'deepseek/deepseek-chat-v3-0324:free','messages'=>[['role'=>'user','content'=>$prompt]],'temperature'=>0.7,'max_tokens'=>200]);
    $ch = curl_init('https://openrouter.ai/api/v1/chat/completions');
    curl_setopt_array($ch,[CURLOPT_RETURNTRANSFER=>true,CURLOPT_POST=>true,CURLOPT_POSTFIELDS=>$b,CURLOPT_TIMEOUT=>30,CURLOPT_SSL_VERIFYPEER => true,CURLOPT_IPRESOLVE=>CURL_IPRESOLVE_V4,CURLOPT_HTTPHEADER=>['Content-Type: application/json','Authorization: Bearer '.OPENROUTER_API_KEY,'HTTP-Referer: https://chulbuldesign.com']]);
    $resp = curl_exec($ch); curl_close($ch);
    $t = trim(json_decode($resp,true)['choices'][0]['message']['content']??'');
    if ($t) return $t;
    return '';
}

function is_weak_meta(string $meta_title, string $title): bool {
    $clean_meta = trim(str_ireplace(['| chulbul design','| chulbuldesign'], '', $meta_title));
    // Weak if: empty, same as title, too short (<35), or no power/location words
    if (empty($meta_title)) return true;
    if (mb_strlen($meta_title) < 35) return true;
    if (mb_strtolower(trim($clean_meta)) === mb_strtolower(trim($title))) return true;
    return false;
}

function meta_char_count(string $s): int { return mb_strlen($s); }

// ── Handle AJAX ───────────────────────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'generate_one') {
    verify_csrf();
    header('Content-Type: application/json');
    $id = (int)($_POST['post_id'] ?? 0);
    $force = !empty($_POST['force']);
    if (!$id) { echo json_encode(['ok'=>false,'msg'=>'No ID']); exit; }

    $row = $pdo->prepare("SELECT id,slug,title,meta_title,meta_desc,category,excerpt FROM posts WHERE id=?");
    $row->execute([$id]);
    $row = $row->fetch();
    if (!$row) { echo json_encode(['ok'=>false,'msg'=>'Not found']); exit; }

    if (!$force && !is_weak_meta($row['meta_title'], $row['title'])) {
        echo json_encode(['ok'=>true,'skipped'=>true,'msg'=>'Already strong','current'=>$row['meta_title']]);
        exit;
    }

    $excerpt = mb_substr(strip_tags($row['excerpt']),0,250);

    $prompt = <<<PROMPT
You are an expert SEO specialist writing meta titles for a web design & digital marketing agency blog (Chulbul Design, India).

Blog post details:
- Title: {$row['title']}
- Category: {$row['category']}
- Excerpt: {$excerpt}

Write ONE strong SEO meta title for this blog post.

Rules:
- Maximum 44 characters (we append " | Chulbul Design" = 18 chars, total must be under 62)
- Include the primary keyword naturally at the start
- Use power words where natural (Best, Complete, Guide, Ultimate, Top, How to, etc.)
- Add India/Indian context ONLY if relevant to the topic
- Must be click-worthy and clear — not clickbait
- NO quotes, NO punctuation at end, NO markdown
- Return ONLY the meta title text — nothing else, no explanation

Examples of good output:
WordPress vs Shopify: Which Is Better in 2026?
Top 10 SEO Tips for Small Businesses in India
How to Choose a Web Design Company in India
Complete Guide to Branding for Indian Businesses
PROMPT;

    $raw = mt_run_ai($prompt);
    if (!$raw) { echo json_encode(['ok'=>false,'msg'=>'AI failed: '.$last_error]); exit; }

    // Clean output — remove quotes, extra lines, markdown
    $new_meta = trim(preg_replace('/^["\'`*#\s]+|["\'`*#\s]+$/', '', explode("\n", $raw)[0]));
    $new_meta = preg_replace('/\s*\|.*$/', '', $new_meta); // strip any | Brand suffix AI added
    $new_meta = trim($new_meta);

    if (mb_strlen($new_meta) < 20) {
        echo json_encode(['ok'=>false,'msg'=>'Output too short: '.$new_meta]);
        exit;
    }

    // Append brand
    $final = $new_meta . ' | Chulbul Design';

    $pdo->prepare("UPDATE posts SET meta_title=?, updated_at=NOW() WHERE id=?")->execute([$final, $id]);

    echo json_encode([
        'ok'      => true,
        'skipped' => false,
        'msg'     => $final,
        'chars'   => mb_strlen($final),
        'old'     => $row['meta_title'],
    ]);
    exit;
}

// ── Load posts ────────────────────────────────────────────────────────────────
$posts = $pdo->query(
    "SELECT id,slug,title,meta_title,category,status FROM posts WHERE deleted_at IS NULL ORDER BY date DESC"
)->fetchAll();

$total  = count($posts);
$weak   = count(array_filter($posts, fn($p) => is_weak_meta($p['meta_title'], $p['title'])));
$strong = $total - $weak;
$token  = csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Meta Title Generator — Chulbul Admin</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
body{font-family:system-ui,sans-serif;background:#f8f9fa;margin:0;padding:24px;color:#333}
h1{font-size:1.4rem;font-weight:800;color:#1e1e5c;margin:0 0 4px}
.sub{color:#6b7280;font-size:.85rem;margin-bottom:24px}
.stats{display:flex;gap:16px;margin-bottom:20px;flex-wrap:wrap}
.stat{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:14px 20px;min-width:110px}
.stat-n{font-size:1.6rem;font-weight:800;color:#1e1e5c}
.stat-l{font-size:.72rem;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em}
.btns{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:16px}
.btn{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:10px;font-weight:700;font-size:.83rem;cursor:pointer;border:none;transition:.2s}
.btn-primary{background:#EE483D;color:#fff}.btn-primary:hover{background:#d43c31}
.btn-secondary{background:#1e1e5c;color:#fff}.btn-secondary:hover{background:#2d2d7a}
.btn:disabled{opacity:.5;cursor:not-allowed}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;font-size:.8rem}
th{background:#f8f9ff;padding:9px 12px;text-align:left;font-size:.68rem;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;border-bottom:1px solid #e5e7eb}
td{padding:9px 12px;border-bottom:1px solid #f1f5f9;vertical-align:top}
tr:last-child td{border-bottom:none}
.badge{display:inline-block;padding:2px 8px;border-radius:999px;font-size:.68rem;font-weight:700}
.badge-strong{background:#dcfce7;color:#16a34a}
.badge-weak{background:#fee2e2;color:#dc2626}
.meta-old{color:#9ca3af;font-size:.75rem;text-decoration:line-through;margin-top:2px}
.meta-new{color:#16a34a;font-weight:600;font-size:.78rem;margin-top:2px}
.chars{font-size:.68rem;color:#6b7280;margin-left:4px}
.chars.over{color:#dc2626}
#log{margin-top:16px;background:#1e1e5c;color:#a5f3fc;border-radius:12px;padding:16px;font-family:monospace;font-size:.75rem;max-height:260px;overflow-y:auto;display:none}
#log p{margin:2px 0}
.log-ok{color:#4ade80}.log-err{color:#f87171}.log-skip{color:#fbbf24}.log-info{color:#93c5fd}
#progress-bar{height:5px;background:#e5e7eb;border-radius:3px;margin:10px 0;display:none}
#progress-fill{height:5px;background:#EE483D;border-radius:3px;width:0;transition:width .3s}
</style>
</head>
<body>

<h1>🏷️ Meta Title Generator</h1>
<p class="sub">AI generates strong, SEO-optimized meta titles (keyword-rich, under 62 chars). Replaces weak/generic ones in the database.</p>

<div class="stats">
    <div class="stat"><div class="stat-n"><?= $total ?></div><div class="stat-l">Total Posts</div></div>
    <div class="stat"><div class="stat-n" id="weak-count" style="color:#dc2626"><?= $weak ?></div><div class="stat-l">Weak / Generic</div></div>
    <div class="stat"><div class="stat-n" id="strong-count" style="color:#16a34a"><?= $strong ?></div><div class="stat-l">Already Strong</div></div>
</div>

<div class="btns">
    <button class="btn btn-primary" id="btn-weak" onclick="runGenerate(false)" <?= $weak===0?'disabled':'' ?>>
        ▶ Fix <?= $weak ?> Weak Meta Titles
    </button>
    <button class="btn btn-secondary" id="btn-all" onclick="runGenerate(true)">
        🔄 Regenerate All <?= $total ?> Posts
    </button>
</div>

<div id="progress-bar"><div id="progress-fill"></div></div>
<div id="log"></div>
<br>

<table>
<thead><tr><th>#</th><th>Title</th><th>Category</th><th>Current Meta Title</th><th>Status</th></tr></thead>
<tbody>
<?php foreach ($posts as $i => $p):
    $weak_flag = is_weak_meta($p['meta_title'], $p['title']);
    $chars = meta_char_count($p['meta_title']);
?>
<tr id="row-<?= $p['id'] ?>">
    <td><?= $i+1 ?></td>
    <td style="font-weight:600;color:#1e1e5c;max-width:220px"><?= htmlspecialchars($p['title']) ?></td>
    <td><?= htmlspecialchars($p['category']) ?></td>
    <td style="max-width:300px">
        <div id="meta-display-<?= $p['id'] ?>"><?= htmlspecialchars($p['meta_title'] ?: '—') ?></div>
        <span class="chars <?= $chars>62?'over':'' ?>" id="chars-<?= $p['id'] ?>"><?= $chars ?> chars</span>
    </td>
    <td id="status-<?= $p['id'] ?>">
        <span class="badge <?= $weak_flag?'badge-weak':'badge-strong' ?>">
            <?= $weak_flag ? '✗ Weak' : '✓ Strong' ?>
        </span>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<script>
const CSRF  = <?= json_encode($token) ?>;
const POSTS = <?= json_encode(array_map(fn($p) => [
    'id'    => $p['id'],
    'title' => mb_substr($p['title'],0,55),
    'weak'  => is_weak_meta($p['meta_title'], $p['title']),
], $posts)) ?>;

async function runGenerate(forceAll) {
    const btnW = document.getElementById('btn-weak');
    const btnA = document.getElementById('btn-all');
    btnW.disabled = true; btnA.disabled = true;
    btnW.textContent = '⏳ Running…'; btnA.textContent = '⏳ Running…';

    const log  = document.getElementById('log');
    const bar  = document.getElementById('progress-bar');
    const fill = document.getElementById('progress-fill');
    log.style.display = 'block'; bar.style.display = 'block';
    log.innerHTML = '';

    const targets = forceAll ? POSTS : POSTS.filter(p => p.weak);
    let done=0, errors=0, skipped=0;

    for (const post of targets) {
        addLog('info', `⚙ ${post.title}…`);
        const fd = new FormData();
        fd.append('action',     'generate_one');
        fd.append('post_id',    post.id);
        fd.append('csrf_token', CSRF);
        if (forceAll) fd.append('force', '1');

        try {
            const res  = await fetch('meta-title-generator.php', {method:'POST',body:fd});
            const data = await res.json();

            if (data.skipped) {
                skipped++;
                addLog('skip', `⏭ Skipped: ${post.title}`);
            } else if (data.ok) {
                done++;
                const chars = data.chars;
                document.getElementById('meta-display-'+post.id).textContent = data.msg;
                document.getElementById('chars-'+post.id).textContent = chars+' chars';
                document.getElementById('chars-'+post.id).className = 'chars'+(chars>62?' over':'');
                document.getElementById('status-'+post.id).innerHTML = '<span class="badge badge-strong">✓ Strong</span>';
                addLog('ok', `✅ ${data.msg} (${chars})`);
                // Update counter
                if (post.weak) {
                    document.getElementById('weak-count').textContent   = Math.max(0,parseInt(document.getElementById('weak-count').textContent)-1);
                    document.getElementById('strong-count').textContent = parseInt(document.getElementById('strong-count').textContent)+1;
                }
            } else {
                errors++;
                addLog('err', `❌ ${post.title} — ${data.msg}`);
            }
        } catch(e) {
            errors++;
            addLog('err', `❌ Network: ${e.message}`);
        }

        fill.style.width = Math.round(((done+errors+skipped)/targets.length)*100)+'%';
        await new Promise(r => setTimeout(r, 600));
    }

    addLog('info', `\n🏁 Done — ${done} updated, ${skipped} skipped, ${errors} failed.`);
    btnW.textContent = `✅ Done (${done} updated)`;
    btnA.textContent = `✅ Done (${done} updated)`;
    btnW.disabled = false; btnA.disabled = false;
}

function addLog(type, msg) {
    const log = document.getElementById('log');
    const p   = document.createElement('p');
    p.className = 'log-'+type;
    p.textContent = msg;
    log.appendChild(p);
    log.scrollTop = log.scrollHeight;
}
</script>
</body>
</html>
