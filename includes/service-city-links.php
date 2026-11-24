<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

if (!function_exists('cbd_service_apply_db_meta')) {
    /** Apply active DB metadata overrides from the services table when available. */
    function cbd_service_apply_db_meta(string $slug, array $page): array
    {
        $pdo = cbd_database();
        if (!$pdo || $slug === '') {
            return $page;
        }

        try {
            $stmt = $pdo->prepare(
                'SELECT meta_title, meta_desc
                 FROM services
                 WHERE slug = ? AND status = 1 AND deleted_at IS NULL
                 LIMIT 1'
            );
            $stmt->execute([$slug]);
            $row = $stmt->fetch();
            if (is_array($row)) {
                if (isset($row['meta_title']) && trim((string)$row['meta_title']) !== '') {
                    $page['meta_title'] = trim((string)$row['meta_title']);
                }
                if (isset($row['meta_desc']) && trim((string)$row['meta_desc']) !== '') {
                    $page['meta_desc'] = trim((string)$row['meta_desc']);
                }
            }
        } catch (Throwable $error) {
            error_log('Service metadata override unavailable: ' . $error->getMessage());
        }

        return $page;
    }
}

if (!function_exists('cbd_service_city_destinations')) {
    /** Return high-impact city hub and spoke links relevant to the current service page. */
    function cbd_service_city_destinations(string $slug): array
    {
        if (in_array($slug, ['wordpress-development', 'cms-development'], true)) {
            return [
                [
                    'href' => '/city/leeds/wordpress-developer',
                    'icon' => 'bi-wordpress',
                    'title' => 'Leeds WordPress Web Design & Dev',
                    'desc' => 'Custom Gutenberg themes & WooCommerce from £649',
                ],
                [
                    'href' => '/city/chicago/wordpress-developer',
                    'icon' => 'bi-wordpress',
                    'title' => 'Chicago WordPress Developer',
                    'desc' => 'Fast custom WordPress & SEO builds from $799',
                ],
                [
                    'href' => '/city/san-francisco/wordpress-developer',
                    'icon' => 'bi-wordpress',
                    'title' => 'San Francisco WordPress Web Design',
                    'desc' => 'Bay Area startup & SaaS WordPress builds from $799',
                ],
                [
                    'href' => '/city/bristol/wordpress-developer',
                    'icon' => 'bi-wordpress',
                    'title' => 'Bristol WordPress Agency',
                    'desc' => 'Affordable UK WordPress & SEO websites from £649',
                ],
                [
                    'href' => '/city/seattle/wordpress-developer',
                    'icon' => 'bi-wordpress',
                    'title' => 'Seattle & Kirkland WordPress Dev',
                    'desc' => 'Clean block themes & migrations from $799',
                ],
                [
                    'href' => '/city/london/small-business-web-design',
                    'icon' => 'bi-geo-alt-fill',
                    'title' => 'London Small Business Web Design',
                    'desc' => 'Custom UK WordPress & business sites from £649',
                ],
            ];
        }

        if ($slug === 'magento-development') {
            return [
                [
                    'href' => '/city/leeds/magento-development',
                    'icon' => 'bi-boxes',
                    'title' => 'Leeds Magento Web Design & Dev',
                    'desc' => 'Magento 2 builds & SEO-safe UK migrations',
                ],
                [
                    'href' => '/city/birmingham/ecommerce-development',
                    'icon' => 'bi-cart-check',
                    'title' => 'Birmingham & Jewellery Quarter Ecommerce',
                    'desc' => 'Custom UK Shopify & WooCommerce from £649',
                ],
                [
                    'href' => '/city/london/ecommerce-development',
                    'icon' => 'bi-cart-check',
                    'title' => 'London Ecommerce Website Agency',
                    'desc' => 'High-converting UK online stores from £649',
                ],
                [
                    'href' => '/city/chicago/ecommerce-development',
                    'icon' => 'bi-cart-check',
                    'title' => 'Chicago Ecommerce Web Development',
                    'desc' => 'D2C & B2B wholesale stores from $799',
                ],
                [
                    'href' => '/city/dallas/ecommerce-development',
                    'icon' => 'bi-cart-check',
                    'title' => 'Dallas TX Ecommerce Web Design',
                    'desc' => 'Custom B2B & retail storefronts from $799',
                ],
                [
                    'href' => '/city/seattle/ecommerce-development',
                    'icon' => 'bi-cart-check',
                    'title' => 'Seattle & Kirkland Ecommerce Design',
                    'desc' => 'Shopify, WooCommerce & ERP sync from $799',
                ],
            ];
        }

        if (in_array($slug, ['website-redesign', 'website-migration', 'conversion-rate-optimization'], true)) {
            return [
                [
                    'href' => '/city/glasgow/website-redesign',
                    'icon' => 'bi-arrow-repeat',
                    'title' => 'Glasgow Website Redesign & Revamp',
                    'desc' => 'SEO-safe UK redesigns & 301 mapping from £649',
                ],
                [
                    'href' => '/city/chicago/b2b-web-design',
                    'icon' => 'bi-building',
                    'title' => 'Chicago B2B & Financial Web Design',
                    'desc' => 'ADA-ready corporate redesigns & CRM sync from $799',
                ],
                [
                    'href' => '/city/dallas/b2b-web-design',
                    'icon' => 'bi-building',
                    'title' => 'Dallas TX B2B Website Design',
                    'desc' => 'DFW enterprise & industrial RFQ sites from $799',
                ],
                [
                    'href' => '/city/bristol/b2b-web-design',
                    'icon' => 'bi-building',
                    'title' => 'Bristol B2B Web Design Agency',
                    'desc' => 'Corporate UX & pipeline websites from £649',
                ],
                [
                    'href' => '/city/boston/biotech-web-design',
                    'icon' => 'bi-heart-pulse',
                    'title' => 'Boston Biotech & Life Sciences Web Design',
                    'desc' => 'Clinical pipeline & investor-ready UX from $799',
                ],
                [
                    'href' => '/city/toronto',
                    'icon' => 'bi-geo-alt-fill',
                    'title' => 'Toronto Web Design & Redesign Agency',
                    'desc' => 'Custom GTA websites & redesigns from C$699',
                ],
            ];
        }

        $ecommerceSlugs = [
            'shopify-development',
            'woocommerce-development',
            'multi-vendor-marketplace',
            'ecommerce-development',
            'ecommerce-website-development',
            'custom-ecommerce-development',
        ];

        if (in_array($slug, $ecommerceSlugs, true)) {
            return [
                [
                    'href' => '/pet-food-shopify-australia',
                    'icon' => 'bi-bag-heart-fill',
                    'title' => 'Australia Pet Food & Pet Store Shopify',
                    'desc' => 'Shopify 2.0 design, store management & 40–60% cost saving',
                ],
                [
                    'href' => '/city/seattle/ecommerce-development',
                    'icon' => 'bi-cart-check',
                    'title' => 'Seattle & Kirkland Ecommerce Design',
                    'desc' => 'Shopify, WooCommerce & ERP sync from $799',
                ],
                [
                    'href' => '/city/birmingham/ecommerce-development',
                    'icon' => 'bi-cart-check',
                    'title' => 'Birmingham & Jewellery Quarter Ecommerce',
                    'desc' => 'Custom UK Shopify & WooCommerce from £649',
                ],
                [
                    'href' => '/city/chicago/ecommerce-development',
                    'icon' => 'bi-cart-check',
                    'title' => 'Chicago Ecommerce Web Development',
                    'desc' => 'D2C & B2B wholesale stores from $799',
                ],
                [
                    'href' => '/city/london/ecommerce-development',
                    'icon' => 'bi-cart-check',
                    'title' => 'London Ecommerce Website Agency',
                    'desc' => 'High-converting UK online stores from £649',
                ],
                [
                    'href' => '/city/dubai/ecommerce-development',
                    'icon' => 'bi-cart-check',
                    'title' => 'Dubai Bilingual Ecommerce Stores',
                    'desc' => 'Arabic/English stores & UAE gateways from AED 2,999',
                ],
            ];
        }

        return [
            [
                'href' => '/city/dallas/b2b-web-design',
                'icon' => 'bi-building',
                'title' => 'Dallas TX B2B Website Design',
                'desc' => 'Custom DFW B2B & RFQ websites from $799',
            ],
            [
                'href' => '/city/chicago/b2b-web-design',
                'icon' => 'bi-building',
                'title' => 'Chicago B2B & Financial Web Design',
                'desc' => 'Corporate & financial services sites from $799',
            ],
            [
                'href' => '/city/bristol/b2b-web-design',
                'icon' => 'bi-building',
                'title' => 'Bristol B2B Web Design Agency',
                'desc' => 'South West UK corporate & SaaS sites from £649',
            ],
            [
                'href' => '/city/boston/biotech-web-design',
                'icon' => 'bi-heart-pulse',
                'title' => 'Boston Biotech & Life Sciences Sites',
                'desc' => 'Clinical pipeline & investor-ready UX from $799',
            ],
            [
                'href' => '/city/leeds/wordpress-developer',
                'icon' => 'bi-wordpress',
                'title' => 'Leeds WordPress Web Design',
                'desc' => 'Custom Gutenberg WordPress builds from £649',
            ],
            [
                'href' => '/city/glasgow/website-redesign',
                'icon' => 'bi-arrow-repeat',
                'title' => 'Glasgow Website Redesign Agency',
                'desc' => 'SEO-safe revamps & 301 mapping from £649',
            ],
        ];
    }
}

