<?php
require_once __DIR__ . '/includes/http.php';
require_once __DIR__ . '/includes/seo.php';

$cbd_page_has_charset = true;
$page_styles = ['/assets/css/web-development.css'];

if (cbd_is_direct_script_request('web-development.php')) {
    cbd_redirect_path('/web-development');
}

$base = cbd_base_path();
$canonical = cbd_canonical_url('/web-development');
$page_robots = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
$pageTitle = 'Custom Web Development Company | Chulbul Design';
$pageDescription = 'Custom web development company for secure websites, web apps, portals, CMS platforms and API integrations built for performance and scalable growth.';
$shareImage = cbd_public_url('/assets/images/service-heroes/web-development-hero.webp');

$developmentServices = [
    ['icon' => 'bi-code-square', 'title' => 'Custom Web Development', 'text' => 'Purpose-built websites and digital platforms shaped around your workflows, users and commercial goals—not forced into a pre-made template.'],
    ['icon' => 'bi-wordpress', 'title' => 'WordPress Development', 'text' => 'Custom WordPress builds with manageable content, lean themes, practical plugins and a clean foundation for long-term maintenance.', 'href' => '/wordpress-development', 'link' => 'Explore WordPress development'],
    ['icon' => 'bi-window-stack', 'title' => 'Frontend Development', 'text' => 'Responsive interfaces built with semantic HTML, modern CSS, JavaScript and React where interactive application behavior is required.', 'href' => '/reactjs-development', 'link' => 'Explore React development'],
    ['icon' => 'bi-hdd-rack', 'title' => 'Backend Development', 'text' => 'Reliable server-side systems for business rules, authentication, content, payments, notifications and secure data processing.'],
    ['icon' => 'bi-grid-1x2', 'title' => 'Web Application Development', 'text' => 'Browser-based applications for teams and customers, including role-based access, workflows, dashboards and operational tools.'],
    ['icon' => 'bi-person-workspace', 'title' => 'Customer & Business Portals', 'text' => 'Secure self-service portals that help users manage accounts, requests, documents, bookings, orders or support from one place.'],
    ['icon' => 'bi-layout-text-window-reverse', 'title' => 'CMS Development', 'text' => 'Flexible content systems for businesses that need structured publishing, permissions and easier day-to-day updates.', 'href' => '/cms-development', 'link' => 'View CMS solutions'],
    ['icon' => 'bi-plug', 'title' => 'API Development & Integration', 'text' => 'Connect your website with payment, CRM, logistics, messaging or internal systems through carefully designed API workflows.', 'href' => '/api-integration', 'link' => 'Explore API integration'],
    ['icon' => 'bi-speedometer2', 'title' => 'Dashboard Development', 'text' => 'Clear operational dashboards that turn business data into usable views, filters, reports and role-specific actions.'],
    ['icon' => 'bi-arrow-repeat', 'title' => 'Migration & Modernization', 'text' => 'Move ageing websites and applications to a maintainable stack while protecting essential content, URLs and business continuity.', 'href' => '/website-migration', 'link' => 'Plan a website migration'],
];

$solutionTypes = [
    ['icon' => 'bi-building', 'title' => 'Corporate Websites', 'text' => 'Structured, credible websites for established companies, service teams and multi-department organizations.'],
    ['icon' => 'bi-briefcase', 'title' => 'B2B Service Platforms', 'text' => 'Lead journeys, service explainers and gated resources designed around longer business buying decisions.'],
    ['icon' => 'bi-people', 'title' => 'Customer Portals', 'text' => 'Account areas for requests, files, service history, approvals, payments or support communication.'],
    ['icon' => 'bi-calendar-check', 'title' => 'Booking Systems', 'text' => 'Availability, scheduling, confirmations and administrative controls adapted to your operating model.'],
    ['icon' => 'bi-diagram-3', 'title' => 'Marketplace Workflows', 'text' => 'Multi-role listing, enquiry and transaction flows for platforms that connect different user groups.'],
    ['icon' => 'bi-file-earmark-lock', 'title' => 'Member Platforms', 'text' => 'Secure content, subscriptions, permissions and account management for communities or professional networks.'],
    ['icon' => 'bi-clipboard-data', 'title' => 'Internal Operations Tools', 'text' => 'Focused web tools that replace repetitive spreadsheets, fragmented forms and manual hand-offs.'],
    ['icon' => 'bi-mortarboard', 'title' => 'Learning Portals', 'text' => 'Course, resource and progress experiences built around the needs of learners and administrators.'],
    ['icon' => 'bi-newspaper', 'title' => 'Publishing Platforms', 'text' => 'Structured editorial workflows for news, resources, guides and frequently updated content libraries.'],
    ['icon' => 'bi-bar-chart-line', 'title' => 'Reporting Dashboards', 'text' => 'Useful views of business data with filters, permissions, exports and decision-ready summaries.'],
];

