<?php
declare(strict_types=1);

/**
 * Dedicated Sydney Ecommerce Development Page (/city/sydney/ecommerce-development)
 * 100% isolated from Sydney parent page and all other city/spoke pages.
 * Palette: Official Sydney City Flag (#252973, #D52F33, #F0BA19) + Harbour Teal (#1B9BBB) + Sand (#FAF7F0)
 */

if (cbd_is_direct_script_request('city-spoke.php')) {
    cbd_redirect_path('/city/sydney/ecommerce-development');
}

$base = cbd_base_path();
$esc  = static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

// Crisp, complete, uncovered miniature Australian National Flag SVG (2:1 official ratio, unique clipPath IDs)
$auFlagIndex = 0;
$renderAuFlag = static function () use (&$auFlagIndex): string {
    $auFlagIndex++;
    $cid = 'auEcomClip' . $auFlagIndex;
    return '<svg class="syde-au-flag" viewBox="0 0 60 30" width="22" height="11" aria-hidden="true" focusable="false">'
        . '<rect width="60" height="30" fill="#012169"/>'
        . '<clipPath id="' . $cid . '"><path d="M0 0h30v15H0z"/></clipPath>'
        . '<g clip-path="url(#' . $cid . ')">'
        . '<path d="M0 0l30 15m0-15L0 15" stroke="#fff" stroke-width="3"/>'
        . '<path d="M0 0l30 15m0-15L0 15" stroke="#C8102E" stroke-width="1.6"/>'
        . '<path d="M15 0v15M0 7.5h30" stroke="#fff" stroke-width="5"/>'
        . '<path d="M15 0v15M0 7.5h30" stroke="#C8102E" stroke-width="3"/>'
        . '</g>'
        . '<circle cx="15" cy="22.5" r="2.7" fill="#fff"/>'
        . '<circle cx="45" cy="24.5" r="1.7" fill="#fff"/>'
        . '<circle cx="38" cy="13.5" r="1.7" fill="#fff"/>'
        . '<circle cx="45" cy="6.2" r="1.7" fill="#fff"/>'
        . '<circle cx="52.5" cy="11.5" r="1.7" fill="#fff"/>'
        . '<circle cx="48.5" cy="16.8" r="1.1" fill="#fff"/>'
        . '</svg>';
};

// Inherit live DB sibling spokes & blogs when available, with exact verified fallbacks locally
$dbSpokeData = (isset($spokeResult) && ($spokeResult['status'] ?? '') === 'ok')
    ? ($spokeResult['data'] ?? [])
    : [];

$cityBlogs = !empty($dbSpokeData['blogs']) ? $dbSpokeData['blogs'] : [
    [
        'href'    => '/blog/shopify-vs-woocommerce-2026-comparison',
        'title'   => 'Shopify vs WooCommerce (2026): Costs, SEO & Best Choice',
        'excerpt' => 'Compare total platform costs, hosting control, checkout reliability, payment gateways, and SEO foundations before selecting your store stack.',
    ],
    [
        'href'    => '/blog/b2b-lead-generation-website-design-architecture',
        'title'   => 'B2B Lead Generation Website Architecture: Converting High-Value Buyers',
        'excerpt' => 'Structuring wholesale, B2B trade portals, and high-ticket product pages to turn evaluation traffic into qualified enquiries.',
    ],
    [
        'href'    => '/blog/website-redesign-seo-checklist-migration-guide',
        'title'   => 'Website Redesign SEO Checklist: 301 Redirects & Migration Guide',
        'excerpt' => 'Step-by-step URL mapping, canonical continuity, and launch verification to protect organic visibility during a store replatform.',
    ],
    [
        'href'    => '/blog/website-development-cost-2026',
        'title'   => 'How Much Does a Website Cost in 2026? Business Pricing Guide',
        'excerpt' => 'A transparent breakdown of what drives website and ecommerce development budgets from lean stores to custom integrations.',
    ],
];

// SEO Metadata (Exact brief requirements)
$canonicalUrl  = 'https://www.chulbuldesign.com/city/sydney/ecommerce-development';
$pageTitle     = 'Ecommerce Website Development Sydney | Shopify & WooCommerce';
$metaDesc      = 'Build a custom ecommerce website in Sydney with Shopify or WooCommerce. Get mobile-friendly design, Australian payment and shipping integrations, and a clear project quote from Chulbul Design.';
$shareImage    = 'https://www.chulbuldesign.com/assets/images/ecomerse-image.webp';
$shareImageAlt = 'Custom Shopify and WooCommerce ecommerce website development for Sydney businesses by Chulbul Design';

// 1. Platform Comparison Cards (Complete, technically accurate — ZERO empty fields)
$platforms = [
    [
        'name'       => 'Shopify & Shopify Plus',
        'icon'       => 'bi-shop',
        'popular'    => true,
        'best_for'   => 'Direct-to-consumer (D2C) retail brands, subscription stores, and growing Sydney merchants that want reliable managed hosting, fast Online Store 2.0 themes, and a proven mobile checkout without maintaining server infrastructure.',
        'watch_for'  => 'Monthly Shopify plan fees, paid third-party apps, and Shopify Payments or external gateway transaction charges. Deep checkout customization is restricted to Shopify Plus, and core platform infrastructure remains hosted on Shopify.',
        'ownership'  => 'You own your domain, brand assets, product data, customer list, and custom Liquid theme code under your own Shopify account.',
        'href'       => '/shopify-development',
        'link_label' => 'Explore Shopify Development',
    ],
    [
        'name'       => 'WooCommerce & WordPress',
        'icon'       => 'bi-wordpress',
        'popular'    => false,
        'best_for'   => 'Content-driven stores, niche catalogues, and Sydney businesses that already rely on WordPress for SEO publishing or need full control over database fields, hosting location, and custom checkout logic without SaaS platform limits.',
        'watch_for'  => 'Requires quality Australian or Asia-Pacific hosting, disciplined plugin selection, and ongoing WordPress/WooCommerce security updates and backups so performance and checkout reliability stay strong.',
        'ownership'  => '100% open-source ownership of your entire website, custom theme, database, and Australian hosting environment with zero vendor lock-in.',
        'href'       => '/woocommerce-development',
        'link_label' => 'Explore WooCommerce Development',
    ],
    [
        'name'       => 'Custom Headless & PHP Commerce',
        'icon'       => 'bi-layers-fill',
        'popular'    => false,
        'best_for'   => 'Established wholesalers, multi-vendor marketplaces, B2B trade portals, or stores with complex pricing tiers, bespoke configurators, and ERP/warehouse workflows that outgrow standard ecommerce templates.',
        'watch_for'  => 'Higher initial architecture and QA investment compared with Shopify or WooCommerce, plus planned ongoing engineering maintenance for APIs, upgrades, and security.',
        'ownership'  => 'Full ownership of your documented Git repository, API integrations, frontend/backend source code, and cloud hosting accounts.',
        'href'       => '/ecommerce-development',
        'link_label' => 'Explore Custom Ecommerce',
    ],
];

