<?php
/**
 * Cron Job Configuration
 * Configure your Facebook API credentials here
 */

// Facebook Graph API Configuration
define('FACEBOOK_PAGE_ID', '101457405937851');
define('FACEBOOK_ACCESS_TOKEN', 'EAAVmDqqwpFUBQ2gZBSjqJWldaQtGcX2n79AXzXOhTXikv5mac9UKPhpinTrb7ZBOnn0vhYm3UAhrxwMJ2YdMzKZALqQXbOdHOG5xAKwPhRdTekZBNanTANy9dgXmFAz4dzzEXQcZAQXJ89ZBPX0gEA1UXxReHp1ox4WJe3KgPLxN3bWQcxFCNmYDXTDJZCfz9bE4g4qlZCTEnjYgsPasBTiblhrgJmVKBHwzVfepLL5GGSDFgZCRsA3hFidyOIxfJ57L3nwhZCnsNuZBpjDbKEoylUiCCCL6cnPiRa5DILZBLKNG08VZBnPblsnIzAZBMdrDmkaEaqtaM2IEuWgynZC');

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
