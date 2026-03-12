<?php
date_default_timezone_set('UTC');
require_once dirname(__DIR__) . '/cron/config.cron.php';

echo "Testing direct Facebook API call...\n";
echo "Page ID: " . FACEBOOK_PAGE_ID . "\n";
echo "Token: " . substr(FACEBOOK_ACCESS_TOKEN, 0, 20) . "...\n\n";

$message = "✅ Test post from DeAlbidar - " . date('Y-m-d H:i:s');
$endpoint = "https://graph.facebook.com/v19.0/" . FACEBOOK_PAGE_ID . "/feed";
$postData = [
    'message' => $message,
    'access_token' => FACEBOOK_ACCESS_TOKEN
];

echo "Posting to: $endpoint\n\n";

// Use PHP streams directly
$context = stream_context_create([
    'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($postData),
        'timeout' => 30
    ]
]);

echo "Sending request... Please wait...\n";
$response = @file_get_contents($endpoint, false, $context);

if ($response === false) {
    echo "❌ Failed to connect\n";
    exit(1);
}

$decoded = json_decode($response, true);

if (isset($decoded['id'])) {
    echo "✅ SUCCESS!\n";
    echo "Post ID: " . $decoded['id'] . "\n";
    echo "View: https://facebook.com/" . $decoded['id'] . "\n";
} else if (isset($decoded['error'])) {
    echo "❌ ERROR: " . $decoded['error']['message'] . "\n";
    echo "Response:\n";  
    print_r($decoded);
} else {
    echo "Response:\n";
    print_r($decoded);
}

?>
