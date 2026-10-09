<?php
$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';

require_once __DIR__ . '/includes/location-repository.php';
require_once __DIR__ . '/includes/http.php';
$location_page = cbd_location_page($slug);
$db_location_data = null;
$cities = [];
$city_testimonials_data = [];
$city_insights = [];

if (($location_page['status'] ?? '') === 'not_found') {
    $redirect = cbd_location_redirect_target($slug);
    if ($redirect) {
        $location = cbd_location_base_path() . '/city/' . rawurlencode((string)$redirect['slug']);
        header('Location: ' . $location, true, (int)$redirect['code']);
        exit;
    }
}

if (($location_page['status'] ?? '') === 'ok') {
    $db_location_data = $location_page['data'];
    $cities[$slug] = $db_location_data['city'];
    $city_testimonials_data[$db_location_data['country_key']] = $db_location_data['testimonials'];
    $city_insights[$slug] = $db_location_data['market_insight'];
}

if (($location_page['status'] ?? '') === 'unavailable') {
    require __DIR__ . '/503.php';
    exit;
}

if (!isset($cities[$slug])) {
    require __DIR__ . '/404.php';
    exit;
}

// A direct city.php?slug= request is a duplicate URL; send it to the canonical path.
if (cbd_is_direct_script_request('city.php')) {
    cbd_redirect_path('/city/' . rawurlencode($slug));
}

$c = $cities[$slug];

// All location content below comes from the database.
$country = $db_location_data['country_key'];
$stats = $db_location_data['stats'];
$highlights = $db_location_data['highlights'];
$services = $db_location_data['services'];
$related = $db_location_data['related'];
$price_range = $db_location_data['price_range'];
$og_locale = $db_location_data['og_locale'];
$hreflang = $db_location_data['hreflang'];
$delivered_text = $db_location_data['delivery_label'];
$query_sections = $db_location_data['query_sections'] ?? [];
$industries_for_display = $c['industries'];
if ($slug === 'seattle') {
    $industries_for_display = array_map(
        static fn(string $industry): string =>
            $industry === 'Coffee & Food' ? 'Contractors & Home Services' : $industry,
        $industries_for_display
    );
}
$seattle_offer_catalog_items = [
    [
        'section_key' => 'specialized-ecommerce-web-design',
        'name' => 'Ecommerce Development',
        'path' => '/city/seattle/ecommerce-development',
        'visible_description' => 'Ecommerce Development for Shopify and WooCommerce stores with mobile checkout.',
    ],
    [
        'section_key' => 'wordpress-design-development',
        'name' => 'WordPress Web Design',
        'path' => '/city/seattle/wordpress-developer',
        'visible_description' => 'Fast, secure WordPress Web Design with flexible editing and strong SEO foundations.',
    ],
    [
        'section_key' => 'small-business-web-design',
        'name' => 'Small Business Web Design',
        'path' => '/city/seattle/small-business-web-design',
        'visible_description' => 'Trust-building Small Business Web Design that turns Seattle searches into enquiries.',
    ],
    [
        'section_key' => 'contractor-home-services',
        'name' => 'Contractor Website Design',
        'path' => '/city/seattle/contractor-web-design',
        'visible_description' => 'Contractor Website Design with service-area pages, project galleries and quote forms.',
    ],
    [
        'section_key' => 'specialized-landing-page-design',
        'name' => 'Landing Page Design',
        'path' => '/landing-page-design',
        'visible_description' => 'Landing Page Design for focused campaigns, analytics and customer action.',
    ],
    [
        'section_key' => 'specialized-performance-optimization',
        'name' => 'Website Performance Optimization',
        'path' => '/website-speed-optimization',
        'visible_description' => 'Website Performance Optimization through Core Web Vitals and code fixes.',
    ],
];
$seattle_offer_catalog_by_section = [];
foreach ($seattle_offer_catalog_items as $offerCatalogItem) {
    $seattle_offer_catalog_by_section[$offerCatalogItem['section_key']] = $offerCatalogItem;
}
$regional_query_sections = array_values(array_filter(
    $query_sections,
    static fn(array $section): bool => ($section['placement'] ?? '') === 'after_areas'
));
$feature_query_sections = array_values(array_filter(
    $query_sections,
    static fn(array $section): bool =>
        ($section['placement'] ?? '') === 'after_services' &&
        ($section['layout'] ?? '') === 'feature'
));
$specialized_query_sections = array_values(array_filter(
    $query_sections,
    static fn(array $section): bool =>
        ($section['placement'] ?? '') === 'after_services' &&
        ($section['layout'] ?? '') === 'service_card'
));

// Phase 4 merges specialized and performance cards into the existing Seattle
// Services grid. The fallback keeps the six-card layout available when files
// are deployed before the matching DB migration.
if (count($specialized_query_sections) < 6 && $slug === 'seattle') {
    $phase4Fallback = [
        ['source' => 'ecommerce-development', 'key' => 'specialized-ecommerce-web-design',
         'title' => 'Ecommerce Web Design',
         'description' => 'Shopify and WooCommerce stores with mobile checkout and conversion tracking.',
         'icon' => 'bi-cart-check', 'label' => 'Explore Ecommerce Websites',
         'href' => '/city/seattle/ecommerce-development'],
        ['source' => 'small-business-web-design', 'key' => 'small-business-web-design',
         'title' => 'Small Business Web Design',
         'description' => 'Trust-building websites that turn Seattle searches into qualified enquiries.',
         'icon' => 'bi-shop-window', 'label' => 'Small Business Websites',
         'href' => '/city/seattle/small-business-web-design'],
        ['source' => 'wordpress-design-development', 'key' => 'wordpress-design-development',
         'title' => 'WordPress Web Design',
         'description' => 'Fast, secure WordPress websites with flexible editing and strong SEO foundations.',
         'icon' => 'bi-wordpress', 'label' => 'View WordPress Web Design',
         'href' => '/city/seattle/wordpress-developer'],
        ['source' => 'contractor-home-services', 'key' => 'contractor-home-services',
         'title' => 'Contractor & Home Services Web Design',
         'description' => 'Service-area websites with project galleries, quote forms and call tracking.',
         'icon' => 'bi-tools', 'label' => 'Plan a Contractor Website',
         'href' => '/city/seattle/contractor-web-design'],
        ['source' => 'landing-page-performance', 'key' => 'specialized-landing-page-design',
         'title' => 'Landing Page Design',
         'description' => 'Landing pages built for campaigns, analytics and action.',
         'icon' => 'bi-layout-text-window-reverse', 'label' => 'Explore Landing Page Design',
         'href' => '/landing-page-design'],
        ['source' => 'landing-page-performance', 'key' => 'specialized-performance-optimization',
         'title' => 'Performance Optimization',
         'description' => 'Faster pages through Core Web Vitals and code fixes.',
         'icon' => 'bi-speedometer2', 'label' => 'Improve Website Performance',
         'href' => '/website-speed-optimization'],
    ];

    $sectionsByKey = [];
    foreach ($query_sections as $section) {
        $sectionsByKey[(string)($section['key'] ?? '')] = $section;
    }
    $specialized_query_sections = [];
    foreach ($phase4Fallback as $card) {
        if (!isset($sectionsByKey[$card['source']])) {
            continue;
        }
        $specialized_query_sections[] = [
            'key' => $card['key'], 'placement' => 'after_services',
            'layout' => 'service_card', 'eyebrow' => '',
            'title' => $card['title'], 'description' => $card['description'],
            'bullets' => [], 'icon' => $card['icon'],
            'primary_link' => ['label' => $card['label'], 'href' => $card['href']],
            'secondary_link' => ['label' => '', 'href' => ''],
        ];
    }
}

