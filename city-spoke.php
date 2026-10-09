<?php
declare(strict_types=1);

$citySlug = trim((string)($_GET['city'] ?? ''));
$spokeSlug = trim((string)($_GET['service'] ?? ''));

require_once __DIR__ . '/includes/location-repository.php';
require_once __DIR__ . '/includes/http.php';

$spokeResult = cbd_location_spoke_page($citySlug, $spokeSlug);
if (($spokeResult['status'] ?? '') === 'unavailable') {
    require __DIR__ . '/503.php';
    exit;
}
if (($spokeResult['status'] ?? '') !== 'ok') {
    require __DIR__ . '/404.php';
    exit;
}

$data = $spokeResult['data'];
$page = $data['page'];
$city = $data['city'];
$country = $data['country'];
$content = $data['content'];
$proof = $data['proof'];
$faqs = $data['faqs'];

if (cbd_is_direct_script_request('city-spoke.php')) {
    cbd_redirect_path('/city/' . rawurlencode($city['slug']) . '/' . rawurlencode($page['slug']));
}

$escape = static fn(mixed $value): string =>
    htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
$safeHeading = strip_tags((string)$page['heading_html'], '<span><br><strong><em>');
$splitParagraphs = static function (mixed $value): array {
    return array_values(array_filter(
        preg_split('/\R{2,}/', trim((string)$value)) ?: [],
        static fn(string $paragraph): bool => trim($paragraph) !== ''
    ));
};

$contentText = static fn(string $key, string $default = ''): string =>
    trim((string)($content[$key] ?? '')) !== '' ? trim((string)$content[$key]) : $default;
$contentList = static fn(string $key, array $default = []): array =>
    isset($content[$key]) && is_array($content[$key]) ? array_values($content[$key]) : $default;

$schemaOfferRows = $content['schema_offers'] ?? null;
if (is_array($schemaOfferRows)) {
    $solutionNames = array_values(array_filter(array_map(
        static fn(mixed $solution): string => is_array($solution)
            ? trim((string)($solution['name'] ?? ''))
            : trim((string)$solution),
        $schemaOfferRows
    )));
} else {
    $solutionNames = array_values(array_filter(array_map(
        static fn(array $solution): string => trim((string)($solution['name'] ?? '')),
        $content['platforms'] ?? []
    )));
}
$heroCard = isset($content['hero_card']) && is_array($content['hero_card'])
    ? $content['hero_card']
    : [];
$heroTrustItems = $contentList('hero_trust_items', [
    ['icon' => 'bi-clock', 'text' => 'Scheduled Pacific Time meetings'],
    ['icon' => 'bi-geo-alt', 'text' => 'Remote delivery from Gurugram, India'],
    ['icon' => 'bi-file-earmark-check', 'text' => 'Scope documented before development'],
]);
$formService = $contentText('form_service', $page['breadcrumb_label']);
$formSource = $contentText('form_source', 'city-' . $city['slug'] . '-' . $page['slug'] . '-spoke');
$formSuccessMessage = $contentText('form_success_message', 'Thank you. Your project enquiry has been received.');
$ogImage = $contentText('og_image', 'https://www.chulbuldesign.com/assets/images/ecomerse-image.webp');

$serviceSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'Service',
    '@id' => $page['canonical_url'] . '#service',
    'name' => $page['schema_service_name'],
    'serviceType' => $page['schema_service_type'],
    'url' => $page['canonical_url'],
    'description' => $page['meta_description'],
    'areaServed' => [
        '@type' => 'City',
        'name' => $city['schema_city_name'],
        'containedInPlace' => [
            '@type' => 'AdministrativeArea',
            'name' => $city['state'],
        ],
    ],
    'provider' => [
        '@type' => 'Organization',
        '@id' => 'https://www.chulbuldesign.com/#organization',
        'name' => 'Chulbul Design',
        'url' => 'https://www.chulbuldesign.com/',
        'telephone' => '+919990548795',
        'email' => 'info@chulbuldesign.com',
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Gurugram',
            'addressRegion' => 'Haryana',
            'addressCountry' => 'IN',
        ],
    ],
    'availableChannel' => [
        '@type' => 'ServiceChannel',
        'serviceUrl' => $page['canonical_url'],
        'availableLanguage' => 'English',
    ],
    'hasOfferCatalog' => [
        '@type' => 'OfferCatalog',
        'name' => $contentText('schema_catalog_name', $page['schema_service_name'] . ' capabilities'),
        'itemListElement' => array_map(
            static fn(string $name): array => [
                '@type' => 'Offer',
                'itemOffered' => ['@type' => 'Service', 'name' => $name],
            ],
            $solutionNames
        ),
    ],
];

$breadcrumbSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'BreadcrumbList',
    'itemListElement' => [
        ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://www.chulbuldesign.com/'],
        ['@type' => 'ListItem', 'position' => 2, 'name' => $city['name'] . ' Web Design', 'item' => $city['canonical_url']],
        ['@type' => 'ListItem', 'position' => 3, 'name' => $page['breadcrumb_label'], 'item' => $page['canonical_url']],
    ],
];

$faqSchema = [
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(
        static fn(array $faq): array => [
            '@type' => 'Question',
            'name' => (string)($faq['question'] ?? ''),
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => (string)($faq['answer'] ?? ''),
            ],
        ],
        $faqs
    ),
];
$jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
?>
<!DOCTYPE html>
<html lang="<?= $escape($country['hreflang']) ?>">
<head>
    <meta charset="utf-8">
    <title><?= $escape($page['meta_title']) ?></title>
    <meta name="description" content="<?= $escape($page['meta_description']) ?>">
    <link rel="canonical" href="<?= $escape($page['canonical_url']) ?>">
    <!-- Each service has its own canonical; no alternate-language set is published. -->

    <meta property="og:type" content="website">
    <meta property="og:locale" content="<?= $escape($country['og_locale']) ?>">
    <meta property="og:title" content="<?= $escape($page['meta_title']) ?>">
    <meta property="og:description" content="<?= $escape($page['meta_description']) ?>">
    <meta property="og:url" content="<?= $escape($page['canonical_url']) ?>">
    <meta property="og:image" content="<?= $escape($ogImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= $escape($page['meta_title']) ?>">
    <meta name="twitter:description" content="<?= $escape($page['meta_description']) ?>">
    <meta name="twitter:image" content="<?= $escape($ogImage) ?>">

    <script type="application/ld+json"><?= json_encode($serviceSchema, $jsonFlags) ?></script>
    <script type="application/ld+json"><?= json_encode($breadcrumbSchema, $jsonFlags) ?></script>
    <script type="application/ld+json"><?= json_encode($faqSchema, $jsonFlags) ?></script>
<?php
$page_robots = 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1';
$_geo_meta = [
    'region' => $city['geo_region'],
    'placename' => $city['name'],
    'pos' => $city['geo_position'],
    'icbm' => $city['geo_icbm'],
];
require __DIR__ . '/includes/header.php';
?>

