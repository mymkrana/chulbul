<?php
require_once dirname(__DIR__) . '/includes/http.php';

// A direct /blog/index.php request is a duplicate of the canonical /blog URL.
if (cbd_is_direct_script_request('index.php')) {
    $target = cbd_base_path() . '/blog';
    $requestedTag = trim((string)($_GET['tag'] ?? ''));
    if ($requestedTag !== '' && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $requestedTag)) {
        $target .= '?tag=' . rawurlencode($requestedTag);
    }
    header('Location: ' . $target, true, 301);
    exit;
}

require_once __DIR__ . '/data.php';
if (!$_blog_pdo) {
    require dirname(__DIR__) . '/503.php';
    exit;
}

// Extract thumbnail from content for each post
foreach ($blog_posts as &$_bp_idx) {
    $_bp_idx['thumb'] = '';
    if (!empty($_bp_idx['content']) && preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $_bp_idx['content'], $_bm)) {
        $_bp_idx['thumb'] = $_bm[1];
    }
}
unset($_bp_idx);

// Tag filter
$filter_tag  = trim($_GET['tag'] ?? '');
$filter_name = '';
$display_posts = $blog_posts;

if ($filter_tag) {
    $display_posts = array_filter($blog_posts, function($p) use ($filter_tag) {
        foreach ($p['tags'] ?? [] as $t) {
            if ($t['slug'] === $filter_tag) return true;
        }
        return false;
    });
    foreach ($blog_posts as $p) {
        foreach ($p['tags'] ?? [] as $t) {
            if ($t['slug'] === $filter_tag) { $filter_name = $t['name']; break 2; }
        }
    }
}

$page_title       = $filter_name
    ? "#{$filter_name} Posts — Chulbul Design Blog"
    : 'Blog — Web Design, SEO & Digital Marketing Tips | Chulbul Design';
