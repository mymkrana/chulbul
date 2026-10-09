<?php
// Explicit allowlist: content filenames never come from request parameters.
$slugs = [
    'ui-ux-design',
    'figma-design',
    'graphic-design',
    'logo-design',
    'branding-identity',
    'dashboard-ui-design',
];
$pages = [];
foreach ($slugs as $slug) {
    $page = require __DIR__ . '/ui-branding-pages/' . $slug . '.php';
    $dimensions = @getimagesize(dirname(__DIR__) . $page['hero_img']);
    $page['hero_width'] = $dimensions[0] ?? 960;
    $page['hero_height'] = $dimensions[1] ?? 640;
    $page['process'] = array_map(static fn(array $step, int $index): array => [
        'num' => str_pad((string)($index + 1), 2, '0', STR_PAD_LEFT),
        'icon' => 'bi-check2-circle',
        'title' => $step[0],
        'desc' => $step[1],
    ], $page['process'], array_keys($page['process']));
    $page['faqs'] = array_map(static fn(array $faq): array => ['q' => $faq[0], 'a' => $faq[1]], $page['faqs']);
    $pages[$slug] = $page;
}
return $pages;
