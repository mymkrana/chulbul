<?php
require_once __DIR__ . '/includes/http.php';
if (cbd_is_direct_script_request('cities.php')) cbd_redirect_path('/cities');
require_once __DIR__ . '/includes/location-listing.php';
$database_location_groups = cbd_location_listing();

if (!$database_location_groups) {
    require __DIR__ . '/503.php';
    exit;
}

$location_city_count = array_sum(array_map(
    static fn(array $group): int => count($group['cities']),
    $database_location_groups
));
$location_country_count = count($database_location_groups);
$location_nav_groups = $database_location_groups;
$location_escape = static fn($value): string => htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8">
    <title>Cities We Serve | Web Design Worldwide | Chulbul Design</title>
    <link rel="canonical" href="https://www.chulbuldesign.com/cities">
    <meta name="description" content="Chulbul Design offers web design & digital marketing in <?= $location_city_count ?> cities across <?= $location_country_count ?> countries. Find your city.">
    <meta property="og:title" content="Cities We Serve | Chulbul Design">
    <meta property="og:url" content="https://www.chulbuldesign.com/cities">
    <meta property="og:type" content="website">
    <meta property="og:image" content="https://www.chulbuldesign.com/assets/images/Fb_chulbuldesign.jpg">
    <?php require __DIR__ . '/includes/header.php'; ?>

