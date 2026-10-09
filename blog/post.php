<?php
require_once dirname(__DIR__) . '/includes/http.php';
require_once dirname(__DIR__) . '/includes/seo.php';

$slug = trim(strtolower((string)($_GET['slug'] ?? '')));
if (cbd_is_direct_script_request('post.php')
    && preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
    cbd_redirect_path('/blog/' . rawurlencode($slug));
}

require_once __DIR__ . '/data.php';
if (!$_blog_pdo) {
    require dirname(__DIR__) . '/503.php';
    exit;
}

// Find post
$post = null;
foreach ($blog_posts as $p) {
    if ($p['slug'] === $slug) {
        $post = $p;
        break;
    }
}

// Increment only for normal browser GETs; crawler/preview traffic is excluded.
$view_user_agent = (string)($_SERVER['HTTP_USER_AGENT'] ?? '');
$is_crawler = $view_user_agent === '' || preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit|preview|monitor/i', $view_user_agent);
if ($post && $_blog_pdo instanceof PDO
    && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET'
    && !$is_crawler
    && !isset($_SESSION['admin_logged_in'])) {
    try {
        $_blog_pdo->prepare("UPDATE posts SET views = views + 1 WHERE id = ?")->execute([$post['id']]);
        $post['views'] = ($post['views'] ?? 0) + 1;
    } catch (Throwable $_ve) {}
}

// 404 if not found
if (!$post) {
    http_response_code(404);
    header('X-Robots-Tag: noindex, nofollow, noarchive');
    $page_title       = '404 — Post Not Found | Chulbul Design';
    $page_description = 'The blog post you are looking for could not be found.';
    $page_robots      = 'noindex, nofollow';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_description) ?>">
    <?php require_once dirname(__DIR__) . '/includes/header.php'; ?>
    <section class="py-32 text-center">
        <div class="max-w-lg mx-auto px-4">
            <i class="bi bi-file-earmark-x text-6xl text-gray-300 block mb-6"></i>
            <h1 class="text-3xl font-extrabold text-[#1e1e5c] mb-4">Post Not Found</h1>
            <p class="text-gray-500 mb-8">The blog post you are looking for does not exist or may have been moved.</p>
            <a href="<?= $base ?>/blog" class="inline-flex items-center gap-2 bg-[#EE483D] text-white px-6 py-3 rounded-xl font-bold hover:bg-red-600 transition">
                <i class="bi bi-arrow-left"></i> Back to Blog
            </a>
        </div>
    </section>
    <?php
    require_once dirname(__DIR__) . '/includes/footer.php';
    exit;
}

// Set meta from post
$page_title       = trim((string)$post['meta_title']) ?: (string)$post['title'];
$page_description = trim((string)$post['meta_desc']) ?: mb_substr(strip_tags((string)$post['excerpt']), 0, 155);
$page_canonical   = cbd_canonical_url('/blog/' . $post['slug']);
$page_styles[]    = '/assets/css/blog-sidebar.css';

// Post tags
$post_tags      = $post['tags'] ?? [];
$post_tag_slugs = array_column($post_tags, 'slug');

// Related posts — same tag first, then any
$related        = [];
$related_slugs  = [];
foreach ($blog_posts as $p) {
    if ($p['slug'] === $post['slug']) continue;
    $p_tag_slugs = array_column($p['tags'] ?? [], 'slug');
    if (array_intersect($post_tag_slugs, $p_tag_slugs)) {
        $related[]       = $p;
        $related_slugs[] = $p['slug'];
        if (count($related) >= 3) break;
    }
}
if (count($related) < 3) {
    foreach ($blog_posts as $p) {
        if ($p['slug'] === $post['slug'] || in_array($p['slug'], $related_slugs)) continue;
        $related[] = $p;
        if (count($related) >= 3) break;
    }
}

