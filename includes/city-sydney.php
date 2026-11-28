<?php
declare(strict_types=1);

/**
 * Dedicated Sydney City Landing Page (/city/sydney) — Harbour Studio Edition
 * 100% isolated from the other 51 city pages (uses dedicated assets/css/city-sydney.css).
 */

if (cbd_is_direct_script_request('city.php')) {
    cbd_redirect_path('/city/sydney');
}

$base = cbd_base_path();
$esc = static fn(mixed $v): string => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

// Crisp, complete, uncovered miniature Australian National Flag SVG (2:1 proportion, PM&C compliant, unique clipPath IDs)
$auFlagIndex = 0;
$renderAuFlag = static function () use (&$auFlagIndex): string {
    $auFlagIndex++;
    $cid = 'auClip' . $auFlagIndex;
    return '<svg class="syd-au-flag" viewBox="0 0 60 30" width="22" height="11" aria-hidden="true" focusable="false">'
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

// Inherit live DB spokes, blogs, and related cities when DB is available;
// provide exact verified live fallbacks when local MySQL is offline.
$dbData = (isset($location_page) && ($location_page['status'] ?? '') === 'ok')
    ? ($location_page['data'] ?? [])
    : [];

$city_spokes = !empty($dbData['spokes']) ? $dbData['spokes'] : [
    [
        'slug'        => 'small-business-web-design',
        'label'       => 'Small Business Web Design',
        'href'        => '/city/sydney/small-business-web-design',
        'description' => 'Affordable small business web design in Sydney from A$699. Mobile-first websites, clear enquiry forms and on-page SEO foundations delivered on an agreed milestone schedule.',
    ],
    [
        'slug'        => 'ecommerce-development',
        'label'       => 'Ecommerce Website Design',
        'href'        => '/city/sydney/ecommerce-development',
        'description' => 'Custom ecommerce website design in Sydney on Shopify and WooCommerce with merchant-approved Australian payments, GST display setup and fast mobile checkout.',
    ],
];

$city_blogs = !empty($dbData['blogs']) ? $dbData['blogs'] : [
    [
        'href'          => '/blog/shopify-vs-woocommerce-2026-comparison',
        'title'         => 'Shopify vs WooCommerce (2026): Costs, SEO & Best Choice',
        'excerpt'       => 'A practical Shopify vs WooCommerce comparison for brands evaluating costs, control, payments, SEO, maintenance and long-term growth.',
        'category'      => 'Planning Guide',
        'read_time'     => '9 min read',
        'is_city_match' => false,
    ],
    [
        'href'          => '/blog/b2b-lead-generation-website-design-architecture',
        'title'         => 'B2B Lead Generation Website Architecture: Converting High-Value Buyers in 2026',
        'excerpt'       => 'A strategic architectural blueprint for B2B websites designed to build decision-maker trust, shorten sales cycles and improve qualified enquiry conversion.',
        'category'      => 'Planning Guide',
        'read_time'     => '23 min read',
        'is_city_match' => false,
    ],
    [
        'href'          => '/blog/website-redesign-seo-checklist-migration-guide',
        'title'         => 'Website Redesign SEO Checklist (2026): Complete 301 Redirect & Zero Traffic-Loss Migration Guide',
        'excerpt'       => 'A structured SEO migration framework to help protect existing rankings, backlinks and organic traffic during a website redesign.',
        'category'      => 'Planning Guide',
        'read_time'     => '23 min read',
        'is_city_match' => false,
    ],
    [
        'href'          => '/blog/website-development-cost-2026',
        'title'         => 'How Much Does a Website Cost in 2026? The Ultimate Business Pricing Guide',
        'excerpt'       => 'A transparent guide detailing what shapes website design and development budgets — from small business brochure sites to custom ecommerce builds.',
        'category'      => 'Planning Guide',
        'read_time'     => '22 min read',
        'is_city_match' => false,
    ],
];

$related = !empty($dbData['related']) ? $dbData['related'] : [
    'Melbourne' => 'melbourne',
    'Brisbane'  => 'brisbane',
    'Perth'     => 'perth',
    'Adelaide'  => 'adelaide',
];

// SEO Metadata
$canonicalUrl   = 'https://www.chulbuldesign.com/city/sydney';
$pageTitle      = 'Web Design Sydney | Custom Websites from A$699 – Chulbul Design';
$metaDesc       = 'Need a website in Sydney? Get custom business websites, WordPress, Shopify and ecommerce development from A$699. Remote delivery and free quote.';
$shareImage     = 'https://www.chulbuldesign.com/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg';
$shareImageAlt  = 'Chulbul Design — custom website design, WordPress, Shopify and ecommerce development for Sydney businesses';

// Pricing Plans (exact AUD prices & inclusions preserved)
$sydneyPlans = [
    [
        'name'     => 'Starter',
        'price'    => 'A$699',
        'numPrice' => '699',
        'label'    => 'For small businesses going online',
        'bestFor'  => 'Best for sole traders, local service firms and new businesses needing a credible 5-page online presence.',
        'popular'  => false,
        'features' => [
            'Up to 5 Pages',
            'Mobile-First Responsive Design',
            'Contact Form + Google Maps',
            'On-Page SEO Setup',
            'Google Analytics + GA4',
            'SSL Certificate',
            '1 Month Free Support',
        ],
    ],
    [
        'name'     => 'Growth',
        'price'    => 'A$1,299',
        'numPrice' => '1299',
        'label'    => 'Most popular for growing businesses',
        'bestFor'  => 'Best for established service companies and B2B teams that need custom UI, WordPress CMS and deeper SEO structure.',
        'popular'  => true,
        'features' => [
            'Up to 15 Pages',
            'Premium Custom UI Design',
            'Advanced On-Page SEO',
            'Blog / CMS (WordPress)',
            'Social Media Integration',
            'PageSpeed Optimisation',
            'Google Search Console',
            '3 Months Free Support',
        ],
    ],
    [
        'name'     => 'Enterprise',
        'price'    => 'Custom Quote',
        'numPrice' => '',
        'label'    => 'For established brands & portals',
        'bestFor'  => 'Best for Shopify or WooCommerce stores, booking portals, custom web apps and multi-system API integrations.',
        'popular'  => false,
        'features' => [
            'Unlimited Pages',
            'Custom Web Application',
            'E-commerce (Shopify/WooCommerce)',
            'API & CRM Integrations',
            'Performance & Security Audit',
            'Dedicated Account Manager',
            '6 Months Free Support',
        ],
    ],
];

// 7 Focused Bento Services
$sydneyServices = [
    [
        'num'        => '01',
        'span'       => 'syd-span-6',
        'featured'   => true,
        'icon'       => 'bi-shop',
        'icClass'    => 'syd-ic-cyan',
        'title'      => 'Shopify & WooCommerce Ecommerce Development',
        'desc'       => 'Custom Online Store 2.0 and WooCommerce storefronts engineered for fast mobile product discovery, collection filtering, subscription apps, and official platform payment integrations (such as Stripe, PayPal, Afterpay, or Zip where merchant accounts are approved) with client-confirmed GST display settings.',
        'href'       => '/city/sydney/ecommerce-development',
        'link_label' => 'Explore Sydney Ecommerce Services',
    ],
    [
        'num'        => '02',
        'span'       => 'syd-span-6',
        'featured'   => false,
        'icon'       => 'bi-laptop',
        'icClass'    => 'syd-ic-indigo',
        'title'      => 'Website Design',
        'desc'       => 'Custom layouts designed around your brand, service hierarchy and mobile enquiry paths — giving prospective clients clear next steps without bloated page-builder templates.',
        'href'       => '/city/sydney/small-business-web-design',
        'link_label' => 'Explore Small Business Web Design',
    ],
    [
        'num'        => '03',
        'span'       => 'syd-span-4',
        'featured'   => false,
        'icon'       => 'bi-code-slash',
        'icClass'    => 'syd-ic-navy',
        'title'      => 'Website Development',
        'desc'       => 'Clean frontend and backend engineering in PHP, Laravel, React or Next.js for fast loading, reliable forms, CRM webhooks and scalable custom functionality.',
        'href'       => '/web-development',
        'link_label' => 'View Web Development',
    ],
    [
        'num'        => '04',
        'span'       => 'syd-span-4',
        'featured'   => false,
        'icon'       => 'bi-wordpress',
        'icClass'    => 'syd-ic-cyan',
        'title'      => 'WordPress Development',
        'desc'       => 'Lightweight custom WordPress builds structured for straightforward in-house editing, clean URL architecture, security hygiene and long-term stability.',
        'href'       => '/wordpress-development',
        'link_label' => 'View WordPress Development',
    ],
    [
        'num'        => '05',
        'span'       => 'syd-span-4',
        'featured'   => false,
        'icon'       => 'bi-bag-heart',
        'icClass'    => 'syd-ic-emerald',
        'title'      => 'Shopify Development',
        'desc'       => 'Dedicated Shopify theme customization, app consolidation, recurring subscription setup and mobile checkout polish for growing Australian retail brands.',
        'href'       => '/shopify-development',
        'link_label' => 'View Shopify Development',
    ],
    [
        'num'        => '06',
        'span'       => 'syd-span-6',
        'featured'   => false,
        'icon'       => 'bi-palette2',
        'icClass'    => 'syd-ic-violet',
        'title'      => 'UI/UX Design',
        'desc'       => 'Wireframes, interactive Figma prototypes and accessible interface systems tested for readability and conversion clarity before development begins.',
        'href'       => '/ui-ux-branding',
        'link_label' => 'Explore UI/UX Design',
    ],
    [
        'num'        => '07',
        'span'       => 'syd-span-6',
        'featured'   => false,
        'icon'       => 'bi-graph-up-arrow',
        'icClass'    => 'syd-ic-coral',
        'title'      => 'Technical & On-Page SEO',
        'desc'       => 'Semantic headings, structured JSON-LD schema, metadata continuity, Core Web Vitals optimization and local NSW search architecture built cleanly into every page.',
        'href'       => '/seo-digital-marketing',
        'link_label' => 'View SEO Services',
    ],
];

// Exact 8 Sydney Precincts with useful local remote context (zero extra suburb stuffing)
$sydneyPrecincts = [
    ['name' => 'Sydney CBD',    'tag' => 'Corporate',    'desc' => 'B2B, financial and advisory websites built for credibility and fast mobile evaluation.'],
    ['name' => 'Surry Hills',   'tag' => 'Creative',     'desc' => 'Design-led agency, hospitality and boutique brand sites with sharp Figma craft.'],
    ['name' => 'North Sydney',  'tag' => 'Commercial',   'desc' => 'Professional services, SaaS and consultancy sites focused on qualified enquiry flows.'],
    ['name' => 'Parramatta',    'tag' => 'Growth Hub',   'desc' => 'Scalable small business, trades and regional service sites with clear NSW SEO structure.'],
    ['name' => 'Chatswood',     'tag' => 'Retail & B2B', 'desc' => 'Ecommerce storefronts, clinics and multilingual websites for North Shore audiences.'],
    ['name' => 'Manly',         'tag' => 'Coastal',      'desc' => 'Tourism, hospitality and wellness websites built around mobile bookings and speed.'],
    ['name' => 'Bondi',         'tag' => 'Lifestyle',    'desc' => 'Direct-to-consumer Shopify brands, studios and retail storefronts with visual polish.'],
    ['name' => 'Darlinghurst',  'tag' => 'Boutique',     'desc' => 'Independent practices, venues and specialist service firms needing standout UX.'],
];

// Exact 6 Industries with concise, relevant website requirements
$sydneyIndustries = [
    [
        'icon'  => 'bi-bank',
        'title' => 'Finance & Banking',
        'desc'  => 'Trust-focused corporate websites with clear service architecture, compliance-friendly disclosures, downloadable resources and qualified consultation forms.',
    ],
    [
        'icon'  => 'bi-compass',
        'title' => 'Tourism & Hospitality',
        'desc'  => 'Visual service showcases, mobile-friendly menus or itinerary pages and smooth booking or reservation journeys built for on-the-go visitors.',
    ],
    [
        'icon'  => 'bi-houses',
        'title' => 'Property & Real Estate',
        'desc'  => 'Structured property listings, suburb or project showcases, interactive galleries and high-intent inspection or appraisal enquiry capture.',
    ],
    [
        'icon'  => 'bi-cpu',
        'title' => 'Technology & Startups',
        'desc'  => 'Sharp SaaS and product websites that explain complex offerings clearly, highlight technical proof and convert traffic into demo requests.',
    ],
    [
        'icon'  => 'bi-bag-check',
        'title' => 'Retail & E-commerce',
        'desc'  => 'Fast Shopify and WooCommerce stores focused on clean product discovery, filtering, mobile cart usability and merchant-supported Australian checkout options.',
    ],
    [
        'icon'  => 'bi-briefcase',
        'title' => 'Legal & Professional Services',
        'desc'  => 'Authoritative practice-area websites, partner profiles, case insight publishing and confidential consultation booking flows.',
    ],
];

// Exact 4 FAQs — single source of truth for visible accordion AND FAQPage JSON-LD
$sydneyFaqs = [
    [
        'q' => 'How much does a website cost in Sydney?',
        'a' => 'Our fixed-scope Sydney packages start at A$699 for a Starter website (up to 5 pages) and A$1,299 for a Growth website (up to 15 pages with custom UI and WordPress CMS). Larger ecommerce stores, custom web applications, and API integrations are quoted individually under our Enterprise tier after reviewing your requirements. Third-party expenses such as domain registration, hosting, or paid software licences are outlined separately in your written proposal.',
    ],
    [
        'q' => 'Do you offer SEO services for Sydney businesses?',
        'a' => 'Yes. Every website build includes clean semantic HTML, logical heading structure, page metadata, structured JSON-LD schema, and mobile usability checks. We also offer ongoing technical SEO, content architecture, and Google Business Profile alignment for Sydney and NSW search intent. Because search rankings depend on competition, domain history, and ongoing content, we focus on measurable technical foundations rather than guaranteeing specific ranking positions.',
    ],
    [
        'q' => 'Can you build an e-commerce website for my Sydney business?',
        'a' => 'Yes. We develop custom Shopify and WooCommerce stores tailored for Australian online retail, including mobile-friendly product discovery, collection filters, shipping zone setup, and streamlined checkout flows. Where supported by the chosen platform and your approved merchant accounts, we can configure official payment integrations such as Stripe, PayPal, Afterpay, or Zip, alongside GST display settings confirmed by your business or accountant.',
    ],
    [
        'q' => 'How do you work with Sydney clients remotely?',
        'a' => 'Chulbul Design is headquartered in Gurugram, India and delivers all Sydney projects remotely — we do not operate a physical office in Sydney. Communication runs via scheduled Zoom or Google Meet calls, email, and WhatsApp during overlapping AEST/AEDT and IST business hours. You review Figma designs, live staging links, and written milestone checklists before each phase is approved and launched.',
    ],
];

// Clean 5-Schema Graph (zero duplicate Organization or Offer blocks)
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

$offerCatalogItems = [
    [
        '@type' => 'Offer',
        'url'   => $canonicalUrl . '#pricing',
        'itemOffered' => [
            '@type'       => 'Service',
            'name'        => 'Starter Website Package for Sydney Businesses',
            'serviceType' => 'Small Business Website Design',
            'url'         => $canonicalUrl . '#pricing',
        ],
        'priceSpecification' => [
            '@type'         => 'PriceSpecification',
            'price'         => '699',
            'priceCurrency' => 'AUD',
        ],
    ],
    [
        '@type' => 'Offer',
        'url'   => $canonicalUrl . '#pricing',
        'itemOffered' => [
            '@type'       => 'Service',
            'name'        => 'Growth Website Package for Sydney Businesses',
            'serviceType' => 'Custom WordPress & Business Website Design',
            'url'         => $canonicalUrl . '#pricing',
        ],
        'priceSpecification' => [
            '@type'         => 'PriceSpecification',
            'price'         => '1299',
            'priceCurrency' => 'AUD',
        ],
    ],
    [
        '@type' => 'Offer',
        'url'   => $canonicalUrl . '#pricing',
        'itemOffered' => [
            '@type'       => 'Service',
            'name'        => 'Enterprise Website & Ecommerce Package for Sydney Businesses',
            'serviceType' => 'Custom Web Application, Shopify & WooCommerce Development',
            'url'         => $canonicalUrl . '#pricing',
        ],
    ],
];

foreach ($city_spokes as $sp) {
    $spSlug = (string)($sp['slug'] ?? '');
    $spHref = (string)($sp['href'] ?? '');
    if ($spSlug === '' || $spHref === '') {
        continue;
    }
    $spUrl = 'https://www.chulbuldesign.com' . $spHref;
    $spName = $spSlug === 'small-business-web-design'
        ? 'Small Business Web Design in Sydney'
        : ($spSlug === 'ecommerce-development' ? 'Ecommerce Website Design in Sydney' : ($sp['label'] . ' in Sydney'));
    $offerCatalogItems[] = [
        '@type' => 'Offer',
        'url'   => $spUrl,
        'itemOffered' => [
            '@type'       => 'Service',
            '@id'         => $spUrl . '#service',
            'name'        => $spName,
            'serviceType' => $spName,
            'url'         => $spUrl,
        ],
    ];
}

$serviceSchema = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Service',
    '@id'         => $canonicalUrl . '#web-design-service',
    'name'        => 'Web Design and Development Services in Sydney',
    'serviceType' => 'Custom Website Design, WordPress Development, Shopify Development, Ecommerce & SEO',
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
    'knowsAbout'  => [
        'Website Design',
        'Website Development',
        'WordPress Development',
        'Shopify Development',
        'WooCommerce Development',
        'UI/UX Design',
        'Technical SEO',
    ],
    'hasOfferCatalog' => [
        '@type'           => 'OfferCatalog',
        'name'            => 'Sydney Web Design Services & Packages',
        'itemListElement' => $offerCatalogItems,
    ],
    'provider'    => ['@id' => 'https://www.chulbuldesign.com/#organization'],
];

