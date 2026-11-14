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
        $ecommerceSlugs = [
            'shopify-development',
            'woocommerce-development',
            'magento-development',
            'multi-vendor-marketplace',
            'ecommerce-development',
            'ecommerce-website-development',
            'custom-ecommerce-development',
        ];

        if (in_array($slug, $ecommerceSlugs, true)) {
            return [
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
                    'href' => '/city/dallas/ecommerce-development',
                    'icon' => 'bi-cart-check',
                    'title' => 'Dallas TX Ecommerce Web Design',
                    'desc' => 'Custom B2B & retail storefronts from $799',
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
                'href' => '/city/seattle/small-business-web-design',
                'icon' => 'bi-geo-alt-fill',
                'title' => 'Seattle & Kirkland Web Design',
                'desc' => 'Custom WordPress & small business websites from $799',
            ],
            [
                'href' => '/city/dallas',
                'icon' => 'bi-geo-alt-fill',
                'title' => 'Dallas TX B2B & Custom Web Design',
                'desc' => 'Conversion-focused Dallas websites from $799',
            ],
            [
                'href' => '/city/birmingham',
                'icon' => 'bi-geo-alt-fill',
                'title' => 'Birmingham & Jewellery Quarter Web Design',
                'desc' => 'Custom UK business & ecommerce sites from £649',
            ],
            [
                'href' => '/city/atlanta/small-business-web-design',
                'icon' => 'bi-geo-alt-fill',
                'title' => 'Atlanta & Dacula Small Business Sites',
                'desc' => 'Lead-generating Georgia websites from $799',
            ],
            [
                'href' => '/city/london/small-business-web-design',
                'icon' => 'bi-geo-alt-fill',
                'title' => 'London Small Business Web Design',
                'desc' => 'Affordable custom UK websites from £649',
            ],
            [
                'href' => '/city/gurgaon',
                'icon' => 'bi-geo-alt-fill',
                'title' => 'Gurgaon (Gurugram) Web Development',
                'desc' => 'Custom business & B2B websites from ₹18,000',
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
