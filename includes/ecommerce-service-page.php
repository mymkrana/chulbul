<?php

require_once __DIR__ . '/http.php';
require_once __DIR__ . '/seo.php';
require_once __DIR__ . '/database.php';

$pages = require __DIR__ . '/ecommerce-pages-data.php';
$experiencePages = require __DIR__ . '/ecommerce-experience-data.php';
$expansionPages = require __DIR__ . '/ecommerce-page-expansions.php';
$slug = (string)($ecommerce_service_slug ?? '');
$script = (string)($ecommerce_service_script ?? '');

if (!isset($pages[$slug])) {
    require dirname(__DIR__) . '/404.php';
    exit;
}

if ($script !== '' && cbd_is_direct_script_request($script)) {
    cbd_redirect_path('/' . $slug);
}

$page = $pages[$slug];
$experience = $experiencePages[$slug] ?? [];
$expansion = $expansionPages[$slug] ?? [];
$pdo = cbd_database();
$dbPage = null;
if ($pdo) {
    try {
        $statement = $pdo->prepare('SELECT * FROM services WHERE slug = ? AND status = 1 AND deleted_at IS NULL LIMIT 1');
        $statement->execute([$slug]);
        $dbPage = $statement->fetch() ?: null;
    } catch (Throwable $error) {
        error_log('Ecommerce page repository unavailable: ' . $error->getMessage());
    }
}

if (is_array($dbPage)) {
    foreach ([
        'meta_title', 'meta_desc', 'hero_badge', 'hero_h1', 'hero_desc',
        'hero_img', 'hero_img_alt', 'cards_heading', 'cards_subtitle',
        'process_heading',
    ] as $field) {
        if (isset($dbPage[$field]) && trim((string)$dbPage[$field]) !== '') {
            $page[$field] = (string)$dbPage[$field];
        }
    }

    if (trim((string)($dbPage['intro_text'] ?? '')) !== '') {
        $page['intro'] = preg_split('/\R{2,}/', trim((string)$dbPage['intro_text'])) ?: $page['intro'];
    }

    foreach (['sections', 'cards', 'steps', 'faq'] as $field) {
        $decoded = json_decode((string)($dbPage[$field] ?? ''), true);
        if (is_array($decoded) && $decoded !== []) {
            $page[$field === 'faq' ? 'faqs' : $field] = $decoded;
        }
    }
}

$normalizeCard = static function (array $card): array {
    if (array_is_list($card)) {
        return [
            'icon' => (string)($card[0] ?? 'bi-cart3'),
            'title' => (string)($card[1] ?? ''),
            'desc' => (string)($card[2] ?? ''),
            'href' => (string)($card[3] ?? ''),
        ];
    }
    return [
        'icon' => (string)($card['icon'] ?? 'bi-cart3'),
        'title' => (string)($card['title'] ?? ''),
        'desc' => (string)($card['desc'] ?? ''),
        'href' => (string)($card['href'] ?? ''),
    ];
};

$normalizeStep = static function (array $step): array {
    if (array_is_list($step)) {
        return [
            'num' => (string)($step[0] ?? ''),
            'icon' => (string)($step[1] ?? 'bi-check2'),
            'title' => (string)($step[2] ?? ''),
            'desc' => (string)($step[3] ?? ''),
        ];
    }
    return [
        'num' => (string)($step['num'] ?? ''),
        'icon' => (string)($step['icon'] ?? 'bi-check2'),
        'title' => (string)($step['title'] ?? ''),
        'desc' => (string)($step['desc'] ?? ''),
    ];
};

$page['cards'] = array_map($normalizeCard, $page['cards'] ?? []);
$page['steps'] = array_map($normalizeStep, $page['steps'] ?? []);
$experience['solutions'] = array_slice(array_merge(
    is_array($experience['solutions'] ?? null) ? $experience['solutions'] : [],
    is_array($expansion['solutions'] ?? null) ? $expansion['solutions'] : []
), 0, 10);

