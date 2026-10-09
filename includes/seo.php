<?php
require_once __DIR__ . '/database.php';

const CBD_CANONICAL_ORIGIN = 'https://www.chulbuldesign.com';

function cbd_canonical_url(string $path = '/'): string
{
    $path = '/' . ltrim($path, '/');
    return CBD_CANONICAL_ORIGIN . ($path === '/' ? '/' : rtrim($path, '/'));
}

/** Convert a stored image/path into a valid absolute public URL for meta/schema. */
function cbd_public_url(string $value, string $fallback = ''): string
{
    $value = trim(html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($value === '') $value = $fallback;
    if ($value === '') return '';

    if (str_starts_with($value, '//')) return 'https:' . $value;
    if (preg_match('#^https?://#i', $value)) {
        return str_replace('https://chulbuldesign.com', CBD_CANONICAL_ORIGIN, $value);
    }
    if (preg_match('#^[a-z][a-z0-9+.-]*:#i', $value)) return '';

    $path = preg_replace('#^(?:\.\./|\./)+#', '', str_replace('\\', '/', $value));
    $path = '/' . ltrim((string)$path, '/');
    $base = cbd_base_path();
    if ($base !== '' && str_starts_with($path, $base . '/')) {
        $path = substr($path, strlen($base));
    }

    return CBD_CANONICAL_ORIGIN . $path;
}

/** Normalize canonical-host URLs recursively inside decoded JSON-LD data. */
function cbd_normalize_schema_urls(mixed $value): mixed
{
    if (is_array($value)) {
        foreach ($value as $key => $item) $value[$key] = cbd_normalize_schema_urls($item);
        return $value;
    }
    if (is_string($value)) {
        return str_replace('https://chulbuldesign.com', CBD_CANONICAL_ORIGIN, $value);
    }
    return $value;
}

function cbd_json_ld(array $schema): string
{
    return json_encode(
        cbd_normalize_schema_urls($schema),
        JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE
    ) ?: '{}';
}

/** Escape plain CMS text once, even when older rows already contain HTML entities. */
function cbd_escape_text(mixed $value): string
{
    return htmlspecialchars(
        html_entity_decode((string)$value, ENT_QUOTES | ENT_HTML5, 'UTF-8'),
        ENT_QUOTES | ENT_SUBSTITUTE,
        'UTF-8'
    );
}
