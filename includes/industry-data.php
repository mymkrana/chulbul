<?php
require_once __DIR__ . '/industry-catalog.php';
require_once __DIR__ . '/industry-service-catalog.php';

/** Load only an allowlisted sector; never use request input as a file path. */
function cbd_industry_page(string $slug): ?array
{
    $catalog = cbd_industry_catalog();
    if (!isset($catalog[$slug])) {
        return null;
    }
    $page = require __DIR__ . '/industries/' . $catalog[$slug]['slug'] . '.php';
    $page = array_merge($page, $catalog[$slug]);
    $services = cbd_industry_service_catalog();
    foreach ($page['services'] as &$service) {
        if (!isset($services[$service['slug']])) {
            throw new RuntimeException('Unknown industry service route');
        }
        $service = array_merge($service, $services[$service['slug']]);
    }
    unset($service);
    return $page;
}
