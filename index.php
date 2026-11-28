<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/http.php';
if (cbd_is_direct_script_request('index.php')) cbd_redirect_path('/');
require_once __DIR__ . '/includes/seo.php';
require_once __DIR__ . '/includes/industry-catalog.php';

$cbd_page_has_charset = true;
$page_styles = ['/assets/css/home.css'];
$page_robots = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';

// Latest blog posts for homepage section
$_hp_latest_posts = [];
try {
    $_hp_pdo = cbd_database();
    if ($_hp_pdo) {
        $_hp_latest_posts = $_hp_pdo->query(
            "SELECT id, slug, title, excerpt, date, read_time, content
             FROM posts
             WHERE status='published' AND deleted_at IS NULL
             ORDER BY date DESC, created_at DESC
             LIMIT 3"
        )->fetchAll();
    }
    foreach ($_hp_latest_posts as &$_hp_p) {
        $_hp_p['thumb'] = '';
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $_hp_p['content'] ?? '', $_hp_m)) {
            $_hp_p['thumb'] = cbd_asset_url($_hp_m[1]);
        }
    }
    unset($_hp_p);
} catch (Throwable $_hp_e) {
    $_hp_latest_posts = [];
}

$_hp_base = cbd_base_path();
$homeTitle = 'Web Design & Development Company | Chulbul Design';
$homeDescription = 'Looking for a professional web design and development company? We build custom websites, ecommerce platforms and web applications for businesses worldwide.';
$canonicalUrl = 'https://www.chulbuldesign.com/';

$cbd_geo_page = [
    'url' => $canonicalUrl,
    'name' => $homeTitle,
    'description' => $homeDescription,
    'inLanguage' => 'en',
    'about' => [
        'Web Design Company',
        'Web Development Company',
        'Custom Website Design',
        'Ecommerce Development',
        'Custom Web Application Development',
    ],
    'speakable' => ['h1', '.ht-hero-copy', '#faq'],
];

// Single source of truth for BOTH visible FAQ answers and FAQPage JSON-LD
$homeFaqs = [
    [
        'q' => 'Do you work with businesses worldwide?',
        'a' => 'Yes. Chulbul Design is headquartered in Gurugram, India and works remotely with businesses worldwide. Discovery sessions, design reviews, and milestone approvals are coordinated online around your preferred time zone.',
    ],
    [
        'q' => 'What is the difference between web design and web development?',
        'a' => 'Web design covers information architecture, user journeys, visual identity, and responsive layouts. Web development turns approved designs into a working website — including CMS setup, forms, databases, APIs, and third-party integrations. We provide both end-to-end or separately depending on your project brief.',
    ],
    [
        'q' => 'How much does a custom website cost?',
        'a' => 'Pricing depends on page templates, design complexity, CMS or ecommerce requirements, third-party integrations, and ongoing support. After reviewing your brief, we provide a transparent written proposal with scoped deliverables, milestones, and quote currency before work begins.',
    ],
    [
        'q' => 'Which platform or technology stack should we choose?',
        'a' => 'WordPress suits content-led business sites, Shopify and WooCommerce support different ecommerce models, and Laravel, React, Next.js, or Node.js fit custom web applications and portals. We assess editing workflow, scalability, ownership, and budget before recommending a stack.',
    ],
    [
        'q' => 'How long does website design and development take?',
        'a' => 'Project timelines depend on scope, number of templates, content readiness, integrations, and feedback turnaround. We agree a realistic schedule with clear milestones for discovery, design, development, QA testing, and launch in your written proposal.',
    ],
    [
        'q' => 'Can you redesign our existing website and preserve important URLs?',
        'a' => 'Yes. Every website redesign starts with auditing useful content, existing URLs, and enquiry journeys. The agreed migration plan includes 301 redirect mapping, metadata continuity, internal link checks, and pre-launch testing. Search performance can fluctuate during any migration, so specific rankings cannot be guaranteed.',
    ],
    [
        'q' => 'Will our website be mobile-friendly and prepared for SEO?',
        'a' => 'Yes. We build responsive layouts with semantic HTML, logical heading structure, clean metadata, structured JSON-LD schema, and pre-launch speed checks. Search rankings also depend on competition, ongoing content, and off-page signals, so no build can guarantee a specific search position.',
    ],
    [
        'q' => 'Can our team manage the website and own the code after launch?',
        'a' => 'Yes. Clients receive full ownership of their agreed deliverables, source code, design files, and CMS access. Handover documentation, hosting ownership, backups, and any optional ongoing maintenance are clearly defined during project scoping.',
    ],
];

$showreelBuilds = [
    [
        'name' => 'SSF Global',
        'tag' => 'Next.js',
        'url' => 'ssfglobal.org',
        'chipTopTitle' => 'Next.js Platform',
        'chipTopSub' => 'Bilingual EN / FR Architecture',
        'chipBotTitle' => 'Full-Stack Build',
        'chipBotSub' => '26 Centers Directory · Fast SSR',
        'desktop' => '/assets/images/portfolio-work/hero-ssf-d.webp',
        'mobile' => '/assets/images/portfolio-work/hero-ssf-m.webp',
        'alt' => 'SSF Global bilingual Next.js website built by Chulbul Design',
    ],
    [
        'name' => 'Brunswick Fur Food',
        'tag' => 'Shopify',
        'url' => 'brunswickfurfood.com.au',
        'chipTopTitle' => 'Shopify Online Store 2.0',
        'chipTopSub' => 'Custom Subscription UX',
        'chipBotTitle' => 'Ecommerce Storefront',
        'chipBotSub' => 'Mobile-First Product Discovery',
        'desktop' => '/assets/images/portfolio-work/hero-brunswick-d.webp',
        'mobile' => '/assets/images/portfolio-work/hero-brunswick-m.webp',
        'alt' => 'Brunswick Fur Food custom Shopify ecommerce storefront design',
    ],
    [
        'name' => 'SandeshSetu',
        'tag' => 'SaaS',
        'url' => 'sandeshsetu.com',
        'chipTopTitle' => 'SaaS Web Application',
        'chipTopSub' => 'Automation & Shared Inbox',
        'chipBotTitle' => 'Product UI System',
        'chipBotSub' => 'Interactive Dashboard Experience',
        'desktop' => '/assets/images/portfolio-work/hero-sandeshsetu-d.webp',
        'mobile' => '/assets/images/portfolio-work/hero-sandeshsetu-m.webp',
        'alt' => 'SandeshSetu SaaS product website and dashboard interface',
    ],
];

$marqueeLinks = [
    ['Web Design', '/web-design'],
    ['Web Development', '/web-development'],
    ['WordPress Development', '/wordpress-development'],
    ['Ecommerce Development', '/ecommerce-development'],
    ['App & Software', '/mobile-software-development'],
    ['UI/UX Design', '/ui-ux-design'],
    ['SEO & Digital Marketing', '/seo-digital-marketing'],
    ['AI & Automation', '/ai-automation'],
];

$technologies = [
    ['name' => 'WordPress',     'cat' => 'cms',   'catLabel' => 'CMS',        'svg' => 'wordpress.svg', 'icon' => '',                        'use' => 'Custom themes & editorial CMS',       'link' => '/wordpress-development'],
    ['name' => 'Shopify',       'cat' => 'ecom',  'catLabel' => 'Commerce',   'svg' => 'shopify.svg',   'icon' => '',                        'use' => 'Online Store 2.0 & subscriptions',    'link' => '/shopify-development'],
    ['name' => 'React',         'cat' => 'apps',  'catLabel' => 'Frontend',   'svg' => 'react.svg',     'icon' => '',                        'use' => 'Interactive web apps & UIs',          'link' => '/reactjs-development'],
    ['name' => 'Next.js',       'cat' => 'apps',  'catLabel' => 'Full-Stack', 'svg' => '',              'icon' => 'bi-lightning-charge-fill','use' => 'Fast SSR & SEO-ready frontends',      'link' => '/nextjs-development'],
    ['name' => 'Node.js',       'cat' => 'apps',  'catLabel' => 'Backend',    'svg' => 'nodejs.svg',    'icon' => '',                        'use' => 'Scalable APIs & real-time backends',  'link' => '/nodejs-development'],
    ['name' => 'Laravel',       'cat' => 'apps',  'catLabel' => 'Backend',    'svg' => '',              'icon' => 'bi-layers-fill',          'use' => 'Custom portals, SaaS & ERP',          'link' => '/laravel-development'],
    ['name' => 'PHP',           'cat' => 'apps',  'catLabel' => 'Backend',    'svg' => 'php.svg',       'icon' => '',                        'use' => 'Lean server-side web platforms',      'link' => '/php-development'],
    ['name' => 'WooCommerce',   'cat' => 'ecom',  'catLabel' => 'Commerce',   'svg' => '',              'icon' => 'bi-bag-check-fill',       'use' => 'Flexible WordPress commerce',         'link' => '/woocommerce-development'],
    ['name' => 'Magento',       'cat' => 'ecom',  'catLabel' => 'Commerce',   'svg' => 'magento.svg',   'icon' => '',                        'use' => 'Complex B2B & multi-store catalogs',  'link' => '/magento-development'],
    ['name' => 'Android & iOS', 'cat' => 'apps',  'catLabel' => 'Mobile',     'svg' => 'android.svg',   'icon' => '',                        'use' => 'Native & cross-platform mobile apps', 'link' => '/mobile-software-development'],
];

