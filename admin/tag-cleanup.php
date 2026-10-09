<?php
require_once __DIR__ . '/config.php';
require_login();

$pdo = get_db();
if (!$pdo) { die('DB connection failed.'); }

// ── Messy-tag detector (conservative — only clearly bad patterns) ─────────────
function is_messy_tag(string $name): bool {
    $n = trim(mb_strtolower($name));
    if ($n === '') return false;
    $words = preg_split('/\s+/', $n);
    if (preg_match('/\b(vs|versus)\b/', $n)) return true;     // comparison phrase
    if (count($words) >= 3) return true;                      // 3+ words = too specific
    if (count($words) === 2) {
        // known-good 2-word categories — always KEEP
        $good_two = ['web design','web development','digital marketing','social media',
            'content marketing','ui ux','ui/ux design','ux design','ui design','app development',
            'mobile app','mobile apps','graphic design','brand identity','seo strategy','core seo'];
        if (in_array($n, $good_two)) return false;
        // 2-word ending in a generic suffix (e.g. "travel website") = messy
        if (preg_match('/^(website|development|design|company|services?|portal|page|platform|tool|tools|guide|tips)$/', $words[1])) return true;
    }
    if (mb_strlen($n) > 22) return true;                      // overly long
    return false;
}

// ── Backup table for restore ─────────────────────────────────────────────────
$pdo->exec("CREATE TABLE IF NOT EXISTS tag_cleanup_backups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tag_name VARCHAR(190),
    tag_slug VARCHAR(190),
    post_ids TEXT,
    deleted_at DATETIME
) CHARACTER SET utf8mb4");

$message = ''; $msg_type = 'info';

// ── DELETE selected tags (with backup) ───────────────────────────────────────
if (($_POST['action'] ?? '') === 'delete' && !empty($_POST['tag_ids'])) {
    verify_csrf();
    $ids = array_values(array_filter(array_map('intval', (array)$_POST['tag_ids'])));
    $bk  = $pdo->prepare("INSERT INTO tag_cleanup_backups (tag_name, tag_slug, post_ids, deleted_at) VALUES (?,?,?,NOW())");
    $del_pt = $pdo->prepare("DELETE FROM post_tags WHERE tag_id=?");
    $del_t  = $pdo->prepare("DELETE FROM tags WHERE id=?");
    $n = 0;
    foreach ($ids as $tid) {
        $t = $pdo->prepare("SELECT name, slug FROM tags WHERE id=? LIMIT 1");
        $t->execute([$tid]); $trow = $t->fetch();
        if (!$trow) continue;
        $pids = $pdo->prepare("SELECT post_id FROM post_tags WHERE tag_id=?");
        $pids->execute([$tid]); $post_ids = $pids->fetchAll(PDO::FETCH_COLUMN);
        $bk->execute([$trow['name'], $trow['slug'], json_encode($post_ids)]);   // backup
        $del_pt->execute([$tid]);
        $del_t->execute([$tid]);
        $n++;
    }
    $message  = "🗑️ {$n} messy tags deleted (backup saved — restore available).";
    $msg_type = 'success';
}

// ── RESTORE from backup ──────────────────────────────────────────────────────
if (($_POST['action'] ?? '') === 'restore') {
    verify_csrf();
    $backups = $pdo->query("SELECT * FROM tag_cleanup_backups")->fetchAll();
    $n = 0;
    foreach ($backups as $b) {
        // recreate tag if missing
        $ex = $pdo->prepare("SELECT id FROM tags WHERE slug=? OR name=? LIMIT 1");
        $ex->execute([$b['tag_slug'], $b['tag_name']]);
        $tid = $ex->fetchColumn();
        if (!$tid) {
            $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?,?)")->execute([$b['tag_name'], $b['tag_slug']]);
            $tid = (int)$pdo->lastInsertId();
        }
        foreach (json_decode($b['post_ids'] ?? '[]', true) ?: [] as $pid) {
            $pdo->prepare("INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?,?)")->execute([(int)$pid, $tid]);
        }
        $n++;
    }
    $pdo->exec("DELETE FROM tag_cleanup_backups");
    $message  = "↩️ Restored {$n} tags from backup.";
    $msg_type = 'warn';
}