<div class="cbd-spoke-page">
    <section class="cbd-spoke-hero">
        <div class="cbd-spoke-container">
            <nav class="cbd-spoke-breadcrumb" aria-label="Breadcrumb">
                <a href="<?= $base ?>/">Home</a>
                <span aria-hidden="true">/</span>
                <a href="<?= $base ?>/city/<?= $escape($city['slug']) ?>"><?= $escape($city['name']) ?> Web Design</a>
                <span aria-hidden="true">/</span>
                <span aria-current="page"><?= $escape($page['breadcrumb_label']) ?></span>
            </nav>

            <div class="cbd-spoke-hero-grid">
                <div>
                    <span class="cbd-spoke-eyebrow"><i class="bi <?= $escape($contentText('hero_icon', 'bi-cart-check')) ?>" aria-hidden="true"></i> <?= $escape($page['eyebrow']) ?></span>
                    <h1><?= $safeHeading ?></h1>
                    <p class="cbd-spoke-hero-copy"><?= $escape($page['hero_description']) ?></p>
                    <div class="cbd-spoke-hero-actions">
                        <a class="cbd-spoke-btn cbd-spoke-btn-primary" href="#project-enquiry"><?= $escape($contentText('hero_primary_cta', 'Plan Your Ecommerce Project')) ?></a>
                        <a class="cbd-spoke-btn cbd-spoke-btn-secondary" href="<?= $base ?>/city/<?= $escape($city['slug']) ?>"><?= $escape($contentText('hero_secondary_cta', 'Explore ' . $city['name'] . ' web design services')) ?></a>
                    </div>
                    <ul class="cbd-spoke-trust-list" aria-label="Delivery information">
                        <?php foreach ($heroTrustItems as $trustItem): ?>
                        <li><i class="bi <?= $escape($trustItem['icon'] ?? 'bi-check-circle') ?>" aria-hidden="true"></i> <?= $escape($trustItem['text'] ?? '') ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <aside class="cbd-spoke-hero-card" aria-label="<?= $escape($heroCard['aria_label'] ?? 'Project planning checklist') ?>">
                    <span class="cbd-spoke-card-kicker"><?= $escape($heroCard['kicker'] ?? 'Before choosing a platform') ?></span>
                    <h2><?= $escape($heroCard['title'] ?? 'Define the store behind the storefront.') ?></h2>
                    <p><?= $escape($heroCard['description'] ?? 'A reliable ecommerce build starts with products, payments, fulfilment, customer rules and reporting—not a theme selection.') ?></p>
                    <ul>
                        <?php foreach (($heroCard['items'] ?? ['Catalogue and variant structure', 'Shipping, tax and payment rules', 'Migration and redirect inventory', 'Analytics and conversion events']) as $item): ?>
                        <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> <?= $escape($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </aside>
            </div>
        </div>
    </section>

    <section class="cbd-spoke-section cbd-spoke-intro">
        <div class="cbd-spoke-container cbd-spoke-reading-width">
            <span class="cbd-spoke-section-label"><?= $escape($contentText('intro_label', 'Project strategy')) ?></span>
            <h2><?= $escape($content['intro_title'] ?? 'Build the right commerce system before polishing the storefront') ?></h2>
            <?php foreach ($splitParagraphs($page['introduction']) as $paragraph): ?>
            <p><?= $escape($paragraph) ?></p>
            <?php endforeach; ?>
            <p class="cbd-spoke-context-link">
                <?= $escape($contentText('hub_link_intro', 'Need a broader company website, campaign page or brand refresh as well?')) ?>
                <a href="<?= $base ?>/city/<?= $escape($city['slug']) ?>"><?= $escape($contentText('hub_link_label', 'Review our ' . $city['name'] . ' web design and development services')) ?></a>.
            </p>
        </div>
    </section>

    <section class="cbd-spoke-section cbd-spoke-section-alt" id="platforms">
        <div class="cbd-spoke-container">
            <div class="cbd-spoke-section-heading">
                <span class="cbd-spoke-section-label"><?= $escape($contentText('platform_label', 'Platform decision')) ?></span>
                <h2><?= $escape($contentText('platform_title', 'Shopify, WooCommerce or custom ecommerce?')) ?></h2>
                <p><?= $escape($content['platform_intro'] ?? '') ?></p>
            </div>
            <div class="cbd-spoke-platform-grid">
                <?php foreach ($content['platforms'] as $platform): ?>
                <article class="cbd-spoke-platform-card">
                    <div class="cbd-spoke-card-icon"><i class="bi <?= $escape($platform['icon'] ?? 'bi-shop') ?>" aria-hidden="true"></i></div>
                    <h3><?= $escape($platform['name'] ?? '') ?></h3>
                    <p><strong><?= $escape($contentText('platform_primary_label', 'Best fit')) ?>:</strong> <?= $escape($platform['best_for'] ?? $platform['best_fit'] ?? '') ?></p>
                    <p><strong><?= $escape($contentText('platform_secondary_label', 'Watch for')) ?>:</strong> <?= $escape($platform['tradeoff'] ?? '') ?></p>
                    <?php if (!empty($platform['href']) && !empty($platform['link_label'])): ?>
                    <a href="<?= $base . $escape($platform['href']) ?>"><?= $escape($platform['link_label']) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                    <?php endif; ?>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cbd-spoke-section">
        <div class="cbd-spoke-container">
            <div class="cbd-spoke-two-column">
                <div class="cbd-spoke-section-heading cbd-spoke-heading-left">
                    <span class="cbd-spoke-section-label"><?= $escape($contentText('architecture_label', 'Commerce architecture')) ?></span>
                    <h2><?= $escape($content['architecture_title'] ?? '') ?></h2>
                    <p><?= $escape($content['architecture_intro'] ?? '') ?></p>
                </div>
                <div class="cbd-spoke-detail-list">
                    <?php foreach ($content['architecture_items'] ?? [] as $item): ?>
                    <article>
                        <i class="bi <?= $escape($item['icon'] ?? 'bi-diagram-3') ?>" aria-hidden="true"></i>
                        <div>
                            <h3><?= $escape($item['title'] ?? '') ?></h3>
                            <p><?= $escape($item['description'] ?? '') ?></p>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>

    <section class="cbd-spoke-section cbd-spoke-process-section" id="process">
        <div class="cbd-spoke-container">
            <div class="cbd-spoke-section-heading">
                <span class="cbd-spoke-section-label"><?= $escape($contentText('process_label', 'Service-specific process')) ?></span>
                <h2><?= $escape($contentText('process_title', 'How an ecommerce project moves from scope to launch')) ?></h2>
                <p><?= $escape($content['process_intro'] ?? '') ?></p>
            </div>
            <ol class="cbd-spoke-process-list">
                <?php foreach ($content['process'] as $step): ?>
                <li>
                    <span class="cbd-spoke-step-number"><?= $escape($step['step'] ?? '') ?></span>
                    <div>
                        <h3><?= $escape($step['title'] ?? '') ?></h3>
                        <p><?= $escape($step['description'] ?? '') ?></p>
                    </div>
                </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </section>

    <section class="cbd-spoke-section cbd-spoke-section-alt">
        <div class="cbd-spoke-container">
            <div class="cbd-spoke-section-heading">
                <span class="cbd-spoke-section-label"><?= $escape($contentText('operations_label', 'Operations and measurement')) ?></span>
                <h2><?= $escape($content['operations_title'] ?? '') ?></h2>
                <p><?= $escape($content['operations_intro'] ?? '') ?></p>
            </div>
            <div class="cbd-spoke-feature-grid">
                <?php foreach ($content['operations_items'] ?? [] as $item): ?>
                <article>
                    <i class="bi <?= $escape($item['icon'] ?? 'bi-gear') ?>" aria-hidden="true"></i>
                    <h3><?= $escape($item['title'] ?? '') ?></h3>
                    <p><?= $escape($item['description'] ?? '') ?></p>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cbd-spoke-section" id="pricing">
        <div class="cbd-spoke-container">
            <div class="cbd-spoke-section-heading">
                <span class="cbd-spoke-section-label"><?= $escape($contentText('pricing_label', 'Transparent scoping')) ?></span>
                <h2><?= $escape($contentText('pricing_title', 'What affects ' . $city['name'] . ' ' . strtolower($page['breadcrumb_label']) . ' pricing?')) ?></h2>
                <p><?= $escape($content['pricing_intro'] ?? '') ?></p>
            </div>
            <div class="cbd-spoke-pricing-grid">
                <?php foreach ($content['pricing_factors'] as $factor): ?>
                <article>
                    <h3><?= $escape($factor['title'] ?? '') ?></h3>
                    <p><?= $escape($factor['description'] ?? '') ?></p>
                </article>
                <?php endforeach; ?>
            </div>
            <div class="cbd-spoke-pricing-note">
                <i class="bi bi-info-circle-fill" aria-hidden="true"></i>
                <p><?= $escape($content['pricing_note'] ?? '') ?></p>
            </div>
        </div>
    </section>

    <section class="cbd-spoke-section cbd-spoke-proof-section" id="project-proof">
        <div class="cbd-spoke-container">
            <div class="cbd-spoke-proof-card">
                <div class="cbd-spoke-proof-heading">
                    <span class="cbd-spoke-section-label"><?= $escape($proof['eyebrow'] ?? 'Published project proof') ?></span>
                    <h2><?= $escape($proof['title'] ?? '') ?></h2>
                    <p class="cbd-spoke-proof-location"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> <?= $escape($proof['location'] ?? '') ?></p>
                    <p><?= $escape($proof['context'] ?? '') ?></p>
                </div>
                <div class="cbd-spoke-proof-story">
                    <article><span><?= $escape($proof['problem_label'] ?? 'Problem') ?></span><p><?= $escape($proof['problem'] ?? '') ?></p></article>
                    <article><span><?= $escape($proof['work_label'] ?? 'Work completed') ?></span><p><?= $escape($proof['work'] ?? '') ?></p></article>
                    <article><span><?= $escape($proof['result_label'] ?? 'Published result') ?></span><p><?= $escape($proof['result'] ?? '') ?></p></article>
                </div>
                <div class="cbd-spoke-proof-footer">
                    <div class="cbd-spoke-proof-metrics">
                        <?php foreach ($proof['metrics'] ?? [] as $metric): ?>
                        <div><strong><?= $escape($metric['value'] ?? '') ?></strong><span><?= $escape($metric['label'] ?? '') ?></span></div>
                        <?php endforeach; ?>
                    </div>
                    <a href="<?= $base . $escape($proof['source_href'] ?? '/#portfolio') ?>"><?= $escape($proof['source_label'] ?? 'View project portfolio') ?> <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                </div>
            </div>
        </div>
    </section>

    <section class="cbd-spoke-section cbd-spoke-section-alt">
        <div class="cbd-spoke-container">
            <div class="cbd-spoke-section-heading">
                <span class="cbd-spoke-section-label"><?= $escape($contentText('related_label', 'Related planning resources')) ?></span>
                <h2><?= $escape($contentText('related_title', 'Continue planning your project')) ?></h2>
                <p><?= $escape($content['related_intro'] ?? '') ?></p>
            </div>
            <div class="cbd-spoke-related-grid">
                <?php foreach ($content['related_links'] ?? [] as $link): ?>
                <a href="<?= $base . $escape($link['href'] ?? '/') ?>">
                    <span><?= $escape($link['label'] ?? '') ?></span>
                    <small><?= $escape($link['description'] ?? '') ?></small>
                    <i class="bi bi-arrow-up-right" aria-hidden="true"></i>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cbd-spoke-section" id="faq">
        <div class="cbd-spoke-container cbd-spoke-faq-wrap">
            <div class="cbd-spoke-section-heading">
                <span class="cbd-spoke-section-label"><?= $escape($contentText('faq_label', 'Questions before scope')) ?></span>
                <h2><?= $escape($contentText('faq_title', $city['name'] . ' ' . $page['breadcrumb_label'] . ' FAQs')) ?></h2>
            </div>
            <div class="cbd-spoke-faq-list">
                <?php foreach ($faqs as $faq): ?>
                <details>
                    <summary><?= $escape($faq['question'] ?? '') ?><i class="bi bi-plus-lg" aria-hidden="true"></i></summary>
                    <p><?= $escape($faq['answer'] ?? '') ?></p>
                </details>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="cbd-spoke-cta" id="project-enquiry">
        <div class="cbd-spoke-container cbd-spoke-cta-grid">
            <div class="cbd-spoke-cta-copy">
                <span class="cbd-spoke-eyebrow"><i class="bi bi-chat-square-text" aria-hidden="true"></i> <?= $escape($contentText('form_eyebrow', $page['breadcrumb_label'] . ' enquiry')) ?></span>
                <h2><?= $escape($page['cta_title']) ?></h2>
                <p><?= $escape($page['cta_description']) ?></p>
                <ul>
                    <?php foreach ($contentList('form_bullets', ['No-obligation scope discussion', 'Recommendation based on your requirements', 'Written milestones and pricing factors']) as $item): ?>
                    <li><i class="bi bi-check-circle-fill" aria-hidden="true"></i> <?= $escape($item) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <form id="cbdSpokeQuoteForm" class="cbd-spoke-form" action="<?= $base ?>/lead-submit.php" method="post">
                <input type="text" name="_hp" value="" tabindex="-1" autocomplete="off" class="cbd-spoke-honeypot" aria-hidden="true">
                <input type="hidden" name="source" value="<?= $escape($formSource) ?>">
                <input type="hidden" name="city" value="<?= $escape($city['name']) ?>">
                <input type="hidden" name="service" value="<?= $escape($formService) ?>">
                <input type="hidden" name="page_url" value="<?= $escape($page['canonical_url']) ?>">
                <input type="hidden" name="form_version" value="city-project-v2">
                <label>Name <input type="text" name="name" autocomplete="name" required></label>
                <label>Business / company <input type="text" name="business" autocomplete="organization" maxlength="160" required></label>
                <label>Phone or WhatsApp (with country code) <input type="tel" name="phone" autocomplete="tel" maxlength="24" required></label>
                <label>Email <input type="email" name="email" autocomplete="email"></label>
                <label><?= $escape($contentText('form_message_label', 'What are you planning?')) ?> <textarea name="message" rows="4" maxlength="1000" placeholder="<?= $escape($contentText('form_message_placeholder', 'Share your goals, current website and required features')) ?>"></textarea></label>
                <label>Budget range &amp; currency (optional) <input type="text" name="budget" maxlength="100" placeholder="Amount and currency, or Not decided"></label>
                <label>Expected start (optional) <input type="text" name="timeline" maxlength="100" placeholder="Your preferred month, or Exploring options"></label>
                <button id="cbdSpokeQuoteSubmit" class="cbd-spoke-btn cbd-spoke-btn-primary" type="submit"><i class="bi bi-send-fill" aria-hidden="true"></i> <?= $escape($contentText('form_button', 'Request a Consultation')) ?></button>
                <div id="cbdSpokeQuoteMessage" class="cbd-spoke-form-message" role="status" aria-live="polite" hidden></div>
            </form>
        </div>
    </section>
</div>

<script>
(() => {
    const form = document.getElementById('cbdSpokeQuoteForm');
    if (!form) return;
    const button = document.getElementById('cbdSpokeQuoteSubmit');
    const status = document.getElementById('cbdSpokeQuoteMessage');
    const originalButton = button.innerHTML;

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (!form.reportValidity()) return;
        button.disabled = true;
        button.innerHTML = '<i class="bi bi-arrow-repeat" aria-hidden="true"></i> Sending…';
        status.hidden = true;
        status.replaceChildren();

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {'Accept': 'application/json'}
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.error || 'Submission failed');

            status.className = 'cbd-spoke-form-message is-success';
            status.append(document.createTextNode(<?= json_encode($formSuccessMessage, $jsonFlags) ?>));
            if (data.whatsapp_url) {
                const link = document.createElement('a');
                link.href = data.whatsapp_url;
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
                link.textContent = ' Continue on WhatsApp';
                status.append(link);
            }
            status.hidden = false;
            form.reset();
        } catch (error) {
            status.className = 'cbd-spoke-form-message is-error';
            status.textContent = 'Your request could not be sent. Please try again or contact us on WhatsApp.';
            status.hidden = false;
        } finally {
            button.disabled = false;
            button.innerHTML = originalButton;
        }
    });
})();
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
