<?php
/** First-party behaviour collector. No form values, URL queries or raw IP stored. */
require_once __DIR__ . '/includes/analytics-outcomes.php';
header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, max-age=0');
header('X-Robots-Tag: noindex, nofollow, noarchive');
header('X-Content-Type-Options: nosniff');
function cbd_analytics_response(array $data, int $status = 200): void {
    http_response_code($status); echo json_encode($data, JSON_UNESCAPED_SLASHES); exit;
}
function cbd_analytics_text($value, int $length): string {
    if (!is_scalar($value)) return '';
    $value = preg_replace('/[\x00-\x1F\x7F]/u', ' ', strip_tags((string)$value)) ?? '';
    $value = preg_replace('/[\w.+-]+@[\w.-]+\.[a-z]{2,}/i', '[email]', $value);
    $value = preg_replace('/\+?\d[\d ().-]{6,}\d/', '[number]', $value);
    return mb_substr(preg_replace('/\s+/u', ' ', trim($value)), 0, $length);
}
function cbd_analytics_id($value): string {
    return is_string($value) && preg_match('/^[a-f0-9]{32}$/', $value) ? $value : '';
}
function cbd_analytics_path($value): string {
    $path = is_string($value) ? parse_url($value, PHP_URL_PATH) : '/';
    $path = is_string($path) ? preg_replace('/[\x00-\x1F\x7F]/', '', $path) : '/';
    $base = cbd_base_path();
    if ($base !== '' && ($path === $base || str_starts_with($path, $base . '/'))) $path = substr($path, strlen($base));
    return mb_substr('/' . ltrim($path, '/'), 0, 512);
}
function cbd_analytics_safe_url($value, string $serverHost): string {
    if (!is_string($value) || $value === '') return '';
    if (in_array($value, ['whatsapp','telephone','email'], true)) return $value;
    if (stripos($value, 'tel:') === 0) return 'telephone';
    if (stripos($value, 'mailto:') === 0) return 'email';
    $parts = parse_url($value);
    if (!is_array($parts) || (isset($parts['scheme']) && !in_array(strtolower($parts['scheme']), ['http','https'], true))) return '';
    $host = strtolower($parts['host'] ?? '');
    if ($host === 'wa.me' || preg_match('/(^|\.)whatsapp\.com$/', $host)) return 'whatsapp';
    return $host === '' || $host === $serverHost ? cbd_analytics_path($parts['path'] ?? '/') : mb_substr($host, 0, 255);
}
function cbd_analytics_device(string $ua): string {
    if (preg_match('/ipad|tablet|kindle|silk/i', $ua)) return 'Tablet';
    return preg_match('/mobile|iphone|ipod|android/i', $ua) ? 'Mobile' : 'Desktop';
}
function cbd_analytics_browser(string $ua): string {
    foreach (['Edg/' => 'Edge','OPR/' => 'Opera','Firefox/' => 'Firefox','Chrome/' => 'Chrome','Safari/' => 'Safari'] as $needle => $browser) if (stripos($ua, $needle) !== false) return $browser;
    return 'Other';
}
if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') { header('Allow: POST'); cbd_analytics_response(['ok'=>false], 405); }
if ((int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 32768) cbd_analytics_response(['ok'=>false], 413);
$raw = file_get_contents('php://input', false, null, 0, 32769);
if (strlen($raw ?: '') > 32768) cbd_analytics_response(['ok'=>false], 413);
$host = strtolower((string)parse_url('http://' . ($_SERVER['HTTP_HOST'] ?? ''), PHP_URL_HOST));
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$originParts = parse_url($origin);
$requestAuthority = strtolower(preg_replace('/:(80|443)$/', '', $_SERVER['HTTP_HOST'] ?? ''));
$originAuthority = is_array($originParts) ? strtolower($originParts['host'] ?? '') . (isset($originParts['port']) && !in_array($originParts['port'], [80,443],true) ? ':' . $originParts['port'] : '') : '';
if ($origin !== '' && (!is_array($originParts) || strtolower($originParts['host'] ?? '') !== $host
    || $originAuthority !== $requestAuthority || !in_array($originParts['scheme'] ?? '', ['http','https'],true))) {
    cbd_analytics_response(['ok'=>false, 'error'=>'Origin rejected'], 403);
}
if (($_SERVER['HTTP_SEC_FETCH_SITE'] ?? '') === 'cross-site') cbd_analytics_response(['ok'=>false], 403);
if (($_SERVER['HTTP_DNT'] ?? '') === '1' || ($_SERVER['HTTP_SEC_GPC'] ?? '') === '1') cbd_analytics_response(['ok'=>true,'ignored'=>'privacy']);
if (cbd_analytics_staff()) cbd_analytics_response(['ok'=>true,'ignored'=>'staff']);
$ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
if (!$ua || preg_match('/bot|crawl|spider|slurp|preview|monitor|headless|lighthouse/i', $ua)) cbd_analytics_response(['ok'=>true,'ignored'=>'bot']);
$data = json_decode($raw ?: '', true);
if (!is_array($data)) cbd_analytics_response(['ok'=>false, 'error'=>'Invalid JSON'], 422);
$action = $data['action'] ?? '';
if (!is_string($action) || !in_array($action, ['start','update','events'], true)) cbd_analytics_response(['ok'=>false], 422);
$session = cbd_analytics_id($data['session_id'] ?? '');
$visitor = cbd_analytics_id($data['visitor_id'] ?? '');
$view = cbd_analytics_id($data['view_id'] ?? '');
$token = cbd_analytics_id($data['view_token'] ?? '');
if (!$session || !$visitor || !$view || !$token) cbd_analytics_response(['ok'=>false, 'error'=>'Invalid view identity'], 422);
$path = cbd_analytics_path($data['page_path'] ?? '/');
if (preg_match('#^/(admin|includes|assets|database|design-preview)(/|$)#i', rawurldecode($path))) cbd_analytics_response(['ok'=>true,'ignored'=>'private']);
$pdo = cbd_database();
if (!$pdo) cbd_analytics_response(['ok'=>false], 503);
$tokenHash = hash('sha256', $token);
$engaged = max(0, min(7200, (int)($data['engaged_seconds'] ?? 0)));
$scroll = max(0, min(100, (int)($data['max_scroll'] ?? 0)));
try {
    $pdo->beginTransaction();
    if ($action === 'start') {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '';
        // A random local session-file salt; explicit ANALYTICS_SALT is recommended on live.
        $salt = cbd_env('ANALYTICS_SALT');
        if ($salt === '') {
            $saltFile = rtrim(sys_get_temp_dir(), '/') . '/cbd-analytics-' . substr(hash('sha256', __DIR__), 0, 12) . '.salt';
            $handle = @fopen($saltFile, 'c+');
            if (!$handle || !flock($handle, LOCK_EX)) throw new RuntimeException('Analytics salt unavailable');
            @chmod($saltFile, 0600);
            $salt = stream_get_contents($handle);
            if (strlen($salt) < 64) { $salt = bin2hex(random_bytes(32)); rewind($handle); fwrite($handle, $salt); fflush($handle); }
            flock($handle, LOCK_UN); fclose($handle);
        }
        $ipHash = $ip ? hash_hmac('sha256', $ip, $salt . date('Y-m-d')) : null;
        $limit = $pdo->prepare('SELECT COUNT(*) FROM analytics_pageviews pv JOIN analytics_sessions s ON s.session_id = pv.session_id WHERE s.ip_hash = ? AND pv.started_at >= NOW() - INTERVAL 5 MINUTE');
        $limit->execute([$ipHash]);
        if ((int)$limit->fetchColumn() >= 120) { $pdo->rollBack(); cbd_analytics_response(['ok'=>true,'ignored'=>'rate']); }
        $referrer = cbd_analytics_safe_url($data['referrer'] ?? '', $host);
        $pdo->prepare('INSERT INTO analytics_sessions (session_id,visitor_id,landing_page,referrer,utm_source,utm_medium,utm_campaign,device_type,browser,ip_hash) VALUES (?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE session_id = session_id')
            ->execute([$session,$visitor,$path,$referrer ?: null,cbd_analytics_text($data['utm_source'] ?? '',120) ?: null,
                cbd_analytics_text($data['utm_medium'] ?? '',120) ?: null,cbd_analytics_text($data['utm_campaign'] ?? '',160) ?: null,
                cbd_analytics_device($ua),cbd_analytics_browser($ua),$ipHash]);
        $q = $pdo->prepare('SELECT visitor_id FROM analytics_sessions WHERE session_id = ?');
        $q->execute([$session]);
        if ($q->fetchColumn() !== $visitor) throw new DomainException('Session mismatch');
        $pdo->prepare('INSERT INTO analytics_pageviews (view_id,session_id,visitor_id,page_path,page_title,referrer,viewport_width,viewport_height,view_token_hash) VALUES (?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE view_id = view_id')
            ->execute([$view,$session,$visitor,$path,cbd_analytics_text($data['page_title'] ?? '',255),$referrer ?: null,
                max(0,min(10000,(int)($data['viewport_width'] ?? 0))),max(0,min(10000,(int)($data['viewport_height'] ?? 0))),$tokenHash]);
    }
    $q = $pdo->prepare('SELECT * FROM analytics_pageviews WHERE view_id = ? AND session_id = ? AND visitor_id = ? FOR UPDATE');
    $q->execute([$view,$session,$visitor]); $known = $q->fetch();
    if (!$known || !$known['view_token_hash'] || !hash_equals($known['view_token_hash'], $tokenHash)) throw new DomainException('View mismatch');
    $path = $known['page_path']; // Never accept an event's claimed page over its registered page.
    if ($action !== 'events') {
        $age = $pdo->prepare('SELECT GREATEST(0,TIMESTAMPDIFF(SECOND,started_at,NOW())) FROM analytics_pageviews WHERE view_id = ?');
        $age->execute([$view]);
        $engaged = min($engaged, (int)$age->fetchColumn() + 2);
        $pdo->prepare('UPDATE analytics_pageviews SET engaged_seconds = GREATEST(engaged_seconds,?), max_scroll = GREATEST(max_scroll,?), last_seen_at = NOW() WHERE view_id = ?')
            ->execute([$engaged,$scroll,$view]);
    }
    if ($action === 'events' || ($action === 'start' && !empty($data['events']))) {
        $limit = $pdo->prepare('SELECT COUNT(*) FROM analytics_events WHERE session_id = ? AND created_at >= NOW() - INTERVAL 1 MINUTE');
        $limit->execute([$session]); $remaining = max(0, 120 - (int)$limit->fetchColumn());
        $events = is_array($data['events'] ?? null) ? array_slice($data['events'], 0, min(20,$remaining)) : [];
        $insert = $pdo->prepare('INSERT INTO analytics_events (event_key,view_id,session_id,visitor_id,page_path,event_type,event_name,element_tag,element_text,target_path,click_x,click_y) VALUES (?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE id = id');
        foreach ($events as $event) {
            if (!is_array($event)) continue;
            $type = $event['event_type'] ?? '';
            // No client payload can mark a saved lead or an accepted email.
            if (!is_string($type) || !in_array($type,['link_click','button_click','element_click','outbound_click','conversion','form_start','form_submit','scroll','custom'],true)) continue;
            $eventKey = cbd_analytics_id($event['event_key'] ?? ''); if (!$eventKey) continue;
            $key = substr(hash('sha256', 'client|' . $view . '|' . $eventKey),0,32);
            $target = cbd_analytics_safe_url($event['target_path'] ?? '',$host);
            $name = cbd_analytics_text($event['event_name'] ?? '',100);
            if ($type === 'conversion') {
                $names = ['whatsapp'=>'WhatsApp','telephone'=>'Phone call','email'=>'Email'];
                if (!isset($names[$target])) continue;
                $name = $names[$target];
            }
            $coords = in_array($type,['link_click','button_click','element_click','outbound_click','conversion'],true);
            $x = $coords && is_numeric($event['click_x'] ?? null) ? max(0,min(100,(float)$event['click_x'])) : null;
            $y = $coords && is_numeric($event['click_y'] ?? null) ? max(0,min(100,(float)$event['click_y'])) : null;
            $insert->execute([$key,$view,$session,$visitor,$path,$type,$name,cbd_analytics_text($event['element_tag'] ?? '',24),
                cbd_analytics_text($event['element_text'] ?? '',180),$target ?: null,$x,$y]);
        }
    }
    $pdo->prepare('UPDATE analytics_sessions SET last_seen_at = NOW() WHERE session_id = ?')->execute([$session]);
    $pdo->commit();
} catch (DomainException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    cbd_analytics_response(['ok'=>false,'error'=>'View not registered'],403);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    error_log('Analytics collector storage unavailable');
    cbd_analytics_response(['ok'=>false,'error'=>'Analytics storage unavailable'],503);
}
cbd_analytics_response(['ok'=>true]);
