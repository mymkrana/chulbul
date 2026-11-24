<?php
declare(strict_types=1);

// Only CLI or the short-lived authenticated web runner may execute this publisher.
if (PHP_SAPI !== 'cli' && !defined('CBD_DEPLOY_BLOG_AUTHORIZED')) {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/includes/database.php';

function cbd_spoke_char_len(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

/** Load and validate the 10 high-demand city spokes JSON bundle against SERP & quality guards. */
function cbd_load_high_demand_spokes_payload(string $filename): array
{
    if (!preg_match('/^[a-z0-9_.-]+\.json$/i', $filename) || str_contains($filename, '..')) {
        throw new RuntimeException('Invalid spokes bundle filename.');
    }

    $path = __DIR__ . '/' . $filename;
    if (!is_file($path)) {
        throw new RuntimeException('Spokes bundle not found: ' . $filename);
    }

    $payload = json_decode((string)file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($payload) || !isset($payload['spokes']) || !is_array($payload['spokes']) || empty($payload['spokes'])) {
        throw new RuntimeException('Bundle must contain a non-empty spokes array.');
    }

    $requiredStrings = [
        'city_slug', 'slug', 'meta_title', 'meta_description', 'canonical_url',
        'breadcrumb_label', 'eyebrow', 'heading_html', 'hero_description',
        'introduction', 'schema_service_name', 'schema_service_type',
        'cta_title', 'cta_description',
    ];

    $seenKeys = [];
    $seenTitles = [];
    $seenDescs = [];

    foreach ($payload['spokes'] as $index => $sp) {
        if (!is_array($sp)) {
            throw new RuntimeException("Invalid spoke row at index {$index}.");
        }
        foreach ($requiredStrings as $field) {
            if (!is_string($sp[$field] ?? null) || trim($sp[$field]) === '') {
                throw new RuntimeException("Missing {$field} at index {$index}.");
            }
        }
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $sp['city_slug'])
            || !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $sp['slug'])) {
            throw new RuntimeException("Invalid city_slug or slug at index {$index}.");
        }

        $key = $sp['city_slug'] . '/' . $sp['slug'];
        if (isset($seenKeys[$key])) {
            throw new RuntimeException("Duplicate spoke key: {$key}");
        }
        $seenKeys[$key] = true;

        if (cbd_spoke_char_len($sp['meta_title']) > 60) {
            throw new RuntimeException("meta_title exceeds 60 chars for {$key}.");
        }
        if (cbd_spoke_char_len($sp['meta_description']) > 158) {
            throw new RuntimeException("meta_description exceeds 158 chars for {$key}.");
        }
        if (isset($seenTitles[$sp['meta_title']])) {
            throw new RuntimeException("Duplicate meta_title for {$key}.");
        }
        if (isset($seenDescs[$sp['meta_description']])) {
            throw new RuntimeException("Duplicate meta_description for {$key}.");
        }
        $seenTitles[$sp['meta_title']] = true;
        $seenDescs[$sp['meta_description']] = true;

        // Enforce exact cbd_location_spoke_page() quality guards before DB write.
        $content = $sp['content'] ?? null;
        $proof = $sp['proof'] ?? null;
        $faqs = $sp['faqs'] ?? null;
        if (!is_array($content) || !is_array($proof) || !is_array($faqs)) {
            throw new RuntimeException("Missing structured content/proof/faqs for {$key}.");
        }
        if (count($content['platforms'] ?? []) < 3
            || count($content['process'] ?? []) < 5
            || count($content['pricing_factors'] ?? []) < 4
            || count($faqs) < 4
            || trim((string)($proof['problem'] ?? '')) === ''
            || trim((string)($proof['work'] ?? '')) === ''
            || trim((string)($proof['result'] ?? '')) === '') {
            throw new RuntimeException("Spoke {$key} failed cbd_location_spoke_page() quality guards.");
        }
    }

    return $payload;
}

