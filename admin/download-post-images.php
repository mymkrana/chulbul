<?php
require_once __DIR__ . '/config.php';
require_login();
set_time_limit(600);
ini_set('memory_limit', '256M');

$pdo = get_db();
if (!$pdo) die('DB not connected.');

$upload_dir  = dirname(__DIR__) . '/assets/images/uploads/';
$upload_base = '/assets/images/uploads/';
$base_url    = cbd_site_base_url();

// ── Download a single image ────────────────────────────────────────────────────
function dl_one(string $src, string $uploadDir, string $uploadBase): string {
    if (empty($src) || strpos($src, 'http') !== 0) return '';
    if (preg_match('/gravatar|avatar|pixel|1x1|tracking|mailto/i', $src)) return '';

    $remote = cbd_safe_remote_get($src, 8 * 1024 * 1024, 20, 3);
    if (!$remote['ok'] || strlen($remote['body'] ?? '') < 1000) return '';
    $data = $remote['body'];
    $mime = (new finfo(FILEINFO_MIME_TYPE))->buffer($data) ?: '';
    $exts = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp', 'image/gif'=>'gif'];
    if (!isset($exts[$mime]) || @getimagesizefromstring($data) === false) return '';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) return '';

    $fname = 'dl-' . substr(hash('sha256', $src), 0, 16) . '-' . bin2hex(random_bytes(4)) . '.' . $exts[$mime];
    if (file_put_contents($uploadDir . $fname, $data, LOCK_EX)) {
        @chmod($uploadDir . $fname, 0644);
        return $uploadBase . $fname;
    }
    return '';
}

// ── Handle Apply ───────────────────────────────────────────────────────────────
$results = []; $action = $_POST['action'] ?? 'preview';

if ($action === 'run') {
    require_post_request();
    verify_csrf();
    $posts = $pdo->query("SELECT id, title, content FROM posts WHERE status='published' ORDER BY id DESC")->fetchAll();
    $url_cache = []; // original → local (avoid re-downloading same URL)

    foreach ($posts as $p) {
        preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $p['content'], $matches, PREG_SET_ORDER);
        $changed = false; $content = $p['content']; $post_downloaded = 0;

        foreach ($matches as $m) {
            $src = $m[1];
            // Already local — skip
            if (strpos($src, 'http') !== 0) continue;
            if (strpos($src, $base_url) === 0) continue;

            if (isset($url_cache[$src])) {
                $local = $url_cache[$src];
            } else {
                $local = dl_one($src, $upload_dir, $upload_base);
                $url_cache[$src] = $local;
            }
            if ($local) {
                $content = str_replace($src, $local, $content);
                $changed = true; $post_downloaded++;
            }
        }
        if ($changed) {
            $pdo->prepare("UPDATE posts SET content=? WHERE id=?")->execute([$content, $p['id']]);
        }
        $results[] = [
            'id'         => $p['id'],
            'title'      => $p['title'],
            'total_imgs' => count($matches),
            'downloaded' => $post_downloaded,
            'changed'    => $changed,
        ];
    }
}

