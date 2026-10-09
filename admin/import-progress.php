<?php
require_once __DIR__ . '/config.php';
require_login(true);

$key = preg_replace('/[^a-z0-9_]/', '', strtolower($_GET['key'] ?? ''));
if (!$key) { cbd_json_response(['step' => 'waiting', 'pct' => 0, 'detail' => 'Shuru ho raha hai...']); }

$file = sys_get_temp_dir() . '/ci_' . $key . '.json';
if (!file_exists($file)) {
    cbd_json_response(['step' => 'waiting', 'pct' => 0, 'detail' => 'Shuru ho raha hai...', 'ai_model' => '']);
}

$data = json_decode(file_get_contents($file), true);
if (!$data) {
    cbd_json_response(['step' => 'waiting', 'pct' => 0, 'detail' => '...', 'ai_model' => '']);
}

// If file is older than 5 minutes, something went wrong
if ((time() - ($data['ts'] ?? 0)) > 300) {
    @unlink($file);
    cbd_json_response(['step' => 'timeout', 'pct' => 0, 'detail' => 'Timeout — please retry'], 408);
}

cbd_json_response($data);