/** Snapshot any pre-existing matching spoke rows before upsert. */
function cbd_backup_existing_spokes(PDO $pdo, array $spokes): ?string
{
    if (!is_writable(__DIR__)) {
        return null;
    }

    $stmt = $pdo->prepare(
        "SELECT sp.*, ci.slug AS city_slug
         FROM city_spokes sp
         INNER JOIN cities ci ON ci.id = sp.city_id
         WHERE ci.slug = ? AND sp.slug = ?
         LIMIT 1"
    );
    $existingRows = [];
    foreach ($spokes as $sp) {
        $stmt->execute([$sp['city_slug'], $sp['slug']]);
        $row = $stmt->fetch();
        if ($row) {
            $existingRows[] = $row;
        }
    }

    $filename = 'seo_backup_10_spokes_' . gmdate('Ymd_His') . '.json';
    $encoded = json_encode([
        'created_at_utc' => gmdate('c'),
        'existing_matching_spokes' => $existingRows,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($encoded === false) {
        return null;
    }
    return @file_put_contents(__DIR__ . '/' . $filename, $encoded) !== false ? $filename : null;
}

/** Insert or update the 10 high-demand city spoke pages inside one InnoDB transaction. */
function cbd_publish_high_demand_spokes(
    string $filename = 'spokes_high_demand_2026_11_24.json',
    bool $dryRun = false
): array {
    $payload = cbd_load_high_demand_spokes_payload($filename);
    $pdo = cbd_database();
    if (!$pdo) {
        throw new RuntimeException('Database unavailable.');
    }

    $engineStmt = $pdo->query(
        "SELECT TABLE_NAME, ENGINE
         FROM information_schema.TABLES
         WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('cities', 'city_spokes')"
    );
    $engines = [];
    foreach ($engineStmt->fetchAll() as $row) {
        $engines[(string)$row['TABLE_NAME']] = strtoupper((string)$row['ENGINE']);
    }
    foreach (['cities', 'city_spokes'] as $table) {
        if (($engines[$table] ?? '') !== 'INNODB') {
            throw new RuntimeException("Table {$table} must use InnoDB before publishing spokes.");
        }
    }

    $backupFile = $dryRun ? null : cbd_backup_existing_spokes($pdo, $payload['spokes']);

    $summary = [
        'success' => true,
        'dry_run' => $dryRun,
        'backup_file' => $backupFile,
        'inserted' => 0,
        'updated' => 0,
        'unchanged' => 0,
        'missing_cities' => [],
        'published_urls' => [],
    ];

    $jsonFlags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR;

    $pdo->beginTransaction();
    try {
        $findCity = $pdo->prepare("SELECT id FROM cities WHERE slug = ? AND status = 'published' LIMIT 1");
        $findSpoke = $pdo->prepare(
            "SELECT id, meta_title, meta_description, canonical_url, breadcrumb_label, eyebrow,
                    heading_html, hero_description, introduction, content_json, proof_json,
                    faqs_json, schema_service_name, schema_service_type, cta_title,
                    cta_description, status, sort_order
             FROM city_spokes
             WHERE city_id = ? AND slug = ?
             FOR UPDATE"
        );
        $insertSpoke = $pdo->prepare(
            "INSERT INTO city_spokes (
                city_id, slug, meta_title, meta_description, canonical_url,
                breadcrumb_label, eyebrow, heading_html, hero_description, introduction,
                content_json, proof_json, faqs_json, schema_service_name, schema_service_type,
                cta_title, cta_description, status, sort_order, published_at, created_at, updated_at
             ) VALUES (
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, 'published', ?, NOW(), NOW(), NOW()
             )"
        );
        $updateSpoke = $pdo->prepare(
            "UPDATE city_spokes
             SET meta_title = ?, meta_description = ?, canonical_url = ?,
                 breadcrumb_label = ?, eyebrow = ?, heading_html = ?,
                 hero_description = ?, introduction = ?, content_json = ?,
                 proof_json = ?, faqs_json = ?, schema_service_name = ?,
                 schema_service_type = ?, cta_title = ?, cta_description = ?,
                 status = 'published', sort_order = ?,
                 published_at = COALESCE(published_at, NOW()), updated_at = NOW()
             WHERE id = ?"
        );

        foreach ($payload['spokes'] as $sp) {
            $findCity->execute([$sp['city_slug']]);
            $cityId = $findCity->fetchColumn();
            if (!$cityId) {
                $summary['missing_cities'][] = $sp['city_slug'];
                continue;
            }
            $cityId = (int)$cityId;

            $contentJson = json_encode($sp['content'], $jsonFlags);
            $proofJson = json_encode($sp['proof'], $jsonFlags);
            $faqsJson = json_encode($sp['faqs'], $jsonFlags);
            $sortOrder = (int)($sp['sort_order'] ?? 30);

            $findSpoke->execute([$cityId, $sp['slug']]);
            $existing = $findSpoke->fetch();

            if (!$existing) {
                $insertSpoke->execute([
                    $cityId,
                    $sp['slug'],
                    $sp['meta_title'],
                    $sp['meta_description'],
                    $sp['canonical_url'],
                    $sp['breadcrumb_label'],
                    $sp['eyebrow'],
                    $sp['heading_html'],
                    $sp['hero_description'],
                    $sp['introduction'],
                    $contentJson,
                    $proofJson,
                    $faqsJson,
                    $sp['schema_service_name'],
                    $sp['schema_service_type'],
                    $sp['cta_title'],
                    $sp['cta_description'],
                    $sortOrder,
                ]);
                $summary['inserted']++;
                $summary['published_urls'][] = '/city/' . $sp['city_slug'] . '/' . $sp['slug'];
                continue;
            }

            $isSame = (string)$existing['meta_title'] === $sp['meta_title']
                && (string)$existing['meta_description'] === $sp['meta_description']
                && (string)$existing['canonical_url'] === $sp['canonical_url']
                && (string)$existing['breadcrumb_label'] === $sp['breadcrumb_label']
                && (string)$existing['eyebrow'] === $sp['eyebrow']
                && (string)$existing['heading_html'] === $sp['heading_html']
                && (string)$existing['hero_description'] === $sp['hero_description']
                && (string)$existing['introduction'] === $sp['introduction']
                && (string)$existing['content_json'] === $contentJson
                && (string)$existing['proof_json'] === $proofJson
                && (string)$existing['faqs_json'] === $faqsJson
                && (string)$existing['schema_service_name'] === $sp['schema_service_name']
                && (string)$existing['schema_service_type'] === $sp['schema_service_type']
                && (string)$existing['cta_title'] === $sp['cta_title']
                && (string)$existing['cta_description'] === $sp['cta_description']
                && (string)$existing['status'] === 'published'
                && (int)$existing['sort_order'] === $sortOrder;

            if ($isSame) {
                $summary['unchanged']++;
            } else {
                $updateSpoke->execute([
                    $sp['meta_title'],
                    $sp['meta_description'],
                    $sp['canonical_url'],
                    $sp['breadcrumb_label'],
                    $sp['eyebrow'],
                    $sp['heading_html'],
                    $sp['hero_description'],
                    $sp['introduction'],
                    $contentJson,
                    $proofJson,
                    $faqsJson,
                    $sp['schema_service_name'],
                    $sp['schema_service_type'],
                    $sp['cta_title'],
                    $sp['cta_description'],
                    $sortOrder,
                    (int)$existing['id'],
                ]);
                $summary['updated']++;
            }
            $summary['published_urls'][] = '/city/' . $sp['city_slug'] . '/' . $sp['slug'];
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
    $filename = $positional[0] ?? 'spokes_high_demand_2026_11_24.json';

    try {
        echo json_encode(cbd_publish_high_demand_spokes($filename, $dryRun), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
    } catch (Throwable $error) {
        fwrite(STDERR, $error->getMessage() . PHP_EOL);
        exit(1);
    }
}
