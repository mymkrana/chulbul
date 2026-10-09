<?php
require_once __DIR__ . '/config.php';

// Redirect if already logged in
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error   = '';
$db_fail = false;

// ── Brute-force protection: lock an IP after repeated failed logins ──────────
function login_throttle_file(): string {
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
    return sys_get_temp_dir() . '/cbd_login_' . md5($ip) . '.json';
}
function login_attempts_get(): array {
    $d = @json_decode((string)@file_get_contents(login_throttle_file()), true);
    return is_array($d) ? $d + ['count'=>0,'first'=>0,'lock_until'=>0] : ['count'=>0,'first'=>0,'lock_until'=>0];
}
function login_is_locked(): int {
    return max(0, (int)login_attempts_get()['lock_until'] - time());
}
function login_record_fail(): void {
    $d = login_attempts_get(); $now = time();
    if ($now - (int)$d['first'] > 900) { $d['count'] = 0; $d['first'] = $now; } // 15-min window
    if ((int)$d['first'] === 0) $d['first'] = $now;
    $d['count'] = (int)$d['count'] + 1;
    if ($d['count'] >= 5) $d['lock_until'] = $now + 900;                        // lock 15 min after 5 fails
    @file_put_contents(login_throttle_file(), json_encode($d));
}
function login_clear(): void { @unlink(login_throttle_file()); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $lock_wait = login_is_locked();
    if ($lock_wait > 0) {
        $error = 'Too many failed attempts. Try again in ' . ceil($lock_wait / 60) . ' minute(s).';
    } elseif ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $pdo = get_db();
        if ($pdo === null) {
            $db_fail = true;
            $error   = 'Database is not connected. Check the server database configuration.';
        } else {
            try {
                $stmt = $pdo->prepare("SELECT id, username, password FROM admin_users WHERE username = ? LIMIT 1");
                $stmt->execute([$username]);
                $row = $stmt->fetch();

                if ($row && password_verify($password, $row['password'])) {
                    login_clear();
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_user']      = $row['username'];
                    $_SESSION['admin_id']        = $row['id'];
                    session_regenerate_id(true);
                    audit_log('login_success', 'admin_users', $row['id']);
                    header('Location: index.php');
                    exit;
                } else {
                    login_record_fail();
                    audit_log('login_failed', '', null, "username: $username");
                    sleep(1); // slow down automated guessing
                    $left  = max(0, 5 - (int)login_attempts_get()['count']);
                    $error = 'Invalid username or password.' . ($left > 0 && $left <= 2 ? " ($left attempt(s) left)" : '');
                }
            } catch (PDOException $e) {
                $db_fail = true;
                $error   = 'Database error. Check the server database configuration.';
            }
        }
    }
}

// Check DB on GET load too
if ($_SERVER['REQUEST_METHOD'] === 'GET' && get_db() === null) {
    $db_fail = true;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — Chulbul Design</title>
<link rel="stylesheet" href="assets/admin.css">
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
  .login-gradient { background: linear-gradient(135deg, #1e1e5c 0%, #49499A 50%, #1e1e5c 100%); }
</style>
</head>
<body class="min-h-screen login-gradient flex items-center justify-center p-4">

<!-- Decorative circles -->
<div class="fixed top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
    <div class="absolute -top-20 -left-20 w-96 h-96 bg-white/5 rounded-full"></div>
    <div class="absolute -bottom-20 -right-20 w-80 h-80 bg-[#EE483D]/10 rounded-full"></div>
    <div class="absolute top-1/2 left-1/4 w-48 h-48 bg-white/5 rounded-full"></div>
</div>

<div class="relative w-full max-w-md">

    <!-- DB Setup notice -->
    <?php if ($db_fail): ?>
    <div class="mb-4 bg-yellow-50 border border-yellow-300 rounded-2xl px-5 py-4 flex items-start gap-3">
        <i class="bi bi-exclamation-triangle-fill text-yellow-500 text-xl flex-shrink-0 mt-0.5"></i>
        <div>
            <p class="font-bold text-yellow-800 text-sm">Database not set up</p>
            <p class="text-yellow-700 text-xs mt-1">The database doesn't exist yet.</p>
            <p class="text-yellow-700 text-xs mt-2">Please verify the database settings on the server.</p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Card -->
    <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

        <!-- Top accent bar -->
        <div class="h-1.5 w-full bg-gradient-to-r from-[#49499A] via-[#EE483D] to-[#49499A]"></div>

        <div class="px-10 py-10">

            <!-- Logo -->
            <div class="text-center mb-8">
                <img src="<?= cbd_base_path() ?>/assets/images/logo/chulbuldesign.svg" alt="Chulbul Design" class="h-12 mx-auto mb-4">
                <h1 class="text-2xl font-extrabold text-[#1e1e5c]">Admin Panel</h1>
                <p class="text-gray-400 text-sm mt-1">Sign in to manage your blog</p>
            </div>

            <!-- Error -->
            <?php if ($error): ?>
            <div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 mb-6 text-sm">
                <i class="bi bi-exclamation-circle-fill text-red-500 flex-shrink-0"></i>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="login.php" class="space-y-5">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(csrf_token()) ?>">

                <div>
                    <label for="username">
                        <i class="bi bi-person mr-1 text-[#49499A]"></i> Username
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                        required
                        autocomplete="username"
                        placeholder="Enter your username"
                    >
                </div>

                <div>
                    <label for="password">
                        <i class="bi bi-lock mr-1 text-[#49499A]"></i> Password
                    </label>
                    <div class="relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="Enter your password"
                            class="pr-12"
                        >
                        <button type="button" onclick="togglePwd()" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#49499A] transition">
                            <i class="bi bi-eye" id="pwd-icon"></i>
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full bg-[#EE483D] hover:bg-red-600 text-white font-bold py-3.5 rounded-xl transition flex items-center justify-center gap-2 mt-2 shadow-lg shadow-red-200"
                >
                    <i class="bi bi-box-arrow-in-right"></i> Admin Login
                </button>

            </form>

        </div>

        <!-- Footer -->
        <div class="bg-gray-50 border-t border-gray-100 px-10 py-4 text-center">
            <p class="text-xs text-gray-400">
                <i class="bi bi-shield-lock mr-1"></i> Secure Admin Access — Chulbul Design
            </p>
        </div>
    </div>

    <!-- Back to site -->
    <div class="text-center mt-6">
        <a href="<?= cbd_base_path() ?>/" class="text-white/70 hover:text-white text-sm transition flex items-center justify-center gap-1.5">
            <i class="bi bi-arrow-left"></i> Back to Website
        </a>
    </div>

</div>

<script>
function togglePwd() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('pwd-icon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
