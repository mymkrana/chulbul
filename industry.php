<?php
require_once __DIR__ . '/includes/http.php';
require_once __DIR__ . '/includes/industry-data.php';

$slug = isset($_GET['slug']) && is_string($_GET['slug']) ? trim($_GET['slug']) : '';
// A query parameter must not replace the sector named in a clean public URL.
$industry_request_path = parse_url((string)($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
if (is_string($industry_request_path) && preg_match('~^' . preg_quote(cbd_base_path(), '~') . '/industry/([a-z0-9-]+)/?$~D', $industry_request_path, $industry_path_match)) {
    $slug = $industry_path_match[1];
}
$ind = cbd_industry_page($slug);

if ($ind === null) {
    require __DIR__ . '/404.php';
    exit;
}
if (cbd_is_direct_script_request('industry.php')) {
    cbd_redirect_path('/industry/' . rawurlencode($slug));
}

$cbd_page_has_charset = true;
// Reuse the existing service-page design system; no industry-specific CSS.
$page_styles = ['/assets/css/web-development.css'];
$industry_catalog = cbd_industry_catalog();

// ── Schema helpers ─────────────────────────────────────────
$jf = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
$canonical = 'https://www.chulbuldesign.com/industry/' . $ind['slug'];

// Industry service schema: reference the real provider without claiming local offices.
$service_schema = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    '@id' => $canonical . '#service',
    'name' => $ind['h1'],
    'url' => $canonical,
    'description' => $ind['meta_desc'],
    'areaServed' => 'Worldwide',
    'serviceType' => 'Website design, development and digital services for ' . $ind['name'],
    'provider' => [
        '@type' => 'Organization',
        '@id' => 'https://www.chulbuldesign.com/#organization',
        'name' => 'Chulbul Design',
        'url' => 'https://www.chulbuldesign.com/',
        'telephone' => '+919990548795',
        'email' => 'info@chulbuldesign.com',
    ],
    'hasOfferCatalog' => [
        '@type' => 'OfferCatalog',
        'name' => 'Services for ' . $ind['name'],
        'itemListElement' => array_map(static function (array $service): array {
            return [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Service',
                    'name' => $service['title'],
                    'url' => 'https://www.chulbuldesign.com/' . $service['slug'],
                    'description' => $service['desc'],
                ],
            ];
        }, $ind['services']),
    ],
];

// FAQ Schema
$faq_entities = [];
foreach ($ind['faqs'] as $faq) {
    $faq_entities[] = [
        '@type'          => 'Question',
        'name'           => $faq['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
    ];
}
$faq_schema = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq_entities];

// Breadcrumb Schema
$breadcrumb_schema = [
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',       'item' => 'https://www.chulbuldesign.com/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => $ind['name'], 'item' => $canonical],
    ],
];

// Four project priorities retain the existing stats-strip layout.
$industry_stats = $ind['stats'];

// Keep the heading and schema as plain text; style only a matching text segment.
$industry_h1_highlight = (string) ($ind['h1_highlight'] ?? '');
$industry_h1_highlight_position = $industry_h1_highlight !== ''
    ? strpos($ind['h1'], $industry_h1_highlight)
    : false;
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
    <title><?= htmlspecialchars($ind['meta_title']) ?></title>
    <link rel="canonical" href="<?= htmlspecialchars($canonical) ?>">
    <meta name="description" content="<?= htmlspecialchars($ind['meta_desc']) ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($ind['meta_title']) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonical) ?>">
    <meta property="og:type" content="website">
    <meta property="og:description" content="<?= htmlspecialchars($ind['meta_desc']) ?>">
    <meta property="og:image" content="https://www.chulbuldesign.com/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Chulbul Design — website design and development">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@chulbuldesign">
    <meta name="twitter:image" content="https://www.chulbuldesign.com/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg">

    <!-- Schemas -->
    <script type="application/ld+json"><?= json_encode($service_schema, $jf) ?></script>
    <script type="application/ld+json"><?= json_encode($faq_schema, $jf) ?></script>
    <script type="application/ld+json"><?= json_encode($breadcrumb_schema, $jf) ?></script>

    <?php require __DIR__ . '/includes/header.php'; ?>