$faqSchema = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array_map(static fn(array $faq): array => [
        '@type'          => 'Question',
        'name'           => $faq['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
    ], $sydneyFaqs),
];

$breadcrumbSchema = [
    '@context'        => 'https://schema.org',
    '@type'           => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://www.chulbuldesign.com/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => 'Cities We Serve', 'item' => 'https://www.chulbuldesign.com/cities'],
        ['@type' => 'ListItem', 'position' => 3, 'name' => 'Web Design in Sydney', 'item' => $canonicalUrl],
    ],
];

// Header performance & schema flags (isolated to Sydney page only)
$cbd_page_has_charset       = true;
$cbd_skip_global_org_schema = true;
$cbd_defer_custom_css       = true;
$cbd_skip_header_fonts      = true;
$page_styles                = ['/assets/css/city-sydney.css'];
$cbd_geo_page = [
    'url'         => $canonicalUrl,
    'name'        => $pageTitle,
    'description' => $metaDesc,
    'inLanguage'  => 'en-AU',
    'about'       => [
        'Web Design in Sydney',
        'Small Business Website Design Sydney',
        'WordPress & Shopify Development Sydney',
        'Ecommerce Website Development Sydney',
    ],
    'speakable'   => ['h1', '.cbd-ai-summary', '#faq'],
];
// Intentionally omit geo.position, ICBM, geo.region and geo.placename meta tags
// so head metadata never implies a physical Sydney office (service area is in Service JSON-LD).
$_geo_meta = null;
?>
<!DOCTYPE html>
<html lang="en-AU">
<head>
    <meta charset="utf-8">
    <title><?= $esc($pageTitle) ?></title>
    <link rel="canonical" href="<?= $esc($canonicalUrl) ?>">
    <meta name="description" content="<?= $esc($metaDesc) ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= $esc($pageTitle) ?>">
    <meta property="og:url" content="<?= $esc($canonicalUrl) ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_AU">
    <meta property="og:description" content="<?= $esc($metaDesc) ?>">
    <meta property="og:image" content="<?= $esc($shareImage) ?>">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="<?= $esc($shareImageAlt) ?>">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@chulbuldesign">
    <meta name="twitter:title" content="<?= $esc($pageTitle) ?>">
    <meta name="twitter:description" content="<?= $esc($metaDesc) ?>">
    <meta name="twitter:image" content="<?= $esc($shareImage) ?>">
    <meta name="twitter:image:alt" content="<?= $esc($shareImageAlt) ?>">

    <!-- Non-blocking Sydney Typography (Denk One display + Inter body + JetBrains Mono) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Denk+One&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Denk+One&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet"></noscript>

    <!-- Structured Data: Organization + Service + FAQPage + BreadcrumbList (+ WebPage via header.php) -->
    <script type="application/ld+json"><?= json_encode($organizationSchema, $jf) ?></script>
    <script type="application/ld+json"><?= json_encode($serviceSchema, $jf) ?></script>
    <script type="application/ld+json"><?= json_encode($faqSchema, $jf) ?></script>
    <script type="application/ld+json"><?= json_encode($breadcrumbSchema, $jf) ?></script>

    <?php require __DIR__ . '/header.php'; ?>

