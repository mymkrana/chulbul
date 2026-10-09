<?php
// Compatibility entry point for the older bulk-link tool. The canonical tool
// provides preview, backup, rollback and audit logging.
require_once __DIR__ . '/config.php';
require_login();
header('Location: relink-blogs.php', true, 302);
exit;
