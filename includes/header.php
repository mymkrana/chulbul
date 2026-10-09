<?php
require_once __DIR__ . '/database.php';
$base = cbd_base_path();
$__cbd_public_tracking = cbd_is_production_host();

// Load mega menu data from DB
require_once __DIR__ . '/menu-data.php';
require_once __DIR__ . '/industry-catalog.php';
$cbd_industry_menu = cbd_industry_catalog();

// Icon map per category slug (fallback if not in DB)
$_menu_icons = [
    'web-design'      => 'bi-globe2',
    'ecommerce'       => 'bi-cart4',
    'mobile-software' => 'bi-phone',
    'technologies'    => 'bi-code-square',
    'seo-marketing'   => 'bi-graph-up-arrow',
    'ui-branding'     => 'bi-vector-pen',
    'support-speed'   => 'bi-tools',
    'ai-automation'   => 'bi-robot',
];
?>
    <?php if (empty($cbd_page_has_charset)): ?>
    <meta charset="UTF-8">
    <?php endif; ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="<?= htmlspecialchars($page_robots ?? 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1') ?>">
    <meta name="theme-color" content="#EE483D">
    <meta name="author" content="Chulbul Design">
    <?php if (!empty($_geo_meta)): ?>
    <meta name="geo.region" content="<?= htmlspecialchars($_geo_meta['region']) ?>">
    <meta name="geo.placename" content="<?= htmlspecialchars($_geo_meta['placename']) ?>">
    <meta name="geo.position" content="<?= htmlspecialchars($_geo_meta['pos']) ?>">
    <meta name="ICBM" content="<?= htmlspecialchars($_geo_meta['icbm']) ?>">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="512x512" href="<?= $base ?>/assets/images/logo/favicon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="<?= $base ?>/assets/images/logo/apple-touch-icon.png">

    <?php if ($__cbd_public_tracking): ?>
    <!-- Google Analytics GA4 (production only) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-Z45EZTKR2J"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-Z45EZTKR2J');
    </script>
    <?php endif; ?>

    <!-- Preconnects -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <?php if ($__cbd_public_tracking): ?>
    <link rel="preconnect" href="https://www.googletagmanager.com">
    <link rel="dns-prefetch" href="https://www.google-analytics.com">
    <?php endif; ?>

    <!-- Tailwind CSS (compiled — no render-blocking JS) -->
    <link rel="stylesheet" href="<?= $base ?>/assets/css/tailwind.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $base ?>/assets/css/custom.css?v=<?= (int) (@filemtime(__DIR__ . '/../assets/css/custom.css') ?: 1) ?>">
    <?php foreach (($page_styles ?? []) as $_page_stylesheet):
        $_page_stylesheet = '/' . ltrim((string) $_page_stylesheet, '/');
        $_page_stylesheet_file = dirname(__DIR__) . $_page_stylesheet;
    ?>
    <link rel="stylesheet" href="<?= $base . htmlspecialchars($_page_stylesheet) ?>?v=<?= (int) (@filemtime($_page_stylesheet_file) ?: 1) ?>">
    <?php endforeach; ?>
    <!-- Dynamic category colours (generated from DB) -->
    <style><?php foreach ($_mega_menu_data as $_mci => $_mcat): $_mc = $_mcat['color'] ?? '#3b82f6'; $_mhb = $_mcat['hover_bg'] ?? '#eff6ff'; $_mhex = ltrim($_mc,'#'); $_mr = hexdec(substr($_mhex,0,2)); $_mg = hexdec(substr($_mhex,2,2)); $_mb = hexdec(substr($_mhex,4,2)); echo ".mm-cat-{$_mci}{--cat-color:{$_mc};--cat-hover-bg:{$_mhb};--cat-r:{$_mr};--cat-g:{$_mg};--cat-b:{$_mb}}.mm-mob-{$_mci}{--cat-color:{$_mc}}"; endforeach; ?></style>

    <!-- Critical custom CSS (inlined) -->
    <style>
