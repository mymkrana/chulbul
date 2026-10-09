<?php
// Admin bootstrap security: private responses, strict sessions and no indexing.
$__cbd_https = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
    || strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';

if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, private, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
    header('X-Robots-Tag: noindex, nofollow, noarchive');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: same-origin');
}

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    session_name('CBDADMINSESSID');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $__cbd_https,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
unset($__cbd_https);

require_once dirname(__DIR__) . '/includes/database.php';

function _env(string $k, string $default = ''): string {
    return cbd_env($k, $default);
}

// ── Paths ─────────────────────────────────────────────────────────────────────
define('ADMIN_PATH', __DIR__);
define('ROOT_PATH',  dirname(__DIR__));

// ── API Keys (loaded from .env) ────────────────────────────────────────────────
define('GEMINI_API_KEY',      _env('GEMINI_API_KEY'));
define('GROQ_API_KEY',        _env('GROQ_API_KEY'));
define('OPENROUTER_API_KEY',  _env('OPENROUTER_API_KEY'));
define('SAMBANOVA_API_KEY',   _env('SAMBANOVA_API_KEY'));
define('FIRECRAWL_API_KEY',   _env('FIRECRAWL_API_KEY'));
define('SPIDER_API_KEY',      _env('SPIDER_API_KEY'));
define('SCRAPINGBEE_API_KEY', _env('SCRAPINGBEE_API_KEY'));
define('NVIDIA_API_KEY',      _env('NVIDIA_API_KEY'));
define('NVIDIA_API_KEY_2',    _env('NVIDIA_API_KEY_2'));
define('NVIDIA_API_KEY_3',    _env('NVIDIA_API_KEY_3'));
define('NVIDIA_API_KEY_4',    _env('NVIDIA_API_KEY_4'));
define('NVIDIA_API_KEY_5',    _env('NVIDIA_API_KEY_5'));
define('NVIDIA_API_KEY_6',    _env('NVIDIA_API_KEY_6'));
define('NVIDIA_API_KEY_7',    _env('NVIDIA_API_KEY_7'));
define('NVIDIA_API_KEY_8',    _env('NVIDIA_API_KEY_8'));
define('NVIDIA_API_KEY_9',    _env('NVIDIA_API_KEY_9'));
define('NVIDIA_API_KEY_10',   _env('NVIDIA_API_KEY_10'));
define('NVIDIA_API_KEY_11',   _env('NVIDIA_API_KEY_11'));
define('NVIDIA_API_KEY_12',   _env('NVIDIA_API_KEY_12'));
define('NVIDIA_API_KEY_13',   _env('NVIDIA_API_KEY_13'));
define('MAKE_WEBHOOK_URL',    _env('MAKE_WEBHOOK_URL'));
define('META_SYSTEM_USER_TOKEN', _env('META_SYSTEM_USER_TOKEN'));
define('META_PAGE_ID',           _env('META_PAGE_ID', '101472067939005'));
define('META_INSTAGRAM_ID',      _env('META_INSTAGRAM_ID', '17841421920831205'));
define('META_GRAPH_VERSION',     _env('META_GRAPH_VERSION', 'v26.0'));
define('BLOG_CATEGORIES', ['Web Design','Web Development','SEO','WordPress','Ecommerce','Shopify','Mobile App','Digital Marketing','UI/UX Design','Branding']);

// ── Error Logger ──────────────────────────────────────────────────────────────
function cbd_log_error(string $context, string $msg, array $data = []): void {
    $log_dir = ROOT_PATH . '/logs';
    if (!is_dir($log_dir)) { @mkdir($log_dir, 0755, true); }
    $line = date('[Y-m-d H:i:s]') . " [$context] $msg";
    if ($data) $line .= ' ' . json_encode($data, JSON_UNESCAPED_UNICODE);
    $line .= ' | IP:' . ($_SERVER['REMOTE_ADDR'] ?? '-') . "\n";
    @file_put_contents($log_dir . '/errors.log', $line, FILE_APPEND | LOCK_EX);
}

// ── DB Connection ─────────────────────────────────────────────────────────────
function get_db(): ?PDO {
    return cbd_database();
}

