<?php
require_once __DIR__ . '/includes/http.php';
require_once __DIR__ . '/includes/seo.php';

$cbd_page_has_charset = true;
$page_styles = ['/assets/css/web-development.css'];

if (cbd_is_direct_script_request('wordpress-development.php')) {
    cbd_redirect_path('/wordpress-development');
}

$base = cbd_base_path();
$canonical = cbd_canonical_url('/wordpress-development');
$page_robots = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
$pageTitle = 'WordPress Development Company | Chulbul Design';
$pageDescription = 'Custom WordPress development company for fast, secure websites, custom themes, plugin integrations, WooCommerce, migrations and ongoing support.';
$shareImage = cbd_public_url('/assets/images/service-heroes/wordpress-development-hero.webp');

$wordpressServices = [
    ['icon' => 'bi-wordpress', 'title' => 'Custom WordPress Websites', 'text' => 'Business websites planned around your content, customer journey and editing needs instead of an off-the-shelf theme.'],
    ['icon' => 'bi-palette2', 'title' => 'Custom Theme Development', 'text' => 'Lean, responsive themes that translate approved designs into reusable WordPress components without unnecessary builder weight.'],
    ['icon' => 'bi-plug', 'title' => 'Plugin Development & Integration', 'text' => 'Focused plugin functionality and carefully selected integrations for forms, CRM, payments, membership and business workflows.'],
    ['icon' => 'bi-cart3', 'title' => 'WooCommerce Development', 'text' => 'Product, cart, checkout and order-management experiences built for the way your catalogue and operations work.', 'href' => '/woocommerce-development', 'link' => 'Explore WooCommerce development'],
    ['icon' => 'bi-layout-text-window-reverse', 'title' => 'Gutenberg & Editor Experience', 'text' => 'Structured blocks and fields that let editors publish confidently while protecting design consistency across the website.'],
    ['icon' => 'bi-arrow-repeat', 'title' => 'WordPress Migration', 'text' => 'Move content, media and important URLs from another platform or host with redirects, backups and launch checks.', 'href' => '/website-migration', 'link' => 'Plan a website migration'],
    ['icon' => 'bi-speedometer2', 'title' => 'Speed Optimization', 'text' => 'Plugin review, asset optimization, caching and template improvements focused on real user experience and Core Web Vitals.', 'href' => '/website-speed-optimization', 'link' => 'Improve website speed'],
    ['icon' => 'bi-shield-check', 'title' => 'Security & Recovery', 'text' => 'Practical hardening, access controls, backup planning and recovery support for business-critical WordPress websites.', 'href' => '/website-security', 'link' => 'Explore website security'],
    ['icon' => 'bi-braces', 'title' => 'API & Third-Party Connections', 'text' => 'Connect WordPress with CRM, payment, analytics, messaging and other documented business systems.', 'href' => '/api-integration', 'link' => 'Explore API integration'],
    ['icon' => 'bi-tools', 'title' => 'WordPress Maintenance', 'text' => 'Planned updates, backups, testing and technical support that keep the website dependable after launch.', 'href' => '/website-maintenance', 'link' => 'View maintenance support'],
];

$solutionTypes = [
    ['icon' => 'bi-building', 'title' => 'Corporate Websites', 'text' => 'Flexible company websites for services, teams, resources, locations and lead generation.'],
    ['icon' => 'bi-briefcase', 'title' => 'Professional Services', 'text' => 'Trust-led websites for consultants, agencies, legal, finance and specialist B2B teams.'],
    ['icon' => 'bi-shop', 'title' => 'WooCommerce Stores', 'text' => 'Manageable online stores with clear products, checkout and order workflows.'],
    ['icon' => 'bi-newspaper', 'title' => 'Publishing Websites', 'text' => 'Editorial platforms for articles, news, guides, categories and author workflows.'],
    ['icon' => 'bi-calendar-event', 'title' => 'Booking Websites', 'text' => 'Service information combined with scheduling, enquiry and confirmation journeys.'],
    ['icon' => 'bi-people', 'title' => 'Membership Platforms', 'text' => 'Protected resources, user accounts, subscriptions and role-based content access.'],
    ['icon' => 'bi-mortarboard', 'title' => 'Education Websites', 'text' => 'Course, faculty, admission and resource structures that teams can update easily.'],
    ['icon' => 'bi-heart-pulse', 'title' => 'Healthcare Websites', 'text' => 'Accessible service, doctor and appointment journeys with responsible content management.'],
    ['icon' => 'bi-house-gear', 'title' => 'Home-Service Websites', 'text' => 'Service areas, project galleries, reviews and quote-request flows designed for mobile leads.'],
    ['icon' => 'bi-globe2', 'title' => 'Multilingual Websites', 'text' => 'Structured language versions with clear navigation, translation ownership and SEO-ready URLs.'],
];