<!-- Hero -->
<section style="background:linear-gradient(135deg,#1e1e5c 0%,#49499A 100%);position:relative;overflow:hidden">
    <div style="position:absolute;inset:0;background-image:radial-gradient(circle,rgba(255,255,255,.07) 1px,transparent 1px);background-size:24px 24px;pointer-events:none" aria-hidden="true"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center relative">
        <span class="inline-block text-xs font-bold tracking-widest uppercase text-white/70 bg-white/10 border border-white/20 px-4 py-1.5 rounded-full mb-4">Global Reach</span>
        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white mb-4">Cities We Serve</h1>
        <p class="text-white/70 text-lg max-w-2xl mx-auto">Professional web design & digital marketing for businesses across <strong class="text-white"><?= $location_city_count ?> cities</strong> in <?= $location_country_count ?> countries. Find your city below.</p>
        <div class="flex flex-wrap justify-center gap-3 mt-6 text-sm">
            <?php foreach ($location_nav_groups as $nav_group): ?>
            <a href="#<?= $location_escape($nav_group['id']) ?>" class="bg-white/10 hover:bg-white/20 border border-white/20 text-white px-4 py-1.5 rounded-full transition"><?= $location_escape($nav_group['flag']) ?> <?= $location_escape($nav_group['country']) ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Cities Grid -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    <?php
    $groups = $database_location_groups;

    foreach ($groups as $group): ?>

    <!-- Country group -->
    <div id="<?= $location_escape($group['id']) ?>" class="mb-14 scroll-mt-24">

        <!-- Country Header -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6 pb-4 border-b-2 border-gray-100">
            <div class="flex items-center gap-3">
                <span class="text-4xl"><?= $location_escape($group['flag']) ?></span>
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900"><?= $location_escape($group['country']) ?></h2>
                    <p class="text-sm text-gray-400"><?= count($group['cities']) ?> cities · <?= $location_escape($group['currency']) ?></p>
                </div>
            </div>
            <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-xl px-5 py-3">
                <div>
                    <p class="text-xs text-gray-400 leading-none mb-0.5">Starting From</p>
                    <p class="text-xl font-extrabold text-[#49499A]"><?= $location_escape($group['starting']) ?></p>
                </div>
                <a href="<?= $base ?>/contact-us"
                   class="bg-[#EE483D] hover:bg-red-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    Get Quote
                </a>
            </div>
        </div>

        <!-- City Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($group['cities'] as [$cname, $cslug, $cdesc]): ?>
            <a href="<?= $base ?>/city/<?= rawurlencode($cslug) ?>"
               class="flex items-start gap-4 bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md hover:-translate-y-0.5 hover:border-[#49499A]/30 transition-all group">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-white text-sm font-bold"
                     style="background:linear-gradient(135deg,#1e1e5c,#49499A)">
                    <?= $location_escape(strtoupper(substr($cname, 0, 2))) ?>
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-gray-900 group-hover:text-[#49499A] transition text-sm"><?= $location_escape($cname) ?></p>
                    <p class="text-gray-400 text-xs leading-snug mt-0.5 truncate"><?= $location_escape($cdesc) ?></p>
                </div>
                <i class="bi bi-arrow-right text-gray-300 group-hover:text-[#EE483D] ml-auto shrink-0 transition" aria-hidden="true"></i>
            </a>
            <?php endforeach; ?>
        </div>

    </div>
    <?php endforeach; ?>

    <!-- Bottom CTA -->
    <div class="mt-4 rounded-2xl text-center py-12 px-6" style="background:linear-gradient(135deg,#1e1e5c 0%,#49499A 100%)">
        <p class="text-white/70 text-sm uppercase tracking-widest font-bold mb-2">Don't see your city?</p>
        <h3 class="text-2xl font-extrabold text-white mb-4">We serve businesses anywhere in the world.</h3>
        <a href="<?= $base ?>/contact-us"
           class="inline-flex items-center gap-2 bg-[#EE483D] hover:bg-red-600 text-white px-8 py-3.5 rounded-xl font-bold transition text-sm">
            <i class="bi bi-send-fill" aria-hidden="true"></i> Get in Touch
        </a>
    </div>

</section>

<!-- Why Choose Chulbul Design Section -->
<section class="cities-trust-section" aria-labelledby="cities-trust-title">
    <div class="cities-trust-orb cities-trust-orb-one" aria-hidden="true"></div>
    <div class="cities-trust-orb cities-trust-orb-two" aria-hidden="true"></div>

    <div class="cities-trust-container">
        <header class="cities-trust-heading">
            <span class="cities-trust-eyebrow"><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Why Chulbul Design</span>
            <h2 id="cities-trust-title">Why Businesses Across <span><?= $location_city_count ?> Cities</span><br>Trust Chulbul Design</h2>
            <p>One agency. <?= $location_country_count ?> countries. Zero templates. Since 2013 we have delivered 500+ custom websites, apps and digital campaigns for businesses across global markets.</p>
        </header>

        <div class="cities-trust-grid">
            <?php
            $why_points = [
                ['icon'=>'bi-code-slash',       'title'=>'100% Custom Code',              'desc'=>'Every project starts from a blank file. No Wix, no WordPress themes, no shortcuts. Clean, fast, custom-coded websites that load in under 2 seconds.'],
                ['icon'=>'bi-globe2',            'title'=>'International Expertise',       'desc'=>'We have delivered projects for businesses in India, USA, UK, Australia, Canada, UAE, Singapore and South Africa — understanding each market\'s unique requirements.'],
                ['icon'=>'bi-translate',         'title'=>'Local Market Knowledge',        'desc'=>'From Razorpay & WhatsApp integration for Indian businesses, to GDPR compliance for UK clients and PayNow for Singapore — we know exactly what each market needs.'],
                ['icon'=>'bi-speedometer2',      'title'=>'PageSpeed 95+ Guaranteed',      'desc'=>'Every website we deliver scores 95+ on Google PageSpeed. Fast sites rank better, convert better, and reduce bounce rate significantly across all markets.'],
                ['icon'=>'bi-headset',           'title'=>'Direct Team Access',            'desc'=>'No account managers, no ticket queues. You communicate directly with the developers and designers building your project — in your time zone.'],
                ['icon'=>'bi-shield-fill-check', 'title'=>'You Own Everything',            'desc'=>'Code, hosting, domain — all yours. We hand over complete access on launch day. No lock-in, no ongoing fees unless you choose our support package.'],
            ];
            foreach ($why_points as $index => $pt): ?>
            <article class="cities-trust-card">
                <div class="cities-trust-card-top">
                    <span class="cities-trust-icon"><i class="bi <?= $location_escape($pt['icon']) ?>" aria-hidden="true"></i></span>
                    <span class="cities-trust-number" aria-hidden="true"><?= str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
                </div>
                <h3><?= $location_escape($pt['title']) ?></h3>
                <p><?= $location_escape($pt['desc']) ?></p>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="cities-trust-stats" aria-label="Chulbul Design global delivery statistics">
            <div class="cities-trust-stat">
                <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                <p><strong><?= $location_city_count ?></strong><span>Cities Served</span></p>
            </div>
            <div class="cities-trust-stat">
                <i class="bi bi-globe-americas" aria-hidden="true"></i>
                <p><strong><?= $location_country_count ?></strong><span>Countries</span></p>
            </div>
            <div class="cities-trust-stat">
                <i class="bi bi-briefcase-fill" aria-hidden="true"></i>
                <p><strong>500+</strong><span>Projects Delivered</span></p>
            </div>
            <div class="cities-trust-stat">
                <i class="bi bi-calendar2-check-fill" aria-hidden="true"></i>
                <p><strong>12+</strong><span>Years in Business</span></p>
            </div>
        </div>
    </div>
</section>

<!-- CTA -->
<section class="city-cta-section">
    <div class="city-cta-deco-1" aria-hidden="true"></div>
    <div class="city-cta-deco-2" aria-hidden="true"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 city-cta-inner">
        <h2 class="city-cta-title">Don't See Your City?<br><span class="city-cta-title-highlight">We Still Serve You.</span></h2>
        <p class="city-cta-desc">We work with businesses worldwide — not just the cities listed here. If you need a professional website, app or digital marketing campaign, get in touch for a free consultation regardless of your location.</p>
        <div class="city-cta-btns">
            <a href="<?= $base ?>/contact-us" class="city-cta-btn-primary"><i class="bi bi-envelope-fill"></i> Get a Free Quote</a>
            <a href="https://wa.me/919990548795" target="_blank" rel="noopener noreferrer" class="city-cta-btn-wa"><i class="bi bi-whatsapp"></i> WhatsApp Us</a>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
