<?php
require_once __DIR__ . '/database.php';
/**
 * menu-data.php
 * Fetches mega menu structure from DB: parent categories + subcategories.
 * Cached in $_mega_menu_data to avoid duplicate queries per request.
 */
if (!isset($_mega_menu_data)) {
    $_mega_menu_data = [];

    try {
        $__mpdo = cbd_database();
        if (!$__mpdo) throw new RuntimeException('Database unavailable');

        // Try with hub_slug (after migration), fallback without it if column missing
        try {
            $parents = $__mpdo->query(
                "SELECT id, name, slug, hub_slug, color, hover_bg FROM categories
                 WHERE parent_id IS NULL AND status=1 AND deleted_at IS NULL
                 ORDER BY sort_order ASC, name ASC"
            )->fetchAll();
        } catch (PDOException $__col_err) {
            // hub_slug column not yet added — fetch without it
            $parents = $__mpdo->query(
                "SELECT id, name, slug, NULL AS hub_slug, color, hover_bg FROM categories
                 WHERE parent_id IS NULL AND status=1 AND deleted_at IS NULL
                 ORDER BY sort_order ASC, name ASC"
            )->fetchAll();
        }

        foreach ($parents as $_mcat_row) {
            $subs = $__mpdo->prepare(
                "SELECT c.name, c.slug FROM categories c
                 INNER JOIN services s
                   ON CONVERT(s.slug USING utf8mb4) COLLATE utf8mb4_unicode_ci
                    = CONVERT(c.slug USING utf8mb4) COLLATE utf8mb4_unicode_ci
                   AND s.status = 1 AND s.deleted_at IS NULL
                 WHERE c.parent_id=? AND c.status=1 AND c.deleted_at IS NULL
                 ORDER BY c.sort_order ASC, c.name ASC"
            );
            $subs->execute([$_mcat_row['id']]);
            $_mcat_row['children'] = $subs->fetchAll();
            $_mega_menu_data[] = $_mcat_row;
        }
    } catch (Throwable $e) {
        $_mega_menu_data = [];
    }
}