// 2. Catalog, Checkout & Logistics Architecture (4 complete modules)
$architectureItems = [
    [
        'icon'        => 'bi-diagram-3',
        'title'       => 'Structured Product Taxonomy',
        'description' => 'We organize collections, categories, SKU variants, and filterable attributes cleanly from day one — using logical breadcrumbs and canonical URL rules so shoppers find products quickly without creating faceted duplicate crawl issues.',
    ],
    [
        'icon'        => 'bi-credit-card-2-front',
        'title'       => 'Secure Payment Integration',
        'description' => 'Using official Shopify or WooCommerce integrations and your approved merchant accounts, we configure hosted PCI-compliant checkout gateways such as Stripe, PayPal, Apple Pay, Google Pay, Afterpay, or Zip so local buyers can pay in AUD with confidence.',
    ],
    [
        'icon'        => 'bi-truck',
        'title'       => 'Australian Fulfilment & Shipping Rules',
        'description' => 'We structure flat-rate, weight-based, metro Sydney vs regional NSW and interstate Australia shipping zones, local Click & Collect options, and official carrier integrations (such as Australia Post, StarTrack, or Sendle where your merchant accounts are active).',
    ],
    [
        'icon'        => 'bi-receipt',
        'title'       => 'GST Display & Policy Foundations',
        'description' => 'We configure AUD pricing, GST-inclusive or GST-exclusive display settings, and tax-invoice templates strictly according to rules confirmed by your business or Australian accountant, alongside clear shipping, returns, and Australian Consumer Law policy pages.',
    ],
];

// 3. Six Ecommerce Development Process Stages (All 6 complete)
$processStages = [
    [
        'num'         => '01',
        'title'       => 'Catalog & Operational Discovery',
        'description' => 'We audit your SKU structure, variant complexity, shipping zones, payment accounts, existing URLs (if migrating), and third-party tools to lock in a written fixed-scope AUD proposal and milestone schedule.',
    ],
    [
        'num'         => '02',
        'title'       => 'Conversion UX Wireframing',
        'description' => 'Before visual styling begins, we map mobile-first collection filters, product detail pages (PDPs), cart drawers, and checkout flows in Figma using real product data so every buyer question is answered clearly.',
    ],
    [
        'num'         => '03',
        'title'       => 'Frontend & Responsive Development',
        'description' => 'We engineer lightweight Shopify Online Store 2.0 themes, clean WooCommerce templates, or custom React/PHP storefronts with semantic HTML, accessible navigation, and compressed WebP product imagery.',
    ],
    [
        'num'         => '04',
        'title'       => 'Gateways, Shipping & Webhooks Setup',
        'description' => 'We connect your approved payment gateways, Australian shipping rate rules, email notifications, GA4 ecommerce events, and any scoped CRM, ERP, or accounting integrations (such as Xero) in a safe staging environment.',
    ],
    [
        'num'         => '05',
        'title'       => 'Rigorous Pre-Launch QA & Migration Checks',
        'description' => 'We run end-to-end sandbox test orders across iOS, Android, and desktop browsers, validate mobile cart usability, verify 301 redirect mappings for migrated stores, and run Core Web Vitals and accessibility audits.',
    ],
    [
        'num'         => '06',
        'title'       => 'Controlled Launch, Training & Maintenance',
        'description' => 'We coordinate DNS cutover during low-traffic Sydney hours, verify live checkout and tracking events, deliver practical admin training for your team, and offer optional ongoing maintenance and security care.',
    ],
];

// 4. Built-In Ecommerce Capabilities (All 6 complete)
$capabilities = [
    [
        'icon'        => 'bi-phone-flip',
        'title'       => 'Streamlined Mobile Checkouts',
        'description' => 'Thumb-friendly product galleries, sticky add-to-cart actions, slide-out cart summaries, and express wallet buttons (where enabled in your merchant gateway) reduce unnecessary friction on mobile screens.',
    ],
    [
        'icon'        => 'bi-graph-up-arrow',
        'title'       => 'GA4 Ecommerce Event Tracking',
        'description' => 'Structured data-layer implementation for view_item, add_to_cart, begin_checkout, and purchase events so your marketing team can measure product performance and campaign attribution accurately.',
    ],
    [
        'icon'        => 'bi-speedometer2',
        'title'       => 'Core Web Vitals Optimization',
        'description' => 'Properly sized WebP images, deferred non-critical scripts, lean theme code, and restrained third-party app usage help keep store pages responsive without bloated plugin stacks.',
    ],
    [
        'icon'        => 'bi-envelope-paper-heart',
        'title'       => 'Abandoned Cart & Lifecycle Workflows',
        'description' => 'Configuration of native Shopify checkout recovery emails or WooCommerce/Klaviyo email integrations so your team can follow up on incomplete carts within Australian spam and privacy rules.',
    ],
    [
        'icon'        => 'bi-code-square',
        'title'       => 'Product Schema.org Structured Data',
        'description' => 'Clean JSON-LD Product, Offer (AUD), and Breadcrumb markup on catalogue and product templates so search engines parse pricing, stock availability, and category hierarchy accurately.',
    ],
    [
        'icon'        => 'bi-mortarboard',
        'title'       => 'Admin Training & Full Handover',
        'description' => 'Clear handover walkthroughs covering product updates, order fulfilment, refunds, discount codes, and backups so your Sydney team can run day-to-day store operations independently.',
    ],
];

// 5. Ecommerce Pricing Factors (All 4 complete)
$pricingFactors = [
    [
        'title'       => 'Catalog Scale & Data Migration',
        'description' => 'A curated 20-SKU brand store takes far less setup than a 2,000-SKU catalogue with multi-dimensional variants, bundles, custom metafields, or legacy platform migration requiring full 301 redirect and customer/order history imports.',
    ],
    [
        'title'       => 'Design Customization Level',
        'description' => 'Configuring a clean, lightweight storefront layout is faster and more economical than designing bespoke interactive product builders, custom subscription portals, or editorial lookbooks from scratch in Figma.',
    ],
    [
        'title'       => 'B2B & Wholesale Trade Logic',
        'description' => 'Features such as trade account approval gates, ABN capture, tiered wholesale AUD price lists, minimum order quantities, or invoice-on-account workflows add custom development and QA scope.',
    ],
    [
        'title'       => 'Third-Party Software & ERP Integrations',
        'description' => 'Connecting external POS systems, Xero or MYOB accounting sync, multi-warehouse inventory feeds, or custom carrier APIs influences engineering effort and ongoing API testing requirements.',
    ],
];

