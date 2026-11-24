<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/http.php';
require_once __DIR__ . '/includes/seo.php';
require_once __DIR__ . '/includes/database.php';

if (cbd_is_direct_script_request('pet-food-shopify-australia.php')) {
    cbd_redirect_path('/pet-food-shopify-australia');
}

$base = cbd_base_path();
$canonical = cbd_canonical_url('/pet-food-shopify-australia');
$isLive = cbd_is_production_host();

$cssVersion  = (int)(@filemtime(__DIR__ . '/assets/css/pet-food-shopify-australia.css') ?: 1);
$jsVersion   = (int)(@filemtime(__DIR__ . '/assets/js/pet-food-shopify-australia.js') ?: 1);
$heroArtUrl  = cbd_asset_url('/assets/images/pet-food-au/au-pet-happy-owner-hero.jpg');
$caseImgUrl  = cbd_asset_url('/assets/images/pet-food-au/au-pet-food-subscription-ux.jpg');
$shareImage  = cbd_public_url('/assets/images/pet-food-au/au-pet-happy-owner-hero.jpg');

$pageTitle = 'Pet Food & Pet Store Shopify Australia | Design & Cost Save';
$pageDescription = 'Pet food & pet store ecommerce Shopify development in Australia. Custom Shopify 2.0 design, monthly store management & 40–60% app cost savings. Free audit.';

$faqs = [
    [
        'q' => 'What is included in your Shopify Australia ecommerce solution for pet food & pet stores?',
        'a' => 'We provide an end-to-end pet store ecommerce solution on Shopify covering three pillars: (1) Custom Shopify 2.0 pet shop website design with breed/diet/age filters, feeding calculators, auto-ship subscriptions and Afterpay/Stripe checkout, (2) Ongoing monthly Shopify store management (catalog, promos, speed, bug fixes and CRO), and (3) Shopify cost saving by replacing bloated recurring paid apps with lightweight native code.',
    ],
    [
        'q' => 'How do you actually reduce monthly Shopify operating and app costs by 40–60%?',
        'a' => 'Most Australian pet food stores pay A$500–A$1,500+/month for stacked third-party apps (feeding tables, postcode checkers, bundle builders, sticky-cart upsells, review widgets) plus heavy local agency retainers. As a specialist Shopify developer Australia brands trust, we replace redundant paid apps with clean native Shopify 2.0 Liquid code and streamline recurring workflows — a cost-saving approach we have already delivered for Australian pet brands like Melbourne’s Brunswick Fur Food.',
    ],
    [
        'q' => 'What does your ongoing monthly Shopify store management cover?',
        'a' => 'From A$999/month, our dedicated Shopify developer and UI designer handle product & variant uploads, seasonal sale banners, "Buy More, Save More" bundle promos, subscription & auto-ship UX updates, AusPost/Sendle shipping rules, Core Web Vitals speed monitoring, technical SEO hygiene and weekly AEST/AWST progress check-ins.',
    ],
    [
        'q' => 'Can you build a Multi-Vendor Australian Pet Goods Marketplace on Shopify?',
        'a' => 'Yes. Alongside D2C pet food storefronts, we build curated multi-vendor Australian pet marketplaces on Shopify (with Shipturtle or Webkul) featuring supplier portals, automated marketplace commissions, GST-ready split payouts and per-vendor AusPost/Sendle shipping.',
    ],
    [
        'q' => 'Can you build pet-specific features like auto-ship subscriptions, smart filters and feeding calculators?',
        'a' => 'Yes. We engineer every modern pet shop website essential directly into Shopify 2.0: smart filters by pet type, breed, life stage and dietary/allergy needs; interactive dog feeding calculators wired into Recharge, Skio, Appstle or native Shopify Selling Plans; tiered quantity discounts; photo reviews; and fast mobile checkout with Afterpay, Zip, Apple Pay and PayPal.',
    ],
    [
        'q' => 'How do you handle Australian cold-chain delivery postcodes and heavy 15–20kg dog food bags?',
        'a' => 'We build native 4-digit Australian postcode checkers right inside your Shopify theme so Melbourne, Sydney, Brisbane, Perth and Adelaide metro buyers see chilled delivery options while regional postcodes automatically route to shelf-stable treats and dry kibble via AusPost, Sendle, Shippit or Starshipit.',
    ],
    [
        'q' => 'Can you partner white-label with our existing Shopify agency Australia team?',
        'a' => 'Yes. Existing Australian Meta Ads, Google Ads, Klaviyo and Shopify agency Australia partners plug in our NDA-protected Shopify engineering pod to slash backend development costs by 55%+, lower their clients’ monthly Shopify app bills, and turn ongoing store management into a 65%+ gross margin retainer.',
    ],
    [
        'q' => 'What do I get in the Free 5-Minute Shopify Cost-Saving & Store Audit?',
        'a' => 'Within 24 hours our senior Shopify team sends a personalised 5-minute Loom walkthrough of your live .com.au store identifying paid apps you can cut, mobile speed fixes (LCP/CLS), pet-UX conversion upgrades and exact projected AUD savings — with zero admin password required.',
    ],
];

$serviceSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    '@id' => $canonical . '#service',
    'name' => 'Australia Pet Food & Pet Shop Shopify Ecommerce Development',
    'serviceType' => 'Pet Store Ecommerce Website Development, Shopify Cost Optimization, Pet Marketplace and White-Label AU Agency Partner',
    'url' => $canonical,
    'description' => $pageDescription,
    'areaServed' => ['@type' => 'Country', 'name' => 'Australia'],
    'hasOfferCatalog' => [
        '@type' => 'OfferCatalog',
        'name' => 'Australia Pet Food Shopify Packages (AUD)',
        'itemListElement' => [
            [
                '@type' => 'Offer',
                'itemOffered' => ['@type' => 'Service', 'name' => 'Shopify App-Cost Cleanup & Speed Sprint'],
                'priceSpecification' => ['@type' => 'PriceSpecification', 'price' => '799', 'priceCurrency' => 'AUD'],
            ],
            [
                '@type' => 'Offer',
                'itemOffered' => ['@type' => 'Service', 'name' => 'Monthly Shopify Store Care & CRO Pod'],
                'priceSpecification' => ['@type' => 'PriceSpecification', 'price' => '999', 'priceCurrency' => 'AUD'],
            ],
            [
                '@type' => 'Offer',
                'itemOffered' => ['@type' => 'Service', 'name' => 'Custom Pet Food Shopify Store or Multi-Vendor Marketplace'],
                'priceSpecification' => ['@type' => 'PriceSpecification', 'price' => '2499', 'priceCurrency' => 'AUD'],
            ],
        ],
    ],
    'provider' => [
        '@type' => 'Organization',
        '@id' => 'https://www.chulbuldesign.com/#organization',
        'name' => 'Chulbul Design',
        'url' => 'https://www.chulbuldesign.com/',
        'telephone' => '+919990548795',
        'email' => 'info@chulbuldesign.com',
    ],
];

