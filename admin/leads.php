<?php
require_once __DIR__ . '/config.php';
require_login();

$active_page = 'leads';
$pdo = get_db();
$page = max(1, min(100000, (int)($_GET['page'] ?? 1)));
$perPage = 50;
$count = 0;
$leads = [];
$error = '';

if (!$pdo) {
    $error = 'Database connection is unavailable. Enquiries cannot be displayed right now.';
} else {
    try {
        $count = (int)$pdo->query('SELECT COUNT(*) FROM leads')->fetchColumn();
        $page = min($page, max(1, (int)ceil($count / $perPage)));
        $offset = ($page - 1) * $perPage;
        $statement = $pdo->prepare('SELECT id, name, phone, business, message, source, created_at FROM leads ORDER BY created_at DESC, id DESC LIMIT :limit OFFSET :offset');
        $statement->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $statement->bindValue(':offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $leads = $statement->fetchAll();
    } catch (Throwable $exception) {
        error_log('Admin leads list unavailable: ' . $exception->getMessage());
        $error = 'Enquiries could not be loaded. Please check the database connection.';
    }
}

function lead_html($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Leads — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-50 text-gray-800">
<?php include __DIR__ . '/includes/sidebar.php'; ?>
<div class="lg:ml-60 min-h-screen">
  <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
    <button onclick="openSidebar()" class="lg:hidden text-gray-500 hover:text-gray-800"><i class="bi bi-list text-2xl"></i></button>
    <div>
      <h1 class="text-xl font-extrabold text-[#1e1e5c]">Enquiries</h1>
      <p class="text-sm text-gray-500">Contact, city, industry and other website forms</p>
    </div>
    <span class="ml-auto bg-[#49499A]/10 text-[#49499A] text-sm font-bold px-3 py-1.5 rounded-lg"><?= $count ?> total</span>
  </header>
  <main class="max-w-6xl mx-auto p-6">
    <?php if ($error): ?>
      <div role="alert" class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4"><?= lead_html($error) ?></div>
    <?php elseif (!$leads): ?>
      <div class="bg-white border border-gray-200 rounded-xl p-8 text-gray-500">No enquiries have been saved yet.</div>
    <?php else: ?>
      <div class="space-y-4">
        <?php foreach ($leads as $lead):
          $phone = trim((string)$lead['phone']);
          $email = '';
          if (preg_match('/^Email:\s*([^\s\r\n]+)/mi', (string)$lead['message'], $matches) && filter_var($matches[1], FILTER_VALIDATE_EMAIL)) {
              $email = $matches[1];
          }
        ?>
        <article class="bg-white border border-gray-200 rounded-xl p-5 shadow-sm">
          <div class="flex flex-wrap justify-between gap-3">
            <div>
              <h2 class="font-bold text-[#1e1e5c] text-lg"><?= lead_html($lead['name']) ?></h2>
              <p class="text-sm text-gray-500"><?= lead_html($lead['business'] ?: 'Business not provided') ?></p>
            </div>
            <div class="text-sm text-gray-500 sm:text-right">
              <span class="inline-block bg-[#49499A]/10 text-[#49499A] rounded-full px-2.5 py-1 font-semibold"><?= lead_html($lead['source'] ?: 'Website form') ?></span>
              <div class="mt-1"><?= lead_html(date('d M Y, h:i A', strtotime((string)$lead['created_at']))) ?></div>
            </div>
          </div>
          <div class="flex flex-wrap gap-x-5 gap-y-2 mt-4 text-sm">
            <?php if ($phone !== ''): ?><a class="text-[#49499A] hover:underline" href="tel:<?= lead_html($phone) ?>"><i class="bi bi-telephone"></i> <?= lead_html($phone) ?></a><?php endif; ?>
            <?php if ($email !== ''): ?><a class="text-[#49499A] hover:underline" href="mailto:<?= lead_html($email) ?>"><i class="bi bi-envelope"></i> <?= lead_html($email) ?></a><?php endif; ?>
          </div>
          <?php if (trim((string)$lead['message']) !== ''): ?>
          <div class="mt-4 bg-gray-50 rounded-lg p-4 text-sm text-gray-700 whitespace-pre-wrap break-words"><?= lead_html($lead['message']) ?></div>
          <?php endif; ?>
        </article>
        <?php endforeach; ?>
      </div>
      <?php if ($count > $perPage): ?>
      <nav class="mt-6 flex gap-3 items-center" aria-label="Enquiry pages">
        <?php if ($page > 1): ?><a class="px-4 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-100" href="?page=<?= $page - 1 ?>">Previous</a><?php endif; ?>
        <span class="text-sm text-gray-500">Page <?= $page ?> of <?= (int)ceil($count / $perPage) ?></span>
        <?php if ($page * $perPage < $count): ?><a class="px-4 py-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-100" href="?page=<?= $page + 1 ?>">Next</a><?php endif; ?>
      </nav>
      <?php endif; ?>
    <?php endif; ?>
  </main>
</div>
<script src="assets/admin.js"></script>
</body>
</html>
