<?php
require_once __DIR__ . '/includes/http.php';
if (cbd_is_direct_script_request('index.php')) cbd_redirect_path('/');
// Latest blog posts for homepage section
$_hp_latest_posts = [];
try {
    $_hp_pdo = cbd_database();
    if ($_hp_pdo) {
        $_hp_latest_posts = $_hp_pdo->query("SELECT id, slug, title, excerpt, date, read_time, content FROM posts WHERE status='published' AND deleted_at IS NULL ORDER BY date DESC, created_at DESC LIMIT 3")->fetchAll();
    }
    // Extract first image from content for each post
    foreach ($_hp_latest_posts as &$_hp_p) {
        $_hp_p['thumb'] = '';
        if (preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $_hp_p['content'] ?? '', $_hp_m)) {
            $_hp_p['thumb'] = $_hp_m[1];
        }
    }
    unset($_hp_p);
} catch (Exception $_hp_e) {}
$_hp_base = cbd_base_path();
$homeTitle = 'Web Design & Development Company | Chulbul Design';
$homeDescription = 'Chulbul Design builds custom websites, ecommerce stores and web applications for businesses worldwide. Explore our services and discuss your project.';
// Use one source for visible FAQs and their structured data.
$homeFaqs = [
    ['q' => 'Do you work with businesses worldwide?', 'a' => 'Yes. Chulbul Design is based in Gurugram, India and works with businesses worldwide. Projects are coordinated remotely through online discovery, design reviews and milestone approvals. Meeting times and communication arrangements are agreed with your team.'],
    ['q' => 'What is the difference between web design and web development?', 'a' => 'Web design covers page structure, visual identity, user journeys and responsive layouts. Web development turns those designs into a working website, including the CMS, forms, data and integrations. We offer both services together or as a defined part of your project.'],
    ['q' => 'How much does a custom website cost?', 'a' => 'Pricing depends on page templates, content, functionality, languages, integrations and ongoing support. After reviewing your brief, we provide a scoped proposal with deliverables, payment milestones, quote currency and any third-party costs. A business website, ecommerce store and custom application require different estimates.'],
    ['q' => 'Which platform should we choose for our website?', 'a' => 'WordPress can suit content-led websites, while Shopify and WooCommerce support different ecommerce requirements. A custom application may be appropriate for specialist workflows or integrations. We assess editing needs, catalogue size, ownership, maintenance and budget before recommending a platform.'],
    ['q' => 'How long does website design and development take?', 'a' => 'Timing depends on scope, content readiness, integrations and review cycles. We agree a schedule for discovery, design, development, testing and launch in the proposal, with milestones and responsibilities for both teams.'],
    ['q' => 'Can you redesign our existing website and preserve important URLs?', 'a' => 'Yes. A redesign starts with reviewing useful content, existing URLs and enquiry journeys. The agreed migration scope can include redirects, metadata, internal links and launch checks. Search performance can change during a migration, so rankings cannot be guaranteed.'],
    ['q' => 'Will our website be mobile-friendly and prepared for SEO?', 'a' => 'We plan responsive layouts, crawlable content, clear headings and page metadata as part of the agreed build. Performance and usability are tested before launch. Search rankings also depend on competition, content and other signals; no website build can guarantee a particular position.'],
    ['q' => 'Can our team manage the website after launch?', 'a' => 'We agree CMS editing access, training and handover requirements during scoping. The proposal should also identify responsibility for hosting, backups, updates, third-party licences and any ongoing maintenance or support.'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
    <title><?= htmlspecialchars($homeTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="canonical" href="https://www.chulbuldesign.com/">
    <meta name="description" content="<?= htmlspecialchars($homeDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= htmlspecialchars($homeTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars($homeDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="https://www.chulbuldesign.com/">
    <meta property="og:image" content="https://www.chulbuldesign.com/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg">
    <meta property="og:image:type" content="image/jpeg">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Chulbul Design — web design and development for businesses worldwide, with travel, real estate, restaurant and ecommerce website mockups.">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@chulbuldesign">
    <meta name="twitter:title" content="<?= htmlspecialchars($homeTitle, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($homeDescription, ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:image" content="https://www.chulbuldesign.com/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg">
    <meta name="twitter:image:alt" content="Chulbul Design — web design and development for businesses worldwide, with travel, real estate, restaurant and ecommerce website mockups.">
    <meta name="google-site-verification" content="66Yl1e0VD33TbRBSGdMcGfRYaTUguH0LyUovEsgh590">
    <script type="application/ld+json"><?= json_encode([
        '@context' => 'https://schema.org', '@type' => 'ProfessionalService',
        '@id' => 'https://www.chulbuldesign.com/#organization', 'name' => 'Chulbul Design',
        'url' => 'https://www.chulbuldesign.com/', 'logo' => 'https://www.chulbuldesign.com/assets/images/logo/chulbuldesign.svg',
        'telephone' => '+919990548795', 'areaServed' => 'Worldwide',
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => 'Sector 15, Flat No. 1277', 'addressLocality' => 'Gurugram', 'addressRegion' => 'Haryana', 'addressCountry' => 'IN'],
        'description' => $homeDescription,
        'sameAs' => ['https://www.facebook.com/chulbuldesign', 'https://www.instagram.com/chulbuldesign', 'https://twitter.com/ChulbulDesign', 'https://www.linkedin.com/company/chulbuldesign/'],
    ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <script type="application/ld+json">{"@context":"https://schema.org","@type":"WebSite","@id":"https://www.chulbuldesign.com/#website","url":"https://www.chulbuldesign.com/","name":"Chulbul Design","alternateName":"ChulbulDesign","publisher":{"@id":"https://www.chulbuldesign.com/#organization"},"inLanguage":"en"}</script>
    <script type="application/ld+json"><?= json_encode([
        '@context' => 'https://schema.org', '@type' => 'FAQPage',
        'mainEntity' => array_map(static fn(array $faq): array => [
            '@type' => 'Question', 'name' => $faq['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
        ], $homeFaqs),
    ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
    <?php require __DIR__ . '/includes/header.php'; ?>

<!-- ============================================================
     HERO SECTION
============================================================ -->
<section class="bg-white pt-16 pb-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top label -->
        <div class="flex justify-center mb-8">
            <span class="inline-flex items-center gap-2 text-xs font-semibold text-[#EE483D] bg-red-50 border border-red-100 px-4 py-2 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-[#EE483D]"></span>
                Web Design &amp; Development · Serving Businesses Worldwide
            </span>
        </div>

        <!-- Headline -->
        <h1 class="text-center text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight max-w-4xl mx-auto mb-6">
            Custom Web Design &amp; Development<br>
            <span class="text-[#49499A]">for Businesses Worldwide</span>
        </h1>

        <!-- Subtext -->
        <p class="text-center text-gray-500 text-lg max-w-2xl mx-auto mb-5 leading-relaxed">
            Build a website that reflects your brand and makes it easier for customers to take the next step.
            We design and develop business websites, ecommerce stores and web applications around your goals.
        </p>
        <p class="text-center text-gray-500 text-sm mb-5">Based in Gurugram, India. Working with businesses worldwide.</p>
        <!-- Service Pills -->
        <div class="flex flex-wrap justify-center gap-2 mb-10">
            <a href="<?= $base ?>/web-design" class="inline-flex items-center gap-2 bg-blue-50 text-blue-700 border border-blue-100 px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-blue-100 hover:border-blue-300 transition"><i class="bi bi-laptop"></i><span>Web Design</span></a>
            <a href="<?= $base ?>/web-development" class="inline-flex items-center gap-2 bg-indigo-50 text-indigo-700 border border-indigo-100 px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-indigo-100 hover:border-indigo-300 transition"><i class="bi bi-code-slash"></i><span>Web Development</span></a>
            <a href="<?= $base ?>/ecommerce-website-development" class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-100 px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-emerald-100 hover:border-emerald-300 transition"><i class="bi bi-cart4"></i><span>E-Commerce Website</span></a>
            <a href="<?= $base ?>/wordpress-development" class="inline-flex items-center gap-2 bg-sky-50 text-sky-700 border border-sky-100 px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-sky-100 hover:border-sky-300 transition"><i class="bi bi-wordpress"></i><span>WordPress Development</span></a>
            <a href="<?= $base ?>/android-app-development" class="inline-flex items-center gap-2 bg-orange-50 text-orange-700 border border-orange-100 px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-orange-100 hover:border-orange-300 transition"><i class="bi bi-phone"></i><span>Mobile App Development</span></a>
            <a href="<?= $base ?>/local-seo-services" class="inline-flex items-center gap-2 bg-red-50 text-red-700 border border-red-100 px-4 py-1.5 rounded-full text-sm font-semibold hover:bg-red-100 hover:border-red-300 transition"><i class="bi bi-graph-up-arrow"></i><span>Online Marketing</span></a>
        </div>

        <!-- CTAs -->
        <div class="flex flex-wrap justify-center gap-4 mb-14">
            <a href="https://wa.me/919990548795" target="_blank"
               class="inline-flex items-center gap-2 bg-[#EE483D] text-white px-8 py-3.5 rounded-xl font-bold hover:bg-red-600 transition-all shadow-lg shadow-red-200">
                <i class="bi bi-whatsapp"></i> Discuss Your Project
            </a>
            <a href="#portfolio"
               class="inline-flex items-center gap-2 border-2 border-gray-200 text-gray-700 px-8 py-3.5 rounded-xl font-bold hover:border-[#49499A] hover:text-[#49499A] transition-all">
                <i class="bi bi-briefcase"></i> Explore Our Work
            </a>
        </div>

        <!-- Stats row -->
        <div class="flex flex-wrap justify-center gap-10 mb-14">
            <?php foreach([['500+','Projects Delivered'],['10+','Years Experience'],['200+','Happy Clients'],['15+','Technologies']] as $s): ?>
            <div class="text-center">
                <div class="text-3xl font-extrabold text-[#49499A]"><?= $s[0] ?></div>
                <div class="text-gray-400 text-sm mt-1"><?= $s[1] ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Hero Image -->
        <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-[#49499A] to-[#2d2d70]">
            <!-- Dot pattern overlay -->
            <div class="absolute inset-0 opacity-10"
                 style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:24px 24px"></div>

        </div>

    </div>
</section>

<!-- ============================================================
     MARQUEE — TECH LOGOS
============================================================ -->
<section class="tech-marquee-section" aria-labelledby="tech-marquee-title">
    <p class="tech-marquee-title" id="tech-marquee-title">Technologies We Master</p>
    <div class="tech-marquee-viewport">
        <div class="tech-marquee-track">
            <?php
            $techs = ['wordpress','react','nodejs','php','shopify','magento','drupal','java','net','AngularJS','android'];
            $names = ['WordPress','React','Node.js','PHP','Shopify','Magento','Drupal','Java','.NET','Angular','Android'];
            foreach (array_merge($techs,$techs) as $i => $t):
                $n = $names[$i % count($names)]; ?>
            <div class="tech-marquee-item">
                <img src="<?= $base ?>/assets/images/tech/<?= $t ?>.svg" alt="" class="tech-marquee-logo" width="20" height="20" loading="lazy">
                <span class="tech-marquee-name"><?= $n ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     SERVICES
============================================================ -->
<section class="py-24 bg-white" id="services">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="section-badge">Our Services</span>
            <h2 class="text-3xl lg:text-4xl font-extrabold text-[#49499A] mt-4">
                Website Design &amp; Development <br>Services for Your Business
            </h2>
            <p class="text-gray-500 mt-3 leading-relaxed">Choose the expertise your project needs: custom website design, web development, ecommerce and ongoing digital support.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $services = [
                ['icon'=>'bi-laptop',         'gradient'=>'from-blue-500 to-indigo-600',  'light'=>'bg-blue-50',    'title'=>'Web Design & Development',     'desc'=>'Custom, mobile-first websites & powerful web apps built to convert visitors. Pixel-perfect design, zero templates.',    'link'=>'web-design-development'],
                ['icon'=>'bi-cart4',          'gradient'=>'from-emerald-500 to-teal-600', 'light'=>'bg-emerald-50', 'title'=>'Ecommerce Solutions',           'desc'=>'High-converting Shopify, WooCommerce & Magento stores — built to maximize sales and grow your revenue online.',          'link'=>'ecommerce-development'],
                ['icon'=>'bi-phone',          'gradient'=>'from-orange-500 to-red-500',   'light'=>'bg-orange-50',  'title'=>'App & Software Development', 'desc'=>'Android and iOS apps, SaaS platforms, CRM, ERP and custom software shaped around real business workflows.',              'link'=>'mobile-software-development'],
                ['icon'=>'bi-palette2',       'gradient'=>'from-violet-500 to-purple-700','light'=>'bg-violet-50',  'title'=>'UI/UX & Branding',             'desc'=>'Stunning UI/UX design, logo & brand identity that makes your business unforgettable and builds instant trust.',          'link'=>'ui-ux-branding'],
                ['icon'=>'bi-graph-up-arrow', 'gradient'=>'from-[#EE483D] to-rose-600',   'light'=>'bg-red-50',     'title'=>'SEO & Digital Marketing',      'desc'=>'Data-driven SEO, Google Ads & Social Media campaigns that bring real leads and deliver measurable ROI.',                 'link'=>'seo-digital-marketing'],
                ['icon'=>'bi-robot',          'gradient'=>'from-sky-500 to-cyan-600',     'light'=>'bg-sky-50',     'title'=>'AI & Automation',              'desc'=>'AI chatbots, workflow automation & smart integrations — reduce manual work and scale your business faster.',              'link'=>'ai-automation'],
            ];
            foreach ($services as $s): ?>
            <a href="<?= $base ?>/<?= $s['link'] ?>"
               class="service-card group bg-white rounded-3xl p-7 border border-gray-100 hover:border-transparent block overflow-hidden relative">
                <!-- Hover glow bg -->
                <div class="absolute inset-0 bg-gradient-to-br <?= $s['gradient'] ?> opacity-0 group-hover:opacity-5 transition-opacity rounded-3xl"></div>

                <div class="relative">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br <?= $s['gradient'] ?> flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 transition-transform">
                        <i class="bi <?= $s['icon'] ?> text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2"><?= $s['title'] ?></h3>
                    <p class="text-gray-500 text-sm leading-relaxed"><?= $s['desc'] ?></p>
                    <div class="inline-flex items-center gap-1 mt-5 text-[#EE483D] text-sm font-bold group-hover:gap-3 transition-all">
                        Explore <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     WHY US
============================================================ -->
<section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <div class="flex-1 flex justify-center">
                <div class="relative">
                    <!-- Stats floating card -->
                    <div class="absolute -top-6 -right-6 bg-white rounded-2xl shadow-xl p-4 z-10 border border-gray-100">
                        <div class="text-2xl font-extrabold text-[#EE483D]">4.9 ★</div>
                        <div class="text-xs text-gray-500 font-medium">Client Rating</div>
                    </div>
                    <div class="absolute -bottom-6 -left-6 bg-white rounded-2xl shadow-xl p-4 z-10 border border-gray-100">
                        <div class="text-2xl font-extrabold text-[#49499A]">500+</div>
                        <div class="text-xs text-gray-500 font-medium">Projects Done</div>
                    </div>
                        <img src="<?= $base ?>/assets/images/chulbuldesign_webdesign.svg"
                         alt="Website design and development illustration" class="w-full max-w-md relative z-0"
                         width="480" height="400" loading="lazy">
                </div>
            </div>
            <div class="flex-1">
                <span class="section-badge">Why Choose Us</span>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-[#49499A] mt-4 mb-8 leading-tight">
                    A Web Design Partner <br>for Your Next Stage
                </h2>
                <div class="space-y-6">
                    <?php
                    $points = [
                        ['icon'=>'bi-award-fill',        'color'=>'from-yellow-400 to-orange-500','title'=>'Business-Led Planning',       'desc'=>'Start with your audience, services and requirements, then agree the website scope and delivery milestones.'],
                        ['icon'=>'bi-patch-check-fill',  'color'=>'from-emerald-400 to-teal-500', 'title'=>'Design Around Your Brand',   'desc'=>'Page layouts and customer journeys shaped around your content, brand identity and business goals.'],
                        ['icon'=>'bi-search',            'color'=>'from-blue-400 to-indigo-500',  'title'=>'SEO-First Development',        'desc'=>'Every project is built with Google in mind — from site structure and speed to on-page SEO.'],
                        ['icon'=>'bi-headset',           'color'=>'from-[#EE483D] to-rose-500',   'title'=>'Clear Project Communication', 'desc'=>'Agree review calls, milestones and support arrangements that work for your team and time zone.'],
                    ];
                    foreach ($points as $p): ?>
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br <?= $p['color'] ?> flex items-center justify-center flex-shrink-0 shadow-md">
                            <i class="bi <?= $p['icon'] ?> text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-base"><?= $p['title'] ?></h3>
                            <p class="text-gray-500 text-sm mt-1 leading-relaxed"><?= $p['desc'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="https://wa.me/919990548795" target="_blank"
                   class="btn-glow inline-flex items-center gap-2 mt-8 bg-[#EE483D] text-white px-8 py-4 rounded-2xl font-bold hover:bg-red-500 transition-all">
                    <i class="bi bi-whatsapp"></i> Chat on WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     HOW WE WORK
============================================================ -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="section-badge">Our Process</span>
            <h2 class="text-3xl lg:text-4xl font-extrabold text-[#49499A] mt-4">How We Deliver Your Project</h2>
            <p class="text-gray-500 mt-3">A proven 4-step process that ensures on-time delivery and outstanding results every time.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 relative">
            <?php
            $steps = [
                ['num'=>'01','icon'=>'bi-chat-dots-fill',   'color'=>'from-[#EE483D] to-rose-500',   'title'=>'Discovery & Strategy',    'desc'=>'We understand your business, goals, and target audience to create a winning digital strategy.'],
                ['num'=>'02','icon'=>'bi-palette-fill',     'color'=>'from-violet-500 to-purple-600', 'title'=>'Design & Prototype',      'desc'=>'Our designers craft stunning mockups and interactive prototypes for your approval.'],
                ['num'=>'03','icon'=>'bi-code-square',      'color'=>'from-blue-500 to-indigo-600',   'title'=>'Development & Testing',   'desc'=>'Pixel-perfect development with rigorous testing across all browsers and devices.'],
                ['num'=>'04','icon'=>'bi-rocket-takeoff-fill','color'=>'from-emerald-500 to-teal-600','title'=>'Launch & Support',        'desc'=>'Smooth deployment, training, and ongoing support to keep your site performing at its best.'],
            ];
            foreach ($steps as $s): ?>
            <div class="process-step relative flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br <?= $s['color'] ?> flex items-center justify-center shadow-lg mb-5">
                    <i class="bi <?= $s['icon'] ?> text-white text-2xl"></i>
                </div>
                <span class="text-xs font-black text-gray-300 mb-2"><?= $s['num'] ?></span>
                <h3 class="font-bold text-gray-800 text-base mb-2"><?= $s['title'] ?></h3>
                <p class="text-gray-500 text-sm leading-relaxed"><?= $s['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     SELECTED CLIENT LOGOS
============================================================ -->
<section class="client-logo-showcase-section" aria-labelledby="client-logo-showcase-title">
    <div class="client-logo-showcase-heading">
        <span class="client-logo-showcase-badge"><i class="bi bi-stars" aria-hidden="true"></i> Selected Clients</span>
        <h2 id="client-logo-showcase-title">Trusted by Brands That <span>Think Forward.</span></h2>
        <p>A selection of brands supported through design, development and our technology partnership with SoftLes.</p>
    </div>

    <?php
    $clientLogos = [
        ['file' => 'client-logo-1.webp',        'name' => 'Brunswick Fur Food',    'theme' => 'dark'],
        ['file' => 'client-logo-2.webp',        'name' => 'Enviro Guru',           'theme' => 'dark'],
        ['file' => 'client-logo-3.webp',        'name' => 'Busy Bee',              'theme' => 'dark'],
        ['file' => 'client-logo-4.webp',        'name' => 'The Beer Cafe',         'theme' => 'light'],
        ['file' => 'client-logo-5.webp',        'name' => 'Artech',                'theme' => 'dark'],
        ['file' => 'client-logo-6.webp',        'name' => 'Bummlers',              'theme' => 'light'],
        ['file' => 'client-logo-7.webp',        'name' => 'Gaam Gurug',            'theme' => 'light'],
        ['file' => 'client-logo-8.webp',        'name' => 'Swizzle',               'theme' => 'dark'],
        ['file' => 'client-logo-9.webp',        'name' => 'Client Brand',          'theme' => 'light'],
        ['file' => 'gurjar-pragati-manch.webp', 'name' => 'Gurjar Pragati Manch', 'theme' => 'dark'],
        ['file' => 'kisaanjee.webp',            'name' => 'Kisaan Jee',            'theme' => 'light'],
        ['file' => 'kuch-bhi-dalu.webp',        'name' => 'Kuch Bhi Dalu',         'theme' => 'light'],
        ['file' => 'ssf-global.webp',           'name' => 'SSF Global',            'theme' => 'light'],
        ['file' => 'tera-ghar.webp',            'name' => 'Tera Ghar',             'theme' => 'dark'],
    ];
    ?>
    <div class="client-logo-rail-viewport">
        <div class="client-logo-rail-track">
            <?php for ($railCopy = 0; $railCopy < 2; $railCopy++): ?>
            <div class="client-logo-rail-group" <?= $railCopy === 1 ? 'aria-hidden="true"' : '' ?>>
                <?php foreach ($clientLogos as $clientLogo): ?>
                <div class="client-logo-rail-item is-<?= $clientLogo['theme'] ?>">
                    <img src="<?= $base ?>/assets/images/client-logos/<?= htmlspecialchars($clientLogo['file'], ENT_QUOTES, 'UTF-8') ?>"
                         alt="<?= $railCopy === 0 ? htmlspecialchars($clientLogo['name'], ENT_QUOTES, 'UTF-8') : '' ?>"
                         width="160" height="64" loading="lazy">
                </div>
                <?php endforeach; ?>
            </div>
            <?php endfor; ?>
        </div>
    </div>

    <p class="client-logo-showcase-note"><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Shared delivery experience through Chulbul Design × SoftLes</p>
</section>

<!-- ============================================================
     PORTFOLIO / CASE STUDIES
============================================================ -->
<section class="py-24 bg-gray-50" id="portfolio">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="section-badge">Our Work</span>
            <h2 class="text-3xl lg:text-4xl font-extrabold text-[#49499A] mt-4">Real Projects. Real Results.</h2>
            <p class="text-gray-500 mt-3">500+ projects delivered across India, USA &amp; UK — here are a few stories.</p>
        </div>
        <div class="project-slider-shell" data-project-slider>
            <div class="project-slider-track" data-project-track tabindex="0" aria-label="Client project case studies">
            <?php
            $cases = [
                [
                    'gradient'  => 'from-blue-500 to-indigo-600',
                    'icon'      => 'bi-globe2',
                    'badge'     => 'Web Design',
                    'badgebg'   => 'bg-blue-50 text-blue-700',
                    'title'     => 'Hardware Exporter',
                    'city'      => 'Ludhiana, Punjab',
                    'problem'   => 'Old site had no mobile layout. PageSpeed score of 31. Zero enquiries from online traffic.',
                    'solution'  => 'Custom mobile-first redesign with SEO-optimised structure and sub-2 second load time.',
                    'result'    => 'PageSpeed jumped from 31 to 96. More enquiries in first week than previous month combined.',
                    'stat1'     => ['val'=>'96','lbl'=>'PageSpeed Score'],
                    'stat2'     => ['val'=>'3x','lbl'=>'More Enquiries'],
                ],
                [
                    'gradient'  => 'from-emerald-500 to-teal-600',
                    'icon'      => 'bi-shop',
                    'badge'     => 'Ecommerce',
                    'badgebg'   => 'bg-emerald-50 text-emerald-700',
                    'title'     => 'Leather Goods Brand',
                    'city'      => 'Dharavi, Mumbai',
                    'problem'   => 'WooCommerce store had 5-step checkout with no UPI. Instagram traffic was not converting.',
                    'solution'  => 'Rebuilt checkout to 2 steps. Added Razorpay, UPI, PhonePe and COD payment options.',
                    'result'    => 'Sales increased 4x in the first month. Cart abandonment dropped by 60%.',
                    'stat1'     => ['val'=>'4x','lbl'=>'Sales Growth'],
                    'stat2'     => ['val'=>'60%','lbl'=>'Less Abandonment'],
                ],
                [
                    'gradient'  => 'from-[#EE483D] to-rose-500',
                    'icon'      => 'bi-graph-up-arrow',
                    'badge'     => 'Google Ads',
                    'badgebg'   => 'bg-red-50 text-red-700',
                    'title'     => 'Coaching Institute',
                    'city'      => 'Laxmi Nagar, Delhi',
                    'problem'   => 'Google Ads running 6 months with high spend, very few admissions. CPL was ₹1,800.',
                    'solution'  => 'Restructured campaigns with intent-based keywords. Removed broad match, added local targeting.',
                    'result'    => 'Cost per lead dropped from ₹1,800 to ₹340. Same budget, 5x more admissions.',
                    'stat1'     => ['val'=>'₹340','lbl'=>'Cost Per Lead'],
                    'stat2'     => ['val'=>'5x','lbl'=>'ROI Improvement'],
                ],
                [
                    'gradient'  => 'from-violet-500 to-purple-700',
                    'icon'      => 'bi-phone',
                    'badge'     => 'Mobile App',
                    'badgebg'   => 'bg-violet-50 text-violet-700',
                    'title'     => 'Logistics Startup',
                    'city'      => 'Gurugram, Delhi NCR',
                    'problem'   => 'Manual order tracking via WhatsApp. Drivers had no app. Operations team spent 4 hours daily on coordination.',
                    'solution'  => 'Built React Native app for Android & iOS. Driver app + admin dashboard with live order tracking.',
                    'result'    => 'Launched on both platforms within budget. 4 hours of daily manual work eliminated.',
                    'stat1'     => ['val'=>'2','lbl'=>'Platforms Launched'],
                    'stat2'     => ['val'=>'4hrs','lbl'=>'Daily Time Saved'],
                ],
                [
                    'gradient'  => 'from-sky-500 to-blue-600',
                    'icon'      => 'bi-wordpress',
                    'badge'     => 'WordPress',
                    'badgebg'   => 'bg-sky-50 text-sky-700',
                    'title'     => 'IT Services Company',
                    'city'      => 'Sector 62, Noida',
                    'problem'   => 'WordPress site had 52 plugins, PageSpeed of 19, and crashed monthly. SEO was non-existent.',
                    'solution'  => 'Complete rebuild with custom lightweight theme. Plugins reduced from 52 to 6. Full on-page SEO.',
                    'result'    => 'PageSpeed jumped from 19 to 94. Site has not crashed since launch. Organic traffic up 180%.',
                    'stat1'     => ['val'=>'94','lbl'=>'PageSpeed Score'],
                    'stat2'     => ['val'=>'180%','lbl'=>'Organic Traffic'],
                ],
                [
                    'gradient'  => 'from-orange-500 to-amber-500',
                    'icon'      => 'bi-heart-pulse',
                    'badge'     => 'Healthcare',
                    'badgebg'   => 'bg-orange-50 text-orange-700',
                    'title'     => 'Multi-Specialty Clinic',
                    'city'      => 'Koramangala, Bangalore',
                    'problem'   => 'No website. Patients calling for appointments. No online presence or Google visibility.',
                    'solution'  => 'Custom clinic website with online appointment booking, doctor profiles and local SEO.',
                    'result'    => '3x more appointment bookings in 60 days. Now ranking page 1 for clinic-related searches.',
                    'stat1'     => ['val'=>'3x','lbl'=>'More Bookings'],
                    'stat2'     => ['val'=>'Pg 1','lbl'=>'Google Ranking'],
                ],
            ];
            foreach ($cases as $index => $c): ?>
            <article class="project-case-card" aria-label="<?= htmlspecialchars($c['title'], ENT_QUOTES, 'UTF-8') ?> case study">
                <div class="project-case-cover bg-gradient-to-br <?= $c['gradient'] ?>">
                    <div class="project-case-cover-pattern" aria-hidden="true"></div>
                    <i class="bi <?= $c['icon'] ?>" aria-hidden="true"></i>
                    <span><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> <?= $c['city'] ?></span>
                </div>
                <div class="project-case-body">
                    <span class="project-case-badge <?= $c['badgebg'] ?>"><?= $c['badge'] ?></span>
                    <h3 class="project-case-title"><?= $c['title'] ?></h3>
                    <div class="project-case-info-list">
                        <div class="project-case-info-row">
                            <span class="project-case-info-label is-problem">Problem</span>
                            <p><?= $c['problem'] ?></p>
                        </div>
                        <div class="project-case-info-row">
                            <span class="project-case-info-label is-fix">Fix</span>
                            <p><?= $c['solution'] ?></p>
                        </div>
                        <div class="project-case-info-row">
                            <span class="project-case-info-label is-result">Result</span>
                            <p><?= $c['result'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="project-case-footer">
                    <div class="project-case-metric">
                        <strong><?= $c['stat1']['val'] ?></strong>
                        <span><?= $c['stat1']['lbl'] ?></span>
                    </div>
                    <div class="project-case-metric">
                        <strong><?= $c['stat2']['val'] ?></strong>
                        <span><?= $c['stat2']['lbl'] ?></span>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
            </div>
            <div class="project-slider-footer">
                <span class="project-slider-status" data-project-status>Showing projects 1–3 of <?= count($cases) ?></span>
                <div class="project-slider-dots" data-project-dots aria-label="Choose project slide"></div>
            </div>
        </div>
        <div class="text-center mt-12">
            <a href="<?= $base ?>/contact-us" class="inline-flex items-center gap-2 bg-[#49499A] hover:bg-[#3a3a7a] text-white font-bold px-8 py-4 rounded-2xl transition-all hover:-translate-y-1 shadow-lg">
                <i class="bi bi-chat-dots-fill"></i> Discuss Your Project
            </a>
        </div>
    </div>
</section>

<script>
(() => {
    const slider = document.querySelector('[data-project-slider]');
    if (!slider) return;

    const track = slider.querySelector('[data-project-track]');
    const dots = slider.querySelector('[data-project-dots]');
    const status = slider.querySelector('[data-project-status]');
    const cards = Array.from(track.querySelectorAll('.project-case-card'));
    const total = <?= count($cases) ?>;
    const autoplayDelay = 4500;
    let pageCount = 1;
    let autoplayTimer = null;

    const visibleCount = () => {
        if (window.matchMedia('(max-width: 767px)').matches) return 1;
        if (window.matchMedia('(max-width: 1023px)').matches) return 2;
        return 3;
    };
    const maxScroll = () => Math.max(0, track.scrollWidth - track.clientWidth);
    const currentPage = () => {
        const max = maxScroll();
        return max ? Math.min(pageCount - 1, Math.round((track.scrollLeft / max) * (pageCount - 1))) : 0;
    };

    const updateStatus = (page) => {
        const visible = visibleCount();
        const start = (page * visible) + 1;
        const end = Math.min(start + visible - 1, total);
        status.textContent = visible === 1
            ? `Project ${start} of ${total}`
            : `Showing projects ${start}–${end} of ${total}`;
    };

    const update = () => {
        const page = currentPage();
        dots.querySelectorAll('button').forEach((dot, index) => {
            const active = index === page;
            dot.classList.toggle('is-active', active);
            dot.setAttribute('aria-current', active ? 'true' : 'false');
        });
        updateStatus(page);
    };

    const goToPage = (page) => {
        const cardIndex = page * visibleCount();
        const firstOffset = cards[0] ? cards[0].offsetLeft : 0;
        const targetCard = cards[Math.min(cardIndex, cards.length - 1)];
        const target = page === pageCount - 1
            ? maxScroll()
            : Math.max(0, (targetCard ? targetCard.offsetLeft : 0) - firstOffset);
        track.scrollTo({ left: target, behavior: 'smooth' });
    };

    const stopAutoplay = () => {
        if (autoplayTimer !== null) {
            window.clearInterval(autoplayTimer);
            autoplayTimer = null;
        }
    };

    const startAutoplay = () => {
        stopAutoplay();
        if (pageCount <= 1 || document.hidden || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        autoplayTimer = window.setInterval(() => {
            goToPage((currentPage() + 1) % pageCount);
        }, autoplayDelay);
    };

    const restartAutoplay = () => {
        stopAutoplay();
        startAutoplay();
    };

    const buildDots = () => {
        pageCount = Math.ceil(total / visibleCount());
        dots.replaceChildren();
        for (let index = 0; index < pageCount; index += 1) {
            const dot = document.createElement('button');
            dot.type = 'button';
            dot.className = 'project-slider-dot';
            dot.setAttribute('aria-label', `Go to project slide ${index + 1}`);
            dot.addEventListener('click', () => {
                goToPage(index);
                restartAutoplay();
            });
            dots.appendChild(dot);
        }
        update();
        startAutoplay();
    };

    track.addEventListener('scroll', update, { passive: true });
    track.addEventListener('keydown', (event) => {
        if (event.key === 'ArrowRight') {
            event.preventDefault();
            goToPage((currentPage() + 1) % pageCount);
            restartAutoplay();
        }
        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            goToPage((currentPage() - 1 + pageCount) % pageCount);
            restartAutoplay();
        }
    });
    slider.addEventListener('mouseenter', stopAutoplay);
    slider.addEventListener('mouseleave', startAutoplay);
    slider.addEventListener('focusin', stopAutoplay);
    slider.addEventListener('focusout', startAutoplay);
    track.addEventListener('touchstart', stopAutoplay, { passive: true });
    track.addEventListener('touchend', startAutoplay, { passive: true });
    document.addEventListener('visibilitychange', () => document.hidden ? stopAutoplay() : startAutoplay());

    let resizeTimer;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(buildDots, 120);
    });
    buildDots();
})();
</script>

<!-- ============================================================
     STRATEGIC TECHNOLOGY PARTNERSHIP
============================================================ -->
<section class="partner-showcase-section" aria-labelledby="partner-showcase-title">
    <div class="partner-showcase-inner">
        <header class="partner-showcase-heading">
            <span class="partner-showcase-badge"><i class="bi bi-people-fill" aria-hidden="true"></i> Strategic Technology Partnership</span>
            <h2 id="partner-showcase-title">Two Creative Teams. <span>One Delivery Standard.</span></h2>
            <p>Chulbul Design and SoftLes collaborate to deliver dependable digital products with broader expertise, faster execution and long-term support.</p>
        </header>

        <div class="partner-showcase-card">
            <div class="partner-showcase-glow partner-showcase-glow-one" aria-hidden="true"></div>
            <div class="partner-showcase-glow partner-showcase-glow-two" aria-hidden="true"></div>

            <div class="partner-brand-stage" aria-label="Chulbul Design and SoftLes partnership">
                <a class="partner-brand-card partner-brand-card-chulbul" href="<?= $base ?>/" aria-label="Visit Chulbul Design home page">
                    <span class="partner-brand-label">Digital Growth Partner</span>
                    <img src="<?= $base ?>/assets/images/logo/chulbuldesign.svg" alt="Chulbul Design" width="250" height="68" loading="lazy">
                </a>

                <div class="partner-brand-connector" aria-hidden="true">
                    <span></span>
                    <strong><i class="bi bi-link-45deg"></i></strong>
                    <span></span>
                </div>

                <a class="partner-brand-card partner-brand-card-softles" href="https://softles.in/" target="_blank" rel="noopener noreferrer" aria-label="Visit SoftLes website">
                    <span class="partner-brand-label">Technology Partner</span>
                    <span class="partner-softles-logo">
                        <strong>S</strong>
                        <span>SoftLes<small>.in</small></span>
                    </span>
                </a>

                <div class="partner-collaboration-note">
                    <i class="bi bi-patch-check-fill" aria-hidden="true"></i>
                    Independent teams, working together when your project needs more.
                </div>
            </div>

            <div class="partner-showcase-content">
                <span class="partner-showcase-kicker">Built to scale your ideas</span>
                <h3>More expertise behind every project.</h3>
                <p>From websites and eCommerce platforms to mobile apps and custom software, our partnership brings the right design and development talent together for every stage of delivery.</p>

                <ul class="partner-capability-list">
                    <li><i class="bi bi-check2-circle" aria-hidden="true"></i><span>Combined design and development expertise</span></li>
                    <li><i class="bi bi-check2-circle" aria-hidden="true"></i><span>Greater capacity for complex projects</span></li>
                    <li><i class="bi bi-check2-circle" aria-hidden="true"></i><span>Faster delivery with reliable support</span></li>
                    <li><i class="bi bi-check2-circle" aria-hidden="true"></i><span>India and international project support</span></li>
                </ul>

                <div class="partner-showcase-actions">
                    <a class="partner-action-primary" href="<?= $base ?>/contact-us">Discuss a Project <i class="bi bi-arrow-up-right" aria-hidden="true"></i></a>
                    <a class="partner-action-secondary" href="https://softles.in/" target="_blank" rel="noopener noreferrer">Visit SoftLes <i class="bi bi-box-arrow-up-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>

        <div class="partner-value-grid" aria-label="Partnership benefits">
            <div class="partner-value-item">
                <span><i class="bi bi-people" aria-hidden="true"></i></span>
                <div><strong>One Collaborative Team</strong><small>Clear communication across every stage</small></div>
            </div>
            <div class="partner-value-item">
                <span><i class="bi bi-layers" aria-hidden="true"></i></span>
                <div><strong>Wider Tech Expertise</strong><small>Web, commerce, apps and software</small></div>
            </div>
            <div class="partner-value-item">
                <span><i class="bi bi-headset" aria-hidden="true"></i></span>
                <div><strong>Reliable Support</strong><small>From strategy through post-launch growth</small></div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     DIGITAL MARKETING
============================================================ -->
<section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col-reverse lg:flex-row items-center gap-16">
            <div class="flex-1">
                <span class="section-badge">Digital Marketing</span>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-[#49499A] mt-4 mb-4 leading-tight">
                    Drive More Traffic. <br>Get More Customers.
                </h2>
                <p class="text-gray-600 leading-relaxed mb-6">
                    Support your website with <strong>SEO and digital marketing</strong> aligned to your audience and offer. We help plan search visibility, advertising and measurement around enquiries and sales, not traffic alone.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    <?php
                    $mktg = [
                        ['icon'=>'bi-search',        'label'=>'Search Engine Optimization'],
                        ['icon'=>'bi-google',        'label'=>'Google Ads & PPC'],
                        ['icon'=>'bi-instagram',     'label'=>'Social Media Marketing'],
                        ['icon'=>'bi-envelope-fill', 'label'=>'Email Marketing'],
                        ['icon'=>'bi-pencil-square', 'label'=>'Content Marketing'],
                        ['icon'=>'bi-bar-chart-fill','label'=>'Analytics & Reporting'],
                    ];
                    foreach ($mktg as $m): ?>
                    <div class="flex items-center gap-3 bg-white rounded-xl px-4 py-3 border border-gray-100 shadow-sm">
                        <i class="bi <?= $m['icon'] ?> text-[#EE483D] text-lg"></i>
                        <span class="text-gray-700 text-sm font-medium"><?= $m['label'] ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <a href="<?= $base ?>/local-seo-services"
                   class="inline-flex items-center gap-2 bg-[#49499A] text-white px-7 py-3.5 rounded-2xl font-bold hover:bg-indigo-700 transition-all hover:scale-105">
                    Explore All Services <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="flex-1 flex justify-center">
                <img src="<?= $base ?>/assets/images/Chulbuldesign_social.svg"
                     alt="Search and digital marketing illustration" class="w-full max-w-md blob-2"
                     width="480" height="400" loading="lazy">
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     INDUSTRIES
============================================================ -->
<section class="py-24" style="background: linear-gradient(135deg,#1e1e5c,#49499A)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="inline-block bg-white/10 border border-white/20 text-white text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full mb-4">Industries</span>
            <h2 class="text-3xl lg:text-4xl font-extrabold text-white">Digital Solutions Across 16 Industries</h2>
            <p class="text-white/50 mt-3">Explore website, software and automation services shaped around your industry and the people you serve.</p>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <?php
            require_once __DIR__ . '/includes/industry-catalog.php';
            foreach (cbd_industry_catalog() as $ind): ?>
            <a href="<?= $base ?>/industry/<?= $ind['slug'] ?>"
               class="group bg-white/10 hover:bg-white rounded-2xl p-6 flex flex-col items-center gap-3 border border-white/10 transition-all hover:-translate-y-1 hover:shadow-xl no-underline">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br <?= $ind['color'] ?> flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                    <i class="bi <?= $ind['icon'] ?> text-white text-xl"></i>
                </div>
                <h3 class="text-sm font-bold text-white group-hover:text-[#49499A] text-center transition-colors"><?= $ind['name'] ?></h3>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     TESTIMONIALS
============================================================ -->
<section class="py-24 bg-white" id="testimonials">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header with aggregate rating -->
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="section-badge">Client Reviews</span>
            <h2 class="text-3xl lg:text-4xl font-extrabold text-[#49499A] mt-4">What Our Clients Say</h2>
            <p class="text-gray-500 mt-3 mb-6">500+ projects. 10+ years. Here's what businesses say after working with us.</p>
            <!-- Aggregate rating bar -->
            <div class="inline-flex items-center gap-4 bg-amber-50 border border-amber-100 rounded-2xl px-6 py-4">
                <div class="text-4xl font-extrabold text-amber-500">4.9</div>
                <div class="text-left">
                    <div class="flex gap-0.5 mb-1">
                        <?php for($i=0;$i<5;$i++): ?><i class="bi bi-star-fill text-amber-400 text-sm"></i><?php endfor; ?>
                    </div>
                    <p class="text-xs text-gray-500 font-medium">Based on 200+ client reviews</p>
                    <p class="text-xs text-gray-400">Google · Clutch · Direct feedback</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $testimonials = [
                [
                    'name'    => 'Rajesh Sharma',
                    'role'    => 'CEO',
                    'company' => 'TechStart India',
                    'city'    => 'Noida, UP',
                    'project' => 'Web Design',
                    'rating'  => 5,
                    'text'    => 'Chulbul Design built our corporate website from scratch — no templates, complete custom design. The site loads in under 2 seconds and we are already getting organic enquiries within 3 months of launch. Best web design company in Noida.',
                ],
                [
                    'name'    => 'Priya Mehta',
                    'role'    => 'Founder',
                    'company' => 'EduLearn Platform',
                    'city'    => 'Gurugram, Delhi NCR',
                    'project' => 'WordPress Development',
                    'rating'  => 5,
                    'text'    => 'Our WordPress site had 52 plugins and crashed every month. Chulbul Design rebuilt everything from scratch, reduced plugins to 6, and the PageSpeed went from 19 to 94. It has not crashed once since launch. Highly recommended.',
                ],
                [
                    'name'    => 'Arun Kapoor',
                    'role'    => 'Director',
                    'company' => 'RealSpace Realty',
                    'city'    => 'Gurgaon, Haryana',
                    'project' => 'Ecommerce',
                    'rating'  => 5,
                    'text'    => 'They revamped our property listing portal with custom filters, WhatsApp lead integration and fast mobile performance. Our leads from Google increased 3x. The team communicates clearly and delivers what they promise.',
                ],
                [
                    'name'    => 'Sunita Joshi',
                    'role'    => 'Owner',
                    'company' => 'Artisan Leather Co.',
                    'city'    => 'Dharavi, Mumbai',
                    'project' => 'Ecommerce Website',
                    'rating'  => 5,
                    'text'    => 'Our Instagram traffic was never converting. Chulbul Design rebuilt our WooCommerce store with 2-step checkout and UPI payments. Sales went up 4x in the first month. I wish I had approached them earlier.',
                ],
                [
                    'name'    => 'Vivek Malhotra',
                    'role'    => 'Marketing Head',
                    'company' => 'Apex Coaching Centre',
                    'city'    => 'Laxmi Nagar, Delhi',
                    'project' => 'Google Ads',
                    'rating'  => 5,
                    'text'    => 'We were spending heavily on Google Ads with a cost per lead of ₹1,800. After Chulbul Design restructured the campaigns, CPL dropped to ₹340 — same budget, 5x the admissions. ROI is exceptional.',
                ],
                [
                    'name'    => 'Dr. Kavitha Rao',
                    'role'    => 'Medical Director',
                    'company' => 'HealthFirst Clinic',
                    'city'    => 'Koramangala, Bangalore',
                    'project' => 'Website + SEO',
                    'rating'  => 5,
                    'text'    => 'We had no digital presence at all. Chulbul Design built our clinic website with online booking and local SEO setup. Within 60 days we went from zero to page 1 on Google and appointment bookings tripled.',
                ],
            ];
            foreach ($testimonials as $t): ?>
            <div class="testimonial-card bg-white rounded-3xl p-7 border border-gray-100 shadow-sm hover:shadow-md transition-all">
                <!-- Stars + project badge -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex gap-0.5">
                        <?php for($i=0;$i<$t['rating'];$i++): ?>
                        <i class="bi bi-star-fill text-amber-400 text-sm"></i>
                        <?php endfor; ?>
                    </div>
                    <span class="text-xs font-semibold text-gray-400 bg-gray-50 border border-gray-100 px-2 py-1 rounded-lg"><?= $t['project'] ?></span>
                </div>
                <!-- Review text -->
                <p class="text-gray-600 text-sm leading-relaxed mb-6">"<?= $t['text'] ?>"</p>
                <!-- Client info -->
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#EE483D] to-[#49499A] flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                        <?= strtoupper(substr($t['name'],0,1)) ?>
                    </div>
                    <div class="min-w-0">
                        <div class="font-bold text-gray-800 text-sm"><?= $t['name'] ?></div>
                        <div class="text-gray-400 text-xs"><?= $t['role'] ?>, <?= $t['company'] ?></div>
                        <div class="text-gray-400 text-xs flex items-center gap-1 mt-0.5">
                            <i class="bi bi-geo-alt text-[#EE483D] text-xs"></i><?= $t['city'] ?>
                        </div>
                    </div>
                    <div class="ml-auto flex-shrink-0">
                        <i class="bi bi-patch-check-fill text-[#49499A] text-lg" title="Verified Client"></i>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============================================================
     CTA BANNER
============================================================ -->
<section class="py-10 px-4">
    <div class="max-w-7xl mx-auto">
        <div class="relative rounded-3xl overflow-hidden" style="background:linear-gradient(135deg,#EE483D,#c73528)">
            <!-- Pattern -->
            <div class="absolute inset-0 opacity-10"
                 style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:30px 30px"></div>
            <div class="relative flex flex-col lg:flex-row items-center justify-between gap-8 px-10 py-14">
                <div>
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-white leading-tight">
                        Ready to Start Your <br>Digital Journey?
                    </h2>
                    <p class="text-white/70 mt-3 max-w-lg">Join 200+ businesses who chose Chulbul Design as their digital growth partner. Free consultation — no commitment required.</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-4 flex-shrink-0">
                    <a href="https://wa.me/919990548795" target="_blank"
                       class="inline-flex items-center justify-center gap-2 bg-white text-[#EE483D] px-8 py-4 rounded-2xl font-extrabold hover:bg-gray-50 transition-all hover:scale-105 shadow-xl">
                        <i class="bi bi-whatsapp text-green-500 text-lg"></i> WhatsApp Now
                    </a>
                    <a href="<?= $base ?>/contact-us"
                       class="inline-flex items-center justify-center gap-2 bg-white/15 border border-white/30 text-white px-8 py-4 rounded-2xl font-extrabold hover:bg-white/25 transition-all hover:scale-105">
                        <i class="bi bi-envelope-fill"></i> Get Free Quote
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     LET'S BUILD
============================================================ -->
<section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-16">
            <div class="flex-1 flex justify-center">
                <img src="<?= $base ?>/assets/images/Chulbuldesign_lest.svg"
                     alt="Website project collaboration illustration" class="w-full max-w-md blob"
                     width="480" height="400" loading="lazy">
            </div>
            <div class="flex-1">
                <span class="section-badge">Let's Collaborate</span>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-[#49499A] mt-4 mb-4 leading-tight">
                    Let's Build Something <br>Amazing Together
                </h2>
                <p class="text-gray-600 leading-relaxed mb-8">
                    Whether you need a brand-new website, mobile app, logo, eCommerce store, or a complete digital marketing overhaul — Chulbul Design is your one-stop digital partner. 200+ businesses across India, USA & UK trust us.
                </p>
                <div class="flex flex-wrap gap-3">
                    <a href="https://wa.me/919990548795" target="_blank"
                       class="btn-glow inline-flex items-center gap-2 bg-[#EE483D] text-white px-7 py-3.5 rounded-2xl font-bold hover:bg-red-500 transition-all">
                        <i class="bi bi-chat-right-text-fill"></i> Start a Project
                    </a>
                    <a href="<?= $base ?>/about"
                       class="inline-flex items-center gap-2 border-2 border-[#49499A] text-[#49499A] px-7 py-3.5 rounded-2xl font-bold hover:bg-[#49499A] hover:text-white transition-all">
                        About Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     WHY CHOOSE US — CONTENT BLOCK (SEO)
============================================================ -->
<section class="py-24 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-start">

            <!-- Left: Main content -->
            <div>
                <span class="section-badge">Why Chulbul Design</span>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 mt-4 mb-6 leading-tight">
                    Web Design, Development<br>&amp; Worldwide Collaboration
                </h2>
                <p class="text-gray-600 leading-relaxed mb-5">
                    Chulbul Design is a web design and development company based in Gurugram, India, serving businesses worldwide. We connect website strategy, interface design and implementation around <strong>your customers and business requirements</strong>.
                </p>
                <p class="text-gray-600 leading-relaxed mb-5">
                    Our <a href="<?= $base ?>/web-design" class="text-[#49499A] underline">web design services</a> cover page structure, visual hierarchy and responsive customer journeys. Our <a href="<?= $base ?>/web-development" class="text-[#49499A] underline">web development services</a> turn approved designs into working websites, CMS features and integrations. Define the scope you need without treating every project as the same package.
                </p>
                <p class="text-gray-600 leading-relaxed mb-5">
                    For a content-led site, explore <a href="<?= $base ?>/wordpress-development" class="text-[#49499A] underline">WordPress development</a>. For online selling, review our <a href="<?= $base ?>/ecommerce-website-development" class="text-[#49499A] underline">ecommerce website development</a>. Updating an existing site? Plan your <a href="<?= $base ?>/website-redesign" class="text-[#49499A] underline">website redesign</a> around useful content, established URLs and the features your customers already rely on.
                </p>
                <p class="text-gray-600 leading-relaxed mb-8">
                    Remote delivery starts with a clear brief, an agreed proposal and scheduled reviews. We discuss content ownership, integrations, time-zone overlap, launch responsibilities and support before development begins. Review our selected work and ask about the team's role in projects relevant to yours.
                </p>

                <!-- Key differentiators -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php
                    $whys = [
                        ['icon'=>'bi-patch-check-fill','color'=>'text-blue-600','bg'=>'bg-blue-50','title'=>'Defined Project Scope','desc'=>'Agree page templates, features, revisions and third-party costs before the build.'],
                        ['icon'=>'bi-speedometer2','color'=>'text-emerald-600','bg'=>'bg-emerald-50','title'=>'Performance-Aware Build','desc'=>'Review loading, responsive behaviour and key journeys before launch.'],
                        ['icon'=>'bi-translate','color'=>'text-violet-600','bg'=>'bg-violet-50','title'=>'Worldwide Collaboration','desc'=>'Plan online reviews and communication around the agreed time-zone overlap.'],
                        ['icon'=>'bi-people-fill','color'=>'text-orange-600','bg'=>'bg-orange-50','title'=>'Practical Handover','desc'=>'Define editing access, training and maintenance responsibilities with your team.'],
                    ];
                    foreach ($whys as $w): ?>
                    <div class="flex items-start gap-3 bg-white rounded-2xl p-4 border border-gray-100 shadow-sm">
                        <div class="w-10 h-10 rounded-xl <?= $w['bg'] ?> flex items-center justify-center flex-shrink-0">
                            <i class="bi <?= $w['icon'] ?> <?= $w['color'] ?> text-lg"></i>
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm mb-0.5"><?= $w['title'] ?></p>
                            <p class="text-gray-500 text-xs leading-relaxed"><?= $w['desc'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right: SEO Hub — Popular Services, Industries, Cities -->
            <div class="seo-hub">

                <!-- Popular Services -->
                <div class="bg-white rounded-3xl p-7 border border-gray-100 shadow-sm">
                    <h3 class="text-base font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="bi bi-grid-fill text-[#EE483D]"></i> Popular Services
                    </h3>
                    <div class="space-y-2">
                        <?php
                        $popServices = [
                            ['/web-design','Custom Web Design'],
                            ['/web-development','Web Development Services'],
                            ['/ecommerce-website-development','Ecommerce Website Development'],
                            ['/wordpress-development','WordPress Website Development'],
                            ['/web-design-development','Web Design & Development Overview'],
                            ['/local-seo-services','Local SEO Services'],
                            ['/android-app-development','Android App Development'],
                            ['/ui-ux-branding','UI/UX Design Services'],
                        ];
                        foreach ($popServices as [$url, $label]): ?>
                        <a href="<?= $base ?><?= $url ?>" class="flex items-center justify-between py-2.5 px-3 rounded-xl hover:bg-gray-50 transition group border border-transparent hover:border-gray-100">
                            <span class="text-gray-700 text-sm font-medium group-hover:text-[#EE483D] transition"><?= $label ?></span>
                            <i class="bi bi-arrow-right text-gray-300 group-hover:text-[#EE483D] text-xs transition"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Industries -->
                <div class="bg-white rounded-3xl p-7 border border-gray-100 shadow-sm">
                    <h3 class="text-base font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="bi bi-building text-[#49499A]"></i> Industries We Serve
                    </h3>
                    <div class="flex flex-wrap gap-2">
                        <?php
                        foreach (cbd_industry_catalog() as $cbd_home_industry): ?>
                        <a href="<?= $base ?>/industry/<?= htmlspecialchars($cbd_home_industry['slug']) ?>" class="text-xs font-semibold text-gray-600 bg-gray-50 hover:bg-[#EE483D] hover:text-white border border-gray-100 px-3 py-1.5 rounded-full transition"><?= htmlspecialchars($cbd_home_industry['name']) ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Cities -->
                <div class="bg-white rounded-3xl p-7 border border-gray-100 shadow-sm">
                    <h3 class="text-base font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                        <i class="bi bi-geo-alt-fill text-emerald-500"></i> Cities We Serve
                    </h3>
                    <p class="text-xs text-gray-500 mb-3">Headquartered in <strong class="text-gray-700">Gurugram, Delhi NCR</strong> — serving clients across India, USA, UK & UAE.</p>
                    <div class="flex flex-wrap gap-2">
                        <?php
                        $cities = ['Gurgaon','Delhi','Noida','Mumbai','Bangalore','Hyderabad','Pune','Chennai','Jaipur','Chandigarh','Ahmedabad','Kolkata'];
                        $citySlugs = ['gurgaon','delhi','noida','mumbai','bangalore','hyderabad','pune','chennai','jaipur','chandigarh','ahmedabad','kolkata'];
                        foreach ($cities as $i => $city): ?>
                        <a href="<?= $base ?>/city/<?= $citySlugs[$i] ?>" class="text-xs font-semibold text-gray-600 bg-gray-50 hover:bg-[#49499A] hover:text-white border border-gray-100 px-3 py-1.5 rounded-full transition"><?= $city ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- ============================================================
     LATEST BLOG SECTION
============================================================ -->
<?php if (!empty($_hp_latest_posts)): ?>
<section class="py-20 bg-white" id="latest-blog">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex items-end justify-between mb-12">
            <div>
                <span class="section-badge">From Our Blog</span>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-[#1e1e5c] mt-4">Latest Insights</h2>
                <p class="text-gray-500 mt-2 max-w-lg">Web design tips, SEO strategies, and digital growth stories — straight from our team.</p>
            </div>
            <a href="<?= $_hp_base ?>/blog/"
               class="hidden sm:inline-flex items-center gap-2 text-sm font-bold text-[#49499A] hover:text-[#EE483D] transition group">
                View All Posts
                <i class="bi bi-arrow-right group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>

        <!-- Cards Row -->
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:1.75rem;" class="blog-cards-grid">
            <?php foreach ($_hp_latest_posts as $_hp_post): ?>
            <?php
                $_hp_date    = $_hp_post['date'] ? date('d M Y', strtotime($_hp_post['date'])) : '';
                $_hp_excerpt = mb_substr(strip_tags($_hp_post['excerpt'] ?? ''), 0, 110);
                if (strlen(strip_tags($_hp_post['excerpt'] ?? '')) > 110) $_hp_excerpt .= '…';
            ?>
            <a href="<?= $_hp_base ?>/blog/<?= htmlspecialchars($_hp_post['slug']) ?>"
               style="display:flex;flex-direction:column;border-radius:1.25rem;overflow:hidden;border:1px solid #f3f4f6;box-shadow:0 1px 4px rgba(0,0,0,.06);background:#fff;text-decoration:none;transition:transform .25s,box-shadow .25s;"
               onmouseover="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 32px rgba(0,0,0,.10)'"
               onmouseout="this.style.transform='';this.style.boxShadow='0 1px 4px rgba(0,0,0,.06)'">

                <!-- Thumbnail -->
                <div style="width:100%;height:196px;overflow:hidden;background:#f3f4f6;flex-shrink:0;">
                    <?php if ($_hp_post['thumb']): ?>
                    <img src="<?= htmlspecialchars($_hp_post['thumb']) ?>"
                         alt="<?= htmlspecialchars($_hp_post['title']) ?>"
                         style="width:100%;height:100%;object-fit:cover;display:block;"
                         loading="lazy">
                    <?php else: ?>
                    <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,rgba(73,73,154,.08),rgba(238,72,61,.08))">
                        <i class="bi bi-journal-richtext" style="font-size:3rem;color:#49499A;opacity:.3;"></i>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Content -->
                <div style="padding:1.4rem 1.5rem;display:flex;flex-direction:column;flex:1;">
                    <div style="display:flex;gap:14px;font-size:11px;color:#9ca3af;margin-bottom:10px;">
                        <?php if ($_hp_date): ?>
                        <span><i class="bi bi-calendar3" style="margin-right:3px;"></i><?= $_hp_date ?></span>
                        <?php endif; ?>
                        <?php if ($_hp_post['read_time']): ?>
                        <span><i class="bi bi-clock" style="margin-right:3px;"></i><?= htmlspecialchars($_hp_post['read_time']) ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 style="font-size:15px;font-weight:800;color:#1e1e5c;line-height:1.4;margin:0 0 8px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">
                        <?= htmlspecialchars($_hp_post['title']) ?>
                    </h3>
                    <?php if ($_hp_excerpt): ?>
                    <p style="font-size:13px;color:#6b7280;line-height:1.6;margin:0;flex:1;">
                        <?= htmlspecialchars($_hp_excerpt) ?>
                    </p>
                    <?php endif; ?>
                    <div style="margin-top:14px;font-size:12px;font-weight:700;color:#49499A;display:flex;align-items:center;gap:5px;">
                        Read More <i class="bi bi-arrow-right"></i>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>

        <!-- Mobile View More -->
        <div class="text-center mt-10 sm:hidden">
            <a href="<?= $_hp_base ?>/blog/"
               class="inline-flex items-center gap-2 bg-[#49499A] text-white font-bold px-7 py-3 rounded-xl hover:bg-[#1e1e5c] transition shadow-lg shadow-indigo-200">
                View All Posts <i class="bi bi-arrow-right"></i>
            </a>
        </div>

    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     FAQ SECTION
============================================================ -->
<section class="py-24 bg-white" id="faq">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14">
            <span class="section-badge">FAQ</span>
            <h2 class="text-3xl lg:text-4xl font-extrabold text-[#49499A] mt-4">Frequently Asked Questions</h2>
            <p class="text-gray-500 mt-3">Everything you need to know about our web design and development services.</p>
        </div>
        <div class="space-y-4" x-data="{open: null}">
            <?php
            foreach ($homeFaqs as $i => $faq): ?>
            <div class="border border-gray-200 rounded-2xl overflow-hidden" x-data="{open: false}">
                <button @click="open = !open" class="w-full flex items-center justify-between px-6 py-5 text-left bg-white hover:bg-gray-50 transition-colors">
                    <span class="font-bold text-gray-800 text-base pr-4"><?= $faq['q'] ?></span>
                    <i class="bi flex-shrink-0 text-[#EE483D] text-lg transition-transform" :class="open ? 'bi-dash-circle-fill rotate-180' : 'bi-plus-circle-fill'"></i>
                </button>
                <div x-show="open" x-collapse class="px-6 pb-5">
                    <p class="text-gray-500 text-sm leading-relaxed"><?= $faq['a'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ═══════════════════════════════════════════════
     GLOBAL CITIES — Internal Linking Section
═══════════════════════════════════════════════ -->
<section class="bg-gray-50 py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Where We Work</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-4 mb-2">
                Web Design &amp; Development Services Worldwide
            </h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Based in Gurugram, India, with remote delivery for businesses worldwide. Explore service information for India, the USA, UK, Australia, Canada and UAE. These are service areas, not claims of local offices.</p>
        </div>

        <?php
        $city_groups = [
            ['flag'=>'🇮🇳', 'country'=>'India',        'cities'=>[['Delhi','delhi'],['Gurgaon','gurgaon'],['Noida','noida'],['Mumbai','mumbai'],['Bangalore','bangalore'],['Hyderabad','hyderabad'],['Chennai','chennai'],['Pune','pune'],['Kolkata','kolkata'],['Ahmedabad','ahmedabad'],['Jaipur','jaipur'],['Chandigarh','chandigarh'],['Amritsar','amritsar'],['Meerut','meerut'],['Coimbatore','coimbatore'],['Bhubaneswar','bhubaneswar']]],
            ['flag'=>'🇺🇸', 'country'=>'USA',          'cities'=>[['New York','new-york'],['Los Angeles','los-angeles'],['Chicago','chicago'],['Houston','houston'],['Dallas','dallas'],['San Francisco','san-francisco'],['Miami','miami'],['Seattle','seattle'],['Boston','boston'],['Atlanta','atlanta']]],
            ['flag'=>'🇬🇧', 'country'=>'UK',           'cities'=>[['London','london'],['Manchester','manchester'],['Birmingham','birmingham'],['Leeds','leeds'],['Glasgow','glasgow'],['Edinburgh','edinburgh'],['Bristol','bristol']]],
            ['flag'=>'🇦🇺', 'country'=>'Australia',    'cities'=>[['Sydney','sydney'],['Melbourne','melbourne'],['Brisbane','brisbane'],['Perth','perth'],['Adelaide','adelaide']]],
            ['flag'=>'🇨🇦', 'country'=>'Canada',       'cities'=>[['Toronto','toronto'],['Vancouver','vancouver'],['Calgary','calgary'],['Montreal','montreal'],['Ottawa','ottawa']]],
            ['flag'=>'🇦🇪', 'country'=>'UAE',          'cities'=>[['Dubai','dubai'],['Abu Dhabi','abu-dhabi'],['Sharjah','sharjah']]],
            ['flag'=>'🇸🇬', 'country'=>'Singapore',    'cities'=>[['Singapore','singapore']]],
            ['flag'=>'🇿🇦', 'country'=>'South Africa', 'cities'=>[['Johannesburg','johannesburg'],['Cape Town','cape-town'],['Durban','durban']]],
        ];
        foreach ($city_groups as $group): ?>
        <div class="mb-6">
            <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">
                <?= $group['flag'] ?> <?= $group['country'] ?>
            </p>
            <div class="flex flex-wrap gap-2">
                <?php foreach ($group['cities'] as [$cname, $cslug]): ?>
                <a href="<?= $base ?>/city/<?= $cslug ?>"
                   class="text-sm text-gray-600 bg-white border border-gray-200 hover:border-[#49499A] hover:text-[#49499A] px-4 py-1.5 rounded-full transition shadow-sm font-medium">
                    <?= $cname ?>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
