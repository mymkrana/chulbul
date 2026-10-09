<?php
require_once __DIR__ . '/includes/http.php';
if (cbd_is_direct_script_request('about.php')) cbd_redirect_path('/about');
?>
<!DOCTYPE html>
<html lang="en-IN">
<head><meta charset="utf-8">
    <title>About Chulbul Design | Web Design Agency Gurugram Since 2013</title>
    <link rel="canonical" href="https://www.chulbuldesign.com/about">
    <meta name="description" content="Chulbul Design — a Gurugram-born web design & development agency founded in 2013. 500+ projects, 10+ years, clients across India, USA, UK & UAE. Meet our team.">
    <meta property="og:title" content="About Chulbul Design | Gurugram Web Agency Since 2013">
    <meta property="og:url" content="https://www.chulbuldesign.com/about">
    <meta property="og:type" content="website">
    <meta property="og:description" content="From a small studio in Sector 15, Gurugram to 500+ projects delivered globally — the story of Chulbul Design. Meet the team behind your digital growth.">
    <meta property="og:image" content="https://www.chulbuldesign.com/assets/images/Fb_chulbuldesign.jpg">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@chulbuldesign">
    <meta name="twitter:image" content="https://www.chulbuldesign.com/assets/images/Fb_chulbuldesign.jpg">

    <!-- Organization Schema -->
    <script type="application/ld+json">{
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "Chulbul Design",
        "url": "https://www.chulbuldesign.com",
        "logo": "https://www.chulbuldesign.com/assets/images/logo/chulbuldesign.svg",
        "foundingDate": "2013",
        "foundingLocation": "Gurugram, Haryana, India",
        "description": "Web design, development and digital marketing agency based in Gurugram, India. Serving clients across India, USA, UK and UAE since 2013.",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Sector 15, Flat No. 1277",
            "addressLocality": "Gurugram",
            "addressRegion": "Haryana",
            "postalCode": "122001",
            "addressCountry": "IN"
        },
        "telephone": "+919990548795",
        "email": "info@chulbuldesign.com",
        "numberOfEmployees": {"@type": "QuantitativeValue", "value": 15},
        "sameAs": [
            "https://www.facebook.com/chulbuldesign/",
            "https://www.instagram.com/chulbuldesign/",
            "https://twitter.com/ChulbulDesign/",
            "https://www.linkedin.com/company/chulbuldesign/"
        ]
    }</script>

    <!-- BreadcrumbList Schema -->
    <script type="application/ld+json">{
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {"@type": "ListItem", "position": 1, "name": "Home", "item": "https://www.chulbuldesign.com/"},
            {"@type": "ListItem", "position": 2, "name": "About Us", "item": "https://www.chulbuldesign.com/about"}
        ]
    }</script>

    <?php require __DIR__ . '/includes/header.php'; ?>

<?php
$hero = [
    'badge'      => 'About Us',
    'breadcrumb' => 'About',
    'h1'         => 'Built in Gurugram. <span class="text-[#EE483D]">Trusted Globally.</span>',
    'desc'       => 'From a small studio in Sector 15 to 500+ projects delivered across India, USA, UK & UAE — this is the story of Chulbul Design.',
    'img'        => '/assets/images/Aboutus_vh.png',
    'img_alt'    => 'About Chulbul Design — Web Design Agency Gurugram',
];
require __DIR__ . '/includes/page-hero.php';
?>


<!-- ═══════════════════════════════════════════════
     OUR STORY SECTION