$uniqueFaqs = [];
foreach (array_merge(
    is_array($page['faqs'] ?? null) ? $page['faqs'] : [],
    is_array($expansion['faqs'] ?? null) ? $expansion['faqs'] : []
) as $faq) {
    if (!is_array($faq)) {
        continue;
    }
    $question = trim((string)($faq['q'] ?? $faq[0] ?? ''));
    $answer = trim((string)($faq['a'] ?? $faq[1] ?? ''));
    if ($question === '' || $answer === '') {
        continue;
    }
    $key = mb_strtolower(preg_replace('/\s+/', ' ', $question));
    if (!isset($uniqueFaqs[$key])) {
        $uniqueFaqs[$key] = ['q' => $question, 'a' => $answer];
    }
}
$page['faqs'] = array_slice(array_values($uniqueFaqs), 0, 10);

if (count($page['steps']) === 5) {
    $page['steps'][] = [
        'num' => '06',
        'icon' => 'bi-graph-up-arrow',
        'title' => 'Review & Improve',
        'desc' => 'Review agreed customer, commerce and technical signals, then prioritize useful post-launch improvements.',
    ];
}

$contentSections = array_values(array_filter($page['sections'] ?? [], 'is_array'));
$firstSection = $contentSections[0] ?? [];
$secondSection = $contentSections[1] ?? [];
$thirdSection = $contentSections[2] ?? [];
$fourthSection = $contentSections[3] ?? [];
$sectionParagraphs = static fn(array $section): array => preg_split('/\R{2,}/', trim((string)($section['para'] ?? ''))) ?: [];

$base = cbd_base_path();
$canonical = cbd_canonical_url('/' . $slug);
$shareImage = cbd_public_url((string)$page['hero_img']);
$cbd_page_has_charset = true;
$page_styles = ['/assets/css/web-development.css'];
$page_robots = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';

$offerItems = [];
foreach ($page['cards'] as $card) {
    $offer = [
        '@type' => 'Offer',
        'itemOffered' => [
            '@type' => 'Service',
            'name' => $card['title'],
        ],
    ];
    if ($card['href'] !== '') {
        $offer['itemOffered']['url'] = cbd_canonical_url($card['href']);
    }
    $offerItems[] = $offer;
}