$date_fmt = date('d M Y, h:i A', strtotime($post['created_at'] ?? $post['date']));
$published_at = (string)($post['date'] ?: ($post['created_at'] ?? ''));
$modified_at = (string)(($post['updated_at'] ?? '') ?: $published_at);
$post['content'] = (string)cbd_normalize_schema_urls((string)$post['content']);
$schema_img = cbd_public_url('', '/assets/images/Fb_chulbuldesign.jpg');
if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $post['content'], $img_m)) {
    $schema_img = cbd_public_url($img_m[1], '/assets/images/Fb_chulbuldesign.jpg');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?= htmlspecialchars($page_title) ?></title>
<meta name="description" content="<?= htmlspecialchars($page_description) ?>">
<link rel="canonical" href="<?= htmlspecialchars($page_canonical) ?>">
<meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
<meta property="og:description" content="<?= htmlspecialchars($page_description) ?>">
<meta property="og:url" content="<?= htmlspecialchars($page_canonical) ?>">
<meta property="og:type" content="article">
<meta property="og:image" content="<?= htmlspecialchars($schema_img) ?>">
<meta property="article:published_time" content="<?= htmlspecialchars($published_at) ?>">
<meta property="article:modified_time" content="<?= htmlspecialchars($modified_at) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@chulbuldesign">
<meta name="twitter:image" content="<?= htmlspecialchars($schema_img) ?>">
<?php
$jf = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;

// Keywords
$kw_list = [];
if (!empty($post['keywords'])) {
    $kw_list = array_filter(array_map('trim', explode(',', $post['keywords'])));
}

// BlogPosting schema
$schema = [
    '@context'         => 'https://schema.org',
    '@type'            => 'BlogPosting',
    'headline'         => $post['title'],
    'description'      => $post['meta_desc'],
    'url'              => 'https://www.chulbuldesign.com/blog/' . $post['slug'],
    'datePublished'    => $published_at,
    'dateModified'     => $modified_at,
    'author'           => ['@type' => 'Organization', 'name' => 'Chulbul Design', 'url' => 'https://www.chulbuldesign.com'],
    'publisher'        => ['@type' => 'Organization', 'name' => 'Chulbul Design', 'url' => CBD_CANONICAL_ORIGIN, 'logo' => ['@type' => 'ImageObject', 'url' => CBD_CANONICAL_ORIGIN . '/assets/images/logo/chulbuldesign.svg']],
    'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => 'https://www.chulbuldesign.com/blog/' . $post['slug']],
    'keywords'         => !empty($kw_list) ? implode(', ', $kw_list) : $post['category'],
    'articleSection'   => $post['category'],
    'wordCount'        => str_word_count(strip_tags($post['content'])),
];
$schema['image'] = $schema_img;

// BreadcrumbList schema
$breadcrumb_schema = [
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://www.chulbuldesign.com'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Blog', 'item' => 'https://www.chulbuldesign.com/blog'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $post['title'], 'item' => 'https://www.chulbuldesign.com/blog/' . $post['slug']],
    ],
];