$page_description = 'Read the latest insights on web design, SEO, e-commerce, WordPress, and digital marketing from the Chulbul Design team. Tips and guides for Indian businesses.';
$page_canonical   = 'https://www.chulbuldesign.com/blog/';
$page_robots      = $filter_tag ? 'noindex, follow' : 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?= htmlspecialchars($page_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($page_description) ?>">
<link rel="canonical" href="<?= $page_canonical ?>">
<meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($page_description) ?>">
<meta property="og:url" content="<?= $page_canonical ?>">
<meta property="og:type" content="website">
<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<!-- Hero Section -->
<section style="position:relative;overflow:hidden;background:linear-gradient(135deg,#1e1e5c 0%,#2d2d8a 50%,#49499A 100%);padding:72px 0 64px;">
    <div style="position:absolute;top:-60px;right:-60px;width:300px;height:300px;background:rgba(238,72,61,.08);border-radius:50%;pointer-events:none;"></div>
    <div style="position:absolute;bottom:-80px;left:-40px;width:220px;height:220px;background:rgba(255,255,255,.04);border-radius:50%;pointer-events:none;"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="position:relative;text-align:center;">
        <div style="display:inline-flex;align-items:center;gap:8px;background:rgba(238,72,61,.15);border:1px solid rgba(238,72,61,.3);color:#ff8a75;font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;padding:6px 16px;border-radius:999px;margin-bottom:20px;">
            <i class="bi bi-journal-richtext"></i> Our Blog
        </div>
        <h1 style="color:#fff;font-size:clamp(1.8rem,4vw,3rem);font-weight:800;letter-spacing:-0.03em;line-height:1.2;margin:0 0 16px;">
            Web Design, SEO &amp;<br>
            <span style="background:linear-gradient(90deg,#EE483D,#ff8a65);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Digital Marketing Insights</span>
        </h1>
        <p style="color:rgba(255,255,255,.65);font-size:1rem;max-width:540px;margin:0 auto 32px;line-height:1.7;">
            Practical tips, platform comparisons &amp; marketing strategies — written by the Chulbul Design team to help Indian businesses grow online.
        </p>
        <div style="display:inline-flex;flex-wrap:wrap;justify-content:center;gap:10px;">
            <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:10px;padding:10px 18px;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-file-earmark-text-fill" style="color:#EE483D;font-size:1rem;"></i>
                <span style="color:#fff;font-weight:700;font-size:.95rem;"><?= count($blog_posts) ?> Articles</span>
            </div>
            <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:10px;padding:10px 18px;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-tags-fill" style="color:#a5b4fc;font-size:1rem;"></i>
                <span style="color:#fff;font-weight:700;font-size:.95rem;">7 Categories</span>
            </div>
            <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:10px;padding:10px 18px;display:flex;align-items:center;gap:8px;">
                <i class="bi bi-clock-fill" style="color:#4ade80;font-size:1rem;"></i>
                <span style="color:#fff;font-weight:700;font-size:.95rem;">Weekly Updates</span>
            </div>
        </div>
    </div>
    <div style="position:absolute;bottom:-1px;left:0;right:0;line-height:0;">
        <svg viewBox="0 0 1440 40" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none" style="width:100%;height:40px;display:block;">
            <path d="M0,20 C360,40 1080,0 1440,20 L1440,40 L0,40 Z" fill="#ffffff"/>
        </svg>
    </div>
</section>

<!-- Blog Grid -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Tag filter banner -->
        <?php if ($filter_name): ?>
        <div class="mb-8 flex items-center gap-3 flex-wrap">
            <span class="inline-flex items-center gap-2 bg-[#f0f0ff] text-[#49499A] border border-[#49499A]/20 px-4 py-2 rounded-full font-bold text-sm">
                <i class="bi bi-hash"></i><?= htmlspecialchars($filter_name) ?>
                <span class="text-[#49499A]/60"><?= count($display_posts) ?> posts</span>
            </span>
            <a href="<?= $base ?>/blog" class="text-sm text-gray-400 hover:text-[#EE483D] transition flex items-center gap-1">
                <i class="bi bi-x-circle"></i> Clear filter
            </a>
        </div>
        <?php endif; ?>

        <?php if (empty($display_posts)): ?>
        <div style="text-align:center;padding:80px 0;color:#9ca3af;">
            <i class="bi bi-journal-x" style="font-size:3rem;display:block;margin-bottom:16px;"></i>
            <p style="font-size:1.1rem;font-weight:600;">No posts found for <strong>#<?= htmlspecialchars($filter_name) ?></strong></p>
            <a href="<?= $base ?>/blog" style="margin-top:16px;display:inline-block;color:#49499A;font-weight:600;">← View all posts</a>
        </div>
        <?php else: ?>

        <!-- Blog Cards Grid -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.75rem;" class="blog-cards-grid">
            <?php
            usort($display_posts, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));
            foreach ($display_posts as $post):
                $date_fmt  = $post['date'] ? date('d M Y', strtotime($post['date'])) : '';
                $post_tags = array_slice($post['tags'] ?? [], 0, 3);
            ?>
            <a href="<?= $base ?>/blog/<?= htmlspecialchars($post['slug']) ?>"
               style="display:flex;flex-direction:column;border-radius:1.25rem;overflow:hidden;border:1px solid #f3f4f6;box-shadow:0 1px 4px rgba(0,0,0,.06);background:#fff;text-decoration:none;transition:transform .25s,box-shadow .25s;"
               onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(0,0,0,.10)'"
               onmouseout="this.style.transform='';this.style.boxShadow='0 1px 4px rgba(0,0,0,.06)'">

                <!-- Thumbnail -->
                <div style="width:100%;height:196px;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
                    <?php if (!empty($post['thumb'])): ?>
                    <img src="<?= htmlspecialchars($post['thumb']) ?>"
                         alt="<?= htmlspecialchars($post['title']) ?>"
                         style="width:100%;height:100%;object-fit:cover;display:block;"
                         loading="lazy">
                    <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,rgba(73,73,154,.08),rgba(238,72,61,.08));">
                        <i class="bi bi-journal-richtext" style="font-size:3rem;color:#49499A;opacity:.3;"></i>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Content -->
                <div style="padding:1.2rem 1.4rem;display:flex;flex-direction:column;flex:1;">

                    <!-- Tags row -->
                    <?php if (!empty($post_tags)): ?>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:10px;">
                        <?php foreach ($post_tags as $tag): ?>
                        <span onclick="event.preventDefault();event.stopPropagation();window.location='<?= $base ?>/blog?tag=<?= urlencode($tag['slug']) ?>'"
                              style="font-size:11px;font-weight:700;padding:3px 10px;border-radius:999px;background:#f0f0ff;color:#49499A;cursor:pointer;">
                            #<?= htmlspecialchars($tag['name']) ?>
                        </span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Title — max 2 lines -->
                    <h2 style="font-size:15px;font-weight:800;color:#1e1e5c;line-height:1.45;margin:0 0 8px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                        <?= htmlspecialchars($post['title']) ?>
                    </h2>

                    <!-- Excerpt — max 3 lines -->
                    <p style="font-size:13px;color:#6b7280;line-height:1.6;margin:0;flex:1;overflow:hidden;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;">
                        <?= htmlspecialchars($post['excerpt']) ?>
                    </p>

                    <!-- Footer -->
                    <div style="margin-top:14px;padding-top:12px;border-top:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between;">
                        <div style="display:flex;gap:12px;font-size:11px;color:#9ca3af;">
                            <?php if ($date_fmt): ?>
                            <span><i class="bi bi-calendar3" style="margin-right:3px;"></i><?= $date_fmt ?></span>
                            <?php endif; ?>
                            <?php if (!empty($post['read_time'])): ?>
                            <span><i class="bi bi-clock" style="margin-right:3px;"></i><?= htmlspecialchars($post['read_time']) ?></span>
                            <?php endif; ?>
                        </div>
                        <span style="font-size:12px;font-weight:700;color:#49499A;display:flex;align-items:center;gap:4px;">
                            Read More <i class="bi bi-arrow-right"></i>
                        </span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- CTA Section -->
