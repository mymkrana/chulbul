<?php
require_once __DIR__ . '/includes/http.php';
require_once __DIR__ . '/includes/seo.php';

$cbd_page_has_charset = true;
$page_styles = ['/assets/css/web-development.css'];

if (cbd_is_direct_script_request('web-design.php')) {
    cbd_redirect_path('/web-design');
}

$base = cbd_base_path();
$canonical = cbd_canonical_url('/web-design');
$page_robots = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
$pageTitle = 'Web Design Company | Custom Website Design | Chulbul Design';
$pageDescription = 'Custom web design company creating responsive, accessible and conversion-focused websites for businesses, startups and growing organizations.';
$shareImage = cbd_public_url('/assets/images/service-heroes/web-design-hero.webp');

$designServices = [
    ['icon' => 'bi-compass', 'title' => 'Website Strategy & Discovery', 'text' => 'Align business goals, audience needs, content, competition and success measures before visual design begins.'],
    ['icon' => 'bi-diagram-3', 'title' => 'Information Architecture', 'text' => 'Organize pages, navigation and content relationships so visitors can understand the offer and find the next step.'],
    ['icon' => 'bi-palette2', 'title' => 'Custom Website Design', 'text' => 'Original page systems shaped around your brand and customer journey instead of a generic multipurpose template.'],
    ['icon' => 'bi-phone', 'title' => 'Responsive Web Design', 'text' => 'Layouts planned for mobile, tablet and desktop with readable content, clear actions and useful responsive behavior.'],
    ['icon' => 'bi-cursor', 'title' => 'Conversion-Focused Design', 'text' => 'Trust, hierarchy, proof and calls to action arranged around informed customer decisions rather than decorative trends.'],
    ['icon' => 'bi-window', 'title' => 'Landing Page Design', 'text' => 'Focused campaign pages that connect one audience and offer with a measurable enquiry, booking or purchase journey.', 'href' => '/landing-page-design', 'link' => 'Explore landing page design'],
    ['icon' => 'bi-arrow-repeat', 'title' => 'Website Redesign', 'text' => 'Improve an outdated website while preserving useful content, working journeys and important search URLs.', 'href' => '/website-redesign', 'link' => 'Plan a website redesign'],
    ['icon' => 'bi-grid-3x3-gap', 'title' => 'Design Systems', 'text' => 'Reusable colors, typography, spacing and components that keep growing websites visually consistent.'],
    ['icon' => 'bi-universal-access', 'title' => 'Accessible Web Design', 'text' => 'Clear structure, readable contrast, visible focus and keyboard-aware interactions considered throughout design.'],
    ['icon' => 'bi-code-slash', 'title' => 'Design-to-Development Handoff', 'text' => 'Responsive states, component rules and content behavior documented for accurate, maintainable implementation.', 'href' => '/web-development', 'link' => 'Explore web development'],
];

$solutionTypes = [
    ['icon' => 'bi-building', 'title' => 'Corporate Websites', 'text' => 'Credible company, service, leadership and resource journeys for established organizations.'],
    ['icon' => 'bi-briefcase', 'title' => 'B2B Service Websites', 'text' => 'Clear expertise, proof and lead journeys for complex or considered buying decisions.'],
    ['icon' => 'bi-shop', 'title' => 'Small Business Websites', 'text' => 'Focused websites that explain services, build local trust and make enquiries straightforward.'],
    ['icon' => 'bi-rocket-takeoff', 'title' => 'Startup Websites', 'text' => 'Flexible product narratives for validation, launch, fundraising and early customer acquisition.'],
    ['icon' => 'bi-cart3', 'title' => 'Ecommerce Experiences', 'text' => 'Discovery, product, cart and checkout interfaces designed around confident purchase decisions.'],
    ['icon' => 'bi-heart-pulse', 'title' => 'Healthcare Websites', 'text' => 'Accessible service, clinician, location and appointment journeys with responsible communication.'],
    ['icon' => 'bi-airplane', 'title' => 'Travel Websites', 'text' => 'Destination, itinerary, booking and enquiry experiences built for complex customer planning.'],
    ['icon' => 'bi-house-gear', 'title' => 'Home-Service Websites', 'text' => 'Service areas, projects, reviews and quote journeys designed for high-intent mobile visitors.'],
    ['icon' => 'bi-newspaper', 'title' => 'Publishing Websites', 'text' => 'Readable editorial systems for news, articles, guides, categories and related content.'],
    ['icon' => 'bi-globe2', 'title' => 'Multilingual Websites', 'text' => 'Navigation and page systems that accommodate language expansion and regional content clearly.'],
];

