<?php
declare(strict_types=1);

/**
 * Dynamic /llms-full.txt generator for AI Search & Answer Engines
 * (ChatGPT Search, Google Gemini, Perplexity, Claude, Bing Copilot)
 *
 * Automatically pulls ALL published services, 16 industries, 52 city hubs,
 * 101 city service spokes, 8 verified portfolio builds, and 126+ blog posts.
 */

require_once __DIR__ . '/includes/http.php';
require_once __DIR__ . '/includes/seo.php';
require_once __DIR__ . '/includes/location-repository.php';
require_once __DIR__ . '/includes/industry-catalog.php';
require_once __DIR__ . '/includes/portfolio-showcase.php';

if (cbd_is_direct_script_request('llms-full.php')) {
    cbd_redirect_path('/llms-full.txt');
}

header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: public, max-age=3600');
header('X-Content-Type-Options: nosniff');

$origin = CBD_CANONICAL_ORIGIN;
$today  = date('Y-m-d');
$pdo    = cbd_location_database();

$out = [];
$out[] = '# Chulbul Design — Complete Live AI & Search Engine Index (llms-full.txt)';
$out[] = '';
$out[] = '> Auto-generated live reference of all public services, industries, city hubs, city service spokes, verified portfolio work, and planning guides on https://www.chulbuldesign.com/. Updated: ' . $today . '.';
$out[] = '';
$out[] = '## 1. Organization Identity & Transparency';
$out[] = '- Brand: Chulbul Design';
$out[] = '- Website: ' . $origin . '/';
$out[] = '- Concise Summary: ' . $origin . '/llms.txt';
$out[] = '- XML Sitemap: ' . $origin . '/sitemap.xml';
$out[] = '- Headquarters: Sector 15, Flat No. 1277, Gurugram, Haryana, India';
$out[] = '- Delivery Model: Remote web design, Shopify ecommerce, custom development, and SEO agency serving Australia, United States, United Kingdom, Canada, United Arab Emirates, and India (no physical overseas offices claimed).';
$out[] = '- Contact: info@chulbuldesign.com | +91 99905 48795 | ' . $origin . '/contact-us';
$out[] = '- Profiles: LinkedIn (https://www.linkedin.com/company/chulbuldesign/), Facebook (https://www.facebook.com/chulbuldesign), Instagram (https://www.instagram.com/chulbuldesign), X (https://twitter.com/ChulbulDesign)';
$out[] = '';

$out[] = '## 2. Specialist Niche: Pet Food Shopify Development Australia';
$out[] = '- URL: ' . $origin . '/pet-food-shopify-australia';
$out[] = '- Target Audience: Australian pet food brands, online pet stores, dog/cat nutrition subscriptions, and pet supply ecommerce retailers.';
$out[] = '- Capabilities: Custom Shopify Online Store 2.0 design, subscription & recurring order storefront UX, smart dietary/breed/format filtering, brand-formula feeding calculators (non-veterinary sizing guidance), Australian shipping & postcode delivery UX, Shopify app cost consolidation, and ongoing store support.';
$out[] = '- Indicative AUD Tiers (written quote confirms final scope): App Cost Cleanup Sprint (A$799 one-time), Monthly Care & CRO Pod (A$999/month), Custom Pet Store / Marketplace (A$2,499+ starting price).';
$out[] = '- Featured Australian Client: Brunswick Fur Food (Melbourne, VIC — Shopify storefront and cost optimisation).';
$out[] = '';

$out[] = '## 3. Verified Live Portfolio Builds (' . $origin . '/#portfolio)';
foreach (cbd_portfolio_projects('') as $proj) {
    $meta = $proj['category'] . ($proj['tech'] !== '' ? ' · ' . $proj['tech'] : '');
    $out[] = '- **' . $proj['name'] . '** (' . $meta . '): ' . $proj['summary'];
}
$out[] = '';

$out[] = '## 4. Core Service Pages';
$coreServices = [
    'web-design' => 'Custom Web Design',
    'web-development' => 'Custom Web Development',
    'wordpress-development' => 'WordPress Development',
    'cms-development' => 'CMS Development',
    'landing-page-design' => 'Landing Page Design',
    'website-redesign' => 'Website Redesign',
    'ecommerce-website-development' => 'Ecommerce Website Development',
    'custom-ecommerce-development' => 'Custom Ecommerce Development',
    'shopify-development' => 'Shopify Development',
    'pet-food-shopify-australia' => 'Pet Food Shopify Development Australia',
    'woocommerce-development' => 'WooCommerce Development',
    'magento-development' => 'Magento Development',
    'multi-vendor-marketplace' => 'Multi-Vendor Marketplace Development',
    'android-app-development' => 'Android App Development',
    'ios-app-development' => 'iOS App Development',
    'custom-software-development' => 'Custom Software Development',
    'saas-development' => 'SaaS Development',
    'crm-development' => 'CRM Development',
    'erp-software-development' => 'ERP Software Development',
    'local-seo-services' => 'Local SEO Services',
    'seo-services' => 'SEO Services',
    'technical-seo' => 'Technical SEO',
    'google-ads-ppc' => 'Google Ads & PPC Management',
    'social-media-marketing' => 'Social Media Marketing',
    'content-marketing' => 'Content Marketing',
    'ui-ux-design' => 'UI/UX Design',
    'figma-design' => 'Figma Design',
    'graphic-design' => 'Graphic Design',
    'logo-design' => 'Logo Design',
    'branding-identity' => 'Branding & Visual Identity',
    'dashboard-ui-design' => 'Dashboard UI Design',
    'website-maintenance' => 'Website Maintenance',
    'website-speed-optimization' => 'Website Speed Optimization',
    'core-web-vitals-optimization' => 'Core Web Vitals Optimization',
    'website-migration' => 'Website Migration',
    'website-security' => 'Website Security',
    'website-performance-optimization' => 'Website Performance Optimization',
    'nodejs-development' => 'Node.js Development',
    'reactjs-development' => 'ReactJS Development',
    'laravel-development' => 'Laravel Development',
    'php-development' => 'PHP Development',
    'nextjs-development' => 'Next.js Development',
    'ai-business-automation' => 'AI Business Automation',
    'ai-chatbot-integration' => 'AI Chatbot Integration',
    'whatsapp-automation' => 'WhatsApp Business Automation',
    'workflow-automation' => 'Workflow Automation',
    'ai-integration' => 'AI Integration',
    'ai-agent-development' => 'AI Agent Development',
];
foreach ($coreServices as $slug => $label) {
    if (is_file(__DIR__ . '/' . $slug . '.php')) {
        $out[] = '- [' . $label . '](' . $origin . '/' . $slug . ')';
    }
}
$out[] = '';

