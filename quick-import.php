<?php
// Legacy compatibility entry point. The canonical UI lives under /admin.
$scriptPath = str_replace('\\', '/', (string)($_SERVER['SCRIPT_NAME'] ?? '/quick-import.php'));
$basePath = rtrim(str_replace('/quick-import.php', '', $scriptPath), '/');
header('Location: ' . $basePath . '/admin/quick-import.php', true, 302);
exit;