// ── Load all tags + post counts ──────────────────────────────────────────────
$tags = $pdo->query("
    SELECT t.id, t.name, t.slug, COUNT(pt.post_id) AS post_count
    FROM tags t LEFT JOIN post_tags pt ON pt.tag_id = t.id
    GROUP BY t.id ORDER BY post_count DESC, t.name
")->fetchAll();

$messy = []; $good = [];
foreach ($tags as $t) { if (is_messy_tag($t['name'])) $messy[] = $t; else $good[] = $t; }
$has_backup = (int)$pdo->query("SELECT COUNT(*) FROM tag_cleanup_backups")->fetchColumn();
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tag Cleanup — Chulbul Design</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f5f5f7;color:#1d1d1f}
.wrap{max-width:980px;margin:0 auto;padding:32px 20px}
h1{font-size:26px;font-weight:700;margin-bottom:6px}
.sub{color:#666;font-size:14px;margin-bottom:22px}
.msg{padding:13px 18px;border-radius:10px;margin-bottom:20px;font-size:14px;font-weight:500}
.msg.success{background:#f0fff4;color:#276749;border:1px solid #9ae6b4}
.msg.warn{background:#fffaf0;color:#c05621;border:1px solid #fbd38d}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:22px}
.stat{background:#fff;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.08);text-align:center}
.stat .num{font-size:26px;font-weight:800}.stat .lbl{font-size:12px;color:#888;margin-top:3px}
.red .num{color:#e53e3e}.green .num{color:#38a169}.blue .num{color:#3182ce}
.card{background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);overflow:hidden;margin-bottom:22px}
.card-head{padding:15px 20px;border-bottom:1px solid #eee;font-size:15px;font-weight:600;display:flex;justify-content:space-between;align-items:center}
.row{display:flex;align-items:center;gap:12px;padding:10px 20px;border-bottom:1px solid #f3f3f3;font-size:14px}
.row:last-child{border-bottom:none}
.row:hover{background:#fafafa}
.chip{display:inline-block;padding:3px 11px;border-radius:20px;font-size:13px;font-weight:600;background:#f0f0f5;color:#49499A}
.chip.bad{background:#fff5f5;color:#c53030}
.count{margin-left:auto;font-size:12px;color:#999}
.btn{padding:11px 22px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:8px}
.btn-del{background:#e53e3e;color:#fff}.btn-del:hover{background:#c53030}
.btn-restore{background:#fff;color:#c05621;border:1.5px solid #fbd38d}
.actions{display:flex;gap:12px;margin-top:16px;flex-wrap:wrap}
.note{font-size:12px;color:#999;margin-top:14px;line-height:1.6}
.empty{padding:30px;text-align:center;color:#999}
input[type=checkbox]{width:17px;height:17px;accent-color:#e53e3e;cursor:pointer}
</style></head><body>
<div class="wrap">
    <h1>🏷️ Tag Cleanup</h1>
    <p class="sub">Messy / phrase-like tags hata ke SEO-friendly clean tags rakho. Delete se pehle backup ban jaata hai — restore available.</p>

    <?php if ($message): ?><div class="msg <?= $msg_type ?>"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <div class="stats">
        <div class="stat blue"><div class="num"><?= count($tags) ?></div><div class="lbl">Total Tags</div></div>
        <div class="stat red"><div class="num"><?= count($messy) ?></div><div class="lbl">Messy (suggested delete)</div></div>
        <div class="stat green"><div class="num"><?= count($good) ?></div><div class="lbl">Clean (keep)</div></div>
    </div>

    <form method="POST">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
        <div class="card">
            <div class="card-head">
                <span>🔴 Messy Tags — checked = delete</span>
                <label style="font-size:12px;font-weight:500;color:#666;cursor:pointer">
                    <input type="checkbox" id="selall" checked onclick="document.querySelectorAll('.mcb').forEach(c=>c.checked=this.checked)"> Select all
                </label>
            </div>
            <?php if (!$messy): ?>
                <div class="empty">✅ Koi messy tag nahi — sab clean hain!</div>
            <?php else: foreach ($messy as $t): ?>
                <label class="row">
                    <input type="checkbox" class="mcb" name="tag_ids[]" value="<?= $t['id'] ?>" checked>
                    <span class="chip bad">#<?= htmlspecialchars($t['name']) ?></span>
                    <span class="count"><?= $t['post_count'] ?> post<?= $t['post_count']==1?'':'s' ?></span>
                </label>
            <?php endforeach; endif; ?>
        </div>

        <?php if ($messy): ?>
        <div class="actions">
            <button type="submit" class="btn btn-del" onclick="return confirm('Selected messy tags delete karein? Backup ban jayega — restore kar sakte ho.')">
                🗑️ Delete Selected Messy Tags
            </button>
            <?php if ($has_backup): ?>
            <button type="submit" name="action" value="restore" class="btn btn-restore" formnovalidate onclick="return confirm('Deleted tags wapas laayein?')">↩️ Restore Last Deleted</button>
            <?php endif; ?>
        </div>
        <?php elseif ($has_backup): ?>
        <div class="actions"><button type="submit" name="action" value="restore" class="btn btn-restore" formnovalidate onclick="return confirm('Deleted tags wapas laayein?')">↩️ Restore Last Deleted</button></div>
        <?php endif; ?>
    </form>

    <div class="card" style="margin-top:22px">
        <div class="card-head"><span>🟢 Clean Tags — keep (SEO-friendly)</span></div>
        <?php if (!$good): ?><div class="empty">No clean tags yet.</div>
        <?php else: foreach ($good as $t): ?>
            <div class="row">
                <span class="chip">#<?= htmlspecialchars($t['name']) ?></span>
                <span class="count"><?= $t['post_count'] ?> post<?= $t['post_count']==1?'':'s' ?></span>
            </div>
        <?php endforeach; endif; ?>
    </div>

    <p class="note">
        💡 <strong>Messy</strong> = comparison phrases ("X vs Y"), 3+ word tags, ya niche+website combos ("Travel Website") — ye thin one-post pages banate hain (SEO ke liye bure).<br>
        Delete karne pe tag + uske post links backup ho jaate hain. Galti ho to "Restore" se sab wapas.<br>
        <?php if ($has_backup): ?>✅ Backup maujood hai — Restore available.<?php endif; ?>
    </p>
</div>
</body></html>
