<?php
require_once __DIR__ . '/config.php';
require_login(true);

require_post_request(true);
verify_csrf(true);

if (empty($_FILES['file'])) {
    cbd_json_response(['success' => false, 'error' => 'No file received'], 422);
}

$uploadDir = __DIR__ . '/../assets/images/uploads/';
$saved = save_admin_image_upload($_FILES['file'], $uploadDir);
if (!$saved['ok']) {
    cbd_json_response(['success' => false, 'error' => $saved['error']], 422);
}
$filename = $saved['filename'];

$base = cbd_base_path();

cbd_json_response([
    'success' => true,
    'path'    => $base . '/assets/images/uploads/' . $filename,
    'url'     => $base . '/assets/images/uploads/' . $filename,
]);
