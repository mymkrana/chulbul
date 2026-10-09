<?php
require_once __DIR__ . '/includes/http.php';
if (cbd_is_direct_script_request('sitemap.php')) cbd_redirect_path('/sitemap.xml');
require_once __DIR__ . '/includes/location-repository.php';
require_once __DIR__ . '/includes/seo.php';
// Buffer everything — prevent ANY stray output before XML headers
ob_start();
error_reporting(0);
ini_set('display_errors', 0);

$domain = CBD_CANONICAL_ORIGIN;
$today  = date('Y-m-d');
$root   = __DIR__;

// ── DB Connection ─────────────────────────────────────────────────────────────
$_pdo = cbd_location_database();

// ── Fetch: Blog posts ─────────────────────────────────────────────────────────
$blog_posts = [];
$sitemap_db_error = '';
if ($_pdo) {
    try {
        $blog_posts = $_pdo->query(
            "SELECT slug, COALESCE(updated_at, date, created_at) AS lastmod FROM posts
             WHERE status='published' AND deleted_at IS NULL
             ORDER BY date DESC"
        )->fetchAll();
    } catch (Throwable $e) {
        $sitemap_db_error = 'posts';
        error_log('Sitemap posts query failed: ' . $e->getMessage());
    }
}

// ── Fetch: Services ───────────────────────────────────────────────────────────
$service_rows = [];
if ($_pdo) {
    try {
        $service_rows = $_pdo->query(
            "SELECT slug, COALESCE(updated_at, created_at) AS lastmod FROM services
             WHERE status=1 AND deleted_at IS NULL
             ORDER BY slug"
        )->fetchAll();
    } catch (Throwable $e) {
        $sitemap_db_error = $sitemap_db_error ?: 'services';
        error_log('Sitemap services query failed: ' . $e->getMessage());
    }
}

// ── Published city slugs ──────────────────────────────────────────────────────
$city_rows = [];
$city_query_succeeded = false;
if ($_pdo) {
    try {
        $city_rows = $_pdo->query(
            "SELECT ci.slug, ci.updated_at
             FROM cities ci
             INNER JOIN countries co ON co.id = ci.country_id
             WHERE ci.status = 'published' AND co.status = 'published'
             ORDER BY co.sort_order, ci.sort_order, ci.slug"
        )->fetchAll();
        $city_query_succeeded = true;
    } catch (Throwable $e) {
        $sitemap_db_error = $sitemap_db_error ?: 'cities';
        error_log('Sitemap cities query failed: ' . $e->getMessage());
    }
}

if (!$_pdo || $sitemap_db_error !== '' || !$city_query_succeeded) {
    ob_end_clean();
    http_response_code(503);
    header('Content-Type: text/plain; charset=UTF-8');
    header('Retry-After: 300');
    header('X-Robots-Tag: noindex, nofollow');
    echo 'Sitemap temporarily unavailable.';
    exit;
}

// ── Published city service spokes ────────────────────────────────────────────
// Optional by design: this table is introduced by Phase 3. A code-first deploy
// must keep the main sitemap available until the migration is imported.
$city_spoke_rows = [];
if ($_pdo) {
    try {
        $city_spoke_rows = $_pdo->query(
            "SELECT ci.slug AS city_slug, sp.slug AS spoke_slug,
                    GREATEST(COALESCE(ci.updated_at, ci.created_at),
                             COALESCE(sp.updated_at, sp.created_at)) AS lastmod
             FROM city_spokes sp
             INNER JOIN cities ci ON ci.id = sp.city_id
             INNER JOIN countries co ON co.id = ci.country_id
             WHERE sp.status = 'published'
               AND ci.status = 'published'
               AND co.status = 'published'
             ORDER BY ci.slug, sp.sort_order, sp.slug"
        )->fetchAll();
    } catch (Throwable $e) {
        error_log('Sitemap city spokes query skipped: ' . $e->getMessage());
    }
}

// ── Helpers ───────────────────────────────────────────────────────────────────
function sm_valid_slug(string $slug): bool {
    return preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug) === 1;
}

