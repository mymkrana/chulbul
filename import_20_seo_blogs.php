<?php
/**
 * Import/Update 20 Exhaustive SEO Blogs (2026 Edition)
 * Idempotent: Updates existing posts by slug or inserts if missing
 */
require_once __DIR__ . '/includes/database.php';
$pdo = cbd_database();

$posts = json_decode(file_get_contents(__DIR__ . '/scripts/blogs_compiled_2026.json'), true);

if (!$posts) {
    die("Error: Could not load blogs_compiled_2026.json\n");
}

echo "Starting import of " . count($posts) . " blogs...\n";

$checkStmt  = $pdo->prepare("SELECT id FROM posts WHERE slug = ?");
$updateStmt = $pdo->prepare("UPDATE posts SET title=?, meta_title=?, meta_desc=?, category=?, blog_category_id=?, date=?, read_time=?, excerpt=?, content=?, status='published', updated_at=NOW() WHERE slug=?");
$insertStmt = $pdo->prepare("INSERT INTO posts (slug, title, meta_title, meta_desc, category, blog_category_id, date, read_time, excerpt, content, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'published', NOW(), NOW())");

$tagStmt       = $pdo->prepare("SELECT id FROM tags WHERE slug = ?");
$insertTagStmt = $pdo->prepare("INSERT INTO tags (name, slug) VALUES (?, ?)");
$clearTagsStmt = $pdo->prepare("DELETE FROM post_tags WHERE post_id = ?");
$addTagStmt    = $pdo->prepare("INSERT IGNORE INTO post_tags (post_id, tag_id) VALUES (?, ?)");

$updated = 0;
$inserted = 0;

foreach ($posts as $idx => $p) {
    $checkStmt->execute([$p['slug']]);
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        $updateStmt->execute([
            $p['title'],
            $p['meta_title'],
            $p['meta_desc'],
            $p['category'],
            $p['blog_category_id'],
            $p['date'],
            $p['read_time'],
            $p['excerpt'],
            $p['content'],
            $p['slug']
        ]);
        $postId = $existing['id'];
        $updated++;
        echo sprintf("[%02d/20] Updated: #%d %s (Length: %d chars)\n", $idx + 1, $postId, $p['slug'], strlen($p['content']));
    } else {
        $insertStmt->execute([
            $p['slug'],
            $p['title'],
            $p['meta_title'],
            $p['meta_desc'],
            $p['category'],
            $p['blog_category_id'],
            $p['date'],
            $p['read_time'],
            $p['excerpt'],
            $p['content']
        ]);
        $postId = $pdo->lastInsertId();
        $inserted++;
        echo sprintf("[%02d/20] Inserted: #%d %s (Length: %d chars)\n", $idx + 1, $postId, $p['slug'], strlen($p['content']));
    }
    
    // Sync Tags
    if (!empty($p['tags']) && $postId) {
        $clearTagsStmt->execute([$postId]);
        foreach ($p['tags'] as $tagSlug) {
            $tagStmt->execute([$tagSlug]);
            $tagRow = $tagStmt->fetch(PDO::FETCH_ASSOC);
            if ($tagRow) {
                $tagId = $tagRow['id'];
            } else {
                $tagName = ucwords(str_replace('-', ' ', $tagSlug));
                $insertTagStmt->execute([$tagName, $tagSlug]);
                $tagId = $pdo->lastInsertId();
            }
            if ($tagId) {
                $addTagStmt->execute([$postId, $tagId]);
            }
        }
    }
}

echo "\n=== Summary ===\n";
echo "Total processed: " . count($posts) . "\n";
echo "Updated: $updated\n";
echo "Inserted: $inserted\n";
echo "Done!\n";