$faqs = [
    ['q' => 'What does a WordPress development company do?', 'a' => 'A WordPress development company plans, designs and builds websites on WordPress. The work can include custom themes, plugins, structured content, integrations, WooCommerce, migration, performance, security, deployment and ongoing maintenance.'],
    ['q' => 'Do you use pre-made WordPress themes?', 'a' => 'For custom projects, we prefer a lean theme developed around the approved design and content structure. If a ready-made theme is genuinely appropriate for a limited budget or simple requirement, we explain its constraints before recommending it.'],
    ['q' => 'What is the difference between WordPress development and CMS development?', 'a' => 'WordPress development focuses specifically on the WordPress ecosystem, including its editor, themes, plugins and WooCommerce. CMS development is broader and may involve Joomla, a headless CMS or a custom content-management system when WordPress is not the best fit.'],
    ['q' => 'Can you redesign an existing WordPress website?', 'a' => 'Yes. We can review the current theme, plugins, content, analytics and technical issues before planning a redesign. Important URLs, working functionality and search visibility are considered during migration and launch.'],
    ['q' => 'Can you build a WooCommerce store?', 'a' => 'Yes. WooCommerce work can include catalogue structure, product templates, cart and checkout, payment and shipping integrations, customer emails and order-management requirements. Complex commerce needs are scoped separately from a standard content website.'],
    ['q' => 'Can WordPress connect with our CRM or other software?', 'a' => 'Yes, when the other platform provides suitable API access or an established integration. We verify documentation, authentication, data fields, webhooks, error handling and ownership before deciding whether to use an existing connector or custom code.'],
    ['q' => 'How do you make a WordPress website fast?', 'a' => 'Performance starts with a lean theme, appropriate hosting, optimized images, controlled plugin usage and sensible asset loading. We also review caching, database overhead, third-party scripts and Core Web Vitals on representative pages.'],
    ['q' => 'How much does custom WordPress development cost?', 'a' => 'Cost depends on page templates, custom design, content migration, editor requirements, WooCommerce, integrations, languages, accessibility, performance and testing. We provide a written scope after discovery rather than quoting one price for every website.'],
    ['q' => 'How long does a WordPress project take?', 'a' => 'A focused business website may take several weeks, while larger content migrations, custom plugins or WooCommerce workflows take longer. The schedule is confirmed after content, features, integrations and approval responsibilities are clear.'],
    ['q' => 'Do you provide WordPress maintenance after launch?', 'a' => 'Yes. Maintenance can include monitored updates, backups, testing, security checks, performance reviews, bug fixes and planned improvements. The support plan is matched to the website’s plugins, traffic and business importance.'],
];

$schemaOffers = [];
foreach ($wordpressServices as $service) {
    $schemaOffers[] = ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => $service['title']]];
}

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Service',
            '@id' => $canonical . '#service',
            'name' => 'Custom WordPress Development Services',
            'serviceType' => 'WordPress Development',
            'url' => $canonical,
            'description' => $pageDescription,
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
            'hasOfferCatalog' => ['@type' => 'OfferCatalog', 'name' => 'WordPress Development Services', 'itemListElement' => $schemaOffers],
        ],
        [
            '@type' => 'BreadcrumbList',
            '@id' => $canonical . '#breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => CBD_CANONICAL_ORIGIN . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Web Design & Development', 'item' => cbd_canonical_url('/web-design-development')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => 'WordPress Development', 'item' => $canonical],
            ],
        ],
        [
            '@type' => 'FAQPage',
            '@id' => $canonical . '#faq',
            'mainEntity' => array_map(static fn(array $faq): array => [
                '@type' => 'Question',
                'name' => $faq['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
            ], $faqs),
        ],
    ],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= cbd_escape_text($pageTitle) ?></title>
    <meta name="description" content="<?= cbd_escape_text($pageDescription) ?>">
    <link rel="canonical" href="<?= cbd_escape_text($canonical) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= cbd_escape_text($pageTitle) ?>">
    <meta property="og:description" content="<?= cbd_escape_text($pageDescription) ?>">
    <meta property="og:url" content="<?= cbd_escape_text($canonical) ?>">
    <meta property="og:image" content="<?= cbd_escape_text($shareImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@chulbuldesign">
    <meta name="twitter:title" content="<?= cbd_escape_text($pageTitle) ?>">
    <meta name="twitter:description" content="<?= cbd_escape_text($pageDescription) ?>">
    <meta name="twitter:image" content="<?= cbd_escape_text($shareImage) ?>">
    <link rel="preload" as="image" href="<?= $base ?>/assets/images/service-heroes/wordpress-development-hero.webp" fetchpriority="high">
    <script type="application/ld+json"><?= cbd_json_ld($schema) ?></script>
    <?php require __DIR__ . '/includes/header.php'; ?>

