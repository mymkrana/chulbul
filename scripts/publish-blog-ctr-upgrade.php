<?php
declare(strict_types=1);

// Only CLI or the short-lived authenticated web runner may invoke this updater.
if (PHP_SAPI !== 'cli' && !defined('CBD_DEPLOY_BLOG_AUTHORIZED')) {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/includes/database.php';

/** Return UTF-8 character count cleanly even if mbstring is disabled. */
function cbd_blog_ctr_char_len(string $value): int
{
    return function_exists('mb_strlen') ? mb_strlen($value, 'UTF-8') : strlen($value);
}

/** Load and strictly validate the blog CTR upgrade payload. */
function cbd_load_blog_ctr_payload(string $filename): array
{
    if (!preg_match('/^[a-z0-9_.-]+\.json$/i', $filename) || str_contains($filename, '..')) {
        throw new RuntimeException('Invalid upgrade payload filename.');
    }

    $path = __DIR__ . '/' . $filename;
    if (!is_file($path)) {
        throw new RuntimeException('Upgrade payload not found: ' . $filename);
    }

    $payload = json_decode((string)file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($payload) || !isset($payload['posts']) || !is_array($payload['posts'])) {
        throw new RuntimeException('Payload must contain a posts array.');
    }

    $marker = trim((string)($payload['marker'] ?? '<!-- cbd-ctr-faq-2026-11-24 -->'));
    if ($marker === '') {
        throw new RuntimeException('Payload marker cannot be empty.');
    }

    $seenSlugs = [];
    $seenTitles = [];
    $seenDescs = [];
    foreach ($payload['posts'] as $index => $post) {
        if (!is_array($post)) {
            throw new RuntimeException("Invalid post row at index {$index}.");
        }
        foreach (['slug', 'title', 'meta_title', 'meta_desc'] as $field) {
            if (!is_string($post[$field] ?? null) || trim($post[$field]) === '') {
                throw new RuntimeException("Missing {$field} at index {$index}.");
            }
        }
        $slug = $post['slug'];
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            throw new RuntimeException("Invalid slug format: {$slug}");
        }
        if (isset($seenSlugs[$slug])) {
            throw new RuntimeException("Duplicate slug in payload: {$slug}");
        }
        $seenSlugs[$slug] = true;

        if (cbd_blog_ctr_char_len($post['meta_title']) > 60) {
            throw new RuntimeException("meta_title exceeds 60 chars: {$slug}");
        }
        if (cbd_blog_ctr_char_len($post['meta_desc']) > 158) {
            throw new RuntimeException("meta_desc exceeds 158 chars: {$slug}");
        }
        if (isset($seenTitles[$post['meta_title']])) {
            throw new RuntimeException("Duplicate meta_title in payload: {$slug}");
        }
        if (isset($seenDescs[$post['meta_desc']])) {
            throw new RuntimeException("Duplicate meta_desc in payload: {$slug}");
        }
        $seenTitles[$post['meta_title']] = true;
        $seenDescs[$post['meta_desc']] = true;

        $faqHtml = (string)($post['append_faq_html'] ?? '');
        if ($faqHtml !== '' && preg_match('/<\s*(script|iframe|form)\b|\bon[a-z]+\s*=|javascript:/i', $faqHtml)) {
            throw new RuntimeException("Unsafe HTML detected in append_faq_html: {$slug}");
        }
    }

    return $payload;
}

