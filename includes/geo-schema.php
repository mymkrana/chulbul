<?php
declare(strict_types=1);

/**
 * Chulbul Design — Per-Page GEO / AEO (AI Search, Google & Bing) Helpers
 *
 * Provides:
 *  1. cbd_render_webpage_geo_schema(): Unique per-page WebPage + Speakable + about JSON-LD
 *  2. cbd_render_ai_summary_box(): Clean, citation-ready visible Quick Facts block
 */

if (!function_exists('cbd_render_webpage_geo_schema')) {
    function cbd_render_webpage_geo_schema(array $meta): void
    {
        $url = trim((string)($meta['url'] ?? ''));
        $name = trim((string)($meta['name'] ?? ''));
        $description = trim((string)($meta['description'] ?? ''));
        if ($url === '' || $name === '') {
            return;
        }

        $inLanguage = trim((string)($meta['inLanguage'] ?? 'en'));
        $speakableCss = $meta['speakable'] ?? ['h1', '.cbd-ai-summary', '#faq'];
        if (!is_array($speakableCss) || $speakableCss === []) {
            $speakableCss = ['h1', '.cbd-ai-summary'];
        }

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => trim((string)($meta['type'] ?? 'WebPage')),
            '@id' => rtrim($url, '/') . '#webpage',
            'url' => $url,
            'name' => $name,
            'inLanguage' => $inLanguage,
            'isPartOf' => ['@id' => 'https://www.chulbuldesign.com/#website'],
            'publisher' => ['@id' => 'https://www.chulbuldesign.com/#organization'],
            'speakable' => [
                '@type' => 'SpeakableSpecification',
                'cssSelector' => array_values($speakableCss),
            ],
        ];

        if ($description !== '') {
            $schema['description'] = $description;
        }

        if (!empty($meta['dateModified']) && is_string($meta['dateModified'])) {
            $schema['dateModified'] = $meta['dateModified'];
        }

        if (!empty($meta['about']) && is_array($meta['about'])) {
            $aboutEntities = [];
            foreach ($meta['about'] as $topic) {
                $topic = trim((string)$topic);
                if ($topic !== '') {
                    $aboutEntities[] = ['@type' => 'Thing', 'name' => $topic];
                }
            }
            if ($aboutEntities !== []) {
                $schema['about'] = count($aboutEntities) === 1 ? $aboutEntities[0] : $aboutEntities;
            }
        }

        $flags = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
        echo '<script type="application/ld+json">' . json_encode($schema, $flags) . "</script>\n";
    }
}

if (!function_exists('cbd_render_ai_summary_box')) {
    function cbd_render_ai_summary_box(array $opts): void
    {
        static $stylePrinted = false;

        $heading = trim((string)($opts['heading'] ?? ''));
        $summary = trim((string)($opts['summary'] ?? ''));
        $facts   = is_array($opts['facts'] ?? null) ? $opts['facts'] : [];
        if ($heading === '' || $summary === '') {
            return;
        }

        $esc = static fn(string $v): string => htmlspecialchars($v, ENT_QUOTES, 'UTF-8');

        if (!$stylePrinted) {
            $stylePrinted = true;
            echo '<style>
.cbd-ai-summary-wrap{padding:24px 0 8px}
.cbd-ai-summary{max-width:1180px;margin:0 auto;padding:24px 28px;border-radius:18px;background:#f8f9ff;border:1px solid #e2e6f5;border-left:4px solid #ee483d;box-shadow:0 8px 24px rgba(30,30,92,.04);font-family:"Plus Jakarta Sans",Inter,sans-serif;box-sizing:border-box}
.cbd-ai-summary-kicker{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#ee483d;margin-bottom:6px}
.cbd-ai-summary h2{margin:0 0 8px;font-size:clamp(18px,2.1vw,22px);font-weight:800;color:#1e1e5c;line-height:1.25}
.cbd-ai-summary p{margin:0 0 14px;font-size:14.5px;line-height:1.65;color:#374151}
.cbd-ai-summary-facts{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:10px;margin:0;padding:0;list-style:none}
.cbd-ai-summary-facts li{background:#fff;border:1px solid #e6e9f7;border-radius:12px;padding:10px 12px}
.cbd-ai-summary-facts strong{display:block;font-size:10.5px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:#6b7280;margin-bottom:2px}
.cbd-ai-summary-facts span{display:block;font-size:13px;font-weight:700;color:#1e1e5c;line-height:1.35}
@media(max-width:900px){.cbd-ai-summary-facts{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:560px){.cbd-ai-summary{padding:18px 16px}.cbd-ai-summary-facts{grid-template-columns:1fr}}
</style>' . "\n";
        }
        ?>
<div class="cbd-ai-summary-wrap">
    <div style="max-width:1280px;margin:0 auto;padding:0 16px;">
        <aside class="cbd-ai-summary" aria-label="Quick service facts">
            <span class="cbd-ai-summary-kicker"><i class="bi bi-lightning-charge-fill" aria-hidden="true"></i> Quick Facts</span>
            <h2><?= $esc($heading) ?></h2>
            <p><?= $esc($summary) ?></p>
            <?php if ($facts !== []): ?>
            <ul class="cbd-ai-summary-facts">
                <?php foreach ($facts as $label => $val): ?>
                <li>
                    <strong><?= $esc((string)$label) ?></strong>
                    <span><?= $esc((string)$val) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </aside>
    </div>
</div>
        <?php
    }
}
