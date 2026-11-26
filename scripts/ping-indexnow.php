<?php
declare(strict_types=1);

/**
 * CLI Helper — Submit live sitemap.xml URLs to IndexNow (Bing / ChatGPT Search, Yandex, Seznam, Naver)
 * Usage: php scripts/ping-indexnow.php
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit;
}

$host = 'www.chulbuldesign.com';
$key = 'chulbul-indexnow-8f4a2c91e7b34d60';
$keyLocation = "https://{$host}/{$key}.txt";

$xmlRaw = @file_get_contents("https://{$host}/sitemap.xml");
if (!$xmlRaw) {
    fwrite(STDERR, "Failed to fetch live sitemap.xml\n");
    exit(1);
}

$xml = @simplexml_load_string($xmlRaw);
if (!$xml) {
    fwrite(STDERR, "Failed to parse live sitemap.xml\n");
    exit(1);
}

$urls = [];
foreach ($xml->url as $u) {
    $loc = trim((string)$u->loc);
    if ($loc !== '') {
        $urls[] = $loc;
    }
}

// Also include llms.txt for rapid AI discovery
$urls[] = "https://{$host}/llms.txt";
$urls = array_values(array_unique($urls));

$payload = json_encode([
    'host' => $host,
    'key' => $key,
    'keyLocation' => $keyLocation,
    'urlList' => $urls,
], JSON_UNESCAPED_SLASHES);

$ch = curl_init('https://api.indexnow.org/indexnow');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf-8'],
    CURLOPT_TIMEOUT => 20,
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

echo "INDEXNOW_SUBMITTED_URLS=" . count($urls) . "\n";
echo "INDEXNOW_HTTP_CODE=" . $code . "\n";
if ($err) echo "CURL_ERR=" . $err . "\n";
if ($resp) echo "RESPONSE=" . substr((string)$resp, 0, 300) . "\n";
exit(($code === 200 || $code === 202) ? 0 : 1);
