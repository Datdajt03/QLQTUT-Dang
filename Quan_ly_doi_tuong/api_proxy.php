<?php
// Chucnang/api_proxy.php – Proxy PHP → Python Flask API
// Tránh CORS, xử lý lỗi khi API không chạy

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/User/auth.php';
requireRole(['Quản lý', 'Admin']);

define('API_BASE', 'http://localhost:5000');
define('API_TIMEOUT', 15);

$path   = trim($_GET['path'] ?? '', '/');
$method = $_SERVER['REQUEST_METHOD'];

// Security: whitelist allowed paths
$allowed = ['health','api/filters','api/list','api/export/all','api/export/selected'];
$allowed_pattern = '/^(health|api\/(filters|list|export\/(all|selected|single\/\d+)))$/';

if (!preg_match($allowed_pattern, $path)) {
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden']);
    exit;
}

$url = API_BASE . '/' . $path;

// Append GET params for /api/list
if ($method === 'GET' && !empty($_SERVER['QUERY_STRING'])) {
    $qs = $_SERVER['QUERY_STRING'];
    $qs = preg_replace('/&?path=[^&]*/','', $qs);
    if ($qs) $url .= '?' . ltrim($qs, '&');
}

// Use cURL
$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => API_TIMEOUT,
    CURLOPT_HEADER         => true,
    CURLOPT_FOLLOWLOCATION => false,
]);

if ($method === 'POST') {
    $body = file_get_contents('php://input');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
}

$response     = curl_exec($ch);
$http_code    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$header_size  = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
$content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
$errno        = curl_errno($ch);
curl_close($ch);

// Connection failed → API not running
if ($errno) {
    http_response_code(503);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'api_offline',
        'message' => 'Python API chưa chạy. Hãy mở file python_api/start_api.bat để khởi động.'
    ]);
    exit;
}

$headers = substr($response, 0, $header_size);
$body    = substr($response, $header_size);

// Pass through Content-Disposition for file downloads
if (preg_match('/content-disposition:\s*(.+)/i', $headers, $m)) {
    header('Content-Disposition: ' . trim($m[1]));
}

http_response_code($http_code);
header('Content-Type: ' . ($content_type ?: 'application/json'));
echo $body;
