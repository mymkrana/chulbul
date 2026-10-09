<?php

require_once __DIR__ . '/http.php';
require_once __DIR__ . '/seo.php';

$pages = require __DIR__ . '/ui-branding-pages-data.php';
$slug = (string)($branding_slug ?? '');
$script = (string)($branding_script ?? '');

if (!isset($pages[$slug])) {
    require dirname(__DIR__) . '/404.php';
    exit;
}

if ($script !== '' && cbd_is_direct_script_request($script)) {
    cbd_redirect_path('/' . $slug);
}

$page = $pages[$slug];
$hubSlug = 'ui-ux-branding';
$isHub = $slug === $hubSlug;
$base = cbd_base_path();
$canonical = cbd_canonical_url('/' . $slug);
$shareImage = cbd_public_url('/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg');
$cbd_page_has_charset = true;
$page_styles = ['/assets/css/web-development.css'];
$page_robots = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';

$cards = array_map(static function (array $card): array {
    return [
        'icon' => (string)($card['icon'] ?? $card[0] ?? 'bi-code-square'),
        'title' => (string)($card['title'] ?? $card[1] ?? ''),
        'desc' => (string)($card['desc'] ?? $card[2] ?? ''),
        'href' => (string)($card['href'] ?? $card[3] ?? ''),
    ];
}, $page['cards'] ?? []);

$sections = array_values(array_filter($page['sections'] ?? [], 'is_array'));
$sectionParagraphs = static fn(array $section): array => preg_split('/\R{2,}/', trim((string)($section['para'] ?? ''))) ?: [];
$sectionImages = [
    '/assets/images/ux-vs-ui.webp',
    '/assets/images/service-heroes/web-design-hero.webp',
    '/assets/images/built-around-your-requirements.webp',
];

$offerItems = array_map(static fn(array $card): array => [
    '@type' => 'Offer',
    'itemOffered' => [
        '@type' => 'Service',
        'name' => $card['title'],
        'description' => $card['desc'],
    ],
], $cards);

$breadcrumbItems = [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => CBD_CANONICAL_ORIGIN . '/'],
];
if (!$isHub) {
    $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => 2, 'name' => 'UI/UX & Branding', 'item' => cbd_canonical_url('/' . $hubSlug)];
    $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => 3, 'name' => $page['name'], 'item' => $canonical];
} else {
    $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => 2, 'name' => $page['name'], 'item' => $canonical];
}

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Service',
            '@id' => $canonical . '#service',
            'name' => $page['name'],
            'serviceType' => $page['name'],
            'url' => $canonical,
            'description' => $page['meta_desc'],
            'image' => $shareImage,
            'provider' => [
                '@type' => 'Organization',
                '@id' => CBD_CANONICAL_ORIGIN . '/#organization',
                'name' => 'Chulbul Design',
                'url' => CBD_CANONICAL_ORIGIN . '/',
                'telephone' => '+919990548795',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'Sector 15, Flat No. 1277',
                    'addressLocality' => 'Gurugram',
                    'addressRegion' => 'Haryana',
                    'postalCode' => '122001',
                    'addressCountry' => 'IN',
                ],
            ],
            'areaServed' => 'Worldwide',
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => $page['cards_heading'],
                'itemListElement' => $offerItems,
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            '@id' => $canonical . '#breadcrumb',
            'itemListElement' => $breadcrumbItems,
        ],
        [
            '@type' => 'FAQPage',
            '@id' => $canonical . '#faq',
            'mainEntity' => array_map(static fn(array $faq): array => [
                '@type' => 'Question',
                'name' => (string)($faq['q'] ?? ''),
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => (string)($faq['a'] ?? '')],
            ], $page['faqs'] ?? []),
        ],
    ],
];