$services_for_display = $services;
if ($slug === 'seattle' && count($specialized_query_sections) === 6) {
    $services_for_display = array_map(
        static function (array $section) use ($seattle_offer_catalog_by_section): array {
            $offerCatalogItem = $seattle_offer_catalog_by_section[(string)($section['key'] ?? '')] ?? null;
            return [
                'icon' => $section['icon'],
                'title' => $section['title'],
                'href' => $section['primary_link']['href'],
                'desc' => $offerCatalogItem['visible_description'] ?? $section['description'],
                'link_label' => $section['primary_link']['label'],
            ];
        },
        $specialized_query_sections
    );
} else {
    // Dynamic spoke overlay: if this city has dedicated service spokes, link directly to them
    $_loc_pdo = cbd_location_database();
    $city_spoke_map = [];
    if ($_loc_pdo) {
        try {
            $stmt = $_loc_pdo->prepare(
                "SELECT sp.slug, sp.breadcrumb_label 
                 FROM city_spokes sp
                 INNER JOIN cities ci ON ci.id = sp.city_id
                 WHERE ci.slug = ? AND sp.status = 'published'"
            );
            $stmt->execute([$slug]);
            while ($spRow = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $city_spoke_map[$spRow['slug']] = $spRow;
            }
        } catch (Throwable) {}
    }

    if (!empty($city_spoke_map)) {
        $services_for_display = array_map(
            static function (array $svc) use ($slug, $c, $city_spoke_map): array {
                if ($svc['href'] === '/ecommerce-development' && isset($city_spoke_map['ecommerce-development'])) {
                    $svc['href'] = '/city/' . rawurlencode($slug) . '/ecommerce-development';
                    $svc['link_label'] = 'Explore ' . $c['name'] . ' Ecommerce';
                } elseif ($svc['href'] === '/web-design-development' && isset($city_spoke_map['small-business-web-design'])) {
                    $svc['href'] = '/city/' . rawurlencode($slug) . '/small-business-web-design';
                    $svc['link_label'] = 'Small Business Web Design in ' . $c['name'];
                }
                return $svc;
            },
            $services_for_display
        );
    }
}
$compact_query_sections = array_values(array_filter(
    $query_sections,
    static fn(array $section): bool =>
        ($section['placement'] ?? '') === 'after_services' &&
        ($section['layout'] ?? '') === 'compact' &&
        (
            $slug !== 'seattle' ||
            !in_array(
                (string)($section['key'] ?? ''),
                ['landing-page-performance', 'small-business-web-design', 'wordpress-design-development', 'contractor-home-services'],
                true
            )
        )
));
?>
<!DOCTYPE html>
<html lang="<?= $hreflang ?>">
<head><meta charset="utf-8">
    <title><?= htmlspecialchars($c['meta_title']) ?></title>
    <link rel="canonical" href="<?= htmlspecialchars($c['canonical']) ?>">
    <!-- City service pages are distinct destinations, not translated homepages. -->
    <meta name="description" content="<?= htmlspecialchars($c['meta_desc']) ?>">

    <!-- Open Graph -->
    <meta property="og:title" content="<?= htmlspecialchars($c['meta_title']) ?>">
    <meta property="og:url" content="<?= htmlspecialchars($c['canonical']) ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="<?= $og_locale ?>">
    <meta property="og:description" content="<?= htmlspecialchars($c['meta_desc']) ?>">
    <meta property="og:image" content="https://www.chulbuldesign.com/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Chulbul Design — website design and development for businesses worldwide">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@chulbuldesign">
    <meta name="twitter:image" content="https://www.chulbuldesign.com/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg">
    <meta name="twitter:image:alt" content="Chulbul Design — website design and development for businesses worldwide">

<?php
$jf = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
$schema_area_city = $slug === 'seattle' ? 'Seattle' : $c['schema_city'];
$schema_area_region = $slug === 'seattle' ? 'Washington' : $c['state'];

// Service schema: Chulbul Design operates from India and serves the selected
// city remotely. Keeping the provider address inside Organization avoids
// implying that the company has a physical office in every city page.
$service_schema = [
    '@context'     => 'https://schema.org',
    '@type'        => 'Service',
    '@id'          => $c['canonical'] . '#web-design-service',
    'name'         => 'Web Design and Development Services in ' . $schema_area_city,
    'serviceType'  => 'Web design, web development, ecommerce development and SEO',
    'url'          => $c['canonical'],
    'description'  => $c['meta_desc'],
    'areaServed'   => [
        '@type' => 'City',
        'name' => $schema_area_city,
        'containedInPlace' => ['@type' => 'AdministrativeArea', 'name' => $schema_area_region],
    ],
    'knowsAbout'   => ['Web Design', 'Web Development', 'E-commerce Development', 'SEO', 'WordPress Development', 'Mobile App Development'],
    'hasOfferCatalog' => [
        '@type' => 'OfferCatalog',
        'name' => 'Web Design Services',
        'itemListElement' => [
            [
                '@type' => 'Offer',
                'itemOffered' => [
                    '@type' => 'Service',
                    'name' => 'Business Website Design',
                ],
                'priceSpecification' => [
                    '@type' => 'PriceSpecification',
                    'price' => (string)$c['starter_price'],
                    'priceCurrency' => match ($country) {
                        'india' => 'INR',
                        'uk'    => 'GBP',
                        'uae'   => 'AED',
                        default => 'USD',
                    },
                ],
            ],
        ],
    ],
    'provider'     => [
        '@type'     => 'Organization',
        '@id'       => 'https://www.chulbuldesign.com/#organization',
        'name'      => 'Chulbul Design',
        'url'       => 'https://www.chulbuldesign.com/',
        'telephone' => '+919990548795',
        'email'     => 'info@chulbuldesign.com',
        'logo'      => ['@type' => 'ImageObject', 'url' => 'https://www.chulbuldesign.com/assets/images/logo/chulbuldesign.svg'],
        'address'   => ['@type' => 'PostalAddress', 'streetAddress' => 'Sector 15, Flat No. 1277', 'addressLocality' => 'Gurugram', 'addressRegion' => 'Haryana', 'postalCode' => '122001', 'addressCountry' => 'IN'],
        'sameAs'    => ['https://www.facebook.com/chulbuldesign/', 'https://www.instagram.com/chulbuldesign/', 'https://twitter.com/ChulbulDesign/', 'https://www.linkedin.com/company/chulbuldesign/'],
    ],
];

if ($slug === 'seattle') {
    $service_schema['hasOfferCatalog'] = [
        '@type' => 'OfferCatalog',
        'name' => 'Seattle Web Design Service Options',
        'itemListElement' => array_map(
            static function (array $offerCatalogItem): array {
                $serviceUrl = 'https://www.chulbuldesign.com' . $offerCatalogItem['path'];
                $servicePageUrl = explode('#', $serviceUrl, 2)[0];
                return [
                    '@type' => 'Offer',
                    'itemOffered' => [
                        '@type' => 'Service',
                        '@id' => $servicePageUrl . '#service-' . $offerCatalogItem['section_key'],
                        'name' => $offerCatalogItem['name'],
                        'serviceType' => $offerCatalogItem['name'],
                        'url' => $serviceUrl,
                    ],
                ];
            },
            $seattle_offer_catalog_items
        ),
    ];
}

