<?php
require_once __DIR__ . '/database.php';

function cbd_published_blog_posts(): array
{
    $pdo = cbd_database();
    if (!$pdo) return [];

    try {
        $posts = $pdo->query(
            "SELECT id, slug, title, meta_title, meta_desc, category, date, read_time,
                    excerpt, content, status, created_at, updated_at, COALESCE(views, 0) AS views
             FROM posts
             WHERE status = 'published' AND deleted_at IS NULL
             ORDER BY date DESC, created_at DESC, id DESC"
        )->fetchAll();
    } catch (Throwable $error) {
        // Compatibility with a posts table that predates the views column.
        try {
            $posts = $pdo->query(
                "SELECT id, slug, title, meta_title, meta_desc, category, date, read_time,
                        excerpt, content, status, created_at, created_at AS updated_at, 0 AS views
                 FROM posts WHERE status = 'published' AND deleted_at IS NULL
                 ORDER BY date DESC, created_at DESC, id DESC"
            )->fetchAll();
        } catch (Throwable $fallbackError) {
            // Final fallback for the original schema without views/deleted_at.
            try {
                $posts = $pdo->query(
                    "SELECT id, slug, title, meta_title, meta_desc, category, date, read_time,
                            excerpt, content, status, created_at, created_at AS updated_at, 0 AS views
                     FROM posts WHERE status = 'published'
                     ORDER BY date DESC, created_at DESC, id DESC"
                )->fetchAll();
            } catch (Throwable $legacyError) {
                error_log('Blog repository unavailable: ' . $legacyError->getMessage());
                return [];
            }
        }
    }

    foreach ($posts as &$post) $post['tags'] = [];
    unset($post);
    if (!$posts) return [];

    try {
        $ids = array_map('intval', array_column($posts, 'id'));
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $statement = $pdo->prepare(
            "SELECT pt.post_id, t.id AS tag_id, t.name, t.slug
             FROM post_tags pt
             INNER JOIN tags t ON t.id = pt.tag_id
             WHERE pt.post_id IN ($placeholders)
             ORDER BY t.name"
        );
        $statement->execute($ids);
        $tagMap = [];
        foreach ($statement->fetchAll() as $tag) {
            $tagMap[(int)$tag['post_id']][] = [
                'id' => (int)$tag['tag_id'],
                'name' => $tag['name'],
                'slug' => $tag['slug'],
            ];
        }
        foreach ($posts as &$post) $post['tags'] = $tagMap[(int)$post['id']] ?? [];
        unset($post);
    } catch (Throwable $error) {
        error_log('Blog tags unavailable: ' . $error->getMessage());
    }

    return $posts;
}
