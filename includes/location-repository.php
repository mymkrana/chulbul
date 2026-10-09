<?php
declare(strict_types=1);

require_once __DIR__ . '/database.php';

/**
 * Read-only database connection for public country/city content.
 */
function cbd_location_database(): ?PDO
{
    static $attempted = false;
    static $pdo = null;

    if ($attempted) {
        return $pdo;
    }
    $attempted = true;

    if (getenv('CBD_CITY_DATA_SOURCE') === 'php') {
        return null;
    }

    $pdo = cbd_database();

    return $pdo;
}

function cbd_location_country_key(string $isoCode): string
{
    $isoCode = strtoupper($isoCode);
    return [
        'IN' => 'india',
        'US' => 'us',
        'GB' => 'uk',
        'AU' => 'australia',
        'CA' => 'canada',
        'AE' => 'uae',
        'SG' => 'singapore',
        'ZA' => 'south_africa',
    ][$isoCode] ?? strtolower($isoCode);
}

function cbd_location_fetch_all(PDO $pdo, string $sql, array $params = []): array
{
    $statement = $pdo->prepare($sql);
    $statement->execute($params);
    return $statement->fetchAll();
}

/**
 * Base path for redirects. This is empty on the live domain and includes the
 * project directory when the site is running under localhost/XAMPP.
 */
function cbd_location_base_path(): string
{
    return cbd_base_path();
}

/**
 * Resolve a current city slug or a historical slug stored in city_redirects.
 */
function cbd_location_route(string $slug): array
{
    $slug = trim(strtolower($slug));
    if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
        return ['status' => 'not_found'];
    }

    $pdo = cbd_location_database();
    if (!$pdo) {
        return ['status' => 'unavailable'];
    }

    try {
        $current = $pdo->prepare(
            "SELECT ci.slug
             FROM cities ci
             INNER JOIN countries co ON co.id = ci.country_id
             WHERE ci.slug = ? AND ci.status = 'published' AND co.status = 'published'
             LIMIT 1"
        );
        $current->execute([$slug]);
        $currentSlug = $current->fetchColumn();
        if ($currentSlug !== false) {
            return ['status' => 'current', 'slug' => (string)$currentSlug, 'code' => 301];
        }

        $redirect = $pdo->prepare(
            "SELECT ci.slug, cr.redirect_code
             FROM city_redirects cr
             INNER JOIN cities ci ON ci.id = cr.city_id
             INNER JOIN countries co ON co.id = ci.country_id
             WHERE cr.old_slug = ? AND ci.status = 'published' AND co.status = 'published'
             LIMIT 1"
        );
        $redirect->execute([$slug]);
        $row = $redirect->fetch();
        if (!$row) {
            return ['status' => 'not_found'];
        }

        $code = (int)$row['redirect_code'];
        if (!in_array($code, [301, 302, 307, 308], true)) {
            $code = 301;
        }
        return ['status' => 'redirect', 'slug' => (string)$row['slug'], 'code' => $code];
    } catch (Throwable $e) {
        error_log('City route lookup failed for ' . $slug . ': ' . $e->getMessage());
        return ['status' => 'unavailable'];
    }
}

function cbd_location_redirect_target(string $slug): ?array
{
    $route = cbd_location_route($slug);
    return ($route['status'] ?? '') === 'redirect' ? $route : null;
}

/**
 * Returns one of:
 *   ['status' => 'ok', 'source' => 'database', 'data' => [...]]
 *   ['status' => 'not_found', 'source' => 'database']
 *   ['status' => 'unavailable', 'source' => 'php']
 */