═══════════════════════════════════════════════ -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex flex-col lg:flex-row items-center gap-14">

        <div class="flex-1 flex justify-center">
            <img src="<?= $base ?>/assets/images/Aboutus_vh.png"
                 alt="Chulbul Design office Gurugram"
                 class="w-full max-w-md rounded-2xl shadow-lg"
                 width="480" height="360" fetchpriority="high">
        </div>

        <div class="flex-1">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Our Story</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1e1e5c] mt-5 mb-5 leading-tight">
                How a Gurugram Studio Became a Global Web Agency
            </h2>
            <p class="text-gray-600 leading-relaxed mb-4">
                Chulbul Design started in 2013 with one clear belief — that small and medium businesses in India deserve world-class websites without enterprise-level budgets. We set up shop in Sector 15, Gurugram, armed with strong design instincts, clean code practices, and a genuine passion for helping businesses grow online.
            </p>
            <p class="text-gray-600 leading-relaxed mb-4">
                In our early years, we worked closely with local businesses in Delhi NCR — restaurants, clinics, real estate firms, and startups — learning exactly what makes a website convert visitors into customers, not just look pretty. That ground-level understanding is something no agency can fake.
            </p>
            <p class="text-gray-600 leading-relaxed">
                Over 10+ years, we've grown from a 2-person studio into a full-service digital team delivering web design, e-commerce, mobile apps, and SEO campaigns for clients across 4 countries. The name has always stayed the same — bold, memorable, and a little unconventional — just like the work we do.
            </p>
        </div>

    </div>
</section>


<!-- ═══════════════════════════════════════════════
     NUMBERS STRIP
