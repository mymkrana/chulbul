<?php
require_once __DIR__ . '/includes/http.php';
require_once __DIR__ . '/includes/seo.php';

$cbd_page_has_charset = true;
$page_styles = ['/assets/css/web-development.css'];

if (cbd_is_direct_script_request('cms-development.php')) {
    cbd_redirect_path('/cms-development');
}

$base = cbd_base_path();
$canonical = cbd_canonical_url('/cms-development');
$page_robots = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
$pageTitle = 'CMS Development Company | Chulbul Design';
$pageDescription = 'Custom CMS development for scalable content platforms, editorial workflows, Joomla, headless CMS, migrations, multisite and API integrations.';
$shareImage = cbd_public_url('/assets/images/service-heroes/cms-development-hero.webp');

$cmsServices = [
    ['icon' => 'bi-window-stack', 'title' => 'Custom CMS Development', 'text' => 'Purpose-built administration and publishing tools for content structures that do not fit a standard website platform.'],
    ['icon' => 'bi-diagram-3', 'title' => 'Headless CMS Development', 'text' => 'API-first content platforms that can deliver approved content to websites, apps, portals and other digital channels.'],
    ['icon' => 'bi-grid-1x2', 'title' => 'Joomla Development', 'text' => 'Structured Joomla websites, extensions, templates and migrations for teams that need granular content and access control.'],
    ['icon' => 'bi-bezier2', 'title' => 'Content Architecture', 'text' => 'Content models, taxonomies, relationships and reusable fields designed around how your organization creates information.'],
    ['icon' => 'bi-person-check', 'title' => 'Editorial Workflows', 'text' => 'Draft, review, approval, scheduling and publishing stages with roles that match real editorial responsibilities.'],
    ['icon' => 'bi-globe2', 'title' => 'Multisite & Multilingual CMS', 'text' => 'Governed content systems for multiple brands, regions, languages or departments without uncontrolled duplication.'],
    ['icon' => 'bi-braces-asterisk', 'title' => 'CMS API & Integrations', 'text' => 'Connect content with CRM, DAM, search, translation, analytics and other documented business systems.', 'href' => '/api-integration', 'link' => 'Explore API integration'],
    ['icon' => 'bi-arrow-left-right', 'title' => 'CMS Migration', 'text' => 'Move structured content, media, users and important URLs with mapping, validation, redirects and launch checks.', 'href' => '/website-migration', 'link' => 'Plan a CMS migration'],
    ['icon' => 'bi-shield-lock', 'title' => 'Roles & Permissions', 'text' => 'Practical access rules that separate authors, reviewers, publishers and administrators without blocking routine work.'],
    ['icon' => 'bi-tools', 'title' => 'CMS Modernization & Support', 'text' => 'Improve outdated CMS code, editor usability, security, performance and maintainability through a planned roadmap.', 'href' => '/website-maintenance', 'link' => 'View maintenance support'],
];

$solutionTypes = [
    ['icon' => 'bi-building', 'title' => 'Corporate Content Platforms', 'text' => 'Services, teams, locations, resources and governance for growing organizations.'],
    ['icon' => 'bi-newspaper', 'title' => 'Publishing & News', 'text' => 'High-volume articles, sections, authors, reviews, schedules and archive discovery.'],
    ['icon' => 'bi-journal-bookmark', 'title' => 'Knowledge Bases', 'text' => 'Structured guides, documentation, search and ownership for customer or internal support.'],
    ['icon' => 'bi-people', 'title' => 'Member & Customer Portals', 'text' => 'Role-aware content, protected resources, profiles and connected account journeys.'],
    ['icon' => 'bi-mortarboard', 'title' => 'Education Platforms', 'text' => 'Programs, faculty, admissions, resources and distributed departmental editing.'],
    ['icon' => 'bi-heart-pulse', 'title' => 'Healthcare Content', 'text' => 'Governed services, clinicians, locations and reviewed health information.'],
    ['icon' => 'bi-translate', 'title' => 'Multilingual Websites', 'text' => 'Language ownership, translation states, regional variations and SEO-ready URLs.'],
    ['icon' => 'bi-pin-map', 'title' => 'Location Networks', 'text' => 'Controlled location, franchise or service-area content with reusable local data.'],
    ['icon' => 'bi-collection', 'title' => 'Multi-brand Ecosystems', 'text' => 'Shared components and governance with distinct brand experiences and permissions.'],
    ['icon' => 'bi-file-earmark-code', 'title' => 'Documentation Hubs', 'text' => 'Versioned product information, structured navigation and API-delivered technical content.'],
];