<section style="position:relative;overflow:hidden;background:linear-gradient(135deg,#1e1e5c 0%,#2d2d8a 55%,#49499A 100%);padding:80px 0;">
    <div style="position:absolute;top:-80px;right:-80px;width:340px;height:340px;background:rgba(238,72,61,.07);border-radius:50%;pointer-events:none;"></div>
    <div style="position:absolute;bottom:-60px;left:-60px;width:260px;height:260px;background:rgba(255,255,255,.04);border-radius:50%;pointer-events:none;"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6" style="position:relative;text-align:center;">
        <div style="margin-bottom:24px;">
            <span style="display:inline-flex;align-items:center;gap:7px;background:rgba(238,72,61,.15);border:1px solid rgba(238,72,61,.3);color:#ff8a75;font-size:.72rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;padding:6px 16px;border-radius:999px;">
                <i class="bi bi-lightning-charge-fill"></i> Let's Work Together
            </span>
        </div>
        <h2 style="color:#fff;font-size:clamp(1.6rem,3.5vw,2.4rem);font-weight:800;letter-spacing:-0.03em;line-height:1.25;margin:0 auto 16px;max-width:680px;">
            Need a Website, SEO Help, or<br>
            <span style="background:linear-gradient(90deg,#EE483D,#ff8a65);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Digital Marketing Strategy?</span>
        </h2>
        <p style="color:rgba(255,255,255,.6);font-size:1rem;max-width:520px;margin:0 auto 36px;line-height:1.7;">
            Chulbul Design has helped businesses across India and internationally grow their online presence. Let us help you next.
        </p>
        <div style="display:flex;flex-wrap:wrap;justify-content:center;gap:14px;">
            <a href="<?= $base ?>/contact-us"
               style="display:inline-flex;align-items:center;gap:8px;background:#EE483D;color:#fff;padding:14px 32px;border-radius:14px;font-weight:700;font-size:.95rem;text-decoration:none;box-shadow:0 8px 24px rgba(238,72,61,.35);">
                <i class="bi bi-envelope-fill"></i> Get a Free Quote
            </a>
            <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer"
               style="display:inline-flex;align-items:center;gap:8px;background:rgba(34,197,94,.15);border:1.5px solid rgba(34,197,94,.4);color:#4ade80;padding:14px 32px;border-radius:14px;font-weight:700;font-size:.95rem;text-decoration:none;">
                <i class="bi bi-whatsapp"></i> WhatsApp Us
            </a>
        </div>
    </div>
</section>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