$clientLogos = [
    ['type' => 'img',  'file' => 'client-logo-1.webp',        'name' => 'Brunswick Fur Food',    'theme' => 'dark'],
    ['type' => 'img',  'file' => 'client-logo-2.webp',        'name' => 'Enviro Guru',           'theme' => 'dark'],
    ['type' => 'img',  'file' => 'ssf-global.webp',           'name' => 'SSF Global',            'theme' => 'light'],
    ['type' => 'img',  'file' => 'client-logo-3.webp',        'name' => 'Busy Bee',              'theme' => 'dark'],
    ['type' => 'img',  'file' => 'client-logo-4.webp',        'name' => 'The Beer Cafe',         'theme' => 'light'],
    ['type' => 'img',  'file' => 'client-logo-5.webp',        'name' => 'Artech',                'theme' => 'dark'],
    ['type' => 'img',  'file' => 'client-logo-6.webp',        'name' => 'Bummlers',              'theme' => 'light'],
    ['type' => 'text', 'file' => '',                          'name' => 'Umang Aatray',          'theme' => 'dark'],
    ['type' => 'img',  'file' => 'client-logo-7.webp',        'name' => 'Gaam Gurug',            'theme' => 'light'],
    ['type' => 'img',  'file' => 'ayla-solutions.webp',       'name' => 'Ayla Solutions',        'theme' => 'light'],
    ['type' => 'img',  'file' => 'client-logo-8.webp',        'name' => 'Swizzle',               'theme' => 'dark'],
    ['type' => 'text', 'file' => '',                          'name' => 'Tuitionly',             'theme' => 'dark'],
    ['type' => 'img',  'file' => 'client-logo-9.webp',        'name' => 'Partner Client Logo',   'theme' => 'light'],
    ['type' => 'img',  'file' => 'gurjar-pragati-manch.webp', 'name' => 'Gurjar Pragati Manch',  'theme' => 'dark'],
    ['type' => 'img',  'file' => 'kisaanjee.webp',            'name' => 'Kisaan Jee',            'theme' => 'light'],
    ['type' => 'img',  'file' => 'kuch-bhi-dalu.webp',        'name' => 'Kuch Bhi Dalu',         'theme' => 'light'],
    ['type' => 'img',  'file' => 'tera-ghar.webp',            'name' => 'Tera Ghar',             'theme' => 'dark'],
];

$whyCards = [
    ['num' => '01', 'title' => 'Written Scope Before Code', 'desc' => 'Every engagement starts with a documented sitemap, feature list, milestone schedule, and clear quote so both teams align before development begins.'],
    ['num' => '02', 'title' => 'Lean Custom Builds — No Bloat', 'desc' => 'We engineer maintainable websites and custom CMS themes built for speed and accessibility instead of stacking heavy page-builders and unnecessary plugins.'],
    ['num' => '03', 'title' => '100% Client Ownership', 'desc' => 'You retain full control of your domain, hosting, repository, Figma files, and store accounts — delivered with clean handover documentation.'],
    ['num' => '04', 'title' => 'Technical SEO Built In', 'desc' => 'Semantic HTML, logical heading hierarchy, structured JSON-LD schema, responsive image sizing, and 301 redirect planning are part of the core build.'],
    ['num' => '05', 'title' => 'Time-Zone Aligned Delivery', 'desc' => 'Established in 2013 in Gurugram, India, our team works with international businesses through scheduled review calls and shared staging previews.'],
    ['num' => '06', 'title' => 'Extended Tech Capacity', 'desc' => 'When complex projects need broader engineering depth, we collaborate seamlessly with our strategic technology partner SoftLes under one delivery standard.'],
];

// Verified Google Business Profile Reviews (4.8★ across 19 Google reviews)
$googleReviewsUrl = 'https://share.google/rXWXIX2eMw1ulfGgt';
$googleReviews = [
    [
        'name' => 'Marcie Lewis',
        'initial' => 'M',
        'source' => 'Google Review',
        'text' => 'Excellent experience with Chulbul Design! The team is creative, professional, and easy to communicate with. They delivered exactly what we needed with a clean and modern design. Really happy with the quality of work and timely delivery. Highly recommended for web design and development services!',
    ],
    [
        'name' => 'Akshay Kumar',
        'initial' => 'A',
        'source' => 'Google Review',
        'text' => 'Exceptional web design and development by Chulbul Design! They built our luxury art gallery and e-commerce website with stunning UI/UX, fast speed, and seamless mobile responsiveness. Highly recommend Chulbul Design for custom website design and development services!',
    ],
    [
        'name' => 'Virender Chadhary',
        'initial' => 'V',
        'source' => 'Google Review',
        'text' => 'We at News Agency Service were very impressed with all aspects of our new website. We would highly recommend Chulbul Design to anyone looking to build a new website.',
    ],
    [
        'name' => 'Parvez Panwar (LKB News UP)',
        'initial' => 'P',
        'source' => 'Google Review',
        'text' => 'Great team, doing great job... I will recommend everyone to go to them for their web development projects.',
    ],
];

$processSteps = [
    ['num' => '01', 'title' => 'Discovery & Strategy', 'desc' => 'Audit goals, audience journeys, competitors, and technical requirements to define the right platform and scope.', 'output' => 'Deliverable: Written Scope & Sitemap'],
    ['num' => '02', 'title' => 'Design & Prototype',   'desc' => 'Wireframes and responsive Figma visual systems reviewed with realistic content before engineering begins.', 'output' => 'Deliverable: Approved UI System'],
    ['num' => '03', 'title' => 'Development & QA',     'desc' => 'Clean frontend and backend engineering tested across devices, browsers, forms, speed, and technical SEO.', 'output' => 'Deliverable: Staging Preview & QA'],
    ['num' => '04', 'title' => 'Launch & Support',     'desc' => 'Controlled deployment, CMS editing handover, full code ownership, and optional ongoing monthly care.', 'output' => 'Deliverable: Live Launch & Handover'],
];

$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($homeTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="description" content="<?= htmlspecialchars($homeDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($homeTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($homeDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image" content="https://www.chulbuldesign.com/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Chulbul Design — global web design and development company showcase">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@chulbuldesign">
    <meta name="twitter:title" content="<?= htmlspecialchars($homeTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($homeDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="https://www.chulbuldesign.com/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg">
    <meta name="twitter:image:alt" content="Chulbul Design — global web design and development company showcase">
    <meta name="google-site-verification" content="66Yl1e0VD33TbRBSGdMcGfRYaTUguH0LyUovEsgh590">

    <!-- Production Homepage Typography: Denk One (display), Inter (body), JetBrains Mono (labels) — non-blocking -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Denk+One&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Denk+One&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Denk+One&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet"></noscript>

    <!-- Structured Data: ProfessionalService + WebSite + FAQPage (WebPage injected by header.php) -->
    <script type="application/ld+json"><?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        '@id' => 'https://www.chulbuldesign.com/#organization',
        'name' => 'Chulbul Design',
        'url' => 'https://www.chulbuldesign.com/',
        'logo' => 'https://www.chulbuldesign.com/assets/images/logo/chulbuldesign.svg',
        'email' => 'info@chulbuldesign.com',
        'telephone' => '+919990548795',
        'foundingDate' => '2013',
        'areaServed' => 'Worldwide',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Sector 15, Flat No. 1277',
            'addressLocality' => 'Gurugram',
            'addressRegion' => 'Haryana',
            'postalCode' => '122001',
            'addressCountry' => 'IN',
        ],
        'description' => $homeDescription,
        'knowsAbout' => [
            'Custom Web Design',
            'Custom Web Development',
            'Shopify Development',
            'WordPress Development',
            'WooCommerce Development',
            'Custom Web Application Development',
            'Technical SEO',
        ],
        'sameAs' => [
            'https://www.facebook.com/chulbuldesign',
            'https://www.instagram.com/chulbuldesign',
            'https://twitter.com/ChulbulDesign',
            'https://www.linkedin.com/company/chulbuldesign/',
            'https://techbehemoths.com/company/chulbul-design',
        ],
    ], $jsonFlags) ?></script>
    <script type="application/ld+json"><?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => 'https://www.chulbuldesign.com/#website',
        'url' => 'https://www.chulbuldesign.com/',
        'name' => 'Chulbul Design',
        'alternateName' => 'ChulbulDesign',
        'publisher' => ['@id' => 'https://www.chulbuldesign.com/#organization'],
        'inLanguage' => 'en',
    ], $jsonFlags) ?></script>
    <script type="application/ld+json"><?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(static fn(array $faq): array => [
            '@type' => 'Question',
            'name' => $faq['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
        ], $homeFaqs),
    ], $jsonFlags) ?></script>
    <?php $cbd_skip_global_org_schema = true; require __DIR__ . '/includes/header.php'; ?>

<div class="ht-scroll-progress" data-ht-progress aria-hidden="true"></div>