if (!function_exists('cbd_render_service_city_links')) {
    /** Render the Popular Cities We Serve internal linking section for a service template. */
    function cbd_render_service_city_links(string $slug, string $base, string $style = 'wd'): void
    {
        $items = cbd_service_city_destinations($slug);
        if (empty($items)) {
            return;
        }
        $esc = static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8');

        if ($style === 'tailwind'): ?>
<section class="bg-gray-50 py-16 border-t border-gray-100" aria-labelledby="service-city-links-title">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-10">
            <span class="text-xs font-bold tracking-widest uppercase text-[#EE483D] bg-red-50 px-4 py-1.5 rounded-full">Location Hubs</span>
            <h2 id="service-city-links-title" class="text-3xl font-extrabold text-gray-900 mt-4 mb-2">Popular Cities We Serve</h2>
            <p class="text-gray-500 text-sm">Explore local website packages, ecommerce scopes and starter pricing across key cities worldwide.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($items as $item): ?>
            <a href="<?= $esc($base . $item['href']) ?>"
               class="group bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-200 p-6 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-red-50 flex items-center justify-center flex-shrink-0 group-hover:bg-[#EE483D] transition-colors duration-200">
                    <i class="bi <?= $esc($item['icon']) ?> text-[#EE483D] text-lg group-hover:text-white transition-colors duration-200" aria-hidden="true"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-bold text-gray-900 group-hover:text-[#EE483D] transition-colors leading-snug"><?= $esc($item['title']) ?></p>
                    <p class="text-xs text-gray-500 mt-1"><?= $esc($item['desc']) ?></p>
                </div>
                <i class="bi bi-arrow-right text-gray-300 group-hover:text-[#EE483D] group-hover:translate-x-1 transition-all flex-shrink-0" aria-hidden="true"></i>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php else: ?>
<section class="wd-section wd-section-soft" aria-labelledby="service-city-links-title">
    <div class="wd-container">
        <div class="wd-heading wd-reveal">
            <span class="wd-kicker">Global &amp; local delivery</span>
            <h2 id="service-city-links-title">Popular Cities We Serve</h2>
            <p>Compare local website packages, ecommerce deliverables and transparent starter pricing in our top client cities, or <a href="<?= $esc($base) ?>/cities">view all 52 cities we serve</a>.</p>
        </div>
        <div class="wd-related-grid wd-related-grid--three">
            <?php foreach ($items as $item): ?>
            <a class="wd-related-card wd-reveal" href="<?= $esc($base . $item['href']) ?>">
                <i class="bi <?= $esc($item['icon']) ?>" aria-hidden="true"></i>
                <span><strong><?= $esc($item['title']) ?></strong><?= $esc($item['desc']) ?></span>
                <b>Explore City <i class="bi bi-arrow-right" aria-hidden="true"></i></b>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif;
    }
}
