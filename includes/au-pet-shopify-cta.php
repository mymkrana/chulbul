<?php
declare(strict_types=1);

/**
 * Reusable Hero-Style CTA Banner for Australian City Pages & Spokes
 * Links Australian visitors directly to /pet-food-shopify-australia.
 */

if (!function_exists('cbd_render_au_pet_shopify_cta')) {
    function cbd_render_au_pet_shopify_cta(string $cityName = 'Australia', string $base = ''): void
    {
        static $cssPrinted = false;

        $cityName = trim($cityName) !== '' ? trim($cityName) : 'Australia';
        if ($base === '' && function_exists('cbd_base_path')) {
            $base = cbd_base_path();
        }
        $esc = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

        $heroWebp = function_exists('cbd_asset_url')
            ? cbd_asset_url('/assets/images/pet-food-au/au-pet-happy-owner-hero.webp')
            : $base . '/assets/images/pet-food-au/au-pet-happy-owner-hero.webp';
        $heroJpg = function_exists('cbd_asset_url')
            ? cbd_asset_url('/assets/images/pet-food-au/au-pet-happy-owner-hero.jpg')
            : $base . '/assets/images/pet-food-au/au-pet-happy-owner-hero.jpg';

        if (!$cssPrinted) {
            $cssPrinted = true;
            $cssVer = (int)(@filemtime(__DIR__ . '/../assets/css/au-pet-shopify-cta.css') ?: 1);
            echo '<link rel="stylesheet" href="' . $esc($base . '/assets/css/au-pet-shopify-cta.css?v=' . $cssVer) . '">' . "\n";
        }
        ?>
<section class="pfa-city-cta-wrap" aria-labelledby="pfa-city-cta-heading">
    <div class="pfa-city-cta-container">
        <div class="pfa-city-cta-card">
            <div>
                <span class="pfa-city-cta-eyebrow">
                    <i class="bi bi-stars" aria-hidden="true"></i>
                    Specialist Shopify · Australian Pet Brands
                </span>

                <h2 class="pfa-city-cta-title" id="pfa-city-cta-heading">
                    Running a <span>Pet Food or Pet Store</span> Brand in <?= $esc($cityName) ?>?
                </h2>

                <p class="pfa-city-cta-desc">
                    Explore our dedicated Australian Pet Food Shopify service — custom Online Store 2.0 design, subscription &amp; recurring order UX, smart dietary product filters, and ongoing store support built for <?= $esc($cityName) ?> pet businesses.
                </p>

                <div class="pfa-city-cta-actions">
                    <a href="<?= $esc($base . '/pet-food-shopify-australia') ?>" class="pfa-city-cta-btn-primary">
                        Explore Pet Food Shopify Solutions <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                    <a href="<?= $esc($base . '/pet-food-shopify-australia#audit-form') ?>" class="pfa-city-cta-btn-secondary">
                        Get a Free Store Audit
                    </a>
                </div>

                <ul class="pfa-city-cta-points">
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Custom Shopify 2.0 Store Design</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Subscription &amp; Recurring Orders</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> App Cost Cleanup (A$799)</li>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> Ongoing Care (A$999 / month)</li>
                </ul>
            </div>

            <div class="pfa-city-cta-media">
                <div class="pfa-city-cta-portrait">
                    <picture>
                        <source srcset="<?= $esc($heroWebp) ?>" type="image/webp">
                        <img src="<?= $esc($heroJpg) ?>"
                             alt="Australian dog owner with golden retriever and natural dog food pouch for <?= $esc($cityName) ?> Shopify pet food stores"
                             width="896" height="1200" loading="lazy" decoding="async">
                    </picture>
                </div>

                <div class="pfa-city-cta-chip is-top">
                    <i class="bi bi-code-slash" aria-hidden="true"></i>
                    <div>Native Online Store 2.0<br><span>Built for AU Pet Brands</span></div>
                </div>

                <div class="pfa-city-cta-chip is-bottom">
                    <i class="bi bi-tags-fill" aria-hidden="true"></i>
                    <div>Transparent AUD Tiers<br><span>Starting from A$799</span></div>
                </div>
            </div>
        </div>
    </div>
</section>
        <?php
    }
}