function sm_is_public_canonical_url(string $loc): bool {
    $parts = parse_url($loc);
    if (!is_array($parts)
        || ($parts['scheme'] ?? '') !== 'https'
        || ($parts['host'] ?? '') !== 'www.chulbuldesign.com'
        || isset($parts['query'])
        || isset($parts['fragment'])) {
        return false;
    }

    $path = $parts['path'] ?? '/';
    if (preg_match('/\.php(?:\/|$)/i', $path)) {
        return false;
    }
    if (preg_match('#/(?:admin|includes|logs)(?:/|$)#i', $path)) {
        return false;
    }

    $blocked = [
        '/404', '/install', '/track', '/analytics-collect', '/lead-submit', '/quick-import',
        '/import-save', '/live-cleanup3', '/sidebar', '/service-detail',
        '/blog-listing', '/blog-post', '/city',
    ];
    return !in_array(rtrim($path, '/') ?: '/', $blocked, true);
}

function sm_lastmod(mixed $value, string $fallback = ''): string {
    $timestamp = is_numeric($value) ? (int)$value : strtotime((string)$value);
    if (!$timestamp) {
        $timestamp = $fallback !== '' ? strtotime($fallback) : false;
    }
    return $timestamp ? date('Y-m-d', $timestamp) : '';
}

function sm_url(string $loc, string $lastmod = '', string $freq = 'monthly', string $pri = '0.7'): string {
    static $seen = [];
    if (!sm_is_public_canonical_url($loc)) {
        return '';
    }

    // Treat /page and /page/ as the same canonical URL. This prevents the
    // static, database and fallback sources from emitting duplicate entries.
    $parts = parse_url($loc);
    $scheme = strtolower((string)($parts['scheme'] ?? 'https'));
    $host = strtolower((string)($parts['host'] ?? ''));
    $path = (string)($parts['path'] ?? '/');
    $path = $path === '/' ? '/' : rtrim($path, '/');
    $seenKey = $scheme . '://' . $host . $path;

    if (isset($seen[$seenKey])) {
        return '';
    }
    $seen[$seenKey] = true;
    $mod = $lastmod ? "<lastmod>{$lastmod}</lastmod>" : '';
    return "<url><loc>" . htmlspecialchars($loc, ENT_XML1) . "</loc>{$mod}</url>\n";
}
function static_url(string $root, string $domain, string $slug, string $today, string $freq = 'monthly', string $pri = '0.7'): string {
    $file = $root . '/' . $slug . '.php';
    if (is_file($file)) {
        $modified = filemtime($file);
        // Thin technology wrappers share their actual content and renderer.
        if (in_array($slug, ['nodejs-development', 'reactjs-development', 'laravel-development', 'php-development', 'joomla', 'nextjs-development'], true)) {
            foreach (['technology-pages-data.php', 'technology-service-page.php'] as $source) {
                $content_file = $root . '/includes/' . $source;
                if (is_file($content_file)) $modified = max($modified, filemtime($content_file));
            }
        }
        // Marketing page copy is kept separately while retaining the shared layout.
        if (in_array($slug, ['seo-services', 'local-seo-services', 'technical-seo', 'google-ads-ppc', 'social-media-marketing', 'content-marketing'], true)) {
            foreach (['marketing-pages-data.php', 'marketing-service-page.php', 'marketing-pages/' . $slug . '.php'] as $source) {
                $content_file = $root . '/includes/' . $source;
                if (is_file($content_file)) $modified = max($modified, filemtime($content_file));
            }
        }
        // UI/UX and branding wrappers share a renderer and page-specific copy.
        if (in_array($slug, ['ui-ux-design', 'figma-design', 'graphic-design', 'logo-design', 'branding-identity', 'dashboard-ui-design'], true)) {
            foreach (['ui-branding-pages-data.php', 'ui-branding-service-page.php', 'ui-branding-pages/' . $slug . '.php'] as $source) {
                $content_file = $root . '/includes/' . $source;
                if (is_file($content_file)) $modified = max($modified, filemtime($content_file));
            }
        }
        // Support wrappers keep their layout shared and their content service-specific.
        if (in_array($slug, ['website-maintenance', 'website-speed-optimization', 'core-web-vitals-optimization', 'website-migration', 'website-security', 'website-performance-optimization'], true)) {
            foreach (['support-pages-data.php', 'support-service-page.php', 'support-pages/' . $slug . '.php'] as $source) {
                $content_file = $root . '/includes/' . $source;
                if (is_file($content_file)) $modified = max($modified, filemtime($content_file));
            }
        }
        // AI service content uses the same layout with page-specific source files.
        if (in_array($slug, ['ai-chatbot-integration', 'whatsapp-automation', 'workflow-automation', 'ai-integration', 'ai-business-automation', 'ai-agent-development'], true)) {
            foreach (['ai-pages-data.php', 'ai-service-page.php', 'ai-pages/' . $slug . '.php'] as $source) {
                $content_file = $root . '/includes/' . $source;
                if (is_file($content_file)) $modified = max($modified, filemtime($content_file));
            }
        }
        // Category wrappers use shared markup and their own editorial content.
        if (in_array($slug, ['web-design-development', 'technologies', 'seo-digital-marketing', 'ui-ux-branding', 'website-support', 'ai-automation'], true)) {
            foreach (['service-hubs-data.php', 'service-hub-page.php', 'service-hubs/' . $slug . '.php'] as $source) {
                $content_file = $root . '/includes/' . $source;
                if (is_file($content_file)) $modified = max($modified, filemtime($content_file));
            }
        }
        $lastmod = sm_lastmod($modified, $today);
        return sm_url("$domain/$slug", $lastmod ?: $today, $freq, $pri);
    }
    return '';
}