// ── Audit Log ─────────────────────────────────────────────────────────────────
function audit_log(string $action, string $entity = '', $entity_id = null, string $detail = ''): void {
    $pdo = get_db();
    if (!$pdo) return;
    static $table_ok = false;
    if (!$table_ok) {
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS admin_audit_log (
                id         BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                admin_user VARCHAR(100) DEFAULT NULL,
                action     VARCHAR(80)  NOT NULL,
                entity     VARCHAR(80)  DEFAULT NULL,
                entity_id  INT          DEFAULT NULL,
                detail     TEXT         DEFAULT NULL,
                ip         VARCHAR(45)  DEFAULT NULL,
                created_at DATETIME     DEFAULT CURRENT_TIMESTAMP,
                INDEX (action), INDEX (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $table_ok = true;
        } catch (Exception $e) {
            cbd_log_error('audit_log.create_table', $e->getMessage());
            return;
        }
    }
    try {
        $pdo->prepare(
            "INSERT INTO admin_audit_log (admin_user,action,entity,entity_id,detail,ip) VALUES (?,?,?,?,?,?)"
        )->execute([
            $_SESSION['admin_user'] ?? 'system',
            $action,
            $entity    ?: null,
            $entity_id ? (int)$entity_id : null,
            $detail    ?: null,
            $_SERVER['HTTP_CF_CONNECTING_IP'] ?? ($_SERVER['REMOTE_ADDR'] ?? null),
        ]);
    } catch (Exception $e) {
        cbd_log_error('audit_log.insert', $e->getMessage());
    }
}

// ── Auth Helpers ──────────────────────────────────────────────────────────────
function cbd_json_response(array $payload, int $status = 200): void {
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        header('Cache-Control: no-store, no-cache, must-revalidate, private, max-age=0');
    }
    echo json_encode(
        $payload,
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    );
    exit;
}

function require_login(bool $json = false): void {
    if (empty($_SESSION['admin_logged_in'])) {
        if ($json) {
            cbd_json_response(['success' => false, 'error' => 'Admin session expired. Please log in again.'], 401);
        }
        header('Location: login.php');
        exit;
    }
}

function set_flash(string $msg, string $type = 'success'): void {
    $_SESSION['flash'] = ['msg' => $msg, 'type' => $type];
}

function get_flash(): ?array {
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function require_post_request(bool $json = false): void {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') return;
    http_response_code(405);
    header('Allow: POST');
    if ($json) {
        cbd_json_response(['success' => false, 'error' => 'POST request required'], 405);
    } else {
        set_flash('Invalid request method.', 'error');
        header('Location: index.php');
    }
    exit;
}

function verify_csrf(bool $json = false): void {
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        if ($json) {
            cbd_json_response(['success' => false, 'error' => 'Invalid or expired security token'], 403);
        }
        set_flash('Invalid request. Please try again.', 'error');
        $referer = (string)($_SERVER['HTTP_REFERER'] ?? '');
        $path = parse_url($referer, PHP_URL_PATH);
        $target = ($path && str_contains($path, '/admin/')) ? basename($path) : 'index.php';
        header('Location: ' . $target);
        exit;
    }
}

/**
 * Validate an uploaded raster image by its actual bytes and return a safe
 * server-generated filename. SVG is intentionally excluded from public uploads.
 */
function save_admin_image_upload(array $file, string $upload_dir, int $max_bytes = 5242880): array {
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'Upload failed.'];
    }
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['ok' => false, 'error' => 'Invalid upload source.'];
    }
    $size = (int)($file['size'] ?? 0);
    if ($size < 1 || $size > $max_bytes) {
        return ['ok' => false, 'error' => 'Image must be between 1 byte and 5 MB.'];
    }

    $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) ?: '';
    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
    ];
    if (!isset($extensions[$mime]) || @getimagesize($file['tmp_name']) === false) {
        return ['ok' => false, 'error' => 'Only valid JPG, PNG, WEBP or GIF images are allowed.'];
    }

    if (!is_dir($upload_dir) && !mkdir($upload_dir, 0755, true)) {
        return ['ok' => false, 'error' => 'Upload directory is unavailable.'];
    }
    @chmod($upload_dir, 0755);

    $stem = strtolower(pathinfo((string)($file['name'] ?? 'image'), PATHINFO_FILENAME));
    $stem = trim(preg_replace('/[^a-z0-9]+/', '-', $stem), '-');
    $stem = $stem !== '' ? substr($stem, 0, 60) : 'image';
    $filename = $stem . '-' . bin2hex(random_bytes(8)) . '.' . $extensions[$mime];
    $destination = rtrim($upload_dir, '/\\') . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        return ['ok' => false, 'error' => 'Failed to save image.'];
    }
    @chmod($destination, 0644);
    return ['ok' => true, 'filename' => $filename, 'mime' => $mime, 'size' => $size];
}

