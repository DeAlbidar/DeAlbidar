<?php
/**
 * Database Installation Script
 * Run this once to create the required tables for the cron job
 */

require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/libs/Database.php';

try {
    $db = new Database(DB_TYPE, DB_HOST, DB_NAME, DB_USER, DB_PASS);
    
    // Create facebook_posts table
    $createTableSQL = "
    CREATE TABLE IF NOT EXISTS `facebook_posts` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `title` LONGTEXT NOT NULL,
        `category` VARCHAR(100) NOT NULL,
        `link` LONGTEXT,
        `image` LONGTEXT,
        `facebook_post_id` VARCHAR(255),
        `posted_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        `status` VARCHAR(50) DEFAULT 'posted',
        INDEX `idx_category` (`category`),
        INDEX `idx_posted_date` (`posted_date`),
        INDEX `idx_facebook_post_id` (`facebook_post_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    
    // Execute the SQL
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die('Database connection failed: ' . $conn->connect_error);
    }
    
    if ($conn->multi_query($createTableSQL)) {
        echo "✓ Table 'facebook_posts' created successfully!" . PHP_EOL;
    } else {
        echo "✗ Error creating table: " . $conn->error . PHP_EOL;
        exit(1);
    }
    
    $conn->close();
    
    echo "✓ Database installation completed!" . PHP_EOL;
    echo PHP_EOL;
    echo "Next steps:" . PHP_EOL;
    echo "1. Update config.cron.php with your Facebook Page ID and Access Token" . PHP_EOL;
    echo "2. Set up the cron job with the command:" . PHP_EOL;
    echo "   0 */3 * * * /usr/bin/php /var/www/html/DeAlbidar/cron/post_content.php >> /var/log/facebook_poster.log 2>&1" . PHP_EOL;
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
    exit(1);
}

?>
