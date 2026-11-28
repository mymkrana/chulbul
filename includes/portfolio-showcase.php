<?php
declare(strict_types=1);

/**
 * Chulbul Design — Reusable "Work Worth Showing Off" Portfolio Component
 *
 * Drop onto ANY page in 2 lines:
 *   require_once __DIR__ . '/includes/portfolio-showcase.php';
 *   cbd_render_portfolio_showcase();
 *
 * Optional $options:
 *   - 'id'         => 'portfolio'                 (section HTML id)
 *   - 'eyebrow'    => 'Selected Work'
 *   - 'title'      => 'Work worth showing off'
 *   - 'subtitle'   => 'Real, live builds...'
 *   - 'start_slug' => 'ssf-global'                (which project starts active)
 *   - 'only_slugs' => ['brunswick-fur-food', ...] (optional subset)
 *   - 'category'   => ''                          (optional filter: Website|E-commerce|SaaS|Web app)
 *   - 'note'       => 'Shared delivery showcase through Chulbul Design × SoftLes'
 */

if (!function_exists('cbd_portfolio_projects')) {
    function cbd_portfolio_projects(string $basePath = ''): array
    {
        $imgDir = rtrim($basePath, '/') . '/assets/images/portfolio-work';

        return [
            [
                'slug'       => 'ssf-global',
                'name'       => 'SSF Global',
                'category'   => 'Website',
                'tech'       => 'Next.js',
                'summary'    => 'Cross-border medical care, coordinated — a bilingual EN/FR site that makes an intimidating journey feel followable.',
                'highlights' => [
                    'Full EN/FR parity across every page',
                    '21-step patient journey, made legible',
                    '26 centers of excellence directory',
                    'Compliance-first: facilitator, never provider',
                ],
                'metrics'    => [
                    ['val' => '780+', 'label' => 'Patients served'],
                    ['val' => '30+',  'label' => 'Source countries'],
                    ['val' => '12+',  'label' => 'Country offices'],
                ],
                'pages'      => [
                    ['label' => 'Home',            'desktop' => "$imgDir/ssf-p0-d.jpg", 'mobile' => "$imgDir/ssf-p0-m.jpg"],
                    ['label' => 'Patient journey', 'desktop' => "$imgDir/ssf-p1-d.jpg", 'mobile' => "$imgDir/ssf-p1-m.jpg"],
                    ['label' => 'Specialties',     'desktop' => "$imgDir/ssf-p2-d.jpg", 'mobile' => "$imgDir/ssf-p2-m.jpg"],
                    ['label' => 'Global presence', 'desktop' => "$imgDir/ssf-p3-d.jpg", 'mobile' => "$imgDir/ssf-p3-m.jpg"],
                ],
            ],
            [
                'slug'       => 'brunswick-fur-food',
                'name'       => 'Brunswick Fur Food',
                'category'   => 'E-commerce',
                'tech'       => 'Shopify',
                'summary'    => 'Fresh, human-grade dog food for Melbourne — a Shopify store rebuilt to turn product quality into trust and repeat orders.',
                'highlights' => [
                    'Conversion-focused custom theme',
                    'Streamlined subscription & trial flow',
                    'Mobile-first product discovery',
                ],
                'metrics'    => [
                    ['val' => '2.5k+', 'label' => 'Orders'],
                    ['val' => '30%',   'label' => 'Repeat rate'],
                ],
                'pages'      => [
                    ['label' => 'Home',         'desktop' => "$imgDir/brunswick-p0-d.jpg", 'mobile' => "$imgDir/brunswick-p0-m.jpg"],
                    ['label' => 'Subscription', 'desktop' => "$imgDir/brunswick-p1-d.jpg", 'mobile' => "$imgDir/brunswick-p1-m.jpg"],
                    ['label' => 'About',        'desktop' => "$imgDir/brunswick-p2-d.jpg", 'mobile' => "$imgDir/brunswick-p2-m.jpg"],
                ],
            ],
            [
                'slug'       => 'sandeshsetu',
                'name'       => 'SandeshSetu',
                'category'   => 'SaaS',
                'tech'       => 'Web app',
                'summary'    => 'A WhatsApp Business platform — campaigns, automations and a shared inbox in one workspace, wrapped in a crisp product-marketing site.',
                'highlights' => [
                    'Bold, modern product narrative',
                    'Interactive dashboard visuals',
                    'Early-access lead capture',
                ],
                'metrics'    => [],
                'pages'      => [
                    ['label' => 'Home',     'desktop' => "$imgDir/sandeshsetu-p0-d.jpg", 'mobile' => "$imgDir/sandeshsetu-p0-m.jpg"],
                    ['label' => 'Features', 'desktop' => "$imgDir/sandeshsetu-p1-d.jpg", 'mobile' => "$imgDir/sandeshsetu-p1-m.jpg"],
                    ['label' => 'Pricing',  'desktop' => "$imgDir/sandeshsetu-p2-d.jpg", 'mobile' => "$imgDir/sandeshsetu-p2-m.jpg"],
                ],
            ],
            [
                'slug'       => 'recobee',
                'name'       => 'RecoBee',
                'category'   => 'Web app',
                'tech'       => '',
                'summary'    => 'Movie reviews, ratings and watchlists across OTTs — a discovery platform designed to feel cinematic and effortless.',
                'highlights' => [
                    'Cinematic, media-rich interface',
                    'Search & discovery flows',
                    'Editorial blog for organic reach',
                ],
                'metrics'    => [],
                'pages'      => [
                    ['label' => 'Home', 'desktop' => "$imgDir/recobee-p0-d.jpg", 'mobile' => "$imgDir/recobee-p0-m.jpg"],
                    ['label' => 'Blog', 'desktop' => "$imgDir/recobee-p1-d.jpg", 'mobile' => "$imgDir/recobee-p1-m.jpg"],
                ],
            ],
            [
                'slug'       => 'librarysetu',
                'name'       => 'LibrarySetu',
                'category'   => 'SaaS',
                'tech'       => 'Web app',
                'summary'    => 'Study-library management software — seats, students, fees and dues in one calm dashboard built for Indian study libraries.',
                'highlights' => [
                    'Clear product storytelling',
                    'Confident SaaS visual system',
                    'Fast and responsive on every device',
                ],
                'metrics'    => [],
                'pages'      => [
                    ['label' => 'Home', 'desktop' => "$imgDir/librarysetu-p0-d.jpg", 'mobile' => "$imgDir/librarysetu-p0-m.jpg"],
                ],
            ],
            [
                'slug'       => 'umang-aatray',
                'name'       => 'Umang Aatray',
                'category'   => 'Website',
                'tech'       => '',
                'summary'    => 'Personal-brand site for a commercial & criminal lawyer in New Delhi — editorial, trustworthy, and built to book intro calls.',
                'highlights' => [
                    'Editorial personal brand',
                    'Credentials front-and-centre',
                    'Clear consultation CTAs',
                ],
                'metrics'    => [],
                'pages'      => [
                    ['label' => 'Home', 'desktop' => "$imgDir/umang-p0-d.jpg", 'mobile' => "$imgDir/umang-p0-m.jpg"],
                ],
            ],
            [
                'slug'       => 'ayla-solutions',
                'name'       => 'Ayla Solutions',
                'category'   => 'Website',
                'tech'       => 'Headless',
                'summary'    => 'A data & AI consultancy site — corporate credibility and clear service storytelling, built headless for speed.',
                'highlights' => [
                    'Corporate, credibility-first design',
                    'Clear service & solution storytelling',
                    'Fast headless build',
                ],
                'metrics'    => [],
                'pages'      => [
                    ['label' => 'Home', 'desktop' => "$imgDir/ayla-p0-d.jpg", 'mobile' => "$imgDir/ayla-p0-m.jpg"],
                ],
            ],
            [
                'slug'       => 'tuitionly',
                'name'       => 'Tuitionly',
                'category'   => 'Website',
                'tech'       => '',
                'summary'    => 'An online tuition service for all age groups — a bright, trustworthy site built to turn parents into booked demo sessions.',
                'highlights' => [
                    'Warm, trustworthy visual system',
                    'Curriculum-specific pages (IB, IGCSE, CBSE, AP)',
                    'Demo-booking CTAs throughout',
                ],
                'metrics'    => [],
                'pages'      => [
                    ['label' => 'Home',          'desktop' => "$imgDir/tuitionly-p0-d.jpg", 'mobile' => "$imgDir/tuitionly-p0-m.jpg"],
                    ['label' => 'IB curriculum', 'desktop' => "$imgDir/tuitionly-p1-d.jpg", 'mobile' => "$imgDir/tuitionly-p1-m.jpg"],
                ],
            ],
            [
                'slug'       => 'artannu',
                'name'       => 'Artannu Fine Art Studio',
                'category'   => 'E-commerce',
                'tech'       => 'Custom Store',
                'summary'    => 'Fine art studio & ecommerce storefront for original Indian paintings, custom on-demand canvases, wall murals, and bridal mehndi bookings.',
                'highlights' => [
                    'Multi-currency storefront (USD / INR) & art catalogue',
                    'Custom painting demand & wall mural quote flows',
                    'Editorial storytelling with direct WhatsApp enquiry',
                ],
                'metrics'    => [],
                'pages'      => [
                    ['label' => 'Home', 'desktop' => "$imgDir/artannu-p0-d.jpg", 'mobile' => "$imgDir/artannu-p0-m.jpg"],
                ],
            ],
            [
                'slug'       => 'gpmanch',
                'name'       => 'Gurjar Pragati Manch',
                'category'   => 'Website',
                'tech'       => 'Bilingual Portal',
                'summary'    => 'Bilingual Hindi/English community and heritage digital platform featuring structured directories, historical archives, and social initiatives.',
                'highlights' => [
                    'Bilingual Hindi / English navigation & publishing',
                    'Structured archives across history, awards & achievers',
                    'Mentorship & community initiative landing sections',
                ],
                'metrics'    => [],
                'pages'      => [
                    ['label' => 'Home', 'desktop' => "$imgDir/gpmanch-p0-d.jpg", 'mobile' => "$imgDir/gpmanch-p0-m.jpg"],
                ],
            ],
            [
                'slug'       => 'upmuddamanch',
                'name'       => 'UP Mudda Manch',
                'category'   => 'Website',
                'tech'       => 'Civic & News Portal',
                'summary'    => 'Grassroots civic dialogue and regional news platform built for public participation across education, health, roads, water, and village issues.',
                'highlights' => [
                    'Regional news feed with city & topic navigation',
                    'Public issue submission & volunteer onboarding flows',
                    'Fast mobile-first Hindi editorial typography',
                ],
                'metrics'    => [],
                'pages'      => [
                    ['label' => 'Home', 'desktop' => "$imgDir/upmuddamanch-p0-d.jpg", 'mobile' => "$imgDir/upmuddamanch-p0-m.jpg"],
                ],
            ],
            [
                'slug'       => 'teraghar',
                'name'       => 'Tera Ghar',
                'category'   => 'Web app',
                'tech'       => 'Real Estate',
                'summary'    => 'Luxury real estate discovery portal for buying, selling, and renting properties with featured listings and direct site-visit booking.',
                'highlights' => [
                    'High-contrast dark luxury real estate visual system',
                    'Property discovery & direct site-visit booking CTAs',
                    'Integrated WhatsApp & property-listing lead flows',
                ],
                'metrics'    => [],
                'pages'      => [
                    ['label' => 'Home', 'desktop' => "$imgDir/teraghar-p0-d.jpg", 'mobile' => "$imgDir/teraghar-p0-m.jpg"],
                ],
            ],
            [
                'slug'       => 'prideandjustice',
                'name'       => 'Pride & Justice',
                'category'   => 'Website',
                'tech'       => 'Law Firm',
                'summary'    => 'Authoritative law firm website showcasing litigation, advisory, and corporate practice areas with structured team profiles and appointment booking.',
                'highlights' => [
                    'Clear practice-area architecture & case status access',
                    'Credibility-first legal typography & team showcase',
                    'Streamlined consultation & appointment booking',
                ],
                'metrics'    => [],
                'pages'      => [
                    ['label' => 'Home', 'desktop' => "$imgDir/prideandjustice-p0-d.jpg", 'mobile' => "$imgDir/prideandjustice-p0-m.jpg"],
                ],
            ],
        ];
    }
}