$faqs = [
    ['q' => 'What is custom CMS development?', 'a' => 'Custom CMS development creates a content-management experience around an organization’s specific content, permissions, workflows and integrations. It can involve extending an established platform or building focused administration tools when a standard configuration cannot support the requirement responsibly.'],
    ['q' => 'How is CMS development different from WordPress development?', 'a' => 'WordPress development focuses on the WordPress editor, themes, plugins and its ecosystem. CMS development is broader and may use Joomla, a headless CMS or a custom platform for complex content models, multiple channels, approval workflows or enterprise integrations.'],
    ['q' => 'When should we choose a headless CMS?', 'a' => 'A headless CMS can be useful when the same structured content must serve multiple websites, apps or devices, or when the frontend needs an independent technology lifecycle. It also adds architecture and operational complexity, so we recommend it only when the benefits justify that cost.'],
    ['q' => 'Can you develop and support Joomla websites?', 'a' => 'Yes. Joomla work can include templates, extensions, structured content, user permissions, upgrades, migrations, integrations and ongoing support. We first audit the current version and extensions before defining a safe scope.'],
    ['q' => 'Can you migrate content from our old CMS?', 'a' => 'Yes. A migration plan maps source fields to the new content model, identifies media and user requirements, preserves important URLs through redirects and validates content before launch. Automated scripts may be combined with editorial review where source data is inconsistent.'],
    ['q' => 'Can one CMS manage multiple websites or languages?', 'a' => 'Yes, when content ownership and reuse are planned carefully. We define what is shared, what is regional, who can publish each version and how URLs, translation status and search metadata are managed.'],
    ['q' => 'Can a CMS connect to our CRM, DAM or internal software?', 'a' => 'Yes, when the other system offers suitable APIs or data access. We document which system owns each field, how authentication works, what triggers synchronization and how errors or unavailable services are handled.'],
    ['q' => 'How do you make a CMS secure and maintainable?', 'a' => 'We use role-based access, validated input, supported dependencies, backups, update procedures, logging and clear deployment ownership. Security also depends on hosting, user practices and maintenance, so those responsibilities are included in planning.'],
    ['q' => 'How much does CMS development cost?', 'a' => 'Cost depends on content models, design templates, workflows, user roles, integrations, migration volume, languages, search, infrastructure and support. We provide a written scope after discovery instead of assigning one price to every CMS project.'],
    ['q' => 'How long does a CMS project take?', 'a' => 'A focused content website may take several weeks, while a multisite platform, large migration or custom editorial system can take months. The delivery plan is based on confirmed content, integration, approval and testing requirements.'],
];

$schemaOffers = [];
foreach ($cmsServices as $service) {
    $schemaOffers[] = ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => $service['title']]];
}

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Service',
            '@id' => $canonical . '#service',
            'name' => 'Custom CMS Development Services',
            'serviceType' => 'CMS Development',
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
            'hasOfferCatalog' => ['@type' => 'OfferCatalog', 'name' => 'CMS Development Services', 'itemListElement' => $schemaOffers],
        ],
        [
            '@type' => 'BreadcrumbList',
            '@id' => $canonical . '#breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => CBD_CANONICAL_ORIGIN . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Web Design & Development', 'item' => cbd_canonical_url('/web-design-development')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => 'CMS Development', 'item' => $canonical],
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
    <link rel="preload" as="image" href="<?= $base ?>/assets/images/service-heroes/cms-development-hero.webp" fetchpriority="high">
    <script type="application/ld+json"><?= cbd_json_ld($schema) ?></script>
    <?php require __DIR__ . '/includes/header.php'; ?>