$relatedPages = $pages + [
    'ui-ux-branding' => [
        'name' => 'UI/UX & Branding',
        'icon' => 'bi-diagram-3',
        'meta_desc' => 'Explore interface, Figma, graphic, logo and brand identity design for a coherent digital and visual presence.',
    ],
];
$relatedSlugs = array_values(array_filter(array_keys($relatedPages), static fn(string $candidate): bool => $candidate !== $slug));
$relatedSlugs = array_slice($relatedSlugs, 0, 6);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= cbd_escape_text($page['meta_title']) ?></title>
    <meta name="description" content="<?= cbd_escape_text($page['meta_desc']) ?>">
    <link rel="canonical" href="<?= cbd_escape_text($canonical) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= cbd_escape_text($page['meta_title']) ?>">
    <meta property="og:description" content="<?= cbd_escape_text($page['meta_desc']) ?>">
    <meta property="og:url" content="<?= cbd_escape_text($canonical) ?>">
    <meta property="og:image" content="<?= cbd_escape_text($shareImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@chulbuldesign">
    <meta name="twitter:title" content="<?= cbd_escape_text($page['meta_title']) ?>">
    <meta name="twitter:description" content="<?= cbd_escape_text($page['meta_desc']) ?>">
    <meta name="twitter:image" content="<?= cbd_escape_text($shareImage) ?>">
    <link rel="preload" as="image" href="<?= $base . cbd_escape_text($page['hero_img']) ?>" fetchpriority="high">
    <script type="application/ld+json"><?= cbd_json_ld($schema) ?></script>
    <?php require __DIR__ . '/header.php'; ?>

