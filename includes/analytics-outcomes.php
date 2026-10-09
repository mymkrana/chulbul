<?php
/** Server-confirmed outcomes only; no contact details or form values. */
require_once __DIR__ . '/database.php';

function cbd_analytics_staff(): bool
{
    if (session_status() === PHP_SESSION_ACTIVE) return !empty($_SESSION['admin_logged_in']);
    $id = $_COOKIE['CBDADMINSESSID'] ?? '';
    if (!is_string($id) || !preg_match('/^[a-zA-Z0-9,-]{16,128}$/', $id)) return false;
    $oldName = session_name(); $oldId = session_id(); $oldSession = $_SESSION ?? null;
    session_name('CBDADMINSESSID'); session_id($id);
    $started = @session_start(['read_and_close' => true, 'use_cookies' => false, 'cache_limiter' => '', 'use_strict_mode' => true]);
    $isStaff = $started && !empty($_SESSION['admin_logged_in']);
    session_name($oldName); session_id($oldId);
    if ($oldSession === null) unset($_SESSION); else $_SESSION = $oldSession;
    return $isStaff;
}

function cbd_analytics_outcome(string $type, string $reference, string $label, string $target): void
{
    // An analytics outage must never block the actual enquiry.
    try {
        if (!in_array($type, ['lead_saved', 'mail_accepted'], true)) return;
        if (($_SERVER['HTTP_DNT'] ?? '') === '1' || ($_SERVER['HTTP_SEC_GPC'] ?? '') === '1' || cbd_analytics_staff()) return;
        $view = $_POST['_cbd_view'] ?? ''; $token = $_POST['_cbd_token'] ?? '';
        if (!is_string($view) || !is_string($token) || !preg_match('/^[a-f0-9]{32}$/', $view) || !preg_match('/^[a-f0-9]{32}$/', $token)) return;
        $pdo = cbd_database(); if (!$pdo) return;
        $q = $pdo->prepare('SELECT view_id, session_id, visitor_id, page_path, view_token_hash FROM analytics_pageviews WHERE view_id = ? AND started_at >= NOW() - INTERVAL 1 DAY');
        $q->execute([$view]); $row = $q->fetch();
        if (!$row || !$row['view_token_hash'] || !hash_equals($row['view_token_hash'], hash('sha256', $token))) return;
        $key = substr(hash('sha256', 'server|' . $type . '|' . $reference), 0, 32);
        $pdo->prepare('INSERT INTO analytics_events (event_key, view_id, session_id, visitor_id, page_path, event_type, event_name, element_tag, element_text, target_path) VALUES (?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE id = id')
            ->execute([$key, $view, $row['session_id'], $row['visitor_id'], $row['page_path'], $type, $type === 'lead_saved' ? 'Enquiry saved' : 'Mail accepted by server', 'form', $label, $target]);
    } catch (Throwable $error) { error_log('Analytics outcome recording unavailable'); }
}