// ── Build XML ─────────────────────────────────────────────────────────────────
$xml  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// 1. Homepage
$xml .= sm_url("$domain/", sm_lastmod(filemtime($root . '/index.php'), $today), 'weekly', '1.0');

// Landing pages (ad/SEO)
$xml .= static_url($root, $domain, 'business-starter', $today, 'weekly', '0.8');

// 2. Core pages
if (file_exists($root . '/blog/index.php')) {
    $xml .= sm_url("$domain/blog/", sm_lastmod(filemtime($root . '/blog/index.php'), $today), 'weekly', '0.9');
}
foreach (['about', 'contact-us', 'cities'] as $slug) {
    $xml .= static_url($root, $domain, $slug, $today, 'monthly', '0.7');
}

// 3a. Hub pages — always include. Dedicated hub files use their own mtime.
$hub_pages = [
    'web-design-development'       => '0.95',
    'ecommerce-development'        => '0.95',
    'mobile-software-development'  => '0.95',
    'ui-ux-branding'               => '0.95',
    'seo-digital-marketing'        => '0.95',
    'ai-automation'                => '0.95',
    'technologies'                 => '0.90',
    'website-support'              => '0.90',
];
foreach ($hub_pages as $slug => $pri) {
    $hub_file = $root . '/' . $slug . '.php';
    if (is_file($hub_file)) {
        $xml .= static_url($root, $domain, $slug, $today, 'monthly', $pri);
    } else {
        $xml .= sm_url("$domain/$slug", sm_lastmod(filemtime($root . '/service-category.php'), $today), 'monthly', $pri);
    }
}

// 3b. Static sub-service pages (individual .php files)
$static_services = [
    'web-design'                   => '0.9',
    'web-development'              => '0.9',
    'wordpress-development'        => '0.9',
    'cms-development'              => '0.9',
    'landing-page-design'          => '0.9',
    'website-redesign'             => '0.9',
    'android-app-development'      => '0.9',
    'ios-app-development'          => '0.9',
    'custom-software-development'  => '0.9',
    'saas-development'             => '0.9',
    'crm-development'              => '0.9',
    'erp-software-development'     => '0.9',
    'local-seo-services'           => '0.9',
    'seo-services'                 => '0.9',
    'technical-seo'                => '0.9',
    'google-ads-ppc'               => '0.9',
    'social-media-marketing'       => '0.9',
    'content-marketing'            => '0.9',
    'ui-ux-design'                 => '0.9',
    'figma-design'                 => '0.9',
    'graphic-design'               => '0.9',
    'logo-design'                  => '0.9',
    'branding-identity'            => '0.9',
    'dashboard-ui-design'          => '0.9',
    'website-maintenance' => '0.9',
    'website-speed-optimization' => '0.9',
    'core-web-vitals-optimization' => '0.9',
    'website-migration' => '0.9',
    'website-security' => '0.9',
    'website-performance-optimization' => '0.9',
    'ui-ux-branding'               => '0.9',
    'ecommerce-website-development'=> '0.9',
    'custom-ecommerce-development' => '0.9',
    'shopify-development'          => '0.9',
    'woocommerce-development'      => '0.9',
    'magento-development'          => '0.9',
    'multi-vendor-marketplace'     => '0.9',
    'nodejs-development'           => '0.9',
    'reactjs-development'          => '0.9',
    'laravel-development'          => '0.9',
    'php-development'              => '0.9',
    'joomla'                       => '0.9',
    'nextjs-development'           => '0.9',
    'ai-business-automation'       => '0.9',
    'ai-chatbot-integration' => '0.9',
    'whatsapp-automation' => '0.9',
    'workflow-automation' => '0.9',
    'ai-integration' => '0.9',
    'ai-agent-development' => '0.9',
];
foreach ($static_services as $slug => $pri) {
    $xml .= static_url($root, $domain, $slug, $today, 'monthly', $pri);
}

