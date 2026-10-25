<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli' && !defined('CBD_DEPLOY_BLOG_AUTHORIZED')) {
    http_response_code(404);
    exit;
}
require_once dirname(__DIR__) . '/includes/database.php';

function cbd_publish_metadata(string $filename, bool $dryRun = false, bool $allowMissingLocal = false): array
{
    if ($filename !== 'seo_metadata_2026_10_25.json' || ($allowMissingLocal && PHP_SAPI !== 'cli')) {
        throw new RuntimeException('Invalid metadata scope.');
    }
    $changes = json_decode((string)file_get_contents(__DIR__ . '/' . $filename), true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($changes) || count($changes) !== 167) throw new RuntimeException('Unexpected metadata scope.');
    $config = [
        'city' => ['cities', 'meta_description', 'slug', "status = 'published'"],
        'spoke' => ['city_spokes', 'meta_description', 'canonical_url', "status = 'published'"],
        'post' => ['posts', 'meta_desc', 'slug', "status = 'published' AND deleted_at IS NULL"],
        'service' => ['services', 'meta_desc', 'slug', 'status = 1 AND deleted_at IS NULL'],
    ];
    $normalise = static fn(string $value): string => trim((string)preg_replace('/\s+/u', ' ', html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8')));
    $seen = [];
    foreach ($changes as $change) {
        $path = $change['path'] ?? '';
        if (!isset($config[$change['kind'] ?? '']) || !is_string($path)
            || !preg_match('~^/[a-z0-9-]+(?:/[a-z0-9-]+){0,2}$~', $path) || isset($seen[$path])
            || !is_array($change['before'] ?? null) || !is_array($change['after'] ?? null)
            || array_keys($change['before']) !== array_keys($change['after']) || !$change['after']) {
            throw new RuntimeException('Invalid metadata record.');
        }
        $expectedKind = str_starts_with($path, '/city/') ? (substr_count($path, '/') === 2 ? 'city' : 'spoke') : (str_starts_with($path, '/blog/') ? 'post' : 'service');
        $expectedKey = $expectedKind === 'spoke' ? $path : basename($path);
        if ($change['kind'] !== $expectedKind || ($change['key'] ?? '') !== $expectedKey) throw new RuntimeException('Metadata route mismatch.');
        foreach ($change['after'] as $field => $value) {
            if (!in_array($field, ['meta_title', 'meta_description'], true) || !is_string($value)
                || !is_string($change['before'][$field]) || trim($value) === '' || strip_tags($value) !== $value
                || mb_strlen($value, 'UTF-8') > ($field === 'meta_title' ? 60 : 160)) {
                throw new RuntimeException('Invalid metadata text: ' . $path);
            }
        }
        $seen[$path] = true;
    }
    $pdo = cbd_database();
    if (!$pdo) throw new RuntimeException('Database unavailable.');
    foreach (array_unique(array_column($config, 0)) as $table) {
        $checkEngine = $pdo->prepare('SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
        $checkEngine->execute([$table]);
        if (strtoupper((string)$checkEngine->fetchColumn()) !== 'INNODB') throw new RuntimeException('Transactional table required.');
    }
    $updated = 0; $unchanged = 0; $missing = [];
    $pdo->beginTransaction();
    try {
        foreach ($changes as $change) {
            [$table, $descriptionColumn, $keyColumn, $status] = $config[$change['kind']];
            $key = $change['kind'] === 'spoke' ? 'https://www.chulbuldesign.com' . $change['path'] : $change['key'];
            $select = $pdo->prepare("SELECT id, meta_title, $descriptionColumn AS meta_description FROM $table WHERE $keyColumn = ? AND $status FOR UPDATE");
            $select->execute([$key]);
            $row = $select->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                if ($allowMissingLocal) { $missing[] = $change['path']; continue; }
                throw new RuntimeException('Metadata record missing: ' . $change['path']);
            }
            $sets = []; $values = [];
            foreach ($change['after'] as $field => $value) {
                $current = $normalise((string)$row[$field]);
                if ($current === $normalise($value)) continue;
                if ($current !== $normalise($change['before'][$field])) throw new RuntimeException('Metadata baseline conflict: ' . $change['path']);
                $column = $field === 'meta_title' ? 'meta_title' : $descriptionColumn;
                $sets[] = "$column = ?"; $values[] = $value;
            }
            if (!$sets) { $unchanged++; continue; }
            $values[] = $row['id'];
            $pdo->prepare("UPDATE $table SET " . implode(', ', $sets) . ' WHERE id = ?')->execute($values);
            $updated++;
        }
        if ($dryRun) $pdo->rollBack(); else $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $error;
    }
    return ['success' => true, 'updated' => $updated, 'unchanged' => $unchanged, 'missing_local' => $missing, 'dry_run' => $dryRun, 'paths' => array_keys($seen)];
}

if (PHP_SAPI === 'cli' && realpath((string)($_SERVER['SCRIPT_FILENAME'] ?? '')) === __FILE__) {
    try {
        echo json_encode(cbd_publish_metadata($argv[1] ?? '', in_array('--dry-run', $argv, true), in_array('--allow-missing-local', $argv, true)), JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
    } catch (Throwable $error) {
        fwrite(STDERR, $error->getMessage() . PHP_EOL); exit(1);
    }
}