// 6. FAQs — Single source of truth for visible <details> AND FAQPage JSON-LD
$ecomFaqs = [
    [
        'q' => 'How much does an ecommerce website cost in Sydney?',
        'a' => 'Project cost depends on your chosen platform (Shopify, WooCommerce, or custom build), SKU and variant complexity, design customization, data migration, and third-party integrations. After reviewing your product catalogue and functional brief, we provide a transparent fixed-scope AUD written quote with milestone breakdowns before development starts — third-party platform plans, domains, hosting, and paid apps are itemised separately.',
    ],
    [
        'q' => 'Should our Sydney business choose Shopify or WooCommerce?',
        'a' => 'Shopify is generally the stronger fit for direct-to-consumer retail brands that prefer managed hosting, high checkout stability, and minimal server maintenance. WooCommerce suits WordPress-centric teams that want full open-source code and database ownership, flexible content marketing, or custom checkout logic without SaaS platform constraints. We help you compare total 12-month ownership costs before recommending either stack.',
    ],
    [
        'q' => 'Which Australian payment gateways and shipping methods can you integrate?',
        'a' => 'Using official Shopify or WooCommerce integrations and your approved merchant accounts, we can configure Australian payment options including Stripe, PayPal, Apple Pay, Google Pay, Afterpay, and Zip. For logistics, we configure Sydney metro, regional NSW, and national shipping zones, Click & Collect, and supported carrier integrations such as Australia Post, StarTrack, or Sendle where your merchant accounts are active.',
    ],
    [
        'q' => 'How do you handle GST, migrations, and remote collaboration with Sydney clients?',
        'a' => 'Chulbul Design is headquartered in Gurugram, India and delivers all Sydney ecommerce projects remotely — we do not operate a physical Sydney office. Calls, staging reviews, and milestone approvals are scheduled in Sydney local time (AEST/AEDT). We configure GST display settings according to rules confirmed by your business or accountant, map 301 redirects for platform migrations to protect SEO continuity, and provide post-launch handover training.',
    ],
];

// Related Links
$relatedLinks = [
    [
        'href'  => '/city/sydney',
        'label' => 'Sydney Web Design & Development Agency',
        'desc'  => 'View full Sydney website packages (Starter A$699, Growth A$1,299, Enterprise Custom), local precincts, and general web design services.',
    ],
    [
        'href'  => '/city/sydney/small-business-web-design',
        'label' => 'Small Business Web Design in Sydney',
        'desc'  => 'Focused custom websites for Sydney service businesses, trades, and growing teams with clear enquiry journeys and technical SEO.',
    ],
];
foreach ($cityBlogs as $g) {
    $relatedLinks[] = [
        'href'  => (string)($g['href'] ?? ''),
        'label' => (string)($g['title'] ?? ''),
        'desc'  => (string)($g['excerpt'] ?? ''),
    ];
}

// Clean 5-Schema Graph (NO duplicate Organization, NO fake Sydney address, NO unverified claims)
$jf = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;

$organizationSchema = [
    '@context'     => 'https://schema.org',
    '@type'        => 'Organization',
    '@id'          => 'https://www.chulbuldesign.com/#organization',
    'name'         => 'Chulbul Design',
    'url'          => 'https://www.chulbuldesign.com/',
    'logo'         => [
        '@type' => 'ImageObject',
        'url'   => 'https://www.chulbuldesign.com/assets/images/logo/chulbuldesign.svg',
    ],
    'email'        => 'info@chulbuldesign.com',
    'telephone'    => '+919990548795',
    'foundingDate' => '2013',
    'address'      => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => 'Sector 15, Flat No. 1277',
        'addressLocality' => 'Gurugram',
        'addressRegion'   => 'Haryana',
        'postalCode'      => '122001',
        'addressCountry'  => 'IN',
    ],
    'sameAs'       => [
        'https://www.facebook.com/chulbuldesign',
        'https://www.instagram.com/chulbuldesign',
        'https://twitter.com/ChulbulDesign',
        'https://www.linkedin.com/company/chulbuldesign/',
        'https://techbehemoths.com/company/chulbul-design',
    ],
];

$serviceSchema = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Service',
    '@id'         => $canonicalUrl . '#service',
    'name'        => 'Ecommerce Website Development Sydney',
    'serviceType' => 'Shopify Development, WooCommerce Development & Custom Ecommerce Website Development',
    'url'         => $canonicalUrl,
    'description' => $metaDesc,
    'areaServed'  => [
        '@type'            => 'City',
        'name'             => 'Sydney',
        'containedInPlace' => [
            '@type' => 'AdministrativeArea',
            'name'  => 'New South Wales',
        ],
    ],
    'provider'    => ['@id' => 'https://www.chulbuldesign.com/#organization'],
    'availableChannel' => [
        '@type'             => 'ServiceChannel',
        'serviceUrl'        => $canonicalUrl,
        'availableLanguage' => 'English',
    ],
    'hasOfferCatalog' => [
        '@type'           => 'OfferCatalog',
        'name'            => 'Sydney Ecommerce Website Development Capabilities',
        'itemListElement' => [
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Shopify & Shopify Plus Development for Sydney Businesses']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'WooCommerce & WordPress Store Development for Sydney Businesses']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Custom Headless & PHP Ecommerce Development']],
            ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => 'Ecommerce Platform Migration & Technical SEO']],
        ],
    ],
];

$breadcrumbSchema = [
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://www.chulbuldesign.com/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Sydney Web Design', 'item' => 'https://www.chulbuldesign.com/city/sydney'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Ecommerce Development', 'item' => $canonicalUrl],
    ],
];

$faqSchema = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array_map(static fn(array $faq): array => [
        '@type'          => 'Question',
        'name'           => $faq['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
    ], $ecomFaqs),
];

// Header flags: single charset, single Organization, zero misleading geo.* meta, optimal CSS loading
$cbd_page_has_charset       = true;
$cbd_skip_global_org_schema = true;
$cbd_defer_custom_css       = true;
$cbd_skip_header_fonts      = true;
$page_robots                = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
$page_styles                = ['/assets/css/city-sydney-ecommerce.css'];
$_geo_meta                  = null; // Never emit Sydney geo.position / ICBM coordinates
$cbd_geo_page = [
    'url'         => $canonicalUrl,
    'name'        => $pageTitle,
    'description' => $metaDesc,
    'inLanguage'  => 'en-AU',
    'about'       => [
        'Ecommerce Website Development Sydney',
        'Shopify Development Sydney',
        'WooCommerce Development Sydney',
        'Custom Ecommerce Development Sydney',
    ],
    'speakable'   => ['h1', '.cbd-ai-summary', '#faq'],
];
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
    <meta charset="utf-8">
    <title><?= $esc($pageTitle) ?></title>
    <meta name="description" content="<?= $esc($metaDesc) ?>">
    <link rel="canonical" href="<?= $esc($canonicalUrl) ?>">

    <!-- Open Graph -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_AU">
    <meta property="og:title" content="<?= $esc($pageTitle) ?>">
    <meta property="og:description" content="<?= $esc($metaDesc) ?>">
    <meta property="og:url" content="<?= $esc($canonicalUrl) ?>">
    <meta property="og:image" content="<?= $esc($shareImage) ?>">
    <meta property="og:image:alt" content="<?= $esc($shareImageAlt) ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@chulbuldesign">
    <meta name="twitter:title" content="<?= $esc($pageTitle) ?>">
    <meta name="twitter:description" content="<?= $esc($metaDesc) ?>">
    <meta name="twitter:image" content="<?= $esc($shareImage) ?>">
    <meta name="twitter:image:alt" content="<?= $esc($shareImageAlt) ?>">

    <!-- Non-blocking Typography (Denk One display + Inter body + JetBrains Mono) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Denk+One&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Denk+One&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet"></noscript>

    <!-- Structured Data: Organization + Service + BreadcrumbList + FAQPage (+ WebPage via header.php) -->
    <script type="application/ld+json"><?= json_encode($organizationSchema, $jf) ?></script>
    <script type="application/ld+json"><?= json_encode($serviceSchema, $jf) ?></script>
    <script type="application/ld+json"><?= json_encode($breadcrumbSchema, $jf) ?></script>
    <script type="application/ld+json"><?= json_encode($faqSchema, $jf) ?></script>

    <?php require __DIR__ . '/header.php'; ?>