$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://www.chulbuldesign.com/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Shopify Development', 'item' => 'https://www.chulbuldesign.com/shopify-development'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Pet Food Shopify Australia', 'item' => $canonical],
    ],
];

$faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(
        static fn(array $item): array => [
            '@type' => 'Question',
            'name' => $item['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
        ],
        $faqs
    ),
];
?>
<!DOCTYPE html>
<html lang="en-AU" data-base-path="<?= cbd_escape_text($base) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= cbd_escape_text($pageTitle) ?></title>
    <meta name="description" content="<?= cbd_escape_text($pageDescription) ?>">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="theme-color" content="#0E6B47">
    <meta name="geo.region" content="AU">
    <meta name="geo.placename" content="Australia">
    <link rel="canonical" href="<?= cbd_escape_text($canonical) ?>">
    <link rel="alternate" hreflang="en-AU" href="<?= cbd_escape_text($canonical) ?>">
    <link rel="alternate" hreflang="x-default" href="<?= cbd_escape_text($canonical) ?>">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= cbd_escape_text($base) ?>/assets/images/logo/favicon.png">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_AU">
    <meta property="og:title" content="<?= cbd_escape_text($pageTitle) ?>">
    <meta property="og:description" content="<?= cbd_escape_text($pageDescription) ?>">
    <meta property="og:url" content="<?= cbd_escape_text($canonical) ?>">
    <meta property="og:image" content="<?= cbd_escape_text($shareImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= cbd_escape_text($pageTitle) ?>">
    <meta name="twitter:description" content="<?= cbd_escape_text($pageDescription) ?>">
    <meta name="twitter:image" content="<?= cbd_escape_text($shareImage) ?>">

    <?php if ($isLive): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Z45EZTKR2J"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-Z45EZTKR2J');
    </script>
    <?php endif; ?>

    <!-- Clean Pet-Commerce Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;9..144,700&family=JetBrains+Mono:wght@500;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- 100% Isolated Page Stylesheet -->
    <link rel="stylesheet" href="<?= cbd_escape_text($base) ?>/assets/css/pet-food-shopify-australia.css?v=<?= $cssVersion ?>">

    <script type="application/ld+json"><?= cbd_json_ld($serviceSchema) ?></script>
    <script type="application/ld+json"><?= cbd_json_ld($breadcrumbSchema) ?></script>
    <script type="application/ld+json"><?= cbd_json_ld($faqSchema) ?></script>
</head>
<body>

<!-- ═══════════════════════════════════════════════
     CLEAN PET-STORE ADS TOPBAR
════════════════════════════════════════════════ -->
<header class="pfa-header">
    <div class="pfa-wrap pfa-header-inner">
        <div class="pfa-brand">
            <a href="<?= cbd_escape_text($base) ?>/" aria-label="Chulbul Design Home">
                <img src="<?= cbd_escape_text($base) ?>/assets/images/logo/chulbuldesign.svg" alt="Chulbul Design" width="146" height="30">
            </a>
            <span class="pfa-brand-badge">
                <i class="bi bi-heart-fill" style="color:#f26b1d;" aria-hidden="true"></i>
                AU Pet Food Shopify Studio
            </span>
        </div>

        <nav class="pfa-nav" aria-label="Page navigation">
            <a href="#shopify-solutions">Shopify Solutions</a>
            <a href="#live-storefront">Store Features</a>
            <a href="#cost-calculator">Cost Saving</a>
            <a href="#pricing">Pricing</a>
        </nav>

        <a href="#audit-form" class="pfa-btn pfa-btn-orange" style="padding:10px 20px;font-size:13.5px;">
            Free 5-Min Audit <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </a>
    </div>
</header>