// 4. Published DB service pages. Reserved/system slugs and old city-root
// slugs must never enter the sitemap, even if bad service data is added.
$reserved_root_slugs = array_fill_keys([
    'admin', 'includes', 'logs', '404', 'install', 'track', 'analytics-collect', 'lead-submit',
    'quick-import', 'import-save', 'live-cleanup3', 'sidebar',
    'service-detail', 'blog-listing', 'blog-post', 'city', 'cities', 'blog',
    'online-marketing', 'digital-marketing', // Permanent aliases, not indexable destinations.
    'ai-chatbot-development', 'business-process-automation', // Display-name aliases retain established service URLs.
], true);
foreach ($city_rows as $city_row) {
    $city_slug = (string)($city_row['slug'] ?? '');
    if (sm_valid_slug($city_slug)) {
        $reserved_root_slugs[$city_slug] = true;
    }
}
foreach ($service_rows as $service_row) {
    $slug = (string)($service_row['slug'] ?? '');
    if (!sm_valid_slug($slug) || isset($reserved_root_slugs[$slug])) {
        continue;
    }
    $xml .= sm_url("$domain/$slug", sm_lastmod($service_row['lastmod'] ?? '', $today), 'monthly', '0.85');
}

// 5. Industry pages: the same allowlist as navigation and page routing.
if (file_exists($root . '/industry.php')) {
    require_once $root . '/includes/industry-catalog.php';
    foreach (cbd_industry_catalog() as $cbd_sitemap_industry) {
        $cbd_industry_slug = $cbd_sitemap_industry['slug'];
        $cbd_industry_files = [
            $root . '/industry.php',
            $root . '/includes/industry-data.php',
            $root . '/includes/industry-catalog.php',
            $root . '/includes/industry-service-catalog.php',
            $root . '/includes/industries/' . $cbd_industry_slug . '.php',
        ];
        $cbd_industry_modified = 0;
        foreach ($cbd_industry_files as $cbd_industry_file) {
            if (is_file($cbd_industry_file)) {
                $cbd_industry_modified = max($cbd_industry_modified, filemtime($cbd_industry_file));
            }
        }
        $xml .= sm_url("$domain/industry/$cbd_industry_slug", sm_lastmod($cbd_industry_modified, $today), 'monthly', '0.9');
    }
}

// 6. City pages
foreach ($city_rows as $city_row) {
    $city_slug = (string)($city_row['slug'] ?? '');
    if (!sm_valid_slug($city_slug)) {
        continue;
    }
    $lastmod = sm_lastmod($city_row['updated_at'] ?? '', $today);
    $xml .= sm_url("$domain/city/" . $city_slug, $lastmod, 'monthly', '0.85');
}

// 7. Published city service spokes. Only real published DB records enter the
// sitemap, so proposed but unfinished pages can never appear here.
foreach ($city_spoke_rows as $spoke_row) {
    $city_slug = (string)($spoke_row['city_slug'] ?? '');
    $spoke_slug = (string)($spoke_row['spoke_slug'] ?? '');
    if (!sm_valid_slug($city_slug) || !sm_valid_slug($spoke_slug)) {
        continue;
    }
    $lastmod = sm_lastmod($spoke_row['lastmod'] ?? '', $today);
    $xml .= sm_url("$domain/city/$city_slug/$spoke_slug", $lastmod, 'monthly', '0.82');
}

// 8. Blog posts
foreach ($blog_posts as $p) {
    $post_slug = (string)($p['slug'] ?? '');
    if (!sm_valid_slug($post_slug)) {
        continue;
    }
    $lastmod = sm_lastmod($p['lastmod'] ?? '', $today);
    $xml .= sm_url("$domain/blog/" . $post_slug, $lastmod, 'weekly', '0.85');
}

$xml .= '</urlset>';

// ── Flush cleanly — discard any stray output, send pure XML ──────────────────
ob_end_clean();
header('Content-Type: application/xml; charset=utf-8');
header('Cache-Control: public, max-age=3600, must-revalidate');
$etag = '"' . sha1($xml) . '"';
header('ETag: ' . $etag);
if (trim((string)($_SERVER['HTTP_IF_NONE_MATCH'] ?? '')) === $etag) {
    http_response_code(304);
    exit;
}
echo $xml;
