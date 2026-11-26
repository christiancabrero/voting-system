<?php
/**
 * API: Public Participants List (Fully Cacheable with HTTP ETag)
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../includes/Database.php';
require_once __DIR__ . '/../includes/Storage.php';

$options = Storage::getOptions();
$json = json_encode(['success' => true, 'data' => $options]);
$etag = '"' . md5($json) . '"';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=60');
header('ETag: ' . $etag);

if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
    http_response_code(304);
    exit;
}

echo $json;
