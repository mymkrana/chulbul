<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/content-tools.php';
require_login();
set_time_limit(300);

$pdo = get_db();
if (!$pdo) { die('DB connection failed.'); }

// ── Ensure backup table exists ───────────────────────────────────────────────
$pdo->exec("CREATE TABLE IF NOT EXISTS blog_link_backups (
    post_id INT PRIMARY KEY,
    original_content LONGTEXT,
    backed_up_at DATETIME
) CHARACTER SET utf8mb4");

$action  = $_POST['action'] ?? 'preview';
$is_mutation = in_array($action, ['apply', 'restore'], true);
if ($is_mutation) {
    require_post_request();
    verify_csrf();
}
$message  = '';
$msg_type = 'info';

// ════════════════════════════════════════════════════════════════════════════
// APPLY — backup originals (first time) + re-link all posts
// ════════════════════════════════════════════════════════════════════════════
if ($action === 'apply') {
    try {
        $pdo->beginTransaction();
        $posts = $pdo->query("SELECT id, content FROM posts WHERE status='published' FOR UPDATE")->fetchAll();
        $backup_ins = $pdo->prepare("INSERT IGNORE INTO blog_link_backups (post_id, original_content, backed_up_at) VALUES (?,?,NOW())");
        $update     = $pdo->prepare("UPDATE posts SET content=? WHERE id=?");
        $changed = 0;
        foreach ($posts as $p) {
            $backup_ins->execute([$p['id'], $p['content']]);
            $fresh = cbd_inject_service_links(cbd_strip_internal_content_links($p['content']), $pdo);
            if ($fresh !== $p['content']) { $update->execute([$fresh, $p['id']]); $changed++; }
        }
        $pdo->commit();
        audit_log('relink_posts', 'post', null, "{$changed} published posts updated");
        $message  = "✅ Applied! {$changed} posts updated with new links. Original content safely backed up.";
        $msg_type = 'success';
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        cbd_log_error('relink_blogs.apply', $error->getMessage());
        $message  = 'Link update failed. No partial changes were saved.';
        $msg_type = 'error';
    }
    $action   = 'preview';
}

// ════════════════════════════════════════════════════════════════════════════
// RESTORE — revert every post to its backed-up original
// ════════════════════════════════════════════════════════════════════════════
if ($action === 'restore') {
    try {
        $pdo->beginTransaction();
        $backups = $pdo->query("SELECT post_id, original_content FROM blog_link_backups FOR UPDATE")->fetchAll();
        $restore = $pdo->prepare("UPDATE posts SET content=? WHERE id=?");
        $n = 0;
        foreach ($backups as $b) { $restore->execute([$b['original_content'], $b['post_id']]); $n++; }
        $pdo->commit();
        audit_log('restore_post_links', 'post', null, "{$n} posts restored");
        $message  = "↩️ Restored {$n} posts to their original content.";
        $msg_type = 'warn';
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        cbd_log_error('relink_blogs.restore', $error->getMessage());
        $message  = 'Restore failed. No partial changes were saved.';
        $msg_type = 'error';
    }
    $action   = 'preview';
}

// ════════════════════════════════════════════════════════════════════════════
// PREVIEW (default) — dry run, no DB write
// ════════════════════════════════════════════════════════════════════════════
$posts = $pdo->query("SELECT id, slug, title, content FROM posts WHERE status='published' ORDER BY created_at DESC, id DESC")->fetchAll();
$has_backup = (int)$pdo->query("SELECT COUNT(*) FROM blog_link_backups")->fetchColumn();