═══════════════════════════════════════════════ -->
<section class="bg-[#1e1e5c] py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-6 text-center">
            <?php
            $milestones = [
                ['num' => '2013',  'label' => 'Founded in Gurugram'],
                ['num' => '500+',  'label' => 'Projects Delivered'],
                ['num' => '4',     'label' => 'Countries Served'],
                ['num' => '10+',   'label' => 'Years of Expertise'],
                ['num' => '5★',    'label' => 'Average Google Rating'],
            ];
            foreach ($milestones as $m): ?>
            <div class="text-white">
                <p class="text-4xl font-extrabold text-[#EE483D] mb-1"><?= $m['num'] ?></p>
                <p class="text-white/70 text-sm"><?= $m['label'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     WHAT MAKES US DIFFERENT
═══════════════════════════════════════════════ -->
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Why We're Different</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1e1e5c] mt-5 mb-3">
                We Don't Just Build Websites — We Build Business Assets
            </h2>
            <p class="text-gray-500 max-w-2xl mx-auto">Most agencies hand you a pretty design. We give you a site engineered to rank, load fast, and convert — because that's what actually grows your business.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            $diff = [
                [
                    'icon'  => 'bi-speedometer2',
                    'title' => 'PageSpeed 95+ as Standard',
                    'desc'  => 'We don\'t treat performance as an afterthought. Every site we build is optimized for Core Web Vitals — faster load times mean better rankings and lower bounce rates.',
                ],
                [
                    'icon'  => 'bi-search',
                    'title' => 'SEO Baked In From Day One',
                    'desc'  => 'Schema markup, canonical tags, meta titles, image alt tags — all done during build, not as an add-on. Your site is Google-ready the day it goes live.',
                ],
                [
                    'icon'  => 'bi-phone',
                    'title' => 'Mobile-First, Always',
                    'desc'  => 'Over 70% of Indian web traffic is mobile. We design for mobile screens first, then scale up — not the other way around like traditional agencies.',
                ],
                [
                    'icon'  => 'bi-translate',
                    'title' => 'Deep Understanding of Indian Market',
                    'desc'  => 'We know how Indian users browse, what builds trust locally, and how to position businesses for both domestic and international audiences simultaneously.',
                ],
                [
                    'icon'  => 'bi-currency-rupee',
                    'title' => 'Honest, Transparent Pricing',
                    'desc'  => 'No hidden charges, no surprise invoices. You get a fixed-price quote before we start, and that\'s what you pay. Every rupee accounted for.',
                ],
                [
                    'icon'  => 'bi-headset',
                    'title' => 'Post-Launch Support Included',
                    'desc'  => 'We don\'t disappear after delivery. Every project includes free support period — real support from the team who built your site, not a helpdesk ticket.',
                ],
            ];
            foreach ($diff as $d): ?>
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm hover:shadow-md transition">
                <div class="w-12 h-12 rounded-xl bg-[#49499A]/10 flex items-center justify-center mb-4">
                    <i class="bi <?= $d['icon'] ?> text-xl text-[#49499A]"></i>
                </div>
                <h3 class="font-bold text-[#1e1e5c] mb-2"><?= $d['title'] ?></h3>
                <p class="text-gray-500 text-sm leading-relaxed"><?= $d['desc'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     OUR JOURNEY TIMELINE
═══════════════════════════════════════════════ -->
<section class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-12">
        <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Our Journey</span>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1e1e5c] mt-5 mb-3">
            From a Startup Idea to 500+ Delivered Projects
        </h2>
    </div>

    <div class="relative">
        <!-- Vertical line -->
        <div class="absolute left-6 top-0 bottom-0 w-0.5 bg-gradient-to-b from-[#EE483D] to-[#49499A]"></div>

        <?php
        $timeline = [
            ['year' => '2013', 'title' => 'The Beginning', 'desc' => 'Chulbul Design founded in Sector 15, Gurugram. First clients were local SMEs — a restaurant chain, a property dealer, and a coaching centre in Delhi NCR.'],
            ['year' => '2015', 'title' => 'First 100 Projects', 'desc' => 'Crossed 100 delivered websites. Expanded services to include SEO and digital marketing. Started serving clients outside Delhi NCR for the first time.'],
            ['year' => '2017', 'title' => 'Going International', 'desc' => 'First international clients onboarded — UK-based retail brand and a UAE logistics company. Proved that Indian agencies could deliver global-quality work at honest prices.'],
            ['year' => '2019', 'title' => 'E-Commerce Surge', 'desc' => 'Became a specialist in Shopify, WooCommerce and custom e-commerce builds as Indian online retail exploded. Dedicated e-commerce team formed.'],
            ['year' => '2021', 'title' => 'AI & Automation Era', 'desc' => 'Integrated AI tools into our development workflow and began offering AI chatbot integration and automation services to clients across industries.'],
            ['year' => '2024+', 'title' => 'Full-Service Digital Partner', 'desc' => '500+ projects delivered. Serving clients in India, USA, UK & UAE across 8 industries. Still the same commitment — build sites that actually grow businesses.'],
        ];
        foreach ($timeline as $t): ?>
        <div class="relative flex items-start gap-6 mb-10 pl-16">
            <div class="absolute left-0 w-12 h-12 rounded-full bg-[#EE483D] flex items-center justify-center text-white text-xs font-bold shrink-0 shadow-md shadow-red-200">
                <?= $t['year'] === '2024+' ? '\'24+' : "'" . substr($t['year'], 2) ?>
            </div>
            <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100 flex-1">
                <p class="text-xs font-bold text-[#EE483D] uppercase tracking-widest mb-1"><?= $t['year'] ?></p>
                <h3 class="font-extrabold text-[#1e1e5c] mb-2"><?= $t['title'] ?></h3>
                <p class="text-gray-500 text-sm leading-relaxed"><?= $t['desc'] ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     TEAM SECTION
