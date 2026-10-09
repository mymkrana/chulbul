<?php
// ── First-party analytics collector for landing pages ───────────────────────
// Receives lightweight events (pageview / click / time) from the landing page JS
// and stores them in lp_events. Public endpoint (visitors hit it) — no auth.
require_once __DIR__ . '/admin/config.php';
header('Content-Type: application/json');
header('X-Robots-Tag: noindex, nofollow, noarchive');

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['ok' => false, 'error' => 'POST only']);
    exit;
}

$type  = $_POST['type'] ?? '';
if (!in_array($type, ['pageview', 'click', 'time'], true)) { echo json_encode(['ok' => false]); exit; }

$sid   = substr(preg_replace('/[^a-zA-Z0-9]/', '', $_POST['sid'] ?? ''), 0, 40);
$vid   = substr(preg_replace('/[^a-zA-Z0-9]/', '', $_POST['vid'] ?? ''), 0, 40);
$label = substr(trim($_POST['label'] ?? ''), 0, 60);
$secs  = max(0, min(7200, (int)($_POST['secs'] ?? 0)));
$page  = substr(preg_replace('/[^a-z0-9_-]/i', '', $_POST['page'] ?? 'business-starter'), 0, 60);
$ref   = substr($_POST['ref'] ?? '', 0, 255);
if (!$sid) { echo json_encode(['ok' => false]); exit; }

$pdo = get_db();
if ($pdo) {
    try {
        $pdo->exec("CREATE TABLE IF NOT EXISTS lp_events (
            id          INT AUTO_INCREMENT PRIMARY KEY,
            page        VARCHAR(60)  DEFAULT NULL,
            event_type  VARCHAR(20)  DEFAULT NULL,
            label       VARCHAR(60)  DEFAULT NULL,
            secs        INT          DEFAULT 0,
            session_id  VARCHAR(40)  DEFAULT NULL,
            visitor_id  VARCHAR(40)  DEFAULT NULL,
            ip          VARCHAR(45)  DEFAULT NULL,
            ua          VARCHAR(255) DEFAULT NULL,
            referrer    VARCHAR(255) DEFAULT NULL,
            created_at  DATETIME     DEFAULT CURRENT_TIMESTAMP,
            INDEX (page), INDEX (event_type), INDEX (created_at), INDEX (visitor_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? ($_SERVER['REMOTE_ADDR'] ?? '');
        $ua = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255);
        // Skip obvious bots from "real visitor" data
        if ($ua && preg_match('/bot|crawl|spider|slurp|bing|google|facebookexternalhit|preview|monitor/i', $ua)) {
            echo json_encode(['ok' => true, 'bot' => true]); exit;
        }
        $pdo->prepare("INSERT INTO lp_events (page,event_type,label,secs,session_id,visitor_id,ip,ua,referrer) VALUES (?,?,?,?,?,?,?,?,?)")
            ->execute([$page, $type, $label, $secs, $sid, $vid, $ip, $ua, $ref]);
    } catch (Exception $e) { /* table/insert fail — ignore, never break the page */ }
}
echo json_encode(['ok' => true]);