<main>

    <!-- ═══════════════════════════════════════════════
         1. VIBRANT ROUNDED EMERALD HERO (PetDuct / PawVita Spirit)
    ════════════════════════════════════════════════ -->
    <section class="pfa-hero-shell" aria-labelledby="pfa-hero-h1">
        <div class="pfa-wrap">
            <div class="pfa-hero-card pfa-paw-bg">
                <div>
                    <div class="pfa-audience-pills" role="tablist" aria-label="Choose business type">
                        <button type="button" class="pfa-aud-btn is-active" data-pfa-audience="brand" aria-selected="true">
                            🐾 AU Pet Food Brands
                        </button>
                        <button type="button" class="pfa-aud-btn" data-pfa-audience="agency" aria-selected="false">
                            🏢 Existing AU Agencies
                        </button>
                    </div>

                    <h1 id="pfa-hero-h1">
                        Complete <span>Pet Food &amp; Pet Store</span> Shopify Solutions for Australia.
                    </h1>

                    <p class="pfa-hero-desc" id="pfa-hero-desc">
                        Specialist Shopify Australia partner for pet store ecommerce: we design high-converting pet food &amp; pet shop websites, handle ongoing monthly Shopify store management, and slash recurring app &amp; agency costs by 40–60%.
                    </p>

                    <form id="pfa-quick-form" class="pfa-hero-quickbar" novalidate>
                        <input id="pfa-quick-url" type="text" placeholder="Enter yourpetstore.com.au for a free 5-min Shopify cost audit" aria-label="Your Australian Shopify URL">
                        <button type="submit" class="pfa-btn pfa-btn-orange" style="padding:11px 20px;font-size:14px;">
                            Get Free Audit <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </button>
                    </form>

                    <div class="pfa-hero-proof-row">
                        <span><i class="bi bi-check-circle-fill" style="color:#f8c936;" aria-hidden="true"></i> Custom Shopify 2.0 Design</span>
                        <span><i class="bi bi-check-circle-fill" style="color:#f8c936;" aria-hidden="true"></i> Full Store Management</span>
                        <span><i class="bi bi-check-circle-fill" style="color:#f8c936;" aria-hidden="true"></i> 40–60% Cost Saving</span>
                    </div>
                </div>

                <!-- Sunny-Yellow Framed Happy Dog + Owner Portrait -->
                <div class="pfa-hero-art">
                    <div class="pfa-portrait-frame">
                        <img src="<?= cbd_escape_text($heroArtUrl) ?>"
                             alt="Happy Australian dog owner hugging a smiling golden retriever beside organic dog food pouch and bowl"
                             width="900" height="1000" fetchpriority="high">
                    </div>

                    <div class="pfa-sticker is-left">
                        <i class="bi bi-piggy-bank-fill" style="background:#e8f5ee;color:#0e6b47;" aria-hidden="true"></i>
                        <div>Cut Paid App Bills<br><span style="color:#0e6b47;">Up to 45%–60%</span></div>
                    </div>

                    <div class="pfa-sticker is-right">
                        <i class="bi bi-repeat" style="background:#fff1e6;color:#f26b1d;" aria-hidden="true"></i>
                        <div>Shopify Store Care<br><span style="color:#d9550c;">From A$999 / mo</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         2. PAWVITA-STYLE CIRCULAR PET-SHOPIFY CAPABILITIES STRIP
    ════════════════════════════════════════════════ -->
    <section class="pfa-circle-strip" aria-label="Shopify pet store capabilities">
        <div class="pfa-wrap">
            <div class="pfa-circle-grid">
                <a href="#shopify-solutions" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-palette-fill" aria-hidden="true"></i></div>
                    <span>Shopify Store Design</span>
                </a>
                <a href="#shopify-solutions" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-gear-wide-connected" aria-hidden="true"></i></div>
                    <span>Store Management</span>
                </a>
                <a href="#cost-calculator" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-piggy-bank-fill" aria-hidden="true"></i></div>
                    <span>Shopify Cost Saving</span>
                </a>
                <a href="#live-storefront" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-repeat" aria-hidden="true"></i></div>
                    <span>Auto-Ship Subscriptions</span>
                </a>
                <a href="#live-storefront" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-funnel-fill" aria-hidden="true"></i></div>
                    <span>Breed &amp; Diet Filters</span>
                </a>
                <a href="#live-storefront" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-truck" aria-hidden="true"></i></div>
                    <span>AU Cold-Chain Shipping</span>
                </a>
                <a href="#live-storefront" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-shop-window" aria-hidden="true"></i></div>
                    <span>Pet Marketplaces</span>
                </a>
                <a href="#pricing" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-building-check" aria-hidden="true"></i></div>
                    <span>AU Agency White-Label</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         3. PETDUCT-STYLE 3 COLOR-BLOCKED CORE PILLAR CARDS
    ════════════════════════════════════════════════ -->
    <section class="pfa-deals-section" aria-labelledby="pfa-pillars-sr">
        <div class="pfa-wrap">
            <h2 id="pfa-pillars-sr" class="pfa-sr-only">Core Shopify Australia Pet Store Solutions: Design, Management &amp; Cost Saving</h2>
            <div class="pfa-deals-grid">
                <article class="pfa-deal-card is-yellow">
                    <div>
                        <span class="pfa-deal-badge">PILLAR 01 · DESIGN &amp; BUILD</span>
                        <h3>Custom Pet Food Shopify Design</h3>
                        <p>Mobile-first Shopify 2.0 storefronts &amp; multi-vendor pet marketplaces with breed/diet filters, Afterpay/Apple Pay &amp; auto-ship UX from <strong>A$2,499</strong>.</p>
                    </div>
                    <a href="#live-storefront" class="pfa-deal-link">Explore Store Features <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </article>

                <article class="pfa-deal-card is-green pfa-paw-bg">
                    <div>
                        <span class="pfa-deal-badge">PILLAR 02 · MANAGEMENT</span>
                        <h3>Ongoing Shopify Store Management</h3>
                        <p>Dedicated Shopify engineers &amp; designers managing catalog updates, seasonal bundles, speed, CRO &amp; subscription ops from <strong>A$999/mo</strong>.</p>
                    </div>
                    <a href="#pricing" class="pfa-deal-link">View Monthly Care Pod <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </article>

                <article class="pfa-deal-card is-orange">
                    <div>
                        <span class="pfa-deal-badge">PILLAR 03 · SAVE COSTING</span>
                        <h3>Cut Monthly Shopify Costs 40–60%</h3>
                        <p>Audit &amp; replace bloated recurring paid Shopify apps and heavy local retainers with clean native Shopify 2.0 Liquid code — starting at <strong>A$799</strong>.</p>
                    </div>
                    <a href="#cost-calculator" class="pfa-deal-link">Calculate AUD Savings <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </article>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         4. 360° SHOPIFY PET ECOMMERCE SOLUTIONS (DESIGN · MANAGEMENT · COST SAVING)
    ════════════════════════════════════════════════ -->
    <section class="pfa-section pfa-section-cream pfa-paw-bg-light" id="shopify-solutions" aria-labelledby="pfa-solutions-title">
        <div class="pfa-wrap">
            <div class="pfa-heading">
                <span class="pfa-pill"><i class="bi bi-bag-check-fill" aria-hidden="true"></i> End-to-End Shopify Pet eCommerce</span>
                <h2 id="pfa-solutions-title">Built Exclusively for <span>Shopify Design, Management &amp; Cost Saving</span></h2>
                <p>Everything an Australian pet food brand, pet shop, or ecommerce agency needs to launch, scale and run lean on Shopify — without paying bloated app or agency taxes.</p>
            </div>

            <div class="pfa-bff-card">
                <div class="pfa-bff-top">
                    <div class="pfa-bff-body">
                        <span class="pfa-pill pfa-pill-orange">Shopify Developer Australia · Pet Ecommerce</span>
                        <h3>One Shopify Team for <span>Design, Daily Ops &amp; Lower Bills</span></h3>
                        <p style="font-size:15.5px;color:#52625a;margin:0;">
                            Pet store ecommerce has unique demands — recurring auto-ship deliveries, breed &amp; dietary filters, fresh/frozen postcode rules, heavy kibble shipping and promo bundles. Whether you run a growing pet shop website or a Shopify agency Australia team, we engineer and manage your entire Shopify ecosystem cleanly so you convert more pet parents while spending far less every month.
                        </p>
                        <div class="pfa-bff-tags">
                            <span class="pfa-bff-tag">🐾 Breed, Age &amp; Diet Smart Filters</span>
                            <span class="pfa-bff-tag">🔁 Auto-Ship &amp; Loyalty Tiers</span>
                            <span class="pfa-bff-tag">💳 Afterpay, Stripe &amp; Apple Pay</span>
                            <span class="pfa-bff-tag">⚡ Native Code · Zero App Bloat</span>
                        </div>
                        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
                            <a href="#audit-form" class="pfa-btn pfa-btn-emerald">
                                Get Free Shopify Audit <i class="bi bi-arrow-right" aria-hidden="true"></i>
                            </a>
                            <a href="#cost-calculator" class="pfa-btn pfa-btn-outline">
                                Estimate Cost Savings
                            </a>
                        </div>
                        <p style="margin:2px 0 0;font-size:13px;color:#52625a;padding-top:12px;border-top:1px dashed #e2dbd0;">
                            <i class="bi bi-patch-check-fill" style="color:#0e6b47;" aria-hidden="true"></i>
                            <strong>Proven in Australia:</strong> Trusted by Aussie pet food brands including Melbourne’s
                            <a href="https://www.brunswickfurfood.com/" target="_blank" rel="noopener noreferrer" style="color:#0e6b47;font-weight:700;text-decoration:underline;">Brunswick Fur Food ↗</a>,
                            where we helped reduce ongoing Shopify operating costs.
                        </p>
                    </div>

                    <div class="pfa-bff-media">
                        <img src="<?= cbd_escape_text($caseImgUrl) ?>"
                             alt="Australian pet food Shopify subscription box and mobile storefront UX"
                             width="1200" height="750" loading="lazy">
                    </div>
                </div>

                <div class="pfa-bff-steps">
                    <div class="pfa-bff-step">
                        <strong>1. Shopify Design &amp; Conversion UX</strong>
                        <p>Custom Shopify 2.0 storefronts built for dog &amp; cat parents — smart pet filters (breed, weight, allergies), "Buy More, Save More" bundles, photo reviews, pet-profile personalisation and 1-tap mobile checkout.</p>
                    </div>
                    <div class="pfa-bff-step">
                        <strong>2. Full Shopify Store Management</strong>
                        <p>Your hands-free AU Shopify tech &amp; ops team: SKU/variant updates, stock sync, auto-ship &amp; subscription tracking, seasonal promo launches, AusPost/Sendle/Shippit setup, Core Web Vitals speed and SEO care.</p>
                    </div>
                    <div class="pfa-bff-step">
                        <strong>3. Shopify Cost Saving &amp; App Cleanup</strong>
                        <p>We strip out A$300–A$1,000+/mo in redundant paid Shopify apps (feeding calculators, postcode checkers, upsell carts, bundle builders), code them natively in Liquid, and prevent unnecessary Shopify Plus upgrades.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         5. LIVE INTERACTIVE PET STOREFRONT BROWSER PREVIEW
    ════════════════════════════════════════════════ -->
    <section class="pfa-section" id="live-storefront" aria-labelledby="pfa-demo-title">
        <div class="pfa-wrap">
            <div class="pfa-heading">
                <span class="pfa-pill pfa-pill-orange"><i class="bi bi-play-circle-fill" aria-hidden="true"></i> Interactive Storefront Demo</span>
                <h2 id="pfa-demo-title">Test the Exact <span>Pet-Food Shopify</span> Modules We Build Natively</h2>
                <p>Zero slow monthly apps. Try our live Shopify 2.0 Dog Feeding Calculator, AU Cold-Chain Postcode Checker, and Multi-Vendor Pet Marketplace checkout flow.</p>
            </div>

            <div class="pfa-browser">
                <div class="pfa-browser-bar">
                    <div class="pfa-dots" aria-hidden="true"><span></span><span></span><span></span></div>
                    <div class="pfa-url-bar">https://aussie-dog-nutrition.com.au/shop</div>
                    <div class="pfa-demo-tabs" role="tablist" aria-label="Interactive pet store features">
                        <button type="button" class="pfa-demo-tab is-active" data-pfa-tab="feeding" aria-selected="true">🐾 Feeding &amp; Auto-Ship</button>
                        <button type="button" class="pfa-demo-tab" data-pfa-tab="postcode" aria-selected="false">🚚 AU Postcode Cold-Chain</button>
                        <button type="button" class="pfa-demo-tab" data-pfa-tab="marketplace" aria-selected="false">🏪 Pet Marketplace Split</button>
                    </div>
                </div>

                <div class="pfa-browser-body">
                    <!-- TAB 1: Dog Feeding Calculator -->
                    <div class="pfa-panel is-active" data-pfa-panel="feeding">
                        <div>
                            <span class="pfa-pill">Boosts Repeat Subscriptions</span>
                            <h3 style="font-family:var(--font-serif);font-size:28px;margin:12px 0 10px;">Interactive Dog Feeding &amp; Subscribe-and-Save Widget</h3>
                            <p style="color:#52625a;font-size:15px;margin:0 0 18px;">
                                Dog owners convert faster when they instantly see daily grams, box duration, and <strong>15% Auto-Ship savings</strong>. We code this right into your Shopify 2.0 theme (A$0/mo app fee!) wired to Recharge, Skio or Appstle.
                            </p>
                            <ul class="pfa-checks">
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Works for fresh cooked meals, raw frozen boxes, air-dried roo &amp; kibble</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Auto-selects the ideal 2-, 4- or 6-week recurring frequency</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Saves A$79–A$149/mo vs recurring quiz &amp; feeding apps</li>
                            </ul>
                        </div>

                        <div class="pfa-panel-card">
                            <div class="pfa-slider-top">
                                <label for="dog-weight-slider">Your Dog’s Weight (kg)</label>
                                <span class="pfa-mono-badge" id="dog-weight-val">18 kg · Medium Dog</span>
                            </div>
                            <input id="dog-weight-slider" type="range" class="pfa-range" min="3" max="55" step="1" value="18">

                            <p style="font-size:13px;font-weight:700;margin:16px 0 4px;">Choose Nutrition Format:</p>
                            <div class="pfa-chips" id="dog-diet-chips">
                                <button type="button" class="pfa-chip is-active" data-diet="fresh">Fresh Cooked Meals</button>
                                <button type="button" class="pfa-chip" data-diet="kibble">Grain-Free Kibble</button>
                                <button type="button" class="pfa-chip" data-diet="airdried">Air-Dried Roo &amp; Beef</button>
                            </div>

                            <div class="pfa-kpi-grid">
                                <div class="pfa-kpi">
                                    <span>Daily Portion</span>
                                    <strong id="dog-daily-grams">504 g / day</strong>
                                </div>
                                <div class="pfa-kpi">
                                    <span>Recommended Box</span>
                                    <strong id="dog-pack-size">16 × 500g Fresh Meal Box (~16d)</strong>
                                </div>
                                <div class="pfa-kpi">
                                    <span>One-Time Purchase</span>
                                    <strong id="dog-onetime-price">A$132.00</strong>
                                </div>
                                <div class="pfa-kpi is-save">
                                    <span style="color:#094f34;font-weight:700;">Auto-Ship (Save 15%)</span>
                                    <strong id="dog-sub-price">A$112.20 every 2 wks</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: AU Postcode Checker -->
                    <div class="pfa-panel" data-pfa-panel="postcode">
                        <div>
                            <span class="pfa-pill">Built for Australia Shipping</span>
                            <h3 style="font-family:var(--font-serif);font-size:28px;margin:12px 0 10px;">4-Digit AU Postcode Cold-Chain &amp; Heavy-Bag Checker</h3>
                            <p style="color:#52625a;font-size:15px;margin:0 0 18px;">
                                Protect margins on chilled dog meals and 15–20kg kibble bags. Shoppers verify Melbourne, Sydney, Brisbane, Perth or Adelaide chilled delivery before checkout — with zero A$69/mo postcode app fees.
                            </p>
                            <ul class="pfa-checks">
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Instant metro chilled-van validation by Australian postcode</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> AusPost, Sendle, Shippit &amp; Starshipit weight-tier synced</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Guides regional buyers cleanly to shelf-stable treats &amp; dry food</li>
                            </ul>
                        </div>

                        <div class="pfa-panel-card">
                            <label for="au-postcode-input" style="display:block;font-size:13.5px;font-weight:700;margin-bottom:8px;">
                                Test any 4-digit Australian postcode:
                            </label>
                            <div style="display:flex;gap:8px;">
                                <input id="au-postcode-input" type="text" maxlength="4" class="pfa-input" value="3000" placeholder="e.g. 3000, 2000, 4000">
                                <button type="button" id="au-postcode-btn" class="pfa-btn pfa-btn-emerald" style="padding:11px 18px;">Check</button>
                            </div>
                            <div class="pfa-chips" id="au-postcode-chips">
                                <button type="button" class="pfa-chip is-active" data-pc="3000">3000 Melbourne</button>
                                <button type="button" class="pfa-chip" data-pc="2000">2000 Sydney</button>
                                <button type="button" class="pfa-chip" data-pc="4000">4000 Brisbane</button>
                                <button type="button" class="pfa-chip" data-pc="6000">6000 Perth</button>
                                <button type="button" class="pfa-chip" data-pc="4870">4870 Cairns Regional</button>
                            </div>
                            <div id="au-postcode-result" style="background:#fff9f0;border:2px solid #0e6b47;border-radius:14px;padding:15px;font-size:14px;line-height:1.55;">
                                <strong style="color:#094f34;display:block;margin-bottom:4px;">✓ Melbourne Metro (VIC 3000) — Chilled Fresh Delivery Ready</strong>
                                <span style="color:#52625a;">Chilled fresh dog food van delivery (Next-day · Free over A$120) + AusPost Metro for air-dried treats &amp; kibble.</span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: AU Multi-Vendor Pet Marketplace -->
                    <div class="pfa-panel" data-pfa-panel="marketplace">
                        <div>
                            <span class="pfa-pill pfa-pill-orange">Multi-Vendor Shopify Architecture</span>
                            <h3 style="font-family:var(--font-serif);font-size:28px;margin:12px 0 10px;">Curated Australian Pet Goods Marketplace on Shopify</h3>
                            <p style="color:#52625a;font-size:15px;margin:0 0 18px;">
                                Bring 50+ local Aussie dog treat kitchens, organic raw brands, groomers and boutique accessory makers under one joyful Shopify marketplace — with automated commissions and per-vendor AusPost shipping.
                            </p>
                            <ul class="pfa-checks">
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Self-serve vendor dashboards for SKU uploads &amp; order dispatch</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Automated 12–20% marketplace commission &amp; Stripe split payouts</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Filter by Breed, Grain-Free, Australian-Made, Vet-Approved &amp; Auto-Ship</li>
                            </ul>
                        </div>

                        <div class="pfa-panel-card" style="display:grid;gap:12px;">
                            <div style="background:#fff9f0;border:1px solid #ebe4d8;border-radius:14px;padding:14px;">
                                <strong style="color:#0e6b47;font-size:14px;">1. One Joyful Pet-Parent Cart</strong>
                                <p style="margin:4px 0 0;font-size:13px;color:#52625a;">Buy fresh dog meals from a Melbourne kitchen + kangaroo jerky from Queensland in a single Shopify checkout.</p>
                            </div>
                            <div style="background:#fff9f0;border:1px solid #ebe4d8;border-radius:14px;padding:14px;">
                                <strong style="color:#0e6b47;font-size:14px;">2. Automatic Vendor Order &amp; Commission Split</strong>
                                <p style="margin:4px 0 0;font-size:13px;color:#52625a;">Shipturtle / Webkul routes each line item straight to the right Aussie maker and logs your marketplace fee cleanly.</p>
                            </div>
                            <div style="background:#e8f5ee;border:1px solid #9dd4b8;border-radius:14px;padding:14px;">
                                <strong style="color:#094f34;font-size:14px;">3. Vendor Dispatch &amp; GST-Ready Payouts</strong>
                                <p style="margin:4px 0 0;font-size:13px;color:#52625a;">Suppliers print AusPost/Sendle labels from their portal while you control SEO, reviews and brand standards centrally.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         6. INTERACTIVE AUD COST-SAVING CALCULATOR + APP LEDGER
    ════════════════════════════════════════════════ -->
    <section class="pfa-section pfa-section-cream" id="cost-calculator" aria-labelledby="pfa-calc-title">
        <div class="pfa-wrap">
            <div class="pfa-heading">
                <span class="pfa-pill"><i class="bi bi-calculator-fill" aria-hidden="true"></i> Interactive AUD Cost-Saving Calculator</span>
                <h2 id="pfa-calc-title">See How Much Your <span>Pet Brand or AU Agency</span> Saves Every Year</h2>
                <p>Adjust your current monthly Shopify agency retainer, paid app bill and number of stores to see your projected annual AUD savings.</p>
            </div>

            <div class="pfa-calc-grid">
                <div class="pfa-calc-left">
                    <div style="margin-bottom:22px;">
                        <div class="pfa-slider-top">
                            <label for="calc-retainer">Current Monthly Dev / Agency Retainer (per store)</label>
                            <span class="pfa-mono-badge" id="calc-retainer-val">A$3,500</span>
                        </div>
                        <input id="calc-retainer" type="range" class="pfa-range" min="1000" max="10000" step="250" value="3500">
                    </div>

                    <div style="margin-bottom:22px;">
                        <div class="pfa-slider-top">
                            <label for="calc-apps">Current Monthly Paid Shopify Apps (per store)</label>
                            <span class="pfa-mono-badge" id="calc-apps-val">A$650</span>
                        </div>
                        <input id="calc-apps" type="range" class="pfa-range" min="100" max="2500" step="50" value="650">
                    </div>

                    <div>
                        <div class="pfa-slider-top">
                            <label for="calc-stores">Number of Shopify Stores (1 = Brand, 2+ = AU Agency)</label>
                            <span class="pfa-mono-badge" id="calc-stores-val">1 Store</span>
                        </div>
                        <input id="calc-stores" type="range" class="pfa-range" min="1" max="20" step="1" value="1">
                    </div>

                    <div class="pfa-app-table" aria-label="Typical paid Shopify apps we replace natively">
                        <div class="pfa-app-row pfa-app-head">
                            <span>Paid App We Replace Natively</span>
                            <span>App Rent</span>
                            <span>Our Code</span>
                        </div>
                        <div class="pfa-app-row">
                            <span>Dog Feeding &amp; Bundle Builder Apps</span>
                            <span style="text-decoration:line-through;color:#b91c1c;">A$208/mo</span>
                            <span style="color:#0e6b47;font-weight:700;">A$0/mo</span>
                        </div>
                        <div class="pfa-app-row">
                            <span>Postcode Checker + Sticky Cart Upsell</span>
                            <span style="text-decoration:line-through;color:#b91c1c;">A$148/mo</span>
                            <span style="color:#0e6b47;font-weight:700;">A$0/mo</span>
                        </div>
                        <div class="pfa-app-row">
                            <span>Annual App Fees Returned to You</span>
                            <span style="text-decoration:line-through;color:#b91c1c;">A$4,272/yr</span>
                            <span style="color:#094f34;">100% Saved</span>
                        </div>
                    </div>
                </div>

                <div class="pfa-calc-right pfa-paw-bg">
                    <div>
                        <span class="pfa-pill pfa-pill-yellow">Projected Annual AUD Savings</span>
                        <div class="pfa-calc-big" id="calc-annual-total">A$26,610 / yr</div>
                        <p style="margin:0;color:#d6ede2;font-size:14.5px;">
                            Approx. <strong style="color:#fff;" id="calc-monthly-total">A$2,218 / mo</strong> saved every month — reinvest into Meta/Google Ads, fresh ingredients, or agency profit.
                        </p>

                        <div class="pfa-calc-mini-grid">
                            <div class="pfa-calc-mini">
                                <span>Retainer Saved / mo</span>
                                <strong id="calc-dev-saved">A$1,925</strong>
                            </div>
                            <div class="pfa-calc-mini">
                                <span>Paid Apps Cut / mo</span>
                                <strong id="calc-apps-saved">A$293</strong>
                            </div>
                        </div>
                    </div>

                    <button type="button" id="pfa-lock-savings" class="pfa-btn pfa-btn-orange" style="width:100%;">
                        Send My Free Audit With These Numbers <i class="bi bi-arrow-down-circle" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         7. WHITE-LABEL AU AGENCY MARGIN + AUD PRICING
    ════════════════════════════════════════════════ -->
    <section class="pfa-section" id="pricing" aria-labelledby="pfa-pricing-title">
        <div class="pfa-wrap">
            <!-- Dedicated White-Label Box for Existing Australian Agencies -->
            <div class="pfa-agency-box">
                <div>
                    <span class="pfa-pill" style="background:#16241d;color:#f8c936;">🏢 For Existing Australian Agencies</span>
                    <h3>Turn Client Shopify Maintenance Into 65%+ Pure Agency Margin</h3>
                    <p style="margin:0 0 18px;color:#44403c;font-size:15px;">
                        Keep your Sydney, Melbourne, Brisbane or Perth client relationships while our NDA-backed Shopify pod handles Liquid customisations, paid-app cleanups, Core Web Vitals speed and monthly CRO behind your brand.
                    </p>
                    <a href="#audit-form" class="pfa-btn pfa-btn-emerald">Request White-Label AU Rate Card <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </div>

                <div class="pfa-margin-card">
                    <div class="pfa-margin-line"><span>Your AU Agency Bills Client</span><strong>A$4,000 / mo</strong></div>
                    <div class="pfa-margin-line"><span>Chulbul White-Label Pod</span><strong>A$1,299 / mo</strong></div>
                    <div class="pfa-margin-line"><span>Your Monthly Net Profit</span><strong>A$2,701 / mo (67%)</strong></div>
                </div>
            </div>

            <div class="pfa-heading">
                <span class="pfa-pill pfa-pill-orange"><i class="bi bi-tags-fill" aria-hidden="true"></i> Transparent AUD Packages</span>
                <h2 id="pfa-pricing-title">Simple Pricing That <span>Pays for Itself</span> in Shopify Savings</h2>
                <p>Zero lock-in contracts. Start with our A$799 App-Cost Cleanup Sprint or plug in our monthly Shopify store care &amp; CRO pod.</p>
            </div>

            <div class="pfa-pricing-grid">
                <article class="pfa-price-card">
                    <span class="pfa-pill">Quick-Win Sprint</span>
                    <h3>App-Cost Cleanup &amp; Speed Fix</h3>
                    <div class="pfa-price-val">A$799</div>
                    <p style="font-size:13px;color:#788880;margin:6px 0 20px;">One-time sprint · Delivered in 7–10 days</p>
                    <ul class="pfa-checks" style="flex:1;margin-bottom:24px;">
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Full Shopify paid app &amp; subscription cost audit</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Replace up to 3 paid apps with native Shopify 2.0 code</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Mobile LCP &amp; Core Web Vitals speed optimisation</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Trial-box &amp; auto-ship conversion checklist</li>
                    </ul>
                    <a href="#audit-form" class="pfa-btn pfa-btn-outline" style="width:100%;">Book A$799 Sprint</a>
                </article>

                <article class="pfa-price-card is-hot">
                    <span class="pfa-hot-tag">★ Most Popular · Brands &amp; AU Agencies</span>
                    <span class="pfa-pill pfa-pill-orange">Ongoing Management</span>
                    <h3>Monthly Shopify Care &amp; CRO Pod</h3>
                    <div class="pfa-price-val">A$999<span> / mo</span></div>
                    <p style="font-size:13px;color:#788880;margin:6px 0 20px;">Month-to-month · NDA White-Label available</p>
                    <ul class="pfa-checks" style="flex:1;margin-bottom:24px;">
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Dedicated Shopify 2.0 developer &amp; UI designer</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Monthly landing pages, seasonal promos &amp; bundles</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Continuous paid-app cost cuts &amp; speed monitoring</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Subscription, Klaviyo &amp; AusPost shipping updates</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Weekly AEST / AWST review check-ins</li>
                    </ul>
                    <a href="#audit-form" class="pfa-btn pfa-btn-orange" style="width:100%;">Start Monthly Care Pod</a>
                </article>

                <article class="pfa-price-card">
                    <span class="pfa-pill">Complete Build</span>
                    <h3>Custom Pet Store or Marketplace</h3>
                    <div class="pfa-price-val">A$2,499<span>+</span></div>
                    <p style="font-size:13px;color:#788880;margin:6px 0 20px;">Milestone-based · 3–6 week launch window</p>
                    <ul class="pfa-checks" style="flex:1;margin-bottom:24px;">
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Joyful custom Shopify 2.0 theme for Aussie pet parents</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Native Dog Feeding Calculator &amp; Auto-Ship UX</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> AU cold-chain postcodes &amp; AusPost/Sendle shipping</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Optional Multi-Vendor AU Pet Marketplace setup</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> GA4, Meta Pixel, Google Ads &amp; SEO 301 QA</li>
                    </ul>
                    <a href="#audit-form" class="pfa-btn pfa-btn-emerald" style="width:100%;">Scope My Pet Store</a>
                </article>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         8. FAQS (AUTO-MATCHED TO FAQPAGE JSON-LD)
    ════════════════════════════════════════════════ -->
    <section class="pfa-section pfa-section-cream" id="faq" aria-labelledby="pfa-faq-title">
        <div class="pfa-wrap">
            <div class="pfa-heading">
                <span class="pfa-pill"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i> Common Questions</span>
                <h2 id="pfa-faq-title">Australia Pet Food Shopify <span>FAQs</span></h2>
                <p>Clear answers on our Shopify store design, monthly management, cost-saving audits, white-label AU agency NDAs and AUD pricing.</p>
            </div>

            <div class="pfa-faq-grid">
                <?php foreach ($faqs as $idx => $faq): ?>
                <details<?= $idx < 2 ? ' open' : '' ?>>
                    <summary>
                        <span><?= cbd_escape_text($faq['q']) ?></span>
                        <i class="bi bi-plus-lg" aria-hidden="true" style="color:#0e6b47;flex-shrink:0;"></i>
                    </summary>
                    <p><?= cbd_escape_text($faq['a']) ?></p>
                </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         9. FINAL FREE 5-MIN LOOM AUDIT FORM (EMERALD + PAWS)
    ════════════════════════════════════════════════ -->
    <section class="pfa-section pfa-section-emerald pfa-paw-bg" id="audit-form" aria-labelledby="pfa-form-title">
        <div class="pfa-wrap pfa-audit-grid">
            <div>
                <span class="pfa-pill pfa-pill-yellow"><i class="bi bi-camera-video-fill" aria-hidden="true"></i> Free 5-Min Loom Store Teardown</span>
                <h2 id="pfa-form-title" style="font-family:var(--font-serif);font-size:clamp(30px,3.8vw,44px);line-height:1.12;margin:16px 0 14px;color:#fff;">
                    Upgrade Your Shopify Pet Store &amp; Cut Monthly Costs by <span style="color:#f8c936;">Up to 60%</span>
                </h2>
                <p style="color:#d6ede2;font-size:15.5px;margin:0 0 24px;">
                    Send us your Australian pet store URL or agency site. Within 24 hours our Shopify team records a personalised 5-minute Loom showing exact paid apps you can cut, design/CRO wins, and a clear AUD management plan.
                </p>
                <ul class="pfa-checks" style="color:#ecfdf5;margin-bottom:28px;">
                    <li><i class="bi bi-check-circle-fill" style="color:#f8c936;" aria-hidden="true"></i> Zero store admin password needed for the audit</li>
                    <li><i class="bi bi-check-circle-fill" style="color:#f8c936;" aria-hidden="true"></i> Delivered in 24 hrs · AEST &amp; AWST friendly reviews</li>
                    <li><i class="bi bi-check-circle-fill" style="color:#f8c936;" aria-hidden="true"></i> Strict NDA ready on day one for AU agency partners</li>
                </ul>
                <a href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%27d%20like%20a%20free%205-min%20Shopify%20design%2C%20management%20and%20cost-saving%20audit%20for%20our%20Australian%20pet%20store."
                   target="_blank" rel="noopener noreferrer" class="pfa-btn pfa-btn-glass">
                    <i class="bi bi-whatsapp" style="color:#4ade80;" aria-hidden="true"></i> Chat Directly on WhatsApp
                </a>
            </div>

            <div class="pfa-form-card">
                <h3 style="font-family:var(--font-serif);font-size:24px;margin:0 0 6px;color:#16241d;">Request Your Free AUD Cost-Saving Audit</h3>
                <p style="font-size:13.5px;color:#52625a;margin:0 0 20px;">Keep the entire app-reduction &amp; speed roadmap — zero obligation.</p>

                <form id="pfa-main-form" class="pfa-form-stack" novalidate>
                    <input type="text" name="_hp" value="" tabindex="-1" autocomplete="off" style="position:absolute;left:-9999px;opacity:0;" aria-hidden="true">
                    <div class="pfa-form-2">
                        <input id="pfa-name-input" type="text" name="name" class="pfa-input" placeholder="Full Name *" required>
                        <input type="email" name="email" class="pfa-input" placeholder="Work Email *" required>
                    </div>
                    <div class="pfa-form-2">
                        <input type="tel" name="phone" class="pfa-input" placeholder="Phone / WhatsApp (+61...)">
                        <input id="pfa-business-input" type="text" name="business" class="pfa-input" placeholder="Store URL or AU Agency Name *" required>
                    </div>
                    <div class="pfa-form-2">
                        <select id="pfa-service-select" name="service" class="pfa-select" aria-label="Service needed">
                            <option value="AU Pet Shopify App Cleanup Sprint (A$799)">App-Cost Cleanup &amp; Speed Sprint (A$799)</option>
                            <option value="AU Pet Shopify Monthly Care Pod (A$999/mo)">Monthly Store Care &amp; CRO Pod (A$999/mo)</option>
                            <option value="AU Pet Food Shopify Store Build (A$2,499+)">New Pet Food / Dog Goods Shopify Store</option>
                            <option value="AU Pet Multi-Vendor Marketplace">Australia Pet Goods Multi-Vendor Marketplace</option>
                            <option value="AU Agency White-Label Shopify Partner">White-Label Partner for Existing AU Agency</option>
                        </select>
                        <select name="budget" class="pfa-select" aria-label="Current monthly spend">
                            <option value="Under A$1,000/mo">Current Spend: Under A$1,000/mo</option>
                            <option value="A$1,000 - A$3,000/mo" selected>Current Spend: A$1,000 – A$3,000/mo</option>
                            <option value="A$3,000 - A$6,000/mo">Current Spend: A$3,000 – A$6,000/mo</option>
                            <option value="A$6,000+/mo (Agency / Plus)">Current Spend: A$6,000+/mo (Agency / Plus)</option>
                        </select>
                    </div>
                    <textarea id="pfa-message-input" name="message" rows="4" class="pfa-textarea" placeholder="Tell us your store URL, paid apps you want to cut, fresh/kibble shipping setup, or AU agency white-label needs..."></textarea>
                    <button type="submit" class="pfa-btn pfa-btn-orange" style="width:100%;">
                        Send My Free 5-Min Cost Audit <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>
                <div id="pfa-main-status" class="pfa-status" role="status" aria-live="polite"></div>
            </div>
        </div>
    </section>

