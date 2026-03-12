<?php
/**
 * Test Script: Generate and Post Trending Content
 * This script generates a random trending post and posts it to Facebook
 */

// Set timezone
date_default_timezone_set('UTC');

// Include configuration and libraries
require_once dirname(__DIR__) . '/cron/config.cron.php';
require_once dirname(__DIR__) . '/libs/FacebookAPI.php';
require_once dirname(__DIR__) . '/libs/ContentGenerator.php';

echo "=== Generate and Post Trending Content Test ===" . PHP_EOL;
echo "Date: " . date('Y-m-d H:i:s') . PHP_EOL . PHP_EOL;

try {
    // Initialize Facebook API and Content Generator
    $facebook = new FacebookAPI(
        FACEBOOK_ACCESS_TOKEN,
        FACEBOOK_PAGE_ID
    );
    
    $contentGenerator = new ContentGenerator();
    
    echo "✓ Services initialized successfully" . PHP_EOL . PHP_EOL;
    
    // Get all available categories
    $categories = ContentGenerator::getCategories();
    
    echo "Available content categories:" . PHP_EOL;
    foreach ($categories as $index => $category) {
        echo "  " . ($index + 1) . ". " . ucwords(str_replace('_', ' ', $category)) . PHP_EOL;
    }
    echo PHP_EOL;
    
    // Select a random category
    $selectedCategory = $categories[array_rand($categories)];
    echo "Randomly selected category: " . ucwords(str_replace('_', ' ', $selectedCategory)) . PHP_EOL . PHP_EOL;
    
    // Generate content
    echo "Generating trending content..." . PHP_EOL;
    $content = $contentGenerator->generateContent($selectedCategory);
    
    if (!$content) {
        throw new Exception('Failed to generate content');
    }
    
    // Display the generated content
    echo "Generated Content:" . PHP_EOL;
    echo "  Title: " . $content['title'] . PHP_EOL;
    echo "  Content: " . $content['content'] . PHP_EOL;
    echo "  Category: " . $content['category'] . PHP_EOL;
    if (!empty($content['link'])) {
        echo "  Link: " . $content['link'] . PHP_EOL;
    }
    echo "  Image: " . (isset($content['image']) ? "Yes (Unsplash)" : "No") . PHP_EOL;
    echo PHP_EOL;
    
    // Post to Facebook
    echo "Posting to Facebook..." . PHP_EOL;
    
    $message = $content['title'];
    if (!empty($content['link'])) {
        $message .= "\n\n" . $content['link'];
    }
    
    $result = $facebook->postContent(
        $message,
        $content['link'] ?? null,
        $content['image'] ?? null
    );
    
    // Check result
    if (isset($result['error'])) {
        echo "✗ ERROR posting to Facebook:" . PHP_EOL;
        echo json_encode($result, JSON_PRETTY_PRINT) . PHP_EOL;
    } elseif (isset($result['id'])) {
        echo "✓ SUCCESS! Post created successfully" . PHP_EOL;
        echo "✓ Post ID: " . $result['id'] . PHP_EOL;
        echo "✓ View on Facebook: https://www.facebook.com/" . $result['id'] . PHP_EOL;
        echo PHP_EOL;
        echo "Post Details:" . PHP_EOL;
        echo "  Category: " . $content['category'] . PHP_EOL;
        echo "  Posted at: " . date('Y-m-d H:i:s') . PHP_EOL;
        echo "  Status: Posted successfully ✓" . PHP_EOL;
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