function cbd_location_page(string $slug): array
{
    static $cache = [];

    $slug = trim(strtolower($slug));
    if (!preg_match('/^[a-z0-9-]{1,160}$/', $slug)) {
        return ['status' => 'not_found', 'source' => 'database'];
    }
    if (isset($cache[$slug])) {
        return $cache[$slug];
    }

    $pdo = cbd_location_database();
    if (!$pdo) {
        return $cache[$slug] = ['status' => 'unavailable', 'source' => 'php'];
    }

    try {
        $cityQuery = $pdo->prepare(
            "SELECT
                ci.id AS city_id, ci.country_id, ci.name, ci.display_name,
                ci.state_region, ci.slug, ci.tagline, ci.heading_html,
                ci.hero_description, ci.introduction, ci.why_local,
                ci.market_insight, ci.meta_title, ci.meta_description,
                ci.canonical_url, ci.schema_city_name,
                ci.geo_region AS city_geo_region,
                ci.geo_position AS city_geo_position,
                ci.geo_icbm AS city_geo_icbm,
                co.name AS country_name, co.iso_code, co.currency_code,
                co.currency_symbol, co.currency_label, co.hreflang,
                co.og_locale, co.price_range, co.delivery_label,
                co.geo_region AS country_geo_region,
                co.geo_position AS country_geo_position,
                co.geo_icbm AS country_geo_icbm
             FROM cities ci
             INNER JOIN countries co ON co.id = ci.country_id
             WHERE ci.slug = ? AND ci.status = 'published' AND co.status = 'published'
             LIMIT 1"
        );
        $cityQuery->execute([$slug]);
        $row = $cityQuery->fetch();

        if (!$row) {
            return $cache[$slug] = ['status' => 'not_found', 'source' => 'database'];
        }

        $countryKey = cbd_location_country_key((string)$row['iso_code']);
        $cityId = (int)$row['city_id'];
        $countryId = (int)$row['country_id'];

        $cityStatRows = cbd_location_fetch_all(
            $pdo,
            'SELECT value_text, label FROM city_stats WHERE city_id = ? ORDER BY sort_order, id',
            [$cityId]
        );
        $countryStatRows = cbd_location_fetch_all(
            $pdo,
            'SELECT value_text, label FROM country_stats WHERE country_id = ? ORDER BY sort_order, id',
            [$countryId]
        );
        $mapStats = static fn(array $items): array => array_map(
            static fn(array $item): array => ['num' => $item['value_text'], 'label' => $item['label']],
            $items
        );
        $cityStats = $mapStats($cityStatRows);
        $stats = $cityStats ?: $mapStats($countryStatRows);

        $highlightRows = cbd_location_fetch_all(
            $pdo,
            'SELECT highlight FROM country_highlights WHERE country_id = ? ORDER BY sort_order, id',
            [$countryId]
        );
        $highlights = array_column($highlightRows, 'highlight');

        $serviceRows = cbd_location_fetch_all(
            $pdo,
            "SELECT icon_class, title, href, description
             FROM country_services
             WHERE country_id = ? AND status = 'published'
             ORDER BY sort_order, id",
            [$countryId]
        );
        $services = array_map(
            static fn(array $service): array => [
                'icon' => $service['icon_class'],
                'title' => $service['title'],
                'href' => $service['href'],
                'desc' => $service['description'],
            ],
            $serviceRows
        );

        $testimonialRows = cbd_location_fetch_all(
            $pdo,
            "SELECT client_name, client_role, rating, testimonial
             FROM country_testimonials
             WHERE country_id = ? AND status = 'published'
             ORDER BY sort_order, id",
            [$countryId]
        );
        $testimonials = array_map(
            static fn(array $testimonial): array => [
                'name' => $testimonial['client_name'],
                'role' => $testimonial['client_role'],
                'stars' => (int)$testimonial['rating'],
                'text' => $testimonial['testimonial'],
            ],
            $testimonialRows
        );

        $areaRows = cbd_location_fetch_all(
            $pdo,
            'SELECT area_name FROM city_areas WHERE city_id = ? ORDER BY sort_order, id',
            [$cityId]
        );
        $industryRows = cbd_location_fetch_all(
            $pdo,
            'SELECT industry_name FROM city_industries WHERE city_id = ? ORDER BY sort_order, id',
            [$cityId]
        );
        $faqRows = cbd_location_fetch_all(
            $pdo,
            "SELECT question, answer FROM city_faqs
             WHERE city_id = ? AND status = 'published'
             ORDER BY sort_order, id",
            [$cityId]
        );
        $relatedRows = cbd_location_fetch_all(
            $pdo,
            "SELECT name, slug FROM cities
             WHERE country_id = ? AND status = 'published'
             ORDER BY sort_order, id",
            [$countryId]
        );

        // Query-led sections are optional. Keeping this lookup isolated means
        // city pages continue to work if application files are deployed before
        // the accompanying database migration.
        $querySectionRows = [];
        try {
            $querySectionRows = cbd_location_fetch_all(
                $pdo,
                "SELECT
                    section_key, placement, layout_style, eyebrow, title,
                    description, bullet_points, icon_class,
                    primary_link_label, primary_link_href,
                    secondary_link_label, secondary_link_href
                 FROM city_query_sections
                 WHERE city_id = ? AND status = 'published'
                 ORDER BY placement, sort_order, id",
                [$cityId]
            );
        } catch (Throwable $sectionError) {
            error_log(
                'Optional city query sections unavailable for ' . $slug . ': ' .
                $sectionError->getMessage()
            );
        }

        $querySections = array_map(
            static function (array $section): array {
                $bullets = json_decode((string)($section['bullet_points'] ?? ''), true);
                if (!is_array($bullets)) {
                    $bullets = [];
                }

                $bullets = array_values(array_filter(
                    array_map(static fn($bullet): string => trim((string)$bullet), $bullets),
                    static fn(string $bullet): bool => $bullet !== ''
                ));

                return [
                    'key' => (string)$section['section_key'],
                    'placement' => (string)$section['placement'],
                    'layout' => (string)$section['layout_style'],
                    'eyebrow' => (string)($section['eyebrow'] ?? ''),
                    'title' => (string)$section['title'],
                    'description' => (string)$section['description'],
                    'bullets' => $bullets,
                    'icon' => (string)($section['icon_class'] ?? 'bi-check-circle'),
                    'primary_link' => [
                        'label' => (string)($section['primary_link_label'] ?? ''),
                        'href' => (string)($section['primary_link_href'] ?? ''),
                    ],
                    'secondary_link' => [
                        'label' => (string)($section['secondary_link_label'] ?? ''),
                        'href' => (string)($section['secondary_link_href'] ?? ''),
                    ],
                ];
            },
            $querySectionRows
        );

        $planRows = cbd_location_fetch_all(
            $pdo,
            "SELECT
                p.id, p.plan_key, p.plan_name, p.price_text,
                p.short_description, p.billing_label, p.is_popular,
                f.feature_text
             FROM city_pricing_plans p
             LEFT JOIN city_plan_features f ON f.pricing_plan_id = p.id
             WHERE p.city_id = ? AND p.status = 'published'
             ORDER BY p.sort_order, p.id, f.sort_order, f.id",
            [$cityId]
        );
        $plans = [];
        foreach ($planRows as $planRow) {
            $planKey = (string)$planRow['plan_key'];
            if (!isset($plans[$planKey])) {
                $plans[$planKey] = [
                    'name' => $planRow['plan_name'],
                    'price' => $planRow['price_text'],
                    'label' => $planRow['short_description'],
                    'billing_label' => $planRow['billing_label'],
                    'popular' => (bool)$planRow['is_popular'],
                    'features' => [],
                ];
            }
            if ($planRow['feature_text'] !== null) {
                $plans[$planKey]['features'][] = $planRow['feature_text'];
            }
        }

        $symbol = (string)$row['currency_symbol'];
        $city = [
            'name' => $row['name'],
            'display' => $row['display_name'],
            'state' => $row['state_region'],
            'slug' => $row['slug'],
            'symbol' => $symbol,
            'currency_label' => $row['currency_label'],
            'tagline' => $row['tagline'],
            'h1' => $row['heading_html'],
            'hero_desc' => $row['hero_description'],
            'meta_title' => $row['meta_title'],
            'meta_desc' => $row['meta_description'],
            'canonical' => 'https://www.chulbuldesign.com/city/' . $row['slug'],
            'areas' => array_column($areaRows, 'area_name'),
            'industries' => array_column($industryRows, 'industry_name'),
            'intro' => $row['introduction'],
            'why_local' => $row['why_local'],
            'faq' => array_map(
                static fn(array $faq): array => ['q' => $faq['question'], 'a' => $faq['answer']],
                $faqRows
            ),
            'schema_city' => $row['schema_city_name'],
        ];

        if ($cityStats) {
            $city['stats'] = $cityStats;
        }
        foreach (['geo_region', 'geo_position', 'geo_icbm'] as $geoField) {
            $databaseField = 'city_' . $geoField;
            if ($row[$databaseField] !== null && $row[$databaseField] !== '') {
                $city[$geoField] = $row[$databaseField];
            }
        }

        foreach (['starter', 'growth', 'enterprise'] as $planKey) {
            if (!isset($plans[$planKey])) {
                continue;
            }
            $city['plan_' . $planKey] = $plans[$planKey]['features'];
            if ($planKey !== 'enterprise') {
                $price = (string)$plans[$planKey]['price'];
                $city[$planKey . '_price'] = str_starts_with($price, $symbol)
                    ? substr($price, strlen($symbol))
                    : $price;
            }
        }

        $related = [];
        foreach ($relatedRows as $relatedCity) {
            $related[$relatedCity['name']] = $relatedCity['slug'];
        }

        return $cache[$slug] = [
            'status' => 'ok',
            'source' => 'database',
            'data' => [
                'city' => $city,
                'country_key' => $countryKey,
                'stats' => $stats,
                'highlights' => $highlights,
                'services' => $services,
                'testimonials' => $testimonials,
                'related' => $related,
                'pricing_plans' => $plans,
                'market_insight' => $row['market_insight'],
                'query_sections' => $querySections,
                'price_range' => $row['price_range'],
                'og_locale' => $row['og_locale'],
                'hreflang' => $row['hreflang'],
                'delivery_label' => $row['delivery_label'],
                'country_geo' => [
                    'region' => $row['country_geo_region'],
                    'position' => $row['country_geo_position'],
                    'icbm' => $row['country_geo_icbm'],
                ],
            ],
        ];
    } catch (Throwable $e) {
        error_log('City database read failed for ' . $slug . ': ' . $e->getMessage());
        return $cache[$slug] = ['status' => 'unavailable', 'source' => 'php'];
    }
}