$out[] = '## 5. Industry Solutions (16 Verticals)';
foreach (cbd_industry_catalog() as $ind) {
    $out[] = '- [' . $ind['name'] . '](' . $origin . '/industry/' . $ind['slug'] . ')';
}
$out[] = '';

if ($pdo instanceof PDO) {
    try {
        $cityRows = $pdo->query(
            "SELECT ci.slug, ci.name, ci.state_region, co.name AS country_name, co.price_range
             FROM cities ci
             INNER JOIN countries co ON co.id = ci.country_id
             WHERE ci.status = 'published' AND co.status = 'published'
             ORDER BY co.sort_order, ci.sort_order, ci.name"
        )->fetchAll();

        if ($cityRows) {
            $out[] = '## 6. Published City Hubs (' . count($cityRows) . ' Cities Worldwide)';
            $byCountry = [];
            foreach ($cityRows as $row) {
                $byCountry[(string)$row['country_name']][] = $row;
            }
            foreach ($byCountry as $cName => $rows) {
                $out[] = '### ' . $cName;
                foreach ($rows as $r) {
                    $st = trim((string)($r['state_region'] ?? ''));
                    $loc = $r['name'] . ($st !== '' && strcasecmp($st, (string)$r['name']) !== 0 ? ', ' . $st : '');
                    $price = trim((string)($r['price_range'] ?? ''));
                    $out[] = '- [' . $loc . ' Web Design & Development](' . $origin . '/city/' . $r['slug'] . ')' . ($price !== '' ? ' — Typical range: ' . $price : '');
                }
            }
            $out[] = '';
        }
    } catch (Throwable $e) {
        error_log('llms-full cities query failed: ' . $e->getMessage());
    }

    try {
        $spokeRows = $pdo->query(
            "SELECT ci.slug AS city_slug, ci.name AS city_name, sp.slug AS spoke_slug, sp.breadcrumb_label, sp.meta_description
             FROM city_spokes sp
             INNER JOIN cities ci ON ci.id = sp.city_id
             INNER JOIN countries co ON co.id = ci.country_id
             WHERE sp.status = 'published' AND ci.status = 'published' AND co.status = 'published'
             ORDER BY ci.name, sp.sort_order, sp.slug"
        )->fetchAll();

        if ($spokeRows) {
            $out[] = '## 7. Published City Service Spokes (' . count($spokeRows) . ' Dedicated Service Pages)';
            foreach ($spokeRows as $sp) {
                $label = trim((string)($sp['breadcrumb_label'] ?: ucwords(str_replace('-', ' ', (string)$sp['spoke_slug']))));
                $desc = trim((string)($sp['meta_description'] ?? ''));
                $out[] = '- [' . $sp['city_name'] . ' — ' . $label . '](' . $origin . '/city/' . $sp['city_slug'] . '/' . $sp['spoke_slug'] . ')' . ($desc !== '' ? ': ' . $desc : '');
            }
            $out[] = '';
        }
    } catch (Throwable $e) {
        error_log('llms-full spokes query failed: ' . $e->getMessage());
    }

    try {
        $blogRows = $pdo->query(
            "SELECT slug, title, category, date, excerpt
             FROM posts
             WHERE status = 'published' AND deleted_at IS NULL
             ORDER BY date DESC, created_at DESC"
        )->fetchAll();

        if ($blogRows) {
            $out[] = '## 8. Published Planning Guides & Blog Articles (' . count($blogRows) . ' Articles)';
            foreach ($blogRows as $b) {
                $excerpt = trim(preg_replace('/\s+/', ' ', strip_tags((string)($b['excerpt'] ?? ''))));
                if (strlen($excerpt) > 180) {
                    $excerpt = substr($excerpt, 0, 177) . '...';
                }
                $out[] = '- [' . $b['title'] . '](' . $origin . '/blog/' . $b['slug'] . ') (' . $b['date'] . ' · ' . $b['category'] . ')' . ($excerpt !== '' ? ': ' . $excerpt : '');
            }
            $out[] = '';
        }
    } catch (Throwable $e) {
        error_log('llms-full blogs query failed: ' . $e->getMessage());
    }
} else {
    $out[] = '## Live Directory Index';
    $out[] = 'Full city hubs, city service spokes, and published blog guides are indexed in ' . $origin . '/sitemap.xml.';
}

echo implode("\n", $out) . "\n";