// ── Preview scan ───────────────────────────────────────────────────────────────
if ($action === 'preview' || $action === 'run') {
    if ($action === 'preview') {
        $posts = $pdo->query("SELECT id, title, content FROM posts WHERE status='published' ORDER BY id DESC")->fetchAll();
        foreach ($posts as $p) {
            preg_match_all('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $p['content'], $m, PREG_SET_ORDER);
            $ext_count = 0;
            foreach ($m as $img) {
                $src = $img[1];
                if (strpos($src, 'http') !== 0) continue;
                if (strpos($src, $base_url) === 0) continue;
                $ext_count++;
            }
            if ($ext_count > 0 || count($m) > 0)
                $results[] = ['id'=>$p['id'],'title'=>$p['title'],'total_imgs'=>count($m),'ext'=>$ext_count];
        }
    }
}

$total_posts  = count($results);
$total_imgs   = array_sum(array_column($results, 'total_imgs'));
$total_ext    = $action === 'preview' ? array_sum(array_column($results, 'ext')) : array_sum(array_column($results, 'downloaded'));
$changed_posts= $action === 'run' ? count(array_filter($results, fn($r) => $r['changed'])) : 0;
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Download Post Images</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f5f5f7;color:#1d1d1f}
.wrap{max-width:1000px;margin:0 auto;padding:32px 20px}
h1{font-size:24px;font-weight:700;margin-bottom:6px}
.sub{color:#666;font-size:14px;margin-bottom:22px}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:22px}
.stat{background:#fff;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.08);text-align:center}
.stat .num{font-size:26px;font-weight:800}.stat .lbl{font-size:12px;color:#888;margin-top:3px}
.blue .num{color:#3182ce}.green .num{color:#38a169}.orange .num{color:#dd6b20}.red .num{color:#e53e3e}
.actions{display:flex;gap:12px;margin-bottom:22px}
.btn{padding:11px 24px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:8px}
.btn-run{background:#EE483D;color:#fff}.btn-run:hover{background:#c5392f}
.btn-pre{background:#fff;color:#3182ce;border:1.5px solid #bee3f8}
.msg{padding:14px 18px;border-radius:10px;margin-bottom:18px;font-size:14px;font-weight:500;background:#f0fff4;color:#276749;border:1px solid #9ae6b4}
.card{background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);overflow:hidden}
.card-head{padding:14px 18px;border-bottom:1px solid #eee;font-size:15px;font-weight:600}
table{width:100%;border-collapse:collapse}
th{background:#f9f9f9;padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;border-bottom:1px solid #eee}
td{padding:10px 14px;border-bottom:1px solid #f3f3f3;font-size:13px}
tr:last-child td{border-bottom:none}tr:hover td{background:#fafafa}
.tag{display:inline-block;padding:2px 9px;border-radius:20px;font-size:12px;font-weight:600}
.tag-ok{background:#f0fff4;color:#276749}.tag-skip{background:#f0f0f5;color:#888}.tag-dl{background:#ebf8ff;color:#2b6cb0}
.note{font-size:12px;color:#999;margin-top:14px;line-height:1.6}
</style></head><body>
<div class="wrap">
<h1>📥 Post Images Download</h1>
<p class="sub">Saare blog posts ke external raster images ko safely download karke local server pe save karo.</p>

<?php if ($action === 'run'): ?>
<div class="msg">✅ Done! <?= $changed_posts ?> posts updated — <?= $total_ext ?> images downloaded and localized.</div>
<?php endif; ?>

<div class="stats">
    <div class="stat blue"><div class="num"><?= $total_posts ?></div><div class="lbl">Posts Scanned</div></div>
    <div class="stat orange"><div class="num"><?= $total_imgs ?></div><div class="lbl">Total Images</div></div>
    <div class="stat red"><div class="num"><?= $total_ext ?></div><div class="lbl"><?= $action==='run'?'Downloaded':'External (to download)' ?></div></div>
    <div class="stat green"><div class="num"><?= $action==='run'?$changed_posts:($total_posts - count(array_filter($results,fn($r)=>($r['ext']??0)===0))) ?></div><div class="lbl"><?= $action==='run'?'Posts Updated':'Posts Affected' ?></div></div>
</div>

<?php if ($action === 'preview'): ?>
<div class="actions">
    <form method="POST" onsubmit="return confirm('Saare external images download karein? (~2-5 min lag sakta hai)')">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <input type="hidden" name="action" value="run">
        <button type="submit" class="btn btn-run">⬇️ Download All Images Now</button>
    </form>
    <a href="download-post-images.php" class="btn btn-pre">🔄 Refresh</a>
</div>
<?php else: ?>
<div class="actions"><a href="download-post-images.php" class="btn btn-pre">🔄 Refresh Preview</a></div>
<?php endif; ?>

<div class="card">
    <div class="card-head"><?= $action==='run'?'Results':'Preview — posts with external images' ?></div>
    <table>
        <thead><tr>
            <th>#</th><th>Post</th><th>Total Imgs</th>
            <th><?= $action==='run'?'Downloaded':'External' ?></th><th>Status</th>
        </tr></thead>
        <tbody>
        <?php foreach ($results as $i => $r):
            $ext_n   = $action==='run' ? $r['downloaded'] : ($r['ext'] ?? 0);
            $changed = $action==='run' ? $r['changed'] : ($ext_n > 0);
        ?>
        <tr>
            <td style="color:#bbb"><?= $i+1 ?></td>
            <td style="max-width:360px"><?= htmlspecialchars(mb_substr($r['title'],0,60)) ?></td>
            <td style="color:#888"><?= $r['total_imgs'] ?></td>
            <td style="font-weight:700;color:<?= $ext_n>0?'#3182ce':'#aaa' ?>"><?= $ext_n ?></td>
            <td>
                <?php if ($action==='run'): ?>
                    <?= $r['changed']
                        ? '<span class="tag tag-dl">✅ Updated</span>'
                        : '<span class="tag tag-skip">—</span>' ?>
                <?php else: ?>
                    <?= $ext_n > 0
                        ? '<span class="tag tag-dl">⬇️ Will download</span>'
                        : '<span class="tag tag-skip">All local</span>' ?>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<p class="note">
    💡 Sirf <strong>external</strong> image URLs (https://...) download hoti hain — already-local images skip hoti hain.<br>
    Download hone ke baad DB mein URL replace ho jaata hai → image local server pe serve hogi (fast + always available).<br>
    Supported: JPG, PNG, GIF and WEBP. SVG and executable content are rejected. Min size 1KB.
</p>
</div></body></html>