$faqs = [
    ['q' => 'What does a web development company do?', 'a' => 'A web development company plans and builds the technical system behind a website or web application. This may include frontend interfaces, backend logic, databases, content management, integrations, authentication, testing, deployment and ongoing technical support.'],
    ['q' => 'What is the difference between web design and web development?', 'a' => 'Web design defines how a digital experience looks, feels and guides users. Web development turns that experience into a working product through code, data, integrations and technical architecture. Most successful projects need both disciplines to work together.'],
    ['q' => 'Do you build custom websites without templates?', 'a' => 'Yes. When a project needs unique workflows, stronger performance or a distinctive interface, we build around its requirements rather than forcing it into a generic theme. For simpler content projects, we may recommend a carefully developed CMS solution when that is the better business fit.'],
    ['q' => 'Which technologies do you use for web development?', 'a' => 'Our confirmed development stack includes HTML, CSS, JavaScript, React JS, PHP, Laravel, Node.js, WordPress and Joomla. The final stack is selected after reviewing the product requirements, integrations, editing needs, hosting environment and maintenance plan.'],
    ['q' => 'Can you develop a custom web application or customer portal?', 'a' => 'Yes. We can build role-based web applications, customer portals, internal dashboards, booking workflows and other browser-based systems. Discovery is used to map users, permissions, data, integrations and the minimum useful first release.'],
    ['q' => 'Can you integrate payments, CRM or third-party APIs?', 'a' => 'Yes. We work with documented APIs for payments, CRM, messaging, analytics, shipping and other business services. Before development, we confirm API availability, access requirements, rate limits, webhooks, error handling and data-security responsibilities.'],
    ['q' => 'How much does custom web development cost?', 'a' => 'Cost depends on scope, number of user roles, interface complexity, content migration, integrations, data structure, security requirements and testing. After discovery, we provide a written scope with milestones so the estimate is tied to defined deliverables.'],
    ['q' => 'How long does a web development project take?', 'a' => 'A focused business website can take a few weeks, while portals and custom applications usually require a longer phased delivery. Timing depends on requirements, content readiness, integrations and approval speed. We confirm a practical schedule after discovery rather than promising an arbitrary launch date.'],
    ['q' => 'Will the website be secure, fast and mobile-friendly?', 'a' => 'Those requirements are considered throughout architecture, development and testing. We use responsive layouts, optimized assets, clean code, access controls, input validation and deployment checks. Final performance also depends on hosting, third-party scripts and ongoing content practices.'],
    ['q' => 'Do you provide maintenance after launch?', 'a' => 'Yes. Support can cover monitored updates, backups, bug fixes, performance checks, small improvements and planned feature releases. The appropriate maintenance arrangement depends on the stack and how business-critical the system is.'],
];

$schemaServices = [];
foreach ($developmentServices as $service) {
    $schemaServices[] = [
        '@type' => 'Offer',
        'itemOffered' => ['@type' => 'Service', 'name' => $service['title']],
    ];
}

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Service',
            '@id' => $canonical . '#service',
            'name' => 'Custom Web Development Services',
            'serviceType' => 'Web Development',
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
            'hasOfferCatalog' => [
                '@type' => 'OfferCatalog',
                'name' => 'Web Development Services',
                'itemListElement' => $schemaServices,
            ],
        ],
        [
            '@type' => 'BreadcrumbList',
            '@id' => $canonical . '#breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => CBD_CANONICAL_ORIGIN . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Web Design & Development', 'item' => cbd_canonical_url('/web-design-development')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => 'Web Development', 'item' => $canonical],
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
    <link rel="preload" as="image" href="<?= $base ?>/assets/images/service-heroes/web-development-hero.webp" fetchpriority="high">
    <script type="application/ld+json"><?= cbd_json_ld($schema) ?></script>
    <?php require __DIR__ . '/includes/header.php'; ?>

