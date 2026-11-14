<?php
declare(strict_types=1);

// Only CLI or the short-lived authenticated web runner may execute this updater.
if (PHP_SAPI !== 'cli' && !defined('CBD_DEPLOY_BLOG_AUTHORIZED')) {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/includes/database.php';

/** Return UTF-8 character length without failing if mbstring is disabled. */
function cbd_seo_char_len(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

/** Load, parse, and validate the Page-1 SEO upgrade JSON payload. */
function cbd_load_city_page1_payload(string $filename): array
{
    if (!preg_match('/^[a-z0-9_.-]+\.json$/i', $filename) || str_contains($filename, '..')) {
        throw new RuntimeException('Invalid upgrade payload filename.');
    }

    $path = __DIR__ . '/' . $filename;
    if (!is_file($path)) {
        throw new RuntimeException('Upgrade payload not found: ' . $filename);
    }

    $payload = json_decode((string)file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($payload)) {
        throw new RuntimeException('Upgrade payload must decode to an object.');
    }

    foreach (['cities', 'spokes', 'services'] as $section) {
        if (!isset($payload[$section]) || !is_array($payload[$section])) {
            throw new RuntimeException('Missing payload section: ' . $section);
        }
    }

    cbd_validate_seo_rows($payload['cities'], 'city', ['slug', 'meta_title', 'meta_description', 'tagline', 'heading_html', 'hero_description']);
    cbd_validate_seo_rows($payload['spokes'], 'spoke', ['city_slug', 'slug', 'meta_title', 'meta_description', 'heading_html']);
    cbd_validate_seo_rows($payload['services'], 'service', ['slug', 'meta_title', 'meta_description']);

    return $payload;
}

/** Validate required fields, uniqueness, and SERP character budgets for a payload section. */
function cbd_validate_seo_rows(array $rows, string $type, array $requiredFields): void
{
    $seenKeys = [];
    foreach ($rows as $index => $row) {
        if (!is_array($row)) {
            throw new RuntimeException("Invalid {$type} entry at index {$index}.");
        }
        foreach ($requiredFields as $field) {
            if (!is_string($row[$field] ?? null) || trim($row[$field]) === '') {
                throw new RuntimeException("Missing {$type}.{$field} at index {$index}.");
            }
        }

        $key = $type === 'spoke'
            ? $row['city_slug'] . '/' . $row['slug']
            : $row['slug'];
        if (isset($seenKeys[$key])) {
            throw new RuntimeException("Duplicate {$type} key in payload: {$key}");
        }
        $seenKeys[$key] = true;

        if (cbd_seo_char_len($row['meta_title']) > 60) {
            throw new RuntimeException("{$type} meta_title exceeds 60 chars ({$key}).");
        }
        if (cbd_seo_char_len($row['meta_description']) > 158) {
            throw new RuntimeException("{$type} meta_description exceeds 158 chars ({$key}).");
        }
    }
}

/** Ensure required tables exist and use InnoDB before wrapping updates in a transaction. */
function cbd_assert_innodb_tables(PDO $pdo, array $tables): void
{
    $placeholders = implode(', ', array_fill(0, count($tables), '?'));
    $stmt = $pdo->prepare(
        "SELECT TABLE_NAME, ENGINE
         FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ({$placeholders})"
    );
    $stmt->execute(array_values($tables));
    $engines = [];
    foreach ($stmt->fetchAll() as $row) {
        $engines[(string)$row['TABLE_NAME']] = strtoupper((string)$row['ENGINE']);
    }

    foreach ($tables as $table) {
        if (($engines[$table] ?? '') !== 'INNODB') {
            throw new RuntimeException("Table {$table} must exist and use InnoDB before updating.");
        }
    }
}

/** Snapshot current live rows to scripts/seo_backup_before_page1_upgrade_<timestamp>.json. */
function cbd_write_seo_backup(PDO $pdo): ?string
{
    if (!is_writable(__DIR__)) {
        return null;
    }

    $backupData = [
        'created_at_utc' => gmdate('c'),
        'cities' => $pdo->query(
            "SELECT id, slug, meta_title, meta_description, tagline, heading_html, hero_description, status
             FROM cities ORDER BY id ASC"
        )->fetchAll(),
        'spokes' => $pdo->query(
            "SELECT sp.id, ci.slug AS city_slug, sp.slug, sp.meta_title, sp.meta_description, sp.heading_html, sp.status
             FROM city_spokes sp
             INNER JOIN cities ci ON ci.id = sp.city_id
             ORDER BY sp.id ASC"
        )->fetchAll(),
        'services' => $pdo->query(
            "SELECT id, slug, meta_title, meta_desc, status
             FROM services
             WHERE deleted_at IS NULL
             ORDER BY id ASC"
        )->fetchAll(),
    ];

    $filename = 'seo_backup_before_page1_upgrade_' . gmdate('Ymd_His') . '.json';
    $encoded = json_encode($backupData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($encoded === false) {
        return null;
    }

    return @file_put_contents(__DIR__ . '/' . $filename, $encoded) !== false ? $filename : null;
}

/** Apply Page-1 SEO metadata & heading upgrades to cities, city_spokes, and services. */
function cbd_publish_city_page1_seo(
    string $filename = 'seo_city_page1_upgrade_2026_11_14.json',
    bool $dryRun = false
): array {
    $payload = cbd_load_city_page1_payload($filename);
    $pdo = cbd_database();
    if (!$pdo) {
        throw new RuntimeException('Database unavailable.');
    }

    cbd_assert_innodb_tables($pdo, ['cities', 'city_spokes', 'services']);
    $backupFile = $dryRun ? null : cbd_write_seo_backup($pdo);

    $summary = [
        'success' => true,
        'dry_run' => $dryRun,
        'backup_file' => $backupFile,
        'cities_updated' => 0,
        'cities_unchanged' => 0,
        'cities_missing' => 0,
        'missing_city_slugs' => [],
        'spokes_updated' => 0,
        'spokes_unchanged' => 0,
        'spokes_missing' => 0,
        'missing_spoke_slugs' => [],
        'services_updated' => 0,
        'services_unchanged' => 0,
        'services_missing' => 0,
        'missing_service_slugs' => [],
    ];

    $pdo->beginTransaction();
    try {
        $selectCity = $pdo->prepare(
            "SELECT id, meta_title, meta_description, tagline, heading_html, hero_description
             FROM cities
             WHERE slug = ? AND status = 'published'
             FOR UPDATE"
        );
        $updateCity = $pdo->prepare(
            "UPDATE cities
             SET meta_title = ?, meta_description = ?, tagline = ?, heading_html = ?, hero_description = ?
             WHERE slug = ? AND status = 'published'"
        );

        foreach ($payload['cities'] as $city) {
            $selectCity->execute([$city['slug']]);
            $existing = $selectCity->fetch();
            if (!$existing) {
                $summary['cities_missing']++;
                $summary['missing_city_slugs'][] = $city['slug'];
                continue;
            }

            $isSame = (string)$existing['meta_title'] === $city['meta_title']
                && (string)$existing['meta_description'] === $city['meta_description']
                && (string)$existing['tagline'] === $city['tagline']
                && (string)$existing['heading_html'] === $city['heading_html']
                && (string)$existing['hero_description'] === $city['hero_description'];

            if ($isSame) {
                $summary['cities_unchanged']++;
                continue;
            }

            $updateCity->execute([
                $city['meta_title'],
                $city['meta_description'],
                $city['tagline'],
                $city['heading_html'],
                $city['hero_description'],
                $city['slug'],
            ]);
            $summary['cities_updated']++;
        }

        $selectSpoke = $pdo->prepare(
            "SELECT sp.id, sp.meta_title, sp.meta_description, sp.heading_html
             FROM city_spokes sp
             INNER JOIN cities ci ON ci.id = sp.city_id
             WHERE ci.slug = ? AND sp.slug = ? AND sp.status = 'published'
             FOR UPDATE"
        );
        $updateSpoke = $pdo->prepare(
            "UPDATE city_spokes
             SET meta_title = ?, meta_description = ?, heading_html = ?
             WHERE id = ?"
        );

        foreach ($payload['spokes'] as $spoke) {
            $selectSpoke->execute([$spoke['city_slug'], $spoke['slug']]);
            $existing = $selectSpoke->fetch();
            if (!$existing) {
                $summary['spokes_missing']++;
                $summary['missing_spoke_slugs'][] = $spoke['city_slug'] . '/' . $spoke['slug'];
                continue;
            }

            $isSame = (string)$existing['meta_title'] === $spoke['meta_title']
                && (string)$existing['meta_description'] === $spoke['meta_description']
                && (string)$existing['heading_html'] === $spoke['heading_html'];

            if ($isSame) {
                $summary['spokes_unchanged']++;
                continue;
            }

            $updateSpoke->execute([
                $spoke['meta_title'],
                $spoke['meta_description'],
                $spoke['heading_html'],
                (int)$existing['id'],
            ]);
            $summary['spokes_updated']++;
        }

        $selectService = $pdo->prepare(
            "SELECT id, meta_title, meta_desc
             FROM services
             WHERE slug = ? AND status = 1 AND deleted_at IS NULL
             FOR UPDATE"
        );
        $updateService = $pdo->prepare(
            "UPDATE services
             SET meta_title = ?, meta_desc = ?
             WHERE slug = ? AND status = 1 AND deleted_at IS NULL"
        );

        foreach ($payload['services'] as $service) {
            $selectService->execute([$service['slug']]);
            $existing = $selectService->fetch();
            if (!$existing) {
                $summary['services_missing']++;
                $summary['missing_service_slugs'][] = $service['slug'];
                continue;
            }

            $isSame = (string)$existing['meta_title'] === $service['meta_title']
                && (string)$existing['meta_desc'] === $service['meta_description'];

            if ($isSame) {
                $summary['services_unchanged']++;
                continue;
            }

            $updateService->execute([
                $service['meta_title'],
                $service['meta_description'],
                $service['slug'],
            ]);
            $summary['services_updated']++;
        }

        if ($dryRun) {
            $pdo->rollBack();
        } else {
            $pdo->commit();
        }
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $error;
    }

    return $summary;
}

if (PHP_SAPI === 'cli' && realpath((string)($_SERVER['SCRIPT_FILENAME'] ?? '')) === __FILE__) {
    $args = array_slice($argv, 1);
    $dryRun = in_array('--dry-run', $args, true);
    $positional = array_values(array_filter($args, static fn(string $arg): bool => !str_starts_with($arg, '--')));
    $filename = $positional[0] ?? 'seo_city_page1_upgrade_2026_11_14.json';

    try {
        echo json_encode(cbd_publish_city_page1_seo($filename, $dryRun), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
    } catch (Throwable $error) {
        fwrite(STDERR, $error->getMessage() . PHP_EOL);
        exit(1);
    }
}