</main>

<!-- PawVita-Style Emerald Trust Footer Bar -->
<div class="pfa-green-strip">
    <div class="pfa-wrap pfa-green-strip-grid">
        <div><i class="bi bi-palette-fill" aria-hidden="true"></i> Custom Shopify 2.0 Design</div>
        <div><i class="bi bi-gear-wide-connected" aria-hidden="true"></i> Dedicated Store Management</div>
        <div><i class="bi bi-piggy-bank-fill" aria-hidden="true"></i> 40–60% Lower Shopify Costs</div>
        <div><i class="bi bi-shield-lock-fill" aria-hidden="true"></i> NDA White-Label for AU Agencies</div>
    </div>
</div>

<!-- Minimal Standalone Footer -->
<footer class="pfa-footer">
    <div class="pfa-wrap pfa-footer-row">
        <div>
            <strong style="color:#0e6b47;">Chulbul Design</strong> · Headquartered in Gurugram, India · Serving Melbourne, Sydney, Brisbane, Perth &amp; Adelaide remotely.
        </div>
        <div class="pfa-footer-links">
            <a href="<?= cbd_escape_text($base) ?>/">Main Website</a>
            <a href="<?= cbd_escape_text($base) ?>/shopify-development">Shopify Services</a>
            <a href="<?= cbd_escape_text($base) ?>/multi-vendor-marketplace">Marketplaces</a>
            <a href="<?= cbd_escape_text($base) ?>/city/melbourne/ecommerce-development">Melbourne</a>
            <a href="<?= cbd_escape_text($base) ?>/city/sydney/ecommerce-development">Sydney</a>
            <a href="<?= cbd_escape_text($base) ?>/privacy-policy">Privacy</a>
        </div>
    </div>
</footer>

<div class="pfa-mobile-sticky" aria-label="Quick mobile actions">
    <a href="#audit-form" style="background:#f26b1d;color:#ffffff;">Free 5-Min Audit</a>
    <a href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%27d%20like%20a%20free%20AU%20Pet%20Food%20Shopify%20audit." target="_blank" rel="noopener noreferrer" style="background:#f8c936;color:#16241d;">
        <i class="bi bi-whatsapp" aria-hidden="true"></i> WhatsApp
    </a>
</div>

<!-- 100% Isolated Page Script -->
<script src="<?= cbd_escape_text($base) ?>/assets/js/pet-food-shopify-australia.js?v=<?= $jsVersion ?>" defer></script>
</body>
</html>