<div class="wd-page">
    <section class="wd-hero" aria-labelledby="wp-page-title">
        <div class="wd-orb wd-orb-one" aria-hidden="true"></div>
        <div class="wd-orb wd-orb-two" aria-hidden="true"></div>
        <div class="wd-container wd-hero-grid">
            <div class="wd-hero-copy wd-reveal">
                <nav class="wd-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?= $base ?>/">Home</a><span aria-hidden="true">/</span>
                    <a href="<?= $base ?>/web-design-development">Web Design &amp; Development</a><span aria-hidden="true">/</span>
                    <span aria-current="page">WordPress Development</span>
                </nav>
                <span class="wd-eyebrow"><i class="bi bi-wordpress" aria-hidden="true"></i> WordPress Development Services</span>
                <h1 id="wp-page-title">WordPress Development Company for <span>Fast, Flexible Websites</span></h1>
                <p class="wd-hero-lead">We build custom WordPress websites that are straightforward for customers to use and practical for internal teams to manage. From a lean business site to WooCommerce, membership and third-party integrations, the solution is shaped around your content and operating requirements.</p>
                <div class="wd-actions">
                    <a class="wd-button wd-button-primary" href="<?= $base ?>/contact-us"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i> Discuss Your WordPress Project</a>
                    <a class="wd-button wd-button-secondary" href="#wordpress-services">Explore WordPress Services <i class="bi bi-arrow-down" aria-hidden="true"></i></a>
                </div>
                <ul class="wd-trust-list" aria-label="WordPress development commitments">
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Custom, responsive themes</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Editor-friendly content structure</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Controlled plugin architecture</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Launch and maintenance support</li>
                </ul>
            </div>
            <div class="wd-hero-visual wd-reveal">
                <div class="wd-browser">
                    <div class="wd-browser-bar" aria-hidden="true"><span></span><span></span><span></span><b>your-wordpress-site.com</b></div>
                    <img src="<?= $base ?>/assets/images/service-heroes/wordpress-development-hero.webp" alt="Custom WordPress development showing theme, plugin, WooCommerce and performance planning" width="960" height="640" fetchpriority="high">
                    <div class="wd-code-chip wd-code-chip-one" aria-hidden="true"><i class="bi bi-layout-text-window"></i><span>Easy to manage</span></div>
                    <div class="wd-code-chip wd-code-chip-two" aria-hidden="true"><i class="bi bi-lightning-charge-fill"></i><span>Lean by design</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="wd-section wd-intro">
        <div class="wd-container wd-narrow wd-reveal">
            <span class="wd-kicker">WordPress without the usual clutter</span>
            <h2>A Website Your Customers Can Trust and Your Team Can Operate</h2>
            <p>WordPress is flexible enough to support a small company website, a large publishing platform or an online store. That flexibility is valuable, but it can also produce slow, fragile websites when every requirement is solved by adding another plugin or an oversized multipurpose theme. Good WordPress development is not simply installing the software. It means making deliberate choices about structure, editing, code, hosting and maintenance.</p>
            <p>Chulbul Design starts with the content and customer journey. We identify the page types your audience needs, the information your team will update and the actions that should lead to an enquiry, booking or sale. The theme and editor experience are then built around those decisions. Reusable blocks help editors work quickly, while sensible controls protect spacing, typography and brand consistency.</p>
            <p>For requirements that go beyond publishing, we map plugins and integrations carefully. Existing tools are used when they are dependable and appropriate; custom development is reserved for functionality that genuinely needs it. This produces a clearer ownership model and reduces avoidable complexity after launch.</p>
        </div>
    </section>

    <section class="wd-section wd-section-soft" id="wordpress-services" aria-labelledby="wordpress-services-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal">
                <span class="wd-kicker">WordPress capabilities</span>
                <h2 id="wordpress-services-title">Custom WordPress Development Services</h2>
                <p>Build a new website, improve an existing one or create the integrations and support needed for dependable daily operation.</p>
            </div>
            <div class="wd-service-grid">
                <?php foreach ($wordpressServices as $index => $service): ?>
                <article class="wd-service-card wd-reveal">
                    <span class="wd-card-number"><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <div class="wd-icon"><i class="bi <?= cbd_escape_text($service['icon']) ?>" aria-hidden="true"></i></div>
                    <h3><?= cbd_escape_text($service['title']) ?></h3>
                    <p><?= cbd_escape_text($service['text']) ?></p>
                    <?php if (!empty($service['href'])): ?><a href="<?= $base . cbd_escape_text($service['href']) ?>"><?= cbd_escape_text($service['link']) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a><?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-section">
        <div class="wd-container wd-split">
            <div class="wd-media-panel wd-reveal">
                <img src="<?= $base ?>/assets/images/engineer-outcomes@2x.webp" alt="Custom WordPress theme and content architecture planning" width="900" height="725" loading="lazy">
                <div class="wd-media-caption"><i class="bi bi-wordpress" aria-hidden="true"></i><span>Custom theme → structured editor → controlled publishing</span></div>
            </div>
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Custom themes and blocks</span>
                <h2>Designed Around Your Content, Not a Theme Demo</h2>
                <p>A theme demo is designed to sell the theme to thousands of unrelated businesses. Your website has a different job: explain your specific offer, answer customer questions and guide the right action. We translate approved layouts into a custom WordPress theme with templates and blocks matched to your real content.</p>
                <p>The editing model is planned alongside the public design. Repeated information becomes structured fields; flexible content becomes controlled blocks; site-wide elements are managed centrally. This reduces inconsistent pages and lets non-technical editors make routine updates without rebuilding layouts.</p>
                <ul class="wd-check-list">
                    <li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Purpose-built templates:</strong> page types reflect your services, resources, people, locations or products.</span></li>
                    <li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Reusable Gutenberg blocks:</strong> editors get flexibility without unrestricted page-builder clutter.</span></li>
                    <li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Responsive behavior:</strong> layouts are reviewed for real content across common screen sizes.</span></li>
                    <li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Documented ownership:</strong> teams know what they can edit and which changes need development support.</span></li>
                </ul>
            </div>
        </div>
    </section>

    <section class="wd-section wd-solutions" aria-labelledby="wordpress-solutions-title">
        <div class="wd-container">
            <div class="wd-heading wd-heading-light wd-reveal">
                <span class="wd-kicker">Flexible by purpose</span>
                <h2 id="wordpress-solutions-title">WordPress Solutions for Different Business Models</h2>
                <p>WordPress is selected when its publishing ecosystem and ownership model suit the requirement—not as a default answer to every product.</p>
            </div>
            <div class="wd-solution-grid">
                <?php foreach ($solutionTypes as $solution): ?>
                <article class="wd-solution-card wd-reveal"><i class="bi <?= cbd_escape_text($solution['icon']) ?>" aria-hidden="true"></i><h3><?= cbd_escape_text($solution['title']) ?></h3><p><?= cbd_escape_text($solution['text']) ?></p></article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="wordpress-foundation-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal">
                <span class="wd-kicker">A maintainable foundation</span>
                <h2 id="wordpress-foundation-title">Theme, Content and Plugins Working as One System</h2>
                <p>Most WordPress problems come from treating these layers as unrelated. We plan how they interact before the website becomes difficult to change.</p>
            </div>
            <div class="wd-stack-grid">
                <article class="wd-stack-card wd-reveal"><span class="wd-stack-label">01 / Theme</span><h3>A lean presentation layer</h3><p>The theme controls layout, responsive behavior and reusable visual components. Custom code keeps the public experience aligned with the approved design and avoids features the website does not use.</p><div class="wd-tags"><span>Semantic HTML</span><span>Custom CSS</span><span>JavaScript</span><span>Accessibility</span></div></article>
                <article class="wd-stack-card wd-reveal"><span class="wd-stack-label">02 / Content</span><h3>An editor built for real tasks</h3><p>Fields, post types, categories and blocks reflect the information editors maintain. Clear labels and sensible restrictions make publishing faster and reduce accidental layout damage.</p><div class="wd-tags"><span>Gutenberg</span><span>Custom Fields</span><span>Post Types</span><span>Roles</span></div></article>
                <article class="wd-stack-card wd-reveal"><span class="wd-stack-label">03 / Extensions</span><h3>Functionality with clear ownership</h3><p>Plugins are evaluated for quality, support and overlap. Custom functionality is separated cleanly so theme changes do not remove critical business behavior.</p><div class="wd-tags"><span>Plugins</span><span>WooCommerce</span><span>REST API</span><span>Integrations</span></div></article>
            </div>
        </div>
    </section>

    <section class="wd-section wd-section-soft">
        <div class="wd-container wd-split wd-split-reverse">
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Plugins and integrations</span>
                <h2>Extend WordPress Without Turning It Into a Dependency Puzzle</h2>
                <p>Plugins can accelerate delivery, but too many overlapping tools create performance, security and maintenance problems. We first check whether WordPress already supports the requirement, then compare established plugins, a direct API integration and custom development.</p>
                <p>For external systems, we document which platform owns each piece of data and what happens when a connection fails. Leads, payments, bookings and account updates need validation and clear error handling. A dependable integration is one your team can monitor—not a hidden automation nobody understands after launch.</p>
                <div class="wd-integration-list">
                    <span><i class="bi bi-person-lines-fill"></i> CRM and lead routing</span><span><i class="bi bi-credit-card"></i> Payments and checkout</span>
                    <span><i class="bi bi-calendar-check"></i> Booking systems</span><span><i class="bi bi-envelope-check"></i> Email and notifications</span>
                    <span><i class="bi bi-graph-up"></i> Analytics and consent</span><span><i class="bi bi-translate"></i> Multilingual workflows</span>
                </div>
                <a class="wd-text-link" href="<?= $base ?>/api-integration">Learn about API integration services <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="wd-api-visual wd-reveal" aria-label="Illustration of WordPress connected to business systems">
                <div class="wd-api-core"><i class="bi bi-wordpress" aria-hidden="true"></i><strong>WordPress</strong></div>
                <div class="wd-api-node wd-api-node-one"><i class="bi bi-person-lines-fill"></i><span>CRM</span></div>
                <div class="wd-api-node wd-api-node-two"><i class="bi bi-credit-card"></i><span>Payments</span></div>
                <div class="wd-api-node wd-api-node-three"><i class="bi bi-calendar3"></i><span>Bookings</span></div>
                <div class="wd-api-node wd-api-node-four"><i class="bi bi-bar-chart"></i><span>Analytics</span></div>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="wordpress-quality-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal">
                <span class="wd-kicker">Beyond the page builder</span>
                <h2 id="wordpress-quality-title">Performance, Security, SEO Foundations and Accessibility</h2>
                <p>WordPress provides the platform; implementation quality determines whether the final website is dependable and usable.</p>
            </div>
            <div class="wd-quality-grid">
                <article class="wd-quality-card wd-reveal"><i class="bi bi-lightning-charge"></i><h3>Performance</h3><p>Lean templates, optimized media, controlled scripts, caching and hosting alignment help users reach useful content sooner.</p></article>
                <article class="wd-quality-card wd-reveal"><i class="bi bi-shield-check"></i><h3>Security</h3><p>Responsible access, updates, backups, validated inputs and hardened configuration reduce common WordPress risks.</p></article>
                <article class="wd-quality-card wd-reveal"><i class="bi bi-search"></i><h3>SEO Foundation</h3><p>Semantic headings, crawlable links, metadata controls, schema opportunities, redirects and sitemaps support search visibility without guaranteeing rankings.</p></article>
                <article class="wd-quality-card wd-reveal"><i class="bi bi-universal-access"></i><h3>Accessibility</h3><p>Keyboard access, labels, focus visibility, meaningful structure and contrast improve the experience for a wider audience.</p></article>
            </div>
        </div>
    </section>

    <section class="wd-section wd-process" aria-labelledby="wordpress-process-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal"><span class="wd-kicker">A clear WordPress process</span><h2 id="wordpress-process-title">From Content Requirements to Confident Publishing</h2><p>Development and editor experience are reviewed together at every important stage.</p></div>
            <ol class="wd-process-list">
                <li class="wd-reveal"><span>01</span><div><h3>Discovery &amp; Audit</h3><p>We map audiences, content, functionality and editing needs, or audit the current WordPress setup when redesigning.</p></div></li>
                <li class="wd-reveal"><span>02</span><div><h3>Content Architecture</h3><p>Page types, taxonomies, reusable fields, user roles and migration requirements are organized before build.</p></div></li>
                <li class="wd-reveal"><span>03</span><div><h3>UX &amp; Theme Design</h3><p>Public layouts and responsive states are approved with realistic content rather than empty template blocks.</p></div></li>
                <li class="wd-reveal"><span>04</span><div><h3>Development &amp; Integration</h3><p>The theme, blocks, plugins and external connections are built in reviewable increments.</p></div></li>
                <li class="wd-reveal"><span>05</span><div><h3>Content, QA &amp; Training</h3><p>Templates, forms, roles, links, devices and editor workflows are tested before the team is trained.</p></div></li>
                <li class="wd-reveal"><span>06</span><div><h3>Launch &amp; Maintenance</h3><p>Backups, redirects, analytics and production settings are checked before ongoing support begins.</p></div></li>
            </ol>
        </div>
    </section>

    <section class="wd-section wd-proof" aria-labelledby="wordpress-proof-title">
        <div class="wd-container wd-proof-grid">
            <div class="wd-proof-copy wd-reveal">
                <span class="wd-kicker">Review relevant work</span>
                <h2 id="wordpress-proof-title">WordPress Decisions and Relevant Work You Can Review</h2>
                <p>Our portfolio includes digital delivery experience connected with Gurjar Pragati Manch, Kisaan Jee and Tera Ghar. Relevant examples help show how page structure, reusable content, responsive layouts and day-to-day publishing needs are handled in a real website environment.</p>
                <p>During a consultation, we explain our role and demonstrate the decisions behind editing controls, theme structure, integrations, performance and long-term maintenance. For broader engineering requirements, Chulbul Design can also collaborate with SoftLes as a technology partner.</p>
                <a class="wd-button wd-button-primary" href="<?= $base ?>/#portfolio">Review Selected Work</a>
            </div>
            <div class="wd-proof-logos wd-reveal" aria-label="Selected client and partnership portfolio">
                <?php foreach ([['gurjar-pragati-manch.webp','Gurjar Pragati Manch'],['kisaanjee.webp','Kisaan Jee'],['tera-ghar.webp','Tera Ghar']] as $logo): ?>
                <div><img src="<?= $base ?>/assets/images/client-logos/<?= cbd_escape_text($logo[0]) ?>" alt="<?= cbd_escape_text($logo[1]) ?>" width="180" height="72" loading="lazy"><span><?= cbd_escape_text($logo[1]) ?></span></div>
                <?php endforeach; ?>
                <a href="https://softles.in/" target="_blank" rel="noopener noreferrer"><strong>S</strong><span>Technology partner: SoftLes <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></span></a>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="wordpress-why-title">
        <div class="wd-container wd-split">
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Why Chulbul Design</span>
                <h2 id="wordpress-why-title">WordPress Decisions Explained in Business Language</h2>
                <p>Your team should understand why a plugin, custom feature or hosting change is being recommended. We keep technical choices connected to customer experience, editing effort, risk and long-term cost. Scope, responsibilities and review points are visible before launch.</p>
                <p>Our Gurugram-based team serves businesses in India and international markets through planned remote collaboration. We agree meeting windows, milestones and feedback channels at the start without claiming offices where we do not have them.</p>
            </div>
            <div class="wd-reasons wd-reveal">
                <div><i class="bi bi-columns-gap"></i><span><strong>Content-first planning</strong>Templates and fields reflect what the business really publishes.</span></div>
                <div><i class="bi bi-plugin"></i><span><strong>Controlled dependencies</strong>Plugins are selected for a defined purpose and clear ownership.</span></div>
                <div><i class="bi bi-person-check"></i><span><strong>Editor usability</strong>Non-technical teams can handle routine updates with confidence.</span></div>
                <div><i class="bi bi-life-preserver"></i><span><strong>Ongoing support</strong>Updates and improvements can continue after the initial release.</span></div>
            </div>
        </div>
    </section>

    <section class="wd-section wd-section-soft" aria-labelledby="wordpress-cost-title">
        <div class="wd-container wd-cost-grid">
            <div class="wd-heading wd-heading-left wd-reveal"><span class="wd-kicker">Planning the investment</span><h2 id="wordpress-cost-title">What Affects WordPress Development Cost?</h2><p>A clear estimate depends on the website your team needs—not only the number of pages.</p></div>
            <div class="wd-cost-list wd-reveal">
                <div><span>01</span><p><strong>Design and templates</strong>Custom visual direction, number of page types and responsive complexity.</p></div>
                <div><span>02</span><p><strong>Content structure</strong>Post types, reusable blocks, taxonomies, roles and editorial workflow.</p></div>
                <div><span>03</span><p><strong>Functionality</strong>Forms, membership, bookings, WooCommerce, multilingual content and custom plugins.</p></div>
                <div><span>04</span><p><strong>Migration and integrations</strong>Content volume, redirects, external systems, APIs and data cleanup.</p></div>
                <div><span>05</span><p><strong>Quality and support</strong>Accessibility, performance, security, testing, training and maintenance needs.</p></div>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="wordpress-related-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal"><span class="wd-kicker">Related expertise</span><h2 id="wordpress-related-title">Choose the Service That Matches Your Requirement</h2><p>WordPress is one option within a wider design, development and content-management capability.</p></div>
            <div class="wd-related-grid">
                <a class="wd-related-card wd-reveal" href="<?= $base ?>/web-design"><i class="bi bi-palette2"></i><span><strong>Web Design</strong>Customer journeys and custom visual interfaces.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a>
                <a class="wd-related-card wd-reveal" href="<?= $base ?>/web-development"><i class="bi bi-code-slash"></i><span><strong>Web Development</strong>Custom web applications, portals and backend systems.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a>
                <a class="wd-related-card wd-reveal" href="<?= $base ?>/cms-development"><i class="bi bi-layout-text-window"></i><span><strong>CMS Development</strong>WordPress alternatives and broader content platforms.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a>
                <a class="wd-related-card wd-reveal" href="<?= $base ?>/website-maintenance"><i class="bi bi-tools"></i><span><strong>Website Maintenance</strong>Planned updates, testing and technical support.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a>
            </div>
        </div>
    </section>

    <section class="wd-section wd-faq" aria-labelledby="wordpress-faq-title">
        <div class="wd-container wd-faq-grid">
            <div class="wd-heading wd-heading-left wd-reveal"><span class="wd-kicker">Frequently asked questions</span><h2 id="wordpress-faq-title">WordPress Development FAQs</h2><p>Clear answers for businesses planning a new WordPress website, redesign or technical improvement.</p><a class="wd-text-link" href="<?= $base ?>/contact-us">Ask a project-specific question <i class="bi bi-arrow-right"></i></a></div>
            <div class="wd-accordion wd-reveal">
                <?php foreach ($faqs as $index => $faq): ?>
                <details<?= $index === 0 ? ' open' : '' ?>><summary><?= cbd_escape_text($faq['q']) ?><i class="bi bi-plus-lg" aria-hidden="true"></i></summary><p><?= cbd_escape_text($faq['a']) ?></p></details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-final-cta">
        <div class="wd-container wd-reveal">
            <span class="wd-kicker">Planning a WordPress website?</span>
            <h2>Let’s Define the Right Build Before Adding Another Plugin.</h2>
            <p>Share your content, current website and required functionality. We will help you identify a practical WordPress scope and the next step.</p>
            <div class="wd-actions">
                <a class="wd-button wd-button-white" href="<?= $base ?>/contact-us"><i class="bi bi-envelope-fill" aria-hidden="true"></i> Request a Project Discussion</a>
                <a class="wd-button wd-button-outline" href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%20want%20to%20discuss%20a%20WordPress%20development%20project." target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> Chat on WhatsApp</a>
            </div>
        </div>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var items = document.querySelectorAll('.wd-reveal');
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
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