if (!function_exists('cbd_render_portfolio_showcase')) {
    function cbd_render_portfolio_showcase(array $options = []): void
    {
        static $assetsPrinted = false;

        $base = function_exists('cbd_base_path') ? cbd_base_path() : '';
        $projects = cbd_portfolio_projects($base);

        // Optional filtering by slugs or category
        if (!empty($options['only_slugs']) && is_array($options['only_slugs'])) {
            $allowed = array_flip($options['only_slugs']);
            $projects = array_values(array_filter(
                $projects,
                static fn(array $p): bool => isset($allowed[$p['slug']])
            ));
        }
        if (!empty($options['category']) && is_string($options['category'])) {
            $wantCat = strtolower(trim($options['category']));
            $filtered = array_values(array_filter(
                $projects,
                static fn(array $p): bool => strtolower($p['category']) === $wantCat
            ));
            if ($filtered !== []) {
                $projects = $filtered;
            }
        }
        if ($projects === []) {
            return;
        }

        if (!empty($options['hide_metrics'])) {
            foreach ($projects as &$proj) {
                $proj['metrics'] = [];
            }
            unset($proj);
        }

        $startIndex = 0;
        if (!empty($options['start_slug']) && is_string($options['start_slug'])) {
            foreach ($projects as $idx => $p) {
                if ($p['slug'] === $options['start_slug']) {
                    $startIndex = $idx;
                    break;
                }
            }
        }

        $sectionId = (string)($options['id'] ?? 'portfolio');
        $eyebrow   = (string)($options['eyebrow'] ?? 'Selected Work');
        $title     = (string)($options['title'] ?? 'Work worth showing off');
        $subtitle  = (string)($options['subtitle'] ?? 'Real, live builds — e-commerce, SaaS products and business sites. Switch between desktop and mobile, flip through the pages, and hover to pause.');
        $note      = (string)($options['note'] ?? 'Shared delivery showcase through Chulbul Design × SoftLes');

        $first     = $projects[$startIndex];
        $firstPage = $first['pages'][0];
        $total     = count($projects);
        $esc       = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

        if (!$assetsPrinted) {
            $assetsPrinted = true;
            $cssVer = (int)(@filemtime(__DIR__ . '/../assets/css/portfolio-showcase.css') ?: 1);
            $jsVer  = (int)(@filemtime(__DIR__ . '/../assets/js/portfolio-showcase.js') ?: 1);
            $cssHref = $esc($base . '/assets/css/portfolio-showcase.css?v=' . $cssVer);
            echo '<link rel="stylesheet" href="' . $cssHref . '" media="print" onload="this.media=\'all\'">' . "\n";
            echo '<noscript><link rel="stylesheet" href="' . $cssHref . '"></noscript>' . "\n";
            echo '<script src="' . $esc($base . '/assets/js/portfolio-showcase.js?v=' . $jsVer) . '" defer></script>' . "\n";
        }
        ?>
<section class="cbd-work-section" id="<?= $esc($sectionId) ?>" data-cbd-work data-start-index="<?= $startIndex ?>" aria-labelledby="<?= $esc($sectionId) ?>-heading">
    <div class="cbd-work-container">
        <div class="cbd-work-head">
            <div class="cbd-work-eyebrow">
                <span class="cbd-work-eyebrow-line" aria-hidden="true"></span>
                <span><?= $esc($eyebrow) ?></span>
            </div>
            <h2 class="cbd-work-title" id="<?= $esc($sectionId) ?>-heading"><?= $esc($title) ?></h2>
            <p class="cbd-work-subtitle"><?= $esc($subtitle) ?></p>
        </div>

        <!-- Project Pill Selector (Single-Line Horizontal Scroll with Arrows) -->
        <div class="cbd-work-tabs-bar">
            <button type="button" class="cbd-work-tabs-nav is-prev" data-cw-tabs-prev aria-label="Scroll projects left">
                <i class="bi bi-chevron-left" aria-hidden="true"></i>
            </button>
            <div class="cbd-work-tabs" data-cw-tabs-track role="tablist" aria-label="Choose portfolio project">
                <?php foreach ($projects as $idx => $proj): ?>
                <button type="button"
                        class="cbd-work-tab<?= $idx === $startIndex ? ' is-active' : '' ?>"
                        role="tab"
                        aria-selected="<?= $idx === $startIndex ? 'true' : 'false' ?>"
                        data-cw-tab="<?= $idx ?>">
                    <span class="cbd-work-tab-progress" aria-hidden="true"></span>
                    <span><?= $esc($proj['name']) ?></span>
                </button>
                <?php endforeach; ?>
            </div>
            <button type="button" class="cbd-work-tabs-nav is-next" data-cw-tabs-next aria-label="Scroll projects right">
                <i class="bi bi-chevron-right" aria-hidden="true"></i>
            </button>
        </div>

        <!-- Interactive Showcase Card -->
        <div class="cbd-work-stage" data-cw-stage>
            <div class="cbd-work-glow cbd-work-glow-red" aria-hidden="true"></div>
            <div class="cbd-work-glow cbd-work-glow-indigo" aria-hidden="true"></div>

            <div class="cbd-work-grid">
                <!-- Left: Device Preview (Desktop MacBook / Mobile iPhone) -->
                <div class="cbd-work-device-col" data-cw-device-col data-device="desktop">
                    <div class="cbd-work-device-wrap">
                        <!-- Desktop MacBook Frame -->
                        <div class="cbd-work-laptop">
                            <div class="cbd-work-laptop-lid">
                                <div class="cbd-work-browser-bar">
                                    <span class="cbd-work-dots" aria-hidden="true"><span></span><span></span><span></span></span>
                                    <span class="cbd-work-address">
                                        <i class="bi bi-lock-fill" aria-hidden="true"></i>
                                        <span data-cw-address><?= $esc($first['name']) ?></span>
                                    </span>
                                </div>
                                <div class="cbd-work-screen" data-cw-desktop-screen>
                                    <img class="cbd-work-shot"
                                         data-cw-desktop-img
                                         src="<?= $esc($firstPage['desktop']) ?>"
                                         alt="<?= $esc($first['name'] . ' — ' . $firstPage['label'] . ' desktop preview') ?>"
                                         width="1100" height="720"
                                         loading="lazy" decoding="async">
                                    <span class="cbd-work-pause-pill"><i class="bi bi-pause-fill" aria-hidden="true"></i> Paused</span>
                                </div>
                            </div>
                            <div class="cbd-work-laptop-base" aria-hidden="true"></div>
                        </div>

                        <!-- Mobile iPhone Frame -->
                        <div class="cbd-work-phone">
                            <div class="cbd-work-phone-island" aria-hidden="true"></div>
                            <div class="cbd-work-phone-screen" data-cw-mobile-screen>
                                <img class="cbd-work-shot"
                                     data-cw-mobile-img
                                     src="<?= $esc($firstPage['mobile']) ?>"
                                     alt="<?= $esc($first['name'] . ' — ' . $firstPage['label'] . ' mobile preview') ?>"
                                     width="440" height="780"
                                     loading="lazy" decoding="async">
                                <span class="cbd-work-pause-pill"><i class="bi bi-pause-fill" aria-hidden="true"></i> Paused</span>
                            </div>
                        </div>
                    </div>

                    <!-- Device & Subpage Controls Bar -->
                    <div class="cbd-work-controls">
                        <div class="cbd-work-device-toggle" role="group" aria-label="Switch device view">
                            <button type="button" class="cbd-work-dev-btn is-active" data-cw-device="desktop" aria-pressed="true">
                                <i class="bi bi-laptop" aria-hidden="true"></i> Desktop
                            </button>
                            <button type="button" class="cbd-work-dev-btn" data-cw-device="mobile" aria-pressed="false">
                                <i class="bi bi-phone" aria-hidden="true"></i> Mobile
                            </button>
                        </div>

                        <div class="cbd-work-subpages<?= count($first['pages']) <= 1 ? ' is-hidden' : '' ?>" data-cw-subpages>
                            <span class="cbd-work-subpage-label" data-cw-subpage-label><?= $esc($firstPage['label']) ?></span>
                            <div class="cbd-work-subpage-dots" data-cw-subpage-dots></div>
                            <button type="button" class="cbd-work-subbtn" data-cw-sub-prev aria-label="Previous screen">
                                <i class="bi bi-chevron-left" aria-hidden="true"></i>
                            </button>
                            <button type="button" class="cbd-work-subbtn" data-cw-sub-next aria-label="Next screen">
                                <i class="bi bi-chevron-right" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Right: Narrative, Highlights, Metrics & CTAs -->
                <div class="cbd-work-info-col">
                    <span class="cbd-work-watermark" data-cw-watermark aria-hidden="true"><?= $esc(str_pad((string)($startIndex + 1), 2, '0', STR_PAD_LEFT)) ?></span>

                    <div class="cbd-work-meta-row">
                        <span class="cbd-work-cat-badge" data-cw-cat><?= $esc($first['category']) ?></span>
                        <span class="cbd-work-tech-label" data-cw-tech<?= $first['tech'] === '' ? ' style="display:none;"' : '' ?>><?= $esc($first['tech']) ?></span>
                    </div>

                    <h3 class="cbd-work-proj-title" data-cw-title><?= $esc($first['name']) ?></h3>
                    <p class="cbd-work-proj-desc" data-cw-desc><?= $esc($first['summary']) ?></p>

                    <ul class="cbd-work-highlights" data-cw-highlights>
                        <?php foreach ($first['highlights'] as $hl): ?>
                        <li><i class="bi bi-check2" aria-hidden="true"></i><span><?= $esc($hl) ?></span></li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="cbd-work-metrics<?= empty($first['metrics']) ? ' is-empty' : (count($first['metrics']) === 2 ? ' is-two' : '') ?>" data-cw-metrics>
                        <?php foreach ($first['metrics'] as $metric): ?>
                        <div class="cbd-work-metric-card">
                            <span class="cbd-work-metric-val"><?= $esc($metric['val']) ?></span>
                            <span class="cbd-work-metric-lbl"><?= $esc($metric['label']) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="cbd-work-actions">
                        <button type="button" class="cbd-work-btn-primary" data-cw-view-btn>
                            View Project <i class="bi bi-arrow-right" aria-hidden="true"></i>
                        </button>
                        <a href="https://wa.me/919990548795?text=<?= rawurlencode('Hi Chulbul Design, I loved your ' . $first['name'] . ' (' . $first['category'] . ') project and want to build something similar.') ?>"
                           target="_blank" rel="noopener noreferrer"
                           class="cbd-work-btn-ghost"
                           data-cw-similar-btn>
                            Start a Similar Project
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Progress Counter + Prev/Next Controls -->
        <div class="cbd-work-footer">
            <div class="cbd-work-counter" aria-live="polite">
                <span class="cbd-work-counter-cur" data-cw-cur><?= $esc(str_pad((string)($startIndex + 1), 2, '0', STR_PAD_LEFT)) ?></span>
                <span class="cbd-work-progress-track" aria-hidden="true">
                    <span class="cbd-work-progress-fill" data-cw-progress style="width:<?= number_format((($startIndex + 1) / $total) * 100, 2) ?>%;"></span>
                </span>
                <span class="cbd-work-counter-total" data-cw-total><?= $esc(str_pad((string)$total, 2, '0', STR_PAD_LEFT)) ?></span>
            </div>

            <div class="cbd-work-arrows">
                <button type="button" class="cbd-work-arrow" data-cw-prev aria-label="Previous project">
                    <i class="bi bi-arrow-left" aria-hidden="true"></i>
                </button>
                <button type="button" class="cbd-work-arrow" data-cw-next aria-label="Next project">
                    <i class="bi bi-arrow-right" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <?php if ($note !== ''): ?>
        <div class="cbd-work-partner-note">
            <i class="bi bi-patch-check-fill" aria-hidden="true"></i>
            <span><?= $esc($note) ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Built-In Full Screen Preview Modal -->
    <div class="cbd-work-modal" data-cw-modal aria-hidden="true" role="dialog" aria-modal="true" aria-label="Full project preview">
        <div class="cbd-work-modal-box">
            <div class="cbd-work-modal-head">
                <div class="cbd-work-modal-title-group">
                    <h4 class="cbd-work-modal-title" data-cw-modal-title><?= $esc($first['name']) ?></h4>
                    <span class="cbd-work-cat-badge" data-cw-modal-cat><?= $esc($first['category']) ?></span>
                </div>
                <div style="display:flex;align-items:center;gap:10px;">
                    <div class="cbd-work-device-toggle" role="group" aria-label="Modal device toggle">
                        <button type="button" class="cbd-work-dev-btn is-active" data-cw-modal-device="desktop">
                            <i class="bi bi-laptop" aria-hidden="true"></i> Desktop
                        </button>
                        <button type="button" class="cbd-work-dev-btn" data-cw-modal-device="mobile">
                            <i class="bi bi-phone" aria-hidden="true"></i> Mobile
                        </button>
                    </div>
                    <button type="button" class="cbd-work-modal-close" data-cw-modal-close aria-label="Close preview">
                        <i class="bi bi-x-lg" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="cbd-work-modal-body">
                <div class="cbd-work-modal-frame" data-cw-modal-frame>
                    <img data-cw-modal-img src="<?= $esc($firstPage['desktop']) ?>" alt="<?= $esc($first['name']) ?>" width="1100" height="720" loading="lazy" decoding="async">
                </div>
            </div>

            <div class="cbd-work-modal-foot">
                <div class="cbd-work-modal-pages" data-cw-modal-pages></div>
                <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer" class="cbd-work-btn-primary" data-cw-modal-cta>
                    <i class="bi bi-whatsapp" aria-hidden="true"></i> Start a Similar Project
                </a>
            </div>
        </div>
    </div>

    <script type="application/json" data-cbd-work-json><?= json_encode($projects, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?></script>
</section>
        <?php
    }
}