// FAQPage schema — auto-detect <h3> ending with ? followed by <p>
$faq_items = [];
preg_match_all('/<h3[^>]*>(.*?\?)<\/h3>\s*<p[^>]*>([\s\S]*?)<\/p>/i', $post['content'], $faq_matches, PREG_SET_ORDER);
foreach ($faq_matches as $faq) {
    $q = trim(strip_tags($faq[1]));
    $a = trim(strip_tags($faq[2]));
    if ($q && $a) {
        $faq_items[] = [
            '@type'          => 'Question',
            'name'           => $q,
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a],
        ];
    }
}
?>
<script type="application/ld+json"><?= json_encode($schema, $jf) ?></script>
<script type="application/ld+json"><?= json_encode($breadcrumb_schema, $jf) ?></script>
<?php if (!empty($faq_items)): ?>
<script type="application/ld+json"><?= json_encode(['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq_items], $jf) ?></script>
<?php endif; ?>
<?php if (!empty($kw_list)): ?>
<meta name="keywords" content="<?= htmlspecialchars(implode(', ', $kw_list)) ?>">
<?php endif; ?>
<?php require_once dirname(__DIR__) . '/includes/header.php'; ?>

<!-- Breadcrumb -->
<nav aria-label="Breadcrumb" class="bg-gray-50 border-b border-gray-100 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <ol class="flex flex-wrap items-center gap-1.5 text-sm text-gray-500">
            <li><a href="<?= $base ?>/" class="hover:text-[#EE483D] transition">Home</a></li>
            <li><i class="bi bi-chevron-right text-xs"></i></li>
            <li><a href="<?= $base ?>/blog" class="hover:text-[#EE483D] transition">Blog</a></li>
            <li><i class="bi bi-chevron-right text-xs"></i></li>
            <li class="text-[#49499A] font-medium truncate max-w-[200px] sm:max-w-none" aria-current="page">
                <?= htmlspecialchars($post['title']) ?>
            </li>
        </ol>
    </div>
</nav>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="blog-post-layout flex flex-col lg:flex-row gap-12">

        <!-- Article -->
        <article class="flex-1 min-w-0">

            <!-- Date + Read Time -->
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="text-sm text-gray-400 flex items-center gap-1.5">
                    <i class="bi bi-calendar3"></i> <?= $date_fmt ?>
                </span>
                <span class="text-sm text-gray-400 flex items-center gap-1.5">
                    <i class="bi bi-clock"></i> <?= htmlspecialchars($post['read_time']) ?>
                </span>
            </div>

            <!-- Tags — just above title -->
            <?php if (!empty($post_tags)): ?>
            <div class="flex flex-wrap gap-2 mb-5">
                <?php foreach ($post_tags as $tag): ?>
                <a href="<?= $base ?>/blog?tag=<?= urlencode($tag['slug']) ?>"
                   class="inline-flex items-center gap-1 text-xs font-bold px-3 py-1.5 rounded-full bg-[#f0f0ff] text-[#49499A] border border-[#49499A]/20 hover:bg-[#49499A] hover:text-white transition">
                    <i class="bi bi-hash" style="font-size:10px"></i><?= htmlspecialchars($tag['name']) ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Title -->
            <h1 class="font-extrabold text-[#1e1e5c] leading-tight mb-8" style="font-size:36px">
                <?= htmlspecialchars($post['title']) ?>
            </h1>

            <!-- Post Content -->
            <div class="prose-custom text-gray-700 leading-relaxed max-w-none">
<?php
$post_content = preg_replace_callback(
    '/\b(href|src)=(["\'])\/(?!\/)([^"\']*)\2/i',
    static fn(array $match): string => $match[1] . '=' . $match[2] . $base . '/' . $match[3] . $match[2],
    $post['content']
) ?: $post['content'];
?>
                <?= $post_content ?>
            </div>

            <!-- Tags / Share Row -->
            <div class="mt-10 pt-6 border-t border-gray-100 flex flex-wrap items-center justify-between gap-4">
                <div class="flex flex-wrap items-center gap-1.5">
                    <?php foreach ($post_tags as $tag): ?>
                    <a href="<?= $base ?>/blog?tag=<?= urlencode($tag['slug']) ?>"
                       class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-full bg-[#f0f0ff] text-[#49499A] border border-[#49499A]/20 hover:bg-[#49499A] hover:text-white transition">
                        <i class="bi bi-hash" style="font-size:9px"></i><?= htmlspecialchars($tag['name']) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
                <div class="flex items-center gap-3 text-gray-400 text-lg">
                    <span class="text-sm text-gray-500 mr-1">Share:</span>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($page_canonical) ?>"
                       target="_blank" rel="noopener noreferrer" aria-label="Share on Facebook"
                       class="hover:text-blue-600 transition"><i class="bi bi-facebook"></i></a>
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode($page_canonical) ?>&text=<?= urlencode($post['title']) ?>"
                       target="_blank" rel="noopener noreferrer" aria-label="Share on Twitter"
                       class="hover:text-sky-500 transition"><i class="bi bi-twitter-x"></i></a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode($page_canonical) ?>&title=<?= urlencode($post['title']) ?>"
                       target="_blank" rel="noopener noreferrer" aria-label="Share on LinkedIn"
                       class="hover:text-blue-700 transition"><i class="bi bi-linkedin"></i></a>
                    <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' ' . $page_canonical) ?>"
                       target="_blank" rel="noopener noreferrer" aria-label="Share on WhatsApp"
                       class="hover:text-green-500 transition"><i class="bi bi-whatsapp"></i></a>
                </div>
            </div>

        </article>

        <!-- Sidebar -->
        <aside class="w-full lg:w-80 xl:w-96 flex-shrink-0 blog-sidebar">

            <!-- CTA Card -->
            <div class="blog-sidebar-cta rounded-2xl p-6">
                <i class="blog-sidebar-cta-icon bi bi-rocket-takeoff-fill text-3xl block mb-3"></i>
                <h3 class="text-lg font-extrabold mb-2">Need Expert Help?</h3>
                <p class="text-sm mb-5 leading-relaxed">
                    From web design to SEO and digital marketing — Chulbul Design can help your business grow online.
                </p>
                <a href="<?= $base ?>/contact-us"
                   class="blog-cta-btn-primary block text-center py-2.5 rounded-xl font-bold text-sm transition mb-3">
                    Get a Free Quote
                </a>
                <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer"
                   class="blog-cta-btn-wa block text-center py-2.5 rounded-xl font-bold text-sm transition">
                    <i class="bi bi-whatsapp mr-1"></i> Chat on WhatsApp
                </a>
            </div>

            <!-- Related Posts -->
            <?php if (!empty($related)): ?>
            <div>
                <h3 class="text-lg font-extrabold text-[#1e1e5c] mb-4 flex items-center gap-2">
                    <i class="bi bi-layout-text-window-reverse text-[#EE483D]"></i> Related Posts
                </h3>
                <div class="space-y-4">
                    <?php foreach ($related as $r):
                        $r_tags = array_slice($r['tags'] ?? [], 0, 2);
                        $r_date = date('d M Y', strtotime($r['date']));
                    ?>
                    <a href="<?= $base ?>/blog/<?= htmlspecialchars($r['slug']) ?>"
                       class="block bg-gray-50 hover:bg-[#F4F4FF] border border-gray-100 hover:border-[#49499A]/20 rounded-xl p-4 transition">
                        <?php foreach ($r_tags as $rt): ?>
                        <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-[#f0f0ff] text-[#49499A]">
                            #<?= htmlspecialchars($rt['name']) ?>
                        </span>
                        <?php endforeach; ?>
                        <p class="text-sm font-semibold text-[#1e1e5c] mt-2 mb-1 leading-snug hover:text-[#EE483D] transition">
                            <?= htmlspecialchars($r['title']) ?>
                        </p>
                        <span class="text-xs text-gray-400"><i class="bi bi-calendar3 mr-1"></i><?= $r_date ?></span>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Services Quick Links -->
            <nav class="blog-services-card" aria-labelledby="blog-services-title">
                <div class="blog-services-heading">
                    <h3 id="blog-services-title">Our Services</h3>
                    <p>Websites, apps &amp; digital marketing.</p>
                </div>
                <ul class="blog-services-list">
                    <?php
                    $sidebar_services = [
                        ['web-design', 'bi-palette', 'Web Design', 'Custom, responsive websites'],
                        ['web-development', 'bi-code-slash', 'Web Development', 'Web apps & custom builds'],
                        ['ecommerce-development', 'bi-bag', 'E-Commerce Website', 'Stores, payments & checkout'],
                        ['wordpress-development', 'bi-wordpress', 'WordPress Development', 'Flexible, easy-to-manage sites'],
                        ['mobile-software-development', 'bi-phone', 'Mobile App Development', 'iOS & Android applications'],
                        ['seo-digital-marketing', 'bi-graph-up-arrow', 'Online Marketing', 'SEO & digital campaigns'],
                    ];
                    foreach ($sidebar_services as [$service_path, $service_icon, $service_label, $service_description]):
                    ?>
                    <li>
                        <a class="blog-service-link" href="<?= $base ?>/<?= htmlspecialchars($service_path) ?>">
                            <span class="blog-service-icon" aria-hidden="true"><i class="bi <?= htmlspecialchars($service_icon) ?>"></i></span>
                            <span class="blog-service-copy">
                                <span class="blog-service-name"><?= htmlspecialchars($service_label) ?></span>
                                <span class="blog-service-description"><?= htmlspecialchars($service_description) ?></span>
                            </span>
                            <i class="bi bi-arrow-right blog-service-arrow" aria-hidden="true"></i>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </nav>

        </aside>
    </div>
</div>

<section class="city-cta-section">
    <div class="city-cta-deco-1" aria-hidden="true"></div>
    <div class="city-cta-deco-2" aria-hidden="true"></div>
    <div class="city-cta-deco-3" aria-hidden="true"></div>
    <div class="city-cta-deco-4" aria-hidden="true"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 city-cta-inner">
        <div class="city-cta-heading-wrap">
            <span class="city-cta-badge"><i class="bi bi-lightning-charge-fill"></i> Let's Work Together</span>
        </div>
        <h2 class="city-cta-title">Ready to Grow Your <br><span class="city-cta-title-highlight">Business With Us?</span></h2>
        <p class="city-cta-desc">Free consultation — tell us about your project and we'll get back within 1 hour.</p>
        <div class="city-cta-stats">
            <div class="city-cta-stat"><i class="bi bi-patch-check-fill city-cta-stat-icon-green"></i> 500+ Projects Delivered</div>
            <div class="city-cta-stat"><i class="bi bi-star-fill city-cta-stat-icon-yellow"></i> 5★ Google Rating</div>
            <div class="city-cta-stat"><i class="bi bi-clock-fill city-cta-stat-icon-pink"></i> Reply within 1 Hour</div>
            <div class="city-cta-stat"><i class="bi bi-shield-fill-check city-cta-stat-icon-blue"></i> 10+ Years Experience</div>
        </div>
        <div class="city-cta-btns">
            <a href="<?= $base ?>/contact-us" class="city-cta-btn-primary"><i class="bi bi-envelope-fill"></i> Get a Free Quote</a>
            <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer" class="city-cta-btn-wa"><i class="bi bi-whatsapp"></i> WhatsApp Us</a>
        </div>
    </div>
</section>

<script>
(function(){
    var prose = document.querySelector('.prose-custom');
    if (!prose) return;

    // ── Auto Table of Contents (only for long articles: 4+ H2 sections) ──────
    (function(){
        if (prose.querySelector('.blog-toc')) return; // Avoid duplicate TOC if already present in content
        var h2s = prose.querySelectorAll('h2');
        if (h2s.length < 4) return;           // short article — no TOC

        var items = [];
        for (var i = 0; i < h2s.length; i++) {
            var text = (h2s[i].textContent || '').trim();
            if (!text) continue;
            if (!h2s[i].id) {
                h2s[i].id = 'sec-' + (i + 1) + '-' + text.toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '').slice(0, 40);
            }
            items.push({ id: h2s[i].id, text: text });
        }
        if (items.length < 4) return;

        var links = items.map(function (it) {
            return '<li><a href="#' + it.id + '">' + it.text + '</a></li>';
        }).join('');

        var toc = document.createElement('div');
        toc.className = 'blog-toc';
        toc.innerHTML =
            '<button type="button" class="blog-toc-head" aria-expanded="true">' +
                '<span><i class="bi bi-list-ul"></i> Table of Contents</span>' +
                '<i class="bi bi-chevron-up blog-toc-arrow"></i></button>' +
            '<ul class="blog-toc-list">' + links + '</ul>';

        prose.insertBefore(toc, h2s[0]);      // place before the first section

        var head  = toc.querySelector('.blog-toc-head');
        var list  = toc.querySelector('.blog-toc-list');
        var arrow = toc.querySelector('.blog-toc-arrow');
        head.addEventListener('click', function () {
            var open = list.style.display !== 'none';
            list.style.display = open ? 'none' : 'block';
            arrow.className = 'bi blog-toc-arrow ' + (open ? 'bi-chevron-down' : 'bi-chevron-up');
            head.setAttribute('aria-expanded', open ? 'false' : 'true');
        });
        var as = toc.querySelectorAll('a');
        for (var j = 0; j < as.length; j++) {
            as[j].addEventListener('click', function (e) {
                e.preventDefault();
                var t = document.getElementById(this.getAttribute('href').slice(1));
                if (t) window.scrollTo({ top: t.getBoundingClientRect().top + window.pageYOffset - 90, behavior: 'smooth' });
            });
        }
    })();

    // Find the FAQ H2
    var faqH2 = null;
    var headings = prose.querySelectorAll('h2');
    for (var hi = 0; hi < headings.length; hi++) {
        if (/frequently asked questions/i.test(headings[hi].textContent)) {
            faqH2 = headings[hi];
            break;
        }
    }
    if (!faqH2) return;

    // Build accordion wrapper, insert right after H2
    var accordion = document.createElement('div');
    accordion.className = 'faq-accordion';
    faqH2.parentNode.insertBefore(accordion, faqH2.nextSibling);

    // Walk siblings after H2, consume H3+P pairs into accordion items
    var sibling = accordion.nextSibling;
    while (sibling) {
        var next = sibling.nextSibling;
        if (sibling.nodeType === 1) {
            var tag = sibling.tagName.toUpperCase();
            // Stop if we hit another H1/H2
            if (tag === 'H1' || tag === 'H2') break;

            if (tag === 'H3' && sibling.textContent.trim().endsWith('?')) {
                var qEl = sibling;
                var aEl = null;
                // Find the next element sibling (skip text nodes)
                var ns = next;
                while (ns && ns.nodeType !== 1) ns = ns.nextSibling;
                if (ns && ns.tagName.toUpperCase() === 'P') {
                    aEl = ns;
                    next = aEl.nextSibling; // skip past the P
                }

                var item = document.createElement('div');
                item.className = 'faq-item';

                var btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'faq-btn';
                btn.setAttribute('aria-expanded', 'false');
                btn.innerHTML = '<span>' + qEl.innerHTML + '</span><i class="bi bi-chevron-down faq-chevron" aria-hidden="true"></i>';

                var answerDiv = document.createElement('div');
                answerDiv.className = 'faq-answer';
                if (aEl) answerDiv.innerHTML = aEl.outerHTML;

                btn.addEventListener('click', (function(b, a) {
                    return function() {
                        var open = b.getAttribute('aria-expanded') === 'true';
                        b.setAttribute('aria-expanded', open ? 'false' : 'true');
                        a.classList.toggle('open', !open);
                    };
                })(btn, answerDiv));

                item.appendChild(btn);
                item.appendChild(answerDiv);
                accordion.appendChild(item);

                qEl.parentNode.removeChild(qEl);
                if (aEl) aEl.parentNode.removeChild(aEl);
                sibling = next;
                continue;
            }
        }
        sibling = next;
    }
})();
</script>
<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>
