<?php
/**
 * Facebook Cron Job Setup Script
 * 
 * This script sets up the database and creates required directories
 * Run once before deploying the cron job
 * 
 * Usage: 
 * php /var/www/html/DeAlbidar/cron/setup.php
 */

echo "Facebook Poster Cron Job Setup" . PHP_EOL;
echo "==============================" . PHP_EOL . PHP_EOL;

// Change to project root
chdir('/var/www/html/DeAlbidar');

// Include config
require 'config.php';
require 'libs/Database.php';

try {
    // 1. Create logs directory
    echo "Creating logs directory...";
    $logDir = 'logs/facebook_posts';
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
        echo " ✓" . PHP_EOL;
    } else {
        echo " (already exists)" . PHP_EOL;
    }
    
    // 2. Create database table
    echo "Creating database table...";
    
    // Use direct mysqli connection (Database class requires web context)
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die(' ✗ Connection failed: ' . $conn->connect_error . PHP_EOL);
    }
    
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
    
    if ($conn->query($createTableSQL)) {
        echo " ✓" . PHP_EOL;
    } else {
        die(' ✗ Error: ' . $conn->error . PHP_EOL);
    }
    
    $conn->close();
    
    // 3. Verify Facebook credentials
    echo "Checking Facebook credentials...";
    if (defined('FACEBOOK_PAGE_ID') && defined('FACEBOOK_ACCESS_TOKEN')) {
        echo " ✓" . PHP_EOL;
        echo "  Page ID: " . FACEBOOK_PAGE_ID . PHP_EOL;
        echo "  Token: " . substr(FACEBOOK_ACCESS_TOKEN, 0, 20) . "..." . PHP_EOL;
    } else {
        echo " ✗" . PHP_EOL;
        echo "  Facebook credentials not found in config.php" . PHP_EOL;
    }
    
    echo PHP_EOL . "Setup Complete!" . PHP_EOL;
    echo PHP_EOL . "Next steps:" . PHP_EOL;
    echo "1. Make the shell script executable:" . PHP_EOL;
    echo "   chmod +x /var/www/html/DeAlbidar/cron/facebook_poster_cron.sh" . PHP_EOL;
    echo PHP_EOL;
    echo "2. Add to crontab (every 3 hours):" . PHP_EOL;
    echo "   crontab -e" . PHP_EOL;
    echo "   Add this line:" . PHP_EOL;
    echo "   0 */3 * * * /var/www/html/DeAlbidar/cron/facebook_poster_cron.sh >> /var/log/facebook_poster.log 2>&1" . PHP_EOL;
    echo PHP_EOL;
    echo "3. Test the cron job:" . PHP_EOL;
    echo "   /var/www/html/DeAlbidar/cron/facebook_poster_cron.sh" . PHP_EOL;
    echo PHP_EOL;
    echo "4. Monitor logs:" . PHP_EOL;
    echo "   tail -f /var/log/facebook_poster.log" . PHP_EOL;
    
} catch (Exception $e) {
    echo ' ✗ Error: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}

?>
