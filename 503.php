<?php
require_once __DIR__ . '/includes/database.php';

http_response_code(503);
header('Retry-After: 300');
header('X-Robots-Tag: noindex, nofollow, noarchive');

$base = cbd_base_path();
$page_robots = 'noindex, nofollow';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>Service Temporarily Unavailable | Chulbul Design</title>
<meta name="description" content="This page is temporarily unavailable. Please try again shortly.">
<?php require __DIR__ . '/includes/header.php'; ?>

<section class="bg-[#f8f8ff] px-4 py-24 sm:py-32 text-center">
  <div class="max-w-lg mx-auto">
    <span class="inline-flex w-20 h-20 items-center justify-center rounded-3xl bg-indigo-50 text-[#49499A] mb-7" aria-hidden="true">
      <i class="bi bi-cloud-arrow-up text-4xl"></i>
    </span>
    <p class="text-xs font-bold tracking-widest uppercase text-[#EE483D] mb-3">Temporary interruption</p>
    <h1 class="text-3xl sm:text-4xl font-extrabold text-[#1e1e5c] mb-4">We’ll be right back</h1>
    <p class="text-gray-500 text-lg leading-relaxed mb-8">
      This page cannot load its content right now. Please try again in a few minutes.
    </p>
    <div class="flex flex-wrap justify-center gap-3">
      <button type="button" onclick="location.reload()" class="inline-flex items-center gap-2 bg-[#EE483D] hover:bg-red-600 text-white font-bold px-7 py-3.5 rounded-xl transition">
        <i class="bi bi-arrow-clockwise" aria-hidden="true"></i> Try Again
      </button>
      <a href="<?= $base ?>/" class="inline-flex items-center gap-2 bg-white border-2 border-[#49499A] text-[#49499A] hover:bg-[#49499A] hover:text-white font-bold px-7 py-3.5 rounded-xl transition">
        <i class="bi bi-house-fill" aria-hidden="true"></i> Back to Home
      </a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