/** Resolve a public HTTP(S) URL and reject loopback/private/reserved targets. */
function cbd_public_remote_target(string $url): ?array {
    $parts = parse_url($url);
    if (!is_array($parts) || !in_array(strtolower((string)($parts['scheme'] ?? '')), ['http', 'https'], true)) return null;
    if (!empty($parts['user']) || !empty($parts['pass'])) return null;
    $host = strtolower(rtrim((string)($parts['host'] ?? ''), '.'));
    if ($host === '' || $host === 'localhost' || str_ends_with($host, '.local')) return null;

    $ips = [];
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        $ips[] = $host;
    } else {
        foreach (dns_get_record($host, DNS_A | DNS_AAAA) ?: [] as $record) {
            if (!empty($record['ip'])) $ips[] = $record['ip'];
            if (!empty($record['ipv6'])) $ips[] = $record['ipv6'];
        }
    }
    $ips = array_values(array_unique($ips));
    if (!$ips) return null;
    foreach ($ips as $ip) {
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) return null;
    }

    $scheme = strtolower((string)$parts['scheme']);
    $port = (int)($parts['port'] ?? ($scheme === 'https' ? 443 : 80));
    if (!in_array($port, [80, 443], true)) return null;
    $ipv4 = array_values(array_filter($ips, static fn($ip) => filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)));
    $resolveIp = $ipv4[0] ?? ('[' . $ips[0] . ']');
    return ['host' => $host, 'port' => $port, 'ip' => $resolveIp];
}

/**
 * Bounded SSRF-safe GET with TLS verification and redirect re-validation.
 * Returns body/status/mime or an error; it never follows a private target.
 */
function cbd_safe_remote_get(string $url, int $max_bytes = 8388608, int $timeout = 20, int $redirects = 3): array {
    for ($hop = 0; $hop <= $redirects; $hop++) {
        $target = cbd_public_remote_target($url);
        if (!$target) return ['ok' => false, 'error' => 'Unsafe or invalid remote URL.'];

        $body = '';
        $headers = [];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_CONNECTTIMEOUT => min(8, $timeout),
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
            CURLOPT_USERAGENT => 'ChulbulDesign/1.0 SecureFetcher',
            CURLOPT_RESOLVE => [$target['host'] . ':' . $target['port'] . ':' . $target['ip']],
            CURLOPT_HEADERFUNCTION => static function ($ch, string $line) use (&$headers): int {
                $len = strlen($line);
                $pos = strpos($line, ':');
                if ($pos !== false) $headers[strtolower(trim(substr($line, 0, $pos)))] = trim(substr($line, $pos + 1));
                return $len;
            },
            CURLOPT_WRITEFUNCTION => static function ($ch, string $chunk) use (&$body, $max_bytes): int {
                if (strlen($body) + strlen($chunk) > $max_bytes) return 0;
                $body .= $chunk;
                return strlen($chunk);
            },
        ]);
        $executed = curl_exec($ch);
        $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $mime = strtolower(trim(explode(';', (string)curl_getinfo($ch, CURLINFO_CONTENT_TYPE))[0]));
        $error = curl_error($ch);
        curl_close($ch);

        if ($status >= 300 && $status < 400 && !empty($headers['location'])) {
            $next = $headers['location'];
            if (!preg_match('#^https?://#i', $next)) {
                $base = parse_url($url);
                if (str_starts_with($next, '/')) {
                    $next = $base['scheme'] . '://' . $base['host'] . $next;
                } else {
                    $path = rtrim(dirname((string)($base['path'] ?? '/')), '/');
                    $next = $base['scheme'] . '://' . $base['host'] . $path . '/' . $next;
                }
            }
            $url = $next;
            continue;
        }

        if ($executed === false || $error !== '') return ['ok' => false, 'error' => $error ?: 'Remote request failed.'];
        return ['ok' => $status >= 200 && $status < 300, 'status' => $status, 'mime' => $mime, 'body' => $body, 'url' => $url];
    }
    return ['ok' => false, 'error' => 'Too many redirects.'];
}

// ── Social Media Publishing ───────────────────────────────────────────────────
function cbd_social_log(string $platform, bool $ok, string $detail): void {
    $log_dir = ROOT_PATH . '/logs';
    if (!is_dir($log_dir)) @mkdir($log_dir, 0755, true);
    $safe_detail = preg_replace('/access_token=[^&\s]+/i', 'access_token=[redacted]', $detail);
    $line = date('[Y-m-d H:i:s]') . ' [' . $platform . '] ' . ($ok ? 'OK ' : 'ERROR ') . $safe_detail . "\n";
    @file_put_contents($log_dir . '/social.log', $line, FILE_APPEND | LOCK_EX);
}

