<?php
declare(strict_types=1);

// Only CLI or the authenticated, short-lived deployment runner may invoke this.
if (PHP_SAPI !== 'cli' && !defined('CBD_DEPLOY_BLOG_AUTHORIZED')) {
    http_response_code(404);
    exit;
}
require_once dirname(__DIR__) . '/includes/database.php';

function cbd_publish_blog_bundle(string $filename): array
{
    if (!preg_match('/^blogs_[a-z0-9_]+\.json$/', $filename)) {
        throw new RuntimeException('Invalid editorial bundle name.');
    }
    $posts = json_decode((string)file_get_contents(__DIR__ . '/' . $filename), true, 512, JSON_THROW_ON_ERROR);
    if (!is_array($posts) || count($posts) !== 4) {
        throw new RuntimeException('Expected exactly four reviewed articles.');
    }
    $slugs = [];
    foreach ($posts as $post) {
        foreach (['slug', 'title', 'meta_title', 'meta_desc', 'category', 'date', 'read_time', 'excerpt', 'content'] as $field) {
            if (!is_string($post[$field] ?? null) || trim($post[$field]) === '') {
                throw new RuntimeException('Missing required article field: ' . $field);
            }
        }
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $post['slug'])
            || in_array($post['slug'], $slugs, true)
            || preg_match('/<\s*(script|iframe|form)\b|\bon[a-z]+\s*=|javascript:/i', $post['content'])) {
            throw new RuntimeException('Invalid or unsafe editorial content.');
        }
        $slugs[] = $post['slug'];
    }
    $pdo = cbd_database();
    if (!$pdo) throw new RuntimeException('Database unavailable.');

    $inserted = 0;
    $unchanged = 0;
    $pdo->beginTransaction();
    try {
        $check = $pdo->prepare('SELECT id, content, status, deleted_at FROM posts WHERE slug = ? FOR UPDATE');
        $category = $pdo->prepare('SELECT id FROM blog_categories WHERE name = ? LIMIT 1');
        $insert = $pdo->prepare("INSERT INTO posts (slug, title, meta_title, meta_desc, category, blog_category_id, date, read_time, excerpt, content, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'published')");
        $findTag = $pdo->prepare('SELECT id FROM tags WHERE slug = ?');
        $newTag = $pdo->prepare('INSERT INTO tags (name, slug) VALUES (?, ?)');
        $linkTag = $pdo->prepare('INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)');
        foreach ($posts as $post) {
            $check->execute([$post['slug']]);
            $existing = $check->fetch();
            if ($existing) {
                if ($existing['content'] !== $post['content'] || $existing['status'] !== 'published' || $existing['deleted_at'] !== null) {
                    throw new RuntimeException('Existing article conflict: ' . $post['slug']);
                }
                $unchanged++;
                continue;
            }
            $category->execute([$post['category']]);
            $categoryId = $category->fetchColumn();
            if (!$categoryId) throw new RuntimeException('Blog category missing: ' . $post['category']);
            $insert->execute([$post['slug'], $post['title'], $post['meta_title'], $post['meta_desc'], $post['category'], (int)$categoryId, $post['date'], $post['read_time'], $post['excerpt'], $post['content']]);
            $postId = (int)$pdo->lastInsertId();
            foreach ($post['tags'] ?? [] as $tagSlug) {
                if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $tagSlug)) throw new RuntimeException('Invalid tag.');
                $findTag->execute([$tagSlug]);
                $tagId = $findTag->fetchColumn();
                if (!$tagId) {
                    $newTag->execute([ucwords(str_replace('-', ' ', $tagSlug)), $tagSlug]);
                    $tagId = $pdo->lastInsertId();
                }
                $linkTag->execute([$postId, $tagId]);
            }
            $inserted++;
        }
        $pdo->commit();
    } catch (Throwable $error) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        throw $error;
    }
    return ['success' => true, 'inserted' => $inserted, 'unchanged' => $unchanged, 'slugs' => $slugs];
}

if (PHP_SAPI === 'cli' && realpath((string)($_SERVER['SCRIPT_FILENAME'] ?? '')) === __FILE__) {
    try {
        echo json_encode(cbd_publish_blog_bundle($argv[1] ?? ''), JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR) . PHP_EOL;
    } catch (Throwable $error) {
        fwrite(STDERR, $error->getMessage() . PHP_EOL);
        exit(1);
    }
}