═══════════════════════════════════════════════ -->
<section class="bg-[#f8f9ff] py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Our Team</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1e1e5c] mt-5 mb-3">
                The People Behind Your Digital Growth
            </h2>
            <p class="text-gray-500 max-w-2xl mx-auto">A multidisciplinary team blending strategy, design, engineering and growth. We align to outcomes, move fast, and keep every project transparent from brief to launch.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $team = [
                [
                    'name'   => 'Shakti Singh',
                    'role'   => 'Strategy Lead',
                    'img'    => '/assets/images/team/shakti-singh.jpg',
                    'desc'   => 'Drives vision and strategic clarity — connecting market insight, execution, and leadership to deliver long-term, sustainable digital growth for every client.',
                    'quote'  => 'Strong strategy is not about ideas — it\'s about making the right decisions consistently.',
                    'color'  => '#EE483D',
                ],
                [
                    'name'   => 'Shahad Hassan',
                    'role'   => 'FullStack Developer',
                    'img'    => '/assets/images/team/shahad-hassan.jpg',
                    'desc'   => 'Engineers robust, scalable applications with a focus on performance, security, and maintainable architecture across both frontend and backend systems.',
                    'quote'  => 'Clean architecture today prevents production fires tomorrow.',
                    'color'  => '#49499A',
                ],
                [
                    'name'   => 'Neeraj Kumar',
                    'role'   => 'Shopify Developer',
                    'img'    => '/assets/images/team/neeraj-kumar.jpg',
                    'desc'   => 'Builds and optimises high-converting Shopify stores with a strong emphasis on performance, scalability, and seamless customer journeys from browse to checkout.',
                    'quote'  => 'E-commerce success lives at the intersection of speed, clarity, and trust.',
                    'color'  => '#f59e0b',
                ],
                [
                    'name'   => 'Divyansh Veermanya',
                    'role'   => 'Product Lead',
                    'img'    => '/assets/images/team/divyansh-veermanya.jpg',
                    'desc'   => 'Translates client goals into structured product roadmaps — ensuring every feature shipped solves a real business problem and delivers measurable outcomes.',
                    'quote'  => 'Great products are built backwards from the user, not forward from the code.',
                    'color'  => '#10b981',
                ],
            ];
            foreach ($team as $member): ?>
            <div class="bg-white rounded-2xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col">

                <!-- Photo -->
                <div class="relative overflow-hidden" style="height:220px">
                    <img src="<?= $base . $member['img'] ?>"
                         alt="<?= htmlspecialchars($member['name']) ?> — <?= htmlspecialchars($member['role']) ?> at Chulbul Design"
                         class="w-full h-full object-cover object-top"
                         loading="lazy" width="300" height="220">
                    <div class="absolute bottom-0 left-0 right-0 h-16 bg-gradient-to-t from-white to-transparent"></div>
                </div>

                <!-- Info -->
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-extrabold text-[#1e1e5c] text-lg mb-0.5"><?= htmlspecialchars($member['name']) ?></h3>
                    <p class="text-xs font-bold tracking-widest uppercase mb-3" style="color:<?= $member['color'] ?>"><?= htmlspecialchars($member['role']) ?></p>
                    <p class="text-gray-500 text-sm leading-relaxed mb-4 flex-1"><?= htmlspecialchars($member['desc']) ?></p>

                    <!-- Quote -->
                    <div class="bg-gray-50 rounded-xl px-4 py-3 border-l-4 mt-auto" style="border-color:<?= $member['color'] ?>">
                        <p class="text-xs text-gray-600 italic leading-relaxed">"<?= htmlspecialchars($member['quote']) ?>"</p>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     VISION & MISSION
═══════════════════════════════════════════════ -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="flex flex-col lg:flex-row items-center gap-14">

        <div class="flex-1 flex justify-center">
            <img src="<?= $base ?>/assets/images/vision.svg"
                 alt="Chulbul Design vision and mission"
                 class="w-full max-w-md" width="480" height="400" loading="lazy">
        </div>

        <div class="flex-1">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Vision & Mission</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1e1e5c] mt-5 mb-5">
                Helping Indian Businesses Compete Online — Globally
            </h2>

            <div class="space-y-5">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-[#EE483D]/10 flex items-center justify-center shrink-0 mt-1">
                        <i class="bi bi-eye-fill text-[#EE483D]"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-[#1e1e5c] mb-1">Our Vision</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">To be the most trusted web design partner for SMEs across India — businesses that deserve a strong digital presence but have been let down by overpriced or underdelivered agencies in the past.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-[#49499A]/10 flex items-center justify-center shrink-0 mt-1">
                        <i class="bi bi-bullseye text-[#49499A]"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-[#1e1e5c] mb-1">Our Mission</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Every website we build must perform — load fast, rank on Google, look great on mobile, and convert visitors into paying customers. We measure success not by design awards but by the growth we create for clients.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center shrink-0 mt-1">
                        <i class="bi bi-patch-check-fill text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-[#1e1e5c] mb-1">Our Promise</h3>
                        <p class="text-gray-500 text-sm leading-relaxed">Transparent pricing. No hidden costs. Delivery on time. Post-launch support included. If we say we'll do something, it gets done — that's a commitment we've kept for over a decade.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     CORE VALUES
═══════════════════════════════════════════════ -->
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center gap-14">

            <div class="flex-1">
                <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Our Values</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#1e1e5c] mt-5 mb-6">
                    The Principles That Guide Every Project We Take On
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php
                    $values = [
                        ['icon' => 'bi-shield-check',      'title' => 'Integrity',        'desc' => 'We say what we mean and deliver what we promise. No exaggerated claims, no scope creep without consent.'],
                        ['icon' => 'bi-lightbulb',          'title' => 'Craftsmanship',    'desc' => 'We care deeply about the quality of what we build. Every pixel, every line of code, every word of copy matters.'],
                        ['icon' => 'bi-people-fill',        'title' => 'Partnership',      'desc' => 'We treat every client as a long-term partner, not a one-time project. Your success is our portfolio.'],
                        ['icon' => 'bi-arrow-repeat',       'title' => 'Continuous Growth','desc' => 'We stay current with technologies, algorithms, and design trends so our clients always have a competitive edge.'],
                    ];
                    foreach ($values as $v): ?>
                    <div class="flex items-start gap-3 bg-white rounded-xl p-4 border border-gray-100 shadow-sm">
                        <div class="w-9 h-9 rounded-lg bg-[#49499A]/10 flex items-center justify-center shrink-0">
                            <i class="bi <?= $v['icon'] ?> text-[#49499A]"></i>
                        </div>
                        <div>
                            <p class="font-bold text-[#1e1e5c] text-sm mb-1"><?= $v['title'] ?></p>
                            <p class="text-gray-500 text-xs leading-relaxed"><?= $v['desc'] ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex-1 flex justify-center">
                <img src="<?= $base ?>/assets/images/ourvelue.svg"
                     alt="Core values of Chulbul Design"
                     class="w-full max-w-md" width="480" height="400" loading="lazy">
            </div>

        </div>
    </div>
</section>


<!-- ═══════════════════════════════════════════════
     CTA SECTION
═══════════════════════════════════════════════ -->
<section class="city-cta-section">
    <div class="city-cta-deco-1" aria-hidden="true"></div>
    <div class="city-cta-deco-2" aria-hidden="true"></div>
    <div class="city-cta-deco-3" aria-hidden="true"></div>
    <div class="city-cta-deco-4" aria-hidden="true"></div>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 city-cta-inner">
        <div class="city-cta-heading-wrap">
            <span class="city-cta-badge">
                <i class="bi bi-lightning-charge-fill"></i> Work With Us
            </span>
        </div>
        <h2 class="city-cta-title">
            Ready to Grow Your<br>
            <span class="city-cta-title-highlight">Business Online?</span>
        </h2>
        <p class="city-cta-desc">
            Over 500 businesses have trusted Chulbul Design to build their digital presence. Let's talk about yours — free consultation, no commitment, honest advice.
        </p>
        <div class="city-cta-stats">
            <div class="city-cta-stat"><i class="bi bi-patch-check-fill city-cta-stat-icon-green"></i> 500+ Projects Delivered</div>
            <div class="city-cta-stat"><i class="bi bi-globe2 city-cta-stat-icon-blue"></i> 4 Countries Served</div>
            <div class="city-cta-stat"><i class="bi bi-star-fill city-cta-stat-icon-yellow"></i> 5★ Google Rating</div>
            <div class="city-cta-stat"><i class="bi bi-clock-fill city-cta-stat-icon-pink"></i> Reply within 1 Hour</div>
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


<?php require __DIR__ . '/includes/footer.php'; ?>