$breadcrumbItems = [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => CBD_CANONICAL_ORIGIN . '/'],
];
if ($slug !== 'ecommerce-development') {
    $breadcrumbItems[] = ['@type' => 'ListItem', 'position' => 2, 'name' => 'eCommerce Development', 'item' => cbd_canonical_url('/ecommerce-development')];
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
            'name' => strip_tags((string)$page['name']),
            'serviceType' => strip_tags((string)$page['name']),
            'url' => $canonical,
            'description' => (string)$page['meta_desc'],
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
            'areaServed' => ['@type' => 'Country', 'name' => 'India'],
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

$relatedSlugs = array_values(array_filter(array_keys($pages), static fn(string $item): bool => $item !== $slug));
$relatedSlugs = array_slice($relatedSlugs, 0, 6);
$costFactors = [
    ['Catalogue and workflows', 'Product types, variants, customer roles, pricing and operational rules shape the build.'],
    ['Interface scope', 'The number and complexity of storefront, account and administration templates affect effort.'],
    ['Migration', 'Products, customers, orders, media, content and URL history require mapping and validation.'],
    ['Integrations', 'Payments, shipping, tax, CRM, ERP and marketplace providers introduce separate dependencies.'],
    ['Platform and infrastructure', 'Licences, apps, hosting, search and monitoring contribute to ownership cost.'],
    ['Testing and support', 'Devices, transaction scenarios, data, security and post-launch responsibility define QA depth.'],
];
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

<main class="wd-page wd-page-ecommerce">
    <section class="wd-hero" aria-labelledby="ecommerce-page-title">
        <div class="wd-orb wd-orb-one" aria-hidden="true"></div>
        <div class="wd-orb wd-orb-two" aria-hidden="true"></div>
        <div class="wd-container wd-hero-grid">
            <div class="wd-hero-copy wd-reveal">
                <nav class="wd-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?= $base ?>/">Home</a><span aria-hidden="true">/</span>
                    <?php if ($slug !== 'ecommerce-development'): ?>
                    <a href="<?= $base ?>/ecommerce-development">eCommerce Development</a><span aria-hidden="true">/</span>
                    <?php endif; ?>
                    <span aria-current="page"><?= cbd_escape_text($page['name']) ?></span>
                </nav>
                <span class="wd-eyebrow"><i class="bi bi-cart3" aria-hidden="true"></i> <?= cbd_escape_text($page['hero_badge']) ?></span>
                <h1 id="ecommerce-page-title"><?= $page['hero_h1'] ?></h1>
                <p class="wd-hero-lead"><?= cbd_escape_text($page['hero_desc']) ?></p>
                <div class="wd-actions">
                    <a class="wd-button wd-button-primary" href="<?= $base ?>/contact-us"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i> Discuss Your eCommerce Project</a>
                    <a class="wd-button wd-button-secondary" href="#ecommerce-services">Explore Capabilities <i class="bi bi-arrow-down" aria-hidden="true"></i></a>
                </div>
                <ul class="wd-trust-list" aria-label="eCommerce development commitments">
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Requirement-led platform choice</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Responsive and accessible UX</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Clear integration ownership</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Documented testing and handover</li>
                </ul>
            </div>
            <div class="wd-hero-visual wd-reveal">
                <div class="wd-browser">
                    <div class="wd-browser-bar" aria-hidden="true"><span></span><span></span><span></span><b><?= cbd_escape_text($page['browser_label']) ?></b></div>
                    <img src="<?= $base . cbd_escape_text($page['hero_img']) ?>" alt="<?= cbd_escape_text($page['hero_img_alt']) ?>" width="960" height="640" fetchpriority="high">
                    <?php foreach (($page['chips'] ?? []) as $index => $chip): ?>
                    <div class="wd-code-chip <?= $index === 0 ? 'wd-code-chip-one' : 'wd-code-chip-two' ?>" aria-hidden="true"><i class="bi <?= cbd_escape_text($chip[0]) ?>"></i><span><?= cbd_escape_text($chip[1]) ?></span></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="wd-section wd-intro">
        <div class="wd-container wd-narrow wd-reveal">
            <span class="wd-kicker">Strategy before storefront</span>
            <h2><?= cbd_escape_text($page['intro_heading']) ?></h2>
            <?php foreach (($page['intro'] ?? []) as $paragraph): ?><p><?= cbd_escape_text($paragraph) ?></p><?php endforeach; ?>
        </div>
    </section>

    <section class="wd-section wd-section-soft" id="ecommerce-services" aria-labelledby="ecommerce-services-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal">
                <span class="wd-kicker">Service capabilities</span>
                <h2 id="ecommerce-services-title"><?= cbd_escape_text($page['cards_heading']) ?></h2>
                <p><?= cbd_escape_text($page['cards_subtitle']) ?></p>
            </div>
            <div class="wd-service-grid">
                <?php foreach ($page['cards'] as $index => $card): ?>
                <article class="wd-service-card wd-reveal">
                    <span class="wd-card-number"><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <div class="wd-icon"><i class="bi <?= cbd_escape_text($card['icon']) ?>" aria-hidden="true"></i></div>
                    <h3><?= cbd_escape_text($card['title']) ?></h3>
                    <p><?= cbd_escape_text($card['desc']) ?></p>
                    <?php if ($card['href'] !== ''): ?><a href="<?= $base . cbd_escape_text($card['href']) ?>">Explore <?= cbd_escape_text($card['title']) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a><?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ($firstSection !== []): ?>
    <section class="wd-section">
        <div class="wd-container wd-split">
            <div class="wd-media-panel wd-reveal">
                <img src="<?= $base . cbd_escape_text($firstSection['img'] ?? '') ?>" alt="<?= cbd_escape_text($firstSection['img_alt'] ?? $firstSection['h2'] ?? '') ?>" width="900" height="725" loading="lazy">
                <div class="wd-media-caption"><i class="bi bi-cart-check" aria-hidden="true"></i><span><?= cbd_escape_text($page['name']) ?> planned end to end</span></div>
            </div>
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">A dependable commerce foundation</span>
                <h2><?= cbd_escape_text($firstSection['h2'] ?? '') ?></h2>
                <?php foreach ($sectionParagraphs($firstSection) as $paragraph): ?><p><?= cbd_escape_text($paragraph) ?></p><?php endforeach; ?>
                <?php if (!empty($firstSection['bullets'])): ?><ul class="wd-check-list">
                    <?php foreach ($firstSection['bullets'] as $bullet): ?><li><i class="bi bi-check2" aria-hidden="true"></i><span><?= cbd_escape_text($bullet) ?></span></li><?php endforeach; ?>
                </ul><?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="wd-section wd-solutions" aria-labelledby="ecommerce-solutions-title">
        <div class="wd-container">
            <div class="wd-heading wd-heading-light wd-reveal">
                <span class="wd-kicker"><?= cbd_escape_text($experience['solutions_kicker'] ?? 'Commerce for different operating models') ?></span>
                <h2 id="ecommerce-solutions-title"><?= cbd_escape_text($experience['solutions_heading'] ?? 'eCommerce Solutions Shaped Around the Business') ?></h2>
                <p><?= cbd_escape_text($experience['solutions_intro'] ?? 'The right solution depends on customers, products and the team responsible for fulfilment.') ?></p>
            </div>
            <div class="wd-solution-grid">
                <?php foreach (($experience['solutions'] ?? []) as $solution): ?>
                <article class="wd-solution-card wd-reveal">
                    <i class="bi <?= cbd_escape_text($solution[0] ?? 'bi-cart3') ?>" aria-hidden="true"></i>
                    <h3><?= cbd_escape_text($solution[1] ?? '') ?></h3>
                    <p><?= cbd_escape_text($solution[2] ?? '') ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="ecommerce-foundation-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal">
                <span class="wd-kicker"><?= cbd_escape_text($experience['foundation_kicker'] ?? 'A maintainable commerce foundation') ?></span>
                <h2 id="ecommerce-foundation-title"><?= cbd_escape_text($experience['foundation_heading'] ?? 'Experience, Operations and Technology Working Together') ?></h2>
                <p><?= cbd_escape_text($experience['foundation_intro'] ?? 'A dependable store connects customer journeys with the systems and people responsible for delivery.') ?></p>
            </div>
            <div class="wd-stack-grid">
                <?php foreach (($experience['foundations'] ?? []) as $foundation): ?>
                <article class="wd-stack-card wd-reveal">
                    <span class="wd-stack-label"><?= cbd_escape_text($foundation[0] ?? '') ?></span>
                    <h3><?= cbd_escape_text($foundation[1] ?? '') ?></h3>
                    <p><?= cbd_escape_text($foundation[2] ?? '') ?></p>
                    <div class="wd-tags"><?php foreach (($foundation[3] ?? []) as $tag): ?><span><?= cbd_escape_text($tag) ?></span><?php endforeach; ?></div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ($secondSection !== []): ?>
    <section class="wd-section wd-section-soft">
        <div class="wd-container wd-split wd-split-reverse">
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Connected commerce operations</span>
                <h2><?= cbd_escape_text($secondSection['h2'] ?? '') ?></h2>
                <?php foreach ($sectionParagraphs($secondSection) as $paragraph): ?><p><?= cbd_escape_text($paragraph) ?></p><?php endforeach; ?>
                <?php if (!empty($secondSection['bullets'])): ?><div class="wd-integration-list">
                    <?php foreach ($secondSection['bullets'] as $bullet): ?><span><i class="bi bi-check-circle" aria-hidden="true"></i><?= cbd_escape_text($bullet) ?></span><?php endforeach; ?>
                </div><?php endif; ?>
            </div>
            <div class="wd-api-visual wd-reveal" aria-label="Illustration of <?= cbd_escape_text($page['name']) ?> connected to business systems">
                <div class="wd-api-core"><i class="bi <?= cbd_escape_text($experience['integration_core'][0] ?? 'bi-cart3') ?>" aria-hidden="true"></i><strong><?= cbd_escape_text($experience['integration_core'][1] ?? 'Commerce') ?></strong></div>
                <?php foreach (($experience['integration_nodes'] ?? []) as $index => $node): ?>
                <div class="wd-api-node wd-api-node-<?= ['one', 'two', 'three', 'four'][$index] ?? 'one' ?>"><i class="bi <?= cbd_escape_text($node[0] ?? 'bi-plug') ?>" aria-hidden="true"></i><span><?= cbd_escape_text($node[1] ?? '') ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($thirdSection !== []): ?>
    <section class="wd-section">
        <div class="wd-container wd-split">
            <div class="wd-media-panel wd-reveal">
                <img src="<?= $base . cbd_escape_text($thirdSection['img'] ?? '') ?>" alt="<?= cbd_escape_text($thirdSection['img_alt'] ?? $thirdSection['h2'] ?? '') ?>" width="900" height="725" loading="lazy">
                <div class="wd-media-caption"><i class="bi bi-diagram-3" aria-hidden="true"></i><span>Commerce systems with clear ownership</span></div>
            </div>
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Designed for real operations</span>
                <h2><?= cbd_escape_text($thirdSection['h2'] ?? '') ?></h2>
                <?php foreach ($sectionParagraphs($thirdSection) as $paragraph): ?><p><?= cbd_escape_text($paragraph) ?></p><?php endforeach; ?>
                <?php if (!empty($thirdSection['bullets'])): ?><ul class="wd-check-list">
                    <?php foreach ($thirdSection['bullets'] as $bullet): ?><li><i class="bi bi-check2" aria-hidden="true"></i><span><?= cbd_escape_text($bullet) ?></span></li><?php endforeach; ?>
                </ul><?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($fourthSection !== []): ?>
    <section class="wd-section wd-section-soft">
        <div class="wd-container wd-split wd-split-reverse">
            <div class="wd-media-panel wd-reveal">
                <img src="<?= $base . cbd_escape_text($fourthSection['img'] ?? '') ?>" alt="<?= cbd_escape_text($fourthSection['img_alt'] ?? $fourthSection['h2'] ?? '') ?>" width="900" height="725" loading="lazy">
                <div class="wd-media-caption"><i class="bi bi-clipboard-check" aria-hidden="true"></i><span>Validated before launch and handover</span></div>
            </div>
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Launch confidence</span>
                <h2><?= cbd_escape_text($fourthSection['h2'] ?? '') ?></h2>
                <?php foreach ($sectionParagraphs($fourthSection) as $paragraph): ?><p><?= cbd_escape_text($paragraph) ?></p><?php endforeach; ?>
                <?php if (!empty($fourthSection['bullets'])): ?><ul class="wd-check-list">
                    <?php foreach ($fourthSection['bullets'] as $bullet): ?><li><i class="bi bi-check2" aria-hidden="true"></i><span><?= cbd_escape_text($bullet) ?></span></li><?php endforeach; ?>
                </ul><?php endif; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <section class="wd-section" aria-labelledby="ecommerce-quality-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal">
                <span class="wd-kicker"><?= cbd_escape_text($experience['quality_kicker'] ?? 'Quality throughout the commerce journey') ?></span>
                <h2 id="ecommerce-quality-title"><?= cbd_escape_text($experience['quality_heading'] ?? 'Experience, Performance, Reliability and Maintainability') ?></h2>
                <p><?= cbd_escape_text($experience['quality_intro'] ?? 'Commerce quality must support customers and the team responsible for the store after launch.') ?></p>
            </div>
            <div class="wd-quality-grid">
                <?php foreach (($experience['quality'] ?? []) as $quality): ?>
                <article class="wd-quality-card wd-reveal"><i class="bi <?= cbd_escape_text($quality[0] ?? 'bi-check2-circle') ?>" aria-hidden="true"></i><h3><?= cbd_escape_text($quality[1] ?? '') ?></h3><p><?= cbd_escape_text($quality[2] ?? '') ?></p></article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-section wd-process" aria-labelledby="ecommerce-process-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal"><span class="wd-kicker">From discovery to release</span><h2 id="ecommerce-process-title"><?= cbd_escape_text($page['process_heading']) ?></h2><p>Each stage produces something reviewable before the next layer of work begins.</p></div>
            <ol class="wd-process-list">
                <?php foreach ($page['steps'] as $step): ?><li class="wd-reveal"><span><?= cbd_escape_text($step['num']) ?></span><div><i class="bi <?= cbd_escape_text($step['icon']) ?>" aria-hidden="true"></i><h3><?= cbd_escape_text($step['title']) ?></h3><p><?= cbd_escape_text($step['desc']) ?></p></div></li><?php endforeach; ?>
            </ol>
        </div>
    </section>

    <section class="wd-section wd-proof" aria-labelledby="ecommerce-proof-title">
        <div class="wd-container wd-proof-grid">
            <div class="wd-proof-copy wd-reveal">
                <span class="wd-kicker">Review relevant delivery experience</span>
                <h2 id="ecommerce-proof-title">Real Work, Clear Roles and Explainable Decisions</h2>
                <p>Our portfolio includes digital delivery for organizations such as Gurjar Pragati Manch, Kisaan Jee and Tera Ghar. Relevant work helps us explain information architecture, responsive customer journeys, content management and technical implementation through specific, reviewable decisions.</p>
                <p>During a consultation, we walk through our role, the constraints involved and how comparable decisions could apply to your catalogue, customers and operations. For broader engineering requirements, Chulbul Design can also collaborate with SoftLes as a technology partner.</p>
                <a class="wd-button wd-button-primary" href="<?= $base ?>/#portfolio">Review Selected Work</a>
            </div>
            <div class="wd-proof-logos wd-reveal" aria-label="Selected client and partnership portfolio">
                <?php foreach ([['gurjar-pragati-manch.webp','Gurjar Pragati Manch'],['kisaanjee.webp','Kisaan Jee'],['tera-ghar.webp','Tera Ghar']] as $logo): ?><div><img src="<?= $base ?>/assets/images/client-logos/<?= cbd_escape_text($logo[0]) ?>" alt="<?= cbd_escape_text($logo[1]) ?>" width="180" height="72" loading="lazy"><span><?= cbd_escape_text($logo[1]) ?></span></div><?php endforeach; ?>
                <a href="https://softles.in/" target="_blank" rel="noopener noreferrer"><strong>S</strong><span>Technology partner: SoftLes <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></span></a>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="ecommerce-why-title">
        <div class="wd-container wd-split">
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Why Chulbul Design</span>
                <h2 id="ecommerce-why-title"><?= cbd_escape_text($experience['why_heading'] ?? 'Commerce Strategy, Design and Development in One Conversation') ?></h2>
                <?php foreach (($experience['why_paragraphs'] ?? []) as $paragraph): ?><p><?= cbd_escape_text($paragraph) ?></p><?php endforeach; ?>
            </div>
            <div class="wd-reasons wd-reveal">
                <?php foreach (($experience['reasons'] ?? []) as $reason): ?>
                <div><i class="bi <?= cbd_escape_text($reason[0] ?? 'bi-check2-circle') ?>" aria-hidden="true"></i><span><strong><?= cbd_escape_text($reason[1] ?? '') ?></strong><?= cbd_escape_text($reason[2] ?? '') ?></span></div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-section wd-section-soft" aria-labelledby="ecommerce-cost-title">
        <div class="wd-container wd-cost-grid">
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Transparent scoping</span>
                <h2 id="ecommerce-cost-title">What Determines <?= cbd_escape_text($page['name']) ?> Cost?</h2>
                <p>A useful estimate must be connected to products, customer journeys, operational rules and launch responsibility. We review the factors below before recommending a platform or committing to milestones.</p>
                <p>After discovery, you receive a written scope that separates included work, dependencies and later opportunities. This is more dependable than an attractive starting price that excludes the parts needed to operate the store.</p>
                <a class="wd-button wd-button-primary" href="<?= $base ?>/contact-us">Request a Scope Discussion</a>
            </div>
            <div class="wd-cost-list wd-reveal">
                <?php foreach ($costFactors as $index => $factor): ?><div><span><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></span><p><strong><?= cbd_escape_text($factor[0]) ?></strong><?= cbd_escape_text($factor[1]) ?></p></div><?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="related-ecommerce-title">
        <div class="wd-container"><div class="wd-heading wd-reveal"><span class="wd-kicker">Explore the commerce cluster</span><h2 id="related-ecommerce-title">Related eCommerce Development Services</h2><p>Move between the platform-neutral hub and service-specific guidance without repeating the same search intent.</p></div><div class="wd-related-grid wd-related-grid--three">
            <?php foreach ($relatedSlugs as $relatedSlug): $related = $pages[$relatedSlug]; ?><a class="wd-related-card wd-reveal" href="<?= $base ?>/<?= cbd_escape_text($relatedSlug) ?>"><i class="bi bi-cart3" aria-hidden="true"></i><span><strong><?= cbd_escape_text($related['name']) ?></strong><?= cbd_escape_text($related['meta_desc']) ?></span><b>Explore <i class="bi bi-arrow-right" aria-hidden="true"></i></b></a><?php endforeach; ?>
        </div></div>
    </section>

    <section class="wd-section wd-faq" aria-labelledby="ecommerce-faq-title">
        <div class="wd-container wd-faq-grid"><div class="wd-heading wd-heading-left wd-reveal"><span class="wd-kicker">Useful answers before discovery</span><h2 id="ecommerce-faq-title"><?= cbd_escape_text($page['name']) ?> FAQs</h2><p>Clear answers to common planning, platform, migration and cost questions.</p><a class="wd-text-link" href="<?= $base ?>/contact-us">Ask a project-specific question <i class="bi bi-arrow-right" aria-hidden="true"></i></a></div><div class="wd-accordion wd-reveal">
            <?php foreach (($page['faqs'] ?? []) as $index => $faq): ?><details<?= $index === 0 ? ' open' : '' ?>><summary><?= cbd_escape_text($faq['q'] ?? '') ?><i class="bi bi-plus-lg" aria-hidden="true"></i></summary><p><?= cbd_escape_text($faq['a'] ?? '') ?></p></details><?php endforeach; ?>
        </div></div>
    </section>

    <section class="wd-final-cta">
        <div class="wd-container wd-reveal"><span class="wd-kicker">Start with the requirements</span><h2>Planning an eCommerce Project?</h2><p>Tell us about your products, customers, current platform and operational constraints. We will help you identify the next useful step without forcing the project into a pre-selected technology.</p><div class="wd-actions"><a class="wd-button wd-button-white" href="<?= $base ?>/contact-us"><i class="bi bi-envelope-fill" aria-hidden="true"></i> Discuss Your Project</a><a class="wd-button wd-button-outline" href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> WhatsApp Chulbul Design</a></div></div>
    </section>
</main>

<script>
(function () {
    function revealEcommerceContent() {
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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', revealEcommerceContent, { once: true });
    } else {
        revealEcommerceContent();
    }
})();
</script>

<?php require __DIR__ . '/footer.php'; ?>
