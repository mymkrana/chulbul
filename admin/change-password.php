<?php
require_once __DIR__ . '/config.php';
require_login();

$msg = '';
$msg_type = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $current = $_POST['current_password'] ?? '';
    $new     = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $pdo = get_db();
    if (!$pdo) {
        $msg = 'Database connected nahi hai.';
    } else {
        $stmt = $pdo->prepare("SELECT password FROM admin_users WHERE id = ? LIMIT 1");
        $stmt->execute([$_SESSION['admin_id'] ?? 0]);
        $hash = $stmt->fetchColumn();

        if (!$hash || !password_verify($current, $hash)) {
            $msg = 'Current password galat hai.';
        } elseif (strlen($new) < 8) {
            $msg = 'Naya password kam se kam 8 characters ka hona chahiye.';
        } elseif (preg_match('/^Admin@123$/i', $new)) {
            $msg = 'Default password allowed nahi — kuch unique aur strong rakho.';
        } elseif ($new !== $confirm) {
            $msg = 'Naya password aur confirm match nahi karte.';
        } elseif ($new === $current) {
            $msg = 'Naya password purane se alag hona chahiye.';
        } else {
            $upd = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            $upd->execute([password_hash($new, PASSWORD_BCRYPT), $_SESSION['admin_id']]);
            session_regenerate_id(true);
            audit_log('password_changed', 'admin_users', $_SESSION['admin_id'] ?? null);
            $msg = 'Password badal gaya! Agli baar naye password se login karo.';
            $msg_type = 'success';
        }
    }
}

$active_page = '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password — Chulbul Admin</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
<?php include __DIR__ . '/includes/sidebar.php'; ?>

<div class="lg:pl-60 flex flex-col min-h-screen">
    <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center gap-4 sticky top-0 z-10">
        <button onclick="openSidebar()" class="lg:hidden text-gray-500"><i class="bi bi-list text-2xl"></i></button>
        <i class="bi bi-shield-lock text-xl text-[#EE483D]"></i>
        <h1 class="text-xl font-extrabold text-[#1e1e5c]">Change Password</h1>
    </header>

    <main class="flex-1 p-6 max-w-lg mx-auto w-full">

        <?php if ($msg): ?>
        <div class="mb-5 rounded-xl px-5 py-4 text-sm flex items-center gap-3
            <?= $msg_type === 'success'
                ? 'bg-green-50 border border-green-200 text-green-700'
                : 'bg-red-50 border border-red-200 text-red-700' ?>">
            <i class="bi <?= $msg_type === 'success' ? 'bi-check-circle-fill text-green-500' : 'bi-exclamation-circle-fill text-red-500' ?> flex-shrink-0"></i>
            <?= htmlspecialchars($msg) ?>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-5">
                <i class="bi bi-info-circle mr-1"></i>
                Strong password rakho — kam se kam 8 characters, letters + numbers + symbol.
            </p>

            <form method="POST" action="change-password.php" class="space-y-5" autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

                <div>
                    <label for="current_password"><i class="bi bi-lock mr-1 text-[#49499A]"></i> Current Password</label>
                    <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
                           placeholder="Abhi wala password">
                </div>

                <div>
                    <label for="new_password"><i class="bi bi-key mr-1 text-[#49499A]"></i> New Password</label>
                    <input type="password" id="new_password" name="new_password" required autocomplete="new-password"
                           minlength="8" placeholder="Naya password (min 8 chars)">
                </div>

                <div>
                    <label for="confirm_password"><i class="bi bi-key-fill mr-1 text-[#49499A]"></i> Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password"
                           minlength="8" placeholder="Naya password dobara">
                </div>

                <button type="submit"
                        class="w-full bg-[#EE483D] hover:bg-red-600 text-white font-bold py-3.5 rounded-xl transition flex items-center justify-center gap-2">
                    <i class="bi bi-shield-check"></i> Update Password
                </button>
            </form>
        </div>

        <a href="index.php" class="inline-flex items-center gap-1.5 text-sm text-gray-400 hover:text-gray-600 mt-5">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </main>
</div>
<script src="assets/admin.js"></script>
</body>
</html>