<div class="ht-page">

    <!-- ============================================================
         1. FULL-SCREEN BOLD AURORA HERO + 3D SHOWREEL STAGE
    ============================================================ -->
    <section class="ht-hero" aria-labelledby="home-hero-title">
        <div class="ht-mesh-layer" aria-hidden="true">
            <div class="ht-mesh-orb ht-orb-1"></div>
            <div class="ht-mesh-orb ht-orb-2"></div>
            <div class="ht-mesh-orb ht-orb-3"></div>
        </div>
        <div class="ht-hero-grid" aria-hidden="true"></div>

        <div class="ht-wrap">
            <div class="ht-hero-center">
                <div class="ht-hero-chip">
                    <span class="ht-live-dot" aria-hidden="true"></span>
                    Global Web Design &amp; Engineering Studio · Since 2013
                </div>

                <h1 id="home-hero-title" class="ht-hero-h1">
                    <span class="ht-h1-line1">Web Design &amp; Development Company</span>
                    <span class="ht-h1-line2">for Businesses Worldwide</span>
                </h1>

                <p class="ht-hero-copy">
                    Chulbul Design is a web design and development company helping businesses worldwide build powerful digital experiences. From custom website design and development to ecommerce platforms and web applications, we create scalable solutions tailored to your business goals.
                </p>

                <div class="ht-hero-actions">
                    <a href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%20would%20like%20to%20discuss%20a%20web%20design%20or%20development%20project."
                       target="_blank" rel="noopener noreferrer" class="ht-btn ht-btn-vibrant">
                        <i class="bi bi-whatsapp" aria-hidden="true"></i> Discuss Your Project
                    </a>
                    <a href="#portfolio" class="ht-btn ht-btn-glass-light">
                        Explore Live Work <i class="bi bi-arrow-down-right" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="ht-hero-trust" aria-label="Verified agency commitments">
                    <span><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Established 2013 · Gurugram HQ</span>
                    <span><i class="bi bi-check-circle-fill" aria-hidden="true"></i> 100% Client-Owned Code</span>
                    <span><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Written Fixed-Scope Proposals</span>
                </div>
            </div>

            <!-- DRAMATIC FULL-WIDTH 3D LAYERED SHOWREEL STAGE -->
            <div class="ht-stage-outer" data-ht-showreel>
                <div class="ht-stage-controls" role="tablist" aria-label="Switch featured live project">
                    <?php foreach ($showreelBuilds as $idx => $build): ?>
                    <button type="button"
                            class="ht-stage-pill<?= $idx === 0 ? ' is-active' : '' ?>"
                            role="tab"
                            aria-selected="<?= $idx === 0 ? 'true' : 'false' ?>"
                            data-reel-idx="<?= $idx ?>">
                        <?= htmlspecialchars($build['name'], ENT_QUOTES, 'UTF-8') ?>
                        <small><?= htmlspecialchars($build['tag'], ENT_QUOTES, 'UTF-8') ?></small>
                    </button>
                    <?php endforeach; ?>
                </div>

                <div class="ht-showreel-arena" data-ht-arena>
                    <!-- Left Tilted 3D Wing -->
                    <div class="ht-reel-wing ht-reel-wing-left" data-reel-wing-left role="button" tabindex="0" aria-label="Preview previous live build">
                        <img src="<?= htmlspecialchars($base . $showreelBuilds[1]['desktop'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars($showreelBuilds[1]['alt'], ENT_QUOTES, 'UTF-8') ?>"
                             width="640" height="440" loading="lazy" data-wing-left-img>
                        <div class="ht-reel-wing-label">
                            <strong data-wing-left-name><?= htmlspecialchars($showreelBuilds[1]['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <span data-wing-left-tag><?= htmlspecialchars($showreelBuilds[1]['tag'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>

                    <!-- Center Elevated 3D Glass Stage -->
                    <div class="ht-reel-center">
                        <!-- Floating Glass Card Top-Left -->
                        <div class="ht-float-chip ht-chip-tl" aria-hidden="true">
                            <div class="ht-chip-icon"><i class="bi bi-layers-fill"></i></div>
                            <div>
                                <strong data-chip-top-title><?= htmlspecialchars($showreelBuilds[0]['chipTopTitle'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <small data-chip-top-sub><?= htmlspecialchars($showreelBuilds[0]['chipTopSub'], ENT_QUOTES, 'UTF-8') ?></small>
                            </div>
                        </div>

                        <!-- Floating Glass Card Bottom-Left -->
                        <div class="ht-float-chip ht-chip-bl" aria-hidden="true">
                            <div class="ht-chip-icon" style="background:linear-gradient(135deg,#059669,#06b6d4);"><i class="bi bi-lightning-charge-fill"></i></div>
                            <div>
                                <strong data-chip-bot-title><?= htmlspecialchars($showreelBuilds[0]['chipBotTitle'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <small data-chip-bot-sub><?= htmlspecialchars($showreelBuilds[0]['chipBotSub'], ENT_QUOTES, 'UTF-8') ?></small>
                            </div>
                        </div>

                        <!-- Floating 3D Mobile Frame Right -->
                        <div class="ht-chip-phone" aria-hidden="true">
                            <img src="<?= htmlspecialchars($base . $showreelBuilds[0]['mobile'], ENT_QUOTES, 'UTF-8') ?>"
                                 alt="" width="240" height="420" loading="lazy" decoding="async" data-reel-mobile>
                        </div>

                        <div class="ht-reel-browser">
                            <div class="ht-reel-bar">
                                <span class="ht-dots" aria-hidden="true"><i></i><i></i><i></i></span>
                                <span class="ht-bar-url" data-reel-url><?= htmlspecialchars($showreelBuilds[0]['url'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span style="width:40px;" aria-hidden="true"></span>
                            </div>
                            <div class="ht-reel-screen">
                                <img src="<?= htmlspecialchars($base . $showreelBuilds[0]['desktop'], ENT_QUOTES, 'UTF-8') ?>"
                                     alt="<?= htmlspecialchars($showreelBuilds[0]['alt'], ENT_QUOTES, 'UTF-8') ?>"
                                     class="ht-reel-main-img"
                                     data-reel-main
                                     width="960" height="600" fetchpriority="high" decoding="async">
                            </div>
                        </div>
                    </div>

                    <!-- Right Tilted 3D Wing -->
                    <div class="ht-reel-wing ht-reel-wing-right" data-reel-wing-right role="button" tabindex="0" aria-label="Preview next live build">
                        <img src="<?= htmlspecialchars($base . $showreelBuilds[2]['desktop'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars($showreelBuilds[2]['alt'], ENT_QUOTES, 'UTF-8') ?>"
                             width="640" height="440" loading="lazy" data-wing-right-img>
                        <div class="ht-reel-wing-label">
                            <strong data-wing-right-name><?= htmlspecialchars($showreelBuilds[2]['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <span data-wing-right-tag><?= htmlspecialchars($showreelBuilds[2]['tag'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         KINETIC SERVICE MARQUEE
    ============================================================ -->
    <nav class="ht-marquee-band" aria-label="Primary web design and development services">
        <div class="ht-marquee-track">
            <?php for ($copy = 0; $copy < 2; $copy++): ?>
            <div class="ht-marquee-group" <?= $copy === 1 ? 'aria-hidden="true"' : '' ?>>
                <?php foreach ($marqueeLinks as [$label, $slug]): ?>
                <a href="<?= $base . htmlspecialchars($slug, ENT_QUOTES, 'UTF-8') ?>" class="ht-marquee-link" <?= $copy === 1 ? 'tabindex="-1"' : '' ?>>
                    <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                </a>
                <span class="ht-marquee-dot" aria-hidden="true">✦</span>
                <?php endforeach; ?>
            </div>
            <?php endfor; ?>
        </div>
    </nav>

    <!-- ============================================================
         H2 #1: VIBRANT 3D BENTO SERVICES
    ============================================================ -->
    <section class="ht-sec" id="services" aria-labelledby="services-heading">
        <div class="ht-wrap">
            <div class="ht-head ht-reveal">
                <span class="ht-pill-kicker"><i class="bi bi-grid-1x2-fill" aria-hidden="true"></i> Core Services</span>
                <h2 id="services-heading" class="ht-h2">Website Design &amp; <span class="ht-grad-text">Development Services</span></h2>
                <p class="ht-lead">
                    Modular digital capabilities built to work in sync — from high-craft custom interfaces and full-stack web engineering to dedicated ecommerce and technical SEO.
                </p>
            </div>

            <div class="ht-bento">
                <!-- Flagship 01: Web Design -->
                <a href="<?= $base ?>/web-design" class="ht-bento-card ht-bento-flag-light ht-span-6 ht-reveal">
                    <div class="ht-bento-top">
                        <div class="ht-icon-3d ht-g-coral" aria-hidden="true"><i class="bi bi-palette2"></i></div>
                        <span class="ht-bento-tag">01 · DESIGN SYSTEMS</span>
                    </div>
                    <h3>Web Design</h3>
                    <p>Original, mobile-first custom website design shaped around your customers, brand clarity, and conversion journeys — never forced into generic templates.</p>
                    <div class="ht-ui-canvas" aria-hidden="true">
                        <div class="ht-ui-frame">
                            <div class="ht-ui-bar-grad"></div>
                            <div class="ht-ui-bar-sub"></div>
                            <div class="ht-ui-tiles"><span></span><span></span><span></span></div>
                        </div>
                        <div class="ht-ui-tokens">
                            <div class="ht-swatches">
                                <i style="background:#0a0d29"></i>
                                <i style="background:#4f46e5"></i>
                                <i style="background:#EE483D"></i>
                            </div>
                            <span style="font-family:var(--ht-mono);font-size:10px;color:#6c7693;">FIGMA UI · RESPONSIVE</span>
                        </div>
                    </div>
                    <span class="ht-bento-link">Explore custom web design <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                </a>

                <!-- Flagship 02: Web Development (Glowing Dark + Circuit Pattern + Spotlight) -->
                <a href="<?= $base ?>/web-development" class="ht-bento-card ht-bento-flag-dark ht-spotlight ht-span-6 ht-reveal" data-ht-spotlight>
                    <div class="ht-dark-pat-circuit" aria-hidden="true"></div>
                    <div class="ht-bento-top">
                        <div class="ht-icon-3d ht-g-indigo" aria-hidden="true"><i class="bi bi-code-slash"></i></div>
                        <span class="ht-bento-tag">02 · FULL-STACK</span>
                    </div>
                    <h3>Web Development</h3>
                    <p>Fast, scalable business websites, client portals, custom APIs, and full-stack web applications engineered for speed, security, and 100% client ownership.</p>
                    <div class="ht-code-glass" aria-hidden="true">
                        <div><span style="color:#ff9c8a;">const</span> build = {</div>
                        <div>&nbsp;&nbsp;<span style="color:#93c5fd;">stack</span>: [<span style="color:#6ee7b7;">'Next.js'</span>, <span style="color:#6ee7b7;">'Laravel'</span>, <span style="color:#6ee7b7;">'Node.js'</span>],</div>
                        <div>&nbsp;&nbsp;<span style="color:#93c5fd;">ownership</span>: <span style="color:#6ee7b7;">'100% client repository'</span>,</div>
                        <div>&nbsp;&nbsp;<span style="color:#93c5fd;">coreWebVitals</span>: <span style="color:#fde047;">'optimized'</span></div>
                        <div>};</div>
                    </div>
                    <span class="ht-bento-link">Explore web development services <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                </a>

                <!-- Row 2 -->
                <a href="<?= $base ?>/wordpress-development" class="ht-bento-card ht-span-4 ht-reveal">
                    <div class="ht-bento-top">
                        <div class="ht-icon-3d ht-g-sky" aria-hidden="true"><i class="bi bi-wordpress"></i></div>
                        <span class="ht-bento-tag">03</span>
                    </div>
                    <h3>WordPress Development</h3>
                    <p>Lightweight custom WordPress themes, clean block editing, plugin consolidation, and secure publishing platforms your team can run effortlessly.</p>
                    <span class="ht-bento-link">Explore WordPress development <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                </a>

                <a href="<?= $base ?>/ecommerce-development" class="ht-bento-card ht-span-4 ht-reveal">
                    <div class="ht-bento-top">
                        <div class="ht-icon-3d ht-g-emerald" aria-hidden="true"><i class="bi bi-cart4"></i></div>
                        <span class="ht-bento-tag">04</span>
                    </div>
                    <h3>Ecommerce Development</h3>
                    <p>Dedicated Shopify, WooCommerce, Magento, and multi-vendor storefront engineering tailored for smooth discovery, checkout, and repeat orders.</p>
                    <span class="ht-bento-link">Explore ecommerce development <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                </a>

                <a href="<?= $base ?>/mobile-software-development" class="ht-bento-card ht-span-4 ht-reveal">
                    <div class="ht-bento-top">
                        <div class="ht-icon-3d ht-g-orange" aria-hidden="true"><i class="bi bi-phone"></i></div>
                        <span class="ht-bento-tag">05</span>
                    </div>
                    <h3>App &amp; Software Development</h3>
                    <p>Android and iOS applications, SaaS products, CRM/ERP tools, and custom operational software shaped around real business workflows.</p>
                    <span class="ht-bento-link">Explore app &amp; software development <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                </a>

                <!-- Row 3 -->
                <a href="<?= $base ?>/ui-ux-design" class="ht-bento-card ht-span-4 ht-reveal">
                    <div class="ht-bento-top">
                        <div class="ht-icon-3d ht-g-violet" aria-hidden="true"><i class="bi bi-vector-pen"></i></div>
                        <span class="ht-bento-tag">06</span>
                    </div>
                    <h3>UI/UX Design</h3>
                    <p>Figma design systems, interactive prototypes, SaaS dashboards, and accessible interfaces documented for clean developer handoff.</p>
                    <span class="ht-bento-link">Explore UI/UX design services <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                </a>

                <a href="<?= $base ?>/seo-digital-marketing" class="ht-bento-card ht-span-4 ht-reveal">
                    <div class="ht-bento-top">
                        <div class="ht-icon-3d ht-g-rose" aria-hidden="true"><i class="bi bi-graph-up-arrow"></i></div>
                        <span class="ht-bento-tag">07</span>
                    </div>
                    <h3>SEO &amp; Digital Marketing</h3>
                    <p>Technical SEO, Core Web Vitals optimization, content strategy, and paid acquisition aligned around qualified business enquiries.</p>
                    <span class="ht-bento-link">Explore SEO &amp; digital marketing <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                </a>

                <a href="<?= $base ?>/ai-automation" class="ht-bento-card ht-span-4 ht-reveal">
                    <div class="ht-bento-top">
                        <div class="ht-icon-3d ht-g-cyan" aria-hidden="true"><i class="bi bi-robot"></i></div>
                        <span class="ht-bento-tag">08</span>
                    </div>
                    <h3>AI &amp; Automation</h3>
                    <p>Practical AI chatbots, WhatsApp Business automation, and CRM workflow integrations designed to remove manual bottlenecks.</p>
                    <span class="ht-bento-link">Explore AI &amp; automation services <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                </a>
            </div>
        </div>
    </section>

    <!-- ============================================================
         H2 #2: CUSTOM SOLUTIONS + GLOWING 3D BLUEPRINT STACK
    ============================================================ -->
    <section class="ht-sec ht-sec-mesh-light" aria-labelledby="solutions-heading">
        <div class="ht-wrap">
            <div class="ht-solutions-grid">
                <div class="ht-reveal">
                    <span class="ht-pill-kicker"><i class="bi bi-stars" aria-hidden="true"></i> Tailored Architecture</span>
                    <h2 id="solutions-heading" class="ht-h2">Custom Web Development Solutions for <span class="ht-grad-text">Growing Businesses</span></h2>

                    <div class="ht-prose">
                        <p>
                            A high-performing website aligns strategy, usability, and clean engineering around the customers evaluating your business. Our <a href="<?= $base ?>/web-design">custom web design services</a> clarify hierarchy, responsive journeys, and enquiry paths before engineering starts.
                        </p>
                        <p>
                            Our <a href="<?= $base ?>/web-development">custom web development services</a> turn approved designs into fast business websites, client portals, and scalable applications. Need easy publishing? Explore <a href="<?= $base ?>/wordpress-development">WordPress development</a>. Upgrading an existing site? Our <a href="<?= $base ?>/website-redesign">website redesign workflow</a> maps 301 redirects and metadata to protect search continuity.
                        </p>
                    </div>

                    <div class="ht-contrast-row" aria-label="Generic template versus custom build comparison">
                        <div class="ht-contrast-card">
                            <small style="color:#9ca3af;">Generic Template</small>
                            <ul>
                                <li><i class="bi bi-x-circle text-red-400" aria-hidden="true"></i> Heavy builder &amp; plugin bloat</li>
                                <li><i class="bi bi-x-circle text-red-400" aria-hidden="true"></i> Look-alike layouts</li>
                                <li><i class="bi bi-x-circle text-red-400" aria-hidden="true"></i> Fragile mobile experience</li>
                            </ul>
                        </div>
                        <div class="ht-contrast-card is-custom">
                            <small style="color:#4f46e5;">Chulbul Custom Build</small>
                            <ul>
                                <li><i class="bi bi-check-circle-fill text-emerald-500" aria-hidden="true"></i> Clean semantic code &amp; schema</li>
                                <li><i class="bi bi-check-circle-fill text-emerald-500" aria-hidden="true"></i> Tailored brand UI &amp; UX</li>
                                <li><i class="bi bi-check-circle-fill text-emerald-500" aria-hidden="true"></i> 100% client-owned repo</li>
                            </ul>
                        </div>
                    </div>

                    <div style="display:flex;flex-wrap:wrap;gap:12px;margin-top:26px;">
                        <a href="<?= $base ?>/contact-us" class="ht-btn ht-btn-ink">
                            Request a Written Scope <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                        <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer" class="ht-btn ht-btn-outline">
                            <i class="bi bi-whatsapp" aria-hidden="true"></i> WhatsApp Our Team
                        </a>
                    </div>
                </div>

                <!-- Glowing 3D Blueprint Selector -->
                <div class="ht-bp-stack ht-reveal">
                    <div class="ht-bp-glow" aria-hidden="true"></div>
                    <div class="ht-blueprint ht-spotlight" data-ht-blueprint data-ht-spotlight>
                        <div class="ht-dark-pat-iso" aria-hidden="true"></div>
                        <div class="ht-bp-tabs" role="tablist" aria-label="Select project type to inspect deliverables">
                            <button type="button" class="ht-bp-tab is-active" role="tab" aria-selected="true" data-bp-target="website">Business Website</button>
                            <button type="button" class="ht-bp-tab" role="tab" aria-selected="false" data-bp-target="webapp">Web App / Portal</button>
                            <button type="button" class="ht-bp-tab" role="tab" aria-selected="false" data-bp-target="redesign">SEO-Safe Redesign</button>
                        </div>

                        <div class="ht-bp-panel is-active" data-bp-panel="website">
                            <h3>Conversion-Ready Business Website</h3>
                            <p>Designed from scratch for service firms, B2B organizations, and growing companies that need clarity, speed, and easy CMS editing.</p>
                            <ul class="ht-bp-list">
                                <li><i class="bi bi-check2" aria-hidden="true"></i> Custom sitemap &amp; responsive Figma UI</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> Lean custom WordPress or headless build</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> Clean JSON-LD schema &amp; on-page SEO</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> GA4, Clarity &amp; enquiry form tracking</li>
                            </ul>
                            <div class="ht-bp-foot">
                                <span>OWNERSHIP: 100% CLIENT-OWNED</span>
                                <a href="<?= $base ?>/web-design">View Web Design <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        </div>

                        <div class="ht-bp-panel" data-bp-panel="webapp">
                            <h3>Custom Web Application &amp; Client Portal</h3>
                            <p>Engineered for teams that have outgrown spreadsheets and generic plugins — built with clean APIs, authentication, and structured data.</p>
                            <ul class="ht-bp-list">
                                <li><i class="bi bi-check2" aria-hidden="true"></i> Role-based access &amp; customer dashboards</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> React, Next.js, Laravel, or Node.js stack</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> CRM, payment &amp; third-party API workflows</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> Documented Git repository &amp; QA staging</li>
                            </ul>
                            <div class="ht-bp-foot">
                                <span>OWNERSHIP: FULL REPO &amp; IP HANDOVER</span>
                                <a href="<?= $base ?>/web-development">View Web Development <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        </div>

                        <div class="ht-bp-panel" data-bp-panel="redesign">
                            <h3>Controlled Website Redesign &amp; Migration</h3>
                            <p>Modernize outdated design, mobile usability, and page speed without throwing away the search visibility and URLs you already earned.</p>
                            <ul class="ht-bp-list">
                                <li><i class="bi bi-check2" aria-hidden="true"></i> Full URL inventory &amp; content audit first</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> Complete 301 redirect &amp; metadata mapping</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> Core Web Vitals &amp; mobile UX overhaul</li>
                                <li><i class="bi bi-check2" aria-hidden="true"></i> Staged launch checklist &amp; post-launch QA</li>
                            </ul>
                            <div class="ht-bp-foot">
                                <span>FOCUS: UX + SEARCH CONTINUITY</span>
                                <a href="<?= $base ?>/website-redesign">View Website Redesign <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         H2 #3: FLOATING 3D TECH DOCK WITH INTERACTIVE FILTER
    ============================================================ -->
    <section class="ht-sec" aria-labelledby="technologies-heading">
        <div class="ht-wrap">
            <div class="ht-head ht-reveal">
                <span class="ht-pill-kicker"><i class="bi bi-cpu-fill" aria-hidden="true"></i> Modern Tech Stack</span>
                <h2 id="technologies-heading" class="ht-h2">Technologies We Use for <span class="ht-grad-text">Web Development</span></h2>
                <p class="ht-lead">Proven, maintainable frameworks and platforms selected around your editing workflow, integrations, and long-term scale.</p>
                <div class="ht-filter-bar" role="tablist" aria-label="Filter technologies by category" data-ht-tech-filter>
                    <button type="button" class="ht-filter-btn is-active" role="tab" aria-selected="true" data-cat="all">All Stack</button>
                    <button type="button" class="ht-filter-btn" role="tab" aria-selected="false" data-cat="apps">Apps &amp; Frameworks</button>
                    <button type="button" class="ht-filter-btn" role="tab" aria-selected="false" data-cat="cms">CMS</button>
                    <button type="button" class="ht-filter-btn" role="tab" aria-selected="false" data-cat="ecom">Ecommerce</button>
                </div>
            </div>

            <div class="ht-tech-grid ht-reveal" data-ht-tech-grid>
                <?php foreach ($technologies as $tech): ?>
                <a href="<?= $base . htmlspecialchars($tech['link'], ENT_QUOTES, 'UTF-8') ?>"
                   class="ht-tech-tile"
                   data-tech-cat="<?= htmlspecialchars($tech['cat'], ENT_QUOTES, 'UTF-8') ?>">
                    <div class="ht-tech-head">
                        <div class="ht-tech-badge" aria-hidden="true">
                            <?php if ($tech['svg'] !== ''): ?>
                            <img src="<?= $base ?>/assets/images/tech/<?= htmlspecialchars($tech['svg'], ENT_QUOTES, 'UTF-8') ?>" alt="" width="24" height="24" loading="lazy">
                            <?php else: ?>
                            <i class="bi <?= htmlspecialchars($tech['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
                            <?php endif; ?>
                        </div>
                        <span class="ht-tech-cat"><?= htmlspecialchars($tech['catLabel'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <h3><?= htmlspecialchars($tech['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars($tech['use'], ENT_QUOTES, 'UTF-8') ?></p>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ============================================================
         SELECTED CLIENT LOGOS + H2 #4: PORTFOLIO SHOWCASE
    ============================================================ -->
    <section class="client-logo-showcase-section" aria-label="Selected client logos">
        <div class="client-logo-showcase-heading">
            <span class="client-logo-showcase-badge"><i class="bi bi-stars" aria-hidden="true"></i> Selected Clients</span>
            <p class="ht-logo-heading">Brands Supported Through <span>Design &amp; Engineering</span></p>
            <p>Selected businesses supported through Chulbul Design and our technology partnership with SoftLes.</p>
        </div>

        <div class="client-logo-rail-viewport">
            <div class="client-logo-rail-track">
                <?php for ($railCopy = 0; $railCopy < 2; $railCopy++): ?>
                <div class="client-logo-rail-group" <?= $railCopy === 1 ? 'aria-hidden="true"' : '' ?>>
                    <?php foreach ($clientLogos as $clientLogo): ?>
                    <div class="client-logo-rail-item is-<?= htmlspecialchars($clientLogo['theme'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (($clientLogo['type'] ?? 'img') === 'text'): ?>
                        <span class="ht-logo-wordmark"><?= htmlspecialchars($clientLogo['name'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php else: ?>
                        <img src="<?= $base ?>/assets/images/client-logos/<?= htmlspecialchars($clientLogo['file'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= $railCopy === 0 ? htmlspecialchars($clientLogo['name'], ENT_QUOTES, 'UTF-8') : '' ?>"
                             width="160" height="64" loading="lazy">
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </section>

    <?php
    require_once __DIR__ . '/includes/portfolio-showcase.php';
    cbd_render_portfolio_showcase([
        'id' => 'portfolio',
        'eyebrow' => 'Selected Work',
        'title' => 'Our Web Design & Development Portfolio',
        'subtitle' => 'Inspect live desktop and mobile builds across custom corporate sites, Shopify ecommerce stores, and SaaS web applications.',
    ]);
    ?>

    <!-- ============================================================
         H2 #5: COSMIC GLASS INDUSTRIES GRID
    ============================================================ -->
    <section class="ht-sec ht-sec-cosmic ht-pat ht-pat-industries" aria-labelledby="industries-heading">
        <div class="ht-wrap">
            <div class="ht-head ht-reveal">
                <span class="ht-pill-kicker ht-pill-kicker-dark"><i class="bi bi-globe2" aria-hidden="true"></i> Sector Experience</span>
                <h2 id="industries-heading" class="ht-h2">Industries We <span class="ht-grad-text">Serve</span></h2>
                <p class="ht-lead">Explore dedicated web design, custom development, and UX architectures tailored to 16 specialized business sectors.</p>
            </div>

            <div class="ht-ind-grid ht-reveal">
                <?php foreach (cbd_industry_catalog() as $ind): ?>
                <a href="<?= $base ?>/industry/<?= htmlspecialchars($ind['slug'], ENT_QUOTES, 'UTF-8') ?>" class="ht-ind-glass">
                    <div class="ht-ind-icon bg-gradient-to-br <?= htmlspecialchars($ind['color'], ENT_QUOTES, 'UTF-8') ?>" aria-hidden="true">
                        <i class="bi <?= htmlspecialchars($ind['icon'], ENT_QUOTES, 'UTF-8') ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars($ind['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <i class="bi bi-arrow-up-right ht-ind-go" aria-hidden="true"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ============================================================
         H2 #6: WHY BUSINESSES CHOOSE CHULBUL DESIGN
    ============================================================ -->
    <section class="ht-sec" id="why-chulbul" aria-labelledby="why-choose-heading">
        <div class="ht-wrap">
            <div class="ht-head ht-reveal">
                <span class="ht-pill-kicker"><i class="bi bi-shield-check" aria-hidden="true"></i> Delivery Standard</span>
                <h2 id="why-choose-heading" class="ht-h2">Why Businesses Choose <span class="ht-grad-text">Chulbul Design</span></h2>
                <p class="ht-lead">Structured discovery, lean custom engineering, full code ownership, and transparent remote delivery from Gurugram to teams worldwide.</p>
            </div>

            <div class="ht-why-grid ht-reveal">
                <?php foreach ($whyCards as $card): ?>
                <article class="ht-why-card">
                    <span class="ht-why-num" aria-hidden="true"><?= htmlspecialchars($card['num'], ENT_QUOTES, 'UTF-8') ?></span>
                    <h3><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars($card['desc'], ENT_QUOTES, 'UTF-8') ?></p>
                </article>
                <?php endforeach; ?>
            </div>

            <!-- Verified Google Reviews (4.8★ · 19 Real Google Reviews) -->
            <div class="ht-greviews-wrap ht-reveal" id="google-reviews" aria-label="Verified Google reviews for Chulbul Design">
                <div class="ht-greviews-head">
                    <div class="ht-greviews-score-box">
                        <div class="ht-g-logo-badge" aria-hidden="true">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                        </div>
                        <div>
                            <div class="ht-greviews-score-row">
                                <span class="ht-greviews-num">4.8</span>
                                <span class="ht-g-stars" role="img" aria-label="4.8 out of 5 stars on Google">
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                    <i class="bi bi-star-fill" aria-hidden="true"></i>
                                </span>
                            </div>
                            <div class="ht-greviews-sub">Verified Google Rating · Based on 19 Google reviews</div>
                        </div>
                    </div>

                    <div class="ht-trust-pills">
                        <a href="<?= htmlspecialchars($googleReviewsUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="ht-trust-pill">
                            <i class="bi bi-google text-red-500" aria-hidden="true"></i> Read All 19 Google Reviews
                        </a>
                        <a href="<?= htmlspecialchars($googleReviewsUrl, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener noreferrer" class="ht-trust-pill">
                            <i class="bi bi-pencil-square text-indigo-600" aria-hidden="true"></i> Write a Review
                        </a>
                    </div>
                </div>

                <div class="ht-greviews-grid">
                    <?php foreach ($googleReviews as $rev): ?>
                    <article class="ht-greview-card">
                        <div class="ht-greview-top">
                            <div class="ht-greview-author">
                                <div class="ht-greview-avatar" aria-hidden="true"><?= htmlspecialchars($rev['initial'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div>
                                    <div class="ht-greview-name"><?= htmlspecialchars($rev['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <div class="ht-g-stars" style="font-size:12px;margin-top:2px;" role="img" aria-label="5 out of 5 stars">
                                        <i class="bi bi-star-fill" aria-hidden="true"></i>
                                        <i class="bi bi-star-fill" aria-hidden="true"></i>
                                        <i class="bi bi-star-fill" aria-hidden="true"></i>
                                        <i class="bi bi-star-fill" aria-hidden="true"></i>
                                        <i class="bi bi-star-fill" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
                            <span class="ht-greview-badge">
                                <svg width="13" height="13" viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                                <?= htmlspecialchars($rev['source'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                        <p class="ht-greview-quote">"<?= htmlspecialchars($rev['text'], ENT_QUOTES, 'UTF-8') ?>"</p>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="ht-trust-strip ht-reveal">
                <p>
                    <strong>Verified Agency Presence &amp; Growth Continuity:</strong> Pair your build with <a href="<?= $base ?>/seo-digital-marketing" class="ht-inline">SEO &amp; digital marketing</a> or extended technical delivery through our technology partner <a href="https://softles.in/" target="_blank" rel="noopener noreferrer" class="ht-inline">SoftLes</a>.
                </p>
                <div class="ht-trust-pills">
                    <a href="https://techbehemoths.com/company/chulbul-design" target="_blank" rel="noopener noreferrer" class="ht-trust-pill">
                        <i class="bi bi-patch-check-fill text-indigo-600" aria-hidden="true"></i> Verified on TechBehemoths
                    </a>
                    <a href="https://www.linkedin.com/company/chulbuldesign/" target="_blank" rel="noopener noreferrer" class="ht-trust-pill">
                        <i class="bi bi-linkedin text-blue-700" aria-hidden="true"></i> LinkedIn Company Page
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         H2 #7: INTERACTIVE GLOWING PROCESS WAVE TIMELINE
    ============================================================ -->
    <section class="ht-sec ht-sec-mesh-light ht-process-sec" aria-labelledby="process-heading">
        <div class="ht-process-pattern" aria-hidden="true">
            <svg class="ht-process-wave" viewBox="0 0 1440 420" fill="none" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M-40 310 C 260 140, 520 390, 820 210 C 1080 60, 1280 280, 1480 120" stroke="url(#htProcWave1)" stroke-width="2" stroke-dasharray="6 8"/>
                <path d="M-40 355 C 310 190, 560 420, 860 255 C 1110 115, 1300 320, 1480 175" stroke="url(#htProcWave2)" stroke-width="1.4"/>
                <circle cx="240" cy="240" r="88" stroke="rgba(79,70,229,0.12)" stroke-width="1" stroke-dasharray="4 6"/>
                <circle cx="1220" cy="140" r="112" stroke="rgba(238,72,61,0.13)" stroke-width="1" stroke-dasharray="3 7"/>
                <defs>
                    <linearGradient id="htProcWave1" x1="0" y1="0" x2="1440" y2="0" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#4f46e5" stop-opacity="0"/>
                        <stop offset="0.3" stop-color="#4f46e5" stop-opacity="0.28"/>
                        <stop offset="0.7" stop-color="#EE483D" stop-opacity="0.28"/>
                        <stop offset="1" stop-color="#06b6d4" stop-opacity="0"/>
                    </linearGradient>
                    <linearGradient id="htProcWave2" x1="0" y1="0" x2="1440" y2="0" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#7c3aed" stop-opacity="0"/>
                        <stop offset="0.5" stop-color="#7c3aed" stop-opacity="0.18"/>
                        <stop offset="1" stop-color="#EE483D" stop-opacity="0"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>
        <div class="ht-wrap">
            <div class="ht-head ht-reveal">
                <span class="ht-pill-kicker"><i class="bi bi-diagram-3-fill" aria-hidden="true"></i> How We Work</span>
                <h2 id="process-heading" class="ht-h2">Our Web Design &amp; <span class="ht-grad-text">Development Process</span></h2>
                <p class="ht-lead">Four transparent stages with clear sign-offs, staging previews, and documented deliverables from brief to launch.</p>
            </div>

            <div class="ht-timeline ht-reveal" data-ht-timeline>
                <div class="ht-timeline-rail" aria-hidden="true">
                    <div class="ht-timeline-fill" data-ht-timeline-fill></div>
                </div>
                <?php foreach ($processSteps as $sIdx => $step): ?>
                <div class="ht-stage<?= $sIdx === 0 ? ' is-active' : '' ?>" data-stage-idx="<?= $sIdx ?>">
                    <div class="ht-stage-node" aria-hidden="true"><?= htmlspecialchars($step['num'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="ht-stage-card">
                        <h3><?= htmlspecialchars($step['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($step['desc'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="ht-stage-output"><?= htmlspecialchars($step['output'], ENT_QUOTES, 'UTF-8') ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ============================================================
         LATEST INSIGHTS (BLOG SECTION)
    ============================================================ -->
    <?php if (!empty($_hp_latest_posts)): ?>
    <section class="ht-sec" id="latest-blog" aria-labelledby="blog-heading">
        <div class="ht-wrap">
            <div class="ht-head ht-reveal">
                <span class="ht-pill-kicker"><i class="bi bi-journal-richtext" aria-hidden="true"></i> From Our Blog</span>
                <h2 id="blog-heading" class="ht-h2">Latest Web Design &amp; <span class="ht-grad-text">SEO Insights</span></h2>
                <p class="ht-lead">Practical guides on custom web development, ecommerce architecture, and technical search performance.</p>
            </div>

            <div class="ht-blog-grid ht-reveal">
                <?php foreach ($_hp_latest_posts as $_hp_post):
                    $_hp_date = $_hp_post['date'] ? date('d M Y', strtotime((string)$_hp_post['date'])) : '';
                    $_hp_plain = trim(strip_tags((string)($_hp_post['excerpt'] ?? '')));
                    $_hp_excerpt = mb_substr($_hp_plain, 0, 120) . (mb_strlen($_hp_plain) > 120 ? '…' : '');
                ?>
                <a href="<?= $_hp_base ?>/blog/<?= htmlspecialchars((string)$_hp_post['slug'], ENT_QUOTES, 'UTF-8') ?>" class="ht-blog-card">
                    <div class="ht-blog-thumb">
                        <?php if (!empty($_hp_post['thumb'])): ?>
                        <img src="<?= htmlspecialchars((string)$_hp_post['thumb'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars((string)$_hp_post['title'], ENT_QUOTES, 'UTF-8') ?>"
                             width="640" height="360" loading="lazy">
                        <?php else: ?>
                        <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;color:#4f46e5;opacity:.35;">
                            <i class="bi bi-journal-richtext" style="font-size:44px;" aria-hidden="true"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="ht-blog-body">
                        <div class="ht-blog-meta">
                            <?php if ($_hp_date !== ''): ?><span><?= htmlspecialchars($_hp_date, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                            <?php if (!empty($_hp_post['read_time'])): ?><span><?= htmlspecialchars((string)$_hp_post['read_time'], ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?>
                        </div>
                        <h3><?= htmlspecialchars((string)$_hp_post['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <?php if ($_hp_excerpt !== ''): ?><p><?= htmlspecialchars($_hp_excerpt, ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
                        <span class="ht-bento-link">Read Article <i class="bi bi-arrow-right" aria-hidden="true"></i></span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <div style="text-align:center;margin-top:34px;">
                <a href="<?= $_hp_base ?>/blog/" class="ht-btn ht-btn-outline">
                    View All Articles <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- ============================================================
         H2 #8: SPLIT FAQ
    ============================================================ -->
    <section class="ht-sec ht-sec-mesh-light" id="faq" aria-labelledby="faq-heading">
        <div class="ht-wrap">
            <div class="ht-faq-split">
                <div class="ht-faq-aside ht-reveal">
                    <span class="ht-pill-kicker"><i class="bi bi-question-circle-fill" aria-hidden="true"></i> Clarity First</span>
                    <h2 id="faq-heading" class="ht-h2">Frequently Asked <span class="ht-grad-text">Questions</span></h2>
                    <p class="ht-lead">Straight answers about how we scope, price, build, and hand over custom websites and web applications.</p>

                    <div class="ht-faq-contact">
                        <strong>Have a specific project brief?</strong>
                        <p>Share your URL or requirements and we will reply with honest platform and scope guidance.</p>
                        <a href="<?= $base ?>/contact-us" class="ht-btn ht-btn-ink" style="padding:11px 22px;font-size:13.5px;">
                            Send Your Brief <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="ht-faq-list ht-reveal">
                    <?php foreach ($homeFaqs as $idx => $faq): ?>
                    <details class="ht-faq-item"<?= $idx === 0 ? ' open' : '' ?>>
                        <summary>
                            <span><?= htmlspecialchars($faq['q'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="ht-faq-plus" aria-hidden="true"><i class="bi bi-plus-lg"></i></span>
                        </summary>
                        <p class="ht-faq-answer"><?= htmlspecialchars($faq['a'], ENT_QUOTES, 'UTF-8') ?></p>
                    </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         EXPLOSIVE AURORA MESH CONVERSION FINALE
    ============================================================ -->
    <section class="ht-sec" style="padding:72px 0;">
        <div class="ht-wrap">
            <div class="ht-cta-panel ht-spotlight ht-reveal" data-ht-spotlight>
                <div class="ht-cta-pattern" aria-hidden="true">
                    <svg class="ht-cta-rings" viewBox="0 0 600 420" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="430" cy="210" r="115" stroke="rgba(255,255,255,0.14)" stroke-width="1" stroke-dasharray="4 6"/>
                        <circle cx="430" cy="210" r="185" stroke="rgba(255,255,255,0.11)" stroke-width="1"/>
                        <circle cx="430" cy="210" r="260" stroke="rgba(255,156,138,0.16)" stroke-width="1" stroke-dasharray="2 8"/>
                        <circle cx="430" cy="210" r="340" stroke="rgba(165,180,252,0.1)" stroke-width="1"/>
                        <circle cx="245" cy="210" r="4" fill="#ff8a65" fill-opacity="0.85"/>
                        <circle cx="510" cy="48" r="3.5" fill="#a5b4fc" fill-opacity="0.85"/>
                        <circle cx="575" cy="310" r="3" fill="#34d399" fill-opacity="0.8"/>
                        <path d="M0 320 Q 180 250, 340 310 T 600 240" stroke="url(#htCtaWave)" stroke-width="1.2" opacity="0.5"/>
                        <defs>
                            <linearGradient id="htCtaWave" x1="0" y1="0" x2="600" y2="0" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#818cf8" stop-opacity="0"/>
                                <stop offset="0.5" stop-color="#ff8a65" stop-opacity="0.7"/>
                                <stop offset="1" stop-color="#38bdf8" stop-opacity="0"/>
                            </linearGradient>
                        </defs>
                    </svg>
                </div>
                <div class="ht-cta-copy">
                    <span class="ht-pill-kicker ht-pill-kicker-dark"><i class="bi bi-rocket-takeoff-fill" aria-hidden="true"></i> Start Your Build</span>
                    <h2>Ready to Build a Faster, Higher-Converting Website?</h2>
                    <p>Tell us what you are planning — a new custom website, a Shopify or WordPress build, a web application, or an SEO-safe redesign. We will respond with a clear scope and next steps.</p>
                </div>
                <div class="ht-cta-actions">
                    <a href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%20would%20like%20to%20discuss%20a%20website%20project."
                       target="_blank" rel="noopener noreferrer" class="ht-btn ht-btn-vibrant">
                        <i class="bi bi-whatsapp" aria-hidden="true"></i> WhatsApp Now
                    </a>
                    <a href="<?= $base ?>/contact-us" class="ht-btn ht-btn-glass-light">
                        <i class="bi bi-envelope-fill" aria-hidden="true"></i> Request Free Quote
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ============================================================
         INTERACTIVE COUNTRY-WISE LOCATIONS DIRECTORY
    ============================================================ -->
    <?php
    $locationGroups = [
        ['key' => 'india',        'flag' => '🇮🇳', 'country' => 'India',        'cities' => [['Delhi','delhi'],['Gurgaon','gurgaon'],['Noida','noida'],['Mumbai','mumbai'],['Bangalore','bangalore'],['Hyderabad','hyderabad'],['Chennai','chennai'],['Pune','pune'],['Kolkata','kolkata'],['Ahmedabad','ahmedabad'],['Jaipur','jaipur'],['Chandigarh','chandigarh'],['Amritsar','amritsar'],['Meerut','meerut'],['Coimbatore','coimbatore'],['Bhubaneswar','bhubaneswar']]],
        ['key' => 'usa',          'flag' => '🇺🇸', 'country' => 'USA',          'cities' => [['New York','new-york'],['Los Angeles','los-angeles'],['Chicago','chicago'],['Houston','houston'],['Dallas','dallas'],['San Francisco','san-francisco'],['Miami','miami'],['Seattle','seattle'],['Boston','boston'],['Atlanta','atlanta']]],
        ['key' => 'uk',           'flag' => '🇬🇧', 'country' => 'UK',           'cities' => [['London','london'],['Manchester','manchester'],['Birmingham','birmingham'],['Leeds','leeds'],['Glasgow','glasgow'],['Edinburgh','edinburgh'],['Bristol','bristol']]],
        ['key' => 'australia',    'flag' => '🇦🇺', 'country' => 'Australia',    'cities' => [['Sydney','sydney'],['Melbourne','melbourne'],['Brisbane','brisbane'],['Perth','perth'],['Adelaide','adelaide']]],
        ['key' => 'canada',       'flag' => '🇨🇦', 'country' => 'Canada',       'cities' => [['Toronto','toronto'],['Vancouver','vancouver'],['Calgary','calgary'],['Montreal','montreal'],['Ottawa','ottawa']]],
        ['key' => 'uae',          'flag' => '🇦🇪', 'country' => 'UAE',          'cities' => [['Dubai','dubai'],['Abu Dhabi','abu-dhabi'],['Sharjah','sharjah']]],
        ['key' => 'singapore',    'flag' => '🇸🇬', 'country' => 'Singapore',    'cities' => [['Singapore','singapore']]],
        ['key' => 'south-africa', 'flag' => '🇿🇦', 'country' => 'South Africa', 'cities' => [['Johannesburg','johannesburg'],['Cape Town','cape-town'],['Durban','durban']]],
    ];
    ?>
    <section class="ht-locations-sec" aria-label="Service locations by country">
        <div class="ht-wrap">
            <div class="ht-loc-shell ht-reveal" data-ht-locations>
                <div class="ht-loc-top">
                    <div>
                        <span class="ht-pill-kicker" style="margin-bottom:10px;"><i class="bi bi-globe2" aria-hidden="true"></i> Where We Work</span>
                        <p class="ht-loc-title">Explore Our <span class="ht-grad-text">Service Locations</span></p>
                        <p class="ht-loc-sub">
                            Headquartered in Gurugram, India with remote delivery for businesses worldwide. Filter by region below — these are remote service areas, not local branch office claims.
                        </p>
                    </div>
                    <a href="<?= $base ?>/cities" class="ht-locations-btn">
                        View Full Directory <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <!-- Interactive Country Filter Tabs -->
                <div class="ht-loc-tabs" role="tablist" aria-label="Filter service locations by country">
                    <button type="button" class="ht-loc-tab is-active" role="tab" aria-selected="true" data-loc-filter="all">
                        🌍 All Regions <small>50</small>
                    </button>
                    <?php foreach ($locationGroups as $grp): ?>
                    <button type="button" class="ht-loc-tab" role="tab" aria-selected="false" data-loc-filter="<?= htmlspecialchars($grp['key'], ENT_QUOTES, 'UTF-8') ?>">
                        <?= $grp['flag'] ?> <?= htmlspecialchars($grp['country'], ENT_QUOTES, 'UTF-8') ?>
                        <small><?= count($grp['cities']) ?></small>
                    </button>
                    <?php endforeach; ?>
                </div>

                <!-- Country-Wise Cards Grid -->
                <div class="ht-loc-grid" data-loc-grid>
                    <?php foreach ($locationGroups as $grp): ?>
                    <div class="ht-loc-card" data-loc-card="<?= htmlspecialchars($grp['key'], ENT_QUOTES, 'UTF-8') ?>">
                        <div class="ht-loc-card-head">
                            <span class="ht-loc-country"><?= $grp['flag'] ?> <?= htmlspecialchars($grp['country'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="ht-loc-count"><?= count($grp['cities']) ?> <?= count($grp['cities']) === 1 ? 'city' : 'cities' ?></span>
                        </div>
                        <div class="ht-loc-cities">
                            <?php foreach ($grp['cities'] as [$cname, $cslug]): ?>
                            <a href="<?= $base ?>/city/<?= htmlspecialchars($cslug, ENT_QUOTES, 'UTF-8') ?>" class="ht-loc-city"><?= htmlspecialchars($cname, ENT_QUOTES, 'UTF-8') ?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

</div>

<script type="application/json" id="htShowreelData"><?= json_encode(array_map(static function (array $b) use ($base): array {
    $b['desktop'] = $base . $b['desktop'];
    $b['mobile']  = $base . $b['mobile'];
    return $b;
}, $showreelBuilds), $jsonFlags) ?></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // 1. Scroll Progress Bar
    var progressBar = document.querySelector('[data-ht-progress]');
    if (progressBar) {
        window.addEventListener('scroll', function () {
            var docH = document.documentElement.scrollHeight - window.innerHeight;
            var pct = docH > 0 ? Math.min(100, Math.max(0, (window.scrollY / docH) * 100)) : 0;
            progressBar.style.width = pct.toFixed(1) + '%';
        }, { passive: true });
    }

    // 2. Scroll Reveal Choreography
    var reveals = document.querySelectorAll('.ht-reveal');
    if (reducedMotion || !('IntersectionObserver' in window)) {
        reveals.forEach(function (el) { el.classList.add('is-in'); });
    } else {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (e) {
                if (e.isIntersecting) {
                    e.target.classList.add('is-in');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
        reveals.forEach(function (el) { io.observe(el); });
    }

    // 3. Cursor Spotlights
    if (!reducedMotion) {
        document.querySelectorAll('[data-ht-spotlight]').forEach(function (card) {
            card.addEventListener('mousemove', function (e) {
                var r = card.getBoundingClientRect();
                card.style.setProperty('--mx', (e.clientX - r.left) + 'px');
                card.style.setProperty('--my', (e.clientY - r.top) + 'px');
            });
        });
    }

    // 4. Full-Width 3D Showreel Stage (click pills or wings + subtle 3D parallax)
    var reelWrap = document.querySelector('[data-ht-showreel]');
    var reelJson = document.getElementById('htShowreelData');
    if (reelWrap && reelJson) {
        var slides = JSON.parse(reelJson.textContent || '[]');
        var pills = Array.prototype.slice.call(reelWrap.querySelectorAll('[data-reel-idx]'));
        var arena = reelWrap.querySelector('[data-ht-arena]');
        var mainImg = reelWrap.querySelector('[data-reel-main]');
        var mobImg = reelWrap.querySelector('[data-reel-mobile]');
        var urlEl = reelWrap.querySelector('[data-reel-url]');
        var cTopT = reelWrap.querySelector('[data-chip-top-title]');
        var cTopS = reelWrap.querySelector('[data-chip-top-sub]');
        var cBotT = reelWrap.querySelector('[data-chip-bot-title]');
        var cBotS = reelWrap.querySelector('[data-chip-bot-sub]');
        var wLeftImg = reelWrap.querySelector('[data-wing-left-img]');
        var wLeftName = reelWrap.querySelector('[data-wing-left-name]');
        var wLeftTag = reelWrap.querySelector('[data-wing-left-tag]');
        var wRightImg = reelWrap.querySelector('[data-wing-right-img]');
        var wRightName = reelWrap.querySelector('[data-wing-right-name]');
        var wRightTag = reelWrap.querySelector('[data-wing-right-tag]');
        var active = 0;
        var paused = false;

        function renderShowreel(idx) {
            if (!slides.length) return;
            active = (idx + slides.length) % slides.length;
            var cur = slides[active];
            var left = slides[(active + 1) % slides.length];
            var right = slides[(active + 2) % slides.length];

            pills.forEach(function (p, i) {
                p.classList.toggle('is-active', i === active);
                p.setAttribute('aria-selected', i === active ? 'true' : 'false');
            });

            if (mainImg) {
                mainImg.style.opacity = '0.35';
                mainImg.src = cur.desktop;
                mainImg.alt = cur.alt;
                mainImg.onload = function () { mainImg.style.opacity = '1'; };
                setTimeout(function () { mainImg.style.opacity = '1'; }, 180);
            }
            if (mobImg) mobImg.src = cur.mobile;
            if (urlEl) urlEl.textContent = cur.url;
            if (cTopT) cTopT.textContent = cur.chipTopTitle;
            if (cTopS) cTopS.textContent = cur.chipTopSub;
            if (cBotT) cBotT.textContent = cur.chipBotTitle;
            if (cBotS) cBotS.textContent = cur.chipBotSub;

            if (wLeftImg) { wLeftImg.src = left.desktop; wLeftImg.alt = left.alt; }
            if (wLeftName) wLeftName.textContent = left.name;
            if (wLeftTag) wLeftTag.textContent = left.tag;
            if (wRightImg) { wRightImg.src = right.desktop; wRightImg.alt = right.alt; }
            if (wRightName) wRightName.textContent = right.name;
            if (wRightTag) wRightTag.textContent = right.tag;
        }

        pills.forEach(function (p, i) {
            p.addEventListener('click', function () { paused = true; renderShowreel(i); });
        });

        var wingL = reelWrap.querySelector('[data-reel-wing-left]');
        var wingR = reelWrap.querySelector('[data-reel-wing-right]');
        if (wingL) wingL.addEventListener('click', function () { paused = true; renderShowreel(active + 1); });
        if (wingR) wingR.addEventListener('click', function () { paused = true; renderShowreel(active + 2); });

        reelWrap.addEventListener('mouseenter', function () { paused = true; });
        reelWrap.addEventListener('mouseleave', function () {
            paused = false;
            if (arena) arena.style.transform = '';
        });

        if (!reducedMotion && arena && window.innerWidth > 1024) {
            reelWrap.addEventListener('mousemove', function (e) {
                var r = reelWrap.getBoundingClientRect();
                var x = (e.clientX - r.left) / r.width - 0.5;
                var y = (e.clientY - r.top) / r.height - 0.5;
                arena.style.transform = 'rotateY(' + (x * 4).toFixed(2) + 'deg) rotateX(' + (-y * 3).toFixed(2) + 'deg)';
            });
        }

        if (!reducedMotion) {
            setInterval(function () { if (!paused) renderShowreel(active + 1); }, 5500);
        }
    }

    // 5. Scope Blueprint Tabs
    var bp = document.querySelector('[data-ht-blueprint]');
    if (bp) {
        var bpTabs = bp.querySelectorAll('.ht-bp-tab');
        var bpPanels = bp.querySelectorAll('.ht-bp-panel');
        bpTabs.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var target = btn.dataset.bpTarget;
                bpTabs.forEach(function (b) { b.classList.remove('is-active'); b.setAttribute('aria-selected', 'false'); });
                bpPanels.forEach(function (p) { p.classList.toggle('is-active', p.dataset.bpPanel === target); });
                btn.classList.add('is-active');
                btn.setAttribute('aria-selected', 'true');
            });
        });
    }

    // 6. Tech Stack Filter
    var filterBar = document.querySelector('[data-ht-tech-filter]');
    var techGrid = document.querySelector('[data-ht-tech-grid]');
    if (filterBar && techGrid) {
        var fBtns = filterBar.querySelectorAll('.ht-filter-btn');
        var tItems = techGrid.querySelectorAll('.ht-tech-tile');
        fBtns.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var cat = btn.dataset.cat;
                fBtns.forEach(function (b) { b.classList.remove('is-active'); b.setAttribute('aria-selected', 'false'); });
                btn.classList.add('is-active');
                btn.setAttribute('aria-selected', 'true');
                tItems.forEach(function (item) {
                    var match = cat === 'all' || item.dataset.techCat === cat;
                    item.classList.toggle('is-dim', !match);
                });
            });
        });
    }

    // 7. Interactive Process Timeline Fill
    var timeline = document.querySelector('[data-ht-timeline]');
    if (timeline) {
        var stages = Array.prototype.slice.call(timeline.querySelectorAll('.ht-stage'));
        var fill = timeline.querySelector('[data-ht-timeline-fill]');
        function setStage(idx) {
            stages.forEach(function (st, i) { st.classList.toggle('is-active', i <= idx); });
            if (fill && stages.length > 1) fill.style.width = ((idx / (stages.length - 1)) * 100) + '%';
        }
        stages.forEach(function (st, i) {
            st.addEventListener('mouseenter', function () { setStage(i); });
            st.addEventListener('click', function () { setStage(i); });
        });
    }

    // 8. Interactive Country-Wise Locations Filter
    var locBox = document.querySelector('[data-ht-locations]');
    if (locBox) {
        var locTabs = locBox.querySelectorAll('[data-loc-filter]');
        var locGrid = locBox.querySelector('[data-loc-grid]');
        var locCards = locBox.querySelectorAll('[data-loc-card]');
        locTabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                var key = tab.dataset.locFilter;
                locTabs.forEach(function (t) {
                    var on = t === tab;
                    t.classList.toggle('is-active', on);
                    t.setAttribute('aria-selected', on ? 'true' : 'false');
                });
                if (locGrid) locGrid.classList.toggle('is-single', key !== 'all');
                locCards.forEach(function (card) {
                    card.classList.toggle('is-hidden', key !== 'all' && card.dataset.locCard !== key);
                });
            });
        });
    }
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
