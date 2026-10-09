<?php
require_once __DIR__ . '/env-load.php';

function cbd_env(string $key, string $default = ''): string
{
    $value = $_ENV[$key] ?? getenv($key);
    return ($value !== false && $value !== '') ? (string)$value : $default;
}

function cbd_is_production_host(): bool
{
    $host = strtolower((string)($_SERVER['HTTP_HOST'] ?? ''));
    $host = preg_replace('/:\d+$/', '', $host);
    return in_array($host, ['chulbuldesign.com', 'www.chulbuldesign.com'], true);
}

/** Return the project's URL prefix, e.g. /chulbuldesign on XAMPP and '' live. */
function cbd_base_path(): string
{
    static $basePath = null;
    if ($basePath !== null) return $basePath;

    $configured = trim(cbd_env('CBD_BASE_PATH'));
    if ($configured !== '') {
        $basePath = '/' . trim($configured, '/');
        return $basePath === '/' ? '' : $basePath;
    }

    $projectRoot = realpath(dirname(__DIR__));
    $documentRootValue = trim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $documentRoot = $documentRootValue !== '' ? realpath($documentRootValue) : false;
    if ($projectRoot && $documentRoot
        && ($projectRoot === $documentRoot
            || str_starts_with($projectRoot, rtrim($documentRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR))) {
        $relative = trim(str_replace(DIRECTORY_SEPARATOR, '/', substr($projectRoot, strlen($documentRoot))), '/');
        $basePath = $relative === '' ? '' : '/' . $relative;
        return $basePath;
    }

    $basePath = '';
    return $basePath;
}

/** Absolute project URL for crawlers/admin tools; canonical HTTPS URL on live. */
function cbd_site_base_url(): string
{
    if (cbd_is_production_host()) return 'https://www.chulbuldesign.com';

    $host = (string)($_SERVER['HTTP_HOST'] ?? 'localhost');
    if (!preg_match('/^[a-z0-9.:-]+$/i', $host)) $host = 'localhost';
    $https = (!empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off')
        || strtolower((string)($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '')) === 'https';

    return ($https ? 'https://' : 'http://') . $host . cbd_base_path();
}

function cbd_database(): ?PDO
{
    static $attempted = false;
    static $pdo = null;
    if ($attempted) return $pdo;
    $attempted = true;

    $live = cbd_is_production_host();
    $host = cbd_env($live ? 'DB_HOST_LIVE' : 'DB_HOST_LOCAL', cbd_env('DB_HOST', 'localhost'));
    $name = cbd_env($live ? 'DB_NAME_LIVE' : 'DB_NAME_LOCAL', cbd_env('DB_NAME', 'chulbuldesign_blog'));
    $user = cbd_env($live ? 'DB_USER_LIVE' : 'DB_USER_LOCAL', $live ? 'chulbuldesign_admin' : 'root');
    $pass = cbd_env($live ? 'DB_PASS_LIVE' : 'DB_PASS_LOCAL', '');
    $port = (int)cbd_env($live ? 'DB_PORT_LIVE' : 'DB_PORT_LOCAL', cbd_env('DB_PORT', '3306'));

    try {
        $dsn = 'mysql:host=' . $host . ';dbname=' . $name . ';charset=utf8mb4';
        if ($port > 0) $dsn .= ';port=' . $port;
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (Throwable $error) {
        error_log('Chulbul Design database unavailable: ' . $error->getMessage());
        $pdo = null;
    }

    return $pdo;
}
