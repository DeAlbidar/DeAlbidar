<?php
/**
 * Cron Job Configuration
 * Configure your Facebook API credentials here
 */

// Facebook Graph API Configuration
define('FACEBOOK_PAGE_ID', '101849324975567');
define('FACEBOOK_ACCESS_TOKEN', 'EAANU5a3nV4MBQw5isoTQ1Qn6HUYiijrF0ouo2MkVZAVJ8dXzElGARzSXZClxfkaH53617VyQ1Lbb0A9MLZAbrZBmeycGzxgKbUmZAJXbcbFzQHWTlaL3QlE0wu17M3R85hhVK8GIVAx9aNvwhoQdw8PGtWbB5DxZBkuuaXrwDjh1XBu5wIkgzyGmOV9VxgwA7nEc4XtJWuyY4mGVdNTBIpgrnt9P6hYQq6HhwQHR28ikkZD');

// Database configuration
define('CRON_DB_TYPE', 'mysql');
define('CRON_DB_HOST', 'localhost');
define('CRON_DB_NAME', 'w3multim_Albidar');
define('CRON_DB_USER', 'w3multim_albidar');
define('CRON_DB_PASS', 'U=U2tR*%&gs.');

// Logging configuration
define('CRON_LOG_DIR', dirname(__FILE__) . '/logs/');
define('ENABLE_CRON_LOGGING', true);

// Content posting configuration
define('POST_INTERVAL_HOURS', 3); // Post every 3 hours

// Categories to post from
$CONTENT_CATEGORIES = [
    'tech_ai',
    'funny_memes',
    'relationship_stories',
    'motivational_content',
    'news_trending',
    'football_highlights',
    'health_tips'
];

// Create logs directory if it doesn't exist
if (!is_dir(CRON_LOG_DIR)) {
    mkdir(CRON_LOG_DIR, 0755, true);
}

?>
