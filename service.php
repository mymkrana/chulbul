<?php
require_once __DIR__ . '/includes/db-services.php';
require_once __DIR__ . '/includes/http.php';
require_once __DIR__ . '/includes/seo.php';

$slug = trim($_GET['slug'] ?? '');

if (preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)
    && cbd_is_direct_script_request('service.php')) {
    cbd_redirect_path('/' . rawurlencode($slug));
}

if (!$slug) {
    require __DIR__ . '/404.php';
    exit;
}

if (!$svc_pdo) {
    require __DIR__ . '/503.php';
    exit;
}

try {
    $stmt = $svc_pdo->prepare("SELECT * FROM services WHERE slug = ? AND status = 1 AND deleted_at IS NULL LIMIT 1");
    $stmt->execute([$slug]);
    $svc = $stmt->fetch();
} catch (Throwable $error) {
    error_log('Service repository unavailable: ' . $error->getMessage());
    require __DIR__ . '/503.php';
    exit;
}

if (!$svc) {
    require __DIR__ . '/404.php';
    exit;
}

// Decode JSON fields
$s1_bullets = json_decode($svc['s1_bullets'] ?? '[]', true) ?: [];
$s2_bullets = json_decode($svc['s2_bullets'] ?? '[]', true) ?: [];
$s3_bullets = json_decode($svc['s3_bullets'] ?? '[]', true) ?: [];
$cards      = json_decode($svc['cards']      ?? '[]', true) ?: [];
$steps      = json_decode($svc['steps']      ?? '[]', true) ?: [];
$faq        = json_decode($svc['faq']        ?? '[]', true) ?: [];

// Dynamic sections — use new sections JSON if present, else fall back to legacy s1/s2/s3
$sections = json_decode($svc['sections'] ?? '[]', true) ?: [];
if (empty($sections)) {
    foreach ([1,2,3] as $n) {
        if (!empty($svc["s{$n}_h2"]) || !empty($svc["s{$n}_para"])) {
            $sections[] = [
                'h2'      => $svc["s{$n}_h2"]      ?? '',
                'para'    => $svc["s{$n}_para"]     ?? '',
                'bullets' => json_decode($svc["s{$n}_bullets"] ?? '[]', true) ?: [],
                'img'     => $svc["s{$n}_img"]      ?? '',
                'img_alt' => $svc["s{$n}_img_alt"]  ?? '',
            ];
        }
    }
}

$base = cbd_base_path();
$canonical = cbd_canonical_url('/' . $svc['slug']);
$meta_title = trim((string)($svc['meta_title'] ?? '')) ?: ucwords(str_replace('-', ' ', $svc['slug'])) . ' | Chulbul Design';
$meta_desc = trim((string)($svc['meta_desc'] ?? '')) ?: mb_substr(strip_tags((string)($svc['intro_text'] ?? '')), 0, 155);
$meta_image = cbd_public_url(
    (string)($svc['meta_image'] ?: ($svc['hero_img'] ?? '')),
    '/assets/images/Fb_chulbuldesign.jpg'
);

