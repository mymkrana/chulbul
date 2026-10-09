<?php
// Legacy compatibility wrapper. The maintained admin sidebar has one source.
require_once __DIR__ . '/admin/config.php';
require_login();
require __DIR__ . '/admin/includes/sidebar.php';