<div class="wd-page" data-industry-page="<?= htmlspecialchars($ind['slug']) ?>">


<!-- ═══════════════════════════════════════════════
     HERO SECTION
═══════════════════════════════════════════════ -->
<section class="city-hero-section wd-hero" aria-labelledby="industry-page-title">

    <!-- Animated blobs -->
    <div class="h-blob-1 city-hero-blob-1" aria-hidden="true"></div>
    <div class="h-blob-2 city-hero-blob-2" aria-hidden="true"></div>
    <div class="h-blob-3 city-hero-blob-3" aria-hidden="true"></div>

    <!-- Dot grid -->
    <div class="city-hero-dot-grid" aria-hidden="true"></div>

    <!-- Spinning decorative rings -->
    <div class="h-ring-1 city-hero-ring-1" aria-hidden="true"></div>
    <div class="h-ring-2 city-hero-ring-2" aria-hidden="true"></div>

    <!-- Floating accent squares -->
    <div class="h-blob-1 city-hero-sq-1" aria-hidden="true"></div>
    <div class="h-blob-3 city-hero-sq-2" aria-hidden="true"></div>
    <div class="h-blob-2 city-hero-sq-3" aria-hidden="true"></div>

    <div class="wd-container relative">
        <div class="wd-hero-grid">

            <!-- Left: Text -->
            <div class="w-full text-left">

                <!-- Breadcrumb -->
                <nav aria-label="Breadcrumb" class="wd-breadcrumb">
                    <a href="<?= $base ?>/" class="hover:text-[#49499A] transition">Home</a>
                    <i class="bi bi-chevron-right text-xs" aria-hidden="true"></i>
                    <span class="text-[#49499A] font-semibold"><?= htmlspecialchars($ind['name']) ?></span>
                </nav>

                <!-- Badge -->
                <div class="h-badge mb-5">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold tracking-widest uppercase text-[#49499A] bg-[#49499A]/10 border border-[#49499A]/25 px-4 py-1.5 rounded-full">
                        <i class="bi <?= htmlspecialchars($ind['icon']) ?>" aria-hidden="true"></i>
                        <?= htmlspecialchars($ind['name']) ?>
                    </span>
                </div>

                <!-- H1 -->
                <h1 id="industry-page-title">
                    <?php if ($industry_h1_highlight_position !== false): ?>
                    <?= htmlspecialchars(substr($ind['h1'], 0, $industry_h1_highlight_position)) ?><span><?= htmlspecialchars($industry_h1_highlight) ?></span><?= htmlspecialchars(substr($ind['h1'], $industry_h1_highlight_position + strlen($industry_h1_highlight))) ?>
                    <?php else: ?>
                    <?= htmlspecialchars($ind['h1']) ?>
                    <?php endif; ?>
                </h1>

                <p class="text-gray-600 text-base leading-relaxed mb-8">
                    <?= htmlspecialchars($ind['desc']) ?>
                </p>

                <!-- CTA Buttons -->
                <div class="wd-actions">
                    <a href="https://wa.me/919990548795?text=Hi%2C+I+need+a+<?= urlencode($ind['name']) ?>+website+for+my+business"
                       target="_blank" rel="noopener noreferrer"
                       class="wd-button wd-button-primary">
                        <i class="bi bi-whatsapp" aria-hidden="true"></i> Get Free Quote
                    </a>
                    <a href="tel:+919990548795"
                       class="wd-button wd-button-secondary">
                        <i class="bi bi-telephone-fill" aria-hidden="true"></i> +91 9990 548 795
                    </a>
                </div>

                <!-- Starting Price Badge -->
                <div class="mt-6 grid grid-cols-2 gap-4 bg-white border border-gray-200 rounded-2xl px-5 py-3">
                    <div class="text-left">
                        <p class="text-gray-500 text-xs mb-2">Project Scope</p>
                        <p class="text-[#1e1e5c] font-extrabold text-sm">Custom quote</p>
                        <p class="text-gray-500 text-xs">tailored to you</p>
                    </div>
                    <div class="text-left">
                        <p class="text-gray-500 text-xs mb-2">Free Consultation</p>
                        <p class="text-[#1e1e5c] font-bold text-sm">No Commitment</p>
                    </div>
                </div>

            </div>

            <!-- Right: Contact Form -->
            <div class="w-full">
                <div class="bg-white border border-gray-200 rounded-2xl p-8 w-full shadow-xl">
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-widest mb-1">Get A Free Quote</p>
                    <p class="text-[#1e1e5c] font-extrabold text-lg mb-5">Tell us about your project</p>
                    <form id="industryQuoteForm" action="<?= $base ?>/lead-submit.php" method="POST" class="space-y-3">
                        <input type="text" name="_hp" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                        <input type="hidden" name="source" value="industry-<?= htmlspecialchars($ind['slug'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="industry" value="<?= htmlspecialchars($ind['name'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="business" value="<?= htmlspecialchars($ind['name'], ENT_QUOTES, 'UTF-8') ?> Industry">
                        <input type="hidden" name="page_url" value="<?= htmlspecialchars($canonical, ENT_QUOTES, 'UTF-8') ?>">
                        <div>
                            <input type="text" name="name" aria-label="Your name" autocomplete="name" placeholder="Your Name" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#49499A] transition">
                        </div>
                        <div>
                            <input type="tel" name="phone" aria-label="Phone or WhatsApp number including country code" autocomplete="tel" placeholder="Phone / WhatsApp (with country code)" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#49499A] transition">
                        </div>
                        <div>
                            <input type="email" name="email" aria-label="Email address" autocomplete="email" placeholder="Email Address"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#49499A] transition">
                        </div>
                        <div>
                            <select name="service" aria-label="Service required" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-500 focus:outline-none focus:border-[#49499A] transition">
                                <option value="" disabled selected>Select Service</option>
                                <?php foreach ($ind['services'] as $cbd_quote_service): ?>
                                <option value="<?= htmlspecialchars($cbd_quote_service['title']) ?>"><?= htmlspecialchars($cbd_quote_service['title']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button id="industryQuoteSubmit" type="submit"
                            class="mt-2 w-full flex items-center justify-center gap-2 bg-[#EE483D] hover:bg-red-600 text-white py-3 rounded-xl font-semibold transition text-sm shadow-md">
                            <i class="bi bi-send-fill"></i> Get Free Quote
                        </button>
                        <div id="industryQuoteMessage" class="hidden rounded-xl px-4 py-3 text-sm" role="status" aria-live="polite"></div>
                    </form>
                    <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-400">
                        <i class="bi bi-shield-check text-green-500"></i>
                        <span>Project consultation</span>
                        <span class="text-gray-300">·</span>
                        <i class="bi bi-patch-check-fill text-[#49499A]"></i>
                        <span>Clear scope</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     TRUST BAR
═══════════════════════════════════════════════ -->
<section class="bg-gray-50 border-y border-gray-200 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-wrap items-center justify-center gap-x-10 gap-y-4 text-sm text-gray-500 font-medium">
            <span class="flex items-center gap-2"><i class="bi bi-patch-check-fill text-[#49499A]" aria-hidden="true"></i> Scope-Led Delivery</span>
            <span class="flex items-center gap-2"><i class="bi bi-shield-lock-fill text-[#49499A]" aria-hidden="true"></i> Privacy-Aware Planning</span>
            <span class="flex items-center gap-2"><i class="bi bi-phone-fill text-[#49499A]" aria-hidden="true"></i> Mobile-First Design</span>
            <span class="flex items-center gap-2"><i class="bi bi-graph-up-arrow text-[#49499A]" aria-hidden="true"></i> Search-Friendly Structure</span>
            <span class="flex items-center gap-2"><i class="bi bi-lightning-charge-fill text-[#49499A]" aria-hidden="true"></i> Performance Testing</span>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     STATS STRIP
═══════════════════════════════════════════════ -->
<section class="bg-white py-12" data-industry-section="priorities">
    <div class="wd-container">
        <p class="text-center text-xs font-bold tracking-widest uppercase text-gray-400 mb-8">
            Project Priorities for <?= htmlspecialchars($ind['name']) ?>
        </p>
        <div class="wd-quality-grid">
            <?php foreach ($industry_stats as $stat): ?>
            <div class="wd-quality-card">
                <h3><?= htmlspecialchars($stat['num']) ?></h3>
                <p><?= htmlspecialchars($stat['label']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- The same six services: descriptions and features now sit with their links. -->
<section class="wd-section wd-section-soft" data-industry-section="services">
    <div class="wd-container">
        <div class="wd-heading">
            <span class="wd-kicker">What We Do</span>
            <h2>Services for <?= htmlspecialchars($ind['name']) ?></h2>
            <p>Explore the service pages below, then review how each can support your <?= htmlspecialchars($ind['name']) ?> project.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" data-industry-service-cards data-industry-services>
            <?php foreach ($ind['services'] as $i => $service): ?>
            <article class="wd-service-card flex flex-col">
                <span class="wd-card-number" aria-hidden="true"><?= sprintf('%02d', $i + 1) ?></span>
                <div class="wd-icon"><i class="bi <?= htmlspecialchars($service['icon']) ?>" aria-hidden="true"></i></div>
                <h3><?= htmlspecialchars($service['title']) ?></h3>
                <p><?= htmlspecialchars($service['desc']) ?></p>
                <ul class="wd-check-list">
                    <?php foreach ($service['features'] as $feature): ?>
                    <li><i class="bi bi-check-lg" aria-hidden="true"></i><span><?= htmlspecialchars($feature) ?></span></li>
                    <?php endforeach; ?>
                </ul>
                <div class="flex-1"></div>
                <a href="<?= $base ?>/<?= htmlspecialchars($service['slug']) ?>" class="wd-text-link" aria-label="Explore <?= htmlspecialchars($service['title']) ?>">Explore service <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="wd-section" data-industry-section="why-us">
    <div class="wd-container">
        <div class="wd-heading">
            <span class="wd-kicker">Why Chulbul Design</span>
            <h2>The Right Partner for Your <span class="text-[#EE483D]"><?= htmlspecialchars($ind['name']) ?></span> Website</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($ind['whyus'] as $reason): ?>
            <article class="wd-quality-card">
                <i class="bi <?= htmlspecialchars($reason['icon']) ?>" aria-hidden="true"></i>
                <h3><?= htmlspecialchars($reason['title']) ?></h3>
                <p><?= htmlspecialchars($reason['desc']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 rounded-2xl overflow-hidden border border-gray-200 mt-10">
            <div class="city-whyus-card-header p-8 text-white">
                <p class="text-white/70 text-sm font-semibold uppercase tracking-widest mb-1">Free Consultation</p>
                <h3 class="text-2xl font-extrabold">Let's build your<br><?= htmlspecialchars($ind['name']) ?> website</h3>
                <p class="text-white/70 text-sm mt-2">No obligation — just an honest conversation about your requirements.</p>
            </div>
            <div class="bg-white p-8">
                <div class="flex flex-wrap gap-3">
                    <a href="https://wa.me/919990548795?text=Hi%2C+I+need+a+<?= urlencode($ind['name']) ?>+website+for+my+business" target="_blank" rel="noopener noreferrer" class="wd-button wd-button-primary"><i class="bi bi-whatsapp" aria-hidden="true"></i>Chat on WhatsApp</a>
                    <a href="tel:+919990548795" class="wd-button wd-button-secondary"><i class="bi bi-telephone-fill" aria-hidden="true"></i>Call +91 9990 548 795</a>
                    <a href="<?= $base ?>/contact-us" class="wd-button wd-button-secondary"><i class="bi bi-envelope-fill" aria-hidden="true"></i>Send Us a Message</a>
                </div>
                <p class="text-gray-500 text-xs mt-5"><i class="bi bi-shield-check text-green-500" aria-hidden="true"></i> Discuss your goals · Agree the scope · Plan the next step</p>
            </div>
        </div>
    </div>
</section>

<!-- All feature groups remain in the HTML and are visible without tab switching. -->
<section class="wd-section wd-section-soft" data-industry-section="features">
    <div class="wd-container">
        <div class="wd-heading">
            <span class="wd-kicker">Feature Planning</span>
            <h2>Features to Consider for <?= htmlspecialchars($ind['name']) ?></h2>
            <p>Choose the features your team needs. Integrations, licences and ongoing costs are agreed in the project scope.</p>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" data-industry-features>
            <?php foreach ($ind['features'] as $i => $category): ?>
            <article id="industry-feature-panel-<?= $i ?>" class="wd-stack-card">
                <div class="wd-icon"><i class="bi <?= htmlspecialchars($category['icon']) ?>" aria-hidden="true"></i></div>
                <span class="wd-stack-label"><?= count($category['items']) ?> features</span>
                <h3><?= htmlspecialchars($category['category']) ?></h3>
                <ul class="wd-check-list">
                    <?php foreach ($category['items'] as $item): ?>
                    <li><i class="bi bi-check-lg" aria-hidden="true"></i><span><?= htmlspecialchars($item['text']) ?><?php if ($item['ai']): ?> <span class="text-xs font-bold text-[#49499A]">AI</span><?php endif; ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="wd-section wd-solutions" data-industry-section="niches">
    <div class="wd-container">
        <div class="wd-heading wd-heading-light">
            <span class="wd-kicker">Who We Serve</span>
            <h2>Who We Build for in <?= htmlspecialchars($ind['name']) ?></h2>
            <p>Different organisations need different journeys. We start with your audience, operating model and existing tools.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($ind['industries'] as $niche): ?>
            <div class="wd-solution-card">
                <h3><i class="bi bi-check-circle-fill text-[#EE483D]" aria-hidden="true"></i> <?= htmlspecialchars($niche) ?></h3>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="wd-section wd-process" data-industry-section="process">
    <div class="wd-container wd-cost-grid">
        <div class="wd-heading wd-heading-left">
            <span class="wd-kicker">How We Work</span>
            <h2>Our <?= htmlspecialchars($ind['name']) ?> Website Process</h2>
            <p>From discovery to handover, each stage has agreed deliverables, feedback and a clear next step.</p>
        </div>
        <div class="wd-cost-list">
            <?php foreach ($ind['process'] as $step): ?>
            <div>
                <span aria-hidden="true"><?= htmlspecialchars($step['step']) ?></span>
                <div>
                    <h3 class="text-base font-extrabold text-gray-900 mb-2"><?= htmlspecialchars($step['title']) ?></h3>
                    <p><?= htmlspecialchars($step['desc']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="wd-section wd-section-soft" data-industry-section="examples">
    <div class="wd-container">
        <div class="wd-heading">
            <span class="wd-kicker">Project Possibilities</span>
            <h2>Example <?= htmlspecialchars($ind['name']) ?> Projects</h2>
            <p>These are example briefs to help you plan, not client case studies or promised results.</p>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <?php foreach ($ind['examples'] as $example): ?>
            <article class="wd-stack-card flex flex-col">
                <span class="wd-stack-label">Illustrative Project Brief</span>
                <h3><?= htmlspecialchars($example['title']) ?></h3>
                <p class="flex-1"><?= htmlspecialchars($example['desc']) ?></p>
                <ul class="wd-check-list">
                    <?php foreach ($example['features'] as $feature): ?>
                    <li><i class="bi bi-check-lg" aria-hidden="true"></i><span><?= htmlspecialchars($feature) ?></span></li>
                    <?php endforeach; ?>
                </ul>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="wd-section" data-industry-section="scope">
    <div class="wd-container">
        <div class="wd-heading">
            <span class="wd-kicker">Project Scope</span>
            <h2>Plan Your <?= htmlspecialchars($ind['name']) ?> Project</h2>
            <p>Start with a scope that matches your needs. We confirm deliverables, currency, third-party fees, timeline and support before work begins.</p>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <?php foreach ($ind['plans'] as $planIndex => $plan): ?>
            <article class="relative rounded-2xl border flex flex-col overflow-hidden <?= $planIndex === 1 ? 'border-[#EE483D] bg-gray-50' : 'border-gray-200 bg-white' ?>">
                <div class="h-8 <?= $planIndex === 1 ? 'bg-[#EE483D] text-white text-xs font-bold text-center py-2 tracking-widest uppercase' : '' ?>">
                    <?php if ($planIndex === 1): ?>Connected Project Scope<?php endif; ?>
                </div>
                <div class="p-8 flex flex-col flex-1">
                    <h3 class="text-xl font-extrabold text-gray-900 mb-3"><?= htmlspecialchars($plan['name']) ?></h3>
                    <p class="text-gray-600 text-sm leading-relaxed mb-6"><?= htmlspecialchars($plan['desc']) ?></p>
                    <p class="text-3xl font-extrabold text-[#49499A] mb-1">Custom</p>
                    <p class="text-gray-500 text-xs">Quoted after discovery</p>
                    <ul class="wd-check-list flex-1">
                        <?php foreach ($plan['features'] as $feat): ?>
                        <li><i class="bi bi-check-lg" aria-hidden="true"></i><span><?= htmlspecialchars($feat) ?></span></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="https://wa.me/919990548795?text=Hi%2C+I%27m+interested+in+the+<?= urlencode($plan['name']) ?>+package+for+a+<?= urlencode($ind['name']) ?>+website" target="_blank" rel="noopener noreferrer" class="wd-button <?= $planIndex === 1 ? 'wd-button-primary' : 'wd-button-secondary' ?> mt-8">Get Started</a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Native accordions keep FAQ answers accessible with or without JavaScript. -->
<section class="wd-section wd-faq" data-industry-section="faq">
    <div class="wd-container wd-faq-grid">
        <div class="wd-heading wd-heading-left">
            <span class="wd-kicker">FAQ</span>
            <h2><?= htmlspecialchars($ind['name']) ?> — Common Questions</h2>
        </div>
        <div class="wd-accordion">
            <?php foreach ($ind['faqs'] as $i => $faq): ?>
            <details id="industry-faq-<?= $i ?>" name="industry-faq">
                <summary><?= htmlspecialchars($faq['q']) ?><i class="bi bi-plus-lg" aria-hidden="true"></i></summary>
                <p><?= htmlspecialchars($faq['a']) ?></p>
            </details>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($ind['related'])): ?>
<section class="bg-white py-12" data-industry-section="related">
    <div class="wd-container">
        <p class="text-center text-xs font-bold tracking-widest uppercase text-gray-400 mb-8">We Also Build Websites For These Industries</p>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php foreach ($ind['related'] as $rslug):
                if (!isset($industry_catalog[$rslug]) || $rslug === $ind['slug']) continue;
                $rm = $industry_catalog[$rslug];
            ?>
            <a href="<?= $base ?>/industry/<?= htmlspecialchars($rslug) ?>" class="group bg-gray-50 hover:bg-white rounded-2xl p-6 flex flex-col items-center gap-3 border border-gray-100 hover:border-[#49499A]/30 transition-all">
                <div class="wd-icon"><i class="bi <?= htmlspecialchars($rm['icon']) ?>" aria-hidden="true"></i></div>
                <span class="text-sm font-bold text-gray-700 group-hover:text-[#49499A] text-center transition-colors"><?= htmlspecialchars($rm['name']) ?></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="city-cta-section">
    <div class="city-cta-deco-1" aria-hidden="true"></div>
    <div class="city-cta-deco-2" aria-hidden="true"></div>
    <div class="city-cta-deco-3" aria-hidden="true"></div>
    <div class="city-cta-deco-4" aria-hidden="true"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 city-cta-inner">
        <div class="city-cta-heading-wrap">
            <span class="city-cta-badge">
                <i class="bi bi-lightning-charge-fill"></i> Let's Work Together
            </span>
        </div>
        <h2 class="city-cta-title">
            Ready to Build Your<br>
            <span class="city-cta-title-highlight"><?= htmlspecialchars($ind['name']) ?> Website?</span>
        </h2>
        <p class="city-cta-desc">
            Tell us about your <?= htmlspecialchars($ind['name']) ?> project, your audience and what needs to work better. We work with organisations worldwide and will help define a practical next step.
        </p>
        <div class="city-cta-stats">
            <div class="city-cta-stat">
                <i class="bi bi-patch-check-fill city-cta-stat-icon-green"></i> Defined Deliverables
            </div>
            <div class="city-cta-stat">
                <i class="bi bi-buildings-fill city-cta-stat-icon-blue"></i> Worldwide Collaboration
            </div>
            <div class="city-cta-stat">
                <i class="bi bi-star-fill city-cta-stat-icon-yellow"></i> Industry-Specific Planning
            </div>
            <div class="city-cta-stat">
                <i class="bi bi-clock-fill city-cta-stat-icon-pink"></i> Agreed Milestones
            </div>
        </div>
        <div class="city-cta-btns">
            <a href="<?= $base ?>/contact-us" class="city-cta-btn-primary">
                <i class="bi bi-envelope-fill"></i> Get a Free Quote
            </a>
            <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer" class="city-cta-btn-wa">
                <i class="bi bi-whatsapp"></i> WhatsApp Us
            </a>
        </div>
    </div>
</section>


</div><!-- Shared industry design -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('industryQuoteForm');
    const submitButton = document.getElementById('industryQuoteSubmit');
    const messageBox = document.getElementById('industryQuoteMessage');
    if (!form || !submitButton || !messageBox) return;

    form.addEventListener('submit', async function (event) {
        event.preventDefault();
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const originalButtonHtml = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="inline-block animate-spin">&#9696;</span> Sending...';
        messageBox.className = 'hidden rounded-xl px-4 py-3 text-sm';

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json().catch(function () { return {}; });
            if (!response.ok || !data.success) {
                throw new Error(data.error || 'We could not send your request. Please try again.');
            }

            messageBox.className = 'rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800';
            messageBox.textContent = 'Thank you! Your request has been received. Our team will contact you.';
            const whatsappLink = document.createElement('a');
            whatsappLink.className = 'font-bold underline';
            whatsappLink.href = 'https://wa.me/919990548795';
            whatsappLink.target = '_blank';
            whatsappLink.rel = 'noopener noreferrer';
            whatsappLink.textContent = ' Continue on WhatsApp';
            messageBox.appendChild(whatsappLink);
            form.reset();
        } catch (error) {
            const fallbackWhatsapp = 'https://wa.me/919990548795';
            messageBox.className = 'rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800';
            messageBox.textContent = (error.message || 'We could not send your request. Please try again.') + ' ';
            const fallbackLink = document.createElement('a');
            fallbackLink.className = 'font-bold underline';
            fallbackLink.href = fallbackWhatsapp;
            fallbackLink.target = '_blank';
            fallbackLink.rel = 'noopener noreferrer';
            fallbackLink.textContent = 'Contact us on WhatsApp';
            messageBox.appendChild(fallbackLink);
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonHtml;
        }
    });
});
</script>


<?php require __DIR__ . '/includes/footer.php'; ?>