$faqs = [
    ['q' => 'What does a web design company do?', 'a' => 'A web design company plans how a website communicates and guides people. The work can include discovery, information architecture, user journeys, wireframes, visual design, responsive behavior, design systems, accessibility considerations, content hierarchy and collaboration with developers.'],
    ['q' => 'What is the difference between web design and web development?', 'a' => 'Web design defines the customer journey, content hierarchy, interface and responsive behavior. Web development turns that approved design into a working website or application through code, data, integrations and technical architecture. The two disciplines should be planned together.'],
    ['q' => 'Do you use pre-made website templates?', 'a' => 'Our custom projects are designed around the organization’s content, customers and brand rather than a theme demo. A controlled template or CMS approach can still be sensible for a limited scope, but its tradeoffs are explained before it is recommended.'],
    ['q' => 'Will you help organize our website content?', 'a' => 'Yes. We can map the sitemap, page purpose, priority questions, proof and calls to action. Final copywriting depth depends on the agreed scope, but design decisions are made with realistic content rather than empty placeholder sections.'],
    ['q' => 'Will the website design work on mobile devices?', 'a' => 'Yes. Mobile is considered from the beginning, not treated as a compressed desktop layout. We plan reading order, navigation, tap targets, forms, media, tables and calls to action across relevant screen sizes.'],
    ['q' => 'How does web design improve conversions?', 'a' => 'Design can reduce confusion, make value easier to understand, surface relevant proof and guide a clear next action. Conversion also depends on offer quality, traffic intent, content, pricing and follow-up, so we avoid promising results based on visual changes alone.'],
    ['q' => 'Can you redesign our existing website without losing SEO?', 'a' => 'A redesign can preserve important URLs, metadata, content and internal links when migration is planned correctly. We audit the current structure and identify pages with search or business value before deciding what to retain, improve, merge or redirect.'],
    ['q' => 'How much does custom web design cost?', 'a' => 'Cost depends on the number and variety of page templates, research, content support, responsive complexity, interactions, languages, accessibility, ecommerce and development requirements. We provide a written scope after discovery rather than one price for every website.'],
    ['q' => 'How long does a web design project take?', 'a' => 'A focused business website may be designed over several weeks, while a content-heavy, multilingual or application-style experience takes longer. Timing depends on discovery, content readiness, review cycles and the number of unique templates.'],
    ['q' => 'Do you also develop the website after design?', 'a' => 'Yes. Chulbul Design provides web development, WordPress, CMS and ecommerce implementation. Design and development can be delivered together, or we can provide a documented design system and handoff for another qualified development team.'],
];

$schemaOffers = [];
foreach ($designServices as $service) {
    $schemaOffers[] = ['@type' => 'Offer', 'itemOffered' => ['@type' => 'Service', 'name' => $service['title']]];
}

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'Service',
            '@id' => $canonical . '#service',
            'name' => 'Custom Web Design Services',
            'serviceType' => 'Web Design',
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
            'hasOfferCatalog' => ['@type' => 'OfferCatalog', 'name' => 'Web Design Services', 'itemListElement' => $schemaOffers],
        ],
        [
            '@type' => 'BreadcrumbList',
            '@id' => $canonical . '#breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => CBD_CANONICAL_ORIGIN . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Web Design & Development', 'item' => cbd_canonical_url('/web-design-development')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => 'Web Design', 'item' => $canonical],
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
    <link rel="preload" as="image" href="<?= $base ?>/assets/images/service-heroes/web-design-hero.webp" fetchpriority="high">
    <script type="application/ld+json"><?= cbd_json_ld($schema) ?></script>
    <?php require __DIR__ . '/includes/header.php'; ?>