<div class="syde-page">

    <!-- ═══════════════════════════════════════════════
         1. SYDNEY ECOMMERCE FLAGSHIP HERO + INTERACTIVE STOREFRONT PREVIEW
    ═══════════════════════════════════════════════ -->
    <section class="syde-hero" aria-labelledby="syd-ecom-h1">
        <div class="syde-glow-1" aria-hidden="true"></div>
        <div class="syde-glow-2" aria-hidden="true"></div>
        <div class="syde-glow-3" aria-hidden="true"></div>
        <div class="syde-hero-grid-bg" aria-hidden="true"></div>

        <div class="syde-wrap">
            <div class="syde-hero-split">
                <!-- Left: Ecommerce Value Proposition -->
                <div>
                    <nav class="syde-crumb" aria-label="Breadcrumb">
                        <a href="<?= $base ?>/">Home</a>
                        <span aria-hidden="true">/</span>
                        <a href="<?= $base ?>/city/sydney">Sydney Web Design</a>
                        <span aria-hidden="true">/</span>
                        <span aria-current="page">Ecommerce Development</span>
                    </nav>

                    <div>
                        <span class="syde-live-pill">
                            <?= $renderAuFlag() ?>
                            <span>Shopify · WooCommerce · Custom Stores</span>
                            <span aria-hidden="true">·</span>
                            <span class="syde-tz-clock" id="sydeLiveClock">Sydney AEST/AEDT</span>
                        </span>
                    </div>

                    <h1 id="syd-ecom-h1" class="syde-h1">
                        Ecommerce Website Development in <span class="syde-h1-accent">Sydney</span>
                    </h1>

                    <p class="syde-hero-lead">
                        Launch or upgrade a high-trust online store built for Australian shoppers. We engineer custom <strong>Shopify</strong>, <strong>WooCommerce</strong>, and tailored ecommerce platforms with clean product discovery, mobile checkout flows, and Australian payment and shipping configurations — delivered remotely by our Gurugram, India team with Sydney (AEST/AEDT) review windows.
                    </p>

                    <div class="syde-hero-ctas">
                        <a href="#project-enquiry" class="syde-btn syde-btn-red">
                            <i class="bi bi-cart-check-fill" aria-hidden="true"></i> Plan Your Sydney Store
                        </a>
                        <a href="<?= $base ?>/city/sydney" class="syde-btn syde-btn-glass">
                            Explore Sydney Web Design <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>

                    <ul class="syde-trust-list" aria-label="Delivery commitments">
                        <li><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> Remote delivery from Gurugram, India</li>
                        <li><i class="bi bi-clock-fill" aria-hidden="true"></i> Sydney local time (AEST/AEDT) calls</li>
                        <li><i class="bi bi-file-earmark-check-fill" aria-hidden="true"></i> Written AUD scope before build</li>
                    </ul>
                </div>

                <!-- Right: Interactive Storefront & Checkout Architecture Preview -->
                <div class="syde-mock-shell" data-syde-preview>
                    <div class="syde-mock-window">
                        <div class="syde-mock-bar">
                            <span class="syde-mock-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                            <span class="syde-mock-url" data-syde-url>storefront.com.au/collections/featured</span>
                            <span style="font-size:11px;font-weight:700;color:#F0BA19;">AUD $</span>
                        </div>

                        <div class="syde-mock-tabs" role="tablist" aria-label="Preview ecommerce architecture by platform">
                            <button type="button" class="syde-mock-tab is-active" role="tab" aria-selected="true" data-syde-tab="shopify">Shopify 2.0</button>
                            <button type="button" class="syde-mock-tab" role="tab" aria-selected="false" data-syde-tab="woo">WooCommerce</button>
                            <button type="button" class="syde-mock-tab" role="tab" aria-selected="false" data-syde-tab="custom">Custom B2B</button>
                        </div>

                        <div class="syde-mock-body">
                            <div class="syde-store-grid">
                                <!-- Interactive PDP Mini Card -->
                                <div class="syde-pdp-card">
                                    <div class="syde-pdp-visual">
                                        <div>
                                            <span class="syde-sku-tag" data-syde-badge>ONLINE STORE 2.0</span>
                                            <div style="font-size:11px;color:#e0f2fe;margin-top:6px;" data-syde-sub>Native Liquid · Fast Mobile PDP</div>
                                        </div>
                                        <i class="bi bi-bag-check-fill" style="font-size:26px;color:#F0BA19;" aria-hidden="true"></i>
                                    </div>
                                    <div class="syde-pdp-title" data-syde-pdp>Product Discovery &amp; Variants</div>
                                    <div class="syde-variants" data-syde-vars>
                                        <span class="is-on">Variant Swatches</span>
                                        <span>Collection Filters</span>
                                        <span>Subscriptions</span>
                                    </div>
                                    <div style="font-size:11px;color:rgba(226,232,240,0.8);" data-syde-note>
                                        Clean product schema, fast mobile cart drawer &amp; GA4 events built in.
                                    </div>
                                </div>

                                <!-- Interactive AUD Cart & Checkout Card -->
                                <div class="syde-cart-card">
                                    <div class="syde-cart-head">
                                        <span><i class="bi bi-cart3" aria-hidden="true"></i> AUD Checkout Flow</span>
                                        <span data-syde-zone>NSW / AU</span>
                                    </div>
                                    <div class="syde-cart-rows">
                                        <div class="syde-cart-row"><span>Catalogue &amp; Pricing</span><span data-syde-r1>AUD Inclusive</span></div>
                                        <div class="syde-cart-row"><span>Shipping Rules</span><span data-syde-r2>Metro &amp; Regional</span></div>
                                        <div class="syde-cart-row"><span>GST Display</span><span>Client-Confirmed</span></div>
                                        <div class="syde-cart-row"><span>Order Tracking</span><span>GA4 + Webhooks</span></div>
                                    </div>
                                    <a href="#project-enquiry" class="syde-checkout-cta">
                                        Scope Your Store Architecture <i class="bi bi-arrow-right" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="syde-pay-strip">
                                <div class="syde-pay-strip-label">
                                    <span>Official Platform Payment &amp; Fulfilment Integrations</span>
                                    <span style="color:#F0BA19;">Merchant-Approved</span>
                                </div>
                                <div class="syde-pay-pills">
                                    <span>Stripe</span>
                                    <span>PayPal</span>
                                    <span>Afterpay</span>
                                    <span>Zip</span>
                                    <span>Apple Pay</span>
                                    <span>AusPost / Sendle</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         2. ECOMMERCE STRATEGY INTRO + QUICK FACTS
    ═══════════════════════════════════════════════ -->
    <section class="syde-sec" aria-labelledby="syde-intro-h2">
        <div class="syde-wrap">
            <div class="syde-intro-grid">
                <div>
                    <span class="syde-kicker"><i class="bi bi-compass-fill" aria-hidden="true"></i> Ecommerce Strategy</span>
                    <h2 id="syde-intro-h2" class="syde-h2">
                        Engineering Reliable <span class="syde-grad">Ecommerce Operations</span> Before Polishing the Theme
                    </h2>
                    <div class="syde-prose">
                        <p>
                            Effective ecommerce website development for Sydney businesses starts with how your store actually operates behind the storefront: how products are categorised, how variants and stock sync, how shipping is calculated between metro Sydney and regional Australia, and how buyers move from mobile discovery to payment without friction.
                        </p>
                        <p>
                            Whether you need focused <strong>Shopify development</strong> for a direct-to-consumer brand, open-source <strong>WooCommerce development</strong> integrated with WordPress content marketing, or <strong>custom ecommerce development</strong> for wholesale and B2B workflows, we plan your architecture around measurable usability, clean technical SEO, and straightforward post-launch management.
                        </p>
                        <p>
                            Need a broader company website or brand refresh alongside your online store? <a href="<?= $base ?>/city/sydney">Review our Sydney web design and development services</a>.
                        </p>
                    </div>
                </div>

                <aside class="syde-checklist-card" aria-label="Ecommerce project planning checklist">
                    <div class="syde-pat-stars" aria-hidden="true"></div>
                    <span class="syde-kicker" style="color:#F0BA19;background:rgba(240,186,25,0.14);border-color:rgba(240,186,25,0.34);margin-bottom:6px;">
                        Before Choosing a Platform
                    </span>
                    <h3>Define the store behind the storefront.</h3>
                    <p>Every scoped ecommerce build begins with operational clarity — never a rushed template install.</p>
                    <ul>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Catalogue, SKU variants &amp; collection taxonomy</span></li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Australian shipping zones, GST display &amp; payment gateways</span></li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>Migration URL inventory &amp; 301 redirect planning</span></li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span>GA4 ecommerce events &amp; post-launch admin handover</span></li>
                    </ul>
                </aside>
            </div>

            <div style="margin-top:42px;">
                <?php
                require_once __DIR__ . '/geo-schema.php';
                cbd_render_ai_summary_box([
                    'heading' => 'Quick Facts: Ecommerce Website Development in Sydney',
                    'summary' => 'Chulbul Design provides custom ecommerce website design and development for Sydney businesses across Shopify, WooCommerce, and custom PHP/headless platforms. Headquartered in Gurugram, India, our team delivers projects remotely with Sydney (AEST/AEDT) review windows, written fixed-scope AUD proposals, and full client ownership of deliverables.',
                    'facts' => [
                        'Specialist Service' => 'Shopify, WooCommerce & Custom Stores',
                        'Service Area'       => 'Sydney, New South Wales (Remote)',
                        'Scoping Model'      => 'Written fixed-scope AUD quote & milestones',
                        'Delivery HQ'        => 'Gurugram, India (AEST/AEDT calls)',
                    ],
                ]);
                ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         3. PLATFORM COMPARISON (Shopify vs WooCommerce vs Custom)
    ═══════════════════════════════════════════════ -->
    <section id="platforms" class="syde-sec syde-sec-sand" aria-labelledby="syde-platforms-h2">
        <div class="syde-wrap">
            <div class="syde-head">
                <span class="syde-kicker"><?= $renderAuFlag() ?> Platform Comparison</span>
                <h2 id="syde-platforms-h2" class="syde-h2">
                    Shopify, WooCommerce or <span class="syde-grad">Custom Ecommerce?</span>
                </h2>
                <p class="syde-lead">
                    Each platform has distinct strengths, hosting obligations, and ownership trade-offs. Here is how we help Sydney businesses pick the right stack.
                </p>
            </div>

            <div class="syde-platform-grid">
                <?php foreach ($platforms as $plat): ?>
                <article class="syde-plat-card<?= $plat['popular'] ? ' is-popular' : '' ?>">
                    <?php if ($plat['popular']): ?>
                    <div class="syde-pat-stars" aria-hidden="true"></div>
                    <?php endif; ?>
                    <div class="syde-plat-icon" aria-hidden="true"><i class="bi <?= $esc($plat['icon']) ?>"></i></div>
                    <h3><?= $esc($plat['name']) ?></h3>
                    <p class="syde-plat-block"><strong>Best fit:</strong> <?= $esc($plat['best_for']) ?></p>
                    <p class="syde-plat-block"><strong>Watch for:</strong> <?= $esc($plat['watch_for']) ?></p>
                    <p class="syde-plat-block"><strong>Ownership &amp; hosting:</strong> <?= $esc($plat['ownership']) ?></p>
                    <a href="<?= $esc($base . $plat['href']) ?>" class="syde-plat-link">
                        <?= $esc($plat['link_label']) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         4. CATALOG, CHECKOUT & LOGISTICS ARCHITECTURE
    ═══════════════════════════════════════════════ -->
    <section id="architecture" class="syde-sec" aria-labelledby="syde-arch-h2">
        <div class="syde-wrap">
            <div class="syde-arch-split">
                <div class="syde-arch-aside">
                    <span class="syde-kicker"><i class="bi bi-cpu-fill" aria-hidden="true"></i> Commerce Architecture</span>
                    <h2 id="syde-arch-h2" class="syde-h2">
                        Catalog, Checkout &amp; <span class="syde-grad">Logistics Architecture</span>
                    </h2>
                    <p class="syde-lead" style="margin-bottom:22px;">
                        We structure product data, payment workflows, shipping zones, and GST display settings cleanly so your store scales smoothly without plugin conflicts or checkout surprises.
                    </p>
                    <a href="#project-enquiry" class="syde-btn syde-btn-blue">
                        Discuss Your Store Architecture <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="syde-arch-grid">
                    <?php foreach ($architectureItems as $item): ?>
                    <article class="syde-arch-card">
                        <span class="syde-arch-icon" aria-hidden="true"><i class="bi <?= $esc($item['icon']) ?>"></i></span>
                        <h3><?= $esc($item['title']) ?></h3>
                        <p><?= $esc($item['description']) ?></p>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         5. SIX-STAGE ECOMMERCE DEVELOPMENT PROCESS
    ═══════════════════════════════════════════════ -->
    <section id="process" class="syde-sec syde-sec-sand" aria-labelledby="syde-process-h2">
        <div class="syde-wrap">
            <div class="syde-head">
                <span class="syde-kicker"><i class="bi bi-diagram-3-fill" aria-hidden="true"></i> Structured Delivery</span>
                <h2 id="syde-process-h2" class="syde-h2">
                    How an Ecommerce Project Moves from <span class="syde-grad">Scope to Launch</span>
                </h2>
                <p class="syde-lead">
                    Six transparent stages with Figma sign-offs, staging previews, and end-to-end checkout QA before your store goes live.
                </p>
            </div>

            <div class="syde-process-grid">
                <?php foreach ($processStages as $st): ?>
                <article class="syde-step-card">
                    <span class="syde-step-num" aria-hidden="true"><?= $esc($st['num']) ?></span>
                    <h3><?= $esc($st['title']) ?></h3>
                    <p><?= $esc($st['description']) ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         6. DARK SYDNEY HARBOUR BUILT-IN CAPABILITIES
    ═══════════════════════════════════════════════ -->
    <section id="capabilities" class="syde-sec syde-sec-cosmic" aria-labelledby="syde-cap-h2">
        <div class="syde-pat-stars" aria-hidden="true"></div>
        <div class="syde-wrap">
            <div class="syde-head">
                <span class="syde-kicker"><i class="bi bi-stars" aria-hidden="true"></i> Operations &amp; Measurement</span>
                <h2 id="syde-cap-h2" class="syde-h2">
                    Built-In Capabilities for <span class="syde-grad">Sydney Commerce Teams</span>
                </h2>
                <p class="syde-lead">
                    Practical technical foundations engineered into every storefront — focused on usability, measurement, and straightforward day-to-day management.
                </p>
            </div>

            <div class="syde-cap-grid">
                <?php foreach ($capabilities as $cap): ?>
                <article class="syde-cap-card">
                    <span class="syde-cap-icon" aria-hidden="true"><i class="bi <?= $esc($cap['icon']) ?>"></i></span>
                    <h3><?= $esc($cap['title']) ?></h3>
                    <p><?= $esc($cap['description']) ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         7. ECOMMERCE PRICING FACTORS (#pricing)
    ═══════════════════════════════════════════════ -->
    <section id="pricing" class="syde-sec" aria-labelledby="syde-pricing-h2">
        <div class="syde-wrap">
            <div class="syde-head">
                <span class="syde-kicker"><?= $renderAuFlag() ?> Transparent Scoping</span>
                <h2 id="syde-pricing-h2" class="syde-h2">
                    What Affects Sydney <span class="syde-grad">Ecommerce Pricing?</span>
                </h2>
                <p class="syde-lead">
                    Rather than quoting blind flat rates before seeing your catalogue, we scope every ecommerce build transparently in AUD around four core factors.
                </p>
            </div>

            <div class="syde-factors-grid">
                <?php foreach ($pricingFactors as $factor): ?>
                <article class="syde-factor-card">
                    <h3><?= $esc($factor['title']) ?></h3>
                    <p><?= $esc($factor['description']) ?></p>
                </article>
                <?php endforeach; ?>
            </div>

            <div class="syde-pricing-note">
                <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                <div>
                    <strong>Transparent Quote &amp; Timeline Commitment:</strong> After reviewing your SKU count, platform preference, and integration brief, we provide an itemised written AUD proposal covering deliverables, milestone schedule, and any separate third-party costs (domains, hosting, Shopify subscription plans, paid apps, or payment gateway fees). Project timelines are confirmed against your actual scope rather than a blanket turnaround claim.
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         8. HONEST LIVE PORTFOLIO SHOWCASE + AU PET FOOD SHOPIFY
    ═══════════════════════════════════════════════ -->
    <div style="background:#ffffff;padding:24px 24px 0;">
        <div class="syde-proof-banner" role="note" aria-label="Portfolio project origin note">
            <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
            <div>
                <strong>Verified Portfolio Note:</strong> Featured first below is <strong>Brunswick Fur Food</strong> — a live <strong>Melbourne-based Australian</strong> dog food Shopify store we designed and developed (not a Sydney client). We showcase it here to demonstrate our relevant Australian Shopify Online Store 2.0, subscription flow, and mobile product discovery work alongside our broader international builds.
            </div>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/portfolio-showcase.php';
    cbd_render_portfolio_showcase([
        'id'           => 'portfolio',
        'eyebrow'      => 'Selected Live Builds',
        'title'        => 'Australian & International Ecommerce Portfolio',
        'subtitle'     => 'Inspect live desktop and mobile websites — starting with our Melbourne-based Australian Shopify build (Brunswick Fur Food), followed by corporate, SaaS, and custom web projects.',
        'start_slug'   => 'brunswick-fur-food',
        'hide_metrics' => true,
    ]);
    ?>

    <?php
    $pfaCssVer = (int)(@filemtime(__DIR__ . '/../assets/css/au-pet-shopify-cta.css') ?: 1);
    $pfaWebp = function_exists('cbd_asset_url')
        ? cbd_asset_url('/assets/images/pet-food-au/au-pet-happy-owner-hero.webp')
        : $base . '/assets/images/pet-food-au/au-pet-happy-owner-hero.webp';
    $pfaJpg = function_exists('cbd_asset_url')
        ? cbd_asset_url('/assets/images/pet-food-au/au-pet-happy-owner-hero.jpg')
        : $base . '/assets/images/pet-food-au/au-pet-happy-owner-hero.jpg';
    ?>
    <link rel="stylesheet" href="<?= $esc($base . '/assets/css/au-pet-shopify-cta.css?v=' . $pfaCssVer) ?>" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="<?= $esc($base . '/assets/css/au-pet-shopify-cta.css?v=' . $pfaCssVer) ?>"></noscript>

    <section class="pfa-city-cta-wrap" aria-labelledby="pfa-syde-cta-heading">
        <div class="pfa-city-cta-container">
            <div class="pfa-city-cta-card">
                <div>
                    <span class="pfa-city-cta-eyebrow">
                        <i class="bi bi-stars" aria-hidden="true"></i>
                        Specialist Shopify · Australian Pet Brands
                    </span>
                    <h2 class="pfa-city-cta-title" id="pfa-syde-cta-heading">
                        Running a <span>Pet Food or Pet Store</span> Brand in Sydney?
                    </h2>
                    <p class="pfa-city-cta-desc">
                        Backed by our Melbourne Australian pet food Shopify build (Brunswick Fur Food), we deliver dedicated Online Store 2.0 storefronts, recurring subscription UX, dietary filters, and ongoing store care for Sydney and NSW pet brands.
                    </p>
                    <div class="pfa-city-cta-actions">
                        <a href="<?= $esc($base . '/pet-food-shopify-australia') ?>" class="pfa-city-cta-btn-primary">
                            Explore Australian Pet Food Shopify Service <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                        <a href="<?= $esc($base . '/pet-food-shopify-australia#audit-form') ?>" class="pfa-city-cta-btn-secondary">
                            Request a Free Store Audit
                        </a>
                    </div>
                    <ul class="pfa-city-cta-points">
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Custom Shopify 2.0 Store Design</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Subscription &amp; Recurring Order UX</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> App Cost Cleanup (A$799)</li>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Ongoing Store Care (A$999 / month)</li>
                    </ul>
                </div>

                <div class="pfa-city-cta-media">
                    <div class="pfa-city-cta-portrait">
                        <picture>
                            <source srcset="<?= $esc($pfaWebp) ?>" type="image/webp">
                            <img src="<?= $esc($pfaJpg) ?>"
                                 alt="Australian dog owner with natural dog food pouch representing Chulbul Design Shopify ecommerce capabilities for Sydney pet brands"
                                 width="896" height="1200" loading="lazy" decoding="async">
                        </picture>
                    </div>
                    <div class="pfa-city-cta-chip is-top">
                        <i class="bi bi-code-slash" aria-hidden="true"></i>
                        <div>Native Online Store 2.0<br><span>Built for AU Pet Brands</span></div>
                    </div>
                    <div class="pfa-city-cta-chip is-bottom">
                        <i class="bi bi-tags-fill" aria-hidden="true"></i>
                        <div>Transparent AUD Tiers<br><span>Starting from A$799</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         9. RELATED SYDNEY SERVICES & PLANNING GUIDES
    ═══════════════════════════════════════════════ -->
    <section id="related" class="syde-sec syde-sec-sand" aria-labelledby="syde-related-h2">
        <div class="syde-wrap">
            <div class="syde-head">
                <span class="syde-kicker"><i class="bi bi-journal-richtext" aria-hidden="true"></i> Related Planning Resources</span>
                <h2 id="syde-related-h2" class="syde-h2">
                    More Sydney Services &amp; <span class="syde-grad">Ecommerce Guides</span>
                </h2>
                <p class="syde-lead">
                    Compare platforms, review migration checklists, and explore our full Sydney web design capabilities.
                </p>
            </div>

            <div class="syde-res-grid">
                <?php foreach ($relatedLinks as $rl): ?>
                <a href="<?= $esc($base . $rl['href']) ?>" class="syde-res-card">
                    <h3><?= $esc($rl['label']) ?></h3>
                    <p><?= $esc($rl['desc']) ?></p>
                    <span>Explore resource <i class="bi bi-arrow-up-right" aria-hidden="true"></i></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         10. SPLIT FAQ SECTION (100% Synced with FAQPage Schema)
    ═══════════════════════════════════════════════ -->
    <section id="faq" class="syde-sec" aria-labelledby="syde-faq-h2">
        <div class="syde-wrap">
            <div class="syde-faq-split">
                <div class="syde-faq-aside">
                    <span class="syde-kicker"><i class="bi bi-question-circle-fill" aria-hidden="true"></i> Questions Before Scope</span>
                    <h2 id="syde-faq-h2" class="syde-h2">
                        Sydney Ecommerce Development <span class="syde-grad">FAQs</span>
                    </h2>
                    <p class="syde-lead" style="margin-bottom:22px;">
                        Clear, practical answers on Shopify vs WooCommerce, Australian payment and shipping setup, GST display, and remote AEST/AEDT delivery.
                    </p>
                    <a href="#project-enquiry" class="syde-btn syde-btn-blue">
                        Ask About Your Store <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="syde-faq-list">
                    <?php foreach ($ecomFaqs as $i => $faq): ?>
                    <details class="syde-faq-item"<?= $i === 0 ? ' open' : '' ?>>
                        <summary>
                            <span><?= $esc($faq['q']) ?></span>
                            <span class="syde-faq-plus" aria-hidden="true"><i class="bi bi-plus-lg"></i></span>
                        </summary>
                        <div class="syde-faq-body"><?= $esc($faq['a']) ?></div>
                    </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         11. SYDNEY ECOMMERCE ENQUIRY FORM (#project-enquiry)
    ═══════════════════════════════════════════════ -->
    <section id="project-enquiry" class="syde-sec" style="padding-top:20px;" aria-labelledby="syde-cta-h2">
        <div class="syde-wrap">
            <div class="syde-enquiry-panel">
                <div class="syde-pat-stars" aria-hidden="true"></div>
                <div class="syde-enquiry-grid">
                    <div class="syde-enquiry-copy">
                        <span class="syde-kicker" style="color:#F0BA19;background:rgba(240,186,25,0.14);border-color:rgba(240,186,25,0.34);">
                            <?= $renderAuFlag() ?> Sydney Ecommerce Enquiry
                        </span>
                        <h2 id="syde-cta-h2">Ready to Build or Scale Your <span class="syde-h1-accent">Sydney Online Store?</span></h2>
                        <p>
                            Share your product catalogue size, platform preference (Shopify, WooCommerce, or custom), and any payment or shipping requirements. Our Gurugram team will review your brief and respond during Sydney business hours (AEST/AEDT) with a clear written AUD scope.
                        </p>
                        <ul class="syde-enquiry-bullets">
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Honest Shopify vs WooCommerce platform advice</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Itemised AUD proposal &amp; milestone schedule</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> 100% client ownership of code, store &amp; accounts</li>
                            <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Remote delivery with AEST/AEDT video check-ins</li>
                        </ul>
                        <a href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%20would%20like%20to%20discuss%20an%20ecommerce%20website%20for%20my%20Sydney%20business."
                           target="_blank" rel="noopener noreferrer" class="syde-btn syde-btn-glass">
                            <i class="bi bi-whatsapp" aria-hidden="true"></i> Chat Directly on WhatsApp
                        </a>
                    </div>

                    <div class="syde-form-box">
                        <form id="cbdSpokeQuoteForm" action="<?= $base ?>/lead-submit.php" method="post" style="display:flex;flex-direction:column;gap:12px;">
                            <input type="text" name="_hp" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                            <input type="hidden" name="source" value="city-sydney-ecommerce-development-spoke">
                            <input type="hidden" name="city" value="Sydney">
                            <input type="hidden" name="service" value="Ecommerce Website Development">
                            <input type="hidden" name="page_url" value="<?= $esc($canonicalUrl) ?>">
                            <input type="hidden" name="form_version" value="city-sydney-ecom-v3">

                            <div class="syde-form-row">
                                <div class="syde-field">
                                    <label for="sydeName">Your Name *</label>
                                    <input id="sydeName" class="syde-input" type="text" name="name" autocomplete="name" required>
                                </div>
                                <div class="syde-field">
                                    <label for="sydeBiz">Business / Store Name *</label>
                                    <input id="sydeBiz" class="syde-input" type="text" name="business" autocomplete="organization" maxlength="160" required>
                                </div>
                            </div>

                            <div class="syde-form-row">
                                <div class="syde-field">
                                    <label for="sydePhone">Phone / WhatsApp (+61) *</label>
                                    <input id="sydePhone" class="syde-input" type="tel" name="phone" autocomplete="tel" maxlength="24" required>
                                </div>
                                <div class="syde-field">
                                    <label for="sydeEmail">Work Email</label>
                                    <input id="sydeEmail" class="syde-input" type="email" name="email" autocomplete="email">
                                </div>
                            </div>

                            <div class="syde-field">
                                <label for="sydeMsg">Store goals, platform preference &amp; approximate SKU count</label>
                                <textarea id="sydeMsg" class="syde-input" name="message" rows="3" maxlength="1000" placeholder="e.g. Shopify or WooCommerce, ~80 SKUs, migrating from an existing store, Stripe/Afterpay &amp; AusPost shipping"></textarea>
                            </div>

                            <div class="syde-form-row">
                                <div class="syde-field">
                                    <label for="sydeBudget">Budget range in AUD (optional)</label>
                                    <input id="sydeBudget" class="syde-input" type="text" name="budget" maxlength="100" placeholder="e.g. A$1,500–A$5,000 or Not decided">
                                </div>
                                <div class="syde-field">
                                    <label for="sydeTime">Target launch window (optional)</label>
                                    <input id="sydeTime" class="syde-input" type="text" name="timeline" maxlength="100" placeholder="e.g. Next month or Exploring">
                                </div>
                            </div>

                            <button id="cbdSpokeQuoteSubmit" type="submit" class="syde-btn syde-btn-red" style="width:100%;margin-top:4px;">
                                <i class="bi bi-send-fill" aria-hidden="true"></i> Request Free Ecommerce Quote
                            </button>
                            <div id="cbdSpokeQuoteMessage" class="syde-form-msg" role="status" aria-live="polite" hidden></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

</div>

<script>
(() => {
    // 1. Live Sydney Clock (AEST/AEDT)
    const clockEl = document.getElementById('sydeLiveClock');
    if (clockEl && typeof Intl !== 'undefined' && Intl.DateTimeFormat) {
        const tick = () => {
            try {
                const t = new Intl.DateTimeFormat('en-AU', {
                    timeZone: 'Australia/Sydney',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true,
                    timeZoneName: 'short'
                }).format(new Date());
                clockEl.textContent = 'Sydney ' + t;
            } catch (e) {}
        };
        tick();
        setInterval(tick, 60000);
    }

    // 2. Interactive Storefront Architecture Preview Switcher
    const preview = document.querySelector('[data-syde-preview]');
    if (preview) {
        const states = {
            shopify: {
                url: 'storefront.com.au/collections/featured',
                badge: 'ONLINE STORE 2.0',
                sub: 'Native Liquid · Fast Mobile PDP',
                pdp: 'Product Discovery & Variants',
                vars: ['Variant Swatches', 'Collection Filters', 'Subscriptions'],
                note: 'Clean product schema, fast mobile cart drawer & GA4 events built in.',
                zone: 'NSW / AU',
                r1: 'AUD Inclusive',
                r2: 'Metro & Regional'
            },
            woo: {
                url: 'storefront.com.au/shop/category',
                badge: 'WOOCOMMERCE + WP',
                sub: 'Open-Source · Full Code Ownership',
                pdp: 'SEO Content + Storefront Sync',
                vars: ['Custom Attributes', 'Editorial SEO', 'Local Hosting'],
                note: 'Complete database ownership with lightweight custom templates and lean plugins.',
                zone: 'AU Hosted',
                r1: 'AUD + Tax Rules',
                r2: 'Custom Zones'
            },
            custom: {
                url: 'trade.storefront.com.au/portal',
                badge: 'CUSTOM / B2B',
                sub: 'Laravel / Next.js · API Integrations',
                pdp: 'Wholesale & Trade Ordering',
                vars: ['ABN Login Gate', 'Tiered AUD Pricing', 'ERP / Xero Sync'],
                note: 'Tailored checkout logic, bulk order matrices, and warehouse API webhooks.',
                zone: 'B2B / Trade',
                r1: 'Contract Pricing',
                r2: 'Multi-Warehouse'
            }
        };
        const tabs = preview.querySelectorAll('[data-syde-tab]');
        const setTab = (key) => {
            const s = states[key];
            if (!s) return;
            tabs.forEach((b) => {
                const on = b.getAttribute('data-syde-tab') === key;
                b.classList.toggle('is-active', on);
                b.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            const q = (sel) => preview.querySelector(sel);
            q('[data-syde-url]').textContent = s.url;
            q('[data-syde-badge]').textContent = s.badge;
            q('[data-syde-sub]').textContent = s.sub;
            q('[data-syde-pdp]').textContent = s.pdp;
            q('[data-syde-note]').textContent = s.note;
            q('[data-syde-zone]').textContent = s.zone;
            q('[data-syde-r1]').textContent = s.r1;
            q('[data-syde-r2]').textContent = s.r2;
            const varsBox = q('[data-syde-vars]');
            if (varsBox) {
                varsBox.innerHTML = s.vars.map((v, i) => '<span' + (i === 0 ? ' class="is-on"' : '') + '>' + v + '</span>').join('');
            }
        };
        tabs.forEach((b) => b.addEventListener('click', () => setTab(b.getAttribute('data-syde-tab'))));
    }

    // 3. Lead Form Submission (lead-submit.php)
    const form = document.getElementById('cbdSpokeQuoteForm');
    if (!form) return;
    const button = document.getElementById('cbdSpokeQuoteSubmit');
    const status = document.getElementById('cbdSpokeQuoteMessage');
    const originalButton = button.innerHTML;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!form.reportValidity()) return;
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-arrow-repeat animate-spin" aria-hidden="true"></i> Sending…';
        status.hidden = true;
        status.replaceChildren();

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.error || 'Submission failed');

            status.className = 'syde-form-msg is-success';
            status.append(document.createTextNode('Thank you! Your Sydney ecommerce enquiry has been received. We will reply during AEST/AEDT business hours.'));
            if (data.whatsapp_url) {
                const link = document.createElement('a');
                link.href = data.whatsapp_url;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.style.textDecoration = 'underline';
                link.textContent = ' Continue on WhatsApp';
                status.append(link);
            }
            status.hidden = false;
            form.reset();
        } catch (error) {
            status.className = 'syde-form-msg is-error';
            status.textContent = 'Your request could not be sent right now. Please try again or reach us directly on WhatsApp.';
            status.hidden = false;
        } finally {
            button.disabled = false;
            button.innerHTML = originalButton;
        }
    });
})();
</script>

<?php require __DIR__ . '/footer.php'; ?>
