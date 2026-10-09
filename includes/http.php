<?php
require_once __DIR__ . '/database.php';

/** True only when the browser explicitly requested this PHP filename. */
function cbd_is_direct_script_request(string $filename): bool
{
    $requestLine = trim((string)($_SERVER['THE_REQUEST'] ?? ''));
    $requestTarget = '';
    if ($requestLine !== '' && preg_match('/^[A-Z]+\s+(\S+)/i', $requestLine, $match)) {
        $requestTarget = $match[1];
    } else {
        // PHP's built-in server does not populate THE_REQUEST. REQUEST_URI is
        // a safe fallback there; Apache uses THE_REQUEST so internal rewrites
        // are never mistaken for direct PHP requests.
        $requestTarget = (string)($_SERVER['REQUEST_URI'] ?? '');
    }

    $path = parse_url($requestTarget, PHP_URL_PATH);
    return is_string($path) && basename($path) === basename($filename);
}

/** Redirect to a project-relative canonical path and end the request. */
function cbd_redirect_path(string $path, int $status = 301): never
{
    $path = '/' . ltrim($path, '/');
    header('Location: ' . cbd_base_path() . $path, true, $status);
    exit;
}