[x-cloak]{display:none!important}
*{font-family:'Plus Jakarta Sans',Inter,sans-serif}
h1,h2,h3,h4,h5,h6{font-family:'Plus Jakarta Sans',sans-serif;letter-spacing:-0.02em}
.typewrite>.wrap{border-right:.08em solid #EE483D}
@keyframes gradientShift{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
.hero-gradient{background:linear-gradient(-45deg,#1e1e5c,#49499A,#2d2d7a,#3b0f6e);background-size:400% 400%;animation:gradientShift 10s ease infinite}
@keyframes float{0%,100%{transform:translateY(0) rotate(0deg)}33%{transform:translateY(-20px) rotate(5deg)}66%{transform:translateY(10px) rotate(-3deg)}}
.blob{animation:float 8s ease-in-out infinite}.blob-2{animation:float 11s ease-in-out infinite reverse}
@keyframes fadeInUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
.fade-up{animation:fadeInUp .7s ease forwards}.fade-up-2{animation:fadeInUp .9s ease forwards}.fade-up-3{animation:fadeInUp 1.1s ease forwards}
.mm-cat-name-link{text-decoration:none;color:inherit;transition:color .2s}
.mm-cat-name-link:hover{color:var(--cat-color,#3b82f6)}
.gradient-text{background:linear-gradient(135deg,#EE483D,#ff8a65);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text}
.glass{background:rgba(255,255,255,.07);backdrop-filter:blur(12px);border:1px solid rgba(255,255,255,.12)}
.mobile-backdrop{position:fixed!important;inset:0;background:rgba(0,0,0,.5);z-index:40}
.mobile-panel{position:fixed!important;top:0;left:0;height:100vh;width:18rem;max-width:85vw;background:#fff;z-index:50;display:flex;flex-direction:column;box-shadow:8px 0 30px rgba(0,0,0,.15);overflow-y:auto}
    </style>

    <!-- Bootstrap Icons woff2 preload (critical for icon rendering) -->
    <link rel="preload" as="font" type="font/woff2" crossorigin href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/fonts/bootstrap-icons.woff2?dd67030699838ea613ee6dbda90effa6">

    <!-- Google Fonts (non-blocking) -->
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&display=swap" rel="stylesheet"></noscript>

    <!-- Bootstrap Icons (non-blocking) -->
    <link rel="preload" as="style" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"></noscript>

    <!-- Alpine.js (deferred) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js"></script>

</head>
<body x-data="{ mobileOpen: false }" class="font-sans bg-white text-gray-800 antialiased">


<!-- Navbar -->
<nav aria-label="Main navigation" class="bg-white shadow-md sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Logo -->
            <a href="<?= $base ?>/" class="flex-shrink-0">
                <img src="<?= $base ?>/assets/images/logo/chulbuldesign.svg" alt="Chulbul Design Logo" class="h-8" width="198" height="32">
            </a>

            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center gap-6">


                <!-- Services Mega Menu -->
                <div x-data="{ open: false }" @mouseenter="open=true" @mouseleave="open=false">
                    <button :aria-expanded="open.toString()" aria-haspopup="true"
                        :style="open ? 'color:#EE483D' : ''"
                        class="flex items-center gap-1 text-gray-700 hover:text-[#EE483D] font-medium transition focus-visible:outline-none rounded">
                        Services
                        <i class="bi bi-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''" aria-hidden="true"></i>
                    </button>

                    <!-- FULL-WIDTH MEGA MENU PANEL -->
                    <div x-cloak x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         role="menu"
                         class="mm-panel">

                        <!-- Top accent bar -->
                        <div class="mm-accent-bar"></div>

                        <div class="mm-container">

                            <!-- Dynamic category grid from DB -->
                            <?php if (!empty($_mega_menu_data)): ?>
                            <div class="mm-grid">
                            <?php foreach ($_mega_menu_data as $ci => $mcat):
                                $icon = $_menu_icons[$mcat['slug']] ?? 'bi-grid';
                            ?>
                                <div class="mm-cat-cell mm-cat-<?= $ci ?>">
                                    <div class="mm-cat-header">
                                        <div class="mm-cat-icon-box">
                                            <i class="bi <?= $icon ?> mm-cat-icon"></i>
                                        </div>
                                        <?php if (!empty($mcat['hub_slug'])): ?>
                                        <a href="<?= $base ?>/<?= htmlspecialchars($mcat['hub_slug']) ?>"
                                           class="mm-cat-name mm-cat-name-link"><?= htmlspecialchars($mcat['name']) ?></a>
                                        <?php else: ?>
                                        <span class="mm-cat-name"><?= htmlspecialchars($mcat['name']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="mm-cat-links">
                                        <?php foreach ($mcat['children'] as $child): ?>
                                        <a href="<?= $base ?>/<?= htmlspecialchars($child['slug']) ?>"
                                           role="menuitem"
                                           class="mm-cat-link">
                                            <?= htmlspecialchars($child['name']) ?>
                                        </a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            </div><!-- /grid -->
                            <?php endif; ?>

                            <!-- Bottom CTA bar -->
                            <div class="mm-bottom-bar">
                                <span class="mm-bottom-bar-text">Not sure which service fits your project? <strong>We'll guide you — free.</strong></span>
                                <a href="<?= $base ?>/contact-us" class="mm-bottom-bar-link">
                                    Get Free Consultation <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>

                        </div><!-- /max-width container -->
                    </div><!-- /mega menu panel -->
                </div>

                <!-- Industries Mega Menu -->
                <div class="relative" x-data="{ open: false, closeTimer: null }" @mouseenter="clearTimeout(closeTimer); open=true" @mouseleave="closeTimer=setTimeout(() => open=false, 200)" @focusout="if (!$el.contains($event.relatedTarget)) open=false" @keydown.escape.prevent.stop="$refs.industryToggle.focus(); open=false">
                    <button x-ref="industryToggle" @click="open = !open" @keydown.arrow-down.prevent="open=true; $nextTick(() => $el.parentElement.querySelector('[role=menuitem]').focus())" :aria-expanded="open.toString()" aria-haspopup="true" aria-controls="desktop-industry-menu"
                        :style="open ? 'color:#EE483D' : ''"
                        class="flex items-center gap-1 text-gray-700 hover:text-[#EE483D] font-medium transition focus-visible:outline-none rounded">
                        Industries
                        <i class="bi bi-chevron-down text-xs transition-transform duration-200" :class="open ? 'rotate-180' : ''" aria-hidden="true"></i>
                    </button>
                    <div x-cloak x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0"
                         role="menu"
                         class="mm-panel" id="desktop-industry-menu">
                        <div class="mm-accent-bar"></div>
                        <div class="mm-container">
                            <div class="ind-header rounded-xl mb-4">
                                <span class="ind-header-title">Industries We Serve</span>
                                <span class="ind-header-count"><?= count($cbd_industry_menu) ?> industry verticals</span>
                            </div>
                            <!-- Reuse the Services four-column grid: 16 industries, four rows. -->
                            <div class="mm-grid">
                                <?php foreach ($cbd_industry_menu as $cbd_industry_nav): ?>
                                <a href="<?= $base ?>/industry/<?= htmlspecialchars($cbd_industry_nav['slug']) ?>" role="menuitem" class="mm-cat-cell ind-item <?= htmlspecialchars($cbd_industry_nav['menu_class']) ?>">
                                    <div class="ind-icon-box"><i class="bi <?= htmlspecialchars($cbd_industry_nav['icon']) ?>" aria-hidden="true"></i></div>
                                    <div><p class="ind-item-name"><?= htmlspecialchars($cbd_industry_nav['name']) ?></p><p class="ind-item-desc"><?= htmlspecialchars($cbd_industry_nav['description']) ?></p></div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <div class="mm-bottom-bar">
                                <span class="mm-bottom-bar-text">Need a solution for your industry? <strong>Let's discuss your project.</strong></span>
                                <a href="<?= $base ?>/contact-us" class="mm-bottom-bar-link">Get Free Consultation <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <a href="<?= $base ?>/about" class="text-gray-700 hover:text-[#EE483D] font-medium transition">About Us</a>
                <a href="<?= $base ?>/blog/" class="text-gray-700 hover:text-[#EE483D] font-medium transition">Blog</a>
                <a href="<?= $base ?>/contact-us" class="text-gray-700 hover:text-[#EE483D] font-medium transition">Contact Us</a>

                <!-- Social Icons -->
                <div class="flex items-center gap-3 ml-2 text-lg">
                    <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" class="text-gray-500 hover:text-green-500 transition"><i class="bi bi-whatsapp" aria-hidden="true"></i></a>
                    <a href="https://www.facebook.com/chulbuldesign/" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="text-gray-500 hover:text-blue-600 transition"><i class="bi bi-facebook" aria-hidden="true"></i></a>
                    <a href="https://www.instagram.com/chulbuldesign/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="text-gray-500 hover:text-pink-500 transition"><i class="bi bi-instagram" aria-hidden="true"></i></a>
                    <a href="https://twitter.com/ChulbulDesign/" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X" class="text-gray-500 hover:text-sky-500 transition"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
                    <a href="https://www.linkedin.com/company/chulbuldesign/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="text-gray-500 hover:text-blue-700 transition"><i class="bi bi-linkedin" aria-hidden="true"></i></a>
                </div>
            </div>

            <!-- Mobile Hamburger -->
            <button @click="mobileOpen = true" class="lg:hidden text-gray-700 text-2xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#EE483D] rounded" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu">
                <i class="bi bi-list" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</nav>

<!-- Mobile Backdrop -->
<div x-cloak x-show="mobileOpen"
     @click="mobileOpen = false"
     aria-hidden="true"
     class="mobile-backdrop lg:hidden"
     x-transition:enter="transition-opacity ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
</div>

<!-- Mobile Slide Panel -->
<div x-cloak x-show="mobileOpen"
     id="mobile-menu"
     role="dialog"
     aria-modal="true"
     aria-label="Navigation menu"
     class="mobile-panel lg:hidden"
     x-transition:enter="transition-transform ease-out duration-300"
     x-transition:enter-start="-translate-x-full"
     x-transition:enter-end="translate-x-0"
     x-transition:leave="transition-transform ease-in duration-200"
     x-transition:leave-start="translate-x-0"
     x-transition:leave-end="-translate-x-full">

    <!-- Panel Header -->
    <div class="flex justify-between items-center px-5 py-4 border-b border-gray-100 flex-shrink-0">
        <a href="<?= $base ?>/">
            <img src="<?= $base ?>/assets/images/logo/chulbuldesign.svg" alt="Chulbul Design" class="h-8" width="198" height="32">
        </a>
        <button @click="mobileOpen = false" class="text-gray-500 hover:text-gray-800 text-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#EE483D] rounded" aria-label="Close menu">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>

    <!-- Panel Links -->
    <div class="flex-1 px-5 py-4 space-y-1 overflow-y-auto">


        <!-- Services Accordion -->
        <div x-data="{ open: false }">
            <button @click="open = !open" :aria-expanded="open.toString()" class="flex justify-between w-full py-2.5 text-gray-700 font-semibold border-b border-gray-50 focus-visible:outline-none focus-visible:text-[#EE483D]">
                Services <i class="bi bi-chevron-down transition-transform duration-200" :class="open ? 'rotate-180' : ''" aria-hidden="true"></i>
            </button>
            <div x-cloak x-show="open" x-transition class="pt-2 pb-3 space-y-3">
                <?php if (!empty($_mega_menu_data)): ?>
                <?php foreach ($_mega_menu_data as $ci => $mcat): ?>
                <?php if (empty($mcat['children'])) continue; ?>
                <div>
                    <?php if (!empty($mcat['hub_slug'])): ?>
                    <a href="<?= $base ?>/<?= htmlspecialchars($mcat['hub_slug']) ?>"
                       class="mm-mobile-cat-label mm-mob-<?= $ci ?> block"
                    ><?= htmlspecialchars($mcat['name']) ?> <i class="bi bi-arrow-right text-xs"></i></a>
                    <?php else: ?>
                    <p class="mm-mobile-cat-label mm-mob-<?= $ci ?>"
                    ><?= htmlspecialchars($mcat['name']) ?></p>
                    <?php endif; ?>
                    <?php foreach ($mcat['children'] as $child): ?>
                    <a href="<?= $base ?>/<?= htmlspecialchars($child['slug']) ?>"
                       class="block py-1.5 px-2 text-sm text-gray-600 hover:text-[#EE483D] rounded"
                    ><?= htmlspecialchars($child['name']) ?></a>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Industries Accordion -->
        <div x-data="{ open: false }">
            <button @click="open = !open" :aria-expanded="open.toString()" class="flex justify-between w-full py-2.5 text-gray-700 font-semibold border-b border-gray-50 focus-visible:outline-none focus-visible:text-[#EE483D]">
                Industries <i class="bi bi-chevron-down transition-transform duration-200" :class="open ? 'rotate-180' : ''" aria-hidden="true"></i>
            </button>
            <div x-cloak x-show="open" x-transition class="pl-3 pt-1 pb-2 space-y-1">
                <?php foreach ($cbd_industry_menu as $cbd_industry_nav): ?>
                <a href="<?= $base ?>/industry/<?= htmlspecialchars($cbd_industry_nav['slug']) ?>" class="flex items-center gap-2 py-1.5 text-sm text-gray-600 hover:text-[#EE483D]"><i class="bi <?= htmlspecialchars($cbd_industry_nav['icon']) ?> text-[#49499A]" aria-hidden="true"></i> <?= htmlspecialchars($cbd_industry_nav['name']) ?></a>
                <?php endforeach; ?>
            </div>
        </div>

        <a href="<?= $base ?>/about" class="flex items-center py-2.5 text-gray-700 font-semibold border-b border-gray-50 hover:text-[#EE483D]">About Us</a>
        <a href="<?= $base ?>/blog/" class="flex items-center py-2.5 text-gray-700 font-semibold border-b border-gray-50 hover:text-[#EE483D]">Blog</a>
        <a href="<?= $base ?>/contact-us" class="flex items-center py-2.5 text-gray-700 font-semibold hover:text-[#EE483D]">Contact Us</a>

        <!-- Social Icons -->
        <div class="flex gap-4 pt-4 text-xl">
            <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" class="text-gray-400 hover:text-green-500"><i class="bi bi-whatsapp" aria-hidden="true"></i></a>
            <a href="https://www.facebook.com/chulbuldesign/" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="text-gray-400 hover:text-blue-600"><i class="bi bi-facebook" aria-hidden="true"></i></a>
            <a href="https://www.instagram.com/chulbuldesign/" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="text-gray-400 hover:text-pink-500"><i class="bi bi-instagram" aria-hidden="true"></i></a>
            <a href="https://twitter.com/ChulbulDesign/" target="_blank" rel="noopener noreferrer" aria-label="Twitter/X" class="text-gray-400 hover:text-sky-500"><i class="bi bi-twitter-x" aria-hidden="true"></i></a>
            <a href="https://www.linkedin.com/company/chulbuldesign/" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn" class="text-gray-400 hover:text-blue-700"><i class="bi bi-linkedin" aria-hidden="true"></i></a>
        </div>
    </div>

    <!-- CTA Button -->
    <div class="px-5 py-4 border-t border-gray-100 flex-shrink-0">
        <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer"
           class="flex items-center justify-center gap-2 w-full bg-[#EE483D] text-white py-3 rounded-xl font-semibold hover:bg-red-600 transition">
            <i class="bi bi-whatsapp" aria-hidden="true"></i> Get Free Quote
        </a>
    </div>
</div>

<main id="main-content">
