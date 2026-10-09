<?php
declare(strict_types=1);

require_once __DIR__ . '/location-repository.php';

/**
 * Published countries and cities for the public /cities directory.
 * An empty array signals that the database is unavailable or returned no
 * published locations. The caller must not substitute hardcoded city data.
 */
function cbd_location_listing(): array
{
    $pdo = cbd_location_database();
    if (!$pdo) {
        return [];
    }

    try {
        $countries = $pdo->query(
            "SELECT id, name, slug, iso_code, currency_code, currency_symbol
             FROM countries
             WHERE status = 'published'
             ORDER BY sort_order, name"
        )->fetchAll();

        $cities = $pdo->query(
            "SELECT ci.id, ci.country_id, ci.name, ci.display_name, ci.state_region,
                    ci.slug, ci.tagline
             FROM cities ci
             INNER JOIN countries co ON co.id = ci.country_id
             WHERE ci.status = 'published' AND co.status = 'published'
             ORDER BY co.sort_order, ci.sort_order, ci.name"
        )->fetchAll();

        $industryRows = $pdo->query(
            "SELECT i.city_id, i.industry_name
             FROM city_industries i
             INNER JOIN cities ci ON ci.id = i.city_id
             INNER JOIN countries co ON co.id = ci.country_id
             WHERE ci.status = 'published' AND co.status = 'published'
             ORDER BY i.city_id, i.sort_order, i.id"
        )->fetchAll();

        $priceRows = $pdo->query(
            "SELECT p.city_id, p.price_text
             FROM city_pricing_plans p
             INNER JOIN cities ci ON ci.id = p.city_id
             INNER JOIN countries co ON co.id = ci.country_id
             WHERE p.plan_key = 'starter' AND p.status = 'published'
               AND ci.status = 'published' AND co.status = 'published'
             ORDER BY p.city_id, p.sort_order, p.id"
        )->fetchAll();

        $industries = [];
        foreach ($industryRows as $row) {
            $industries[(int)$row['city_id']][] = (string)$row['industry_name'];
        }
        $prices = [];
        foreach ($priceRows as $row) {
            $cityId = (int)$row['city_id'];
            if (!isset($prices[$cityId])) {
                $prices[$cityId] = (string)$row['price_text'];
            }
        }

        $citiesByCountry = [];
        foreach ($cities as $city) {
            $cityId = (int)$city['id'];
            $marketParts = array_slice($industries[$cityId] ?? [], 0, 4);
            $region = trim((string)$city['state_region']);
            $description = implode(', ', $marketParts);
            if ($region !== '' && $description !== '') {
                $description = $region . ' — ' . $description;
            } elseif ($region !== '') {
                $description = $region;
            } elseif ($description === '') {
                $description = (string)$city['tagline'];
            }

            $citiesByCountry[(int)$city['country_id']][] = [
                (string)($city['display_name'] ?: $city['name']),
                (string)$city['slug'],
                $description,
                $prices[$cityId] ?? '',
            ];
        }

        $flags = [
            'IN' => '🇮🇳', 'US' => '🇺🇸', 'GB' => '🇬🇧', 'AU' => '🇦🇺',
            'CA' => '🇨🇦', 'AE' => '🇦🇪', 'SG' => '🇸🇬', 'ZA' => '🇿🇦',
        ];
        $anchorIds = ['US' => 'usa', 'GB' => 'uk', 'AE' => 'uae', 'ZA' => 'south-africa'];
        $colors = [
            'IN' => '#FF9933', 'US' => '#3C3B6E', 'GB' => '#012169', 'AU' => '#00008B',
            'CA' => '#FF0000', 'AE' => '#009900', 'SG' => '#EF3340', 'ZA' => '#007A4D',
        ];

        $groups = [];
        foreach ($countries as $country) {
            $countryId = (int)$country['id'];
            $countryCities = $citiesByCountry[$countryId] ?? [];
            if (!$countryCities) {
                continue;
            }

            $iso = strtoupper((string)$country['iso_code']);
            $symbol = trim((string)$country['currency_symbol']);
            $currencyCode = strtoupper((string)$country['currency_code']);
            $currency = strtoupper($symbol) === $currencyCode
                ? $currencyCode
                : trim($symbol . ' ' . $currencyCode);
            $starting = '';
            foreach ($countryCities as $city) {
                if ($city[3] !== '') {
                    $starting = $city[3];
                    break;
                }
            }

            $groups[] = [
                'id' => $anchorIds[$iso] ?? (string)$country['slug'],
                'flag' => $flags[$iso] ?? '🌍',
                'country' => (string)$country['name'],
                'currency' => $currency,
                'starting' => $starting ?: 'Custom Quote',
                'color' => $colors[$iso] ?? '#49499A',
                'cities' => array_map(
                    static fn(array $city): array => [$city[0], $city[1], $city[2]],
                    $countryCities
                ),
            ];
        }

        return $groups;
    } catch (Throwable $e) {
        error_log('Location listing database read failed: ' . $e->getMessage());
        return [];
    }
}