<div class="wd-page">
    <section class="wd-hero" aria-labelledby="wd-page-title">
        <div class="wd-orb wd-orb-one" aria-hidden="true"></div>
        <div class="wd-orb wd-orb-two" aria-hidden="true"></div>
        <div class="wd-container wd-hero-grid">
            <div class="wd-hero-copy wd-reveal">
                <nav class="wd-breadcrumb" aria-label="Breadcrumb">
                    <a href="<?= $base ?>/">Home</a><span aria-hidden="true">/</span>
                    <a href="<?= $base ?>/web-design-development">Web Design &amp; Development</a><span aria-hidden="true">/</span>
                    <span aria-current="page">Web Development</span>
                </nav>
                <span class="wd-eyebrow"><i class="bi bi-braces" aria-hidden="true"></i> Web Development Services</span>
                <h1 id="wd-page-title">Web Development Company for <span>Scalable Business Platforms</span></h1>
                <p class="wd-hero-lead">We develop secure, responsive websites, web applications and business platforms around the way your organization actually works. From a focused company website to a portal with custom roles, data and integrations, every build starts with clear requirements and ends with a maintainable technical foundation.</p>
                <div class="wd-actions">
                    <a class="wd-button wd-button-primary" href="<?= $base ?>/contact-us"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i> Discuss Your Project</a>
                    <a class="wd-button wd-button-secondary" href="#development-services">Explore Development Services <i class="bi bi-arrow-down" aria-hidden="true"></i></a>
                </div>
                <ul class="wd-trust-list" aria-label="Development commitments">
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Requirement-led architecture</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Responsive, accessible interfaces</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Secure development practices</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Clear handover and support</li>
                </ul>
            </div>
            <div class="wd-hero-visual wd-reveal">
                <div class="wd-browser">
                    <div class="wd-browser-bar" aria-hidden="true"><span></span><span></span><span></span><b>your-product.com</b></div>
                    <img src="<?= $base ?>/assets/images/service-heroes/web-development-hero.webp" alt="Custom web development architecture showing frontend, backend, API and database components" width="960" height="640" fetchpriority="high">
                    <div class="wd-code-chip wd-code-chip-one" aria-hidden="true"><i class="bi bi-code-slash"></i><span>Clean code</span></div>
                    <div class="wd-code-chip wd-code-chip-two" aria-hidden="true"><i class="bi bi-lightning-charge-fill"></i><span>Built to perform</span></div>
                </div>
            </div>
        </div>
    </section>

    <section class="wd-section wd-intro">
        <div class="wd-container wd-narrow wd-reveal">
            <span class="wd-kicker">Development that solves the right problem</span>
            <h2>A Working Digital Product, Not Just a Collection of Pages</h2>
            <p>A website can look polished and still fail when its technical foundation is difficult to update, slow under real traffic or disconnected from the systems a business uses every day. Web development covers the code, data, integrations and architecture that make the experience work. It is where approved designs become responsive interfaces, content becomes manageable, forms reach the correct teams and business rules behave predictably.</p>
            <p>Chulbul Design approaches development as a product decision. We first identify the users, actions, content, permissions and integrations involved. That lets us recommend a suitable build instead of adding complexity for its own sake. A straightforward service website may need a lean content management system. A customer portal may require authentication, role-based access, structured data and an API layer. Both deserve the same attention to clarity, testing and maintainability.</p>
            <p>Our work combines frontend and backend development with practical communication. You see the scope, milestones and review points before launch. The result is a digital platform your users can navigate, your team can operate and future developers can understand.</p>
        </div>
    </section>

    <section class="wd-section wd-section-soft" id="development-services" aria-labelledby="development-services-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal">
                <span class="wd-kicker">Core capabilities</span>
                <h2 id="development-services-title">Web Development Services Built Around Your Requirements</h2>
                <p>Choose a focused service or combine the capabilities needed for a complete website, application or platform.</p>
            </div>
            <div class="wd-service-grid">
                <?php foreach ($developmentServices as $index => $service): ?>
                <article class="wd-service-card wd-reveal">
                    <span class="wd-card-number"><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <div class="wd-icon"><i class="bi <?= cbd_escape_text($service['icon']) ?>" aria-hidden="true"></i></div>
                    <h3><?= cbd_escape_text($service['title']) ?></h3>
                    <p><?= cbd_escape_text($service['text']) ?></p>
                    <?php if (!empty($service['href'])): ?>
                    <a href="<?= $base . cbd_escape_text($service['href']) ?>"><?= cbd_escape_text($service['link']) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-section">
        <div class="wd-container wd-split">
            <div class="wd-media-panel wd-reveal">
                <img src="<?= $base ?>/assets/images/built-around-your-requirements.webp" alt="Custom web development planned around business requirements" width="720" height="620" loading="lazy">
                <div class="wd-media-caption"><i class="bi bi-diagram-3-fill" aria-hidden="true"></i><span>Requirements → architecture → tested release</span></div>
            </div>
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Custom development</span>
                <h2>When Off-the-Shelf Software Does Not Match Your Workflow</h2>
                <p>Templates and ready-made tools are useful when a requirement is standard. They become limiting when your users need a specific journey, your team follows a unique process or several systems need to exchange data. In those situations, custom web development creates the right behavior without forcing people to work around the software.</p>
                <p>Discovery begins with the business flow: who uses the system, what each role can see, which action starts the process and what must happen next. We translate that flow into a practical technical plan covering interface states, data relationships, permissions, integrations and failure handling. The first release is scoped around the smallest version that can deliver genuine value, with later improvements kept visible on a roadmap.</p>
                <ul class="wd-check-list">
                    <li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Clear product scope:</strong> user roles, workflows, data and acceptance criteria documented before build.</span></li>
                    <li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Maintainable architecture:</strong> organized code and reusable components suited to the expected life of the product.</span></li>
                    <li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Phased delivery:</strong> complex features divided into reviewable releases instead of one risky final handover.</span></li>
                    <li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Operational readiness:</strong> deployment, content ownership, access and post-launch support considered from the start.</span></li>
                </ul>
            </div>
        </div>
    </section>

    <section class="wd-section wd-solutions" aria-labelledby="solutions-title">
        <div class="wd-container">
            <div class="wd-heading wd-heading-light wd-reveal">
                <span class="wd-kicker">Solution types</span>
                <h2 id="solutions-title">Web Solutions for Customer Experiences and Business Operations</h2>
                <p>The right format depends on the job users need to complete. These are common starting points, not fixed packages.</p>
            </div>
            <div class="wd-solution-grid">
                <?php foreach ($solutionTypes as $solution): ?>
                <article class="wd-solution-card wd-reveal">
                    <i class="bi <?= cbd_escape_text($solution['icon']) ?>" aria-hidden="true"></i>
                    <h3><?= cbd_escape_text($solution['title']) ?></h3>
                    <p><?= cbd_escape_text($solution['text']) ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="stack-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal">
                <span class="wd-kicker">Full-stack capability</span>
                <h2 id="stack-title">Frontend, Backend and CMS Development Working as One System</h2>
                <p>A stable product needs more than a fashionable framework. We select technologies according to user experience, business logic, integrations, hosting and the people who will maintain the platform.</p>
            </div>
            <div class="wd-stack-grid">
                <article class="wd-stack-card wd-reveal">
                    <span class="wd-stack-label">01 / Frontend</span>
                    <h3>Interfaces people can understand and use</h3>
                    <p>Frontend development controls what users see and how the product responds. We use semantic HTML, custom CSS and JavaScript for clear, responsive experiences, adding React when reusable components and richer application states justify it.</p>
                    <div class="wd-tags"><span>HTML</span><span>CSS</span><span>JavaScript</span><span>React JS</span></div>
                </article>
                <article class="wd-stack-card wd-reveal">
                    <span class="wd-stack-label">02 / Backend</span>
                    <h3>Business logic that behaves predictably</h3>
                    <p>The backend manages data, permissions, forms, integrations and rules. PHP, Laravel and Node.js are used where they fit the application, team and deployment environment—not simply because a technology is popular.</p>
                    <div class="wd-tags"><span>PHP</span><span>Laravel</span><span>Node.js</span><span>REST APIs</span></div>
                </article>
                <article class="wd-stack-card wd-reveal">
                    <span class="wd-stack-label">03 / Content systems</span>
                    <h3>Content your team can manage safely</h3>
                    <p>For content-led sites, WordPress or Joomla can provide practical publishing tools. We structure fields, permissions and reusable components so editors can work efficiently without damaging layout or creating inconsistent pages.</p>
                    <div class="wd-tags"><span>WordPress</span><span>Joomla</span><span>Custom CMS</span><span>Editor UX</span></div>
                </article>
            </div>
        </div>
    </section>

    <section class="wd-section wd-section-soft">
        <div class="wd-container wd-split wd-split-reverse">
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Connected systems</span>
                <h2>API Development and Integration Without Fragile Workarounds</h2>
                <p>Modern websites rarely operate alone. A lead may need to enter a CRM, a successful payment may need to update an order, or a booking may need to trigger confirmation messages. API integration connects those steps so information moves accurately and teams spend less time copying data between tools.</p>
                <p>Before coding, we confirm the provider's documentation, authentication method, available endpoints and operational limits. We then define what should happen when a request succeeds, fails or arrives twice. This error-aware approach matters because a connection that works only during a perfect demo is not ready for real customers.</p>
                <div class="wd-integration-list">
                    <span><i class="bi bi-credit-card" aria-hidden="true"></i> Payment workflows</span>
                    <span><i class="bi bi-person-lines-fill" aria-hidden="true"></i> CRM and lead routing</span>
                    <span><i class="bi bi-truck" aria-hidden="true"></i> Shipping and logistics</span>
                    <span><i class="bi bi-chat-left-text" aria-hidden="true"></i> Messaging and notifications</span>
                    <span><i class="bi bi-graph-up" aria-hidden="true"></i> Analytics and reporting</span>
                    <span><i class="bi bi-box-arrow-in-down" aria-hidden="true"></i> Internal data exchange</span>
                </div>
                <a class="wd-text-link" href="<?= $base ?>/api-integration">Learn about API integration services <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            </div>
            <div class="wd-api-visual wd-reveal" aria-label="Illustration of connected business systems">
                <div class="wd-api-core"><i class="bi bi-braces-asterisk" aria-hidden="true"></i><strong>Your Platform</strong></div>
                <div class="wd-api-node wd-api-node-one"><i class="bi bi-credit-card"></i><span>Payments</span></div>
                <div class="wd-api-node wd-api-node-two"><i class="bi bi-person-lines-fill"></i><span>CRM</span></div>
                <div class="wd-api-node wd-api-node-three"><i class="bi bi-bar-chart"></i><span>Analytics</span></div>
                <div class="wd-api-node wd-api-node-four"><i class="bi bi-chat-square-text"></i><span>Messaging</span></div>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="quality-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal">
                <span class="wd-kicker">Engineering quality</span>
                <h2 id="quality-title">Performance, Security and Scalability Are Build Decisions</h2>
                <p>They cannot be reliably added as decoration at the end. We address them during planning, implementation and release.</p>
            </div>
            <div class="wd-quality-grid">
                <article class="wd-quality-card wd-reveal"><i class="bi bi-lightning-charge"></i><h3>Performance</h3><p>Responsive image delivery, sensible asset loading, efficient code and reduced dependency weight help pages become usable sooner. We test key templates and investigate bottlenecks instead of relying on a single score.</p></article>
                <article class="wd-quality-card wd-reveal"><i class="bi bi-shield-check"></i><h3>Security</h3><p>Validation, access control, secure configuration and careful handling of credentials reduce avoidable risk. Security responsibilities are documented across the application, hosting and third-party services.</p></article>
                <article class="wd-quality-card wd-reveal"><i class="bi bi-arrows-angle-expand"></i><h3>Scalability</h3><p>Reusable components, structured data and separation of concerns make future features easier to plan. We avoid premature complexity while keeping likely growth paths visible in the architecture.</p></article>
                <article class="wd-quality-card wd-reveal"><i class="bi bi-universal-access"></i><h3>Accessibility</h3><p>Semantic structure, keyboard access, labels, focus states, contrast and meaningful interaction feedback help more people use the product and improve overall interface quality.</p></article>
            </div>
        </div>
    </section>

    <section class="wd-section wd-process" aria-labelledby="process-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal">
                <span class="wd-kicker">A visible delivery process</span>
                <h2 id="process-title">From Requirements to a Tested Web Product</h2>
                <p>Each stage produces something reviewable, keeping decisions clear and reducing expensive surprises near launch.</p>
            </div>
            <ol class="wd-process-list">
                <li class="wd-reveal"><span>01</span><div><h3>Discovery &amp; Requirements</h3><p>We clarify audiences, user roles, business goals, content, integrations, constraints and what success should look like.</p></div></li>
                <li class="wd-reveal"><span>02</span><div><h3>Technical Planning</h3><p>The solution is divided into features, data, architecture, milestones and acceptance criteria with risks made visible.</p></div></li>
                <li class="wd-reveal"><span>03</span><div><h3>UX &amp; Interface Alignment</h3><p>Approved flows and designs are checked against real states, responsive behavior and development requirements.</p></div></li>
                <li class="wd-reveal"><span>04</span><div><h3>Iterative Development</h3><p>Frontend, backend and integrations are built in reviewable increments so feedback arrives while change is manageable.</p></div></li>
                <li class="wd-reveal"><span>05</span><div><h3>Quality Assurance</h3><p>We test supported devices, forms, permissions, links, content states, integrations and important failure paths.</p></div></li>
                <li class="wd-reveal"><span>06</span><div><h3>Launch &amp; Support</h3><p>Deployment, analytics, redirects, access and handover are checked before the project moves into support or planned iteration.</p></div></li>
            </ol>
        </div>
    </section>

    <section class="wd-section wd-proof" aria-labelledby="proof-title">
        <div class="wd-container wd-proof-grid">
            <div class="wd-proof-copy wd-reveal">
                <span class="wd-kicker">Real delivery experience</span>
                <h2 id="proof-title">Relevant Web Development Work and Delivery Experience</h2>
                <p>Our portfolio includes digital delivery experience connected with Gurjar Pragati Manch, Kisaan Jee and Tera Ghar. Relevant work can demonstrate how business requirements are translated into responsive interfaces, structured content, practical administration and maintainable digital systems.</p>
                <p>During a project discussion, we explain our role and walk through the architecture, integrations, responsive behavior and quality checks that matter for a similar build. Chulbul Design can also collaborate with SoftLes as a technology partner when a project requires broader engineering support.</p>
                <a class="wd-button wd-button-primary" href="<?= $base ?>/#portfolio">Review Selected Work</a>
            </div>
            <div class="wd-proof-logos wd-reveal" aria-label="Selected client and partnership portfolio">
                <?php foreach ([
                    ['gurjar-pragati-manch.webp', 'Gurjar Pragati Manch'],
                    ['kisaanjee.webp', 'Kisaan Jee'],
                    ['tera-ghar.webp', 'Tera Ghar'],
                ] as $logo): ?>
                <div><img src="<?= $base ?>/assets/images/client-logos/<?= cbd_escape_text($logo[0]) ?>" alt="<?= cbd_escape_text($logo[1]) ?>" width="180" height="72" loading="lazy"><span><?= cbd_escape_text($logo[1]) ?></span></div>
                <?php endforeach; ?>
                <a href="https://softles.in/" target="_blank" rel="noopener noreferrer"><strong>S</strong><span>Technology partner: SoftLes <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></span></a>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="why-title">
        <div class="wd-container wd-split">
            <div class="wd-copy wd-reveal">
                <span class="wd-kicker">Why Chulbul Design</span>
                <h2 id="why-title">A Development Partner That Keeps Business and Engineering Connected</h2>
                <p>Technical decisions affect customer experience, marketing, operations and future cost. We keep those connections visible instead of treating development as a closed coding exercise. You receive understandable explanations, clear review points and recommendations tied to the actual requirement.</p>
                <p>Our Gurugram-based team serves businesses in India and international markets through planned remote collaboration. Meeting windows, milestones, responsibilities and feedback channels are agreed before work begins. This creates a practical working rhythm without pretending to have offices where we do not.</p>
            </div>
            <div class="wd-reasons wd-reveal">
                <div><i class="bi bi-bullseye"></i><span><strong>Requirement-first decisions</strong>Technology follows the product need and operating context.</span></div>
                <div><i class="bi bi-chat-square-dots"></i><span><strong>Clear communication</strong>Milestones, open questions and risks are kept visible.</span></div>
                <div><i class="bi bi-layers"></i><span><strong>Design-to-code continuity</strong>UX and engineering are reviewed as one customer journey.</span></div>
                <div><i class="bi bi-life-preserver"></i><span><strong>Post-launch continuity</strong>Handover, maintenance and future iterations can be planned.</span></div>
            </div>
        </div>
    </section>

    <section class="wd-section wd-section-soft" aria-labelledby="cost-title">
        <div class="wd-container wd-cost-grid">
            <div class="wd-heading wd-heading-left wd-reveal">
                <span class="wd-kicker">Planning your investment</span>
                <h2 id="cost-title">What Affects Web Development Cost?</h2>
                <p>A useful estimate comes from a defined scope. We review these factors before recommending a budget and delivery plan.</p>
            </div>
            <div class="wd-cost-list wd-reveal">
                <div><span>01</span><p><strong>Product scope</strong>Number of templates, workflows, user roles and administrative features.</p></div>
                <div><span>02</span><p><strong>Interface complexity</strong>Custom states, interactions, responsive behavior and accessibility requirements.</p></div>
                <div><span>03</span><p><strong>Data and integrations</strong>Existing systems, APIs, migration volume and synchronization rules.</p></div>
                <div><span>04</span><p><strong>Quality requirements</strong>Testing depth, security controls, performance targets and supported environments.</p></div>
                <div><span>05</span><p><strong>Delivery model</strong>Content readiness, approval workflow, launch constraints and ongoing support.</p></div>
            </div>
        </div>
    </section>

    <section class="wd-section" aria-labelledby="related-title">
        <div class="wd-container">
            <div class="wd-heading wd-reveal">
                <span class="wd-kicker">Related expertise</span>
                <h2 id="related-title">Services That Often Support a Development Project</h2>
                <p>Continue with the service that matches your immediate need.</p>
            </div>
            <div class="wd-related-grid">
                <a class="wd-related-card wd-reveal" href="<?= $base ?>/web-design"><i class="bi bi-palette2"></i><span><strong>Web Design</strong>User journeys and visual interfaces for your build.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a>
                <a class="wd-related-card wd-reveal" href="<?= $base ?>/cms-development"><i class="bi bi-layout-text-window"></i><span><strong>CMS Development</strong>Structured content management for editorial teams.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a>
                <a class="wd-related-card wd-reveal" href="<?= $base ?>/ecommerce-website-design"><i class="bi bi-cart3"></i><span><strong>Ecommerce Development</strong>Product, checkout and order experiences for online selling.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a>
                <a class="wd-related-card wd-reveal" href="<?= $base ?>/website-maintenance"><i class="bi bi-tools"></i><span><strong>Website Maintenance</strong>Planned updates, checks and technical support after launch.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a>
            </div>
        </div>
    </section>

    <section class="wd-section wd-faq" aria-labelledby="faq-title">
        <div class="wd-container wd-faq-grid">
            <div class="wd-heading wd-heading-left wd-reveal">
                <span class="wd-kicker">Frequently asked questions</span>
                <h2 id="faq-title">Web Development FAQs</h2>
                <p>Practical answers for teams comparing development partners and planning a new build.</p>
                <a class="wd-text-link" href="<?= $base ?>/contact-us">Ask a project-specific question <i class="bi bi-arrow-right"></i></a>
            </div>
            <div class="wd-accordion wd-reveal">
                <?php foreach ($faqs as $index => $faq): ?>
                <details<?= $index === 0 ? ' open' : '' ?>>
                    <summary><?= cbd_escape_text($faq['q']) ?><i class="bi bi-plus-lg" aria-hidden="true"></i></summary>
                    <p><?= cbd_escape_text($faq['a']) ?></p>
                </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="wd-final-cta">
        <div class="wd-container wd-reveal">
            <span class="wd-kicker">Have a website or platform in mind?</span>
            <h2>Let’s Turn the Requirement Into a Clear Development Plan.</h2>
            <p>Tell us what users need to do, what is not working today and which systems are involved. We will help you define a practical next step.</p>
            <div class="wd-actions">
                <a class="wd-button wd-button-white" href="<?= $base ?>/contact-us"><i class="bi bi-envelope-fill" aria-hidden="true"></i> Request a Project Discussion</a>
                <a class="wd-button wd-button-outline" href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%20want%20to%20discuss%20a%20web%20development%20project." target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> Chat on WhatsApp</a>
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