$report = [];
$total_current = 0; $total_new = 0; $posts_changed = 0;
foreach ($posts as $p) {
    $cur = cbd_count_internal_content_links($p['content']);
    $fresh = cbd_inject_service_links(cbd_strip_internal_content_links($p['content']), $pdo);
    $new = cbd_count_internal_content_links($fresh);
    $total_current += $cur; $total_new += $new;
    if ($fresh !== $p['content']) $posts_changed++;
    $report[] = ['title'=>$p['title'],'slug'=>$p['slug'],'cur'=>$cur,'new'=>$new,'changed'=>($fresh!==$p['content'])];
}
$base_url = cbd_base_path();
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Re-link Blogs — Chulbul Design</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f5f5f7;color:#1d1d1f}
.wrap{max-width:1100px;margin:0 auto;padding:32px 20px}
h1{font-size:26px;font-weight:700;margin-bottom:6px}
.sub{color:#666;font-size:14px;margin-bottom:24px}
.msg{padding:14px 18px;border-radius:10px;margin-bottom:20px;font-size:14px;font-weight:500}
.msg.success{background:#f0fff4;color:#276749;border:1px solid #9ae6b4}
.msg.warn{background:#fffaf0;color:#c05621;border:1px solid #fbd38d}
.msg.error{background:#fff5f5;color:#c53030;border:1px solid #feb2b2}
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:24px}
.stat{background:#fff;border-radius:12px;padding:18px;box-shadow:0 1px 3px rgba(0,0,0,.08);text-align:center}
.stat .num{font-size:28px;font-weight:800}.stat .lbl{font-size:12px;color:#888;margin-top:3px}
.blue .num{color:#3182ce}.green .num{color:#38a169}.orange .num{color:#dd6b20}
.actions{display:flex;gap:12px;margin-bottom:24px;flex-wrap:wrap}
.btn{padding:11px 22px;border-radius:10px;font-size:14px;font-weight:600;cursor:pointer;border:none;text-decoration:none;display:inline-flex;align-items:center;gap:8px}
.btn-apply{background:#EE483D;color:#fff}.btn-apply:hover{background:#c5392f}
.btn-restore{background:#fff;color:#c05621;border:1.5px solid #fbd38d}.btn-restore:hover{background:#fffaf0}
.btn-refresh{background:#fff;color:#3182ce;border:1.5px solid #bee3f8}
.card{background:#fff;border-radius:12px;box-shadow:0 1px 3px rgba(0,0,0,.08);overflow:hidden}
.card-head{padding:16px 20px;border-bottom:1px solid #eee;font-size:15px;font-weight:600}
table{width:100%;border-collapse:collapse}
th{background:#f9f9f9;padding:10px 14px;text-align:left;font-size:11px;font-weight:700;color:#888;text-transform:uppercase;letter-spacing:.5px;border-bottom:1px solid #eee}
td{padding:10px 14px;border-bottom:1px solid #f2f2f2;font-size:13px}
tr:last-child td{border-bottom:none}tr:hover td{background:#fafafa}
.tag{display:inline-block;padding:2px 9px;border-radius:20px;font-size:12px;font-weight:600}
.tag-up{background:#f0fff4;color:#276749}.tag-same{background:#f0f0f5;color:#888}
.arrow{color:#aaa;margin:0 4px}
.note{font-size:12px;color:#999;margin-top:14px;line-height:1.6}
</style>
</head><body>
<div class="wrap">
    <h1>🔗 Re-link All Blog Posts</h1>
    <p class="sub">Naye smart internal-link system se saare blog posts update karo. Pehle Preview dekho, phir Apply. Pasand na aaye to Restore.</p>

    <?php if ($message): ?><div class="msg <?= $msg_type ?>"><?= htmlspecialchars($message) ?></div><?php endif; ?>

    <div class="stats">
        <div class="stat blue"><div class="num"><?= count($posts) ?></div><div class="lbl">Total Posts</div></div>
        <div class="stat orange"><div class="num"><?= $posts_changed ?></div><div class="lbl">Will Change</div></div>
        <div class="stat blue"><div class="num"><?= $total_current ?></div><div class="lbl">Links Now</div></div>
        <div class="stat green"><div class="num"><?= $total_new ?></div><div class="lbl">Links After</div></div>
    </div>

    <div class="actions">
        <form method="POST" onsubmit="return confirm('Saare posts pe naye links lagayein? Original content backup ho jayega.')">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <input type="hidden" name="action" value="apply">
            <button type="submit" class="btn btn-apply">⚡ Apply New Links</button>
        </form>
        <?php if ($has_backup): ?>
        <form method="POST" onsubmit="return confirm('Saare posts ko original content pe wapas le jayein?')">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">
            <input type="hidden" name="action" value="restore">
            <button type="submit" class="btn btn-restore">↩️ Restore Original</button>
        </form>
        <?php endif; ?>
        <a href="relink-blogs.php" class="btn btn-refresh">🔄 Refresh Preview</a>
    </div>

    <div class="card">
        <div class="card-head">Preview — har post pe links (dry run, abhi kuch change nahi hua)</div>
        <table>
            <thead><tr><th>#</th><th>Post</th><th>Links Now</th><th></th><th>After</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($report as $i => $r): ?>
            <tr>
                <td style="color:#bbb;width:34px"><?= $i+1 ?></td>
                <td>
                    <div style="font-weight:500"><?= htmlspecialchars(mb_substr($r['title'],0,60)) ?></div>
                    <div style="font-size:12px;color:#999"><a href="<?= $base_url ?>/blog/<?= htmlspecialchars($r['slug']) ?>" target="_blank" style="color:#3182ce;text-decoration:none">/blog/<?= htmlspecialchars($r['slug']) ?></a></div>
                </td>
                <td style="color:#888"><?= $r['cur'] ?></td>
                <td><span class="arrow">→</span></td>
                <td style="font-weight:700;color:#38a169"><?= $r['new'] ?></td>
                <td>
                    <?php if ($r['changed']): ?><span class="tag tag-up">Update</span>
                    <?php else: ?><span class="tag tag-same">No change</span><?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <p class="note">
        💡 <strong>Kaise kaam karta hai:</strong> "Apply" sabse pehle har post ka original content ek backup table me save karta hai, phir purane internal links hata ke naye smart system se dobara lagata hai.
        Pasand na aaye to "Restore" se sab wapas original ho jayega.<br>
        <?php if ($has_backup): ?>✅ Backup maujood hai (<?= $has_backup ?> posts) — Restore available hai.
        <?php else: ?>ℹ️ Abhi koi backup nahi — pehli baar "Apply" karne pe ban jayega.<?php endif; ?>
    </p>
</div>
</body></html>
