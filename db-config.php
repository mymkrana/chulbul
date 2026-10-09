<?php
// Compatibility wrapper for older includes that expect a $pdo variable.
require_once __DIR__ . '/includes/database.php';
$pdo = cbd_database();