<main class="wd-page">
    <section class="wd-hero" aria-labelledby="branding-page-title">
        <div class="wd-orb wd-orb-one" aria-hidden="true"></div>
        <div class="wd-orb wd-orb-two" aria-hidden="true"></div>
        <div class="wd-container wd-hero-grid">
            <div class="wd-hero-copy wd-reveal">
                <nav class="wd-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?= $base ?>/">Home</a><span aria-hidden="true">/</span>
                    <?php if (!$isHub): ?><a href="<?= $base ?>/<?= $hubSlug ?>">UI/UX &amp; Branding</a><span aria-hidden="true">/</span><?php endif; ?>
                    <span aria-current="page"><?= cbd_escape_text($page['name']) ?></span>
                </nav>
                <span class="wd-eyebrow"><i class="bi <?= cbd_escape_text($page['icon']) ?>" aria-hidden="true"></i> <?= cbd_escape_text($page['hero_badge']) ?></span>
                <h1 id="branding-page-title"><?= cbd_escape_text($page['hero_h1']) ?> <span><?= cbd_escape_text($page['hero_highlight']) ?></span></h1>
                <p class="wd-hero-lead"><?= cbd_escape_text($page['hero_desc']) ?></p>
                <div class="wd-actions">
                    <a class="wd-button wd-button-primary" href="<?= $base ?>/contact-us"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i> <?= cbd_escape_text($page['hero_cta']) ?></a>
                    <a class="wd-button wd-button-secondary" href="#branding-services">Explore Capabilities <i class="bi bi-arrow-down" aria-hidden="true"></i></a>
                </div>
                <ul class="wd-trust-list" aria-label="Design delivery commitments">
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Brief-led design</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Clear review milestones</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Defined deliverables</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Practical handoff</li>
                </ul>
            </div>
            <div class="wd-hero-visual wd-reveal">
                <div class="wd-browser">
                    <div class="wd-browser-bar" aria-hidden="true"><span></span><span></span><span></span><b><?= cbd_escape_text($page['browser_label']) ?></b></div>
                    <img src="<?= $base . cbd_escape_text($page['hero_img']) ?>" alt="<?= cbd_escape_text($page['hero_img_alt']) ?>" width="<?= (int)$page['hero_width'] ?>" height="<?= (int)$page['hero_height'] ?>" fetchpriority="high">
                    <?php foreach (($page['chips'] ?? []) as $index => $chip): ?><div class="wd-code-chip <?= $index === 0 ? 'wd-code-chip-one' : 'wd-code-chip-two' ?>" aria-hidden="true"><i class="bi <?= cbd_escape_text($chip[0]) ?>"></i><span><?= cbd_escape_text($chip[1]) ?></span></div><?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="wd-section wd-intro"><div class="wd-container wd-narrow wd-reveal">
        <span class="wd-kicker"><?= cbd_escape_text($page['intro_kicker']) ?></span>
        <h2><?= cbd_escape_text($page['intro_heading']) ?></h2>
        <?php foreach ($page['intro'] as $paragraph): ?><p><?= cbd_escape_text($paragraph) ?></p><?php endforeach; ?>
    </div></section>

    <section class="wd-section wd-section-soft" id="branding-services" aria-labelledby="branding-services-title"><div class="wd-container">
        <div class="wd-heading wd-reveal"><span class="wd-kicker">Service capabilities</span><h2 id="branding-services-title"><?= cbd_escape_text($page['cards_heading']) ?></h2><p><?= cbd_escape_text($page['cards_subtitle']) ?></p></div>
        <div class="wd-service-grid">
            <?php foreach ($cards as $index => $card): ?><article class="wd-service-card wd-reveal">
                <span class="wd-card-number"><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></span><div class="wd-icon"><i class="bi <?= cbd_escape_text($card['icon']) ?>" aria-hidden="true"></i></div>
                <h3><?= cbd_escape_text($card['title']) ?></h3><p><?= cbd_escape_text($card['desc']) ?></p>
                <?php if ($card['href'] !== ''): ?><a href="<?= $base . cbd_escape_text($card['href']) ?>">Explore <?= cbd_escape_text($card['title']) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a><?php endif; ?>
            </article><?php endforeach; ?>
        </div>
    </div></section>

    <?php if (!empty($sections[0])): $section = $sections[0]; ?>
    <section class="wd-section"><div class="wd-container wd-split">
        <div class="wd-media-panel wd-reveal"><img src="<?= $base . $sectionImages[0] ?>" alt="Design planning desk with wireframes and colour samples" width="900" height="725" loading="lazy"><div class="wd-media-caption"><i class="bi bi-diagram-3" aria-hidden="true"></i><span>Brief → design decisions → useful deliverables</span></div></div>
        <div class="wd-copy wd-reveal"><span class="wd-kicker">Start with a useful plan</span><h2><?= cbd_escape_text($section['h2']) ?></h2><?php foreach ($sectionParagraphs($section) as $paragraph): ?><p><?= cbd_escape_text($paragraph) ?></p><?php endforeach; ?><ul class="wd-check-list"><?php foreach ($section['bullets'] as $bullet): ?><li><i class="bi bi-check2" aria-hidden="true"></i><span><?= cbd_escape_text($bullet) ?></span></li><?php endforeach; ?></ul></div>
    </div></section>
    <?php endif; ?>

    <section class="wd-section wd-solutions" aria-labelledby="branding-solutions-title"><div class="wd-container">
        <div class="wd-heading wd-heading-light wd-reveal"><span class="wd-kicker"><?= cbd_escape_text($page['solutions_kicker']) ?></span><h2 id="branding-solutions-title"><?= cbd_escape_text($page['solutions_heading']) ?></h2><p><?= cbd_escape_text($page['solutions_intro']) ?></p></div>
        <div class="wd-solution-grid"><?php foreach ($page['solutions'] as $solution): ?><article class="wd-solution-card wd-reveal"><i class="bi <?= cbd_escape_text($solution[0]) ?>" aria-hidden="true"></i><h3><?= cbd_escape_text($solution[1]) ?></h3><p><?= cbd_escape_text($solution[2]) ?></p></article><?php endforeach; ?></div>
    </div></section>

    <section class="wd-section" aria-labelledby="branding-foundation-title"><div class="wd-container">
        <div class="wd-heading wd-reveal"><span class="wd-kicker">Foundations for coherent design</span><h2 id="branding-foundation-title"><?= cbd_escape_text($page['foundation_heading']) ?></h2><p><?= cbd_escape_text($page['foundation_intro']) ?></p></div>
        <div class="wd-stack-grid"><?php foreach ($page['foundation'] as $foundation): ?><article class="wd-stack-card wd-reveal"><span class="wd-stack-label"><?= cbd_escape_text($foundation[0]) ?></span><h3><?= cbd_escape_text($foundation[1]) ?></h3><p><?= cbd_escape_text($foundation[2]) ?></p><div class="wd-tags"><?php foreach ($foundation[3] as $tag): ?><span><?= cbd_escape_text($tag) ?></span><?php endforeach; ?></div></article><?php endforeach; ?></div>
    </div></section>

    <?php if (!empty($sections[1])): $section = $sections[1]; ?>
    <section class="wd-section wd-section-soft"><div class="wd-container wd-split wd-split-reverse">
        <div class="wd-copy wd-reveal"><span class="wd-kicker">Connect the design decisions</span><h2><?= cbd_escape_text($section['h2']) ?></h2><?php foreach ($sectionParagraphs($section) as $paragraph): ?><p><?= cbd_escape_text($paragraph) ?></p><?php endforeach; ?><div class="wd-integration-list"><?php foreach ($section['bullets'] as $bullet): ?><span><i class="bi bi-check-circle" aria-hidden="true"></i><?= cbd_escape_text($bullet) ?></span><?php endforeach; ?></div></div>
        <div class="wd-api-visual wd-reveal" aria-label="<?= cbd_escape_text($page['name']) ?> workflow illustration"><div class="wd-api-core"><i class="bi <?= cbd_escape_text($page['integration_core'][0]) ?>" aria-hidden="true"></i><strong><?= cbd_escape_text($page['integration_core'][1]) ?></strong></div><?php foreach ($page['integration_nodes'] as $index => $node): ?><div class="wd-api-node wd-api-node-<?= ['one','two','three','four'][$index] ?>"><i class="bi <?= cbd_escape_text($node[0]) ?>" aria-hidden="true"></i><span><?= cbd_escape_text($node[1]) ?></span></div><?php endforeach; ?></div>
    </div></section>
    <?php endif; ?>

    <?php foreach ([2, 3] as $position): if (empty($sections[$position])) continue; $section = $sections[$position]; ?>
    <section class="wd-section <?= $position === 3 ? 'wd-section-soft' : '' ?>"><div class="wd-container wd-split <?= $position === 3 ? 'wd-split-reverse' : '' ?>">
        <div class="wd-media-panel wd-reveal"><img src="<?= $base . $sectionImages[$position - 1] ?>" alt="<?= $position === 3 ? 'Illustration of requirements and delivery planning' : 'Illustration of interface components, typography and responsive layouts' ?>" width="900" height="725" loading="lazy"><div class="wd-media-caption"><i class="bi <?= $position === 3 ? 'bi-clipboard-check' : 'bi-bezier2' ?>" aria-hidden="true"></i><span><?= $position === 3 ? 'Review, refine and hand over' : 'Designed for real applications' ?></span></div></div>
        <div class="wd-copy wd-reveal"><span class="wd-kicker"><?= $position === 3 ? 'Delivery and implementation' : 'Useful to your audience' ?></span><h2><?= cbd_escape_text($section['h2']) ?></h2><?php foreach ($sectionParagraphs($section) as $paragraph): ?><p><?= cbd_escape_text($paragraph) ?></p><?php endforeach; ?><ul class="wd-check-list"><?php foreach ($section['bullets'] as $bullet): ?><li><i class="bi bi-check2" aria-hidden="true"></i><span><?= cbd_escape_text($bullet) ?></span></li><?php endforeach; ?></ul></div>
    </div></section>
    <?php endforeach; ?>

    <section class="wd-section" aria-labelledby="branding-quality-title"><div class="wd-container">
        <div class="wd-heading wd-reveal"><span class="wd-kicker">Quality throughout delivery</span><h2 id="branding-quality-title"><?= cbd_escape_text($page['quality_heading']) ?></h2><p><?= cbd_escape_text($page['quality_intro']) ?></p></div>
        <div class="wd-quality-grid"><?php foreach ($page['quality'] as $quality): ?><article class="wd-quality-card wd-reveal"><i class="bi <?= cbd_escape_text($quality[0]) ?>" aria-hidden="true"></i><h3><?= cbd_escape_text($quality[1]) ?></h3><p><?= cbd_escape_text($quality[2]) ?></p></article><?php endforeach; ?></div>
    </div></section>

    <section class="wd-section wd-process" aria-labelledby="branding-process-title"><div class="wd-container">
        <div class="wd-heading wd-reveal"><span class="wd-kicker">From discovery to improvement</span><h2 id="branding-process-title">How We Deliver <?= cbd_escape_text($page['name']) ?></h2><p>Each stage has a clear purpose, approval point and responsibility before the next work begins.</p></div>
        <ol class="wd-process-list"><?php foreach ($page['process'] as $step): ?><li class="wd-reveal"><span><?= cbd_escape_text($step['num']) ?></span><div><i class="bi <?= cbd_escape_text($step['icon']) ?>" aria-hidden="true"></i><h3><?= cbd_escape_text($step['title']) ?></h3><p><?= cbd_escape_text($step['desc']) ?></p></div></li><?php endforeach; ?></ol>
    </div></section>

    <section class="wd-section wd-proof" aria-labelledby="branding-proof-title"><div class="wd-container wd-proof-grid">
        <div class="wd-proof-copy wd-reveal"><span class="wd-kicker">Review our wider digital work</span><h2 id="branding-proof-title">Review the Thinking Behind the Design</h2><p>Our wider website portfolio includes work connected with Gurjar Pragati Manch, Kisaan Jee and Tera Ghar. These logos provide context for our wider digital work; they do not mean every project included <?= cbd_escape_text($page['platform']) ?> or original identity creation.</p><p><?= cbd_escape_text($page['proof_text']) ?></p><a class="wd-button wd-button-primary" href="<?= $base ?>/#portfolio">Review Selected Work</a></div>
        <div class="wd-proof-logos wd-reveal" aria-label="Selected client and partnership portfolio"><?php foreach ([['gurjar-pragati-manch.webp','Gurjar Pragati Manch'],['kisaanjee.webp','Kisaan Jee'],['tera-ghar.webp','Tera Ghar']] as $logo): ?><div><img src="<?= $base ?>/assets/images/client-logos/<?= cbd_escape_text($logo[0]) ?>" alt="<?= cbd_escape_text($logo[1]) ?> logo" width="180" height="72" loading="lazy"><span><?= cbd_escape_text($logo[1]) ?></span></div><?php endforeach; ?><a href="https://softles.in/" target="_blank" rel="noopener noreferrer"><strong>S</strong><span>Development partner: SoftLes <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></span></a></div>
    </div></section>

    <section class="wd-section" aria-labelledby="branding-why-title"><div class="wd-container wd-split">
        <div class="wd-copy wd-reveal"><span class="wd-kicker">Why Chulbul Design</span><h2 id="branding-why-title"><?= cbd_escape_text($page['why_heading']) ?></h2><?php foreach ($page['why_paragraphs'] as $paragraph): ?><p><?= cbd_escape_text($paragraph) ?></p><?php endforeach; ?></div>
        <div class="wd-reasons wd-reveal"><?php foreach ($page['reasons'] as $reason): ?><div><i class="bi <?= cbd_escape_text($reason[0]) ?>" aria-hidden="true"></i><span><strong><?= cbd_escape_text($reason[1]) ?></strong><?= cbd_escape_text($reason[2]) ?></span></div><?php endforeach; ?></div>
    </div></section>

    <section class="wd-section wd-section-soft" aria-labelledby="branding-cost-title"><div class="wd-container wd-cost-grid">
        <div class="wd-copy wd-reveal"><span class="wd-kicker">Transparent scoping</span><h2 id="branding-cost-title">What Determines <?= cbd_escape_text($page['name']) ?> Cost?</h2><p><?= cbd_escape_text($page['cost_intro']) ?></p><p>The proposal separates design work from development, printing, licensed assets and any additional production. Scope changes and approval responsibilities are discussed before extra work begins.</p><a class="wd-button wd-button-primary" href="<?= $base ?>/contact-us">Request a Scope Discussion</a></div>
        <div class="wd-cost-list wd-reveal"><?php foreach ($page['cost_factors'] as $index => $factor): ?><div><span><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></span><p><strong><?= cbd_escape_text($factor[0]) ?></strong><?= cbd_escape_text($factor[1]) ?></p></div><?php endforeach; ?></div>
    </div></section>

    <section class="wd-section" aria-labelledby="related-branding-title"><div class="wd-container">
        <div class="wd-heading wd-reveal"><span class="wd-kicker">Choose a connected capability</span><h2 id="related-branding-title">Related UI/UX &amp; Branding Services</h2><p>Compare the services below, or <a href="<?= $base ?>/ui-ux-branding">explore UI/UX and branding</a> to choose the right design support for your product, identity and business materials.</p></div>
        <div class="wd-related-grid wd-related-grid--three"><?php foreach ($relatedSlugs as $relatedSlug): $related = $relatedPages[$relatedSlug]; ?><a class="wd-related-card wd-reveal" href="<?= $base ?>/<?= cbd_escape_text($relatedSlug) ?>"><i class="bi <?= cbd_escape_text($related['icon']) ?>" aria-hidden="true"></i><span><strong><?= cbd_escape_text($related['name']) ?></strong><?= cbd_escape_text($related['meta_desc']) ?></span><b>Explore <i class="bi bi-arrow-right" aria-hidden="true"></i></b></a><?php endforeach; ?></div>
    </div></section>

    <section class="wd-section wd-faq" aria-labelledby="branding-faq-title"><div class="wd-container wd-faq-grid">
        <div class="wd-heading wd-heading-left wd-reveal"><span class="wd-kicker">Useful answers before getting started</span><h2 id="branding-faq-title"><?= cbd_escape_text($page['name']) ?> FAQs</h2><p>Clear answers about scope, review rounds, deliverables, implementation and cost.</p><a class="wd-text-link" href="<?= $base ?>/contact-us">Ask about your business <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div>
        <div class="wd-accordion wd-reveal"><?php foreach ($page['faqs'] as $index => $faq): ?><details<?= $index === 0 ? ' open' : '' ?>><summary><?= cbd_escape_text($faq['q']) ?><i class="bi bi-plus-lg" aria-hidden="true"></i></summary><p><?= cbd_escape_text($faq['a']) ?></p></details><?php endforeach; ?></div>
    </div></section>

    <section class="wd-final-cta"><div class="wd-container wd-reveal"><span class="wd-kicker">Start with your business priorities</span><h2><?= cbd_escape_text($page['cta_heading']) ?></h2><p><?= cbd_escape_text($page['cta_text']) ?></p><div class="wd-actions"><a class="wd-button wd-button-white" href="<?= $base ?>/contact-us"><i class="bi bi-envelope-fill" aria-hidden="true"></i> Discuss Your Design Project</a><a class="wd-button wd-button-outline" href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> WhatsApp Chulbul Design</a></div></div></section>
</main>

<script>
(function () {
    function revealBrandingContent() {
        var items = document.querySelectorAll('.wd-reveal');
        if (!items.length) return;
        if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            items.forEach(function (item) { item.classList.add('is-visible'); });
            return;
        }
        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px' });
        items.forEach(function (item) { observer.observe(item); });
    }
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', revealBrandingContent, { once: true });
    else revealBrandingContent();
})();
</script>

<?php require __DIR__ . '/footer.php'; ?>