<div class="wd-page">
    <section class="wd-hero" aria-labelledby="cms-page-title">
        <div class="wd-orb wd-orb-one" aria-hidden="true"></div>
        <div class="wd-orb wd-orb-two" aria-hidden="true"></div>
        <div class="wd-container wd-hero-grid">
            <div class="wd-hero-copy wd-reveal">
                <nav class="wd-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?= $base ?>/">Home</a><span aria-hidden="true">/</span>
                    <a href="<?= $base ?>/web-design-development">Web Design &amp; Development</a><span aria-hidden="true">/</span>
                    <span aria-current="page">CMS Development</span>
                </nav>
                <span class="wd-eyebrow"><i class="bi bi-window-stack" aria-hidden="true"></i> CMS Development Services</span>
                <h1 id="cms-page-title">CMS Development Company for <span>Structured, Manageable Content</span></h1>
                <p class="wd-hero-lead">We design and develop content-management systems for organizations that need structured publishing, clear editorial control and dependable integrations. From Joomla and headless CMS platforms to focused custom administration tools, every solution is shaped around how your team actually creates and governs content.</p>
                <div class="wd-actions">
                    <a class="wd-button wd-button-primary" href="<?= $base ?>/contact-us"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i> Discuss Your CMS Project</a>
                    <a class="wd-button wd-button-secondary" href="#cms-services">Explore CMS Services <i class="bi bi-arrow-down" aria-hidden="true"></i></a>
                </div>
                <ul class="wd-trust-list" aria-label="CMS development commitments">
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Structured content models</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Practical roles and approvals</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Multisite and multilingual planning</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Migration and integration support</li>
                </ul>
            </div>
            <div class="wd-hero-visual wd-reveal">
                <div class="wd-browser">
                    <div class="wd-browser-bar" aria-hidden="true"><span></span><span></span><span></span><b>your-content-platform.com</b></div>
                    <img src="<?= $base ?>/assets/images/service-heroes/cms-development-hero.webp" alt="Custom CMS development showing content models, editorial workflows, permissions and integrations" width="960" height="640" fetchpriority="high">
                    <div class="wd-code-chip wd-code-chip-one" aria-hidden="true"><i class="bi bi-diagram-3"></i><span>Structured content</span></div>
                    <div class="wd-code-chip wd-code-chip-two" aria-hidden="true"><i class="bi bi-person-check-fill"></i><span>Controlled publishing</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="wd-section wd-intro">
        <div class="wd-container wd-narrow wd-reveal">
            <span class="wd-kicker">Manage information, not page layouts</span>
            <h2>A Content Platform Built Around Your Publishing Operation</h2>
            <p>A useful CMS does more than place a text editor behind a website. It represents the content your organization owns, the people responsible for it and the channels where it appears. A service, location, author, policy or product should be stored as meaningful information—not copied into dozens of unrelated page-builder blocks.</p>
            <p>Chulbul Design starts by studying content types, relationships, publishing frequency and governance. We identify what must be reusable, what needs approval, what differs by language or brand and which systems already own important data. This creates a content model that editors can understand and developers can extend without repeatedly rebuilding the platform.</p>
            <p>The right technology follows those requirements. WordPress may be ideal for a straightforward marketing website, while Joomla, a headless CMS or focused custom tools can better support granular permissions, multiple frontends or specialized workflows. We explain that choice clearly and avoid adding architectural complexity that your team does not need.</p>
        </div>
    </section>

    <section class="wd-section wd-section-soft" id="cms-services" aria-labelledby="cms-services-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal"><span class="wd-kicker">CMS capabilities</span><h2 id="cms-services-title">Custom CMS Development Services</h2><p>Plan, build, migrate and improve the systems your team uses to create, review and distribute content.</p></div>
            <div class="wd-service-grid">
                <?php foreach ($cmsServices as $index => $service): ?>
                <article class="wd-service-card wd-reveal">
                    <span class="wd-card-number"><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <div class="wd-icon"><i class="bi <?= cbd_escape_text($service['icon']) ?>" aria-hidden="true"></i></div>
                    <h3><?= cbd_escape_text($service['title']) ?></h3><p><?= cbd_escape_text($service['text']) ?></p>
                    <?php if (!empty($service['href'])): ?><a href="<?= $base . cbd_escape_text($service['href']) ?>"><?= cbd_escape_text($service['link']) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a><?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-section">
        <div class="wd-container wd-split">
            <div class="wd-media-panel wd-reveal">
                <img src="<?= $base ?>/assets/images/cms.svg" alt="CMS content architecture and editorial workflow planning" width="900" height="725" loading="lazy">
                <div class="wd-media-caption"><i class="bi bi-bezier2" aria-hidden="true"></i><span>Content model → workflow → reusable delivery</span></div>
            </div>
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Content modelling and governance</span>
                <h2>Structure Content Once and Use It with Confidence</h2>
                <p>When information is stored as unstructured pages, every new channel, redesign or regional version creates duplication. A considered content model separates meaning from presentation. Teams can update a person, location or policy once and reuse that approved information wherever the platform needs it.</p>
                <p>Governance is designed at the same time. Authors should see the fields relevant to their work, reviewers should understand what changed and publishers should know what is ready. Labels, validation, ownership and sensible defaults reduce training time and prevent avoidable publishing errors.</p>
                <ul class="wd-check-list">
                    <li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Content types and relationships:</strong> model real entities instead of assembling every page manually.</span></li>
                    <li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Taxonomy and discovery:</strong> organize content for navigation, search, filtering and related information.</span></li>
                    <li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Editorial states:</strong> draft, review, approval, schedule, archive and revision handling reflect real responsibilities.</span></li>
                    <li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Reusable governance:</strong> validation, help text and permissions keep distributed teams aligned.</span></li>
                </ul>
            </div>
        </div>
    </section>

    <section class="wd-section wd-solutions" aria-labelledby="cms-solutions-title">
        <div class="wd-container">
            <div class="wd-heading wd-heading-light wd-reveal"><span class="wd-kicker">Built for different publishing models</span><h2 id="cms-solutions-title">CMS Solutions for Content-Heavy Organizations</h2><p>The platform should match who publishes, how information is reused and where customers need to find it.</p></div>
            <div class="wd-solution-grid">
                <?php foreach ($solutionTypes as $solution): ?>
                <article class="wd-solution-card wd-reveal"><i class="bi <?= cbd_escape_text($solution['icon']) ?>" aria-hidden="true"></i><h3><?= cbd_escape_text($solution['title']) ?></h3><p><?= cbd_escape_text($solution['text']) ?></p></article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="cms-foundation-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal"><span class="wd-kicker">A durable CMS foundation</span><h2 id="cms-foundation-title">Content, Administration and Delivery Working as One System</h2><p>A maintainable CMS separates these responsibilities while giving teams a clear view of how they connect.</p></div>
            <div class="wd-stack-grid">
                <article class="wd-stack-card wd-reveal"><span class="wd-stack-label">01 / Content</span><h3>A model that reflects the business</h3><p>Fields, relationships, taxonomies, revisions and validation describe your information independently of a single visual layout.</p><div class="wd-tags"><span>Content Types</span><span>Taxonomy</span><span>Media</span><span>Localization</span></div></article>
                <article class="wd-stack-card wd-reveal"><span class="wd-stack-label">02 / Administration</span><h3>A focused editor experience</h3><p>Dashboards, permissions, workflows and previews help each user complete the right task without exposing unnecessary controls.</p><div class="wd-tags"><span>Roles</span><span>Approvals</span><span>Preview</span><span>Audit Trail</span></div></article>
                <article class="wd-stack-card wd-reveal"><span class="wd-stack-label">03 / Delivery</span><h3>Content ready for every channel</h3><p>Templates or APIs deliver structured content to websites, applications and connected experiences with caching and error handling.</p><div class="wd-tags"><span>Frontend</span><span>REST API</span><span>Search</span><span>CDN</span></div></article>
            </div>
        </div>
    </section>

    <section class="wd-section wd-section-soft">
        <div class="wd-container wd-split wd-split-reverse">
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Connected content operations</span>
                <h2>Integrate the CMS Without Losing Data Ownership</h2>
                <p>A CMS rarely operates alone. Customer data may belong in a CRM, approved assets in a DAM, product information in an ERP and translations in a localization platform. We define which system owns each record before designing a connection, so editors do not have to guess where an update should happen.</p>
                <p>Each integration includes practical decisions about authentication, mapping, synchronization, retries, logging and failure states. This is especially important for headless and multisite platforms where one unavailable service can affect several customer experiences.</p>
                <div class="wd-integration-list">
                    <span><i class="bi bi-person-lines-fill"></i> CRM and customer data</span><span><i class="bi bi-images"></i> Digital asset management</span>
                    <span><i class="bi bi-search"></i> Site and enterprise search</span><span><i class="bi bi-translate"></i> Translation platforms</span>
                    <span><i class="bi bi-boxes"></i> Product and ERP data</span><span><i class="bi bi-graph-up"></i> Analytics and consent</span>
                </div>
                <a class="wd-text-link" href="<?= $base ?>/api-integration">Learn about API integration services <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="wd-api-visual wd-reveal" aria-label="Illustration of a CMS connected to business systems">
                <div class="wd-api-core"><i class="bi bi-window-stack" aria-hidden="true"></i><strong>CMS</strong></div>
                <div class="wd-api-node wd-api-node-one"><i class="bi bi-person-lines-fill"></i><span>CRM</span></div>
                <div class="wd-api-node wd-api-node-two"><i class="bi bi-images"></i><span>DAM</span></div>
                <div class="wd-api-node wd-api-node-three"><i class="bi bi-translate"></i><span>Languages</span></div>
                <div class="wd-api-node wd-api-node-four"><i class="bi bi-phone"></i><span>Channels</span></div>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="cms-quality-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal"><span class="wd-kicker">Quality across the platform</span><h2 id="cms-quality-title">Governance, Security, Performance and Accessibility</h2><p>A good editor interface is only one part of a dependable content platform.</p></div>
            <div class="wd-quality-grid">
                <article class="wd-quality-card wd-reveal"><i class="bi bi-person-check"></i><h3>Governance</h3><p>Clear ownership, approvals, revisions and publishing rules keep high-volume content trustworthy.</p></article>
                <article class="wd-quality-card wd-reveal"><i class="bi bi-shield-check"></i><h3>Security</h3><p>Role-based access, supported dependencies, validation, backups and logging reduce preventable risk.</p></article>
                <article class="wd-quality-card wd-reveal"><i class="bi bi-lightning-charge"></i><h3>Performance</h3><p>Efficient queries, caching, media handling and delivery architecture support editors and public users.</p></article>
                <article class="wd-quality-card wd-reveal"><i class="bi bi-universal-access"></i><h3>Accessibility</h3><p>Accessible administration and semantic public output help more people create and consume content.</p></article>
            </div>
        </div>
    </section>

    <section class="wd-section wd-process" aria-labelledby="cms-process-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal"><span class="wd-kicker">A clear CMS process</span><h2 id="cms-process-title">From Content Audit to Controlled Publishing</h2><p>Technology, migration and editorial adoption are planned together instead of treated as separate launch problems.</p></div>
            <ol class="wd-process-list">
                <li class="wd-reveal"><span>01</span><div><h3>Discovery &amp; Content Audit</h3><p>We review audiences, content volume, ownership, systems, pain points and future publishing goals.</p></div></li>
                <li class="wd-reveal"><span>02</span><div><h3>Content Model &amp; Governance</h3><p>Types, relationships, taxonomies, roles, approvals and lifecycle rules are documented before build.</p></div></li>
                <li class="wd-reveal"><span>03</span><div><h3>Platform &amp; Architecture</h3><p>WordPress, Joomla, headless or custom options are compared against requirements and operating cost.</p></div></li>
                <li class="wd-reveal"><span>04</span><div><h3>UX, Development &amp; Integration</h3><p>Editor interfaces, public components and external connections are built in reviewable stages.</p></div></li>
                <li class="wd-reveal"><span>05</span><div><h3>Migration, QA &amp; Training</h3><p>Content, permissions, URLs, devices and publishing workflows are validated with the people who use them.</p></div></li>
                <li class="wd-reveal"><span>06</span><div><h3>Launch &amp; Improvement</h3><p>Backups, redirects, monitoring and production settings are checked before ongoing support begins.</p></div></li>
            </ol>
        </div>
    </section>

    <section class="wd-section wd-proof" aria-labelledby="cms-proof-title">
        <div class="wd-container wd-proof-grid">
            <div class="wd-proof-copy wd-reveal">
                <span class="wd-kicker">Review relevant work</span>
                <h2 id="cms-proof-title">CMS and Content Platform Experience You Can Review</h2>
                <p>Our portfolio includes digital delivery experience connected with Gurjar Pragati Manch, Kisaan Jee and Tera Ghar. Relevant work helps demonstrate how structured content, navigation, editorial responsibilities and responsive presentation can operate as one manageable system.</p>
                <p>During a consultation, we explain our role and review decisions related to content models, publishing workflows, permissions, integrations and migration planning. For broader engineering requirements, Chulbul Design can also collaborate with SoftLes as a technology partner.</p>
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

    <section class="wd-section" aria-labelledby="cms-why-title">
        <div class="wd-container wd-split">
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Why Chulbul Design</span>
                <h2 id="cms-why-title">CMS Decisions Explained Beyond the Technology Label</h2>
                <p>Choosing a CMS affects editors, developers, infrastructure and future channels. We connect platform recommendations to publishing effort, governance, integration risk and long-term ownership. Your team sees the tradeoffs before a technology decision becomes difficult to reverse.</p>
                <p>Our Gurugram-based team serves businesses in India and international markets through planned remote collaboration. We agree meeting windows, milestones and feedback channels at the start without claiming offices where we do not have them.</p>
            </div>
            <div class="wd-reasons wd-reveal">
                <div><i class="bi bi-bezier2"></i><span><strong>Content-first architecture</strong>The model reflects real information and publishing responsibilities.</span></div>
                <div><i class="bi bi-signpost-split"></i><span><strong>Platform-neutral advice</strong>Technology follows requirements instead of a default product preference.</span></div>
                <div><i class="bi bi-person-check"></i><span><strong>Editor usability</strong>Workflows are tested with the people responsible for daily publishing.</span></div>
                <div><i class="bi bi-life-preserver"></i><span><strong>Long-term support</strong>Migration, maintenance and improvement remain part of the ownership plan.</span></div>
            </div>
        </div>
    </section>

    <section class="wd-section wd-section-soft" aria-labelledby="cms-cost-title">
        <div class="wd-container wd-cost-grid">
            <div class="wd-heading wd-heading-left wd-reveal"><span class="wd-kicker">Planning the investment</span><h2 id="cms-cost-title">What Affects CMS Development Cost?</h2><p>The estimate depends on content operations and integrations—not only the number of public pages.</p></div>
            <div class="wd-cost-list wd-reveal">
                <div><span>01</span><p><strong>Content architecture</strong>Types, relationships, taxonomies, search and reuse across channels.</p></div>
                <div><span>02</span><p><strong>Workflow and access</strong>Roles, approvals, revisions, scheduling, audit history and governance.</p></div>
                <div><span>03</span><p><strong>Platform and interfaces</strong>CMS choice, custom administration, frontend templates and preview requirements.</p></div>
                <div><span>04</span><p><strong>Migration and integrations</strong>Source quality, media, users, redirects, APIs and external data ownership.</p></div>
                <div><span>05</span><p><strong>Scale and support</strong>Languages, sites, traffic, infrastructure, security, training and maintenance.</p></div>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="cms-related-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal"><span class="wd-kicker">Related expertise</span><h2 id="cms-related-title">Choose the Content Platform Service That Fits</h2><p>A dedicated CMS is one option within a wider website, application and publishing capability.</p></div>
            <div class="wd-related-grid">
                <a class="wd-related-card wd-reveal" href="<?= $base ?>/wordpress-development"><i class="bi bi-wordpress"></i><span><strong>WordPress Development</strong>Custom themes, plugins, WooCommerce and manageable business websites.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a>
                <a class="wd-related-card wd-reveal" href="<?= $base ?>/web-development"><i class="bi bi-code-slash"></i><span><strong>Web Development</strong>Custom web applications, portals, APIs and backend systems.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a>
                <a class="wd-related-card wd-reveal" href="<?= $base ?>/website-migration"><i class="bi bi-arrow-left-right"></i><span><strong>Website Migration</strong>Content mapping, URL preservation, validation and controlled launch.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a>
                <a class="wd-related-card wd-reveal" href="<?= $base ?>/website-maintenance"><i class="bi bi-tools"></i><span><strong>CMS Maintenance</strong>Planned updates, monitoring and technical support after launch.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a>
            </div>
        </div>
    </section>

    <section class="wd-section wd-faq" aria-labelledby="cms-faq-title">
        <div class="wd-container wd-faq-grid">
            <div class="wd-heading wd-heading-left wd-reveal"><span class="wd-kicker">Frequently asked questions</span><h2 id="cms-faq-title">CMS Development FAQs</h2><p>Clear answers for teams planning a new content platform, migration or modernization project.</p><a class="wd-text-link" href="<?= $base ?>/contact-us">Ask a project-specific question <i class="bi bi-arrow-right"></i></a></div>
            <div class="wd-accordion wd-reveal">
                <?php foreach ($faqs as $index => $faq): ?>
                <details<?= $index === 0 ? ' open' : '' ?>><summary><?= cbd_escape_text($faq['q']) ?><i class="bi bi-plus-lg" aria-hidden="true"></i></summary><p><?= cbd_escape_text($faq['a']) ?></p></details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-final-cta">
        <div class="wd-container wd-reveal">
            <span class="wd-kicker">Planning a content platform?</span>
            <h2>Let’s Define the Content Model Before Choosing the CMS.</h2>
            <p>Share your current platform, publishing workflow and integration requirements. We will help you identify a practical CMS scope and the next step.</p>
            <div class="wd-actions">
                <a class="wd-button wd-button-white" href="<?= $base ?>/contact-us"><i class="bi bi-envelope-fill" aria-hidden="true"></i> Request a Project Discussion</a>
                <a class="wd-button wd-button-outline" href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%20want%20to%20discuss%20a%20CMS%20development%20project." target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> Chat on WhatsApp</a>
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
