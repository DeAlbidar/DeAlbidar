<?php
/**
 * Cron Job Configuration
 * Configure your Facebook API credentials here
 */

// Main app configuration (loads Facebook Graph API config + the gitignored
// config.local.php secrets — see config.local.php.example)
require_once dirname(__DIR__) . '/config.php';

// Database configuration (reuses the credentials already resolved above,
// instead of duplicating them here)
define('CRON_DB_TYPE', DB_TYPE);
define('CRON_DB_HOST', DB_HOST);
define('CRON_DB_NAME', DB_NAME);
define('CRON_DB_USER', DB_USER);
define('CRON_DB_PASS', DB_PASS);

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