function cbd_meta_request(string $endpoint, array $fields, string $access_token, bool $post = true): array {
    $version = preg_match('/^v\d+\.\d+$/', META_GRAPH_VERSION) ? META_GRAPH_VERSION : 'v26.0';
    $url = 'https://graph.facebook.com/' . $version . '/' . ltrim($endpoint, '/');
    $fields['access_token'] = $access_token;

    $ch = curl_init();
    $options = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPHEADER => ['Accept: application/json'],
    ];
    if ($post) {
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_POST] = true;
        $options[CURLOPT_POSTFIELDS] = http_build_query($fields, '', '&', PHP_QUERY_RFC3986);
    } else {
        $options[CURLOPT_URL] = $url . '?' . http_build_query($fields, '', '&', PHP_QUERY_RFC3986);
    }
    curl_setopt_array($ch, $options);
    $raw = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($raw === false || $curl_error !== '') {
        return ['ok' => false, 'error' => $curl_error ?: 'Meta request failed', 'status' => $status];
    }
    $decoded = json_decode((string)$raw, true);
    if (!is_array($decoded)) {
        return ['ok' => false, 'error' => 'Invalid JSON from Meta', 'status' => $status];
    }
    if ($status < 200 || $status >= 300 || isset($decoded['error'])) {
        return [
            'ok' => false,
            'error' => (string)($decoded['error']['message'] ?? ('Meta HTTP ' . $status)),
            'code' => $decoded['error']['code'] ?? null,
            'status' => $status,
        ];
    }
    return ['ok' => true, 'data' => $decoded, 'status' => $status];
}

function cbd_social_hashtags(array $tag_names): string {
    $hashtags = [];
    foreach (array_slice($tag_names, 0, 20) as $tag) {
        $words = preg_split('/[\s\/\-_&]+/u', trim((string)$tag), -1, PREG_SPLIT_NO_EMPTY);
        $value = preg_replace('/[^a-zA-Z0-9]/', '', implode('', array_map('ucfirst', array_map('strtolower', $words ?: []))));
        if ($value !== '') $hashtags[] = '#' . $value;
    }
    return implode(' ', array_values(array_unique($hashtags)));
}

