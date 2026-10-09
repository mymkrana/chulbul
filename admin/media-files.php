<?php
require_once __DIR__ . '/config.php';
require_login(true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf(true);
}

$resolvedImageRoot = realpath(__DIR__ . '/../assets/images');
if ($resolvedImageRoot === false) {
    cbd_json_response(['success' => false, 'error' => 'Image library is unavailable'], 503);
}
$imgRoot   = rtrim($resolvedImageRoot, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
$uploadDir = $imgRoot . 'uploads/';
$base      = cbd_base_path();

// ── Upload ──────────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['file'])) {
    $saved = save_admin_image_upload($_FILES['file'], $uploadDir);
    if (!$saved['ok']) { cbd_json_response(['success' => false, 'error' => $saved['error']], 422); }
    $filename = $saved['filename'];
    cbd_json_response([
        'success' => true,
        'file'    => buildFileEntry($uploadDir . $filename, $imgRoot, $base),
    ]);
}

// ── Delete (uploads only) ───────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['delete'])) {
    $name = basename($_POST['delete']);
    $path = $uploadDir . $name;
    $realUpload = realpath($uploadDir);
    $realPath   = realpath($path);
    if ($realUpload && $realPath && is_file($realPath) && str_starts_with($realPath, rtrim($realUpload, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR)) {
        unlink($realPath);
        cbd_json_response(['success' => true]);
    } else {
        cbd_json_response(['success' => false, 'error' => 'Only uploaded files can be deleted'], 422);
    }
}

// ── List all images recursively ─────────────────────────────────────────────
function buildFileEntry(string $abs, string $root, string $base): array {
    $rel     = str_replace('\\', '/', substr($abs, strlen($root)));
    $folder  = dirname($rel) === '.' ? 'images' : dirname($rel);
    return [
        'name'       => basename($abs),
        'path'       => $base . '/assets/images/' . $rel,
        'folder'     => $folder,
        'size'       => filesize($abs),
        'mtime'      => filemtime($abs),
        'deletable'  => str_starts_with($abs, $root . 'uploads/'),
    ];
}

$allowed_ext = ['jpg','jpeg','png','webp','gif','svg'];
$files       = [];

$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($imgRoot, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $fileInfo) {
    if (!$fileInfo->isFile()) continue;
    $ext = strtolower($fileInfo->getExtension());
    if (!in_array($ext, $allowed_ext)) continue;
    $files[] = buildFileEntry($fileInfo->getPathname(), $imgRoot, $base);
}

// Sort: uploads first (newest), then rest alphabetically
usort($files, function($a, $b) {
    $aUp = $a['folder'] === 'uploads';
    $bUp = $b['folder'] === 'uploads';
    if ($aUp !== $bUp) return $bUp <=> $aUp; // uploads first... wait, uploads should come first
    if ($aUp && $bUp) return $b['mtime'] - $a['mtime']; // newest upload first
    return strcmp($a['folder'] . $a['name'], $b['folder'] . $b['name']);
});

// Get unique folders for filter tabs
$folders = array_values(array_unique(array_column($files, 'folder')));
sort($folders);

cbd_json_response(['files' => $files, 'folders' => $folders, 'base' => $base]);