/** Backup current live rows for target blog slugs before applying updates. */
function cbd_write_blog_ctr_backup(PDO $pdo, array $slugs): ?string
{
    if (!is_writable(__DIR__) || empty($slugs)) {
        return null;
    }

    $placeholders = implode(', ', array_fill(0, count($slugs), '?'));
    $stmt = $pdo->prepare(
        "SELECT id, slug, title, meta_title, meta_desc, content, status, updated_at
         FROM posts
         WHERE slug IN ({$placeholders}) AND deleted_at IS NULL
         ORDER BY id ASC"
    );
    $stmt->execute(array_values($slugs));

    $filename = 'seo_backup_blog_ctr_' . gmdate('Ymd_His') . '.json';
    $encoded = json_encode([
        'created_at_utc' => gmdate('c'),
        'posts' => $stmt->fetchAll(),
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($encoded === false) {
        return null;
    }

    return @file_put_contents(__DIR__ . '/' . $filename, $encoded) !== false ? $filename : null;
}

/** Apply Page 1-2 blog CTR metadata, H1 title, and idempotent FAQ upgrades. */
function cbd_publish_blog_ctr_upgrade(
    string $filename = 'blog_ctr_upgrade_2026_11_24.json',
    bool $dryRun = false
): array {
    $payload = cbd_load_blog_ctr_payload($filename);
    $marker = (string)$payload['marker'];
    $pdo = cbd_database();
    if (!$pdo) {
        throw new RuntimeException('Database unavailable.');
    }

    $engine = (string)$pdo->query(
        "SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'posts' LIMIT 1"
    )->fetchColumn();
    if (strtoupper($engine) !== 'INNODB') {
        throw new RuntimeException('Table posts must exist and use InnoDB before updating.');
    }

    $slugs = array_column($payload['posts'], 'slug');
    $backupFile = $dryRun ? null : cbd_write_blog_ctr_backup($pdo, $slugs);

    $summary = [
        'success' => true,
        'dry_run' => $dryRun,
        'backup_file' => $backupFile,
        'posts_updated' => 0,
        'posts_unchanged' => 0,
        'posts_missing' => 0,
        'faqs_appended' => 0,
        'updated_slugs' => [],
        'missing_slugs' => [],
    ];

    $pdo->beginTransaction();
    try {
        $selectPost = $pdo->prepare(
            "SELECT id, title, meta_title, meta_desc, content
             FROM posts
             WHERE slug = ? AND status = 'published' AND deleted_at IS NULL
             FOR UPDATE"
        );
        $updatePost = $pdo->prepare(
            "UPDATE posts
             SET title = ?, meta_title = ?, meta_desc = ?, content = ?, updated_at = NOW()
             WHERE id = ?"
        );

        foreach ($payload['posts'] as $post) {
            $selectPost->execute([$post['slug']]);
            $existing = $selectPost->fetch();
            if (!$existing) {
                $summary['posts_missing']++;
                $summary['missing_slugs'][] = $post['slug'];
                continue;
            }

            $currentContent = (string)$existing['content'];
            $newContent = $currentContent;
            $faqHtml = trim((string)($post['append_faq_html'] ?? ''));
            $appendedFaq = false;

            if ($faqHtml !== '' && !str_contains($currentContent, $marker)) {
                $newContent = rtrim($currentContent) . "\n" . $faqHtml . "\n";
                $appendedFaq = true;
            }

            $isSame = (string)$existing['title'] === $post['title']
                && (string)$existing['meta_title'] === $post['meta_title']
                && (string)$existing['meta_desc'] === $post['meta_desc']
                && $currentContent === $newContent;

            if ($isSame) {
                $summary['posts_unchanged']++;
                continue;
            }

            $updatePost->execute([
                $post['title'],
                $post['meta_title'],
                $post['meta_desc'],
                $newContent,
                (int)$existing['id'],
            ]);
            $summary['posts_updated']++;
            $summary['updated_slugs'][] = $post['slug'];
            if ($appendedFaq) {
                $summary['faqs_appended']++;
            }
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
    $filename = $positional[0] ?? 'blog_ctr_upgrade_2026_11_24.json';

    try {
        echo json_encode(cbd_publish_blog_ctr_upgrade($filename, $dryRun), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
    } catch (Throwable $error) {
        fwrite(STDERR, $error->getMessage() . PHP_EOL);
        exit(1);
    }
}