<div class="wd-page">
    <section class="wd-hero" aria-labelledby="web-design-page-title">
        <div class="wd-orb wd-orb-one" aria-hidden="true"></div><div class="wd-orb wd-orb-two" aria-hidden="true"></div>
        <div class="wd-container wd-hero-grid">
            <div class="wd-hero-copy wd-reveal">
                <nav class="wd-breadcrumb" aria-label="Breadcrumb"><a href="<?= $base ?>/">Home</a><span aria-hidden="true">/</span><a href="<?= $base ?>/web-design-development">Web Design &amp; Development</a><span aria-hidden="true">/</span><span aria-current="page">Web Design</span></nav>
                <span class="wd-eyebrow"><i class="bi bi-palette2" aria-hidden="true"></i> Custom Web Design Services</span>
                <h1 id="web-design-page-title">Custom Web Design Services for <span>High-Converting Websites</span></h1>
                <p class="wd-hero-lead">We design responsive websites around your customers, content and commercial goals. From information architecture and wireframes to a distinctive visual system and development-ready handoff, every page is planned to communicate clearly, build trust and support meaningful action.</p>
                <div class="wd-actions"><a class="wd-button wd-button-primary" href="<?= $base ?>/contact-us"><i class="bi bi-chat-dots-fill" aria-hidden="true"></i> Discuss Your Website</a><a class="wd-button wd-button-secondary" href="#web-design-services">Explore Design Services <i class="bi bi-arrow-down" aria-hidden="true"></i></a></div>
                <ul class="wd-trust-list" aria-label="Web design commitments"><li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Original, brand-led design</li><li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Responsive page systems</li><li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Accessible interaction planning</li><li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Clear development handoff</li></ul>
            </div>
            <div class="wd-hero-visual wd-reveal"><div class="wd-browser"><div class="wd-browser-bar" aria-hidden="true"><span></span><span></span><span></span><b>your-brand.com</b></div><img src="<?= $base ?>/assets/images/service-heroes/web-design-hero.webp" alt="Custom responsive website design showing wireframes, page hierarchy and user journey planning" width="960" height="640" fetchpriority="high"><div class="wd-code-chip wd-code-chip-one" aria-hidden="true"><i class="bi bi-grid-3x3-gap"></i><span>Clear visual system</span></div><div class="wd-code-chip wd-code-chip-two" aria-hidden="true"><i class="bi bi-phone"></i><span>Responsive by design</span></div></div></div>
        </div>
    </section>

    <section class="wd-section wd-intro"><div class="wd-container wd-narrow wd-reveal"><span class="wd-kicker">Design with a reason</span><h2>A Website Should Clarify Your Business Before It Tries to Impress</h2><p>Strong web design is not a collection of attractive sections. It is a connected system that helps the right visitor recognize a relevant offer, understand why it matters, trust the organization and decide what to do next. Visual quality supports that journey, but it cannot replace clear structure and useful content.</p><p>Chulbul Design begins with questions: Who is the website for? What are they trying to compare or complete? Which concerns delay a decision? What proof can the business honestly provide? These answers shape the sitemap, page hierarchy and calls to action before decorative choices begin. The result is a design that reflects the way customers think rather than the internal structure of the company.</p><p>Typography, color, imagery, spacing and motion are then developed as a reusable system. Important information receives visual priority, repeated patterns become familiar and responsive rules keep the experience coherent on smaller screens. This approach creates an original website without making every page behave differently.</p></div></section>

    <section class="wd-section wd-section-soft" id="web-design-services" aria-labelledby="web-design-services-title"><div class="wd-container"><div class="wd-heading wd-reveal"><span class="wd-kicker">Web design capabilities</span><h2 id="web-design-services-title">Custom Web Design Services</h2><p>Strategy, structure, interface and responsive behavior developed as one practical website system.</p></div><div class="wd-service-grid">
        <?php foreach ($designServices as $index => $service): ?><article class="wd-service-card wd-reveal"><span class="wd-card-number"><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></span><div class="wd-icon"><i class="bi <?= cbd_escape_text($service['icon']) ?>" aria-hidden="true"></i></div><h3><?= cbd_escape_text($service['title']) ?></h3><p><?= cbd_escape_text($service['text']) ?></p><?php if (!empty($service['href'])): ?><a href="<?= $base . cbd_escape_text($service['href']) ?>"><?= cbd_escape_text($service['link']) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a><?php endif; ?></article><?php endforeach; ?>
    </div></div></section>

    <section class="wd-section"><div class="wd-container wd-split"><div class="wd-media-panel wd-reveal"><img src="<?= $base ?>/assets/images/built-around-your-requirements.webp" alt="Website strategy, information architecture and customer journey planning" width="900" height="725" loading="lazy"><div class="wd-media-caption"><i class="bi bi-signpost-split" aria-hidden="true"></i><span>Audience → questions → content → action</span></div></div><div class="wd-copy wd-reveal"><span class="wd-kicker">Structure before screens</span><h2>Plan the Customer Journey Before Styling the Interface</h2><p>A visitor rarely arrives ready to read every page in order. They scan headings, compare services, look for proof and move between pages based on their immediate question. Information architecture gives that behavior a clear path. It defines what belongs in primary navigation, which pages support each other and where important decisions need explanation.</p><p>Wireframes turn that structure into page-level hierarchy without allowing color or decoration to hide weak content. We use realistic headings, proof and calls to action so stakeholders can review whether a page makes sense before detailed visual design begins.</p><ul class="wd-check-list"><li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Audience and intent:</strong> identify what different visitors need to understand or complete.</span></li><li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Sitemap and navigation:</strong> organize services, resources and supporting pages around findability.</span></li><li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Page hierarchy:</strong> sequence value, detail, proof and action without unnecessary repetition.</span></li><li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Content readiness:</strong> expose missing evidence or unclear offers before development makes them expensive to fix.</span></li></ul></div></div></section>

    <section class="wd-section wd-solutions" aria-labelledby="design-solutions-title"><div class="wd-container"><div class="wd-heading wd-heading-light wd-reveal"><span class="wd-kicker">Designed around the business model</span><h2 id="design-solutions-title">Website Design for Different Customer Journeys</h2><p>The right structure and proof change with the audience, offer, decision and action the website supports.</p></div><div class="wd-solution-grid"><?php foreach ($solutionTypes as $solution): ?><article class="wd-solution-card wd-reveal"><i class="bi <?= cbd_escape_text($solution['icon']) ?>" aria-hidden="true"></i><h3><?= cbd_escape_text($solution['title']) ?></h3><p><?= cbd_escape_text($solution['text']) ?></p></article><?php endforeach; ?></div></div></section>

    <section class="wd-section" aria-labelledby="design-foundation-title"><div class="wd-container"><div class="wd-heading wd-reveal"><span class="wd-kicker">A coherent design foundation</span><h2 id="design-foundation-title">Strategy, Experience and Visual Language Working Together</h2><p>Each layer has a different responsibility, but customers experience them as one website.</p></div><div class="wd-stack-grid"><article class="wd-stack-card wd-reveal"><span class="wd-stack-label">01 / Strategy</span><h3>A clear communication job</h3><p>Audience, positioning, priorities and measurable actions give the website a purpose beyond looking modern.</p><div class="wd-tags"><span>Audience</span><span>Offer</span><span>Sitemap</span><span>Content</span></div></article><article class="wd-stack-card wd-reveal"><span class="wd-stack-label">02 / Experience</span><h3>A journey people can follow</h3><p>Navigation, hierarchy, wireframes and interaction states make information easier to find and actions easier to complete.</p><div class="wd-tags"><span>User Flow</span><span>Wireframes</span><span>Forms</span><span>Accessibility</span></div></article><article class="wd-stack-card wd-reveal"><span class="wd-stack-label">03 / Interface</span><h3>A distinctive reusable system</h3><p>Typography, color, imagery, spacing and components express the brand consistently across every page and device.</p><div class="wd-tags"><span>Typography</span><span>Color</span><span>Components</span><span>Motion</span></div></article></div></div></section>

    <section class="wd-section wd-section-soft"><div class="wd-container wd-split wd-split-reverse"><div class="wd-copy wd-reveal"><span class="wd-kicker">Responsive design as behavior</span><h2>Design for Real Screens, Content and Interaction</h2><p>Responsive design is more than allowing columns to stack. A smaller screen changes available space, reading patterns, navigation and how comfortably people can tap controls or complete forms. We define which information remains prominent, how complex components adapt and when an interaction needs a simpler mobile alternative.</p><p>Design reviews use realistic content because short placeholder copy can hide overflow, wrapping and hierarchy problems. Developers receive relevant states for menus, forms, errors, cards, tables and repeated components so responsive decisions do not have to be improvised during the build.</p><div class="wd-integration-list"><span><i class="bi bi-phone"></i> Mobile reading order</span><span><i class="bi bi-hand-index"></i> Practical tap targets</span><span><i class="bi bi-menu-button-wide"></i> Responsive navigation</span><span><i class="bi bi-ui-checks"></i> Form and error states</span><span><i class="bi bi-image"></i> Flexible media</span><span><i class="bi bi-speedometer2"></i> Performance-aware choices</span></div></div><div class="wd-api-visual wd-reveal" aria-label="Illustration of one design system adapting across devices"><div class="wd-api-core"><i class="bi bi-grid-3x3-gap" aria-hidden="true"></i><strong>Design</strong></div><div class="wd-api-node wd-api-node-one"><i class="bi bi-phone"></i><span>Mobile</span></div><div class="wd-api-node wd-api-node-two"><i class="bi bi-tablet"></i><span>Tablet</span></div><div class="wd-api-node wd-api-node-three"><i class="bi bi-laptop"></i><span>Laptop</span></div><div class="wd-api-node wd-api-node-four"><i class="bi bi-display"></i><span>Desktop</span></div></div></div></section>

    <section class="wd-section"><div class="wd-container wd-split"><div class="wd-media-panel wd-reveal"><img src="<?= $base ?>/assets/images/responcive.webp" alt="Conversion-focused responsive website design" width="900" height="725" loading="lazy"><div class="wd-media-caption"><i class="bi bi-cursor" aria-hidden="true"></i><span>Clarity → trust → informed action</span></div></div><div class="wd-copy wd-reveal"><span class="wd-kicker">Conversion without manipulation</span><h2>Help Visitors Make a Confident Decision</h2><p>Conversion-focused design makes an offer easier to evaluate. It does not mean filling every screen with urgent buttons or hiding important details. We create a clear relationship between customer questions, useful explanation, relevant proof and the next logical action.</p><p>Calls to action are matched to intent. A first-time visitor may need to review services or work before requesting a proposal, while a returning high-intent visitor may want direct contact. Forms ask only for information the business can use, with labels and feedback that make completion understandable.</p><ul class="wd-check-list"><li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Clear value:</strong> headings explain the offer and audience without relying on vague slogans.</span></li><li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Relevant proof:</strong> real work, process and business details appear near important decisions.</span></li><li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Intent-aware actions:</strong> pages support exploration and enquiry without competing CTAs.</span></li><li><i class="bi bi-check2" aria-hidden="true"></i><span><strong>Measurable journeys:</strong> development handoff identifies meaningful actions for analytics and improvement.</span></li></ul></div></div></section>

    <section class="wd-section" aria-labelledby="design-quality-title"><div class="wd-container"><div class="wd-heading wd-reveal"><span class="wd-kicker">Quality beyond appearance</span><h2 id="design-quality-title">Clarity, Accessibility, Performance and Consistency</h2><p>A polished interface should remain useful when content changes, screens shrink and people interact in different ways.</p></div><div class="wd-quality-grid"><article class="wd-quality-card wd-reveal"><i class="bi bi-eye"></i><h3>Clarity</h3><p>Readable hierarchy and familiar patterns help visitors scan, understand and act without unnecessary effort.</p></article><article class="wd-quality-card wd-reveal"><i class="bi bi-universal-access"></i><h3>Accessibility</h3><p>Contrast, focus, labels, structure and keyboard-aware interactions are considered before development.</p></article><article class="wd-quality-card wd-reveal"><i class="bi bi-lightning-charge"></i><h3>Performance</h3><p>Responsible media, typography and motion choices support a faster implementation and better experience.</p></article><article class="wd-quality-card wd-reveal"><i class="bi bi-grid"></i><h3>Consistency</h3><p>Reusable components help new pages feel part of the same product instead of becoming one-off designs.</p></article></div></div></section>

    <section class="wd-section wd-process" aria-labelledby="design-process-title"><div class="wd-container"><div class="wd-heading wd-reveal"><span class="wd-kicker">A clear web design process</span><h2 id="design-process-title">From Business Context to Development-Ready Design</h2><p>Each stage answers a different question before the project moves into detailed implementation.</p></div><ol class="wd-process-list"><li class="wd-reveal"><span>01</span><div><h3>Discovery &amp; Audit</h3><p>We review goals, audiences, offers, content, brand assets, competitors and the current website where relevant.</p></div></li><li class="wd-reveal"><span>02</span><div><h3>Sitemap &amp; Content Plan</h3><p>Pages, navigation, customer questions, proof and calls to action are organized into a practical structure.</p></div></li><li class="wd-reveal"><span>03</span><div><h3>Wireframes &amp; User Flows</h3><p>Important templates and journeys are mapped with realistic content before visual styling begins.</p></div></li><li class="wd-reveal"><span>04</span><div><h3>Visual Direction &amp; UI System</h3><p>Typography, color, imagery, components and key pages establish a distinctive but reusable language.</p></div></li><li class="wd-reveal"><span>05</span><div><h3>Responsive Design &amp; Review</h3><p>Relevant screen sizes, interactions, forms and content states are refined through structured feedback.</p></div></li><li class="wd-reveal"><span>06</span><div><h3>Handoff, Development &amp; QA</h3><p>Design rules and assets are documented, then implementation is reviewed for visual and functional accuracy.</p></div></li></ol></div></section>

    <section class="wd-section wd-proof" aria-labelledby="design-proof-title"><div class="wd-container wd-proof-grid"><div class="wd-proof-copy wd-reveal"><span class="wd-kicker">Review relevant work</span><h2 id="design-proof-title">Selected Web Design Work and Delivery Experience</h2><p>Our portfolio includes digital delivery experience connected with Gurjar Pragati Manch, Kisaan Jee and Tera Ghar. This work gives prospective clients practical examples of how service information, content-rich pages and responsive customer journeys can be organized for different audiences.</p><p>During a consultation, we can present relevant examples and explain our role, including the reasoning behind hierarchy, navigation, reusable components, mobile behavior and content-management decisions. For broader engineering requirements, Chulbul Design can also collaborate with SoftLes as a technology partner.</p><a class="wd-button wd-button-primary" href="<?= $base ?>/#portfolio">Review Selected Work</a></div><div class="wd-proof-logos wd-reveal" aria-label="Selected client and partnership portfolio"><?php foreach ([['gurjar-pragati-manch.webp','Gurjar Pragati Manch'],['kisaanjee.webp','Kisaan Jee'],['tera-ghar.webp','Tera Ghar']] as $logo): ?><div><img src="<?= $base ?>/assets/images/client-logos/<?= cbd_escape_text($logo[0]) ?>" alt="<?= cbd_escape_text($logo[1]) ?>" width="180" height="72" loading="lazy"><span><?= cbd_escape_text($logo[1]) ?></span></div><?php endforeach; ?><a href="https://softles.in/" target="_blank" rel="noopener noreferrer"><strong>S</strong><span>Technology partner: SoftLes <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></span></a></div></div></section>

    <section class="wd-section" aria-labelledby="design-why-title"><div class="wd-container wd-split"><div class="wd-copy wd-reveal"><span class="wd-kicker">Why Chulbul Design</span><h2 id="design-why-title">Creative Direction Connected to Business Decisions</h2><p>We explain design recommendations through audience needs, content, brand expression and implementation—not personal preference alone. Stakeholders can review why information has priority, how a component behaves and what must remain consistent as the website grows.</p><p>Our Gurugram-based team serves businesses in India and international markets through planned remote collaboration. We agree meeting windows, milestones and feedback channels at the start without claiming offices where we do not have them.</p></div><div class="wd-reasons wd-reveal"><div><i class="bi bi-compass"></i><span><strong>Strategy before styling</strong>Business and customer context guide the design direction.</span></div><div><i class="bi bi-palette2"></i><span><strong>Original visual system</strong>Components reflect the brand without sacrificing usability.</span></div><div><i class="bi bi-phone"></i><span><strong>Responsive thinking</strong>Mobile and content behavior are planned before handoff.</span></div><div><i class="bi bi-code-slash"></i><span><strong>Design-development alignment</strong>Approved decisions remain practical to build and maintain.</span></div></div></div></section>

    <section class="wd-section wd-section-soft" aria-labelledby="design-cost-title"><div class="wd-container wd-cost-grid"><div class="wd-heading wd-heading-left wd-reveal"><span class="wd-kicker">Planning the investment</span><h2 id="design-cost-title">What Affects Custom Web Design Cost?</h2><p>A useful estimate depends on the experience and page system required—not only a raw page count.</p></div><div class="wd-cost-list wd-reveal"><div><span>01</span><p><strong>Discovery and strategy</strong>Research depth, stakeholder input, current-site audit and content planning.</p></div><div><span>02</span><p><strong>Templates and journeys</strong>Number of unique page types, user flows, forms and decision paths.</p></div><div><span>03</span><p><strong>Brand and visual direction</strong>Existing identity maturity, original art direction, imagery and design-system depth.</p></div><div><span>04</span><p><strong>Responsive and interaction complexity</strong>Devices, navigation, animation, states, accessibility and complex components.</p></div><div><span>05</span><p><strong>Content and implementation</strong>Copy support, languages, CMS, ecommerce, integrations, development and QA.</p></div></div></div></section>

    <section class="wd-section" aria-labelledby="design-related-title"><div class="wd-container"><div class="wd-heading wd-reveal"><span class="wd-kicker">Related expertise</span><h2 id="design-related-title">Continue From Design to the Right Build</h2><p>Choose the implementation and content platform that matches the approved experience.</p></div><div class="wd-related-grid"><a class="wd-related-card wd-reveal" href="<?= $base ?>/web-development"><i class="bi bi-code-slash"></i><span><strong>Web Development</strong>Custom websites, applications, portals and integrations.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a><a class="wd-related-card wd-reveal" href="<?= $base ?>/wordpress-development"><i class="bi bi-wordpress"></i><span><strong>WordPress Development</strong>Custom themes, manageable content and WooCommerce.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a><a class="wd-related-card wd-reveal" href="<?= $base ?>/cms-development"><i class="bi bi-window-stack"></i><span><strong>CMS Development</strong>Structured publishing, workflows, migration and integrations.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a><a class="wd-related-card wd-reveal" href="<?= $base ?>/ecommerce-website-design"><i class="bi bi-cart3"></i><span><strong>Ecommerce Website Design</strong>Product discovery, checkout and store experiences.</span><b>Explore <i class="bi bi-arrow-right"></i></b></a></div></div></section>

    <section class="wd-section wd-faq" aria-labelledby="design-faq-title"><div class="wd-container wd-faq-grid"><div class="wd-heading wd-heading-left wd-reveal"><span class="wd-kicker">Frequently asked questions</span><h2 id="design-faq-title">Web Design FAQs</h2><p>Clear answers for businesses planning a new custom website or redesign.</p><a class="wd-text-link" href="<?= $base ?>/contact-us">Ask a project-specific question <i class="bi bi-arrow-right"></i></a></div><div class="wd-accordion wd-reveal"><?php foreach ($faqs as $index => $faq): ?><details<?= $index === 0 ? ' open' : '' ?>><summary><?= cbd_escape_text($faq['q']) ?><i class="bi bi-plus-lg" aria-hidden="true"></i></summary><p><?= cbd_escape_text($faq['a']) ?></p></details><?php endforeach; ?></div></div></section>

    <section class="wd-final-cta"><div class="wd-container wd-reveal"><span class="wd-kicker">Planning a new website?</span><h2>Let’s Make the Design Clear Before Development Begins.</h2><p>Share your current website, audience and goals. We will help you identify the right pages, design scope and next step.</p><div class="wd-actions"><a class="wd-button wd-button-white" href="<?= $base ?>/contact-us"><i class="bi bi-envelope-fill" aria-hidden="true"></i> Request a Project Discussion</a><a class="wd-button wd-button-outline" href="https://wa.me/919990548795?text=Hi%20Chulbul%20Design%2C%20I%20want%20to%20discuss%20a%20custom%20web%20design%20project." target="_blank" rel="noopener noreferrer"><i class="bi bi-whatsapp" aria-hidden="true"></i> Chat on WhatsApp</a></div></div></section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var items = document.querySelectorAll('.wd-reveal');
    if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) { items.forEach(function (item) { item.classList.add('is-visible'); }); return; }
    var observer = new IntersectionObserver(function (entries) { entries.forEach(function (entry) { if (entry.isIntersecting) { entry.target.classList.add('is-visible'); observer.unobserve(entry.target); } }); }, { threshold: 0.08, rootMargin: '0px 0px -40px' });
    items.forEach(function (item) { observer.observe(item); });
});
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
