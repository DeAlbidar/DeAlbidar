<?php
/**
 * Facebook API Connection Test
 * This script tests if the Facebook credentials are working correctly
 */

// Set timezone
date_default_timezone_set('UTC');

// Include configuration and libraries
require_once dirname(__DIR__) . '/cron/config.cron.php';
require_once dirname(__DIR__) . '/libs/FacebookAPI.php';

echo "=== Facebook API Connection Test ===" . PHP_EOL;
echo "Date: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

try {
    // Initialize Facebook API
    $facebook = new FacebookAPI(
        FACEBOOK_ACCESS_TOKEN,
        FACEBOOK_PAGE_ID
    );
    
    echo "✓ FacebookAPI initialized successfully" . PHP_EOL;
    echo "✓ Page ID: " . FACEBOOK_PAGE_ID . PHP_EOL;
    echo "✓ Access Token: " . substr(FACEBOOK_ACCESS_TOKEN, 0, 20) . "..." . PHP_EOL . PHP_EOL;
    
    // Test posting
    echo "Testing a simple post to Facebook..." . PHP_EOL;
    
    $testMessage = "🤖 Test post from DeAlbidar - " . date('Y-m-d H:i:s');
    $result = $facebook->postContent($testMessage);
    
    if (isset($result['error'])) {
        echo "✗ ERROR posting to Facebook:" . PHP_EOL;
        echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
    } elseif (isset($result['id'])) {
        echo "✓ SUCCESS! Post created successfully" . PHP_EOL;
        echo "✓ Post ID: " . $result['id'] . PHP_EOL;
        echo "✓ View on Facebook: https://www.facebook.com/" . $result['id'] . PHP_EOL;
    } else {
        echo "? Unexpected response:" . PHP_EOL;
        echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
    }
    
} catch (Exception $e) {
    echo "✗ FATAL ERROR: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

echo PHP_EOL . "=== Test Complete ===" . PHP_EOL;

?>