$service_schema = json_decode((string)($svc['schema_json'] ?? ''), true);
if (!is_array($service_schema)) {
    $service_schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $meta_title,
        'description' => $meta_desc,
        'url' => $canonical,
        'provider' => ['@type' => 'Organization', 'name' => 'Chulbul Design', 'url' => CBD_CANONICAL_ORIGIN],
        'areaServed' => ['@type' => 'Country', 'name' => 'India'],
    ];
}
$service_schema = cbd_normalize_schema_urls($service_schema);
$breadcrumb_schema = null;
if (isset($service_schema['breadcrumb']) && is_array($service_schema['breadcrumb'])) {
    $breadcrumb_schema = $service_schema['breadcrumb'];
    unset($service_schema['breadcrumb']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title><?= htmlspecialchars($meta_title) ?></title>
<link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
<meta name="description" content="<?= htmlspecialchars($meta_desc) ?>">
<meta property="og:title"       content="<?= htmlspecialchars($meta_title) ?>">
<meta property="og:url"         content="<?= htmlspecialchars($canonical) ?>">
<meta property="og:type"        content="website">
<meta property="og:description" content="<?= htmlspecialchars($meta_desc) ?>">
<meta property="og:image"       content="<?= htmlspecialchars($meta_image) ?>">
<meta name="twitter:card"  content="summary_large_image">
<meta name="twitter:site"  content="@chulbuldesign">
<meta name="twitter:image" content="<?= htmlspecialchars($meta_image) ?>">
<script type="application/ld+json"><?= cbd_json_ld($service_schema) ?></script>
<?php if ($breadcrumb_schema): ?><script type="application/ld+json"><?= cbd_json_ld($breadcrumb_schema) ?></script><?php endif; ?>
<?php if (!empty($svc['hero_img'])): ?>
<link rel="preload" as="image" href="<?= $base . htmlspecialchars($svc['hero_img']) ?>" fetchpriority="high">
<?php endif; ?>
<?php require __DIR__ . '/includes/header.php'; ?>

<?php
$hero = [
    'badge'      => $svc['hero_badge']      ?? '',
    'breadcrumb' => $svc['hero_breadcrumb'] ?? '',
    'h1'         => $svc['hero_h1']         ?? '',
    'desc'       => $svc['hero_desc']       ?? '',
    'img'        => $svc['hero_img']        ?? '',
    'img_alt'    => $svc['hero_img_alt']    ?? '',
];
require __DIR__ . '/includes/page-hero.php';
?>

<!-- Intro -->
<?php if (!empty($svc['intro_text'])): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <p class="text-gray-600 text-center text-lg leading-relaxed max-w-4xl mx-auto">
        <?= $svc['intro_text'] ?>
    </p>
</section>
<?php endif; ?>

<!-- Dynamic Content Sections — alternating layout -->
<?php foreach ($sections as $si => $sec):
    $imgLeft = ($si % 2 === 0); // even index = image left; odd = image right
    $grayBg  = ($si % 2 === 1);
    $hasImg  = !empty($sec['img']);
    $bullets = is_array($sec['bullets']) ? $sec['bullets'] : [];
?>
<section class="<?= $grayBg ? 'bg-gray-50' : '' ?> py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex <?= $imgLeft ? 'flex-col' : 'flex-col-reverse' ?> lg:flex-row<?= !$imgLeft ? '' : '' ?> items-center gap-12">

            <?php if ($hasImg && $imgLeft): ?>
            <div class="flex-1 flex justify-center">
                <img src="<?= $base . htmlspecialchars($sec['img']) ?>" alt="<?= htmlspecialchars(trim(($sec['img_alt'] ?: ($sec['h2'] ?? '')) . ' — ' . ($svc['meta_title'] ?? ''))) ?>"
                     class="w-full max-w-md rounded-2xl shadow-sm" width="480" height="400" loading="lazy">
            </div>
            <?php endif; ?>

            <div class="flex-1">
                <h2 class="text-3xl font-extrabold text-[#EE483D] mb-4"><?= cbd_escape_text($sec['h2'] ?? '') ?></h2>
                <p class="text-gray-600 leading-relaxed mb-4"><?= nl2br(cbd_escape_text($sec['para'] ?? '')) ?></p>
                <?php if ($bullets): ?>
                <ul class="space-y-2 text-gray-600 text-sm">
                    <?php foreach ($bullets as $b): ?>
                    <li class="flex items-center gap-2">
                        <i class="bi bi-check-circle-fill text-[#EE483D] flex-shrink-0"></i>
                        <?= cbd_escape_text($b) ?>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>

            <?php if ($hasImg && !$imgLeft): ?>
            <div class="flex-1 flex justify-center">
                <img src="<?= $base . htmlspecialchars($sec['img']) ?>" alt="<?= htmlspecialchars(trim(($sec['img_alt'] ?: ($sec['h2'] ?? '')) . ' — ' . ($svc['meta_title'] ?? ''))) ?>"
                     class="w-full max-w-md rounded-2xl shadow-sm" width="480" height="400" loading="lazy">
            </div>
            <?php endif; ?>

        </div>
    </div>
</section>
<?php endforeach; ?>

<!-- Stats Bar -->
<?php if (!empty($svc['stat1_val'])): ?>
<section class="bg-[#1e1e5c] py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 text-center">
            <?php
            for ($i = 1; $i <= 4; $i++):
                $val   = $svc["stat{$i}_val"]   ?? '';
                $label = $svc["stat{$i}_label"] ?? '';
                if (!$val) continue;
            ?>
            <div>
                <p class="text-4xl font-extrabold text-[#EE483D] mb-1"><?= cbd_escape_text($val) ?></p>
                <p class="text-white/70 text-sm"><?= cbd_escape_text($label) ?></p>
            </div>
            <?php endfor; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Cards -->
<?php if ($cards): ?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <?php if (!empty($svc['cards_heading'])): ?>
        <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">What We Do</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-4 mb-3"><?= cbd_escape_text($svc['cards_heading']) ?></h2>
        <?php endif; ?>
        <?php if (!empty($svc['cards_subtitle'])): ?>
        <p class="text-gray-500 max-w-2xl mx-auto"><?= cbd_escape_text($svc['cards_subtitle']) ?></p>
        <?php endif; ?>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($cards as $card): ?>
        <div class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 transition-all group">
            <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-5 group-hover:scale-110 transition-transform"
                 style="background:<?= htmlspecialchars($card['color'] ?? '#EE483D') ?>1a">
                <i class="bi <?= htmlspecialchars($card['icon'] ?? '') ?> text-2xl"
                   style="color:<?= htmlspecialchars($card['color'] ?? '#EE483D') ?>"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-2"><?= cbd_escape_text($card['title'] ?? '') ?></h3>
            <p class="text-gray-500 text-sm leading-relaxed"><?= cbd_escape_text($card['desc'] ?? '') ?></p>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<!-- Process -->
<?php if ($steps): ?>
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Our Process</span>
            <h2 class="text-3xl font-extrabold text-gray-900 mt-4 mb-3"><?= cbd_escape_text($svc['process_heading'] ?? 'How We Work') ?></h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            <?php foreach ($steps as $step): ?>
            <div class="bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100">
                <div class="w-12 h-12 rounded-xl bg-[#EE483D] flex items-center justify-center mx-auto mb-3 relative">
                    <i class="bi <?= htmlspecialchars($step['icon'] ?? 'bi-check-lg') ?> text-white text-lg"></i>
                    <span class="absolute -top-2 -right-2 w-5 h-5 rounded-full bg-[#49499A] text-white text-xs font-bold flex items-center justify-center">
                        <?= cbd_escape_text($step['num'] ?? '') ?>
                    </span>
                </div>
                <h3 class="font-bold text-gray-900 mb-1"><?= cbd_escape_text($step['title'] ?? '') ?></h3>
                <p class="text-gray-500 text-xs leading-relaxed"><?= cbd_escape_text($step['desc'] ?? '') ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Related Services -->
<?php
$related = [];
if ($svc_pdo) {
    $rel_stmt = $svc_pdo->prepare(
        "SELECT slug, meta_title, hero_badge FROM services
         WHERE category=? AND slug!=? AND status=1 AND deleted_at IS NULL
         ORDER BY RAND() LIMIT 6"
    );
    $rel_stmt->execute([$svc['category'], $slug]);
    $related = $rel_stmt->fetchAll();
}
?>
<?php if ($related): ?>
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Explore More</span>
            <h2 class="text-3xl font-extrabold text-gray-900 mt-4 mb-2">Related Services</h2>
            <p class="text-gray-500 text-sm">More ways we can help your business grow</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($related as $rel): ?>
            <a href="<?= $base ?>/<?= htmlspecialchars($rel['slug']) ?>"
               class="group bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-200 p-6 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 group-hover:bg-[#EE483D] transition-colors duration-200">
                    <i class="bi bi-grid-1x2 text-[#EE483D] text-lg group-hover:text-white transition-colors duration-200"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <?php if (!empty($rel['hero_badge'])): ?>
                    <p class="text-xs font-semibold text-[#EE483D] uppercase tracking-wide mb-0.5"><?= cbd_escape_text($rel['hero_badge']) ?></p>
                    <?php endif; ?>
                    <p class="text-sm font-bold text-gray-800 group-hover:text-[#EE483D] transition-colors leading-snug line-clamp-2"><?= cbd_escape_text($rel['meta_title'] ?? '') ?></p>
                </div>
                <i class="bi bi-arrow-right text-gray-300 group-hover:text-[#EE483D] group-hover:translate-x-1 transition-all flex-shrink-0"></i>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- FAQ -->
<?php if ($faq): ?>
<section class="py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6">
        <div class="text-center mb-10">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">FAQ</span>
            <h2 class="text-3xl font-extrabold text-gray-900 mt-4">Frequently Asked Questions</h2>
        </div>
        <div class="space-y-3" id="faq-accordion">
            <?php foreach ($faq as $i => $item): ?>
            <div class="border border-gray-100 rounded-2xl overflow-hidden shadow-sm">
                <button onclick="toggleFaq(<?= $i ?>)"
                        class="w-full flex items-center justify-between px-6 py-4 text-left font-semibold text-gray-900 hover:bg-gray-50 transition text-sm gap-4"
                        id="faq-btn-<?= $i ?>">
                    <span><?= cbd_escape_text($item['q'] ?? '') ?></span>
                    <i class="bi bi-chevron-down text-[#EE483D] flex-shrink-0 transition-transform" id="faq-icon-<?= $i ?>"></i>
                </button>
                <div id="faq-ans-<?= $i ?>" class="hidden px-6 pb-5 text-gray-600 text-sm leading-relaxed border-t border-gray-50">
                    <div class="pt-4"><?= nl2br(cbd_escape_text($item['a'] ?? '')) ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<script>
function toggleFaq(i) {
    const ans  = document.getElementById('faq-ans-'  + i);
    const icon = document.getElementById('faq-icon-' + i);
    const open = !ans.classList.contains('hidden');
    // close all
    document.querySelectorAll('[id^="faq-ans-"]').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('[id^="faq-icon-"]').forEach(el => el.style.transform = '');
    if (!open) {
        ans.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
    }
}
</script>
<?php endif; ?>

<!-- CTA -->
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

<?php require __DIR__ . '/includes/footer.php'; ?>
