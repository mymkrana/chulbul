<?php
declare(strict_types=1);

/**
 * Chulbul Design — Shared Legal & Compliance Page Renderer
 *
 * Renders clean, accessible, consistent legal pages:
 *   - /privacy-policy
 *   - /terms-of-service
 *   - /cookie-policy
 *   - /refund-cancellation-policy
 *   - /disclaimer
 */

if (!function_exists('cbd_render_legal_page')) {
    function cbd_render_legal_page(array $doc): void
    {
        require_once __DIR__ . '/http.php';
        require_once __DIR__ . '/seo.php';

        $slug = trim((string)($doc['slug'] ?? ''), '/');
        if ($slug !== '' && cbd_is_direct_script_request($slug . '.php')) {
            cbd_redirect_path('/' . $slug);
        }

        $base = cbd_base_path();
        $canonical = cbd_canonical_url('/' . $slug);
        $title = (string)($doc['title'] ?? 'Legal Information');
        $metaTitle = (string)($doc['meta_title'] ?? ($title . ' | Chulbul Design'));
        $metaDesc = (string)($doc['meta_desc'] ?? '');
        $eyebrow = (string)($doc['eyebrow'] ?? 'Legal & Compliance');
        $intro = (string)($doc['intro'] ?? '');
        $updated = (string)($doc['updated'] ?? 'November 28, 2026');
        $sections = is_array($doc['sections'] ?? null) ? $doc['sections'] : [];
        $extraCta = $doc['extra_cta'] ?? null;

        $esc = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');
        $fixHref = static function (string $html) use ($base): string {
            if ($base === '') {
                return $html;
            }
            return (string)preg_replace('/href="\/(?!\/)/', 'href="' . $base . '/', $html);
        };

        $legalNav = [
            ['slug' => 'privacy-policy',             'label' => 'Privacy Policy',              'icon' => 'bi-shield-lock'],
            ['slug' => 'terms-of-service',           'label' => 'Terms of Service',            'icon' => 'bi-file-earmark-text'],
            ['slug' => 'cookie-policy',              'label' => 'Cookie Policy',               'icon' => 'bi-cookie'],
            ['slug' => 'refund-cancellation-policy', 'label' => 'Refund & Cancellation',       'icon' => 'bi-arrow-counterclockwise'],
            ['slug' => 'disclaimer',                 'label' => 'Disclaimer',                  'icon' => 'bi-info-circle'],
        ];

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => CBD_CANONICAL_ORIGIN . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $title, 'item' => $canonical],
            ],
        ];

        $cbd_page_has_charset = true;
        $page_robots = 'index, follow';
        $cbd_geo_page = [
            'url' => $canonical,
            'name' => $metaTitle,
            'description' => $metaDesc,
            'inLanguage' => 'en',
            'about' => [$title, 'Chulbul Design Legal Information'],
            'speakable' => ['h1', '.cbd-legal-intro'],
        ];
        $shareImage = CBD_CANONICAL_ORIGIN . '/assets/images/chulbuldesign-social-share-original-logo-2026-09.jpg';
        $jsonFlags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
        ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= $esc($metaTitle) ?></title>
    <link rel="canonical" href="<?= $esc($canonical) ?>">
    <meta name="description" content="<?= $esc($metaDesc) ?>">
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?= $esc($metaTitle) ?>">
    <meta property="og:description" content="<?= $esc($metaDesc) ?>">
    <meta property="og:url" content="<?= $esc($canonical) ?>">
    <meta property="og:image" content="<?= $esc($shareImage) ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@chulbuldesign">
    <meta name="twitter:title" content="<?= $esc($metaTitle) ?>">
    <meta name="twitter:description" content="<?= $esc($metaDesc) ?>">
    <meta name="twitter:image" content="<?= $esc($shareImage) ?>">
    <script type="application/ld+json"><?= json_encode($breadcrumbSchema, $jsonFlags) ?></script>
    <?php require __DIR__ . '/header.php'; ?>