/**
 * Read one published city service spoke. Spokes are intentionally independent
 * records so a nested URL only exists after substantial page content is ready.
 */
function cbd_location_spoke_page(string $citySlug, string $spokeSlug): array
{
    static $cache = [];

    $citySlug = trim(strtolower($citySlug));
    $spokeSlug = trim(strtolower($spokeSlug));
    $cacheKey = $citySlug . '/' . $spokeSlug;

    $validSlug = static fn(string $value): bool =>
        preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value) === 1;

    if (!$validSlug($citySlug) || !$validSlug($spokeSlug)) {
        return ['status' => 'not_found', 'source' => 'database'];
    }
    if (isset($cache[$cacheKey])) {
        return $cache[$cacheKey];
    }

    $pdo = cbd_location_database();
    if (!$pdo) {
        return $cache[$cacheKey] = ['status' => 'unavailable', 'source' => 'php'];
    }

    try {
        $statement = $pdo->prepare(
            "SELECT
                sp.id, sp.slug, sp.meta_title, sp.meta_description,
                sp.canonical_url, sp.breadcrumb_label, sp.eyebrow,
                sp.heading_html, sp.hero_description, sp.introduction,
                sp.content_json, sp.proof_json, sp.faqs_json,
                sp.schema_service_name, sp.schema_service_type,
                sp.cta_title, sp.cta_description, sp.updated_at,
                ci.id AS city_id, ci.slug AS city_slug, ci.name AS city_name,
                ci.display_name AS city_display_name, ci.state_region,
                ci.schema_city_name, ci.canonical_url AS city_canonical_url,
                ci.geo_region, ci.geo_position, ci.geo_icbm,
                co.name AS country_name, co.iso_code, co.og_locale, co.hreflang
             FROM city_spokes sp
             INNER JOIN cities ci ON ci.id = sp.city_id
             INNER JOIN countries co ON co.id = ci.country_id
             WHERE ci.slug = ? AND sp.slug = ?
               AND sp.status = 'published'
               AND ci.status = 'published'
               AND co.status = 'published'
             LIMIT 1"
        );
        $statement->execute([$citySlug, $spokeSlug]);
        $row = $statement->fetch();

        if (!$row) {
            return $cache[$cacheKey] = ['status' => 'not_found', 'source' => 'database'];
        }

        $decodeObject = static function (mixed $json, string $label) use ($cacheKey): array {
            $decoded = json_decode((string)$json, true);
            if (!is_array($decoded)) {
                throw new RuntimeException('Invalid ' . $label . ' JSON for city spoke ' . $cacheKey);
            }
            return $decoded;
        };

        $content = $decodeObject($row['content_json'], 'content');
        $proof = $decodeObject($row['proof_json'], 'proof');
        $faqs = $decodeObject($row['faqs_json'], 'FAQ');

        // Quality guard: never render an accidentally thin or incomplete spoke.
        if (count($content['platforms'] ?? []) < 3
            || count($content['process'] ?? []) < 5
            || count($content['pricing_factors'] ?? []) < 4
            || count($faqs) < 4
            || empty($proof['problem'])
            || empty($proof['work'])
            || empty($proof['result'])) {
            throw new RuntimeException('Incomplete content for city spoke ' . $cacheKey);
        }

        return $cache[$cacheKey] = [
            'status' => 'ok',
            'source' => 'database',
            'data' => [
                'page' => [
                    'id' => (int)$row['id'],
                    'slug' => (string)$row['slug'],
                    'meta_title' => (string)$row['meta_title'],
                    'meta_description' => (string)$row['meta_description'],
                    'canonical_url' => (string)$row['canonical_url'],
                    'breadcrumb_label' => (string)$row['breadcrumb_label'],
                    'eyebrow' => (string)$row['eyebrow'],
                    'heading_html' => (string)$row['heading_html'],
                    'hero_description' => (string)$row['hero_description'],
                    'introduction' => (string)$row['introduction'],
                    'schema_service_name' => (string)$row['schema_service_name'],
                    'schema_service_type' => (string)$row['schema_service_type'],
                    'cta_title' => (string)$row['cta_title'],
                    'cta_description' => (string)$row['cta_description'],
                    'updated_at' => (string)$row['updated_at'],
                ],
                'city' => [
                    'id' => (int)$row['city_id'],
                    'slug' => (string)$row['city_slug'],
                    'name' => (string)$row['city_name'],
                    'display_name' => (string)$row['city_display_name'],
                    'state' => (string)($row['state_region'] ?? ''),
                    'schema_city_name' => (string)$row['schema_city_name'],
                    'canonical_url' => (string)$row['city_canonical_url'],
                    'geo_region' => (string)($row['geo_region'] ?? ''),
                    'geo_position' => (string)($row['geo_position'] ?? ''),
                    'geo_icbm' => (string)($row['geo_icbm'] ?? ''),
                ],
                'country' => [
                    'name' => (string)$row['country_name'],
                    'iso_code' => (string)$row['iso_code'],
                    'og_locale' => (string)$row['og_locale'],
                    'hreflang' => (string)$row['hreflang'],
                ],
                'content' => $content,
                'proof' => $proof,
                'faqs' => array_values($faqs),
            ],
        ];
    } catch (Throwable $e) {
        error_log('City spoke read failed for ' . $cacheKey . ': ' . $e->getMessage());
        return $cache[$cacheKey] = ['status' => 'unavailable', 'source' => 'database'];
    }
}