<div class="syd-page">

    <!-- ═══════════════════════════════════════════════
         1. BESPOKE SYDNEY HARBOUR STUDIO HERO
    ═══════════════════════════════════════════════ -->
    <section class="syd-hero" aria-labelledby="sydney-hero-h1">
        <div class="syd-hero-glow-1" aria-hidden="true"></div>
        <div class="syd-hero-glow-2" aria-hidden="true"></div>
        <div class="syd-hero-glow-3" aria-hidden="true"></div>
        <div class="syd-hero-grid-bg" aria-hidden="true"></div>

        <!-- Subtle Architectural Sydney Opera Sails, Harbour Bridge Arch & Southern Cross Sky -->
        <div class="syd-sail-bg" aria-hidden="true">
            <svg class="syd-sail-svg" viewBox="0 0 1440 440" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M-20 385 Q 260 320, 560 375 T 1180 355 T 1460 390" stroke="url(#sydWave1)" stroke-width="1.5"/>
                <path d="M-20 412 Q 340 355, 680 402 T 1460 385" stroke="url(#sydWave2)" stroke-width="1"/>
                <!-- Opera House Sails (Left Horizon — Harbour Teal & Sydney Gold) -->
                <path d="M76 378 Q 134 156, 290 374" stroke="rgba(27,155,187,0.34)" stroke-width="1.6" fill="rgba(27,155,187,0.045)"/>
                <path d="M184 376 Q 256 108, 434 370" stroke="rgba(240,186,25,0.28)" stroke-width="1.6" fill="rgba(240,186,25,0.03)"/>
                <path d="M328 372 Q 392 194, 518 368" stroke="rgba(213,47,51,0.24)" stroke-width="1.3" fill="rgba(213,47,51,0.025)"/>
                <!-- Sydney Harbour Bridge Arch Silhouette (Right Horizon) -->
                <path d="M890 368 Q 1160 120, 1430 368" stroke="rgba(240,186,25,0.28)" stroke-width="1.7"/>
                <path d="M926 368 Q 1160 165, 1394 368" stroke="rgba(27,155,187,0.24)" stroke-width="1.2"/>
                <line x1="890" y1="368" x2="1430" y2="368" stroke="rgba(240,186,25,0.2)" stroke-width="1.2"/>
                <line x1="1010" y1="266" x2="1010" y2="368" stroke="rgba(255,255,255,0.09)" stroke-width="1"/>
                <line x1="1085" y1="218" x2="1085" y2="368" stroke="rgba(255,255,255,0.09)" stroke-width="1"/>
                <line x1="1160" y1="202" x2="1160" y2="368" stroke="rgba(255,255,255,0.09)" stroke-width="1"/>
                <line x1="1235" y1="218" x2="1235" y2="368" stroke="rgba(255,255,255,0.09)" stroke-width="1"/>
                <line x1="1310" y1="266" x2="1310" y2="368" stroke="rgba(255,255,255,0.09)" stroke-width="1"/>
                <!-- Delicate Southern Cross Constellation Stars in Sydney Gold -->
                <g fill="#F0BA19" fill-opacity="0.45">
                    <circle cx="670" cy="64" r="2.3"/>
                    <circle cx="642" cy="108" r="2.1"/>
                    <circle cx="708" cy="112" r="2.1"/>
                    <circle cx="676" cy="166" r="2.5"/>
                    <circle cx="696" cy="134" r="1.5"/>
                </g>
                <g stroke="#F0BA19" stroke-opacity="0.22" stroke-width="0.9">
                    <line x1="670" y1="64" x2="642" y2="108"/>
                    <line x1="670" y1="64" x2="708" y2="112"/>
                    <line x1="642" y1="108" x2="676" y2="166"/>
                    <line x1="708" y1="112" x2="676" y2="166"/>
                </g>
                <defs>
                    <linearGradient id="sydWave1" x1="0" y1="0" x2="1440" y2="0" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#1B9BBB" stop-opacity="0"/>
                        <stop offset="0.3" stop-color="#1B9BBB" stop-opacity="0.48"/>
                        <stop offset="0.65" stop-color="#F0BA19" stop-opacity="0.4"/>
                        <stop offset="0.88" stop-color="#D52F33" stop-opacity="0.28"/>
                        <stop offset="1" stop-color="#252973" stop-opacity="0"/>
                    </linearGradient>
                    <linearGradient id="sydWave2" x1="0" y1="0" x2="1440" y2="0" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#ffffff" stop-opacity="0"/>
                        <stop offset="0.5" stop-color="#F0BA19" stop-opacity="0.26"/>
                        <stop offset="1" stop-color="#ffffff" stop-opacity="0"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>

        <div class="syd-wrap">
            <div class="syd-hero-split">
                <!-- Left: Sydney Value Proposition -->
                <div>
                    <nav aria-label="Breadcrumb" class="syd-crumb">
                        <a href="<?= $base ?>/">Home</a>
                        <i class="bi bi-chevron-right" aria-hidden="true"></i>
                        <a href="<?= $base ?>/cities">Cities We Serve</a>
                        <i class="bi bi-chevron-right" aria-hidden="true"></i>
                        <span aria-current="page">Sydney</span>
                    </nav>

                    <div>
                        <span class="syd-live-bar">
                            <?= $renderAuFlag() ?>
                            <span class="syd-live-dot" aria-hidden="true"></span>
                            <span>Serving Sydney, NSW Remotely · Gurugram HQ</span>
                            <span aria-hidden="true">·</span>
                            <span class="syd-tz-clock" id="sydLiveClock">Sydney AEST/AEDT</span>
                        </span>
                    </div>

                    <h1 id="sydney-hero-h1" class="syd-h1">
                        Custom &amp; Small Business Web Design in <span class="syd-h1-accent">Sydney, NSW</span>
                    </h1>

                    <p class="syd-hero-lead">
                        Custom business websites, WordPress builds, and Shopify or WooCommerce stores designed for Sydney companies — delivered remotely by our Gurugram, India studio with transparent fixed-scope pricing starting from A$699.
                    </p>

                    <div class="syd-hero-ctas">
                        <a href="#sydney-quote-form" class="syd-btn syd-btn-coral">
                            <i class="bi bi-file-earmark-text" aria-hidden="true"></i> Request Free Sydney Quote
                        </a>
                        <a href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%20would%20like%20a%20free%20website%20quote%20for%20my%20Sydney%20business."
                           target="_blank" rel="noopener noreferrer" class="syd-btn syd-btn-glass">
                            <i class="bi bi-whatsapp" aria-hidden="true"></i> Chat on WhatsApp
                        </a>
                    </div>

                    <div class="syd-hero-price-strip" aria-label="Sydney transparent AUD pricing tiers">
                        <div class="syd-price-pill-item">
                            <small>Starter Website</small>
                            <strong>A$699</strong>
                        </div>
                        <span class="syd-price-divider" aria-hidden="true"></span>
                        <div class="syd-price-pill-item">
                            <small>Growth Website</small>
                            <strong>A$1,299</strong>
                        </div>
                        <span class="syd-price-divider" aria-hidden="true"></span>
                        <div class="syd-price-pill-item">
                            <small>Enterprise &amp; E-Com</small>
                            <strong>Custom Quote</strong>
                        </div>
                    </div>
                </div>

                <!-- Right: Architectural Quote Card -->
                <div class="syd-form-shell" id="sydney-quote-form">
                    <div class="syd-form-card">
                        <span class="syd-form-kicker"><?= $renderAuFlag() ?> Free AUD Proposal</span>
                        <p class="syd-form-title">Tell us about your Sydney project</p>
                        <p class="syd-form-sub">Get a written scope, platform recommendation &amp; AUD estimate.</p>

                        <form id="cityQuoteForm" action="<?= $base ?>/lead-submit.php" method="POST" style="display:flex;flex-direction:column;gap:10px;">
                            <input type="text" name="_hp" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                            <input type="hidden" name="source" value="city-sydney">
                            <input type="hidden" name="city" value="Sydney">
                            <input type="hidden" name="page_url" value="<?= $esc($canonicalUrl) ?>">
                            <input type="hidden" name="form_version" value="city-sydney-harbour-v4">

                            <div class="syd-form-row">
                                <input class="syd-input" type="text" name="name" placeholder="Your Name *" aria-label="Your name" autocomplete="name" required>
                                <input class="syd-input" type="text" name="business" placeholder="Business Name *" aria-label="Business or company name" autocomplete="organization" maxlength="160" required>
                            </div>
                            <div class="syd-form-row">
                                <input class="syd-input" type="tel" name="phone" placeholder="Phone / WhatsApp (+61) *" aria-label="Phone or WhatsApp number with country code" autocomplete="tel" maxlength="24" required>
                                <input class="syd-input" type="email" name="email" placeholder="Work Email" aria-label="Email address (optional)" autocomplete="email">
                            </div>
                            <div>
                                <select id="sydneyServiceSelect" name="service" aria-label="Service required" required class="syd-input">
                                    <option value="" disabled selected>Select Service or Package *</option>
                                    <option value="Starter Website (A$699)">Starter Website (A$699)</option>
                                    <option value="Growth Website (A$1,299)">Growth Website (A$1,299)</option>
                                    <option value="Shopify / WooCommerce Ecommerce">Shopify / WooCommerce Ecommerce</option>
                                    <option value="WordPress Development">WordPress Development</option>
                                    <option value="Website Redesign & SEO">Website Redesign &amp; SEO</option>
                                    <option value="Enterprise / Custom Web App">Enterprise / Custom Web App</option>
                                </select>
                            </div>
                            <div>
                                <textarea class="syd-input" name="message" rows="2" placeholder="Goals, page count, or current website URL (optional)" aria-label="Project requirements (optional)" maxlength="1000"></textarea>
                            </div>
                            <details style="font-size:12.5px;color:#475569;">
                                <summary style="cursor:pointer;font-weight:700;padding:2px 0;">Optional: Budget &amp; target timeline</summary>
                                <div class="syd-form-row" style="margin-top:8px;">
                                    <input class="syd-input" type="text" name="budget" maxlength="100" placeholder="Budget in AUD" aria-label="Project budget range in AUD (optional)">
                                    <select class="syd-input" name="timeline" aria-label="Expected project start (optional)">
                                        <option value="">Target start</option>
                                        <option value="Within 1 month">Within 1 month</option>
                                        <option value="In 1–3 months">In 1–3 months</option>
                                        <option value="After 3 months">After 3 months</option>
                                        <option value="Exploring options">Exploring options</option>
                                    </select>
                                </div>
                            </details>
                            <button id="cityQuoteSubmit" type="submit" class="syd-btn syd-btn-coral" style="width:100%;margin-top:4px;">
                                <i class="bi bi-send-fill" aria-hidden="true"></i> Request Free Sydney Quote
                            </button>
                            <div id="cityQuoteMessage" class="hidden rounded-xl px-4 py-3 text-sm text-center" role="status" aria-live="polite"></div>
                        </form>

                        <div class="syd-form-meta">
                            <span><i class="bi bi-shield-check text-green-600" aria-hidden="true"></i> Written Scope First</span>
                            <span aria-hidden="true">·</span>
                            <span><i class="bi bi-clock text-indigo-700" aria-hidden="true"></i> AEST/AEDT Replies</span>
                            <span aria-hidden="true">·</span>
                            <span><i class="bi bi-key text-cyan-700" aria-hidden="true"></i> 100% Ownership</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         2. FLOATING SERVICE SNAPSHOT RIBBON
    ═══════════════════════════════════════════════ -->
    <div class="syd-ribbon-wrap">
        <div class="syd-wrap">
            <div class="syd-ribbon-grid" aria-label="Sydney service snapshot">
                <div class="syd-snap-card">
                    <div class="syd-snap-top"><span>Agency HQ</span><i class="bi bi-buildings text-[#49499A]" aria-hidden="true"></i></div>
                    <div><div class="syd-snap-val">Chulbul Design</div><div class="syd-snap-sub">Gurugram, India (2013)</div></div>
                </div>
                <div class="syd-snap-card">
                    <div class="syd-snap-top"><span>Service Area</span><i class="bi bi-geo-alt-fill text-[#c8231a]" aria-hidden="true"></i></div>
                    <div><div class="syd-snap-val">Sydney, NSW</div><div class="syd-snap-sub">Remote Project Delivery</div></div>
                </div>
                <div class="syd-snap-card">
                    <div class="syd-snap-top"><span>Timeline</span><i class="bi bi-stopwatch text-indigo-600" aria-hidden="true"></i></div>
                    <div><div class="syd-snap-val">Scope-Based</div><div class="syd-snap-sub">Agreed Milestone Plan</div></div>
                </div>
                <div class="syd-snap-card">
                    <div class="syd-snap-top"><span>AUD Pricing</span><i class="bi bi-tag-fill text-emerald-700" aria-hidden="true"></i></div>
                    <div><div class="syd-snap-val">From A$699</div><div class="syd-snap-sub">Growth A$1,299 · Custom</div></div>
                </div>
                <div class="syd-snap-card">
                    <div class="syd-snap-top"><span>Platforms</span><i class="bi bi-code-slash text-cyan-700" aria-hidden="true"></i></div>
                    <div><div class="syd-snap-val">WP · Shopify</div><div class="syd-snap-sub">WooCommerce &amp; Custom</div></div>
                </div>
                <div class="syd-snap-card">
                    <div class="syd-snap-top"><span>Time Zone</span><i class="bi bi-clock-history text-violet-700" aria-hidden="true"></i></div>
                    <div><div class="syd-snap-val">AEST / AEDT</div><div class="syd-snap-sub">Live Staging &amp; Zoom</div></div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════════════════════════════
         3. EDITORIAL INTRO + DARK SYDNEY MARKET PLAYBOOK + QUICK FACTS
    ═══════════════════════════════════════════════ -->
    <section class="syd-sec" aria-labelledby="sydney-intro-h2">
        <div class="syd-wrap">
            <div class="syd-editorial-grid">
                <div>
                    <span class="syd-kicker"><i class="bi bi-compass-fill" aria-hidden="true"></i> Built for Sydney Businesses</span>
                    <h2 id="sydney-intro-h2" class="syd-h2">
                        Custom Website Design &amp; Development in <span class="syd-grad">Sydney, New South Wales</span>
                    </h2>
                    <div class="syd-intro-prose">
                        <p>
                            Chulbul Design provides custom website design and development for <strong>Sydney</strong> businesses that want a fast, modern site structured around real customer enquiries. Whether you need a focused small business website, a clean WordPress build your team can update in-house, or a Shopify or WooCommerce store built for Australian shoppers, we design every page around clarity and conversion.
                        </p>
                        <p>
                            Our design and engineering studio operates remotely from Gurugram, India, partnering directly with founders and marketing teams via scheduled AEST/AEDT calls and live staging previews. With transparent fixed-scope AUD packages starting from <strong>A$699</strong>, you get documented milestones, full ownership of your code and accounts, and practical post-launch handover.
                        </p>
                    </div>
                </div>

                <!-- Dark Frosted Local Market Playbook (Factual, Zero Unverified Stats) -->
                <aside class="syd-market-card" aria-labelledby="sydney-market-h2">
                    <div class="syd-pat-waves" aria-hidden="true"></div>
                    <span class="syd-market-tag"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> Local Market Strategy</span>
                    <h2 id="sydney-market-h2">Sydney Web Design Agency Insights &amp; Local Market Strategy</h2>
                    <p>
                        Across Greater Sydney — Australia's largest metropolitan market — buyers regularly shortlist service providers and online stores on mobile before reaching out. Slow pages, cluttered navigation, or unclear pricing cause high-intent visitors to drop off quickly across competitive precincts like the CBD, North Sydney, and Parramatta.
                    </p>
                    <p>
                        Winning enquiries requires fast mobile UX, clear service pages that answer buyer questions upfront, strong technical SEO for NSW search intent, and smooth checkout or multilingual site options where your customers need them.
                    </p>
                    <div class="syd-market-pills">
                        <span><i class="bi bi-phone" aria-hidden="true"></i> Mobile-First Evaluation</span>
                        <span><i class="bi bi-search" aria-hidden="true"></i> NSW Search Structure</span>
                        <span><i class="bi bi-translate" aria-hidden="true"></i> Multilingual Ready</span>
                        <span><i class="bi bi-lightning-charge" aria-hidden="true"></i> Core Web Vitals Focus</span>
                    </div>
                </aside>
            </div>

            <!-- Quick Facts AI Summary Box (Verified AUD Range — Zero Formatting Bugs) -->
            <div style="margin-top:42px;">
                <?php
                require_once __DIR__ . '/geo-schema.php';
                cbd_render_ai_summary_box([
                    'heading' => 'Quick Facts: Web Design & Development in Sydney',
                    'summary' => 'Chulbul Design provides custom website design, small business websites, WordPress and Shopify development, WooCommerce stores, and technical SEO for Sydney businesses. Operating remotely from Gurugram, India, we deliver transparent fixed-scope AUD packages with full client ownership of code and domains.',
                    'facts' => [
                        'Primary Services' => 'Web Design, WordPress, Shopify & SEO',
                        'Service Area'     => 'Sydney, New South Wales (Remote)',
                        'Indicative Range' => 'Starter A$699 · Growth A$1,299 · Enterprise Custom Quote',
                        'Delivery Model'   => 'Remote from Gurugram, India (AEST/AEDT sync)',
                    ],
                ]);
                ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         4. SYDNEY PRECINCT EXPLORER (Exact 8 Areas, Honest Remote Delivery)
    ═══════════════════════════════════════════════ -->
    <section class="syd-sec syd-sec-tint" aria-labelledby="sydney-areas-h2">
        <div class="syd-wrap">
            <div class="syd-head">
                <span class="syd-kicker"><i class="bi bi-pin-map-fill" aria-hidden="true"></i> Remote Service Coverage</span>
                <h2 id="sydney-areas-h2" class="syd-h2">
                    Remote Web Design Across <span class="syd-grad">Sydney's Key Business Precincts</span>
                </h2>
                <p class="syd-lead">
                    Our Gurugram, India team supports businesses across these Sydney precincts with online discovery workshops, interactive Figma reviews, live staging previews, and full handover.
                </p>
            </div>

            <div class="syd-precinct-grid" aria-label="Remote service areas supported across Sydney">
                <?php foreach ($sydneyPrecincts as $p): ?>
                <article class="syd-precinct-card">
                    <div class="syd-precinct-top">
                        <span class="syd-precinct-name"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> <?= $esc($p['name']) ?></span>
                        <span class="syd-precinct-tag"><?= $esc($p['tag']) ?></span>
                    </div>
                    <p class="syd-precinct-desc"><?= $esc($p['desc']) ?></p>
                </article>
                <?php endforeach; ?>
            </div>

            <p class="syd-remote-note">
                <strong>Transparency Note:</strong> These locations represent remote service areas we support online — Chulbul Design is headquartered in Gurugram, India and does not maintain a physical office in these precincts.
            </p>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         5. BENTO SERVICES ARCHITECTURE (7 Specific Capabilities)
    ═══════════════════════════════════════════════ -->
    <section id="services" class="syd-sec" aria-labelledby="sydney-services-h2">
        <div class="syd-wrap">
            <div class="syd-head">
                <span class="syd-kicker"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i> Core Capabilities</span>
                <h2 id="sydney-services-h2" class="syd-h2">
                    Website Design, WordPress, Shopify &amp; <span class="syd-grad">SEO Services</span>
                </h2>
                <p class="syd-lead">
                    Focused design, engineering, and ecommerce services scoped clearly around your business goals and Australian customer journeys.
                </p>
            </div>

            <div class="syd-bento">
                <?php foreach ($sydneyServices as $svc): ?>
                <a href="<?= $esc($base . $svc['href']) ?>" class="syd-bento-card <?= $esc($svc['span']) ?><?= !empty($svc['featured']) ? ' is-featured' : '' ?>">
                    <?php if (!empty($svc['featured'])): ?>
                    <div class="syd-pat-waves" aria-hidden="true"></div>
                    <?php endif; ?>
                    <div class="syd-bento-top">
                        <span class="syd-icon-badge <?= $esc($svc['icClass']) ?>" aria-hidden="true"><i class="bi <?= $esc($svc['icon']) ?>"></i></span>
                        <span class="syd-bento-num"><?= $esc($svc['num']) ?></span>
                    </div>
                    <h3><?= $esc($svc['title']) ?></h3>
                    <p><?= $esc($svc['desc']) ?></p>
                    <span class="syd-bento-cta"><?= $esc($svc['link_label']) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         6. DARK COSMIC INDUSTRIES SHOWCASE (6 Sector Playbooks)
    ═══════════════════════════════════════════════ -->
    <section class="syd-sec syd-sec-cosmic" aria-labelledby="sydney-industries-h2">
        <div class="syd-pat-sails" aria-hidden="true"></div>
        <div class="syd-wrap">
            <div class="syd-head">
                <span class="syd-kicker"><i class="bi bi-buildings-fill" aria-hidden="true"></i> Sector Experience</span>
                <h2 id="sydney-industries-h2" class="syd-h2">
                    Industries We Serve in <span class="syd-grad">Sydney</span>
                </h2>
                <p class="syd-lead">
                    Every sector converts differently — we align page architecture, trust cues, and enquiry or checkout flows to how your specific buyers decide.
                </p>
            </div>

            <div class="syd-ind-grid">
                <?php foreach ($sydneyIndustries as $ind): ?>
                <article class="syd-ind-card">
                    <div class="syd-ind-head">
                        <span class="syd-ind-icon" aria-hidden="true"><i class="bi <?= $esc($ind['icon']) ?>"></i></span>
                        <h3><?= $esc($ind['title']) ?></h3>
                    </div>
                    <p><?= $esc($ind['desc']) ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         7. INTERACTIVE 5-STEP PROCESS + AEST/IST OVERLAP STRIP
    ═══════════════════════════════════════════════ -->
    <section class="syd-sec" aria-labelledby="sydney-process-h2">
        <div class="syd-wrap">
            <div class="syd-head">
                <span class="syd-kicker"><i class="bi bi-diagram-3-fill" aria-hidden="true"></i> Remote Delivery Process</span>
                <h2 id="sydney-process-h2" class="syd-h2">
                    How We Take Your Website from <span class="syd-grad">Brief to Launch</span>
                </h2>
                <p class="syd-lead">
                    Five structured milestones with Figma approvals, live staging previews, and full ownership handover.
                </p>
            </div>

            <div class="syd-timeline" data-syd-timeline>
                <div class="syd-timeline-rail" aria-hidden="true">
                    <div class="syd-timeline-fill" data-syd-timeline-fill></div>
                </div>
                <?php
                $steps = [
                    ['num' => '01', 'title' => 'Discovery & Scope',  'desc' => 'Align on goals, sitemap, platform fit, and a transparent written AUD proposal.'],
                    ['num' => '02', 'title' => 'UI Design & Review', 'desc' => 'Design responsive Figma layouts using realistic content before engineering begins.'],
                    ['num' => '03', 'title' => 'Development',        'desc' => 'Build clean WordPress, Shopify, or custom templates with technical SEO structure.'],
                    ['num' => '04', 'title' => 'QA & Speed Checks',  'desc' => 'Test forms, mobile responsiveness, browser rendering, and Core Web Vitals.'],
                    ['num' => '05', 'title' => 'Launch & Handover',  'desc' => 'Deploy live, transfer full admin and code ownership, and provide included support.'],
                ];
                foreach ($steps as $idx => $st): ?>
                <div class="syd-step<?= $idx === 0 ? ' is-active' : '' ?>" data-syd-step="<?= $idx ?>">
                    <div class="syd-step-num" aria-hidden="true"><?= $esc($st['num']) ?></div>
                    <div class="syd-step-card">
                        <h3><?= $esc($st['title']) ?></h3>
                        <p><?= $esc($st['desc']) ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="syd-tz-bar">
                <div>
                    <strong>Seamless Sydney (AEST/AEDT) ↔ Gurugram (IST) Overlap</strong>
                    <p>With a 4.5–5.5 hour time difference, your afternoon review meetings overlap cleanly with our morning engineering sprint — keeping feedback loops fast without overnight delays.</p>
                </div>
                <span class="syd-tz-pill"><i class="bi bi-camera-video-fill" aria-hidden="true"></i> Zoom · Meet · WhatsApp · Staging Links</span>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         8. ELEVATED PRICING ARENA (#pricing)
    ═══════════════════════════════════════════════ -->
    <section id="pricing" class="syd-sec syd-sec-tint" aria-labelledby="sydney-pricing-h2">
        <div class="syd-wrap">
            <div class="syd-head">
                <span class="syd-kicker"><?= $renderAuFlag() ?> Transparent AUD Pricing</span>
                <h2 id="sydney-pricing-h2" class="syd-h2">
                    Sydney Website Design Cost &amp; <span class="syd-grad">Clear Packages</span>
                </h2>
                <p class="syd-lead">
                    Fixed-scope Australian Dollar packages with documented inclusions before we write a single line of code.
                </p>
            </div>

            <div class="syd-pricing-grid">
                <?php foreach ($sydneyPlans as $plan): ?>
                <article class="syd-price-card<?= $plan['popular'] ? ' is-popular' : '' ?>">
                    <?php if ($plan['popular']): ?>
                    <div class="syd-pat-waves" aria-hidden="true"></div>
                    <span class="syd-pop-badge"><i class="bi bi-star-fill" aria-hidden="true"></i> Most Popular</span>
                    <?php endif; ?>
                    <h3><?= $esc($plan['name']) ?></h3>
                    <p class="syd-price-sub"><?= $esc($plan['label']) ?></p>
                    <p class="syd-price-num"><?= $esc($plan['price']) ?></p>
                    <p class="syd-price-term"><?= $plan['numPrice'] !== '' ? 'One-time fixed package (AUD)' : 'Scoped to your exact requirements' ?></p>
                    <p class="syd-best-for"><?= $esc($plan['bestFor']) ?></p>

                    <ul class="syd-price-list">
                        <?php foreach ($plan['features'] as $feat): ?>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i><span><?= $esc($feat) ?></span></li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="syd-price-actions">
                        <a href="#sydney-quote-form"
                           data-syd-select-plan="<?= $esc($plan['name'] === 'Starter' ? 'Starter Website (A$699)' : ($plan['name'] === 'Growth' ? 'Growth Website (A$1,299)' : 'Enterprise / Custom Web App')) ?>"
                           class="syd-btn <?= $plan['popular'] ? 'syd-btn-coral' : 'syd-btn-navy' ?>" style="width:100%;">
                            <?= $plan['numPrice'] !== '' ? 'Select ' . $esc($plan['name']) . ' Plan' : 'Request Custom Quote' ?>
                        </a>
                        <a href="https://wa.me/919990548795?text=<?= rawurlencode('Hi Chulbul Design, I am interested in the ' . $plan['name'] . ' (' . $plan['price'] . ') web design package for my Sydney business.') ?>"
                           target="_blank" rel="noopener noreferrer" class="syd-wa-soft">
                            <i class="bi bi-whatsapp text-green-600" aria-hidden="true"></i> Ask About <?= $esc($plan['name']) ?> on WhatsApp
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <div class="syd-extra-box">
                <div>
                    <h3>What May Cost Extra (Itemised Upfront)</h3>
                    <p>
                        Third-party costs remain under your own accounts and are never marked up silently: domain registration, hosting or Shopify plan fees, paid apps/plugins, bespoke copywriting or photography, multilingual translations, custom CRM/ERP integrations, and optional ongoing maintenance after your included support window.
                    </p>
                </div>
                <div>
                    <h3>How to Request a Custom Quote</h3>
                    <p>
                        Not sure which tier fits your project? Share your approximate page count, required functionality (Shopify, WooCommerce, booking, or custom portals), and reference sites in our <a href="#sydney-quote-form" style="color:#49499A;font-weight:700;text-decoration:underline;">Sydney quote form</a> or on WhatsApp for a written AUD breakdown.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         9. TRANSPARENT LIVE PORTFOLIO PROOF + AU PET FOOD SHOPIFY
    ═══════════════════════════════════════════════ -->
    <div style="background:#ffffff;padding:56px 24px 0;">
        <div class="syd-proof-banner" role="note" aria-label="Portfolio project origin note">
            <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
            <div>
                <strong>Transparent Portfolio Note:</strong> Featured first below is <strong>Brunswick Fur Food</strong> — a live <strong>Melbourne-based Australian</strong> pet food Shopify store we built, shown here to demonstrate our relevant Australian Shopify ecommerce capabilities alongside our broader international and custom web projects. We showcase verified live builds rather than inventing Sydney-specific case studies.
            </div>
        </div>
    </div>
    <?php
    require_once __DIR__ . '/portfolio-showcase.php';
    cbd_render_portfolio_showcase([
        'id'           => 'portfolio',
        'eyebrow'      => 'Selected Live Builds',
        'title'        => 'Selected Australian & International Web Projects',
        'subtitle'     => 'Inspect live desktop and mobile websites — starting with our Melbourne-based Australian Shopify build (Brunswick Fur Food), followed by international corporate, SaaS and ecommerce projects.',
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

    <section class="pfa-city-cta-wrap" aria-labelledby="pfa-sydney-cta-heading">
        <div class="pfa-city-cta-container">
            <div class="pfa-city-cta-card">
                <div>
                    <span class="pfa-city-cta-eyebrow">
                        <i class="bi bi-stars" aria-hidden="true"></i>
                        Specialist Shopify · Australian Pet Brands
                    </span>
                    <h2 class="pfa-city-cta-title" id="pfa-sydney-cta-heading">
                        Running a <span>Pet Food or Pet Store</span> Brand in Sydney?
                    </h2>
                    <p class="pfa-city-cta-desc">
                        Building on our hands-on experience designing and developing an Australian pet food Shopify store (Brunswick Fur Food in Melbourne), we offer dedicated Online Store 2.0 design, recurring subscription UX, dietary filters, and ongoing store support for Sydney and NSW pet brands.
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
                                 alt="Australian dog owner holding a natural dog food pouch representing Chulbul Design Shopify ecommerce capabilities for Sydney pet brands"
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
         10. DEDICATED SYDNEY SPOKES & PLANNING GUIDES
    ═══════════════════════════════════════════════ -->
    <section id="local-resources" class="syd-sec" aria-labelledby="sydney-resources-h2">
        <div class="syd-wrap">
            <div class="syd-head">
                <span class="syd-kicker"><i class="bi bi-journal-richtext" aria-hidden="true"></i> Local Resources</span>
                <h2 id="sydney-resources-h2" class="syd-h2">
                    Dedicated Sydney Services &amp; <span class="syd-grad">Planning Guides</span>
                </h2>
                <p class="syd-lead">
                    Explore focused Sydney service scopes and practical guides to compare platforms, redesign checklists, and website costs.
                </p>
            </div>

            <div class="syd-res-grid">
                <?php
                $sydSpokeCopy = [
                    'small-business-web-design' => 'Affordable small business web design in Sydney from A$699. Mobile-first websites, clear enquiry forms and on-page SEO foundations delivered on an agreed milestone schedule.',
                    'ecommerce-development'     => 'Custom ecommerce website design in Sydney on Shopify and WooCommerce with official platform payment integrations, client-confirmed GST display setup and fast mobile checkout.',
                ];
                foreach ($city_spokes as $spoke):
                    $spSlug = (string)($spoke['slug'] ?? '');
                    $spTitle = $spSlug === 'small-business-web-design'
                        ? 'Small Business Web Design in Sydney'
                        : ($spSlug === 'ecommerce-development' ? 'Ecommerce Website Design in Sydney' : ($spoke['label'] . ' — Sydney'));
                    $spDesc = $sydSpokeCopy[$spSlug]
                        ?? ((string)($spoke['description'] ?: 'Scoped website design and development deliverables tailored for Sydney businesses.'));
                ?>
                <article class="syd-res-card">
                    <span class="syd-kicker" style="width:fit-content;margin-bottom:12px;color:#3730a3;background:#eef2ff;border-color:#c7d2fe;">Dedicated Service</span>
                    <h3><?= $esc($spTitle) ?></h3>
                    <p><?= $esc($spDesc) ?></p>
                    <a href="<?= $esc($base . $spoke['href']) ?>" class="syd-bento-cta">
                        <?= $esc($spTitle) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </article>
                <?php endforeach; ?>

                <?php foreach ($city_blogs as $guide): ?>
                <article class="syd-res-card">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:12px;">
                        <span class="syd-kicker" style="margin:0;"><?= $esc((string)($guide['category'] ?: 'Planning Guide')) ?></span>
                        <?php if (!empty($guide['read_time'])): ?>
                        <span style="font-size:12px;color:#4b5563;font-weight:600;"><?= $esc((string)$guide['read_time']) ?></span>
                        <?php endif; ?>
                    </div>
                    <h3><?= $esc((string)$guide['title']) ?></h3>
                    <?php if (!empty($guide['excerpt'])): ?>
                    <p><?= $esc((string)$guide['excerpt']) ?></p>
                    <?php endif; ?>
                    <a href="<?= $esc($base . $guide['href']) ?>" class="syd-bento-cta" style="color:#49499A;">
                        Read Full Guide <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         11. SPLIT FAQ SECTION (100% Exact Match with FAQPage Schema)
    ═══════════════════════════════════════════════ -->
    <section id="faq" class="syd-sec syd-sec-tint" aria-labelledby="sydney-faq-h2">
        <div class="syd-wrap">
            <div class="syd-faq-split">
                <div class="syd-faq-aside">
                    <span class="syd-kicker"><i class="bi bi-question-circle-fill" aria-hidden="true"></i> Clear Answers</span>
                    <h2 id="sydney-faq-h2" class="syd-h2">
                        Sydney Web Design, Pricing &amp; <span class="syd-grad">SEO FAQs</span>
                    </h2>
                    <p class="syd-lead" style="margin-bottom:22px;">
                        Straight answers on AUD pricing, Australian ecommerce gateways, SEO foundations, and remote AEST/AEDT collaboration.
                    </p>
                    <a href="#sydney-quote-form" class="syd-btn syd-btn-navy">
                        Ask About Your Project <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="syd-faq-list">
                    <?php foreach ($sydneyFaqs as $i => $faq): ?>
                    <details class="syd-faq-item"<?= $i === 0 ? ' open' : '' ?>>
                        <summary>
                            <span><?= $esc($faq['q']) ?></span>
                            <span class="syd-faq-plus" aria-hidden="true"><i class="bi bi-plus-lg"></i></span>
                        </summary>
                        <div class="syd-faq-body"><?= $esc($faq['a']) ?></div>
                    </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ═══════════════════════════════════════════════
         12. NEARBY AUSTRALIAN CITIES + HARBOUR FINAL CTA
    ═══════════════════════════════════════════════ -->
    <section class="syd-sec" style="padding-top:42px;">
        <div class="syd-wrap">
            <?php if (count($related) > 0): ?>
            <div style="text-align:center;margin-bottom:48px;">
                <p style="display:inline-flex;align-items:center;gap:8px;font-size:12px;font-weight:800;letter-spacing:0.09em;text-transform:uppercase;color:#4b5563;margin:0 0 16px;">
                    <?= $renderAuFlag() ?> We Also Serve Businesses Across Australia
                </p>
                <div class="syd-nearby-pills">
                    <?php foreach ($related as $cname => $cslug):
                        if ($cslug === 'sydney') continue; ?>
                    <a href="<?= $esc($base . '/city/' . $cslug) ?>" class="syd-nearby-pill">
                        <i class="bi bi-geo-alt-fill" style="color:#c8231a;" aria-hidden="true"></i>
                        <?= $esc($cname) ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="syd-cta-panel">
                <div class="syd-pat-sails" aria-hidden="true"></div>
                <div class="syd-cta-copy">
                    <span class="syd-kicker" style="color:#F0BA19;background:rgba(240,186,25,0.14);border-color:rgba(240,186,25,0.34);">
                        <i class="bi bi-lightning-charge-fill" aria-hidden="true"></i> Ready to Start?
                    </span>
                    <h2>Get a Clear AUD Quote for Your <span class="syd-h1-accent">Sydney Website Project</span></h2>
                    <p>
                        Share your business goals, page count, or ecommerce brief — our Gurugram team will reply with a practical platform recommendation and fixed-scope AUD proposal.
                    </p>
                    <div class="syd-cta-checks">
                        <span><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Starter From A$699</span>
                        <span><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Remote AEST/AEDT Delivery</span>
                        <span><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Written Milestone Scope</span>
                        <span><i class="bi bi-check-circle-fill" aria-hidden="true"></i> 100% Client Ownership</span>
                    </div>
                </div>
                <div class="syd-cta-actions">
                    <a href="#sydney-quote-form" class="syd-btn syd-btn-coral">
                        <i class="bi bi-file-earmark-text" aria-hidden="true"></i> Request Free Sydney Quote
                    </a>
                    <a href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%20need%20a%20website%20quote%20for%20my%20Sydney%20business."
                       target="_blank" rel="noopener noreferrer" class="syd-btn syd-btn-glass">
                        <i class="bi bi-whatsapp" aria-hidden="true"></i> WhatsApp Us
                    </a>
                </div>
            </div>
        </div>
    </section>

</div>

<script>
(() => {
    // 1. Live Sydney Clock (Australia/Sydney timezone)
    const clockEl = document.getElementById('sydLiveClock');
    if (clockEl && typeof Intl !== 'undefined' && Intl.DateTimeFormat) {
        const updateClock = () => {
            try {
                const now = new Date();
                const t = new Intl.DateTimeFormat('en-AU', {
                    timeZone: 'Australia/Sydney',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true,
                    timeZoneName: 'short'
                }).format(now);
                clockEl.textContent = 'Sydney ' + t;
            } catch (e) {}
        };
        updateClock();
        setInterval(updateClock, 60000);
    }

    // 2. Interactive 5-step timeline progress
    const tl = document.querySelector('[data-syd-timeline]');
    if (tl) {
        const steps = Array.from(tl.querySelectorAll('[data-syd-step]'));
        const fill = tl.querySelector('[data-syd-timeline-fill]');
        const setStep = (idx) => {
            steps.forEach((s, i) => s.classList.toggle('is-active', i <= idx));
            if (fill && steps.length > 1) {
                fill.style.width = ((idx / (steps.length - 1)) * 100) + '%';
            }
        };
        steps.forEach((s, i) => {
            s.addEventListener('mouseenter', () => setStep(i));
            s.addEventListener('click', () => setStep(i));
        });
    }

    // 3. Pricing CTA plan pre-selection
    document.querySelectorAll('[data-syd-select-plan]').forEach((el) => {
        el.addEventListener('click', () => {
            const sel = document.getElementById('sydneyServiceSelect');
            const val = el.getAttribute('data-syd-select-plan');
            if (sel && val) {
                for (let i = 0; i < sel.options.length; i++) {
                    if (sel.options[i].value === val) {
                        sel.selectedIndex = i;
                        break;
                    }
                }
            }
        });
    });

    // 4. Sydney Lead Form AJAX submission
    const form = document.getElementById('cityQuoteForm');
    if (!form) return;

    const button = document.getElementById('cityQuoteSubmit');
    const message = document.getElementById('cityQuoteMessage');
    const originalButtonHtml = button.innerHTML;

    const showMessage = (type, text, whatsappUrl = '') => {
        message.className = type === 'success'
            ? 'rounded-xl px-4 py-3 text-sm text-center bg-green-50 text-green-800 border border-green-200'
            : 'rounded-xl px-4 py-3 text-sm text-center bg-red-50 text-red-800 border border-red-200';
        message.replaceChildren(document.createTextNode(text));

        if (whatsappUrl) {
            const link = document.createElement('a');
            link.href = whatsappUrl;
            link.target = '_blank';
            link.rel = 'noopener noreferrer';
            link.className = 'mt-2 inline-flex items-center justify-center gap-1.5 font-bold underline';
            link.innerHTML = '<i class="bi bi-whatsapp" aria-hidden="true"></i> Continue on WhatsApp';
            message.appendChild(document.createElement('br'));
            message.appendChild(link);
        }
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!form.reportValidity()) return;

        button.disabled = true;
        button.classList.add('opacity-70', 'cursor-not-allowed');
        button.innerHTML = '<i class="bi bi-arrow-repeat animate-spin" aria-hidden="true"></i> Sending…';
        message.classList.add('hidden');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.error || 'Submission failed');

            showMessage('success', 'Thank you! Your Sydney project enquiry has been received. We will reply with next steps shortly.', data.whatsapp_url || '');
            form.reset();
        } catch (error) {
            showMessage(
                'error',
                'Your request could not be sent right now. Please try again or reach us directly on WhatsApp.',
                'https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%20would%20like%20a%20quote%20for%20my%20Sydney%20website.'
            );
        } finally {
            button.disabled = false;
            button.classList.remove('opacity-70', 'cursor-not-allowed');
            button.innerHTML = originalButtonHtml;
        }
    });
})();
</script>

<?php require __DIR__ . '/footer.php'; ?>