<style>
.cbd-legal-hero{position:relative;padding:64px 0 54px;background:linear-gradient(135deg,#0f1233 0%,#1e1e5c 55%,#34347a 100%);color:#fff;overflow:hidden}
.cbd-legal-hero::before{content:"";position:absolute;inset:0;background-image:radial-gradient(rgba(255,255,255,.14) 1px,transparent 1px);background-size:24px 24px;opacity:.45;pointer-events:none}
.cbd-legal-wrap{max-width:1200px;margin:0 auto;padding:0 20px;position:relative;z-index:1}
.cbd-legal-crumb{display:inline-flex;align-items:center;gap:8px;font-size:12.5px;color:rgba(255,255,255,.72);margin-bottom:16px}
.cbd-legal-crumb a{color:rgba(255,255,255,.85);text-decoration:none}
.cbd-legal-crumb a:hover{color:#ff8f85}
.cbd-legal-kicker{display:inline-flex;align-items:center;gap:7px;padding:5px 13px;border-radius:999px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.2);font-size:11.5px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#ffb4ad;margin-bottom:14px}
.cbd-legal-hero h1{margin:0 0 14px;font-size:clamp(30px,4vw,46px);font-weight:800;line-height:1.15;color:#fff}
.cbd-legal-intro{margin:0 0 18px;max-width:740px;font-size:16px;line-height:1.7;color:rgba(255,255,255,.82)}
.cbd-legal-meta{display:flex;flex-wrap:wrap;gap:14px 24px;font-size:13px;color:rgba(255,255,255,.75)}
.cbd-legal-meta span{display:inline-flex;align-items:center;gap:6px}
.cbd-legal-body{padding:56px 0 80px;background:#f8f9fd}
.cbd-legal-grid{display:grid;grid-template-columns:290px 1fr;gap:36px;align-items:start}
.cbd-legal-aside{position:sticky;top:88px;display:flex;flex-direction:column;gap:18px}
.cbd-legal-box{background:#fff;border:1px solid #e4e7f4;border-radius:20px;padding:20px;box-shadow:0 6px 20px rgba(30,30,92,.04)}
.cbd-legal-box h2{margin:0 0 12px;font-size:12px;font-weight:800;letter-spacing:.09em;text-transform:uppercase;color:#6b7280}
.cbd-legal-toc{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:4px}
.cbd-legal-toc a{display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:10px;font-size:13px;font-weight:600;color:#4b5563;text-decoration:none;transition:all .18s ease}
.cbd-legal-toc a:hover,.cbd-legal-toc a.is-active{background:#f3f4fc;color:#49499A}
.cbd-legal-toc a.is-active{font-weight:800;color:#1e1e5c}
.cbd-legal-article{background:#fff;border:1px solid #e4e7f4;border-radius:24px;padding:40px 42px;box-shadow:0 10px 30px rgba(30,30,92,.05)}
.cbd-legal-sec{padding-bottom:28px;margin-bottom:28px;border-bottom:1px solid #eef0f8;scroll-margin-top:96px}
.cbd-legal-sec:last-child{padding-bottom:0;margin-bottom:0;border-bottom:0}
.cbd-legal-sec h2{margin:0 0 12px;font-size:21px;font-weight:800;color:#1e1e5c;line-height:1.3}
.cbd-legal-sec p{margin:0 0 12px;font-size:15px;line-height:1.75;color:#374151}
.cbd-legal-sec p:last-child{margin-bottom:0}
.cbd-legal-sec ul{margin:10px 0 14px;padding-left:20px;display:flex;flex-direction:column;gap:8px;font-size:14.5px;line-height:1.7;color:#374151}
.cbd-legal-sec a{color:#49499A;font-weight:700;text-decoration:underline;text-underline-offset:3px}
.cbd-legal-sec a:hover{color:#EE483D}
.cbd-legal-callout{margin-top:26px;padding:22px 24px;border-radius:18px;background:#f7f8ff;border:1px solid #dfe3f7;border-left:4px solid #EE483D}
.cbd-legal-callout strong{display:block;font-size:15px;font-weight:800;color:#1e1e5c;margin-bottom:6px}
.cbd-legal-callout p{margin:0 0 12px;font-size:14px;color:#4b5563;line-height:1.65}
.cbd-legal-btn{display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:999px;background:#1e1e5c;color:#fff;border:0;font-size:13.5px;font-weight:700;cursor:pointer;text-decoration:none;transition:background .2s ease}
.cbd-legal-btn:hover{background:#EE483D;color:#fff}
@media(max-width:960px){.cbd-legal-grid{grid-template-columns:1fr}.cbd-legal-aside{position:static}.cbd-legal-article{padding:26px 20px}}
</style>

<section class="cbd-legal-hero" aria-labelledby="legal-page-title">
    <div class="cbd-legal-wrap">
        <nav class="cbd-legal-crumb" aria-label="Breadcrumb">
            <a href="<?= $base ?>/">Home</a>
            <span aria-hidden="true">/</span>
            <span aria-current="page"><?= $esc($title) ?></span>
        </nav>
        <div><span class="cbd-legal-kicker"><i class="bi bi-shield-check" aria-hidden="true"></i> <?= $esc($eyebrow) ?></span></div>
        <h1 id="legal-page-title"><?= $esc($title) ?></h1>
        <p class="cbd-legal-intro"><?= $esc($intro) ?></p>
        <div class="cbd-legal-meta">
            <span><i class="bi bi-calendar3" aria-hidden="true"></i> Last updated: <?= $esc($updated) ?></span>
            <span><i class="bi bi-building" aria-hidden="true"></i> Chulbul Design LLP · Gurugram, India</span>
            <span><i class="bi bi-envelope" aria-hidden="true"></i> info@chulbuldesign.com</span>
        </div>
    </div>
</section>

<section class="cbd-legal-body">
    <div class="cbd-legal-wrap">
        <div class="cbd-legal-grid">
            <aside class="cbd-legal-aside" aria-label="Legal navigation and contents">
                <div class="cbd-legal-box">
                    <h2>On This Page</h2>
                    <ul class="cbd-legal-toc">
                        <?php foreach ($sections as $idx => $sec):
                            $anchor = $sec['id'] ?? ('section-' . ($idx + 1));
                        ?>
                        <li><a href="#<?= $esc($anchor) ?>"><?= ($idx + 1) . '. ' . $esc((string)$sec['heading']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="cbd-legal-box">
                    <h2>Legal Documents</h2>
                    <ul class="cbd-legal-toc">
                        <?php foreach ($legalNav as $item): ?>
                        <li>
                            <a href="<?= $base ?>/<?= $esc($item['slug']) ?>" class="<?= $item['slug'] === $slug ? 'is-active' : '' ?>">
                                <i class="bi <?= $esc($item['icon']) ?>" aria-hidden="true"></i>
                                <?= $esc($item['label']) ?>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>

            <article class="cbd-legal-article">
                <?php foreach ($sections as $idx => $sec):
                    $anchor = $sec['id'] ?? ('section-' . ($idx + 1));
                ?>
                <section class="cbd-legal-sec" id="<?= $esc($anchor) ?>">
                    <h2><?= ($idx + 1) . '. ' . $esc((string)$sec['heading']) ?></h2>
                    <?php foreach (($sec['paragraphs'] ?? []) as $para): ?>
                    <p><?= $fixHref((string)$para) ?></p>
                    <?php endforeach; ?>
                    <?php if (!empty($sec['bullets']) && is_array($sec['bullets'])): ?>
                    <ul>
                        <?php foreach ($sec['bullets'] as $b): ?>
                        <li><?= $fixHref((string)$b) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </section>
                <?php endforeach; ?>

                <?php if (is_array($extraCta)): ?>
                <div class="cbd-legal-callout">
                    <strong><?= $esc((string)($extraCta['title'] ?? '')) ?></strong>
                    <p><?= $esc((string)($extraCta['text'] ?? '')) ?></p>
                    <?php if (!empty($extraCta['onclick'])): ?>
                    <button type="button" class="cbd-legal-btn" onclick="<?= $esc((string)$extraCta['onclick']) ?>">
                        <i class="bi bi-sliders" aria-hidden="true"></i> <?= $esc((string)($extraCta['button'] ?? 'Manage Preferences')) ?>
                    </button>
                    <?php elseif (!empty($extraCta['href'])):
                        $ctaHref = (string)$extraCta['href'];
                        if ($base !== '' && str_starts_with($ctaHref, '/') && !str_starts_with($ctaHref, '//')) {
                            $ctaHref = $base . $ctaHref;
                        }
                    ?>
                    <a href="<?= $esc($ctaHref) ?>" class="cbd-legal-btn">
                        <?= $esc((string)($extraCta['button'] ?? 'Contact Us')) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </article>
        </div>
    </div>
</section>

<?php require __DIR__ . '/footer.php';
    }
}
