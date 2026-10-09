<?php
require_once __DIR__ . '/includes/database.php';

http_response_code(404);
header('X-Robots-Tag: noindex, nofollow, noarchive');

$base = cbd_base_path();
$page_robots = 'noindex, nofollow';
$requestedUrl = htmlspecialchars((string)($_SERVER['REQUEST_URI'] ?? ''), ENT_QUOTES, 'UTF-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>404 — Page Not Found | Chulbul Design</title>
<meta name="description" content="The requested page could not be found on Chulbul Design.">
<style>
@keyframes cbd404Float{0%,100%{transform:translateY(0)}50%{transform:translateY(-18px)}}
.cbd-404-float{animation:cbd404Float 3.5s ease-in-out infinite}
.cbd-404-card{transition:transform .25s ease,box-shadow .25s ease}
.cbd-404-card:hover{transform:translateY(-4px);box-shadow:0 12px 30px rgba(73,73,154,.12)}
</style>
<?php require __DIR__ . '/includes/header.php'; ?>

<section class="bg-[#f8f8ff] px-4 py-16 sm:py-24">
  <div class="max-w-3xl mx-auto text-center">
    <div class="cbd-404-float inline-block mb-8 select-none" aria-hidden="true">
      <div class="relative inline-flex items-center justify-center">
        <span class="text-[120px] sm:text-[160px] font-black leading-none tracking-tight"
              style="background:linear-gradient(135deg,#49499A,#EE483D);-webkit-background-clip:text;-webkit-text-fill-color:transparent">404</span>
        <span class="absolute -top-4 -right-4 bg-[#EE483D] text-white rounded-2xl p-3 shadow-lg rotate-12">
          <i class="bi bi-compass text-2xl"></i>
        </span>
      </div>
    </div>

    <h1 class="text-3xl sm:text-4xl font-extrabold text-[#1e1e5c] mb-4 leading-tight">
      Oops! This page went <span class="text-[#EE483D]">off the grid.</span>
    </h1>
    <p class="text-gray-500 text-lg mb-5 max-w-xl mx-auto leading-relaxed">
      The page you are looking for does not exist, was moved, or the address was typed incorrectly.
    </p>

    <?php if ($requestedUrl !== '' && !in_array($requestedUrl, ['/404', '/404.php'], true)): ?>
    <p class="inline-flex items-center gap-2 bg-red-50 border border-red-100 text-red-500 text-sm font-mono px-4 py-2 rounded-full mb-8 max-w-full">
      <i class="bi bi-slash-circle" aria-hidden="true"></i>
      <span class="truncate"><?= $requestedUrl ?></span>
    </p>
    <?php endif; ?>

    <div class="flex flex-wrap gap-3 justify-center mb-14">
      <a href="<?= $base ?>/" class="inline-flex items-center gap-2 bg-[#EE483D] hover:bg-red-600 text-white font-bold px-7 py-3.5 rounded-xl transition shadow-lg">
        <i class="bi bi-house-fill" aria-hidden="true"></i> Back to Home
      </a>
      <a href="<?= $base ?>/contact-us" class="inline-flex items-center gap-2 bg-white border-2 border-[#49499A] text-[#49499A] hover:bg-[#49499A] hover:text-white font-bold px-7 py-3.5 rounded-xl transition">
        <i class="bi bi-chat-dots-fill" aria-hidden="true"></i> Contact Us
      </a>
      <button type="button" onclick="history.back()" class="inline-flex items-center gap-2 bg-white border border-gray-200 text-gray-500 hover:text-gray-800 hover:border-gray-400 font-semibold px-6 py-3.5 rounded-xl transition">
        <i class="bi bi-arrow-left" aria-hidden="true"></i> Go Back
      </button>
    </div>

    <div class="text-left max-w-2xl mx-auto">
      <p class="text-xs font-bold tracking-widest uppercase text-gray-400 text-center mb-5">Popular pages</p>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <?php
        $popularPages = [
            ['bi-globe2', 'Web Design', '/web-design', 'Custom websites that convert'],
            ['bi-cart4', 'E-Commerce Design', '/ecommerce-website-design', 'Online stores built for growth'],
            ['bi-phone', 'App Development', '/android-app-development', 'Mobile app development'],
            ['bi-graph-up-arrow', 'SEO Services', '/local-seo-services', 'Improve visibility on Google'],
            ['bi-vector-pen', 'UI/UX Design', '/ui-ux-branding', 'Clear and engaging experiences'],
            ['bi-wordpress', 'WordPress Design', '/cms-development', 'Fast, manageable websites'],
            ['bi-rss', 'Blog', '/blog', 'Guides, tips and insights'],
            ['bi-telephone-fill', 'Contact Us', '/contact-us', 'Talk to our team'],
        ];
        foreach ($popularPages as [$icon, $label, $url, $description]):
        ?>
        <a href="<?= $base . $url ?>" class="cbd-404-card flex items-center gap-4 bg-white border border-gray-100 rounded-2xl px-5 py-4 shadow-sm">
          <span class="w-10 h-10 bg-indigo-50 rounded-xl flex items-center justify-center shrink-0">
            <i class="bi <?= htmlspecialchars($icon, ENT_QUOTES, 'UTF-8') ?> text-[#49499A] text-lg" aria-hidden="true"></i>
          </span>
          <span class="min-w-0">
            <strong class="block text-gray-800 text-sm leading-tight"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></strong>
            <span class="block text-gray-400 text-xs mt-0.5 truncate"><?= htmlspecialchars($description, ENT_QUOTES, 'UTF-8') ?></span>
          </span>
          <i class="bi bi-arrow-right text-gray-300 ml-auto shrink-0" aria-hidden="true"></i>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
