<?php
// scratch/test_news.php
$urls = [
    'dantri' => 'https://dantri.com.vn/rss/home.rss',
    'nhandan_home' => 'https://nhandan.vn/rss/home.rss',
    'nhandan_political' => 'https://nhandan.vn/rss/chinhtri.rss',
    'dangcongsan' => 'https://dangcongsan.vn/rss',
    'baochinhphu' => 'https://baochinhphu.vn/rss/tin-noi-bat.rss'
];

$context = stream_context_create([
    'http' => [
        'timeout' => 5,
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
    ]
]);

foreach ($urls as $key => $url) {
    echo "--- Testing $key ($url) ---\n";
    $content = @file_get_contents($url, false, $context);
    if ($content === false) {
        echo "FAILED to fetch\n";
    } else {
        echo "SUCCESS: Fetched " . strlen($content) . " bytes\n";
        if (strpos($content, '<rss') !== false || strpos($content, '<xml') !== false) {
            echo "Format: XML/RSS\n";
            $xml = @simplexml_load_string($content);
            if ($xml) {
                echo "Parsed: YES, Title = " . ($xml->channel->title ?? 'N/A') . "\n";
            } else {
                echo "Parsed: NO (SimpleXML failed)\n";
            }
        } else {
            echo "Format: HTML/Other (HTML length: " . strlen($content) . ")\n";
            echo "Snippet: " . substr(strip_tags($content), 0, 100) . "\n";
        }
    }
    echo "\n";
}
