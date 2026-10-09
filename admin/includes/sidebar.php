<?php
/**
 * Reusable Admin Sidebar
 * Set $active_page before including: 'dashboard', 'posts', 'add-post'
 */
$active_page = $active_page ?? '';
$public_base = cbd_base_path();
$admin_base = $public_base . '/admin';

$nav_items = [
    ['page' => 'dashboard',     'href' => 'index.php',          'icon' => 'bi-speedometer2',        'label' => 'Dashboard'],
    ['page' => 'analytics',      'href' => 'analytics.php',       'icon' => 'bi-graph-up-arrow',      'label' => 'Site Analytics'],
    ['page' => 'leads', 'href' => 'leads.php', 'icon' => 'bi-inbox', 'label' => 'Leads'],
    ['page' => 'posts',         'href' => 'posts.php',          'icon' => 'bi-file-earmark-text',   'label' => 'All Posts'],
    ['page' => 'add-post',        'href' => 'add-post.php',        'icon' => 'bi-plus-circle',    'label' => 'Add Post'],
    ['page' => 'blog-tags',      'href' => 'blog-tags.php',       'icon' => 'bi-tags-fill',      'label' => 'Blog Tags'],
    ['page' => 'auto-tag-posts',    'href' => 'auto-tag-posts.php',     'icon' => 'bi-magic',          'label' => 'Auto Tag Posts'],
    ['page' => 'site-audit',        'href' => 'site-audit.php',        'icon' => 'bi-shield-check',    'label' => 'Site Audit'],
    ['page' => 'audit-log',         'href' => 'audit-log.php',         'icon' => 'bi-shield-lock-fill', 'label' => 'Audit Log'],
    ['page' => 'locations',         'href' => 'locations.php',         'icon' => 'bi-geo-alt-fill',      'label' => 'Countries &amp; Cities'],
    ['page' => 'divider', 'href' => '', 'icon' => '', 'label' => ''],
    ['page' => 'services',      'href' => 'services.php',       'icon' => 'bi-grid-1x2-fill',       'label' => 'All Services'],
    ['page' => 'service-add',   'href' => 'service-edit.php',   'icon' => 'bi-plus-square-fill',    'label' => 'Add Service'],
    ['page' => 'quick-import',  'href' => 'quick-import.php',  'icon' => 'bi-stars',               'label' => 'AI Quick Import'],
    ['page' => 'categories',    'href' => 'categories.php',     'icon' => 'bi-diagram-3-fill',      'label' => 'Categories'],
];

// Trash count badge
$_trash_count = 0;
if (function_exists('get_db')) {
    $_pdo_sb = get_db();
    if ($_pdo_sb) {
        try {
            $s = (int)$_pdo_sb->query("SELECT COUNT(*) FROM services  WHERE deleted_at IS NOT NULL")->fetchColumn();
            $p = (int)$_pdo_sb->query("SELECT COUNT(*) FROM posts     WHERE deleted_at IS NOT NULL")->fetchColumn();
            $c = (int)$_pdo_sb->query("SELECT COUNT(*) FROM categories WHERE deleted_at IS NOT NULL")->fetchColumn();
            $_trash_count = $s + $p + $c;
        } catch (Exception $e) {}
    }
}
?>

<!-- Mobile overlay -->
<div id="sidebar-overlay" onclick="closeSidebar()"
     class="fixed inset-0 bg-black/50 z-20 lg:hidden hidden"></div>

<!-- Sidebar -->
<aside id="sidebar"
       class="fixed top-0 left-0 h-full w-60 bg-[#1e1e5c] flex flex-col z-30
              transform -translate-x-full lg:translate-x-0 transition-transform duration-300">

    <!-- Logo -->
    <div class="px-6 py-5 border-b border-white/10 flex items-center gap-3">
        <img src="<?= $public_base ?>/assets/images/logo/chulbuldesign.svg" alt="Chulbul Design" class="h-8 brightness-0 invert">
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-6 px-3 space-y-1 overflow-y-auto">

        <?php foreach ($nav_items as $item): ?>
        <?php if ($item['page'] === 'divider'): ?>
        <div class="border-t border-white/10 my-2 pt-1"><p class="text-white/30 text-xs px-4 font-semibold tracking-widest uppercase">Services</p></div>
        <?php continue; endif; ?>
        <?php $is_active = ($active_page === $item['page']); ?>
        <a href="<?= $admin_base ?>/<?= $item['href'] ?>"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                  <?= $is_active
                    ? 'bg-[#49499A] text-white shadow-lg shadow-[#49499A]/30'
                    : 'text-white/70 hover:bg-white/10 hover:text-white' ?>">
            <i class="bi <?= $item['icon'] ?> text-base <?= $is_active ? 'text-white' : 'text-white/60' ?>"></i>
            <?= $item['label'] ?>
        </a>
        <?php endforeach; ?>

        <!-- Divider -->
        <div class="border-t border-white/10 my-3"></div>

        <a href="<?= $public_base ?>/blog" target="_blank"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-white/60 hover:bg-white/10 hover:text-white transition">
            <i class="bi bi-journal-richtext text-base text-white/40"></i>
            View Blog
            <i class="bi bi-box-arrow-up-right text-xs ml-auto text-white/30"></i>
        </a>

        <a href="<?= $public_base ?>/" target="_blank"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium text-white/60 hover:bg-white/10 hover:text-white transition">
            <i class="bi bi-globe text-base text-white/40"></i>
            View Site
            <i class="bi bi-box-arrow-up-right text-xs ml-auto text-white/30"></i>
        </a>

        <a href="<?= $admin_base ?>/trash.php"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-medium transition
                  <?= ($active_page === 'trash') ? 'bg-[#49499A] text-white' : 'text-white/60 hover:bg-white/10 hover:text-white' ?>">
            <i class="bi bi-trash3 text-base <?= ($active_page === 'trash') ? 'text-white' : 'text-white/40' ?>"></i>
            Trash
            <?php if ($_trash_count > 0): ?>
            <span class="ml-auto bg-red-500 text-white text-xs font-bold px-1.5 py-0.5 rounded-full min-w-[1.25rem] text-center">
                <?= $_trash_count ?>
            </span>
            <?php endif; ?>
        </a>

    </nav>

    <!-- User + Logout -->
    <div class="px-4 py-4 border-t border-white/10">
        <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-full bg-[#EE483D] flex items-center justify-center flex-shrink-0">
                <i class="bi bi-person-fill text-white text-sm"></i>
            </div>
            <div class="min-w-0">
                <p class="text-white text-sm font-semibold truncate">
                    <?= htmlspecialchars($_SESSION['admin_user'] ?? 'Admin') ?>
                </p>
                <p class="text-white/40 text-xs">Administrator</p>
            </div>
        </div>
        <a href="<?= $admin_base ?>/change-password.php"
           class="flex items-center gap-2 w-full px-3 py-2 rounded-xl text-sm text-white/60 hover:bg-white/10 hover:text-white transition">
            <i class="bi bi-shield-lock text-sm"></i> Change Password
        </a>
        <a href="<?= $admin_base ?>/logout.php"
           class="flex items-center gap-2 w-full px-3 py-2 rounded-xl text-sm text-white/60 hover:bg-white/10 hover:text-white transition">
            <i class="bi bi-box-arrow-right text-sm"></i> Logout
        </a>
    </div>

</aside>

<script>
function openSidebar() {
    document.getElementById('sidebar').classList.remove('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.remove('hidden');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.add('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.add('hidden');
}
</script>