// FAQ Schema
$faq_entities = [];
foreach ($c['faq'] as $faq) {
    $faq_entities[] = [
        '@type'          => 'Question',
        'name'           => $faq['q'],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
    ];
}
$faq_schema = ['@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $faq_entities];
?>
    <!-- Service Schema -->
    <script type="application/ld+json"><?= json_encode($service_schema, $jf) ?></script>

    <!-- FAQ Schema -->
    <script type="application/ld+json"><?= json_encode($faq_schema, $jf) ?></script>

    <!-- BreadcrumbList Schema -->
    <?php $breadcrumb_schema = ['@context'=>'https://schema.org','@type'=>'BreadcrumbList','itemListElement'=>[['@type'=>'ListItem','position'=>1,'name'=>'Home','item'=>'https://www.chulbuldesign.com/'],['@type'=>'ListItem','position'=>2,'name'=>'Cities We Serve','item'=>'https://www.chulbuldesign.com/cities'],['@type'=>'ListItem','position'=>3,'name'=>'Web Design in '.$c['name'],'item'=>$c['canonical']]]]; ?>
    <script type="application/ld+json"><?= json_encode($breadcrumb_schema, $jf) ?></script>

    <?php
    $_country_geo = [
        'uk'           => ['region' => 'GB',   'pos' => '54.7024;-3.2765',    'icbm' => '54.7024, -3.2765'],
        'us'           => ['region' => 'US',   'pos' => '37.0902;-95.7129',   'icbm' => '37.0902, -95.7129'],
        'australia'    => ['region' => 'AU',   'pos' => '-25.2744;133.7751',  'icbm' => '-25.2744, 133.7751'],
        'canada'       => ['region' => 'CA',   'pos' => '56.1304;-106.3468',  'icbm' => '56.1304, -106.3468'],
        'uae'          => ['region' => 'AE',   'pos' => '23.4241;53.8478',    'icbm' => '23.4241, 53.8478'],
        'singapore'    => ['region' => 'SG',   'pos' => '1.3521;103.8198',    'icbm' => '1.3521, 103.8198'],
        'south_africa' => ['region' => 'ZA',   'pos' => '-30.5595;22.9375',   'icbm' => '-30.5595, 22.9375'],
    ];
    if ($db_location_data !== null && !empty($db_location_data['country_geo']['region'])) {
        $_country_geo[$country] = [
            'region' => $db_location_data['country_geo']['region'],
            'pos'    => $db_location_data['country_geo']['position'],
            'icbm'   => $db_location_data['country_geo']['icbm'],
        ];
    }
    $_cg = $_country_geo[$country] ?? null;
    $_geo_meta = $_cg ? [
        'region'    => $c['geo_region']   ?? $_cg['region'],
        'placename' => $c['name'],
        'pos'       => $c['geo_position'] ?? $_cg['pos'],
        'icbm'      => $c['geo_icbm']     ?? $_cg['icbm'],
    ] : null;
    require __DIR__ . '/includes/header.php'; ?>

<!-- ═══════════════════════════════════════════════
     HERO SECTION — Light animated style
═══════════════════════════════════════════════ -->
<!-- Hero animations & grid → custom.css -->
<section class="city-hero-section">

    <!-- Animated blobs -->
    <div class="h-blob-1 city-hero-blob-1" aria-hidden="true"></div>
    <div class="h-blob-2 city-hero-blob-2" aria-hidden="true"></div>
    <div class="h-blob-3 city-hero-blob-3" aria-hidden="true"></div>

    <!-- Dot grid -->
    <div class="city-hero-dot-grid" aria-hidden="true"></div>

    <!-- Spinning decorative rings (top-right) -->
    <div class="h-ring-1 city-hero-ring-1" aria-hidden="true"></div>
    <div class="h-ring-2 city-hero-ring-2" aria-hidden="true"></div>

    <!-- Floating accent squares -->
    <div class="h-blob-1 city-hero-sq-1" aria-hidden="true"></div>
    <div class="h-blob-3 city-hero-sq-2" aria-hidden="true"></div>
    <div class="h-blob-2 city-hero-sq-3" aria-hidden="true"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative city-hero-inner">
        <div class="flex flex-col lg:flex-row items-center gap-10">

            <!-- Left: Text -->
            <div class="w-full lg:w-[60%] text-center lg:text-left">

                <!-- Breadcrumb -->
                <nav aria-label="Breadcrumb" class="h-anim-1 flex items-center justify-center lg:justify-start gap-2 text-gray-400 text-sm mb-5">
                    <a href="<?= $base ?>/" class="hover:text-[#49499A] transition">Home</a>
                    <i class="bi bi-chevron-right text-xs" aria-hidden="true"></i>
                    <span class="text-[#49499A] font-semibold"><?= htmlspecialchars($c['name']) ?></span>
                </nav>

                <!-- Badge -->
                <div class="h-badge mb-5">
                    <span class="inline-flex items-center gap-1.5 text-xs font-bold tracking-widest uppercase text-[#49499A] bg-[#49499A]/10 border border-[#49499A]/25 px-4 py-1.5 rounded-full">
                        <span class="city-hero-badge-dot"></span>
                        <?= htmlspecialchars($c['tagline']) ?>
                    </span>
                </div>

                <!-- H1 -->
                <h1 class="h-anim-2 text-3xl sm:text-4xl lg:text-5xl xl:text-5xl font-extrabold text-[#1e1e5c] leading-tight mb-5">
                    <?= $c['h1'] ?>
                </h1>

                <p class="h-anim-3 text-gray-500 text-base lg:text-lg leading-relaxed max-w-xl mx-auto lg:mx-0 mb-8">
                    <?= htmlspecialchars($c['hero_desc']) ?>
                </p>

                <!-- CTA Buttons -->
                <div class="h-anim-4 flex flex-wrap gap-3 justify-center lg:justify-start">
                    <a href="https://wa.me/919990548795?text=Hi%2C+I+need+a+website+for+my+<?= urlencode($c['name']) ?>+business"
                       target="_blank" rel="noopener noreferrer"
                       class="inline-flex items-center gap-2 bg-[#EE483D] hover:bg-red-600 text-white px-7 py-3.5 rounded-xl font-semibold transition-all text-sm shadow-lg shadow-red-400/30">
                        <i class="bi bi-whatsapp" aria-hidden="true"></i> Get Free Quote
                    </a>
                </div>

                <!-- Starting Price Badge -->
                <div class="h-anim-4 mt-6 inline-flex items-center gap-3 bg-white border border-gray-200 shadow-md rounded-2xl px-5 py-3">
                    <div class="w-9 h-9 rounded-xl bg-[#EE483D] flex items-center justify-center shrink-0">
                        <i class="bi bi-tag-fill text-white text-sm" aria-hidden="true"></i>
                    </div>
                    <div class="text-left">
                        <p class="text-gray-400 text-xs leading-none mb-0.5">Websites Starting From</p>
                        <p class="text-[#1e1e5c] font-extrabold text-lg leading-none"><?= ($c['symbol'] ?? '₹') . ($c['starter_price'] ?? '18,000') ?> <span class="text-gray-400 font-normal text-xs">one-time</span></p>
                    </div>
                    <div class="h-8 w-px bg-gray-200 mx-1"></div>
                    <div class="text-left">
                        <p class="text-gray-400 text-xs leading-none mb-0.5">Free Consultation</p>
                        <p class="text-[#1e1e5c] font-bold text-sm leading-none">No Commitment</p>
                    </div>
                </div>

            </div>

            <!-- Right: Stats Card -->
            <div class="w-full lg:w-[40%] flex justify-center lg:justify-end">
                <div class="bg-white border border-gray-200 rounded-2xl p-8 w-full shadow-2xl shadow-indigo-100/60">
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-widest mb-1">Get A Free Quote</p>
                    <p class="text-[#1e1e5c] font-extrabold text-lg mb-5">Tell us about your <?= htmlspecialchars($c['name']) ?> project</p>
                    <form id="cityQuoteForm" action="<?= $base ?>/lead-submit.php" method="POST" class="space-y-3">
                        <input type="text" name="_hp" value="" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                        <input type="hidden" name="source" value="city-<?= htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="city" value="<?= htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="page_url" value="<?= htmlspecialchars($c['canonical'], ENT_QUOTES, 'UTF-8') ?>">
                        <input type="hidden" name="form_version" value="city-project-v2">
                        <div>
                            <input type="text" name="name" placeholder="Your Name" aria-label="Your name" autocomplete="name" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#49499A] transition">
                        </div>
                        <div>
                            <input type="text" name="business" placeholder="Business / Company Name" aria-label="Business or company name" autocomplete="organization" maxlength="160" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#49499A] transition">
                        </div>
                        <div>
                            <input type="tel" name="phone" placeholder="Phone / WhatsApp with country code" aria-label="Phone or WhatsApp number with country code" autocomplete="tel" maxlength="24" required
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#49499A] transition">
                        </div>
                        <div>
                            <input type="email" name="email" placeholder="Email Address" aria-label="Email address (optional)" autocomplete="email"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#49499A] transition">
                        </div>
                        <div>
                            <select name="service" aria-label="Service required" required class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-500 focus:outline-none focus:border-[#49499A] transition">
                                <option value="" disabled selected>Select Service</option>
                                <option>Web Design</option>
                                <option>Web Development</option>
                                <option>E-commerce Website</option>
                                <option>WordPress Website</option>
                                <option>Mobile App</option>
                                <option>SEO / Digital Marketing</option>
                            </select>
                        </div>
                        <div>
                            <textarea name="message" rows="3" placeholder="Brief project requirements (optional)" aria-label="Project requirements (optional)"
                                maxlength="1000"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:border-[#49499A] transition resize-y"></textarea>
                        </div>
                        <details class="text-sm text-gray-600">
                            <summary class="cursor-pointer font-semibold py-1">Project budget &amp; timing (optional)</summary>
                            <div class="space-y-3 mt-3">
                                <input type="text" name="budget" maxlength="100" placeholder="Budget range + currency, or Not decided" aria-label="Project budget range and currency (optional)"
                                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#49499A] transition">
                                <select name="timeline" aria-label="Expected project start (optional)" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#49499A] transition">
                                    <option value="">When would you like to start?</option>
                                    <option value="Within 1 month">Within 1 month</option>
                                    <option value="In 1–3 months">In 1–3 months</option>
                                    <option value="After 3 months">After 3 months</option>
                                    <option value="Exploring options">Exploring options</option>
                                </select>
                            </div>
                        </details>
                        <button id="cityQuoteSubmit" type="submit"
                            class="mt-2 w-full flex items-center justify-center gap-2 bg-[#EE483D] hover:bg-red-600 text-white py-3 rounded-xl font-semibold transition text-sm shadow-md">
                            <i class="bi bi-send-fill"></i> Get Free Quote
                        </button>
                        <div id="cityQuoteMessage" class="hidden rounded-xl px-4 py-3 text-sm text-center" role="status" aria-live="polite"></div>
                    </form>
                    <div class="mt-4 flex items-center justify-center gap-2 text-xs text-gray-400">
                        <i class="bi bi-shield-check text-green-500"></i>
                        <span>Used to respond to your enquiry</span>
                        <span class="text-gray-300">·</span>
                        <i class="bi bi-patch-check-fill text-[#49499A]"></i>
                        <span>No Hidden Costs</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     SERVICE SNAPSHOT (GEO & Visitor Trust)
═══════════════════════════════════════════════ -->
<?php
$service_region_label = htmlspecialchars($c['name']);
if ($country === 'india') {
    $service_region_label .= ($c['name'] === 'Delhi' ? ' NCR' : ', ' . $c['state']);
    $timezone_label = 'IST (Indian Standard Time)';
} elseif ($country === 'uk') {
    $service_region_label .= ', UK';
    $timezone_label = 'UK Business Hours (GMT/BST)';
} elseif ($country === 'us') {
    $service_region_label .= ', ' . $c['state'] . ' (USA)';
    $timezone_label = 'US Business Hours (EST/PST)';
} elseif ($country === 'uae') {
    $service_region_label .= ', UAE';
    $timezone_label = 'GST (Gulf Standard Time)';
} else {
    $service_region_label .= ($c['state'] !== $c['name'] ? ', ' . $c['state'] : '');
    $timezone_label = 'Timezone-Aligned Online Reviews';
}
?>
<section class="bg-gradient-to-b from-white to-gray-50/80 border-b border-gray-200/80 py-8" aria-label="Web design service overview for <?= htmlspecialchars($c['name']) ?>">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5">
            <div class="flex items-center gap-2.5">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-[#49499A]/10 text-[#49499A] text-sm">
                    <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                </span>
                <h2 class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-800">
                    Service Snapshot · <span class="text-[#EE483D]"><?= htmlspecialchars($c['name']) ?></span>
                </h2>
            </div>
            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-white border border-gray-200 px-3 py-1 rounded-full shadow-xs w-fit">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Accepting New Projects
            </span>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
            <!-- 1. Agency -->
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-500">Agency</span>
                    <i class="bi bi-buildings text-[#49499A] text-base" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-900 leading-snug">Chulbul Design</div>
                    <div class="text-[11px] text-gray-400 mt-0.5">Custom Web &amp; AI</div>
                </div>
            </div>

            <!-- 2. Coverage -->
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-500">Service Area</span>
                    <i class="bi bi-geo-alt-fill text-[#EE483D] text-base" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-900 leading-snug truncate" title="<?= $service_region_label ?>"><?= $service_region_label ?></div>
                    <div class="text-[11px] text-gray-400 mt-0.5">Remote Delivery</div>
                </div>
            </div>

            <!-- 3. Turnaround -->
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-500">Timeline</span>
                    <i class="bi bi-stopwatch text-indigo-500 text-base" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-900 leading-snug">2–4 Weeks</div>
                    <div class="text-[11px] text-gray-400 mt-0.5">Typical Launch</div>
                </div>
            </div>

            <!-- 4. Starting Price -->
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-500">Starting Price</span>
                    <i class="bi bi-tag-fill text-emerald-600 text-base" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-900 leading-snug"><?= $c['symbol'] ?><?= htmlspecialchars($c['starter_price']) ?></div>
                    <div class="text-[11px] text-gray-400 mt-0.5">Transparent Quote</div>
                </div>
            </div>

            <!-- 5. Technology -->
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-500">Tech Stack</span>
                    <i class="bi bi-code-slash text-amber-500 text-base" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-900 leading-snug">WordPress · PHP</div>
                    <div class="text-[11px] text-gray-400 mt-0.5">Shopify &amp; Custom Code</div>
                </div>
            </div>

            <!-- 6. Client Reviews / Collaboration -->
            <div class="bg-white border border-gray-100 rounded-2xl p-4 shadow-xs hover:shadow-md transition-shadow flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-medium text-gray-500">Collaboration</span>
                    <i class="bi bi-clock-history text-cyan-600 text-base" aria-hidden="true"></i>
                </div>
                <div>
                    <div class="text-sm font-bold text-gray-900 leading-snug truncate" title="<?= $timezone_label ?>"><?= $timezone_label ?></div>
                    <div class="text-[11px] text-gray-400 mt-0.5">Milestone Approvals</div>
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
            <span class="flex items-center gap-2"><i class="bi bi-patch-check-fill text-[#49499A]" aria-hidden="true"></i> Quality-Focused Delivery</span>
            <span class="flex items-center gap-2"><i class="bi bi-shield-lock-fill text-[#49499A]" aria-hidden="true"></i> Privacy & Security Planning</span>
            <span class="flex items-center gap-2"><i class="bi bi-phone-fill text-[#49499A]" aria-hidden="true"></i> Mobile-First Design</span>
            <span class="flex items-center gap-2"><i class="bi bi-graph-up-arrow text-[#49499A]" aria-hidden="true"></i> SEO Built-In</span>
            <span class="flex items-center gap-2"><i class="bi bi-lightning-charge-fill text-[#49499A]" aria-hidden="true"></i> Performance-Focused Build</span>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     INTRO SECTION
═══════════════════════════════════════════════ -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-5">
            Web Design &amp; Development Services for <span class="text-[#EE483D]"><?= htmlspecialchars($c['name']) ?></span>
        </h2>
        <p class="text-gray-600 text-lg leading-relaxed">
            <?= preg_replace(
                '/\b(' . preg_quote($c['name'], '/') . ')\b/',
                '<strong>$1</strong>',
                htmlspecialchars($c['intro']),
                2
            ) ?>
        </p>
    </div>
</section>


<?php if (!empty($city_insights[$slug])): ?>
<!-- ═══════════════════════════════════════════════
     CITY MARKET INSIGHT CALLOUT
═══════════════════════════════════════════════ -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-4">
    <div class="bg-[#49499A]/5 border-l-4 border-[#49499A] rounded-xl px-6 py-5 flex gap-4 items-start">
        <i class="bi bi-geo-alt-fill text-[#EE483D] text-2xl mt-0.5 shrink-0" aria-hidden="true"></i>
        <div>
            <p class="text-xs font-bold tracking-widest uppercase text-[#49499A] mb-1"><?= htmlspecialchars($c['name']) ?> Market Insight</p>
            <p class="text-gray-700 text-sm leading-relaxed"><?= htmlspecialchars($city_insights[$slug]) ?></p>
        </div>
    </div>
</section>
<?php endif; ?>


<?php if (!empty($c['areas'])): ?>
<!-- ═══════════════════════════════════════════════
     SERVICE AREAS — transparent remote coverage
═══════════════════════════════════════════════ -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-5xl mx-auto text-center">
        <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Service Area</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-4 mb-3">
            Website Services Across <?= htmlspecialchars($c['name']) ?> and Nearby Areas
        </h2>
        <p class="text-gray-500 max-w-3xl mx-auto mb-7">
            Our India-based team delivers projects remotely for businesses across the <?= htmlspecialchars($c['name']) ?> market, with online discovery, design reviews and project updates.
        </p>
        <ul class="flex flex-wrap justify-center gap-3" aria-label="Areas served around <?= htmlspecialchars($c['name']) ?>">
            <?php foreach ($c['areas'] as $area): ?>
            <li class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-700 text-sm font-semibold px-4 py-2.5 rounded-full shadow-sm">
                <i class="bi bi-geo-alt-fill text-[#EE483D]" aria-hidden="true"></i>
                <?= htmlspecialchars($area) ?>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php foreach ($regional_query_sections as $section): ?>
        <article class="city-query-regional mt-8 text-left rounded-2xl px-6 sm:px-8 py-7">
            <?php if ($section['eyebrow'] !== ''): ?>
            <p class="text-xs font-bold tracking-widest uppercase text-[#EE483D] mb-2">
                <?= htmlspecialchars($section['eyebrow']) ?>
            </p>
            <?php endif; ?>
            <h3 class="text-xl sm:text-2xl font-extrabold text-gray-900 mb-3">
                <?= htmlspecialchars($section['title']) ?>
            </h3>
            <div class="space-y-3 text-gray-600 leading-relaxed">
                <?php foreach (preg_split('/\R{2,}/', trim($section['description'])) as $paragraph): ?>
                <p><?= htmlspecialchars($paragraph) ?></p>
                <?php endforeach; ?>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>


<!-- ═══════════════════════════════════════════════
     LOCAL MARKET STATS
═══════════════════════════════════════════════ -->
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-xs font-bold tracking-widest uppercase text-gray-400 mb-8">
            What a High-Performing <?= htmlspecialchars($c['name']) ?> Website Needs
        </p>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
            <?php foreach ($stats as $stat): ?>
            <div class="city-capability-card bg-white rounded-2xl p-6 text-center shadow-sm border border-gray-100 hover:shadow-md transition">
                <p class="city-capability-value font-extrabold text-[#49499A] mb-2"><?= htmlspecialchars($stat['num']) ?></p>
                <p class="text-gray-500 text-sm leading-snug"><?= htmlspecialchars($stat['label']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Country-specific tech highlights -->
        <div class="mt-8 flex flex-wrap justify-center gap-3">
            <?php foreach ($highlights as $h): ?>
            <span class="inline-flex items-center gap-2 bg-white border border-[#49499A]/20 text-[#49499A] text-xs font-semibold px-4 py-2 rounded-full shadow-sm">
                <i class="bi bi-check-circle-fill text-[#EE483D]" aria-hidden="true"></i>
                <?= htmlspecialchars($h) ?>
            </span>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     SERVICES SECTION
═══════════════════════════════════════════════ -->
<section id="services" class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">What We Do</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-4 mb-3">
                Services for <?= htmlspecialchars($c['name']) ?> Businesses
            </h2>
            <p class="text-gray-500 max-w-2xl mx-auto">Website design, development and supporting services, scoped around your business.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php foreach ($services_for_display as $svc): ?>
            <article class="city-service-card bg-white rounded-2xl p-7 shadow-sm border border-gray-100 hover:shadow-md hover:-translate-y-1 transition-all group">
                <div class="w-14 h-14 rounded-2xl bg-[#49499A]/10 flex items-center justify-center mb-5 group-hover:bg-[#49499A] transition-colors">
                    <i class="bi <?= $svc['icon'] ?> text-2xl text-[#49499A] group-hover:text-white transition-colors" aria-hidden="true"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2"><?= htmlspecialchars($svc['title']) ?></h3>
                <p class="city-service-card-description text-gray-500 text-sm leading-relaxed"><?= htmlspecialchars($svc['desc']) ?></p>
                <a href="<?= htmlspecialchars($base . $svc['href']) ?>" class="city-service-card-link inline-flex items-center gap-1 text-[#EE483D] text-sm font-semibold mt-4 hover:gap-2 transition-all">
                    <?= htmlspecialchars($svc['link_label'] ?? 'Learn More') ?> <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </article>
            <?php endforeach; ?>

        </div>
    </div>
</section>


<?php foreach ($feature_query_sections as $section): ?>
<!-- ═══════════════════════════════════════════════
     QUERY-LED FEATURE — optional city content
════════════════════════════════════════════════ -->
<section class="city-query-feature py-16">
    <div class="absolute inset-0 city-hero-dot-grid opacity-10" aria-hidden="true"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="city-query-feature-grid">
            <div>
                <?php if ($section['eyebrow'] !== ''): ?>
                <span class="city-query-eyebrow inline-flex text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full mb-5">
                    <?= htmlspecialchars($section['eyebrow']) ?>
                </span>
                <?php endif; ?>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-white leading-tight mb-5">
                    <?= htmlspecialchars($section['title']) ?>
                </h2>
                <div class="city-query-copy space-y-4 leading-relaxed">
                    <?php foreach (preg_split('/\R{2,}/', trim($section['description'])) as $paragraph): ?>
                    <p><?= htmlspecialchars($paragraph) ?></p>
                    <?php endforeach; ?>
                </div>

                <div class="flex flex-wrap gap-3 mt-7">
                    <?php if ($section['primary_link']['label'] !== '' && $section['primary_link']['href'] !== ''): ?>
                    <a href="<?= htmlspecialchars($base . $section['primary_link']['href']) ?>"
                       class="inline-flex items-center gap-2 bg-[#EE483D] hover:bg-red-600 text-white px-6 py-3 rounded-xl font-semibold text-sm transition-all">
                        <?= htmlspecialchars($section['primary_link']['label']) ?>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                    <?php endif; ?>
                    <?php if ($section['secondary_link']['label'] !== '' && $section['secondary_link']['href'] !== ''): ?>
                    <a href="<?= htmlspecialchars($base . $section['secondary_link']['href']) ?>"
                       class="city-query-secondary-link inline-flex items-center gap-2 text-white px-6 py-3 rounded-xl font-semibold text-sm transition-all">
                        <?= htmlspecialchars($section['secondary_link']['label']) ?>
                        <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($section['bullets']): ?>
            <div class="city-query-capability-card bg-white rounded-3xl p-6 sm:p-8 shadow-xl">
                <div class="city-query-icon w-14 h-14 rounded-2xl flex items-center justify-center mb-6">
                    <i class="bi <?= htmlspecialchars($section['icon']) ?> text-2xl text-[#EE483D]" aria-hidden="true"></i>
                </div>
                <p class="text-sm font-bold tracking-widest uppercase text-[#49499A] mb-5">Project capabilities</p>
                <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php foreach ($section['bullets'] as $bullet): ?>
                    <li class="flex items-start gap-3 text-sm font-semibold text-gray-700">
                        <i class="bi bi-check-circle-fill text-[#EE483D] mt-0.5 shrink-0" aria-hidden="true"></i>
                        <span><?= htmlspecialchars($bullet) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php endforeach; ?>


<?php if ($compact_query_sections): ?>
<!-- ═══════════════════════════════════════════════
     SPECIALIZED CITY SERVICES — optional query-led cards
════════════════════════════════════════════════ -->
<section class="city-query-specialized py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="city-query-specialized-heading text-center mx-auto">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Specialized Services</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-4 mb-3">
                Specialized Web Design Services for <?= htmlspecialchars($c['name']) ?> Businesses
            </h2>
            <p class="text-gray-500">
                Focused solutions for campaigns, growing companies, content teams and service-based businesses.
            </p>
        </div>

        <div class="city-query-card-grid">
            <?php foreach ($compact_query_sections as $section): ?>
            <article id="<?= htmlspecialchars((string)$section['key'], ENT_QUOTES, 'UTF-8') ?>" class="city-query-card bg-white rounded-2xl p-7 border border-gray-100 shadow-sm transition-all group">
                <div class="w-12 h-12 rounded-xl bg-[#49499A]/10 flex items-center justify-center mb-5 group-hover:bg-[#49499A] transition-colors">
                    <i class="bi <?= htmlspecialchars($section['icon']) ?> text-xl text-[#49499A] group-hover:text-white transition-colors" aria-hidden="true"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-3"><?= htmlspecialchars($section['title']) ?></h3>
                <div class="space-y-3 text-gray-500 text-sm leading-relaxed">
                    <?php foreach (preg_split('/\R{2,}/', trim($section['description'])) as $paragraph): ?>
                    <p><?= htmlspecialchars($paragraph) ?></p>
                    <?php endforeach; ?>
                </div>
                <?php if ($section['primary_link']['label'] !== '' && $section['primary_link']['href'] !== ''): ?>
                <a href="<?= htmlspecialchars($base . $section['primary_link']['href']) ?>"
                   class="inline-flex items-center gap-1 text-[#EE483D] text-sm font-semibold mt-5 hover:gap-2 transition-all">
                    <?= htmlspecialchars($section['primary_link']['label']) ?>
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
                <?php endif; ?>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ═══════════════════════════════════════════════
     WHY US — LOCAL ANGLE
═══════════════════════════════════════════════ -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex flex-col lg:flex-row items-center gap-14">

        <!-- Left: Content -->
        <div class="flex-1">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Why Chulbul Design</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-5 mb-5">
                The Right Choice for <span class="text-[#EE483D]"><?= htmlspecialchars($c['name']) ?></span> Businesses
            </h2>
            <p class="text-gray-600 leading-relaxed mb-8">
                <?= htmlspecialchars($c['why_local']) ?>
            </p>

            <ul class="space-y-4">
                <?php
                $usp = [
                    ['icon' => 'bi-speedometer2',       'title' => 'Performance-Focused Websites',   'desc' => 'Images, code and key templates are optimised for practical speed and usability.'],
                    ['icon' => 'bi-phone',               'title' => 'Responsive Website Design',      'desc' => 'Key layouts and contact journeys are tested across agreed mobile and desktop sizes.'],
                    ['icon' => 'bi-search-heart',        'title' => 'SEO Built Into Every Page',      'desc' => 'On-page SEO, schema markup and technical SEO from day one.'],
                    ['icon' => 'bi-tag-fill',             'title' => 'Transparent Pricing',            'desc' => 'Clear quotes, no hidden costs, no surprise invoices. Ever.'],
                    ['icon' => 'bi-headset',             'title' => 'Direct Project Support',         'desc' => 'Speak with the project team through clear, agreed communication channels.'],
                    ['icon' => 'bi-clock-history',       'title' => 'Milestone-Based Delivery',       'desc' => 'Scope, review points and target dates are documented before development begins.'],
                ];
                foreach ($usp as $item): ?>
                <li class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-[#49499A]/10 flex items-center justify-center shrink-0 mt-0.5">
                        <i class="bi <?= $item['icon'] ?> text-[#49499A]" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-sm"><?= $item['title'] ?></p>
                        <p class="text-gray-500 text-sm"><?= $item['desc'] ?></p>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Right: CTA Card -->
        <div class="flex-1 flex justify-center lg:justify-end w-full">
            <div class="w-full max-w-md rounded-2xl overflow-hidden shadow-xl border border-gray-100">
                <!-- Card Header -->
                <div class="city-whyus-card-header px-8 py-7 text-white">
                    <p class="text-white/70 text-sm font-semibold uppercase tracking-widest mb-1">Free Consultation</p>
                    <h3 class="text-2xl font-extrabold">Let's discuss your<br><?= htmlspecialchars($c['name']) ?> project</h3>
                    <p class="text-white/70 text-sm mt-2">No obligation — just an honest conversation about your requirements.</p>
                </div>
                <!-- Card Body -->
                <div class="bg-white p-8 space-y-4">
                    <a href="https://wa.me/919990548795?text=Hi%2C+I+need+a+website+for+my+<?= urlencode($c['name']) ?>+business"
                       target="_blank" rel="noopener noreferrer"
                       class="flex items-center justify-center gap-3 w-full bg-green-500 hover:bg-green-600 text-white py-3.5 rounded-xl font-semibold transition text-sm">
                        <i class="bi bi-whatsapp text-xl" aria-hidden="true"></i>
                        Chat on WhatsApp
                    </a>
                    <a href="<?= $base ?>/contact-us"
                       class="flex items-center justify-center gap-3 w-full border-2 border-[#EE483D] text-[#EE483D] hover:bg-[#EE483D] hover:text-white py-3.5 rounded-xl font-semibold transition text-sm">
                        <i class="bi bi-envelope-fill" aria-hidden="true"></i>
                        Send Us a Message
                    </a>
                    <p class="text-center text-gray-400 text-xs pt-2">
                        <i class="bi bi-shield-check text-green-500" aria-hidden="true"></i>
                        Free consultation · Replies during business hours
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>


<!-- ═══════════════════════════════════════════════
     INDUSTRIES WE SERVE
═══════════════════════════════════════════════ -->
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Industries</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-4 mb-3">
                Industries We Serve in <?= htmlspecialchars($c['name']) ?>
            </h2>
            <p class="text-gray-500 max-w-xl mx-auto">We understand your sector's unique needs and build solutions that fit perfectly.</p>
        </div>

        <div class="flex flex-wrap justify-center gap-4">
            <?php foreach ($industries_for_display as $ind): ?>
            <?php $industry_icon = $slug === 'seattle' && $ind === 'Contractors & Home Services' ? 'bi-tools' : 'bi-building'; ?>
            <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-full px-5 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:border-[#49499A] hover:text-[#49499A] transition cursor-default">
                <i class="bi <?= htmlspecialchars($industry_icon) ?> text-[#EE483D]" aria-hidden="true"></i>
                <?= htmlspecialchars($ind) ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     PROCESS SECTION
═══════════════════════════════════════════════ -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">How We Work</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-4 mb-3">
            Our Simple 5-Step Process
        </h2>
        <p class="text-gray-500 max-w-xl mx-auto">From first call to final launch — transparent, on-time, and hassle-free for you.</p>
    </div>

    <div class="relative">
        <!-- Connecting line (desktop) -->
        <div class="hidden lg:block absolute top-12 left-[10%] right-[10%] h-px bg-gray-200 z-0" aria-hidden="true"></div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-8 relative z-10">
            <?php
            $steps = [
                ['num' => '01', 'icon' => 'bi-chat-dots-fill',     'title' => 'Discovery Call',        'desc' => 'We understand your business, goals and target audience.'],
                ['num' => '02', 'icon' => 'bi-pencil-square',       'title' => 'Design & Prototype',    'desc' => 'We create wireframes and UI mockups for your approval.'],
                ['num' => '03', 'icon' => 'bi-code-slash',          'title' => 'Development',           'desc' => 'Clean, fast code built with SEO and performance in mind.'],
                ['num' => '04', 'icon' => 'bi-bug-fill',            'title' => 'Testing & QA',          'desc' => 'Tested on all devices, browsers and screen sizes.'],
                ['num' => '05', 'icon' => 'bi-rocket-takeoff-fill', 'title' => 'Launch & Support',      'desc' => 'We go live and provide ongoing support & maintenance.'],
            ];
            foreach ($steps as $step): ?>
            <div class="flex flex-col items-center text-center">
                <div class="city-process-icon w-16 h-16 rounded-2xl flex items-center justify-center mb-4 shadow-sm relative">
                    <i class="bi <?= $step['icon'] ?> text-2xl text-white" aria-hidden="true"></i>
                    <span class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-[#EE483D] text-white text-xs font-bold flex items-center justify-center"><?= $step['num'] ?></span>
                </div>
                <h3 class="font-bold text-gray-900 mb-1"><?= $step['title'] ?></h3>
                <p class="text-gray-500 text-sm leading-relaxed"><?= $step['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     DELIVERY EXPECTATIONS — reviews require attributable sources before reuse.
═══════════════════════════════════════════════ -->
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Working Together</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-4 mb-3">
                What Your Project Includes
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ([
                ['icon' => 'bi-file-earmark-text', 'title' => 'A written scope', 'text' => 'We agree pages, functionality, content responsibilities and costs before development. Hosting, subscriptions and ongoing support are listed separately where applicable.'],
                ['icon' => 'bi-chat-square-text', 'title' => 'Visible progress', 'text' => 'Discovery, design reviews and milestone approvals are handled online. Our Gurugram, India team agrees meeting windows with you; service coverage does not imply an office in every city.'],
                ['icon' => 'bi-key', 'title' => 'A practical handover', 'text' => 'Launch checks cover agreed pages and enquiry journeys. Account access, editing guidance, backups and maintenance responsibilities are documented for your project.'],
            ] as $delivery): ?>
            <article class="bg-white rounded-2xl p-7 shadow-sm border border-gray-100 flex flex-col">
                <i class="bi <?= $delivery['icon'] ?> text-[#49499A] text-2xl mb-4" aria-hidden="true"></i>
                <h3 class="font-bold text-gray-900 mb-3"><?= htmlspecialchars($delivery['title']) ?></h3>
                <p class="text-gray-600 text-sm leading-relaxed flex-1"><?= htmlspecialchars($delivery['text']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<!-- ═══════════════════════════════════════════════
     PRICING TEASER
═══════════════════════════════════════════════ -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Pricing</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-4 mb-3">
            Transparent Pricing for <?= htmlspecialchars($c['name']) ?> Businesses
        </h2>
        <p class="text-gray-500 max-w-xl mx-auto">No hidden costs. No surprises. All prices in <?= $c['currency_label'] ?? 'Indian Rupees (INR)' ?>. Choose a plan that fits your goals.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php
        $sym = $c['symbol'] ?? '₹';
        if ($db_location_data !== null) {
            $plans = array_values($db_location_data['pricing_plans']);
        } else {
            $plans = [
                [
                    'name'     => 'Starter',
                    'price'    => $sym . ($c['starter_price'] ?? '18,000'),
                    'label'    => 'For small businesses going online',
                    'popular'  => false,
                    'features' => $c['plan_starter'] ?? ['Up to 5 Pages', 'Mobile Responsive', 'Contact Form', 'Basic SEO Setup', 'Google Maps Integration', '1 Month Free Support'],
                ],
                [
                    'name'     => 'Growth',
                    'price'    => $sym . ($c['growth_price'] ?? '25,000'),
                    'label'    => 'Most popular for growing businesses',
                    'popular'  => true,
                    'features' => $c['plan_growth'] ?? ['Up to 15 Pages', 'Premium UI Design', 'Advanced SEO On-Page', 'WhatsApp Integration', 'Blog / CMS Setup', 'Google Analytics + GSC', '3 Months Free Support'],
                ],
                [
                    'name'     => 'Enterprise',
                    'price'    => 'Custom',
                    'label'    => 'For established brands & portals',
                    'popular'  => false,
                    'features' => $c['plan_enterprise'] ?? ['Unlimited Pages', 'Custom Web Application', 'E-commerce / Booking', 'API Integrations', 'Performance Audit', 'Dedicated Account Manager', '6 Months Free Support'],
                ],
            ];
        }
        foreach ($plans as $plan): ?>
        <div class="relative rounded-2xl border-2 flex flex-col overflow-hidden <?= $plan['popular'] ? 'border-[#EE483D] shadow-xl shadow-red-100' : 'border-gray-200 shadow-sm' ?>">
            <?php if ($plan['popular']): ?>
            <div class="bg-[#EE483D] text-white text-xs font-bold text-center py-2 tracking-widest uppercase">
                ⭐ Most Popular
            </div>
            <?php endif; ?>
            <div class="p-8 flex flex-col flex-1">
                <h3 class="text-lg font-extrabold text-gray-900"><?= $plan['name'] ?></h3>
                <p class="text-gray-400 text-sm mb-4"><?= $plan['label'] ?></p>
                <p class="text-4xl font-extrabold text-[#49499A] mb-1"><?= $plan['price'] ?></p>
                <?php if ($plan['price'] !== 'Custom'): ?>
                <p class="text-gray-400 text-xs mb-6">One-time cost</p>
                <?php else: ?>
                <p class="text-gray-400 text-xs mb-6">Based on requirements</p>
                <?php endif; ?>

                <ul class="space-y-3 mb-8 flex-1">
                    <?php foreach ($plan['features'] as $feat): ?>
                    <li class="flex items-center gap-2 text-sm text-gray-600">
                        <i class="bi bi-check-circle-fill text-green-500 shrink-0" aria-hidden="true"></i>
                        <?= htmlspecialchars($feat) ?>
                    </li>
                    <?php endforeach; ?>
                </ul>

                <a href="https://wa.me/919990548795?text=Hi%2C+I%27m+interested+in+the+<?= urlencode($plan['name']) ?>+web+design+package+for+my+<?= urlencode($c['name']) ?>+business"
                   target="_blank" rel="noopener noreferrer"
                   class="block text-center py-3 rounded-xl font-semibold text-sm transition <?= $plan['popular'] ? 'bg-[#EE483D] text-white hover:bg-red-600' : 'bg-gray-100 text-gray-800 hover:bg-gray-200' ?>">
                    Get Started
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     FAQ SECTION
═══════════════════════════════════════════════ -->
<section class="bg-gray-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">FAQ</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mt-4 mb-3">
                Frequently Asked Questions — <?= htmlspecialchars($c['name']) ?>
            </h2>
        </div>

        <div class="space-y-4" x-data="{open: null}">
            <?php foreach ($c['faq'] as $i => $faq): ?>
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <button
                    @click="open === <?= $i ?> ? open = null : open = <?= $i ?>"
                    :aria-expanded="(open === <?= $i ?>).toString()"
                    class="w-full flex items-center justify-between px-6 py-5 text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#49499A]">
                    <span class="font-semibold text-gray-900 pr-4"><?= htmlspecialchars($faq['q']) ?></span>
                    <i class="bi bi-chevron-down text-[#49499A] shrink-0 transition-transform duration-200"
                       :class="open === <?= $i ?> ? 'rotate-180' : ''"
                       aria-hidden="true"></i>
                </button>
                <div x-show="open === <?= $i ?>" x-collapse class="px-6 pb-5">
                    <p class="text-gray-600 text-sm leading-relaxed"><?= htmlspecialchars($faq['a']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     RELATED CITIES
═══════════════════════════════════════════════ -->
<?php if (count($related) > 1): ?>
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-xs font-bold tracking-widest uppercase text-gray-400 mb-6">
            We Also Serve These Cities
        </p>
        <div class="flex flex-wrap justify-center gap-3">
            <?php foreach ($related as $cname => $cslug):
                if ($cslug === $slug) continue; ?>
            <a href="<?= $base ?>/city/<?= $cslug ?>"
               class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:border-[#49499A] hover:text-[#49499A] text-gray-600 text-sm font-medium px-4 py-2 rounded-full shadow-sm transition">
                <i class="bi bi-geo-alt-fill text-[#EE483D] text-xs" aria-hidden="true"></i>
                <?= htmlspecialchars($cname) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>


<!-- ═══════════════════════════════════════════════
     FINAL CTA BANNER
═══════════════════════════════════════════════ -->
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
            Need a Website, SEO Help, or<br>
            <span class="city-cta-title-highlight">Digital Marketing Strategy?</span>
        </h2>
        <p class="city-cta-desc">
            Chulbul Design has helped businesses across India and internationally grow their online presence. Let us help you next.
        </p>
        <div class="city-cta-stats">
            <div class="city-cta-stat">
                <i class="bi bi-patch-check-fill city-cta-stat-icon-green"></i> Defined Project Scope
            </div>
            <div class="city-cta-stat">
                <i class="bi bi-geo-alt-fill city-cta-stat-icon-blue"></i> Worldwide Remote Delivery
            </div>
            <div class="city-cta-stat">
                <i class="bi bi-check-circle-fill city-cta-stat-icon-yellow"></i> Agreed Review Milestones
            </div>
            <div class="city-cta-stat">
                <i class="bi bi-clock-fill city-cta-stat-icon-pink"></i> Business-Hour Replies
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


<script>
(() => {
    const form = document.getElementById('cityQuoteForm');
    if (!form) return;

    const button = document.getElementById('cityQuoteSubmit');
    const message = document.getElementById('cityQuoteMessage');
    const originalButtonHtml = button.innerHTML;

    const showMessage = (type, text, whatsappUrl = '') => {
        message.className = type === 'success'
            ? 'rounded-xl px-4 py-3 text-sm text-center bg-green-50 text-green-700 border border-green-200'
            : 'rounded-xl px-4 py-3 text-sm text-center bg-red-50 text-red-700 border border-red-200';
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

            showMessage('success', 'Thank you! Your request has been received. We will contact you shortly.', data.whatsapp_url || '');
            form.reset();
        } catch (error) {
            showMessage(
                'error',
                'Your request could not be sent. Please try again or contact us on WhatsApp.',
                'https://wa.me/919990548795'
            );
        } finally {
            button.disabled = false;
            button.classList.remove('opacity-70', 'cursor-not-allowed');
            button.innerHTML = originalButtonHtml;
        }
    });
})();
</script>


<?php require __DIR__ . '/includes/footer.php'; ?>