function cbd_social_image(string $content): string {
    $fallback = 'https://www.chulbuldesign.com/assets/images/Fb_chulbuldesign.jpg';
    if ($content !== '' && preg_match('/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $match)) {
        $src = html_entity_decode(trim((string)$match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (preg_match('#^https?://#i', $src)) return $src;
        $src = preg_replace('#^(?:\.\./)+#', '', ltrim($src, '/'));
        if ($src !== '') return 'https://www.chulbuldesign.com/' . $src;
    }
    return $fallback;
}

function cbd_trigger_make_webhook(string $title, string $excerpt, string $url, string $image, string $hashtags): void {
    if (!MAKE_WEBHOOK_URL) return;
    $data = json_encode(['title' => $title, 'excerpt' => $excerpt, 'url' => $url, 'image' => $image, 'hashtags' => $hashtags]);
    $ch = curl_init(MAKE_WEBHOOK_URL);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    ]);
    $result = curl_exec($ch);
    $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    cbd_social_log('make', $result !== false && $error === '' && $status >= 200 && $status < 300, $error ?: ('HTTP ' . $status));
}

/**
 * Instagram rejects WEBP/GIF URLs. Let Meta create an unpublished Page photo
 * and use its processed CDN rendition; this preserves the real blog thumbnail
 * without requiring GD/Imagick WEBP support on the hosting server.
 */
function cbd_instagram_image(string $image, string $page_token): string {
    $path = (string)(parse_url($image, PHP_URL_PATH) ?? '');
    if (preg_match('/\.jpe?g$/i', $path)) return $image;

    $photo = cbd_meta_request(META_PAGE_ID . '/photos', [
        'url' => $image,
        'published' => 'false',
    ], $page_token);
    if (!$photo['ok'] || empty($photo['data']['id'])) {
        cbd_social_log('instagram-thumbnail', false, (string)($photo['error'] ?? 'Meta thumbnail conversion failed.'));
        return 'https://www.chulbuldesign.com/assets/images/Fb_chulbuldesign.jpg';
    }

    $renditions = cbd_meta_request((string)$photo['data']['id'], ['fields' => 'images'], $page_token, false);
    $converted = (string)($renditions['data']['images'][0]['source'] ?? '');
    if (!$renditions['ok'] || $converted === '') {
        cbd_social_log('instagram-thumbnail', false, (string)($renditions['error'] ?? 'Converted thumbnail URL unavailable.'));
        return 'https://www.chulbuldesign.com/assets/images/Fb_chulbuldesign.jpg';
    }

    cbd_social_log('instagram-thumbnail', true, 'Blog thumbnail converted by Meta.');
    return $converted;
}

/**
 * Instagram image containers are processed asynchronously. Publishing a
 * container immediately can fail even though its image URL is valid, so wait
 * briefly for Meta to report FINISHED before calling /media_publish.
 */
function cbd_wait_for_instagram_container(string $container_id, string $page_token): array {
    $last_status = '';

    for ($attempt = 0; $attempt < 12; $attempt++) {
        $result = cbd_meta_request(
            $container_id,
            ['fields' => 'status_code,status'],
            $page_token,
            false
        );

        if (!$result['ok']) {
            return ['ok' => false, 'error' => (string)($result['error'] ?? 'Could not check Instagram media status.')];
        }

        $last_status = strtoupper((string)($result['data']['status_code'] ?? ''));
        if ($last_status === 'FINISHED') {
            return ['ok' => true];
        }

        if (in_array($last_status, ['ERROR', 'EXPIRED'], true)) {
            return [
                'ok' => false,
                'error' => (string)($result['data']['status'] ?? ('Instagram media status: ' . $last_status)),
            ];
        }

        usleep(1000000);
    }

    return [
        'ok' => false,
        'error' => 'Instagram media was not ready in time (last status: ' . ($last_status ?: 'unknown') . ').',
    ];
}

function trigger_social_post(string $title, string $excerpt, string $slug, string $content = '', array $tag_names = []): void {
    $url = 'https://www.chulbuldesign.com/blog/' . rawurlencode($slug);
    $image = cbd_social_image($content);
    $hashtags = cbd_social_hashtags($tag_names);

    // Safe migration fallback: until a Meta token is configured, keep the old Make flow.
    if (META_SYSTEM_USER_TOKEN === '' || META_PAGE_ID === '') {
        cbd_trigger_make_webhook($title, $excerpt, $url, $image, $hashtags);
        return;
    }

    $identity = cbd_meta_request(
        META_PAGE_ID,
        ['fields' => 'access_token,instagram_business_account,connected_instagram_account'],
        META_SYSTEM_USER_TOKEN,
        false
    );
    if (!$identity['ok']) {
        cbd_social_log('meta-auth', false, (string)$identity['error']);
        return;
    }

    $identity_data = $identity['data'];
    $page_token = (string)($identity_data['access_token'] ?? META_SYSTEM_USER_TOKEN);
    $instagram_image = cbd_instagram_image($image, $page_token);
    $instagram_id = META_INSTAGRAM_ID
        ?: (string)($identity_data['instagram_business_account']['id']
            ?? $identity_data['connected_instagram_account']['id']
            ?? '');

    $message_parts = [trim($title), trim($excerpt), trim($hashtags)];
    $message = implode("\n\n", array_values(array_filter($message_parts, static fn($value) => $value !== '')));
    $facebook = cbd_meta_request(META_PAGE_ID . '/feed', ['message' => $message, 'link' => $url], $page_token);
    cbd_social_log(
        'facebook',
        (bool)$facebook['ok'],
        $facebook['ok'] ? ('Post ID ' . ($facebook['data']['id'] ?? 'created')) : (string)$facebook['error']
    );

    if ($instagram_id === '') {
        cbd_social_log('instagram', false, 'No connected Instagram business account ID configured.');
        return;
    }

    $caption_parts = [trim($title), trim($excerpt), 'Read more: ' . $url, trim($hashtags)];
    $caption = implode("\n\n", array_values(array_filter($caption_parts, static fn($value) => $value !== '')));
    if (function_exists('mb_strlen') && mb_strlen($caption, 'UTF-8') > 2200) {
        $caption = mb_substr($caption, 0, 2197, 'UTF-8') . '...';
    } elseif (strlen($caption) > 2200) {
        $caption = substr($caption, 0, 2197) . '...';
    }

    $container = cbd_meta_request($instagram_id . '/media', [
        'image_url' => $instagram_image,
        'caption' => $caption,
    ], $page_token);
    if (!$container['ok'] || empty($container['data']['id'])) {
        cbd_social_log('instagram', false, (string)($container['error'] ?? 'Media container was not created.'));
        return;
    }

    $container_ready = cbd_wait_for_instagram_container((string)$container['data']['id'], $page_token);
    if (!$container_ready['ok']) {
        cbd_social_log('instagram', false, (string)$container_ready['error']);
        return;
    }

    $published = cbd_meta_request($instagram_id . '/media_publish', [
        'creation_id' => (string)$container['data']['id'],
    ], $page_token);
    cbd_social_log(
        'instagram',
        (bool)$published['ok'],
        $published['ok'] ? ('Media ID ' . ($published['data']['id'] ?? 'created')) : (string)$published['error']
    );
}
