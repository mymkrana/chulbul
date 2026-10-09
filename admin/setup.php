<?php
/**
 * Setup — creates DB, tables, default admin user, and imports blog posts
 * Visit once at /admin/setup.php — no authentication required
 */

require_once __DIR__ . '/config.php';

$host = 'localhost';
$user = (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'chulbuldesign.com') !== false)
    ? 'chulbuldesign_admin' : 'root';
$pass = _env('DB_PASS_LIVE');
$db   = 'chulbuldesign_blog';

// ── Guard: once an admin exists, setup can only be re-run by a logged-in admin ──
$__gdb = get_db();
if ($__gdb) {
    try {
        if ((int)$__gdb->query("SELECT COUNT(*) FROM admin_users")->fetchColumn() > 0
            && empty($_SESSION['admin_logged_in'])) {
            http_response_code(403);
            exit('Setup already completed. Log in as admin to re-run setup.');
        }
    } catch (Throwable $e) { /* admin_users table missing → first run, allow */ }
}

$steps  = [];
$errors = [];
$done   = false;

try {
    // Connect without DB first to create it
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $steps[] = ['ok' => true, 'msg' => 'Connected to MySQL server.'];

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $steps[] = ['ok' => true, 'msg' => "Database <strong>$db</strong> created / already exists."];

    // Switch to that DB
    $pdo->exec("USE `$db`");

    // Posts table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `posts` (
            `id`         INT AUTO_INCREMENT PRIMARY KEY,
            `slug`       VARCHAR(255) UNIQUE NOT NULL,
            `title`      VARCHAR(500) NOT NULL,
            `meta_title` VARCHAR(500),
            `meta_desc`  TEXT,
            `category`   VARCHAR(100),
            `date`       DATE,
            `read_time`  VARCHAR(50),
            `excerpt`    TEXT,
            `content`    LONGTEXT,
            `status`     ENUM('published','draft') DEFAULT 'published',
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $steps[] = ['ok' => true, 'msg' => "Table <strong>posts</strong> created / already exists."];

    // Admin users table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `admin_users` (
            `id`         INT AUTO_INCREMENT PRIMARY KEY,
            `username`   VARCHAR(100) UNIQUE NOT NULL,
            `password`   VARCHAR(255) NOT NULL,
            `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
    $steps[] = ['ok' => true, 'msg' => "Table <strong>admin_users</strong> created / already exists."];

    // First-party analytics tables
    $analyticsMigration = dirname(__DIR__) . '/database/migrations/2026-09-08-site-analytics.sql';
    if (is_readable($analyticsMigration)) {
        $pdo->exec(file_get_contents($analyticsMigration));
        $steps[] = ['ok' => true, 'msg' => "First-party <strong>analytics tables</strong> created / already exist."];
    } else {
        $steps[] = ['ok' => false, 'msg' => "Analytics migration file is missing — analytics setup skipped."];
    }

    // Default admin user
    $check = $pdo->prepare("SELECT id FROM admin_users WHERE username = ?");
    $check->execute(['admin']);
    if (!$check->fetch()) {
        $hash = password_hash('Admin@123', PASSWORD_BCRYPT);
        $ins  = $pdo->prepare("INSERT INTO admin_users (username, password) VALUES (?, ?)");
        $ins->execute(['admin', $hash]);
        $steps[] = ['ok' => true, 'msg' => "Default admin user created: <strong>admin</strong> / <strong>Admin@123</strong>"];
    } else {
        $steps[] = ['ok' => true, 'msg' => "Admin user <strong>admin</strong> already exists — skipped."];
    }

    // Import blog posts from blog-data.php
    $blogDataPath = dirname(__DIR__) . '/blog-data.php';
    if (file_exists($blogDataPath)) {
        require_once $blogDataPath;
        $imported = 0;
        $skipped  = 0;

        $stmt = $pdo->prepare("
            INSERT IGNORE INTO posts
                (slug, title, meta_title, meta_desc, category, date, read_time, excerpt, content, status)
            VALUES
                (:slug, :title, :meta_title, :meta_desc, :category, :date, :read_time, :excerpt, :content, 'published')
        ");

        if (!empty($blog_posts) && is_array($blog_posts)) {
            foreach ($blog_posts as $p) {
                $stmt->execute([
                    ':slug'       => $p['slug']       ?? '',
                    ':title'      => $p['title']      ?? '',
                    ':meta_title' => $p['meta_title'] ?? '',
                    ':meta_desc'  => $p['meta_desc']  ?? '',
                    ':category'   => $p['category']   ?? '',
                    ':date'       => $p['date']        ?? date('Y-m-d'),
                    ':read_time'  => $p['read_time']  ?? '',
                    ':excerpt'    => $p['excerpt']    ?? '',
                    ':content'    => $p['content']    ?? '',
                ]);
                if ($stmt->rowCount() > 0) {
                    $imported++;
                } else {
                    $skipped++;
                }
            }
            $steps[] = ['ok' => true, 'msg' => "Blog posts imported: <strong>$imported</strong> new, <strong>$skipped</strong> skipped (already existed)."];
        } else {
            $steps[] = ['ok' => true, 'msg' => "blog-data.php found but contains no posts."];
        }
    } else {
        $steps[] = ['ok' => false, 'msg' => "blog-data.php not found at: <code>$blogDataPath</code> — posts not imported."];
    }

    $done = empty($errors);

} catch (PDOException $e) {
    $errors[] = 'Database error: ' . htmlspecialchars($e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup — Chulbul Design Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-[#1e1e5c] to-[#49499A] flex items-center justify-center p-6">

<div class="w-full max-w-lg bg-white rounded-3xl shadow-2xl overflow-hidden">

    <!-- Header -->
    <div class="bg-[#1e1e5c] px-8 py-6 flex items-center gap-4">
        <img src="<?= cbd_base_path() ?>/assets/images/logo/chulbuldesign.svg" alt="Chulbul Design" class="h-8 brightness-0 invert">
        <div>
            <span class="text-white font-bold text-lg block">Database Setup</span>
            <span class="text-white/50 text-xs">Chulbul Design Admin Panel</span>
        </div>
    </div>

    <div class="p-8 space-y-3">

        <!-- Steps -->
        <?php foreach ($steps as $step): ?>
        <div class="flex items-start gap-3 <?= $step['ok'] ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' ?> rounded-xl px-4 py-3">
            <i class="bi <?= $step['ok'] ? 'bi-check-circle-fill text-green-500' : 'bi-exclamation-triangle-fill text-yellow-500' ?> mt-0.5 text-lg flex-shrink-0"></i>
            <span class="text-sm <?= $step['ok'] ? 'text-green-800' : 'text-yellow-800' ?>"><?= $step['msg'] ?></span>
        </div>
        <?php endforeach; ?>

        <!-- Errors -->
        <?php foreach ($errors as $err): ?>
        <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-xl px-4 py-3">
            <i class="bi bi-x-circle-fill text-red-500 mt-0.5 text-lg flex-shrink-0"></i>
            <span class="text-sm text-red-800"><?= $err ?></span>
        </div>
        <?php endforeach; ?>

        <!-- Done state -->
        <?php if ($done): ?>
        <div class="pt-5 border-t border-gray-100 mt-2">
            <div class="bg-[#49499A]/5 border border-[#49499A]/20 rounded-xl p-4 mb-5">
                <p class="text-sm font-bold text-[#1e1e5c] mb-1">Setup complete!</p>
                <p class="text-xs text-gray-500">Default credentials: <strong>admin</strong> / <strong>Admin@123</strong></p>
                <p class="text-xs text-gray-400 mt-1">Change your password after first login.</p>
            </div>
            <a href="login.php"
               class="inline-flex items-center gap-2 bg-[#EE483D] text-white px-6 py-3 rounded-xl font-bold hover:bg-red-600 transition text-sm shadow-lg shadow-red-200">
                <i class="bi bi-box-arrow-in-right"></i> Go to Admin Login
            </a>
        </div>
        <?php elseif (!empty($errors)): ?>
        <div class="pt-4 border-t border-gray-100">
            <p class="text-sm text-gray-500 mb-3">Fix the error above and refresh to try again.</p>
            <button onclick="location.reload()"
                    class="inline-flex items-center gap-2 bg-gray-100 text-gray-700 px-5 py-2.5 rounded-xl font-semibold hover:bg-gray-200 transition text-sm">
                <i class="bi bi-arrow-clockwise"></i> Retry Setup
            </button>
        </div>
        <?php endif; ?>

    </div>
</div>

</body>
</html>
