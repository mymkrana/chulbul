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

$cssVersion = (int)(@filemtime(__DIR__ . '/assets/css/pet-food-shopify-australia.css') ?: 1);
$jsVersion  = (int)(@filemtime(__DIR__ . '/assets/js/pet-food-shopify-australia.js') ?: 1);

$heroWebpUrl  = cbd_asset_url('/assets/images/pet-food-au/au-pet-happy-owner-hero.webp');
$heroJpgUrl   = cbd_asset_url('/assets/images/pet-food-au/au-pet-happy-owner-hero.jpg');
$caseWebpUrl  = cbd_asset_url('/assets/images/pet-food-au/au-pet-food-subscription-ux.webp');
$caseJpgUrl   = cbd_asset_url('/assets/images/pet-food-au/au-pet-food-subscription-ux.jpg');
$bffWebpUrl   = cbd_asset_url('/assets/images/pet-food-au/brunswick-fur-food-preview.webp');
$bffJpgUrl    = cbd_asset_url('/assets/images/pet-food-au/brunswick-fur-food-preview.jpg');
$shareImage   = cbd_public_url('/assets/images/pet-food-au/au-pet-happy-owner-hero.jpg');

$pageTitle = 'Pet Food Shopify Development Australia | Chulbul Design';
$pageDescription = 'Build a high-converting Shopify store for your Australian pet food brand. Get custom Shopify design, subscription features, smart product filters and ongoing support from Chulbul Design.';

$faqs = [
    [
        'q' => 'How much does it cost to build a Shopify pet food store in Australia?',
        'a' => 'Starting prices on this page are A$799 (one-time) for an App Cost Cleanup & Speed Sprint, A$999 per month for Ongoing Shopify Store Management & CRO, and A$2,499+ (one-time, starting from) for a Custom Pet Store or Marketplace build. Final pricing for custom builds depends on catalogue size, design complexity, subscription requirements, and third-party integrations, and is confirmed in a written quotation.',
    ],
    [
        'q' => 'Can you build a Shopify subscription website for pet food?',
        'a' => 'Yes. We design and configure Shopify product pages, recurring delivery selectors, trial-pack flows, and customer portal layouts so Australian pet owners can schedule repeat orders at their preferred frequency. Subscription billing relies on Shopify Selling Plans or a compatible Shopify subscription app selected during scoping.',
    ],
    [
        'q' => 'Can you redesign my existing pet store Shopify website?',
        'a' => 'Yes. We audit your current Shopify theme, navigation, mobile product pages, and checkout flow, then upgrade or rebuild the storefront on Online Store 2.0 while preserving your existing product URLs, SEO metadata, and customer data.',
    ],
    [
        'q' => 'Can you help reduce Shopify app costs?',
        'a' => 'Yes. Many pet food stores accumulate overlapping monthly apps for simple storefront features like feeding tables, bundle layouts, postcode notices, or cart upsells. We audit your installed apps and replace suitable front-end widgets with lightweight native Shopify 2.0 Liquid code where practical, helping lower unnecessary recurring app spend without sacrificing usability.',
    ],
    [
        'q' => 'Can you integrate recurring orders and subscription features?',
        'a' => 'Yes. Depending on your operational requirements, we can implement recurring order workflows using Shopify Selling Plans or integrate third-party subscription tools that support flexible intervals, order skipping, and subscription management. Specific app compatibility is confirmed during discovery.',
    ],
    [
        'q' => 'Do you provide ongoing Shopify maintenance?',
        'a' => 'Yes. Our Monthly Care & CRO package (A$999 per month) covers theme updates, new landing pages and seasonal promotions, product and content updates, troubleshooting, storefront speed checks, and ongoing conversion improvements.',
    ],
    [
        'q' => 'Can you help with Australian shipping and postcode-based delivery?',
        'a' => 'Yes. We configure Shopify shipping zones, weight-based rates for heavy pet food bags, local delivery rules, and storefront postcode availability messaging tailored to your courier setup. Any carrier or chilled-delivery integration depends on the shipping providers and apps your business uses.',
    ],
    [
        'q' => 'How long does a custom Shopify pet store project take?',
        'a' => 'Timelines vary based on design scope, product data readiness, custom features, and review turnaround. Smaller cleanup sprints typically take days to a couple of weeks, while custom Shopify store builds generally span several weeks. An estimated schedule and milestone plan are provided in your written quote.',
    ],
    [
        'q' => 'Do you work with Australian pet food brands remotely?',
        'a' => 'Yes. Chulbul Design is headquartered in Gurugram, India, and works with Australian pet food and ecommerce businesses remotely. We coordinate via scheduled AEST/AEDT and AWST-friendly calls, shared staging previews, email, and WhatsApp.',
    ],
];

$organizationSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    '@id' => 'https://www.chulbuldesign.com/#organization',
    'name' => 'Chulbul Design',
    'url' => 'https://www.chulbuldesign.com/',
    'logo' => 'https://www.chulbuldesign.com/assets/images/logo/chulbuldesign.svg',
    'telephone' => '+919990548795',
    'email' => 'info@chulbuldesign.com',
    'address' => [
        '@type' => 'PostalAddress',
        'streetAddress' => 'Sector 15, Flat No. 1277',
        'addressLocality' => 'Gurugram',
        'addressRegion' => 'Haryana',
        'addressCountry' => 'IN',
    ],
    'knowsAbout' => [
        'Pet Food Shopify Development',
        'Shopify Online Store 2.0',
        'Pet Store Ecommerce Design',
        'Shopify Subscriptions and Recurring Orders',
        'Shopify App Cost Optimization',
    ],
    'sameAs' => [
        'https://www.facebook.com/chulbuldesign',
        'https://www.instagram.com/chulbuldesign',
        'https://twitter.com/ChulbulDesign',
        'https://www.linkedin.com/company/chulbuldesign/',
    ],
];

$serviceSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    '@id' => $canonical . '#service',
    'name' => 'Pet Food Shopify Development Australia',
    'serviceType' => 'Custom Shopify Store Development, Pet Ecommerce Design, Subscription UX, and Ongoing Shopify Management',
    'url' => $canonical,
    'description' => $pageDescription,
    'areaServed' => ['@type' => 'Country', 'name' => 'Australia'],
    'provider' => ['@id' => 'https://www.chulbuldesign.com/#organization'],
    'hasOfferCatalog' => [
        '@type' => 'OfferCatalog',
        'name' => 'Pet Food Shopify Development Pricing (AUD)',
        'itemListElement' => [
            [
                '@type' => 'Offer',
                'name' => 'App Cost Cleanup Sprint',
                'description' => 'One-time Shopify app audit, native Online Store 2.0 code consolidation, and storefront performance optimisation.',
                'priceSpecification' => [
                    '@type' => 'PriceSpecification',
                    'price' => '799',
                    'priceCurrency' => 'AUD',
                ],
            ],
            [
                '@type' => 'Offer',
                'name' => 'Monthly Care & CRO Pod',
                'description' => 'Ongoing monthly Shopify store management, content and campaign updates, troubleshooting, and conversion improvements.',
                'priceSpecification' => [
                    '@type' => 'UnitPriceSpecification',
                    'price' => '999',
                    'priceCurrency' => 'AUD',
                    'unitText' => 'MONTH',
                ],
            ],
            [
                '@type' => 'Offer',
                'name' => 'Custom Pet Store / Marketplace',
                'description' => 'Custom Shopify 2.0 pet food store or marketplace development, starting from A$2,499 subject to written quotation.',
                'priceSpecification' => [
                    '@type' => 'PriceSpecification',
                    'minPrice' => '2499',
                    'priceCurrency' => 'AUD',
                ],
            ],
        ],
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
    <link rel="alternate" type="text/plain" href="https://www.chulbuldesign.com/llms.txt" title="Chulbul Design AI and LLM Reference">
    <link rel="icon" type="image/png" sizes="512x512" href="<?= cbd_escape_text($base) ?>/assets/images/logo/favicon.png">

    <!-- Preload Primary Hero LCP Image -->
    <link rel="preload" as="image" type="image/webp" href="<?= cbd_escape_text($heroWebpUrl) ?>" fetchpriority="high">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_AU">
    <meta property="og:site_name" content="Chulbul Design">
    <meta property="og:title" content="<?= cbd_escape_text($pageTitle) ?>">
    <meta property="og:description" content="<?= cbd_escape_text($pageDescription) ?>">
    <meta property="og:url" content="<?= cbd_escape_text($canonical) ?>">
    <meta property="og:image" content="<?= cbd_escape_text($shareImage) ?>">
    <meta property="og:image:width" content="896">
    <meta property="og:image:height" content="1200">
    <meta property="og:image:alt" content="Smiling dog owner with golden retriever and dog food pouch representing custom Shopify development for Australian pet food brands">
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"></noscript>

    <!-- 100% Isolated Page Stylesheet -->
    <link rel="stylesheet" href="<?= cbd_escape_text($base) ?>/assets/css/pet-food-shopify-australia.css?v=<?= $cssVersion ?>">

    <script type="application/ld+json"><?= cbd_json_ld($organizationSchema) ?></script>
    <script type="application/ld+json"><?= cbd_json_ld($serviceSchema) ?></script>
    <script type="application/ld+json"><?= cbd_json_ld($breadcrumbSchema) ?></script>
    <script type="application/ld+json"><?= cbd_json_ld($faqSchema) ?></script>
</head>
<body>

<a href="#main-content" class="pfa-skip-link">Skip to main content</a>

<!-- ═══════════════════════════════════════════════
     HEADER & ACCESSIBLE NAVIGATION
════════════════════════════════════════════════ -->
<header class="pfa-header">
    <div class="pfa-wrap pfa-header-inner">
        <div class="pfa-brand">
            <a href="<?= cbd_escape_text($base) ?>/" aria-label="Chulbul Design Home">
                <img src="<?= cbd_escape_text($base) ?>/assets/images/logo/chulbuldesign.svg" alt="Chulbul Design" width="146" height="30">
            </a>
            <span class="pfa-brand-badge">
                <i class="bi bi-bag-heart-fill" aria-hidden="true"></i>
                Pet Food Shopify · Australia
            </span>
        </div>

        <nav class="pfa-nav" aria-label="Page navigation">
            <a href="#shopify-solutions">Solutions</a>
            <a href="#live-storefront">Features</a>
            <a href="#cost-calculator">App Cost Estimator</a>
            <a href="#pricing">Pricing</a>
            <a href="#why-chulbul">Why Chulbul</a>
            <a href="#faq">FAQs</a>
        </nav>

        <div class="pfa-header-actions">
            <a href="#audit-form" class="pfa-btn pfa-btn-orange pfa-header-cta" data-track-cta="header_free_audit">
                Get a Free Audit <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
            <button type="button"
                    class="pfa-menu-btn"
                    id="pfa-menu-toggle"
                    aria-expanded="false"
                    aria-controls="pfa-mobile-nav"
                    aria-label="Toggle navigation menu">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Navigation Drawer -->
    <nav id="pfa-mobile-nav" class="pfa-mobile-nav" aria-label="Mobile page navigation" hidden>
        <div class="pfa-wrap pfa-mobile-nav-inner">
            <a href="#shopify-solutions">Shopify Solutions</a>
            <a href="#live-storefront">Interactive Store Features</a>
            <a href="#cost-calculator">App Cost Estimator</a>
            <a href="#process">Our Process</a>
            <a href="#pricing">Pricing</a>
            <a href="#why-chulbul">Why Chulbul Design</a>
            <a href="#faq">FAQs</a>
            <a href="#audit-form" class="pfa-btn pfa-btn-orange" data-track-cta="mobile_nav_free_audit">Get a Free Store Audit</a>
        </div>
    </nav>
</header>

<main id="main-content">

    <!-- Visible Breadcrumb Navigation (Matches BreadcrumbList JSON-LD) -->
    <nav class="pfa-breadcrumb-bar" aria-label="Breadcrumb">
        <div class="pfa-wrap">
            <ol class="pfa-breadcrumb">
                <li><a href="<?= cbd_escape_text($base) ?>/">Home</a></li>
                <li aria-hidden="true">/</li>
                <li><a href="<?= cbd_escape_text($base) ?>/shopify-development">Shopify Development</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page">Pet Food Shopify Australia</li>
            </ol>
        </div>
    </nav>

    <!-- ═══════════════════════════════════════════════
         1. EMERALD HERO SECTION (PRESERVED IDENTITY, REFINED HIERARCHY)
    ════════════════════════════════════════════════ -->
    <section class="pfa-hero-shell" aria-labelledby="pfa-hero-h1">
        <div class="pfa-wrap">
            <div class="pfa-hero-card pfa-paw-bg">
                <div>
                    <span class="pfa-hero-eyebrow">
                        <i class="bi bi-stars" aria-hidden="true"></i>
                        SHOPIFY DEVELOPMENT FOR AUSTRALIAN PET BRANDS
                    </span>

                    <h1 id="pfa-hero-h1">
                        Shopify Development for <span>Pet Food Brands</span> in Australia
                    </h1>

                    <p class="pfa-hero-desc" id="pfa-hero-desc">
                        Build a smarter, faster, and more profitable online pet store with custom Shopify development. From product subscriptions and recurring orders to conversion-focused design, we help Australian pet businesses create better shopping experiences.
                    </p>

                    <div class="pfa-hero-ctas">
                        <a href="#audit-form" class="pfa-btn pfa-btn-orange" data-track-cta="hero_primary_audit">
                            Get a Free Store Audit <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                        <a href="#shopify-solutions" class="pfa-btn pfa-btn-glass" data-track-cta="hero_secondary_solutions">
                            Explore Our Shopify Solutions
                        </a>
                    </div>

                    <form id="pfa-quick-form" class="pfa-hero-quickbar" novalidate>
                        <label for="pfa-quick-url" class="pfa-sr-only">Enter your Shopify store URL</label>
                        <input id="pfa-quick-url" type="url" inputmode="url" placeholder="Have a live store? Enter yourpetstore.com.au to pre-fill your audit" aria-label="Your Australian Shopify store URL">
                        <button type="submit" class="pfa-btn pfa-btn-yellow pfa-quick-btn" data-track-cta="hero_quick_url">
                            Pre-fill Audit <i class="bi bi-arrow-down-short" aria-hidden="true"></i>
                        </button>
                    </form>

                    <ul class="pfa-hero-proof-row" aria-label="Core capabilities">
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Custom Shopify Development</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Pet Ecommerce Solutions</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Subscription &amp; Recurring Orders</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Ongoing Shopify Support</li>
                    </ul>
                </div>

                <!-- Sunny-Yellow Framed Hero Dog + Owner Portrait -->
                <div class="pfa-hero-art">
                    <div class="pfa-portrait-frame">
                        <picture>
                            <source srcset="<?= cbd_escape_text($heroWebpUrl) ?>" type="image/webp">
                            <img src="<?= cbd_escape_text($heroJpgUrl) ?>"
                                 alt="Smiling Australian dog owner hugging a golden retriever beside a natural dog food pouch and bowl"
                                 width="896" height="1200" fetchpriority="high" decoding="async">
                        </picture>
                    </div>

                    <div class="pfa-sticker is-left">
                        <i class="bi bi-code-slash" aria-hidden="true"></i>
                        <div>Native Online Store 2.0<br><span>Clean Liquid &amp; Speed Focus</span></div>
                    </div>

                    <div class="pfa-sticker is-right">
                        <i class="bi bi-repeat" aria-hidden="true"></i>
                        <div>Ongoing Shopify Support<br><span class="is-orange-txt">From A$999 / month</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         2. CIRCULAR PET-SHOPIFY CAPABILITY STRIP
    ════════════════════════════════════════════════ -->
    <section class="pfa-circle-strip" aria-label="Pet ecommerce Shopify capabilities">
        <div class="pfa-wrap">
            <div class="pfa-circle-grid">
                <a href="#shopify-solutions" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-palette-fill" aria-hidden="true"></i></div>
                    <span>Custom Shopify Design</span>
                </a>
                <a href="#live-storefront" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-repeat" aria-hidden="true"></i></div>
                    <span>Pet Subscriptions</span>
                </a>
                <a href="#shopify-solutions" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-funnel-fill" aria-hidden="true"></i></div>
                    <span>Smart Product Filters</span>
                </a>
                <a href="#live-storefront" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-sliders" aria-hidden="true"></i></div>
                    <span>Feeding Calculators</span>
                </a>
                <a href="#live-storefront" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-truck" aria-hidden="true"></i></div>
                    <span>AU Delivery Rules</span>
                </a>
                <a href="#cost-calculator" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-piggy-bank-fill" aria-hidden="true"></i></div>
                    <span>App Cost Reduction</span>
                </a>
                <a href="#shopify-solutions" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-speedometer2" aria-hidden="true"></i></div>
                    <span>Store Performance</span>
                </a>
                <a href="#pricing" class="pfa-circle-item">
                    <div class="pfa-circle-icon"><i class="bi bi-gear-wide-connected" aria-hidden="true"></i></div>
                    <span>Ongoing Management</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         3. THREE CORE SHOPIFY SERVICE CARDS (YELLOW / GREEN / ORANGE)
    ════════════════════════════════════════════════ -->
    <section class="pfa-deals-section" aria-labelledby="pfa-pillars-title">
        <div class="pfa-wrap">
            <div class="pfa-heading pfa-heading-compact">
                <span class="pfa-pill"><i class="bi bi-grid-fill" aria-hidden="true"></i> Core Shopify Services</span>
                <h2 id="pfa-pillars-title">Build a Better Shopify Store for Your <span>Pet Food Business</span></h2>
                <p>Whether you are launching a new Australian pet food brand, migrating onto Shopify, or fixing a slow existing storefront, we structure our services around measurable store outcomes.</p>
            </div>

            <div class="pfa-deals-grid">
                <article class="pfa-deal-card is-yellow">
                    <div>
                        <span class="pfa-deal-badge">01 · DESIGN &amp; DEVELOPMENT</span>
                        <h3>Custom Shopify Pet Store Design</h3>
                        <p>Tailored Online Store 2.0 themes with clear nutrition hierarchy, dietary filters, mobile-friendly product pages, and smooth checkout flows — starting from <strong>A$2,499+</strong>.</p>
                    </div>
                    <a href="#shopify-solutions" class="pfa-deal-link" data-track-cta="pillar_custom_design">Explore Solutions <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </article>

                <article class="pfa-deal-card is-green pfa-paw-bg">
                    <div>
                        <span class="pfa-deal-badge">02 · ONGOING SUPPORT</span>
                        <h3>Shopify Store Management &amp; Optimization</h3>
                        <p>Consistent monthly support covering catalogue updates, seasonal campaigns, speed checks, troubleshooting, and conversion improvements — <strong>A$999 / month</strong>.</p>
                    </div>
                    <a href="#pricing" class="pfa-deal-link" data-track-cta="pillar_ongoing_management">View Care Package <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </article>

                <article class="pfa-deal-card is-orange">
                    <div>
                        <span class="pfa-deal-badge">03 · APP CLEANUP SPRINT</span>
                        <h3>Reduce Unnecessary Shopify App Costs</h3>
                        <p>Review overlapping monthly apps and replace suitable front-end widgets with lightweight native Liquid code to cut bloat and recurring fees — <strong>A$799 one-time</strong>.</p>
                    </div>
                    <a href="#cost-calculator" class="pfa-deal-link" data-track-cta="pillar_app_cleanup">Estimate App Spend <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </article>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         4. PET FOOD SHOPIFY SOLUTIONS SECTION (WHAT / WHY / PROBLEM / HOW)
    ════════════════════════════════════════════════ -->
    <section class="pfa-section pfa-section-cream pfa-paw-bg-light" id="shopify-solutions" aria-labelledby="pfa-solutions-title">
        <div class="pfa-wrap">
            <div class="pfa-heading">
                <span class="pfa-pill"><i class="bi bi-bag-check-fill" aria-hidden="true"></i> Pet Ecommerce Expertise</span>
                <h2 id="pfa-solutions-title">Shopify Solutions Designed for <span>Pet Brands</span></h2>
                <p>Selling pet food online involves repeat purchase cycles, varied bag weights, dietary needs, and local Australian delivery rules. Every solution below addresses a real operational or conversion bottleneck.</p>
            </div>

            <!-- Preserved Featured Visual Card -->
            <div class="pfa-bff-card">
                <div class="pfa-bff-top">
                    <div class="pfa-bff-body">
                        <span class="pfa-pill pfa-pill-orange">Shopify Online Store 2.0 · Australian Pet Retail</span>
                        <h3>Purpose-Built Storefronts That Make Ordering <span>Simple for Pet Parents</span></h3>
                        <p class="pfa-body-copy">
                            Generic retail templates rarely fit how Australians buy dog food, cat nutrition, treats, or supplements. Shoppers want to confirm ingredients, match portions to their pet's weight, check delivery availability for their postcode, and set up hassle-free recurring orders on mobile. We combine <a href="<?= cbd_escape_text($base) ?>/shopify-development" class="pfa-inline-link">custom Shopify development</a> with practical <a href="<?= cbd_escape_text($base) ?>/ecommerce-website-development" class="pfa-inline-link">ecommerce engineering</a> so your store is easy to shop and easy for your team to manage.
                        </p>
                        <div class="pfa-bff-tags">
                            <span class="pfa-bff-tag">Custom Shopify 2.0 Themes</span>
                            <span class="pfa-bff-tag">Subscription &amp; Repeat Orders</span>
                            <span class="pfa-bff-tag">Dietary &amp; Breed Filtering</span>
                            <span class="pfa-bff-tag">Australian Shipping Logic</span>
                        </div>
                        <div class="pfa-btn-row">
                            <a href="#audit-form" class="pfa-btn pfa-btn-emerald" data-track-cta="solutions_free_audit">
                                Get a Free Store Audit <i class="bi bi-arrow-right" aria-hidden="true"></i>
                            </a>
                            <a href="#live-storefront" class="pfa-btn pfa-btn-outline" data-track-cta="solutions_try_demos">
                                View Interactive Demos
                            </a>
                        </div>
                    </div>

                    <div class="pfa-bff-media">
                        <picture>
                            <source srcset="<?= cbd_escape_text($caseWebpUrl) ?>" type="image/webp">
                            <img src="<?= cbd_escape_text($caseJpgUrl) ?>"
                                 alt="Dog food subscription meal box, air-dried treats, and mobile Shopify product view"
                                 width="1376" height="768" loading="lazy" decoding="async">
                        </picture>
                    </div>
                </div>

                <!-- 6 Detailed Solution Cards Answering What / Why / Problem / How -->
                <div class="pfa-solutions-grid">
                    <article class="pfa-sol-item">
                        <div class="pfa-sol-head">
                            <i class="bi bi-window-sidebar" aria-hidden="true"></i>
                            <h3>Custom Shopify Store Development</h3>
                        </div>
                        <p><strong>The problem it solves:</strong> Generic templates bury ingredients, feeding guides, and pack options, lowering buyer confidence on mobile.</p>
                        <p><strong>How we help:</strong> We design clean Shopify 2.0 product and collection templates that highlight nutrition benefits, pack sizes, and trust details clearly so pet owners buy with confidence.</p>
                    </article>

                    <article class="pfa-sol-item">
                        <div class="pfa-sol-head">
                            <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
                            <h3>Pet Food Subscription Solutions</h3>
                        </div>
                        <p><strong>The problem it solves:</strong> Pet food is a repeat purchase, yet clunky subscribe-and-save widgets cause shoppers to check out once and forget to reorder.</p>
                        <p><strong>How we help:</strong> We build clear delivery-frequency selectors, starter-pack flows, and customer-friendly recurring order interfaces powered by Shopify Selling Plans or your chosen compatible subscription app.</p>
                    </article>

                    <article class="pfa-sol-item">
                        <div class="pfa-sol-head">
                            <i class="bi bi-funnel" aria-hidden="true"></i>
                            <h3>Product Catalogue &amp; Smart Filtering</h3>
                        </div>
                        <p><strong>The problem it solves:</strong> Shoppers leave when they cannot quickly narrow down dry food, raw meals, puppy formulas, or allergy-safe recipes.</p>
                        <p><strong>How we help:</strong> We organise metafields and collection filters by pet type, food format, life stage, breed size, protein source, and dietary needs to speed up product discovery.</p>
                    </article>

                    <article class="pfa-sol-item">
                        <div class="pfa-sol-head">
                            <i class="bi bi-calculator" aria-hidden="true"></i>
                            <h3>Interactive Feeding Calculator</h3>
                        </div>
                        <p><strong>The problem it solves:</strong> First-time buyers hesitate when they are unsure how much food to order or how long a box or bag will last.</p>
                        <p><strong>How we help:</strong> We code lightweight feeding estimators driven strictly by your brand's supplied feeding guidelines and product weights — clearly presented as brand sizing guidance, never veterinary advice.</p>
                    </article>

                    <article class="pfa-sol-item">
                        <div class="pfa-sol-head">
                            <i class="bi bi-geo-alt" aria-hidden="true"></i>
                            <h3>Australian Shipping &amp; Delivery UX</h3>
                        </div>
                        <p><strong>The problem it solves:</strong> Heavy bags and metro-only fresh deliveries create checkout surprises and abandoned carts when rules are unclear.</p>
                        <p><strong>How we help:</strong> We structure Shopify shipping zones, local delivery messaging, and postcode eligibility indicators aligned to the carriers and fulfilment rules your business actively operates.</p>
                    </article>

                    <article class="pfa-sol-item">
                        <div class="pfa-sol-head">
                            <i class="bi bi-speedometer2" aria-hidden="true"></i>
                            <h3>Shopify Store Optimization &amp; Ongoing Care</h3>
                        </div>
                        <p><strong>The problem it solves:</strong> Over time, extra apps, unoptimised imagery, and ad-hoc edits slow down mobile pages and break conversion flow.</p>
                        <p><strong>How we help:</strong> We improve page speed, navigation, product pages, and checkout flow — and provide ongoing monthly maintenance, campaign updates, and technical support as your catalogue grows.</p>
                    </article>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         5. INTERACTIVE SHOPIFY MODULE DEMONSTRATION (CLEARLY LABELLED SAMPLE DEMOS)
    ════════════════════════════════════════════════ -->
    <section class="pfa-section" id="live-storefront" aria-labelledby="pfa-demo-title">
        <div class="pfa-wrap">
            <div class="pfa-heading">
                <span class="pfa-pill pfa-pill-orange"><i class="bi bi-play-circle-fill" aria-hidden="true"></i> Interactive Storefront Concept Demo</span>
                <h2 id="pfa-demo-title">Essential Features for a <span>High-Converting Pet Store</span></h2>
                <p>Test how custom Shopify 2.0 storefront modules can guide pet owners before checkout. All figures and rules below use <strong>sample demonstration data</strong> and are customised to your store's actual products and shipping setup.</p>
            </div>

            <div class="pfa-browser">
                <div class="pfa-browser-bar">
                    <div class="pfa-dots" aria-hidden="true"><span></span><span></span><span></span></div>
                    <div class="pfa-url-bar" aria-label="Sample store preview URL">demo-pet-store.myshopify.com (Sample Demo)</div>
                    <div class="pfa-demo-tabs" role="tablist" aria-label="Interactive pet store feature demos">
                        <button type="button" id="tab-feeding" class="pfa-demo-tab is-active" role="tab" aria-selected="true" aria-controls="panel-feeding" tabindex="0" data-pfa-tab="feeding">
                            Feeding &amp; Subscriptions
                        </button>
                        <button type="button" id="tab-postcode" class="pfa-demo-tab" role="tab" aria-selected="false" aria-controls="panel-postcode" tabindex="-1" data-pfa-tab="postcode">
                            Postcode Delivery Demo
                        </button>
                        <button type="button" id="tab-marketplace" class="pfa-demo-tab" role="tab" aria-selected="false" aria-controls="panel-marketplace" tabindex="-1" data-pfa-tab="marketplace">
                            Marketplace Flow Demo
                        </button>
                    </div>
                </div>

                <div class="pfa-browser-body">
                    <!-- TAB 1: Feeding Calculator & Make Pet Food Subscriptions Easier -->
                    <div id="panel-feeding" class="pfa-panel is-active" role="tabpanel" aria-labelledby="tab-feeding" data-pfa-panel="feeding">
                        <div>
                            <span class="pfa-pill">Make Pet Food Subscriptions Easier</span>
                            <h3 class="pfa-panel-heading">Feeding Estimator &amp; Recurring Order Selector (Sample Demo)</h3>
                            <p class="pfa-panel-copy">
                                Help pet parents choose the right pack size and delivery cadence in seconds. When built natively into your Shopify 2.0 theme, a feeding estimator connects directly to your product variants and selected Shopify subscription setup without heavy third-party script embeds.
                            </p>
                            <ul class="pfa-checks">
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Adapts to fresh meals, raw packs, air-dried food, or dry kibble</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Suggests an estimated reorder cadence based on your pack weights</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Displays one-time vs recurring order options clearly before cart</li>
                            </ul>
                            <p class="pfa-demo-disclaimer">
                                <i class="bi bi-info-circle" aria-hidden="true"></i>
                                <strong>Sample demo notice:</strong> Portion weights, pack sizes, and sample pricing shown here are illustrative only. Live calculators use the exact nutrition data and formula supplied by your business and do not constitute veterinary advice.
                            </p>
                        </div>

                        <div class="pfa-panel-card">
                            <div class="pfa-demo-tag">Sample Interactive Calculator</div>
                            <div class="pfa-slider-top">
                                <label for="dog-weight-slider">Sample Dog Weight (kg)</label>
                                <span class="pfa-mono-badge" id="dog-weight-val">18 kg · Medium</span>
                            </div>
                            <input id="dog-weight-slider" type="range" class="pfa-range" min="3" max="55" step="1" value="18" aria-describedby="dog-weight-val">

                            <p class="pfa-mini-label">Select Sample Food Format:</p>
                            <div class="pfa-chips" id="dog-diet-chips" role="group" aria-label="Sample food format">
                                <button type="button" class="pfa-chip is-active" data-diet="fresh" aria-pressed="true">Fresh Cooked Meals</button>
                                <button type="button" class="pfa-chip" data-diet="kibble" aria-pressed="false">Dry Kibble</button>
                                <button type="button" class="pfa-chip" data-diet="airdried" aria-pressed="false">Air-Dried Food</button>
                            </div>

                            <div class="pfa-kpi-grid" aria-live="polite">
                                <div class="pfa-kpi">
                                    <span>Sample Daily Portion</span>
                                    <strong id="dog-daily-grams">504 g / day</strong>
                                </div>
                                <div class="pfa-kpi">
                                    <span>Illustrative Pack Match</span>
                                    <strong id="dog-pack-size">8 kg Meal Box (~16 days)</strong>
                                </div>
                                <div class="pfa-kpi">
                                    <span>Sample One-Time Price</span>
                                    <strong id="dog-onetime-price">A$132.00</strong>
                                </div>
                                <div class="pfa-kpi is-save">
                                    <span class="pfa-kpi-accent">Sample Recurring Plan</span>
                                    <strong id="dog-sub-price">Every 2 weeks</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: Australian Shipping & Postcode Delivery Demo -->
                    <div id="panel-postcode" class="pfa-panel" role="tabpanel" aria-labelledby="tab-postcode" data-pfa-panel="postcode" hidden>
                        <div>
                            <span class="pfa-pill">Australian Shipping &amp; Delivery UX</span>
                            <h3 class="pfa-panel-heading">4-Digit Australian Postcode Delivery Checker (Sample Demo)</h3>
                            <p class="pfa-panel-copy">
                                If your pet food brand offers local metro delivery for perishable meals and standard courier delivery for dry goods nationwide, a storefront postcode checker sets clear expectations early and reduces checkout drop-offs.
                            </p>
                            <ul class="pfa-checks">
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Displays custom delivery eligibility rules by 4-digit AU postcode</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Separates local delivery zones from regional dry-goods shipping</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Tailored to the actual couriers and zones configured in your Shopify admin</li>
                            </ul>
                            <p class="pfa-demo-disclaimer">
                                <i class="bi bi-info-circle" aria-hidden="true"></i>
                                <strong>Sample demo notice:</strong> Zones and delivery labels in this preview use illustrative sample data only — not live carrier rates, active cold-chain coverage, or verified courier integrations.
                            </p>
                        </div>

                        <div class="pfa-panel-card">
                            <div class="pfa-demo-tag">Sample Postcode Lookup</div>
                            <label for="au-postcode-input" class="pfa-field-label">
                                Try a 4-digit Australian postcode (sample zones):
                            </label>
                            <div class="pfa-inline-input-row">
                                <input id="au-postcode-input" type="text" inputmode="numeric" maxlength="4" class="pfa-input" value="3000" placeholder="e.g. 3000, 2000, 4000">
                                <button type="button" id="au-postcode-btn" class="pfa-btn pfa-btn-emerald pfa-compact-btn">Check</button>
                            </div>
                            <div class="pfa-chips" id="au-postcode-chips" role="group" aria-label="Sample Australian postcodes">
                                <button type="button" class="pfa-chip is-active" data-pc="3000">3000 Melbourne</button>
                                <button type="button" class="pfa-chip" data-pc="2000">2000 Sydney</button>
                                <button type="button" class="pfa-chip" data-pc="4000">4000 Brisbane</button>
                                <button type="button" class="pfa-chip" data-pc="6000">6000 Perth</button>
                                <button type="button" class="pfa-chip" data-pc="4870">4870 Regional</button>
                            </div>
                            <div id="au-postcode-result" class="pfa-postcode-result" aria-live="polite">
                                <strong>Sample Result: Melbourne Metro Zone (VIC 3000)</strong>
                                <span>Illustrative rule: Local metro delivery zone eligible for fresh/perishable items plus standard parcel options for dry food and treats.</span>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: Multi-Vendor Pet Marketplace Architecture Demo -->
                    <div id="panel-marketplace" class="pfa-panel" role="tabpanel" aria-labelledby="tab-marketplace" data-pfa-panel="marketplace" hidden>
                        <div>
                            <span class="pfa-pill pfa-pill-orange">Multi-Vendor Pet Retail Concept</span>
                            <h3 class="pfa-panel-heading">Multi-Vendor Pet Marketplace Workflow on Shopify</h3>
                            <p class="pfa-panel-copy">
                                Planning a curated Australian pet marketplace bringing multiple pet treat makers, nutrition brands, or accessory suppliers into one storefront? We architect <a href="<?= cbd_escape_text($base) ?>/multi-vendor-marketplace" class="pfa-inline-link">multi-vendor marketplace workflows</a> using compatible Shopify marketplace apps and custom storefront discovery.
                            </p>
                            <ul class="pfa-checks">
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Unified storefront browsing by pet type, diet, brand, and product category</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Vendor onboarding and catalogue workflows configured via your chosen marketplace app</li>
                                <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Order routing and payout structure scoped to your exact business model</li>
                            </ul>
                            <p class="pfa-demo-disclaimer">
                                <i class="bi bi-info-circle" aria-hidden="true"></i>
                                <strong>Illustrative architecture:</strong> Specific vendor portal features, payout automation, and shipping split rules depend on the marketplace app and payment gateway selected during project scoping.
                            </p>
                        </div>

                        <div class="pfa-panel-card pfa-flow-stack">
                            <div class="pfa-demo-tag">Illustrative Marketplace Flow</div>
                            <div class="pfa-flow-step">
                                <strong>1. Unified Pet-Parent Shopping Experience</strong>
                                <p>Shoppers filter across pet food, treats, and accessories from approved brands inside one consistent Shopify storefront.</p>
                            </div>
                            <div class="pfa-flow-step">
                                <strong>2. Order Routing by Supplier</strong>
                                <p>Line items are organised by vendor through your configured multi-vendor marketplace app so each supplier sees their items clearly.</p>
                            </div>
                            <div class="pfa-flow-step is-highlight">
                                <strong>3. Centralised Brand &amp; SEO Control</strong>
                                <p>Your team manages collection SEO, merchandising, and customer experience centrally while scaling product breadth.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         6. REDUCE UNNECESSARY SHOPIFY APP COSTS (ILLUSTRATIVE ESTIMATOR)
    ════════════════════════════════════════════════ -->
    <section class="pfa-section pfa-section-cream" id="cost-calculator" aria-labelledby="pfa-calc-title">
        <div class="pfa-wrap">
            <div class="pfa-heading">
                <span class="pfa-pill"><i class="bi bi-calculator-fill" aria-hidden="true"></i> Illustrative Cost Estimator</span>
                <h2 id="pfa-calc-title">Reduce Unnecessary <span>Shopify App Costs</span></h2>
                <p>Many Shopify pet stores pay monthly subscriptions for simple front-end widgets that can be built natively in Shopify 2.0 Liquid. Use this illustrative estimator to model what consolidating overlapping apps and support costs could mean for your annual budget.</p>
            </div>

            <div class="pfa-calc-grid">
                <div class="pfa-calc-left">
                    <div class="pfa-slider-group">
                        <div class="pfa-slider-top">
                            <label for="calc-apps">Current Monthly Paid Shopify Apps (AUD)</label>
                            <span class="pfa-mono-badge" id="calc-apps-val">A$450 / mo</span>
                        </div>
                        <input id="calc-apps" type="range" class="pfa-range" min="0" max="2000" step="50" value="450">
                    </div>

                    <div class="pfa-slider-group">
                        <div class="pfa-slider-top">
                            <label for="calc-replace-pct">Share of Apps Replaceable With Native Code (Estimate)</label>
                            <span class="pfa-mono-badge" id="calc-replace-val">35%</span>
                        </div>
                        <input id="calc-replace-pct" type="range" class="pfa-range" min="10" max="60" step="5" value="35">
                    </div>

                    <div class="pfa-slider-group-last">
                        <div class="pfa-slider-top">
                            <label for="calc-retainer">Current Monthly Dev / Support Retainer (AUD, optional)</label>
                            <span class="pfa-mono-badge" id="calc-retainer-val">A$2,000 / mo</span>
                        </div>
                        <input id="calc-retainer" type="range" class="pfa-range" min="0" max="6000" step="250" value="2000">
                    </div>

                    <div class="pfa-app-table" aria-label="Examples of front-end features often consolidated into native Shopify 2.0 code">
                        <div class="pfa-app-row pfa-app-head">
                            <span>Common Front-End Feature</span>
                            <span>Typical Approach</span>
                            <span>Native 2.0 Option</span>
                        </div>
                        <div class="pfa-app-row">
                            <span>Feeding guide &amp; size charts</span>
                            <span class="pfa-muted-cell">Recurring app</span>
                            <span class="pfa-emerald-cell">Theme Liquid</span>
                        </div>
                        <div class="pfa-app-row">
                            <span>Postcode delivery notice</span>
                            <span class="pfa-muted-cell">Recurring app</span>
                            <span class="pfa-emerald-cell">Theme Liquid</span>
                        </div>
                        <div class="pfa-app-row">
                            <span>Cart upsell / free-shipping bar</span>
                            <span class="pfa-muted-cell">Recurring app</span>
                            <span class="pfa-emerald-cell">Theme Liquid</span>
                        </div>
                    </div>
                </div>

                <div class="pfa-calc-right pfa-paw-bg">
                    <div>
                        <span class="pfa-pill pfa-pill-yellow">Illustrative Annual Estimate</span>
                        <div class="pfa-calc-big" id="calc-annual-total">A$13,902 / yr</div>
                        <p class="pfa-calc-subcopy">
                            Estimated <strong id="calc-monthly-total">A$1,159 / mo</strong> potential reduction compared against your entered app spend and our A$999/mo ongoing care tier.
                        </p>

                        <div class="pfa-calc-mini-grid">
                            <div class="pfa-calc-mini">
                                <span>Est. App Spend Reduced / mo</span>
                                <strong id="calc-apps-saved">A$158</strong>
                            </div>
                            <div class="pfa-calc-mini">
                                <span>Est. Retainer Difference / mo</span>
                                <strong id="calc-dev-saved">A$1,001</strong>
                            </div>
                        </div>

                        <p class="pfa-calc-note">
                            <strong>Important:</strong> Figures above are illustrative estimates based purely on your slider inputs — not a guarantee. Exact savings depend on which third-party apps your store relies on and are verified during your free audit.
                        </p>
                    </div>

                    <button type="button" id="pfa-lock-savings" class="pfa-btn pfa-btn-orange pfa-full-btn" data-track-cta="calculator_prefill_audit">
                        Include These Inputs in My Free Audit <i class="bi bi-arrow-down-circle" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         7. OUR SHOPIFY DEVELOPMENT PROCESS (YELLOW CALLOUT) + PRICING
    ════════════════════════════════════════════════ -->
    <section class="pfa-section" id="process" aria-labelledby="pfa-process-title">
        <div class="pfa-wrap">
            <!-- Preserved Warm Yellow Featured Banner -> Now Our Shopify Development Process -->
            <div class="pfa-process-box">
                <div class="pfa-process-header">
                    <span class="pfa-pill pfa-pill-dark">Structured Delivery Workflow</span>
                    <h2 id="pfa-process-title">Our Shopify Development Process</h2>
                    <p>Clear milestones from initial store audit to launch and ongoing support — built for busy Australian pet food founders and ecommerce managers.</p>
                </div>

                <ol class="pfa-process-grid">
                    <li class="pfa-process-step">
                        <span class="pfa-step-num">01</span>
                        <h3>Audit &amp; Discovery</h3>
                        <p>We review your store URL, product catalogue, subscription needs, shipping rules, and installed apps, then share a clear written scope.</p>
                    </li>
                    <li class="pfa-process-step">
                        <span class="pfa-step-num">02</span>
                        <h3>UX Design &amp; Shopify 2.0 Build</h3>
                        <p>We craft mobile-first layouts and build clean Online Store 2.0 Liquid templates on a staging theme without disrupting live orders.</p>
                    </li>
                    <li class="pfa-process-step">
                        <span class="pfa-step-num">03</span>
                        <h3>QA, Speed &amp; Launch Checks</h3>
                        <p>Before going live, we verify mobile responsiveness, product filters, checkout paths, redirects, metadata, and analytics tracking.</p>
                    </li>
                    <li class="pfa-process-step">
                        <span class="pfa-step-num">04</span>
                        <h3>Handover &amp; Ongoing Support</h3>
                        <p>Your team receives clear guidance to manage products daily, with optional monthly support for campaigns, updates, and CRO.</p>
                    </li>
                </ol>
            </div>

            <!-- Pricing Section -->
            <div id="pricing" class="pfa-pricing-anchor">
                <div class="pfa-heading">
                    <span class="pfa-pill pfa-pill-orange"><i class="bi bi-tags-fill" aria-hidden="true"></i> Clear AUD Pricing</span>
                    <h2 id="pfa-pricing-title">Pet Food Shopify <span>Development Pricing</span></h2>
                    <p>Choose a focused one-time cleanup sprint, ongoing monthly Shopify support, or a custom pet store build. Custom projects are scoped with a clear written quotation.</p>
                </div>

                <div class="pfa-pricing-grid">
                    <article class="pfa-price-card">
                        <span class="pfa-pill">One-Time Fee</span>
                        <h3>App Cost Cleanup Sprint</h3>
                        <div class="pfa-price-val">A$799</div>
                        <p class="pfa-price-meta">One-time project fee · Ideal for existing Shopify stores</p>
                        <ul class="pfa-checks pfa-price-list">
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Full review of installed Shopify apps and front-end script bloat</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Consolidate suitable front-end widgets into native Shopify 2.0 code</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Mobile speed and layout stability improvements</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Actionable UX and conversion recommendations</li>
                        </ul>
                        <a href="#audit-form" class="pfa-btn pfa-btn-outline pfa-full-btn" data-select-service="App Cost Cleanup Sprint (A$799 one-time)" data-track-pricing="app_cost_cleanup_sprint">
                            Request Cleanup Sprint
                        </a>
                    </article>

                    <article class="pfa-price-card is-hot">
                        <span class="pfa-hot-tag">Ongoing Store Support</span>
                        <span class="pfa-pill pfa-pill-orange">Monthly Fee</span>
                        <h3>Monthly Care &amp; CRO Pod</h3>
                        <div class="pfa-price-val">A$999<span> / month</span></div>
                        <p class="pfa-price-meta">Recurring monthly support · Flexible ongoing engagement</p>
                        <ul class="pfa-checks pfa-price-list">
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Ongoing Shopify 2.0 development and design support</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> New landing pages, seasonal promos, and content updates</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Troubleshooting, theme maintenance, and performance checks</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Ongoing conversion flow and product-page improvements</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> AEST / AEDT &amp; AWST-friendly remote check-ins</li>
                        </ul>
                        <a href="#audit-form" class="pfa-btn pfa-btn-orange pfa-full-btn" data-select-service="Monthly Care & CRO Pod (A$999/month)" data-track-pricing="monthly_care_cro_pod">
                            Enquire About Monthly Care
                        </a>
                    </article>

                    <article class="pfa-price-card">
                        <span class="pfa-pill">One-Time · Custom Quote</span>
                        <h3>Custom Pet Store / Marketplace</h3>
                        <div class="pfa-price-val">A$2,499<span>+</span></div>
                        <p class="pfa-price-meta">Starting price · Final quote scoped to requirements</p>
                        <ul class="pfa-checks pfa-price-list">
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Custom Shopify Online Store 2.0 design for pet food &amp; supplies</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Product taxonomy, dietary filters, and feeding-guide layout</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Subscription UX setup with your selected Shopify subscription app</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Australian shipping zones, postcode rules, or marketplace scoping</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Technical SEO foundations, analytics setup, and launch QA</li>
                        </ul>
                        <a href="#audit-form" class="pfa-btn pfa-btn-emerald pfa-full-btn" data-select-service="Custom Pet Store / Marketplace (A$2,499+)" data-track-pricing="custom_pet_store_build">
                            Request a Custom Quote
                        </a>
                    </article>
                </div>

                <p class="pfa-pricing-note">
                    All prices are indicative starting amounts in AUD. Custom store builds, platform migrations, and multi-vendor marketplaces require a written quotation after reviewing your requirements. Applicable tax / GST treatment is confirmed on your formal quote.
                    <span class="pfa-agency-footnote">Are you an Australian digital agency looking for Shopify development support? <a href="<?= cbd_escape_text($base) ?>/contact-us">Contact us directly</a> to discuss agency collaboration.</span>
                </p>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         8. WHY CHOOSE CHULBUL DESIGN & VERIFIED CLIENT WORK (E-E-A-T)
    ════════════════════════════════════════════════ -->
    <section class="pfa-section pfa-section-cream pfa-paw-bg-light" id="why-chulbul" aria-labelledby="pfa-why-title">
        <div class="pfa-wrap">
            <div class="pfa-heading">
                <span class="pfa-pill"><i class="bi bi-shield-check" aria-hidden="true"></i> Trust &amp; Transparency</span>
                <h2 id="pfa-why-title">Why Choose <span>Chulbul Design?</span></h2>
                <p>Straightforward communication, clean Shopify 2.0 engineering, and real Australian pet ecommerce experience — without inflated claims or hidden lock-ins.</p>
            </div>

            <!-- Real Client Project Spotlight (Brunswick Fur Food) -->
            <article class="pfa-case-card" aria-labelledby="pfa-case-heading">
                <div class="pfa-case-grid">
                    <div class="pfa-case-media">
                        <picture>
                            <source srcset="<?= cbd_escape_text($bffWebpUrl) ?>" type="image/webp">
                            <img src="<?= cbd_escape_text($bffJpgUrl) ?>"
                                 alt="Brunswick Fur Food Melbourne Shopify storefront desktop preview"
                                 width="1100" height="720" loading="lazy" decoding="async">
                        </picture>
                    </div>
                    <div class="pfa-case-body">
                        <span class="pfa-pill pfa-pill-orange">Australian Pet Food Client · Melbourne, VIC</span>
                        <h3 id="pfa-case-heading">Featured Shopify Client: Brunswick Fur Food</h3>
                        <p>
                            <strong>Business overview:</strong> <a href="https://www.brunswickfurfood.com/" target="_blank" rel="noopener noreferrer" class="pfa-inline-link">Brunswick Fur Food</a> is a Melbourne-based fresh dog food brand selling gently cooked meals, starter packs, subscription meal boxes, and air-dried treats on Shopify.
                        </p>
                        <dl class="pfa-case-dl">
                            <div>
                                <dt>Focus area &amp; challenge</dt>
                                <dd>Streamlining ongoing Shopify operating costs while keeping a clean, mobile-friendly shopping and subscription experience for Melbourne dog owners.</dd>
                            </div>
                            <div>
                                <dt>Work completed</dt>
                                <dd>Shopify storefront improvements, subscription and starter-pack discovery refinement, and practical cost optimisation across the store setup.</dd>
                            </div>
                            <div>
                                <dt>Verified metrics &amp; client quote</dt>
                                <dd class="pfa-placeholder-note">Detailed before/after metrics and an approved client testimonial can be published here upon written client sign-off. Explore more live builds in our <a href="<?= cbd_escape_text($base) ?>/#portfolio" class="pfa-inline-link">selected work portfolio</a>.</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </article>

            <!-- Genuine Credibility Pillars -->
            <div class="pfa-trust-grid">
                <div class="pfa-trust-card">
                    <i class="bi bi-globe-asia-australia" aria-hidden="true"></i>
                    <h3>Transparent Remote Partnership</h3>
                    <p>Chulbul Design is headquartered in Gurugram, India, and serves Australian pet businesses remotely across Melbourne, Sydney, Brisbane, Perth, and Adelaide with AEST/AEDT and AWST-friendly communication.</p>
                </div>
                <div class="pfa-trust-card">
                    <i class="bi bi-code-square" aria-hidden="true"></i>
                    <h3>Clean Shopify 2.0 Engineering</h3>
                    <p>We prioritise maintainable Liquid code, clear product architecture, and sensible app choices over bloated plugins that slow down mobile storefronts.</p>
                </div>
                <div class="pfa-trust-card">
                    <i class="bi bi-file-earmark-check" aria-hidden="true"></i>
                    <h3>Clear Scope &amp; Ownership</h3>
                    <p>You retain full ownership of your Shopify store, domain, and custom theme code. Every sprint or custom build starts with clear deliverables and written pricing.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         9. FAQS (MATCHED 1-TO-1 WITH FAQPAGE JSON-LD)
    ════════════════════════════════════════════════ -->
    <section class="pfa-section" id="faq" aria-labelledby="pfa-faq-title">
        <div class="pfa-wrap">
            <div class="pfa-heading">
                <span class="pfa-pill"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i> Common Questions</span>
                <h2 id="pfa-faq-title">Frequently Asked <span>Questions</span></h2>
                <p>Practical answers about Shopify pet food store costs, subscriptions, app cleanup, Australian shipping setup, and working remotely with Chulbul Design.</p>
            </div>

            <div class="pfa-faq-grid">
                <?php foreach ($faqs as $idx => $faq): ?>
                <details<?= $idx < 2 ? ' open' : '' ?>>
                    <summary>
                        <span><?= cbd_escape_text($faq['q']) ?></span>
                        <i class="bi bi-plus-lg pfa-faq-icon" aria-hidden="true"></i>
                    </summary>
                    <p><?= cbd_escape_text($faq['a']) ?></p>
                </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         10. FINAL GREEN CTA & ACCESSIBLE FREE AUDIT FORM
    ════════════════════════════════════════════════ -->
    <section class="pfa-section pfa-section-emerald pfa-paw-bg" id="audit-form" aria-labelledby="pfa-form-title">
        <div class="pfa-wrap pfa-audit-grid">
            <div>
                <span class="pfa-pill pfa-pill-yellow"><i class="bi bi-clipboard2-check-fill" aria-hidden="true"></i> Free Shopify Store Audit</span>
                <h2 id="pfa-form-title" class="pfa-audit-heading">
                    Improve Your Shopify Store Performance &amp; <span>Conversion Flow</span>
                </h2>
                <p class="pfa-audit-copy">
                    Share your Australian pet store URL or upcoming project brief. Our Shopify team will review your storefront UX, product filtering, subscription journey, and visible app stack, then send clear, practical recommendations.
                </p>
                <ul class="pfa-checks pfa-checks-light">
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> No store admin password required for the initial review</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Practical UX, speed, and app-consolidation recommendations</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Remote AEST/AEDT &amp; AWST-friendly follow-up calls</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Zero obligation — keep the recommendations either way</li>
                </ul>
                <div class="pfa-audit-contact">
                    <a href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%27d%20like%20to%20request%20a%20free%20Shopify%20store%20audit%20for%20our%20Australian%20pet%20brand."
                       target="_blank" rel="noopener noreferrer" class="pfa-btn pfa-btn-glass" data-track-whatsapp="audit_section">
                        <i class="bi bi-whatsapp pfa-wa-icon" aria-hidden="true"></i> Chat on WhatsApp
                    </a>
                    <a href="mailto:info@chulbuldesign.com" class="pfa-audit-email">info@chulbuldesign.com</a>
                </div>
            </div>

            <div class="pfa-form-card">
                <h3 class="pfa-form-title">Request Your Free Shopify Store Audit</h3>
                <p class="pfa-form-subtitle">Fields marked with <span aria-hidden="true">*</span><span class="pfa-sr-only">an asterisk</span> are required.</p>

                <form id="pfa-main-form" class="pfa-form-stack" novalidate>
                    <input type="text" name="_hp" value="" tabindex="-1" autocomplete="off" class="pfa-hp-field" aria-hidden="true">

                    <div class="pfa-form-2">
                        <div class="pfa-field">
                            <label for="pfa-name-input" class="pfa-field-label">Name <span aria-hidden="true">*</span></label>
                            <input id="pfa-name-input" type="text" name="name" class="pfa-input" autocomplete="name" placeholder="e.g. Sarah Mitchell" required>
                        </div>
                        <div class="pfa-field">
                            <label for="pfa-email-input" class="pfa-field-label">Business Email <span aria-hidden="true">*</span></label>
                            <input id="pfa-email-input" type="email" name="email" class="pfa-input" autocomplete="email" placeholder="sarah@yourpetbrand.com.au" required>
                        </div>
                    </div>

                    <div class="pfa-form-2">
                        <div class="pfa-field">
                            <label for="pfa-store-url-input" class="pfa-field-label">Shopify Store URL <span aria-hidden="true">*</span></label>
                            <input id="pfa-store-url-input" type="url" name="store_url" class="pfa-input" inputmode="url" placeholder="https://yourpetbrand.com.au (or 'New Store')" required>
                        </div>
                        <div class="pfa-field">
                            <label for="pfa-business-input" class="pfa-field-label">Business Name <span class="pfa-optional">(optional)</span></label>
                            <input id="pfa-business-input" type="text" name="business" class="pfa-input" autocomplete="organization" placeholder="e.g. Melbourne Paws Co.">
                        </div>
                    </div>

                    <div class="pfa-form-2">
                        <div class="pfa-field">
                            <label for="pfa-service-select" class="pfa-field-label">Main Requirement <span class="pfa-optional">(optional)</span></label>
                            <select id="pfa-service-select" name="service" class="pfa-select">
                                <option value="Free Shopify Store Audit">Free Shopify Store Audit</option>
                                <option value="App Cost Cleanup Sprint (A$799 one-time)">App Cost Cleanup Sprint (A$799 one-time)</option>
                                <option value="Monthly Care & CRO Pod (A$999/month)">Monthly Care &amp; CRO Pod (A$999/month)</option>
                                <option value="Custom Pet Store / Marketplace (A$2,499+)">Custom Pet Store / Marketplace (A$2,499+)</option>
                                <option value="Shopify Subscriptions & Feeding Calculator">Subscriptions &amp; Feeding Calculator</option>
                                <option value="Migration to Shopify">Platform Migration to Shopify</option>
                            </select>
                        </div>
                        <div class="pfa-field">
                            <label for="pfa-phone-input" class="pfa-field-label">Phone / WhatsApp <span class="pfa-optional">(optional)</span></label>
                            <input id="pfa-phone-input" type="tel" name="phone" class="pfa-input" autocomplete="tel" placeholder="+61 4XX XXX XXX">
                        </div>
                    </div>

                    <div class="pfa-field">
                        <label for="pfa-message-input" class="pfa-field-label">Project Notes <span class="pfa-optional">(optional)</span></label>
                        <textarea id="pfa-message-input" name="message" rows="3" class="pfa-textarea" placeholder="Share any goals around subscriptions, product filters, Australian shipping rules, or apps you want reviewed..."></textarea>
                    </div>

                    <button type="submit" class="pfa-btn pfa-btn-orange pfa-full-btn">
                        Get My Free Shopify Audit <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </button>
                </form>
                <div id="pfa-main-status" class="pfa-status" role="status" aria-live="polite"></div>
            </div>
        </div>
    </section>

</main>

<!-- Preserved Emerald Trust Footer Bar -->
<div class="pfa-green-strip">
    <div class="pfa-wrap pfa-green-strip-grid">
        <div><i class="bi bi-palette-fill" aria-hidden="true"></i> Custom Shopify Development</div>
        <div><i class="bi bi-repeat" aria-hidden="true"></i> Pet Subscription UX</div>
        <div><i class="bi bi-gear-wide-connected" aria-hidden="true"></i> Ongoing Shopify Support</div>
        <div><i class="bi bi-globe-asia-australia" aria-hidden="true"></i> Serving Australia Remotely</div>
    </div>
</div>

<!-- Preserved Standalone Footer with Clean Contextual Links -->
<footer class="pfa-footer">
    <div class="pfa-wrap pfa-footer-row">
        <div>
            <strong class="pfa-footer-brand">Chulbul Design</strong> · Headquartered in Gurugram, India · Serving Australian pet food and ecommerce businesses remotely across Melbourne, Sydney, Brisbane, Perth &amp; Adelaide.
        </div>
        <div class="pfa-footer-links">
            <a href="<?= cbd_escape_text($base) ?>/shopify-development">Shopify Development</a>
            <a href="<?= cbd_escape_text($base) ?>/ecommerce-website-development">Ecommerce Services</a>
            <a href="<?= cbd_escape_text($base) ?>/multi-vendor-marketplace">Marketplaces</a>
            <a href="<?= cbd_escape_text($base) ?>/#portfolio">Portfolio</a>
            <a href="<?= cbd_escape_text($base) ?>/city/melbourne/ecommerce-development">Melbourne Ecommerce</a>
            <a href="<?= cbd_escape_text($base) ?>/city/sydney/ecommerce-development">Sydney Ecommerce</a>
            <a href="<?= cbd_escape_text($base) ?>/contact-us">Contact</a>
            <a href="<?= cbd_escape_text($base) ?>/about">About Chulbul Design</a>
        </div>
    </div>
</footer>

<div class="pfa-mobile-sticky" aria-label="Quick mobile actions">
    <a href="#audit-form" class="pfa-sticky-primary" data-track-cta="mobile_sticky_audit">Free Store Audit</a>
    <a href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%27d%20like%20a%20free%20Shopify%20store%20audit%20for%20our%20Australian%20pet%20brand." target="_blank" rel="noopener noreferrer" class="pfa-sticky-wa" data-track-whatsapp="mobile_sticky">
        <i class="bi bi-whatsapp" aria-hidden="true"></i> WhatsApp
    </a>
</div>

<!-- 100% Isolated Page Script -->
<script src="<?= cbd_escape_text($base) ?>/assets/js/pet-food-shopify-australia.js?v=<?= $jsVersion ?>" defer></script>
</body>
</html>
